@extends('layouts.app')

@section('title', 'Manajemen Member')

@section('content')

<!-- Alert Notification -->
<div id="alertContainer" class="fixed top-4 right-4 z-50 space-y-3 w-80">
    <!-- Alert will appear here dynamically -->
</div>

<!-- Modal Konfirmasi Hapus -->
<div id="modalHapusMember" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl p-6 w-96">
        <div class="flex items-start gap-4">
            <div class="flex-shrink-0 p-2 bg-red-100 rounded-full">
                <i data-lucide="alert-triangle" class="w-6 h-6 text-red-600"></i>
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-semibold text-gray-900">Konfirmasi Hapus</h3>
                <div class="mt-2">
                    <p class="text-sm text-gray-600">Anda yakin ingin menghapus member ini? Data yang dihapus tidak dapat dikembalikan.</p>
                </div>
                <div class="mt-4 flex justify-end gap-3">
                    <button id="btnBatalHapus" type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none">
                        Batal
                    </button>
                    <button id="btnKonfirmasiHapus" type="button" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md shadow-sm hover:bg-red-700 focus:outline-none">
                        Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Page Title + Action -->
<div class="mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <h1 class="text-2xl font-bold text-gray-800">Manajemen Member</h1>
        <div class="flex items-center gap-2 w-full md:w-auto">
            <!-- Search input with icon -->
            <div class="relative w-full md:w-64">
              <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <i data-lucide="search" class="w-5 h-5 text-gray-400"></i>
              </span>
              <input id="searchInput" type="text" placeholder="Search..."
                  class="w-full pl-10 pr-4 py-3 border rounded-lg text-base focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" />
          </div>

            @if(auth()->check() && auth()->user()->role !== 'supervisor')
            <!-- Add Member Button -->
            <button onclick="openModalTambah()"
                class="px-5 py-3 text-base font-medium text-white bg-green-500 rounded-lg hover:bg-green-600 shadow">
                + Tambah Member
            </button>
            @endif
        </div>
    </div>
</div>

<!-- Card: Member Info + Action -->
<div class="bg-white rounded-md p-4 shadow-md mb-4 flex flex-col md:flex-row md:items-center md:justify-between">
    <!-- Left: Title -->
    <div class="mb-3 md:mb-0 flex items-start gap-2">
        <i data-lucide="users" class="w-5 h-5 text-gray-600 mt-1"></i>
        <div>
            <h2 class="text-lg font-semibold text-gray-800">Manajemen Member</h2>
            <p class="text-sm text-gray-600">Kelola semua member Aladdin Karpet di sini.</p>
        </div>
    </div>
</div>

<!-- Card: Member Table -->
<div class="bg-white rounded-lg shadow-lg p-6">
    <!-- Table -->
    <div class="overflow-x-auto">
        <table id="memberTable" class="w-full text-base">
            <thead class="text-left text-gray-700 border-b-2">
                <tr>
                    <th class="py-3 font-semibold">No.</th>
                    <th class="py-3 font-semibold">Nama</th>
                    <th class="py-3 font-semibold">Kode Member</th>
                    <th class="py-3 font-semibold">Email</th>
                    <th class="py-3 font-semibold">Alamat</th>
                    <th class="py-3 font-semibold">Jenis Kelamin</th>
                    <th class="py-3 font-semibold">Total Transaksi</th>
                    <th class="py-3 font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody id="memberTableBody" class="text-gray-700 divide-y">
                <!-- Data will be filled dynamically -->
            </tbody>
        </table>
        
        <!-- Loading Indicator -->
        <div id="loadingIndicator" class="flex flex-col items-center justify-center gap-2 py-8">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                 class="animate-spin text-green-500">
                <path d="M21 12a9 9 0 1 1-6.219-8.56"/>
            </svg>
            <span class="text-gray-500">Memuat data...</span>
        </div>
    </div>
</div>

@include('partials.member.tambah-member')
@include('partials.member.edit-member')
@include('partials.member.history-member')

