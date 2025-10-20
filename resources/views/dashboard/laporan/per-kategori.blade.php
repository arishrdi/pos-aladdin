@extends('layouts.app')

@section('title', 'Laporan Per Kategori')

@section('content')

<!-- Alert Notification -->
<div id="alertContainer" class="fixed top-4 right-4 z-50 space-y-3 w-80">
    <!-- Alert will appear here dynamically -->
</div>

<!-- Page Title + Action -->
<div class="mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
       <h1 class="text-4xl font-bold text-gray-800">Laporan Per Kategori</h1>
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
            <h4 class="text-lg font-semibold text-gray-800" id="reportTitle">Memuat data outlet...</h4>
            <p class="text-sm text-gray-600" id="reportSubtitle">Data yang ditampilkan adalah khusus untuk outlet yang dipilih.</p>
        </div>
    </div>
    
    <!-- Kanan: Mode Komparasi -->
    <div class="flex items-center space-x-4">
        <div class="flex items-center space-x-2">
            <span class="text-sm text-gray-600">Mode Komparasi</span>
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" id="comparisonMode" class="sr-only peer">
                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
            </label>
        </div>
    </div>
</div>

<!-- Outlet Comparison Selector (Hidden by default) -->
<div id="outletComparisonSection" class="hidden mb-6">
    <div class="bg-white rounded-lg p-4 shadow-md">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Pilih Outlet untuk Komparasi</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div>
                <div class="flex justify-between items-center mb-2">
                    <p class="block text-sm font-medium text-gray-700">Outlet yang ingin dibandingkan:</p>
                    <button id="selectAllOutlets" class="text-xs bg-gray-100 hover:bg-gray-200 px-2 py-1 rounded transition-colors">
                        Pilih Semua
                    </button>
                </div>
                <div id="outletCheckboxContainer" class="space-y-2 max-h-48 overflow-y-auto">
                    <!-- Outlet checkboxes will be populated here -->
                </div>
            </div>
            <div class="md:col-span-2 lg:col-span-2">
                <div class="bg-green-50 p-4 rounded-lg">
                    <div class="flex items-start space-x-3">
                        <i data-lucide="info" class="text-green-600 mt-0.5 flex-shrink-0 w-5 h-5"></i>
                        <div>
                            <p class="text-sm font-medium text-green-800">Cara menggunakan mode komparasi:</p>
                            <ul class="text-sm text-green-700 mt-1 space-y-1">
                                <li>• Pilih minimal 2 outlet untuk mulai komparasi</li>
                                <li>• Data akan ditampilkan dalam bentuk tabel perbandingan kategori</li>
                                <li>• Gunakan rentang tanggal untuk mengatur periode komparasi</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-4 flex justify-between items-center">
            <p class="text-sm text-gray-600">
                <span id="selectedOutletsCount">0</span> outlet dipilih
            </p>
            <button id="startComparison" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                Mulai Komparasi
            </button>
        </div>
    </div>
</div>

<!-- Comparison Results (Hidden by default) -->
<div id="comparisonResults" class="hidden bg-white rounded-lg shadow-lg p-6 mb-6">
    <h2 class="text-xl font-bold text-gray-800 mb-4">Hasil Komparasi Kategori Per Outlet</h2>
    <div id="comparisonContent">
        <!-- Comparison data will be populated here -->
    </div>
</div>

