<div id="modalEditKategori" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center" onclick="closeModal('modalEditKategori')">
    <div class="bg-white w-full max-w-md rounded-lg shadow-lg max-h-screen flex flex-col" onclick="event.stopPropagation()">
        <!-- Header -->
        <div class="p-4 border-b">
            <h2 class="text-lg font-semibold">Edit Kategori</h2>
            <p class="text-md text-gray-400 text-[14px]">Ubah detail kategori di bawah ini. Klik simpan setelah selesai.</p>
        </div>

        <!-- Scrollable Content -->
        <div class="overflow-y-auto p-4 space-y-4 flex-1">
            <!-- Input hidden untuk menyimpan ID -->
            <input type="hidden" id="editKategoriId">
            
            <!-- Card: Form Kategori -->
            <div class="space-y-4">
                <!-- Nama Kategori -->
                <div>
                    <label class="block font-medium mb-1">Nama Kategori</label>
                    <input 
                        type="text" 
                        id="editNamaKategori" 
                        class="w-full border border-gray-400 rounded-lg px-3 py-2 text-sm hover:border-2 hover:border-green-600 focus:border-green-600 focus:ring-green-500" 
                        placeholder="Nama kategori"
                    >
                </div>

                <!-- Deskripsi Kategori -->
                <div>
                    <label class="block font-medium mb-1">Deskripsi</label>
                    <textarea 
                        id="editDeskripsiKategori" 
                        class="w-full border border-gray-400 rounded-lg px-3 py-2 text-sm hover:border-2 hover:border-green-600 focus:border-green-600 focus:ring-green-500" 
                        rows="3" 
                        placeholder="Deskripsi kategori"
                    ></textarea>
                </div>
            </div>
        </div>

        <!-- Footer: Tombol Aksi -->
        <div class="p-4 border-t flex justify-between">
            <div>
                <button 
                    id="btnHapusKategori" 
                    class="px-4 py-2 text-red-600 hover:text-red-800 text-sm"
                    onclick="hapusKategori()"
                >
                </button>
            </div>
            <div class="space-x-2">
                <button 
                    id="btnBatalEditKategori" 
                    class="px-4 py-2 border rounded-lg hover:bg-gray-100"
                    onclick="closeModal('modalEditKategori')"
                >
                    Batal
                </button>
                <button 
                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700"
                    onclick="simpanPerubahanKategori()"
                >
                    Simpan Perubahan
                </button>
            </div>
        </div>
    </div>
</div>