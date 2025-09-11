<!-- Modal Tambah Member -->
<div id="addMemberModal" class="modal hidden fixed inset-0 z-50 flex items-center justify-center overflow-y-auto">
    <div class="modal-overlay absolute w-full h-full bg-gray-900 opacity-50"></div>
    <div class="modal-container bg-white w-full max-w-md mx-auto rounded shadow-lg z-50 overflow-y-auto relative my-16">
        <div class="modal-content py-4 text-left px-6">
            <div class="flex justify-between items-center pb-3">
                <h3 class="text-xl font-bold text-gray-800">Tambah Member Baru</h3>
                <button class="modal-close cursor-pointer z-50" onclick="closeAddMemberModal()">
                    <i class="fas fa-times text-gray-500 hover:text-gray-700"></i>
                </button>
            </div>
            
            <form id="addMemberForm" class="space-y-4">
                <!-- Nama Member -->
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="newMemberName">
                        Nama Member <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="newMemberName" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-green-500" 
                        placeholder="Masukkan nama member" required>
                    <p id="errorNewMemberName" class="text-red-500 text-xs mt-1 hidden">Nama member wajib diisi</p>
                </div>
                
                <!-- Nomor Telepon -->
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="newMemberPhone">
                        Nomor Telepon <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="newMemberPhone" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-green-500" 
                        placeholder="Masukkan nomor telepon" required>
                    <p id="errorNewMemberPhone" class="text-red-500 text-xs mt-1 hidden">Nomor telepon wajib diisi</p>
                </div>
                
                <!-- Email -->
                {{-- <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="newMemberEmail">
                        Email
                    </label>
                    <input type="email" id="newMemberEmail" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-green-500" 
                        placeholder="Masukkan email (opsional)">
                    <p id="errorNewMemberEmail" class="text-red-500 text-xs mt-1 hidden">Format email tidak valid</p>
                </div> --}}
                
                <!-- Alamat -->
                {{-- <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="newMemberAddress">
                        Alamat
                    </label>
                    <textarea id="newMemberAddress" rows="3" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-green-500" 
                        placeholder="Masukkan alamat (opsional)"></textarea>
                </div> --}}
                
                <!-- Gender -->
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="newMemberGender">
                        Jenis Kelamin  <span class="text-red-500">*</span>
                    </label>
                    <select id="newMemberGender" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-green-500">
                        <option value="">Pilih jenis kelamin</option>
                        <option value="male">Laki-laki</option>
                        <option value="female">Perempuan</option>
                    </select>
                </div>
                
                
            </form>
            
            <div class="flex justify-end pt-4 gap-3">
                <button type="button" onclick="closeAddMemberModal()" 
                    class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">
                    Batal
                </button>
                <button type="button" id="btnSaveMember" onclick="saveMember()" 
                    class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600 transition-colors">
                    <i class="fas fa-save mr-2"></i>
                    Simpan Member
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Fungsi untuk membuka modal tambah member
window.openAddMemberModal = function() {
    const modal = document.getElementById('addMemberModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.style.overflow = 'hidden';
    
    // Focus ke nama member
    const memberNameInput = document.getElementById('newMemberName');
    if (memberNameInput) {
        memberNameInput.focus();
    }
}

// Fungsi untuk menutup modal tambah member
window.closeAddMemberModal = function() {
    const modal = document.getElementById('addMemberModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.style.overflow = '';
    
    // Reset form
    resetAddMemberForm();
}

// Fungsi untuk reset form tambah member
function resetAddMemberForm() {
    const form = document.getElementById('addMemberForm');
    if (form) {
        form.reset();
    }
    
    // Reset error messages
    document.querySelectorAll('#addMemberModal [id^="error"]').forEach(el => {
        if (el) el.classList.add('hidden');
    });
    
    // Reset border colors
    document.querySelectorAll('#addMemberModal .border-red-500').forEach(el => {
        if (el) {
            el.classList.remove('border-red-500');
            el.classList.add('border-gray-300');
        }
    });
}

// Fungsi untuk validasi form tambah member
window.validateAddMemberForm = function() {
    let isValid = true;
    
    // Validasi nama
    const memberName = document.getElementById('newMemberName');
    const errorMemberName = document.getElementById('errorNewMemberName');
    if (!memberName || !memberName.value.trim()) {
        if (errorMemberName) errorMemberName.classList.remove('hidden');
        if (memberName) {
            memberName.classList.add('border-red-500');
            memberName.classList.remove('border-gray-300');
        }
        isValid = false;
    } else {
        if (errorMemberName) errorMemberName.classList.add('hidden');
        memberName.classList.remove('border-red-500');
        memberName.classList.add('border-gray-300');
    }
    
    // Validasi nomor telepon
    const memberPhone = document.getElementById('newMemberPhone');
    const errorMemberPhone = document.getElementById('errorNewMemberPhone');
    if (!memberPhone || !memberPhone.value.trim()) {
        if (errorMemberPhone) errorMemberPhone.classList.remove('hidden');
        if (memberPhone) {
            memberPhone.classList.add('border-red-500');
            memberPhone.classList.remove('border-gray-300');
        }
        isValid = false;
    } else {
        if (errorMemberPhone) errorMemberPhone.classList.add('hidden');
        memberPhone.classList.remove('border-red-500');
        memberPhone.classList.add('border-gray-300');
    }
    
    // Validasi email (jika diisi)
    const memberEmail = document.getElementById('newMemberEmail');
    const errorMemberEmail = document.getElementById('errorNewMemberEmail');
    if (memberEmail && memberEmail.value.trim() && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(memberEmail.value)) {
        if (errorMemberEmail) errorMemberEmail.classList.remove('hidden');
        memberEmail.classList.add('border-red-500');
        memberEmail.classList.remove('border-gray-300');
        isValid = false;
    } else {
        if (errorMemberEmail) errorMemberEmail.classList.add('hidden');
        if (memberEmail) {
            memberEmail.classList.remove('border-red-500');
            memberEmail.classList.add('border-gray-300');
        }
    }
    
    return isValid;
};

// Fungsi untuk menyimpan member baru
window.saveMember = async function() {
    if (!validateAddMemberForm()) {
        return;
    }
    
    const btnSave = document.getElementById('btnSaveMember');
    const originalText = btnSave.innerHTML;
    
    // Show loading state
    btnSave.innerHTML = `
        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        Menyimpan...
    `;
    btnSave.disabled = true;
    
    try {
        // Get elements with null checks
        const memberName = document.getElementById('newMemberName');
        const memberPhone = document.getElementById('newMemberPhone');
        const memberEmail = document.getElementById('newMemberEmail');
        const memberAddress = document.getElementById('newMemberAddress');
        const memberGender = document.getElementById('newMemberGender');
        
        const memberData = {
            name: memberName ? memberName.value.trim() : '',
            phone: memberPhone ? memberPhone.value.trim() : '',
            email: memberEmail ? memberEmail.value.trim() : '',
            address: memberAddress ? memberAddress.value.trim() : '',
            gender: memberGender ? memberGender.value : ''
        };
        
        const response = await fetch('/api/members', {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${POS_CONFIG.API_TOKEN}`,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(memberData)
        });
        
        const data = await response.json();
        
        if (!response.ok) {
            throw new Error(data.message || 'Gagal menambahkan member');
        }
        
        if (data.success) {
            showNotification('Member baru berhasil ditambahkan!', 'success');
            
            // Otomatis pilih member yang baru dibuat
            if (window.simplePaymentManager) {
                window.simplePaymentManager.selectMember(data.data);
            }
            
            closeAddMemberModal();
        } else {
            throw new Error(data.message || 'Gagal menambahkan member');
        }
        
    } catch (error) {
        console.error('Error saving member:', error);
        showNotification(error.message || 'Terjadi kesalahan saat menyimpan member', 'error');
    } finally {
        // Restore button
        btnSave.innerHTML = originalText;
        btnSave.disabled = false;
    }
};

// Event listener untuk form submission dengan Enter
document.addEventListener('DOMContentLoaded', function() {
    const addMemberForm = document.getElementById('addMemberForm');
    if (addMemberForm) {
        addMemberForm.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                saveMember();
            }
        });
    }
});
</script>