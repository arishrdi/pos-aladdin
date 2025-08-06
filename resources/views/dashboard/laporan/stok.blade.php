@extends('layouts.app')

@section('title', 'Laporan Stok')

@section('content')

<!-- Alert Notification -->
<div id="alertContainer" class="fixed top-4 right-4 z-50 space-y-3 w-80">
    <!-- Alert will appear here dynamically -->
</div>

<!-- Page Title + Action -->
<div class="mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
       <h1 class="text-4xl font-bold text-gray-800">Laporan Stok</h1>
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
            <h4 class="text-lg font-semibold text-gray-800"><span id="outletName">Loading...</span></h4>
            <p class="text-sm text-gray-600">Periode: <span id="dateRangeDisplay">s/d {{ date('d M Y') }}</span></p>
        </div>
    </div>
</div>

<!-- Produk Dengan Stock Masuk Terbanyak -->
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-800">Laporan Stok</h1>
        <p class="text-sm text-gray-600" id="outletSubtitle">Perubahan stok produk di Aladdin Karpet Pusat</p>
        
        <!-- Filter + Search -->
        <div class="flex flex-col md:flex-row md:items-end gap-4 mt-3 w-full">
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
    </div>
    
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-6">
        <!-- Total Saldo Awal -->
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Saldo Awal</p>
                    <h3 class="text-2xl font-bold text-gray-800" id="totalSaldoAwal">0 pcs</h3>
                </div>
                <div class="p-3 bg-blue-50 rounded-full">
                    <i data-lucide="box" class="w-6 h-6 text-blue-500"></i>
                </div>
            </div>
        </div>
        
        <!-- Total Stock Masuk -->
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Stock Masuk</p>
                    <h3 class="text-2xl font-bold text-gray-800" id="totalStockMasuk">0 pcs</h3>
                </div>
                <div class="p-3 bg-green-50 rounded-full">
                    <i data-lucide="arrow-down-circle" class="w-6 h-6 text-green-500"></i>
                </div>
            </div>
        </div>
        
        <!-- Total Stock Keluar -->
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Stock Keluar</p>
                    <h3 class="text-2xl font-bold text-gray-800" id="totalStockKeluar">0 pcs</h3>
                </div>
                <div class="p-3 bg-red-50 rounded-full">
                    <i data-lucide="arrow-up-circle" class="w-6 h-6 text-red-500"></i>
                </div>
            </div>
        </div>
        
        <!-- Total Stock Akhir -->
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Stock Akhir</p>
                    <h3 class="text-2xl font-bold text-gray-800" id="totalStockAkhir">0 pcs</h3>
                </div>
                <div class="p-3 bg-purple-50 rounded-full">
                    <i data-lucide="package-check" class="w-6 h-6 text-purple-500"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Data Produk -->
    <h2 class="text-xl font-bold text-gray-800 mt-8 mb-4 flex items-center gap-2">
        <i data-lucide="database" class="w-5 h-5 text-blue-500"></i>
        Data Produk
    </h2>
    
    <div class="overflow-x-auto mb-8">
        <table class="w-full text-sm">
            <thead class="text-left text-gray-700 bg-gray-50">
                <tr>
                    <th class="py-3 font-bold px-4">Produk</th>
                    <th class="py-3 font-bold px-4">Satuan</th>
                    <th class="py-3 font-bold px-4 text-right">Saldo Awal</th>
                    <th class="py-3 font-bold px-4 text-right">Stock Masuk</th>
                    <th class="py-3 font-bold px-4 text-right">Stock Keluar</th>
                    <th class="py-3 font-bold px-4 text-right">Stock Akhir</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 divide-y" id="productTable">
                <!-- Data will be loaded here from the API -->
            </tbody>
        </table>
    </div>

    <h2 class="text-xl font-bold text-gray-800 mt-8 flex items-center gap-2">
        <i data-lucide="package-plus" class="w-5 h-5 text-green-500"></i>
        Produk Dengan Stock Masuk Terbanyak
    </h2>
    
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="text-left text-gray-700 bg-gray-50">
                <tr>
                    <th class="py-3 font-bold px-4">Produk</th>
                    <th class="py-3 font-bold px-4 text-right">Qty</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 divide-y" id="stockInTable">
                <!-- Data will be loaded here from the API -->
            </tbody>
        </table>
    </div>

    <h2 class="text-xl font-bold text-gray-800 mb-4 mt-6 flex items-center gap-2">
        <i data-lucide="package-minus" class="w-5 h-5 text-red-500"></i>
        Produk Dengan Stock Keluar Terbanyak
    </h2>
    
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="text-left text-gray-700 bg-gray-50">
                <tr>
                    <th class="py-3 font-bold px-4">Produk</th>
                    <th class="py-3 font-bold px-4 text-right">Qty</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 divide-y" id="stockOutTable">
                <!-- Data will be loaded here from the API -->
            </tbody>
        </table>
    </div>
