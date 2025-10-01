<!-- Modal Bulk Update Distribusi Outlet -->
<div id="modalBulkUpdateDistribusi" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center" onclick="closeModal()">
    <div
        class="bg-white w-full max-w-2xl rounded-lg shadow-lg max-h-screen flex flex-col"
        onclick="event.stopPropagation()"
    >
        <!-- Header -->
        <div class="p-4 border-b">
            <h2 class="text-lg font-semibold">Update Distribusi Outlet - Bulk</h2>
            <p class="text-sm text-gray-500">Mengatur distribusi untuk <span id="bulkSelectedCount">0</span> produk terpilih</p>
        </div>

        <!-- Scrollable Content -->
        <div class="overflow-y-auto p-4 space-y-4 flex-1">
            
            <!-- Daftar Produk Terpilih -->
            <div class="p-4 border rounded shadow">
                <h3 class="font-semibold mb-2">Produk Terpilih</h3>
                <div id="bulkSelectedProductsList" class="space-y-2 max-h-32 overflow-y-auto">
                    <!-- Daftar produk terpilih akan diisi via JavaScript -->
                </div>
            </div>

            <!-- Card: Distribusi Outlet -->
            <div class="p-4 border rounded shadow">
                <div class="flex justify-between items-center mb-2">
                    <h3 class="font-semibold">Pengaturan Distribusi Outlet</h3>
                    <button id="btnSelectAllOutlets" class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700">
                        Pilih Semua
                    </button>
                </div>
                <p class="text-sm text-gray-600 mb-4">Pilih outlet yang akan mendistribusikan produk-produk terpilih</p>
                <div id="bulkOutletList" class="space-y-2 text-sm">
                    <!-- Daftar outlet akan diisi via JavaScript -->
                </div>
            </div>

            <!-- Opsi Bulk Action -->
            <div class="p-4 border rounded shadow">
                <h3 class="font-semibold mb-2">Opsi Update</h3>
                <div class="space-y-2">
                    <label class="flex items-center">
                        <input type="radio" name="bulkUpdateMode" value="add" class="mr-2" checked>
                        <span>Tambahkan ke outlet yang dipilih (tidak mengubah distribusi existing)</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="bulkUpdateMode" value="replace" class="mr-2">
                        <span>Ganti distribusi (hanya outlet yang dipilih, hapus distribusi lainnya)</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="bulkUpdateMode" value="remove" class="mr-2">
                        <span>Hapus dari outlet yang dipilih</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Footer: Tombol Aksi -->
        <div class="p-4 border-t flex justify-between">
            <div>
                <!-- Placeholder untuk tombol kiri jika diperlukan -->
            </div>
            <div class="space-x-2">
                <button id="btnBatalBulkUpdate" class="px-4 py-2 border rounded hover:bg-gray-100">Batal</button>
                <button id="btnSimpanBulkUpdate" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Simpan Perubahan</button>
            </div>
        </div>
    </div>
</div>