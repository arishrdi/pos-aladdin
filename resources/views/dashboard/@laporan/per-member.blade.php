@extends('layouts.app')

@section('title', 'Laporan Penjualan per Member')

@section('content')

<!-- Alert Notification -->
<div id="alertContainer" class="fixed top-4 right-4 z-50 space-y-3 w-80">
    <!-- Alert will appear here dynamically -->
</div>

<!-- Page Title + Action -->
<div class="mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
       <h1 class="text-4xl font-bold text-gray-800">Laporan Penjualan per Member</h1>
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
            <h4 class="text-lg font-semibold text-gray-800" id="outletName">Memuat data outlet...</h4>
            <p class="text-sm text-gray-600">Periode: <span id="dateRangeDisplay">Memuat...</span></p>
        </div>
    </div>
    {{-- <div class="text-right">
        <p class="text-sm font-medium text-gray-600">Total Member</p>
        <h4 class="text-xl font-bold text-gray-800" id="totalMembers">Memuat...</h4>
    </div> --}}
</div>

<!-- Laporan Penjualan per Member -->
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-800">Laporan Penjualan per Member</h1>
        <p class="text-sm text-gray-600">Riwayat transaksi pembelian masing-masing member</p>
        
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
            <!-- Cari Member -->
            <div class="flex-1">
                <h2 class="text-sm font-medium text-gray-800 mb-1">Cari Member</h2>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i data-lucide="search" class="w-5 h-5 text-gray-400"></i>
                    </span>
                    <input type="text" id="searchInput" placeholder="Cari member..."
                        class="w-full pl-10 pr-4 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500" />
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
        <!-- Total Transaksi -->
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Transaksi</p>
                    <h3 class="text-2xl font-bold text-gray-800" id="totalTransactions">Memuat...</h3>
                </div>
                <div class="p-3 bg-blue-50 rounded-full">
                    <i data-lucide="shopping-bag" class="w-6 h-6 text-blue-500"></i>
                </div>
            </div>
        </div>
        
        <!-- Total Produk Terjual -->
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Produk Terjual</p>
                    <h3 class="text-2xl font-bold text-gray-800" id="totalProductsSold">Memuat...</h3>
                </div>
                <div class="p-3 bg-green-50 rounded-full">
                    <i data-lucide="package" class="w-6 h-6 text-green-500"></i>
                </div>
            </div>
        </div>
        
        <!-- Total Pendapatan -->
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Pendapatan</p>
                    <h3 class="text-2xl font-bold text-gray-800" id="totalRevenue">Memuat...</h3>
                </div>
                <div class="p-3 bg-purple-50 rounded-full">
                    <i data-lucide="dollar-sign" class="w-6 h-6 text-purple-500"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Member Tables Section -->
    <div id="memberTablesContainer" class="mt-8 space-y-8 w-full">
        <!-- Tables will be generated here dynamically -->
        <div id="loadingIndicator" style="display: none;" class="grid place-items-center py-8 gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" 
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                 class="animate-spin text-green-500">
                <path d="M21 12a9 9 0 1 1-6.219-8.56"/>
            </svg>
            <span class="text-gray-500">Memuat data laporan...</span>
        </div>
    </div>
</div>

<!-- Flatpickr JS -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>