<!-- Analisis Kategori -->
<div id="singleOutletReport" class="bg-white rounded-lg shadow-lg p-6 mb-6">
    <!-- Header + Filter -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Analisis Per Kategori</h1>
            <p class="text-sm text-gray-600">Grafik dan analisis penjualan berdasarkan kategori produk</p>
            
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
                <!-- Cari Kategori -->
                <div class="flex-1">
                    <h2 class="text-sm font-medium text-gray-800 mb-1">Cari Kategori</h2>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i data-lucide="search" class="w-5 h-5 text-gray-400"></i>
                        </span>
                        <input type="text" id="searchInput" placeholder="Cari kategori..."
                            class="w-full pl-10 pr-4 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grafik Batang -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Total Pendapatan per Kategori</h2>
            <div class="h-80">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>
        
        <!-- Summary Card -->
        <div class="bg-gray-100 rounded-lg shadow p-6 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="text-center">
                    <p class="text-sm font-medium text-gray-600">Total Kategori</p>
                    <h3 class="text-2xl font-bold text-gray-800" id="total_categories">0</h3>
                </div>
                <div class="text-center">
                    <p class="text-sm font-medium text-gray-600">Total Produk</p>
                    <h3 class="text-2xl font-bold text-gray-800" id="total_products">0</h3>
                </div>
                <div class="text-center">
                    <p class="text-sm font-medium text-gray-600">Total Kuantitas</p>
                    <h3 class="text-2xl font-bold text-gray-800" id="total_quantity">0</h3>
                </div>
                <div class="text-center">
                    <p class="text-sm font-medium text-gray-600">Total Penjualan</p>
                    <h3 class="text-2xl font-bold text-gray-800" id="total_sales">Rp 0</h3>
                </div>
            </div>
        </div>

        <!-- Categories Container - Dynamic content will be added here -->
        <div id="categories-container">
            <!-- Categories will be added here dynamically -->
            <div class="flex items-center justify-center py-8">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-500"></div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- Flatpickr JS -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>

