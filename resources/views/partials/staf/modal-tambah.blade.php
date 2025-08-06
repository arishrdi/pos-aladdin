<!-- Modal Tambah Staff -->
<div id="modalTambahStaff" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center" onclick="closeModalTambahStaff()">
  <div class="bg-white w-full max-w-2xl rounded-xl shadow-lg max-h-screen flex flex-col" onclick="event.stopPropagation()">
    
    <!-- Header -->
    <div class="p-6 border-b">
      <h2 class="text-xl font-semibold">Tambah Staff Baru</h2>
      <p class="text-sm text-gray-500">Tambahkan staff baru dengan mengisi detail di bawah ini.</p>
    </div>

    <!-- Scrollable Content -->
    <div class="overflow-y-auto p-6 space-y-6 flex-1">
      <div class="space-y-4">
        
        <!-- Nama Staff -->
        <div>
          <label class="block font-medium mb-1">Nama <span class="text-red-500">*</span></label>
          <input type="text" id="namaStaff" class="w-full border rounded-lg px-4 py-2 text-sm" placeholder="Nama staff" required>
          <p id="errorNamaStaff" class="text-red-500 text-xs mt-1 hidden">Nama staff wajib diisi</p>
        </div>

        <!-- Email Staff -->
        <div>
          <label class="block font-medium mb-1">Email <span class="text-red-500">*</span></label>
          <input type="email" id="emailStaff" class="w-full border rounded-lg px-4 py-2 text-sm" placeholder="Email staff" required>
          <p id="errorEmailStaff" class="text-red-500 text-xs mt-1 hidden">Email wajib diisi dan valid</p>
        </div>

        <!-- Password -->
        <div>
          <label class="block font-medium mb-1">Password <span class="text-red-500">*</span></label>
          <input type="password" id="passwordStaff" class="w-full border rounded-lg px-4 py-2 text-sm" placeholder="Password" required>
          <p id="errorPasswordStaff" class="text-red-500 text-xs mt-1 hidden">Password wajib diisi (min. 8 karakter)</p>
        </div>

        <!-- Peran -->
        <div>
          <label class="block font-medium mb-1">Peran <span class="text-red-500">*</span></label>
          <select id="peranStaff" class="w-full border rounded-lg px-4 py-2 text-sm" required>
            <option value="" disabled selected>Pilih peran</option>
            <option value="kasir">Kasir</option>
            <option value="supervisor">Supervisor</option>
            <option value="admin">Admin</option>
            <option value="manajer">Manajer</option>
          </select>
          <p id="errorPeranStaff" class="text-red-500 text-xs mt-1 hidden">Peran wajib dipilih</p>
        </div>

        <!-- Shift -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block font-medium mb-1">Waktu Mulai <span class="text-red-500">*</span></label>
            <input type="time" id="waktuMulai" class="w-full border rounded-lg px-4 py-2 text-sm" required>
            <p id="errorWaktuMulai" class="text-red-500 text-xs mt-1 hidden">Waktu mulai wajib diisi</p>
          </div>
          <div>
            <label class="block font-medium mb-1">Waktu Selesai <span class="text-red-500">*</span></label>
            <input type="time" id="waktuSelesai" class="w-full border rounded-lg px-4 py-2 text-sm" required>
            <p id="errorWaktuSelesai" class="text-red-500 text-xs mt-1 hidden">Waktu selesai wajib diisi</p>
          </div>
        </div>

        <!-- Outlet -->
        <div>
          <label class="block font-medium mb-1">Outlet <span class="text-red-500">*</span></label>
          <select id="outletStaff" data-url="{{ url('/api/outlets') }}" class="w-full border rounded-lg px-4 py-2 text-sm" required>
            <option value="" disabled selected>Memuat outlet...</option>
          </select>
          <p id="errorOutletStaff" class="text-red-500 text-xs mt-1 hidden">Outlet wajib dipilih</p>
        </div>

      </div>
    </div>

    <!-- Footer -->
    <div class="p-6 border-t flex justify-end gap-3">
      <button id="btnBatalModalTambahStaff" class="px-4 py-2 border rounded hover:bg-gray-100">Batal</button>
      <button id="btnTambahStaff" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 flex items-center gap-2">
        <i data-lucide="user-plus" class="w-4 h-4"></i>
        <span>Simpan</span>
      </button>
    </div>

  </div>
</div>
