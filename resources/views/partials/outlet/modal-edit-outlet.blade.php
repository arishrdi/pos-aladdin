<div id="modalEditOutlet" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center" onclick="closeModalEdit()">
  <div
    class="bg-white w-full max-w-4xl rounded-xl shadow-lg max-h-screen flex flex-col"
    onclick="event.stopPropagation()"
  >
    <!-- Header -->
    <div class="p-6 border-b">
      <h2 class="text-xl font-semibold">Edit Outlet</h2>
      <p class="text-sm text-gray-500">Perbarui informasi outlet sesuai kebutuhan.</p>
    </div>

    <!-- Scrollable Content -->
    <div class="overflow-y-auto p-6 space-y-6 flex-1">

      <!-- Hidden ID field -->
      <input type="hidden" id="outletIdToEdit" value="">

      <!-- Informasi Dasar -->
      <div class="p-5 border rounded-lg shadow-sm bg-gray-50">
        <h3 class="font-semibold mb-4 text-gray-700">Informasi Dasar</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block font-medium mb-1">Nama Outlet <span class="text-red-500">*</span></label>
            <input type="text" class="w-full border rounded-lg px-4 py-2 text-sm" id="editNamaOutlet" placeholder="Masukkan nama outlet" required>
            <p id="errorEditNama" class="text-red-500 text-xs mt-1 hidden">Nama outlet wajib diisi</p>
          </div>
          <div>
            <label class="block font-medium mb-1">Nomor Telepon <span class="text-red-500">*</span></label>
            <input type="text" class="w-full border rounded-lg px-4 py-2 text-sm" id="editNomorTelepon" placeholder="Masukkan nomor telepon" required>
            <p id="errorEditTelepon" class="text-red-500 text-xs mt-1 hidden">Nomor telepon wajib diisi</p>
          </div>
          <div class="md:col-span-2">
            <label class="block font-medium mb-1">Alamat Lengkap <span class="text-red-500">*</span></label>
            <textarea class="w-full border rounded-lg px-4 py-2 text-sm" id="editAlamatLengkap" placeholder="Masukkan alamat lengkap" required></textarea>
            <p id="errorEditAlamat" class="text-red-500 text-xs mt-1 hidden">Alamat wajib diisi</p>
          </div>
        </div>
      </div>

      <!-- Informasi Tambahan -->
      <div class="p-5 border rounded-lg shadow-sm bg-gray-50">
        <h3 class="font-semibold mb-4 text-gray-700">Informasi Tambahan</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block font-medium mb-1">Email</label>
            <input type="email" class="w-full border rounded-lg px-4 py-2 text-sm" id="editEmail" placeholder="Masukkan email">
            <p id="errorEditEmail" class="text-red-500 text-xs mt-1 hidden">Format email tidak valid</p>
          </div>
          <div>
            <label class="block font-medium mb-1">Persentase Pajak (%)</label>
            <input type="number" class="w-full border rounded-lg px-4 py-2 text-sm" id="editPersentasePajak" placeholder="0%">
          </div>
        </div>
      </div>

      <!-- Nomor Transaksi -->
      <div class="p-5 border rounded-lg shadow-sm bg-gray-50">
        <h3 class="font-semibold mb-4 text-gray-700">Nomor Transaksi</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block font-medium mb-1">Nomor Transaksi Default</label>
            <input type="text" class="w-full border rounded-lg px-4 py-2 text-sm" id="editNoTransaksi" placeholder="Contoh: 001">
          </div>
          <div>
            <label class="block font-medium mb-1">Nama Bank</label>
            <input type="text" class="w-full border rounded-lg px-4 py-2 text-sm" id="editNamaBank" placeholder="Contoh: BCA">
          </div>
          <div class="md:col-span-2">
            <label class="block font-medium mb-1">Atas Nama</label>
            <input type="text" class="w-full border rounded-lg px-4 py-2 text-sm" id="editAtasNama" placeholder="Nama pemilik rekening">
          </div>
        </div>
      </div>

      <!-- Foto Outlet -->
      <div class="p-5 border rounded-lg shadow-sm bg-gray-50">
        <h3 class="font-semibold mb-4 text-gray-700">Foto Qris</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-start">
          <!-- Preview Foto -->
          <div>
            <p class="text-sm text-gray-600 mb-1">Foto Qris:</p>
            <div class="h-24 w-24 bg-gray-200 rounded flex items-center justify-center overflow-hidden">
              <img id="editCurrentFoto" src="#" alt="Foto Outlet" class="object-cover w-full h-full hidden">
              <i data-lucide="image" class="w-8 h-8 text-gray-400" id="editDefaultIcon"></i>
            </div>
          </div>

          <!-- Upload Foto Baru -->
          <div>
            <label class="block font-medium mb-1">Ganti Foto</label>
            <input type="file" id="editFotoOutlet" class="w-full text-sm" accept=".jpg,.jpeg,.png" onchange="previewEditFoto(this)">
            <p class="text-gray-500 text-xs mt-1">Format: JPG, PNG. Ukuran maksimal: 2MB</p>
            <p id="errorEditFoto" class="text-red-500 text-xs mt-1 hidden">Ukuran file terlalu besar (maks 2MB)</p>
          </div>
        </div>
      </div>

      <!-- Status Aktif -->
      <div class="p-5 border rounded-lg shadow-sm bg-gray-50">
        <h3 class="font-semibold mb-4 text-gray-700">Status Outlet</h3>
        <div class="flex items-center space-x-4">
          <label class="relative inline-flex items-center cursor-pointer">
            <input type="checkbox" id="editStatusAktif" class="sr-only peer">
            <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-500"></div>
            <span id="editStatusText" class="ml-3 text-sm font-medium text-gray-700">Aktif</span>
          </label>
          <span class="text-sm text-gray-500">Outlet hanya muncul jika status aktif.</span>
        </div>
      </div>
    </div>

    <!-- Footer -->
    <div class="p-6 border-t flex justify-end gap-3">
      <button id="btnBatalModalEdit" class="px-4 py-2 border rounded hover:bg-gray-100">Batal</button>
      <button id="btnSimpanPerubahan" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 flex items-center gap-2">
        <i data-lucide="save" class="w-4 h-4"></i>
        <span>Simpan Perubahan</span>
      </button>
    </div>
  </div>
