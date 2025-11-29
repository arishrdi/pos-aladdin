@extends('layouts.app')

@section('title', 'Riwayat Transaksi')

@section('content')

<!-- Alert Container -->
<div id="alertContainer" class="fixed top-4 right-4 z-50 space-y-3 w-80"></div>

<!-- title Page -->
<div class="mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <h1 class="text-3xl font-bold text-gray-800">Riwayat Transaksi V2</h1>
    </div>
</div>

<!-- Card: Outlet Info -->
<div class="bg-white rounded-md p-4 shadow-md mb-4 flex flex-col md:flex-row md:items-center md:justify-between">
    <div class="mb-3 md:mb-0 flex items-start gap-2">
        <i data-lucide="store" class="w-5 h-5 text-gray-600"></i>
        <div>
            <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">Menampilkan riwayat transaksi: <span
                    class="outlet-name">Loading...</span></h2>
            <p class="text-sm text-gray-600">Data riwayat transaksi untuk <span class=" outlet-name"></span></p>
        </div>
    </div>
</div>

<!-- Table Riwayat Transaksi -->
<div class="bg-white rounded-lg shadow p-4">
    <!-- Header & Filter Section -->
    <div class="mb-6">
        <!-- Page Title -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
            <div>
                <h3 class="text-2xl font-bold text-gray-800 mb-1">Riwayat Transaksi</h3>
                <p class="text-sm text-gray-600">Kelola dan pantau semua transaksi yang telah dilakukan</p>
            </div>
        </div>

        <!-- Filters Section -->
        <div class="bg-gray-50 rounded-lg border border-gray-200 p-4">
            <!-- Search and Date Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
                <!-- Search Invoice -->
                <div class="relative">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i data-lucide="search" class="w-4 h-4 inline mr-1"></i>
                        Cari Invoice
                    </label>
                    <input type="text" id="searchInvoice"
                        class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent bg-white"
                        placeholder="Masukkan nomor invoice..." />
                    <span class="absolute bottom-3 left-3 text-gray-400">
                        <i data-lucide="search" class="w-4 h-4"></i>
                    </span>
                </div>

                <!-- Date Range -->
                <div class="relative">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i data-lucide="calendar" class="w-4 h-4 inline mr-1"></i>
                        Rentang Tanggal
                    </label>
                    <input id="transDateInput" type="text"
                        class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent bg-white"
                        placeholder="Pilih rentang tanggal..." />
                    <span class="absolute bottom-3 left-3 text-gray-400">
                        <i data-lucide="calendar" class="w-4 h-4"></i>
                    </span>
                </div>
            </div>

            <!-- Filter Controls Section -->
            <div class="border-t border-gray-200 pt-4">
                <div class="flex items-center justify-between mb-3">
                    <h4 class="text-sm font-medium text-gray-700 flex items-center">
                        <i data-lucide="filter" class="w-4 h-4 mr-1"></i>
                        Filter Transaksi
                    </h4>
                    <button onclick="clearAllFilters()" class="text-xs text-green-600 hover:text-green-700 font-medium flex items-center">
                        <i data-lucide="rotate-ccw" class="w-3 h-3 mr-1"></i>
                        Reset Filter
                    </button>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-3">
                    <!-- Status Filter -->
                    <div class="relative">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Status Transaksi</label>
                        <select id="statusFilter"
                            class="w-full pl-3 pr-8 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent bg-white">
                            <option value="">Semua Status</option>
                            <option value="pending">Menunggu</option>
                            <option value="completed">Selesai</option>
                            <option value="cancelled">Dibatalkan</option>
                        </select>
                    </div>

                    <!-- Approval Filter -->
                    <div class="relative">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Status Approval</label>
                        <select id="approvalFilter"
                            class="w-full pl-3 pr-8 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent bg-white">
                            <option value="">Semua Approval</option>
                            <option value="pending">Menunggu Approval</option>
                            <option value="approved">Disetujui</option>
                            <option value="rejected">Ditolak</option>
                            <option value="edit_pending">Edit Pending</option>
                        </select>
                    </div>

                    <!-- Category Filter -->
                    <div class="relative">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Kategori Pembayaran</label>
                        <select id="categoryFilter"
                            class="w-full pl-3 pr-8 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent bg-white">
                            <option value="">Semua Kategori</option>
                            <option value="lunas">Lunas</option>
                            <option value="dp">DP (Uang Muka)</option>
                        </select>
                    </div>

                    <!-- DP Status Filter -->
                    <div class="relative">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Status DP</label>
                        <select id="dpStatusFilter"
                            class="w-full pl-3 pr-8 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent bg-white">
                            <option value="">Semua Status DP</option>
                            <option value="pending">DP Belum Lunas</option>
                            <option value="completed">DP Sudah Lunas</option>
                        </select>
                    </div>

                    <!-- Outlet Filter -->
                    <div class="relative">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Outlet</label>
                        <select id="outletFilter"
                            class="w-full pl-3 pr-8 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent bg-white">
                            <option value="">Outlet Saat Ini</option>
                            <option value="all">Semua Outlet</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Content -->
    <div class="relative">
        <!-- Scroll indicator shadows -->
        <div class="absolute top-0 right-0 bottom-0 w-8 bg-gradient-to-l from-white via-white to-transparent pointer-events-none z-10 rounded-r-lg"
            id="scrollIndicatorRight">
            <!-- Scroll hint icon -->
            <div class="absolute top-1/2 right-2 transform -translate-y-1/2">
                <div class="flex items-center justify-center w-4 h-4 text-gray-400"
                    title="Scroll horizontal untuk melihat lebih banyak kolom">
                    <i data-lucide="chevrons-right" class="w-3 h-3"></i>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto border border-gray-200 rounded-lg" id="tableContainer">
            <table class="min-w-full text-sm border-collapse">
                <thead class="text-left text-gray-700 border-b-2 bg-gray-50 sticky top-0">
                    <tr>
                        <!-- Basic Info -->
                        <th class="px-2 py-3 font-bold min-w-[50px] border-r">NO</th>
                        <th class="px-2 py-3 font-bold min-w-[100px] border-r">BULAN CUT OFF</th>

                        <!-- Lead Info -->
                        <th class="px-2 py-3 font-bold min-w-[120px] border-r bg-blue-50">TANGGAL LEADS MASUK</th>
                        <th class="px-2 py-3 font-bold min-w-[120px] border-r bg-blue-50">CHANNEL MARKETING</th>
                        <th class="px-2 py-3 font-bold min-w-[150px] border-r bg-blue-50">LEADS</th>

                        <!-- Order Info -->
                        <th class="px-2 py-3 font-bold min-w-[120px] border-r">TANGGAL ORDER</th>
                        <th class="px-2 py-3 font-bold min-w-[150px] border-r">NOMER FAKTUR</th>
                        <th class="px-2 py-3 font-bold min-w-[150px] border-r">PEMESAN</th>
                        <th class="px-2 py-3 font-bold min-w-[150px] border-r">NAMA MASJID</th>
                        <th class="px-2 py-3 font-bold min-w-[200px] border-r">ALAMAT</th>
                        <th class="px-2 py-3 font-bold min-w-[120px] border-r">CONTACT PERSON</th>

                        <!-- Product Info -->
                        <th class="px-2 py-3 font-bold min-w-[200px] border-r bg-green-50">PRODUK</th>
                        <th class="px-2 py-3 font-bold min-w-[80px] border-r bg-green-50">UNIT (m)</th>
                        <th class="px-2 py-3 font-bold min-w-[120px] border-r bg-green-50">HARGA SATUAN/meter</th>
                        <th class="px-2 py-3 font-bold min-w-[120px] border-r bg-green-50">TOTAL HARGA JUAL</th>
                        <th class="px-2 py-3 font-bold min-w-[120px] border-r bg-green-50">DISCOUNT/POTONGAN</th>
                        <th class="px-2 py-3 font-bold min-w-[120px] border-r bg-green-50">TOTAL PEMBAYARAN</th>
                        <th class="px-2 py-3 font-bold min-w-[150px] border-r bg-green-50">BONUS</th>

                        <!-- Payment Info -->
                        <th class="px-2 py-3 font-bold min-w-[100px] border-r bg-yellow-50">UANG MUKA I - TGL</th>
                        <th class="px-2 py-3 font-bold min-w-[120px] border-r bg-yellow-50">UANG MUKA I - Nominal</th>
                        <th class="px-2 py-3 font-bold min-w-[100px] border-r bg-yellow-50">UANG MUKA I - Bank</th>

                        <th class="px-2 py-3 font-bold min-w-[100px] border-r bg-yellow-50">UANG MUKA II - TGL</th>
                        <th class="px-2 py-3 font-bold min-w-[120px] border-r bg-yellow-50">UANG MUKA II - Nominal</th>
                        <th class="px-2 py-3 font-bold min-w-[100px] border-r bg-yellow-50">UANG MUKA II - Bank</th>

                        <th class="px-2 py-3 font-bold min-w-[100px] border-r bg-yellow-50">PELUNASAN - TGL</th>
                        <th class="px-2 py-3 font-bold min-w-[120px] border-r bg-yellow-50">PELUNASAN - Nominal</th>
                        <th class="px-2 py-3 font-bold min-w-[100px] border-r bg-yellow-50">PELUNASAN - Bank</th>

                        <!-- Additional Info -->
                        <th class="px-2 py-3 font-bold min-w-[150px] border-r">TAMBAHAN BIAYA KIRIM</th>
                        <th class="px-2 py-3 font-bold min-w-[150px] border-r">TAMBAHAN BIAYA PEMASANGAN</th>
                        <th class="px-2 py-3 font-bold min-w-[200px] border-r">KET.</th>
                        <th class="px-2 py-3 font-bold min-w-[120px] border-r">TAGIHAN</th>
                        <th class="px-2 py-3 font-bold min-w-[120px] border-r">Total Pendapatan</th>
                        <th class="px-2 py-3 font-bold min-w-[120px]">CABANG</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 divide-y" id="transactionTableBody">
                    <!-- Data akan diisi secara dinamis -->
                    <tr>
                        <td colspan="35" class="py-8 text-center">
                            <div class="flex flex-col items-center justify-center gap-2 mx-auto">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="animate-spin text-green-500">
                                    <path d="M21 12a9 9 0 1 1-6.219-8.56" />
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
                    <p class="text-gray-500">No Order</p>
                    <p id="detailOrderId" class="font-medium"></p>
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
                <div id="financeApprovalRow" class="hidden">
                    <p class="text-gray-500">Approval Keuangan</p>
                    <div id="detailFinanceApproval" class="font-medium">
                        <div id="financeApproverName" class="text-sm"></div>
                        <div id="financeApprovalDate" class="text-xs text-gray-600"></div>
                    </div>
                </div>
                <div id="operationalApprovalRow" class="hidden">
                    <p class="text-gray-500">Approval Operasional</p>
                    <div id="detailOperationalApproval" class="font-medium">
                        <div id="operationalApproverName" class="text-sm"></div>
                        <div id="operationalApprovalDate" class="text-xs text-gray-600"></div>
                    </div>
                </div>
                <div id="financeRejectionRow" class="hidden">
                    <p class="text-gray-500">Penolakan Keuangan</p>
                    <div id="detailFinanceRejection" class="font-medium">
                        <div id="financeRejectorName" class="text-sm text-red-600"></div>
                        <div id="financeRejectionDate" class="text-xs text-gray-600"></div>
                        <div id="financeRejectionReasonDisplay" class="text-xs text-red-700 mt-1 p-2 bg-red-50 rounded border border-red-200"></div>
                    </div>
                </div>
                <div id="operationalRejectionRow" class="hidden">
                    <p class="text-gray-500">Penolakan Operasional</p>
                    <div id="detailOperationalRejection" class="font-medium">
                        <div id="operationalRejectorName" class="text-sm text-red-600"></div>
                        <div id="operationalRejectionDate" class="text-xs text-gray-600"></div>
                        <div id="operationalRejectionReasonDisplay" class="text-xs text-red-700 mt-1 p-2 bg-red-50 rounded border border-red-200"></div>
                    </div>
                </div>
                <div id="editRequestRow" class="hidden">
                    <p class="text-gray-500">Permintaan Edit</p>
                    <div id="detailEditRequest" class="font-medium">
                        <div id="editRequestStatus" class="text-sm"></div>
                        <div id="editRequestInfo" class="text-xs text-gray-600"></div>
                        <button id="reviewEditBtn" onclick="openEditApprovalModal()" 
                            class="mt-1 text-xs text-blue-600 hover:text-blue-800 underline hidden">
                            Review Edit Request
                        </button>
                    </div>
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
                    <p class="text-gray-500">Akad Jual Beli/Sketsa Masjid</p>
                    <div id="detailContractPdf" class="font-medium flex gap-2">
                        <button
                            onclick="previewContractPdf(window.currentTransactionDetail.contract_pdf_url, window.currentTransactionDetail.order_number)"
                            class="inline-flex items-center px-3 py-1 text-sm bg-blue-100 text-blue-700 hover:bg-blue-200 rounded transition-colors">
                            <i class="fas fa-eye mr-2"></i>
                            Preview
                        </button>
                        <button
                            onclick="downloadContractPdf(window.currentTransactionDetail.contract_pdf_url, window.currentTransactionDetail.order_number)"
                            class="inline-flex items-center px-3 py-1 text-sm bg-orange-100 text-orange-700 hover:bg-orange-200 rounded transition-colors">
                            <i class="fas fa-download mr-2"></i>
                            Download
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
                        <a id="detailPaymentProofLink" href="#"
                            class="font-medium ml-1 text-green-600 hover:text-green-800">Lihat Bukti</a>
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
                        <div id="detailInstallationNotes"
                            class="font-medium mt-1 p-2 bg-white rounded border text-gray-700"></div>
                    </div>
                    <div id="leadsCabangInfo" class="hidden">
                        <span class="text-gray-500">Leads Cabang:</span>
                        <span id="detailLeadsCabang" class="font-medium ml-1"></span>
                    </div>
                    <div id="dealMakerInfo" class="hidden">
                        <span class="text-gray-500">Deal Maker:</span>
                        <span id="detailDealMaker" class="font-medium ml-1"></span>
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
                        <select id="refundReason"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
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
                        <textarea id="customReason" rows="2"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500"
                            placeholder="Jelaskan alasan refund secara detail..."></textarea>
                    </div>

                    <div class="mt-3 p-3 bg-yellow-50 rounded-md">
                        <p class="text-xs text-yellow-700"><strong>Perhatian:</strong> Refund tidak dapat dibatalkan.
                            Pastikan produk telah dikembalikan dan semua persyaratan refund terpenuhi.</p>
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button onclick="closeRefundModal()" type="button"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none">
                        Batal
                    </button>
                    <button onclick="processRefund()" type="button"
                        class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md shadow-sm hover:bg-red-700 focus:outline-none">
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
                        <textarea id="approvalNotes" rows="3"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="Tambahkan catatan approval..."></textarea>
                    </div>
                </div>
                <div class="mt-4 flex justify-end gap-3">
                    <button onclick="closeApproveModal()" type="button"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none">
                        Batal
                    </button>
                    <button onclick="processApprove()" type="button"
                        class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-md shadow-sm hover:bg-green-700 focus:outline-none">
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
                        <textarea id="rejectionReason" rows="3"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500"
                            placeholder="Jelaskan alasan penolakan..." required></textarea>
                    </div>
                </div>
                <div class="mt-4 flex justify-end gap-3">
                    <button onclick="closeRejectModal()" type="button"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none">
                        Batal
                    </button>
                    <button onclick="processReject()" type="button"
                        class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md shadow-sm hover:bg-red-700 focus:outline-none">
                        Tolak Transaksi
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Payment Proof -->
<div id="modalPaymentProof"
    class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Bukti Pembayaran</h3>
            <button onclick="closePaymentProofModal()" class="text-gray-500 hover:text-gray-700">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <div class="text-center">
            <img id="paymentProofImage" src="" alt="Bukti Pembayaran"
                class="max-w-full max-h-96 mx-auto rounded-lg shadow-md">
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
                <input type="text" id="settlementAmount"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                    placeholder="Masukkan jumlah pelunasan" required>
                <input type="hidden" id="settlementAmountRaw" name="amount_received">
                <div class="mt-2 flex gap-2">
                    <button type="button" onclick="setSettlementAmount('full')"
                        class="px-3 py-1 bg-green-100 text-green-700 rounded text-sm hover:bg-green-200">Lunas
                        Penuh</button>
                    <button type="button" onclick="setSettlementAmount('half')"
                        class="px-3 py-1 bg-blue-100 text-blue-700 rounded text-sm hover:bg-blue-200">Setengah</button>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Metode Pembayaran *</label>
                <select id="settlementPaymentMethod"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                    required>
                    <option value="">Pilih metode pembayaran</option>
                    <option value="cash">Tunai</option>
                    <option value="transfer">Transfer Bank</option>
                    <option value="qris">QRIS</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Bukti Pembayaran</label>
                <input type="file" id="settlementPaymentProof" accept="image/*,application/pdf"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, PDF. Maksimal 5MB.</p>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Catatan (Opsional)</label>
                <textarea id="settlementNotes" rows="3"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                    placeholder="Tambahkan catatan jika diperlukan"></textarea>
            </div>

            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeSettlementModal()"
                    class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
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
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tanggal</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Jumlah</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Metode</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Petugas</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Bukti</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Catatan</th>
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
            <button onclick="closeDpHistoryModal()"
                class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                Tutup
            </button>
        </div>
    </div>
