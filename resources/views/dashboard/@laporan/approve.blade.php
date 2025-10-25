@extends('layouts.app')

@section('title', 'Laporan Persetujuan Penyesuaian Stok')

@section('content')

<!-- Alert Notification -->
<div id="alertContainer" class="fixed top-4 right-4 z-50 space-y-3 w-80">
    <!-- Alert will appear here dynamically -->
</div>

<!-- Page Title + Action -->
<div class="mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
       <h1 class="text-4xl font-bold text-gray-800">Laporan Persetujuan Penyesuaian Stok</h1>
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
            <h4 class="text-lg font-semibold text-gray-800">Menampilkan laporan untuk: <span id="outletName">Loading...</span></h4>
            <p class="text-sm text-gray-600">Periode: <span id="dateRangeDisplay">01 Mei 2025 - 11 Mei 2025</span></p>
        </div>
    </div>
</div>

<!-- Laporan Persetujuan Penyesuaian Stok -->
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-800">Laporan Persetujuan Penyesuaian Stok</h1>
        <p class="text-sm text-gray-600">Laporan persetujuan dan penolakan penyesuaian stok</p>
        
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
            <!-- Filter Status -->
            <div class="flex-1">
                <h2 class="text-sm font-medium text-gray-800 mb-1">Status</h2>
                <select id="statusFilter" class="w-full pl-3 pr-4 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                    <option value="all">Semua Status</option>
                    <option value="approved">Disetujui</option>
                    <option value="rejected">Ditolak</option>
                </select>
            </div>
        </div>
    </div>
    
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
        <!-- Disetujui -->
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Disetujui</p>
                    <h3 class="text-2xl font-bold text-gray-800">14 penyesuaian</h3>
                </div>
                <div class="p-3 bg-green-50 rounded-full">
                    <i data-lucide="check-circle" class="w-6 h-6 text-green-500"></i>
                </div>
            </div>
        </div>
        
        <!-- Ditolak -->
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Ditolak</p>
                    <h3 class="text-2xl font-bold text-gray-800">0 penyesuaian</h3>
                </div>
                <div class="p-3 bg-red-50 rounded-full">
                    <i data-lucide="x-circle" class="w-6 h-6 text-red-500"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Laporan -->
    <div class="overflow-x-auto mt-8">
        <table class="w-full text-sm">
            <thead class="text-left text-gray-700 bg-gray-50">
                <tr>
                    <th class="py-3 font-bold px-4">Tanggal</th>
                    <th class="py-3 font-bold px-4">SKU</th>
                    <th class="py-3 font-bold px-4">Nama Item</th>
                    <th class="py-3 font-bold px-4 text-center">Perubahan</th>
                    <th class="py-3 font-bold px-4">Keterangan</th>
                    <th class="py-3 font-bold px-4">Status</th>
                    <th class="py-3 font-bold px-4">Disetujui Oleh</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 divide-y" id="adjustmentTable">
                
            </tbody>
        </table>
    </div>
</div>

<!-- Flatpickr JS -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>

