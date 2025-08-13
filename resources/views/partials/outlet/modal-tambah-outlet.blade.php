<div id="modalTambahOutlet" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center" onclick="closeModalTambah()">
  <div class="bg-white w-full max-w-4xl rounded-xl shadow-lg max-h-screen flex flex-col" onclick="event.stopPropagation()">
    
    <!-- Header -->
    <div class="p-6 border-b">
      <h2 class="text-xl font-semibold">Tambah Outlet Baru</h2>
      <p class="text-sm text-gray-500">Lengkapi informasi outlet baru dengan detail yang sesuai.</p>
    </div>

    <!-- Scrollable Content -->
    <div class="overflow-y-auto p-6 space-y-6 flex-1">

      <!-- Informasi Dasar -->
      <div class="p-5 bg-gray-100 border rounded-lg shadow-sm">
        <h3 class="font-semibold mb-4 text-gray-700">Informasi Dasar</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block font-medium mb-1">Nama Outlet <span class="text-red-500">*</span></label>
            <input type="text" id="namaOutlet" class="w-full border rounded-lg px-4 py-2 text-sm" placeholder="Masukkan nama outlet" required>
            <p id="errorNama" class="text-red-500 text-xs mt-1 hidden">Nama outlet wajib diisi</p>
          </div>
          <div>
            <label class="block font-medium mb-1">Nomor Telepon <span class="text-red-500">*</span></label>
            <input type="text" id="teleponOutlet" class="w-full border rounded-lg px-4 py-2 text-sm" placeholder="Masukkan nomor telepon" required>
            <p id="errorTelepon" class="text-red-500 text-xs mt-1 hidden">Nomor telepon wajib diisi</p>
          </div>
          <div class="md:col-span-2">
            <label class="block font-medium mb-1">Alamat Lengkap <span class="text-red-500">*</span></label>
            <textarea id="alamatOutlet" class="w-full border rounded-lg px-4 py-2 text-sm" placeholder="Masukkan alamat lengkap" required></textarea>
            <p id="errorAlamat" class="text-red-500 text-xs mt-1 hidden">Alamat wajib diisi</p>
          </div>
        </div>
      </div>

      <!-- Informasi Tambahan -->
      <div class="p-5 bg-gray-100 border rounded-lg shadow-sm">
        <h3 class="font-semibold mb-4 text-gray-700">Informasi Tambahan</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block font-medium mb-1">Email <span class="text-red-500">*</span> </label>
            <input type="email" id="emailOutlet" class="w-full border rounded-lg px-4 py-2 text-sm" placeholder="Masukkan email (wajib diisi)">
            <p id="errorEmail" class="text-red-500 text-xs mt-1 hidden">Format email tidak valid</p>
          </div>
          <div>
            <label class="block font-medium mb-1">Persentase Pajak (%)</label>
            <input type="number" id="pajakOutlet" class="w-full border rounded-lg px-4 py-2 text-sm" placeholder="0%">
          </div>
          <div>
            <label class="block font-medium mb-1">Jenis Pajak Default <span class="text-red-500">*</span></label>
            <select id="taxType" class="w-full border rounded-lg px-4 py-2 text-sm" onchange="updateDefaultTaxPercentage()" required>
              <option value="">Pilih jenis pajak default</option>
              <option value="pkp">PKP (11%)</option>
              <option value="non_pkp">Non-PKP (0%)</option>
            </select>
            <p id="errorTaxType" class="text-red-500 text-xs mt-1 hidden">Jenis pajak default wajib dipilih</p>
            <p class="text-xs text-gray-500 mt-1">Kasir tetap bisa memilih PKP atau Non-PKP per transaksi</p>
          </div>
        </div>
      </div>

      <!-- Nomor Transaksi -->
      {{-- <div class="p-5 bg-gray-100 border rounded-lg shadow-sm">
        <h3 class="font-semibold mb-4 text-gray-700">Nomor Transaksi</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block font-medium mb-1">Nomor Transaksi Default</label>
            <input type="text" id="nomorTransaksi" class="w-full border rounded-lg px-4 py-2 text-sm" placeholder="Contoh: 001">
          </div>
          <div>
            <label class="block font-medium mb-1">Nama Bank</label>
            <input type="text" id="namaBank" class="w-full border rounded-lg px-4 py-2 text-sm" placeholder="Contoh: BCA">
          </div>
          <div class="md:col-span-2">
            <label class="block font-medium mb-1">Atas Nama</label>
            <input type="text" id="atasNama" class="w-full border rounded-lg px-4 py-2 text-sm" placeholder="Nama pemilik rekening">
          </div>
        </div>
      </div> --}}

      <!-- PKP Banking Info -->
      <div id="pkpBankingSection" class="p-5 bg-blue-50 border border-blue-200 rounded-lg shadow-sm">
        <h3 class="font-semibold mb-4 text-blue-700">Informasi Bank PKP (11%)</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block font-medium mb-1">Nomor Rekening PKP <span class="text-red-500">*</span></label>
            <input type="text" id="pkpNomorTransaksi" class="w-full border rounded-lg px-4 py-2 text-sm" placeholder="Nomor rekening PKP">
            <p id="errorPkpNomor" class="text-red-500 text-xs mt-1 hidden">Nomor rekening PKP wajib diisi</p>
          </div>
          <div>
            <label class="block font-medium mb-1">Nama Bank PKP <span class="text-red-500">*</span></label>
            <input type="text" id="pkpNamaBank" class="w-full border rounded-lg px-4 py-2 text-sm" placeholder="Contoh: BCA">
            <p id="errorPkpBank" class="text-red-500 text-xs mt-1 hidden">Nama bank PKP wajib diisi</p>
          </div>
          <div class="md:col-span-2">
            <label class="block font-medium mb-1">Atas Nama PKP <span class="text-red-500">*</span></label>
            <input type="text" id="pkpAtasNama" class="w-full border rounded-lg px-4 py-2 text-sm" placeholder="Nama pemilik rekening PKP">
            <p id="errorPkpAtasNama" class="text-red-500 text-xs mt-1 hidden">Atas nama PKP wajib diisi</p>
          </div>
        </div>
      </div>

      <!-- Non-PKP Banking Info -->
      <div id="nonPkpBankingSection" class="p-5 bg-green-50 border border-green-200 rounded-lg shadow-sm">
        <h3 class="font-semibold mb-4 text-green-700">Informasi Bank Non-PKP (0%)</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block font-medium mb-1">Nomor Rekening Non-PKP <span class="text-red-500">*</span></label>
            <input type="text" id="nonPkpNomorTransaksi" class="w-full border rounded-lg px-4 py-2 text-sm" placeholder="Nomor rekening Non-PKP">
            <p id="errorNonPkpNomor" class="text-red-500 text-xs mt-1 hidden">Nomor rekening Non-PKP wajib diisi</p>
          </div>
          <div>
            <label class="block font-medium mb-1">Nama Bank Non-PKP <span class="text-red-500">*</span></label>
            <input type="text" id="nonPkpNamaBank" class="w-full border rounded-lg px-4 py-2 text-sm" placeholder="Contoh: BCA">
            <p id="errorNonPkpBank" class="text-red-500 text-xs mt-1 hidden">Nama bank Non-PKP wajib diisi</p>
          </div>
          <div class="md:col-span-2">
            <label class="block font-medium mb-1">Atas Nama Non-PKP <span class="text-red-500">*</span></label>
            <input type="text" id="nonPkpAtasNama" class="w-full border rounded-lg px-4 py-2 text-sm" placeholder="Nama pemilik rekening Non-PKP">
            <p id="errorNonPkpAtasNama" class="text-red-500 text-xs mt-1 hidden">Atas nama Non-PKP wajib diisi</p>
          </div>
        </div>
      </div>

      <!-- Foto Outlet -->
      <div class="p-5 bg-gray-100 border rounded-lg shadow-sm">
        <h3 class="font-semibold mb-4 text-gray-700">Foto Qris <span class="text-red-500">*</span></h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-start" required>
          <!-- Preview Foto -->
          <div>
            <p class="text-sm text-gray-600 mb-1">Foto Qris:</p>
            <div class="h-24 w-24 bg-gray-200 rounded flex items-center justify-center overflow-hidden">
              <img id="currentFotoOutlet" src="#" alt="Foto Outlet" class="object-cover w-full h-full hidden">
              <i data-lucide="image" class="w-8 h-8 text-gray-400" id="defaultIcon"></i>
            </div>
          </div>

          <!-- Upload Foto Baru -->
          <div>
            <label class="block font-medium mb-1">Ganti Foto</label>
            <input type="file" id="fotoOutlet" class="w-full text-sm" accept=".jpg,.jpeg,.png" onchange="previewFotoOutlet(this)">
            <p class="text-gray-500 text-xs mt-1">Format: JPG, PNG. Ukuran maksimal: 2MB</p>
            <p id="errorFoto" class="text-red-500 text-xs mt-1 hidden">Ukuran file terlalu besar (maks 2MB)</p>
          </div>
        </div>
      </div>

      <!-- Status Aktif -->
      <div class="p-5 bg-gray-100 border rounded-lg shadow-sm">
        <h3 class="font-semibold mb-4 text-gray-700">Status Outlet</h3>
        <div class="flex items-center space-x-4">
          <label class="flex items-center cursor-pointer">
            <input type="checkbox" class="sr-only peer" id="statusAktif" checked>
            <div class="w-11 h-6 bg-gray-300 rounded-full peer peer-checked:bg-green-500 relative transition-all duration-300">
              <div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full shadow-md transform transition-all duration-300 peer-checked:translate-x-5"></div>
            </div>
            <span class="ml-3 text-sm font-medium text-gray-700 peer-checked:text-green-600">Aktif</span>
          </label>
          <span class="text-sm text-gray-500">Outlet hanya muncul jika status aktif.</span>
        </div>
      </div>

    </div>

    <!-- Footer -->
    <div class="p-6 border-t flex justify-end gap-3">
      <button id="btnBatalModalTambah" class="px-4 py-2 border rounded hover:bg-gray-100">Batal</button>
      <button id="btnTambahOutlet" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 flex items-center gap-2">
        <i data-lucide="plus" class="w-4 h-4"></i>
        <span>Tambah Outlet</span>
      </button>
    </div>
  </div>
