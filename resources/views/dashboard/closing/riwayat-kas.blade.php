@extends('layouts.app')

@section('title', 'Manajemen Riwayat Kas')

@section('content')

<!-- Page Title -->
<div class="mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <h1 class="text-3xl font-bold text-gray-800">Manajemen Riwayat Kas</h1>
    </div>
</div>

<!-- Card: Outlet Info -->
<div class="bg-white rounded-md p-4 shadow-md mb-4 flex flex-col md:flex-row md:items-center md:justify-between">
    <div class="mb-3 md:mb-0 flex items-start gap-2">
        <i data-lucide="store" class="w-5 h-5 text-gray-600"></i>
        <div>
            <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2 outlet-name">Outlet Aktif:</h2>
            <p class="text-sm text-gray-600 outlet-name"">Data riwayat transaksi kas untuk outlet .</p>
        </div>
    </div>
</div>

<!-- Card: Tabel Riwayat Kas -->
<div class="bg-white rounded-lg shadow p-4">
    <!-- Header Card + Filter Tanggal -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4">
        <h3 class="text-2xl font-bold text-gray-800">Riwayat Kas</h3>
        <div class="relative mt-2 sm:mt-0">
            <input id="cashDateInput" type="text"
                class="w-full sm:w-56 pl-10 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                placeholder="Pilih Tanggal" />
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <i data-lucide="calendar" class="w-4 h-4 text-gray-500"></i>
            </span>
        </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full text-base">
            <thead class="text-left text-gray-700 border-b-2">
                <tr>
                    <th class="py-3 font-bold">User</th>
                    <th class="py-3 font-bold">Waktu</th>
                    <th class="py-3 font-bold">Tipe</th>
                    <th class="py-3 font-bold">Alasan</th>
                    <th class="py-3 font-bold">Total</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 divide-y" id="cash-history-table">
                <!-- Data akan dimasukkan lewat JavaScript -->
                <tr class="border-b hover:bg-gray-50">
                    <td colspan="5" class="py-8 text-center">
                        <div class="inline-flex flex-col items-center justify-center gap-2 w-full">
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.5.0/axios.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/locale/id.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // Cek token
        const token = localStorage.getItem('token');
        if (!token) {
            console.error('Token not found. User might need to login again.');
            document.getElementById('cash-history-table').innerHTML = `
                <tr class="border-b hover:bg-gray-50">
                    <td colspan="5" class="py-4 text-center text-red-500">Sesi login telah berakhir. Silakan login kembali.</td>
                </tr>
            `;
            return;
        }

        let selectedDate = null;

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

        // Inisialisasi datepicker
        flatpickr("#cashDateInput", {
            dateFormat: "Y-m-d",
            defaultDate: "today",
            onChange: function(selectedDates, dateStr) {
                selectedDate = dateStr;
                const outletId = getSelectedOutletId();
                fetchCashHistory(outletId, dateStr);
            },
            onReady: function() {
                // Ambil tanggal hari ini dalam format YYYY-MM-DD
                const today = new Date();
                const year = today.getFullYear();
                const month = String(today.getMonth() + 1).padStart(2, '0');
                const day = String(today.getDate()).padStart(2, '0');
                const dateStr = `${year}-${month}-${day}`;
                
                // Set nilai input dan filter data
                document.getElementById('cashDateInput').value = dateStr;
                selectedDate = dateStr;
            },
            locale: {
                firstDayOfWeek: 1
            }
        });

        // Fungsi untuk mengambil data riwayat kas
        function fetchCashHistory(outletId, date = null) {
            console.log(`Fetching cash history for outlet ID: ${outletId} on date: ${date}`);
            
            // Tampilkan loading
            document.getElementById('cash-history-table').innerHTML = `
                <tr class="border-b hover:bg-gray-50">
                    <td colspan="5" class="py-4 text-center text-gray-500">Memuat data...</td>
                </tr>
            `;

            // Siapkan query parameters
            let params = {
                source: 'cash',
                outlet_id: outletId
            };

            if (date) {
                params.date = date;
            }

            // Panggil API untuk mendapatkan riwayat kas
            axios.get('/api/cash-register-transactions', { 
                params,
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('token')}`,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => {
                const data = response.data.data;
                
                // Update info outlet
                if (data.length > 0 && data[0].outlet) {
                    updateOutletInfo(data[0].outlet);
                } else {
                    // No data available, still update outlet name based on selected outlet
                    updateOutletInfoFromSelection();
                }
                
                renderCashHistory(data);
            })
            .catch(error => {
                console.error('Error fetching cash history:', error);
                if (error.response && error.response.status === 401) {
                    localStorage.removeItem('token');
                    document.getElementById('cash-history-table').innerHTML = `
                        <tr class="border-b hover:bg-gray-50">
                            <td colspan="5" class="py-4 text-center text-red-500">Sesi login telah berakhir. Silakan login kembali.</td>
                        </tr>
                    `;
                } else {
                    document.getElementById('cash-history-table').innerHTML = `
                        <tr class="border-b hover:bg-gray-50">
                            <td colspan="5" class="py-4 text-center text-red-500">Terjadi kesalahan saat memuat data: ${error.response?.data?.message || error.message}</td>
                        </tr>
                    `;
                }
                
                // Still try to update outlet info from selection
                updateOutletInfoFromSelection();
            });
        }

        // Update outlet info from API response
        function updateOutletInfo(outlet) {
            const outletElements = document.querySelectorAll('.outlet-name');
            outletElements.forEach(el => {
                el.textContent = `Menampilkan riwayat kas untuk: ${outlet.name}`;
            });
            
            const addressElements = document.querySelectorAll('.outlet-address');
            addressElements.forEach(el => {
                el.textContent = outlet.address || '';
            });
        }

        // Update outlet info when no data is available
        async function updateOutletInfoFromSelection() {
            try {
                const outletId = getSelectedOutletId();
                const response = await axios.get(`/api/outlets/${outletId}`, {
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('token')}`,
                        'Accept': 'application/json'
                    }
                });
                
                if (response.data.success && response.data.data) {
                    updateOutletInfo(response.data.data);
                }
            } catch (error) {
                console.error('Failed to fetch outlet details:', error);
            }
        }

        // Connect to outlet selection dropdown
        function connectOutletSelectionToCashHistory() {
            // Listen for outlet changes in localStorage
            window.addEventListener('storage', function(event) {
                if (event.key === 'selectedOutletId') {
                    // Get current date
                    const datePicker = document.getElementById('cashDateInput');
                    const currentDate = selectedDate || new Date().toISOString().split('T')[0];
                    
                    // Reload history with new outlet
                    const outletId = getSelectedOutletId();
                    fetchCashHistory(outletId, currentDate);
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
                        // Update history after a short delay to allow existing code to complete
                        setTimeout(() => {
                            const currentDate = selectedDate || new Date().toISOString().split('T')[0];
                            const outletId = getSelectedOutletId();
                            fetchCashHistory(outletId, currentDate);
                        }, 100);
                    }
                });
            }
        }

        // Fungsi untuk menampilkan data dalam tabel
        function renderCashHistory(data) {
            const tableBody = document.getElementById('cash-history-table');
            
            // Clear existing content
            tableBody.innerHTML = '';

            if (!data || data.length === 0) {
                tableBody.innerHTML = `
                    <tr class="border-b hover:bg-gray-50">
                        <td colspan="5" class="py-4 text-center text-gray-500">Tidak ada data transaksi kas untuk ditampilkan</td>
                    </tr>
                `;
                return;
            }

            data.forEach(transaction => {
                const formattedTime = moment(transaction.created_at).format('HH:mm:ss');
                const formattedDate = moment(transaction.created_at).format('DD MMM YYYY');
                const isAdd = transaction.type === 'add';
                const formattedAmount = new Intl.NumberFormat('id-ID').format(transaction.amount);
                const userName = transaction.user ? transaction.user.name : 'System';
                const reason = transaction.reason || '-';
                const source = transaction.source ? transaction.source.toUpperCase() : '-';

                const row = document.createElement('tr');
                row.className = 'border-b hover:bg-gray-50';
                row.innerHTML = `
                    <td class="py-4">${userName}</td>
                    <td class="py-4">
                        <div>${formattedTime}</div>
                        <div class="text-xs text-gray-500">${formattedDate}</div>
                    </td>
                    <td>
                        ${isAdd 
                            ? '<span class="px-2 py-1 bg-green-100 text-green-600 rounded text-xs">Pemasukan</span>' 
                            : '<span class="px-2 py-1 bg-red-100 text-red-600 rounded text-xs">Pengeluaran</span>'
                        }
                        
                    </td>
                    <td class="text-sm text-gray-600">${reason}</td>
                    <td class="${isAdd ? 'text-green-600' : 'text-red-600'} font-semibold">
                        ${isAdd ? '+' : '-'}Rp ${formattedAmount}
                    </td>
                `;
                
                tableBody.appendChild(row);
            });
        }

        // Show alert function for error/success messages
        function showAlert(type, message) {
            const alertContainer = document.getElementById('alertContainer');
            if (!alertContainer) return;

            const alert = document.createElement('div');
            alert.className = `px-4 py-3 rounded-lg shadow-md ${type === 'error' ? 'bg-red-100 text-red-700' : 
                            type === 'success' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700'}`;
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
                window.lucide.createIcons({ icons });
            }
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                if (alert.parentNode) {
                    alert.remove();
                }
            }, 5000);
        }

        // Initialize with current date and outlet
        const initialDate = new Date().toISOString().split('T')[0];
        const initialOutletId = getSelectedOutletId();
        
        // Load initial data
        fetchCashHistory(initialOutletId, initialDate);
        
        // Connect outlet selection to cash history updates
        connectOutletSelectionToCashHistory();
    });
</script>

@endsection