</div>

<script>

// Fungsi untuk membuka modal edit
function openModalEdit(outletData) {
  const modal = document.getElementById('modalEditOutlet');
  modal.classList.remove('hidden');
  loadOutletDataToEdit(outletData);
}

// Fungsi untuk menutup modal edit
function closeModalEdit() {
  const modal = document.getElementById('modalEditOutlet');
  modal.classList.add('hidden');
}

// Fungsi untuk memuat data outlet ke modal edit
function loadOutletDataToEdit(outletData) {
  // Isi form dengan data outlet
  document.getElementById('outletIdToEdit').value = outletData.id;
  document.getElementById('editNamaOutlet').value = outletData.nama;
  document.getElementById('editNomorTelepon').value = outletData.telepon;
  document.getElementById('editAlamatLengkap').value = outletData.alamat;
  document.getElementById('editEmail').value = outletData.email;
  document.getElementById('editPersentasePajak').value = outletData.pajak;
  document.getElementById('editNoTransaksi').value = outletData.nomorTransaksi;
  document.getElementById('editNamaBank').value = outletData.namaBank;
  document.getElementById('editAtasNama').value = outletData.atasNama;
  
  // Set status toggle
  const isActive = outletData.status === "Aktif";
  document.getElementById('editStatusAktif').checked = isActive;
  updateToggleStatus();
  
  // Set foto (jika ada)
  if (outletData.foto) {
    document.getElementById('editCurrentFoto').src = outletData.foto;
    document.getElementById('editCurrentFoto').classList.remove('hidden');
    document.getElementById('editDefaultIcon').classList.add('hidden');
  }
}

// Fungsi untuk update status toggle
function updateToggleStatus() {
  const toggle = document.getElementById('editStatusAktif');
  const statusText = document.getElementById('editStatusText');
  
  if (toggle.checked) {
    statusText.textContent = "Aktif";
    statusText.classList.add('text-green-600');
    statusText.classList.remove('text-gray-700');
  } else {
    statusText.textContent = "Non-Aktif";
    statusText.classList.remove('text-green-600');
    statusText.classList.add('text-gray-700');
  }
}

// Fungsi untuk preview foto outlet di modal edit
function previewEditFoto(input) {
  const preview = document.getElementById('editCurrentFoto');
  const icon = document.getElementById('editDefaultIcon');
  const errorFoto = document.getElementById('errorEditFoto');
  
  // Reset error
  errorFoto.classList.add('hidden');
  
  if (input.files && input.files[0]) {
    // Cek ukuran file (maks 2MB)
    if (input.files[0].size > 2 * 1024 * 1024) {
      errorFoto.classList.remove('hidden');
      return;
    }
    
    const reader = new FileReader();
    reader.onload = e => {
      preview.src = e.target.result;
      preview.classList.remove('hidden');
      icon.classList.add('hidden');
    };
    reader.readAsDataURL(input.files[0]);
  }
}