</div>

<script>
// Fungsi untuk preview foto outlet
function previewFotoOutlet(input) {
  const preview = document.getElementById('currentFotoOutlet');
  const icon = document.getElementById('defaultIcon');
  const errorFoto = document.getElementById('errorFoto');
  
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

// Fungsi untuk update tax percentage berdasarkan default selection
function updateDefaultTaxPercentage() {
  const taxType = document.getElementById('taxType').value;
  const pajakOutlet = document.getElementById('pajakOutlet');
  
  if (taxType === 'pkp') {
    pajakOutlet.value = 11;
  } else if (taxType === 'non_pkp') {
    pajakOutlet.value = 0;
  } else {
    pajakOutlet.value = '';
  }
}

// Fungsi untuk validasi form
function validateForm() {
  let isValid = true;
  
  // Validasi nama outlet
  const namaOutlet = document.getElementById('namaOutlet');
  const errorNama = document.getElementById('errorNama');
  if (!namaOutlet.value.trim()) {
    errorNama.classList.remove('hidden');
    namaOutlet.classList.add('border-red-500');
    isValid = false;
  } else {
    errorNama.classList.add('hidden');
    namaOutlet.classList.remove('border-red-500');
  }
  
  // Validasi telepon
  const teleponOutlet = document.getElementById('teleponOutlet');
  const errorTelepon = document.getElementById('errorTelepon');
  if (!teleponOutlet.value.trim()) {
    errorTelepon.classList.remove('hidden');
    teleponOutlet.classList.add('border-red-500');
    isValid = false;
  } else {
    errorTelepon.classList.add('hidden');
    teleponOutlet.classList.remove('border-red-500');
  }
  
  // Validasi alamat
  const alamatOutlet = document.getElementById('alamatOutlet');
  const errorAlamat = document.getElementById('errorAlamat');
  if (!alamatOutlet.value.trim()) {
    errorAlamat.classList.remove('hidden');
    alamatOutlet.classList.add('border-red-500');
    isValid = false;
  } else {
    errorAlamat.classList.add('hidden');
    alamatOutlet.classList.remove('border-red-500');
  }
  
  // Validasi email (jika diisi)
  const emailOutlet = document.getElementById('emailOutlet');
  const errorEmail = document.getElementById('errorEmail');
  if (emailOutlet.value.trim() && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailOutlet.value)) {
    errorEmail.classList.remove('hidden');
    emailOutlet.classList.add('border-red-500');
    isValid = false;
  } else {
    errorEmail.classList.add('hidden');
    emailOutlet.classList.remove('border-red-500');
  }
  
  // Validasi tax type
  const taxType = document.getElementById('taxType');
  const errorTaxType = document.getElementById('errorTaxType');
  if (!taxType.value.trim()) {
    errorTaxType.classList.remove('hidden');
    taxType.classList.add('border-red-500');
    isValid = false;
  } else {
    errorTaxType.classList.add('hidden');
    taxType.classList.remove('border-red-500');
  }
  
  // Validasi PKP banking fields (always validate)
  const pkpFields = [
    { id: 'pkpNomorTransaksi', error: 'errorPkpNomor' },
    { id: 'pkpNamaBank', error: 'errorPkpBank' },
    { id: 'pkpAtasNama', error: 'errorPkpAtasNama' }
  ];
  
  pkpFields.forEach(field => {
    const input = document.getElementById(field.id);
    const error = document.getElementById(field.error);
    if (!input.value.trim()) {
      error.classList.remove('hidden');
      input.classList.add('border-red-500');
      isValid = false;
    } else {
      error.classList.add('hidden');
      input.classList.remove('border-red-500');
    }
  });
  
  // Validasi Non-PKP banking fields (always validate)
  const nonPkpFields = [
    { id: 'nonPkpNomorTransaksi', error: 'errorNonPkpNomor' },
    { id: 'nonPkpNamaBank', error: 'errorNonPkpBank' },
    { id: 'nonPkpAtasNama', error: 'errorNonPkpAtasNama' }
  ];
  
  nonPkpFields.forEach(field => {
    const input = document.getElementById(field.id);
    const error = document.getElementById(field.error);
    if (!input.value.trim()) {
      error.classList.remove('hidden');
      input.classList.add('border-red-500');
      isValid = false;
    } else {
      error.classList.add('hidden');
      input.classList.remove('border-red-500');
    }
  });
  
  return isValid;
}



