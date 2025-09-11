@extends('layouts.app')

@section('title', 'Riwayat Transaksi')

@section('content')

<!-- Alert Container -->
<div id="alertContainer" class="fixed top-4 right-4 z-50 space-y-3 w-80"></div>

<!-- title Page -->
<div class="mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <h1 class="text-3xl font-bold text-gray-800">Riwayat Transaksi</h1>
    </div>
</div>

<!-- Card: Outlet Info -->
<div class="bg-white rounded-md p-4 shadow-md mb-4 flex flex-col md:flex-row md:items-center md:justify-between">
    <div class="mb-3 md:mb-0 flex items-start gap-2">
        <i data-lucide="store" class="w-5 h-5 text-gray-600"></i>
        <div>
            <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">Menampilkan riwayat transaksi: <span class="outlet-name">Loading...</span></h2>
            <p class="text-sm text-gray-600">Data riwayat transaksi untuk <span class=" outlet-name"></span></p>
        </div>
    </div>
</div>

<!-- Table Riwayat Transaksi -->
<div class="bg-white rounded-lg shadow p-4">
    <!-- Header & Filter Row -->
    <div class="flex flex-col mb-4">
        <!-- Title and Date Filter Row -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-3">
            <h3 class="text-2xl font-bold text-gray-800">Riwayat Transaksi</h3>
            
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
                <input type="text" id="searchInvoice"
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                    placeholder="Cari Invoice..." />
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                    <i data-lucide="search" class="w-4 h-4"></i>
                </span>
            </div>
            
            <!-- Status Filter -->
            <div class="relative">
                <select id="statusFilter" class="w-full sm:w-48 pl-3 pr-8 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500 bg-white">
                    <option value="">Semua Status</option>
                    <option value="pending">Menunggu</option>
                    <option value="completed">Selesai</option>
                    <option value="cancelled">Dibatalkan</option>
                </select>
            </div>
            
            <!-- Approval Filter -->
            <div class="relative">
                <select id="approvalFilter" class="w-full sm:w-48 pl-3 pr-8 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500 bg-white">
                    <option value="">Semua Approval</option>
                    <option value="pending">Menunggu Approval</option>
                    <option value="approved">Disetujui</option>
                    <option value="rejected">Ditolak</option>
                </select>
            </div>
            
            <!-- Category Filter -->
            <div class="relative">
                <select id="categoryFilter" class="w-full sm:w-48 pl-3 pr-8 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500 bg-white">
                    <option value="">Semua Kategori</option>
                    <option value="lunas">Lunas</option>
                    <option value="dp">DP</option>
                </select>
            </div>
            
            <!-- DP Status Filter -->
            <div class="relative">
                <select id="dpStatusFilter" class="w-full sm:w-48 pl-3 pr-8 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500 bg-white">
                    <option value="">Semua Status DP</option>
                    <option value="pending">DP Belum Lunas</option>
                    <option value="completed">DP Sudah Lunas</option>
                </select>
            </div>
        </div>
    </div>
    
    <!-- Table Content -->
    <div class="relative">
        <!-- Scroll indicator shadows -->
        <div class="absolute top-0 right-0 bottom-0 w-8 bg-gradient-to-l from-white via-white to-transparent pointer-events-none z-10 rounded-r-lg" id="scrollIndicatorRight">
            <!-- Scroll hint icon -->
            <div class="absolute top-1/2 right-2 transform -translate-y-1/2">
                <div class="flex items-center justify-center w-4 h-4 text-gray-400" title="Scroll horizontal untuk melihat lebih banyak kolom">
                    <i data-lucide="chevrons-right" class="w-3 h-3"></i>
                </div>
            </div>
        </div>
        
        <div class="overflow-x-auto border border-gray-200 rounded-lg" id="tableContainer">
            <table class="min-w-full text-sm">
            <thead class="text-left text-gray-700 border-b-2 bg-gray-50">
                <tr>
                    <th class="px-4 py-3 font-bold min-w-[120px]">Invoice</th>
                    <th class="px-4 py-3 font-bold min-w-[140px]">Waktu</th>
                    <th class="px-4 py-3 font-bold min-w-[100px]">Kasir</th>
                    <th class="px-4 py-3 font-bold min-w-[80px]">Kategori</th>
                    <th class="px-4 py-3 font-bold min-w-[120px]">Layanan</th>
                    <th class="px-4 py-3 font-bold min-w-[80px]">Pajak</th>
                    <th class="px-4 py-3 font-bold min-w-[100px]">Pembayaran</th>
                    <th class="px-4 py-3 font-bold min-w-[80px]">Status</th>
                    <th class="px-4 py-3 font-bold min-w-[120px]">Approval</th>
                    <th class="px-4 py-3 font-bold min-w-[100px]">Total</th>
                    <th class="px-4 py-3 font-bold min-w-[100px]">Sisa Bayar</th>
                    <th class="px-4 py-3 font-bold min-w-[400px] text-left">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 divide-y">
                <!-- Data akan diisi secara dinamis -->
                <tr>
                    <td colspan="12" class="py-8 text-center">
                        <div class="flex flex-col items-center justify-center gap-2 mx-auto">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                                 class="animate-spin text-green-500">
                                <path d="M21 12a9 9 0 1 1-6.219-8.56"/>
                            </svg>
                            <span class="text-gray-500">Memuat data transaksi...</span>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        </div>
    </div>
</div>

