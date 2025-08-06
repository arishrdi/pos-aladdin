@extends('layouts.app')

@section('title', 'Manajemen Kategori')

@section('content')

<!-- Alert Notification -->
<div id="alertContainer" class="fixed top-4 right-4 z-50 space-y-3 w-80">
    <!-- Alert will appear here dynamically -->
</div>

@include('partials.kategori.modal-konfirmasi-hapus')

<!-- Page Title + Action -->
<div class="mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2">
        <h1 class="text-2xl font-bold text-gray-800">Manajemen Kategori</h1>
        <div class="flex items-center gap-2">
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 103.5 3.5a7.5 7.5 0 0013.65 13.65z" />
                    </svg>
                </span>
                <input 
                    type="text" 
                    placeholder="Cari kategori..." 
                    class="w-full pl-10 pr-4 py-3 border rounded-lg text-base focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    id="searchKategori"
                />
            </div>
            <button 
                onclick="openModal('modalTambahKategori')" 
                class="px-5 py-3 text-base font-medium text-white bg-green-500 rounded-lg hover:bg-green-600 shadow"
            >
                + Tambah Kategori
            </button>
        </div>
    </div>
</div>

<!-- Card: Outlet Info -->
<div class="bg-white rounded-lg p-4 shadow mb-4 flex flex-col md:flex-row md:items-center md:justify-between">
    <div class="mb-4 md:mb-0 flex items-start gap-2">
        <i data-lucide="store" class="w-5 h-5 text-gray-600"></i>
        <div>
            <h2 class="text-lg font-semibold text-gray-800">Menampilkan daftar kategori</h2>
            <p class="text-sm text-gray-600">Data yang ditampilkan adalah kategori produk.</p>
        </div>
    </div>
</div>

<!-- Card: Tabel Kategori -->
<div class="bg-white rounded-lg shadow p-4">
    <!-- Header Table -->
    <div class="mb-4">
        <h1 class="font-medium text-[20px] text-gray-800">Daftar Kategori</h1>
        <span class="font-medium text-gray-500">Kelola kategori produk yang tersedia di toko Anda</span>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full text-base">
            <thead class="text-left text-gray-600 border-b">
                <tr>
                    <th class="py-2 font-semibold">No.</th>
                    <th class="py-2 font-semibold">Kategori</th>
                    <th class="py-2 font-semibold">Deskripsi</th>
                    <th class="py-2 font-semibold">Jumlah Produk</th>
                    <th class="py-2 font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-700" id="kategoriTableBody">
                <!-- Loading indicator -->
                <tr class="border-b">
                    <td colspan="5" class="py-8 text-center">
                        <div class="flex flex-col items-center justify-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="animate-spin text-green-500">
                                <path d="M21 12a9 9 0 1 1-6.219-8.56"/>
                            </svg>
                            <span class="text-gray-500">Memuat data kategori...</span>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

@include('partials.kategori.modal-tambah-kategori')
@include('partials.kategori.modal-edit-kategori')