</div>

<!-- Modal Riwayat Edit Transaksi -->
<div id="modalEditHistory" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-6xl mx-4 max-h-[90vh] overflow-hidden">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Riwayat Edit Transaksi</h3>
                    <p class="text-sm text-gray-600">
                        Order: <span id="editHistoryOrderNumber" class="font-mono font-bold"></span>
                    </p>
                </div>
                <button onclick="closeEditHistoryModal()" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>
        </div>

        <!-- Content -->
        <div class="px-6 py-4 max-h-96 overflow-y-auto">
            <div id="editHistoryLoading" class="text-center py-8">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-500"></div>
                <p class="mt-2 text-gray-600">Memuat riwayat edit...</p>
            </div>

            <div id="editHistoryContent" class="hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe Edit</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alasan</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Diminta oleh</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Selisih Total</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Finance</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Operational</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Diterapkan</th>
                            </tr>
                        </thead>
                        <tbody id="editHistoryTableBody" class="bg-white divide-y divide-gray-200">
                            <!-- Data will be populated here -->
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="editHistoryEmpty" class="hidden text-center py-8">
                <i data-lucide="edit-3" class="w-12 h-12 text-gray-400 mx-auto mb-3"></i>
                <p class="text-gray-600">Belum ada riwayat edit untuk transaksi ini.</p>
            </div>
        </div>

        <!-- Footer -->
        <div class="px-6 py-4 border-t border-gray-200 flex justify-end">
            <button onclick="closeEditHistoryModal()"
                class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                Tutup
            </button>
        </div>
    </div>
</div>

<!-- Finance Action Selection Modal -->
<div id="financeActionModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg w-full max-w-md">
        <div class="p-6">
            <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-blue-100 rounded-full">
                <i data-lucide="wallet" class="w-6 h-6 text-blue-600"></i>
            </div>
            <h3 class="text-lg font-semibold text-center mb-2">Aksi Keuangan</h3>
            <p class="text-gray-600 text-center mb-6">
                Silakan pilih tindakan yang ingin dilakukan untuk transaksi ini dari sisi Keuangan.
            </p>
            <div class="space-y-3">
                <button onclick="handleFinanceAction('approve')"
                    class="w-full inline-flex justify-center items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                    <i data-lucide="check-circle" class="w-4 h-4 mr-2"></i>
                    Setujui Transaksi
                </button>
                <button onclick="handleFinanceAction('reject')"
                    class="w-full inline-flex justify-center items-center px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors">
                    <i data-lucide="x-circle" class="w-4 h-4 mr-2"></i>
                    Tolak Transaksi
                </button>
            </div>
            <button onclick="closeFinanceActionModal()"
                class="w-full mt-4 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                Batal
            </button>
        </div>
    </div>
</div>

<!-- Operational Action Selection Modal -->
<div id="operationalActionModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg w-full max-w-md">
        <div class="p-6">
            <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-purple-100 rounded-full">
                <i data-lucide="settings" class="w-6 h-6 text-purple-600"></i>
            </div>
            <h3 class="text-lg font-semibold text-center mb-2">Aksi Operasional</h3>
            <p class="text-gray-600 text-center mb-6">
                Pilih tindakan yang sesuai untuk transaksi ini dari sisi Operasional.
            </p>
            <div class="space-y-3">
                <button onclick="handleOperationalAction('approve')"
                    class="w-full inline-flex justify-center items-center px-4 py-2 text-sm font-medium text-white bg-purple-600 rounded-lg hover:bg-purple-700 transition-colors">
                    <i data-lucide="check-circle" class="w-4 h-4 mr-2"></i>
                    Setujui Transaksi
                </button>
                <button onclick="handleOperationalAction('reject')"
                    class="w-full inline-flex justify-center items-center px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors">
                    <i data-lucide="x-circle" class="w-4 h-4 mr-2"></i>
                    Tolak Transaksi
                </button>
            </div>
            <button onclick="closeOperationalActionModal()"
                class="w-full mt-4 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                Batal
            </button>
        </div>
    </div>
</div>