<!-- Modal Detail Transaksi -->
<div id="modalDetail" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold">Detail Transaksi</h3>
                <button onclick="closeDetailModal()" class="text-gray-500 hover:text-gray-700">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <p class="text-gray-500">No. Invoice</p>
                    <p id="detailInvoice" class="font-medium"></p>
                </div>
                <div>
                    <p class="text-gray-500">Tanggal/Waktu</p>
                    <p id="detailDateTime" class="font-medium"></p>
                </div>
                <div>
                    <p class="text-gray-500">Metode Pembayaran</p>
                    <p id="detailPaymentMethod" class="font-medium"></p>
                </div>
                <div>
                    <p class="text-gray-500">Status</p>
                    <p id="detailStatus" class="font-medium"></p>
                </div>
                <div>
                    <p class="text-gray-500">Status Approval</p>
                    <p id="detailApprovalStatus" class="font-medium"></p>
                </div>
                <div>
                    <p class="text-gray-500">Kategori Transaksi</p>
                    <p id="detailTransactionCategory" class="font-medium"></p>
                </div>
                <div id="memberInfoRow" class="hidden">
                    <p class="text-gray-500">Member</p>
                    <p id="detailMember" class="font-medium"></p>
                </div>
                <div id="masjidInfoRow" class="hidden">
                    <p class="text-gray-500">Masjid Tujuan</p>
                    <p id="detailMasjid" class="font-medium"></p>
                </div>
                <div id="contractPdfInfoRow" class="hidden">
                    <p class="text-gray-500">Akad Jual Beli</p>
                    <div id="detailContractPdf" class="font-medium">
                        <button onclick="downloadContractPdf(window.currentTransactionDetail.contract_pdf_url, window.currentTransactionDetail.order_number)" 
                            class="inline-flex items-center px-3 py-1 text-sm bg-orange-100 text-orange-700 hover:bg-orange-200 rounded transition-colors">
                            <i class="fas fa-file-pdf mr-2"></i>
                            Download Akad Jual Beli
                        </button>
                    </div>
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
                        <a id="detailPaymentProofLink" href="#" class="font-medium ml-1 text-green-600 hover:text-green-800">Lihat Bukti</a>
                    </div>
                </div>
            </div>
            
            <!-- Carpet Service Information Section -->
            <div id="carpetServiceSection" class="hidden mb-4 p-3 bg-gray-50 rounded-lg">
                <h4 class="font-medium mb-2 text-green-700">
                    Layanan Karpet Masjid
                </h4>
                <div class="grid grid-cols-1 gap-2 text-sm">
                    <div id="serviceTypeInfo" class="hidden">
                        <span class="text-gray-500">Jenis Layanan:</span>
                        <span id="detailServiceType" class="font-medium ml-1"></span>
                    </div>
                    <div id="installationDateInfo" class="hidden">
                        <span class="text-gray-500">Estimasi Pemasangan:</span>
                        <span id="detailInstallationDate" class="font-medium ml-1"></span>
                    </div>
                    <div id="installationNotesInfo" class="hidden">
                        <span class="text-gray-500">Rincian Pemasangan:</span>
                        <div id="detailInstallationNotes" class="font-medium mt-1 p-2 bg-white rounded border text-gray-700"></div>
                    </div>
                </div>
            </div>
            
            <div class="mb-4">
                <h4 class="font-medium mb-2">Item Pembelian</h4>
                <div id="detailItems"></div>
            </div>
            
            <!-- Bonus Items Section -->
            <div id="bonusItemsSection" class="mb-4 hidden">
                <h4 class="font-medium mb-2 text-green-600">Item Bonus</h4>
                <div id="detailBonusItems" class="bg-green-50 p-3 rounded-lg"></div>
            </div>
            
            <div class="border-t pt-4 space-y-2">
                <div class="flex justify-between">
                    <span>Subtotal</span>
                    <span id="detailSubtotal" class="font-medium"></span>
                </div>
                <div class="flex justify-between">
                    <span>Pajak</span>
                    <span id="detailTax" class="font-medium"></span>
                </div>
                <div class="flex justify-between">
                    <span>Diskon</span>
                    <span id="detailDiscount" class="font-medium"></span>
                </div>
                <div class="flex justify-between">
                    <span>Total Dibayar</span>
                    <span id="detailTotalPaid" class="font-medium"></span>
                </div>
                <div id="detailRemainingBalanceRow" class="hidden flex justify-between">
                    <span>Sisa Pembayaran</span>
                    <span id="detailRemainingBalance" class="font-bold text-red-600"></span>
                </div>
                <div class="flex justify-between">
                    <span>Kembalian</span>
                    <span id="detailChange" class="font-medium"></span>
                </div>
                <div class="flex justify-between border-t pt-2 font-bold text-lg">
                    <span>Total</span>
                    <span id="detailTotal" class="text-green-600"></span>
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

<!-- Modal Settlement DP -->
<div id="modalSettlement" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold text-gray-900">Pelunasan DP</h3>
            <button onclick="closeSettlementModal()" class="text-gray-500 hover:text-gray-700">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        
        <!-- Order Details -->
        <div class="mb-6 p-4 bg-gray-50 rounded-lg">
            <h4 class="font-semibold text-gray-800 mb-3">Detail Order</h4>
            <div class="grid grid-cols-2 gap-3 text-sm">
                <div>
                    <span class="text-gray-600">No. Invoice:</span>
                    <span id="settlementInvoice" class="font-medium ml-1"></span>
                </div>
                <div>
                    <span class="text-gray-600">Tanggal:</span>
                    <span id="settlementDate" class="font-medium ml-1"></span>
                </div>
                <div>
                    <span class="text-gray-600">Total:</span>
                    <span id="settlementTotal" class="font-medium ml-1 text-green-600"></span>
                </div>
                <div>
                    <span class="text-gray-600">Sudah Dibayar:</span>
                    <span id="settlementPaid" class="font-medium ml-1"></span>
                </div>
                <div class="col-span-2">
                    <span class="text-gray-600">Sisa Pembayaran:</span>
                    <span id="settlementRemaining" class="font-bold ml-1 text-red-600 text-lg"></span>
                </div>
            </div>
        </div>

        <!-- Settlement Form -->
        <form id="settlementForm" enctype="multipart/form-data">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah Pelunasan *</label>
                <input type="number" id="settlementAmount" step="0.01" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" placeholder="Masukkan jumlah pelunasan" required>
                <div class="mt-2 flex gap-2">
                    <button type="button" onclick="setSettlementAmount('full')" class="px-3 py-1 bg-green-100 text-green-700 rounded text-sm hover:bg-green-200">Lunas Penuh</button>
                    <button type="button" onclick="setSettlementAmount('half')" class="px-3 py-1 bg-blue-100 text-blue-700 rounded text-sm hover:bg-blue-200">Setengah</button>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Metode Pembayaran *</label>
                <select id="settlementPaymentMethod" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" required>
                    <option value="">Pilih metode pembayaran</option>
                    <option value="cash">Tunai</option>
                    <option value="transfer">Transfer Bank</option>
                    <option value="qris">QRIS</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Bukti Pembayaran</label>
                <input type="file" id="settlementPaymentProof" accept="image/*,application/pdf" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, PDF. Maksimal 5MB.</p>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Catatan (Opsional)</label>
                <textarea id="settlementNotes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" placeholder="Tambahkan catatan jika diperlukan"></textarea>
            </div>

            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeSettlementModal()" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    Proses Pelunasan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Riwayat Pelunasan DP -->