<script>
    // Global variables
    let memberIdToDelete = null;
    let allMembers = [];
    let filteredMembers = [];
    let debounceTimer;
    let alertTimeout;

    // Initialize when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        initializeLucide();
        initializeEventListeners();
        loadMembers();
    });

    function initializeLucide() {
        if (window.lucide) {
            window.lucide.createIcons();
        }
    }

    function initializeEventListeners() {
        // Delete modal event listeners
        const btnBatalHapus = document.getElementById('btnBatalHapus');
        const btnKonfirmasiHapus = document.getElementById('btnKonfirmasiHapus');
        if (btnBatalHapus) btnBatalHapus.addEventListener('click', closeConfirmDelete);
        if (btnKonfirmasiHapus) btnKonfirmasiHapus.addEventListener('click', hapusMember);

        // Modal event listeners
        const batalTambah = document.getElementById('btnBatalModalTambah');
        const batalEdit = document.getElementById('btnBatalModalEdit');
        if (batalTambah) batalTambah.addEventListener('click', closeModalTambah);
        if (batalEdit) batalEdit.addEventListener('click', closeModalEdit);

        // Form event listeners
        const formTambah = document.getElementById('formTambahMember');
        const formEdit = document.getElementById('formEditMember');
        if (formTambah) formTambah.addEventListener('submit', handleFormSubmit);
        if (formEdit) formEdit.addEventListener('submit', handleEditSubmit);

        // Search event listeners
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.addEventListener('input', handleSearchInput);
            searchInput.addEventListener('keypress', handleSearchEnter);
        }
    }

    function handleFormSubmit(e) {
        e.preventDefault();
        submitForm();
    }

    function handleEditSubmit(e) {
        e.preventDefault();
        submitEditMember(e);
    }

    function handleSearchInput() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(searchMembers, 300);
    }

    function handleSearchEnter(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            clearTimeout(debounceTimer);
            searchMembers();
        }
    }

    function showAlert(type, message) {
        // Clear existing alerts
        clearExistingAlerts();
        
        // Clear previous timeout
        if (alertTimeout) {
            clearTimeout(alertTimeout);
        }

        const alertContainer = document.getElementById('alertContainer');
        if (!alertContainer) return;

        const alertConfig = {
            success: {
                bgColor: 'bg-green-50',
                borderColor: 'border-green-200',
                textColor: 'text-green-800',
                icon: 'check-circle',
                iconColor: 'text-green-500'
            },
            error: {
                bgColor: 'bg-red-50',
                borderColor: 'border-red-200',
                textColor: 'text-red-800',
                icon: 'alert-circle',
                iconColor: 'text-red-500'
            }
        };

        const config = alertConfig[type] || alertConfig.success;
        const alertId = 'alert-' + Date.now();

        const alertElement = document.createElement('div');
        alertElement.id = alertId;
        alertElement.className = `p-4 border rounded-lg shadow-sm ${config.bgColor} ${config.borderColor} ${config.textColor} flex items-start gap-3 animate-fade-in-up`;
        alertElement.innerHTML = `
            <i data-lucide="${config.icon}" class="w-5 h-5 mt-0.5 ${config.iconColor}"></i>
            <div class="flex-1"><p class="text-sm font-medium">${message}</p></div>
            <button onclick="closeAlert('${alertId}')" class="p-1 rounded-full hover:bg-gray-100">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        `;

        alertContainer.prepend(alertElement);
        if (window.lucide) window.lucide.createIcons();
        
        // Auto close after 5 seconds
        alertTimeout = setTimeout(() => closeAlert(alertId), 5000);
    }

    function clearExistingAlerts() {
        const alertContainer = document.getElementById('alertContainer');
        if (!alertContainer) return;
        
        const alerts = alertContainer.querySelectorAll('[id^="alert-"]');
        alerts.forEach(alert => {
            alert.remove();
        });
    }

    function closeAlert(id) {
        const alert = document.getElementById(id);
        if (alert) {
            alert.classList.add('animate-fade-out');
            setTimeout(() => {
                if (alert && alert.parentNode) {
                    alert.remove();
                }
            }, 300);
        }
    }

    async function loadMembers() {
        try {
            const token = localStorage.getItem('token');
            const loadingIndicator = document.getElementById('loadingIndicator');
            const memberTableBody = document.getElementById('memberTableBody');
            
            if (loadingIndicator) loadingIndicator.classList.remove('hidden');
            if (memberTableBody) memberTableBody.innerHTML = '';

            const response = await fetch('/api/members', {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (response.ok) {
                allMembers = data.data || [];
                filteredMembers = [...allMembers];
                renderMembers();
            } else {
                throw new Error(data.message || 'Failed to load member data');
            }
        } catch (error) {
            console.error('Error loading members:', error);
            showAlert('error', error.message);
        } finally {
            const loadingIndicator = document.getElementById('loadingIndicator');
            if (loadingIndicator) loadingIndicator.classList.add('hidden');
        }
    }

    function renderMembers() {
        const tableBody = document.getElementById('memberTableBody');
        if (!tableBody) return;
        
        tableBody.innerHTML = '';
        
        // Get the current user role from somewhere in your application
        // This should be set when the user logs in
        const currentUserRole = getCurrentUserRole(); // You need to implement this function
        const isSupervisor = currentUserRole === 'supervisor';

        if (filteredMembers.length === 0) {
            tableBody.innerHTML = `
                <tr id="noResultsMessage">
                    <td colspan="8" class="py-8 text-center">
                        <div class="flex flex-col items-center justify-center gap-2">
                            <i data-lucide="search-x" class="w-8 h-8 text-gray-400"></i>
                            <p class="text-gray-500 font-medium">No members found</p>
                        </div>
                    </td>
                </tr>
            `;
            if (window.lucide) window.lucide.createIcons();
            return;
        }

        filteredMembers.forEach((member, index) => {
            const row = document.createElement('tr');
            row.className = 'hover:bg-gray-50';
            
            // Basic member information cells remain the same
            let rowContent = `
                <td class="py-4">${index + 1}</td>
                <td class="py-4">
                    <div class="flex items-center gap-4">
                        <div class="bg-green-100 p-2 rounded-full">
                            <i data-lucide="user" class="w-6 h-6 text-green-500"></i>
                        </div>
                        <div>
                            <div class="font-semibold text-base text-gray-900">${member.name}</div>
                            <div class="text-sm text-gray-500">${member.phone || '-'}</div>
                        </div>
                    </div>
                </td>
                <td class="py-4">${member.member_code}</td>
                <td class="py-4">${member.email || '-'}</td>
                <td class="py-4">${member.address || '-'}</td>
                <td class="py-4">${member.gender === 'male' ? 'Male' : member.gender === 'female' ? 'Female' : '-'}</td>
                <td class="py-4">${member.orders_count || 0}</td>
            `;
            
            // Only show action buttons if user is NOT a supervisor
            if (!isSupervisor) {
                rowContent += `
                    <td class="py-4 relative">
                        <div class="relative inline-block">
                            <button onclick="toggleDropdown(this)" class="p-2 hover:bg-gray-100 rounded-lg">
                                <i data-lucide="more-vertical" class="w-5 h-5 text-gray-500"></i>
                            </button>
                            <div class="dropdown-menu hidden absolute right-0 z-50 mt-1 w-40 bg-white border border-gray-200 rounded-lg shadow-xl text-base">
                                <div class="px-4 py-2 font-bold text-left border-b">Actions</div>
                                <button onclick="showMemberHistory(${member.id})" class="flex items-center w-full px-4 py-2.5 hover:bg-gray-100 text-left">
                                    <i data-lucide="history" class="w-5 h-5 mr-3 text-gray-500"></i> History
                                </button>
                                <button onclick="editMember(${member.id})" class="flex items-center w-full px-4 py-2.5 hover:bg-gray-100 text-left">
                                    <i data-lucide="pencil" class="w-5 h-5 mr-3 text-gray-500"></i> Edit
                                </button>
                                <button onclick="showConfirmDelete(${member.id})" class="flex items-center w-full px-4 py-2.5 hover:bg-gray-100 text-left text-red-600">
                                    <i data-lucide="trash-2" class="w-5 h-5 mr-3"></i> Delete
                                </button>
                            </div>
                        </div>
                    </td>
                `;
            } else {
                // For supervisors, add an empty cell or a cell with limited options
                // rowContent += `<td class="py-4">-</td>`;
                // Alternatively, you could show only the history button:
                
                rowContent += `
                    <td class="py-4 relative">
                        <div class="relative inline-block">
                            <button onclick="showMemberHistory(${member.id})" class="p-2 hover:bg-gray-100 rounded-lg">
                                <i data-lucide="history" class="w-5 h-5 text-gray-500"></i>
                            </button>
                        </div>
                    </td>
                `;
                
            }
            
            row.innerHTML = rowContent;
            tableBody.appendChild(row);
        });

        if (window.lucide) window.lucide.createIcons();
    }

    //fungsi panggil role di localStorage
    function getCurrentUserRole() {
        // Example implementation:
        return localStorage.getItem('role') || 'default';
    }

    function showConfirmDelete(id) {
        memberIdToDelete = id;
        const modal = document.getElementById('modalHapusMember');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
    }

    function closeConfirmDelete() {
        const modal = document.getElementById('modalHapusMember');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
        memberIdToDelete = null;
    }

    async function hapusMember() {
        if (!memberIdToDelete) return;
        
        try {
            const token = localStorage.getItem('token');
            const response = await fetch(`/api/members/${memberIdToDelete}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                }
            });

            const data = await response.json();

            if (response.ok) {
                showAlert('success', 'Member deleted successfully!');
                loadMembers();
            } else {
                throw new Error(data.message || 'Failed to delete member');
            }
        } catch (error) {
            console.error('Error deleting member:', error);
            showAlert('error', error.message);
        } finally {
            closeConfirmDelete();
        }
    }

    // History Member Functionality
    function showMemberHistory(id) {
        // Call the historyMember function from the included modal
        if (window.historyMember) {
            window.historyMember(id);
        } else {
            console.error('History member modal not loaded');
            showAlert('error', 'History feature not available');
        }
    }

    function editMember(id) {
        const member = allMembers.find(m => m.id === id);
        if (!member) return;
        
        const editNama = document.getElementById('editNamaMember');
        const editTelepon = document.getElementById('editTeleponMember');
        const editEmail = document.getElementById('editEmailMember');
        const editAlamat = document.getElementById('editAlamatMember');
        const editGender = document.getElementById('editJenisKelamin');
        const editId = document.getElementById('memberIdToEdit');
        
        if (editNama) editNama.value = member.name;
        if (editTelepon) editTelepon.value = member.phone || '';
        if (editEmail) editEmail.value = member.email || '';
        if (editAlamat) editAlamat.value = member.address || '';
        if (editGender) editGender.value = member.gender || '';
        if (editId) editId.value = member.id;
        
        openModalEdit();
    }

    async function submitEditMember(e) {
        e.preventDefault();
        
        if (!validateEditForm()) {
            return;
        }
        
        const btnEdit = document.getElementById('btnEditMember');
        if (!btnEdit) return;
        
        const originalText = btnEdit.innerHTML;
        btnEdit.innerHTML = `<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Saving...`;
        btnEdit.disabled = true;
        
        const memberId = document.getElementById('memberIdToEdit')?.value;
        const token = localStorage.getItem('token');
        
        try {
            const response = await fetch(`/api/members/${memberId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                body: JSON.stringify({
                    name: document.getElementById('editNamaMember')?.value,
                    phone: document.getElementById('editTeleponMember')?.value,
                    email: document.getElementById('editEmailMember')?.value,
                    address: document.getElementById('editAlamatMember')?.value,
                    gender: document.getElementById('editJenisKelamin')?.value
                })
            });
            
            const data = await response.json();
            
            if (response.ok) {
                showAlert('success', 'Member updated successfully!');
                loadMembers();
                closeModalEdit();
            } else {
                throw new Error(data.message || 'Failed to update member');
            }
        } catch (error) {
            console.error('Error updating member:', error);
            showAlert('error', error.message);
        } finally {
            btnEdit.innerHTML = originalText;
            btnEdit.disabled = false;
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Form event listeners
        document.getElementById('formTambahMember')?.addEventListener('submit', function (e) {
        e.preventDefault();
        submitForm();
        });

        document.getElementById('btnTambahMember')?.addEventListener('click', function () {
            submitForm();
         });
    });
    
    async function submitForm() {
        if (!validateForm()) return;

        const btnTambah = document.getElementById('btnTambahMember');
        if (!btnTambah) return;
        
        const originalText = btnTambah.innerHTML;
        btnTambah.innerHTML = `<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Saving...`;
        btnTambah.disabled = true;

        const token = localStorage.getItem('token');

        try {
            const response = await fetch('/api/members', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                body: JSON.stringify({
                    name: document.getElementById('namaMember')?.value,
                    phone: document.getElementById('teleponMember')?.value,
                    email: document.getElementById('emailMember')?.value,
                    address: document.getElementById('alamatMember')?.value,
                    gender: document.getElementById('jenisKelamin')?.value
                })
            });

            const result = await response.json();

            if (response.ok) {
                showAlert('success', 'Member added successfully!');
                resetForm();
                closeModalTambah();
                loadMembers();
            } else {
                throw new Error(result.message || 'Failed to add member');
            }
        } catch (error) {
            console.error('Error adding member:', error);
            showAlert('error', error.message);
        } finally {
            btnTambah.innerHTML = originalText;
            btnTambah.disabled = false;
        }
    }

    function toggleDropdown(button) {
        const menu = button.nextElementSibling;
        if (!menu) return;

        // Close all other dropdowns
        document.querySelectorAll('.dropdown-menu').forEach(m => {
            if (m !== menu) {
                m.classList.add('hidden');
            }
        });

        // Toggle current dropdown
        menu.classList.toggle('hidden');

        // Calculate position relative to viewport
        const buttonRect = button.getBoundingClientRect();
        const spaceBelow = window.innerHeight - buttonRect.bottom;
        const spaceRight = window.innerWidth - buttonRect.right;
        const menuHeight = menu.offsetHeight || 176;
        const menuWidth = menu.offsetWidth || 160;
        
        // Reset positioning
        menu.style.position = 'fixed';
        menu.style.top = '';
        menu.style.bottom = '';
        menu.style.left = '';
        menu.style.right = '';

        // Vertical position
        if (spaceBelow < menuHeight) {
            // If not enough space below, show above
            menu.style.bottom = `${window.innerHeight - buttonRect.top + 5}px`;
            menu.classList.add('dropdown-animation-up');
            menu.classList.remove('dropdown-animation-down');
        } else {
            // If enough space below, show below
            menu.style.top = `${buttonRect.bottom + 5}px`;
            menu.classList.add('dropdown-animation-down');
            menu.classList.remove('dropdown-animation-up');
        }

        // Horizontal position
        if (spaceRight < menuWidth) {
            // If not enough space on right, show left
            menu.style.right = `${window.innerWidth - buttonRect.left}px`;
            menu.classList.add('dropdown-animation-right');
            menu.classList.remove('dropdown-animation-left');
        } else {
            // If enough space on right, show right
            menu.style.left = `${buttonRect.left}px`;
            menu.classList.add('dropdown-animation-left');
            menu.classList.remove('dropdown-animation-right');
        }
    }

    // Close all dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.relative.inline-block')) {
            document.querySelectorAll('.dropdown-menu').forEach(menu => {
                menu.classList.add('hidden');
            });
        }
    });

    function openModalTambah() {
        const modal = document.getElementById('modalTambahMember');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
    }

    function closeModalTambah() {
        const modal = document.getElementById('modalTambahMember');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    }
    
    function openModalEdit() {
        const modal = document.getElementById('modalEditMember');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
    }
    
    function closeModalEdit() {
        const modal = document.getElementById('modalEditMember');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    }

    function searchMembers() {
        const searchInput = document.getElementById('searchInput');
        if (!searchInput) return;
        
        const searchTerm = searchInput.value.toLowerCase();
        
        if (searchTerm.trim() === '') {
            filteredMembers = [...allMembers];
        } else {
            filteredMembers = allMembers.filter(member => {
                return (
                    member.name.toLowerCase().includes(searchTerm) ||
                    (member.phone && member.phone.toLowerCase().includes(searchTerm)) ||
                    (member.member_code && member.member_code.toLowerCase().includes(searchTerm)) ||
                    (member.email && member.email.toLowerCase().includes(searchTerm)) ||
                    (member.address && member.address.toLowerCase().includes(searchTerm))
                );
            });
        }
        
        renderMembers();
    }

    function validateForm() {
        // Form validation implementation
        return true;
    }

    function validateEditForm() {
        // Edit form validation implementation
        return true;
    }

    function resetForm() {
        // Form reset implementation
    }
