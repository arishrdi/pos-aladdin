    <style>
    
.modal-container {
    font-size: 0.875rem; /* text-sm */
    padding: 1rem;
    max-height: calc(100vh - 4rem);
    overflow-y: auto;
}

.modal-content {
    padding: 1rem;
}

    .member-dropdown-container {
        position: relative;
    }

    .dropdown-list {
        z-index: 50;
    }

    .member-item {
        padding: 8px 12px;
        cursor: pointer;
        border-bottom: 1px solid #f3f4f6;
    }

    .member-item:last-child {
        border-bottom: none;
    }

    .member-item:hover {
        background-color: #f9fafb;
    }

    .member-item.active {
        background-color: #f3f4f6;
    }
</style>

    <!-- Payment Modal -->
<div class="modal-container bg-white w-full max-w-sm mx-auto rounded shadow-lg z-50 relative text-sm p-4">
    <div id="paymentModal" class="modal hidden fixed inset-0 z-50 flex items-center justify-center overflow-y-auto">
        <div class="modal-overlay absolute w-full h-full bg-gray-900 opacity-50"></div>
        <div class="modal-container bg-white w-full max-w-md mx-auto rounded shadow-lg z-50 overflow-y-auto relative my-16">
            <div class="modal-content py-4 text-left px-6">
                <div class="flex justify-between items-center pb-3">
                    <h3 class="text-xl font-bold text-gray-800">Pembayaran</h3>
                    <button class="modal-close cursor-pointer z-50" onclick="closeModal('paymentModal')">
                        <i class="fas fa-times text-gray-500 hover:text-gray-700"></i>
                    </button>
                </div>
                
                <div class="mb-4">
                    <div class="flex justify-between mb-4">
                        <span class="text-gray-700">Total Pembayaran:</span>
                        <span id="paymentGrandTotal" class="text-green-500 font-bold text-lg">Rp 0</span>
                    </div>
                    
                    <!-- Kategori Transaksi -->
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Kategori Transaksi
                        </label>
                        <div class="flex space-x-4">
                            <label class="flex items-center">
                                <input type="radio" name="transactionCategory" value="lunas" class="mr-2 text-green-600" checked>
                                <span class="text-sm">Lunas</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="transactionCategory" value="dp" class="mr-2 text-green-600">
                                <span class="text-sm">DP (Uang Muka)</span>
                            </label>
                        </div>
                    </div>

                    <!-- Upload Akad Jual Beli - Only show for DP transactions -->
                    <div id="akadJualBeliSection" class="mb-4 hidden">
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Upload Akad Jual Beli <span class="text-red-500">*</span>
                        </label>
                        <div class="border-2 border-dashed border-orange-300 rounded-lg p-4 text-center bg-orange-50">
                            <input type="file" id="akadJualBeliUpload" accept=".pdf" class="hidden">
                            <div class="cursor-pointer" onclick="document.getElementById('akadJualBeliUpload').click()">
                                <i class="fas fa-file-pdf text-2xl text-orange-500 mb-2"></i>
                                <p class="text-sm font-medium text-orange-600">Klik untuk upload akad jual beli</p>
                                <p class="text-xs text-orange-500 mt-1">File PDF • Maks 10MB</p>
                            </div>
                        </div>
                        <div id="akadJualBeliPreview" class="mt-2 space-y-1">
                            <!-- File preview will appear here -->
                        </div>
                       
                    </div>

                    <!-- Tax Type Display (Read-only) -->
                    <div class="mb-4 p-3 bg-gray-50 rounded-lg">
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Jenis Pajak Transaksi
                        </label>
                        <div class="flex items-center space-x-2">
                            <span id="modalTaxTypeDisplay" class="text-sm px-2 py-1 rounded-full bg-green-100 text-green-600">Non-PKP (0%)</span>
                            <span class="text-xs text-gray-500">- Sesuai pilihan di keranjang</span>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="paymentMethod">
                            Metode Pembayaran
                        </label>
                        <div id="paymentMethods">
                            <!-- Payment methods will be added here -->
                        </div>
                        <div id="qrisDetails" class="hidden mt-4">
                            <div class="bg-gray-50 p-4 rounded-lg text-center">
                                <h3 class="font-medium mb-2">Pembayaran QRIS</h3>
                                <div id="qrisImageContainer" class="flex justify-center">
                                    <!-- Gambar QRIS akan dimuat di sini -->
                                </div>
                                <p class="text-sm text-gray-500 mt-2">Scan QR code di atas untuk melakukan pembayaran</p>
                            </div>
                        </div>
                        <div id="transferDetails" class="hidden mt-4">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="flex justify-between items-center mb-2">
                                    <h3 class="font-medium">Informasi Rekening Bank</h3>
                                    <span id="bankTaxTypeIndicator" class="text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-600">-</span>
                                </div>
                                <p class="text-sm">Nama Pemilik: <span id="bankAccountName">-</span></p>
                                <p class="text-sm">Nama Bank: <span id="bankName">-</span></p>
                                <p class="text-sm">Nomor Rekening: <span id="bankAccountNumber">-</span></p>
                                {{-- <div class="mt-2 p-2 bg-green-50 rounded text-xs">
                                    <p class="text-green-600">
                                        <span id="taxInfoText">Transfer ke rekening sesuai dengan jenis pajak outlet</span>
                                    </p>
                                </div> --}}
                            </div>
                        </div>

                        <!-- Upload Bukti Pembayaran -->
                        <div id="uploadProofSection" class="hidden mt-4">
                            <div class="mb-4">
                                <!-- Cash Proof Upload -->
                                <div id="cashProofUploadSection" class="hidden">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">
                                        Upload Bukti Pembayaran Cash
                                    </label>
                                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center bg-gray-50">
                                        <input type="file" id="cashProofUpload" multiple accept="image/*,.pdf" class="hidden">
                                        <div class="cursor-pointer" onclick="document.getElementById('cashProofUpload').click()">
                                            <i class="fas fa-cloud-upload-alt text-2xl text-gray-400 mb-2"></i>
                                            <p class="text-sm text-gray-600">Klik untuk upload bukti pembayaran</p>
                                            <p class="text-xs text-gray-500 mt-1">JPG, PNG, PDF • Maks 5MB per file</p>
                                        </div>
                                    </div>
                                    <div id="cashProofPreview" class="mt-2 space-y-1">
                                        <!-- File previews will appear here -->
                                    </div>
                                </div>

                                <!-- Transfer Proof Upload -->
                                <div id="transferProofUploadSection" class="hidden">
                                    <label class="block text-sm font-bold mb-2 text-red-600">
                                        Upload Bukti Transfer (Wajib) *
                                    </label>
                                    <div class="border-2 border-dashed border-red-300 rounded-lg p-4 text-center bg-red-50">
                                        <input type="file" id="transferProofUpload" multiple accept="image/*,.pdf" class="hidden" required>
                                        <div class="cursor-pointer" onclick="document.getElementById('transferProofUpload').click()">
                                            <i class="fas fa-cloud-upload-alt text-2xl text-red-500 mb-2"></i>
                                            <p class="text-sm font-medium text-red-600">Klik untuk upload bukti transfer</p>
                                            <p class="text-xs text-red-500 mt-1">JPG, PNG, PDF • Maks 5MB per file</p>
                                        </div>
                                    </div>
                                    <div id="transferProofPreview" class="mt-2 space-y-1">
                                        <!-- File previews will appear here -->
                                    </div>
                                </div>

                                <!-- QRIS Proof Upload -->
                                <div id="qrisProofUploadSection" class="hidden">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">
                                        Upload Bukti Pembayaran QRIS
                                    </label>
                                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center bg-gray-50">
                                        <input type="file" id="qrisProofUpload" multiple accept="image/*,.pdf" class="hidden">
                                        <div class="cursor-pointer" onclick="document.getElementById('qrisProofUpload').click()">
                                            <i class="fas fa-cloud-upload-alt text-2xl text-gray-400 mb-2"></i>
                                            <p class="text-sm text-gray-600">Klik untuk upload bukti pembayaran QRIS</p>
                                            <p class="text-xs text-gray-500 mt-1">JPG, PNG, PDF • Maks 5MB per file</p>
                                        </div>
                                    </div>
                                    <div id="qrisProofPreview" class="mt-2 space-y-1">
                                        <!-- File previews will appear here -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Amount Received Section - Always show for DP, only for cash when Lunas -->
                    <div id="amountReceivedSection" class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="amountReceived">
                            <span id="amountReceivedLabel">Jumlah Uang Diterima</span>
                        </label>
                        <input type="text" id="amountReceived" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-green-500" placeholder="Rp 0">
                        <input type="hidden" id="amountReceivedRaw" name="total_paid">
                    </div>
                    
                    <!-- Change Section - Only show for cash payments when Lunas -->
                    <div id="changeSection" class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="changeAmount">
                            Kembalian
                        </label>
                        <input type="text" id="changeAmount" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100" readonly>
                    </div>

                    <!-- Member Search -->
                    <div class="mb-4">
                        <div class="flex justify-between items-center mb-2">
                            <label class="block text-gray-700 text-sm font-bold" for="memberDropdown">
                                Member
                            </label>
                            <button type="button" onclick="openAddMemberModal()" 
                                class="text-xs text-green-600 hover:text-green-700 font-medium flex items-center">
                                <i class="fas fa-plus mr-1"></i>
                                Tambah Member
                            </button>
                        </div>
                        <div class="member-dropdown-container relative">
                            <div class="flex items-center relative">
                                <input
                                    id="memberSearch"
                                    type="text"
                                    class="w-full px-3 py-2 text-sm rounded-md border border-gray-300 focus:border-green-500 focus:ring-1 focus:ring-green-500 placeholder-gray-400"
                                    placeholder="Cari member (nama/no hp)"
                                    autocomplete="off"
                                >
                                <div class="absolute right-0 top-0 h-full flex items-center pr-3">
                                    <i data-lucide="chevron-down" class="w-4 h-4 text-gray-500"></i>
                                </div>
                            </div>
                            <div id="memberDropdownList" class="dropdown-list absolute z-30 mt-1 w-full bg-white border border-gray-300 rounded-md shadow-lg hidden">
                                <div id="memberResults" class="max-h-48 overflow-y-auto p-1">
                                    <!-- Results will appear here -->
                                </div>
                                <!-- Add member button in dropdown -->
                                <div class="border-t border-gray-200 p-2">
                                    <button type="button" onclick="openAddMemberModal()" 
                                        class="w-full text-left px-3 py-2 text-sm text-green-600 hover:bg-green-50 rounded flex items-center">
                                        <i class="fas fa-plus mr-2"></i>
                                        Tambah Member Baru
                                    </button>
                                </div>
                            </div>
                            <div id="selectedMember" class="mt-2 hidden">
                                <div class="flex justify-between items-center bg-green-50 p-2 rounded">
                                    <div class="flex flex-col">
                                        <span id="memberName" class="font-medium text-sm"></span>
                                        <span id="memberCode" class="text-xs text-gray-500"></span>
                                    </div>
                                    <button id="removeMember" class="text-red-500 hover:text-red-700">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Masjid Search -->
                    <div class="mb-4">
                        <div class="flex justify-between items-center mb-2">
                            <label class="block text-gray-700 text-sm font-bold" for="masjidDropdown">
                                Masjid Tujuan
                            </label>
                            <button type="button" onclick="openAddMasjidModal()" 
                                class="text-xs text-green-600 hover:text-green-700 font-medium flex items-center">
                                <i class="fas fa-plus mr-1"></i>
                                Tambah Masjid
                            </button>
                        </div>
                        <div class="masjid-dropdown-container relative">
                            <div class="flex items-center relative">
                                <input
                                    id="masjidSearchPayment"
                                    type="text"
                                    class="w-full px-3 py-2 text-sm rounded-md border border-gray-300 focus:border-green-500 focus:ring-1 focus:ring-green-500 placeholder-gray-400"
                                    placeholder="Cari masjid (nama/alamat)"
                                    autocomplete="off"
                                >
                                <div class="absolute right-0 top-0 h-full flex items-center pr-3">
                                    <i data-lucide="chevron-down" class="w-4 h-4 text-gray-500"></i>
                                </div>
                            </div>
                            <div id="masjidDropdownListPayment" class="dropdown-list absolute z-30 mt-1 w-full bg-white border border-gray-300 rounded-md shadow-lg hidden">
                                <div id="masjidResultsPayment" class="max-h-48 overflow-y-auto p-1">
                                    <!-- Results will appear here -->
                                </div>
                                <!-- Add masjid button in dropdown -->
                                <div class="border-t border-gray-200 p-2">
                                    <button type="button" onclick="openAddMasjidModal()" 
                                        class="w-full text-left px-3 py-2 text-sm text-green-600 hover:bg-green-50 rounded flex items-center">
                                        <i class="fas fa-plus mr-2"></i>
                                        Tambah Masjid Baru
                                    </button>
                                </div>
                            </div>
                            <div id="selectedMasjidPayment" class="mt-2 hidden">
                                <div class="flex justify-between items-center bg-green-50 p-2 rounded">
                                    <div class="flex flex-col">
                                        <span id="masjidNamePayment" class="font-medium text-sm"></span>
                                        <span id="masjidAddressPayment" class="text-xs text-gray-500"></span>
                                    </div>
                                    <button id="removeMasjidPayment" class="text-red-500 hover:text-red-700">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="notes">
                            Catatan (Opsional)
                        </label>
                        <textarea id="notes" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-green-500" rows="2"></textarea>
                    </div> --}}
                </div>
                
                <div class="flex justify-end pt-2">
                    <button id="btnProcessPayment" class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600 transition-colors">
                        Proses Pembayaran
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Handle transaction category change to show/hide akad jual beli section
document.addEventListener('DOMContentLoaded', function() {
    const transactionCategoryRadios = document.querySelectorAll('input[name="transactionCategory"]');
    const akadJualBeliSection = document.getElementById('akadJualBeliSection');
    const akadJualBeliUpload = document.getElementById('akadJualBeliUpload');
    const akadJualBeliPreview = document.getElementById('akadJualBeliPreview');

    // Handle category change
    transactionCategoryRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'dp') {
                akadJualBeliSection.classList.remove('hidden');
                akadJualBeliUpload.setAttribute('required', 'required');
            } else {
                akadJualBeliSection.classList.add('hidden');
                akadJualBeliUpload.removeAttribute('required');
                // Clear file input and preview
                akadJualBeliUpload.value = '';
                akadJualBeliPreview.innerHTML = '';
            }
        });
    });

    // Handle file upload preview
    if (akadJualBeliUpload) {
        akadJualBeliUpload.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Validate file type
                if (file.type !== 'application/pdf') {
                    alert('Hanya file PDF yang diperbolehkan');
                    this.value = '';
                    return;
                }

                // Validate file size (10MB)
                if (file.size > 10 * 1024 * 1024) {
                    alert('Ukuran file maksimal 10MB');
                    this.value = '';
                    return;
                }

                // Show preview
                akadJualBeliPreview.innerHTML = `
                    <div class="flex items-center justify-between p-2 bg-white border border-orange-200 rounded">
                        <div class="flex items-center">
                            <i class="fas fa-file-pdf text-orange-500 mr-2"></i>
                            <span class="text-sm font-medium">${file.name}</span>
                            <span class="text-xs text-gray-500 ml-2">(${(file.size / 1024 / 1024).toFixed(2)} MB)</span>
                        </div>
                        <button type="button" onclick="clearAkadJualBeli()" class="text-red-500 hover:text-red-700">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `;
            }
        });
    }
});

