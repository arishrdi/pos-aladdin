@extends('layouts.app')

@section('title', 'Riwayat Stok')

@section('content')

<!-- Page Title -->
<div class="mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <h1 class="text-3xl font-bold text-gray-800">Manajemen Riwayat Stok</h1>
        <div class="relative w-full md:w-64">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                <!-- Heroicons search icon -->
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 103.5 3.5a7.5 7.5 0 0013.65 13.65z" />
                </svg>
            </span>
            <input 
                type="text" 
                placeholder="Cari Produk..." 
                class="w-full pl-10 pr-4 py-3 border rounded-lg text-base focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                id="searchProduct"
            />
        </div>
    </div>
</div>

<!-- Card: Outlet Info -->
<div class="bg-white rounded-md p-4 shadow-md mb-4 flex flex-col md:flex-row md:items-center md:justify-between">
    <div class="mb-3 md:mb-0 flex items-start gap-2">
        <i data-lucide="store" class="w-5 h-5 text-gray-600"></i>
        <div>
            <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2 outlet-name">Outlet Aktif: Loading...</h2>
            <p class="text-sm text-gray-600 outlet-address">Memuat data outlet...</p>
        </div>
    </div>
</div>

<!-- Card: Tabel Riwayat Stok -->
<div class="bg-white rounded-lg shadow p-4">
    <!-- Header Table: Filter Tanggal -->
    <div class="mb-6">
        <div class="mt-2">
        <label for="reportDateInput" class="block text-sm font-medium text-gray-700 mb-1">Pilih Tanggal</label>
            <div class="relative">
                <input id="reportDateInput" type="text"
                    class="w-full sm:w-56 pl-10 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                    placeholder="Tanggal" />
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <i data-lucide="calendar" class="w-4 h-4 text-gray-500"></i>
                </span>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full text-base">
            <thead class="text-left text-gray-700 border-b-2">
                <tr>
                    <th class="py-3 font-bold">Jam</th>
                    <th class="py-3 font-bold">Produk</th>
                    <th class="py-3 font-bold">Stok Sebelumnya</th>
                    <th class="py-3 font-bold">Stok Baru</th>
                    <th class="py-3 font-bold">Perubahan</th>
                    <th class="py-3 font-bold">Tipe</th>
                    <th class="py-3 font-bold">Catatan</th>
                </tr>
            </thead>
            <tbody id="historyTableBody" class="text-gray-700 divide-y">
                <!-- Data akan diisi via JavaScript -->
                <tr id="loadingRow">
                    <td colspan="7" class="py-4 text-center">
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

