@extends('layouts.app')

@section('title', 'Riwayat Stok')

@section('content')

<!-- Alert Notification -->
<div id="alertContainer" class="fixed top-4 right-4 z-50 space-y-3 w-80">
    <!-- Alert will appear here dynamically -->
</div>

<!-- Page Title + Action -->
<div class="mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
       <h1 class="text-4xl font-bold text-gray-800">Riwayat Stok</h1>
        <div class="flex gap-2">
            <button onclick="printReport()" class="px-4 py-2 bg-white text-green-500 border border-green-500 rounded-lg hover:bg-green-50 flex items-center gap-2">
                <i data-lucide="printer" class="w-5 h-5"></i>
                Cetak
            </button>
            <button onclick="exportReport()" class="px-4 py-2 bg-white text-green-500 border border-green-500 rounded-lg hover:bg-green-50 flex items-center gap-2">
                <i data-lucide="file-text" class="w-5 h-5"></i>
                Ekspor
            </button>
        </div>
    </div>
</div>

<!-- Card: Info Outlet -->
<div class="bg-white rounded-md p-4 shadow-md mb-4 flex flex-col md:flex-row md:items-center md:justify-between">
    <div class="mb-3 md:mb-0 flex items-start gap-2">
        <i data-lucide="store" class="w-5 h-5 text-gray-600 mt-1"></i>
        <div>
            <h4 class="text-lg font-semibold text-gray-800">Menampilkan laporan untuk: <span id="outletName"></span></h4>
            <p class="text-sm text-gray-600">Periode: <span id="dateRangeDisplay">01 Mei 2025 - 14 Mei 2025</span></p>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="bg-white rounded-lg shadow p-6 mb-6">
     <h1 class="text-3xl font-bold text-gray-800">Laporan Stok</h1>
        <p class="text-sm text-gray-600">Perubahan stok produk di <span id="outletName2">Aladdin Karpet Pusat</span></p>
        
    <!-- Filter + Search -->
    <div class="flex flex-col md:flex-row md:items-end gap-4 mt-4 w-full">
        <!-- Filter Tanggal -->
        <div class="flex-1">
            <h2 class="text-sm font-medium text-gray-800 mb-1">Rentang Tanggal</h2>
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="relative flex-1">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i data-lucide="calendar" class="w-5 h-5 text-gray-400"></i>
                    </span>
                    <input type="text" id="dateRange" placeholder="Pilih rentang tanggal"
                        class="w-full pl-10 pr-4 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500" />
                </div>
            </div>
        </div>
        <!-- Cari Produk -->
        <div class="flex-1">
            <h2 class="text-sm font-medium text-gray-800 mb-1">Cari Produk</h2>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i data-lucide="search" class="w-5 h-5 text-gray-400"></i>
                </span>
                <input type="text" id="searchInput" placeholder="Cari produk..."
                    class="w-full pl-10 pr-4 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500" />
            </div>
        </div>
    </div>

    <!-- Loading state -->
    {{-- <div  class="py-12 flex flex-col items-center justify-center gap-2"> --}}
        <div id="loadingState" class="flex flex-col items-center justify-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" 
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                 class="animate-spin text-green-500">
                <path d="M21 12a9 9 0 1 1-6.219-8.56"/>
            </svg>
            <span class="text-gray-500">Memuat data...</span>
        </div>
    {{-- </div> --}}

    <!-- No data state -->
    <div id="noDataState" class="py-12 flex flex-col items-center justify-center hidden">
        {{-- <i data-lucide="package-x" class="w-16 h-16 text-gray-400"></i> --}}
        <p class="mt-4 text-gray-600">Tidak ada data untuk periode yang dipilih</p>
    </div>

    <!-- Content area (tables will be inserted here) -->
    <div id="tablesContainer" class="mt-5 space-y-8 hidden">
        <!-- Table: Penyesuaian -->
        <div id="adjustmentContainer" class="hidden">
            <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i data-lucide="package-plus" class="w-5 h-5 text-blue-500"></i>
                Penyesuaian Stok
            </h2>
            
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="text-left text-gray-700 bg-gray-50">
                        <tr>
                            <th class="py-3 font-bold px-4">Produk</th>
                            <th class="py-3 font-bold px-4">SKU</th>
                            <th class="py-3 font-bold px-4 text-right">Stok Akhir</th>
                            <th class="py-3 font-bold px-4 text-right">Total Perubahan</th>
                            <th class="py-3 font-bold px-4 text-right">Total Entri</th>
                            <th class="py-3 font-bold px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700 divide-y" id="adjustmentTable">
                        <!-- Data will be inserted here -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Table: Kiriman Pabrik -->
        <div id="shipmentContainer" class="hidden">
            <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i data-lucide="truck" class="w-5 h-5 text-green-500"></i>
                Kiriman Pabrik
            </h2>
            
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="text-left text-gray-700 bg-gray-50">
                        <tr>
                            <th class="py-3 font-bold px-4">Produk</th>
                            <th class="py-3 font-bold px-4">SKU</th>
                            <th class="py-3 font-bold px-4 text-right">Stok Akhir</th>
                            <th class="py-3 font-bold px-4 text-right">Total Perubahan</th>
                            <th class="py-3 font-bold px-4 text-right">Total Entri</th>
                            <th class="py-3 font-bold px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700 divide-y" id="shipmentTable">
                        <!-- Data will be inserted here -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Table: Pembelian -->
        <div id="purchaseContainer" class="hidden">
            <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i data-lucide="shopping-cart" class="w-5 h-5 text-purple-500"></i>
                Pembelian
            </h2>
            
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="text-left text-gray-700 bg-gray-50">
                        <tr>
                            <th class="py-3 font-bold px-4">Produk</th>
                            <th class="py-3 font-bold px-4">SKU</th>
                            <th class="py-3 font-bold px-4 text-right">Stok Akhir</th>
                            <th class="py-3 font-bold px-4 text-right">Total Perubahan</th>
                            <th class="py-3 font-bold px-4 text-right">Total Entri</th>
                            <th class="py-3 font-bold px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700 divide-y" id="purchaseTable">
                        <!-- Data will be inserted here -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Table: Penjualan -->
        <div id="saleContainer" class="hidden">
            <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i data-lucide="receipt" class="w-5 h-5 text-red-500"></i>
                Penjualan
            </h2>
            
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="text-left text-gray-700 bg-gray-50">
                        <tr>
                            <th class="py-3 font-bold px-4">Produk</th>
                            <th class="py-3 font-bold px-4">SKU</th>
                            <th class="py-3 font-bold px-4 text-right">Stok Akhir</th>
                            <th class="py-3 font-bold px-4 text-right">Total Perubahan</th>
                            <th class="py-3 font-bold px-4 text-right">Total Entri</th>
                            <th class="py-3 font-bold px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700 divide-y" id="saleTable">
                        <!-- Data will be inserted here -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Table: Transfer Masuk -->
        <div id="transferInContainer" class="hidden">
            <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i data-lucide="arrow-down-circle" class="w-5 h-5 text-blue-500"></i>
                Transfer Masuk
            </h2>
            
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="text-left text-gray-700 bg-gray-50">
                        <tr>
                            <th class="py-3 font-bold px-4">Produk</th>
                            <th class="py-3 font-bold px-4">SKU</th>
                            <th class="py-3 font-bold px-4 text-right">Stok Akhir</th>
                            <th class="py-3 font-bold px-4 text-right">Total Perubahan</th>
                            <th class="py-3 font-bold px-4 text-right">Total Entri</th>
                            <th class="py-3 font-bold px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700 divide-y" id="transferInTable">
                        <!-- Data will be inserted here -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Table: Transfer Keluar -->
        <div id="transferOutContainer" class="hidden">
            <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i data-lucide="arrow-up-circle" class="w-5 h-5 text-red-500"></i>
                Transfer Keluar
            </h2>
            
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="text-left text-gray-700 bg-gray-50">
                        <tr>
                            <th class="py-3 font-bold px-4">Produk</th>
                            <th class="py-3 font-bold px-4">SKU</th>
                            <th class="py-3 font-bold px-4 text-right">Stok Akhir</th>
                            <th class="py-3 font-bold px-4 text-right">Total Perubahan</th>
                            <th class="py-3 font-bold px-4 text-right">Total Entri</th>
                            <th class="py-3 font-bold px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700 divide-y" id="transferOutTable">
                        <!-- Data will be inserted here -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Table: Lainnya -->
        <div id="otherContainer" class="hidden">
            <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i data-lucide="layers" class="w-5 h-5 text-gray-500"></i>
                Lainnya
            </h2>
            
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="text-left text-gray-700 bg-gray-50">
                        <tr>
                            <th class="py-3 font-bold px-4">Produk</th>
                            <th class="py-3 font-bold px-4">SKU</th>
                            <th class="py-3 font-bold px-4 text-right">Stok Akhir</th>
                            <th class="py-3 font-bold px-4 text-right">Total Perubahan</th>
                            <th class="py-3 font-bold px-4 text-right">Total Entri</th>
                            <th class="py-3 font-bold px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700 divide-y" id="otherTable">
                        <!-- Data will be inserted here -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
