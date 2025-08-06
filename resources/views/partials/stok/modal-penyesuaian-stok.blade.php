<!-- Modal Penyesuaian Stok -->
<div id="modalAdjustStock" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 items-center justify-center p-4 overflow-y-auto">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl"> <!-- Lebarkan modal -->
        <!-- Header -->
        <div class="border-b px-6 py-4 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-800">Penyesuaian Stok</h3>
            <button onclick="closeModalAdjust()" class="text-gray-500 hover:text-gray-700">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <input type="hidden" id="adjustProductId" name="adjustProductId" value="">
        <!-- Body - Gunakan container dengan max-height dan overflow-auto -->
        <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto">
            <!-- Informasi Produk - Ubah ke grey -->
            <div class="bg-gray-100 rounded-lg p-4 border border-gray-200">
                <h4 class="font-medium text-gray-800 mb-3">Informasi Produk</h4>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    
                    <div>
                        <div class="text-gray-600 mb-1">SKU</div>
                        <div class="font-medium bg-gray-50 p-2 rounded border border-gray-200" id="adjustSku">-</div>
                    </div>
                    <div>
                        <div class="text-gray-600 mb-1">Nama Produk</div>
                        <div class="font-medium bg-gray-50 p-2 rounded border border-gray-200" id="adjustProduk">-</div>
                    </div>
                    <div>
                        <div class="text-gray-600 mb-1">Outlet</div>
                        <div class="font-medium bg-gray-50 p-2 rounded border border-gray-200" id="adjustOutlet">-</div>
                    </div>
                    <div>
                        <div class="text-gray-600 mb-1">Stok Saat Ini</div>
                        <div class="font-medium bg-gray-50 p-2 rounded border border-gray-200 text-green-600" id="stokSaatIni">-</div>
                    </div>
                </div>
            </div>
            
            <!-- Form Penyesuaian - Susun horizontal -->
           <div class="bg-gray-100 rounded-lg p-4 border border-gray-200">
    <h4 class="font-medium text-gray-800 mb-3">Penyesuaian Stok</h4>
    
    <!-- Baris Jumlah dan Tipe Penyesuaian -->
    <div class="grid grid-cols-2 gap-4">
        <!-- Jumlah Penyesuaian -->
        <div>
            <label for="jumlahAdjust" class="block text-sm font-medium text-gray-700 mb-1">Jumlah Penyesuaian</label>
            <input type="number" id="jumlahAdjust" name="jumlahAdjust" 
                class="w-full border rounded-lg px-4 py-2 text-base focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                placeholder="+/- nilai">
            <p class="text-xs text-gray-500 mt-1">Gunakan tanda minus (-) untuk mengurangi stok</p>
        </div>

        <!-- Tipe Penyesuaian -->
        <div>
            <label for="tipeAdjust" class="block text-sm font-medium text-gray-700 mb-1">Tipe Penyesuaian</label>
            <select id="tipeAdjust" name="tipeAdjust" 
                class="w-full border rounded-lg px-4 py-2 text-base focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                <option value="">Pilih Tipe Penyesuaian</option>
                <option value="purchase">Pembelian</option>
                <option value="sale">Penjualan</option>
                <option value="adjustment">Penyesuaian</option>
                <option value="shipment">Kiriman Pabrik</option>
                <option value="other">Lainnya</option>
            </select>
        </div>
    </div>

    <!-- Keterangan (di bawah) -->
    <div class="mt-4">
        <label for="keteranganAdjust" class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
        <textarea id="keteranganAdjust" name="keteranganAdjust" rows="4"
            class="w-full border rounded-lg px-4 py-2 text-base focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
            placeholder="Masukkan keterangan penyesuaian (opsional)"></textarea>
    </div>
</div>

        </div>
        
        <!-- Footer -->
        <div class="border-t px-6 py-4 flex justify-end gap-3">
            <button id="btnBatalAdjust" type="button" 
                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                Batal
            </button>
            <button id="btnSubmitAdjust" type="button" 
                class="px-4 py-2 text-sm font-medium text-white bg-green-500 rounded-md hover:bg-green-600">
                Simpan Penyesuaian
            </button>
        </div>
    </div>
</div>