// Fungsi untuk reset form
function resetForm() {
  document.getElementById('namaOutlet').value = '';
  document.getElementById('teleponOutlet').value = '';
  document.getElementById('alamatOutlet').value = '';
  document.getElementById('emailOutlet').value = '';
  document.getElementById('pajakOutlet').value = '';
  document.getElementById('taxType').value = '';
  document.getElementById('nomorTransaksi').value = '';
  document.getElementById('namaBank').value = '';
  document.getElementById('atasNama').value = '';
  document.getElementById('pkpNomorTransaksi').value = '';
  document.getElementById('pkpNamaBank').value = '';
  document.getElementById('pkpAtasNama').value = '';
  document.getElementById('nonPkpNomorTransaksi').value = '';
  document.getElementById('nonPkpNamaBank').value = '';
  document.getElementById('nonPkpAtasNama').value = '';
  document.getElementById('fotoOutlet').value = '';
  document.getElementById('statusAktif').checked = true;
  
  // Banking sections remain visible (no hiding needed)
  
  // Reset pajak field to editable
  const pajakOutlet = document.getElementById('pajakOutlet');
  pajakOutlet.readOnly = false;
  
  // Reset preview foto
  document.getElementById('currentFotoOutlet').src = '#';
  document.getElementById('currentFotoOutlet').classList.add('hidden');
  document.getElementById('defaultIcon').classList.remove('hidden');
  
  // Reset error messages
  document.querySelectorAll('[id^="error"]').forEach(el => el.classList.add('hidden'));
  document.querySelectorAll('.border-red-500').forEach(el => el.classList.remove('border-red-500'));
}