<script>
    // Global variables
    let apiData = null;
    let filteredMembers = [];
    // const outletId = 1;
    let currentStartDate = null;
    let currentEndDate = null;

    // Initialize date range picker
    function formatDateToApi(date) {
        const year = date.getFullYear();
        const month = `${date.getMonth() + 1}`.padStart(2, '0');
        const day = `${date.getDate()}`.padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    function formatDate(date) {
        const options = { day: 'numeric', month: 'long', year: 'numeric' };
        return date.toLocaleDateString('id-ID', options);
    }

    function getDefaultDateRange() {
        const today = new Date();
        const firstDayOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);
        return [firstDayOfMonth, today];
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

    // Initialize the page
    function filterData() {
        if (!apiData || !apiData.data) return;
        
        const searchTerm = document.getElementById('searchInput').value.trim().toLowerCase();
        const data = apiData.data;
        
        // Filter members based on search term
        filteredMembers = data.members.filter(member => 
            (member.member_name && member.member_name.toLowerCase().includes(searchTerm))
        );
        
        // Update member tables
        const container = document.getElementById('memberTablesContainer');
        container.innerHTML = '';
        
        if (filteredMembers.length === 0) {
            container.innerHTML = '<div class="text-center py-8"><p class="text-gray-600">Tidak ada data member yang sesuai dengan pencarian</p></div>';
            return;
        }
        
        filteredMembers.forEach(member => {
            // Create member card
            const memberCard = document.createElement('div');
            memberCard.className = 'bg-gray-50 rounded-lg p-4 mb-4';
            
            // Display member info
            const memberName = member.member_name || 'Member Umum';
            memberCard.innerHTML = `
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <div>
                        <h3 class="text-xl font-bold text-gray-800">${memberName}</h3>
                        <div class="flex flex-wrap gap-x-4 gap-y-1 mt-1">
                            ${member.member_id ? `<p class="text-sm text-gray-600">ID: ${member.member_id}</p>` : ''}
                            <p class="text-sm text-gray-600">Total Belanja: <span class="font-semibold">Rp ${formatNumber(member.total_spent)}</span></p>
                        </div>
                    </div>
                    <div class="mt-2 md:mt-0">
                        <p class="text-sm text-gray-600">Total Transaksi: <span class="font-semibold">${member.total_orders}</span></p>
                    </div>
                </div>
            `;
            container.appendChild(memberCard);
            
            // Create transactions table for this member
            const tableDiv = document.createElement('div');
            tableDiv.className = 'overflow-x-auto';
            tableDiv.innerHTML = `
                <table class="w-full text-sm">
                    <thead class="text-left text-gray-700 bg-gray-100">
                        <tr>
                            <th class="py-3 font-bold px-4">Produk</th>
                            <th class="py-3 font-bold px-4">SKU</th>
                            <th class="py-3 font-bold px-4">Kategori</th>
                            <th class="py-3 font-bold px-4 text-right">Qty</th>
                            <th class="py-3 font-bold px-4 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700 divide-y" id="products-${member.member_id || 'umum'}">
                        <!-- Products will be inserted here -->
                    </tbody>
                </table>
            `;
            container.appendChild(tableDiv);
            
            // Add products for this member
            const tbody = document.getElementById(`products-${member.member_id || 'umum'}`);
            
            let memberTotalQty = 0;
            let memberTotalSpent = 0;
            
            member.products.forEach(product => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td class="py-4 px-4">${product.product_name}</td>
                    <td class="py-4 px-4">${product.sku}</td>
                    <td class="py-4 px-4">${product.category}</td>
                    <td class="py-4 px-4 text-right">${product.quantity}</td>
                    <td class="py-4 px-4 text-right">Rp ${formatNumber(product.total_spent)}</td>
                `;
                tbody.appendChild(row);
                
                memberTotalQty += parseInt(product.quantity);
                memberTotalSpent += parseFloat(product.total_spent);
            });
            
            // Add summary row
            const totalRow = document.createElement('tr');
            totalRow.className = 'bg-gray-100 font-bold';
            totalRow.innerHTML = `
                <td class="py-3 px-4" colspan="3">Total</td>
                <td class="py-3 px-4 text-right">${memberTotalQty}</td>
                <td class="py-3 px-4 text-right">Rp ${formatNumber(memberTotalSpent)}</td>
            `;
            tbody.appendChild(totalRow);
        });
    }

    // Initialize the page
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize date range picker
        const dateRangePicker = flatpickr("#dateRange", {
            mode: "range",
            dateFormat: "d M Y",
            defaultDate: getDefaultDateRange(),
            locale: "id",
            onChange: function(selectedDates, dateStr) {
                if (selectedDates.length === 2) {
                    currentStartDate = selectedDates[0];
                    currentEndDate = selectedDates[1];
                    updateDateDisplay();
                    fetchData();
                }
            },
            onReady: function(selectedDates) {
                if (selectedDates.length === 2) {
                    currentStartDate = selectedDates[0];
                    currentEndDate = selectedDates[1];
                    updateDateDisplay();
                }
            }
        });

        // Connect search input to filter function
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                filterData();
            });
        }

        // Load initial data
        fetchData();
        
        // Connect outlet selection to report updates
        connectOutletSelectionToReport();
    });

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

    // Fungsi helper untuk update tampilan tanggal
    function updateDateDisplay() {
        if (currentStartDate && currentEndDate) {
            const formattedStart = formatDate(currentStartDate);
            const formattedEnd = formatDate(currentEndDate);
            document.getElementById('dateRangeDisplay').textContent = `${formattedStart} - ${formattedEnd}`;
        }
    }

    // Fetch API data
    async function fetchData() {
        try {
            const outletId = getSelectedOutletId();
            // Get loading indicator element safely
            const loadingIndicator = document.getElementById('loadingIndicator');
            if (loadingIndicator) loadingIndicator.style.display = 'block';
            
            // If no dates provided, use default (first day of month to today)
            const startDate = currentStartDate ? formatDateToApi(currentStartDate) : formatDateToApi(getDefaultDateRange()[0]);
            const endDate = currentEndDate ? formatDateToApi(currentEndDate) : formatDateToApi(getDefaultDateRange()[1]);
            
            const apiUrl = `/api/reports/sales-by-member/${outletId}?start_date=${startDate}&end_date=${endDate}`;
            const response = await fetch(apiUrl, {
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('token')}`,
                    'Accept': 'application/json'
                }
            });
            
            if (!response.ok) {
                throw new Error('Network response was not ok: ' + response.statusText);
            }
            
            apiData = await response.json();
            
            if (apiData.status === true) {
                updateUI(apiData.data);
                filterData();
                showAlert('success', 'Data berhasil dimuat');
            } else {
                showAlert('error', 'Terjadi kesalahan saat memuat data');
            }
        } catch (error) {
            console.error('Error fetching data:', error);
            showAlert('error', 'Gagal memuat data: ' + error.message);
        } finally {
            const loadingIndicator = document.getElementById('loadingIndicator');
            if (loadingIndicator) loadingIndicator.style.display = 'none';
        }
    }

    // Panggil fetchData saat pertama load (fallback)
    document.addEventListener('DOMContentLoaded', function() {
        // Jika belum ada tanggal yang ter-set, gunakan default
        if (!currentStartDate || !currentEndDate) {
            const defaultRange = getDefaultDateRange();
            currentStartDate = defaultRange[0];
            currentEndDate = defaultRange[1];
            fetchData(formatDateToApi(currentStartDate), formatDateToApi(currentEndDate));
        }
    });

    // Update UI with API data
    function updateUI(data) {
        // Update outlet info
        document.getElementById('outletName').textContent = `Menampilkan laporan untuk: ${data.outlet}`;
        
        // Update date range
        const startDate = new Date(data.date_range.start_date);
        const endDate = new Date(data.date_range.end_date);
        document.getElementById('dateRangeDisplay').textContent = `${formatDate(startDate)} - ${formatDate(endDate)}`;
        
        // Update summary cards
        // document.getElementById('totalMembers').textContent = `${data.summary.total_members} member`;
        document.getElementById('totalTransactions').textContent = `${data.summary.total_orders} transaksi`;
        
        // Calculate total product count
        let totalProducts = 0;
        data.members.forEach(member => {
            member.products.forEach(product => {
                totalProducts += parseInt(product.quantity);
            });
        });
        
        document.getElementById('totalProductsSold').textContent = `${totalProducts} produk`;
        document.getElementById('totalRevenue').textContent = `Rp ${formatNumber(data.summary.total_sales)}`;
    }

    // Filter data function
