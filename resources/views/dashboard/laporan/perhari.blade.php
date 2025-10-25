@extends('layouts.app')

@section('title', 'Laporan Harian')

@section('content')

<!-- Alert Notification -->
<div id="alertContainer" class="fixed top-4 right-4 z-50 space-y-3 w-80">
    <!-- Alert will appear here dynamically -->
</div>

<!-- Page Title + Action -->
<div class="mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
       <h1 class="text-3xl font-bold text-gray-800">Laporan Harian</h1>
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
            <h2 class="text-lg font-semibold text-gray-800" id="reportTitle">Menampilkan Laporan untuk: <span class="outlet-name">Loading...</span></h2>
            <p class="text-sm text-gray-600" id="reportSubtitle">Data yang ditampilkan adalah khusus untuk outlet <span class="outlet-name">...</span></p>
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
                                <li>• Data akan ditampilkan dalam bentuk tabel perbandingan</li>
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
    <h2 class="text-xl font-bold text-gray-800 mb-4">Hasil Komparasi Outlet</h2>
    <div id="comparisonContent">
        <!-- Comparison data will be populated here -->
    </div>
</div>

<!-- Analisis Penjualan -->
<div id="singleOutletReport" class="bg-white rounded-lg shadow-lg p-6 mb-6">
    <!-- Header + Filter -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-4">
        <div>
            <h1 class="text-xl font-bold text-gray-800">Analisis Penjualan</h1>
            <p class="text-sm text-gray-600">Grafik dan analisis penjualan berdasarkan periode yang dipilih</p>
            
            <!-- Filter + Search - Now placed below the title -->
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
                <!-- Cari Produk/Invoice -->
                <div class="flex-1">
                    <h2 class="text-sm font-medium text-gray-800 mb-1">Cari Invoice/Produk</h2>
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
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6" id="summaryCards">
        <!-- Cards will be populated by JavaScript -->
    </div>
    
    <!-- Tabel Transaksi -->
    <div class="overflow-x-auto mt-6">
        <table class="w-full text-sm">
            <thead class="text-left text-gray-700 border-b-2">
                <tr>
                    <th class="py-3 font-bold">Invoice</th>
                    <th class="py-3 font-bold">Waktu</th>
                    <th class="py-3 font-bold">Kasir</th>
                    <th class="py-3 font-bold">Metode Pembayaran</th>
                    <th class="py-3 font-bold">Status</th>
                    <th class="py-3 font-bold text-right">Total</th>
                    <th class="py-3 font-bold text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 divide-y" id="transactionTableBody">
                <!-- Transactions will be populated by JavaScript -->
            </tbody>
        </table>
    </div>
</div>

<!-- Include Modal Partial -->
@include('partials.laporan.modal-perhari-transaksi')

<!-- Flatpickr JS -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>