</div>

<!-- Flatpickr JS -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>

<script>
    // Inventory data from API
    let inventoryData = {
        outlet: '',
        periode: {
            start_date: '',
            end_date: ''
        },
        products: [],
        summary: {
            total_saldo_awal: 0,
            total_stock_masuk: 0,
            total_stock_keluar: 0,
            total_stock_akhir: 0,
            total_selisih: 0,
            produk_stok_negatif: 0,
            produk_tidak_sesuai: 0
        }
    };

    // Format date for API
    function formatDateForAPI(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    // Format date for display
    function formatDateForDisplay(dateStr) {
        const date = new Date(dateStr);
        const options = { day: 'numeric', month: 'long', year: 'numeric' };
        return date.toLocaleDateString('id-ID', options);
    }

    // Get selected outlet ID - matches the implementation from the sales-by-member report
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

    // Connect outlet selection to report updates
    function connectOutletSelectionToReport() {
        // Listen for outlet changes in localStorage
        window.addEventListener('storage', function(event) {
            if (event.key === 'selectedOutletId') {
                // Reload report with new outlet
                fetchInventoryData();
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
                        fetchInventoryData();
                    }, 100);
                }
            });
        }
    }

    // Initialize date range picker
    function initializeDateRangePicker() {
        // Set default date to today
        const today = new Date();
        const todayFormatted = formatDateForAPI(today);
        
        flatpickr("#dateRange", {
            mode: "range",
            dateFormat: "d M Y",
            defaultDate: [today, today], // Set default to today only
            locale: "id",
            onChange: function(selectedDates, dateStr) {
                if (selectedDates.length === 2) {
                    const startDate = formatDateForAPI(selectedDates[0]);
                    const endDate = formatDateForAPI(selectedDates[1]);
                    
                    // Update display
                    document.getElementById('dateRangeDisplay').textContent = 
                        `${formatDateForDisplay(startDate)} - ${formatDateForDisplay(endDate)}`;
                    
                    // Fetch data with new date range
                    fetchInventoryData(startDate, endDate);
                } else if (selectedDates.length === 1) {
                    // Handle single date selection (same day)
                    const date = formatDateForAPI(selectedDates[0]);
                    document.getElementById('dateRangeDisplay').textContent = formatDateForDisplay(date);
                    fetchInventoryData(date, date);
                }
            }
        });
        
        // Set initial display to today
        document.getElementById('dateRangeDisplay').textContent = formatDateForDisplay(todayFormatted);
    }

    // Fetch inventory data from API
    async function fetchInventoryData(startDate = null, endDate = null) {
        try {
            // Default to today if no dates provided
            const today = new Date();
            const todayFormatted = formatDateForAPI(today);
            
            if (!startDate) startDate = todayFormatted;
            if (!endDate) endDate = todayFormatted;
            
            // Get loading indicator element safely
            const loadingIndicator = document.getElementById('loadingIndicator');
            if (loadingIndicator) loadingIndicator.style.display = 'block';
            
            // Get the outlet ID
            const outletId = getSelectedOutletId();
            
            // Build the URL with the outlet ID
            const url = `/api/reports/monthly-inventory/${outletId}?start_date=${startDate}&end_date=${endDate}`;
            
            const response = await fetch(url, {
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('token')}`,
                    'Accept': 'application/json'
                }
            });
            const result = await response.json();
            
            if (result.success) {
                inventoryData = result.data;
                updateUI();
                showAlert('success', 'Data berhasil dimuat');
            } else {
                showAlert('error', 'Gagal memuat data');
            }
        } catch (error) {
            console.error('Error fetching data:', error);
            showAlert('error', 'Gagal terhubung ke server');
        } finally {
            const loadingIndicator = document.getElementById('loadingIndicator');
            if (loadingIndicator) loadingIndicator.style.display = 'none';
        }
    }
    
    // Update UI with data
    function updateUI() {
        // Update outlet information
        document.getElementById('outletName').textContent = `Menampilkan laporan untuk: ${inventoryData.outlet}`;
        document.getElementById('outletSubtitle').textContent = `Perubahan stok produk di ${inventoryData.outlet}`;
        
        // Update summary cards
        document.getElementById('totalSaldoAwal').textContent = `${inventoryData.summary.total_saldo_awal} pcs`;
        document.getElementById('totalStockMasuk').textContent = `${inventoryData.summary.total_stock_masuk} pcs`;
        document.getElementById('totalStockKeluar').textContent = `${inventoryData.summary.total_stock_keluar} pcs`;
        document.getElementById('totalStockAkhir').textContent = `${inventoryData.summary.total_stock_akhir} pcs`;
        
        // Update product table
        updateProductTable();
        
        // Update stock in and stock out tables
        updateStockTables();
    }

    // Update main product table
    function updateProductTable() {
        const productTable = document.getElementById('productTable');
        productTable.innerHTML = '';
        
        const filteredProducts = filterProducts();
        
        filteredProducts.forEach(product => {
            productTable.innerHTML += `
                <tr>
                    <td class="py-4 px-4">${product.product_name}</td>
                    <td class="py-4 px-4">${product.unit}</td>
                    <td class="py-4 px-4 text-right">${product.saldo_awal}</td>
                    <td class="py-4 px-4 text-right">${product.stock_masuk}</td>
                    <td class="py-4 px-4 text-right">${product.stock_keluar}</td>
                    <td class="py-4 px-4 text-right">${product.stock_akhir}</td>
                </tr>
            `;
        });
        
        if (filteredProducts.length === 0) {
            productTable.innerHTML = `
                <tr>
                    <td colspan="6" class="py-4 px-4 text-center text-gray-500">Tidak ada data produk</td>
                </tr>
            `;
        }
    }

    // Update stock in and stock out tables
    function updateStockTables() {
        // Get products sorted by stock_masuk (descending)
        const productsWithStockIn = [...inventoryData.products]
            .filter(product => product.stock_masuk > 0)
            .sort((a, b) => b.stock_masuk - a.stock_masuk)
            .slice(0, 5);
            
        // Get products sorted by stock_keluar (descending)
        const productsWithStockOut = [...inventoryData.products]
            .filter(product => product.stock_keluar > 0)
            .sort((a, b) => b.stock_keluar - a.stock_keluar)
            .slice(0, 5);
        
        // Update stock in table
        const stockInTable = document.getElementById('stockInTable');
        stockInTable.innerHTML = '';
        
        if (productsWithStockIn.length > 0) {
            productsWithStockIn.forEach(product => {
                stockInTable.innerHTML += `
                    <tr>
                        <td class="py-4 px-4">${product.product_name}</td>
                        <td class="py-4 px-4 text-right">${product.stock_masuk} ${product.unit}</td>
                    </tr>
                `;
            });
        } else {
            stockInTable.innerHTML = `
                <tr>
                    <td colspan="2" class="py-4 px-4 text-center text-gray-500">Tidak ada data stock masuk</td>
                </tr>
            `;
        }
        
        // Update stock out table
        const stockOutTable = document.getElementById('stockOutTable');
        stockOutTable.innerHTML = '';
        
        if (productsWithStockOut.length > 0) {
            productsWithStockOut.forEach(product => {
                stockOutTable.innerHTML += `
                    <tr>
                        <td class="py-4 px-4">${product.product_name}</td>
                        <td class="py-4 px-4 text-right">${product.stock_keluar} ${product.unit}</td>
                    </tr>
                `;
            });
        } else {
            stockOutTable.innerHTML = `
                <tr>
                    <td colspan="2" class="py-4 px-4 text-center text-gray-500">Tidak ada data stock keluar</td>
                </tr>
            `;
        }
    }

    // Filter products based on search
    function filterProducts() {
        const searchTerm = document.getElementById('searchInput').value.trim().toLowerCase();
        
        if (!searchTerm) {
            return inventoryData.products;
        }
        
        return inventoryData.products.filter(product => 
            product.product_name.toLowerCase().includes(searchTerm) ||
            product.product_code.toLowerCase().includes(searchTerm)
        );
    }

    // Search input handler
    document.getElementById('searchInput').addEventListener('keyup', function(e) {
        updateProductTable();
    });

    // Print report function
    function printReport() {
        if (!inventoryData || inventoryData.products.length === 0) {
            showAlert('error', 'Tidak ada data untuk dicetak');
            return;
        }

        showAlert('info', 'Mempersiapkan laporan untuk dicetak...');

        setTimeout(() => {
        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>Laporan Stok Bulanan</title>
                <style>
                    body { font-family: Arial, sans-serif; margin: 20px; }
                    h1, h2 { color: #333; }
                    table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
                    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                    th { background-color: #f2f2f2; }
                    .text-right { text-align: right; }
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
                        <h1>LAPORAN STOK BULANAN</h1>
                        <div class="header-info">
                            Outlet: ${inventoryData.outlet}<br>
                            Periode: ${inventoryData.periode.start_date} hingga ${inventoryData.periode.end_date}<br>
                            Dicetak pada: ${new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })}
                        </div>
                    </div>
                </div>
                <hr>

                <table>
                    <thead>
                        <tr>
                            <th>Nama Produk</th>
                            <th>Satuan</th>
                            <th class="text-right">Saldo Awal</th>
                            <th class="text-right">Stok Masuk</th>
                            <th class="text-right">Stok Keluar</th>
                            <th class="text-right">Stok Akhir</th>
                        </tr>
                    </thead>
                    <tbody>
        `);

        inventoryData.products.forEach(product => {
            printWindow.document.write(`
                <tr>
                    <td>${product.product_name}</td>
                    <td>${product.unit}</td>
                    <td class="text-right">${product.saldo_awal}</td>
                    <td class="text-right">${product.stock_masuk}</td>
                    <td class="text-right">${product.stock_keluar}</td>
                    <td class="text-right">${product.stock_akhir}</td>
                </tr>
            `);
        });

        printWindow.document.write(`
                    </tbody>
                </table>

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

    // Export report function
    function exportReport() {
        if (!inventoryData || inventoryData.products.length === 0) {
            showAlert('error', 'Tidak ada data untuk diekspor');
            return;
        }
        
        showAlert('info', 'Mempersiapkan laporan untuk diekspor...');
        
        setTimeout(() => {
            try {
                // Create CSV content
                let csvContent = "data:text/csv;charset=utf-8,";
                
                // Add headers
                csvContent += "Produk,Satuan,Saldo Awal,Stock Masuk,Stock Keluar,Stock Akhir\n";
                
                // Add data rows
                inventoryData.products.forEach(product => {
                    csvContent += `"${product.product_name}",`;
                    csvContent += `${product.unit},`;
                    csvContent += `${product.saldo_awal},`;
                    csvContent += `${product.stock_masuk},`;
                    csvContent += `${product.stock_keluar},`;
                    csvContent += `${product.stock_akhir}\n`;
                });
                
                // Create download link
                const encodedUri = encodeURI(csvContent);
                const link = document.createElement("a");
                link.setAttribute("href", encodedUri);
                link.setAttribute("download", `laporan-stok-${inventoryData.outlet}-${inventoryData.periode.start_date}.csv`);
                document.body.appendChild(link);
                
                // Download file
                link.click();
                document.body.removeChild(link);
                
                showAlert('success', 'Laporan berhasil diekspor');
            } catch (error) {
                console.error('Error exporting data:', error);
                showAlert('error', 'Gagal mengekspor data');
            }
        }, 1000);
    }

    // Show alert function
    function showAlert(type, message) {
        const alertContainer = document.getElementById('alertContainer');
        const alert = document.createElement('div');
        alert.className = `px-4 py-3 rounded-lg shadow-md ${
            type === 'error' ? 'bg-red-100 text-red-700' : 
            type === 'success' ? 'bg-green-100 text-green-700' : 'bg-green-100 text-green-700'
        }`;
        alert.innerHTML = `
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i data-lucide="${
                        type === 'error' ? 'alert-circle' : 
                        type === 'success' ? 'check-circle' : 'info'
                    }" class="w-5 h-5"></i>
                    <span>${message}</span>
                </div>
                <button onclick="this.parentElement.parentElement.remove()">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
        `;
        alertContainer.appendChild(alert);

        if (window.lucide) {
            window.lucide.createIcons();
        }
        
        // Auto remove alert after 5 seconds
        setTimeout(() => {
            alert.remove();
        }, 5000);
    }

    // Initialize data load and setup outlet selection
    document.addEventListener('DOMContentLoaded', () => {
        // Initialize date range picker
        initializeDateRangePicker();
        
        // Connect outlet selection to report updates
        connectOutletSelectionToReport();
        
        // Initial data fetch
        fetchInventoryData();
    });
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

    /* Hari ini */
    .flatpickr-day.today:not(.selected):not(.inRange) {
        border: 1px solid #fb923c; /* Tailwind green-400 */
        background-color: #fff7ed; /* Tailwind green-50 */
    }
</style>

@endsection