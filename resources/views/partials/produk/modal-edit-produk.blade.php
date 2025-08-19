<!-- Modal Edit Produk -->
<div id="modalEditProduk" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center" onclick="closeModal()">
    <div
        class="bg-white w-full max-w-4xl rounded-lg shadow-lg max-h-screen flex flex-col"
        onclick="event.stopPropagation()"
    >
        <!-- Header -->
        <div class="p-4 border-b">
            <h2 class="text-lg font-semibold">Edit Produk</h2>
            <p class="text-sm text-gray-500">ID Produk: <span id="editProdukId">-</span></p>
        </div>

        <!-- Scrollable Content -->
        <div class="overflow-y-auto p-4 space-y-4 flex-1">

            <!-- Card: Informasi Dasar -->
            <div class="p-4 border rounded shadow">
                <div class="mb-4">
                    <label class="block font-medium mb-1">Nama Produk</label>
                    <input type="text" id="editNamaProduk" class="w-full border rounded px-3 py-2 text-sm" placeholder="Nama produk">
                </div>
                <div class="mb-4">
                    <label class="block font-medium mb-1">SKU Produk</label>
                    <input type="text" id="editSkuProduk" class="w-full border rounded px-3 py-2 text-sm" placeholder="Kode unik produk">
                </div>
                <div class="mb-4">
                    <label class="block font-medium mb-1">Barcode</label>
                    <div class="flex gap-2">
                        <input type="text" id="editBarcode" class="flex-1 border rounded px-3 py-2 text-sm" placeholder="Kode barcode">
                        <button type="button" id="generateBarcodeBtnEdit" class="px-3 py-2 bg-gray-100 border rounded hover:bg-gray-200 text-sm whitespace-nowrap">
                            Generate
                        </button>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Biarkan kosong untuk generate otomatis</p>
                </div>
                <div class="mb-4">
                    <label class="block font-medium mb-1">Deskripsi Produk</label>
                    <textarea id="editDeskripsi" class="w-full border rounded px-3 py-2 text-sm" placeholder="Deskripsi produk"></textarea>
                </div>
            </div>

            <!-- Card: Harga & Kategori -->
            <div class="p-4 border rounded shadow">
                <div class="space-y-4">
                    <div>
                        <label class="block font-medium mb-1">Harga Jual</label>
                        <input type="text" id="editHarga" class="w-full border rounded px-3 py-2 text-sm" placeholder="Rp">
                    </div>
                    <div>
                        <label class="block font-medium mb-1">Kategori</label>
                        <select id="editKategori" class="w-full border rounded px-3 py-2 text-sm">
                            <option value="">Pilih Kategori</option>
                            <!-- Options akan diisi via JavaScript -->
                        </select>
                    </div>
                    <div>
                        <label class="block font-medium mb-1">Satuan</label>
                        <select id="editUnitType" class="w-full border rounded px-3 py-2 text-sm" name="unit_type">
                            <option value="">Pilih Satuan</option>
                            <option value="meter">Meter</option>
                            <option value="pcs">Pcs</option>
                            <option value="unit">Unit</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Meter: angka desimal (10.3), Pcs/Unit: angka bulat (10)</p>
                    </div>
                </div>
            </div>

            <!-- Card: Manajemen Stok -->
            <div class="p-4 border rounded shadow">
                <div class="space-y-4">
                    <div>
                        <label class="block font-medium mb-1">Stok</label>
                        <input
                            type="number"
                            id="editStok"
                            class="w-full border rounded px-3 py-2 text-sm bg-gray-100 text-gray-500 cursor-not-allowed"
                            disabled
                        >
                    </div>                                        
                    <div>
                        <label class="block font-medium mb-1">Stok Minimum</label>
                        <input type="number" step="0.1" id="editStokMinimum" class="w-full border rounded px-3 py-2 text-sm" placeholder="contoh: 10 atau 5.5">
                    </div>
                </div>
            </div>

            <!-- Card: Distribusi Outlet -->
            <div class="p-4 border rounded shadow">
                <h3 class="font-semibold mb-2">Distribusi Outlet</h3>
                <div id="editOutletList" class="space-y-2 text-sm" >
                    <!-- Daftar outlet akan diisi via JavaScript -->
                </div>
            </div>

            <!-- Card: Gambar Produk -->
            <div class="p-4 border rounded shadow">
                <label class="block font-medium mb-1">Gambar Produk</label>
                <div class="mb-3">
                    <img id="editGambarPreview" src="" alt="Preview Gambar" class="max-w-full h-32 object-contain border rounded hidden">
                </div>
                <input type="file" id="editGambar" class="w-full text-sm" accept="image/*">
                <input type="hidden" id="editGambarCurrent">
            </div>

            <!-- Status Produk -->
            <div class="p-4 border rounded shadow">
                <label class="block font-medium mb-1">Status Produk</label>
                <select id="editStatus" class="w-full border rounded px-3 py-2 text-sm">
                    <option value="active">Aktif</option>
                    <option value="inactive">Nonaktif</option>
                </select>
            </div>
        </div>

        <!-- Footer: Tombol Aksi -->
        <div class="p-4 border-t flex justify-between">
            <div>
                {{-- <button id="btnHapusProduk" class="px-4 py-2 text-red-600 hover:text-red-800 text-sm">
                    <i data-lucide="trash-2" class="w-4 h-4 mr-1"></i> Hapus Produk
                </button> --}}
            </div>
            <div class="space-x-2">
                <button id="btnBatalEdit" class="px-4 py-2 border rounded hover:bg-gray-100">Batal</button>
                <button id="btnSimpanEdit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Simpan Perubahan</button>
            </div>
        </div>
    </div>
</div>