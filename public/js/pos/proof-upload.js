/**
 * Proof Upload System untuk Transaksi dan Kas
 */
class ProofUploadManager {
    constructor() {
        this.allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];
        this.maxFileSize = 5 * 1024 * 1024; // 5MB
        this.pendingProofs = [];
    }

    // Enhanced Payment Modal dengan Upload Bukti
    enhancePaymentModalWithProof(paymentData) {
        return `
            <div class="space-y-6">
                <!-- Existing payment content... -->
                
                <!-- Proof Upload Section -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h4 class="font-medium text-blue-800 mb-3 flex items-center">
                        <i data-lucide="upload" class="w-4 h-4 mr-2"></i>
                        Upload Bukti Transaksi
                    </h4>
                    
                    <div class="space-y-4">
                        <!-- Upload Area -->
                        <div class="border-2 border-dashed border-blue-300 rounded-lg p-6 text-center bg-white">
                            <input type="file" id="transactionProofUpload" multiple accept="image/*,.pdf" class="hidden">
                            <div id="uploadDropzone" class="cursor-pointer" onclick="document.getElementById('transactionProofUpload').click()">
                                <i data-lucide="cloud-upload" class="w-12 h-12 mx-auto text-blue-400 mb-3"></i>
                                <p class="text-sm font-medium text-blue-600">Klik untuk upload bukti transaksi</p>
                                <p class="text-xs text-blue-500 mt-1">Maksimal 5MB • JPG, PNG, PDF • Bisa multiple files</p>
                            </div>
                        </div>

                        <!-- Upload Progress -->
                        <div id="uploadProgress" class="hidden">
                            <div class="bg-gray-200 rounded-full h-2">
                                <div id="uploadProgressBar" class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                            </div>
                            <p class="text-sm text-gray-600 mt-1">Mengupload <span id="uploadProgressText">0%</span></p>
                        </div>

                        <!-- Uploaded Files Preview -->
                        <div id="uploadedFilesPreview" class="space-y-2">
                            <!-- Will be populated dynamically -->
                        </div>

                        <!-- Upload Type Selection -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Bukti</label>
                            <select id="proofType" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="transaction">Struk Transaksi</option>
                                <option value="payment_confirmation">Konfirmasi Pembayaran</option>
                                <option value="receipt">Kwitansi</option>
                                <option value="invoice">Faktur</option>
                                <option value="other">Lainnya</option>
                            </select>
                        </div>

                        <!-- Upload Notes -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Upload</label>
                            <textarea id="proofNotes" rows="2" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Catatan tambahan tentang bukti yang diupload..."></textarea>
                        </div>

                        <!-- Warning Notice -->
                        <div class="bg-yellow-50 border border-yellow-200 rounded-md p-3">
                            <div class="flex items-start">
                                <i data-lucide="alert-triangle" class="w-5 h-5 text-yellow-600 mt-0.5 mr-2"></i>
                                <div class="text-sm text-yellow-800">
                                    <p class="font-medium">Penting:</p>
                                    <ul class="list-disc list-inside mt-1 space-y-1">
                                        <li>Semua bukti akan diverifikasi oleh supervisor/admin</li>
                                        <li>Transaksi akan berstatus "Pending" hingga bukti disetujui</li>
                                        <li>Pastikan file jelas dan dapat dibaca</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    // Cash Management Modal dengan Upload Bukti
    showCashManagementModal() {
        const modal = document.createElement('div');
        modal.id = 'cashManagementModal';
        modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
        
        modal.innerHTML = `
            <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <i data-lucide="banknote" class="w-5 h-5 mr-2 text-green-500"></i>
                            Manajemen Kas
                        </h3>
                        <button onclick="this.closest('#cashManagementModal').remove()" class="text-gray-400 hover:text-gray-600">
                            <i data-lucide="x" class="w-5 h-5"></i>
                        </button>
                    </div>

                    <!-- Cash Action Tabs -->
                    <div class="flex border-b border-gray-200 mb-6">
                        <button class="cash-tab-btn active px-4 py-2 border-b-2 border-green-500 text-green-600 font-medium" data-tab="add">
                            <i data-lucide="plus-circle" class="w-4 h-4 inline mr-2"></i>
                            Tambah Kas
                        </button>
                        <button class="cash-tab-btn px-4 py-2 border-b-2 border-transparent text-gray-500 hover:text-gray-700" data-tab="withdraw">
                            <i data-lucide="minus-circle" class="w-4 h-4 inline mr-2"></i>
                            Ambil Kas
                        </button>
                        <button class="cash-tab-btn px-4 py-2 border-b-2 border-transparent text-gray-500 hover:text-gray-700" data-tab="transfer">
                            <i data-lucide="arrow-right-left" class="w-4 h-4 inline mr-2"></i>
                            Transfer
                        </button>
                    </div>

                    <div id="cashTabContent">
                        <!-- Add Cash Tab -->
                        <div id="addCashTab" class="cash-tab-content">
                            <form id="addCashForm" class="space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah</label>
                                        <input type="text" id="addCashAmount" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                                            placeholder="Rp 0" required>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Sumber Dana</label>
                                        <select id="addCashSource" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500" required>
                                            <option value="">Pilih sumber...</option>
                                            <option value="owner_deposit">Setoran Pemilik</option>
                                            <option value="bank_withdrawal">Penarikan Bank</option>
                                            <option value="daily_sales">Hasil Penjualan</option>
                                            <option value="loan">Pinjaman</option>
                                            <option value="other">Lainnya</option>
                                        </select>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                                    <textarea id="addCashNotes" rows="2" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                                        placeholder="Keterangan tambahan..." required></textarea>
                                </div>

                                ${this.renderProofUploadSection('addCash')}

                                <div class="flex justify-end space-x-3 pt-4">
                                    <button type="button" onclick="this.closest('#cashManagementModal').remove()" 
                                        class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                                        Batal
                                    </button>
                                    <button type="submit" 
                                        class="px-6 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 transition-colors">
                                        Tambah Kas
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Withdraw Cash Tab -->
                        <div id="withdrawCashTab" class="cash-tab-content hidden">
                            <form id="withdrawCashForm" class="space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah</label>
                                        <input type="text" id="withdrawCashAmount" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500"
                                            placeholder="Rp 0" required>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Tujuan</label>
                                        <select id="withdrawCashPurpose" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500" required>
                                            <option value="">Pilih tujuan...</option>
                                            <option value="bank_deposit">Setoran Bank</option>
                                            <option value="operational_expense">Biaya Operasional</option>
                                            <option value="owner_withdrawal">Penarikan Pemilik</option>
                                            <option value="supplier_payment">Bayar Supplier</option>
                                            <option value="emergency">Darurat</option>
                                            <option value="other">Lainnya</option>
                                        </select>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                                    <textarea id="withdrawCashNotes" rows="2" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500"
                                        placeholder="Keterangan tambahan..." required></textarea>
                                </div>

                                ${this.renderProofUploadSection('withdrawCash')}

                                <div class="flex justify-end space-x-3 pt-4">
                                    <button type="button" onclick="this.closest('#cashManagementModal').remove()" 
                                        class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                                        Batal
                                    </button>
                                    <button type="submit" 
                                        class="px-6 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 transition-colors">
                                        Ambil Kas
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Transfer Cash Tab -->
                        <div id="transferCashTab" class="cash-tab-content hidden">
                            <form id="transferCashForm" class="space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah</label>
                                        <input type="text" id="transferCashAmount" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                            placeholder="Rp 0" required>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Dari Outlet</label>
                                        <select id="transferFromOutlet" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                            <!-- Will be populated -->
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Ke Outlet</label>
                                        <select id="transferToOutlet" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                            <!-- Will be populated -->
                                        </select>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                                    <textarea id="transferCashNotes" rows="2" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        placeholder="Keterangan transfer..." required></textarea>
                                </div>

                                ${this.renderProofUploadSection('transferCash')}

                                <div class="flex justify-end space-x-3 pt-4">
                                    <button type="button" onclick="this.closest('#cashManagementModal').remove()" 
                                        class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                                        Batal
                                    </button>
                                    <button type="submit" 
                                        class="px-6 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition-colors">
                                        Transfer Kas
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        `;

        document.body.appendChild(modal);
        this.attachCashManagementEventListeners(modal);
        lucide.createIcons({ icons });
    }

    // Render proof upload section
    renderProofUploadSection(formType) {
        return `
            <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                <h4 class="font-medium text-orange-800 mb-3 flex items-center">
                    <i data-lucide="camera" class="w-4 h-4 mr-2"></i>
                    Upload Bukti (Wajib)
                </h4>
                
                <div class="space-y-3">
                    <!-- Upload Area -->
                    <div class="border-2 border-dashed border-orange-300 rounded-lg p-4 text-center bg-white">
                        <input type="file" id="${formType}ProofUpload" multiple accept="image/*,.pdf" class="hidden" required>
                        <div class="cursor-pointer" onclick="document.getElementById('${formType}ProofUpload').click()">
                            <i data-lucide="upload-cloud" class="w-8 h-8 mx-auto text-orange-400 mb-2"></i>
                            <p class="text-sm font-medium text-orange-600">Upload bukti ${formType === 'addCash' ? 'penambahan kas' : formType === 'withdrawCash' ? 'pengambilan kas' : 'transfer kas'}</p>
                            <p class="text-xs text-orange-500 mt-1">Foto struk, kwitansi, atau dokumen pendukung</p>
                        </div>
                    </div>

                    <!-- Preview Area -->
                    <div id="${formType}ProofPreview" class="space-y-2">
                        <!-- Will show uploaded files -->
                    </div>

                    <!-- Proof Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Bukti</label>
                        <select id="${formType}ProofType" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                            <option value="receipt">Struk/Kwitansi</option>
                            <option value="bank_statement">Mutasi Bank</option>
                            <option value="photo">Foto Kas</option>
                            <option value="document">Dokumen Pendukung</option>
                            <option value="other">Lainnya</option>
                        </select>
                    </div>
                </div>
            </div>
        `;
    }

    // Attach cash management event listeners
    attachCashManagementEventListeners(modal) {
        // Tab switching
        modal.querySelectorAll('.cash-tab-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const tab = e.target.dataset.tab;
                this.switchCashTab(modal, tab);
            });
        });

        // File uploads for each form type
        ['addCash', 'withdrawCash', 'transferCash'].forEach(formType => {
            this.attachFileUploadListeners(modal, formType);
        });

        // Form submissions
        modal.querySelector('#addCashForm').addEventListener('submit', (e) => {
            e.preventDefault();
            this.processCashTransaction(modal, 'add');
        });

        modal.querySelector('#withdrawCashForm').addEventListener('submit', (e) => {
            e.preventDefault();
            this.processCashTransaction(modal, 'withdraw');
        });

        modal.querySelector('#transferCashForm').addEventListener('submit', (e) => {
            e.preventDefault();
            this.processCashTransaction(modal, 'transfer');
        });
    }

    // Switch cash tab
    switchCashTab(modal, activeTab) {
        // Update tab buttons
        modal.querySelectorAll('.cash-tab-btn').forEach(btn => {
            btn.classList.remove('active', 'border-green-500', 'text-green-600');
            btn.classList.add('border-transparent', 'text-gray-500');
        });

        modal.querySelector(`[data-tab="${activeTab}"]`).classList.add('active', 'border-green-500', 'text-green-600');
        modal.querySelector(`[data-tab="${activeTab}"]`).classList.remove('border-transparent', 'text-gray-500');

        // Update tab content
        modal.querySelectorAll('.cash-tab-content').forEach(content => {
            content.classList.add('hidden');
        });

        modal.querySelector(`#${activeTab}CashTab`).classList.remove('hidden');
    }

    // Attach file upload listeners
    attachFileUploadListeners(modal, formType) {
        const fileInput = modal.querySelector(`#${formType}ProofUpload`);
        const previewContainer = modal.querySelector(`#${formType}ProofPreview`);

        fileInput.addEventListener('change', (e) => {
            this.handleFileUpload(e.target.files, previewContainer, formType);
        });
    }

    // Handle file upload with validation and preview
    handleFileUpload(files, previewContainer, formType) {
        previewContainer.innerHTML = '';

        Array.from(files).forEach((file, index) => {
            // Validate file
            if (!this.validateFile(file)) return;

            // Create preview element
            const previewElement = document.createElement('div');
            previewElement.className = 'flex items-center justify-between p-3 bg-green-50 border border-green-200 rounded-lg';
            
            previewElement.innerHTML = `
                <div class="flex items-center space-x-3">
                    ${file.type.startsWith('image/') ? 
                        `<img src="${URL.createObjectURL(file)}" alt="Preview" class="w-12 h-12 rounded object-cover">` :
                        '<div class="w-12 h-12 bg-red-100 rounded flex items-center justify-center"><i data-lucide="file-text" class="w-6 h-6 text-red-600"></i></div>'
                    }
                    <div>
                        <div class="font-medium text-gray-900">${file.name}</div>
                        <div class="text-sm text-gray-500">${this.formatFileSize(file.size)}</div>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">Valid</span>
                    <button type="button" class="text-red-500 hover:text-red-700" onclick="this.closest('div').remove()">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
            `;

            previewContainer.appendChild(previewElement);
        });

        lucide.createIcons({ icons });
    }

    // Validate file
    validateFile(file) {
        // Check file type
        if (!this.allowedTypes.includes(file.type)) {
            showNotification(`File ${file.name} tidak didukung. Hanya JPG, PNG, dan PDF yang diperbolehkan.`, 'error');
            return false;
        }

        // Check file size
        if (file.size > this.maxFileSize) {
            showNotification(`File ${file.name} terlalu besar. Maksimal 5MB.`, 'error');
            return false;
        }

        return true;
    }

    // Format file size
    formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // Process cash transaction
    async processCashTransaction(modal, type) {
        const formId = `${type}CashForm`;
        const form = modal.querySelector(`#${formId}`);
        const formData = new FormData(form);

        // Get uploaded files
        const fileInput = modal.querySelector(`#${type}CashProofUpload`);
        const files = Array.from(fileInput.files);

        if (files.length === 0) {
            showNotification('Upload bukti wajib dilakukan', 'error');
            return;
        }

        // Show loading
        const loadingOverlay = showLoading('Memproses transaksi kas...');

        try {
            // Create cash transaction record
            const transactionData = {
                id: `cash_${Date.now()}`,
                type: type,
                amount: parseCurrencyInput(modal.querySelector(`#${type}CashAmount`).value),
                outlet_id: outletInfo.id,
                outlet_name: outletInfo.name,
                user_id: 'current_user', // TODO: get from auth
                timestamp: new Date(),
                status: 'pending_approval',
                proof_files: files.map(file => ({
                    name: file.name,
                    size: file.size,
                    type: file.type,
                    url: URL.createObjectURL(file) // Temporary for preview
                })),
                notes: modal.querySelector(`#${type}CashNotes`).value,
                proof_type: modal.querySelector(`#${type}CashProofType`).value
            };

            // Add specific fields based on transaction type
            if (type === 'add') {
                transactionData.source = modal.querySelector('#addCashSource').value;
            } else if (type === 'withdraw') {
                transactionData.purpose = modal.querySelector('#withdrawCashPurpose').value;
            } else if (type === 'transfer') {
                transactionData.from_outlet = modal.querySelector('#transferFromOutlet').value;
                transactionData.to_outlet = modal.querySelector('#transferToOutlet').value;
            }

            // Store in pending approvals
            this.addToPendingApprovals(transactionData);

            // Show success message
            showNotification(`Transaksi ${type === 'add' ? 'penambahan' : type === 'withdraw' ? 'pengambilan' : 'transfer'} kas berhasil diajukan`, 'success');
            
            // Close modal
            modal.remove();

            // Show approval pending notification
            this.showApprovalPendingNotification(transactionData);

        } catch (error) {
            console.error('Cash transaction error:', error);
            showNotification('Gagal memproses transaksi kas', 'error');
        } finally {
            hideLoading();
        }
    }

    // Add to pending approvals
    addToPendingApprovals(transactionData) {
        let pendingApprovals = JSON.parse(localStorage.getItem('pendingCashApprovals') || '[]');
        pendingApprovals.push(transactionData);
        localStorage.setItem('pendingCashApprovals', JSON.stringify(pendingApprovals));
    }

    // Show approval pending notification
    showApprovalPendingNotification(transactionData) {
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-60';
        
        modal.innerHTML = `
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
                <div class="p-6 text-center">
                    <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="clock" class="w-8 h-8 text-yellow-600"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Menunggu Persetujuan</h3>
                    <p class="text-gray-600 text-sm mb-4">
                        Transaksi kas ${transactionData.type === 'add' ? 'penambahan' : transactionData.type === 'withdraw' ? 'pengambilan' : 'transfer'} 
                        sebesar <strong>${formatCurrency(transactionData.amount)}</strong> 
                        sedang menunggu verifikasi dari admin/supervisor.
                    </p>
                    
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4 text-left">
                        <div class="text-sm text-blue-800">
                            <p class="font-medium mb-2">Yang akan diverifikasi:</p>
                            <ul class="list-disc list-inside space-y-1">
                                <li>Bukti dokumen yang diupload</li>
                                <li>Kesesuaian jumlah dengan bukti</li>
                                <li>Kelengkapan keterangan</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="flex justify-center space-x-3">
                        <button onclick="this.closest('div').remove()" 
                            class="px-4 py-2 bg-yellow-500 text-white rounded-md hover:bg-yellow-600 transition-colors">
                            Mengerti
                        </button>
                        <button onclick="proofUploadManager.showPendingApprovals(); this.closest('div').remove()" 
                            class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition-colors">
                            Lihat Status
                        </button>
                    </div>
                </div>
            </div>
        `;

        document.body.appendChild(modal);
        lucide.createIcons({ icons });
    }

    // Show pending approvals dashboard
    showPendingApprovals() {
        // Implementation for showing pending approvals dashboard
        // This would be similar to bonus tracking but for cash transactions
        console.log('Show pending approvals dashboard');
    }
}

// Global instance
const proofUploadManager = new ProofUploadManager();