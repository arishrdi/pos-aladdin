<div id="stockHistoryModal" class="modal fixed inset-0 z-50 overflow-y-auto hidden" aria-hidden="true">
    <div class="modal-overlay absolute w-full h-full bg-gray-900 opacity-50"></div>
    
    <div class="modal-container bg-white w-full max-w-3xl mx-auto rounded shadow-lg z-50 my-8 relative max-h-[90vh] overflow-y-auto">
        <!-- Modal header -->
        <div class="modal-header bg-gray-100 px-6 py-4 border-b flex items-center justify-between">
            <h3 class="text-xl font-bold text-gray-800">Detail Riwayat Stok</h3>
            <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>
        
        <!-- Modal content -->
        <div class="modal-content p-6">
            <p class="text-sm text-gray-600 mb-6">Informasi lengkap mengenai riwayat stok produk</p>
            
            <!-- Product Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div>
                    <h4 class="text-sm font-medium text-gray-600 mb-1">Nama Produk</h4>
                    <p id="modalProductName" class="text-lg font-semibold text-gray-800">-</p>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-600 mb-1">SKU</h4>
                    <p id="modalSKU" class="text-lg font-semibold text-gray-800">-</p>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-600 mb-1">Stok Akhir Periode</h4>
                    <p id="modalEndStock" class="text-lg font-semibold text-gray-800">-</p>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-600 mb-1">Total Perubahan</h4>
                    <p id="modalTotalChange" class="text-lg font-semibold text-gray-800">-</p>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-600 mb-1">Total Entri</h4>
                    <p id="modalTotalEntries" class="text-lg font-semibold text-gray-800">-</p>
                </div>
            </div>
            
            <!-- History Entries -->
            <div>
                <h4 class="text-lg font-semibold text-gray-800 mb-3">Detail Entri</h4>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="text-left text-gray-700 bg-gray-50">
                            <tr>
                                <th class="py-3 font-bold px-4">Tanggal</th>
                                <th class="py-3 font-bold px-4 text-right">Stok Sebelum</th>
                                <th class="py-3 font-bold px-4 text-right">Stok Sesudah</th>
                                <th class="py-3 font-bold px-4 text-right">Perubahan</th>
                                <th class="py-3 font-bold px-4">Catatan</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700 divide-y" id="historyEntries">
                            <!-- Entries will be populated here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Modal footer -->
        <div class="modal-footer bg-gray-100 px-6 py-4 border-t flex justify-end">
            <button onclick="closeModal()" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600">
                Tutup
            </button>
        </div>
    </div>
</div>