// Fungsi untuk submit form
function submitForm() {
  if (!validateForm()) {
    return;
  }
  
  // Simulasi loading
  const btnTambah = document.getElementById('btnTambahOutlet');
  const originalText = btnTambah.innerHTML;
  btnTambah.innerHTML = `
    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
    </svg>
    Menyimpan...
  `;
  btnTambah.disabled = true;
  
  // Simulasi AJAX request (di production, ganti dengan fetch/axios)
  setTimeout(() => {
    // Ambil nilai dari form
    const taxType = document.getElementById('taxType').value;
    const formData = {
      nama: document.getElementById('namaOutlet').value,
      telepon: document.getElementById('teleponOutlet').value,
      alamat: document.getElementById('alamatOutlet').value,
      email: document.getElementById('emailOutlet').value,
      pajak: document.getElementById('pajakOutlet').value || 0,
      tax_type: taxType,
      nomorTransaksi: document.getElementById('nomorTransaksi').value,
      namaBank: document.getElementById('namaBank').value,
      atasNama: document.getElementById('atasNama').value,
      status: document.getElementById('statusAktif').checked ? 'Aktif' : 'Tidak Aktif',
      foto: document.getElementById('fotoOutlet').files[0]?.name || null
    };
    
    // Add both PKP and NonPKP banking fields
    formData.pkp_atas_nama_bank = document.getElementById('pkpAtasNama').value;
    formData.pkp_nama_bank = document.getElementById('pkpNamaBank').value;
    formData.pkp_nomor_transaksi_bank = document.getElementById('pkpNomorTransaksi').value;
    formData.non_pkp_atas_nama_bank = document.getElementById('nonPkpAtasNama').value;
    formData.non_pkp_nama_bank = document.getElementById('nonPkpNamaBank').value;
    formData.non_pkp_nomor_transaksi_bank = document.getElementById('nonPkpNomorTransaksi').value;
    
    console.log('Data yang akan dikirim:', formData);
    
    // Reset form dan tutup modal
    resetForm();
    closeModalTambah();
    
    // Tampilkan alert sukses
    showAlert('success', 'Outlet baru berhasil ditambahkan!');
    
    // Kembalikan tombol ke state semula
    btnTambah.innerHTML = originalText;
    btnTambah.disabled = false;
  }, 1500);
}

// Event listener untuk tombol tambah
document.getElementById('btnTambahOutlet').addEventListener('click', submitForm);

// Event listener untuk form (submit saat tekan enter)
document.querySelectorAll('#modalTambahOutlet input').forEach(input => {
  input.addEventListener('keypress', e => {
    if (e.key === 'Enter') {
      submitForm();
    }
  });
});
</script>