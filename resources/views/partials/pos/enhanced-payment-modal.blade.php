<!-- Enhanced Payment Modal dengan semua fitur baru -->
<div id="paymentModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full mx-4 max-h-[95vh] overflow-y-auto">
        <div id="paymentModalContent" class="p-6">
            <!-- Content will be dynamically rendered by PaymentManager -->
        </div>
    </div>
</div>

<!-- Invoice Modal Enhanced -->
<div id="invoiceModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="p-6">
            <div class="text-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-2">{{ $outletName ?? 'Aladdin Karpet' }}</h2>
                <p class="text-gray-600 text-sm">Struk Pembayaran</p>
            </div>
            
            <!-- Invoice Header -->
            <div class="border-b pb-4 mb-4">
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <div><strong>No. Invoice:</strong> <span id="invoiceNumber">-</span></div>
                        <div><strong>No. Order:</strong> <span id="orderNumber">-</span></div>
                        <div><strong>Tanggal:</strong> <span id="invoiceDate">-</span></div>
                    </div>
                    <div>
                        <div><strong>Kasir:</strong> <span id="cashierName">{{ auth()->user()->name ?? '-' }}</span></div>
                        <div><strong>Jenis Transaksi:</strong> <span id="transactionTypeDisplay">Regular</span></div>
                    </div>
                </div>
            </div>

            <!-- Customer Info -->
            <div id="customerInfo" class="mb-4">
                <!-- Will be populated dynamically -->
            </div>

            <!-- Items List -->
            <div class="border-b pb-4 mb-4">
                <h4 class="font-semibold mb-3">Detail Pembelian</h4>
                <div id="invoiceItems" class="space-y-2">
                    <!-- Items will be populated dynamically -->
                </div>
            </div>

            <!-- Invoice Totals -->
            <div class="space-y-2 mb-6">
                <div class="flex justify-between text-sm">
                    <span>Subtotal:</span>
                    <span id="invoiceSubtotal">Rp 0</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span>Diskon:</span>
                    <span id="invoiceDiscount">Rp 0</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span>Pajak:</span>
                    <span id="invoiceTax">Rp 0</span>
                </div>
                <div class="border-t pt-2 flex justify-between font-semibold text-lg">
                    <span>Total:</span>
                    <span id="invoiceTotal" class="text-green-600">Rp 0</span>
                </div>
            </div>

            <!-- Payment Info -->
            <div class="bg-gray-50 rounded p-4 mb-6">
                <h4 class="font-semibold mb-2">Informasi Pembayaran</h4>
                <div class="text-sm space-y-1">
                    <div><strong>Metode:</strong> <span id="paymentMethod">-</span></div>
                    <div id="paymentDetails">
                        <!-- Payment specific details -->
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-center space-x-3">
                <button onclick="window.print()" 
                    class="px-6 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 transition-colors">
                    <i data-lucide="printer" class="w-4 h-4 inline mr-2"></i>
                    Cetak Struk
                </button>
                <button onclick="closeModal('invoiceModal')" 
                    class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Supervisor Approval Modal -->
<div id="supervisorApprovalModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="p-6">
            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="clock" class="w-8 h-8 text-yellow-600"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Menunggu Persetujuan</h3>
                <p class="text-gray-600 text-sm">Pembayaran transfer sedang menunggu konfirmasi dari supervisor.</p>
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <div class="flex items-start">
                    <i data-lucide="info" class="w-5 h-5 text-blue-600 mt-0.5 mr-3"></i>
                    <div class="text-sm text-blue-800">
                        <p class="font-medium mb-1">Informasi:</p>
                        <ul class="list-disc list-inside space-y-1">
                            <li>Bukti transfer telah diterima</li>
                            <li>Customer akan mendapat notifikasi setelah disetujui</li>
                            <li>Transaksi dapat dibatalkan jika bukti tidak valid</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="flex justify-center">
                <button onclick="closeModal('supervisorApprovalModal')" 
                    class="px-6 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition-colors">
                    Mengerti
                </button>
            </div>
        </div>
    </div>
</div>

<style>
/* Print styles for invoice */
@media print {
    body * {
        visibility: hidden;
    }
    
    #invoiceModal, #invoiceModal * {
        visibility: visible;
    }
    
    #invoiceModal {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        height: auto;
        background: white !important;
    }
    
    #invoiceModal .bg-black {
        background: transparent !important;
    }
    
    #invoiceModal .rounded-lg {
        border-radius: 0 !important;
        box-shadow: none !important;
    }
    
    /* Hide buttons when printing */
    #invoiceModal button {
        display: none !important;
    }
}
</style>