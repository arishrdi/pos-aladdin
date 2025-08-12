@extends('layouts.app')

@section('title', 'Manajemen Staff')

@section('content')

<!-- Alert Notification -->
<div id="alertContainer" class="fixed top-4 right-4 z-50 space-y-3 w-80">
    <!-- Alert will appear here dynamically -->
</div>

<!-- Page Title + Action -->
<div class="mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <h1 class="text-2xl font-bold text-gray-800">Manajemen Staff</h1>
        <div class="flex items-center gap-2 w-full md:w-auto">
            <!-- Input dengan ikon pencarian -->
            <div class="relative w-full md:w-64">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i data-lucide="search" class="w-5 h-5 text-gray-400"></i>
                </span>
                <input type="text" id="searchInput" placeholder="Pencarian...."
                    class="w-full pl-10 pr-4 py-3 border rounded-lg text-base focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" />
            </div>

            <!-- Tombol Tambah Staff -->
            <a href="#" onclick="openModalTambahStaff()"
                class="px-5 py-3 text-base font-medium text-white bg-green-500 rounded-lg hover:bg-green-600 shadow">
                + Tambah Staff
            </a>
        </div>
    </div>
</div>

<!-- Card: Staff Info + Aksi -->
<div class="bg-white rounded-md p-4 shadow-md mb-4 flex flex-col md:flex-row md:items-center md:justify-between">
    <!-- Kiri: Judul -->
    <div class="mb-3 md:mb-0 flex items-start gap-2">
        <i data-lucide="users" class="w-5 h-5 text-gray-600 mt-1"></i>
        <div>
            <h2 class="text-lg font-semibold text-gray-800 outlet-name">Loading...</h2>
            <p class="text-sm text-gray-600">Kelola staff dan penugasan shift.</p>
        </div>
    </div>
</div>

<!-- Card: Tabel Staff -->
<div class="bg-white rounded-lg shadow-lg p-6">
    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full text-base">
            <thead class="text-left text-gray-700 border-b-2">
                <tr>
                    <th class="py-3 font-semibold">Nama</th>
                    <th class="py-3 font-semibold">Peran</th>
                    <th class="py-3 font-semibold">Shift</th>
                    <th class="py-3 font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody id="staffTableBody" class="text-gray-700 divide-y">
                <!-- Staff data will be loaded here -->
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Konfirmasi Hapus -->
<div id="modalHapusStaff" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl p-6 w-96">
        <div class="flex items-start gap-4">
            <div class="flex-shrink-0 p-2 bg-red-100 rounded-full">
                <i data-lucide="alert-triangle" class="w-6 h-6 text-red-600"></i>
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-semibold text-gray-900">Konfirmasi Hapus</h3>
                <div class="mt-2">
                    <p class="text-sm text-gray-600">Anda yakin ingin menghapus staff ini? Data yang dihapus tidak dapat dikembalikan.</p>
                </div>
                <div class="mt-4 flex justify-end gap-3">
                    <button id="btnBatalHapusStaff" type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none">
                        Batal
                    </button>
                    <button id="btnKonfirmasiHapusStaff" type="button" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md shadow-sm hover:bg-red-700 focus:outline-none">
                        Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@include('partials.staf.modal-tambah')
@include('partials.staf.modal-edit')

