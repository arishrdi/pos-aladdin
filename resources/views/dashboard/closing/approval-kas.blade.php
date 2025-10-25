@extends('layouts.app')

@section('title', 'Approval & Riwayat Kas')

@section('content')

<!-- Page Title -->
<div class="mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <h1 class="text-3xl font-bold text-gray-800">Approval & Riwayat Kas</h1>
    </div>
</div>

<!-- Card: Outlet Info -->
<div class="bg-white rounded-md p-4 shadow-md mb-4 flex flex-col md:flex-row md:items-center md:justify-between">
    <div class="mb-3 md:mb-0 flex items-start gap-2">
        <i data-lucide="store" class="w-5 h-5 text-gray-600"></i>
        <div>
            <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2 outlet-name">Outlet Aktif:</h2>
            <p class="text-sm text-gray-600 outlet-name"">Approval dan riwayat transaksi kas untuk outlet .</p>
        </div>
    </div>
</div>

<!-- Card: Saldo Kas -->
<div class="bg-white rounded-lg shadow p-4 mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4">
        <h3 class="text-2xl font-bold text-blue-600">
            <i data-lucide="wallet" class="w-6 h-6 inline mr-2"></i>
            Saldo Kas Hari Ini
        </h3>
        <div class="flex items-center gap-2 mt-2 sm:mt-0">
            <span class="text-xs text-gray-500" id="balanceLastUpdate">Terakhir diperbarui: -</span>
            <button onclick="refreshBalanceInfo()" class="px-3 py-1 bg-blue-100 text-blue-600 rounded hover:bg-blue-200 transition-colors">
                <i data-lucide="refresh-cw" class="w-4 h-4"></i>
            </button>
        </div>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Saldo Awal -->
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex items-center justify-between mb-2">
                <h4 class="font-semibold text-green-800">Saldo Awal</h4>
                <i data-lucide="sunrise" class="w-5 h-5 text-green-600"></i>
            </div>
            <p class="text-2xl font-bold text-green-700" id="openingBalance">Rp 0</p>
            <p class="text-xs text-green-600 mt-1">Mulai hari</p>
        </div>

        <!-- Saldo Saat Ini -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-center justify-between mb-2">
                <h4 class="font-semibold text-blue-800">Saldo Saat Ini</h4>
                <i data-lucide="dollar-sign" class="w-5 h-5 text-blue-600"></i>
            </div>
            <p class="text-2xl font-bold text-blue-700" id="currentBalance">Rp 0</p>
            <div class="flex items-center mt-1">
                <span class="text-xs" id="balanceChangeText">Tidak berubah</span>
                <i data-lucide="trending-up" class="w-3 h-3 ml-1 text-green-500 hidden" id="balanceUpIcon"></i>
                <i data-lucide="trending-down" class="w-3 h-3 ml-1 text-red-500 hidden" id="balanceDownIcon"></i>
            </div>
        </div>

        <!-- Perubahan Hari Ini -->
        <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
            <div class="flex items-center justify-between mb-2">
                <h4 class="font-semibold text-orange-800">Perubahan Hari Ini</h4>
                <i data-lucide="activity" class="w-5 h-5 text-orange-600"></i>
            </div>
            <p class="text-2xl font-bold text-orange-700" id="netChange">Rp 0</p>
            <p class="text-xs text-orange-600 mt-1" id="transactionCount">0 transaksi</p>
        </div>
    </div>

    <!-- Detail Breakdown -->
    <div class="mt-4 pt-4 border-t border-gray-200">
        <h5 class="font-semibold text-gray-700 mb-2">Rincian Kas Hari Ini:</h5>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-sm">
            <div class="flex justify-between">
                <span class="text-gray-600">Penjualan Tunai:</span>
                <span class="font-semibold text-green-600" id="salesCash">Rp 0</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Tambah Kas:</span>
                <span class="font-semibold text-blue-600" id="manualAdditions">Rp 0</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Kurang Kas:</span>
                <span class="font-semibold text-red-600" id="manualSubtractions">Rp 0</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Refund:</span>
                <span class="font-semibold text-orange-600" id="refunds">Rp 0</span>
            </div>
        </div>
    </div>
</div>

<!-- Card: Permintaan Kas Menunggu Approval -->
<div class="bg-white rounded-lg shadow p-4 mb-6" id="pendingRequestsCard">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4">
        <h3 class="text-2xl font-bold text-orange-600">
            <i data-lucide="clock" class="w-6 h-6 inline mr-2"></i>
            Permintaan Kas Menunggu Approval
        </h3>
        <div class="flex items-center gap-2 mt-2 sm:mt-0">
            <span id="pendingCount" class="px-3 py-1 bg-orange-100 text-orange-700 rounded-full text-sm font-semibold">0</span>
            <button onclick="openAdminCashModal('add')" class="px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600 transition-colors">
                <i data-lucide="plus" class="w-4 h-4 mr-1"></i>
                Tambah Kas
            </button>
            <button onclick="openAdminCashModal('subtract')" class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition-colors">
                <i data-lucide="minus" class="w-4 h-4 mr-1"></i>
                Kurang Kas
            </button>
            <button onclick="refreshPendingRequests()" class="px-3 py-1 bg-gray-100 text-gray-600 rounded hover:bg-gray-200 transition-colors">
                <i data-lucide="refresh-cw" class="w-4 h-4"></i>
            </button>
        </div>
    </div>

    <!-- Table Permintaan Pending -->
    <div class="overflow-x-auto">
        <table class="w-full text-base">
            <thead class="text-left text-gray-700 border-b-2">
                <tr>
                    <th class="py-3 font-bold">Kasir</th>
                    <th class="py-3 font-bold">Jenis</th>
                    <th class="py-3 font-bold">Jumlah</th>
                    <th class="py-3 font-bold">Alasan</th>
                    <th class="py-3 font-bold">Waktu</th>
                    <th class="py-3 font-bold">Bukti</th>
                    <th class="py-3 font-bold">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 divide-y" id="pending-requests-table">
                <!-- Data akan dimasukkan lewat JavaScript -->
                <tr class="border-b hover:bg-gray-50">
                    <td colspan="7" class="py-8 text-center">
                        <div class="inline-flex flex-col items-center justify-center gap-2 w-full">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                                 class="animate-spin text-orange-500">
                                <path d="M21 12a9 9 0 1 1-6.219-8.56"/>
                            </svg>
                            <span class="text-gray-500">Memuat permintaan...</span>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Card: Riwayat Permintaan Kas -->
