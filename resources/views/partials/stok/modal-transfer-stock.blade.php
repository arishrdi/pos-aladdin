<div id="modalTransferStock" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-lg max-h-[90vh] overflow-hidden flex flex-col">
        <!-- Header -->
        <div class="flex items-start justify-between mb-4">
            <h3 class="text-xl font-semibold text-gray-900 flex items-center gap-2">
                <i data-lucide="truck" class="w-6 h-6 text-green-500"></i>
                Transfer Stok
            </h3>
            <button onclick="closeModalTransfer()" class="p-1 rounded-full hover:bg-gray-100">
                <i data-lucide="x" class="w-5 h-5 text-gray-500"></i>
            </button>
        </div>
        
        <!-- Content (Scrollable) -->
        <div class="overflow-y-auto pr-2 flex-1">
            <!-- Card 1: Informasi Produk -->
            <div class="bg-gray-50 rounded-lg p-4 mb-4 border border-gray-200">
                <h4 class="text-md font-medium text-gray-800 mb-3 flex items-center gap-2">
                    <i data-lucide="package" class="w-5 h-5 text-gray-600"></i>
                    Informasi Produk
                </h4>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">SKU</p>
                        <p id="transferSku" class="font-medium text-gray-800">-</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Nama Produk</p>
                        <p id="transferProduk" class="font-medium text-gray-800">-</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Stok Saat Ini</p>
                        <p id="stokTersedia" class="font-medium text-gray-800">-</p>
                    </div>
                </div>
            </div>
            
            <!-- Card 2: Detail Transfer -->
            <div class="bg-gray-50 rounded-lg p-4 mb-4 border border-gray-200">
                <h4 class="text-md font-medium text-gray-800 mb-3 flex items-center gap-2">
                    <i data-lucide="clipboard-list" class="w-5 h-5 text-gray-600"></i>
                    Detail Transfer
                </h4>
                
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <!-- Outlet Asal -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Outlet Asal</label>
                            <div class="w-full border rounded-lg px-4 py-2 bg-gray-100">
                                <p id="outletAsal" class="font-medium text-gray-800">-</p>
                            </div>
                        </div>
                        
                        <!-- Outlet Tujuan (Dropdown) -->
                        <div>
                            <label for="tujuanTransfer" class="block text-sm font-medium text-gray-700 mb-1">Outlet Tujuan</label>
                            <select id="tujuanTransfer" class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                <option value="">Pilih Outlet Tujuan</option>
                                <!-- Options akan diisi secara dinamis -->
                            </select>
                        </div>

                        <!-- Tambahkan input hidden untuk menyimpan ID produk dan outlet asal -->
                        <input type="hidden" id="productId">
                        <input type="hidden" id="sourceOutletId">
                    </div>
                    
                    <!-- Jumlah Transfer -->
                    <div>
                        <label for="jumlahTransfer" class="block text-sm font-medium text-gray-700 mb-1">Jumlah Transfer</label>
                        <input 
                            type="number" 
                            id="jumlahTransfer" 
                            min="1" 
                            class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                            placeholder="Masukkan jumlah"
                            oninput="validateTransferAmount(this)"
                        >
                        <p class="text-xs text-gray-500 mt-1">Stok tersedia: <span id="stokTersediaLabel" class="font-medium">0</span></p>
                    </div>
                    
                    <!-- Keterangan -->
                    <div>
                        <label for="catatanTransfer" class="block text-sm font-medium text-gray-700 mb-1">Keterangan Transfer</label>
                        <textarea 
                            id="catatanTransfer" 
                            rows="2" 
                            class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                            placeholder="Contoh: Stok untuk promo akhir bulan"
                        ></textarea>
                        <p class="text-xs text-gray-500 mt-1">Masukkan alasan transfer stok ini</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Footer (Fixed at bottom) -->
        <div class="mt-4 pt-4 border-t border-gray-200 flex justify-end gap-3">
            <button id="btnBatalTransfer" type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none">
                Batal
            </button>
            <button id="btnSubmitTransfer" type="button" class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-md shadow-sm hover:bg-green-700 focus:outline-none flex items-center gap-2">
                <i data-lucide="truck" class="w-4 h-4"></i> Proses Transfer
            </button>
        </div>
    </div>
</div>