<script>
    // Data laporan dari API
    let reportData = {
        approved: [],
        rejected: []
    };

    // Fungsi untuk memformat tanggal ke format Indonesia
    function formatDate(date) {
        if (!date) return '';
        const options = { day: 'numeric', month: 'long', year: 'numeric' };
        return new Date(date).toLocaleDateString('id-ID', options);
    }

    // Fungsi untuk format ISO date ke format API (YYYY-MM-DD)
    function formatISODate(isoDateString) {
        if (!isoDateString) return '';
        const date = new Date(isoDateString);
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    // Fungsi untuk mendapatkan tanggal awal bulan dan hari ini
    function getDefaultDateRange() {
        const today = new Date();
        const firstDayOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);
        return {
            startDate: firstDayOfMonth,
            endDate: today
        };
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

    // Initialize date range picker dengan default awal bulan sampai hari ini
    const defaultRange = getDefaultDateRange();
    const dateRangePicker = flatpickr("#dateRange", {
        mode: "range",
        dateFormat: "Y-m-d",
        defaultDate: [defaultRange.startDate, defaultRange.endDate],
        locale: "id",
        onChange: function(selectedDates, dateStr) {
            if (selectedDates.length === 2) {
                const startDate = formatISODate(selectedDates[0]);
                const endDate = formatISODate(selectedDates[1]);
                
                // Format untuk display
                const startDateDisplay = formatDate(selectedDates[0]);
                const endDateDisplay = formatDate(selectedDates[1]);
                document.getElementById('dateRangeDisplay').textContent = `${startDateDisplay} - ${endDateDisplay}`;
                
                // Fetch data dengan tanggal baru
                fetchReportData(getSelectedOutletId(), startDate, endDate);
            }
        }
    });

    // Set default tanggal display saat pertama load
    document.addEventListener('DOMContentLoaded', function() {
        const defaultRange = getDefaultDateRange();
        const startDateDisplay = formatDate(defaultRange.startDate);
        const endDateDisplay = formatDate(defaultRange.endDate);
        document.getElementById('dateRangeDisplay').textContent = `${startDateDisplay} - ${endDateDisplay}`;
        
        // Format untuk API (YYYY-MM-DD)
        const startDateAPI = formatISODate(defaultRange.startDate);
        const endDateAPI = formatISODate(defaultRange.endDate);
        
        // Get outlet ID
        const outletId = getSelectedOutletId();
        
        // Update outlet name in UI
        updateOutletNameDisplay(outletId);

        // Connect outlet selection to report updates
        connectOutletSelectionToReport();
        
        // Fetch data awal
        fetchReportData(outletId, startDateAPI, endDateAPI);
    });

    // Connect outlet selection dropdown to report updates
    function connectOutletSelectionToReport() {
        // Listen for outlet changes in localStorage
        window.addEventListener('storage', function(event) {
            if (event.key === 'selectedOutletId') {
                // Update outlet name in UI
                updateOutletNameDisplay(event.newValue);
                
                // Get current date range
                const selectedDates = dateRangePicker.selectedDates;
                const startDate = formatISODate(selectedDates[0]);
                const endDate = formatISODate(selectedDates[1]);
                
                // Reload report with new outlet
                fetchReportData(event.newValue, startDate, endDate);
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
                        const outletId = getSelectedOutletId();
                        updateOutletNameDisplay(outletId);
                        
                        // Get current date range
                        const selectedDates = dateRangePicker.selectedDates;
                        const startDate = formatISODate(selectedDates[0]);
                        const endDate = formatISODate(selectedDates[1]);
                        
                        // Reload report with new outlet
                        fetchReportData(outletId, startDate, endDate);
                    }, 100);
                }
            });
        }
    }

    // Update outlet name display based on outlet ID
    function updateOutletNameDisplay(outletId) {
        // In a real app, you might fetch outlet details or get them from a global state
        // For now, we'll just set a placeholder text
        const outletName = document.getElementById('outletName');
        if (outletName) {
            // You could replace this with an API call to get the real outlet name
            outletName.textContent = `Outlet ${outletId}`;
        }
        
        // Update any other UI elements that show outlet name
        const outletNameHeader = document.getElementById('outletNameHeader');
        if (outletNameHeader) {
            outletNameHeader.textContent = `Outlet ${outletId}`;
        }
    }

    // Fungsi untuk fetch data dari API
    async function fetchReportData(outletId, startDate, endDate) {
        try {
            showAlert('info', 'Memuat data laporan...');
            
            const response = await fetch(`/api/reports/inventory-approvals/${outletId}?start_date=${startDate}&end_date=${endDate}`, {
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('token')}`,
                    'Accept': 'application/json'
                }
            });
            
            if (!response.ok) {
                throw new Error('Gagal mengambil data dari server');
            }
            
            const data = await response.json();
            reportData = data;
            
            // Update UI dengan data baru
            updateReportUI();
            showAlert('success', 'Data laporan berhasil dimuat');
        } catch (error) {
            console.error('Error fetching report data:', error);
            showAlert('error', 'Gagal memuat data: ' + error.message);
        }
    }

    // Fungsi untuk update tampilan laporan
    function updateReportUI() {
        // Update jumlah pada summary cards
        document.querySelectorAll('.bg-white.rounded-lg.shadow.p-4')[0].querySelector('h3').textContent = 
            `${reportData.approved.length} penyesuaian`;
        document.querySelectorAll('.bg-white.rounded-lg.shadow.p-4')[1].querySelector('h3').textContent = 
            `${reportData.rejected.length} penyesuaian`;
        
        // Reset dan filter data berdasarkan pencarian dan status
        filterData();
    }

    // Filter data function
    function filterData() {
        const searchTerm = document.getElementById('searchInput').value.trim().toLowerCase();
        const statusFilter = document.getElementById('statusFilter').value;
        
        // Gabungkan data approved dan rejected
        let allData = [];
        
        if (statusFilter === 'all' || statusFilter === 'approved') {
            allData = allData.concat(reportData.approved.map(item => ({...item, status: 'approved'})));
        }
        
        if (statusFilter === 'all' || statusFilter === 'rejected') {
            allData = allData.concat(reportData.rejected.map(item => ({...item, status: 'rejected'})));
        }
        
        // Filter berdasarkan pencarian
        const filteredData = allData.filter(item => {
            return (
                item.product.sku.toLowerCase().includes(searchTerm) ||
                item.product.name.toLowerCase().includes(searchTerm) ||
                (item.notes && item.notes.toLowerCase().includes(searchTerm)) ||
                item.approver.name.toLowerCase().includes(searchTerm)
            );
        });
        
        // Update tabel adjustment
        const adjustmentTable = document.getElementById('adjustmentTable');
        adjustmentTable.innerHTML = '';
        
        if (filteredData.length === 0) {
            adjustmentTable.innerHTML = `
                <tr>
                    <td colspan="6" class="py-4 px-4 text-center text-gray-500">
                        Tidak ada data yang ditemukan
                    </td>
                </tr>
            `;
        } else {
            filteredData.forEach(item => {
                const change = item.quantity_change > 0 ? '+' + item.quantity_change : item.quantity_change;
                const changeColor = item.quantity_change >= 0 ? 'text-green-500' : 'text-red-500';
                const formattedDate = formatISODate(item.created_at);
                
                adjustmentTable.innerHTML += `
                    <tr>
                        <td class="py-4 px-4">${formattedDate}</td>
                        <td class="py-4 px-4">${item.product.sku}</td>
                        <td class="py-4 px-4">${item.product.name}</td>
                        <td class="py-4 px-4 text-center ${changeColor}">${change}</td>
                        <td class="py-4 px-4">${item.notes || '-'}</td>
                        <td class="py-4 px-4">${item.status}</td>
                        <td class="py-4 px-4">${item.approver.name}</td>
                    </tr>
                `;
            });
        }
        
        // Update jumlah disetujui dan ditolak berdasarkan hasil filter
        const approvedCount = filteredData.filter(item => item.status === 'approved').length;
        const rejectedCount = filteredData.filter(item => item.status === 'rejected').length;
        
        document.querySelectorAll('.bg-white.rounded-lg.shadow.p-4')[0].querySelector('h3').textContent = 
            `${approvedCount} penyesuaian`;
        document.querySelectorAll('.bg-white.rounded-lg.shadow.p-4')[1].querySelector('h3').textContent = 
            `${rejectedCount} penyesuaian`;
    }

    // Search input handler
    document.getElementById('searchInput').addEventListener('keyup', function(e) {
        filterData();
    });

    // Status filter handler
    document.getElementById('statusFilter').addEventListener('change', function(e) {
        filterData();
    });

    // Print report function
    function printReport() {
        if (!reportData || (!reportData.approved.length && !reportData.rejected.length)) {
            showAlert('error', 'Tidak ada data untuk dicetak');
            return;
        }

        showAlert('info', 'Mempersiapkan laporan untuk dicetak...');

        setTimeout(() => {
            const printWindow = window.open('', '_blank');

            // Ambil tanggal dari date picker (flatpickr)
            const selectedDates = dateRangePicker.selectedDates;
            const startDate = selectedDates[0] || new Date();
            const endDate = selectedDates[1] || new Date();

            const formattedStart = formatDate(startDate);
            const formattedEnd = formatDate(endDate);
            const outletName = document.getElementById('outletName')?.textContent || 'Outlet';

            printWindow.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Laporan Persetujuan Penyesuaian Stok</title>
                    <style>
                        body { font-family: Arial, sans-serif; margin: 20px; }
                        h1, h2 { color: #333; }
                        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
                        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                        th { background-color: #f2f2f2; }
                        .text-right { text-align: right; }
                        .text-green-500 { color: green; }
                        .text-red-500 { color: red; }
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
                            <h1>LAPORAN PERSETUJUAN PENYESUAIAN STOK</h1>
                            <div class="header-info">
                                Outlet: ${outletName}<br>
                                Periode: ${formattedStart} hingga ${formattedEnd}<br>
                                Dicetak pada: ${new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })}
                            </div>
                        </div>
                    </div>
                    <hr>
                    <table>
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>SKU</th>
                                <th>Nama Produk</th>
                                <th class="text-center">Perubahan</th>
                                <th>Keterangan</th>
                                <th>Disetujui Oleh</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
            `);

            const allData = [
                ...reportData.approved.map(item => ({ ...item, status: 'Disetujui' })),
                ...reportData.rejected.map(item => ({ ...item, status: 'Ditolak' }))
            ];

            allData.forEach(item => {
                const change = item.quantity_change > 0 ? '+' + item.quantity_change : item.quantity_change;
                const colorClass = item.quantity_change >= 0 ? 'text-green-500' : 'text-red-500';
                const formattedDate = formatISODate(item.created_at);
                const notes = item.notes || '-';

                printWindow.document.write(`
                    <tr>
                        <td>${formattedDate}</td>
                        <td>${item.product.sku}</td>
                        <td>${item.product.name}</td>
                        <td class="text-center ${colorClass}">${change}</td>
                        <td>${notes}</td>
                        <td>${item.approver.name}</td>
                        <td>${item.status}</td>
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
        showAlert('info', 'Mempersiapkan laporan untuk diekspor...');
        setTimeout(() => {
            // Persiapkan data CSV
            let csvContent = 'Tanggal,SKU,Nama Item,Perubahan,Keterangan,Disetujui Oleh,Status\n';
            
            // Gabungkan data approved dan rejected
            const allData = [
                ...reportData.approved.map(item => ({...item, status: 'approved'})),
                ...reportData.rejected.map(item => ({...item, status: 'rejected'}))
            ];
            
            // Tambahkan setiap baris data
            allData.forEach(item => {
                const date = formatISODate(item.created_at);
                const change = item.quantity_change > 0 ? '+' + item.quantity_change : item.quantity_change;
                const notes = item.notes || '-';
                const status = item.status === 'approved' ? 'Disetujui' : 'Ditolak';
                
                csvContent += `${date},${item.product.sku},"${item.product.name}",${change},"${notes}",${item.approver.name},${status}\n`;
            });
            
            // Get outlet name for filename
            const outletName = document.getElementById('outletName')?.textContent || 'Outlet';
            const outletNameForFile = outletName.replace(/\s+/g, '-').toLowerCase();
            
            // Create and download CSV file
            const encodedUri = encodeURI('data:text/csv;charset=utf-8,' + csvContent);
            const link = document.createElement('a');
            link.setAttribute('href', encodedUri);
            link.setAttribute('download', `laporan-persetujuan-stok-${outletNameForFile}-${new Date().toISOString().slice(0,10)}.csv`);
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
        
        // Refresh Lucide icons
        if (window.lucide) {
            window.lucide.createIcons({ icons });
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