<!-- Finance Approval Confirmation Modal -->
<div id="financeApprovalModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg w-full max-w-md">
        <div class="p-6">
            <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-blue-100 rounded-full">
                <i data-lucide="banknote" class="w-6 h-6 text-blue-600"></i>
            </div>
            <h3 class="text-lg font-semibold text-center mb-2">Konfirmasi Approval Keuangan</h3>
            <p class="text-gray-600 text-center mb-6">
                Apakah Anda yakin ingin menyetujui transaksi ini dari sisi Keuangan?
            </p>
            <div class="flex gap-3">
                <button onclick="closeFinanceApprovalModal()" 
                    class="flex-1 px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                    Batal
                </button>
                <button onclick="confirmFinanceApproval()" 
                    class="flex-1 px-4 py-2 text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                    Ya, Setujui
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Operational Approval Confirmation Modal -->
<div id="operationalApprovalModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg w-full max-w-md">
        <div class="p-6">
            <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-purple-100 rounded-full">
                <i data-lucide="settings" class="w-6 h-6 text-purple-600"></i>
            </div>
            <h3 class="text-lg font-semibold text-center mb-2">Konfirmasi Approval Operasional</h3>
            <p class="text-gray-600 text-center mb-6">
                Apakah Anda yakin ingin menyetujui transaksi ini dari sisi Operasional?
            </p>
            <div class="flex gap-3">
                <button onclick="closeOperationalApprovalModal()"
                    class="flex-1 px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                    Batal
                </button>
                <button onclick="confirmOperationalApproval()"
                    class="flex-1 px-4 py-2 text-white bg-purple-600 rounded-lg hover:bg-purple-700">
                    Ya, Setujui
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Finance Rejection Modal -->
<div id="financeRejectionModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg w-full max-w-md">
        <div class="p-6">
            <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-red-100 rounded-full">
                <i data-lucide="x-circle" class="w-6 h-6 text-red-600"></i>
            </div>
            <h3 class="text-lg font-semibold text-center mb-2">Tolak Transaksi (Keuangan)</h3>
            <p class="text-gray-600 text-center mb-4">
                Silakan masukkan alasan penolakan transaksi dari sisi Keuangan
            </p>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Alasan Penolakan <span class="text-red-500">*</span></label>
                <textarea id="financeRejectionReason"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                    rows="4"
                    placeholder="Contoh: Metode pembayaran tidak sesuai, bukti transfer tidak valid, dll."
                    maxlength="1000"></textarea>
                <p class="text-xs text-gray-500 mt-1">Maksimal 1000 karakter</p>
            </div>
            <div class="flex gap-3">
                <button onclick="closeFinanceRejectionModal()"
                    class="flex-1 px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                    Batal
                </button>
                <button onclick="confirmFinanceRejection()"
                    class="flex-1 px-4 py-2 text-white bg-red-600 rounded-lg hover:bg-red-700">
                    Ya, Tolak
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Operational Rejection Modal -->
<div id="operationalRejectionModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg w-full max-w-md">
        <div class="p-6">
            <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-red-100 rounded-full">
                <i data-lucide="x-circle" class="w-6 h-6 text-red-600"></i>
            </div>
            <h3 class="text-lg font-semibold text-center mb-2">Tolak Transaksi (Operasional)</h3>
            <p class="text-gray-600 text-center mb-4">
                Silakan masukkan alasan penolakan transaksi dari sisi Operasional
            </p>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Alasan Penolakan <span class="text-red-500">*</span></label>
                <textarea id="operationalRejectionReason"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                    rows="4"
                    placeholder="Contoh: Data produk tidak lengkap, informasi pengiriman salah, dll."
                    maxlength="1000"></textarea>
                <p class="text-xs text-gray-500 mt-1">Maksimal 1000 karakter</p>
            </div>
            <div class="flex gap-3">
                <button onclick="closeOperationalRejectionModal()"
                    class="flex-1 px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                    Batal
                </button>
                <button onclick="confirmOperationalRejection()"
                    class="flex-1 px-4 py-2 text-white bg-red-600 rounded-lg hover:bg-red-700">
                    Ya, Tolak
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Transaction Approval Modal -->
<div id="editApprovalModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg w-full max-w-4xl max-h-[90vh] overflow-hidden">
        <div class="p-6 border-b">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold">Review Edit Transaksi</h3>
                <button onclick="closeEditApprovalModal()" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>
        </div>
        
        <div class="p-6 overflow-y-auto max-h-[calc(90vh-120px)]">
            <!-- Edit Request Info -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h4 class="font-medium mb-3 text-gray-800">Informasi Permintaan</h4>
                    <div class="space-y-2 text-sm">
                        <div><span class="text-gray-600">Invoice:</span> <span id="editInvoiceNumber" class="font-medium"></span></div>
                        <div><span class="text-gray-600">Diminta oleh:</span> <span id="editRequester" class="font-medium"></span></div>
                        <div><span class="text-gray-600">Tanggal Permintaan:</span> <span id="editRequestDate" class="font-medium"></span></div>
                        <div><span class="text-gray-600">Tipe Edit:</span> <span id="editType" class="font-medium"></span></div>
                        <div><span class="text-gray-600">Alasan:</span> <span id="editReason" class="font-medium"></span></div>
                        <div id="editNotesDiv" class="hidden"><span class="text-gray-600">Catatan:</span> <span id="editNotes" class="font-medium"></span></div>
                    </div>
                </div>
                
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h4 class="font-medium mb-3 text-gray-800">Perubahan Finansial</h4>
                    <div class="space-y-2 text-sm">
                        <div><span class="text-gray-600">Total Asli:</span> <span id="editOriginalTotal" class="font-medium"></span></div>
                        <div><span class="text-gray-600">Total Baru:</span> <span id="editNewTotal" class="font-medium"></span></div>
                        <div><span class="text-gray-600">Selisih:</span> <span id="editTotalDifference" class="font-medium"></span></div>
                        <div><span class="text-gray-600">Status Approval:</span> <span id="editApprovalStatus" class="font-medium"></span></div>
                    </div>
                </div>
            </div>
            
            <!-- Approval Information -->
            <div id="editApprovalInfo" class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6 hidden">
                <div class="bg-blue-50 p-4 rounded-lg">
                    <h4 class="font-medium mb-3 text-blue-800">Approval Keuangan</h4>
                    <div class="space-y-2 text-sm">
                        <div id="editFinanceApprover" class="hidden"><span class="text-gray-600">Disetujui oleh:</span> <span id="editFinanceApproverName" class="font-medium"></span></div>
                        <div id="editFinanceApprovalDate" class="hidden"><span class="text-gray-600">Tanggal:</span> <span id="editFinanceApprovalTime" class="font-medium"></span></div>
                        <div id="editFinanceNotApproved" class="text-yellow-600 font-medium">Belum disetujui</div>
                    </div>
                </div>
                
                <div class="bg-purple-50 p-4 rounded-lg">
                    <h4 class="font-medium mb-3 text-purple-800">Approval Operasional</h4>
                    <div class="space-y-2 text-sm">
                        <div id="editOperationalApprover" class="hidden"><span class="text-gray-600">Disetujui oleh:</span> <span id="editOperationalApproverName" class="font-medium"></span></div>
                        <div id="editOperationalApprovalDate" class="hidden"><span class="text-gray-600">Tanggal:</span> <span id="editOperationalApprovalTime" class="font-medium"></span></div>
                        <div id="editOperationalNotApproved" class="text-yellow-600 font-medium">Belum disetujui</div>
                    </div>
                </div>
            </div>
            
            <!-- Items Comparison -->
            <div class="mb-6">
                <h4 class="font-medium mb-3 text-gray-800">Perbandingan Item</h4>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <!-- Original Items -->
                    <div>
                        <h5 class="text-sm font-medium text-gray-700 mb-2">Item Asli</h5>
                        <div class="border rounded-lg overflow-hidden">
                            <table class="w-full text-xs">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-2 py-1 text-left">Produk</th>
                                        <th class="px-2 py-1 text-center">Qty</th>
                                        <th class="px-2 py-1 text-right">Harga</th>
                                        <th class="px-2 py-1 text-right">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody id="originalItemsList">
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- New Items -->
                    <div>
                        <h5 class="text-sm font-medium text-gray-700 mb-2">Item Baru</h5>
                        <div class="border rounded-lg overflow-hidden">
                            <table class="w-full text-xs">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-2 py-1 text-left">Produk</th>
                                        <th class="px-2 py-1 text-center">Qty</th>
                                        <th class="px-2 py-1 text-right">Harga</th>
                                        <th class="px-2 py-1 text-right">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody id="newItemsList">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Approval Actions -->
            <div id="editApprovalActions" class="flex gap-3 justify-end pt-4 border-t">
                <button onclick="closeEditApprovalModal()" 
                    class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                    Tutup
                </button>
                <button id="rejectEditBtn" onclick="rejectEditRequest()" 
                    class="px-4 py-2 text-white bg-red-600 rounded-lg hover:bg-red-700">
                    Tolak Edit
                </button>
                <button id="approveEditFinanceBtn" onclick="approveEditFinance()" 
                    class="px-4 py-2 text-white bg-blue-600 rounded-lg hover:bg-blue-700 hidden">
                    Approve Keuangan
                </button>
                <button id="approveEditOperationalBtn" onclick="approveEditOperational()" 
                    class="px-4 py-2 text-white bg-purple-600 rounded-lg hover:bg-purple-700 hidden">
                    Approve Operasional
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Reject Edit Modal -->
<div id="rejectEditModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg w-full max-w-md">
        <div class="p-6">
            <h3 class="text-lg font-semibold mb-4">Tolak Edit Transaksi</h3>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Alasan Penolakan</label>
                <textarea id="rejectEditReason" rows="3" 
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500"
                    placeholder="Jelaskan alasan penolakan edit..."></textarea>
            </div>
            <div class="flex gap-3 justify-end">
                <button onclick="closeRejectEditModal()" 
                    class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                    Batal
                </button>
                <button onclick="confirmRejectEdit()" 
                    class="px-4 py-2 text-white bg-red-600 rounded-lg hover:bg-red-700">
                    Tolak Edit
                </button>
            </div>
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

        // Inisialisasi flatpickr untuk filter tanggal range
        flatpickr("#transDateInput", {
            mode: "range",
            dateFormat: "d/m/Y",
            maxDate: "today",
            locale: "id",
            onChange: function(selectedDates, dateStr) {
                if (selectedDates.length >= 1) {
                    // For range mode, pass start and end dates
                    const startDate = formatDateForAPI(selectedDates[0]);
                    const endDate = selectedDates.length === 2 ? formatDateForAPI(selectedDates[1]) : startDate;
                    const searchValue = document.getElementById('searchInvoice').value;
                    fetchTransactionHistory(startDate, endDate, searchValue);
                } else {
                    // Jika tidak ada tanggal terpilih, tampilkan semua transaksi
                    const searchValue = document.getElementById('searchInvoice').value;
                    fetchTransactionHistory(null, null, searchValue);
                }
            }
        });

        // Load data awal dan handle URL parameters
        initializeFromUrlParams();
        const initialSearch = document.getElementById('searchInvoice').value;
        fetchTransactionHistory(null, null, initialSearch);
        
        // Pencarian dan Filter
        document.getElementById('searchInvoice').addEventListener('input', function() {
            const searchValue = this.value;
            const datePicker = document.getElementById('transDateInput');
            let startDate = null, endDate = null;
            
            if (datePicker && datePicker._flatpickr && datePicker._flatpickr.selectedDates.length > 0) {
                startDate = formatDateForAPI(datePicker._flatpickr.selectedDates[0]);
                if (datePicker._flatpickr.selectedDates.length === 2) {
                    endDate = formatDateForAPI(datePicker._flatpickr.selectedDates[1]);
                } else {
                    endDate = startDate;
                }
            }
            
            // Update URL parameters
            updateUrlParams();
            // Fetch data from backend with search parameter
            fetchTransactionHistory(startDate, endDate, searchValue);
        });
        document.getElementById('statusFilter').addEventListener('change', applyFilters);
        document.getElementById('approvalFilter').addEventListener('change', applyFilters);
        document.getElementById('categoryFilter').addEventListener('change', applyFilters);
        document.getElementById('dpStatusFilter').addEventListener('change', applyFilters);
        document.getElementById('outletFilter').addEventListener('change', function() {
            // When outlet filter changes, refresh data
            const datePicker = document.getElementById('transDateInput');
            let startDate = null, endDate = null;
            
            if (datePicker && datePicker._flatpickr && datePicker._flatpickr.selectedDates.length > 0) {
                startDate = formatDateForAPI(datePicker._flatpickr.selectedDates[0]);
                if (datePicker._flatpickr.selectedDates.length === 2) {
                    endDate = formatDateForAPI(datePicker._flatpickr.selectedDates[1]);
                }
            }
            
            fetchTransactionHistory(startDate, endDate);
        });

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

    // Function to initialize form fields from URL parameters
    function initializeFromUrlParams() {
        const urlParams = new URLSearchParams(window.location.search);
        const searchParam = urlParams.get('search');
        
        if (searchParam) {
            const searchInput = document.getElementById('searchInvoice');
            if (searchInput) {
                searchInput.value = searchParam;
            }
        }
    }

    // Function to update URL parameters when searching
    function updateUrlParams() {
        const searchValue = document.getElementById('searchInvoice').value;
        const currentUrl = new URL(window.location);
        
        if (searchValue.trim()) {
            currentUrl.searchParams.set('search', searchValue.trim());
        } else {
            currentUrl.searchParams.delete('search');
        }
        
        // Update URL without page reload
        window.history.replaceState({}, '', currentUrl);
    }

    // Helper function to refresh transaction history with current state
    function refreshTransactionHistory() {
        const searchValue = document.getElementById('searchInvoice').value;
        const datePicker = document.getElementById('transDateInput');
        let startDate = null, endDate = null;
        
        if (datePicker && datePicker._flatpickr && datePicker._flatpickr.selectedDates.length > 0) {
            startDate = formatDateForAPI(datePicker._flatpickr.selectedDates[0]);
            if (datePicker._flatpickr.selectedDates.length === 2) {
                endDate = formatDateForAPI(datePicker._flatpickr.selectedDates[1]);
            } else {
                endDate = startDate;
            }
        }
        
        fetchTransactionHistory(startDate, endDate, searchValue);
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
                const searchValue = document.getElementById('searchInvoice').value;
                if (date) {
                    fetchTransactionHistory(date, date, searchValue);
                } else {
                    fetchTransactionHistory(null, null, searchValue);
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
                    // Update history after a short delay to allow your existing code to complete
                    setTimeout(() => {
                        const datePicker = document.getElementById('transDateInput');
                        let date = null;
                        if (datePicker && datePicker._flatpickr && datePicker._flatpickr.selectedDates.length > 0) {
                            date = formatDateForAPI(datePicker._flatpickr.selectedDates[0]);
                        }
                        
                        const searchValue = document.getElementById('searchInvoice').value;
                        if (date) {
                            fetchTransactionHistory(date, date, searchValue);
                        } else {
                            fetchTransactionHistory(null, null, searchValue);
                        }
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
    async function fetchTransactionHistory(startDate = null, endDate = null, search = null) {
        try {
            const token = localStorage.getItem('token');
            if (!token) {
                window.location.href = '/login';
                return;
            }

            // Check outlet filter selection
            const outletFilterValue = document.getElementById('outletFilter').value;
            
            // Format parameter tanggal untuk date range
            const params = new URLSearchParams();
            if (startDate) {
                params.append('date_from', startDate);
                params.append('date_to', endDate || startDate);
            } else {
                const currentDate = new Date().toISOString().split('T')[0];
                params.append('date_from', currentDate);
                params.append('date_to', currentDate);
            }
            
            // Add outlet parameter - send 'all' for all outlets, specific ID for single outlet
            if (outletFilterValue === 'all') {
                params.append('outlet_id', 'all');
            } else {
                const outletId = getSelectedOutletId();
                params.append('outlet_id', outletId);
            }
            
            // Add search parameter if provided
            if (search && search.trim()) {
                params.append('search', search.trim());
            }
            
            // Fetch data dari endpoint dengan token authorization
            const response = await fetch(`/api/orders/history-v2?${params.toString()}`, {
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

        // Update outlet info display
        const outletFilterValue = document.getElementById('outletFilter')?.value;
        const outletElements = document.querySelectorAll('.outlet-name');

        if (outletFilterValue === 'all') {
            outletElements.forEach(el => {
                el.textContent = 'Semua Outlet';
            });
        }
        
        if (!transactions || transactions.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="35" class="py-4 text-center text-gray-500">
                        Tidak ada transaksi pada tanggal ini.
                    </td>
                </tr>
            `;
            return;
        }

        transactions.forEach((transaction, index) => {
            console.log('Processing transaction:', {id: transaction.id, order_number: transaction.order_number}); // Debug log
            const row = document.createElement('tr');
            row.className = 'border-b hover:bg-gray-50';
            row.dataset.transactionId = transaction.id;

            // Helper functions
            const parseDateString = (dateString) => {
                if (!dateString) return null;

                // Remove time part if exists
                const datePart = dateString.split(' ')[0];

                // Check if format is Y-m-d (from Leads API: "2024-09-23")
                if (datePart.includes('-')) {
                    const parts = datePart.split('-');
                    if (parts.length === 3) {
                        const year = parseInt(parts[0], 10);
                        const month = parseInt(parts[1], 10) - 1; // Month is 0-indexed in JS
                        const day = parseInt(parts[2], 10);
                        return new Date(year, month, day);
                    }
                }

                // Check if format is d/m/Y (from backend: "23/09/2024")
                if (datePart.includes('/')) {
                    const parts = datePart.split('/');
                    if (parts.length === 3) {
                        const day = parseInt(parts[0], 10);
                        const month = parseInt(parts[1], 10) - 1; // Month is 0-indexed in JS
                        const year = parseInt(parts[2], 10);
                        return new Date(year, month, day);
                    }
                }

                return null;
            };

            const formatDate = (dateString) => {
                if (!dateString) return '-';
                const date = parseDateString(dateString);
                if (!date || isNaN(date.getTime())) return '-';

                const day = String(date.getDate()).padStart(2, '0');
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const year = date.getFullYear();
                return `${day}/${month}/${year}`;
            };

            const getMonthYear = (dateString) => {
                if (!dateString) return '-';
                const date = parseDateString(dateString);
                if (!date || isNaN(date.getTime())) return '-';

                const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
                return `${months[date.getMonth()]} ${date.getFullYear()}`;
            };

            const formatNumber = (number) => {
                if (!number || isNaN(number)) return '0';
                return new Intl.NumberFormat('id-ID').format(number);
            };

            // Product info (combine all items)
            const productList = transaction.items ? transaction.items.map(item => item.product).join(', ') : '-';
            const totalQuantity = transaction.items ? transaction.items.reduce((sum, item) => sum + parseFloat(item.quantity), 0) : 0;
            const avgPrice = transaction.items && transaction.items.length > 0 ?
                transaction.items.reduce((sum, item) => sum + parseFloat(item.price), 0) / transaction.items.length : 0;

            // Bonus items
            const bonusList = transaction.bonus_items && transaction.bonus_items.length > 0 ?
                transaction.bonus_items.map(item => `${item.product_name} (${item.quantity})`).join(', ') : '-';

            row.innerHTML = `
                <!-- NO -->
                <td class="px-2 py-2 border-r text-center">${index + 1}</td>

                <!-- BULAN CUT OFF -->
                <td class="px-2 py-2 border-r whitespace-nowrap">${getMonthYear(transaction.created_at)}</td>

                <!-- TANGGAL LEADS MASUK -->
                <td class="px-2 py-2 border-r whitespace-nowrap bg-blue-50">
                    ${transaction.lead_info && transaction.lead_info.tanggal_leads_masuk ? formatDate(transaction.lead_info.tanggal_leads_masuk) : '-'}
                </td>

                <!-- CHANNEL MARKETING -->
                <td class="px-2 py-2 border-r bg-blue-50">
                    ${transaction.lead_info && transaction.lead_info.channel_marketing ? transaction.lead_info.channel_marketing : '-'}
                </td>

                <!-- LEADS (Customer Name) -->
                <td class="px-2 py-2 border-r bg-blue-50">
                    ${transaction.lead_info && transaction.lead_info.customer_name ? transaction.lead_info.customer_name : '-'}
                </td>

                <!-- TANGGAL ORDER -->
                <td class="px-2 py-2 border-r whitespace-nowrap">${formatDate(transaction.created_at)}</td>

                <!-- NOMER FAKTUR -->
                <td class="px-2 py-2 border-r">${transaction.order_number}</td>

                <!-- PEMESAN -->
                <td class="px-2 py-2 border-r">
                    ${transaction.member ? transaction.member.name : (transaction.lead_info && transaction.lead_info.customer_name ? transaction.lead_info.customer_name : '-')}
                </td>

                <!-- NAMA MASJID -->
                <td class="px-2 py-2 border-r">
                    ${transaction.mosque ? transaction.mosque.name : '-'}
                </td>

                <!-- ALAMAT -->
                <td class="px-2 py-2 border-r text-sm">
                    ${transaction.lead_info && transaction.lead_info.alamat ? transaction.lead_info.alamat : (transaction.mosque && transaction.mosque.address ? transaction.mosque.address : '-')}
                </td>

                <!-- CONTACT PERSON -->
                <td class="px-2 py-2 border-r whitespace-nowrap">
                    ${transaction.member ? transaction.member.phone : (transaction.lead_info && transaction.lead_info.customer_name ? transaction.lead_info.customer_name : '-')}
                    <!-- ${transaction.lead_info && transaction.lead_info.contact_person ? transaction.lead_info.contact_person : '-'} -->
                </td>

                <!-- PRODUK -->
                <td class="px-2 py-2 border-r text-sm bg-green-50">${productList}</td>

                <!-- UNIT (m) -->
                <td class="px-2 py-2 border-r text-right bg-green-50">${totalQuantity.toFixed(2)}</td>

                <!-- HARGA SATUAN/meter -->
                <td class="px-2 py-2 border-r text-right bg-green-50">${formatCurrency(avgPrice)}</td>

                <!-- TOTAL HARGA JUAL -->
                <td class="px-2 py-2 border-r text-right bg-green-50">${formatCurrency(transaction.subtotal)}</td>

                <!-- DISCOUNT/POTONGAN -->
                <td class="px-2 py-2 border-r text-right bg-green-50">${formatCurrency(transaction.discount)}</td>

                <!-- TOTAL PEMBAYARAN -->
                <td class="px-2 py-2 border-r text-right font-semibold bg-green-50">${formatCurrency(transaction.total)}</td>

                <!-- BONUS -->
                <td class="px-2 py-2 border-r text-sm bg-green-50">${bonusList}</td>

                <!-- UANG MUKA I - TGL -->
                <td class="px-2 py-2 border-r whitespace-nowrap bg-yellow-50">
                    ${transaction.settlement_payments && transaction.settlement_payments.uang_muka_1 ?
                        transaction.settlement_payments.uang_muka_1.date : '-'}
                </td>

                <!-- UANG MUKA I - Nominal -->
                <td class="px-2 py-2 border-r text-right bg-yellow-50">
                    ${transaction.settlement_payments && transaction.settlement_payments.uang_muka_1 ?
                        formatCurrency(transaction.settlement_payments.uang_muka_1.amount) : '-'}
                </td>

                <!-- UANG MUKA I - Bank -->
                <td class="px-2 py-2 border-r bg-yellow-50">
                    ${transaction.settlement_payments && transaction.settlement_payments.uang_muka_1 ?
                        transaction.settlement_payments.uang_muka_1.bank : '-'}
                </td>

                <!-- UANG MUKA II - TGL -->
                <td class="px-2 py-2 border-r whitespace-nowrap bg-yellow-50">
                    ${transaction.settlement_payments && transaction.settlement_payments.uang_muka_2 ?
                        transaction.settlement_payments.uang_muka_2.date : '-'}
                </td>

                <!-- UANG MUKA II - Nominal -->
                <td class="px-2 py-2 border-r text-right bg-yellow-50">
                    ${transaction.settlement_payments && transaction.settlement_payments.uang_muka_2 ?
                        formatCurrency(transaction.settlement_payments.uang_muka_2.amount) : '-'}
                </td>

                <!-- UANG MUKA II - Bank -->
                <td class="px-2 py-2 border-r bg-yellow-50">
                    ${transaction.settlement_payments && transaction.settlement_payments.uang_muka_2 ?
                        transaction.settlement_payments.uang_muka_2.bank : '-'}
                </td>

                <!-- PELUNASAN - TGL -->
                <td class="px-2 py-2 border-r whitespace-nowrap bg-yellow-50">
                    ${transaction.settlement_payments && transaction.settlement_payments.pelunasan ?
                        transaction.settlement_payments.pelunasan.date : '-'}
                </td>

                <!-- PELUNASAN - Nominal -->
                <td class="px-2 py-2 border-r text-right bg-yellow-50">
                    ${transaction.settlement_payments && transaction.settlement_payments.pelunasan ?
                        formatCurrency(transaction.settlement_payments.pelunasan.amount) : '-'}
                </td>

                <!-- PELUNASAN - Bank -->
                <td class="px-2 py-2 border-r bg-yellow-50">
                    ${transaction.settlement_payments && transaction.settlement_payments.pelunasan ?
                        transaction.settlement_payments.pelunasan.bank : '-'}
                </td>

                <!-- TAMBAHAN BIAYA KIRIM -->
                <td class="px-2 py-2 border-r text-right">
                    ${transaction.delivery_fee > 0 ? formatNumber(transaction.delivery_fee) : '-'}
                </td>

                <!-- TAMBAHAN BIAYA PEMASANGAN -->
                <td class="px-2 py-2 border-r text-right">
                    ${transaction.installation_fee > 0 ? formatNumber(transaction.installation_fee) : '-'}
                </td>

                <!-- KET. -->
                <td class="px-2 py-2 border-r text-sm">${transaction.notes || '-'}</td>

                <!-- TAGIHAN -->
                <td class="px-2 py-2 border-r text-right font-semibold">
                    ${transaction.remaining_balance > 0 ? formatCurrency(transaction.remaining_balance) : '-'}
                </td>

                <!-- Total Pendapatan -->
                <td class="px-2 py-2 border-r text-right font-bold">${formatCurrency(transaction.total)}</td>

                <!-- CABANG -->
                <td class="px-2 py-2">${transaction.outlet}</td>
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
                orderId: document.getElementById('detailOrderId'),
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
            elements.orderId.textContent = transaction.id;
            elements.dateTime.textContent = formatDateTime(transaction.created_at);
            elements.paymentMethod.textContent = getPaymentMethodText(transaction.payment_method);
            elements.status.textContent = getStatusText(transaction.status);
            elements.approvalStatus.textContent = getDualApprovalStatusText(transaction.dual_approval_status);
            
            // Isi kategori transaksi
            const categoryText = transaction.transaction_category === 'dp' ? 'DP (Uang Muka)' : 'Lunas';
            elements.transactionCategory.textContent = categoryText;
            
            // Isi approval info
            const financeApprovalRow = document.getElementById('financeApprovalRow');
            const operationalApprovalRow = document.getElementById('operationalApprovalRow');
            
            if (transaction.is_finance_approved && transaction.finance_approved_by && transaction.finance_approved_at) {
                document.getElementById('financeApproverName').textContent = `Disetujui oleh: ${transaction.finance_approved_by}`;
                document.getElementById('financeApprovalDate').textContent = `Pada: ${transaction.finance_approved_at}`;
                financeApprovalRow.classList.remove('hidden');
            } else {
                financeApprovalRow.classList.add('hidden');
            }
            
            if (transaction.is_operational_approved && transaction.operational_approved_by && transaction.operational_approved_at) {
                document.getElementById('operationalApproverName').textContent = `Disetujui oleh: ${transaction.operational_approved_by}`;
                document.getElementById('operationalApprovalDate').textContent = `Pada: ${transaction.operational_approved_at}`;
                operationalApprovalRow.classList.remove('hidden');
            } else {
                operationalApprovalRow.classList.add('hidden');
            }

            // Isi rejection info untuk Finance
            const financeRejectionRow = document.getElementById('financeRejectionRow');
            console.log('Finance Rejection Data:', {
                is_finance_rejected: transaction.is_finance_rejected,
                finance_rejected_by: transaction.finance_rejected_by,
                finance_rejected_at: transaction.finance_rejected_at,
                finance_rejection_reason: transaction.finance_rejection_reason
            });

            if (transaction.is_finance_rejected && transaction.finance_rejected_by && transaction.finance_rejected_at) {
                document.getElementById('financeRejectorName').textContent = `Ditolak oleh: ${transaction.finance_rejected_by}`;
                document.getElementById('financeRejectionDate').textContent = `Pada: ${transaction.finance_rejected_at}`;
                document.getElementById('financeRejectionReason').textContent = `Alasan: ${transaction.finance_rejection_reason || '-'}`;
                financeRejectionRow.classList.remove('hidden');
            } else {
                financeRejectionRow.classList.add('hidden');
            }

            // Isi rejection info untuk Operational
            const operationalRejectionRow = document.getElementById('operationalRejectionRow');
            console.log('Operational Rejection Data:', {
                is_operational_rejected: transaction.is_operational_rejected,
                operational_rejected_by: transaction.operational_rejected_by,
                operational_rejected_at: transaction.operational_rejected_at,
                operational_rejection_reason: transaction.operational_rejection_reason
            });

            if (transaction.is_operational_rejected && transaction.operational_rejected_by && transaction.operational_rejected_at) {
                document.getElementById('operationalRejectorName').textContent = `Ditolak oleh: ${transaction.operational_rejected_by}`;
                document.getElementById('operationalRejectionDate').textContent = `Pada: ${transaction.operational_rejected_at}`;
                document.getElementById('operationalRejectionReason').textContent = `Alasan: ${transaction.operational_rejection_reason || '-'}`;
                operationalRejectionRow.classList.remove('hidden');
            } else {
                operationalRejectionRow.classList.add('hidden');
            }

            // Isi edit request info
            const editRequestRow = document.getElementById('editRequestRow');
            const reviewEditBtn = document.getElementById('reviewEditBtn');
            
            if (transaction.pending_edit) {
                const editData = transaction.pending_edit;
                document.getElementById('editRequestStatus').textContent = getEditStatusText(editData.status);
                document.getElementById('editRequestInfo').textContent = `Diminta oleh ${editData.requester_name} - ${editData.edit_type}`;
                
                // Setup review button
                reviewEditBtn.onclick = () => openEditApprovalModal(editData.id);
                reviewEditBtn.classList.remove('hidden');
                editRequestRow.classList.remove('hidden');
            } else {
                editRequestRow.classList.add('hidden');
            }
            
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
                    
                    const itemContent = document.createElement('div');
                    itemContent.className = 'flex gap-3';
                    
                    // Image container
                    const imageContainer = document.createElement('div');
                    imageContainer.className = 'flex-shrink-0';
                    
                    if (item.product_image) {
                        const img = document.createElement('img');
                        img.src = item.product_image;
                        img.alt = item.product;
                        img.className = 'w-16 h-16 object-cover rounded-lg border border-gray-200 clickable-image';
                        img.onclick = () => openImageModal(item.product_image, item.product, `Produk - Qty: ${formatQuantityWithUnit(item.quantity, item.unit_type)} × ${formatCurrency(item.price)}`);
                        imageContainer.appendChild(img);
                    } else {
                        imageContainer.innerHTML = `
                            <div class="w-16 h-16 bg-gray-100 rounded-lg border border-gray-200 flex items-center justify-center">
                                <i data-lucide="image" class="w-6 h-6 text-gray-400"></i>
                            </div>
                        `;
                    }
                    
                    // Content container
                    const contentContainer = document.createElement('div');
                    contentContainer.className = 'flex-1 flex justify-between';
                    const formattedQuantity = formatQuantityWithUnit(item.quantity, item.unit_type);
                    contentContainer.innerHTML = `
                        <div>
                            <p class="font-medium">${item.product}</p>
                            <p class="text-sm text-gray-500">${formattedQuantity} × ${formatCurrency(item.price)}</p>
                            ${item.bonus_qty > 0 ? `<p class="text-sm text-green-600">+ ${formatQuantityWithUnit(item.bonus_qty, item.unit_type)} bonus</p>` : ''}
                        </div>
                        <div class="text-right">
                            <p class="font-medium">${formatCurrency(item.total)}</p>
                            ${item.discount > 0 ? `<p class="text-sm text-red-500">Diskon: ${formatCurrency(item.discount)}</p>` : ''}
                        </div>
                    `;
                    
                    itemContent.appendChild(imageContainer);
                    itemContent.appendChild(contentContainer);
                    itemElement.appendChild(itemContent);
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
                    
                    const bonusContent = document.createElement('div');
                    bonusContent.className = 'flex gap-3';
                    
                    // Image container for bonus
                    const imageContainer = document.createElement('div');
                    imageContainer.className = 'flex-shrink-0';
                    
                    if (bonusItem.product_image) {
                        const img = document.createElement('img');
                        img.src = bonusItem.product_image;
                        img.alt = bonusItem.product || bonusItem.product_name;
                        img.className = 'w-12 h-12 object-cover rounded-lg border border-green-200 clickable-image';
                        img.onclick = () => openImageModal(bonusItem.product_image, bonusItem.product || bonusItem.product_name, `Bonus Item - Qty: ${formatQuantityWithUnit(bonusItem.quantity || 0, bonusItem.unit_type)}`);
                        imageContainer.appendChild(img);
                    } else {
                        imageContainer.innerHTML = `
                            <div class="w-12 h-12 bg-green-100 rounded-lg border border-green-200 flex items-center justify-center">
                                <i data-lucide="gift" class="w-5 h-5 text-green-500"></i>
                            </div>
                        `;
                    }
                    
                    // Content container for bonus
                    const contentContainer = document.createElement('div');
                    contentContainer.className = 'flex-1 flex justify-between items-center';
                    const bonusFormattedQuantity = formatQuantityWithUnit(bonusItem.quantity || 0, bonusItem.unit_type);
                    contentContainer.innerHTML = `
                        <div>
                            <p class="font-medium text-green-700">
                                <i class="fas fa-gift mr-1"></i>
                                ${bonusItem.product || bonusItem.product_name || '-'}
                            </p>
                            <p class="text-sm text-green-600">${bonusFormattedQuantity} bonus</p>
                        </div>
                        <div class="text-sm text-green-600 font-medium">
                            GRATIS
                        </div>
                    `;
                    
                    bonusContent.appendChild(imageContainer);
                    bonusContent.appendChild(contentContainer);
                    bonusElement.appendChild(bonusContent);
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

            // Handle leads cabang and deal maker information
            const leadsCabangInfo = document.getElementById('leadsCabangInfo');
            const dealMakerInfo = document.getElementById('dealMakerInfo');

            // Show leads cabang if available
            if (transaction.leads_cabang_outlet && transaction.leads_cabang_outlet.name) {
                document.getElementById('detailLeadsCabang').textContent = transaction.leads_cabang_outlet.name;
                leadsCabangInfo.classList.remove('hidden');
                showCarpetServiceSection = true;
            } else {
                leadsCabangInfo.classList.add('hidden');
            }

            // Show deal maker if available
            if (transaction.deal_maker_outlet && transaction.deal_maker_outlet.name) {
                document.getElementById('detailDealMaker').textContent = 'BC-' + transaction.deal_maker_outlet.name;
                dealMakerInfo.classList.remove('hidden');
                showCarpetServiceSection = true;
            } else {
                dealMakerInfo.classList.add('hidden');
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
            refreshTransactionHistory(); // Refresh data
            
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
            refreshTransactionHistory(); // Refresh data

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
            refreshTransactionHistory(); // Refresh data

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

    // Function to clear all filters
    function clearAllFilters() {
        // Clear search input
        document.getElementById('searchInvoice').value = '';
        
        // Clear date input
        const datePicker = document.getElementById('transDateInput');
        if (datePicker && datePicker._flatpickr) {
            datePicker._flatpickr.clear();
        } else {
            datePicker.value = '';
        }
        
        // Reset all select filters
        document.getElementById('statusFilter').value = '';
        document.getElementById('approvalFilter').value = '';
        document.getElementById('categoryFilter').value = '';
        document.getElementById('dpStatusFilter').value = '';
        document.getElementById('outletFilter').value = '';
        
        // Refresh transaction history with no filters  
        fetchTransactionHistory();
        
        // Show success message
        showAlert('success', 'Semua filter berhasil direset');
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
            
            // Enhanced approval filter to include edit_pending
            let matchesApproval = true;
            if (approvalFilter) {
                if (approvalFilter === 'edit_pending') {
                    // Filter for transactions with pending edit requests
                    const transaction = transactionsCache.find(t => t.id == row.dataset.transactionId);
                    matchesApproval = transaction && transaction.pending_edit && transaction.pending_edit.status === 'pending';
                } else {
                    matchesApproval = actualApprovalStatus === approvalFilter;
                }
            }
            
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

    // Dual Approval Helper Functions
    function getDualApprovalStatusText(dualApprovalStatus) {
        const statusMap = {
            'pending': 'Menunggu Approval',
            'partially_approved': 'Sebagian Disetujui',
            'fully_approved': 'Disetujui Penuh'
        };
        return statusMap[dualApprovalStatus] || 'Tidak diketahui';
    }

    function getDualApprovalBadgeClass(dualApprovalStatus) {
        const classMap = {
            'pending': 'bg-orange-100 text-orange-800 border-orange-200',
            'partially_approved': 'bg-yellow-100 text-yellow-800 border-yellow-200',
            'fully_approved': 'bg-green-100 text-green-800 border-green-200'
        };
        return classMap[dualApprovalStatus] || 'bg-gray-100 text-gray-800';
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
            refreshTransactionHistory();
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
            refreshTransactionHistory();
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
        const rawInput = document.getElementById('settlementAmountRaw');

        let amount;
        if (type === 'full') {
            amount = remainingBalance;
        } else if (type === 'half') {
            amount = Math.floor(remainingBalance / 2);
        }

        // Set raw value
        if (rawInput) {
            rawInput.value = amount;
        }

        // Set formatted display value
        if (amount) {
            const formatted = amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            amountInput.value = formatted;
        } else {
            amountInput.value = '';
        }
    }

    // Settlement form submission
    document.getElementById('settlementForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const modal = document.getElementById('modalSettlement');
        const orderId = modal.dataset.orderId;
        const remainingBalance = parseFloat(modal.dataset.remainingBalance || 0);

        // Get amount from raw input or parse from formatted input
        const rawAmountInput = document.getElementById('settlementAmountRaw');
        const amount = rawAmountInput && rawAmountInput.value ? 
                      parseFloat(rawAmountInput.value) : 
                      parseFloat(document.getElementById('settlementAmount').value.replace(/[^\d]/g, ''));

        const formData = new FormData();
        formData.append('amount_received', amount);
        formData.append('payment_method', document.getElementById('settlementPaymentMethod').value);
        formData.append('notes', document.getElementById('settlementNotes').value);

        const paymentProofFile = document.getElementById('settlementPaymentProof').files[0];
        if (paymentProofFile) {
            formData.append('payment_proof', paymentProofFile);
        }

        // Validasi
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
            refreshTransactionHistory(); // Refresh data

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
    
    // Function to preview contract PDF/image
    function previewContractPdf(pdfUrl, orderNumber) {
        try {
            // Validate URL
            if (!pdfUrl || typeof pdfUrl !== 'string') {
                showAlert('error', 'URL file tidak valid');
                return;
            }

            // Determine file type from URL
            const urlParts = pdfUrl.split('?')[0]; // Remove query parameters if any
            const fileExtension = urlParts.substring(urlParts.lastIndexOf('.') + 1).toLowerCase();
            const isPdf = fileExtension === 'pdf';
            const isImage = ['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(fileExtension);

            if (!isPdf && !isImage) {
                showAlert('error', 'Format file tidak didukung untuk preview');
                return;
            }

            // Create modal for preview with escaped URL
            const escapedUrl = pdfUrl.replace(/'/g, "\\'").replace(/"/g, "&quot;");
            const modalHTML = `
                <div id="previewModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" style="animation: fadeIn 0.3s ease-in;">
                    <div class="bg-white rounded-lg shadow-lg max-w-4xl w-full max-h-[90vh] overflow-hidden flex flex-col">
                        <div class="flex justify-between items-center p-4 border-b">
                            <h3 class="text-lg font-semibold">Preview - Akad Jual Beli/Sketsa Masjid (${orderNumber})</h3>
                            <button onclick="closePreviewModal()" class="text-gray-500 hover:text-gray-700" type="button">
                                <i class="fas fa-times text-xl"></i>
                            </button>
                        </div>
                        <div class="flex-1 p-4 overflow-auto" style="max-height: calc(90vh - 180px);">
                            ${isPdf ?
                                `<iframe src="${escapedUrl}" width="100%" height="600" style="border: 1px solid #ddd; display: block;"></iframe>` :
                                `<div style="display: flex; justify-content: center; align-items: center; min-height: 300px;">
                                    <img src="${escapedUrl}" alt="Preview" style="max-width: 100%; max-height: 100%; object-fit: contain;" onerror="handleImageError()">
                                </div>`
                            }
                        </div>
                        <div class="flex justify-end gap-2 p-4 border-t bg-gray-50">
                            <button onclick="downloadContractPdf('${pdfUrl}', '${orderNumber}')" class="inline-flex items-center px-4 py-2 bg-orange-500 text-white hover:bg-orange-600 rounded transition-colors" type="button">
                                <i class="fas fa-download mr-2"></i>
                                Download
                            </button>
                            <button onclick="closePreviewModal()" class="inline-flex items-center px-4 py-2 bg-gray-300 text-gray-700 hover:bg-gray-400 rounded transition-colors" type="button">
                                Tutup
                            </button>
                        </div>
                    </div>
                </div>
            `;

            // Remove existing preview modal if any
            const existingModal = document.getElementById('previewModal');
            if (existingModal) {
                existingModal.remove();
            }

            // Create and append modal
            const temp = document.createElement('div');
            temp.innerHTML = modalHTML;
            const modalElement = temp.firstElementChild;

            // Add fade-in animation
            const style = document.createElement('style');
            if (!document.getElementById('previewModalStyle')) {
                style.id = 'previewModalStyle';
                style.textContent = `
                    @keyframes fadeIn {
                        from { opacity: 0; }
                        to { opacity: 1; }
                    }
                `;
                document.head.appendChild(style);
            }

            document.body.appendChild(modalElement);

            // Add event listener to close on backdrop click
            modalElement.addEventListener('click', function(e) {
                if (e.target === this) {
                    closePreviewModal();
                }
            });

        } catch (error) {
            console.error('Error previewing contract PDF:', error);
            showAlert('error', 'Gagal membuka preview akad jual beli: ' + error.message);
        }
    }

    // Function to handle image load errors
    function handleImageError() {
        console.error('Gagal memuat gambar preview');
        showAlert('error', 'Gagal memuat gambar. File mungkin rusak atau format tidak didukung.');
        closePreviewModal();
    }

    // Function to close preview modal
    function closePreviewModal() {
        const previewModal = document.getElementById('previewModal');
        if (previewModal) {
            previewModal.remove();
        }
    }

    // Function to download contract PDF
    function downloadContractPdf(pdfUrl, orderNumber) {
        try {
            // Validate URL
            if (!pdfUrl || typeof pdfUrl !== 'string') {
                showAlert('error', 'URL file tidak valid');
                return;
            }

            // Remove query parameters for extension detection
            const urlParts = pdfUrl.split('?')[0];
            const fileExtension = urlParts.substring(urlParts.lastIndexOf('.') + 1).toLowerCase();

            // Create a temporary link to download the file
            const link = document.createElement('a');
            link.href = pdfUrl;

            // Set appropriate filename based on file type
            if (fileExtension === 'pdf') {
                link.download = `Akad_Jual_Beli_Sketsa_Masjid_${orderNumber}.pdf`;
            } else if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(fileExtension)) {
                link.download = `Akad_Jual_Beli_Sketsa_Masjid_${orderNumber}.${fileExtension}`;
            } else {
                link.download = `Akad_Jual_Beli_Sketsa_Masjid_${orderNumber}`;
            }

            link.target = '_blank';

            // Append to body, click, then remove
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);

            showAlert('success', 'Download akad jual beli dimulai');
        } catch (error) {
            console.error('Error downloading contract PDF:', error);
            showAlert('error', 'Gagal mendownload akad jual beli: ' + error.message);
        }
    }
    
    // Setup polling for transaction history updates
    document.addEventListener('DOMContentLoaded', function() {
        // Start polling for transaction updates every 30 seconds
        if (window.pollingManager) {
            window.pollingManager.start('transactionHistory', async () => {
                console.log('Polling transaction history...');
                await refreshTransactionHistory();
            }, 30000); // 30 seconds interval
        }
    });
    
    // Stop polling when leaving the page
    window.addEventListener('beforeunload', () => {
        if (window.pollingManager) {
            window.pollingManager.stop('transactionHistory');
        }
    });

    // Modal Management Variables
    let currentApprovalOrderId = null;

    // Compact Action Modal Functions
    function openFinanceActionModal(orderId) {
        if (!orderId || orderId === 'undefined' || orderId === 'null' || orderId === undefined || orderId === null) {
            showAlert('error', 'Order ID tidak valid untuk aksi Keuangan: ' + orderId);
            return;
        }
        currentApprovalOrderId = orderId;
        document.getElementById('financeActionModal').classList.remove('hidden');
    }

    function closeFinanceActionModal(resetId = true) {
        if (resetId) {
            currentApprovalOrderId = null;
        }
        document.getElementById('financeActionModal').classList.add('hidden');
    }

    function handleFinanceAction(action) {
        const orderId = currentApprovalOrderId;
        if (!orderId) {
            showAlert('error', 'Order ID tidak valid untuk aksi Keuangan.');
            return;
        }

        closeFinanceActionModal(false);

        if (action === 'approve') {
            openFinanceApprovalModal(orderId);
        } else if (action === 'reject') {
            openFinanceRejectionModal(orderId);
        }
    }

    function openOperationalActionModal(orderId) {
        if (!orderId || orderId === 'undefined' || orderId === 'null' || orderId === undefined || orderId === null) {
            showAlert('error', 'Order ID tidak valid untuk aksi Operasional: ' + orderId);
            return;
        }
        currentApprovalOrderId = orderId;
        document.getElementById('operationalActionModal').classList.remove('hidden');
    }

    function closeOperationalActionModal(resetId = true) {
        if (resetId) {
            currentApprovalOrderId = null;
        }
        document.getElementById('operationalActionModal').classList.add('hidden');
    }

    function handleOperationalAction(action) {
        const orderId = currentApprovalOrderId;
        if (!orderId) {
            showAlert('error', 'Order ID tidak valid untuk aksi Operasional.');
            return;
        }

        closeOperationalActionModal(false);

        if (action === 'approve') {
            openOperationalApprovalModal(orderId);
        } else if (action === 'reject') {
            openOperationalRejectionModal(orderId);
        }
    }

    // Dual Approval Modal Functions
    function openFinanceApprovalModal(orderId) {
        console.log('openFinanceApprovalModal called with orderId:', orderId);
        if (!orderId || orderId === 'undefined' || orderId === 'null' || orderId === undefined || orderId === null) {
            showAlert('error', 'Order ID tidak valid untuk approval Keuangan: ' + orderId);
            return;
        }
        currentApprovalOrderId = orderId;
        document.getElementById('financeApprovalModal').classList.remove('hidden');
    }

    function closeFinanceApprovalModal() {
        currentApprovalOrderId = null;
        document.getElementById('financeApprovalModal').classList.add('hidden');
    }

    function openOperationalApprovalModal(orderId) {
        console.log('openOperationalApprovalModal called with orderId:', orderId);
        if (!orderId || orderId === 'undefined' || orderId === 'null' || orderId === undefined || orderId === null) {
            showAlert('error', 'Order ID tidak valid untuk approval Operasional: ' + orderId);
            return;
        }
        currentApprovalOrderId = orderId;
        document.getElementById('operationalApprovalModal').classList.remove('hidden');
    }

    function closeOperationalApprovalModal() {
        currentApprovalOrderId = null;
        document.getElementById('operationalApprovalModal').classList.add('hidden');
    }

    // Approval Functions
    async function confirmFinanceApproval() {
        console.log('confirmFinanceApproval called with currentApprovalOrderId:', currentApprovalOrderId);
        if (!currentApprovalOrderId) return;
        
        const orderId = currentApprovalOrderId; // Store the ID before closing modal
        closeFinanceApprovalModal();
        await approveFinance(orderId);
    }

    async function confirmOperationalApproval() {
        console.log('confirmOperationalApproval called with currentApprovalOrderId:', currentApprovalOrderId);
        if (!currentApprovalOrderId) return;
        
        const orderId = currentApprovalOrderId; // Store the ID before closing modal
        closeOperationalApprovalModal();
        await approveOperational(orderId);
    }

    async function approveFinance(orderId) {
        try {
            console.log('approveFinance called with orderId:', orderId);
            if (!orderId || orderId === 'undefined' || orderId === 'null' || orderId === undefined || orderId === null) {
                showAlert('error', 'Order ID tidak valid untuk approval Keuangan');
                return;
            }
            const token = localStorage.getItem('token');
            if (!token) {
                showAlert('error', 'Token tidak ditemukan. Silakan login ulang.');
                return;
            }

            const response = await fetch(`/api/orders/approve-finance/${orderId}`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`,
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            const result = await response.json();

            if (response.ok && result.success) {
                showAlert('success', 'Transaksi berhasil di-approve oleh Keuangan');
                // Refresh the table
                await refreshTransactionHistory();
            } else {
                showAlert('error', result.message || 'Gagal melakukan approval Keuangan');
            }
        } catch (error) {
            console.error('Error approving finance:', error);
            showAlert('error', 'Terjadi kesalahan saat melakukan approval Keuangan');
        }
    }

    async function approveOperational(orderId) {
        try {
            console.log('approveOperational called with orderId:', orderId);
            if (!orderId || orderId === 'undefined' || orderId === 'null' || orderId === undefined || orderId === null) {
                showAlert('error', 'Order ID tidak valid untuk approval Operasional');
                return;
            }
            const token = localStorage.getItem('token');
            if (!token) {
                showAlert('error', 'Token tidak ditemukan. Silakan login ulang.');
                return;
            }

            const response = await fetch(`/api/orders/approve-operational/${orderId}`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`,
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            const result = await response.json();

            if (response.ok && result.success) {
                showAlert('success', 'Transaksi berhasil di-approve oleh Operasional');
                // Refresh the table
                await refreshTransactionHistory();
            } else {
                showAlert('error', result.message || 'Gagal melakukan approval Operasional');
            }
        } catch (error) {
            console.error('Error approving operational:', error);
            showAlert('error', 'Terjadi kesalahan saat melakukan approval Operasional');
        }
    }

    // Rejection Modal Functions
    function openFinanceRejectionModal(orderId) {
        console.log('openFinanceRejectionModal called with orderId:', orderId);
        if (!orderId || orderId === 'undefined' || orderId === 'null' || orderId === undefined || orderId === null) {
            showAlert('error', 'Order ID tidak valid untuk penolakan Keuangan: ' + orderId);
            return;
        }
        currentApprovalOrderId = orderId;
        document.getElementById('financeRejectionReason').value = ''; // Clear previous input
        document.getElementById('financeRejectionModal').classList.remove('hidden');
    }

    function closeFinanceRejectionModal() {
        currentApprovalOrderId = null;
        document.getElementById('financeRejectionReason').value = '';
        document.getElementById('financeRejectionModal').classList.add('hidden');
    }

    function openOperationalRejectionModal(orderId) {
        console.log('openOperationalRejectionModal called with orderId:', orderId);
        if (!orderId || orderId === 'undefined' || orderId === 'null' || orderId === undefined || orderId === null) {
            showAlert('error', 'Order ID tidak valid untuk penolakan Operasional: ' + orderId);
            return;
        }
        currentApprovalOrderId = orderId;
        document.getElementById('operationalRejectionReason').value = ''; // Clear previous input
        document.getElementById('operationalRejectionModal').classList.remove('hidden');
    }

    function closeOperationalRejectionModal() {
        currentApprovalOrderId = null;
        document.getElementById('operationalRejectionReason').value = '';
        document.getElementById('operationalRejectionModal').classList.add('hidden');
    }

    async function confirmFinanceRejection() {
        console.log('confirmFinanceRejection called with currentApprovalOrderId:', currentApprovalOrderId);
        if (!currentApprovalOrderId) return;

        const reason = document.getElementById('financeRejectionReason').value.trim();
        if (!reason) {
            showAlert('error', 'Alasan penolakan wajib diisi');
            return;
        }

        const orderId = currentApprovalOrderId;
        closeFinanceRejectionModal();
        await rejectFinance(orderId, reason);
    }

    async function confirmOperationalRejection() {
        console.log('confirmOperationalRejection called with currentApprovalOrderId:', currentApprovalOrderId);
        if (!currentApprovalOrderId) return;

        const reason = document.getElementById('operationalRejectionReason').value.trim();
        if (!reason) {
            showAlert('error', 'Alasan penolakan wajib diisi');
            return;
        }

        const orderId = currentApprovalOrderId;
        closeOperationalRejectionModal();
        await rejectOperational(orderId, reason);
    }

    // Rejection API Functions
    async function rejectFinance(orderId, reason) {
        try {
            console.log('rejectFinance called with orderId:', orderId, 'reason:', reason);
            if (!orderId || orderId === 'undefined' || orderId === 'null' || orderId === undefined || orderId === null) {
                showAlert('error', 'Order ID tidak valid untuk penolakan Keuangan');
                return;
            }
            const token = localStorage.getItem('token');
            if (!token) {
                showAlert('error', 'Token tidak ditemukan. Silakan login ulang.');
                return;
            }

            const response = await fetch(`/api/orders/reject-finance/${orderId}`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`,
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ reason: reason })
            });

            const result = await response.json();

            if (response.ok && result.success) {
                showAlert('success', 'Transaksi berhasil ditolak oleh Keuangan. Kasir perlu memperbaiki transaksi sesuai arahan.');
                // Refresh the table
                await refreshTransactionHistory();
            } else {
                showAlert('error', result.message || 'Gagal menolak transaksi dari Keuangan');
            }
        } catch (error) {
            console.error('Error rejecting finance:', error);
            showAlert('error', 'Terjadi kesalahan saat menolak transaksi (Keuangan)');
        }
    }

    async function rejectOperational(orderId, reason) {
        try {
            console.log('rejectOperational called with orderId:', orderId, 'reason:', reason);
            if (!orderId || orderId === 'undefined' || orderId === 'null' || orderId === undefined || orderId === null) {
                showAlert('error', 'Order ID tidak valid untuk penolakan Operasional');
                return;
            }
            const token = localStorage.getItem('token');
            if (!token) {
                showAlert('error', 'Token tidak ditemukan. Silakan login ulang.');
                return;
            }

            const response = await fetch(`/api/orders/reject-operational/${orderId}`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`,
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ reason: reason })
            });

            const result = await response.json();

            if (response.ok && result.success) {
                showAlert('success', 'Transaksi berhasil ditolak oleh Operasional. Kasir perlu memperbaiki transaksi sesuai arahan.');
                // Refresh the table
                await refreshTransactionHistory();
            } else {
                showAlert('error', result.message || 'Gagal menolak transaksi dari Operasional');
            }
        } catch (error) {
            console.error('Error rejecting operational:', error);
            showAlert('error', 'Terjadi kesalahan saat menolak transaksi (Operasional)');
        }
    }

    // Setup currency formatting for Settlement Amount field
    document.addEventListener('DOMContentLoaded', function() {
        const settlementAmountInput = document.getElementById('settlementAmount');
        if (settlementAmountInput) {
            // Format on input
            settlementAmountInput.addEventListener('input', function() {
                const rawValue = this.value.replace(/[^\d]/g, '');
                
                // Update hidden field with raw value
                const hiddenInput = document.getElementById('settlementAmountRaw');
                if (hiddenInput) {
                    hiddenInput.value = rawValue;
                }
                
                // Format display value
                if (rawValue) {
                    const formatted = rawValue.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                    this.value = formatted;
                } else {
                    this.value = '';
                }
            });
            
            // Format on paste
            settlementAmountInput.addEventListener('paste', function() {
                setTimeout(() => {
                    const rawValue = this.value.replace(/[^\d]/g, '');
                    
                    const hiddenInput = document.getElementById('settlementAmountRaw');
                    if (hiddenInput) {
                        hiddenInput.value = rawValue;
                    }
                    
                    if (rawValue) {
                        const formatted = rawValue.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                        this.value = formatted;
                    } else {
                        this.value = '';
                    }
                }, 10);
            });
            
            // Keep formatting on focus but select all for easy replacement
            settlementAmountInput.addEventListener('focus', function() {
                this.select();
            });
            
            // Re-format on blur
            settlementAmountInput.addEventListener('blur', function() {
                const rawValue = this.value.replace(/[^\d]/g, '');
                
                const hiddenInput = document.getElementById('settlementAmountRaw');
                if (hiddenInput) {
                    hiddenInput.value = rawValue;
                }
                
                if (rawValue) {
                    const formatted = rawValue.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                    this.value = formatted;
                } else {
                    this.value = '';
                }
            });
        }
    });

    // ============== EDIT TRANSACTION APPROVAL FUNCTIONS ==============
    
    let currentEditRequest = null;
    let pendingEditsCache = [];

    // Fetch pending edit requests
    async function fetchPendingEdits() {
        try {
            const token = localStorage.getItem('token');
            if (!token) {
                console.error('Token tidak ditemukan');
                return [];
            }

            const response = await fetch('/api/transaction-edits/pending', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`,
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            const result = await response.json();
            
            if (response.ok && result.success) {
                pendingEditsCache = result.data;
                return result.data;
            } else {
                console.error('Gagal mengambil data edit pending:', result.message);
                return [];
            }
        } catch (error) {
            console.error('Error fetching pending edits:', error);
            return [];
        }
    }

    // Open edit approval modal
    async function openEditApprovalModal(editId) {
        try {
            const token = localStorage.getItem('token');
            if (!token) {
                showAlert('error', 'Token tidak ditemukan. Silakan login ulang.');
                return;
            }

            // Get edit request details
            const response = await fetch(`/api/transaction-edits/${editId}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`,
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            const result = await response.json();
            
            if (!response.ok || !result.success) {
                showAlert('error', result.message || 'Gagal mengambil data edit request');
                return;
            }

            currentEditRequest = result.data;
            populateEditApprovalModal(currentEditRequest);
            document.getElementById('editApprovalModal').classList.remove('hidden');

        } catch (error) {
            console.error('Error opening edit approval modal:', error);
            showAlert('error', 'Terjadi kesalahan saat membuka modal approval');
        }
    }

    // Populate edit approval modal with data
    function populateEditApprovalModal(editData) {
        // Basic info
        document.getElementById('editInvoiceNumber').textContent = editData.order_number || '';
        document.getElementById('editRequester').textContent = editData.requester_name || '';
        document.getElementById('editRequestDate').textContent = editData.requested_at || '';
        document.getElementById('editType').textContent = getEditTypeText(editData.edit_type);
        document.getElementById('editReason').textContent = getEditReasonText(editData.reason) || '';
        
        // Notes (optional)
        const notesDiv = document.getElementById('editNotesDiv');
        const notesSpan = document.getElementById('editNotes');
        if (editData.notes) {
            notesSpan.textContent = editData.notes;
            notesDiv.classList.remove('hidden');
        } else {
            notesDiv.classList.add('hidden');
        }

        // Financial changes
        document.getElementById('editOriginalTotal').textContent = formatCurrency(editData.original_data.total);
        document.getElementById('editNewTotal').textContent = formatCurrency(editData.new_data.total);
        
        const difference = editData.total_difference;
        const diffElement = document.getElementById('editTotalDifference');
        if (difference > 0) {
            diffElement.textContent = `+${formatCurrency(difference)}`;
            diffElement.className = 'font-medium text-green-600';
        } else if (difference < 0) {
            diffElement.textContent = `-${formatCurrency(Math.abs(difference))}`;
            diffElement.className = 'font-medium text-red-600';
        } else {
            diffElement.textContent = formatCurrency(0);
            diffElement.className = 'font-medium text-gray-600';
        }

        document.getElementById('editApprovalStatus').textContent = editData.approval_status || 'Pending';

        // Show approval information if any approvals have been made
        const approvalInfoSection = document.getElementById('editApprovalInfo');
        let hasApprovals = false;

        // Finance approval info
        if (editData.finance_approved) {
            document.getElementById('editFinanceApproverName').textContent = editData.finance_approved_by || 'Unknown';
            document.getElementById('editFinanceApprovalTime').textContent = editData.finance_approved_at || '';
            document.getElementById('editFinanceApprover').classList.remove('hidden');
            document.getElementById('editFinanceApprovalDate').classList.remove('hidden');
            document.getElementById('editFinanceNotApproved').classList.add('hidden');
            hasApprovals = true;
        } else {
            document.getElementById('editFinanceApprover').classList.add('hidden');
            document.getElementById('editFinanceApprovalDate').classList.add('hidden');
            document.getElementById('editFinanceNotApproved').classList.remove('hidden');
        }

        // Operational approval info
        if (editData.operational_approved) {
            document.getElementById('editOperationalApproverName').textContent = editData.operational_approved_by || 'Unknown';
            document.getElementById('editOperationalApprovalTime').textContent = editData.operational_approved_at || '';
            document.getElementById('editOperationalApprover').classList.remove('hidden');
            document.getElementById('editOperationalApprovalDate').classList.remove('hidden');
            document.getElementById('editOperationalNotApproved').classList.add('hidden');
            hasApprovals = true;
        } else {
            document.getElementById('editOperationalApprover').classList.add('hidden');
            document.getElementById('editOperationalApprovalDate').classList.add('hidden');
            document.getElementById('editOperationalNotApproved').classList.remove('hidden');
        }

        // Show approval info section if there are any approvals
        if (hasApprovals) {
            approvalInfoSection.classList.remove('hidden');
        } else {
            approvalInfoSection.classList.add('hidden');
        }

        // Populate items comparison
        populateItemsComparison(editData.original_data.items, editData.new_data.items);

        // Show/hide approval buttons based on current status
        updateApprovalButtons(editData);
    }

    // Populate items comparison tables
    function populateItemsComparison(originalItems, newItems) {
        const originalTbody = document.getElementById('originalItemsList');
        const newTbody = document.getElementById('newItemsList');

        console.log('Populating items comparison:', { originalItems, newItems });
        // Original items
        originalTbody.innerHTML = originalItems.map(item => `
            <tr>
                <td class="px-2 py-1">${item.product || (item.product && item.product.name) || 'Unknown Product'}</td>
                <td class="px-2 py-1 text-center">${formatQuantityWithUnit(item.quantity, item.unit_type || (item.product && item.product.unit_type))}</td>
                <td class="px-2 py-1 text-right">${formatCurrency(item.price)}</td>
                <td class="px-2 py-1 text-right">${formatCurrency((item.quantity * item.price) - (item.discount || 0))}</td>
            </tr>
        `).join('');

        // New items
        newTbody.innerHTML = newItems.map(item => `
            <tr>
                <td class="px-2 py-1">${item.product}</td>
                <td class="px-2 py-1 text-center">${formatQuantityWithUnit(item.quantity, item.unit_type)}</td>
                <td class="px-2 py-1 text-right">${formatCurrency(item.price)}</td>
                <td class="px-2 py-1 text-right">${formatCurrency((item.quantity * item.price) - (item.discount || 0))}</td>
            </tr>
        `).join('');
    }

    // Update approval buttons visibility
    function updateApprovalButtons(editData) {
        const financeBtn = document.getElementById('approveEditFinanceBtn');
        const operationalBtn = document.getElementById('approveEditOperationalBtn');
        const rejectBtn = document.getElementById('rejectEditBtn');

        // Show reject button only if edit is still pending
        rejectBtn.style.display = editData.status === 'pending' ? 'block' : 'none';

        // Show finance button if not yet approved by finance
        financeBtn.style.display = (!editData.finance_approved && editData.status === 'pending') ? 'block' : 'none';

        // Show operational button if not yet approved by operational
        operationalBtn.style.display = (!editData.operational_approved && editData.status === 'pending') ? 'block' : 'none';
    }

    // Close edit approval modal
    function closeEditApprovalModal() {
        currentEditRequest = null;
        document.getElementById('editApprovalModal').classList.add('hidden');
    }

    // Approve edit - Finance
    async function approveEditFinance() {
        if (!currentEditRequest) return;

        try {
            const token = localStorage.getItem('token');
            const response = await fetch(`/api/transaction-edits/${currentEditRequest.id}/approve-finance`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`,
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            const result = await response.json();
            
            if (response.ok && result.success) {
                showAlert('success', result.message);
                closeEditApprovalModal();
                await refreshTransactionHistory(); // Refresh table
            } else {
                showAlert('error', result.message || 'Gagal approve finance');
            }
        } catch (error) {
            console.error('Error approving edit finance:', error);
            showAlert('error', 'Terjadi kesalahan saat approve finance');
        }
    }

    // Approve edit - Operational
    async function approveEditOperational() {
        if (!currentEditRequest) return;

        try {
            const token = localStorage.getItem('token');
            const response = await fetch(`/api/transaction-edits/${currentEditRequest.id}/approve-operational`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`,
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            const result = await response.json();
            
            if (response.ok && result.success) {
                showAlert('success', result.message);
                closeEditApprovalModal();
                await refreshTransactionHistory(); // Refresh table
            } else {
                showAlert('error', result.message || 'Gagal approve operational');
            }
        } catch (error) {
            console.error('Error approving edit operational:', error);
            showAlert('error', 'Terjadi kesalahan saat approve operational');
        }
    }

    // Show reject edit modal
    function rejectEditRequest() {
        document.getElementById('rejectEditModal').classList.remove('hidden');
    }

    // Close reject edit modal
    function closeRejectEditModal() {
        document.getElementById('rejectEditModal').classList.add('hidden');
        document.getElementById('rejectEditReason').value = '';
    }

    // Confirm reject edit
    async function confirmRejectEdit() {
        if (!currentEditRequest) return;

        const reason = document.getElementById('rejectEditReason').value.trim();
        if (!reason) {
            showAlert('error', 'Alasan penolakan harus diisi');
            return;
        }

        try {
            const token = localStorage.getItem('token');
            const response = await fetch(`/api/transaction-edits/${currentEditRequest.id}/reject`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`,
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ reason: reason })
            });

            const result = await response.json();
            
            if (response.ok && result.success) {
                showAlert('success', result.message);
                closeRejectEditModal();
                closeEditApprovalModal();
                await refreshTransactionHistory(); // Refresh table
            } else {
                showAlert('error', result.message || 'Gagal menolak edit');
            }
        } catch (error) {
            console.error('Error rejecting edit:', error);
            showAlert('error', 'Terjadi kesalahan saat menolak edit');
        }
    }

    // Helper function to get edit type text
    function getEditTypeText(editType) {
        const typeMap = {
            'quantity_adjustment': 'Penyesuaian Kuantitas',
            'item_addition': 'Penambahan Item',
            'item_removal': 'Pengurangan Item',
            'item_modification': 'Modifikasi Item'
        };
        return typeMap[editType] || editType;
    }

    // Helper function to get edit status text
    function getEditStatusText(status) {
        const statusMap = {
            'pending': 'Menunggu Approval',
            'approved': 'Disetujui',
            'rejected': 'Ditolak',
            'applied': 'Diterapkan'
        };
        return statusMap[status] || status;
    }

    // Helper function to format quantity with unit type
    function formatQuantityWithUnit(quantity, unitType) {
        const qty = parseFloat(quantity);
        const unit = unitType || 'pcs';
        
        if (unit === 'meter') {
            // For meter, show decimal if not whole number
            return qty % 1 === 0 ? `${qty.toFixed(0)} ${unit}` : `${qty.toFixed(1)} ${unit}`;
        } else {
            // For other units, show as integer
            return `${qty.toFixed(0)} ${unit}`;
        }
    }

    // ============== END EDIT TRANSACTION APPROVAL FUNCTIONS ==============
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
        box-shadow: 2px 0 4px rgba(0, 0, 0, 0.1);
    }

    /* Scroll indicator styling */
    #scrollIndicatorRight {
        transition: opacity 0.3s ease;
        background: linear-gradient(to left, rgba(255, 255, 255, 1) 0%, rgba(255, 255, 255, 0.8) 50%, rgba(255, 255, 255, 0) 100%);
    }

    #scrollIndicatorRight.hidden {
        opacity: 0;
    }

    /* Smooth scrolling for table */
    #tableContainer {
        scroll-behavior: smooth;
    }
    
    /* Image modal styles */
    .image-modal {
        backdrop-filter: blur(4px);
    }
    
    .image-modal img {
        max-width: 90vw;
        max-height: 90vh;
        object-fit: contain;
        border-radius: 8px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }
    
    .clickable-image {
        cursor: pointer;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    
    .clickable-image:hover {
        transform: scale(1.05);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }
</style>

<!-- Modal untuk menampilkan gambar produk/bonus -->
<div id="imageModal" class="hidden fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center p-4 image-modal">
    <div class="relative max-w-full max-h-full">
        <!-- Close button -->
        <button onclick="closeImageModal()" class="absolute -top-4 -right-4 bg-white rounded-full p-2 shadow-lg hover:bg-gray-100 transition-colors z-10">
            <i data-lucide="x" class="w-5 h-5 text-gray-700"></i>
        </button>
        
        <!-- Image container -->
        <div class="bg-white p-2 rounded-lg">
            <img id="modalImage" src="" alt="" class="max-w-full max-h-full">
        </div>
        
        <!-- Image info -->
        <div class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-50 text-white p-4 rounded-b-lg">
            <p id="modalImageTitle" class="font-medium"></p>
            <p id="modalImageDescription" class="text-sm text-gray-200"></p>
        </div>
    </div>
    
    <!-- Click outside to close -->
    <div class="absolute inset-0" onclick="closeImageModal()"></div>
</div>

<script>
    // Image modal functions
    function openImageModal(imageSrc, title, description = '') {
        const modal = document.getElementById('imageModal');
        const modalImage = document.getElementById('modalImage');
        const modalTitle = document.getElementById('modalImageTitle');
        const modalDescription = document.getElementById('modalImageDescription');
        
        modalImage.src = imageSrc;
        modalImage.alt = title;
        modalTitle.textContent = title;
        modalDescription.textContent = description;
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        
        // Prevent body scroll
        document.body.style.overflow = 'hidden';
    }
    
    function closeImageModal() {
        const modal = document.getElementById('imageModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        
        // Restore body scroll
        document.body.style.overflow = '';
    }
    
    // Close modal on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeImageModal();
        }
    });

    // Helper functions for quantity formatting
    function formatQuantityWithUnit(qty, unitType) {
        // Convert to number first
        const numQty = parseFloat(qty);
        if (isNaN(numQty) || numQty === 0) return '0 pcs';
        
        const formattedQty = numQty % 1 === 0 ? numQty.toString() : numQty.toFixed(1);
        const unit = unitType || 'pcs';
        
        return `${formattedQty} ${unit}`;
    }

    // Format quantity for display (with multiplier if needed)
    function formatQuantityForDisplay(qty, unitType) {
        // Convert to number first
        const numQty = parseFloat(qty);
        if (isNaN(numQty) || numQty === 0) return '';
        
        const hideQuantity = ['pasang', 'kirim'].includes(unitType);
        if (hideQuantity) return '';
        
        const formattedQty = numQty % 1 === 0 ? numQty.toString() : numQty.toFixed(1);
        return `${formattedQty}x `;
    }

    // ============== EDIT HISTORY FUNCTIONS ==============
    
    // Open edit history modal
    async function openEditHistoryModal(orderNumber, orderId) {
        try {
            if (!orderId || orderId === 'undefined') {
                showAlert('error', 'ID transaksi tidak valid');
                return;
            }

            // Set order number in modal
            document.getElementById('editHistoryOrderNumber').textContent = orderNumber;

            // Show modal
            const modal = document.getElementById('modalEditHistory');
            modal.classList.remove('hidden');
            modal.classList.add('flex');

            // Load edit history
            await loadEditHistory(orderId);

        } catch (error) {
            console.error('Error in openEditHistoryModal:', error);
            showAlert('error', 'Gagal membuka modal riwayat edit: ' + error.message);
        }
    }

    // Close edit history modal
    function closeEditHistoryModal() {
        const modal = document.getElementById('modalEditHistory');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    // Load edit history data
    async function loadEditHistory(orderId) {
        try {
            // Show loading state
            document.getElementById('editHistoryLoading').classList.remove('hidden');
            document.getElementById('editHistoryContent').classList.add('hidden');
            document.getElementById('editHistoryEmpty').classList.add('hidden');

            const token = localStorage.getItem('token');
            if (!token) {
                throw new Error('Token tidak ditemukan');
            }

            const response = await fetch(`/api/orders/${orderId}/edit-history`, {
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Authorization': `Bearer ${token}`
                }
            });

            const result = await response.json();

            // Hide loading state
            document.getElementById('editHistoryLoading').classList.add('hidden');

            if (response.ok && result.success) {
                renderEditHistoryTable(result.data);
            } else {
                throw new Error(result.message || 'Gagal memuat riwayat edit');
            }

        } catch (error) {
            console.error('Error loading edit history:', error);
            
            // Hide loading, show empty state
            document.getElementById('editHistoryLoading').classList.add('hidden');
            document.getElementById('editHistoryContent').classList.add('hidden');
            document.getElementById('editHistoryEmpty').classList.remove('hidden');
            
            showAlert('error', 'Gagal memuat riwayat edit: ' + error.message);
        }
    }

    // Render edit history table
    function renderEditHistoryTable(edits) {
        const tableBody = document.getElementById('editHistoryTableBody');
        const contentElement = document.getElementById('editHistoryContent');
        const emptyElement = document.getElementById('editHistoryEmpty');

        // Check if there's data
        if (!edits || edits.length === 0) {
            emptyElement.classList.remove('hidden');
            return;
        }

        // Show content
        contentElement.classList.remove('hidden');

        // Render table rows
        tableBody.innerHTML = edits.map(edit => `
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 text-sm">${edit.requested_at}</td>
                <td class="px-4 py-3 text-sm">
                    <span class="px-2 py-1 text-xs font-medium rounded-full ${getEditTypeClass(edit.edit_type)}">
                        ${getEditTypeText(edit.edit_type)}
                    </span>
                </td>
                <td class="px-4 py-3 text-sm max-w-xs truncate" title="${getEditReasonText(edit.reason)}">
                    ${getEditReasonText(edit.reason)}
                    ${edit.notes ? `<br><small class="text-gray-500">${edit.notes}</small>` : ''}
                </td>
                <td class="px-4 py-3 text-sm">${edit.requester}</td>
                <td class="px-4 py-3 text-sm font-medium ${edit.total_difference >= 0 ? 'text-green-600' : 'text-red-600'}">
                    ${edit.total_difference >= 0 ? '+' : ''}${formatCurrency(edit.total_difference)}
                </td>
                <td class="px-4 py-3 text-sm">
                    <span class="px-2 py-1 text-xs font-medium rounded-full ${getEditStatusClass(edit.status)}">
                        ${getEditStatusText(edit.status)}
                    </span>
                </td>
                <td class="px-4 py-3 text-sm">
                    ${edit.finance_approver ? `
                        <div class="text-green-600">
                            <i data-lucide="check" class="w-3 h-3 inline mr-1"></i>
                            ${edit.finance_approver}
                        </div>
                        <small class="text-gray-500">${edit.finance_approved_at}</small>
                    ` : '<span class="text-gray-400">Belum</span>'}
                </td>
                <td class="px-4 py-3 text-sm">
                    ${edit.operational_approver ? `
                        <div class="text-green-600">
                            <i data-lucide="check" class="w-3 h-3 inline mr-1"></i>
                            ${edit.operational_approver}
                        </div>
                        <small class="text-gray-500">${edit.operational_approved_at}</small>
                    ` : '<span class="text-gray-400">Belum</span>'}
                </td>
                <td class="px-4 py-3 text-sm">
                    ${edit.applied_at ? `
                        <span class="text-green-600 font-medium">
                            <i data-lucide="check-circle" class="w-3 h-3 inline mr-1"></i>
                            ${edit.applied_at}
                        </span>
                    ` : (edit.status === 'rejected' ? `
                        <span class="text-red-600">
                            <i data-lucide="x-circle" class="w-3 h-3 inline mr-1"></i>
                            Ditolak
                        </span>
                        ${edit.rejector ? `<br><small class="text-gray-500">oleh ${edit.rejector}</small>` : ''}
                        ${edit.rejected_at ? `<br><small class="text-gray-500">${edit.rejected_at}</small>` : ''}
                    ` : '<span class="text-gray-400">Menunggu</span>')}
                </td>
            </tr>
        `).join('');

        // Refresh Lucide icons
        if (window.lucide) window.lucide.createIcons();
    }

    // Helper function to get edit type class
    function getEditTypeClass(editType) {
        const typeClasses = {
            'quantity_adjustment': 'bg-blue-100 text-blue-800',
            'item_modification': 'bg-yellow-100 text-yellow-800',
            'item_addition': 'bg-green-100 text-green-800',
            'item_removal': 'bg-red-100 text-red-800'
        };
        return typeClasses[editType] || 'bg-gray-100 text-gray-800';
    }

    // Helper function to get edit type text
    function getEditTypeText(editType) {
        const typeTexts = {
            'quantity_adjustment': 'Penyesuaian Qty',
            'item_modification': 'Modifikasi Item',
            'item_addition': 'Tambah Item',
            'item_removal': 'Hapus Item'
        };
        return typeTexts[editType] || editType;
    }

    // Helper function to get edit status class
    function getEditStatusClass(status) {
        const statusClasses = {
            'pending': 'bg-yellow-100 text-yellow-800',
            'approved': 'bg-green-100 text-green-800',
            'rejected': 'bg-red-100 text-red-800'
        };
        return statusClasses[status] || 'bg-gray-100 text-gray-800';
    }

    // Helper function to get edit reason text
    function getEditReasonText(reason) {
        const reasonTexts = {
            'quantity_adjustment': 'Penyesuaian Jumlah di Lokasi',
            'customer_request': 'Permintaan Tambahan Customer',
            'measurement_correction': 'Koreksi Pengukuran',
            'item_change': 'Perubahan Item',
            'other': 'Lainnya'
        };
        return reasonTexts[reason] || reason;
    }

    // Helper function to get edit status text
    function getEditStatusText(status) {
        const statusTexts = {
            'pending': 'Menunggu',
            'approved': 'Disetujui',
            'rejected': 'Ditolak'
        };
        return statusTexts[status] || status;
    }
</script>

@endsection