<script>
    let currentOutletId = 1; // Default outlet ID, adjust as needed
    let staffIdToDelete = null;
    let currentEditStaffId = null;
    let outletsList = [];

    // DOM Elements
    const modalHapusStaff = document.getElementById('modalHapusStaff');
    const batalBtnHapusStaff = document.getElementById('btnBatalHapusStaff');
    const konfirmasiBtnHapusStaff = document.getElementById('btnKonfirmasiHapusStaff');
    const staffTableBody = document.getElementById('staffTableBody');
    const searchInput = document.getElementById('searchInput');

    // Function to get currently selected outlet ID
    function getSelectedOutletId() {
        // First check URL parameters
        const urlParams = new URLSearchParams(window.location.search);
        const outletIdFromUrl = urlParams.get('outlet_id');
        
        if (outletIdFromUrl) {
            return outletIdFromUrl;
        }
        
        // Then check localStorage
        const savedOutletId = localStorage.getItem('selectedOutletId');
        
        if (savedOutletId) {
            return savedOutletId;
        }
        
        // Default to outlet ID 1 if nothing is found
        return 1;
    }

    // Initialize the page
    document.addEventListener('DOMContentLoaded', function() {
        // Get dynamic outlet ID
        currentOutletId = getSelectedOutletId();
        
        // Load staff data
        loadStaffData();
        
        // Initialize Lucide Icons
        if (window.lucide) {
            window.lucide.createIcons();
        }
        
        // Setup event listeners
        setupEventListeners();
        setupRealTimeSearch();
        
        // Connect outlet selection to staff management
        connectOutletSelectionToStaff();
        
        // Update outlet info display
        updateOutletInfoDisplay();
    });

    // Connect to outlet selection dropdown
    function connectOutletSelectionToStaff() {
        // Listen for outlet changes in localStorage
        window.addEventListener('storage', function(event) {
            if (event.key === 'selectedOutletId') {
                currentOutletId = event.newValue;
                loadStaffData();
                updateOutletInfoDisplay();
            }
        });
        
        // Also watch for clicks on outlet items in dropdown
        const outletListContainer = document.getElementById('outletListContainer');
        if (outletListContainer) {
            outletListContainer.addEventListener('click', function(event) {
                // Find the clicked li element
                let targetElement = event.target;
                while (targetElement && targetElement !== outletListContainer && targetElement.tagName !== 'LI') {
                    targetElement = targetElement.parentElement;
                }
                
                // If we clicked on an outlet list item
                if (targetElement && targetElement.tagName === 'LI') {
                    // Update staff data after a short delay to allow your existing code to complete
                    setTimeout(() => {
                        currentOutletId = getSelectedOutletId();
                        loadStaffData();
                        updateOutletInfoDisplay();
                    }, 100);
                }
            });
        }
        
        // Watch for direct outlet selection (if there's a direct outlet selector on this page)
        const outletSelector = document.getElementById('outletSelector');
        if (outletSelector) {
            outletSelector.addEventListener('change', function(event) {
                currentOutletId = event.target.value;
                localStorage.setItem('selectedOutletId', currentOutletId);
                loadStaffData();
                updateOutletInfoDisplay();
            });
        }
    }

    // Update outlet info display
    async function updateOutletInfoDisplay() {
        try {
            const response = await fetch(`/api/outlets/${currentOutletId}`, {
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('token')}`,
                    'Accept': 'application/json'
                }
            });
            
            const { data, success } = await response.json();
            
            if (success && data) {
                // Update outlet name displays
                const outletElements = document.querySelectorAll('.outlet-name');
                outletElements.forEach(el => {
                    el.textContent = `Daftar staff: ${data.name}`;
                });
                
                // Update outlet address displays
                const addressElements = document.querySelectorAll('.outlet-address');
                addressElements.forEach(el => {
                    el.textContent = data.address || '';
                });
                
                // Update page title if exists
                const pageTitleElement = document.querySelector('.page-title');
                if (pageTitleElement) {
                    pageTitleElement.textContent = `Staff Management - ${data.name}`;
                }
                
                // Update any outlet info cards
                const outletInfoCards = document.querySelectorAll('.outlet-info-card');
                outletInfoCards.forEach(card => {
                    const nameElement = card.querySelector('.outlet-card-name');
                    const addressElement = card.querySelector('.outlet-card-address');
                    
                    if (nameElement) nameElement.textContent = data.name;
                    if (addressElement) addressElement.textContent = data.address || 'Alamat tidak tersedia';
                });
            }
        } catch (error) {
            console.error('Failed to fetch outlet details:', error);
        }
    }

    async function loadProductData(outletId) {
        try {
            // Update current outlet ID
            currentOutletId = outletId;
            
            // Sembunyikan dropdown outlet setelah memilih
            const outletDropdown = document.getElementById('outletDropdown');
            if (outletDropdown) outletDropdown.classList.add('hidden');
            
            // Load staff data for the selected outlet
            loadStaffData();
            
            // Update outlet info display
            updateOutletInfoDisplay();
            
            // Jika perlu menyimpan outlet yang dipilih
            localStorage.setItem('selectedOutletId', outletId);
            
        } catch (error) {
            console.error('Error loading product data:', error);
            showAlert('error', 'Gagal memuat data outlet');
        }
    }

    // Tambahkan fungsi ini di bagian setup atau fungsi inisialisasi
    async function loadOutlets() {
        try {
            const outletSelect = document.getElementById('outletStaff');
            const url = outletSelect.getAttribute('data-url');
            
            // Set loading state
            outletSelect.innerHTML = '<option value="" disabled selected>Memuat outlet...</option>';
            
            const response = await fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${localStorage.getItem('token')}`
                }
            });
            
            if (!response.ok) throw new Error('Gagal memuat outlet');
            
            const data = await response.json();
            
            if (data.success && data.data.length > 0) {
                outletSelect.innerHTML = '<option value="" disabled selected>Pilih Outlet</option>';
                
                data.data.forEach(outlet => {
                    outletSelect.innerHTML += `<option value="${outlet.id}">${outlet.name}</option>`;
                });
                
                // Set default value to current outlet
                if (currentOutletId) {
                    outletSelect.value = currentOutletId;
                }
            } else {
                outletSelect.innerHTML = '<option value="" disabled selected>Tidak ada outlet tersedia</option>';
            }
        } catch (error) {
            console.error('Error loading outlets:', error);
            const outletSelect = document.getElementById('outletStaff');
            outletSelect.innerHTML = '<option value="" disabled selected>Gagal memuat outlet</option>';
            showAlert('error', 'Gagal memuat daftar outlet');
        }
    }

    async function loadOutletsForEdit() {
        try {
            const outletSelect = document.getElementById('editOutletStaff');
            const url = outletSelect.getAttribute('data-url');
            
            const response = await fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${localStorage.getItem('token')}`
                }
            });
            
            if (!response.ok) throw new Error('Gagal memuat outlet');
            
            const data = await response.json();
            
            if (data.success && data.data.length > 0) {
                outletSelect.innerHTML = '';
                
                data.data.forEach(outlet => {
                    outletSelect.innerHTML += `<option value="${outlet.id}">${outlet.name}</option>`;
                });
            } else {
                outletSelect.innerHTML = '<option value="" disabled selected>Tidak ada outlet tersedia</option>';
            }
        } catch (error) {
            console.error('Error loading outlets:', error);
            const outletSelect = document.getElementById('editOutletStaff');
            outletSelect.innerHTML = '<option value="" disabled selected>Gagal memuat outlet</option>';
        }
    }

    // Setup event listeners
    function setupEventListeners() {
        // Delete confirmation modal
        batalBtnHapusStaff.addEventListener('click', closeConfirmDeleteStaff);
        konfirmasiBtnHapusStaff.addEventListener('click', hapusStaff);
        
        // Add staff form submission
        document.getElementById('btnTambahStaff').addEventListener('click', tambahStaff);
        
        // Edit staff form submission
        document.getElementById('btnSimpanEditStaff').addEventListener('click', updateStaff);
    }

    // Load staff data from API
    function loadStaffData() {
        // Show loading state in the table
        staffTableBody.innerHTML = `
            <tr>
                <td colspan="4" class="py-8 text-center">
                    <div class="flex flex-col items-center justify-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                            class="animate-spin text-green-500">
                            <path d="M21 12a9 9 0 1 1-6.219-8.56"/>
                        </svg>
                        <span class="text-gray-500">Memuat data staff...</span>
                    </div>
                </td>
            </tr>
        `;

        console.log(`Loading staff data for outlet ID: ${currentOutletId}`);
        
        fetch(`/api/user/all/${currentOutletId}`, {
            headers: {
                'Accept': 'application/json',
                'Authorization': `Bearer ${localStorage.getItem('token')}`
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                renderStaffTable(data.data);
                
                // Update outlet information if available in response
                if (data.data.length > 0 && data.data[0].outlet) {
                    updateOutletInfoFromData(data.data[0].outlet);
                }
            } else {
                showAlert('error', 'Gagal memuat data staff');
                staffTableBody.innerHTML = `
                    <tr>
                        <td colspan="4" class="py-4 text-center text-red-500">
                            Gagal memuat data staff
                        </td>
                    </tr>
                `;
            }
        })
        .catch(error => {
            console.error('Error loading staff data:', error);
            showAlert('error', 'Gagal memuat data staff');
            staffTableBody.innerHTML = `
                <tr>
                    <td colspan="4" class="py-4 text-center text-red-500">
                        Terjadi kesalahan saat memuat data
                    </td>
                </tr>
            `;
        });
    }

    // Update outlet info from staff data response
    function updateOutletInfoFromData(outletData) {
        // Update outlet name displays
        const outletElements = document.querySelectorAll('.outlet-name');
        outletElements.forEach(el => {
            el.textContent = `Daftar staff: ${outletData.name}`;
        });
        
        // Update outlet address displays
        const addressElements = document.querySelectorAll('.outlet-address');
        addressElements.forEach(el => {
            el.textContent = outletData.address || '';
        });
    }

    // Render staff table
    function renderStaffTable(staffList) {
        staffTableBody.innerHTML = '';
        
        if (staffList.length === 0) {
            staffTableBody.innerHTML = `
                <tr>
                    <td colspan="4" class="py-8 text-center text-gray-500">
                        <div class="flex flex-col items-center gap-2">
                            <i data-lucide="users" class="w-8 h-8 text-gray-400"></i>
                            <span>Tidak ada data staff untuk outlet ini</span>
                            <span class="text-sm text-gray-400">Klik "Tambah Staff" untuk menambahkan staff baru</span>
                        </div>
                    </td>
                </tr>
            `;
            
            // Refresh Lucide icons for the empty state
            if (window.lucide) {
                window.lucide.createIcons();
            }
            return;
        }
        
        staffList.forEach(staff => {
            const roleClass = {
                'admin': 'bg-green-100 text-green-700',
                'kasir': 'bg-blue-100 text-blue-700',
                'supervisor': 'bg-purple-100 text-purple-700'
            }[staff.role] || 'bg-gray-100 text-gray-700';
            
            const shiftTime = staff.last_shift ? 
                `${staff.last_shift.start_time || '--:--'} - ${staff.last_shift.end_time || '--:--'}` : 
                '--:-- - --:--';
            
            // Build outlets display
            let outletsDisplay = '';
            if (staff.role === 'supervisor' && staff.outlets && staff.outlets.length > 0) {
                const outletNames = staff.outlets.map(o => o.name).join(', ');
                outletsDisplay = `<div class="text-xs text-gray-400">Outlets: ${outletNames}</div>`;
            } else if (staff.outlet) {
                outletsDisplay = `<div class="text-xs text-gray-400">Outlet: ${staff.outlet.name}</div>`;
            }

            staffTableBody.innerHTML += `
                <tr data-id="${staff.id}" class="border-b hover:bg-gray-50 transition-colors">
                    <td class="py-4">
                        <div class="flex items-center gap-4">
                            <div class="bg-green-100 p-2 rounded-full">
                                <i data-lucide="user" class="w-6 h-6 text-green-500"></i>
                            </div>
                            <div>
                                <div class="font-semibold text-base text-gray-900">${staff.name}</div>
                                <div class="text-sm text-gray-500">${staff.email}</div>
                                ${outletsDisplay}
                            </div>
                        </div>
                    </td>
                    <td class="py-4">
                        <span class="px-3 py-1.5 text-sm font-medium ${roleClass} rounded-full capitalize">${staff.role}</span>
                        ${staff.role === 'supervisor' && staff.outlets ? `<div class="text-xs text-gray-400 mt-1">${staff.outlets.length} outlet(s)</div>` : ''}
                    </td>
                    <td class="py-4">
                        <span class="text-sm text-gray-600">${shiftTime}</span>
                    </td>
                    <td class="py-4 relative">
                        <div class="relative inline-block">
                            <button onclick="toggleDropdown(this)" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                                <i data-lucide="more-vertical" class="w-5 h-5 text-gray-500"></i>
                            </button>
                            <!-- Dropdown -->
                            <div class="dropdown-menu hidden absolute right-0 z-20 mt-1 w-40 bg-white border border-gray-200 rounded-lg shadow-xl text-base">
                                <button onclick="editStaff(${staff.id})" class="flex items-center w-full px-4 py-2.5 hover:bg-gray-100 text-left rounded-t-lg transition-colors">
                                    <i data-lucide="pencil" class="w-5 h-5 mr-3 text-gray-500"></i> Edit
                                </button>
                                <button onclick="showConfirmDelete(${staff.id})" class="flex items-center w-full px-4 py-2.5 hover:bg-gray-100 text-left text-red-600 rounded-b-lg transition-colors">
                                    <i data-lucide="trash-2" class="w-5 h-5 mr-3"></i> Hapus
                                </button>
                            </div>
                        </div>
                    </td>
                </tr>
            `;
        });
        
        // Refresh Lucide icons
        if (window.lucide) {
            window.lucide.createIcons();
        }
    }

    // Setup real-time search
    function setupRealTimeSearch() {
        searchInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('#staffTableBody tr');
            
            rows.forEach(row => {
                const nama = row.querySelector('td:first-child .font-semibold').textContent.toLowerCase();
                const email = row.querySelector('td:first-child .text-sm').textContent.toLowerCase();
                const peran = row.querySelector('td:nth-child(2) span').textContent.toLowerCase();
                const shift = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
                
                if (nama.includes(searchTerm) || 
                    email.includes(searchTerm) || 
                    peran.includes(searchTerm) || 
                    shift.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }

    // Show alert
    function showAlert(type, message) {
        const alertContainer = document.getElementById('alertContainer');
        const alertId = 'alert-' + Date.now();
        
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
        
        const alertElement = document.createElement('div');
        alertElement.id = alertId;
        alertElement.className = `p-4 border rounded-lg shadow-sm ${config.bgColor} ${config.borderColor} ${config.textColor} flex items-start gap-3 animate-fade-in-up`;
        alertElement.innerHTML = `
            <i data-lucide="${config.icon}" class="w-5 h-5 mt-0.5 ${config.iconColor}"></i>
            <div class="flex-1">
                <p class="text-sm font-medium">${message}</p>
            </div>
            <button onclick="closeAlert('${alertId}')" class="p-1 rounded-full hover:bg-gray-100">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        `;
        
        alertContainer.prepend(alertElement);
        
        if (window.lucide) {
            window.lucide.createIcons();
        }
        
        setTimeout(() => {
            closeAlert(alertId);
        }, 5000);
    }

    // Close alert
    function closeAlert(id) {
        const alert = document.getElementById(id);
        if (alert) {
            alert.classList.add('animate-fade-out');
            setTimeout(() => {
                alert.remove();
            }, 300);
        }
    }

    // Toggle dropdown
    function toggleDropdown(button) {
        const menu = button.nextElementSibling;

        document.querySelectorAll('.dropdown-menu').forEach(m => {
            if (m !== menu) {
                m.classList.add('hidden');
                m.classList.remove('dropdown-up');
                m.classList.remove('dropdown-down');
            }
        });

        menu.classList.toggle('hidden');
        menu.classList.remove('dropdown-up', 'dropdown-down');

        const menuRect = menu.getBoundingClientRect();
        const buttonRect = button.getBoundingClientRect();
        const spaceBelow = window.innerHeight - buttonRect.bottom;
        const spaceAbove = buttonRect.top;

        if (spaceBelow < menuRect.height && spaceAbove > menuRect.height) {
            menu.classList.add('dropdown-up');
        } else {
            menu.classList.add('dropdown-down');
        }
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function (e) {
        if (!e.target.closest('.relative.inline-block')) {
            document.querySelectorAll('.dropdown-menu').forEach(menu => {
                menu.classList.add('hidden');
                menu.classList.remove('dropdown-up');
                menu.classList.remove('dropdown-down');
            });
        }
    });

    // Modal functions
    function openModalTambahStaff() {
        document.getElementById('modalTambahStaff').classList.remove('hidden');
        document.getElementById('modalTambahStaff').classList.add('flex');

        loadOutlets();
        setupRoleChangeHandler();
    }

    function closeModalTambahStaff() {
        document.getElementById('modalTambahStaff').classList.add('hidden');
        document.getElementById('modalTambahStaff').classList.remove('flex');
        resetTambahStaffForm();
    }

    function resetTambahStaffForm() {
        document.getElementById('namaStaff').value = '';
        document.getElementById('emailStaff').value = '';
        document.getElementById('passwordStaff').value = '';
        document.getElementById('peranStaff').value = 'kasir';
        document.getElementById('waktuMulai').value = '';
        document.getElementById('waktuSelesai').value = '';
        
        // Clear error messages
        document.querySelectorAll('[id^="error"]').forEach(el => {
            el.classList.add('hidden');
        });
        
        // Remove error borders
        document.querySelectorAll('.border-red-500').forEach(el => {
            el.classList.remove('border-red-500');
        });
    }

    function openModalEditStaff(id) {
        currentEditStaffId = id;
        
        // Setup role change handler and load outlets first
        setupRoleChangeHandler();
        loadOutletsForEdit().then(() => {
            // Then fetch staff data
            fetch(`/api/user/all/${currentOutletId}`, {
            headers: {
                'Accept': 'application/json',
                'Authorization': `Bearer ${localStorage.getItem('token')}`
            }
            })
            .then(response => response.json())
            .then(data => {
            if (data.success) {
                const staff = data.data.find(s => s.id == id);
                if (staff) {
                document.getElementById('editNamaStaff').value = staff.name;
                document.getElementById('editEmailStaff').value = staff.email;
                document.getElementById('editPeranStaff').value = staff.role;
                
                // Handle outlet selection based on role
                if (staff.role === 'supervisor') {
                    // Load supervisor outlets and set selected ones
                    loadOutletsForEditSupervisor().then(() => {
                        const supervisorOutletIds = staff.outlets ? staff.outlets.map(o => o.id) : [];
                        setSelectedEditSupervisorOutlets(supervisorOutletIds);
                        handleEditRoleChange(); // Show the correct section
                    });
                } else {
                    document.getElementById('editOutletStaff').value = staff.outlet_id;
                    handleEditRoleChange(); // Show the correct section
                }
                
                if (staff.last_shift) {
                    const startTime = staff.last_shift.start_time ? 
                    staff.last_shift.start_time.substring(0, 5) : '';
                    const endTime = staff.last_shift.end_time ? 
                    staff.last_shift.end_time.substring(0, 5) : '';
                    
                    document.getElementById('editWaktuMulai').value = startTime;
                    document.getElementById('editWaktuSelesai').value = endTime;
                }
                
                document.getElementById('modalEditStaff').classList.remove('hidden');
                document.getElementById('modalEditStaff').classList.add('flex');
                }
            }
            })
            .catch(error => {
            console.error('Error fetching staff data:', error);
            showAlert('error', 'Gagal memuat data staff');
            });
        });
    }

    function closeModalEditStaff() {
        document.getElementById('modalEditStaff').classList.add('hidden');
        document.getElementById('modalEditStaff').classList.remove('flex');
        currentEditStaffId = null;
    }

    function showConfirmDelete(id) {
        staffIdToDelete = id;
        modalHapusStaff.classList.remove('hidden');
        modalHapusStaff.classList.add('flex');
    }

    function closeConfirmDeleteStaff() {
        modalHapusStaff.classList.add('hidden');
        modalHapusStaff.classList.remove('flex');
        staffIdToDelete = null;
    }

    // Form validation
    function validateFormStaff() {
        let isValid = true;
        
        // Validate name
        const namaStaff = document.getElementById('namaStaff');
        const errorNamaStaff = document.getElementById('errorNamaStaff');
        if (!namaStaff.value.trim()) {
            errorNamaStaff.classList.remove('hidden');
            namaStaff.classList.add('border-red-500');
            isValid = false;
        } else {
            errorNamaStaff.classList.add('hidden');
            namaStaff.classList.remove('border-red-500');
        }
        
        // Validate email
        const emailStaff = document.getElementById('emailStaff');
        const errorEmailStaff = document.getElementById('errorEmailStaff');
        if (!emailStaff.value.trim() || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailStaff.value)) {
            errorEmailStaff.classList.remove('hidden');
            emailStaff.classList.add('border-red-500');
            isValid = false;
        } else {
            errorEmailStaff.classList.add('hidden');
            emailStaff.classList.remove('border-red-500');
        }
        
        // Validate password
        const passwordStaff = document.getElementById('passwordStaff');
        const errorPasswordStaff = document.getElementById('errorPasswordStaff');
        if (!passwordStaff.value.trim() || passwordStaff.value.length < 6) {
            errorPasswordStaff.classList.remove('hidden');
            passwordStaff.classList.add('border-red-500');
            isValid = false;
        } else {
            errorPasswordStaff.classList.add('hidden');
            passwordStaff.classList.remove('border-red-500');
        }
        
        // Validate role
        const peranStaff = document.getElementById('peranStaff');
        const errorPeranStaff = document.getElementById('errorPeranStaff');
        if (!peranStaff.value) {
            errorPeranStaff.classList.remove('hidden');
            peranStaff.classList.add('border-red-500');
            isValid = false;
        } else {
            errorPeranStaff.classList.add('hidden');
            peranStaff.classList.remove('border-red-500');
        }

        // Validate outlet based on role
        const role = document.getElementById('peranStaff').value;
        
        if (role === 'supervisor') {
            // Validate supervisor outlets
            const selectedOutlets = getSelectedSupervisorOutlets();
            const errorSupervisorOutlets = document.getElementById('errorSupervisorOutlets');
            
            if (selectedOutlets.length === 0) {
                errorSupervisorOutlets.classList.remove('hidden');
                isValid = false;
            } else {
                errorSupervisorOutlets.classList.add('hidden');
            }
        } else {
            // Validate single outlet for kasir/admin
            const outletStaff = document.getElementById('outletStaff');
            const errorOutletStaff = document.getElementById('errorOutletStaff');
            
            if (!outletStaff.value) {
                errorOutletStaff.classList.remove('hidden');
                outletStaff.classList.add('border-red-500');
                isValid = false;
            } else {
                errorOutletStaff.classList.add('hidden');
                outletStaff.classList.remove('border-red-500');
            }
        }
        
        return isValid;
    }

    function validateEditFormStaff() {
        let isValid = true;
        
        // Validate name
        const namaStaff = document.getElementById('editNamaStaff');
        const errorNamaStaff = document.getElementById('errorEditNamaStaff');
        if (!namaStaff.value.trim()) {
            errorNamaStaff.classList.remove('hidden');
            namaStaff.classList.add('border-red-500');
            isValid = false;
        } else {
            errorNamaStaff.classList.add('hidden');
            namaStaff.classList.remove('border-red-500');
        }
        
        // Validate email
        const emailStaff = document.getElementById('editEmailStaff');
        const errorEmailStaff = document.getElementById('errorEditEmailStaff');
        if (!emailStaff.value.trim() || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailStaff.value)) {
            errorEmailStaff.classList.remove('hidden');
            emailStaff.classList.add('border-red-500');
            isValid = false;
        } else {
            errorEmailStaff.classList.add('hidden');
            emailStaff.classList.remove('border-red-500');
        }
        
        // Validate password (optional)
        const passwordStaff = document.getElementById('editPasswordStaff');
        const errorPasswordStaff = document.getElementById('errorEditPasswordStaff');
        if (passwordStaff.value.trim() && passwordStaff.value.length < 6) {
            errorPasswordStaff.classList.remove('hidden');
            passwordStaff.classList.add('border-red-500');
            isValid = false;
        } else {
            errorPasswordStaff.classList.add('hidden');
            passwordStaff.classList.remove('border-red-500');
        }
        
        // Validate role
        const peranStaff = document.getElementById('editPeranStaff');
        const errorPeranStaff = document.getElementById('errorEditPeranStaff');
        if (!peranStaff.value) {
            errorPeranStaff.classList.remove('hidden');
            peranStaff.classList.add('border-red-500');
            isValid = false;
        } else {
            errorPeranStaff.classList.add('hidden');
            peranStaff.classList.remove('border-red-500');
        }

        // Validate outlet based on role
        const role = document.getElementById('editPeranStaff').value;
        
        if (role === 'supervisor') {
            // Validate supervisor outlets
            const selectedOutlets = getSelectedEditSupervisorOutlets();
            const errorSupervisorOutlets = document.getElementById('errorEditSupervisorOutlets');
            
            if (selectedOutlets.length === 0) {
                errorSupervisorOutlets.classList.remove('hidden');
                isValid = false;
            } else {
                errorSupervisorOutlets.classList.add('hidden');
            }
        } else {
            // Validate single outlet for kasir/admin
            const outletStaff = document.getElementById('editOutletStaff');
            const errorOutletStaff = document.getElementById('errorEditOutletStaff');
            
            if (!outletStaff.value) {
                errorOutletStaff.classList.remove('hidden');
                outletStaff.classList.add('border-red-500');
                isValid = false;
            } else {
                errorOutletStaff.classList.add('hidden');
                outletStaff.classList.remove('border-red-500');
            }
        }
        
        return isValid;
    }

    // CRUD Operations
    function tambahStaff() {
        if (!validateFormStaff()) return;

        const btnTambah = document.getElementById('btnTambahStaff');
        const originalText = btnTambah.innerHTML;
        
        // Show loading state
        btnTambah.innerHTML = `
            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Menyimpan...
        `;
        btnTambah.disabled = true;

        const role = document.getElementById('peranStaff').value;
        const formData = {
            name: document.getElementById('namaStaff').value,
            email: document.getElementById('emailStaff').value,
            password: document.getElementById('passwordStaff').value,
            role: role,
            start_time: document.getElementById('waktuMulai').value,
            end_time: document.getElementById('waktuSelesai').value
        };

        // Handle outlets based on role
        if (role === 'supervisor') {
            const selectedOutlets = getSelectedSupervisorOutlets();
            if (selectedOutlets.length > 0) {
                formData.outlet_ids = selectedOutlets;
                formData.outlet_id = selectedOutlets[0]; // Primary outlet
            }
        } else {
            formData.outlet_id = document.getElementById('outletStaff').value;
        }

        fetch('/api/user/register', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'Authorization': `Bearer ${localStorage.getItem('token')}`
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', 'Staff berhasil ditambahkan!');
                closeModalTambahStaff();
                loadStaffData();
            } else {
                showAlert('error', data.message || 'Gagal menambahkan staff');
            }
        })
        .catch(error => {
            console.error('Error adding staff:', error);
            showAlert('error', 'Terjadi kesalahan saat menambahkan staff');
        })
        .finally(() => {
            btnTambah.innerHTML = originalText;
            btnTambah.disabled = false;
        });
    }

    function updateStaff() {
        if (!validateEditFormStaff() || !currentEditStaffId) return;

        const btnEdit = document.getElementById('btnSimpanEditStaff');
        const originalText = btnEdit.innerHTML;
        
        // Show loading state
        btnEdit.innerHTML = `
            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Menyimpan...
        `;
        btnEdit.disabled = true;

        const role = document.getElementById('editPeranStaff').value;
        const formData = {
            name: document.getElementById('editNamaStaff').value,
            email: document.getElementById('editEmailStaff').value,
            role: role,
            start_time: document.getElementById('editWaktuMulai').value,
            end_time: document.getElementById('editWaktuSelesai').value
        };

        // Handle outlets based on role
        if (role === 'supervisor') {
            const selectedOutlets = getSelectedEditSupervisorOutlets();
            if (selectedOutlets.length > 0) {
                formData.outlet_ids = selectedOutlets;
                formData.outlet_id = selectedOutlets[0]; // Primary outlet
            }
        } else {
            formData.outlet_id = document.getElementById('editOutletStaff').value;
        }

        // Only include password if it's not empty
        const password = document.getElementById('editPasswordStaff').value;
        if (password.trim()) {
            formData.password = password;
        }

        fetch(`/api/user/update/${currentEditStaffId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'Authorization': `Bearer ${localStorage.getItem('token')}`
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', 'Data staff berhasil diperbarui!');
                closeModalEditStaff();
                loadStaffData();
            } else {
                showAlert('error', data.message || 'Gagal memperbarui data staff');
            }
        })
        .catch(error => {
            console.error('Error updating staff:', error);
            showAlert('error', 'Terjadi kesalahan saat memperbarui staff');
        })
        .finally(() => {
            btnEdit.innerHTML = originalText;
            btnEdit.disabled = false;
        });
    }

     document.getElementById('btnBatalModalTambahStaff').addEventListener('click', closeModalTambahStaff);

  function closeModalTambahStaff() {
    document.getElementById('modalTambahStaff').classList.add('hidden');
  }

  document.getElementById('btnBatalModalEditStaff').addEventListener('click', closeModalEditStaff);

