@extends('layouts.app')

@section('title', 'Laporan Penjualan Per Item')

@section('content')

<!-- Alert Notification -->
<div id="alertContainer" class="fixed top-4 right-4 z-50 space-y-3 w-80">
    <!-- Alert will appear here dynamically -->
</div>

<!-- Page Title + Action -->
<div class="mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
       <h1 class="text-4xl font-bold text-gray-800">Laporan Per Item</h1>
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

<!-- Card: Stok Info + Aksi -->
<div class="bg-white rounded-md p-4 shadow-md mb-4 flex flex-col md:flex-row md:items-center md:justify-between">
    <!-- Kiri: Judul -->
    <div class="mb-3 md:mb-0 flex items-start gap-2">
        <i data-lucide="package" class="w-5 h-5 text-gray-600 mt-1"></i>
        <div>
            <h4 class="text-lg font-semibold text-gray-800">Menampilkan laporan untuk: <span id="outletName">Loading...</span></h4>
            <p class="text-sm text-gray-600">Data yang ditampilkan adalah khusus untuk outlet <span id="outletNameSub">Loading...</span>.</p>
        </div>
    </div>
</div>

<!-- Analisis Produk -->
<div class="bg-white rounded-lg shadow-lg p-6 mb-6">
    <!-- Header + Filter -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Daftar Produk</h1>
            <p class="text-sm text-gray-600">Daftar produk berdasarkan rentang tanggal untuk <span id="outletNameHeader">Loading...</span></p>
            
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
                <!-- Cari Produk/Kategori -->
                <div class="flex-1">
                    <h2 class="text-sm font-medium text-gray-800 mb-1">Cari Produk/Kategori</h2>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i data-lucide="search" class="w-5 h-5 text-gray-400"></i>
                        </span>
                        <input type="text" id="searchInput" placeholder="Cari..."
                            class="w-full pl-10 pr-4 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cards Summary -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Total Penjualan -->
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Penjualan</p>
                    <h3 class="text-2xl font-bold text-gray-800" id="totalSales">Loading...</h3>
                </div>
                <div class="p-3 rounded-full bg-green-100 text-green-500">
                    <i data-lucide="dollar-sign" class="w-6 h-6"></i>
                </div>
            </div>
            <p class="text-xs text-gray-500 mt-2" id="totalSalesComparison">Memuat data perbandingan...</p>
        </div>

        <!-- Total Kuantitas -->
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Kuantitas</p>
                    <h3 class="text-2xl font-bold text-gray-800" id="totalQuantity">Loading...</h3>
                </div>
                <div class="p-3 rounded-full bg-blue-100 text-blue-500">
                    <i data-lucide="package" class="w-6 h-6"></i>
                </div>
            </div>
            <p class="text-xs text-gray-500 mt-2" id="totalQuantityComparison">Memuat data perbandingan...</p>
        </div>

        <!-- Total Transaksi -->
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Transaksi</p>
                    <h3 class="text-2xl font-bold text-gray-800" id="totalOrders">Loading...</h3>
                </div>
                <div class="p-3 rounded-full bg-green-100 text-green-500">
                    <i data-lucide="shopping-bag" class="w-6 h-6"></i>
                </div>
            </div>
            <p class="text-xs text-gray-500 mt-2" id="totalOrdersComparison">Memuat data perbandingan...</p>
        </div>

        <!-- Rata-rata/Transaksi -->
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Rata-rata/Transaksi</p>
                    <h3 class="text-2xl font-bold text-gray-800" id="averageOrderValue">Loading...</h3>
                </div>
                <div class="p-3 rounded-full bg-purple-100 text-purple-500">
                    <i data-lucide="bar-chart-2" class="w-6 h-6"></i>
                </div>
            </div>
            <p class="text-xs text-gray-500 mt-2" id="averageOrderValueComparison">Memuat data perbandingan...</p>
        </div>
    </div>
    
    <!-- Tabel Produk -->
    <div class="overflow-x-auto mt-6">
        <table class="w-full text-sm">
            <thead class="text-left text-gray-700 border-b-2">
                <tr>
                    <th class="py-3 font-bold">SKU</th>
                    <th class="py-3 font-bold">Nama Produk</th>
                    <th class="py-3 font-bold">Kategori</th>
                    <th class="py-3 font-bold text-right">Jumlah Order</th>
                    <th class="py-3 font-bold text-right">Total Kuantitas</th>
                    <th class="py-3 font-bold text-right">Total Penjualan</th>
                    <th class="py-3 font-bold text-right">Kontribusi (%)</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 divide-y" id="productTableBody">
                <tr>
                    <td colspan="7" class="py-4 text-center">
                        <div class="flex flex-col items-center justify-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                                 class="animate-spin text-green-500 mx-auto">
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

