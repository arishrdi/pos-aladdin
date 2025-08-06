<!-- Modal Tambah Kategori -->
<div id="modalTambahKategori" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center" onclick="closeModal('modalTambahKategori')">
    <div
        class="bg-white w-full max-w-md rounded-lg shadow-lg max-h-screen flex flex-col"
        onclick="event.stopPropagation()"
    >
        <!-- Header -->
        <div class="p-4 border-b">
            <h2 class="text-lg font-semibold">Tambah Kategori Baru</h2>
            <span class="text-md text-gray-400 text-[14px]">Isi detail kategori baru di bawah ini. Klik simpan setelah selesai.</span>
        </div>

        <!-- Scrollable Content -->
        <div class="overflow-y-auto p-4 space-y-4 flex-1">
            <!-- Card: Form Kategori -->
            <div class="space-y-4">
                <!-- Nama Kategori -->
                <div>
                    <label class="block font-medium mb-1">Nama Kategori</label>
                    <input 
                        type="text" 
                        id="namaKategori" 
                        class="w-full border border-gray-400 rounded-lg px-3 py-2 text-sm hover:border-2 hover:border-green-600 focus:border-green-600 focus:ring-green-500" 
                        placeholder="Contoh: Lokal, Turkiye"
                    >
                </div>

                <!-- Deskripsi Kategori -->
                <div>
                    <label class="block font-medium mb-1">Deskripsi</label>
                    <textarea 
                        id="deskripsiKategori" 
                        class="w-full border border-gray-400 rounded-lg px-3 py-2 text-sm hover:border-2 hover:border-green-600 focus:border-green-600 focus:ring-green-500" 
                        rows="3" 
                        placeholder="Deskripsi singkat tentang kategori ini"
                    ></textarea>
                </div>
            </div>
        </div>

        <!-- Footer: Tombol Aksi -->
        <div class="p-4 border-t flex justify-end space-x-2">
            <button 
                id="btnBatalModalKategori" 
                class="px-4 py-2 border rounded-lg hover:bg-gray-100"
                onclick="closeModal('modalTambahKategori')"
            >
                Batal
            </button>
            <button 
                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700"
                onclick="tambahKategori()"
            >
                + Tambah Kategori
            </button>
        </div>
    </div>
</div>