function closeModalEditStaff() {
  document.getElementById('modalEditStaff').classList.add('hidden');
}

    function hapusStaff() {
        if (!staffIdToDelete) return;

        const btnHapus = document.getElementById('btnKonfirmasiHapusStaff');
        const originalText = btnHapus.innerHTML;
        
        // Show loading state
        btnHapus.innerHTML = `
            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Menghapus...
        `;
        btnHapus.disabled = true;

        fetch(`/api/user/delete/${staffIdToDelete}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'Authorization': `Bearer ${localStorage.getItem('token')}`
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', 'Staff berhasil dihapus!');
                closeConfirmDeleteStaff();
                loadStaffData();
            } else {
                showAlert('error', data.message || 'Gagal menghapus staff');
            }
        })
        .catch(error => {
            console.error('Error deleting staff:', error);
            showAlert('error', 'Terjadi kesalahan saat menghapus staff');
        })
        .finally(() => {
            btnHapus.innerHTML = originalText;
            btnHapus.disabled = false;
        });
    }

    // Helper functions
    function editStaff(id) {
        openModalEditStaff(id);
    }

    // Styles for animations
    const styleElement = document.createElement('style');
    styleElement.textContent = `
        /* Animasi untuk alert */
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
        
        /* Style untuk dropdown */
        .dropdown-up {
            bottom: 100%;
            top: auto;
            margin-bottom: 5px;
        }
        
        .dropdown-down {
            top: 100%;
            bottom: auto;
            margin-top: 5px;
        }
    `;
    document.head.appendChild(styleElement);

    // Multiple outlets functionality for supervisors
    function setupRoleChangeHandler() {
        const roleSelect = document.getElementById('peranStaff');
        const editRoleSelect = document.getElementById('editPeranStaff');
        
        if (roleSelect) {
            roleSelect.addEventListener('change', handleRoleChange);
        }
        
        if (editRoleSelect) {
            editRoleSelect.addEventListener('change', handleEditRoleChange);
        }
    }

    function handleRoleChange() {
        const role = document.getElementById('peranStaff').value;
        const singleOutletSection = document.getElementById('singleOutletSection');
        const multipleOutletSection = document.getElementById('multipleOutletSection');

        if (role === 'supervisor') {
            singleOutletSection.classList.add('hidden');
            multipleOutletSection.classList.remove('hidden');
            loadOutletsForSupervisor();
        } else {
            singleOutletSection.classList.remove('hidden');
            multipleOutletSection.classList.add('hidden');
            clearSupervisorOutlets();
        }
    }

    function handleEditRoleChange() {
        const role = document.getElementById('editPeranStaff').value;
        const singleOutletSection = document.getElementById('editSingleOutletSection');
        const multipleOutletSection = document.getElementById('editMultipleOutletSection');

        if (role === 'supervisor') {
            singleOutletSection.classList.add('hidden');
            multipleOutletSection.classList.remove('hidden');
            loadOutletsForEditSupervisor();
        } else {
            singleOutletSection.classList.remove('hidden');
            multipleOutletSection.classList.add('hidden');
            clearEditSupervisorOutlets();
        }
    }

    async function loadOutletsForSupervisor() {
        try {
            const response = await fetch('/api/outlets', {
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${localStorage.getItem('token')}`
                }
            });

            if (!response.ok) throw new Error('Gagal memuat outlet');

            const data = await response.json();
            populateSupervisorOutlets(data.data || []);
        } catch (error) {
            console.error('Error loading outlets for supervisor:', error);
            showAlert('error', 'Gagal memuat daftar outlet untuk supervisor');
        }
    }

    async function loadOutletsForEditSupervisor() {
        try {
            const response = await fetch('/api/outlets', {
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${localStorage.getItem('token')}`
                }
            });

            if (!response.ok) throw new Error('Gagal memuat outlet');

            const data = await response.json();
            populateEditSupervisorOutlets(data.data || []);
        } catch (error) {
            console.error('Error loading outlets for edit supervisor:', error);
            showAlert('error', 'Gagal memuat daftar outlet untuk supervisor');
        }
    }

    function populateSupervisorOutlets(outlets) {
        const container = document.getElementById('supervisorOutletsContainer');
        container.innerHTML = '';

        outlets.forEach(outlet => {
            const checkboxDiv = document.createElement('div');
            checkboxDiv.className = 'flex items-center';
            checkboxDiv.innerHTML = `
                <input type="checkbox" id="supervisor_outlet_${outlet.id}" value="${outlet.id}" 
                       class="supervisor-outlet-checkbox h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                <label for="supervisor_outlet_${outlet.id}" class="ml-2 block text-sm text-gray-700 cursor-pointer">
                    ${outlet.name}
                </label>
            `;
            container.appendChild(checkboxDiv);
        });
    }

    function populateEditSupervisorOutlets(outlets) {
        const container = document.getElementById('editSupervisorOutletsContainer');
        container.innerHTML = '';

        outlets.forEach(outlet => {
            const checkboxDiv = document.createElement('div');
            checkboxDiv.className = 'flex items-center';
            checkboxDiv.innerHTML = `
                <input type="checkbox" id="edit_supervisor_outlet_${outlet.id}" value="${outlet.id}" 
                       class="edit-supervisor-outlet-checkbox h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                <label for="edit_supervisor_outlet_${outlet.id}" class="ml-2 block text-sm text-gray-700 cursor-pointer">
                    ${outlet.name}
                </label>
            `;
            container.appendChild(checkboxDiv);
        });
    }

    function clearSupervisorOutlets() {
        const container = document.getElementById('supervisorOutletsContainer');
        if (container) container.innerHTML = '';
    }

    function clearEditSupervisorOutlets() {
        const container = document.getElementById('editSupervisorOutletsContainer');
        if (container) container.innerHTML = '';
    }

    function getSelectedSupervisorOutlets() {
        const checkboxes = document.querySelectorAll('.supervisor-outlet-checkbox:checked');
        return Array.from(checkboxes).map(checkbox => parseInt(checkbox.value));
    }

    function getSelectedEditSupervisorOutlets() {
        const checkboxes = document.querySelectorAll('.edit-supervisor-outlet-checkbox:checked');
        return Array.from(checkboxes).map(checkbox => parseInt(checkbox.value));
    }

    function setSelectedEditSupervisorOutlets(outletIds) {
        // Clear all checkboxes first
        document.querySelectorAll('.edit-supervisor-outlet-checkbox').forEach(checkbox => {
            checkbox.checked = false;
        });

        // Check the selected outlets
        outletIds.forEach(outletId => {
            const checkbox = document.getElementById(`edit_supervisor_outlet_${outletId}`);
            if (checkbox) {
                checkbox.checked = true;
            }
        });
    }
</script>

@endsection