<div id="modalEditMember" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center" onclick="closeModalEdit()">
  <div class="bg-white w-full max-w-md rounded-xl shadow-lg max-h-screen flex flex-col" onclick="event.stopPropagation()">
    
    <!-- Header -->
    <div class="p-6 border-b">
      <h2 class="text-xl font-semibold">Edit Member</h2>
      <p class="text-sm text-gray-500">Perbarui informasi member dengan detail yang sesuai.</p>
    </div>

    <!-- Scrollable Content -->
    <div class="overflow-y-auto p-6 space-y-4 flex-1">
      <form id="formEditMember">
        <input type="hidden" id="memberIdToEdit">
        
        <!-- Kode Member -->
        <div>
          <label class="block font-medium mb-1">Kode Member <span class="text-red-500">*</span></label>
          <input type="text" id="editKodeMember" class="w-full border rounded-lg px-4 py-2 text-sm" placeholder="Kode member" required>
          <p id="errorEditKode" class="text-red-500 text-xs mt-1 hidden">Kode member wajib diisi</p>
        </div>

        <!-- Nama -->
        <div>
          <label class="block font-medium mb-1">Nama <span class="text-red-500">*</span></label>
          <input type="text" id="editNamaMember" class="w-full border rounded-lg px-4 py-2 text-sm" placeholder="Nama member" required>
          <p id="errorEditNama" class="text-red-500 text-xs mt-1 hidden">Nama member wajib diisi</p>
        </div>

        <!-- Telepon -->
        <div>
          <label class="block font-medium mb-1">Telp</label>
          <input type="text" id="editTeleponMember" class="w-full border rounded-lg px-4 py-2 text-sm" placeholder="No. telp member">
        </div>

        <!-- Email -->
        {{-- <div>
          <label class="block font-medium mb-1">Email</label>
          <input type="email" id="editEmailMember" class="w-full border rounded-lg px-4 py-2 text-sm" placeholder="Email member">
          <p id="errorEditEmail" class="text-red-500 text-xs mt-1 hidden">Format email tidak valid</p>
        </div> --}}

        <!-- Alamat -->
        {{-- <div>
          <label class="block font-medium mb-1">Alamat</label>
          <textarea id="editAlamatMember" class="w-full border rounded-lg px-4 py-2 text-sm" placeholder="Alamat member"></textarea>
        </div> --}}

        <!-- Jenis Kelamin -->
        <div>
          <label class="block font-medium mb-1">Jenis Kelamin</label>
          <select id="editJenisKelamin" class="w-full border rounded-lg px-4 py-2 text-sm">
            <option value="">Pilih gender</option>
            <option value="male">Laki-laki</option>
            <option value="female">Perempuan</option>
          </select>
        </div>
      </form>
    </div>

    <!-- Footer -->
    <div class="p-6 border-t flex justify-end gap-3">
      <button id="btnBatalModalEdit" class="px-4 py-2 border rounded hover:bg-gray-100">Batal</button>
      <button id="btnEditMember" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 flex items-center gap-2">
        <i data-lucide="save" class="w-4 h-4"></i>
        <span>Simpan Perubahan</span>
      </button>
    </div>
  </div>
</div>

<script>
  // Fungsi untuk membuka modal edit dengan data member
  function openEditMemberModal(member) {
    document.getElementById('memberIdToEdit').value = member.id;
    document.getElementById('editKodeMember').value = member.member_code || '';
    document.getElementById('editNamaMember').value = member.name || '';
    document.getElementById('editTeleponMember').value = member.phone || '';
    document.getElementById('editEmailMember').value = member.email || '';
    document.getElementById('editAlamatMember').value = member.address || '';
    document.getElementById('editJenisKelamin').value = member.gender || '';

    // Buka modal
    document.getElementById('modalEditMember').classList.remove('hidden');
    document.getElementById('modalEditMember').classList.add('flex');
  }

  // Fungsi untuk validasi form edit
  function validateEditForm() {
    let isValid = true;

    // Validasi kode member
    const kodeMember = document.getElementById('editKodeMember');
    const errorKode = document.getElementById('errorEditKode');
    if (!kodeMember.value.trim()) {
      errorKode.classList.remove('hidden');
      kodeMember.classList.add('border-red-500');
      isValid = false;
    } else {
      errorKode.classList.add('hidden');
      kodeMember.classList.remove('border-red-500');
    }

    // Validasi nama member
    const namaMember = document.getElementById('editNamaMember');
    const errorNama = document.getElementById('errorEditNama');
    if (!namaMember.value.trim()) {
      errorNama.classList.remove('hidden');
      namaMember.classList.add('border-red-500');
      isValid = false;
    } else {
      errorNama.classList.add('hidden');
      namaMember.classList.remove('border-red-500');
    }

    // Validasi email (jika diisi)
    const emailMember = document.getElementById('editEmailMember');
    const errorEmail = document.getElementById('errorEditEmail');
    if (emailMember.value.trim() && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailMember.value)) {
      errorEmail.classList.remove('hidden');
      emailMember.classList.add('border-red-500');
      isValid = false;
    } else {
      errorEmail.classList.add('hidden');
      emailMember.classList.remove('border-red-500');
    }

    return isValid;
  }

  // Fungsi untuk submit form edit
  async function submitEditMember() {
    if (!validateEditForm()) {
      return;
    }

    const btnEdit = document.getElementById('btnEditMember');
    const originalText = btnEdit.innerHTML;
    
    // Tampilkan loading state
    btnEdit.innerHTML = `
      <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
      </svg>
      Menyimpan...
    `;
    btnEdit.disabled = true;

    try {
      const formData = {
        name: document.getElementById('editNamaMember').value,
        member_code: document.getElementById('editKodeMember').value,
        phone: document.getElementById('editTeleponMember').value || null,
        email: document.getElementById('editEmailMember').value || null,
        address: document.getElementById('editAlamatMember').value || null,
        gender: document.getElementById('editJenisKelamin').value || null
      };

      const memberId = document.getElementById('memberIdToEdit').value;

      // Kirim data ke API
      const token = localStorage.getItem('token');
      const response = await fetch(`/api/members/${memberId}`, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${token}`
        },
        body: JSON.stringify(formData)
      });

      const data = await response.json();

      if (!response.ok) {
        throw new Error(data.message || 'Gagal memperbarui member');
      }

      // Jika sukses
      showAlert('success', 'Member berhasil diperbarui');
      closeModalEdit();

      // Refresh data member jika diperlukan
      if (typeof loadMembers === 'function') {
        loadMembers();
      }

    } catch (error) {
      console.error('Error:', error);
      showAlert('error', error.message);
    } finally {
      // Kembalikan tombol ke state awal
      btnEdit.innerHTML = originalText;
      btnEdit.disabled = false;
    }
  }

  // Fungsi untuk menutup modal edit
  function closeModalEdit() {
    document.getElementById('modalEditMember').classList.add('hidden');
    document.getElementById('modalEditMember').classList.remove('flex');
  }

  // Event listener untuk tombol edit
  document.getElementById('btnEditMember')?.addEventListener('click', submitEditMember);

  // Event listener untuk tombol batal
  document.getElementById('btnBatalModalEdit')?.addEventListener('click', closeModalEdit);

  // Submit form saat tekan enter
  document.querySelectorAll('#modalEditMember input').forEach(input => {
    input.addEventListener('keypress', e => {
      if (e.key === 'Enter') {
        submitEditMember();
      }
    });
  });
</script>