<div class="bg-white rounded-lg shadow p-4 mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4">
        <h3 class="text-2xl font-bold text-gray-800">
            <i data-lucide="history" class="w-6 h-6 inline mr-2"></i>
            Riwayat Permintaan Kas
        </h3>
        <div class="flex items-center gap-2 mt-2 sm:mt-0">
            <select id="statusFilter" class="px-3 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Semua Status</option>
                <option value="approved">Disetujui</option>
                <option value="rejected">Ditolak</option>
            </select>
            <button onclick="refreshCashRequestHistory()" class="px-3 py-1 bg-gray-100 text-gray-600 rounded hover:bg-gray-200 transition-colors">
                <i data-lucide="refresh-cw" class="w-4 h-4"></i>
            </button>
        </div>
    </div>

    <!-- Table Riwayat Permintaan -->
    <div class="overflow-x-auto">
        <table class="w-full text-base">
            <thead class="text-left text-gray-700 border-b-2">
                <tr>
                    <th class="py-3 font-bold">Kasir</th>
                    <th class="py-3 font-bold">Jenis</th>
                    <th class="py-3 font-bold">Jumlah</th>
                    <th class="py-3 font-bold">Status</th>
                    <th class="py-3 font-bold">Diproses</th>
                    <th class="py-3 font-bold">Waktu</th>
                    <th class="py-3 font-bold">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 divide-y" id="cash-request-history-table">
                <tr class="border-b hover:bg-gray-50">
                    <td colspan="7" class="py-8 text-center">
                        <div class="inline-flex flex-col items-center justify-center gap-2 w-full">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                                 class="animate-spin text-blue-500">
                                <path d="M21 12a9 9 0 1 1-6.219-8.56"/>
                            </svg>
                            <span class="text-gray-500">Memuat riwayat...</span>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Card: Tabel Riwayat Kas -->
<div class="bg-white rounded-lg shadow p-4">
    <!-- Header Card + Filter Tanggal -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4">
        <div class="flex flex-col">
            <h3 class="text-2xl font-bold text-gray-800">Riwayat Transaksi Kas</h3>
            <div id="balanceMethodIndicator" class="hidden mt-1 flex items-center gap-2">
                {{-- <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold flex items-center gap-1">
                    <i data-lucide="check-circle" class="w-3 h-3"></i>
                    Saldo Komprehensif
                </span> --}}
                {{-- <span class="text-xs text-gray-500">Termasuk POS, refund, dan manual kas</span> --}}
            </div>
            <div id="balanceFallbackIndicator" class="hidden mt-1 flex items-center gap-2">
                <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-semibold flex items-center gap-1">
                    <i data-lucide="alert-triangle" class="w-3 h-3"></i>
                    Manual Kas Saja
                </span>
                <span class="text-xs text-gray-500">Hanya menghitung transaksi manual</span>
            </div>
        </div>
        <div class="relative mt-2 sm:mt-0">
            <input id="cashDateInput" type="text"
                class="w-full sm:w-56 pl-10 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                placeholder="Pilih Tanggal" />
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <i data-lucide="calendar" class="w-4 h-4 text-gray-500"></i>
            </span>
        </div>
    </div>

    <!-- Informational Banner -->
    <div id="balanceInfoBanner" class="hidden mb-4 p-4 rounded-lg border-l-4">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <i data-lucide="info" class="w-5 h-5 mt-0.5"></i>
            </div>
            <div class="ml-3 flex-1">
                <div class="text-sm font-medium" id="bannerTitle"></div>
                <div class="text-sm mt-1" id="bannerDescription"></div>
            </div>
            <div class="ml-4 flex-shrink-0">
                <button onclick="closeBanner()" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
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
                    <th class="py-3 font-bold relative group">
                        Saldo Awal
                        <i data-lucide="info" class="w-3 h-3 inline ml-1 text-gray-400 cursor-help"></i>
                        <div class="absolute bottom-full left-0 mb-2 hidden group-hover:block bg-gray-800 text-white text-xs rounded px-2 py-1 whitespace-nowrap z-10">
                            Saldo kas sebelum transaksi ini
                        </div>
                    </th>
                    <th class="py-3 font-bold">Total</th>
                    <th class="py-3 font-bold relative group">
                        Saldo Akhir
                        <i data-lucide="info" class="w-3 h-3 inline ml-1 text-gray-400 cursor-help"></i>
                        <div class="absolute bottom-full right-0 mb-2 hidden group-hover:block bg-gray-800 text-white text-xs rounded px-2 py-1 whitespace-nowrap z-10">
                            Saldo kas setelah transaksi ini (termasuk dampak POS & refund)
                        </div>
                    </th>
                </tr>
            </thead>
            <tbody class="text-gray-700 divide-y" id="cash-history-table">
                <!-- Data akan dimasukkan lewat JavaScript -->
                <tr class="border-b hover:bg-gray-50">
                    <td colspan="7" class="py-8 text-center">
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

<!-- Modal Approve Request -->
<div id="approveModal" class="fixed inset-0 z-50 hidden">
    <div class="modal-overlay absolute w-full h-full bg-gray-900 opacity-50"></div>
    <div class="modal-container bg-white w-11/12 md:max-w-md mx-auto rounded-lg shadow-lg z-50 overflow-y-auto relative top-1/2 transform -translate-y-1/2">
        <div class="modal-content py-6 px-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-green-600">
                    <i data-lucide="check-circle" class="w-5 h-5 inline mr-2"></i>
                    Setujui Permintaan
                </h2>
                <button onclick="closeApprovalModal()" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            
            <div id="approveRequestInfo" class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                <!-- Request info will be populated here -->
            </div>
            
            <form onsubmit="submitApproval(event)">
                <input type="hidden" id="approveRequestId">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Admin (Opsional)</label>
                    <textarea id="approveNotes" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" 
                              rows="3" placeholder="Masukkan catatan jika diperlukan..."></textarea>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeApprovalModal()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600">
                        <i data-lucide="check" class="w-4 h-4 inline mr-1"></i>
                        Setujui
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Reject Request -->
<div id="rejectModal" class="fixed inset-0 z-50 hidden">
    <div class="modal-overlay absolute w-full h-full bg-gray-900 opacity-50"></div>
    <div class="modal-container bg-white w-11/12 md:max-w-md mx-auto rounded-lg shadow-lg z-50 overflow-y-auto relative top-1/2 transform -translate-y-1/2">
        <div class="modal-content py-6 px-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-red-600">
                    <i data-lucide="x-circle" class="w-5 h-5 inline mr-2"></i>
                    Tolak Permintaan
                </h2>
                <button onclick="closeRejectModal()" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            
            <div id="rejectRequestInfo" class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                <!-- Request info will be populated here -->
            </div>
            
            <form onsubmit="submitRejection(event)">
                <input type="hidden" id="rejectRequestId">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Alasan Penolakan <span class="text-red-500">*</span></label>
                    <textarea id="rejectNotes" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500" 
                              rows="3" placeholder="Masukkan alasan penolakan..." required></textarea>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeRejectModal()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
                        <i data-lucide="x" class="w-4 h-4 inline mr-1"></i>
                        Tolak
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Admin Direct Cash Operations -->
<div id="adminCashModal" class="fixed inset-0 z-50 hidden">
    <div class="modal-overlay absolute w-full h-full bg-gray-900 opacity-50"></div>
    <div class="modal-container bg-white w-11/12 md:max-w-md mx-auto rounded-lg shadow-lg z-50 overflow-y-auto relative top-1/2 transform -translate-y-1/2">
        <div class="modal-content py-6 px-6">
            <div class="flex justify-between items-center mb-4">
                <h2 id="adminCashModalTitle" class="text-lg font-semibold">
                    <i id="adminCashModalIcon" data-lucide="plus" class="w-5 h-5 inline mr-2"></i>
                    Tambah Kas (Admin)
                </h2>
                <button onclick="closeAdminCashModal()" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            
            <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="flex items-center gap-2">
                    <i data-lucide="shield-check" class="w-4 h-4 text-blue-600"></i>
                    <span class="text-sm font-medium text-blue-800">Mode Admin</span>
                </div>
                <p class="text-xs text-blue-600 mt-1">Transaksi akan diproses langsung tanpa perlu approval</p>
            </div>
            
            <form onsubmit="submitAdminCash(event)">
                <input type="hidden" id="adminCashType">
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah *</label>
                    <input type="text" id="adminCashAmount" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                           placeholder="Masukkan jumlah" required>
                    <input type="hidden" id="adminCashAmountRaw">
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Alasan</label>
                    <textarea id="adminCashReason" rows="3" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                              placeholder="Jelaskan alasan transaksi kas..."></textarea>
                </div>
                
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeAdminCashModal()" 
                            class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="submit" id="adminCashSubmitBtn" 
                            class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                        <i data-lucide="check" class="w-4 h-4 inline mr-1"></i>
                        Proses Langsung
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal View Proof Files -->
<div id="proofModal" class="fixed inset-0 z-50 hidden">
    <div class="modal-overlay absolute w-full h-full bg-gray-900 opacity-50"></div>
    <div class="modal-container bg-white w-11/12 md:max-w-2xl mx-auto rounded-lg shadow-lg z-50 overflow-y-auto relative top-1/2 transform -translate-y-1/2">
        <div class="modal-content py-6 px-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-800">
                    <i data-lucide="file-text" class="w-5 h-5 inline mr-2"></i>
                    Bukti Pendukung
                </h2>
                <button onclick="closeProofModal()" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            
            <div id="proofFilesContainer" class="space-y-3">
                <!-- Proof files will be displayed here -->
            </div>
            
            <div class="flex justify-end mt-4">
                <button onclick="closeProofModal()" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.5.0/axios.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/locale/id.min.js"></script>