// Filter data function
function filterData() {
    if (!apiData || !apiData.data) return;
    
    const searchTerm = document.getElementById('searchInput').value.trim().toLowerCase();
    const data = apiData.data;
    
    // Filter members based on search term
    filteredMembers = data.members.filter(member => 
        (member.member_name && member.member_name.toLowerCase().includes(searchTerm))
    );
    
    // Update member tables
    const container = document.getElementById('memberTablesContainer');
    container.innerHTML = '';
    
    if (filteredMembers.length === 0) {
        container.innerHTML = '<div class="text-center py-8"><p class="text-gray-600">Tidak ada data member yang sesuai dengan pencarian</p></div>';
        return;
    }
    
    filteredMembers.forEach(member => {
        // Create member card
        const memberCard = document.createElement('div');
        memberCard.className = 'bg-gray-50 rounded-lg p-4 mb-4';
        
        // Display member info
        const memberName = member.member_name || 'Member Umum';
        memberCard.innerHTML = `
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div>
                    <h3 class="text-xl font-bold text-gray-800">${memberName}</h3>
                    <div class="flex flex-wrap gap-x-4 gap-y-1 mt-1">
                        ${member.member_id ? `<p class="text-sm text-gray-600">ID: ${member.member_id}</p>` : ''}
                        <p class="text-sm text-gray-600">Total Belanja: <span class="font-semibold">Rp ${formatNumber(member.total_spent)}</span></p>
                    </div>
                </div>
                <div class="mt-2 md:mt-0">
                    <p class="text-sm text-gray-600">Total Transaksi: <span class="font-semibold">${member.total_orders}</span></p>
                </div>
            </div>
        `;
        container.appendChild(memberCard);
        
        // Create transactions table for this member
        const tableDiv = document.createElement('div');
        tableDiv.className = 'overflow-x-auto';
        tableDiv.innerHTML = `
            <table class="w-full text-sm">
                <thead class="text-left text-gray-700 bg-gray-100">
                    <tr>
                        <th class="py-3 font-bold px-4">Produk</th>
                        <th class="py-3 font-bold px-4">SKU</th>
                        <th class="py-3 font-bold px-4">Kategori</th>
                        <th class="py-3 font-bold px-4 text-right">Qty</th>
                        <th class="py-3 font-bold px-4 text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 divide-y" id="products-${member.member_id || 'umum'}">
                    <!-- Products will be inserted here -->
                </tbody>
            </table>
        `;
        container.appendChild(tableDiv);
        
        // Add products for this member
        const tbody = document.getElementById(`products-${member.member_id || 'umum'}`);
        
        let memberTotalQty = 0;
        let memberTotalSpent = 0;
        
        member.products.forEach(product => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td class="py-4 px-4">${product.product_name}</td>
                <td class="py-4 px-4">${product.sku}</td>
                <td class="py-4 px-4">${product.category}</td>
                <td class="py-4 px-4 text-right">${product.quantity}</td>
                <td class="py-4 px-4 text-right">Rp ${formatNumber(product.total_spent)}</td>
            `;
            tbody.appendChild(row);
            
            memberTotalQty += parseInt(product.quantity);
            memberTotalSpent += parseFloat(product.total_spent);
        });
        
        // Add summary row
        const totalRow = document.createElement('tr');
        totalRow.className = 'bg-gray-100 font-bold';
        totalRow.innerHTML = `
            <td class="py-3 px-4" colspan="3">Total</td>
            <td class="py-3 px-4 text-right">${memberTotalQty}</td>
            <td class="py-3 px-4 text-right">Rp ${formatNumber(memberTotalSpent)}</td>
        `;
        tbody.appendChild(totalRow);
    });
}

    // Format number with thousand separators
    function formatNumber(num) {
        return parseFloat(num).toLocaleString('id-ID');
    }

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
                <title>Laporan Penjualan per Member</title>
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
                        <h1>LAPORAN PENJUALAN PER MEMBER</h1>
                        <div class="header-info">
                            Outlet: ${apiData.data.outlet}<br>
                            Periode: ${apiData.data.date_range.start_date} hingga ${apiData.data.date_range.end_date}<br>
                            Dicetak pada: ${new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })}
                        </div>
                    </div>
                </div>
                <hr>
        `);

        // Tambahkan data per member
        apiData.data.members.forEach(member => {
            const memberName = member.member_name || 'Member Umum';

            printWindow.document.write(`
                <h2>${memberName}</h2>
                <p><strong>Total Transaksi:</strong> ${member.total_orders}</p>
                <p><strong>Total Pembelanjaan:</strong> Rp ${formatNumber(member.total_spent)}</p>
                <table>
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>SKU</th>
                            <th>Kategori</th>
                            <th class="text-right">Qty</th>
                            <th class="text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody>
            `);

            let totalQty = 0;
            let totalSpent = 0;

            member.products.forEach(product => {
                totalQty += parseInt(product.quantity);
                totalSpent += parseFloat(product.total_spent);

                printWindow.document.write(`
                    <tr>
                        <td>${product.product_name}</td>
                        <td>${product.sku}</td>
                        <td>${product.category}</td>
                        <td class="text-right">${product.quantity}</td>
                        <td class="text-right">Rp ${formatNumber(product.total_spent)}</td>
                    </tr>
                `);
            });

            printWindow.document.write(`
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3"><strong>Total</strong></td>
                            <td class="text-right"><strong>${totalQty}</strong></td>
                            <td class="text-right"><strong>Rp ${formatNumber(totalSpent)}</strong></td>
                        </tr>
                    </tfoot>
                </table>
            `);
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
                
                // Add header
                csvContent += "Laporan Penjualan per Member\n";
                csvContent += `Outlet: ${apiData.data.outlet}\n`;
                csvContent += `Periode: ${apiData.data.date_range.start_date} s/d ${apiData.data.date_range.end_date}\n\n`;
                
                // For each member
                apiData.data.members.forEach(member => {
                    csvContent += `Member: ${member.member_name || 'Member Umum'}\n`;
                    csvContent += "Produk,SKU,Kategori,Quantity,Total\n";
                    
                    // Add products
                    member.products.forEach(product => {
                        csvContent += `"${product.product_name}","${product.sku}","${product.category}",${product.quantity},${product.total_spent}\n`;
                    });
                    
                    csvContent += `\nTotal Transaksi: ${member.total_orders}\n`;
                    csvContent += `Total Pembelanjaan: Rp ${member.total_spent}\n\n`;
                });
                
                // Create download link
                const encodedUri = encodeURI(csvContent);
                const link = document.createElement("a");
                link.setAttribute("href", encodedUri);
                link.setAttribute("download", `laporan-penjualan-member-${new Date().toISOString().slice(0,10)}.csv`);
                document.body.appendChild(link);
                
                // Trigger download
                link.click();
                document.body.removeChild(link);
                
                showAlert('success', 'Laporan berhasil diekspor');
            } catch (error) {
                console.error('Error exporting report:', error);
                showAlert('error', 'Gagal mengekspor laporan: ' + error.message);
            }
            
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

        if (window.lucide) {
            window.lucide.createIcons({ icons });
        }
        
        // Auto remove alert after 5 seconds
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