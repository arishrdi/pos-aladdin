<div id="transactionDetailModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl max-h-[90vh] overflow-y-auto">
        <!-- Modal Header -->
        <div class="border-b px-6 py-4 flex items-center justify-between">
            <h3 class="text-xl font-bold text-gray-800">Detail Transaksi</h3>
            <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>
        
        <!-- Modal Content -->
        <div class="p-6">
            <!-- Transaction Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <p class="text-sm text-gray-500">Nomor Invoice</p>
                    <p id="modalInvoiceNumber" class="font-medium">-</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Tanggal/Waktu</p>
                    <p id="modalTransactionDate" class="font-medium">-</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Kasir</p>
                    <p id="modalCashierName" class="font-medium">-</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Metode Pembayaran</p>
                    <p class="font-medium">
                        <span id="modalPaymentMethod" class="px-2 py-1 rounded-full text-xs">-</span>
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Status</p>
                    <p class="font-medium">
                        <span id="modalStatus" class="px-2 py-1 rounded-full text-xs">-</span>
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Member</p>
                    <p id="modalMember" class="font-medium">-</p>
                </div>
            </div>
            
            <!-- Items Table -->
            <div class="border rounded-lg overflow-hidden mb-6">
                <table class="w-full text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="py-3 px-4 text-left">Produk</th>
                            <th class="py-3 px-4 text-left">SKU</th>
                            <th class="py-3 px-4 text-center">Qty</th>
                            <th class="py-3 px-4 text-right">Harga</th>
                            <th class="py-3 px-4 text-right">Diskon</th>
                            <th class="py-3 px-4 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody id="modalItemsTableBody" class="divide-y">
                        <!-- Items will be populated dynamically -->
                    </tbody>
                </table>
            </div>
            
            <!-- Summary -->
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <h4 class="font-medium text-gray-800 mb-2">Catatan Transaksi</h4>
                        <p class="text-sm text-gray-600">Tidak ada catatan</p>
                    </div>
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="text-gray-600">Subtotal</span>
                            <span id="modalSubtotal" class="font-medium">Rp 0</span>
                        </div>
                        <div class="flex justify-between mb-1">
                            <span class="text-gray-600">Pajak</span>
                            <span id="modalTax" class="font-medium">Rp 0</span>
                        </div>
                        <div class="flex justify-between mb-1">
                            <span class="text-gray-600">Diskon</span>
                            <span id="modalDiscount" class="font-medium">Rp 0</span>
                        </div>
                        <div class="flex justify-between mb-1">
                            <span class="text-gray-600">Total Dibayar</span>
                            <span id="modalTotalPaid" class="font-medium">Rp 0</span>
                        </div>
                        <div class="flex justify-between mb-1">
                            <span class="text-gray-600">Kembalian</span>
                            <span id="modalChange" class="font-medium">Rp 0</span>
                        </div>
                        <div class="flex justify-between border-t pt-2 mt-2">
                            <span class="text-gray-800 font-bold">Total</span>
                            <span id="modalTotal" class="text-green-600 font-bold">Rp 0</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Modal Footer -->
        {{-- <div class="border-t px-6 py-4 flex justify-end gap-3">
            <button onclick="closeModal()" class="px-4 py-2 border rounded-lg text-gray-700 hover:bg-gray-100">
                Tutup
            </button>
            <button class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 flex items-center gap-2">
                <i data-lucide="printer" class="w-5 h-5"></i>
                Cetak Ulang
            </button>
        </div> --}}
    </div>
</div>

<script>
    // Close modal when clicking outside
    document.getElementById('transactionDetailModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });
</script>