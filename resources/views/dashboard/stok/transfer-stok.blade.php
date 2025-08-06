@extends('layouts.app')

@section('title', 'Manajemen Stok')

@section('content')

<!-- Alert Notification -->
<div id="alertContainer" class="fixed top-4 right-4 z-50 space-y-3 w-80">
    <!-- Alert akan muncul di sini secara dinamis -->
</div>

<!-- Modal Transfer Stok -->
@include('partials.stok.modal-transfer-stock')

<!-- Page Title + Action -->
<div class="mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <h1 class="text-2xl font-bold text-gray-800">Transfer Stok</h1>
       <div class="relative w-full md:w-64">
        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <i data-lucide="search" class="w-5 h-5 text-gray-400"></i>
        </span>
        <input type="text" id="searchInput" placeholder="Pencarian..."
            class="w-full pl-10 pr-4 py-3 border rounded-lg text-base font-medium focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" />
    </div>
    </div>
</div>

<!-- Card: Stok Info + Aksi -->
<div class="bg-white rounded-md p-4 shadow-md mb-4 flex flex-col md:flex-row md:items-center md:justify-between">
    <!-- Kiri: Judul -->
    <div class="mb-3 md:mb-0 flex items-start gap-2">
        <i data-lucide="package" class="w-5 h-5 text-gray-600 mt-1"></i>
        <div>
            <h2 class="text-lg font-semibold text-gray-800">Daftar Stok Produk</h2>
            <p class="text-sm text-gray-600">Kelola stok produk di semua cabang Aladdin Karpet.</p>
        </div>
    </div>
</div>

<!-- Card: Tabel Stok -->
<div class="bg-white rounded-lg shadow-lg p-6">
    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full text-base">
            <thead class="text-left text-gray-700 border-b-2">
                <tr>
                    <th class="py-3 font-semibold">SKU</th>
                    <th class="py-3 font-semibold">Produk</th>
                    <th class="py-3 font-semibold">Kategori</th>
                    <th class="py-3 font-semibold">Stok</th>
                    <th class="py-3 font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody id="productTableBody" class="text-gray-700 divide-y">
                <!-- Data will be loaded dynamically -->
                <tr>
                    <td colspan="5" class="py-8 text-center">
                        <div class="flex flex-col items-center justify-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                 class="animate-spin text-green-500">
                                <path d="M21 12a9 9 0 1 1-6.219-8.56"/>
                            </svg>
                            <span class="text-gray-500">Memuat data...</span>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
