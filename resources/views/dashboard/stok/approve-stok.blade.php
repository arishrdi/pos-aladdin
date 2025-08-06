@extends('layouts.app')

@section('title', 'Approve Stok')

@section('content')

<!-- Alert Notification -->
<div id="alertContainer" class="fixed top-4 right-4 z-50 space-y-3 w-80">
    <!-- Alert will appear here dynamically -->
</div>

<!-- Page Title + Action -->
<div class="mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
       <h1 class="text-3xl font-bold text-gray-800">Approve Stok</h1>
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
        <i data-lucide="package-check" class="w-5 h-5 text-black mt-1"></i>
        <div>
            <h2 class="text-lg font-semibold text-gray-800">Menampilkan permintaan perubahan stok untuk: <span id="outletName">Loading...</span></h2>
            <p id="reportDate" class="text-sm text-gray-600">Permintaan yang belum diproses <span class="font-medium">{{ date('d M Y') }}</span></p>
        </div>
    </div>
</div>
<!-- Card: Tabel Laporan Stok -->
<div class="bg-white rounded-lg shadow-lg p-6">
   <div class="mb-4">
    <h1 class="text-3xl font-bold text-gray-800">Penyesuaian Stok Menunggu Persetujuan</h1>
    <p class="text-sm text-gray-600 mb-2">Persetujuan penyesuaian stok dari kasir yang membutuhkan tindakan Anda</p>
     <!-- Filter Tanggal -->
    <div class="mt-8">
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

    <!-- Error State -->
    <div id="errorState" class="py-8 flex justify-center items-center hidden">
        <div class="flex flex-col items-center">
            <i data-lucide="alert-circle" class="w-10 h-10 text-red-500 mb-3"></i>
            <p class="text-gray-600 mb-2">Terjadi kesalahan saat memuat data</p>
            <button id="retryButton" class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 transition-colors">Coba Lagi</button>
        </div>
    </div>

    <!-- Empty State -->
    <div id="emptyState" class="py-8 flex justify-center items-center hidden">
        <div class="flex flex-col items-center">
            <i data-lucide="package-x" class="w-10 h-10 text-gray-400 mb-3"></i>
            <p class="text-gray-600">Tidak ada permintaan perubahan stok yang menunggu persetujuan</p>
        </div>
    </div>

    <div class="overflow-x-auto mt-8" id="tableContainer">
        <table class="w-full text-base">
         <thead class="bg-white text-gray-800 border-b-2">
                <tr>
                    <th class="py-3 font-bold">Produk</th>
                    <th class="py-3 font-bold text-center">Stok Sebelum</th>
                    <th class="py-3 font-bold text-center">Perubahan</th>
                    <th class="py-3 font-bold text-center">Stok Saat Ini</th>
                    <th class="py-3 font-bold text-center">Tipe</th>
                    <th class="py-3 font-bold text-center">Status</th>
                    <th class="py-3 font-bold">Diajukan Oleh</th>
                    <th class="py-3 font-bold">Waktu</th>
                    <th class="py-3 font-bold">Catatan</th>
                    <th class="py-3 font-bold text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 divide-y" id="approvalTableBody">
                <!-- Data will be loaded here dynamically -->
                
                
            </tbody>
            <!-- Loading State -->
            <div id="loadingState" class="py-8 flex flex-col items-center justify-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                     class="animate-spin text-green-500">
                    <path d="M21 12a9 9 0 1 1-6.219-8.56"/>
                </svg>
                <span class="text-gray-500">Memuat data permintaan perubahan stok...</span>
            </div>
        </table>

        <!-- Tambahkan ini di bagian atas content -->
        <div id="modalContainer" class="fixed inset-0 z-50 hidden">
            <div class="fixed inset-0 bg-black bg-opacity-50"></div>
            <div class="fixed inset-0 flex items-center justify-center p-4">
                <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
                    <div class="p-6">
                        <h3 id="modalTitle" class="text-lg font-bold mb-2"></h3>
                        <p id="modalMessage" class="text-gray-600 mb-4"></p>
                        <div class="flex justify-end gap-3">
                            <button id="modalCancel" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                                Batal
                            </button>
                            <button id="modalConfirm" class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600">
                                Konfirmasi
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://npmcdn.com/flatpickr/dist/l10n/id.js"></script>
<script src="https://unpkg.com/lucide@latest"></script>
<script>
    // Global variables
    let currentApprovalId = null;
    let currentApprovalAction = null;
    let currentOutletId = null;
    let currentOutletName = '';
    let currentDate = '{{ date("Y-m-d") }}';

    async function loadProductData(outletId) {
        try {
            // Sembunyikan dropdown outlet setelah memilih
            const outletDropdown = document.getElementById('outletDropdown');
            if (outletDropdown) outletDropdown.classList.add('hidden');
            
            // Ambil tanggal terpilih atau gunakan hari ini
            const datePicker = document.getElementById('reportDateInput');
            const selectedDate = datePicker ? datePicker.value : new Date().toISOString().split('T')[0];
            
            // Panggil fungsi yang sudah ada untuk memuat data inventory
            fetchInventoryData(selectedDate);
            
            // Jika perlu menyimpan outlet yang dipilih
            localStorage.setItem('selectedOutletId', outletId);
            currentOutletId = outletId;
            
        } catch (error) {
            console.error('Error loading product data:', error);
            showAlert('error', 'Gagal memuat data produk');
        }
    }

    // Fungsi standar untuk mendapatkan ID outlet yang dipilih (mirip dengan script riwayat stok)
    function getSelectedOutletId() {
        // Periksa URL parameters terlebih dahulu
        const urlParams = new URLSearchParams(window.location.search);
        const outletIdFromUrl = urlParams.get('outlet_id');
        
        if (outletIdFromUrl) {
            return outletIdFromUrl;
        }
        
        // Kemudian periksa localStorage
        const savedOutletId = localStorage.getItem('selectedOutletId');
        
        if (savedOutletId) {
            return savedOutletId;
        }
        
        // Default ke outlet ID 1 jika tidak ditemukan
        return 1;
    }

    function connectOutletSelectionToApproval() {
    // Mendengarkan perubahan pada localStorage
    window.addEventListener('storage', function(event) {
        if (event.key === 'selectedOutletId') {
            // Perbarui ID outlet saat ini
            const newOutletId = event.newValue || 1;
            currentOutletId = newOutletId;
            
            // Perbarui nama outlet jika perlu
            updateOutletNameDisplay(newOutletId);
            
            // Muat ulang data
            fetchInventoryData();
        }
    });
    
    // Mendengarkan klik pada elemen outlet di dropdown
    const outletListContainer = document.getElementById('outletListContainer');
    if (outletListContainer) {
        outletListContainer.addEventListener('click', function(event) {
            // Cari elemen li yang diklik
            let targetElement = event.target;
            while (targetElement && targetElement !== outletListContainer && targetElement.tagName !== 'LI') {
                targetElement = targetElement.parentElement;
            }
            
            // Jika kita mengklik item daftar outlet
            if (targetElement && targetElement.tagName === 'LI') {
                // Perbarui setelah penundaan singkat untuk memungkinkan kode yang ada selesai
                setTimeout(() => {
                    const newOutletId = getSelectedOutletId();
                    currentOutletId = newOutletId;
                    
                    // Perbarui nama outlet jika perlu
                    updateOutletNameDisplay(newOutletId);
                    
                    // Muat ulang data
                    fetchInventoryData();
                }, 100);
            }
        });
    }
    
    // Tetap gunakan event outletChanged yang sudah ada sebagai metode ketiga untuk deteksi
    window.addEventListener('outletChanged', function(e) {
        console.log('Outlet changed event received in approval page:', e.detail);
        if (e.detail && e.detail.outletId) {
            // Perbarui outlet saat ini
            currentOutletId = e.detail.outletId;
            currentOutletName = e.detail.outletName;
            
            // Perbarui URL tanpa reload halaman
            const url = new URL(window.location.href);
            url.searchParams.set('outlet_id', currentOutletId);
            url.searchParams.set('outlet_name', currentOutletName);
            window.history.pushState({}, '', url);
            
            // Perbarui UI
            document.getElementById('outletName').textContent = currentOutletName;
            
            // Ambil data baru
            fetchInventoryData();
        }
    });
}

