@extends('layouts.app')

@section('title', 'Riwayat Bonus')

@section('content')

<!-- Alert Container -->
<div id="alertContainer" class="fixed top-4 right-4 z-50 space-y-3 w-80"></div>

<!-- title Page -->
<div class="mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <h1 class="text-3xl font-bold text-gray-800">Riwayat Bonus</h1>
    </div>
</div>

<!-- Card: Outlet Info -->
<div class="bg-white rounded-md p-4 shadow-md mb-4 flex flex-col md:flex-row md:items-center md:justify-between">
    <div class="mb-3 md:mb-0 flex items-start gap-2">
        <i data-lucide="store" class="w-5 h-5 text-gray-600"></i>
        <div>
            <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">Menampilkan riwayat bonus: <span class="outlet-name">Loading...</span></h2>
            <p class="text-sm text-gray-600">Data riwayat bonus untuk <span class=" outlet-name"></span></p>
        </div>
    </div>
</div>

<!-- Table Riwayat Transaksi -->
<div class="bg-white rounded-lg shadow p-4">
    <!-- Header & Filter Row -->
    <div class="flex flex-col mb-4">
        <!-- Title and Date Filter Row -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-3">
            <h3 class="text-2xl font-bold text-gray-800">Riwayat Bonus</h3>
            
            <div class="relative mt-2 sm:mt-0">
                <input id="transDateInput" type="text"
                    class="w-full sm:w-56 pl-10 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                    placeholder="Pilih Tanggal" />
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <i data-lucide="calendar" class="w-4 h-4 text-gray-500"></i>
                </span>
            </div>
        </div>
        
        <!-- Filter and Search Row -->
        <div class="flex flex-col sm:flex-row gap-3">
            <!-- Search Bar -->
            <div class="flex-1 relative">
                <input type="text" id="searchMember"
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                    placeholder="Cari member atau kasir..." />
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                    <i data-lucide="search" class="w-4 h-4"></i>
                </span>
            </div>
            
            <!-- Status Filter -->
            <div class="relative">
                <select id="statusFilter" class="w-full sm:w-48 pl-3 pr-8 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500 bg-white">
                    <option value="">Semua Status</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Disetujui</option>
                    <option value="rejected">Ditolak</option>
                    <option value="completed">Selesai</option>
                </select>
            </div>
            
            <!-- Type Filter -->
            <div class="relative">
                <select id="typeFilter" class="w-full sm:w-48 pl-3 pr-8 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500 bg-white">
                    <option value="">Semua Tipe</option>
                    <option value="automatic">Otomatis</option>
                    <option value="manual">Manual</option>
                </select>
            </div>
        </div>
    </div>
    
    <!-- Table Content -->
    <div class="overflow-x-auto">
        <table class="w-full text-base">
            <thead class="text-left text-gray-700 border-b-2">
                <tr>
                    <th class="py-3 font-bold">Tanggal</th>
                    <th class="py-3 font-bold">Member</th>
                    <th class="py-3 font-bold">Kasir</th>
                    <th class="py-3 font-bold">Tipe</th>
                    <th class="py-3 font-bold">Produk Bonus</th>
                    <th class="py-3 font-bold">Jumlah</th>
                    <th class="py-3 font-bold">Status</th>
                    <th class="py-3 font-bold text-left">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 divide-y">
                <!-- Data akan diisi secara dinamis -->
                <tr>
                    <td colspan="8" class="py-8 text-center">
                        <div class="flex flex-col items-center justify-center gap-2 mx-auto">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                                 class="animate-spin text-green-500">
                                <path d="M21 12a9 9 0 1 1-6.219-8.56"/>
                            </svg>
                            <span class="text-gray-500">Memuat data bonus...</span>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Detail Bonus -->
<div id="modalDetail" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold">Detail Bonus</h3>
                <button onclick="closeDetailModal()" class="text-gray-500 hover:text-gray-700">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <p class="text-gray-500">Member</p>
                    <p id="detailMember" class="font-medium"></p>
                </div>
                <div>
                    <p class="text-gray-500">Tanggal/Waktu</p>
                    <p id="detailDateTime" class="font-medium"></p>
                </div>
                <div>
                    <p class="text-gray-500">Kasir</p>
                    <p id="detailCashier" class="font-medium"></p>
                </div>
                <div>
                    <p class="text-gray-500">Tipe Bonus</p>
                    <p id="detailType" class="font-medium"></p>
                </div>
                <div>
                    <p class="text-gray-500">Status</p>
                    <p id="detailStatus" class="font-medium"></p>
                </div>
                <div>
                    <p class="text-gray-500">Total Nilai</p>
                    <p id="detailTotalValue" class="font-medium"></p>
                </div>
            </div>
            
            <!-- Approval Information Section -->
            <div id="approvalInfoSection" class="hidden mb-4 p-3 bg-gray-50 rounded-lg">
                <h4 class="font-medium mb-2 text-gray-700">Informasi Approval</h4>
                <div class="grid grid-cols-1 gap-2 text-sm">
                    <div id="approverInfo" class="hidden">
                        <span class="text-gray-500">Disetujui/Ditolak oleh:</span>
                        <span id="detailApprover" class="font-medium ml-1"></span>
                    </div>
                    <div id="approvalDateInfo" class="hidden">
                        <span class="text-gray-500">Tanggal Approval:</span>
                        <span id="detailApprovalDate" class="font-medium ml-1"></span>
                    </div>
                    <div id="approvalNotesInfo" class="hidden">
                        <span class="text-gray-500">Catatan:</span>
                        <span id="detailApprovalNotes" class="font-medium ml-1"></span>
                    </div>
                    <div id="rejectionReasonInfo" class="hidden">
                        <span class="text-gray-500">Alasan Penolakan:</span>
                        <span id="detailRejectionReason" class="font-medium ml-1 text-red-600"></span>
                    </div>
                    <div id="paymentProofInfo" class="hidden">
                        <span class="text-gray-500">Bukti Pembayaran:</span>
                        <a id="detailPaymentProofLink" href="#" class="font-medium ml-1 text-blue-600 hover:text-blue-800">Lihat Bukti</a>
                    </div>
                </div>
            </div>
            
            <div class="mb-4">
                <h4 class="font-medium mb-2">Item Bonus</h4>
                <div id="detailBonusItems"></div>
            </div>
            
            <div class="border-t pt-4 space-y-2">
                <div class="flex justify-between">
                    <span>Aturan Bonus</span>
                    <span id="detailBonusRule" class="font-medium"></span>
                </div>
                <div class="flex justify-between">
                    <span>Keterangan</span>
                    <span id="detailDescription" class="font-medium"></span>
                </div>
                <div class="flex justify-between border-t pt-2 font-bold text-lg">
                    <span>Total Nilai Bonus</span>
                    <span id="detailTotalValueMain" class="text-green-600"></span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Refund -->