<div id="modalDpHistory" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl mx-4 max-h-[90vh] overflow-hidden">
        <div class="flex justify-between items-center px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Riwayat Pelunasan DP</h3>
            <button onclick="closeDpHistoryModal()" class="text-gray-500 hover:text-gray-700">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        
        <!-- Order Info -->
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
                <div>
                    <span class="text-gray-600">No. Invoice:</span>
                    <span id="dpHistoryInvoice" class="font-medium ml-1"></span>
                </div>
                <div>
                    <span class="text-gray-600">Total:</span>
                    <span id="dpHistoryTotal" class="font-medium ml-1 text-green-600"></span>
                </div>
                <div>
                    <span class="text-gray-600">Sudah Dibayar:</span>
                    <span id="dpHistoryPaid" class="font-medium ml-1"></span>
                </div>
                <div>
                    <span class="text-gray-600">Sisa Bayar:</span>
                    <span id="dpHistoryRemaining" class="font-medium ml-1 text-red-600"></span>
                </div>
            </div>
        </div>
        
        <!-- Content -->
        <div class="px-6 py-4 max-h-96 overflow-y-auto">
            <div id="dpHistoryLoading" class="text-center py-8">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-green-500"></div>
                <p class="mt-2 text-gray-600">Memuat riwayat pelunasan...</p>
            </div>
            
            <div id="dpHistoryContent" class="hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Metode</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Petugas</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bukti</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catatan</th>
                            </tr>
                        </thead>
                        <tbody id="dpHistoryTableBody" class="bg-white divide-y divide-gray-200">
                            <!-- Data akan dimuat di sini -->
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div id="dpHistoryEmpty" class="hidden text-center py-8">
                <i data-lucide="file-text" class="w-12 h-12 text-gray-400 mx-auto mb-3"></i>
                <p class="text-gray-600">Belum ada riwayat pelunasan untuk transaksi DP ini.</p>
            </div>
        </div>
        
        <!-- Summary Footer -->
        <div id="dpHistorySummary" class="hidden px-6 py-4 bg-gray-50 border-t border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                <div>
                    <span class="text-gray-600">Total Pelunasan:</span>
                    <span id="dpHistoryTotalSettlements" class="font-medium ml-1">0 kali</span>
                </div>
                <div>
                    <span class="text-gray-600">Total Dibayar:</span>
                    <span id="dpHistoryTotalAmount" class="font-medium ml-1 text-green-600">Rp 0</span>
                </div>
                <div>
                    <span class="text-gray-600">Status:</span>
                    <span id="dpHistoryStatus" class="font-medium ml-1"></span>
                </div>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="px-6 py-4 border-t border-gray-200 flex justify-end">
            <button onclick="closeDpHistoryModal()" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                Tutup
            </button>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