// Function to clear akad jual beli file
function clearAkadJualBeli() {
    const akadJualBeliUpload = document.getElementById('akadJualBeliUpload');
    const akadJualBeliPreview = document.getElementById('akadJualBeliPreview');
    
    if (akadJualBeliUpload) {
        akadJualBeliUpload.value = '';
    }
    if (akadJualBeliPreview) {
        akadJualBeliPreview.innerHTML = '';
    }
}

// Format currency input functions
function formatCurrency(input) {
    // Remove all non-digit characters
    let value = input.value.replace(/[^\d]/g, '');
    
    // Convert to number and format with dots as thousand separators
    if (value) {
        // Add dots for thousands separator
        let formatted = value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        input.value = formatted;
        
        // Store raw value (without formatting) in hidden input
        const hiddenInput = document.getElementById('amountReceivedRaw');
        if (hiddenInput) {
            hiddenInput.value = value;
        }
    } else {
        input.value = '';
        const hiddenInput = document.getElementById('amountReceivedRaw');
        if (hiddenInput) {
            hiddenInput.value = '';
        }
    }
}

function unformatCurrency(value) {
    // Remove all non-digit characters to get raw number
    return value.replace(/[^\d]/g, '');
}

// Initialize currency formatting for amount received input
document.addEventListener('DOMContentLoaded', function() {
    const amountReceivedInput = document.getElementById('amountReceived');
    
    if (amountReceivedInput) {
        // Format on input - immediately format and update display
        amountReceivedInput.addEventListener('input', function() {
            const rawValue = this.value.replace(/[^\d]/g, '');
            
            // Update hidden field with raw value
            const hiddenInput = document.getElementById('amountReceivedRaw');
            if (hiddenInput) {
                hiddenInput.value = rawValue;
            }
            
            // Format display value like kembalian does
            if (rawValue) {
                const formatted = rawValue.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                this.value = formatted;
            } else {
                this.value = '';
            }
        });
        
        // Format on paste
        amountReceivedInput.addEventListener('paste', function() {
            setTimeout(() => {
                const rawValue = this.value.replace(/[^\d]/g, '');
                
                // Update hidden field with raw value
                const hiddenInput = document.getElementById('amountReceivedRaw');
                if (hiddenInput) {
                    hiddenInput.value = rawValue;
                }
                
                // Format display value
                if (rawValue) {
                    const formatted = rawValue.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                    this.value = formatted;
                } else {
                    this.value = '';
                }
            }, 10);
        });
        
        // Keep formatting on focus but select all for easy replacement
        amountReceivedInput.addEventListener('focus', function() {
            // Select all text for easier replacement while keeping format
            this.select();
        });
        
        // Re-format on blur to ensure consistency
        amountReceivedInput.addEventListener('blur', function() {
            const rawValue = this.value.replace(/[^\d]/g, '');
            
            // Update hidden field with raw value
            const hiddenInput = document.getElementById('amountReceivedRaw');
            if (hiddenInput) {
                hiddenInput.value = rawValue;
            }
            
            // Format display value
            if (rawValue) {
                const formatted = rawValue.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                this.value = formatted;
            } else {
                this.value = '';
            }
        });
    }
});
</script>