<div id="modalRefund" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
        <div class="flex items-start gap-4">
            <div class="flex-shrink-0 p-2 bg-red-100 rounded-full">
                <i data-lucide="alert-triangle" class="w-6 h-6 text-red-600"></i>
            </div>
            <div class="flex-1">
                <h3 id="modalRefundTitle" class="text-lg font-semibold text-gray-900">Konfirmasi Refund</h3>
                <div class="mt-2">
                    <p class="text-sm text-gray-600">Anda yakin ingin melakukan refund untuk transaksi ini?</p>
                    <p id="refundInvoiceText" class="text-sm font-medium mt-1"></p>
                    
                    <!-- Transaction Summary -->
                    <div id="refundTransactionSummary" class="mt-3 p-3 bg-gray-50 rounded-md">
                        <h4 class="text-sm font-medium text-gray-900 mb-2">Detail Transaksi:</h4>
                        <div class="text-sm text-gray-600 space-y-1">
                            <div class="flex justify-between">
                                <span>Total Transaksi:</span>
                                <span id="refundTotalAmount" class="font-medium"></span>
                            </div>
                            <div class="flex justify-between">
                                <span>Metode Pembayaran:</span>
                                <span id="refundPaymentMethod" class="font-medium"></span>
                            </div>
                            <div class="flex justify-between">
                                <span>Tanggal:</span>
                                <span id="refundDate" class="font-medium"></span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Refund Reason -->
                    <div class="mt-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Alasan Refund *</label>
                        <select id="refundReason" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                            <option value="">Pilih alasan refund</option>
                            <option value="produk_rusak">Produk Rusak/Cacat</option>
                            <option value="salah_produk">Salah Produk</option>
                            <option value="tidak_sesuai">Tidak Sesuai Ekspektasi</option>
                            <option value="dibatalkan_pelanggan">Dibatalkan Pelanggan</option>
                            <option value="kesalahan_sistem">Kesalahan Sistem</option>
                            <option value="lainnya">Lainnya</option>
                        </select>
                    </div>
                    
                    <!-- Custom Reason -->
                    <div id="customReasonSection" class="hidden mt-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan Tambahan</label>
                        <textarea id="customReason" rows="2" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500" placeholder="Jelaskan alasan refund secara detail..."></textarea>
                    </div>
                    
                    <div class="mt-3 p-3 bg-yellow-50 rounded-md">
                        <p class="text-xs text-yellow-700"><strong>Perhatian:</strong> Refund tidak dapat dibatalkan. Pastikan produk telah dikembalikan dan semua persyaratan refund terpenuhi.</p>
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button onclick="closeRefundModal()" type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none">
                        Batal
                    </button>
                    <button onclick="processRefund()" type="button" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md shadow-sm hover:bg-red-700 focus:outline-none">
                        Proses Refund
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Approve Transaksi -->
<div id="modalApprove" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl p-6 w-96">
        <div class="flex items-start gap-4">
            <div class="flex-shrink-0 p-2 bg-green-100 rounded-full">
                <i data-lucide="check-circle" class="w-6 h-6 text-green-600"></i>
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-semibold text-gray-900">Setujui Transaksi</h3>
                <div class="mt-2">
                    <p class="text-sm text-gray-600">Anda yakin ingin menyetujui transaksi ini?</p>
                    <p id="approveInvoiceText" class="text-sm font-medium mt-1"></p>
                    <div class="mt-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Catatan (Opsional)</label>
                        <textarea id="approvalNotes" rows="3" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500" placeholder="Tambahkan catatan approval..."></textarea>
                    </div>
                </div>
                <div class="mt-4 flex justify-end gap-3">
                    <button onclick="closeApproveModal()" type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none">
                        Batal
                    </button>
                    <button onclick="processApprove()" type="button" class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-md shadow-sm hover:bg-green-700 focus:outline-none">
                        Setujui Transaksi
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Reject Transaksi -->
<div id="modalReject" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl p-6 w-96">
        <div class="flex items-start gap-4">
            <div class="flex-shrink-0 p-2 bg-red-100 rounded-full">
                <i data-lucide="x-circle" class="w-6 h-6 text-red-600"></i>
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-semibold text-gray-900">Tolak Transaksi</h3>
                <div class="mt-2">
                    <p class="text-sm text-gray-600">Berikan alasan penolakan transaksi ini:</p>
                    <p id="rejectInvoiceText" class="text-sm font-medium mt-1"></p>
                    <div class="mt-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Alasan Penolakan *</label>
                        <textarea id="rejectionReason" rows="3" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500" placeholder="Jelaskan alasan penolakan..." required></textarea>
                    </div>
                </div>
                <div class="mt-4 flex justify-end gap-3">
                    <button onclick="closeRejectModal()" type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none">
                        Batal
                    </button>
                    <button onclick="processReject()" type="button" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md shadow-sm hover:bg-red-700 focus:outline-none">
                        Tolak Transaksi
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Payment Proof -->
<div id="modalPaymentProof" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Bukti Pembayaran</h3>
            <button onclick="closePaymentProofModal()" class="text-gray-500 hover:text-gray-700">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <div class="text-center">
            <img id="paymentProofImage" src="" alt="Bukti Pembayaran" class="max-w-full max-h-96 mx-auto rounded-lg shadow-md">
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
<script>
    let bonusCache = [];
    // Script utama untuk halaman Riwayat Bonus
    document.addEventListener('DOMContentLoaded', () => {
        // Cek jika token ada di localStorage
        if (!localStorage.getItem('token')) {
            window.location.href = '/login';
            return;
        }

        // Inisialisasi flatpickr untuk filter tanggal
        flatpickr("#transDateInput", {
            dateFormat: "d/m/Y",
            maxDate: "today",
            locale: "id",
            onChange: function(selectedDates, dateStr) {
                if (selectedDates.length > 0) {
                    // Pastikan tanggal dikirim dalam format YYYY-MM-DD dengan timezone yang benar
                    const date = formatDateForAPI(selectedDates[0]);
                    fetchBonusHistory(date);
                } else {
                    // Jika tidak ada tanggal terpilih, tampilkan semua bonus
                    fetchBonusHistory(null);
                }
            }
        });

        // Load data awal
        fetchBonusHistory();
        
        // Pencarian dan Filter
        document.getElementById('searchMember').addEventListener('input', applyFilters);
        document.getElementById('statusFilter').addEventListener('change', applyFilters);
        document.getElementById('typeFilter').addEventListener('change', applyFilters);

        // Connect outlet selection to bonus history updates
        connectOutletSelectionToHistory();

        // Refresh Lucide icons
        if (window.lucide) window.lucide.createIcons();
    });

    // Function to get currently selected outlet ID - sama seperti di riwayat stok
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

    // Connect to outlet selection dropdown
    function connectOutletSelectionToHistory() {
        // Listen for outlet changes in localStorage
        window.addEventListener('storage', function(event) {
            if (event.key === 'selectedOutletId') {
                // Get current date if available
                const datePicker = document.getElementById('transDateInput');
                let date = null;
                if (datePicker && datePicker._flatpickr && datePicker._flatpickr.selectedDates.length > 0) {
                    date = formatDateForAPI(datePicker._flatpickr.selectedDates[0]);
                }
                
                // Reload history with new outlet
                fetchBonusHistory(date);
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
                        const datePicker = document.getElementById('transDateInput');
                        let date = null;
                        if (datePicker && datePicker._flatpickr && datePicker._flatpickr.selectedDates.length > 0) {
                            date = formatDateForAPI(datePicker._flatpickr.selectedDates[0]);
                        }
                        
                        fetchBonusHistory(date);
                    }, 100);
                }
            });
        }
    }

    // Update outlet info when data is loaded
    function updateOutletInfo(data) {
        if (data && data.outlet) {
            const outletElements = document.querySelectorAll('.outlet-name');
            outletElements.forEach(el => {
                el.textContent = `${data.outlet.name}`;
            });
            
            const addressElements = document.querySelectorAll('.outlet-address');
            addressElements.forEach(el => {
                el.textContent = data.outlet.address || '';
            });
        } else {
            // No outlet info in data, fetch it separately
            updateOutletInfoFromSelection();
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
                    el.textContent = `${data.name}`;
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

    // Fungsi untuk fetch data bonus - dimodifikasi untuk menyertakan outlet_id
    async function fetchBonusHistory(date = null) {
        try {
            const token = localStorage.getItem('token');
            if (!token) {
                window.location.href = '/login';
                return;
            }

            // Get the current outlet ID
            const outletId = getSelectedOutletId();
            
            // Format parameter tanggal
            const params = new URLSearchParams();
            if (date) {
                // Gunakan tanggal yang sama untuk date_from dan date_to
                // untuk menampilkan bonus pada hari yang dipilih saja
                params.append('date_from', date);
                params.append('date_to', date);
            }
            
            // Tambahkan outlet_id ke parameters
            params.append('outlet_id', outletId);
            
            // Fetch data dari endpoint bonus dengan token authorization
            const response = await fetch(`/api/bonus/history?${params.toString()}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Authorization': `Bearer ${token}`
                }
            });
            
            if (!response.ok) {
                if (response.status === 401) {
                    // Jika unauthorized, redirect ke login
                    window.location.href = '/login';
                    return;
                }
                throw new Error('Gagal mengambil data bonus');
            }
            
            const result = await response.json();
            
            // Update outlet info if available
            updateOutletInfoFromSelection();
            
            // Pastikan kita mengakses data dari response
            if (result.data && result.data.data) {
                // Store in global cache
                bonusCache = result.data.data;
                renderBonusData(result.data.data);
            } else if (result.data && Array.isArray(result.data)) {
                // Alternatif jika struktur data berbeda
                bonusCache = result.data;
                renderBonusData(result.data);
            } else {
                // Jika tidak ada data
                bonusCache = [];
                renderBonusData([]);
            }
            
        } catch (error) {
            console.error('Error:', error);
            showAlert('error', error.message);
        }
    }


    // Fungsi untuk render data bonus ke tabel
    function renderBonusData(bonusTransactions) {
        const tbody = document.querySelector('tbody');
        tbody.innerHTML = '';
        
        console.log("Data bonus untuk dirender:", bonusTransactions); // Untuk debugging
        
        if (!bonusTransactions || bonusTransactions.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="8" class="py-4 text-center text-gray-500">
                        Tidak ada data bonus pada tanggal ini.
                    </td>
                </tr>
            `;
            return;
        }
        
        bonusTransactions.forEach(bonus => {
            const row = document.createElement('tr');
            row.className = 'border-b hover:bg-gray-50';
            row.dataset.bonusId = bonus.id; // Add bonus ID for filtering
            
            // Calculate total bonus items and value
            let totalItems = 0;
            let totalValue = 0;
            let bonusProducts = [];
            
            if (bonus.bonus_items && Array.isArray(bonus.bonus_items)) {
                bonus.bonus_items.forEach(item => {
                    totalItems += item.quantity || 0;
                    totalValue += (item.quantity || 0) * (item.product?.price || 0);
                    bonusProducts.push(item.product?.name || 'Produk');
                });
            }
            
            row.innerHTML = `
                <td class="py-4">${formatDateTime(bonus.created_at)}</td>
                <td class="py-4">${bonus.member?.name || 'Guest'}</td>
                <td class="py-4">${bonus.cashier?.name || 'Kasir'}</td>
                <td class="py-4">
                    <span class="px-2 py-1 ${getBonusTypeBadgeClass(bonus.type)} rounded-full text-xs font-medium">
                        ${getBonusTypeText(bonus.type)}
                    </span>
                </td>
                <td class="py-4">
                    <div class="max-w-xs">
                        <p class="text-sm font-medium truncate">${bonusProducts.slice(0, 2).join(', ')}</p>
                        ${bonusProducts.length > 2 ? `<p class="text-xs text-gray-500">+${bonusProducts.length - 2} lainnya</p>` : ''}
                    </div>
                </td>
                <td class="py-4 font-medium">${totalItems} item</td>
                <td class="py-4">
                    <span class="px-2 py-1 ${getBonusStatusBadgeClass(bonus.status)} rounded-full text-xs font-medium">
                        ${getBonusStatusText(bonus.status)}
                    </span>
                </td>
                <td class="py-4">
                    <div class="flex space-x-2">
                        <a href="#" onclick="openBonusDetailModal('${bonus.id}')" class="text-gray-600 hover:text-blue-600" title="Lihat Detail">
                            <i data-lucide="eye" class="w-4 h-4"></i>
                        </a>
                        ${bonus.status === 'pending' ? `
                        <a href="#" onclick="openApproveBonusModal('${bonus.id}')" class="text-gray-600 hover:text-green-600" title="Setujui">
                            <i data-lucide="check-circle" class="w-4 h-4"></i>
                        </a>
                        <a href="#" onclick="openRejectBonusModal('${bonus.id}')" class="text-gray-600 hover:text-red-600" title="Tolak">
                            <i data-lucide="x-circle" class="w-4 h-4"></i>
                        </a>
                        ` : ''}
                    </div>
                </td>
            `;
            tbody.appendChild(row);
        });
        
        if (window.lucide) window.lucide.createIcons();
    }

    // Fungsi untuk fetch detail transaksi
    function getTransactionDetail(orderId) {
        // Find order in cached data
        const detail = transactionsCache.find(order => order.id == orderId);
        
        if (!detail) {
            return null;
        }
        
        return detail;
    }

    // Fungsi untuk modal detail
    async function openDetailModal(orderId) {
        try {
            console.log(`Opening detail modal for order ID: ${orderId}`);
            
            const transaction = transactionsCache.find(t => t.id == orderId);
            if (!transaction) {
                showAlert('error', 'Detail transaksi tidak ditemukan');
                return;
            }

            // Daftar element yang diperlukan
            const elements = {
                invoice: document.getElementById('detailInvoice'),
                dateTime: document.getElementById('detailDateTime'),
                paymentMethod: document.getElementById('detailPaymentMethod'),
                status: document.getElementById('detailStatus'),
                approvalStatus: document.getElementById('detailApprovalStatus'),
                total: document.getElementById('detailTotal'),
                subtotal: document.getElementById('detailSubtotal'),
                tax: document.getElementById('detailTax'),
                discount: document.getElementById('detailDiscount'),
                totalPaid: document.getElementById('detailTotalPaid'),
                change: document.getElementById('detailChange'),
                items: document.getElementById('detailItems')
            };

            // Validasi element
            for (const [key, element] of Object.entries(elements)) {
                if (!element) {
                    console.error(`Element not found: ${key}`);
                    throw new Error(`Element ${key} tidak ditemukan`);
                }
            }

            // Isi data
            elements.invoice.textContent = transaction.order_number;
            elements.dateTime.textContent = formatDateTime(transaction.created_at);
            elements.paymentMethod.textContent = getPaymentMethodText(transaction.payment_method);
            elements.status.textContent = getStatusText(transaction.status);
            elements.approvalStatus.textContent = getApprovalStatusText(transaction.approval_status);
            elements.total.textContent = formatCurrency(transaction.total);
            elements.subtotal.textContent = formatCurrency(transaction.subtotal);
            elements.tax.textContent = formatCurrency(transaction.tax);
            elements.discount.textContent = formatCurrency(transaction.discount);
            elements.totalPaid.textContent = formatCurrency(transaction.total_paid);
            elements.change.textContent = formatCurrency(transaction.change);

            // Handle approval information
            const approvalSection = document.getElementById('approvalInfoSection');
            const approverInfo = document.getElementById('approverInfo');
            const approvalDateInfo = document.getElementById('approvalDateInfo');
            const approvalNotesInfo = document.getElementById('approvalNotesInfo');
            const rejectionReasonInfo = document.getElementById('rejectionReasonInfo');
            const paymentProofInfo = document.getElementById('paymentProofInfo');

            // Reset approval info visibility
            approverInfo.classList.add('hidden');
            approvalDateInfo.classList.add('hidden');
            approvalNotesInfo.classList.add('hidden');
            rejectionReasonInfo.classList.add('hidden');
            paymentProofInfo.classList.add('hidden');

            let showApprovalSection = false;

            // Show approver info if available
            if (transaction.approved_by) {
                document.getElementById('detailApprover').textContent = transaction.approved_by;
                approverInfo.classList.remove('hidden');
                showApprovalSection = true;
            }

            // Show approval date if available
            if (transaction.approved_at) {
                document.getElementById('detailApprovalDate').textContent = transaction.approved_at;
                approvalDateInfo.classList.remove('hidden');
                showApprovalSection = true;
            }

            // Show approval notes if available
            if (transaction.approval_notes) {
                document.getElementById('detailApprovalNotes').textContent = transaction.approval_notes;
                approvalNotesInfo.classList.remove('hidden');
                showApprovalSection = true;
            }

            // Show rejection reason if available
            if (transaction.rejection_reason) {
                document.getElementById('detailRejectionReason').textContent = transaction.rejection_reason;
                rejectionReasonInfo.classList.remove('hidden');
                showApprovalSection = true;
            }

            // Show payment proof link if available
            if (transaction.payment_proof_url) {
                const proofLink = document.getElementById('detailPaymentProofLink');
                proofLink.onclick = (e) => {
                    e.preventDefault();
                    openPaymentProofModal(transaction.payment_proof_url);
                };
                paymentProofInfo.classList.remove('hidden');
                showApprovalSection = true;
            }

            // Show/hide approval section
            if (showApprovalSection) {
                approvalSection.classList.remove('hidden');
            } else {
                approvalSection.classList.add('hidden');
            }

            // Isi items
            elements.items.innerHTML = '';
            if (transaction.items?.length > 0) {
                transaction.items.forEach(item => {
                    const itemElement = document.createElement('div');
                    itemElement.className = 'border-b py-2';
                    itemElement.innerHTML = `
                        <div class="flex justify-between">
                            <div>
                                <p class="font-medium">${item.product}</p>
                                <p class="text-sm text-gray-500">${item.quantity} × ${formatCurrency(item.price)}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-medium">${formatCurrency(item.total)}</p>
                                ${item.discount > 0 ? `<p class="text-sm text-red-500">Diskon: ${formatCurrency(item.discount)}</p>` : ''}
                            </div>
                        </div>
                    `;
                    elements.items.appendChild(itemElement);
                });
            } else {
                elements.items.innerHTML = '<p class="text-gray-500 py-4">Tidak ada item</p>';
            }

            // Tampilkan modal
            const modal = document.getElementById('modalDetail');
            if (!modal) {
                throw new Error('Modal detail tidak ditemukan');
            }
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            
            if (window.lucide) {
                window.lucide.createIcons();
            }

        } catch (error) {
            console.error('Error in openDetailModal:', error);
            showAlert('error', 'Gagal memuat detail transaksi: ' + error.message);
        }
    }

    // Fungsi untuk menutup modal detail
    function closeDetailModal() {
        const modal = document.getElementById('modalDetail');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    // Fungsi untuk modal refund
    // Request cancellation/refund (for cashiers and admin)
    function openCancellationRequestModal(invoiceNumber, orderId) {
        try {
            // Find transaction in cache
            const transaction = transactionsCache.find(t => t.id == orderId);
            if (!transaction) {
                showAlert('error', 'Data transaksi tidak ditemukan');
                return;
            }

            // Check if transaction can request cancellation
            if (!['pending', 'completed'].includes(transaction.status)) {
                showAlert('error', 'Transaksi ini tidak dapat dibatalkan atau direfund');
                return;
            }

            // Check if already has cancellation request
            if (transaction.cancellation_status && transaction.cancellation_status !== 'none') {
                showAlert('error', 'Transaksi ini sudah memiliki permintaan pembatalan/refund');
                return;
            }

            // Pastikan modal dan elemen-elemennya ada
            const modal = document.getElementById('modalRefund');
            const invoiceTextEl = document.getElementById('refundInvoiceText');
            const modalTitleEl = document.getElementById('modalRefundTitle');
            const confirmButton = modal?.querySelector('button:last-child');
            const refundReasonSelect = document.getElementById('refundReason');
            const customReasonSection = document.getElementById('customReasonSection');
            const customReasonTextarea = document.getElementById('customReason');
            
            if (!modal || !invoiceTextEl || !modalTitleEl || !confirmButton) {
                throw new Error('Elemen modal tidak ditemukan. Pastikan struktur HTML benar.');
            }

            // Determine request type
            const requestType = transaction.status === 'pending' ? 'pembatalan' : 'refund';
            
            // Isi data ke modal
            invoiceTextEl.textContent = `Invoice: ${invoiceNumber}`;
            modalTitleEl.textContent = `Permintaan ${requestType.charAt(0).toUpperCase() + requestType.slice(1)}`;
            
            // Fill transaction summary
            document.getElementById('refundTotalAmount').textContent = formatCurrency(transaction.total);
            document.getElementById('refundPaymentMethod').textContent = getPaymentMethodText(transaction.payment_method);
            document.getElementById('refundDate').textContent = formatDateTime(transaction.created_at);
            
            // Reset form
            refundReasonSelect.value = '';
            customReasonSection.classList.add('hidden');
            customReasonTextarea.value = '';
            
            // Simpan transaction data di modal
            modal.dataset.orderId = orderId;
            modal.dataset.transactionData = JSON.stringify(transaction);
            
            // Setup reason change handler
            refundReasonSelect.onchange = function() {
                if (this.value === 'lainnya') {
                    customReasonSection.classList.remove('hidden');
                } else {
                    customReasonSection.classList.add('hidden');
                    customReasonTextarea.value = '';
                }
            };
            
            // Ubah teks tombol konfirmasi
            confirmButton.textContent = `Ajukan ${requestType.charAt(0).toUpperCase() + requestType.slice(1)}`;
            
            // Tampilkan modal
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            
        } catch (error) {
            console.error('Error in openRefundModal:', error);
            showAlert('error', 'Gagal membuka modal refund: ' + error.message);
        }
    }

    function closeRefundModal() {
        document.getElementById('modalRefund').classList.add('hidden');
        document.getElementById('modalRefund').classList.remove('flex');
    }

    // Process cancellation/refund request
    async function processRefund() {
        const modal = document.getElementById('modalRefund');
        const orderId = modal.dataset.orderId;
        const refundReasonSelect = document.getElementById('refundReason');
        const customReasonTextarea = document.getElementById('customReason');
        
        try {
            // Validate required fields
            if (!refundReasonSelect.value) {
                showAlert('error', 'Pilih alasan terlebih dahulu');
                return;
            }

            if (refundReasonSelect.value === 'lainnya' && !customReasonTextarea.value.trim()) {
                showAlert('error', 'Keterangan tambahan wajib diisi untuk alasan "Lainnya"');
                return;
            }

            const token = localStorage.getItem('token');
            if (!token) {
                window.location.href = '/login';
                return;
            }

            // Prepare cancellation reason
            let cancellationReason = refundReasonSelect.options[refundReasonSelect.selectedIndex].text;
            if (refundReasonSelect.value === 'lainnya') {
                cancellationReason += ': ' + customReasonTextarea.value.trim();
            }

            // Tampilkan loading state
            const confirmButton = modal.querySelector('button:last-child');
            const originalButtonText = confirmButton.innerHTML;
            confirmButton.innerHTML = `
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Mengajukan Permintaan...
            `;
            confirmButton.disabled = true;

            // Create cancellation request
            const requestData = {
                reason: cancellationReason,
                notes: customReasonTextarea.value.trim() || null
            };

            // Call the new cancellation request API
            const response = await fetch(`/api/orders/cancellation/request/${orderId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                body: JSON.stringify(requestData)
            });

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || `Gagal mengajukan permintaan (Status: ${response.status})`);
            }

            const result = await response.json();
            
            // Get transaction details for success message
            const transactionData = JSON.parse(modal.dataset.transactionData || '{}');
            const requestType = transactionData.status === 'pending' ? 'pembatalan' : 'refund';
            const successMessage = `Permintaan ${requestType} berhasil diajukan untuk Invoice ${transactionData.order_number || ''}. Menunggu persetujuan admin.`;
            
            showAlert('success', successMessage);
            closeRefundModal();
            fetchTransactionHistory(); // Refresh data
            
        } catch (error) {
            console.error('Error:', error);
            showAlert('error', error.message);
        } finally {
            // Kembalikan tombol ke state awal
            const confirmButton = modal.querySelector('button:last-child');
            confirmButton.innerHTML = 'Proses Refund';
            confirmButton.disabled = false;
        }
    }

    // Admin function to review and approve/reject cancellation requests
    function openCancellationApprovalModal(invoiceNumber, orderId) {
        try {
            // Find transaction in cache
            const transaction = transactionsCache.find(t => t.id == orderId);
            if (!transaction) {
                showAlert('error', 'Data transaksi tidak ditemukan');
                return;
            }

            // Check if transaction has pending cancellation request
            if (transaction.cancellation_status !== 'requested') {
                showAlert('error', 'Tidak ada permintaan pembatalan/refund yang pending');
                return;
            }

            // Create and show approval modal
            const modalHtml = `
                <div id="cancellationApprovalModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
                    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
                        <h3 class="text-lg font-semibold mb-4">Review Permintaan ${transaction.status === 'pending' ? 'Pembatalan' : 'Refund'}</h3>
                        
                        <div class="mb-4">
                            <p class="text-sm text-gray-600 mb-2">Invoice: ${invoiceNumber}</p>
                            <p class="text-sm text-gray-600 mb-2">Total: ${formatCurrency(transaction.total)}</p>
                            <p class="text-sm text-gray-600 mb-2">Alasan: ${transaction.cancellation_reason || 'Tidak ada alasan'}</p>
                            <p class="text-sm text-gray-600 mb-4">Keterangan: ${transaction.cancellation_notes || 'Tidak ada keterangan'}</p>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Admin (opsional)</label>
                            <textarea id="adminNotes" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" rows="3" placeholder="Tambahkan catatan jika diperlukan"></textarea>
                        </div>

                        <div class="flex justify-end space-x-3">
                            <button onclick="closeCancellationApprovalModal()" class="px-4 py-2 text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200">Batal</button>
                            <button onclick="rejectCancellationRequest('${orderId}')" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">Tolak</button>
                            <button onclick="approveCancellationRequest('${orderId}')" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600">Setujui</button>
                        </div>
                    </div>
                </div>
            `;

            document.body.insertAdjacentHTML('beforeend', modalHtml);

        } catch (error) {
            console.error('Error in openCancellationApprovalModal:', error);
            showAlert('error', 'Gagal membuka modal approval: ' + error.message);
        }
    }

    function closeCancellationApprovalModal() {
        const modal = document.getElementById('cancellationApprovalModal');
        if (modal) {
            modal.remove();
        }
    }

    async function approveCancellationRequest(orderId) {
        try {
            const adminNotes = document.getElementById('adminNotes').value.trim();
            const token = localStorage.getItem('token');
            
            if (!token) {
                window.location.href = '/login';
                return;
            }

            const response = await fetch(`/api/orders/cancellation/approve/${orderId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                body: JSON.stringify({
                    admin_notes: adminNotes
                })
            });

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || 'Gagal menyetujui permintaan');
            }

            const result = await response.json();
            showAlert('success', 'Permintaan pembatalan/refund berhasil disetujui');
            closeCancellationApprovalModal();
            fetchTransactionHistory(); // Refresh data

        } catch (error) {
            console.error('Error approving cancellation:', error);
            showAlert('error', error.message);
        }
    }

    async function rejectCancellationRequest(orderId) {
        try {
            const adminNotes = document.getElementById('adminNotes').value.trim();
            
            if (!adminNotes) {
                showAlert('error', 'Catatan admin wajib diisi untuk penolakan');
                return;
            }

            const token = localStorage.getItem('token');
            
            if (!token) {
                window.location.href = '/login';
                return;
            }

            const response = await fetch(`/api/orders/cancellation/reject/${orderId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                body: JSON.stringify({
                    admin_notes: adminNotes
                })
            });

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || 'Gagal menolak permintaan');
            }

            const result = await response.json();
            showAlert('success', 'Permintaan pembatalan/refund berhasil ditolak');
            closeCancellationApprovalModal();
            fetchTransactionHistory(); // Refresh data

        } catch (error) {
            console.error('Error rejecting cancellation:', error);
            showAlert('error', error.message);
        }
    }

    // Fungsi untuk menampilkan alert
    function showAlert(type, message) {
        const alertContainer = document.getElementById('alertContainer');
        const alertId = 'alert-' + Date.now();
        const alert = document.createElement('div');
        alert.id = alertId;
        alert.className = `p-4 rounded-md shadow-md animate-fade-in-up ${type === 'error' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800'}`;
        alert.innerHTML = `
            <div class="flex items-center gap-2">
                <i data-lucide="${type === 'error' ? 'alert-circle' : 'check-circle'}" class="w-5 h-5"></i>
                <span>${message}</span>
                <button onclick="document.getElementById('${alertId}').classList.add('animate-fade-out'); setTimeout(() => document.getElementById('${alertId}').remove(), 300)" class="ml-auto">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        `;
        alertContainer.appendChild(alert);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (document.getElementById(alertId)) {
                document.getElementById(alertId).classList.add('animate-fade-out');
                setTimeout(() => {
                    if (document.getElementById(alertId)) {
                        document.getElementById(alertId).remove();
                    }
                }, 300);
            }
        }, 5000);
        
        // Refresh ikon Lucide
        if (window.lucide) window.lucide.createIcons();
    }

    // Function untuk apply filters
    function applyFilters() {
        const searchTerm = document.getElementById('searchMember').value.toLowerCase();
        const statusFilter = document.getElementById('statusFilter').value;
        const typeFilter = document.getElementById('typeFilter').value;
        const rows = document.querySelectorAll('tbody tr');
        
        rows.forEach(row => {
            // Skip loading/empty state rows
            if (row.cells.length < 8) {
                return;
            }
            
            const member = row.cells[1]?.textContent?.toLowerCase() || '';
            const cashier = row.cells[2]?.textContent?.toLowerCase() || '';
            const statusText = row.cells[6]?.textContent?.toLowerCase() || '';
            const typeText = row.cells[3]?.textContent?.toLowerCase() || '';
            
            // Get actual values from the data
            let actualStatus = '';
            let actualType = '';
            
            // Extract values from bonus data if available
            if (row.dataset && row.dataset.bonusId) {
                const bonus = bonusCache.find(b => b.id == row.dataset.bonusId);
                if (bonus) {
                    actualStatus = bonus.status;
                    actualType = bonus.type;
                }
            } else {
                // Fallback: derive from display text
                if (statusText.includes('pending')) actualStatus = 'pending';
                else if (statusText.includes('disetujui')) actualStatus = 'approved';
                else if (statusText.includes('ditolak')) actualStatus = 'rejected';
                else if (statusText.includes('selesai')) actualStatus = 'completed';
                
                if (typeText.includes('otomatis')) actualType = 'automatic';
                else if (typeText.includes('manual')) actualType = 'manual';
            }
            
            // Apply filters
            const matchesSearch = member.includes(searchTerm) || cashier.includes(searchTerm);
            const matchesStatus = !statusFilter || actualStatus === statusFilter;
            const matchesType = !typeFilter || actualType === typeFilter;
            
            const shouldShow = matchesSearch && matchesStatus && matchesType;
            row.style.display = shouldShow ? '' : 'none';
        });
    }

    // Helper untuk format tanggal
    function formatDateTime(dateString) {
        if (!dateString) return '-';
        
        // Jika format sudah "DD/MM/YYYY HH:mm" seperti dari API
        if (dateString.match(/^\d{2}\/\d{2}\/\d{4} \d{2}:\d{2}$/)) {
            return dateString; // Return langsung karena format sudah sesuai
        }
        
        try {
            const date = new Date(dateString);
            return date.toLocaleDateString('id-ID', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            }).replace(',', '');
        } catch (error) {
            console.error('Error formatting date:', error);
            return dateString;
        }
    }

    // Helper untuk format mata uang
    function formatCurrency(amount) {
        try {
            return 'Rp ' + Number(amount || 0).toLocaleString('id-ID');
        } catch (error) {
            console.error('Error formatting currency:', error);
            return 'Rp 0';
        }
    }

    // Helper untuk tampilan payment method
    function getPaymentMethodText(method) {
        const methods = {
            'cash': 'Tunai',
            'qris': 'QRIS',
            'transfer': 'Transfer'
        };
        return methods[method] || method || 'Tidak diketahui';
    }

    function getPaymentBadgeClass(method) {
        const classes = {
            'cash': 'bg-green-100 text-green-800 border-green-200',
            'qris': 'bg-green-100 text-green-800 border-green-200',
            'transfer': 'bg-purple-100 text-purple-800 border-purple-200'
        };
        return classes[method] || 'bg-gray-100 text-gray-800';
    }

    // Format tanggal untuk API (YYYY-MM-DD)
    function formatDateForAPI(date) {
        // Ambil tanggal dalam timezone lokal user
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        
        // Format dalam YYYY-MM-DD
        return `${year}-${month}-${day}`;
    }

    // Helper untuk status transaksi
    function getStatusText(status) {
        const statusMap = {
            'pending': 'Menunggu',
            'completed': 'Selesai',
            'cancelled': 'Dibatalkan'
        };
        return statusMap[status] || status || 'Tidak diketahui';
    }

    function getStatusBadgeClass(status) {
        const classMap = {
            'pending': 'bg-yellow-100 text-yellow-800 border-yellow-200',
            'completed': 'bg-green-100 text-green-800 border-green-200',
            'cancelled': 'bg-red-100 text-red-800 border-red-200'
        };
        return classMap[status] || 'bg-gray-100 text-gray-800';
    }

    // Helper untuk approval status
    function getApprovalStatusText(approvalStatus) {
        const statusMap = {
            'pending': 'Menunggu Approval',
            'approved': 'Disetujui',
            'rejected': 'Ditolak'
        };
        return statusMap[approvalStatus] || approvalStatus || 'Tidak diketahui';
    }

    function getApprovalBadgeClass(approvalStatus) {
        const classMap = {
            'pending': 'bg-orange-100 text-orange-800 border-orange-200',
            'approved': 'bg-green-100 text-green-800 border-green-200',
            'rejected': 'bg-red-100 text-red-800 border-red-200'
        };
        return classMap[approvalStatus] || 'bg-gray-100 text-gray-800';
    }

    // Modal functions untuk approval
    function openApproveModal(invoiceNumber, orderId) {
        try {
            const modal = document.getElementById('modalApprove');
            const invoiceTextEl = document.getElementById('approveInvoiceText');
            const notesTextarea = document.getElementById('approvalNotes');
            
            if (!modal || !invoiceTextEl || !notesTextarea) {
                throw new Error('Elemen modal approve tidak ditemukan');
            }

            invoiceTextEl.textContent = `Invoice: ${invoiceNumber}`;
            notesTextarea.value = '';
            modal.dataset.orderId = orderId;
            
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        } catch (error) {
            console.error('Error in openApproveModal:', error);
            showAlert('error', 'Gagal membuka modal approval: ' + error.message);
        }
    }

    function closeApproveModal() {
        const modal = document.getElementById('modalApprove');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    async function processApprove() {
        const modal = document.getElementById('modalApprove');
        const orderId = modal.dataset.orderId;
        const notes = document.getElementById('approvalNotes').value.trim();
        
        try {
            const token = localStorage.getItem('token');
            if (!token) {
                window.location.href = '/login';
                return;
            }

            const confirmButton = modal.querySelector('button:last-child');
            const originalButtonText = confirmButton.innerHTML;
            confirmButton.innerHTML = `
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Memproses...
            `;
            confirmButton.disabled = true;

            const response = await fetch(`/api/orders/approve/${orderId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                body: JSON.stringify({ notes: notes })
            });

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || `Gagal menyetujui transaksi (Status: ${response.status})`);
            }

            const result = await response.json();
            showAlert('success', 'Transaksi berhasil disetujui');
            closeApproveModal();
            fetchTransactionHistory();
        } catch (error) {
            console.error('Error:', error);
            showAlert('error', error.message);
        } finally {
            const confirmButton = modal.querySelector('button:last-child');
            confirmButton.innerHTML = 'Setujui Transaksi';
            confirmButton.disabled = false;
        }
    }

    function openRejectModal(invoiceNumber, orderId) {
        try {
            const modal = document.getElementById('modalReject');
            const invoiceTextEl = document.getElementById('rejectInvoiceText');
            const reasonTextarea = document.getElementById('rejectionReason');
            
            if (!modal || !invoiceTextEl || !reasonTextarea) {
                throw new Error('Elemen modal reject tidak ditemukan');
            }

            invoiceTextEl.textContent = `Invoice: ${invoiceNumber}`;
            reasonTextarea.value = '';
            modal.dataset.orderId = orderId;
            
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        } catch (error) {
            console.error('Error in openRejectModal:', error);
            showAlert('error', 'Gagal membuka modal penolakan: ' + error.message);
        }
    }

    function closeRejectModal() {
        const modal = document.getElementById('modalReject');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    async function processReject() {
        const modal = document.getElementById('modalReject');
        const orderId = modal.dataset.orderId;
        const reason = document.getElementById('rejectionReason').value.trim();
        
        if (!reason) {
            showAlert('error', 'Alasan penolakan harus diisi');
            return;
        }
        
        try {
            const token = localStorage.getItem('token');
            if (!token) {
                window.location.href = '/login';
                return;
            }

            const confirmButton = modal.querySelector('button:last-child');
            const originalButtonText = confirmButton.innerHTML;
            confirmButton.innerHTML = `
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Memproses...
            `;
            confirmButton.disabled = true;

            const response = await fetch(`/api/orders/reject/${orderId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                body: JSON.stringify({ reason: reason })
            });

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || `Gagal menolak transaksi (Status: ${response.status})`);
            }

            const result = await response.json();
            showAlert('success', 'Transaksi berhasil ditolak');
            closeRejectModal();
            fetchTransactionHistory();
        } catch (error) {
            console.error('Error:', error);
            showAlert('error', error.message);
        } finally {
            const confirmButton = modal.querySelector('button:last-child');
            confirmButton.innerHTML = 'Tolak Transaksi';
            confirmButton.disabled = false;
        }
    }

    function openPaymentProofModal(imageUrl) {
        try {
            const modal = document.getElementById('modalPaymentProof');
            const image = document.getElementById('paymentProofImage');
            
            if (!modal || !image) {
                throw new Error('Elemen modal payment proof tidak ditemukan');
            }

            image.src = imageUrl;
            image.onerror = function() {
                image.alt = 'Gambar tidak dapat dimuat';
                image.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZGRkIi8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCwgc2Fucy1zZXJpZiIgZm9udC1zaXplPSIxNCIgZmlsbD0iIzk5OSIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0iPkdhZ2FsIG1lbXVhdCBnYW1iYXI8L3RleHQ+PC9zdmc+';
            };
            
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        } catch (error) {
            console.error('Error in openPaymentProofModal:', error);
            showAlert('error', 'Gagal membuka bukti pembayaran: ' + error.message);
        }
    }

    function closePaymentProofModal() {
        const modal = document.getElementById('modalPaymentProof');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    // Helper functions untuk bonus
    function getBonusTypeText(type) {
        const typeMap = {
            'automatic': 'Otomatis',
            'manual': 'Manual'
        };
        return typeMap[type] || type || 'Tidak diketahui';
    }

    function getBonusTypeBadgeClass(type) {
        const classMap = {
            'automatic': 'bg-blue-100 text-blue-800 border-blue-200',
            'manual': 'bg-purple-100 text-purple-800 border-purple-200'
        };
        return classMap[type] || 'bg-gray-100 text-gray-800';
    }

    function getBonusStatusText(status) {
        const statusMap = {
            'pending': 'Pending',
            'approved': 'Disetujui',
            'rejected': 'Ditolak',
            'completed': 'Selesai'
        };
        return statusMap[status] || status || 'Tidak diketahui';
    }

    function getBonusStatusBadgeClass(status) {
        const classMap = {
            'pending': 'bg-yellow-100 text-yellow-800 border-yellow-200',
            'approved': 'bg-green-100 text-green-800 border-green-200',
            'rejected': 'bg-red-100 text-red-800 border-red-200',
            'completed': 'bg-green-100 text-green-800 border-green-200'
        };
        return classMap[status] || 'bg-gray-100 text-gray-800';
    }

    // Function untuk modal detail bonus
    function openBonusDetailModal(bonusId) {
        try {
            console.log(`Opening bonus detail modal for ID: ${bonusId}`);
            
            const bonus = bonusCache.find(b => b.id == bonusId);
            if (!bonus) {
                showAlert('error', 'Detail bonus tidak ditemukan');
                return;
            }

            // Fill modal with bonus data
            document.getElementById('detailMember').textContent = bonus.member?.name || 'Guest';
            document.getElementById('detailDateTime').textContent = formatDateTime(bonus.created_at);
            document.getElementById('detailCashier').textContent = bonus.cashier?.name || 'Kasir';
            document.getElementById('detailType').textContent = getBonusTypeText(bonus.type);
            document.getElementById('detailStatus').textContent = getBonusStatusText(bonus.status);
            
            // Calculate total value
            let totalValue = 0;
            if (bonus.bonus_items && Array.isArray(bonus.bonus_items)) {
                bonus.bonus_items.forEach(item => {
                    totalValue += (item.quantity || 0) * (item.product?.price || 0);
                });
            }
            
            document.getElementById('detailTotalValue').textContent = formatCurrency(totalValue);
            document.getElementById('detailTotalValueMain').textContent = formatCurrency(totalValue);

            // Fill bonus items
            const bonusItemsEl = document.getElementById('detailBonusItems');
            bonusItemsEl.innerHTML = '';
            
            if (bonus.bonus_items && bonus.bonus_items.length > 0) {
                bonus.bonus_items.forEach(item => {
                    const itemElement = document.createElement('div');
                    itemElement.className = 'border-b py-2';
                    itemElement.innerHTML = `
                        <div class="flex justify-between">
                            <div>
                                <p class="font-medium">${item.product?.name || 'Produk'}</p>
                                <p class="text-sm text-gray-500">${item.quantity || 0} × ${formatCurrency(item.product?.price || 0)}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-medium">${formatCurrency((item.quantity || 0) * (item.product?.price || 0))}</p>
                                <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded">BONUS</span>
                            </div>
                        </div>
                    `;
                    bonusItemsEl.appendChild(itemElement);
                });
            } else {
                bonusItemsEl.innerHTML = '<p class="text-gray-500 py-4">Tidak ada item bonus</p>';
            }

            // Fill bonus rule and description
            document.getElementById('detailBonusRule').textContent = bonus.bonus_rule?.name || 'Manual Bonus';
            document.getElementById('detailDescription').textContent = bonus.description || '-';

            // Show modal
            const modal = document.getElementById('modalDetail');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            
            if (window.lucide) {
                window.lucide.createIcons();
            }

        } catch (error) {
            console.error('Error in openBonusDetailModal:', error);
            showAlert('error', 'Gagal memuat detail bonus: ' + error.message);
        }
    }

    // Placeholder functions for bonus approval/rejection (to be implemented if needed)
    function openApproveBonusModal(bonusId) {
        // Implementation for approving bonus
        console.log('Approve bonus:', bonusId);
        // You can implement this similar to order approval
    }

    function openRejectBonusModal(bonusId) {
        // Implementation for rejecting bonus
        console.log('Reject bonus:', bonusId);
        // You can implement this similar to order rejection
    }
</script>

<style>
    /* Animasi untuk alert */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @keyframes fadeOut {
        from {
            opacity: 1;
            transform: translateY(0);
        }
        to {
            opacity: 0;
            transform: translateY(10px);
        }
    }
    
    .animate-fade-in-up {
        animation: fadeInUp 0.3s ease-out forwards;
    }
    
    .animate-fade-out {
        animation: fadeOut 0.3s ease-out forwards;
    }
</style>

@endsection