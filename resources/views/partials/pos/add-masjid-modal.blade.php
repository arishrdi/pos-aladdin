<!-- Modal Tambah Masjid -->
<div id="addMasjidModal" class="modal hidden fixed inset-0 z-50 flex items-center justify-center overflow-y-auto">
    <div class="modal-overlay absolute w-full h-full bg-gray-900 opacity-50"></div>
    <div class="modal-container bg-white w-full max-w-md mx-auto rounded shadow-lg z-50 overflow-y-auto relative my-16">
        <div class="modal-content py-4 text-left px-6">
            <div class="flex justify-between items-center pb-3">
                <h3 class="text-xl font-bold text-gray-800">Tambah Masjid Baru</h3>
                <button class="modal-close cursor-pointer z-50" onclick="closeAddMasjidModal()">
                    <i class="fas fa-times text-gray-500 hover:text-gray-700"></i>
                </button>
            </div>
            
            <form id="addMasjidForm" class="space-y-4">
                <!-- Nama Masjid -->
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="newMasjidName">
                        Nama Masjid <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="newMasjidName" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-green-500" 
                        placeholder="Masukkan nama masjid" required>
                    <p id="errorNewMasjidName" class="text-red-500 text-xs mt-1 hidden">Nama masjid wajib diisi</p>
                </div>
                
                <!-- Alamat Masjid -->
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="newMasjidAddress">
                        Alamat Masjid <span class="text-red-500">*</span>
                    </label>
                    <textarea id="newMasjidAddress" rows="3" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-green-500" 
                        placeholder="Masukkan alamat masjid" required></textarea>
                    <p id="errorNewMasjidAddress" class="text-red-500 text-xs mt-1 hidden">Alamat masjid wajib diisi</p>
                </div>
                
               
            </form>
            
            <div class="flex justify-end pt-4 gap-3">
                <button type="button" onclick="closeAddMasjidModal()" 
                    class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">
                    Batal
                </button>
                <button type="button" id="btnSaveMasjid" onclick="saveMasjid()" 
                    class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600 transition-colors">
                    <i class="fas fa-save mr-2"></i>
                    Simpan Masjid
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Fungsi untuk membuka modal tambah masjid
window.openAddMasjidModal = function() {
    const modal = document.getElementById('addMasjidModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.style.overflow = 'hidden';
    
    // Focus ke nama masjid
    const masjidNameInput = document.getElementById('newMasjidName');
    if (masjidNameInput) {
        masjidNameInput.focus();
    }
}

// Fungsi untuk menutup modal tambah masjid
window.closeAddMasjidModal = function() {
    const modal = document.getElementById('addMasjidModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.style.overflow = '';
    
    // Reset form
    resetAddMasjidForm();
}

// Fungsi untuk reset form tambah masjid
function resetAddMasjidForm() {
    const form = document.getElementById('addMasjidForm');
    if (form) {
        form.reset();
    }
    
    // Reset error messages
    document.querySelectorAll('#addMasjidModal [id^="error"]').forEach(el => {
        if (el) el.classList.add('hidden');
    });
    
    // Reset border colors
    document.querySelectorAll('#addMasjidModal .border-red-500').forEach(el => {
        if (el) {
            el.classList.remove('border-red-500');
            el.classList.add('border-gray-300');
        }
    });
}

// Fungsi untuk validasi form tambah masjid
window.validateAddMasjidForm = function() {
    let isValid = true;
    
    // Validasi nama
    const masjidName = document.getElementById('newMasjidName');
    const errorMasjidName = document.getElementById('errorNewMasjidName');
    if (!masjidName || !masjidName.value.trim()) {
        if (errorMasjidName) errorMasjidName.classList.remove('hidden');
        if (masjidName) {
            masjidName.classList.add('border-red-500');
            masjidName.classList.remove('border-gray-300');
        }
        isValid = false;
    } else {
        if (errorMasjidName) errorMasjidName.classList.add('hidden');
        masjidName.classList.remove('border-red-500');
        masjidName.classList.add('border-gray-300');
    }
    
    // Validasi alamat
    const masjidAddress = document.getElementById('newMasjidAddress');
    const errorMasjidAddress = document.getElementById('errorNewMasjidAddress');
    if (!masjidAddress || !masjidAddress.value.trim()) {
        if (errorMasjidAddress) errorMasjidAddress.classList.remove('hidden');
        if (masjidAddress) {
            masjidAddress.classList.add('border-red-500');
            masjidAddress.classList.remove('border-gray-300');
        }
        isValid = false;
    } else {
        if (errorMasjidAddress) errorMasjidAddress.classList.add('hidden');
        masjidAddress.classList.remove('border-red-500');
        masjidAddress.classList.add('border-gray-300');
    }
    
    return isValid;
};

// Fungsi untuk menyimpan masjid baru
window.saveMasjid = async function() {
    if (!validateAddMasjidForm()) {
        return;
    }
    
    const btnSave = document.getElementById('btnSaveMasjid');
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
        const masjidName = document.getElementById('newMasjidName');
        const masjidAddress = document.getElementById('newMasjidAddress');
        
        const masjidData = {
            name: masjidName ? masjidName.value.trim() : '',
            address: masjidAddress ? masjidAddress.value.trim() : ''
        };
        
        const response = await fetch('/api/mosques', {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${POS_CONFIG.API_TOKEN}`,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(masjidData)
        });
        
        const data = await response.json();
        
        if (!response.ok) {
            throw new Error(data.message || 'Gagal menambahkan masjid');
        }
        
        if (data.success) {
            showNotification('Masjid baru berhasil ditambahkan!', 'success');
            
            // Otomatis pilih masjid yang baru dibuat seperti pada member
            if (window.simplePaymentManager && window.simplePaymentManager.selectMasjid) {
                window.simplePaymentManager.selectMasjid(data.data);
            } else if (window.onMasjidAdded && typeof window.onMasjidAdded === 'function') {
                window.onMasjidAdded(data.data);
            }
            
            closeAddMasjidModal();
        } else {
            throw new Error(data.message || 'Gagal menambahkan masjid');
        }
        
    } catch (error) {
        console.error('Error saving masjid:', error);
        showNotification(error.message || 'Terjadi kesalahan saat menyimpan masjid', 'error');
    } finally {
        // Restore button
        btnSave.innerHTML = originalText;
        btnSave.disabled = false;
    }
};

// Event listener untuk form submission dengan Enter
document.addEventListener('DOMContentLoaded', function() {
    const addMasjidForm = document.getElementById('addMasjidForm');
    if (addMasjidForm) {
        addMasjidForm.addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA') {
                e.preventDefault();
                saveMasjid();
            }
        });
    }
});
</script>