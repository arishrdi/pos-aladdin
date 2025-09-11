<div id="modalTambahMasjid" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center" onclick="closeModalTambah()">
  <div class="bg-white w-full max-w-md rounded-xl shadow-lg max-h-screen flex flex-col" onclick="event.stopPropagation()">
    
    <!-- Header -->
    <div class="p-6 border-b">
      <h2 class="text-xl font-semibold">Tambah Masjid Baru</h2>
      <p class="text-sm text-gray-500">Tambahkan data masjid baru dengan mengisi detail di bawah ini.</p>
    </div>

    <!-- Scrollable Content -->
    <div class="overflow-y-auto p-6 space-y-4 flex-1">
      <form id="formTambahMasjid">
        <!-- Nama Masjid -->
        <div>
          <label class="block font-medium mb-1">Nama Masjid <span class="text-red-500">*</span></label>
          <input type="text" id="namaMasjid" class="w-full border rounded-lg px-4 py-2 text-sm" placeholder="Nama masjid" required>
          <p id="errorNama" class="text-red-500 text-xs mt-1 hidden">Nama masjid wajib diisi</p>
        </div>

        <!-- Alamat -->
        <div>
          <label class="block font-medium mb-1">Alamat <span class="text-red-500">*</span></label>
          <textarea id="alamatMasjid" class="w-full border rounded-lg px-4 py-2 text-sm" placeholder="Alamat masjid" rows="3" required></textarea>
          <p id="errorAlamat" class="text-red-500 text-xs mt-1 hidden">Alamat masjid wajib diisi</p>
        </div>
      </form>
    </div>

    <!-- Footer -->
    <div class="p-6 border-t flex justify-end gap-3">
      <button id="btnBatalModalTambah" class="px-4 py-2 border rounded hover:bg-gray-100">Batal</button>
      <button id="btnTambahMasjid" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 flex items-center gap-2">
        <i data-lucide="plus" class="w-4 h-4"></i>
        <span>Simpan</span>
      </button>
    </div>
  </div>
</div>

<script>
    // Fungsi untuk validasi form
    function validateForm() {
      let isValid = true;

      // Validasi nama masjid 
      const namaMasjid = document.getElementById('namaMasjid');
      const errorNama = document.getElementById('errorNama');
      if (!namaMasjid.value.trim()) {
        errorNama.classList.remove('hidden');
        namaMasjid.classList.add('border-red-500');
        isValid = false;
      } else {
        errorNama.classList.add('hidden');
        namaMasjid.classList.remove('border-red-500');
      }

      // Validasi alamat
      const alamatMasjid = document.getElementById('alamatMasjid');
      const errorAlamat = document.getElementById('errorAlamat');
      if (!alamatMasjid.value.trim()) {
        errorAlamat.classList.remove('hidden');
        alamatMasjid.classList.add('border-red-500');
        isValid = false;
      } else {
        errorAlamat.classList.add('hidden');
        alamatMasjid.classList.remove('border-red-500');
      }

      return isValid;
    }

    // Fungsi untuk reset form
    function resetForm() {
      document.getElementById('namaMasjid').value = '';
      document.getElementById('alamatMasjid').value = '';

      // Reset error messages dan styling
      document.querySelectorAll('[id^="error"]').forEach(el => el.classList.add('hidden'));
      document.querySelectorAll('.border-red-500').forEach(el => el.classList.remove('border-red-500'));
    }

    // Event listener untuk tombol tambah
    document.getElementById('btnTambahMasjid')?.addEventListener('click', submitForm);

    // Submit form saat tekan enter
    document.querySelectorAll('#modalTambahMasjid input, #modalTambahMasjid textarea').forEach(input => {
      input.addEventListener('keypress', e => {
        if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA') {
          submitForm();
        }
      });
    });

    // Fungsi untuk menutup modal
    function closeModalTambah() {
      document.getElementById('modalTambahMasjid').classList.add('hidden');
      resetForm();
    }

    // Event listener untuk tombol batal
    document.getElementById('btnBatalModalTambah')?.addEventListener('click', closeModalTambah);
</script>