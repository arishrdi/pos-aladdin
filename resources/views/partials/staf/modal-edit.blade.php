<!-- Modal Edit Staff -->
<div id="modalEditStaff" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center" onclick="closeModalEditStaff()">
    <div class="bg-white w-full max-w-2xl rounded-xl shadow-lg max-h-screen flex flex-col" onclick="event.stopPropagation()">
      
      <!-- Header -->
      <div class="p-6 border-b">
        <h2 class="text-xl font-semibold">Edit Staff</h2>
        <p class="text-sm text-gray-500">Edit informasi staff</p>
      </div>
  
      <!-- Scrollable Content -->
      <div class="overflow-y-auto p-6 space-y-6 flex-1">
        <div class="space-y-4">
          <!-- Nama Staff -->
          <div>
            <label class="block font-medium mb-1">Nama <span class="text-red-500">*</span></label>
            <input type="text" id="editNamaStaff" class="w-full border rounded-lg px-4 py-2 text-sm" placeholder="Nama staff" required>
            <p id="errorEditNamaStaff" class="text-red-500 text-xs mt-1 hidden">Nama staff wajib diisi</p>
          </div>
  
          <!-- Email Staff -->
          <div>
            <label class="block font-medium mb-1">Email <span class="text-red-500">*</span></label>
            <input type="email" id="editEmailStaff" class="w-full border rounded-lg px-4 py-2 text-sm" placeholder="Email staff" required>
            <p id="errorEditEmailStaff" class="text-red-500 text-xs mt-1 hidden">Email wajib diisi dan valid</p>
          </div>
  
          <!-- Password -->
          <div>
            <label class="block font-medium mb-1">Password</label>
            <input type="password" id="editPasswordStaff" class="w-full border rounded-lg px-4 py-2 text-sm" placeholder="Kosongkan jika tidak ingin mengubah">
            <p id="errorEditPasswordStaff" class="text-red-500 text-xs mt-1 hidden">Password minimal 8 karakter</p>
          </div>
  
          <!-- Peran -->
          <div>
            <label class="block font-medium mb-1">Peran <span class="text-red-500">*</span></label>
            <select id="editPeranStaff" class="w-full border rounded-lg px-4 py-2 text-sm" required>
              <option value="" disabled>Pilih peran</option>
              <option value="kasir">Kasir</option>
              <option value="supervisor">Supervisor</option>
              <option value="admin">Admin</option>
              <option value="manajer">Manajer</option>
            </select>
            <p id="errorEditPeranStaff" class="text-red-500 text-xs mt-1 hidden">Peran wajib dipilih</p>
          </div>
  
          <!-- Shift -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block font-medium mb-1">Waktu Mulai</label>
              <input type="time" id="editWaktuMulai" class="w-full border rounded-lg px-4 py-2 text-sm" placeholder="--.--">
            </div>
            <div>
              <label class="block font-medium mb-1">Waktu Selesai</label>
              <input type="time" id="editWaktuSelesai" class="w-full border rounded-lg px-4 py-2 text-sm" placeholder="--.--">
            </div>
          </div>
  
          <!-- Outlet -->
          <div>
            <label class="block font-medium mb-1">Outlet</label>
            <select id="editOutletStaff" data-url="{{ url('/api/outlets') }}" class="w-full border rounded-lg px-4 py-2 text-sm">
              <option value="" disabled selected>Memuat outlet...</option>
            </select>
            <p id="errorEditOutletStaff" class="text-red-500 text-xs mt-1 hidden">Outlet wajib dipilih</p>
          </div>
        </div>
      </div>
  
      <!-- Footer -->
      <div class="p-6 border-t flex justify-end gap-3">
        <button id="btnBatalModalEditStaff" class="px-4 py-2 border rounded hover:bg-gray-100">Batal</button>
        <button id="btnSimpanEditStaff" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 flex items-center gap-2">
          <i data-lucide="save" class="w-4 h-4"></i>
          <span>Simpan Perubahan</span>
        </button>
      </div>
    </div>
</div>