<!-- Flatpickr JS -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>

<script>
    // Global variables
    const today = new Date();
    const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
    let currentStartDate = formatDateLocal(firstDay);
    let currentEndDate = formatDateLocal(today);
    // let outletId = 1;
    
    // Helper function to format quantity based on unit_type
    function formatQuantity(quantity, unit_type) {
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

    // Format currency to Indonesian Rupiah
    function formatRupiah(amount) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(amount);
    }
    
    // Initialize date range picker
    function formatDateLocal(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
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

    document.addEventListener('DOMContentLoaded', function () {
        const today = new Date();
        const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
        let currentStartDate = formatDateLocal(firstDay);
        let currentEndDate = formatDateLocal(today);
        const outletId = getSelectedOutletId();

        flatpickr("#dateRange", {
            mode: "range",
            dateFormat: "d M Y",
            defaultDate: [firstDay, today],
            locale: "id",
            onChange: function (selectedDates, dateStr) {
                if (selectedDates.length === 2) {
                    currentStartDate = formatDateLocal(selectedDates[0]);
                    currentEndDate = formatDateLocal(selectedDates[1]);
                    loadData(getSelectedOutletId(), currentStartDate, currentEndDate);
                }
            }
        });

        // Panggil data awal
        loadData(outletId, currentStartDate, currentEndDate);

        // Connect outlet selection to report updates
        connectOutletSelectionToReport();
    });

    // Connect outlet selection dropdown to report updates
    function connectOutletSelectionToReport() {
        // Listen for outlet changes in localStorage
        window.addEventListener('storage', function(event) {
            if (event.key === 'selectedOutletId') {
                // Reload report with new outlet
                loadData(event.newValue, currentStartDate, currentEndDate);
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
                        loadData(getSelectedOutletId(), currentStartDate, currentEndDate);
                    }, 100);
                }
            });
        }
    }
    
    // Load data from API
    function loadData(outletId, startDate, endDate) {
        // showAlert('info', 'Memuat data laporan...');
        
        const url = `/api/reports/monthly-sales/${outletId}?start_date=${startDate}&end_date=${endDate}`;
        
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
                if (data.status) {
                    updatePageData(data.data);
                    showAlert('success', 'Data berhasil dimuat');
                } else {
                    showAlert('error', 'Gagal memuat data');
                }
            })
            .catch(error => {
                console.error('Error fetching data:', error);
                showAlert('error', 'Terjadi kesalahan saat memuat data');
            });
    }
    
    // Update page with data from API
    function updatePageData(data) {
        // Update outlet name
        const outletName = data.outlet;
        document.getElementById('outletName').textContent = outletName;
        document.getElementById('outletNameSub').textContent = outletName;
        document.getElementById('outletNameHeader').textContent = outletName;
        
        // Update summary cards
        document.getElementById('totalSales').textContent = formatRupiah(data.summary.total_sales);
        document.getElementById('totalQuantity').textContent = data.summary.total_quantity + ' item';
        document.getElementById('totalOrders').textContent = data.summary.total_orders;
        document.getElementById('averageOrderValue').textContent = formatRupiah(data.summary.average_order_value);
        
        // For now, we don't have comparison data so use placeholder
        document.getElementById('totalSalesComparison').textContent = 'Data periode saat ini';
        document.getElementById('totalQuantityComparison').textContent = 'Data periode saat ini';
        document.getElementById('totalOrdersComparison').textContent = 'Data periode saat ini';
        document.getElementById('averageOrderValueComparison').textContent = 'Data periode saat ini';
        
        // Update product table
        const tableBody = document.getElementById('productTableBody');
        tableBody.innerHTML = '';
        
        if (data.products.length === 0) {
            const row = document.createElement('tr');
            row.innerHTML = `<td colspan="7" class="py-4 text-center">Tidak ada data produk untuk periode ini</td>`;
            tableBody.appendChild(row);
        } else {
            data.products.forEach(product => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td class="py-4 font-medium">${product.sku}</td>
                    <td class="py-4">${product.product_name}</td>
                    <td class="py-4">${product.category_name}</td>
                    <td class="py-4 text-right">${product.order_count}</td>
                    <td class="py-4 text-right">${formatQuantity(product.total_quantity, product.unit_type)}</td>
                    <td class="py-4 text-right font-bold">${formatRupiah(product.total_sales)}</td>
                    <td class="py-4 text-right">${product.sales_percentage.toFixed(1)}%</td>
                `;
                tableBody.appendChild(row);
            });
        }
    }
    
    // Search input handler
    document.getElementById('searchInput').addEventListener('keyup', function(e) {
        const searchTerm = this.value.trim().toLowerCase();
        const rows = document.querySelectorAll('#productTableBody tr');
        let foundCount = 0;
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            if (text.includes(searchTerm)) {
                row.style.display = '';
                foundCount++;
            } else {
                row.style.display = 'none';
            }
        });
        
        // if (searchTerm) {
        //     showAlert('info', `Menampilkan ${foundCount} hasil pencarian: "${searchTerm}"`);
        // }
    });
    
    // Print report function
    function printReport() {
        showAlert('info', 'Mempersiapkan laporan untuk dicetak...');

        setTimeout(() => {
            const outletName = document.getElementById('outletName').textContent;
            const dateRange = `${new Date(currentStartDate).toLocaleDateString('id-ID')} - ${new Date(currentEndDate).toLocaleDateString('id-ID')}`;
            const now = new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });

            const summary = {
                totalSales: document.getElementById('totalSales').textContent,
                totalQuantity: document.getElementById('totalQuantity').textContent,
                totalOrders: document.getElementById('totalOrders').textContent,
                averageOrderValue: document.getElementById('averageOrderValue').textContent
            };

            const tableRows = document.querySelectorAll('#productTableBody tr');

            let printContent = `
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Laporan Penjualan - Aladdin Karpet</title>
                    <style>
                        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; }
                        h1 { font-size: 18px; margin: 0 0 10px 0; }
                        .report-header { display: flex; align-items: center; gap: 20px; margin-bottom: 20px; }
                        .logo { width: 60px; height: auto; }
                        .header-info { font-size: 14px; }
                        .summary { display: flex; flex-wrap: wrap; gap: 10px; max-width: 600px; margin-bottom: 20px; }
                        .summary-item { flex: 1 1 calc(50% - 10px); background: #f4f4f4; padding: 10px; border-radius: 6px; box-sizing: border-box; }
                        table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 13px; }
                        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
                        th { background-color: #eee; }
                        .footer { margin-top: 30px; font-size: 12px; text-align: center; }
                        @media print {
                            body { padding: 0; }
                        }
                    </style>
                </head>
                <body>
                    <div class="report-header">
                        <img src="/images/logo.png" alt="Logo Aladdin Karpet" class="logo">
                        <div>
                            <h1>LAPORAN PENJUALAN</h1>
                            <div class="header-info">
                                Outlet: ${outletName}<br>
                                Tanggal: ${dateRange}<br>
                                Dicetak pada: ${now}
                            </div>
                        </div>
                    </div>

                    <div class="summary">
                        <div class="summary-item"><strong>Total Penjualan</strong><br>${summary.totalSales}</div>
                        <div class="summary-item"><strong>Total Order</strong><br>${summary.totalOrders}</div>
                        <div class="summary-item"><strong>Total Item</strong><br>${summary.totalQuantity}</div>
                        <div class="summary-item"><strong>Rata-rata Order</strong><br>${summary.averageOrderValue}</div>
                    </div>

                    <table>
                        <thead>
                            <tr>
                                <th>SKU</th>
                                <th>Nama Produk</th>
                                <th>Kategori</th>
                                <th>Jumlah Order</th>
                                <th>Total Kuantitas</th>
                                <th>Total Penjualan</th>
                                <th>Kontribusi (%)</th>
                            </tr>
                        </thead>
                        <tbody>`;

            tableRows.forEach(row => {
                if (row.style.display !== 'none') {
                    const cells = row.querySelectorAll('td');
                    if (!row.querySelector('td[colspan]')) {
                        printContent += '<tr>';
                        cells.forEach(cell => {
                            printContent += `<td>${cell.textContent}</td>`;
                        });
                        printContent += '</tr>';
                    }
                }
            });

            printContent += `
                        </tbody>
                    </table>

                    <div class="footer">
                        Laporan ini dibuat secara otomatis oleh sistem.<br>
                        © ${new Date().getFullYear()} Aladdin Karpet
                    </div>
                </body>
                </html>
            `;

            const printWindow = window.open('', '_blank');
            printWindow.document.open();
            printWindow.document.write(printContent);
            printWindow.document.close();

            printWindow.onload = function () {
                setTimeout(() => {
                    printWindow.print();
                    printWindow.close();
                }, 500);
            };
        }, 1000);
    }

    // Export report function
    function exportReport() {
        showAlert('info', 'Mempersiapkan laporan untuk diekspor...');

        setTimeout(() => {
            const outletName = document.getElementById('outletName').textContent;
            const formattedStartDate = new Date(currentStartDate).toLocaleDateString('id-ID');
            const formattedEndDate = new Date(currentEndDate).toLocaleDateString('id-ID');
            
            // In a real app, you would generate CSV content from actual data
            // Here we'll create a basic CSV from the visible table data
            let csvContent = 'data:text/csv;charset=utf-8,';
            csvContent += '"SKU","Nama Produk","Kategori","Jumlah Order","Total Kuantitas","Total Penjualan","Kontribusi (%)"\n';
            
            const rows = document.querySelectorAll('#productTableBody tr');
            rows.forEach(row => {
                // Skip hidden rows (filtered out by search)
                if (row.style.display !== 'none') {
                    // Skip rows with colspan (empty state messages)
                    if (!row.querySelector('td[colspan]')) {
                        const cells = row.querySelectorAll('td');
                        let rowData = [];
                        cells.forEach(cell => {
                            // Quote the cell data and escape any quotes inside it
                            rowData.push('"' + cell.innerText.replace(/"/g, '""') + '"');
                        });
                        csvContent += rowData.join(',') + '\n';
                    }
                }
            });
            
            // Create and trigger download
            const encodedUri = encodeURI(csvContent);
            const link = document.createElement('a');
            link.setAttribute('href', encodedUri);
            link.setAttribute('download', `laporan-peritem-${outletName}-${formattedStartDate}-${formattedEndDate}.csv`);
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        
            showAlert('success', 'Laporan berhasil diekspor');
        }, 1000);
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
        
        // Make sure Lucide icons are initialized for the new alert
        if (window.lucide) {
            window.lucide.createIcons();
        }
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (alert.parentNode) {
                alert.remove();
            }
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