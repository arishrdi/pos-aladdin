<div id="modalKonfirmasiHapus" class="fixed inset-0 z-50 hidden bg-gray-900 bg-opacity-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md overflow-hidden">
        <!-- Header -->
        <div class="p-4 border-b">
            <h3 class="text-lg font-semibold text-gray-800">Konfirmasi Hapus</h3>
        </div>
        
        <!-- Content -->
        <div class="p-4">
            <div class="flex items-start gap-3">
                <div class="flex-shrink-0 p-2 bg-red-50 rounded-full">
                    <i data-lucide="alert-triangle" class="w-6 h-6 text-red-500"></i>
                </div>
                <div>
                    <h4 class="text-base font-medium text-gray-800 mb-1">Hapus Kategori</h4>
                    <p class="text-sm text-gray-600">Yakin ingin menghapus kategori "<span id="hapusNamaKategori" class="font-medium">Kategori</span>"? Data yang dihapus tidak dapat dikembalikan.</p>
                </div>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="flex items-center justify-end gap-2 p-4 border-t bg-gray-50">
            <button id="btnBatalHapus" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                Batal
            </button>
            <button id="btnKonfirmasiHapus" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700">
                Hapus
            </button>
        </div>
    </div>
</div>