<script>
    // Initialize lucide icons
    lucide.createIcons({ icons });

    // Global variables
    let currentOutletId = getSelectedOutletId(); // Get initial outlet ID using function
    let currentData = null;
    let selectedOutlets = [];
    let isComparisonMode = false;

    // Function to get currently selected outlet ID (sama seperti di riwayat stok)
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

    // Initialize date range picker
    flatpickr("#dateRange", {
        mode: "range",
        dateFormat: "d M Y",
        defaultDate: [
            new Date(new Date().getFullYear(), new Date().getMonth(), 1),
            new Date()
        ],
        locale: "id",
        onChange: function(selectedDates, dateStr) {
            if (selectedDates.length === 2) {
                const dateFrom = formatDateForAPI(selectedDates[0]);
                const dateTo = formatDateForAPI(selectedDates[1]);
                fetchData(currentOutletId, dateFrom, dateTo);
            }
        }
    });

    // Format date for API (YYYY-MM-DD)
    function formatDateForAPI(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0'); // Bulan dimulai dari 0
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    // Format date to Indonesian format
    function formatDateToID(dateString) {
        const options = { day: 'numeric', month: 'long', year: 'numeric' };
        return new Date(dateString).toLocaleDateString('id-ID', options);
    }

    // Format currency to IDR
    function formatCurrency(amount) {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(amount);
    }

    // Get payment method badge class
    function getPaymentMethodBadge(method) {
        switch(method) {
            case 'cash':
                return 'bg-green-100 text-green-800';
            case 'transfer':
                return 'bg-blue-100 text-blue-800';
            case 'qris':
                return 'bg-purple-100 text-purple-800';
            case 'credit':
                return 'bg-yellow-100 text-yellow-800';
            default:
                return 'bg-gray-100 text-gray-800';
        }
    }

    // Get payment method display text
    function getPaymentMethodText(method) {
        switch(method) {
            case 'cash':
                return 'Tunai';
            case 'transfer':
                return 'Transfer';
            case 'qris':
                return 'Qris';
            case 'credit':
                return 'Kredit Card';
            default:
                return method;
        }
    }

    // Get status badge class
    function getStatusBadge(status) {
        switch(status) {
            case 'completed':
                return 'bg-green-100 text-green-800';
            case 'cancelled':
                return 'bg-red-100 text-red-800';
            case 'pending':
                return 'bg-yellow-100 text-yellow-800';
            default:
                return 'bg-gray-100 text-gray-800';
        }
    }

    // Get status display text
    function getStatusText(status) {
        switch(status) {
            case 'completed':
                return 'Selesai';
            case 'cancelled':
                return 'Dibatalkan';
            case 'pending':
                return 'Menunggu';
            default:
                return status;
        }
    }

    // Fetch data from API
    async function fetchData(outletId, dateFrom, dateTo) {
        try {
            // Update outlet info in UI
            updateOutletInfoDisplay(outletId);
            
            // showAlert('info', 'Memuat data laporan...');
            
            const response = await fetch(`/api/orders/history?outlet_id=${outletId}&date_from=${dateFrom}&date_to=${dateTo}`, {
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('token')}`,
                'Accept': 'application/json'
            }
            });
            const data = await response.json();
            
            if (data.success) {
                currentData = data.data;
                renderSummaryCards();
                renderTransactionTable();
                showAlert('success', 'Data berhasil dimuat');
            } else {
                throw new Error(data.message || 'Gagal memuat data');
            }
        } catch (error) {
            console.error('Error fetching data:', error);
            showAlert('error', error.message);
        }
    }

    // Update outlet info in UI based on selected outlet ID
    async function updateOutletInfoDisplay(outletId) {
        try {
            // Fetch outlet details
            const response = await fetch(`/api/outlets/${outletId}`, {
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('token')}`,
                    'Accept': 'application/json'
                }
            });
            
            const { data, success } = await response.json();
            
            // Update UI elements with outlet info
            if (success && data) {
                // Update outlet name in header or wherever it appears
                const outletElements = document.querySelectorAll('.outlet-name');
                outletElements.forEach(el => {
                    el.textContent = `${data.name}`;
                });
                
                // Update outlet address if needed
                const addressElements = document.querySelectorAll('.outlet-address');
                addressElements.forEach(el => {
                    el.textContent = data.address || '';
                });
                
                // Update any other elements that should show outlet info
                document.getElementById('reportTitle').textContent = `Laporan Penjualan - ${data.name}`;
            }
        } catch (error) {
            console.error('Failed to fetch outlet details:', error);
        }
    }

    // Connect to outlet selection dropdown and localStorage changes
    function connectOutletSelectionToReport() {
        // Listen for outlet changes in localStorage
        window.addEventListener('storage', function(event) {
            if (event.key === 'selectedOutletId') {
                // Update current outlet ID
                currentOutletId = event.newValue || 1;
                
                // Get current date range
                const dateRangePicker = document.getElementById('dateRange');
                const selectedDates = dateRangePicker._flatpickr.selectedDates;
                
                if (selectedDates.length === 2) {
                    const dateFrom = formatDateForAPI(selectedDates[0]);
                    const dateTo = formatDateForAPI(selectedDates[1]);
                    
                    // Reload data with new outlet ID
                    fetchData(currentOutletId, dateFrom, dateTo);
                }
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
                    // Update outletId after a short delay to allow existing code to complete
                    setTimeout(() => {
                        // Update current outlet ID with new selected value
                        currentOutletId = getSelectedOutletId();
                        
                        const dateRangePicker = document.getElementById('dateRange');
                        const selectedDates = dateRangePicker._flatpickr.selectedDates;
                        
                        if (selectedDates.length === 2) {
                            const dateFrom = formatDateForAPI(selectedDates[0]);
                            const dateTo = formatDateForAPI(selectedDates[1]);
                            
                            // Reload data with new outlet ID
                            fetchData(currentOutletId, dateFrom, dateTo);
                        }
                    }, 100);
                }
            });
        }
    }

    // Render summary cards
    function renderSummaryCards() {
        if (!currentData) return;
        
        const cardsContainer = document.getElementById('summaryCards');
        const { total_orders, total_revenue, average_order_value, total_discount, total_items_sold, gross_sales } = currentData;
        
        cardsContainer.innerHTML = `
            <!-- Card Template -->
            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total Order</p>
                        <h3 class="text-2xl font-bold text-gray-800">${total_orders}</h3>
                    </div>
                    <div class="p-3 rounded-full bg-green-100 text-green-500">
                        <i data-lucide="shopping-bag" class="w-6 h-6"></i>
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-2">Periode ${currentData.date_from} - ${currentData.date_to}</p>
            </div>

            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total Item Terjual</p>
                        <h3 class="text-2xl font-bold text-gray-800">${total_items_sold}</h3>
                    </div>
                    <div class="p-3 rounded-full bg-blue-100 text-blue-500">
                        <i data-lucide="package" class="w-6 h-6"></i>
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-2">Periode ${currentData.date_from} - ${currentData.date_to}</p>
            </div>

            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Penjualan Kotor</p>
                        <h3 class="text-2xl font-bold text-gray-800">${formatCurrency(gross_sales)}</h3>
                    </div>
                    <div class="p-3 rounded-full bg-green-100 text-green-500">
                        <i data-lucide="dollar-sign" class="w-6 h-6"></i>
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-2">Periode ${currentData.date_from} - ${currentData.date_to}</p>
            </div>

            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Penjualan Bersih</p>
                        <h3 class="text-2xl font-bold text-gray-800">${formatCurrency(total_revenue)}</h3>
                    </div>
                    <div class="p-3 rounded-full bg-purple-100 text-purple-500">
                        <i data-lucide="credit-card" class="w-6 h-6"></i>
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-2">Periode ${currentData.date_from} - ${currentData.date_to}</p>
            </div>

            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total Diskon</p>
                        <h3 class="text-2xl font-bold text-gray-800">${formatCurrency(total_discount)}</h3>
                    </div>
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-500">
                        <i data-lucide="tag" class="w-6 h-6"></i>
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-2">Periode ${currentData.date_from} - ${currentData.date_to}</p>
            </div>

            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Rata-rata Order</p>
                        <h3 class="text-2xl font-bold text-gray-800">${formatCurrency(average_order_value)}</h3>
                    </div>
                    <div class="p-3 rounded-full bg-red-100 text-red-500">
                        <i data-lucide="bar-chart-2" class="w-6 h-6"></i>
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-2">Periode ${currentData.date_from} - ${currentData.date_to}</p>
            </div>
        `;
        lucide.createIcons({ icons });
    }

    // Render transaction table
    function renderTransactionTable() {
        if (!currentData || !currentData.orders) return;
        
        const tableBody = document.getElementById('transactionTableBody');
        tableBody.innerHTML = '';
        
        currentData.orders.forEach(order => {
            const row = document.createElement('tr');
            row.className = 'py-4';
            row.innerHTML = `
                <td class="py-4 font-medium">${order.order_number}</td>
                <td class="py-4">${order.created_at}</td>
                <td class="py-4">${order.user}</td>
                <td class="py-4">
                    <span class="px-2 py-1 ${getPaymentMethodBadge(order.payment_method)} rounded-full text-xs">
                        ${getPaymentMethodText(order.payment_method)}
                    </span>
                </td>
                <td class="py-4">
                    <span class="px-2 py-1 ${getStatusBadge(order.status)} rounded-full text-xs">
                        ${getStatusText(order.status)}
                    </span>
                </td>
                <td class="py-4 text-right font-bold">${formatCurrency(order.total)}</td>
                <td class="py-4 text-right">
                    <button onclick="showTransactionDetail('${order.id}')" class="text-green-500 hover:text-green-700">
                        <i data-lucide="eye" class="w-5 h-5"></i>
                    </button>
                </td>
            `;
            tableBody.appendChild(row);
        lucide.createIcons({ icons });
        });
    }

    // Search input handler
    document.getElementById('searchInput').addEventListener('keyup', function(e) {
        const searchTerm = this.value.trim().toLowerCase();
        const rows = document.querySelectorAll('#transactionTableBody tr');
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            if (text.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
        
        // if (searchTerm) {
        //     showAlert('info', `Menampilkan hasil pencarian: ${searchTerm}`);
        // }
    });

    // Print report function
    function printReport() {
        if (!currentData) {
            showAlert('error', 'Tidak ada data untuk dicetak');
            return;
        }

        showAlert('info', 'Mempersiapkan laporan untuk diekspor...');

        setTimeout(() => {
        // Create a print window
        const printWindow = window.open('', '_blank');
        
        // HTML content for printing
        let printContent = `
            <!DOCTYPE html>
            <html>
            <head>
                <title>Laporan Penjualan - Aladdin Karpet</title>
                <style>
                    body { font-family: Arial, sans-serif; margin: 0; padding: 20px; }
                    h1 { font-size: 18px; margin: 0 0 10px 0; }
                    hr { border: 0; border-top: 1px solid #000; margin: 10px 0; }
                    .header-info { margin-bottom: 15px; }
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
                    .summary {
                        display: flex;
                        flex-wrap: wrap;
                        gap: 10px;
                        max-width: 600px;
                        margin-bottom: 20px;
                    }

                    .summary-item {
                        flex: 1 1 calc(50% - 10px);
                        background: #f4f4f4;
                        padding: 10px;
                        border-radius: 6px;
                        box-sizing: border-box;
                    }
                    .transaction { margin-bottom: 30px; page-break-inside: avoid; }
                    .transaction-header { margin-bottom: 10px; }
                    .transaction-items { width: 100%; border-collapse: collapse; margin: 10px 0; }
                    .transaction-items th { text-align: left; padding: 5px; border-bottom: 1px solid #000; }
                    .transaction-items td { padding: 5px; border-bottom: 1px solid #ddd; }
                    .transaction-total { margin-top: 10px; text-align: right; }
                    .footer { margin-top: 30px; font-size: 12px; text-align: center; }
                    @page { size: auto; margin: 10mm; }
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
                            Outlet: ${currentData.outlet || 'Outlet 1'}<br>
                            Tanggal: ${currentData.date_from} - ${currentData.date_to}<br>
                            Dicetak pada: ${new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })}
                        </div>
                    </div>
                </div>
                
                <hr>
                
                <div class="summary">
                    <div class="summary-item"><strong>Total Penjualan</strong><br><strong>${formatCurrency(currentData.total_revenue)}</strong></div>
                    <div class="summary-item"><strong>Total Order</strong><br><strong>${currentData.total_orders}</strong></div>
                    <div class="summary-item"><strong>Total Item</strong><br><strong>${currentData.total_items_sold}</strong></div>
                    <div class="summary-item"><strong>Rata-rata Order</strong><br><strong>${formatCurrency(currentData.average_order_value)}</strong></div>
                </div>
                
                <hr>
        `;

        // Add each transaction to the print content
        currentData.orders.forEach(order => {
            printContent += `
                <div class="transaction">
                    <div class="transaction-header">
                        <strong>No Transaksi</strong><br>
                        <strong>#${order.order_number}</strong><br>
                        ${order.created_at}<br>
                        Kasir: ${order.user}<br>
                        ${getPaymentMethodText(order.payment_method)}
                    </div>
                    
                    <table class="transaction-items">
                        <thead>
                            <tr>
                                <th>Nama Item</th>
                                <th>Kode Item</th>
                                <th>Harga</th>
                                <th>Jumlah</th>
                                <th>Satuan</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

            // Add items for this transaction
            order.items.forEach(item => {
                printContent += `
                    <tr>
                        <td>${item.product}</td>
                        <td>${item.sku}</td>
                        <td>${formatCurrency(item.price)}</td>
                        <td>${item.quantity}</td>
                        <td>${item.unit || 'pcs'}</td>
                        <td>${formatCurrency(item.total)}</td>
                    </tr>
                `;
            });

            printContent += `
                        </tbody>
                    </table>
                    
                    <div class="transaction-total">
                        <div>Tax<br>${formatCurrency(order.tax)}</div>
                        <hr>
                        <div><strong>Total<br>${formatCurrency(order.total)}</strong></div>
                    </div>
                </div>
                
                <hr>
            `;
        });

        // Add footer
        printContent += `
                <div class="footer">
                    Laporan ini dibuat secara otomatis oleh sistem.<br>
                    © ${new Date().getFullYear()} Aladdin Karpet
                </div>
            </body>
            </html>
        `;

        // Write content to print window
        printWindow.document.open();
        printWindow.document.write(printContent);
        printWindow.document.close();

        // Wait for content to load before printing
        printWindow.onload = function() {
            setTimeout(() => {
                printWindow.print();
                // printWindow.close();
            }, 2000);
        };

        }, 1000);
    }

    function exportReport() {
        if (!currentData) {
            showAlert('error', 'Tidak ada data untuk diekspor');
            return;
        }

        showAlert('info', 'Mempersiapkan laporan untuk diekspor...');

        function formatCurrency(value) {
            return new Intl.NumberFormat('id-ID', {
                maximumFractionDigits: 0
            }).format(value);
        }

        function formatDate(dateStr) {
            // Misalnya: "31/05/2025 10:03"
            const parts = dateStr.split(' ');
            return parts[0]; // Ambil tanggal saja → "31/05/2025"
        }

        function formatTime(dateStr) {
            // Misalnya: "31/05/2025 10:03"
            const parts = dateStr.split(' ');
            return parts[1] || ''; // Ambil waktu saja → "10:03"
        }

        setTimeout(() => {
            let csvContent = [];

            // Header kolom
            csvContent.push([
                'Order ID', 'Tanggal', 'Waktu', 'Kasir', 'Metode Pembayaran',
                'Produk', 'SKU', 'Harga', 'Kuantitas', 'Subtotal', 'Total Order'
            ]);

            // Data transaksi
            currentData.orders.forEach(order => {
                order.items.forEach((item, index) => {
                    const kuantitas = item.quantity;
                    const harga = item.price;
                    const subtotal = harga * kuantitas;

                    csvContent.push([
                        order.order_number,
                        formatDate(order.created_at),
                        formatTime(order.created_at),
                        order.user,
                        getPaymentMethodText(order.payment_method),
                        item.product,
                        item.sku,
                        formatCurrency(harga),
                        kuantitas,
                        formatCurrency(subtotal),
                        '' // kosongkan kolom Total Order
                    ]);
                });
            });

            // Baris kosong pemisah
            csvContent.push([]);
            csvContent.push(['RINGKASAN PENJUALAN']);
            csvContent.push(['Total Penjualan', formatCurrency(currentData.total_revenue)]);
            csvContent.push(['Total Order', currentData.total_orders]);
            csvContent.push(['Total Item', currentData.total_items_sold]);
            csvContent.push(['Rata-rata Order', formatCurrency(currentData.average_order_value)]);

            // Buat dan unduh CSV
            const csvString = csvContent.map(row => row.join(',')).join('\n');
            const a = document.createElement('a');
            a.href = 'data:text/csv;charset=utf-8,' + encodeURIComponent(csvString);
            a.download = `Laporan_Penjualan_Harian_Outlet_${currentData.outlet || '1'}_${new Date().toISOString().slice(0,10)}.csv`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);

            showAlert('success', 'Laporan berhasil diekspor');
        }, 1000);
    }





    // Show transaction detail modal
    function showTransactionDetail(orderId) {
        if (!currentData || !currentData.orders) return;
        
        const order = currentData.orders.find(o => o.id == orderId);
        if (!order) return;
        
        // Populate modal with order details
        document.getElementById('modalInvoiceNumber').textContent = order.order_number;
        document.getElementById('modalTransactionDate').textContent = order.created_at;
        document.getElementById('modalCashierName').textContent = order.user;
        document.getElementById('modalPaymentMethod').textContent = getPaymentMethodText(order.payment_method);
        document.getElementById('modalStatus').textContent = getStatusText(order.status);
        document.getElementById('modalSubtotal').textContent = formatCurrency(order.subtotal);
        document.getElementById('modalTax').textContent = formatCurrency(order.tax);
        document.getElementById('modalDiscount').textContent = formatCurrency(order.discount);
        document.getElementById('modalTotal').textContent = formatCurrency(order.total);
        document.getElementById('modalTotalPaid').textContent = formatCurrency(order.total_paid);
        document.getElementById('modalChange').textContent = formatCurrency(order.change);
        
        // Populate items table
        const itemsTableBody = document.getElementById('modalItemsTableBody');
        itemsTableBody.innerHTML = '';
        
        order.items.forEach(item => {
            const row = document.createElement('tr');
            row.className = 'border-b';
            row.innerHTML = `
                <td class="py-3">${item.product}</td>
                <td class="py-3">${item.sku}</td>
                <td class="py-3 text-center">${item.quantity}</td>
                <td class="py-3 text-right">${formatCurrency(item.price)}</td>
                <td class="py-3 text-right">${formatCurrency(item.discount)}</td>
                <td class="py-3 text-right">${formatCurrency(item.total)}</td>
            `;
            itemsTableBody.appendChild(row);
        });
        
        // Show modal
        const modal = document.getElementById('transactionDetailModal');
        modal.classList.remove('hidden');
    }

    // Close modal
    function closeModal() {
        const modal = document.getElementById('transactionDetailModal');
        modal.classList.add('hidden');
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

        if (window.lucide) {
            window.lucide.createIcons({ icons });
        }
        
        // Auto remove after 5 seconds
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
            
            const dateRangePicker = document.getElementById('dateRange');
            const selectedDates = dateRangePicker._flatpickr.selectedDates;
            
            if (selectedDates.length !== 2) {
                showAlert('error', 'Pilih rentang tanggal terlebih dahulu');
                return;
            }
            
            const dateFrom = formatDateForAPI(selectedDates[0]);
            const dateTo = formatDateForAPI(selectedDates[1]);
            const outletIds = selectedOutlets.map(outlet => outlet.id).join(',');
            
            const response = await fetch(`/api/orders/history/compare?outlet_ids=${outletIds}&date_from=${dateFrom}&date_to=${dateTo}`, {
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('token')}`,
                    'Accept': 'application/json'
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                console.log('Data komparasi diterima:', data.data);
                console.log('Jumlah outlet:', data.data ? data.data.length : 0);
                
                displayComparisonResults(data.data);
                
                if (data.data && data.data.length > 0) {
                    showAlert('success', data.message || 'Data komparasi berhasil dimuat');
                } else {
                    showAlert('info', 'Tidak ada transaksi untuk periode dan outlet yang dipilih');
                }
            } else {
                throw new Error(data.message || 'Gagal memuat data komparasi');
            }
        } catch (error) {
            console.error('Error in comparison:', error);
            showAlert('error', 'Gagal memuat data komparasi: ' + error.message);
        }
    }

    function displayComparisonResults(comparisonData) {
        const content = document.getElementById('comparisonContent');
        
        if (!comparisonData || comparisonData.length === 0) {
            content.innerHTML = `
                <div class="overflow-x-auto">
                    <table class="w-full text-sm border-collapse border border-gray-300">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="border border-gray-300 px-4 py-3 text-left font-bold">Outlet</th>
                                <th class="border border-gray-300 px-4 py-3 text-right font-bold">Total Order</th>
                                <th class="border border-gray-300 px-4 py-3 text-right font-bold">Total Pendapatan</th>
                                <th class="border border-gray-300 px-4 py-3 text-right font-bold">Rata-rata Order</th>
                                <th class="border border-gray-300 px-4 py-3 text-right font-bold">Total Item Terjual</th>
                                <th class="border border-gray-300 px-4 py-3 text-right font-bold">Penjualan Kotor</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="6" class="border border-gray-300 px-4 py-8 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <i data-lucide="calendar-x" class="w-8 h-8 mb-2"></i>
                                        <p class="font-medium">Tidak ada transaksi ditemukan</p>
                                        <p class="text-sm">untuk periode dan outlet yang dipilih</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            `;
            lucide.createIcons({ icons });
            return;
        }
        
        let html = `
            <div class="overflow-x-auto">
                <table class="w-full text-sm border-collapse border border-gray-300">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="border border-gray-300 px-4 py-3 text-left font-bold">Outlet</th>
                            <th class="border border-gray-300 px-4 py-3 text-right font-bold">Total Order</th>
                            <th class="border border-gray-300 px-4 py-3 text-right font-bold">Total Pendapatan</th>
                            <th class="border border-gray-300 px-4 py-3 text-right font-bold">Rata-rata Order</th>
                            <th class="border border-gray-300 px-4 py-3 text-right font-bold">Total Item Terjual</th>
                            <th class="border border-gray-300 px-4 py-3 text-right font-bold">Penjualan Kotor</th>
                        </tr>
                    </thead>
                    <tbody>
        `;
        
        comparisonData.forEach(outlet => {
            html += `
                <tr class="hover:bg-gray-50">
                    <td class="border border-gray-300 px-4 py-3 font-medium">${outlet.outlet_name || 'Outlet Tidak Diketahui'}</td>
                    <td class="border border-gray-300 px-4 py-3 text-right">${outlet.total_orders || 0}</td>
                    <td class="border border-gray-300 px-4 py-3 text-right font-bold text-green-600">${formatCurrency(outlet.total_revenue || 0)}</td>
                    <td class="border border-gray-300 px-4 py-3 text-right">${formatCurrency(outlet.average_order_value || 0)}</td>
                    <td class="border border-gray-300 px-4 py-3 text-right">${outlet.total_items_sold || 0}</td>
                    <td class="border border-gray-300 px-4 py-3 text-right">${formatCurrency(outlet.gross_sales || 0)}</td>
                </tr>
            `;
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
        document.getElementById('reportTitle').textContent = 'Komparasi Laporan Penjualan';
        document.getElementById('reportSubtitle').textContent = `Menampilkan perbandingan ${selectedOutlets.length} outlet`;
    }

    // Initialize data on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Set default dates (today)
        const today = new Date();
        const dateFrom = formatDateForAPI(today);
        const dateTo = formatDateForAPI(today);
        
        // Get current selected outlet
        currentOutletId = getSelectedOutletId();
        
        // Setup outlet selection connection
        connectOutletSelectionToReport();
        
        // Fetch initial data
        fetchData(currentOutletId, dateFrom, dateTo);
        
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
                    
                    // Reset report title
                    const outletNameElement = document.querySelector('.outlet-name');
                    const outletName = outletNameElement ? outletNameElement.textContent : 'Loading...';
                    document.getElementById('reportTitle').textContent = 'Menampilkan Laporan untuk: ' + outletName;
                    document.getElementById('reportSubtitle').textContent = 'Data yang ditampilkan adalah khusus untuk outlet ' + outletName;
                    
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