// Fungsi untuk validasi form edit
function validateEditForm() {
  let isValid = true;
  
  // Validasi nama outlet
  const namaOutlet = document.getElementById('editNamaOutlet');
  const errorNama = document.getElementById('errorEditNama');
  if (!namaOutlet.value.trim()) {
    errorNama.classList.remove('hidden');
    namaOutlet.classList.add('border-red-500');
    isValid = false;
  } else {
    errorNama.classList.add('hidden');
    namaOutlet.classList.remove('border-red-500');
  }
  
  // Validasi telepon
  const teleponOutlet = document.getElementById('editNomorTelepon');
  const errorTelepon = document.getElementById('errorEditTelepon');
  if (!teleponOutlet.value.trim()) {
    errorTelepon.classList.remove('hidden');
    teleponOutlet.classList.add('border-red-500');
    isValid = false;
  } else {
    errorTelepon.classList.add('hidden');
    teleponOutlet.classList.remove('border-red-500');
  }
  
  // Validasi alamat
  const alamatOutlet = document.getElementById('editAlamatLengkap');
  const errorAlamat = document.getElementById('errorEditAlamat');
  if (!alamatOutlet.value.trim()) {
    errorAlamat.classList.remove('hidden');
    alamatOutlet.classList.add('border-red-500');
    isValid = false;
  } else {
    errorAlamat.classList.add('hidden');
    alamatOutlet.classList.remove('border-red-500');
  }
  
  // Validasi email (jika diisi)
  const emailOutlet = document.getElementById('editEmail');
  const errorEmail = document.getElementById('errorEditEmail');
  if (emailOutlet.value.trim() && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailOutlet.value)) {
    errorEmail.classList.remove('hidden');
    emailOutlet.classList.add('border-red-500');
    isValid = false;
  } else {
    errorEmail.classList.add('hidden');
    emailOutlet.classList.remove('border-red-500');
  }
  
  return isValid;
}

// Fungsi untuk submit form edit
function submitEditForm() {
  if (!validateEditForm()) {
    return;
  }
  
  // Simulasi loading
  const btnSimpan = document.getElementById('btnSimpanPerubahan');
  const originalText = btnSimpan.innerHTML;
  btnSimpan.innerHTML = `
    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
    </svg>
    Menyimpan...
  `;
  btnSimpan.disabled = true;
  
  // Simulasi AJAX request
  setTimeout(() => {
    // Ambil nilai dari form
    const formData = {
      id: document.getElementById('outletIdToEdit').value,
      nama: document.getElementById('editNamaOutlet').value,
      telepon: document.getElementById('editNomorTelepon').value,
      alamat: document.getElementById('editAlamatLengkap').value,
      email: document.getElementById('editEmail').value,
      pajak: document.getElementById('editPersentasePajak').value || 0,
      nomorTransaksi: document.getElementById('editNoTransaksi').value,
      namaBank: document.getElementById('editNamaBank').value,
      atasNama: document.getElementById('editAtasNama').value,
      status: document.getElementById('editStatusAktif').checked ? 'Aktif' : 'Tidak Aktif',
      foto: document.getElementById('editFotoOutlet').files[0]?.name || null
    };
    
    console.log('Data yang akan diupdate:', formData);
    
    // Tutup modal edit
    closeModalEdit();
    
    // Kembalikan tombol ke state semula
    btnSimpan.innerHTML = originalText;
    btnSimpan.disabled = false;
    
    // Tampilkan notifikasi
    showAlert('success', 'Perubahan outlet berhasil disimpan!');
    
    // Auto-refresh halaman setelah 1.5 detik
    setTimeout(() => {
      window.location.reload();
    }, 1500);
    
  }, 1500);
}

// Event Listeners
document.addEventListener('DOMContentLoaded', function() {
  // Toggle status
  document.getElementById('editStatusAktif').addEventListener('change', updateToggleStatus);
  
  // Tombol batal
  document.getElementById('btnBatalModalEdit').addEventListener('click', closeModalEdit);
  
  // Tombol simpan
  document.getElementById('btnSimpanPerubahan').addEventListener('click', submitEditForm);
  
  // Submit form saat tekan enter
  document.querySelectorAll('#modalEditOutlet input').forEach(input => {
    input.addEventListener('keypress', e => {
      if (e.key === 'Enter') {
        submitEditForm();
      }
    });
  });
  
  // Close modal saat klik di luar area modal
  document.getElementById('modalEditOutlet').addEventListener('click', function(e) {
    if (e.target === this) {
      closeModalEdit();
    }
  });
});

// Contoh fungsi showAlert (jika diperlukan)
function showAlert(type, message) {
  const alert = document.createElement('div');
  alert.className = `fixed top-4 right-4 px-4 py-2 rounded-md shadow-md ${
    type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
  }`;
  alert.textContent = message;
  document.body.appendChild(alert);
  
  setTimeout(() => {
    alert.remove();
  }, 3000);
}
</script>