@include('partials.laporan.modal-riwayat-stok')

<!-- Flatpickr JS -->

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>

<script>
    // Data storage
    let stockData = {};
    let modal;
    let currentStartDate;
    let currentEndDate;

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize modal globally
        modal = new Modal(document.getElementById('stockHistoryModal'));
        
        // Event listener untuk overlay - pastikan overlay menutup modal
        document.querySelector('.modal-overlay').addEventListener('click', function() {
            if (modal) modal.hide();
        });
        
        // Event listener untuk tombol close di modal
        const closeButtons = document.querySelectorAll('[data-dismiss="modal"], .close-modal');
        closeButtons.forEach(button => {
            button.addEventListener('click', function() {
                if (modal) modal.hide();
            });
        });

        // Initialize the page
        initializePage();
    });

    // Initialize page with outlet management
    function initializePage() {
        // Set default dates
        const defaultDates = getDefaultDateRange();
        currentStartDate = formatDateForAPI(defaultDates[0]);
        currentEndDate = formatDateForAPI(defaultDates[1]);
        
        // Set display
        document.getElementById('dateRangeDisplay').textContent = 
            `${formatDateForDisplay(defaultDates[0])} - ${formatDateForDisplay(defaultDates[1])}`;
        
        // Get selected outlet
        const outletId = getSelectedOutletId();
        
        // Load initial data
        fetchStockData(outletId, currentStartDate, currentEndDate);
        
        // Connect outlet selection to report updates
        connectOutletSelectionToReport();
        
        // Initialize Lucide icons
        if (window.lucide) {
            lucide.createIcons({ icons });
        }
    }

    // Get currently selected outlet ID
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

    // Connect outlet selection dropdown to report updates
    function connectOutletSelectionToReport() {
        // Listen for outlet changes in localStorage
        window.addEventListener('storage', function(event) {
            if (event.key === 'selectedOutletId') {
                // Reload report with new outlet
                fetchStockData(event.newValue, currentStartDate, currentEndDate);
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
                    // Update report after a short delay to allow existing code to complete
                    setTimeout(() => {
                        fetchStockData(getSelectedOutletId(), currentStartDate, currentEndDate);
                    }, 100);
                }
            });
        }
    }

    function closeModal() {
        const modalElement = document.getElementById('stockHistoryModal');
        if (modalElement) {
            modalElement.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
    }

    function getDefaultDateRange() {
        const today = new Date();
        const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
        return [firstDay, today];
    }

    // Initialize date range picker
    const dateRangePicker = flatpickr("#dateRange", {
        mode: "range",
        dateFormat: "d M Y",
        defaultDate: getDefaultDateRange(),
        locale: "id",
        onChange: function(selectedDates, dateStr) {
            if (selectedDates.length === 2) {
                // Format tanggal untuk API (YYYY-MM-DD)
                currentStartDate = formatDateForAPI(selectedDates[0]);
                currentEndDate = formatDateForAPI(selectedDates[1]);
                
                // Format tanggal untuk tampilan UI
                const displayStartDate = formatDateForDisplay(selectedDates[0]);
                const displayEndDate = formatDateForDisplay(selectedDates[1]);
                
                document.getElementById('dateRangeDisplay').textContent = `${displayStartDate} - ${displayEndDate}`;
                
                // Kirim ke API dengan outlet yang dipilih
                fetchStockData(getSelectedOutletId(), currentStartDate, currentEndDate);
                
                // Tampilkan notifikasi
                if (currentStartDate === currentEndDate) {
                    showAlert('info', `Memuat data untuk tanggal ${displayStartDate}`);
                } else {
                    showAlert('info', `Memuat data dari ${displayStartDate} sampai ${displayEndDate}`);
                }
            }
        }
    });

    // Fungsi untuk format tanggal ke API (YYYY-MM-DD)
    function formatDateForAPI(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    // Fungsi untuk format tampilan tanggal
    function formatDateForDisplay(date) {
        return date.toLocaleDateString('id-ID', {
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        });
    }

    // Format date to Indonesian format
    function formatDate(date) {
        const options = { day: 'numeric', month: 'long', year: 'numeric' };
        return date.toLocaleDateString('id-ID', options);
    }

    // Search input handler
    document.getElementById('searchInput').addEventListener('keyup', function(e) {
        filterData();
    });

    // Filter data function
    function filterData() {
        const searchTerm = document.getElementById('searchInput').value.trim().toLowerCase();
        
        // Get all rows from all tables
        const allContainers = ['adjustment', 'shipment', 'purchase', 'sale', 'transferIn', 'transferOut', 'other'];
        allContainers.forEach(type => {
            const tableId = `${type}Table`;
            const rows = document.querySelectorAll(`#${tableId} tr`);
            
            let hasVisibleRows = false;
            
            rows.forEach(row => {
                const productNameCell = row.querySelector('td:first-child');
                if (productNameCell) {
                    const productName = productNameCell.textContent.toLowerCase();
                    const skuCell = row.querySelector('td:nth-child(2)');
                    const sku = skuCell ? skuCell.textContent.toLowerCase() : '';
                    
                    if (productName.includes(searchTerm) || sku.includes(searchTerm)) {
                        row.style.display = '';
                        hasVisibleRows = true;
                    } else {
                        row.style.display = 'none';
                    }
                }
            });
            
            // Show/hide section based on if there are visible results
            document.getElementById(`${type}Container`).classList.toggle('hidden', !hasVisibleRows);
        });
    }

    // Fetch stock data from API with outlet management
    function fetchStockData(outletId, startDate, endDate) {
        // Validasi parameter
        if (!outletId) {
            outletId = getSelectedOutletId();
        }
        
        if (!startDate || !endDate) {
            const defaultDates = getDefaultDateRange();
            startDate = formatDateForAPI(defaultDates[0]);
            endDate = formatDateForAPI(defaultDates[1]);
        }
        
        showLoading(true);
        // showAlert('info', 'Memuat data...');
        
        const url = `/api/inventory-histories/type/${outletId}?start_date=${startDate}&end_date=${endDate}`;
        
        console.log('Request URL:', url); // Untuk debugging
        
        fetch(url, {
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('token')}`,
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            stockData = data;
            displayStockData(data);
            showLoading(false);
            showAlert('success', 'Data laporan berhasil dimuat');
        })
        .catch(error => {
            console.error('Error fetching stock data:', error);
            showAlert('error', 'Gagal memuat data. Silakan coba lagi nanti.');
            showLoading(false);
            showNoData(true);
        });
    }

    // Display stock data with outlet information
    function displayStockData(data) {
        // Reset all tables
        const allContainers = ['adjustment', 'shipment', 'purchase', 'sale', 'transferIn', 'transferOut', 'other'];
        allContainers.forEach(type => {
            document.getElementById(`${type}Table`).innerHTML = '';
            document.getElementById(`${type}Container`).classList.add('hidden');
        });
        
        // Check if we have data
        if (!data || !data.data || !data.data.summary_by_type) {
            showNoData(true);
            return;
        }
        
        // Update outlet name in multiple places
        if (data.data && data.data.outlet_name) {
            const outletName = data.data.outlet_name;
            document.getElementById('outletName').textContent = outletName;
            document.getElementById('outletName2').textContent = outletName;
            // document.getElementById('outletNameHeader').textContent = outletName;
        } else {
            // Fallback if outlet name not in response
            const fallbackOutletName = 'Riwayat Stok';
            document.getElementById('outletName').textContent = fallbackOutletName;
            document.getElementById('outletName2').textContent = fallbackOutletName;
            // document.getElementById('outletNameHeader').textContent = fallbackOutletName;
        }
        
        // Process each type of stock change
        const summaryByType = data.data.summary_by_type;
        let hasData = false;
        
        // Process sales data
        if (summaryByType.sale && summaryByType.sale.products && summaryByType.sale.products.length > 0) {
            populateTable('sale', summaryByType.sale.products);
            document.getElementById('saleContainer').classList.remove('hidden');
            hasData = true;
        }
        
        // Process shipment data
        if (summaryByType.shipment && summaryByType.shipment.products && summaryByType.shipment.products.length > 0) {
            populateTable('shipment', summaryByType.shipment.products);
            document.getElementById('shipmentContainer').classList.remove('hidden');
            hasData = true;
        }
        
        // Process adjustment data
        if (summaryByType.adjustment && summaryByType.adjustment.products && summaryByType.adjustment.products.length > 0) {
            populateTable('adjustment', summaryByType.adjustment.products);
            document.getElementById('adjustmentContainer').classList.remove('hidden');
            hasData = true;
        }
        
        // Process purchase data
        if (summaryByType.purchase && summaryByType.purchase.products && summaryByType.purchase.products.length > 0) {
            populateTable('purchase', summaryByType.purchase.products);
            document.getElementById('purchaseContainer').classList.remove('hidden');
            hasData = true;
        }

        // Process transfer in data
        if (summaryByType.transfer_in && summaryByType.transfer_in.products && summaryByType.transfer_in.products.length > 0) {
            populateTable('transferIn', summaryByType.transfer_in.products);
            document.getElementById('transferInContainer').classList.remove('hidden');
            hasData = true;
        }
        
        // Process transfer out data
        if (summaryByType.transfer_out && summaryByType.transfer_out.products && summaryByType.transfer_out.products.length > 0) {
            populateTable('transferOut', summaryByType.transfer_out.products);
            document.getElementById('transferOutContainer').classList.remove('hidden');
            hasData = true;
        }
        
        // Process other data
        if (summaryByType.other && summaryByType.other.products && summaryByType.other.products.length > 0) {
            populateTable('other', summaryByType.other.products);
            document.getElementById('otherContainer').classList.remove('hidden');
            hasData = true;
        }
        
        // Show/hide content areas
        document.getElementById('tablesContainer').classList.toggle('hidden', !hasData);
        showNoData(!hasData);
    }

    // Helper function to format stock quantity based on unit_type
    function formatStockQuantity(quantity, unit_type) {
        if (!quantity && quantity !== 0) return '0';
        
        // Convert to number for proper formatting
        const numQuantity = parseFloat(quantity);
        
        // If unit_type is 'meter', show decimal places, otherwise show as integer
        if (unit_type && unit_type.toLowerCase() === 'meter') {
            return numQuantity % 1 === 0 ? numQuantity.toString() : numQuantity.toFixed(1);
        } else {
            return Math.floor(numQuantity).toString();
        }
    }

    // Populate table for specific type
    function populateTable(type, products) {
        const tableId = `${type}Table`;
        const table = document.getElementById(tableId);
        
        products.forEach(product => {
            const row = document.createElement('tr');
            
            // Determine if total change is positive or negative for styling
            const changeClass = parseInt(product.total_quantity_changed) >= 0 ? 'text-green-600' : 'text-red-600';
            
            row.innerHTML = `
                <td class="py-4 px-4">${product.product_name}</td>
                <td class="py-4 px-4">${product.sku}</td>
                <td class="py-4 px-4 text-right">${formatStockQuantity(product.stock_as_of_end_date, product.unit_type)} ${product.unit_type || 'pcs'}</td>
                <td class="py-4 px-4 text-right ${changeClass}">${parseInt(product.total_quantity_changed) >= 0 ? '+' : ''}${formatStockQuantity(product.total_quantity_changed, product.unit_type)} ${product.unit_type || 'pcs'}</td>
                <td class="py-4 px-4 text-right">${product.total_entries}</td>
                <td class="py-4 px-4 text-right">
                    <button onclick="showStockHistory('${type}', '${product.product_id}', '${product.product_name}', '${product.sku}')" class="text-green-500 hover:text-green-700 flex items-center gap-1 justify-end w-full">
                        <i data-lucide="eye" class="w-4 h-4"></i>
                        Detail
                    </button>
                </td>
            `;
            
            table.appendChild(row);
        });
    }

    // Show stock history modal
    function showStockHistory(type, productId, productName, sku) {
        // 1. Temukan produk dalam data
        let product = null;
        if (stockData && stockData.data && stockData.data.summary_by_type[type]) {
            product = stockData.data.summary_by_type[type].products.find(
                p => p.product_id.toString() === productId.toString()
            );
        }

        if (!product) {
            showAlert('error', 'Data produk tidak ditemukan');
            return;
        }

        // 2. Isi konten modal
        document.getElementById('modalProductName').textContent = productName;
        document.getElementById('modalSKU').textContent = sku;
        document.getElementById('modalEndStock').textContent = 
            `${formatStockQuantity(product.stock_as_of_end_date, product.unit_type)} ${product.unit_type || 'pcs'}`;

        const changeClass = parseInt(product.total_quantity_changed) >= 0 ? 'text-green-600' : 'text-red-600';
        const changePrefix = parseInt(product.total_quantity_changed) >= 0 ? '+' : '';
        document.getElementById('modalTotalChange').textContent = 
            `${changePrefix}${formatStockQuantity(product.total_quantity_changed, product.unit_type)}`;
        document.getElementById('modalTotalChange').className = `text-lg font-semibold ${changeClass}`;
        
        document.getElementById('modalTotalEntries').textContent = product.total_entries;

        // 3. Isi tabel riwayat
        const historyTable = document.getElementById('historyEntries');
        historyTable.innerHTML = '';

        if (product.entries && product.entries.length > 0) {
            product.entries.forEach(entry => {
                const row = document.createElement('tr');
                const entryDate = new Date(entry.created_at);
                const formattedDate = entryDate.toLocaleString('id-ID', {
                    day: '2-digit',
                    month: 'long',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });

                const entryChangeClass = parseInt(entry.quantity_change) >= 0 ? 'text-green-600' : 'text-red-600';
                const entryChangePrefix = parseInt(entry.quantity_change) >= 0 ? '+' : '';

                row.innerHTML = `
                    <td class="py-3 px-4">${formattedDate}</td>
                    <td class="py-3 px-4 text-right">${formatStockQuantity(entry.quantity_before, product.unit_type)} ${product.unit_type || 'pcs'}</td>
                    <td class="py-3 px-4 text-right">${formatStockQuantity(entry.quantity_after, product.unit_type)} ${product.unit_type || 'pcs'}</td>
                    <td class="py-3 px-4 text-right ${entryChangeClass}">${entryChangePrefix}${formatStockQuantity(entry.quantity_change, product.unit_type)} ${product.unit_type || 'pcs'}</td>
                    <td class="py-3 px-4">${entry.notes || '-'}</td>
                `;
                historyTable.appendChild(row);
            });
        } else {
            historyTable.innerHTML = `
                <tr>
                    <td colspan="5" class="py-3 px-4 text-center text-gray-500">
                        Tidak ada data riwayat
                    </td>
                </tr>
            `;
        }

        // 4. Tampilkan modal dengan cara yang lebih reliable
        const modalElement = document.getElementById('stockHistoryModal');
        if (modalElement) {
            // Hapus class hidden dan reset style
            modalElement.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
            
            // Pastikan overlay tampil dengan benar
            const overlay = document.querySelector('.modal-overlay');
            overlay.classList.remove('hidden');
            overlay.classList.add('bg-gray-900', 'opacity-50');
            
            // Reset semua kemungkinan class yang tidak diinginkan
            overlay.classList.remove('bg-blue-500', 'bg-opacity-50', 'bg-blue-200');
        }
    }

    // Show/hide loading state
    function showLoading(show) {
        document.getElementById('loadingState').style.display = show ? 'flex' : 'none';
        document.getElementById('tablesContainer').classList.toggle('hidden', show);
    }

    // Show/hide no data state
    function showNoData(show) {
        document.getElementById('noDataState').style.display = show ? 'flex' : 'none';
    }

    // Print report function with outlet name
    function printReport() {
        if (!stockData || !stockData.data || !stockData.data.summary_by_type) {
            showAlert('error', 'Tidak ada data untuk dicetak');
            return;
        }

        showAlert('info', 'Mempersiapkan laporan untuk dicetak...');

        setTimeout(() => {
            const printWindow = window.open('', '_blank');
            const periodeStart = document.querySelector("#dateRange")._flatpickr.selectedDates?.[0] || new Date();
            const periodeEnd = document.querySelector("#dateRange")._flatpickr.selectedDates?.[1] || new Date();
            const outletName = document.getElementById('outletName')?.textContent || 'Outlet';

            printWindow.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Laporan Riwayat Stok - ${outletName}</title>
                    <style>
                        body { font-family: Arial, sans-serif; margin: 20px; }
                        h1, h2 { color: #333; }
                        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
                        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                        th { background-color: #f2f2f2; }
                        .text-right { text-align: right; }
                        .text-green-600 { color: green; }
                        .text-red-600 { color: red; }
                        .report-header {
                            display: flex;
                            align-items: center;
                            gap: 20px;
                            margin-bottom: 20px;
                        }
                        .logo {
                            width: 60px;
                            height: auto;
                        }
                        .header-info {
                            margin-bottom: 15px;
                        }
                        hr { border: 0; border-top: 1px solid #000; margin: 10px 0; }
                        .footer { margin-top: 30px; font-size: 0.8em; text-align: center; color: #666; }
                    </style>
                </head>
                <body>
                    <div class="report-header">
                        <img src="/images/logo.png" alt="Logo Aladdin Karpet" class="logo">
                        <div>
                            <h1>LAPORAN RIWAYAT STOK</h1>
                            <div class="header-info">
                                Outlet: ${outletName}<br>
                                Periode: ${periodeStart.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })} 
                                hingga ${periodeEnd.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })}<br>
                                Dicetak pada: ${new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })}
                            </div>
                        </div>
                    </div>
                    <hr>
            `);

            const typeLabels = {
                adjustment: 'Penyesuaian',
                shipment: 'Kiriman Pabrik',
                purchase: 'Pembelian',
                sale: 'Penjualan',
                transfer_in: 'Transfer Masuk',
                transfer_out: 'Transfer Keluar',
                other: 'Lainnya'
            };

            Object.keys(stockData.data.summary_by_type).forEach(type => {
                const section = stockData.data.summary_by_type[type];
                if (!section.products || section.products.length === 0) return;

                printWindow.document.write(`
                    <h2>${typeLabels[type]}</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>SKU</th>
                                <th class="text-right">Stok Akhir</th>
                                <th class="text-right">Perubahan</th>
                                <th class="text-right">Total Entri</th>
                            </tr>
                        </thead>
                        <tbody>
                `);

                section.products.forEach(product => {
                    const changeClass = parseInt(product.total_quantity_changed) >= 0 ? 'text-green-600' : 'text-red-600';
                    const changePrefix = parseInt(product.total_quantity_changed) >= 0 ? '+' : '';
                    printWindow.document.write(`
                        <tr>
                            <td>${product.product_name}</td>
                            <td>${product.sku}</td>
                            <td class="text-right">${formatStockQuantity(product.stock_as_of_end_date, product.unit_type)} ${product.unit_type || 'pcs'}</td>
                            <td class="text-right ${changeClass}">${changePrefix}${formatStockQuantity(product.total_quantity_changed, product.unit_type)} ${product.unit_type || 'pcs'}</td>
                            <td class="text-right">${product.total_entries}</td>
                        </tr>
                    `);
                });

                printWindow.document.write(`</tbody></table>`);
            });

            printWindow.document.write(`
                    <div class="footer">
                        Laporan ini dicetak secara otomatis oleh sistem.<br>
                        © ${new Date().getFullYear()} Aladdin Karpet
                    </div>
                </body>
                </html>
            `);

            printWindow.document.close();

            setTimeout(() => {
                printWindow.print();
                printWindow.close();
            }, 1000);
        }, 1000);
    }

    // Export report function with outlet name
    async function exportReport() {
        showAlert('info', 'Mempersiapkan laporan untuk diekspor...');
        
        try {
            // 1. Dapatkan outlet ID dan nama outlet secara dinamis
            const outletId = stockData?.meta?.outlet_id;
            const outletName = await getOutletName(outletId);

            // 2. Format tanggal untuk nama file
            const formattedStartDate = new Date(currentStartDate).toLocaleDateString('id-ID', { 
                day: 'numeric', 
                month: 'long', 
                year: 'numeric' 
            }).replace(/ /g, ' ');
            
            const formattedEndDate = new Date(currentEndDate).toLocaleDateString('id-ID', { 
                day: 'numeric', 
                month: 'long', 
                year: 'numeric' 
            }).replace(/ /g, ' ');
            
            // 3. Buat header CSV hanya dengan kolom tabel
            let csvContent = "data:text/csv;charset=utf-8,";
            csvContent += "Jenis Transaksi,Nama Produk,SKU,Stok Akhir Periode,Satuan,Total Perubahan Stok,Jumlah Transaksi,Tanggal Transaksi,Stok Sebelum,Perubahan Stok,Stok Sesudah,Catatan,Outlet\n";

            // 4. Mapping jenis transaksi
            const typeMapping = {
                'adjustment': 'Penyesuaian',
                'shipment': 'Pengiriman',
                'sale': 'Penjualan',
                'purchase': 'Pembelian',
                'transfer_in': 'Transfer Masuk',
                'transfer_out': 'Transfer Keluar',
                'other': 'Lainnya'
            };

            // 5. Proses data transaksi
            if (stockData?.data?.summary_by_type) {
                Object.keys(stockData.data.summary_by_type).forEach(type => {
                    const typeData = stockData.data.summary_by_type[type];
                    const typeLabel = typeMapping[type] || type;
                    
                    typeData.products.forEach(product => {
                        product.entries.forEach(entry => {
                            const transactionDate = new Date(entry.created_at);
                            const formattedDate = transactionDate.toLocaleDateString('id-ID', {
                                day: 'numeric',
                                month: 'long',
                                year: 'numeric'
                            }) + ' ' + transactionDate.toLocaleTimeString('id-ID', {
                                hour: '2-digit',
                                minute: '2-digit'
                            });
                            
                            csvContent += [
                                typeLabel,
                                `"${product.product_name || 'Unknown Product'}"`,
                                product.sku || '',
                                formatStockQuantity(product.stock_as_of_end_date, product.unit_type),
                                `"${product.unit_type || 'pcs'}"`,
                                formatStockQuantity(product.total_quantity_changed, product.unit_type),
                                product.total_entries,
                                `"${formattedDate}"`,
                                formatStockQuantity(entry.quantity_before, product.unit_type),
                                formatStockQuantity(entry.quantity_change, product.unit_type),
                                formatStockQuantity(entry.quantity_after, product.unit_type),
                                `"${entry.notes || ''}"`,
                                `"${outletName}"`
                            ].join(',') + '\n';
                        });
                    });
                });
            }

            // 6. Proses download
            const encodedUri = encodeURI(csvContent);
            const link = document.createElement("a");
            link.setAttribute("href", encodedUri);
            link.setAttribute(
                "download", 
                `Laporan_Histori_Stok_${outletName.replace(/\s+/g, '_')}_${formattedStartDate}_${formattedEndDate}.csv`
            );
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            
            showAlert('success', 'Laporan berhasil diekspor');
        } catch (error) {
            console.error('Error exporting data:', error);
            showAlert('error', 'Gagal mengekspor data: ' + error.message);
        }
    }

    // Fungsi getOutletName tetap sama dengan versi pertama
    async function getOutletName(outletId) {
        try {
            if (!outletId) return "Outlet";
            
            if (window.outletsData && Array.isArray(window.outletsData.data)) {
                const outlet = window.outletsData.data.find(o => o.id.toString() === outletId.toString());
                if (outlet) return outlet.name;
            }
            
            const response = await fetch(`/api/outlets/${outletId}`);
            if (!response.ok) throw new Error(`Error: ${response.status}`);
            
            const data = await response.json();
            return data.name || `Outlet ${outletId}`;
        } catch (error) {
            console.error('Error getting outlet name:', error);
            return `Outlet ${outletId}`;
        }
    }

    // Show alert function
    function showAlert(type, message) {
        const alertContainer = document.getElementById('alertContainer');
        const alert = document.createElement('div');
        alert.className = `px-4 py-3 rounded-lg shadow-md ${type === 'error' ? 'bg-red-100 text-red-700' : 
                        type === 'success' ? 'bg-green-100 text-green-700' : 'bg-green-100 text-green-700'}`;
        alert.innerHTML = `
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i data-lucide="${type === 'error' ? 'alert-circle' : 
                                    type === 'success' ? 'check-circle' : 'info'}" 
                    class="w-5 h-5"></i>
                    <span>${message}</span>
                </div>
                <button onclick="this.parentElement.parentElement.remove()">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
        `;
        alertContainer.appendChild(alert);
        
        // Initialize Lucide icons for the new alert
        if (window.lucide) {
            lucide.createIcons({ icons });
        }
        
        setTimeout(() => {
            alert.remove();
        }, 5000);
    }