<script>
    // Global variables
    let apiData = null;
    let categoryChart = null;
    const today = new Date();
    const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
    let currentStartDate = formatDateForApi(firstDay);
    let currentEndDate = formatDateForApi(today);
    let selectedOutlets = [];
    let isComparisonMode = false;

    // Initialize date range picker
    function getDefaultDateRange() {
        return [firstDay, today];
    }

    // Format date for API (YYYY-MM-DD)
    function formatDateForApi(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    // Initialize the page
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize date range picker
        flatpickr("#dateRange", {
            mode: "range",
            dateFormat: "d M Y",
            defaultDate: getDefaultDateRange(),
            locale: "id",
            onChange: function(selectedDates, dateStr) {
                if (selectedDates.length === 2) {
                    currentStartDate = formatDateForApi(selectedDates[0]);
                    currentEndDate = formatDateForApi(selectedDates[1]);
                    fetchData();
                }
            }
        });

        // Load initial data
        fetchData();
        
        // Connect outlet selection to report updates
        connectOutletSelectionToReport();
    });

    // Fetch data from API
    async function fetchData() {
        try {
            const outletId = getSelectedOutletId();
            
            const response = await fetch(`/api/reports/sales-by-category/${outletId}?start_date=${currentStartDate}&end_date=${currentEndDate}`, {
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('token')}`,
                    'Accept': 'application/json'
                }
            });
            
            if (!response.ok) {
                throw new Error('Failed to fetch data');
            }
            
            apiData = await response.json();
            
            if (apiData.status) {
                updateUI(apiData.data);
                showAlert('success', 'Data berhasil dimuat');
            } else {
                throw new Error('API returned error status');
            }
        } catch (error) {
            console.error('Error fetching data:', error);
            showAlert('error', 'Gagal memuat data: ' + error.message);
        }
    }

    // Format currency
    function formatCurrency(amount) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(amount);
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
                fetchData();
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
                        fetchData();
                    }, 100);
                }
            });
        }
    }

    

    // Update UI with API data
    function updateUI(data) {
        // Update outlet name
        document.getElementById('reportTitle').textContent = `Menampilkan laporan untuk: ${data.outlet}`;
        document.getElementById('reportSubtitle').textContent = `Data yang ditampilkan adalah khusus untuk outlet ${data.outlet}.`;

        // Update summary card
        document.getElementById('total_categories').textContent = data.summary.total_categories;
        document.getElementById('total_products').textContent = data.summary.total_products;
        document.getElementById('total_quantity').textContent = data.summary.total_quantity;
        document.getElementById('total_sales').textContent = formatCurrency(data.summary.total_sales);

        // Update chart
        updateChart(data.categories);

        // Update categories and products tables
        updateCategoriesTables(data.categories);
    }

    // Update chart with category data
    function updateChart(categories) {
        const labels = categories.map(cat => cat.category_name);
        const dataValues = categories.map(cat => cat.total_sales);
        
        // Destroy existing chart if it exists
        if (categoryChart) {
            categoryChart.destroy();
        }
        
        // Create new chart
        const ctx = document.getElementById('categoryChart').getContext('2d');
        categoryChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Total Pendapatan (Rp)',
                    data: dataValues,
                    backgroundColor: 'rgba(255, 159, 64, 0.7)',
                    borderColor: 'rgba(255, 159, 64, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return formatCurrency(value);
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return formatCurrency(context.raw);
                            }
                        }
                    }
                }
            }
        });
    }

    // Update categories tables
    function updateCategoriesTables(categories) {
        const container = document.getElementById('categories-container');
        container.innerHTML = ''; // Clear previous content
        
        // Get category icons mapping
        const categoryIcons = {
            'Cake': 'cake',
            'Roti': 'croissant',
            'Pastry': 'pie-chart',
            'Minuman': 'coffee',
            'Kue Basah': 'cookie',
            'default': 'shopping-basket'
        };
        
        // Generate HTML for each category
        categories.forEach(category => {
            const iconName = categoryIcons[category.category_name] || categoryIcons.default;
            const iconColor = getRandomColor(category.category_name);
            
            let categoryHtml = `
                <div class="mb-8">
                    <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <i data-lucide="${iconName}" class="w-5 h-5 text-${iconColor}-500"></i>
                        ${category.category_name}
                    </h2>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="text-left text-gray-700 bg-gray-50">
                                <tr>
                                    <th class="py-3 font-bold px-4">SKU</th>
                                    <th class="py-3 font-bold px-4">Nama Produk</th>
                                    <th class="py-3 font-bold px-4 text-right">Kuantitas</th>
                                    <th class="py-3 font-bold px-4 text-right">Penjualan</th>
                                    <th class="py-3 font-bold px-4 text-right">Kontribusi (%)</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-700 divide-y">
            `;
            
            // Add products for this category
            category.products.forEach(product => {
                categoryHtml += `
                    <tr>
                        <td class="py-4 font-medium px-4">${product.product_sku}</td>
                        <td class="py-4 px-4">${product.product_name}</td>
                        <td class="py-4 px-4 text-right">${product.quantity}</td>
                        <td class="py-4 px-4 text-right font-bold">${formatCurrency(product.sales)}</td>
                        <td class="py-4 px-4 text-right">${product.sales_percentage}%</td>
                    </tr>
                `;
            });
            
            // Add category footer
            categoryHtml += `
                            </tbody>
                            <tfoot class="bg-gray-50 font-semibold">
                                <tr>
                                    <td class="py-3 px-4" colspan="2">Total ${category.category_name}</td>
                                    <td class="py-3 px-4 text-right">${category.total_quantity}</td>
                                    <td class="py-3 px-4 text-right">${formatCurrency(category.total_sales)}</td>
                                    <td class="py-3 px-4 text-right">${category.sales_percentage}%</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            `;
            
            container.innerHTML += categoryHtml;
        });
        
        // Initialize Lucide icons after adding new DOM elements
        if (window.lucide) {
            lucide.createIcons();
        }
    }

    // Get a consistent color based on category name
    function getRandomColor(categoryName) {
        const colorOptions = ['pink', 'amber', 'emerald', 'blue', 'purple', 'indigo', 'red', 'green'];
        let sum = 0;
        for (let i = 0; i < categoryName.length; i++) {
            sum += categoryName.charCodeAt(i);
        }
        return colorOptions[sum % colorOptions.length];
    }

    // Search input handler
    document.getElementById('searchInput').addEventListener('keyup', function(e) {
        const searchTerm = this.value.trim().toLowerCase();
        const categorySections = document.querySelectorAll('#categories-container > div');
        
        categorySections.forEach(section => {
            const heading = section.querySelector('h2').textContent.toLowerCase();
            if (heading.includes(searchTerm)) {
                section.style.display = '';
            } else {
                section.style.display = 'none';
            }
        });
        
        // if (searchTerm) {
        //     showAlert('info', `Menampilkan hasil pencarian: ${searchTerm}`);
        // }
    });
    
    // Print report function
    function printReport() {
        if (!apiData || !apiData.data) {
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
                <title>Laporan Penjualan Per Kategori</title>
                <style>
                    body { font-family: Arial, sans-serif; margin: 20px; }
                    h1, h2 { color: #333; }
                    table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
                    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                    th { background-color: #f2f2f2; }
                    .text-right { text-align: right; }
                    .summary-card { margin-bottom: 30px; padding: 15px; background: #f9f9f9; border-radius: 5px; }
                    .summary-item { display: inline-block; margin-right: 20px; }
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
                        <h1>LAPORAN PENJUALAN PER KATEGORI</h1>
                        <div class="header-info">
                            Outlet: ${apiData.data.outlet}<br>
                            Periode: ${apiData.data.date_range.start_date} hingga ${apiData.data.date_range.end_date}<br>
                            Dicetak pada: ${new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })}
                        </div>
                    </div>
                </div>
                <hr>

                <div class="summary-card">
                    <h2>Ringkasan</h2>
                    <div class="summary-item"><strong>Total Kategori:</strong> ${apiData.data.summary.total_categories}</div>
                    <div class="summary-item"><strong>Total Produk:</strong> ${apiData.data.summary.total_products}</div>
                    <div class="summary-item"><strong>Total Kuantitas:</strong> ${apiData.data.summary.total_quantity}</div>
                    <div class="summary-item"><strong>Total Penjualan:</strong> ${formatCurrency(apiData.data.summary.total_sales)}</div>
                </div>
        `);

        // Add categories data
        apiData.data.categories.forEach(category => {
            printWindow.document.write(`
                <h2>${category.category_name}</h2>
                <table>
                    <thead>
                        <tr>
                            <th>SKU</th>
                            <th>Nama Produk</th>
                            <th class="text-right">Kuantitas</th>
                            <th class="text-right">Penjualan</th>
                            <th class="text-right">Kontribusi (%)</th>
                        </tr>
                    </thead>
                    <tbody>
            `);

            // Add products
            category.products.forEach(product => {
                printWindow.document.write(`
                    <tr>
                        <td>${product.product_sku}</td>
                        <td>${product.product_name}</td>
                        <td class="text-right">${product.quantity}</td>
                        <td class="text-right">${formatCurrency(product.sales)}</td>
                        <td class="text-right">${product.sales_percentage}%</td>
                    </tr>
                `);
            });

            // Add category footer
            printWindow.document.write(`
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2"><strong>Total ${category.category_name}</strong></td>
                            <td class="text-right"><strong>${category.total_quantity}</strong></td>
                            <td class="text-right"><strong>${formatCurrency(category.total_sales)}</strong></td>
                            <td class="text-right"><strong>${category.sales_percentage}%</strong></td>
                        </tr>
                    </tfoot>
                </table>
            `);
        });

        // Add footer and close
        printWindow.document.write(`
                <div class="footer">
                    Laporan dicetak pada ${new Date().toLocaleString('id-ID')}
                </div>
            </body>
            </html>
        `);

        printWindow.document.close();

        // Wait for content to load before printing
        setTimeout(() => {
            printWindow.print();
            printWindow.close();
        }, 500);
        }, 1000);
    }

    // Export report function
    function exportReport() {
        if (!apiData || !apiData.data) {
            showAlert('error', 'Tidak ada data untuk diekspor');
            return;
        }
        
        showAlert('info', 'Mempersiapkan laporan untuk diekspor...');
        
        setTimeout(() => {
            try {
                // Create CSV content
                let csvContent = "data:text/csv;charset=utf-8,";
                csvContent += "Laporan Penjualan Per Kategori\n";
                csvContent += `Outlet: ${apiData.data.outlet}\n`;
                csvContent += `Periode: ${apiData.data.date_range.start_date} s/d ${apiData.data.date_range.end_date}\n\n`;
                
                // Add summary
                csvContent += "Ringkasan\n";
                csvContent += `Total Kategori,${apiData.data.summary.total_categories}\n`;
                csvContent += `Total Produk,${apiData.data.summary.total_products}\n`;
                csvContent += `Total Kuantitas,${apiData.data.summary.total_quantity}\n`;
                csvContent += `Total Penjualan,${apiData.data.summary.total_sales}\n\n`;
                
                // Add category details
                apiData.data.categories.forEach(category => {
                    csvContent += `Kategori: ${category.category_name}\n`;
                    csvContent += "SKU,Nama Produk,Kuantitas,Penjualan,Kontribusi (%)\n";
                    
                    // Add products
                    category.products.forEach(product => {
                        csvContent += `${product.product_sku},${product.product_name},${product.quantity},${product.sales},${product.sales_percentage}\n`;
                    });
                    
                    csvContent += `Total,${category.category_name},${category.total_quantity},${category.total_sales},${category.sales_percentage}\n\n`;
                });
                
                // Create and trigger download
                const encodedUri = encodeURI(csvContent);
                const link = document.createElement("a");
                link.setAttribute("href", encodedUri);
                link.setAttribute("download", `laporan-kategori-${apiData.data.date_range.start_date}_${apiData.data.date_range.end_date}.csv`);
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                
                showAlert('success', 'Laporan berhasil diekspor');
            } catch (error) {
                console.error('Error exporting report:', error);
                showAlert('error', 'Gagal mengekspor laporan: ' + error.message);
            }

            // showAlert('success', 'Laporan berhasil diekspor');
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
        
        // Initialize Lucide icons in the alert
        if (window.lucide) {
            lucide.createIcons();
        }
        
        setTimeout(() => {
            alert.remove();
        }, 5000);
    }

    // Comparison mode functions
    async function loadOutletsForComparison() {
        try {
            const response = await fetch('/api/outlets', {
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('token')}`,
                    'Accept': 'application/json'
                }
            });
            const data = await response.json();
            
            if (data.success) {
                populateOutletCheckboxes(data.data);
            }
        } catch (error) {
            console.error('Error loading outlets:', error);
            showAlert('error', 'Gagal memuat daftar outlet');
        }
    }

    function populateOutletCheckboxes(outlets) {
        const container = document.getElementById('outletCheckboxContainer');
        container.innerHTML = '';
        
        outlets.forEach(outlet => {
            const checkboxDiv = document.createElement('div');
            checkboxDiv.className = 'flex items-center space-x-2';
            checkboxDiv.innerHTML = `
                <input type="checkbox" id="outlet_${outlet.id}" value="${outlet.id}" 
                       class="outlet-checkbox rounded border-gray-300 text-green-600 focus:ring-green-500">
                <label for="outlet_${outlet.id}" class="text-sm text-gray-700 cursor-pointer">${outlet.name}</label>
            `;
            container.appendChild(checkboxDiv);
        });
        
        // Add event listeners to checkboxes
        document.querySelectorAll('.outlet-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', updateSelectedOutlets);
        });
        
        // Add event listener to Select All button
        const selectAllButton = document.getElementById('selectAllOutlets');
        if (selectAllButton) {
            selectAllButton.addEventListener('click', toggleSelectAllOutlets);
        }
        
        updateSelectedOutlets();
    }

    function toggleSelectAllOutlets() {
        const checkboxes = document.querySelectorAll('.outlet-checkbox');
        const selectAllButton = document.getElementById('selectAllOutlets');
        const allChecked = Array.from(checkboxes).every(checkbox => checkbox.checked);
        
        checkboxes.forEach(checkbox => {
            checkbox.checked = !allChecked;
        });
        
        selectAllButton.textContent = allChecked ? 'Pilih Semua' : 'Batal Pilih';
        updateSelectedOutlets();
    }

    function updateSelectedOutlets() {
        selectedOutlets = [];
        const checkboxes = document.querySelectorAll('.outlet-checkbox');
        const checkedBoxes = document.querySelectorAll('.outlet-checkbox:checked');
        const selectAllButton = document.getElementById('selectAllOutlets');
        
        checkedBoxes.forEach(checkbox => {
            const outletId = parseInt(checkbox.value);
            const outletName = checkbox.nextElementSibling.textContent;
            selectedOutlets.push({ id: outletId, name: outletName });
        });
        
        if (selectAllButton) {
            const allChecked = checkboxes.length > 0 && Array.from(checkboxes).every(checkbox => checkbox.checked);
            selectAllButton.textContent = allChecked ? 'Batal Pilih' : 'Pilih Semua';
        }
        
        const countElement = document.getElementById('selectedOutletsCount');
        if (countElement) {
            countElement.textContent = selectedOutlets.length;
        }
        
        const startButton = document.getElementById('startComparison');
        if (startButton) {
            startButton.disabled = selectedOutlets.length < 2;
        }
    }

    async function startComparison() {
        if (selectedOutlets.length < 2) {
            showAlert('error', 'Pilih minimal 2 outlet untuk komparasi');
            return;
        }
        
        try {
            showAlert('info', 'Memuat data komparasi...');
            
            const outletIds = selectedOutlets.map(outlet => outlet.id).join(',');

            const response = await fetch(`/api/reports/sales-by-category/compare?outlet_ids=${outletIds}&start_date=${currentStartDate}&end_date=${currentEndDate}`, {
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('token')}`,
                    'Accept': 'application/json'
                }
            });
            
            const data = await response.json();
            
            console.log('API Response:', data);
            
            if (data.success) {
                displayComparisonResults(data.data);
                showAlert('success', 'Data komparasi berhasil dimuat');
            } else {
                console.error('API Error:', data.message || 'Unknown error');
                throw new Error(data.message || 'Gagal memuat data komparasi');
            }
        } catch (error) {
            console.error('Error in comparison:', error);
            const errorMessage = error.message || 'Terjadi kesalahan saat memuat data komparasi';
            showAlert('error', errorMessage);
            
            // Show empty data state
            const content = document.getElementById('comparisonContent');
            if (content) {
                content.innerHTML = `
                    <div class="text-center py-8">
                        <p class="text-gray-500 mb-2">Tidak dapat memuat data komparasi</p>
                        <p class="text-sm text-gray-400">${errorMessage}</p>
                    </div>
                `;
            }
        }
    }

    function displayComparisonResults(comparisonData) {
        console.log('Displaying comparison data:', comparisonData);
        
        const content = document.getElementById('comparisonContent');
        if (!content) {
            console.error('comparisonContent element not found');
            return;
        }
        
        if (!comparisonData || !Array.isArray(comparisonData) || comparisonData.length === 0) {
            content.innerHTML = `
                <div class="text-center py-8">
                    <p class="text-gray-500">Tidak ada data kategori untuk komparasi pada periode ini</p>
                </div>
            `;
            return;
        }
        
        let html = `
            <div class="overflow-x-auto">
                <table class="w-full text-sm border-collapse border border-gray-300">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="border border-gray-300 px-4 py-3 text-left font-bold">Kategori</th>
        `;
        
        // Add outlet columns
        selectedOutlets.forEach(outlet => {
            html += `<th class="border border-gray-300 px-4 py-3 text-center font-bold" colspan="3">${outlet.name}</th>`;
        });
        
        html += `
                        </tr>
                        <tr class="bg-gray-100">
                            <th class="border border-gray-300 px-4 py-2"></th>
        `;
        
        // Add sub-headers for products, quantity and sales
        selectedOutlets.forEach(() => {
            html += `
                <th class="border border-gray-300 px-2 py-2 text-xs">Produk</th>
                <th class="border border-gray-300 px-2 py-2 text-xs">Qty</th>
                <th class="border border-gray-300 px-2 py-2 text-xs">Penjualan</th>
            `;
        });
        
        html += `
                        </tr>
                    </thead>
                    <tbody>
        `;
        
        // Group categories across all outlets
        const allCategories = new Map();
        
        comparisonData.forEach(outletData => {
            outletData.categories.forEach(category => {
                if (!allCategories.has(category.category_name)) {
                    allCategories.set(category.category_name, {
                        category_name: category.category_name,
                        outlets: {}
                    });
                }
                allCategories.get(category.category_name).outlets[outletData.outlet_id] = {
                    total_products: category.total_products,
                    total_quantity: category.total_quantity,
                    total_sales: category.total_sales
                };
            });
        });
        
        // Display categories
        allCategories.forEach(category => {
            html += `
                <tr class="hover:bg-gray-50">
                    <td class="border border-gray-300 px-4 py-3 font-medium">${category.category_name}</td>
            `;
            
            selectedOutlets.forEach(outlet => {
                const outletData = category.outlets[outlet.id];
                if (outletData) {
                    html += `
                        <td class="border border-gray-300 px-2 py-3 text-right">${outletData.total_products}</td>
                        <td class="border border-gray-300 px-2 py-3 text-right">${outletData.total_quantity}</td>
                        <td class="border border-gray-300 px-2 py-3 text-right">${formatCurrency(outletData.total_sales)}</td>
                    `;
                } else {
                    html += `
                        <td class="border border-gray-300 px-2 py-3 text-right text-gray-400">-</td>
                        <td class="border border-gray-300 px-2 py-3 text-right text-gray-400">-</td>
                        <td class="border border-gray-300 px-2 py-3 text-right text-gray-400">-</td>
                    `;
                }
            });
            
            html += '</tr>';
        });
        
        html += `
                    </tbody>
                </table>
            </div>
        `;
        
        content.innerHTML = html;
        
        // Show comparison results and hide single outlet report
        document.getElementById('comparisonResults').classList.remove('hidden');
        document.getElementById('singleOutletReport').classList.add('hidden');
        
        // Update report title
        document.getElementById('reportTitle').textContent = 'Komparasi Laporan Penjualan Per Kategori';
        document.getElementById('reportSubtitle').textContent = `Menampilkan perbandingan ${selectedOutlets.length} outlet`;
    }

    // Load data when page loads
    document.addEventListener('DOMContentLoaded', function() {
        fetchData();
        
        // Initialize comparison mode toggle
        const comparisonModeToggle = document.getElementById('comparisonMode');
        if (comparisonModeToggle) {
            comparisonModeToggle.addEventListener('change', function() {
                isComparisonMode = this.checked;
                const comparisonSection = document.getElementById('outletComparisonSection');
                
                if (isComparisonMode) {
                    comparisonSection.classList.remove('hidden');
                    loadOutletsForComparison();
                } else {
                    comparisonSection.classList.add('hidden');
                    document.getElementById('comparisonResults').classList.add('hidden');
                    document.getElementById('singleOutletReport').classList.remove('hidden');

                    // Reset report title - fetch data again to restore original state
                    fetchData();

                    // Reset selected outlets
                    selectedOutlets = [];
                    updateSelectedOutlets();
                }
            });
        }
        
        // Initialize start comparison button
        const startComparisonButton = document.getElementById('startComparison');
        if (startComparisonButton) {
            startComparisonButton.addEventListener('click', startComparison);
        }
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