// Fungsi untuk memperbarui tampilan nama outlet
async function updateOutletNameDisplay(outletId) {
    try {
        // Coba dapatkan nama outlet dari event atau localStorage dulu
        const storedOutlet = localStorage.getItem('activeOutlet');
        if (storedOutlet) {
            try {
                const parsed = JSON.parse(storedOutlet);
                if (parsed.id == outletId) {
                    currentOutletName = parsed.name;
                    document.getElementById('outletName').textContent = currentOutletName;
                    return;
                }
            } catch (e) {
                console.error('Error parsing stored outlet', e);
            }
        }
        
        // Jika gagal, ambil dari API
        const response = await fetch(`/api/outlets/${outletId}`, {
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('token')}`,
                'Accept': 'application/json'
            }
        });
        
        const { data, success } = await response.json();
        
        if (success && data) {
            currentOutletName = data.name;
            document.getElementById('outletName').textContent = currentOutletName;
            
            // Perbarui localStorage dengan informasi terbaru
            localStorage.setItem('activeOutlet', JSON.stringify({
                id: outletId,
                name: data.name
            }));
        }
    } catch (error) {
        console.error('Failed to fetch outlet details:', error);
    }
}

    function getOutletFromURL() {
        const urlParams = new URLSearchParams(window.location.search);
        const outletId = urlParams.get('outlet_id');
        
        if (outletId) {
            return {
                id: parseInt(outletId),
                name: urlParams.get('outlet_name') || `Outlet ${outletId}`
            };
        }
        
        return null;
    }

    // Get active outlet from URL, localStorage, or default
    function getActiveOutlet() {
        // 1. Cek URL parameter pertama
        const urlOutlet = getOutletFromURL();
        if (urlOutlet) {
            // Simpan ke localStorage untuk konsistensi
            localStorage.setItem('activeOutlet', JSON.stringify(urlOutlet));
            return urlOutlet;
        }
        
        // 2. Cek localStorage
        const storedOutlet = localStorage.getItem('activeOutlet');
        if (storedOutlet) {
            try {
                return JSON.parse(storedOutlet);
            } catch (e) {
                console.error('Error parsing stored outlet', e);
            }
        }
        
        // 3. Fallback ke default
        return {
            id: 1, 
            name: 'Outlet Default'
        };
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Inisialisasi outlet dari URL atau localStorage
        const outletId = getSelectedOutletId();
        currentOutletId = outletId;

        updateOutletNameDisplay(outletId);

        document.getElementById('modalCancel').addEventListener('click', hideConfirmationModal);
    
    document.getElementById('modalConfirm').addEventListener('click', function() {
        hideConfirmationModal();
        if (currentApprovalId !== null && currentApprovalAction !== null) {
            processApproval(currentApprovalId, currentApprovalAction);
        }
    });
        
        // Update UI dengan outlet yang dipilih
        document.getElementById('outletName').textContent = currentOutletName;
        
        // ... kode yang ada sebelumnya ...
        
        // Modifikasi event listener untuk outletChanged
        window.addEventListener('outletChanged', function(e) {
            console.log('Outlet changed event received in approval page:', e.detail);
            
            if (e.detail && e.detail.outletId) {
                // Update current outlet
                currentOutletId = e.detail.outletId;
                currentOutletName = e.detail.outletName;
                
                // Update URL tanpa reload halaman
                const url = new URL(window.location.href);
                url.searchParams.set('outlet_id', currentOutletId);
                url.searchParams.set('outlet_name', currentOutletName);
                window.history.pushState({}, '', url);
                
                // Update UI
                document.getElementById('outletName').textContent = currentOutletName;

                connectOutletSelectionToApproval();
                
                // Fetch data baru
                fetchInventoryData();
            }
        });
        
        // Initial data load
        fetchInventoryData();
    });

    // Fungsi untuk menampilkan modal konfirmasi
    function showConfirmationModal(id, isApproved) {
        currentApprovalId = id;
        currentApprovalAction = isApproved;
        
        const modal = document.getElementById('modalContainer');
        const title = document.getElementById('modalTitle');
        const message = document.getElementById('modalMessage');
        
        if (isApproved) {
            title.textContent = 'Setujui Permintaan Stok';
            message.textContent = 'Anda yakin ingin menyetujui penyesuaian stok ini?';
            document.getElementById('modalConfirm').className = 'px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600';
        } else {
            title.textContent = 'Tolak Permintaan Stok';
            message.textContent = 'Anda yakin ingin menolak penyesuaian stok ini?';
            document.getElementById('modalConfirm').className = 'px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600';
        }
        
        modal.classList.remove('hidden');
    }

    // Fungsi untuk menyembunyikan modal
    function hideConfirmationModal() {
        document.getElementById('modalContainer').classList.add('hidden');
    }

    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons(); // Cara baru untuk v2+
        
        // Event listener untuk modal
        document.getElementById('modalCancel').addEventListener('click', hideConfirmationModal);
        
        document.getElementById('modalConfirm').addEventListener('click', function() {
            hideConfirmationModal();
            if (currentApprovalId !== null && currentApprovalAction !== null) {
                processApproval(currentApprovalId, currentApprovalAction);
            }
        });
        
        // Initialize flatpickr
        const datePicker = flatpickr("#reportDateInput", {
            dateFormat: "d M Y",
            defaultDate: "today",
            locale: "id",
            onChange: function(selectedDates, dateStr, instance) {
                // Convert to YYYY-MM-DD for API
                const date = selectedDates[0];
                currentDate = `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}`;
                
                // Update display date
                document.getElementById('reportDate').innerHTML = 
                    `Permintaan yang belum diproses <span class="font-medium">${dateStr}</span>`;
                
                // Fetch data with new date
                fetchInventoryData();
            }
        });
        
        // Search functionality
        document.getElementById('searchInput').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('#approvalTableBody tr');
            
            rows.forEach(row => {
                const productName = row.querySelector('td:first-child .font-semibold').textContent.toLowerCase();
                const productSku = row.querySelector('td:first-child .text-xs').textContent.toLowerCase();
                const userName = row.querySelector('td:nth-child(7)').textContent.toLowerCase();
                
                if (productName.includes(searchTerm) || productSku.includes(searchTerm) || userName.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
        
        // Retry button
        document.getElementById('retryButton').addEventListener('click', fetchInventoryData);
        
        // IMPORTANT: Listen for outlet changes from sidebar
        window.addEventListener('outletChanged', function(e) {
            console.log('Outlet changed event received in approval page:', e.detail);
            // Update current outlet from the event detail
            if (e.detail && e.detail.outletId) {
                currentOutletId = e.detail.outletId;
                currentOutletName = e.detail.outletName;
                
                // Update outlet name display
                document.getElementById('outletName').textContent = currentOutletName;
                
                // Fetch data with new outlet
                fetchInventoryData();
            }
        });
        
        // Initial data load
        fetchInventoryData();
    });

    // Get active outlet from localStorage
    function getActiveOutlet() {
        // Get from localStorage
        const storedOutlet = localStorage.getItem('activeOutlet');
        
        if (storedOutlet) {
            try {
                return JSON.parse(storedOutlet);
            } catch (e) {
                console.error('Error parsing stored outlet', e);
            }
        }
        
        // Fallback to default
        return {
            id: 1, 
            name: 'Outlet Default'
        };
    }

    // Format datetime to readable format
    function formatDateTime(dateTimeStr) {
        const date = new Date(dateTimeStr);
        
        // Format time: HH:MM
        const hours = String(date.getHours()).padStart(2, '0');
        const minutes = String(date.getMinutes()).padStart(2, '0');
        const timeStr = `${hours}:${minutes}`;
        
        // Format date: DD MMM YYYY
        const day = String(date.getDate()).padStart(2, '0');
        const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
        const month = monthNames[date.getMonth()];
        const year = date.getFullYear();
        const dateStr = `${day} ${month} ${year}`;
        
        return `${timeStr}, ${dateStr}`;
    }

    // Get type label and class based on adjustment type
    function getTypeLabel(type) {
        switch(type.toLowerCase()) {
            case 'adjustment':
                return {
                    label: 'Penyesuaian',
                    class: 'bg-blue-100 text-blue-700'
                };
            case 'shipment':
                return {
                    label: 'Pengiriman',
                    class: 'bg-green-100 text-green-700'
                };
            case 'purchase':
                return {
                    label: 'Pembelian',
                    class: 'bg-red-100 text-red-700'
                };
            case 'sale' :
                return {
                    label : 'Penjualan',
                    class: 'bg-red-100 text-red-700'
                }
            case 'other':
                return {
                    label: 'Lain-lain',
                    class: 'bg-yellow-100 text-yellow-700'
                };
            default:
                return {
                    label: type,
                    class: 'bg-gray-100 text-gray-700'
                };
        }
    }

    // Get product icon based on category
    function getProductIcon(product) {
        const categoryIcons = {
            'food': 'utensils',
            'drink': 'coffee',
            'snack': 'croissant',
            'material': 'box',
            'default': 'package'
        };
        
        const category = product?.category?.toLowerCase() || 'default';
        return categoryIcons[category] || categoryIcons.default;
    }

    // Fetch inventory adjustment data from API
    async function fetchInventoryData() {
        // Get active outlet from localStorage first
        currentOutletId = getSelectedOutletId();
        
        // Update outlet name display
        // document.getElementById('outletName').textContent = currentOutletName;
        updateOutletNameDisplay(currentOutletId);

        
        // Show loading state
        document.getElementById('loadingState').classList.remove('hidden');
        document.getElementById('errorState').classList.add('hidden');
        document.getElementById('emptyState').classList.add('hidden');
        document.getElementById('tableContainer').classList.add('hidden');
        
        try {
            console.log(`Fetching inventory data for outlet ${currentOutletId} on date ${currentDate}`);
            
            // Make API request
            const response = await fetch(`/api/adjust-inventory/${currentOutletId}?date=${currentDate}`, {
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('token')}`,
                    'Accept': 'application/json'
                }
            });
            
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            
            const result = await response.json();
            
            // Hide loading state
            document.getElementById('loadingState').classList.add('hidden');
            
            if (result.success) {
                const data = result.data;
                
                // Check if we have data
                if (data && data.length > 0) {
                    // Filter for pending requests if needed (or your API might already do this)
                    const pendingRequests = data.filter(item => item.status === 'pending');
                    
                    if (pendingRequests.length > 0) {
                        // Render table
                        renderInventoryTable(pendingRequests);
                        document.getElementById('tableContainer').classList.remove('hidden');
                    } else {
                        // Show empty state
                        document.getElementById('emptyState').classList.remove('hidden');
                    }
                } else {
                    // Show empty state
                    document.getElementById('emptyState').classList.remove('hidden');
                }
            } else {
                // Show error state if API returns success: false
                document.getElementById('errorState').classList.remove('hidden');
            }
            
        } catch (error) {
            console.error('Error fetching inventory data:', error);
            document.getElementById('loadingState').classList.add('hidden');
            document.getElementById('errorState').classList.remove('hidden');
        }
    }

    // Render inventory table
    function renderInventoryTable(data) {
        const tableBody = document.getElementById('approvalTableBody');
        tableBody.innerHTML = '';

        data.forEach(item => {
            const product = item.product || {};
            const user = item.user || {};
            const typeInfo = getTypeLabel(item.type);
            const statusInfo = getStatusLabel(item.status);
            
            const row = document.createElement('tr');
            row.className = item.status === 'pending' ? 'bg-yellow-50' : '';
            row.innerHTML = `
                <td class="py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-md bg-green-100 flex items-center justify-center">
                            <i data-lucide="${getProductIcon(product)}" class="w-5 h-5 text-green-500"></i>
                        </div>
                        <div>
                            <span class="font-semibold block">${product.name || 'N/A'}</span>
                            <span class="text-xs text-gray-500">${product.sku || 'N/A'}</span>
                        </div>
                    </div>
                </td>
                <td class="py-4 font-bold text-center">${item.quantity_before}</td>
                <td class="py-4 font-bold text-center ${item.quantity_change > 0 ? 'text-green-600' : 'text-red-600'}">
                    ${item.quantity_change > 0 ? '+' : ''}${item.quantity_change}
                </td>
                <td class="py-4 font-bold text-center">${item.quantity_after}</td>
                <td class="py-4 text-center">
                    <span class="px-3 py-1 text-xs font-bold ${typeInfo.class} rounded-full">
                        ${typeInfo.label}
                    </span>
                </td>
                <td class="py-4">
                    <span class="px-3 py-1 text-xs font-bold ${statusInfo.class} rounded-full">
                        ${statusInfo.label}
                    </span>
                </td>
                <td class="py-4">${user.name || 'Unknown'}</td>
                <td class="py-4 text-sm">${formatDateTime(item.created_at)}</td>
                <td class="py-4 text-sm">${item.notes || '-'}</td>
                <td class="py-4 text-center">
                    ${item.status === 'pending' ? `
                    <div class="flex gap-2 justify-center">
                        <button onclick="showConfirmationModal(${item.id}, true)" class="p-2 rounded-md bg-green-100 text-green-600 hover:bg-green-200 transition-colors">
                            <i data-lucide="check" class="w-5 h-5"></i>
                        </button>
                        <button onclick="showConfirmationModal(${item.id}, false)" class="p-2 rounded-md bg-red-100 text-red-600 hover:bg-red-200 transition-colors">
                            <i data-lucide="x" class="w-5 h-5"></i>
                        </button>
                    </div>
                    ` : '-'}
                </td>
            `;
            tableBody.appendChild(row);
        });

        // Inisialisasi ulang ikon Lucide
        if (window.lucide) {
            lucide.createIcons();
        }
    }

    // Process approval or rejection
    async function processApproval(id, isApproved) {
        try {
            // Tentukan endpoint berdasarkan jenis aksi
            const endpoint = isApproved 
                ? '/api/inventory-histories/approval' 
                : '/api/inventory-histories/reject';
                
            const payload = isApproved
                ? { inventory_history_id: id, approved: true }
                : { inventory_history_id: id };
                
            const response = await fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('token')}`,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            });

            const result = await response.json();

            if (result.success) {
                showAlert('success', isApproved ? 'Permintaan berhasil disetujui' : 'Permintaan berhasil ditolak');
                fetchInventoryData(); // Refresh data
            } else {
                showAlert('error', result.message || 'Gagal memproses permintaan');
            }
        } catch (error) {
            console.error('Error:', error);
            showAlert('error', 'Terjadi kesalahan saat memproses permintaan');
        }
    }

    // Get status label and class
    function getStatusLabel(status) {
        const statusMap = {
            'pending': { label: 'Menunggu', class: 'bg-yellow-100 text-yellow-700' },
            'approved': { label: 'Disetujui', class: 'bg-green-100 text-green-700' },
            'rejected': { label: 'Ditolak', class: 'bg-red-100 text-red-700' },
            'default': { label: status, class: 'bg-gray-100 text-gray-700' }
        };
        return statusMap[status.toLowerCase()] || statusMap.default;
    }

    // Show alert notification
    function showAlert(type, message) {
        const alertContainer = document.getElementById('alertContainer');
        const alert = document.createElement('div');
        alert.className = `p-4 rounded-md shadow-md ${
            type === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'
        } animate-fadeIn`;
        alert.innerHTML = `
            <div class="flex items-center gap-2">
                <i data-lucide="${type === 'success' ? 'check-circle' : 'alert-circle'}" class="w-5 h-5"></i>
                <span>${message}</span>
            </div>
        `;
        alertContainer.appendChild(alert);
        
        // Inisialisasi ikon
        if (window.lucide) {
            lucide.createIcons();
        }

        // Hapus alert setelah 5 detik
        setTimeout(() => {
            alert.remove();
        }, 5000);
    }
</script>

<style>
    /* Animation for alerts */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    /* Table styling */
    table {
        border-collapse: separate;
        border-spacing: 0;
    }
    
    th, td {
        padding: 12px 15px;
        vertical-align: middle;
    }
    
    th {
        background-color: #f9fafb;
    }
    
    tr:hover {
        background-color: #f9fafb;
    }
    
    /* Loading spinner animation */
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    
    .animate-spin {
        animation: spin 1s linear infinite;
    }
    
    /* Transition effects */
    .transition-opacity {
        transition-property: opacity;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    /* Hide elements */
    .hidden {
        display: none !important;
    }
</style>

@endsection