</script>

<style>
    /* Tanggal terpilih: awal & akhir range */
    .flatpickr-day.selected,
    .flatpickr-day.startRange,
    .flatpickr-day.endRange {
        background-color: #f97316; /* Tailwind green-500 */
        color: white;
        border-color: #f97316;
    }

    .flatpickr-day.selected:hover,
    .flatpickr-day.startRange:hover,
    .flatpickr-day.endRange:hover {
        background-color: #fb923c; /* Tailwind green-400 */
        color: white;
        border-color: #fb923c;
    }

    /* Tanggal di antara range */
    .flatpickr-day.inRange {
        background-color: #fed7aa; /* Tailwind green-200 */
        color: #78350f; /* Tailwind green-800 */
    }

    /* Hover efek pada hari */
    .flatpickr-day:hover {
        background-color: #fdba74; /* Tailwind green-300 */
        color: black;
    }

    /* Hilangkan outline biru saat klik/tap */
    .flatpickr-day:focus {
        outline: none;
        box-shadow: 0 0 0 2px #fdba74; /* Tailwind green-300 glow */
    }

    /* Hari ini */
    .flatpickr-day.today:not(.selected):not(.inRange) {
        border: 1px solid #fb923c; /* Tailwind green-400 */
        background-color: #fff7ed; /* Tailwind green-50 */
    }
</style>

@endsection