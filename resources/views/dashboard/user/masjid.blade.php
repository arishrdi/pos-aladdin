@extends('layouts.app')

@section('title', 'Manajemen Masjid')

@section('content')

<!-- Alert Notification -->
<div id="alertContainer" class="fixed top-4 right-4 z-50 space-y-3 w-80">
    <!-- Alert will appear here dynamically -->
</div>

<!-- Modal Konfirmasi Hapus -->
<div id="modalHapusMasjid" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl p-6 w-96">
        <div class="flex items-start gap-4">
            <div class="flex-shrink-0 p-2 bg-red-100 rounded-full">
                <i data-lucide="alert-triangle" class="w-6 h-6 text-red-600"></i>
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-semibold text-gray-900">Konfirmasi Hapus</h3>
                <div class="mt-2">
                    <p class="text-sm text-gray-600">Anda yakin ingin menghapus data masjid ini? Data yang dihapus tidak dapat dikembalikan.</p>
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
        <h1 class="text-2xl font-bold text-gray-800">Manajemen Masjid</h1>
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
            <!-- Add Masjid Button -->
            <button onclick="openModalTambah()"
                class="px-5 py-3 text-base font-medium text-white bg-green-500 rounded-lg hover:bg-green-600 shadow">
                + Tambah Masjid
            </button>
            @endif
        </div>
    </div>
</div>

<!-- Card: Masjid Info + Action -->
<div class="bg-white rounded-md p-4 shadow-md mb-4 flex flex-col md:flex-row md:items-center md:justify-between">
    <!-- Left: Title -->
    <div class="mb-3 md:mb-0 flex items-start gap-2">
        <i data-lucide="building" class="w-5 h-5 text-gray-600 mt-1"></i>
        <div>
            <h2 class="text-lg font-semibold text-gray-800">Manajemen Masjid</h2>
            <p class="text-sm text-gray-600">Kelola semua data masjid di sini.</p>
        </div>
    </div>
</div>

<!-- Card: Masjid Table -->
<div class="bg-white rounded-lg shadow-lg p-6">
    <!-- Table -->
    <div class="overflow-x-auto">
        <table id="masjidTable" class="w-full text-base">
            <thead class="text-left text-gray-700 border-b-2">
                <tr>
                    <th class="py-3 font-semibold">No.</th>
                    <th class="py-3 font-semibold">Nama Masjid</th>
                    <th class="py-3 font-semibold">Alamat</th>
                    <th class="py-3 font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody id="masjidTableBody" class="text-gray-700 divide-y">
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

@include('partials.masjid.tambah-masjid')
@include('partials.masjid.edit-masjid')