<script>
// Global variables
let selectedDate = null;

// Global functions (accessible by onclick handlers)
function getSelectedOutletId() {
    const urlParams = new URLSearchParams(window.location.search);
    const outletIdFromUrl = urlParams.get('outlet_id');
    
    if (outletIdFromUrl) {
        return outletIdFromUrl;
    }
    
    const savedOutletId = localStorage.getItem('selectedOutletId');
    if (savedOutletId) {
        return savedOutletId;
    }
    
    return 1; // default
}

function refreshBalanceInfo() {
    const outletId = getSelectedOutletId();
    if (window.fetchBalanceInfo) {
        window.fetchBalanceInfo(outletId);
    }
}

function showAlert(type, message) {
    let alertContainer = document.getElementById('alertContainer');
    if (!alertContainer) {
        alertContainer = document.createElement('div');
        alertContainer.id = 'alertContainer';
        alertContainer.className = 'fixed top-5 right-5 z-50 space-y-3';
        document.body.appendChild(alertContainer);
    }

    const alert = document.createElement('div');
    alert.className = `px-4 py-3 rounded-lg shadow-md ${type === 'error' ? 'bg-red-100 text-red-700 border border-red-200' : 
                    type === 'success' ? 'bg-green-100 text-green-700 border border-green-200' : 'bg-blue-100 text-blue-700 border border-blue-200'}`;
    alert.innerHTML = `
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i data-lucide="${type === 'error' ? 'alert-circle' : type === 'success' ? 'check-circle' : 'info'}" class="w-5 h-5"></i>
                <span>${message}</span>
            </div>
            <button onclick="this.parentElement.parentElement.remove()">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
    `;
    alertContainer.appendChild(alert);
    
    if (window.lucide) window.lucide.createIcons({ icons });
    
    setTimeout(() => {
        if (alert.parentNode) alert.remove();
    }, 5000);
}

function refreshPendingRequests() {
    const outletId = getSelectedOutletId();
    fetchPendingRequests(outletId);
}

function refreshCashRequestHistory() {
    const outletId = getSelectedOutletId();
    const status = document.getElementById('statusFilter').value;
    fetchCashRequestHistory(outletId, status);
}

function closeBanner() {
    document.getElementById('balanceInfoBanner').classList.add('hidden');
    sessionStorage.setItem('bannerClosed', 'true');
}