<script>
    // Mapping style untuk status stok
    const stockStyles = {
        'normal': { bg: 'bg-green-100', text: 'text-green-700' },
        'low': { bg: 'bg-yellow-100', text: 'text-yellow-700' },
        'critical': { bg: 'bg-red-100', text: 'text-red-700' }
    };

    // Fungsi untuk menampilkan alert
    function showAlert(type, message) {
        const alertContainer = document.getElementById('alertContainer');
        const alertId = 'alert-' + Date.now();
        
        // Warna dan ikon berdasarkan jenis alert
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
            },
            warning: {
                bgColor: 'bg-yellow-50',
                borderColor: 'border-yellow-200',
                textColor: 'text-yellow-800',
                icon: 'alert-triangle',
                iconColor: 'text-yellow-500'
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
        
        // Inisialisasi ikon Lucide
        if (window.lucide) {
            window.lucide.createIcons();
        }
        
        // Auto close setelah 5 detik
        setTimeout(() => {
            closeAlert(alertId);
        }, 5000);
    }

    // Fungsi untuk menutup alert
    function closeAlert(id) {
        const alert = document.getElementById(id);
        if (alert) {
            alert.classList.add('animate-fade-out');
            setTimeout(() => {
                alert.remove();
            }, 300);
        }
    }

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

    // Fungsi untuk memuat data outlet
    async function loadOutlets() {
        try {
            const response = await fetch('/api/outlets', {
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('token')}`,
                    'Accept': 'application/json'
                }
            });
            const result = await response.json();
            
            if (!result.success) {
                throw new Error(result.message || 'Gagal memuat data outlet');
            }
            
            return result.data;
        } catch (error) {
            console.error('Error loading outlets:', error);
            showAlert('error', 'Gagal memuat data outlet');
            return [];
        }
    }
    
    // Fungsi untuk mendapatkan ikon berdasarkan kategori produk
    function getCategoryIcon(categoryName) {
        const icons = {
            'Roti Manis': 'croissant',
            'Kue Basah': 'cake',
            'Kue Kering': 'cookie',
            'Pastry': 'pizza',
            'Minuman': 'coffee'
        };
        
        return icons[categoryName] || 'package';
    }

        // Fungsi untuk update informasi outlet
    function updateOutletInfo(outlet) {
        // Update elemen yang menampilkan nama outlet
        const outletNameElements = document.querySelectorAll('.outlet-name');
        outletNameElements.forEach(el => {
            el.textContent = `Outlet Aktif: ${outlet.name || 'Tidak diketahui'}`;
        });
        
        // Update elemen yang menampilkan alamat outlet
        const outletAddressElements = document.querySelectorAll('.outlet-address');
        outletAddressElements.forEach(el => {
            el.textContent = outlet.address || '';
        });
    }

    // Fungsi untuk membuka modal transfer
    async function openModalTransfer(productId, sku, produk, outletId, outlet, stok) {
        const modal = document.getElementById('modalTransferStock');
        
        // Set data ke form
        document.getElementById('productId').value = productId;
        document.getElementById('transferSku').textContent = sku;
        document.getElementById('transferProduk').textContent = produk;
        document.getElementById('stokTersedia').textContent = stok;
        document.getElementById('stokTersediaLabel').textContent = stok;
        document.getElementById('outletAsal').textContent = outlet;
        document.getElementById('sourceOutletId').value = outletId;
        document.getElementById('jumlahTransfer').max = stok;
        document.getElementById('jumlahTransfer').value = '';
        document.getElementById('catatanTransfer').value = '';
        
        // Load dan isi dropdown outlet tujuan
        const outlets = await loadOutlets();
        const outletSelect = document.getElementById('tujuanTransfer');
        
        // Kosongkan dropdown kecuali option pertama
        while (outletSelect.options.length > 1) {
            outletSelect.remove(1);
        }
        
        // Tambahkan outlet yang tersedia (kecuali outlet asal)
        outlets.forEach(outlet => {
            if (outlet.id != outletId) {
                const option = document.createElement('option');
                option.value = outlet.id;
                option.textContent = outlet.name;
                outletSelect.appendChild(option);
            }
        });
        
        // Tampilkan modal
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeModalTransfer() {
        const modal = document.getElementById('modalTransferStock');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    // Fungsi untuk mengirim data transfer
    async function submitTransfer() {
        const productId = document.getElementById('productId').value;
        const sourceOutletId = document.getElementById('sourceOutletId').value;
        const targetOutletId = document.getElementById('tujuanTransfer').value;
        const quantity = document.getElementById('jumlahTransfer').value;
        const notes = document.getElementById('catatanTransfer').value;
        const userId = localStorage.getItem('user_id'); // Asumsikan user_id disimpan di localStorage saat login
        
        if (!quantity || quantity <= 0) {
            showAlert('error', 'Jumlah transfer harus lebih dari 0');
            return;
        }
        
        if (!targetOutletId) {
            showAlert('error', 'Silakan pilih tujuan transfer');
            return;
        }
        
        if (sourceOutletId === targetOutletId) {
            showAlert('error', 'Outlet tujuan harus berbeda dengan outlet asal');
            return;
        }
        
        try {
            const response = await fetch('/api/inventories/transfer', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${localStorage.getItem('token')}`
                },
                body: JSON.stringify({
                    product_id: productId,
                    source_outlet_id: sourceOutletId,
                    target_outlet_id: targetOutletId,
                    quantity: quantity,
                    user_id: userId,
                    notes: notes,
                    // date: document.getElementById('reportDateInput').value
                })
            });
            
            const result = await response.json();
            
            if (!result.success) {
                throw new Error(result.message || 'Gagal melakukan transfer');
            }
            
            closeModalTransfer();
            showAlert('success', 'Transfer stok berhasil dilakukan');
            
            // Refresh data stok dengan tanggal dan outlet yang sedang dipilih
            // const currentDate = document.getElementById('reportDateInput').value;
            loadProductData();
        } catch (error) {
            console.error('Transfer error:', error);
            showAlert('error', error.message || 'Gagal melakukan transfer');
        }
    }

    function validateTransferAmount(input) {
        const max = parseInt(input.max);
        const value = parseInt(input.value);
        
        if (value > max) {
            input.value = max;
            showAlert('warning', `Jumlah transfer melebihi stok tersedia. Diubah menjadi ${max}`);
        }
    }

    // Fungsi untuk memuat data produk berdasarkan outlet dan tanggal
    async function loadProductData(date) {
        try {
            // Tampilkan loading state
            const tableBody = document.getElementById('productTableBody');
            tableBody.innerHTML = `
                <tr>
                    <td colspan="5" class="py-8 text-center">
                        <div class="flex flex-col items-center justify-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="animate-spin text-green-500">
                                <path d="M21 12a9 9 0 1 1-6.219-8.56"/>
                            </svg>
                            <span class="text-gray-500">Memuat data...</span>
                        </div>
                    </td>
                </tr>
            `;
            
            // Get dynamic outlet ID
            const outletId = getSelectedOutletId();
            
            console.log(`Fetching product data for outlet ID: ${outletId} on date: ${date}`);
            
            // Fetch data dari API dengan parameter outlet dan tanggal
            const response = await fetch(`/api/products/outlet/${outletId}?date=${date}`, {
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('token')}`,
                    'Accept': 'application/json'
                }
            });
            
            const result = await response.json();
            
            if (!result.success) {
                throw new Error(result.message || 'Gagal memuat data');
            }

            // Update informasi outlet
            updateOutletInfo(result.outlet || {});

            const outletResponse = await fetch(`/api/outlets/${outletId}`, {
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('token')}`,
                    'Accept': 'application/json'
                }
            });
            const outletResult = await outletResponse.json();
            
            if (!outletResult.success) {
                throw new Error(outletResult.message || 'Gagal memuat detail outlet');
            }

            // Update informasi outlet dengan data yang benar
            updateOutletInfo(outletResult.data || {});
            
            // Render tabel produk
            renderProductTable(result.data, outletId, outletResult.data?.name || 'Outlet');
        } catch (error) {
            console.error('Error loading data:', error);
            document.getElementById('productTableBody').innerHTML = `
                <tr>
                    <td colspan="5" class="py-6 text-center text-red-500">
                        <div class="flex flex-col items-center justify-center gap-2">
                            <i data-lucide="alert-triangle" class="w-8 h-8"></i>
                            <span>Gagal memuat data. ${error.message}</span>
                            <button onclick="retryLoadData()" class="mt-2 px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600">
                                Coba Lagi
                            </button>
                        </div>
                    </td>
                </tr>
            `;
            
            if (window.lucide) {
                window.lucide.createIcons();
            }
        }
    }

    // Fungsi untuk coba lagi jika gagal
    function retryLoadData() {
        const datePicker = document.getElementById('reportDateInput');
        const currentDate = datePicker?._flatpickr?.selectedDates[0] || new Date();
        const formattedDate = currentDate.toISOString().split('T')[0];
        
        loadProductData(formattedDate);
    }

    // Fungsi untuk render tabel produk
    function renderProductTable(products, outletId, outletName) {
        const tableBody = document.getElementById('productTableBody');
        
        if (!products || products.length === 0) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="5" class="py-8 text-center text-gray-500">
                        <div class="flex flex-col items-center justify-center gap-2">
                            <i data-lucide="package-x" class="w-10 h-10 text-gray-400"></i>
                            <span class="mt-2">Tidak ada data produk pada tanggal ini</span>
                        </div>
                    </td>
                </tr>
            `;
            
            if (window.lucide) {
                window.lucide.createIcons();
            }
            return;
        }
        
        let tableContent = '';
        
        products.forEach(product => {
            // Cek apakah produk aktif
            if (!product.is_active) return; // Skip produk tidak aktif
            
            const categoryIcon = getCategoryIcon(product.category.name);
            
            // Tentukan status stok (normal, low, atau critical)
            let stockStatus = 'normal';
            if (product.quantity <= product.min_stock * 0.5) {
                stockStatus = 'critical';
            } else if (product.quantity <= product.min_stock) {
                stockStatus = 'low';
            }
            
            const stockStyle = stockStyles[stockStatus];
            
            tableContent += `
                <tr class="border-b hover:bg-gray-50">
                    <td class="py-4 font-medium">${product.sku}</td>
                    <td class="py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-md bg-green-100 flex items-center justify-center">
                                <i data-lucide="${categoryIcon}" class="w-5 h-5 text-green-500"></i>
                            </div>
                            <span>${product.name}</span>
                        </div>
                    </td>
                    <td class="py-4">${product.category.name}</td>
                    <td class="py-4">
                        <div class="flex flex-col">
                            <span class="px-3 py-1.5 text-sm font-medium ${stockStyle.bg} ${stockStyle.text} rounded-full w-fit">${product.quantity}</span>
                            <span class="text-xs text-gray-500 mt-1">Min: ${product.min_stock}</span>
                        </div>
                    </td>
                    <td class="py-4">
                        <button onclick="openModalTransfer(
                            '${product.id}', 
                            '${product.sku}', 
                            '${product.name}', 
                            ${outletId}, 
                            '${outletName.replace(/'/g, "\\'")}', // Handle apostrophes
                            ${product.quantity})" 
                            class="...">
                            <i data-lucide="truck" class="..."></i> Transfer
                        </button>
                    </td>
                </tr>
            `;
        });
        
        tableBody.innerHTML = tableContent;
        
        // Inisialisasi ikon Lucide
        if (window.lucide) {
            window.lucide.createIcons();
        }
    }

    // Fungsi untuk memperbarui info outlet ketika tidak ada data
    async function updateOutletInfoFromSelection() {
        try {
            const outletId = getSelectedOutletId();
            const response = await fetch(`/api/outlets/${outletId}`, {
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('token')}`,
                    'Accept': 'application/json'
                }
            });
            
            const { data, success } = await response.json();
            
            if (success && data) {
                updateOutletInfo(data);
            }
        } catch (error) {
            console.error('Failed to fetch outlet details:', error);
        }
    }

    // Connect to outlet selection dropdown
    function connectOutletSelectionToProducts() {
        // Listen for outlet changes in localStorage
        window.addEventListener('storage', function(event) {
            if (event.key === 'selectedOutletId') {
                // Get current date
                const datePicker = document.getElementById('reportDateInput');
                const currentDate = datePicker?._flatpickr?.selectedDates[0] || new Date();
                const formattedDate = currentDate.toISOString().split('T')[0];
                
                // Reload product data with new outlet
                loadProductData(formattedDate);
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
                    // Update product data after a short delay to allow your existing code to complete
                    setTimeout(() => {
                        const datePicker = document.getElementById('reportDateInput');
                        const currentDate = datePicker?._flatpickr?.selectedDates[0] || new Date();
                        const formattedDate = currentDate.toISOString().split('T')[0];
                        
                        loadProductData(formattedDate);
                    }, 100);
                }
            });
        }
    }

    // Fungsi pencarian produk
    function setupSearch() {
        const searchInput = document.getElementById('searchInput');
        
        if (!searchInput) return;
        
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            const rows = document.querySelectorAll('#productTableBody tr');
            
            rows.forEach(row => {
                // Skip the "no data" row
                if (row.querySelector('td[colspan]')) return;
                
                const sku = row.querySelector('td:first-child').textContent.toLowerCase();
                const name = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                const category = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
                
                const matches = 
                    sku.includes(searchTerm) || 
                    name.includes(searchTerm) || 
                    category.includes(searchTerm);
                
                row.style.display = matches ? '' : 'none';
            });
            
            // Show "no results" message if all rows are hidden
            let allHidden = true;
            rows.forEach(row => {
                if (row.style.display !== 'none' && !row.querySelector('td[colspan]')) {
                    allHidden = false;
                }
            });
            
            // If search term exists and no results found
            if (searchTerm && allHidden) {
                // Remove existing "no results" row if it exists
                const existingNoResults = document.querySelector('#noResultsRow');
                if (existingNoResults) existingNoResults.remove();
                
                // Add "no results" row
                const tbody = document.getElementById('productTableBody');
                const noResultsRow = document.createElement('tr');
                noResultsRow.id = 'noResultsRow';
                noResultsRow.innerHTML = `
                    <td colspan="5" class="py-4 text-center text-gray-500">
                        <div class="flex flex-col items-center justify-center gap-2">
                            <i data-lucide="search-x" class="w-8 h-8"></i>
                            <span>Tidak ada hasil untuk "${searchTerm}"</span>
                        </div>
                    </td>
                `;
                tbody.appendChild(noResultsRow);
                
                if (window.lucide) {
                    window.lucide.createIcons();
                }
            } else {
                // Remove "no results" row if it exists
                const existingNoResults = document.querySelector('#noResultsRow');
                if (existingNoResults) existingNoResults.remove();
            }
        });
    }
    
    // Event listener untuk tombol di modal
    document.addEventListener('DOMContentLoaded', function() {
        const btnBatalTransfer = document.getElementById('btnBatalTransfer');
        if (btnBatalTransfer) {
            btnBatalTransfer.addEventListener('click', closeModalTransfer);
        }
        
        const btnSubmitTransfer = document.getElementById('btnSubmitTransfer');
        if (btnSubmitTransfer) {
            btnSubmitTransfer.addEventListener('click', submitTransfer);
        }
        
        // Setup date picker untuk filter tanggal
        flatpickr("#reportDateInput", {
            dateFormat: "Y-m-d",
            defaultDate: "today",
            onChange: async function(selectedDates, dateStr) {
                await loadProductData(dateStr);
            },
            locale: {
                firstDayOfWeek: 1
            }
        });
        
        // Inisialisasi dengan tanggal hari ini
        const initialDate = new Date().toISOString().split('T')[0];
        loadProductData(initialDate);
        
        // Setup pencarian
        setupSearch();
        
        // Connect outlet selection to product data updates
        connectOutletSelectionToProducts();
    });
</script>

<style>
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
</style>

@endsection