<script>
    // Global variables
    let kategoriHapusId = null;
    
    // Initialize when DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
        setupModals();
        createAuthInterceptor();
        loadKategories();
        setupEventListeners();
        
        // Initialize Lucide icons
        if (window.lucide) {
            window.lucide.createIcons();
        }
    });

    // Setup all modals
    function setupModals() {
        setupModal('modalTambahKategori');
        setupModal('modalEditKategori');
        setupModal('modalKonfirmasiHapus');
    }

    // Setup individual modal
    function setupModal(modalId) {
        const modal = document.getElementById(modalId);
        if (!modal) return;

        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeModal(modalId);
            }
        });

        const modalContent = modal.querySelector('div[onclick]');
        if (modalContent) {
            modalContent.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        }
    }

    // Create fetch interceptor for authentication
    function createAuthInterceptor() {
        const originalFetch = window.fetch;
        
        window.fetch = async function(resource, options = {}) {
            const token = localStorage.getItem('token');
            if (token) {
                options.headers = options.headers || {};
                options.headers.Authorization = `Bearer ${token}`;
                options.headers.Accept = 'application/json';
                options.headers['Content-Type'] = 'application/json';
                options.headers['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').content;
            }
            
            const response = await originalFetch(resource, options);
            
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                const textResponse = await response.text();
                throw new Error(`Invalid response format: ${textResponse.substring(0, 100)}`);
            }
            
            return response;
        };
    }

    // Show alert notification
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

    // Modal functions
    function openModal(modalId) {
        document.querySelectorAll('[id^="modal"]').forEach(modal => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        });
        
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = '';
        }
    }

    // Setup event listeners
    function setupEventListeners() {
        // Form submission
        document.querySelector('#modalTambahKategori form')?.addEventListener('submit', function(e) {
            e.preventDefault();
            tambahKategori();
        });
        
        document.querySelector('#modalEditKategori form')?.addEventListener('submit', function(e) {
            e.preventDefault();
            simpanPerubahanKategori();
        });

        // Cancel buttons
        document.getElementById('btnBatalModalKategori')?.addEventListener('click', function() {
            closeModal('modalTambahKategori');
        });

        document.getElementById('btnBatalEditKategori')?.addEventListener('click', function() {
            closeModal('modalEditKategori');
        });

        document.getElementById('btnBatalHapus')?.addEventListener('click', function() {
            closeModal('modalKonfirmasiHapus');
            kategoriHapusId = null;
        });
        
        // Confirm delete button
        document.getElementById('btnKonfirmasiHapus')?.addEventListener('click', konfirmasiHapusKategori);

        // Search input
        document.getElementById('searchKategori')?.addEventListener('input', function(e) {
            filterKategories(e.target.value.toLowerCase());
        });
    }

    // Toggle dropdown menu
    function toggleDropdown(button) {
        const menu = button.nextElementSibling;

        document.querySelectorAll('.dropdown-menu').forEach(m => {
            if (m !== menu) {
                m.classList.add('hidden');
                m.classList.remove('dropdown-up', 'dropdown-down');
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
            menu.style.bottom = "100%";
            menu.style.marginBottom = "0.25rem";
            menu.style.top = "auto";
            menu.style.marginTop = "0";
        } else {
            menu.classList.add('dropdown-down');
            menu.style.top = "100%";
            menu.style.marginTop = "0.25rem";
            menu.style.bottom = "auto";
            menu.style.marginBottom = "0";
        }
    }

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.relative.inline-block')) {
            document.querySelectorAll('.dropdown-menu').forEach(menu => {
                menu.classList.add('hidden');
                menu.classList.remove('dropdown-up', 'dropdown-down');
            });
        }
    });

    // Load categories from API
    async function loadKategories() {
        try {
            // Show loading state
            const tbody = document.getElementById('kategoriTableBody');
            if (tbody) {
                tbody.innerHTML = `
                    <tr class="border-b">
                        <td colspan="5" class="py-8 text-center">
                            <div class="flex flex-col items-center justify-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                     class="animate-spin text-green-500">
                                    <path d="M21 12a9 9 0 1 1-6.219-8.56"/>
                                </svg>
                                <span class="text-gray-500">Memuat data kategori...</span>
                            </div>
                        </td>
                    </tr>
                `;
            }

            const token = localStorage.getItem('token');
            if (!token) {
                window.location.href = '/login';
                return;
            }

            const response = await fetch('/api/categories', {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            // Handle response error
            if (!response.ok) {
                const error = await response.json().catch(() => null);
                throw new Error(error?.message || `HTTP error! status: ${response.status}`);
            }

            const result = await response.json();
            
            if (!result.success) {
                throw new Error(result.message || 'Invalid response format');
            }

            renderKategories(result);
            
        } catch (error) {
            console.error('Load Categories Error:', error);
            showAlert('error', `Gagal memuat kategori: ${error.message}`);
            
            // Jika error 401/403, redirect ke login
            if (error.message.includes('401') || error.message.includes('403')) {
                window.location.href = '/login';
            }
        }
    }

    // Render categories to table
    function renderKategories(responseData) {
        const tbody = document.getElementById('kategoriTableBody');
        if (!tbody) return;

        tbody.innerHTML = '';

        const kategories = responseData?.data || [];
    
        if (kategories.length === 0) {
            tbody.innerHTML = `
                <tr class="border-b">
                    <td colspan="5" class="py-4 text-center text-gray-500">
                        Tidak ada data kategori
                    </td>
                </tr>
            `;
            return;
        }

        kategories.forEach((kategori, index) => {
            const row = document.createElement('tr');
            row.className = 'border-b';
            row.innerHTML = `
                <td class="py-3">${index + 1}</td>
                <td class="py-3 font-medium">${kategori.name || '-'}</td>
                <td class="py-3">${kategori.description || '-'}</td>
                <td class="py-3">${kategori.total_inventory_quantity || 0} stok</td>
                <td class="py-3 relative">
                    <div class="relative inline-block">
                        <button onclick="toggleDropdown(this)" class="p-2 hover:bg-gray-100 rounded">
                            <i data-lucide="more-vertical" class="w-5 h-5 text-gray-500"></i>
                        </button>
                        <div class="dropdown-menu hidden absolute right-0 z-10 mt-1 w-32 bg-white border border-gray-200 rounded-lg shadow-xl text-sm">
                            <button onclick="openEditModal(${kategori.id})" class="flex items-center w-full px-3 py-2.5 hover:bg-gray-100 text-left rounded-t-lg">
                                <i data-lucide="pencil" class="w-4 h-4 mr-2 text-gray-500"></i> Edit
                            </button>
                            <button onclick="${kategori.total_inventory_quantity > 0 ? 'showAlert(\'error\', \'Kategori tidak dapat dihapus karena masih memiliki produk\')' : `hapusKategori(${kategori.id})`}" 
                                class="flex items-center w-full px-3 py-2.5 hover:bg-gray-100 text-left ${kategori.total_inventory_quantity > 0 ? 'text-gray-400 cursor-not-allowed' : 'text-red-600'} rounded-b-lg">
                                <i data-lucide="trash-2" class="w-4 h-4 mr-2"></i> Hapus
                            </button>
                        </div>
                    </div>
                </td>
            `;
            tbody.appendChild(row);
        });

        if (window.lucide) {
            window.lucide.createIcons();
        }
    }

    // Filter categories by search term
    async function filterKategories(searchTerm) {
        try {
            // Show loading state during search
            const tbody = document.getElementById('kategoriTableBody');
            if (tbody) {
                tbody.innerHTML = `
                    <tr class="border-b">
                        <td colspan="5" class="py-8 text-center">
                            <div class="flex flex-col items-center justify-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                     class="animate-spin text-green-500">
                                    <path d="M21 12a9 9 0 1 1-6.219-8.56"/>
                                </svg>
                                <span class="text-gray-500">Mencari kategori...</span>
                            </div>
                        </td>
                    </tr>
                `;
            }

            const response = await fetch('/api/categories');
            if (!response.ok) throw new Error('Gagal memuat data kategori');
            
            const { data: kategories } = await response.json();
            
            const filtered = kategories.filter(kategori => 
                kategori.name.toLowerCase().includes(searchTerm) || 
                kategori.description.toLowerCase().includes(searchTerm)
            );
            
            renderKategories({ data: filtered });
        } catch (error) {
            console.error('Error:', error);
            showAlert('error', error.message);
        }
    }

    // Add new category
    async function tambahKategori() {
        try {
            const nama = document.getElementById('namaKategori').value;
            const deskripsi = document.getElementById('deskripsiKategori').value;
            
            if (!nama) {
                showAlert('error', 'Nama kategori harus diisi');
                return;
            }
            
            const response = await fetch('/api/categories', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    name: nama,
                    description: deskripsi
                })
            });
            
            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || 'Gagal menambahkan kategori');
            }
            
            await response.json();
            showAlert('success', 'Kategori berhasil ditambahkan');
            closeModal('modalTambahKategori');
            loadKategories();
            
            document.getElementById('namaKategori').value = '';
            document.getElementById('deskripsiKategori').value = '';
        } catch (error) {
            console.error('Error:', error);
            showAlert('error', error.message);
        }
    }

    // Open edit modal with category data
    async function openEditModal(kategoriId) {
        try {
            const token = localStorage.getItem('token');
            if (!token) {
                throw new Error('Token tidak ditemukan');
            }

            const response = await fetch(`/api/categories/${kategoriId}`, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                const textResponse = await response.text();
                throw new Error(`Response bukan JSON: ${textResponse.substring(0, 100)}`);
            }

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'Gagal memuat data kategori');
            }

            document.getElementById('editKategoriId').value = data.data.id;
            document.getElementById('editNamaKategori').value = data.data.name;
            document.getElementById('editDeskripsiKategori').value = data.data.description;
            
            openModal('modalEditKategori');
        } catch (error) {
            console.error('Error:', error);
            showAlert('error', `Gagal memuat kategori: ${error.message}`);
            
            if (error.message.includes('token') || error.message.includes('401')) {
                window.location.href = '/login';
            }
        }
    }

    // Save category changes
    async function simpanPerubahanKategori() {
        const id = document.getElementById('editKategoriId').value;
        const nama = document.getElementById('editNamaKategori').value;
        const deskripsi = document.getElementById('editDeskripsiKategori').value;
        
        if (!nama || !deskripsi) {
            showAlert('error', 'Nama dan deskripsi kategori harus diisi');
            return;
        }
        
        try {
            const response = await fetch(`/api/categories/${id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    name: nama,
                    description: deskripsi
                })
            });
            
            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || 'Gagal mengupdate kategori');
            }
            
            await response.json();
            showAlert('success', 'Kategori berhasil diperbarui');
            closeModal('modalEditKategori');
            loadKategories();
        } catch (error) {
            console.error('Error:', error);
            showAlert('error', error.message);
        }
    }

    // Prepare delete confirmation
    async function hapusKategori(id) {
        try {
            const token = localStorage.getItem('token');
            if (!token) {
                throw new Error('Token tidak ditemukan');
            }

            const response = await fetch(`/api/categories/${id}`, {
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                const textResponse = await response.text();
                throw new Error(`Response bukan JSON: ${textResponse.substring(0, 100)}`);
            }

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'Gagal memuat data kategori');
            }

            // Cek jika kategori masih memiliki produk
            if (data.data.total_inventory_quantity > 0) {
                showAlert('error', 'Kategori tidak dapat dihapus karena masih memiliki produk. Harap hapus atau pindahkan produk terlebih dahulu.');
                return;
            }

            kategoriHapusId = id;
            document.getElementById('hapusNamaKategori').textContent = data.data.name;
            openModal('modalKonfirmasiHapus');
        } catch (error) {
            console.error('Error:', error);
            showAlert('error', `Gagal memuat kategori: ${error.message}`);
            
            if (error.message.includes('token') || error.message.includes('401')) {
                window.location.href = '/login';
            }
        }
    }

    // Confirm category deletion
    async function konfirmasiHapusKategori() {
        try {
            const token = localStorage.getItem('token');
            if (!token) {
                throw new Error('Token tidak ditemukan');
            }

            const response = await fetch(`/api/categories/${kategoriHapusId}`, {
                method: 'DELETE',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                const textResponse = await response.text();
                throw new Error(`Response bukan JSON: ${textResponse.substring(0, 100)}`);
            }

            const data = await response.json();

            if (!response.ok) {
                // Tambahkan penanganan khusus untuk error constraint
                if (data.message.includes('foreign key constraint')) {
                    throw new Error('Kategori tidak dapat dihapus karena masih memiliki produk terkait. Harap hapus atau pindahkan produk terlebih dahulu.');
                }
                throw new Error(data.message || 'Gagal menghapus kategori');
            }

            showAlert('success', 'Kategori dan semua produk terkait berhasil dihapus');
            closeModal('modalKonfirmasiHapus');
            loadKategories();
            kategoriHapusId = null;
        } catch (error) {
            console.error('Error:', error);
            showAlert('error', error.message);
        }
    }
</script>

<style>
    /* Animations for alerts */
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
</style>

@endsection