</script>

<style>
    /* Alert animations */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @keyframes fadeOut {
        from {
            opacity: 1;
            transform: translateY(0);
        }
        to {
            opacity: 0;
            transform: translateY(10px);
        }
    }
    
    .animate-fade-in-up {
        animation: fadeInUp 0.3s ease-out forwards;
    }
    
    .animate-fade-out {
        animation: fadeOut 0.3s ease-out forwards;
    }
    
    /* Dropdown styling */
    .dropdown-menu {
        position: fixed;
        z-index: 9999;
        width: 160px;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }
    
    .relative.inline-block {
        position: relative;
    }
    
    td {
        overflow: visible !important;
    }
    
    /* Dropdown animations */
    @keyframes dropdownFadeIn {
        from {
            opacity: 0;
            transform: translateY(-5px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @keyframes dropdownFadeInUp {
        from {
            opacity: 0;
            transform: translateY(5px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @keyframes dropdownFadeInLeft {
        from {
            opacity: 0;
            transform: translateX(5px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    
    @keyframes dropdownFadeInRight {
        from {
            opacity: 0;
            transform: translateX(-5px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    
    .dropdown-animation-down {
        animation: dropdownFadeIn 0.2s ease-out forwards;
    }
    
    .dropdown-animation-up {
        animation: dropdownFadeInUp 0.2s ease-out forwards;
    }
    
    .dropdown-animation-left {
        animation: dropdownFadeInLeft 0.2s ease-out forwards;
    }
    
    .dropdown-animation-right {
        animation: dropdownFadeInRight 0.2s ease-out forwards;
    }
    
    /* Loading spinner */
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    
    .animate-spin {
        animation: spin 1s linear infinite;
    }

    /* Table styling */
    .table-container {
        position: relative;
    }
</style>

@endsection