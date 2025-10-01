<div id="modalTambahProduk" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center">
    <div class="bg-white w-full max-w-4xl rounded-xl shadow-lg max-h-screen flex flex-col" onclick="event.stopPropagation()">
        <!-- Header -->
        <div class="p-6 border-b">
            <h2 class="text-xl font-semibold">Tambah Produk Baru</h2>
            <p class="text-sm text-gray-500">Lengkapi informasi produk baru dengan detail yang sesuai.</p>
        </div>

        <!-- Scrollable Content -->
        <div class="overflow-y-auto p-6 space-y-6 flex-1">
            <form id="tambahProdukForm" enctype="multipart/form-data">
                <!-- Card: Informasi Dasar -->
                <div class="p-5 border rounded-lg shadow-sm">
                    <h3 class="font-semibold mb-4 text-gray-700">Informasi Dasar</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-medium mb-1" for="nama">Nama Produk <span class="text-red-500">*</span></label>
                            <input type="text" id="nama" name="name" class="w-full border rounded-lg px-4 py-2 text-sm" placeholder="Contoh: Karpet Turki" required>
                        </div>
                        <div>
                            <label class="block font-medium mb-1" for="sku">SKU Produk <span class="text-red-500">*</span></label>
                            <input type="text" id="sku" name="sku" class="w-full border rounded-lg px-4 py-2 text-sm" placeholder="Kode unik produk (wajib diisi)" required>
                        </div>
                        <div>
                            <label class="block font-medium mb-1" for="barcode">Barcode</label>
                            <div class="flex gap-2">
                                <input type="text" id="barcode" name="barcode" class="flex-1 border rounded-lg px-4 py-2 text-sm" placeholder="Kode barcode">
                                <button type="button" id="generateBarcodeBtn" class="px-3 py-2 bg-gray-100 border rounded-lg hover:bg-gray-200 text-sm whitespace-nowrap">
                                    Generate
                                </button>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Biarkan kosong untuk generate otomatis</p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block font-medium mb-1" for="deskripsi">Deskripsi Produk</label>
                            <textarea id="deskripsi" name="description" class="w-full border rounded-lg px-4 py-2 text-sm" placeholder="Deskripsi singkat... (opsional)"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Card: Harga & Kategori -->
                <div class="p-5 border rounded-lg shadow-sm">
                    <h3 class="font-semibold mb-4 text-gray-700">Harga & Kategori</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block font-medium mb-1" for="harga">Harga Jual <span class="text-red-500">*</span></label>
                            <input type="number" id="harga" name="price" class="w-full border rounded-lg px-4 py-2 text-sm" placeholder="Rp" required>
                        </div>
                        <div>
                            <label class="block font-medium mb-1" for="kategori">Kategori <span class="text-red-500">*</span></label>
                            <select id="kategori" name="category_id" class="w-full border rounded-lg px-4 py-2 text-sm" required>
                                <option value="">Pilih Kategori</option>
                                <!-- Options akan diisi via JavaScript -->
                            </select>
                        </div>
                        <div>
                            <label class="block font-medium mb-1" for="unitType">Satuan <span class="text-red-500">*</span></label>
                            <select id="unitType" name="unit_type" class="w-full border rounded-lg px-4 py-2 text-sm" required>
                                <option value="">Pilih Satuan</option>
                                <option value="meter">Meter</option>
                                <option value="pcs">Pcs</option>
                                <option value="unit">Unit</option>
                                <option value="pasang">Pasang</option>
                                <option value="kirim">Kirim</option>
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Meter: angka desimal (10.3), Pcs/Unit: angka bulat (10), Pasang/Kirim: selalu 1</p>
                        </div>
                    </div>
                </div>

                <!-- Card: Manajemen Stok -->
                <div class="p-5 border rounded-lg shadow-sm">
                    <h3 class="font-semibold mb-4 text-gray-700">Manajemen Stok</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-medium mb-1" for="stok">Stok <span class="text-red-500">*</span></label>
                            <input type="number" step="0.1" id="stok" name="quantity" class="w-full border rounded-lg px-4 py-2 text-sm" value="0" placeholder="contoh: 100 atau 50.5" required>
                        </div>
                        <div>
                            <label class="block font-medium mb-1" for="stokMinimum">Stok Minimum</label>
                            <input type="number" step="0.1" id="stokMinimum" name="min_stock" class="w-full border rounded-lg px-4 py-2 text-sm" value="0" placeholder="contoh: 10 atau 5.5">
                        </div>
                    </div>
                </div>

                <!-- Card: Distribusi Outlet -->
                <div class="p-5 border rounded-lg shadow-sm">
                    <h3 class="font-semibold mb-4 text-gray-700">Distribusi Outlet <span class="text-red-500">*</span></h3>
                    <div id="outletCheckboxes" class="space-y-2 text-sm" required>
                        <!-- Checkboxes will be added dynamically -->
                        <p class="text-gray-500 italic">Loading outlets...</p>
                    </div>
                </div>

                <!-- Card: Gambar Produk -->
                <div class="p-5 border rounded-lg shadow-sm">
                    <h3 class="font-semibold mb-4 text-gray-700">Gambar Produk</h3>
                    <input type="file" id="gambar" name="image" class="w-full text-sm" accept="image/*">
                    <p class="text-gray-500 text-xs mt-1">Format: JPG, PNG. Ukuran maksimal: 2MB</p>
                </div>

                <!-- Status Produk -->
                <div class="p-5 border rounded-lg shadow-sm">
                    <h3 class="font-semibold mb-4 text-gray-700">Status Produk</h3>
                    <select id="status" name="is_active" class="w-full border rounded-lg px-4 py-2 text-sm">
                        <option value="1" selected>Aktif</option>
                        <option value="0">Nonaktif</option>
                    </select>
                </div>
            </form>
        </div>

        <!-- Footer: Tombol Aksi -->
        <div class="p-6 border-t flex justify-end gap-3">
            <button id="btnBatalModal" class="px-4 py-2 border rounded hover:bg-gray-100">Batal</button>
            <button type="button" id="btnSimpanProduk" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">+ Tambah Produk</button>
        </div>
    </div>
</div>