<script>
    // Global variables
    let masjidIdToDelete = null;
    let allMasjid = [];
    let filteredMasjid = [];
    let debounceTimer;
    let alertTimeout;

    // Initialize when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        initializeLucide();
        initializeEventListeners();
        loadMasjid();
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
        if (btnKonfirmasiHapus) btnKonfirmasiHapus.addEventListener('click', hapusMasjid);

        // Modal event listeners
        const batalTambah = document.getElementById('btnBatalModalTambah');
        const batalEdit = document.getElementById('btnBatalModalEdit');
        if (batalTambah) batalTambah.addEventListener('click', closeModalTambah);
        if (batalEdit) batalEdit.addEventListener('click', closeModalEdit);

        // Form event listeners
        const formTambah = document.getElementById('formTambahMasjid');
        const formEdit = document.getElementById('formEditMasjid');
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
        submitEditMasjid(e);
    }

    function handleSearchInput() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(searchMasjid, 300);
    }

    function handleSearchEnter(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            clearTimeout(debounceTimer);
            searchMasjid();
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

    async function loadMasjid() {
        try {
            const token = localStorage.getItem('token');
            const loadingIndicator = document.getElementById('loadingIndicator');
            const masjidTableBody = document.getElementById('masjidTableBody');
            
            if (loadingIndicator) loadingIndicator.classList.remove('hidden');
            if (masjidTableBody) masjidTableBody.innerHTML = '';

            const response = await fetch('/api/mosques', {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (response.ok) {
                allMasjid = data.data || [];
                filteredMasjid = [...allMasjid];
                renderMasjid();
            } else {
                throw new Error(data.message || 'Failed to load masjid data');
            }
        } catch (error) {
            console.error('Error loading masjid:', error);
            showAlert('error', error.message);
        } finally {
            const loadingIndicator = document.getElementById('loadingIndicator');
            if (loadingIndicator) loadingIndicator.classList.add('hidden');
        }
    }

    function renderMasjid() {
        const tableBody = document.getElementById('masjidTableBody');
        if (!tableBody) return;
        
        tableBody.innerHTML = '';
        
        // Get the current user role from somewhere in your application
        const currentUserRole = getCurrentUserRole();
        const isSupervisor = currentUserRole === 'supervisor';

        if (filteredMasjid.length === 0) {
            tableBody.innerHTML = `
                <tr id="noResultsMessage">
                    <td colspan="4" class="py-8 text-center">
                        <div class="flex flex-col items-center justify-center gap-2">
                            <i data-lucide="search-x" class="w-8 h-8 text-gray-400"></i>
                            <p class="text-gray-500 font-medium">No masjid found</p>
                        </div>
                    </td>
                </tr>
            `;
            if (window.lucide) window.lucide.createIcons();
            return;
        }

        filteredMasjid.forEach((masjid, index) => {
            const row = document.createElement('tr');
            row.className = 'hover:bg-gray-50';
            
            // Basic masjid information cells
            let rowContent = `
                <td class="py-4">${index + 1}</td>
                <td class="py-4">
                    <div class="flex items-center gap-4">
                        <div class="bg-green-100 p-2 rounded-full">
                            <i data-lucide="building" class="w-6 h-6 text-green-500"></i>
                        </div>
                        <div>
                            <div class="font-semibold text-base text-gray-900">${masjid.name}</div>
                        </div>
                    </div>
                </td>
                <td class="py-4">${masjid.address || '-'}</td>
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
                                <button onclick="editMasjid(${masjid.id})" class="flex items-center w-full px-4 py-2.5 hover:bg-gray-100 text-left">
                                    <i data-lucide="pencil" class="w-5 h-5 mr-3 text-gray-500"></i> Edit
                                </button>
                                <button onclick="showConfirmDelete(${masjid.id})" class="flex items-center w-full px-4 py-2.5 hover:bg-gray-100 text-left text-red-600">
                                    <i data-lucide="trash-2" class="w-5 h-5 mr-3"></i> Delete
                                </button>
                            </div>
                        </div>
                    </td>
                `;
            } else {
                // For supervisors, add an empty cell
                rowContent += `<td class="py-4">-</td>`;
            }
            
            row.innerHTML = rowContent;
            tableBody.appendChild(row);
        });

        if (window.lucide) window.lucide.createIcons();
    }

    //fungsi panggil role di localStorage
    function getCurrentUserRole() {
        return localStorage.getItem('role') || 'default';
    }

    function showConfirmDelete(id) {
        masjidIdToDelete = id;
        const modal = document.getElementById('modalHapusMasjid');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
    }

    function closeConfirmDelete() {
        const modal = document.getElementById('modalHapusMasjid');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
        masjidIdToDelete = null;
    }

    async function hapusMasjid() {
        if (!masjidIdToDelete) return;
        
        try {
            const token = localStorage.getItem('token');
            const response = await fetch(`/api/mosques/${masjidIdToDelete}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                }
            });

            const data = await response.json();

            if (response.ok) {
                showAlert('success', 'Masjid deleted successfully!');
                loadMasjid();
            } else {
                throw new Error(data.message || 'Failed to delete masjid');
            }
        } catch (error) {
            console.error('Error deleting masjid:', error);
            showAlert('error', error.message);
        } finally {
            closeConfirmDelete();
        }
    }

    function editMasjid(id) {
        const masjid = allMasjid.find(m => m.id === id);
        if (!masjid) return;
        
        const editNama = document.getElementById('editNamaMasjid');
        const editAlamat = document.getElementById('editAlamatMasjid');
        const editId = document.getElementById('masjidIdToEdit');
        
        if (editNama) editNama.value = masjid.name;
        if (editAlamat) editAlamat.value = masjid.address || '';
        if (editId) editId.value = masjid.id;
        
        openModalEdit();
    }

    async function submitEditMasjid(e) {
        e.preventDefault();
        
        if (!validateEditForm()) {
            return;
        }
        
        const btnEdit = document.getElementById('btnEditMasjid');
        if (!btnEdit) return;
        
        const originalText = btnEdit.innerHTML;
        btnEdit.innerHTML = `<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Saving...`;
        btnEdit.disabled = true;
        
        const masjidId = document.getElementById('masjidIdToEdit')?.value;
        const token = localStorage.getItem('token');
        
        try {
            const response = await fetch(`/api/mosques/${masjidId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                body: JSON.stringify({
                    name: document.getElementById('editNamaMasjid')?.value,
                    address: document.getElementById('editAlamatMasjid')?.value
                })
            });
            
            const data = await response.json();
            
            if (response.ok) {
                showAlert('success', 'Masjid updated successfully!');
                loadMasjid();
                closeModalEdit();
            } else {
                throw new Error(data.message || 'Failed to update masjid');
            }
        } catch (error) {
            console.error('Error updating masjid:', error);
            showAlert('error', error.message);
        } finally {
            btnEdit.innerHTML = originalText;
            btnEdit.disabled = false;
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Form event listeners
        document.getElementById('formTambahMasjid')?.addEventListener('submit', function (e) {
        e.preventDefault();
        submitForm();
        });

        document.getElementById('btnTambahMasjid')?.addEventListener('click', function () {
            submitForm();
         });
    });
    
    async function submitForm() {
        if (!validateForm()) return;

        const btnTambah = document.getElementById('btnTambahMasjid');
        if (!btnTambah) return;
        
        const originalText = btnTambah.innerHTML;
        btnTambah.innerHTML = `<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Saving...`;
        btnTambah.disabled = true;

        const token = localStorage.getItem('token');

        try {
            const response = await fetch('/api/mosques', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                body: JSON.stringify({
                    name: document.getElementById('namaMasjid')?.value,
                    address: document.getElementById('alamatMasjid')?.value
                })
            });

            const result = await response.json();

            if (response.ok) {
                showAlert('success', 'Masjid added successfully!');
                resetForm();
                closeModalTambah();
                loadMasjid();
            } else {
                throw new Error(result.message || 'Failed to add masjid');
            }
        } catch (error) {
            console.error('Error adding masjid:', error);
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
        const modal = document.getElementById('modalTambahMasjid');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
    }

    function closeModalTambah() {
        const modal = document.getElementById('modalTambahMasjid');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    }
    
    function openModalEdit() {
        const modal = document.getElementById('modalEditMasjid');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
    }
    
    function closeModalEdit() {
        const modal = document.getElementById('modalEditMasjid');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    }

    function searchMasjid() {
        const searchInput = document.getElementById('searchInput');
        if (!searchInput) return;
        
        const searchTerm = searchInput.value.toLowerCase();
        
        if (searchTerm.trim() === '') {
            filteredMasjid = [...allMasjid];
        } else {
            filteredMasjid = allMasjid.filter(masjid => {
                return (
                    masjid.name.toLowerCase().includes(searchTerm) ||
                    (masjid.address && masjid.address.toLowerCase().includes(searchTerm))
                );
            });
        }
        
        renderMasjid();
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