<script>
    // Mapping style untuk tipe stok
    const typeStyles = {
        'purchase': { bg: 'bg-green-100', text: 'text-green-600' },
        'sale': { bg: 'bg-red-100', text: 'text-red-600' },
        'adjustment': { bg: 'bg-yellow-100', text: 'text-yellow-600' },
        'other': { bg: 'bg-gray-100', text: 'text-gray-600' },
        'stocktake': { bg: 'bg-purple-100', text: 'text-purple-600' },
        'shipment': { bg: 'bg-blue-100', text: 'text-blue-600' },
        'transfer_in': { bg: 'bg-teal-100', text: 'text-teal-600' },
        'transfer_out': { bg: 'bg-green-100', text: 'text-green-600' }
    };

    // Format waktu dari ISO
    function formatTime(isoString) {
        const date = new Date(isoString);
        return date.toLocaleTimeString('id-ID', { 
            hour: '2-digit', 
            minute: '2-digit',
            second: '2-digit',
            hour12: false 
        });
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

    // Fetch data dari API dengan outlet ID dinamis
    async function fetchInventoryHistory(date) {
        try {
            // Get dynamic outlet ID
            const outletId = getSelectedOutletId();
            
            console.log(`Fetching inventory history for outlet ID: ${outletId} on date: ${date}`);
            
            const response = await fetch(`/api/inventory-histories/outlet/${outletId}?date=${date}`, {
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('token')}`,
                    'Accept': 'application/json'
                }
            });
            const { data, success, message } = await response.json();
            
            if (!success) throw new Error(message);
            
            return data;
        } catch (error) {
            showAlert('error', `Gagal memuat data: ${error.message}`);
            return [];
        }
    }

    // Function untuk melakukan pencarian
    function searchProducts() {
        const searchTerm = document.getElementById('searchProduct').value.toLowerCase();
        const rows = document.querySelectorAll('#historyTableBody tr');
        
        rows.forEach(row => {
            // Skip loading row or no data row
            if (row.id === 'loadingRow' || row.textContent.includes('Tidak ada riwayat')) {
                return;
            }
            
            const productName = row.querySelector('td:nth-child(2) span').textContent.toLowerCase();
            if (productName.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    // Update tampilan
    async function updateHistoryTable(date) {
        const tbody = document.getElementById('historyTableBody');
        tbody.innerHTML = `<td colspan="7" class="py-4 text-center">
    <div class="flex flex-col items-center justify-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
             class="animate-spin text-green-500">
            <path d="M21 12a9 9 0 1 1-6.219-8.56"/>
        </svg>
        <span class="text-gray-500">Memuat data...</span>
    </div>
</td>`;
        
        const data = await fetchInventoryHistory(date);
        
        // Update info outlet
        if (data.length > 0) {
            const outletElements = document.querySelectorAll('.outlet-name');
            outletElements.forEach(el => {
                el.textContent = `Outlet Aktif: ${data[0].outlet.name}`;
            });
            
            const addressElements = document.querySelectorAll('.outlet-address');
            addressElements.forEach(el => {
                el.textContent = data[0].outlet.address;
            });
        } else {
            // No data available, still update outlet name based on selected outlet
            updateOutletInfoFromSelection();
        }

        // Update tabel
        if (data.length === 0) {
            tbody.innerHTML = `<tr><td colspan="7" class="py-4 text-center text-gray-500">Tidak ada riwayat inventori pada tanggal ini</td></tr>`;
        } else {
            tbody.innerHTML = data.map(history => `
                <tr class="border-b hover:bg-gray-50">
                    <td class="py-4">${formatTime(history.created_at)}</td>
                    <td class="py-4">
                        <div class="flex items-center space-x-2">
                            <span class="product-name">${history.product.name}</span>
                        </div>
                    </td>
                    <td>${history.quantity_before}</td>
                    <td>${history.quantity_after}</td>
                    <td class="${history.quantity_change > 0 ? 'text-green-500' : 'text-red-500'}">
                        ${history.quantity_change > 0 ? '+' : ''}${history.quantity_change}
                    </td>
                    <td>
                        <span class="px-2 py-1 ${typeStyles[history.type].bg} ${typeStyles[history.type].text} rounded text-xs capitalize">
                            ${history.type.replace(/_/g, ' ')}
                        </span>
                    </td>
                    <td class="text-xs text-gray-500">${history.notes || '-'}</td>
                </tr>
            `).join('');
            
            // Apply search filter if there's any search term
            const searchTerm = document.getElementById('searchProduct').value;
            if (searchTerm) {
                searchProducts();
            }
        }
    }

    // Update outlet info when no data is available
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
                const outletElements = document.querySelectorAll('.outlet-name');
                outletElements.forEach(el => {
                    el.textContent = `Outlet Aktif: ${data.name}`;
                });
                
                const addressElements = document.querySelectorAll('.outlet-address');
                addressElements.forEach(el => {
                    el.textContent = data.address || '';
                });
            }
        } catch (error) {
            console.error('Failed to fetch outlet details:', error);
        }
    }

    // Connect to outlet selection dropdown
    function connectOutletSelectionToHistory() {
        // Listen for outlet changes in localStorage
        window.addEventListener('storage', function(event) {
            if (event.key === 'selectedOutletId') {
                // Get current date
                const datePicker = document.getElementById('reportDateInput');
                const currentDate = datePicker?._flatpickr?.selectedDates[0] || new Date();
                const formattedDate = currentDate.toISOString().split('T')[0];
                
                // Reload history with new outlet
                updateHistoryTable(formattedDate);
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
                    // Update history after a short delay to allow your existing code to complete
                    setTimeout(() => {
                        const datePicker = document.getElementById('reportDateInput');
                        const currentDate = datePicker?._flatpickr?.selectedDates[0] || new Date();
                        const formattedDate = currentDate.toISOString().split('T')[0];
                        
                        updateHistoryTable(formattedDate);
                    }, 100);
                }
            });
        }
    }

    // Update filter tanggal
    document.addEventListener('DOMContentLoaded', async () => {
        flatpickr("#reportDateInput", {
            dateFormat: "Y-m-d",
            defaultDate: "today",
            onChange: async function(selectedDates, dateStr) {
                await updateHistoryTable(dateStr);
            },
            locale: {
                firstDayOfWeek: 1
            }
        });

        // Initialize with current date
        const initialDate = new Date().toISOString().split('T')[0];
        await updateHistoryTable(initialDate);
        
        // Connect outlet selection to history updates
        connectOutletSelectionToHistory();
        
        // Add event listener for search input
        document.getElementById('searchProduct').addEventListener('input', searchProducts);
    });

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
            window.lucide.createIcons({ icons });
        }
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (alert.parentNode) {
                alert.remove();
            }
        }, 5000);
    }
</script>

@endsection