<div id="modalEditMasjid" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center" onclick="closeModalEdit()">
  <div class="bg-white w-full max-w-md rounded-xl shadow-lg max-h-screen flex flex-col" onclick="event.stopPropagation()">
    
    <!-- Header -->
    <div class="p-6 border-b">
      <h2 class="text-xl font-semibold">Edit Masjid</h2>
      <p class="text-sm text-gray-500">Ubah data masjid dengan mengisi detail di bawah ini.</p>
    </div>

    <!-- Scrollable Content -->
    <div class="overflow-y-auto p-6 space-y-4 flex-1">
      <form id="formEditMasjid">
        <input type="hidden" id="masjidIdToEdit" value="">
        
        <!-- Nama Masjid -->
        <div>
          <label class="block font-medium mb-1">Nama Masjid <span class="text-red-500">*</span></label>
          <input type="text" id="editNamaMasjid" class="w-full border rounded-lg px-4 py-2 text-sm" placeholder="Nama masjid" required>
          <p id="editErrorNama" class="text-red-500 text-xs mt-1 hidden">Nama masjid wajib diisi</p>
        </div>

        <!-- Alamat -->
        <div>
          <label class="block font-medium mb-1">Alamat <span class="text-red-500">*</span></label>
          <textarea id="editAlamatMasjid" class="w-full border rounded-lg px-4 py-2 text-sm" placeholder="Alamat masjid" rows="3" required></textarea>
          <p id="editErrorAlamat" class="text-red-500 text-xs mt-1 hidden">Alamat masjid wajib diisi</p>
        </div>
      </form>
    </div>

    <!-- Footer -->
    <div class="p-6 border-t flex justify-end gap-3">
      <button id="btnBatalModalEdit" class="px-4 py-2 border rounded hover:bg-gray-100">Batal</button>
      <button id="btnEditMasjid" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 flex items-center gap-2">
        <i data-lucide="save" class="w-4 h-4"></i>
        <span>Update</span>
      </button>
    </div>
  </div>
</div>

<script>
    // Fungsi untuk validasi form edit
    function validateEditForm() {
      let isValid = true;

      // Validasi nama masjid 
      const editNamaMasjid = document.getElementById('editNamaMasjid');
      const editErrorNama = document.getElementById('editErrorNama');
      if (!editNamaMasjid.value.trim()) {
        editErrorNama.classList.remove('hidden');
        editNamaMasjid.classList.add('border-red-500');
        isValid = false;
      } else {
        editErrorNama.classList.add('hidden');
        editNamaMasjid.classList.remove('border-red-500');
      }

      // Validasi alamat
      const editAlamatMasjid = document.getElementById('editAlamatMasjid');
      const editErrorAlamat = document.getElementById('editErrorAlamat');
      if (!editAlamatMasjid.value.trim()) {
        editErrorAlamat.classList.remove('hidden');
        editAlamatMasjid.classList.add('border-red-500');
        isValid = false;
      } else {
        editErrorAlamat.classList.add('hidden');
        editAlamatMasjid.classList.remove('border-red-500');
      }

      return isValid;
    }

    // Event listener untuk tombol edit
    document.getElementById('btnEditMasjid')?.addEventListener('click', function() {
        submitEditMasjid();
    });

    // Submit form saat tekan enter
    document.querySelectorAll('#modalEditMasjid input, #modalEditMasjid textarea').forEach(input => {
      input.addEventListener('keypress', e => {
        if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA') {
          submitEditMasjid();
        }
      });
    });

    // Event listener untuk tombol batal
    document.getElementById('btnBatalModalEdit')?.addEventListener('click', closeModalEdit);
</script>