<script>
    let transactionsCache = [];
    // Script utama untuk halaman Riwayat Transaksi
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
                    fetchTransactionHistory(date);
                } else {
                    // Jika tidak ada tanggal terpilih, tampilkan semua transaksi
                    fetchTransactionHistory(null);
                }
            }
        });

        // Load data awal
        fetchTransactionHistory();
        
        // Pencarian dan Filter
        document.getElementById('searchInvoice').addEventListener('input', applyFilters);
        document.getElementById('statusFilter').addEventListener('change', applyFilters);
        document.getElementById('approvalFilter').addEventListener('change', applyFilters);
        document.getElementById('categoryFilter').addEventListener('change', applyFilters);
        document.getElementById('dpStatusFilter').addEventListener('change', applyFilters);

        // Connect outlet selection to transaction history updates
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
                fetchTransactionHistory(date);
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
                        
                        fetchTransactionHistory(date);
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

    // Fungsi untuk fetch data transaksi - dimodifikasi untuk menyertakan outlet_id
    async function fetchTransactionHistory(date = null) {
        try {
            const token = localStorage.getItem('token');
            if (!token) {
                window.location.href = '/login';
                return;
            }

            // Get the current outlet ID
            const outletId = getSelectedOutletId();
            
            // Format parameter tanggal seperti di versi lama
            const params = new URLSearchParams();
            if (date) {
                // Gunakan tanggal yang sama untuk date_from dan date_to
                // untuk menampilkan transaksi pada hari yang dipilih saja
                params.append('date_from', date);
                params.append('date_to', date);
            } else {
                const currentDate = new Date().toISOString().split('T')[0];
                params.append('date_from', currentDate.toLocaleString());
                params.append('date_to', currentDate.toLocaleString());
            }
            
            // Tambahkan outlet_id ke parameters
            params.append('outlet_id', outletId);
            
            // Fetch data dari endpoint dengan token authorization
            const response = await fetch(`/api/orders/history?${params.toString()}`, {
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
                throw new Error('Gagal mengambil data');
            }
            
            const result = await response.json();
            
            // Debug log untuk API response
            console.log('API Response:', result);
            
            // Update outlet info if available
            if (result.data && result.data.outlet) {
                updateOutletInfo(result.data);
            } else {
                updateOutletInfoFromSelection();
            }
            
            // Pastikan kita mengakses data.orders dari response
            if (result.data && Array.isArray(result.data.orders)) {
                // Store in global cache
                transactionsCache = result.data.orders;
                renderTransactionData(result.data.orders);
            } else if (result.orders) {
                // Alternatif jika struktur data berbeda
                transactionsCache = result.orders;
                renderTransactionData(result.orders);
            } else {
                // Jika tidak ada data
                transactionsCache = [];
                renderTransactionData([]);
            }
            
        } catch (error) {
            console.error('Error:', error);
            showAlert('error', error.message);
        }
    }


    // Fungsi untuk render data ke tabel
    function renderTransactionData(transactions) {
        const tbody = document.querySelector('tbody');
        tbody.innerHTML = '';
        
        console.log("Data transaksi untuk dirender:", transactions); // Untuk debugging
        
        if (!transactions || transactions.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="12" class="py-4 text-center text-gray-500">
                        Tidak ada transaksi pada tanggal ini.
                    </td>
                </tr>
            `;
            return;
        }
        
        transactions.forEach(transaction => {
            const row = document.createElement('tr');
            row.className = 'border-b hover:bg-gray-50';
            row.dataset.transactionId = transaction.id; // Add transaction ID for filtering
            row.innerHTML = `
                <td class="px-4 py-3">${transaction.order_number}</td>
                <td class="px-4 py-3 whitespace-nowrap">${formatDateTime(transaction.created_at)}</td>
                <td class="px-4 py-3">${transaction.user || 'Kasir'}</td>
                <td class="px-4 py-3">
                    <span class="px-2 py-1 ${getCategoryBadgeClass(transaction.transaction_category)} rounded-full text-xs font-medium whitespace-nowrap">
                        ${getCategoryText(transaction.transaction_category)}
                    </span>
                </td>
                <td class="px-4 py-3">
                    ${transaction.service_type ? `
                        <span class="px-2 py-1 bg-purple-100 text-purple-700 rounded-full text-xs font-medium whitespace-nowrap">
                            ${transaction.service_type === 'potong_obras_kirim' ? 'Potong, Obras & Kirim' : 'Pasang di Tempat'}
                        </span>
                    ` : '<span class="text-gray-400 text-xs">-</span>'}
                </td>
                <td class="px-4 py-3">
                    <span class="px-2 py-1 ${getPkpBadgeClass(transaction.tax)} rounded-full text-xs font-medium whitespace-nowrap">
                        ${getPkpText(transaction.tax)}
                    </span>
                </td>
                <td class="px-4 py-3">
                    <span class="px-2 py-1 ${getPaymentBadgeClass(transaction.payment_method)} rounded-full text-xs whitespace-nowrap">
                        ${getPaymentMethodText(transaction.payment_method)}
                    </span>
                </td>
                <td class="px-4 py-3">
                    <span class="px-2 py-1 ${getStatusBadgeClass(transaction.status)} rounded-full text-xs font-medium whitespace-nowrap">
                        ${getStatusText(transaction.status)}
                    </span>
                </td>
                <td class="px-4 py-3">
                    <span class="px-2 py-1 ${getApprovalBadgeClass(transaction.approval_status)} rounded-full text-xs font-medium whitespace-nowrap">
                        ${getApprovalStatusText(transaction.approval_status)}
                    </span>
                </td>
                <td class="px-4 py-3 font-semibold whitespace-nowrap">${formatCurrency(transaction.total)}</td>
                <td class="px-4 py-3 whitespace-nowrap">
                    ${transaction.remaining_balance > 0 ? 
                        `<span class="font-semibold text-red-600">${formatCurrency(transaction.remaining_balance)}</span>` : 
                        `<span class="text-gray-400">-</span>`
                    }
                </td>
                <td class="px-4 py-3">
                    <div class="flex flex-wrap gap-1">
                        <button onclick="openDetailModal('${transaction.id}')" class="inline-flex items-center px-2 py-1 text-xs bg-blue-100 text-blue-700 hover:bg-blue-200 rounded transition-colors" title="Lihat Detail Transaksi">
                            <i data-lucide="eye" class="w-3 h-3 mr-1"></i>
                            Detail
                        </button>
                        ${transaction.payment_proof_url ? `
                        <button onclick="openPaymentProofModal('${transaction.payment_proof_url}')" class="inline-flex items-center px-2 py-1 text-xs bg-purple-100 text-purple-700 hover:bg-purple-200 rounded transition-colors" title="Lihat Bukti Pembayaran">
                            <i data-lucide="image" class="w-3 h-3 mr-1"></i>
                            Bukti
                        </button>
                        ` : ''}
                        ${transaction.can_settle ? `
                        <button onclick="openSettlementModal('${transaction.order_number}', '${transaction.id}')" class="inline-flex items-center px-2 py-1 text-xs bg-green-100 text-green-700 hover:bg-green-200 rounded transition-colors" title="Lunasi DP">
                            <i data-lucide="dollar-sign" class="w-3 h-3 mr-1"></i>
                            Lunasi
                        </button>
                        ` : ''}
                        ${transaction.transaction_category === 'dp' && transaction.total_paid > 0 ? `
                        <button onclick="openDpHistoryModal('${transaction.order_number}', '${transaction.id}')" class="inline-flex items-center px-2 py-1 text-xs bg-blue-100 text-blue-700 hover:bg-blue-200 rounded transition-colors" title="Lihat Riwayat Pelunasan DP">
                            <i data-lucide="history" class="w-3 h-3 mr-1"></i>
                            Riwayat DP
                        </button>
                        ` : ''}
                        ${transaction.transaction_category === 'dp' && transaction.contract_pdf_url ? `
                        <button onclick="downloadContractPdf('${transaction.contract_pdf_url}', '${transaction.order_number}')" class="inline-flex items-center px-2 py-1 text-xs bg-orange-100 text-orange-700 hover:bg-orange-200 rounded transition-colors" title="Download Akad Jual Beli">
                            <i data-lucide="file-text" class="w-3 h-3 mr-1"></i>
                            Akad PDF
                        </button>
                        ` : ''}
                        ${transaction.approval_status === 'pending' ? `
                        <button onclick="openApproveModal('${transaction.order_number}', '${transaction.id}')" class="inline-flex items-center px-2 py-1 text-xs bg-green-100 text-green-700 hover:bg-green-200 rounded transition-colors" title="Setujui Transaksi">
                            <i data-lucide="check-circle" class="w-3 h-3 mr-1"></i>
                            Setujui
                        </button>
                        <button onclick="openRejectModal('${transaction.order_number}', '${transaction.id}')" class="inline-flex items-center px-2 py-1 text-xs bg-red-100 text-red-700 hover:bg-red-200 rounded transition-colors" title="Tolak Transaksi">
                            <i data-lucide="x-circle" class="w-3 h-3 mr-1"></i>
                            Tolak
                        </button>
                        ` : ''}
                        ${(transaction.status === 'completed' || transaction.status === 'pending') && !transaction.cancellation_status ? `
                        <button onclick="openCancellationRequestModal('${transaction.order_number}', '${transaction.id}')" class="inline-flex items-center px-2 py-1 text-xs bg-orange-100 text-orange-700 hover:bg-orange-200 rounded transition-colors" title="${transaction.status === 'pending' ? 'Ajukan Pembatalan' : 'Ajukan Refund'}">
                            <i data-lucide="rotate-ccw" class="w-3 h-3 mr-1"></i>
                            ${transaction.status === 'pending' ? 'Batal' : 'Refund'}
                        </button>
                        ` : ''}
                        ${transaction.cancellation_status === 'requested' ? `
                        <button onclick="openCancellationApprovalModal('${transaction.order_number}', '${transaction.id}')" class="inline-flex items-center px-2 py-1 text-xs bg-yellow-100 text-yellow-700 hover:bg-yellow-200 rounded transition-colors" title="Review Permintaan Pembatalan/Refund">
                            <i data-lucide="clock" class="w-3 h-3 mr-1"></i>
                            Review
                        </button>
                        ` : ''}
                    </div>
                </td>
            `;
            tbody.appendChild(row);
        });
        
        if (window.lucide) window.lucide.createIcons();
        
        // Setup scroll indicator untuk tabel
        setupTableScrollIndicator();
    }
    
    // Setup scroll indicator
    function setupTableScrollIndicator() {
        const tableContainer = document.getElementById('tableContainer');
        const scrollIndicatorRight = document.getElementById('scrollIndicatorRight');
        
        if (!tableContainer || !scrollIndicatorRight) return;
        
        function updateScrollIndicator() {
            const { scrollLeft, scrollWidth, clientWidth } = tableContainer;
            const isScrollable = scrollWidth > clientWidth;
            const isAtEnd = scrollLeft >= (scrollWidth - clientWidth - 10); // 10px threshold
            
            // Show/hide right indicator
            if (isScrollable && !isAtEnd) {
                scrollIndicatorRight.classList.remove('hidden');
            } else {
                scrollIndicatorRight.classList.add('hidden');
            }
        }
        
        // Initial check
        updateScrollIndicator();
        
        // Update on scroll
        tableContainer.addEventListener('scroll', updateScrollIndicator);
        
        // Update on window resize
        window.addEventListener('resize', updateScrollIndicator);
        
        // Update when table content changes
        const observer = new MutationObserver(updateScrollIndicator);
        observer.observe(tableContainer, { childList: true, subtree: true });
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
            
            // Debug log untuk bonus items
            console.log('Transaction data:', transaction);
            console.log('Bonus items:', transaction.bonus_items);

            // Daftar element yang diperlukan
            const elements = {
                invoice: document.getElementById('detailInvoice'),
                dateTime: document.getElementById('detailDateTime'),
                paymentMethod: document.getElementById('detailPaymentMethod'),
                status: document.getElementById('detailStatus'),
                approvalStatus: document.getElementById('detailApprovalStatus'),
                transactionCategory: document.getElementById('detailTransactionCategory'),
                member: document.getElementById('detailMember'),
                total: document.getElementById('detailTotal'),
                subtotal: document.getElementById('detailSubtotal'),
                tax: document.getElementById('detailTax'),
                discount: document.getElementById('detailDiscount'),
                totalPaid: document.getElementById('detailTotalPaid'),
                change: document.getElementById('detailChange'),
                items: document.getElementById('detailItems'),
                bonusItems: document.getElementById('detailBonusItems')
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
            
            // Isi kategori transaksi
            const categoryText = transaction.transaction_category === 'dp' ? 'DP (Uang Muka)' : 'Lunas';
            elements.transactionCategory.textContent = categoryText;
            
            // Isi member info
            const memberRow = document.getElementById('memberInfoRow');
            if (transaction.member && transaction.member.name) {
                elements.member.textContent = `${transaction.member.name} (${transaction.member.member_code})`;
                memberRow.classList.remove('hidden');
            } else {
                memberRow.classList.add('hidden');
            }

            // Isi masjid info
            const masjidRow = document.getElementById('masjidInfoRow');
            const masjidElement = document.getElementById('detailMasjid');
            if (transaction.mosque && transaction.mosque.name) {
                masjidElement.textContent = `${transaction.mosque.name} - ${transaction.mosque.address}`;
                masjidRow.classList.remove('hidden');
            } else {
                masjidRow.classList.add('hidden');
            }

            // Isi contract PDF info untuk transaksi DP
            const contractPdfRow = document.getElementById('contractPdfInfoRow');
            if (transaction.transaction_category === 'dp' && transaction.contract_pdf_url) {
                // Store transaction detail globally for download function
                window.currentTransactionDetail = transaction;
                contractPdfRow.classList.remove('hidden');
            } else {
                contractPdfRow.classList.add('hidden');
            }
            
            elements.total.textContent = formatCurrency(transaction.total);
            elements.subtotal.textContent = formatCurrency(transaction.subtotal);
            elements.tax.textContent = formatCurrency(transaction.tax);
            elements.discount.textContent = formatCurrency(transaction.discount);
            elements.totalPaid.textContent = formatCurrency(transaction.total_paid);
            elements.change.textContent = formatCurrency(transaction.change);

            // Handle remaining balance display for DP transactions
            const remainingBalanceRow = document.getElementById('detailRemainingBalanceRow');
            const remainingBalanceElement = document.getElementById('detailRemainingBalance');
            
            if (transaction.remaining_balance && transaction.remaining_balance > 0) {
                remainingBalanceElement.textContent = formatCurrency(transaction.remaining_balance);
                remainingBalanceRow.classList.remove('hidden');
            } else {
                remainingBalanceRow.classList.add('hidden');
            }

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
                                ${item.bonus_qty > 0 ? `<p class="text-sm text-green-600">+ ${item.bonus_qty} bonus</p>` : ''}
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

            // Isi bonus items
            const bonusSection = document.getElementById('bonusItemsSection');
            elements.bonusItems.innerHTML = '';
            if (transaction.bonus_items?.length > 0) {
                transaction.bonus_items.forEach(bonusItem => {
                    const bonusElement = document.createElement('div');
                    bonusElement.className = 'border-b border-green-200 py-2 last:border-b-0';
                    bonusElement.innerHTML = `
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="font-medium text-green-700">
                                    <i class="fas fa-gift mr-1"></i>
                                    ${bonusItem.product || bonusItem.product_name || '-'}
                                </p>
                                <p class="text-sm text-green-600">${bonusItem.quantity || 0} unit bonus</p>
                            </div>
                            <div class="text-sm text-green-600 font-medium">
                                GRATIS
                            </div>
                        </div>
                    `;
                    elements.bonusItems.appendChild(bonusElement);
                });
                bonusSection.classList.remove('hidden');
            } else {
                bonusSection.classList.add('hidden');
            }

            // Handle carpet service information
            const carpetServiceSection = document.getElementById('carpetServiceSection');
            const serviceTypeInfo = document.getElementById('serviceTypeInfo');
            const installationDateInfo = document.getElementById('installationDateInfo');
            const installationNotesInfo = document.getElementById('installationNotesInfo');

            let showCarpetServiceSection = false;

            // Show service type if available
            if (transaction.service_type) {
                const serviceTypeText = transaction.service_type === 'potong_obras_kirim' ? 'Potong, Obras & Kirim' : 'Pasang di Tempat';
                document.getElementById('detailServiceType').textContent = serviceTypeText;
                serviceTypeInfo.classList.remove('hidden');
                showCarpetServiceSection = true;
            } else {
                serviceTypeInfo.classList.add('hidden');
            }

            // Show installation date if available
            if (transaction.installation_date) {
                const installationDate = new Date(transaction.installation_date);
                const formattedDate = installationDate.toLocaleDateString('id-ID', {
                    day: 'numeric',
                    month: 'long',
                    year: 'numeric'
                });
                document.getElementById('detailInstallationDate').textContent = formattedDate;
                installationDateInfo.classList.remove('hidden');
                showCarpetServiceSection = true;
            } else {
                installationDateInfo.classList.add('hidden');
            }

            // Show installation notes if available
            if (transaction.installation_notes) {
                document.getElementById('detailInstallationNotes').textContent = transaction.installation_notes;
                installationNotesInfo.classList.remove('hidden');
                showCarpetServiceSection = true;
            } else {
                installationNotesInfo.classList.add('hidden');
            }

            // Show/hide carpet service section
            if (showCarpetServiceSection) {
                carpetServiceSection.classList.remove('hidden');
            } else {
                carpetServiceSection.classList.add('hidden');
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
        const searchTerm = document.getElementById('searchInvoice').value.toLowerCase();
        const statusFilter = document.getElementById('statusFilter').value;
        const approvalFilter = document.getElementById('approvalFilter').value;
        const categoryFilter = document.getElementById('categoryFilter').value;
        const dpStatusFilter = document.getElementById('dpStatusFilter').value;
        const rows = document.querySelectorAll('tbody tr');
        
        rows.forEach(row => {
            // Skip loading/empty state rows
            if (row.cells.length < 11) {
                return;
            }
            
            const invoice = row.cells[0]?.textContent?.toLowerCase() || '';
            
            // Get actual values from transaction data
            let actualStatus = '';
            let actualApprovalStatus = '';
            let actualCategory = '';
            let actualRemainingBalance = 0;
            
            // Extract values from transaction data if available
            if (row.dataset && row.dataset.transactionId) {
                const transaction = transactionsCache.find(t => t.id == row.dataset.transactionId);
                if (transaction) {
                    actualStatus = transaction.status;
                    actualApprovalStatus = transaction.approval_status;
                    actualCategory = transaction.transaction_category;
                    actualRemainingBalance = parseFloat(transaction.remaining_balance || 0);
                }
            }
            
            // Apply filters
            const matchesSearch = invoice.includes(searchTerm);
            const matchesStatus = !statusFilter || actualStatus === statusFilter;
            const matchesApproval = !approvalFilter || actualApprovalStatus === approvalFilter;
            const matchesCategory = !categoryFilter || actualCategory === categoryFilter;
            
            // DP Status Filter Logic
            let matchesDpStatus = true;
            if (dpStatusFilter) {
                if (dpStatusFilter === 'pending') {
                    // DP belum lunas = category DP dan remaining balance > 0
                    matchesDpStatus = actualCategory === 'dp' && actualRemainingBalance > 0;
                } else if (dpStatusFilter === 'completed') {
                    // DP sudah lunas = category sudah berubah jadi lunas ATAU remaining balance = 0
                    matchesDpStatus = (actualCategory === 'lunas' && actualRemainingBalance === 0) || 
                                     (actualCategory === 'dp' && actualRemainingBalance === 0);
                }
            }
            
            const shouldShow = matchesSearch && matchesStatus && matchesApproval && matchesCategory && matchesDpStatus;
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

    // Helper functions untuk kategori transaksi
    function getCategoryText(category) {
        const categories = {
            'lunas': 'Lunas',
            'dp': 'DP'
        };
        return categories[category] || category || 'Tidak diketahui';
    }

    function getCategoryBadgeClass(category) {
        const classes = {
            'lunas': 'bg-green-100 text-green-800 border-green-200',
            'dp': 'bg-orange-100 text-orange-800 border-orange-200'
        };
        return classes[category] || 'bg-gray-100 text-gray-800';
    }

    // Helper functions untuk PKP status
    function getPkpText(tax) {
        const taxAmount = parseFloat(tax || 0);
        return taxAmount > 0 ? 'PKP' : 'Non-PKP';
    }

    function getPkpBadgeClass(tax) {
        const taxAmount = parseFloat(tax || 0);
        return taxAmount > 0 
            ? 'bg-blue-100 text-blue-800 border-blue-200' 
            : 'bg-gray-100 text-gray-800 border-gray-200';
    }

    // Settlement Modal Functions
    function openSettlementModal(orderNumber, orderId) {
        try {
            const transaction = transactionsCache.find(t => t.id == orderId);
            if (!transaction) {
                showAlert('error', 'Data transaksi tidak ditemukan');
                return;
            }

            // Check if can settle
            if (!transaction.can_settle) {
                showAlert('error', 'Transaksi ini tidak dapat dilunasi');
                return;
            }

            const modal = document.getElementById('modalSettlement');
            
            // Fill order details
            document.getElementById('settlementInvoice').textContent = orderNumber;
            document.getElementById('settlementDate').textContent = formatDateTime(transaction.created_at);
            document.getElementById('settlementTotal').textContent = formatCurrency(transaction.total);
            document.getElementById('settlementPaid').textContent = formatCurrency(transaction.total_paid);
            document.getElementById('settlementRemaining').textContent = formatCurrency(transaction.remaining_balance);

            // Reset form
            document.getElementById('settlementForm').reset();
            document.getElementById('settlementAmount').max = transaction.remaining_balance;

            // Store transaction data
            modal.dataset.orderId = orderId;
            modal.dataset.remainingBalance = transaction.remaining_balance;

            // Show modal
            modal.classList.remove('hidden');
            modal.classList.add('flex');

        } catch (error) {
            console.error('Error in openSettlementModal:', error);
            showAlert('error', 'Gagal membuka modal pelunasan: ' + error.message);
        }
    }

    function closeSettlementModal() {
        const modal = document.getElementById('modalSettlement');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    // DP History Modal Functions
    async function openDpHistoryModal(orderNumber, orderId) {
        try {
            const transaction = transactionsCache.find(t => t.id == orderId);
            if (!transaction) {
                showAlert('error', 'Data transaksi tidak ditemukan');
                return;
            }

            // Pastikan transaksi adalah DP
            if (transaction.transaction_category !== 'dp') {
                showAlert('error', 'Hanya transaksi DP yang memiliki riwayat pelunasan');
                return;
            }

            const modal = document.getElementById('modalDpHistory');
            
            // Fill order details
            document.getElementById('dpHistoryInvoice').textContent = orderNumber;
            document.getElementById('dpHistoryTotal').textContent = formatCurrency(transaction.total);
            document.getElementById('dpHistoryPaid').textContent = formatCurrency(transaction.total_paid);
            document.getElementById('dpHistoryRemaining').textContent = formatCurrency(transaction.remaining_balance);

            // Show modal
            modal.classList.remove('hidden');
            modal.classList.add('flex');

            // Load settlement history
            await loadDpSettlementHistory(orderId);

        } catch (error) {
            console.error('Error in openDpHistoryModal:', error);
            showAlert('error', 'Gagal membuka modal riwayat: ' + error.message);
        }
    }

    function closeDpHistoryModal() {
        const modal = document.getElementById('modalDpHistory');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        
        // Reset modal content
        document.getElementById('dpHistoryLoading').classList.remove('hidden');
        document.getElementById('dpHistoryContent').classList.add('hidden');
        document.getElementById('dpHistoryEmpty').classList.add('hidden');
        document.getElementById('dpHistorySummary').classList.add('hidden');
    }

    async function loadDpSettlementHistory(orderId) {
        try {
            const token = localStorage.getItem('token');
            if (!token) {
                throw new Error('Token tidak ditemukan');
            }

            const response = await fetch(`/api/orders/${orderId}/settlement-history`, {
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Authorization': `Bearer ${token}`
                }
            });

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || `HTTP Error: ${response.status}`);
            }

            const result = await response.json();
            
            if (result.success) {
                renderDpHistoryTable(result.data);
            } else {
                throw new Error(result.message || 'Gagal memuat riwayat pelunasan');
            }

        } catch (error) {
            console.error('Error loading DP settlement history:', error);
            
            // Hide loading, show empty state
            document.getElementById('dpHistoryLoading').classList.add('hidden');
            document.getElementById('dpHistoryContent').classList.add('hidden');
            document.getElementById('dpHistoryEmpty').classList.remove('hidden');
            
            showAlert('error', 'Gagal memuat riwayat pelunasan: ' + error.message);
        }
    }

    function renderDpHistoryTable(data) {
        const loadingElement = document.getElementById('dpHistoryLoading');
        const contentElement = document.getElementById('dpHistoryContent');
        const emptyElement = document.getElementById('dpHistoryEmpty');
        const summaryElement = document.getElementById('dpHistorySummary');
        const tableBody = document.getElementById('dpHistoryTableBody');

        // Hide loading
        loadingElement.classList.add('hidden');

        // Check if there's data
        if (!data.settlement_history || data.settlement_history.length === 0) {
            emptyElement.classList.remove('hidden');
            return;
        }

        // Render table data
        tableBody.innerHTML = data.settlement_history.map(settlement => `
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 text-sm">${settlement.processed_at}</td>
                <td class="px-4 py-3 text-sm font-medium text-green-600">${formatCurrency(settlement.amount)}</td>
                <td class="px-4 py-3 text-sm">
                    <span class="px-2 py-1 text-xs font-medium rounded-full ${getPaymentMethodClass(settlement.payment_method)}">
                        ${settlement.payment_method.toUpperCase()}
                    </span>
                </td>
                <td class="px-4 py-3 text-sm">${settlement.processed_by}</td>
                <td class="px-4 py-3 text-sm">
                    ${settlement.is_final_payment ? 
                        '<span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">LUNAS</span>' : 
                        '<span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full">SEBAGIAN</span>'
                    }
                </td>
                <td class="px-4 py-3 text-sm">
                    ${settlement.payment_proof_url ? 
                        `<a href="${settlement.payment_proof_url}" target="_blank" class="text-blue-600 hover:text-blue-800 inline-flex items-center">
                            <i data-lucide="external-link" class="w-3 h-3 mr-1"></i>Lihat
                        </a>` : 
                        '<span class="text-gray-400">-</span>'
                    }
                </td>
                <td class="px-4 py-3 text-sm text-gray-600">
                    ${settlement.notes || '<span class="text-gray-400">-</span>'}
                </td>
            </tr>
        `).join('');

        // Show content
        contentElement.classList.remove('hidden');

        // Update summary
        document.getElementById('dpHistoryTotalSettlements').textContent = `${data.total_settlements} kali`;
        document.getElementById('dpHistoryTotalAmount').textContent = formatCurrency(data.total_amount_settled);
        
        const statusText = data.order.is_fully_paid ? 
            '<span class="text-green-600 font-medium">LUNAS</span>' : 
            '<span class="text-red-600 font-medium">BELUM LUNAS</span>';
        document.getElementById('dpHistoryStatus').innerHTML = statusText;
        
        summaryElement.classList.remove('hidden');

        // Refresh Lucide icons
        if (window.lucide) window.lucide.createIcons();
    }

    function getPaymentMethodClass(method) {
        switch (method.toLowerCase()) {
            case 'cash':
                return 'bg-green-100 text-green-800';
            case 'transfer':
                return 'bg-blue-100 text-blue-800';
            case 'qris':
                return 'bg-purple-100 text-purple-800';
            default:
                return 'bg-gray-100 text-gray-800';
        }
    }

    function setSettlementAmount(type) {
        const modal = document.getElementById('modalSettlement');
        const remainingBalance = parseFloat(modal.dataset.remainingBalance || 0);
        const amountInput = document.getElementById('settlementAmount');

        if (type === 'full') {
            amountInput.value = remainingBalance;
        } else if (type === 'half') {
            amountInput.value = (remainingBalance / 2).toFixed(2);
        }
    }

    // Settlement form submission
    document.getElementById('settlementForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const modal = document.getElementById('modalSettlement');
        const orderId = modal.dataset.orderId;
        const remainingBalance = parseFloat(modal.dataset.remainingBalance || 0);

        const formData = new FormData();
        formData.append('amount_received', document.getElementById('settlementAmount').value);
        formData.append('payment_method', document.getElementById('settlementPaymentMethod').value);
        formData.append('notes', document.getElementById('settlementNotes').value);

        const paymentProofFile = document.getElementById('settlementPaymentProof').files[0];
        if (paymentProofFile) {
            formData.append('payment_proof', paymentProofFile);
        }

        // Validasi
        const amount = parseFloat(document.getElementById('settlementAmount').value);
        if (amount <= 0) {
            showAlert('error', 'Jumlah pelunasan harus lebih dari 0');
            return;
        }
        
        if (amount > remainingBalance) {
            showAlert('error', 'Jumlah pelunasan melebihi sisa pembayaran');
            return;
        }

        try {
            const token = localStorage.getItem('token');
            if (!token) {
                window.location.href = '/login';
                return;
            }

            // Show loading state
            const submitButton = modal.querySelector('button[type="submit"]');
            const originalText = submitButton.innerHTML;
            submitButton.innerHTML = `
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Memproses...
            `;
            submitButton.disabled = true;

            const response = await fetch(`/api/orders/${orderId}/settle`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                body: formData
            });

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || 'Gagal memproses pelunasan');
            }

            const result = await response.json();
            showAlert('success', result.message || 'Pelunasan berhasil diproses');
            closeSettlementModal();
            fetchTransactionHistory(); // Refresh data

        } catch (error) {
            console.error('Error processing settlement:', error);
            showAlert('error', error.message);
        } finally {
            // Reset button
            const submitButton = modal.querySelector('button[type="submit"]');
            submitButton.innerHTML = 'Proses Pelunasan';
            submitButton.disabled = false;
        }
    });
    
    // Function to download contract PDF
    function downloadContractPdf(pdfUrl, orderNumber) {
        try {
            // Create a temporary link to download the file
            const link = document.createElement('a');
            link.href = pdfUrl;
            link.download = `Akad_Jual_Beli_${orderNumber}.pdf`;
            link.target = '_blank';
            
            // Append to body, click, then remove
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            
            showAlert('success', 'Download akad jual beli dimulai');
        } catch (error) {
            console.error('Error downloading contract PDF:', error);
            showAlert('error', 'Gagal mendownload akad jual beli');
        }
    }
    
    // Setup polling for transaction history updates
    document.addEventListener('DOMContentLoaded', function() {
        // Start polling for transaction updates every 30 seconds
        if (window.pollingManager) {
            window.pollingManager.start('transactionHistory', async () => {
                console.log('Polling transaction history...');
                await fetchTransactionHistory();
            }, 30000); // 30 seconds interval
        }
    });
    
    // Stop polling when leaving the page
    window.addEventListener('beforeunload', () => {
        if (window.pollingManager) {
            window.pollingManager.stop('transactionHistory');
        }
    });
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
    
    /* Styling untuk scrollable table */
    .overflow-x-auto {
        scrollbar-width: thin;
        scrollbar-color: #CBD5E1 #F1F5F9;
    }
    
    .overflow-x-auto::-webkit-scrollbar {
        height: 8px;
    }
    
    .overflow-x-auto::-webkit-scrollbar-track {
        background: #F1F5F9;
        border-radius: 4px;
    }
    
    .overflow-x-auto::-webkit-scrollbar-thumb {
        background: #CBD5E1;
        border-radius: 4px;
    }
    
    .overflow-x-auto::-webkit-scrollbar-thumb:hover {
        background: #94A3B8;
    }
    
    /* Sticky first column for better UX (optional) */
    .table-sticky-first {
        position: sticky;
        left: 0;
        background: white;
        z-index: 10;
        box-shadow: 2px 0 4px rgba(0,0,0,0.1);
    }
    
    /* Scroll indicator styling */
    #scrollIndicatorRight {
        transition: opacity 0.3s ease;
        background: linear-gradient(to left, rgba(255,255,255,1) 0%, rgba(255,255,255,0.8) 50%, rgba(255,255,255,0) 100%);
    }
    
    #scrollIndicatorRight.hidden {
        opacity: 0;
    }
    
    /* Smooth scrolling for table */
    #tableContainer {
        scroll-behavior: smooth;
    }
</style>

@endsection