function fetchCashRequestHistory(outletId, status = '') {
    console.log(`Fetching cash request history for outlet ID: ${outletId}, status: ${status}`);
    
    document.getElementById('cash-request-history-table').innerHTML = `
        <tr class="border-b hover:bg-gray-50">
            <td colspan="7" class="py-4 text-center text-gray-500">Memuat riwayat...</td>
        </tr>
    `;

    let params = { outlet_id: outletId };
    if (status) {
        params.status = status;
    }

    axios.get('/api/cash-requests/history', { 
        params,
        headers: {
            'Authorization': `Bearer ${localStorage.getItem('token')}`,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => {
        const data = response.data.data || response.data;
        renderCashRequestHistory(data);
    })
    .catch(error => {
        console.error('Error fetching cash request history:', error);
        document.getElementById('cash-request-history-table').innerHTML = `
            <tr class="border-b hover:bg-gray-50">
                <td colspan="7" class="py-4 text-center text-red-500">Terjadi kesalahan saat memuat riwayat: ${error.response?.data?.message || error.message}</td>
            </tr>
        `;
    });
}

function renderCashRequestHistory(data) {
    const tableBody = document.getElementById('cash-request-history-table');
    tableBody.innerHTML = '';

    if (!data || data.length === 0) {
        tableBody.innerHTML = `
            <tr class="border-b hover:bg-gray-50">
                <td colspan="7" class="py-4 text-center text-gray-500">
                    <i data-lucide="file-text" class="w-8 h-8 mx-auto mb-2 text-gray-500"></i>
                    <div>Tidak ada riwayat permintaan kas</div>
                </td>
            </tr>
        `;
        if (window.lucide) window.lucide.createIcons({ icons });
        return;
    }

    data.forEach(request => {
        const formattedRequestedAt = moment(request.requested_at, 'DD/MM/YYYY HH:mm').format('DD MMM HH:mm');
        const formattedProcessedAt = request.processed_at ? moment(request.processed_at, 'DD/MM/YYYY HH:mm').format('DD MMM HH:mm') : '-';
        const isAdd = request.type === 'add';
        const formattedAmount = new Intl.NumberFormat('id-ID').format(request.amount);
        const hasProofFiles = request.has_proof_files;

        let statusBadge = '';
        let statusColor = '';
        
        switch(request.status) {
            case 'approved':
                statusBadge = '<span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs font-semibold">Disetujui</span>';
                statusColor = 'text-green-600';
                break;
            case 'rejected':
                statusBadge = '<span class="px-2 py-1 bg-red-100 text-red-700 rounded text-xs font-semibold">Ditolak</span>';
                statusColor = 'text-red-600';
                break;
            default:
                statusBadge = '<span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded text-xs font-semibold">Pending</span>';
                statusColor = 'text-yellow-600';
        }

        const row = document.createElement('tr');
        row.className = 'border-b hover:bg-gray-50';
        row.innerHTML = `
            <td class="py-4">
                <div class="font-medium">${request.requester}</div>
                <div class="text-xs text-gray-500">${request.outlet}</div>
            </td>
            <td class="py-4">
                ${isAdd 
                    ? '<span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs font-semibold">Tambah Kas</span>' 
                    : '<span class="px-2 py-1 bg-orange-100 text-orange-700 rounded text-xs font-semibold">Kurang Kas</span>'
                }
            </td>
            <td class="py-4">
                <div class="${statusColor} font-semibold">
                    ${isAdd ? '+' : '-'}Rp ${formattedAmount}
                </div>
            </td>
            <td class="py-4">
                ${statusBadge}
            </td>
            <td class="py-4">
                <div class="text-sm">
                    ${request.processor ? request.processor : '-'}
                </div>
                <div class="text-xs text-gray-500">${formattedProcessedAt}</div>
            </td>
            <td class="py-4">
                <div class="text-xs text-gray-500">${formattedRequestedAt}</div>
            </td>
            <td class="py-4 text-center">
                <div class="flex gap-1 justify-center">
                    ${hasProofFiles 
                        ? `<button onclick="viewProofFiles(${request.id})" class="text-blue-600 hover:text-blue-800" title="Lihat Bukti">
                            <i data-lucide="file-text" class="w-4 h-4"></i>
                           </button>`
                        : ''
                    }
                    ${request.admin_notes 
                        ? `<button onclick="viewAdminNotes('${request.admin_notes}', '${request.status}')" class="text-gray-600 hover:text-gray-800 ml-1" title="Lihat Catatan">
                            <i data-lucide="message-circle" class="w-4 h-4"></i>
                           </button>`
                        : ''
                    }
                </div>
            </td>
        `;
        
        tableBody.appendChild(row);
    });
    
    if (window.lucide) window.lucide.createIcons({ icons });
}

function viewAdminNotes(notes, status) {
    const statusText = status === 'approved' ? 'Persetujuan' : 'Penolakan';
    const statusColor = status === 'approved' ? 'text-green-700' : 'text-red-700';
    
    showAlert('info', `Catatan ${statusText}: ${notes}`);
}

function fetchPendingRequests(outletId) {
    console.log(`Fetching pending cash requests for outlet ID: ${outletId}`);
    
    document.getElementById('pending-requests-table').innerHTML = `
        <tr class="border-b hover:bg-gray-50">
            <td colspan="7" class="py-4 text-center text-gray-500">Memuat permintaan...</td>
        </tr>
    `;

    axios.get('/api/cash-requests/pending', { 
        params: { outlet_id: outletId },
        headers: {
            'Authorization': `Bearer ${localStorage.getItem('token')}`,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => {
        const data = response.data.data;
        renderPendingRequests(data);
        updatePendingCount(data.length);
    })
    .catch(error => {
        console.error('Error fetching pending requests:', error);
        document.getElementById('pending-requests-table').innerHTML = `
            <tr class="border-b hover:bg-gray-50">
                <td colspan="7" class="py-4 text-center text-red-500">Terjadi kesalahan saat memuat permintaan: ${error.response?.data?.message || error.message}</td>
            </tr>
        `;
        updatePendingCount(0);
    });
}

function renderPendingRequests(data) {
    const tableBody = document.getElementById('pending-requests-table');
    tableBody.innerHTML = '';

    if (!data || data.length === 0) {
        tableBody.innerHTML = `
            <tr class="border-b hover:bg-gray-50">
                <td colspan="7" class="py-4 text-center text-gray-500">
                    <i data-lucide="check-circle" class="w-8 h-8 mx-auto mb-2 text-green-500"></i>
                    <div>Tidak ada permintaan kas menunggu approval</div>
                </td>
            </tr>
        `;
        if (window.lucide) window.lucide.createIcons({ icons });
        return;
    }

    data.forEach(request => {
        const formattedTime = moment(request.requested_at, 'DD/MM/YYYY HH:mm').format('HH:mm');
        const formattedDate = moment(request.requested_at, 'DD/MM/YYYY HH:mm').format('DD MMM');
        const isAdd = request.type === 'add';
        const formattedAmount = new Intl.NumberFormat('id-ID').format(request.amount);
        const reason = request.reason || '-';
        const hasProofFiles = request.has_proof_files;

        const row = document.createElement('tr');
        row.className = 'border-b hover:bg-gray-50';
        row.innerHTML = `
            <td class="py-4">
                <div class="font-medium">${request.requester}</div>
                <div class="text-xs text-gray-500">${request.outlet}</div>
            </td>
            <td class="py-4">
                ${isAdd 
                    ? '<span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs font-semibold">Tambah Kas</span>' 
                    : '<span class="px-2 py-1 bg-orange-100 text-orange-700 rounded text-xs font-semibold">Kurang Kas</span>'
                }
            </td>
            <td class="py-4">
                <div class="${isAdd ? 'text-green-600' : 'text-orange-600'} font-semibold">
                    ${isAdd ? '+' : '-'}Rp ${formattedAmount}
                </div>
            </td>
            <td class="py-4 max-w-xs">
                <div class="text-sm text-gray-600 truncate" title="${reason}">${reason}</div>
            </td>
            <td class="py-4">
                <div>${formattedTime}</div>
                <div class="text-xs text-gray-500">${formattedDate}</div>
            </td>
            <td class="py-4 text-center">
                ${hasProofFiles 
                    ? `<button onclick="viewProofFiles(${request.id})" class="text-blue-600 hover:text-blue-800">
                        <i data-lucide="file-text" class="w-4 h-4"></i>
                       </button>`
                    : '<span class="text-gray-400">-</span>'
                }
            </td>
            <td class="py-4">
                <div class="flex gap-1">
                    <button onclick="approveRequest(${request.id}, '${request.type_text}', ${request.amount}, '${request.requester}')" 
                            class="px-3 py-1 bg-green-500 text-white rounded text-xs hover:bg-green-600 transition-colors">
                        <i data-lucide="check" class="w-3 h-3 inline mr-1"></i>
                        Setujui
                    </button>
                    <button onclick="rejectRequest(${request.id}, '${request.type_text}', ${request.amount}, '${request.requester}')" 
                            class="px-3 py-1 bg-red-500 text-white rounded text-xs hover:bg-red-600 transition-colors">
                        <i data-lucide="x" class="w-3 h-3 inline mr-1"></i>
                        Tolak
                    </button>
                </div>
            </td>
        `;
        
        tableBody.appendChild(row);
    });
    
    if (window.lucide) window.lucide.createIcons({ icons });
}

function updatePendingCount(count) {
    document.getElementById('pendingCount').textContent = count;
}

function approveRequest(id, typeText, amount, requesterName) {
    document.getElementById('approveRequestId').value = id;
    document.getElementById('approveRequestInfo').innerHTML = `
        <div class="flex items-center justify-between">
            <div>
                <div class="font-semibold text-green-700">${typeText}</div>
                <div class="text-sm text-gray-600">Kasir: ${requesterName}</div>
            </div>
            <div class="text-lg font-bold text-green-700">
                Rp ${new Intl.NumberFormat('id-ID').format(amount)}
            </div>
        </div>
    `;
    document.getElementById('approveNotes').value = '';
    document.getElementById('approveModal').classList.remove('hidden');
    if (window.lucide) window.lucide.createIcons({ icons });
}

function rejectRequest(id, typeText, amount, requesterName) {
    document.getElementById('rejectRequestId').value = id;
    document.getElementById('rejectRequestInfo').innerHTML = `
        <div class="flex items-center justify-between">
            <div>
                <div class="font-semibold text-red-700">${typeText}</div>
                <div class="text-sm text-gray-600">Kasir: ${requesterName}</div>
            </div>
            <div class="text-lg font-bold text-red-700">
                Rp ${new Intl.NumberFormat('id-ID').format(amount)}
            </div>
        </div>
    `;
    document.getElementById('rejectNotes').value = '';
    document.getElementById('rejectModal').classList.remove('hidden');
    if (window.lucide) window.lucide.createIcons({ icons });
}

function viewProofFiles(requestId) {
    // Try to find from both pending and history data
    Promise.all([
        axios.get(`/api/cash-requests/pending?outlet_id=${getSelectedOutletId()}`, {
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('token')}`,
                'Accept': 'application/json'
            }
        }).catch(() => ({ data: { data: [] } })),
        axios.get(`/api/cash-requests/history?outlet_id=${getSelectedOutletId()}`, {
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('token')}`,
                'Accept': 'application/json'
            }
        }).catch(() => ({ data: { data: [] } }))
    ])
    .then(responses => {
        const pendingData = responses[0].data.data || [];
        const historyData = responses[1].data.data || [];
        const allRequests = [...pendingData, ...historyData];
        
        const request = allRequests.find(r => r.id === requestId);
        if (request && request.proof_files_urls) {
            displayProofFiles(request.proof_files_urls);
        } else {
            showAlert('error', 'File bukti tidak ditemukan');
        }
    })
    .catch(error => {
        console.error('Error fetching proof files:', error);
        showAlert('error', 'Gagal memuat file bukti');
    });
}

function displayProofFiles(proofUrls) {
    const container = document.getElementById('proofFilesContainer');
    container.innerHTML = '';
    
    if (!proofUrls || proofUrls.length === 0) {
        container.innerHTML = '<p class="text-gray-500 text-center">Tidak ada file bukti</p>';
    } else {
        proofUrls.forEach((url, index) => {
            const fileDiv = document.createElement('div');
            fileDiv.className = 'border border-gray-200 rounded-lg p-4';
            
            if (url.match(/\.(jpg|jpeg|png|gif)$/i)) {
                fileDiv.innerHTML = `
                    <div class="text-sm text-gray-600 mb-2">Bukti ${index + 1} (Gambar)</div>
                    <img src="${url}" alt="Bukti ${index + 1}" class="max-w-full h-auto rounded">
                `;
            } else {
                fileDiv.innerHTML = `
                    <div class="text-sm text-gray-600 mb-2">Bukti ${index + 1} (PDF)</div>
                    <a href="${url}" target="_blank" class="text-blue-600 hover:text-blue-800 flex items-center">
                        <i data-lucide="external-link" class="w-4 h-4 mr-1"></i>
                        Lihat File
                    </a>
                `;
            }
            
            container.appendChild(fileDiv);
        });
    }
    
    document.getElementById('proofModal').classList.remove('hidden');
    if (window.lucide) window.lucide.createIcons({ icons });
}

function closeApprovalModal() {
    document.getElementById('approveModal').classList.add('hidden');
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
}

function closeProofModal() {
    document.getElementById('proofModal').classList.add('hidden');
}

function submitApproval(event) {
    event.preventDefault();
    
    const requestId = document.getElementById('approveRequestId').value;
    const notes = document.getElementById('approveNotes').value;
    
    if (!requestId) {
        showAlert('error', 'ID permintaan tidak valid');
        return;
    }
    
    const submitBtn = event.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i data-lucide="loader" class="w-4 h-4 inline mr-1 animate-spin"></i>Memproses...';
    submitBtn.disabled = true;
    
    axios.post(`/api/cash-requests/approve/${requestId}`, {
        admin_notes: notes
    }, {
        headers: {
            'Authorization': `Bearer ${localStorage.getItem('token')}`,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => {
        if (response.data.success) {
            showAlert('success', 'Permintaan berhasil disetujui');
            closeApprovalModal();
            const outletId = getSelectedOutletId();
            fetchPendingRequests(outletId);
            if (window.fetchCashHistory) {
                window.fetchCashHistory(outletId, selectedDate);
            }
            fetchCashRequestHistory(outletId);
            fetchBalanceInfo(outletId);
        } else {
            throw new Error(response.data.message || 'Gagal menyetujui permintaan');
        }
    })
    .catch(error => {
        console.error('Error approving request:', error);
        showAlert('error', error.response?.data?.message || error.message || 'Terjadi kesalahan saat menyetujui permintaan');
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
        if (window.lucide) window.lucide.createIcons({ icons });
    });
}

function submitRejection(event) {
    event.preventDefault();
    
    const requestId = document.getElementById('rejectRequestId').value;
    const notes = document.getElementById('rejectNotes').value;
    
    if (!requestId) {
        showAlert('error', 'ID permintaan tidak valid');
        return;
    }
    
    if (!notes.trim()) {
        showAlert('error', 'Alasan penolakan harus diisi');
        return;
    }
    
    const submitBtn = event.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i data-lucide="loader" class="w-4 h-4 inline mr-1 animate-spin"></i>Memproses...';
    submitBtn.disabled = true;
    
    axios.post(`/api/cash-requests/reject/${requestId}`, {
        admin_notes: notes
    }, {
        headers: {
            'Authorization': `Bearer ${localStorage.getItem('token')}`,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => {
        if (response.data.success) {
            showAlert('success', 'Permintaan berhasil ditolak');
            closeRejectModal();
            const outletId = getSelectedOutletId();
            fetchPendingRequests(outletId);
            fetchCashRequestHistory(outletId);
        } else {
            throw new Error(response.data.message || 'Gagal menolak permintaan');
        }
    })
    .catch(error => {
        console.error('Error rejecting request:', error);
        showAlert('error', error.response?.data?.message || error.message || 'Terjadi kesalahan saat menolak permintaan');
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
        if (window.lucide) window.lucide.createIcons({ icons });
    });
}

// Cash History Functions (existing code)
document.addEventListener('DOMContentLoaded', function() {
    const token = localStorage.getItem('token');
    if (!token) {
        console.error('Token not found. User might need to login again.');
        document.getElementById('cash-history-table').innerHTML = `
            <tr class="border-b hover:bg-gray-50">
                <td colspan="7" class="py-4 text-center text-red-500">Sesi login telah berakhir. Silakan login kembali.</td>
            </tr>
        `;
        return;
    }

    // Initialize datepicker
    flatpickr("#cashDateInput", {
        dateFormat: "Y-m-d",
        defaultDate: "today",
        onChange: function(selectedDates, dateStr) {
            selectedDate = dateStr;
            const outletId = getSelectedOutletId();
            fetchCashHistory(outletId, dateStr);
        },
        onReady: function() {
            const today = new Date();
            const year = today.getFullYear();
            const month = String(today.getMonth() + 1).padStart(2, '0');
            const day = String(today.getDate()).padStart(2, '0');
            const dateStr = `${year}-${month}-${day}`;
            
            document.getElementById('cashDateInput').value = dateStr;
            selectedDate = dateStr;
        },
        locale: {
            firstDayOfWeek: 1
        }
    });

    function fetchBalanceInfo(outletId) {
        console.log(`Fetching balance info for outlet ID: ${outletId}`);
        
        // Update loading state
        document.getElementById('openingBalance').textContent = 'Memuat...';
        document.getElementById('currentBalance').textContent = 'Memuat...';
        document.getElementById('netChange').textContent = 'Memuat...';
        
        // Fetch current balance
        axios.get('/api/cash-reports/current-balance', {
            params: { outlet_id: outletId },
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('token')}`,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            const data = response.data.data;
            updateBalanceDisplay(data);
        })
        .catch(error => {
            console.error('Error fetching current balance:', error);
            showBalanceError('Gagal memuat saldo saat ini');
        });
        
        // Fetch dashboard summary for detailed breakdown
        axios.get('/api/cash-reports/dashboard-summary', {
            params: { outlet_id: outletId },
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('token')}`,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            const data = response.data.data;
            updateBalanceBreakdown(data);
        })
        .catch(error => {
            console.error('Error fetching dashboard summary:', error);
        });
    }
    
    function updateBalanceDisplay(data) {
        // Format currency
        const formatCurrency = (amount) => 'Rp ' + new Intl.NumberFormat('id-ID').format(amount || 0);
        
        // Update main balance cards
        document.getElementById('openingBalance').textContent = formatCurrency(data.opening_balance);
        document.getElementById('currentBalance').textContent = formatCurrency(data.current_balance);
        document.getElementById('netChange').textContent = formatCurrency(data.net_change);
        
        // Update change indicators
        const changePercentage = data.net_change_percentage || 0;
        const balanceChangeText = document.getElementById('balanceChangeText');
        const balanceUpIcon = document.getElementById('balanceUpIcon');
        const balanceDownIcon = document.getElementById('balanceDownIcon');
        
        // Reset icons
        balanceUpIcon.classList.add('hidden');
        balanceDownIcon.classList.add('hidden');
        
        if (changePercentage > 0) {
            balanceChangeText.textContent = `+${changePercentage.toFixed(1)}%`;
            balanceChangeText.className = 'text-xs text-green-600';
            balanceUpIcon.classList.remove('hidden');
        } else if (changePercentage < 0) {
            balanceChangeText.textContent = `${changePercentage.toFixed(1)}%`;
            balanceChangeText.className = 'text-xs text-red-600';
            balanceDownIcon.classList.remove('hidden');
        } else {
            balanceChangeText.textContent = 'Tidak berubah';
            balanceChangeText.className = 'text-xs text-gray-500';
        }
        
        // Update last update time
        const now = new Date();
        const timeString = now.toLocaleString('id-ID', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit'
        });
        document.getElementById('balanceLastUpdate').textContent = `Terakhir diperbarui: ${timeString}`;
    }
    
    function updateBalanceBreakdown(data) {
        const formatCurrency = (amount) => 'Rp ' + new Intl.NumberFormat('id-ID').format(amount || 0);
        
        const today = data.today || {};
        
        // Update breakdown details
        document.getElementById('salesCash').textContent = formatCurrency(today.total_sales_cash);
        document.getElementById('manualAdditions').textContent = formatCurrency(today.manual_additions);
        document.getElementById('manualSubtractions').textContent = formatCurrency(today.manual_subtractions);
        document.getElementById('refunds').textContent = formatCurrency(today.refunds);
        
        // Update transaction count
        const transactionCount = today.transactions_count || 0;
        document.getElementById('transactionCount').textContent = `${transactionCount} transaksi`;
    }
    
    function showBalanceError(message) {
        document.getElementById('openingBalance').textContent = 'Error';
        document.getElementById('currentBalance').textContent = 'Error';
        document.getElementById('netChange').textContent = 'Error';
        showAlert('error', message);
    }

    function fetchCashHistory(outletId, date = null) {
        console.log(`Fetching cash history for outlet ID: ${outletId} on date: ${date}`);
        
        document.getElementById('cash-history-table').innerHTML = `
            <tr class="border-b hover:bg-gray-50">
                <td colspan="7" class="py-4 text-center text-gray-500">Memuat data...</td>
            </tr>
        `;

        let params = {
            outlet_id: outletId
        };

        if (date) {
            params.date = date;
        }

        axios.get('/api/cash-register-transactions', { 
            params,
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('token')}`,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            const responseData = response.data.data;
            
            // Handle new API response structure
            let transactions = [];
            let openingBalance = 0;
            let comprehensiveData = null;
            
            if (responseData.transactions) {
                // New structure with balance info
                transactions = responseData.transactions;
                openingBalance = responseData.opening_balance || 0;
                comprehensiveData = responseData.comprehensive_balance_data;
            } else {
                // Fallback for old structure
                transactions = responseData;
            }
            
            if (transactions.length > 0 && transactions[0].outlet) {
                updateOutletInfo(transactions[0].outlet);
            } else {
                updateOutletInfoFromSelection();
            }
            
            renderCashHistory(transactions, openingBalance, comprehensiveData);
        })
        .catch(error => {
            console.error('Error fetching cash history:', error);
            if (error.response && error.response.status === 401) {
                localStorage.removeItem('token');
                document.getElementById('cash-history-table').innerHTML = `
                    <tr class="border-b hover:bg-gray-50">
                        <td colspan="7" class="py-4 text-center text-red-500">Sesi login telah berakhir. Silakan login kembali.</td>
                    </tr>
                `;
            } else {
                document.getElementById('cash-history-table').innerHTML = `
                    <tr class="border-b hover:bg-gray-50">
                        <td colspan="7" class="py-4 text-center text-red-500">Terjadi kesalahan saat memuat data: ${error.response?.data?.message || error.message}</td>
                    </tr>
                `;
            }
            
            updateOutletInfoFromSelection();
        });
    }

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

    function renderCashHistory(data, openingBalance = 0, comprehensiveData = null) {
        const tableBody = document.getElementById('cash-history-table');
        tableBody.innerHTML = '';

        if (!data || data.length === 0) {
            tableBody.innerHTML = `
                <tr class="border-b hover:bg-gray-50">
                    <td colspan="7" class="py-4 text-center text-gray-500">Tidak ada data transaksi kas untuk ditampilkan</td>
                </tr>
            `;
            return;
        }

        // Calculate comprehensive running balance untuk setiap transaksi
        let useComprehensiveBalance = false;
        let comprehensiveRunningBalance = parseFloat(openingBalance) || 0;
        
        // Check if we have comprehensive data
        if (comprehensiveData && comprehensiveData.daily_totals) {
            useComprehensiveBalance = true;
            
            // Use comprehensive opening balance
            comprehensiveRunningBalance = parseFloat(comprehensiveData.opening_balance) || 0;
            
            console.log('Using comprehensive balance calculation:', {
                opening_balance: comprehensiveRunningBalance,
                daily_totals: comprehensiveData.daily_totals
            });
        }
        
        // Sortir transaksi berdasarkan waktu untuk memastikan urutan yang benar
        const sortedData = [...data].sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
        
        // Get all transaction types untuk comprehensive calculation
        let allTransactions = [];
        
        if (useComprehensiveBalance && comprehensiveData.transactions_breakdown) {
            // Combine all transaction types dari comprehensive data
            const breakdown = comprehensiveData.transactions_breakdown;
            allTransactions = [
                ...(breakdown.manual_cash || []),
                ...(breakdown.admin_direct || []),
                ...(breakdown.pos_sales || []),
                ...(breakdown.refunds || []),
                ...(breakdown.other || [])
            ];
            
            // Sort all transactions by created_at
            allTransactions.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
            
            console.log('All comprehensive transactions:', allTransactions.length);
        }
        
        // Render transactions dalam urutan kronologis
        const displayData = [...sortedData].reverse(); // Reverse untuk tampilkan yang terbaru dulu
        const balanceHistory = [];
        
        if (useComprehensiveBalance) {
            // Calculate comprehensive running balance including all transaction types
            let runningBalance = comprehensiveRunningBalance;
            
            // Process all transactions chronologically untuk accurate running balance
            if (allTransactions.length > 0) {
                allTransactions.forEach((transaction, index) => {
                    const previousBalance = runningBalance;
                    
                    // Calculate balance change berdasarkan transaction type dan source
                    let balanceChange = 0;
                    const amount = parseFloat(transaction.amount) || 0;
                    
                    if (transaction.source === 'pos') {
                        // POS sales always add to cash
                        balanceChange = amount;
                    } else if (transaction.source === 'refund') {
                        // Refunds subtract from cash
                        balanceChange = -amount;
                    } else if (transaction.source === 'cash' || transaction.source === 'admin_direct') {
                        // Manual cash operations (including admin direct transactions)
                        balanceChange = transaction.type === 'add' ? amount : -amount;
                    } else {
                        // Other transactions
                        balanceChange = transaction.type === 'add' ? amount : -amount;
                    }
                    
                    runningBalance += balanceChange;
                    
                    // Only store balance untuk manual cash transactions yang akan ditampilkan
                    if (transaction.source === 'cash' || transaction.source === 'admin_direct') {
                        balanceHistory.push({
                            id: transaction.id,
                            beforeBalance: previousBalance,
                            afterBalance: runningBalance
                        });
                    }
                });
            }
            
            // Even if no comprehensive transactions, use comprehensive opening balance 
            // and calculate based on manual cash transactions
            if (balanceHistory.length === 0 && sortedData.length > 0) {
                sortedData.forEach(transaction => {
                    const isAdd = transaction.type === 'add';
                    const amount = parseFloat(transaction.amount);
                    const previousBalance = runningBalance;
                    runningBalance += isAdd ? amount : -amount;
                    
                    balanceHistory.push({
                        id: transaction.id,
                        beforeBalance: previousBalance,
                        afterBalance: runningBalance
                    });
                });
            }
        } else {
            // Fallback to old calculation (manual cash only)
            let runningBalance = parseFloat(openingBalance) || 0;
            sortedData.forEach(transaction => {
                const isAdd = transaction.type === 'add';
                const amount = parseFloat(transaction.amount);
                const previousBalance = runningBalance;
                runningBalance += isAdd ? amount : -amount;
                
                balanceHistory.push({
                    id: transaction.id,
                    beforeBalance: previousBalance,
                    afterBalance: runningBalance
                });
            });
        }
        
        // Render dalam urutan terbalik (terbaru dulu)
        displayData.forEach(transaction => {
            const formattedTime = moment(transaction.created_at).format('HH:mm:ss');
            const formattedDate = moment(transaction.created_at).format('DD MMM YYYY');
            const isAdd = transaction.type === 'add';
            const formattedAmount = new Intl.NumberFormat('id-ID').format(transaction.amount);
            const userName = transaction.user ? transaction.user.name : 'System';
            const reason = transaction.reason || '-';
            
            // Find balance info untuk transaction ini
            const balanceInfo = balanceHistory.find(b => b.id === transaction.id);
            const beforeBalance = balanceInfo ? balanceInfo.beforeBalance : 0;
            const afterBalance = balanceInfo ? balanceInfo.afterBalance : 0;
            
            const formatCurrency = (amount) => 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);

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
                <td class="text-sm">
                    <div class="text-blue-600 font-medium">${formatCurrency(beforeBalance)}</div>
                    ${useComprehensiveBalance ? '<div class="text-xs text-gray-400">Komprehensif</div>' : '<div class="text-xs text-yellow-600">Manual saja</div>'}
                </td>
                <td class="${isAdd ? 'text-green-600' : 'text-red-600'} font-semibold">
                    ${isAdd ? '+' : '-'}Rp ${formattedAmount}
                </td>
                <td class="text-sm">
                    <div class="text-blue-700 font-semibold">${formatCurrency(afterBalance)}</div>
                    ${useComprehensiveBalance ? '<div class="text-xs text-green-600 flex items-center gap-1"><i data-lucide="check" class="w-3 h-3"></i>Akurat</div>' : '<div class="text-xs text-yellow-600 flex items-center gap-1"><i data-lucide="alert-triangle" class="w-3 h-3"></i>Parsial</div>'}
                </td>
            `;
            
            tableBody.appendChild(row);
        });
        
        // Update icons untuk row content
        if (window.lucide) window.lucide.createIcons({ icons });
        
        // Show visual indicators untuk calculation method
        updateBalanceMethodIndicator(useComprehensiveBalance);
        
        // Log info about calculation method
        if (useComprehensiveBalance) {
            console.log('✅ Using comprehensive balance calculation (includes POS sales, refunds, etc.)');
        } else {
            console.log('⚠️ Using fallback balance calculation (manual cash only)');
        }
    }
    
    function updateBalanceMethodIndicator(isComprehensive) {
        const comprehensiveIndicator = document.getElementById('balanceMethodIndicator');
        const fallbackIndicator = document.getElementById('balanceFallbackIndicator');
        
        if (isComprehensive) {
            // Show comprehensive indicator
            comprehensiveIndicator.classList.remove('hidden');
            fallbackIndicator.classList.add('hidden');
            
            // Update informational banner
            updateInfoBanner(true);
            
            // Show success notification (only once)
            if (!sessionStorage.getItem('comprehensiveBalanceNotified')) {
                setTimeout(() => {
                    showAlert('success', '✅ Saldo konsisten dengan dashboard - termasuk POS dan refund');
                    sessionStorage.setItem('comprehensiveBalanceNotified', 'true');
                }, 500);
            }
        } else {
            // Show fallback indicator
            comprehensiveIndicator.classList.add('hidden');
            fallbackIndicator.classList.remove('hidden');
            
            // Update informational banner
            updateInfoBanner(false);
            
            // Show warning notification (only once)
            if (!sessionStorage.getItem('fallbackBalanceNotified')) {
                setTimeout(() => {
                    showAlert('info', '⚠️ Saldo hanya menghitung transaksi manual kas');
                    sessionStorage.setItem('fallbackBalanceNotified', 'true');
                }, 500);
            }
        }
        
        // Update icons
        if (window.lucide) window.lucide.createIcons({ icons });
    }
    
    function updateInfoBanner(isComprehensive) {
        const banner = document.getElementById('balanceInfoBanner');
        const title = document.getElementById('bannerTitle');
        const description = document.getElementById('bannerDescription');
        
        // Don't show banner if user has closed it
        if (sessionStorage.getItem('bannerClosed')) {
            return;
        }
        
        if (isComprehensive) {
            banner.className = 'mb-4 p-4 rounded-lg border-l-4 bg-green-50 border-green-400 text-green-800';
            title.textContent = '✅ Saldo Komprehensif Aktif';
            description.textContent = 'Perhitungan saldo mencakup semua jenis transaksi: manual kas, penjualan POS, dan refund. Hasil saldo konsisten dengan dashboard utama.';
        } else {
            banner.className = 'mb-4 p-4 rounded-lg border-l-4 bg-yellow-50 border-yellow-400 text-yellow-800';
            title.textContent = '⚠️ Mode Saldo Manual';
            description.textContent = 'Perhitungan saldo hanya mencakup transaksi manual kas. Saldo mungkin berbeda dengan dashboard utama yang mencakup POS dan refund.';
        }
        
        // banner.classList.remove('hidden');
        banner.classList.add('hidden');
    }

    // Initialize with current date and outlet
    const initialDate = new Date().toISOString().split('T')[0];
    const initialOutletId = getSelectedOutletId();
    
    // Load initial data
    fetchCashHistory(initialOutletId, initialDate);
    fetchPendingRequests(initialOutletId);
    fetchCashRequestHistory(initialOutletId);
    fetchBalanceInfo(initialOutletId);
    
    // Add status filter event listener
    document.getElementById('statusFilter').addEventListener('change', function() {
        const outletId = getSelectedOutletId();
        const status = this.value;
        fetchCashRequestHistory(outletId, status);
    });
    
    // Make functions available globally for the approval functions
    window.fetchCashHistory = fetchCashHistory;
    window.fetchBalanceInfo = fetchBalanceInfo;
    
    // Setup polling for cash data updates
    if (window.pollingManager) {
        const outletId = getSelectedOutletId();
        const selectedDate = document.getElementById('cashDatePicker').value || new Date().toISOString().split('T')[0];
        
        // Start polling for cash data updates every 45 seconds
        window.pollingManager.start('cashData', async () => {
            console.log('Polling cash data...');
            const currentOutletId = getSelectedOutletId();
            const currentDate = document.getElementById('cashDatePicker').value || new Date().toISOString().split('T')[0];
            
            // Refresh all cash-related data
            await Promise.all([
                fetchCashHistory(currentOutletId, currentDate),
                fetchPendingRequests(currentOutletId),
                fetchBalanceInfo(currentOutletId)
            ]);
        }, 45000); // 45 seconds interval
    }
    
    // Stop polling when leaving the page
    window.addEventListener('beforeunload', () => {
        if (window.pollingManager) {
            window.pollingManager.stop('cashData');
        }
    });

});

// Admin Cash Functions (Global scope for onclick handlers)
function openAdminCashModal(type) {
        const modal = document.getElementById('adminCashModal');
        const title = document.getElementById('adminCashModalTitle');
        const icon = document.getElementById('adminCashModalIcon');
        const submitBtn = document.getElementById('adminCashSubmitBtn');
        
        // Set modal type
        document.getElementById('adminCashType').value = type;
        
        // Update UI based on type
        if (type === 'add') {
            title.innerHTML = '<i data-lucide="plus" class="w-5 h-5 inline mr-2"></i>Tambah Kas (Admin)';
            submitBtn.className = 'px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600';
            submitBtn.innerHTML = '<i data-lucide="check" class="w-4 h-4 inline mr-1"></i>Tambah Langsung';
        } else {
            title.innerHTML = '<i data-lucide="minus" class="w-5 h-5 inline mr-2"></i>Kurang Kas (Admin)';
            submitBtn.className = 'px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600';
            submitBtn.innerHTML = '<i data-lucide="check" class="w-4 h-4 inline mr-1"></i>Kurang Langsung';
        }
        
        // Reset form
        document.getElementById('adminCashAmount').value = '';
        document.getElementById('adminCashAmountRaw').value = '';
        document.getElementById('adminCashReason').value = '';
        
        // Show modal
        modal.classList.remove('hidden');
        
        // Update icons
        if (window.lucide) window.lucide.createIcons({ icons });
        
        // Setup currency formatting for admin cash amount
        setupAdminCashCurrencyFormatting();
    }
    
    function closeAdminCashModal() {
        document.getElementById('adminCashModal').classList.add('hidden');
    }
    
    function setupAdminCashCurrencyFormatting() {
        const adminCashAmount = document.getElementById('adminCashAmount');
        const adminCashAmountRaw = document.getElementById('adminCashAmountRaw');
        
        if (!adminCashAmount || !adminCashAmountRaw) return;
        
        // Simple input handler for currency formatting
        adminCashAmount.addEventListener('input', function(e) {
            const rawValue = this.value.replace(/[^0-9]/g, '');
            adminCashAmountRaw.value = rawValue;
            
            if (rawValue) {
                const formatted = rawValue.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                this.value = formatted;
            } else {
                this.value = '';
            }
        });
        
        adminCashAmount.addEventListener('paste', function(e) {
            setTimeout(() => {
                const rawValue = this.value.replace(/[^0-9]/g, '');
                adminCashAmountRaw.value = rawValue;
                
                if (rawValue) {
                    const formatted = rawValue.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                    this.value = formatted;
                } else {
                    this.value = '';
                }
            }, 10);
        });
        
        adminCashAmount.addEventListener('focus', function(e) {
            this.select();
        });
        
        adminCashAmount.addEventListener('blur', function(e) {
            const rawValue = this.value.replace(/[^0-9]/g, '');
            adminCashAmountRaw.value = rawValue;
            
            if (rawValue) {
                const formatted = rawValue.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                this.value = formatted;
            } else {
                this.value = '';
            }
        });
    }
    
    function submitAdminCash(event) {
        event.preventDefault();
        
        const type = document.getElementById('adminCashType').value;
        const amountRaw = document.getElementById('adminCashAmountRaw').value;
        const amount = amountRaw ? parseFloat(amountRaw) : parseFloat(document.getElementById('adminCashAmount').value.replace(/[^\\d]/g, ''));
        const reason = document.getElementById('adminCashReason').value || 'Transaksi admin langsung';
        const outletId = getSelectedOutletId();
        
        if (isNaN(amount) || amount <= 0) {
            showAlert('error', 'Jumlah tidak valid');
            return;
        }
        
        const submitBtn = document.getElementById('adminCashSubmitBtn');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i data-lucide="loader" class="w-4 h-4 inline mr-1 animate-spin"></i>Memproses...';
        submitBtn.disabled = true;
        
        // First fetch cash register ID for the outlet
        axios.get(`/api/cash-registers/${outletId}`, {
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('token')}`,
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.data.success || !response.data.data) {
                throw new Error('Cash register tidak ditemukan untuk outlet ini');
            }
            
            const cashRegisterId = response.data.data.id;
            
            // Now create the cash register transaction (shift_id is optional for admin transactions)
            return axios.post('/api/cash-register-transactions', {
                cash_register_id: cashRegisterId,
                type: type === 'add' ? 'add' : 'remove',
                amount: amount,
                reason: reason,
                source: 'admin_direct' // Flag to indicate this is an admin direct transaction
            }, {
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('token')}`,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            });
        })
        .then(response => {
            if (response.data.success) {
                const actionText = type === 'add' ? 'menambah' : 'mengurangi';
                const formattedAmount = new Intl.NumberFormat('id-ID').format(amount);
                showAlert('success', `Berhasil ${actionText} kas sebesar Rp ${formattedAmount}`);
                
                closeAdminCashModal();
                
                // Refresh all related data
                const currentOutletId = getSelectedOutletId();
                fetchBalanceInfo(currentOutletId);
                if (window.fetchCashHistory) {
                    window.fetchCashHistory(currentOutletId, selectedDate);
                }
            } else {
                throw new Error(response.data.message || 'Gagal memproses transaksi');
            }
        })
        .catch(error => {
            console.error('Error processing admin cash transaction:', error);
            showAlert('error', error.response?.data?.message || error.message || 'Terjadi kesalahan saat memproses transaksi');
        })
        .finally(() => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
            if (window.lucide) window.lucide.createIcons({ icons });
        });
}
</script>

@endsection