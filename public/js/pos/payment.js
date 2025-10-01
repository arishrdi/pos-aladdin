/**
 * Payment Management untuk POS System
 */
class PaymentManager {
    constructor(cartManager) {
        this.cartManager = cartManager;
        this.currentOrder = null;
        this.uploadedPaymentProofs = {
            cash: [],
            transfer: [],
            qris: []
        };
        this.awaitingApproval = false;
    }

    // Show payment modal dengan fitur lengkap
    showPaymentModal() {
        if (this.cartManager.cart.length === 0) {
            showNotification('Keranjang belanja kosong', 'warning');
            return;
        }

        const totals = this.cartManager.calculateTotals();
        this.renderPaymentModal(totals);
        openModal('paymentModal');
    }

    // Render payment modal dengan semua fitur
    renderPaymentModal(totals) {
        const modalContent = document.getElementById('paymentModalContent');
        
        modalContent.innerHTML = `
            <div class="space-y-6">
                <!-- Header -->
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900">Pembayaran</h3>
                    <button onclick="closeModal('paymentModal')" class="text-gray-400 hover:text-gray-600">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <!-- Customer & Transaction Info -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Member Selection -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Customer</label>
                        ${this.renderMemberSection()}
                    </div>

                    <!-- Transaction Type -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Jenis Transaksi</label>
                        <select id="transactionType" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                            ${POS_CONFIG.TRANSACTION_TYPES.map(type => 
                                `<option value="${type.id}" ${type.id === this.cartManager.transactionType ? 'selected' : ''}>
                                    ${type.name}
                                </option>`
                            ).join('')}
                        </select>
                    </div>
                </div>

                <!-- Tax Type Selection -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">Jenis Pajak</label>
                    <div class="flex space-x-4">
                        ${POS_CONFIG.TAX_TYPES.map(tax => `
                            <label class="flex items-center">
                                <input type="radio" name="taxType" value="${tax.id}" 
                                    ${tax.id === this.cartManager.taxType ? 'checked' : ''}
                                    class="mr-2 text-green-600 focus:ring-green-500">
                                <span class="text-sm">${tax.name} (${tax.rate}%)</span>
                            </label>
                        `).join('')}
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h4 class="font-medium mb-3">Ringkasan Pesanan</h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span>Subtotal (${formatQuantity(totals.totalQuantity)} item)</span>
                            <span>${formatCurrency(totals.subtotal)}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Diskon</span>
                            <span>-${formatCurrency(totals.totalDiscount)}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Pajak (${totals.taxRate}%)</span>
                            <span>${formatCurrency(totals.tax)}</span>
                        </div>
                        <div class="border-t pt-2 flex justify-between font-semibold">
                            <span>Total</span>
                            <span class="text-green-600">${formatCurrency(totals.grandTotal)}</span>
                        </div>
                    </div>
                </div>

                <!-- Payment Methods -->
                <div class="space-y-3">
                    <label class="block text-sm font-medium text-gray-700">Metode Pembayaran</label>
                    <div id="paymentMethodsContainer" class="space-y-2">
                        ${this.renderPaymentMethods()}
                    </div>
                </div>

                <!-- Payment Details berdasarkan method yang dipilih -->
                <div id="paymentDetails" class="space-y-4">
                    <!-- Cash Payment -->
                    <div id="cashPaymentSection" class="hidden space-y-3">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Uang Diterima</label>
                                <input type="text" id="amountReceived" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                                    placeholder="Rp 0">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Kembalian</label>
                                <input type="text" id="changeAmount" readonly
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50">
                            </div>
                        </div>
                        
                        <!-- Upload Bukti Pembayaran Cash -->
                        <div>
                            <label class="block text-sm font-medium text-red-600 mb-2">Upload Bukti Pembayaran (Wajib) *</label>
                            <div class="border-2 border-dashed border-red-300 rounded-lg p-4 text-center bg-red-50">
                                <input type="file" id="cashProofUpload" accept="image/*,.pdf" class="hidden" required>
                                <div class="cursor-pointer" onclick="document.getElementById('cashProofUpload').click()">
                                    <i data-lucide="upload" class="w-6 h-6 mx-auto mb-2 text-red-500"></i>
                                    <p class="text-sm font-medium text-red-600">Klik untuk upload bukti pembayaran</p>
                                    <p class="text-xs text-red-500 mt-1">JPG, PNG, PDF • Maks 5MB per file</p>
                                </div>
                            </div>
                            <div id="cashProofPreview" class="mt-2 space-y-1">
                                <!-- File previews will appear here -->
                            </div>
                        </div>
                    </div>

                    <!-- Transfer Payment -->
                    <div id="transferPaymentSection" class="hidden space-y-3">
                        <div class="bg-blue-50 p-3 rounded-lg">
                            <h5 class="font-medium text-blue-800 mb-2">Informasi Transfer</h5>
                            <div class="text-sm space-y-1 text-blue-700">
                                <p><strong>Bank:</strong> <span id="bankName">${outletInfo.bank_account?.bank || '-'}</span></p>
                                <p><strong>No. Rekening:</strong> <span id="bankAccountNumber">${outletInfo.bank_account?.nomor || '-'}</span></p>
                                <p><strong>Atas Nama:</strong> <span id="bankAccountName">${outletInfo.bank_account?.atas_nama || '-'}</span></p>
                            </div>
                        </div>
                        
                        <!-- Upload Bukti Transfer -->
                        <div>
                            <label class="block text-sm font-medium text-red-600 mb-2">Upload Bukti Transfer (Wajib) *</label>
                            <div class="border-2 border-dashed border-red-300 rounded-lg p-4 text-center bg-red-50">
                                <input type="file" id="transferProofUpload" accept="image/*,.pdf" class="hidden" required>
                                <div class="cursor-pointer" onclick="document.getElementById('transferProofUpload').click()">
                                    <i data-lucide="upload" class="w-6 h-6 mx-auto mb-2 text-red-500"></i>
                                    <p class="text-sm font-medium text-red-600">Klik untuk upload bukti transfer</p>
                                    <p class="text-xs text-red-500 mt-1">JPG, PNG, PDF • Maks 5MB per file</p>
                                </div>
                            </div>
                            <div id="transferProofPreview" class="mt-2 space-y-1">
                                <!-- File previews will appear here -->
                            </div>
                        </div>
                    </div>

                    <!-- QRIS Payment -->
                    <div id="qrisPaymentSection" class="hidden space-y-3">
                        <div class="text-center">
                            <div id="qrisContainer" class="bg-white p-4 rounded-lg border">
                                ${outletInfo.qris ? 
                                    `<img src="${outletInfo.qris}" alt="QRIS" class="w-64 h-64 mx-auto">` :
                                    `<div class="w-64 h-64 mx-auto bg-gray-100 rounded flex items-center justify-center">
                                        <div class="text-gray-400 text-center">
                                            <i data-lucide="qr-code" class="w-12 h-12 mx-auto mb-2"></i>
                                            <p>QR Code tidak tersedia</p>
                                        </div>
                                    </div>`
                                }
                            </div>
                            <p class="text-sm text-gray-600 mt-2">Scan QR Code untuk pembayaran</p>
                        </div>
                        
                        <!-- Upload Bukti Pembayaran QRIS -->
                        <div>
                            <label class="block text-sm font-medium text-red-600 mb-2">Upload Bukti Pembayaran QRIS (Wajib) *</label>
                            <div class="border-2 border-dashed border-red-300 rounded-lg p-4 text-center bg-red-50">
                                <input type="file" id="qrisProofUpload" accept="image/*,.pdf" class="hidden" required>
                                <div class="cursor-pointer" onclick="document.getElementById('qrisProofUpload').click()">
                                    <i data-lucide="upload" class="w-6 h-6 mx-auto mb-2 text-red-500"></i>
                                    <p class="text-sm font-medium text-red-600">Klik untuk upload bukti pembayaran QRIS</p>
                                    <p class="text-xs text-red-500 mt-1">JPG, PNG, PDF • Maks 5MB per file</p>
                                </div>
                            </div>
                            <div id="qrisProofPreview" class="mt-2 space-y-1">
                                <!-- File previews will appear here -->
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">Catatan (Opsional)</label>
                    <textarea id="paymentNotes" rows="2" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                        placeholder="Catatan tambahan..."></textarea>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end space-x-3 pt-4 border-t">
                    <button type="button" onclick="closeModal('paymentModal')" 
                        class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="button" id="btnProcessPayment"
                        class="px-6 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 transition-colors">
                        Proses Pembayaran
                    </button>
                </div>
            </div>
        `;

        this.attachPaymentEventListeners(totals);
        lucide.createIcons();
    }

    // Render member section
    renderMemberSection() {
        const member = this.cartManager.selectedMember;
        
        return `
            <div class="relative">
                ${member ? `
                    <div id="selectedMemberInfo" class="flex items-center justify-between p-3 bg-green-50 border border-green-200 rounded-md">
                        <div class="flex items-center">
                            <i data-lucide="user" class="w-4 h-4 text-green-600 mr-2"></i>
                            <div>
                                <div class="font-medium text-green-800">${member.name}</div>
                                <div class="text-sm text-green-600">${member.member_code || 'No Code'}</div>
                            </div>
                        </div>
                        <button type="button" id="removeMemberBtn" class="text-green-600 hover:text-green-800">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </button>
                    </div>
                ` : `
                    <div class="member-search-container">
                        <input type="text" id="memberSearch" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="Cari member...">
                        <div id="memberDropdown" class="member-results hidden">
                            <!-- Member search results -->
                        </div>
                    </div>
                `}
            </div>
        `;
    }

    // Render payment methods
    renderPaymentMethods() {
        return POS_CONFIG.PAYMENT_METHODS.map(method => `
            <label class="payment-method-option flex items-center p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50">
                <input type="radio" name="paymentMethod" value="${method.id}" class="mr-3 text-green-600 focus:ring-green-500">
                <i data-lucide="${method.icon}" class="w-5 h-5 text-gray-600 mr-3"></i>
                <span class="font-medium">${method.name}</span>
            </label>
        `).join('');
    }

    // Attach event listeners
    attachPaymentEventListeners(totals) {
        // Transaction type change
        document.getElementById('transactionType').addEventListener('change', (e) => {
            this.cartManager.setTransactionType(e.target.value);
        });

        // Tax type change
        document.querySelectorAll('input[name="taxType"]').forEach(radio => {
            radio.addEventListener('change', (e) => {
                this.cartManager.setTaxType(e.target.value);
                // Recalculate and update display
                const newTotals = this.cartManager.calculateTotals();
                this.updatePaymentSummary(newTotals);
            });
        });

        // Payment method change
        document.querySelectorAll('input[name="paymentMethod"]').forEach(radio => {
            radio.addEventListener('change', (e) => {
                this.handlePaymentMethodChange(e.target.value, totals);
            });
        });

        // Cash amount calculation
        const amountReceivedInput = document.getElementById('amountReceived');
        if (amountReceivedInput) {
            amountReceivedInput.addEventListener('input', (e) => {
                // Get raw value from hidden input that's updated by the modal's formatCurrency function
                setTimeout(() => {
                    const amountReceivedRaw = document.getElementById('amountReceivedRaw')?.value;
                    const received = amountReceivedRaw ? parseInt(amountReceivedRaw) : parseCurrencyInput(e.target.value);
                    const change = Math.max(0, received - totals.grandTotal);
                    document.getElementById('changeAmount').value = formatCurrency(change);
                }, 10); // Small delay to ensure hidden input is updated
            });
        }

        // File upload event listeners for all payment methods
        const cashProofUpload = document.getElementById('cashProofUpload');
        if (cashProofUpload) {
            cashProofUpload.addEventListener('change', (e) => {
                this.handlePaymentProofUpload(e, 'cash');
            });
        }

        const transferProofUpload = document.getElementById('transferProofUpload');
        if (transferProofUpload) {
            transferProofUpload.addEventListener('change', (e) => {
                this.handlePaymentProofUpload(e, 'transfer');
            });
        }

        const qrisProofUpload = document.getElementById('qrisProofUpload');
        if (qrisProofUpload) {
            qrisProofUpload.addEventListener('change', (e) => {
                this.handlePaymentProofUpload(e, 'qris');
            });
        }

        // Member search
        this.attachMemberSearchListeners();

        // Process payment button
        document.getElementById('btnProcessPayment').addEventListener('click', () => {
            this.processPayment(totals);
        });
    }

    // Handle payment method change
    handlePaymentMethodChange(method, totals) {
        // Hide all payment sections
        document.getElementById('cashPaymentSection').classList.add('hidden');
        document.getElementById('transferPaymentSection').classList.add('hidden');
        document.getElementById('qrisPaymentSection').classList.add('hidden');

        // Show relevant section
        switch (method) {
            case 'cash':
                document.getElementById('cashPaymentSection').classList.remove('hidden');
                break;
            case 'transfer':
                document.getElementById('transferPaymentSection').classList.remove('hidden');
                break;
            case 'qris':
                document.getElementById('qrisPaymentSection').classList.remove('hidden');
                break;
        }
    }

    // Handle payment proof upload for all payment methods
    handlePaymentProofUpload(event, paymentMethod) {
        const files = event.target.files;
        if (!files || files.length === 0) return;

        const validFiles = [];

        // Process each file
        Array.from(files).forEach((file) => {
            // Validate file type
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];
            if (!allowedTypes.includes(file.type)) {
                showNotification(`File ${file.name} tidak didukung. Hanya JPG, PNG, dan PDF yang diperbolehkan.`, 'error');
                return;
            }

            // Validate file size (max 5MB)
            if (file.size > 5 * 1024 * 1024) {
                showNotification(`File ${file.name} terlalu besar. Maksimal 5MB per file.`, 'error');
                return;
            }

            validFiles.push(file);
        });

        if (validFiles.length > 0) {
            // Store files
            this.uploadedPaymentProofs[paymentMethod] = validFiles;
            
            // Show preview
            this.showPaymentProofPreview(validFiles, paymentMethod);
            
            showNotification(`${validFiles.length} file berhasil diupload untuk ${paymentMethod}`, 'success');
        }
    }

    // Show payment proof preview
    showPaymentProofPreview(files, paymentMethod) {
        const previewContainer = document.getElementById(`${paymentMethod}ProofPreview`);
        if (!previewContainer) return;

        previewContainer.innerHTML = '';

        files.forEach((file, index) => {
            const previewElement = document.createElement('div');
            previewElement.className = 'flex items-center justify-between p-2 bg-gray-50 border border-gray-200 rounded text-sm';
            
            const isImage = file.type.startsWith('image/');
            const iconClass = isImage ? 'fa-file-image' : 'fa-file-pdf';
            
            previewElement.innerHTML = `
                <div class="flex items-center space-x-2">
                    <i class="fas ${iconClass} text-gray-500"></i>
                    <span class="font-medium text-gray-700">${file.name}</span>
                    <span class="text-gray-500">(${this.formatFileSize(file.size)})</span>
                </div>
                <button type="button" class="text-red-500 hover:text-red-700" onclick="window.paymentManager.removePaymentProofFile('${paymentMethod}', ${index})">
                    <i class="fas fa-times"></i>
                </button>
            `;

            previewContainer.appendChild(previewElement);
        });
    }

    // Remove payment proof file
    removePaymentProofFile(paymentMethod, fileIndex) {
        this.uploadedPaymentProofs[paymentMethod].splice(fileIndex, 1);
        this.showPaymentProofPreview(this.uploadedPaymentProofs[paymentMethod], paymentMethod);
        
        // Clear input if no files left
        if (this.uploadedPaymentProofs[paymentMethod].length === 0) {
            const input = document.getElementById(`${paymentMethod}ProofUpload`);
            if (input) input.value = '';
        }
    }

    // Format file size
    formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // Attach member search listeners
    attachMemberSearchListeners() {
        const memberSearch = document.getElementById('memberSearch');
        const memberDropdown = document.getElementById('memberDropdown');
        
        if (!memberSearch) return;

        // Debounced search
        const debouncedSearch = debounce(async (query) => {
            if (query.length < 2) {
                memberDropdown.innerHTML = '';
                memberDropdown.classList.add('hidden');
                return;
            }

            try {
                const response = await fetch(`/api/members?search=${query}`, {
                    headers: {
                        'Authorization': `Bearer ${POS_CONFIG.API_TOKEN}`,
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();
                if (data.success && data.data.length > 0) {
                    this.renderMemberDropdown(data.data, memberDropdown);
                } else {
                    memberDropdown.innerHTML = '<div class="p-3 text-gray-500 text-sm">Tidak ada member ditemukan</div>';
                    memberDropdown.classList.remove('hidden');
                }
            } catch (error) {
                console.error('Error searching members:', error);
            }
        }, 300);

        memberSearch.addEventListener('input', (e) => {
            debouncedSearch(e.target.value.trim());
        });
    }

    // Render member dropdown results
    renderMemberDropdown(members, dropdown) {
        dropdown.innerHTML = members.map(member => `
            <div class="member-item p-3 cursor-pointer hover:bg-gray-50 border-b border-gray-100" data-member='${JSON.stringify(member)}'>
                <div class="font-medium text-gray-800">${member.name}</div>
                <div class="text-sm text-gray-500">${member.member_code || 'No Code'}</div>
            </div>
        `).join('');

        // Add click listeners
        dropdown.querySelectorAll('.member-item').forEach(item => {
            item.addEventListener('click', (e) => {
                const member = JSON.parse(e.currentTarget.dataset.member);
                this.selectMember(member);
                dropdown.classList.add('hidden');
            });
        });

        dropdown.classList.remove('hidden');
    }

    // Select member
    selectMember(member) {
        this.cartManager.setMember(member);
        // Re-render member section
        const memberSection = document.querySelector('.relative');
        memberSection.innerHTML = this.renderMemberSection();
        lucide.createIcons();
    }

    // Update payment summary when tax changes
    updatePaymentSummary(totals) {
        const summaryContainer = document.querySelector('.bg-gray-50');
        summaryContainer.innerHTML = `
            <h4 class="font-medium mb-3">Ringkasan Pesanan</h4>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span>Subtotal (${formatQuantity(totals.totalQuantity)} item)</span>
                    <span>${formatCurrency(totals.subtotal)}</span>
                </div>
                <div class="flex justify-between">
                    <span>Diskon</span>
                    <span>-${formatCurrency(totals.totalDiscount)}</span>
                </div>
                <div class="flex justify-between">
                    <span>Pajak (${totals.taxRate}%)</span>
                    <span>${formatCurrency(totals.tax)}</span>
                </div>
                <div class="border-t pt-2 flex justify-between font-semibold">
                    <span>Total</span>
                    <span class="text-green-600">${formatCurrency(totals.grandTotal)}</span>
                </div>
            </div>
        `;
    }

    // Process payment
    async processPayment(totals) {
        const paymentMethod = document.querySelector('input[name="paymentMethod"]:checked')?.value;
        // Get raw value from hidden input, fallback to parsing display value
        const amountReceivedRaw = document.getElementById('amountReceivedRaw')?.value;
        const amountReceived = amountReceivedRaw ? parseInt(amountReceivedRaw) : parseCurrencyInput(document.getElementById('amountReceived')?.value || '0');
        const notes = document.getElementById('paymentNotes')?.value || '';

        // Validation
        if (!paymentMethod) {
            showNotification('Pilih metode pembayaran', 'warning');
            return;
        }

        if (paymentMethod === 'cash' && amountReceived < totals.grandTotal) {
            showNotification('Jumlah uang tidak mencukupi', 'warning');
            return;
        }

        // Semua payment method sekarang wajib upload bukti pembayaran
        const currentProofs = this.uploadedPaymentProofs[paymentMethod];
        if (!currentProofs || currentProofs.length === 0) {
            showNotification('Upload bukti pembayaran terlebih dahulu', 'warning');
            return;
        }

        const loadingOverlay = showLoading('Memproses pembayaran...');

        try {
            const cartData = this.cartManager.getCartData();
            
            // Prepare FormData untuk file upload
            const formData = new FormData();
            
            // Add basic transaction data
            formData.append('outlet_id', outletInfo.id);
            formData.append('shift_id', outletInfo.shift_id);
            formData.append('payment_method', paymentMethod);
            formData.append('notes', notes);
            formData.append('tax', totals.tax || 0);
            formData.append('discount', totals.totalDiscount || 0);
            
            if (this.cartManager.selectedMember?.id) {
                formData.append('member_id', this.cartManager.selectedMember.id);
            }

            // Add payment amounts
            if (paymentMethod === 'cash') {
                formData.append('total_paid', amountReceived);
            } else {
                formData.append('total_paid', totals.grandTotal);
            }

            // Add cart items
            cartData.items.forEach((item, index) => {
                formData.append(`items[${index}][product_id]`, item.product_id);
                formData.append(`items[${index}][quantity]`, item.quantity);
                formData.append(`items[${index}][price]`, item.price);
                formData.append(`items[${index}][discount]`, item.discount || 0);
            });

            // Add payment proof file (wajib untuk semua payment method)
            const paymentProofFile = this.uploadedPaymentProofs[paymentMethod][0]; // Ambil file pertama
            if (paymentProofFile) {
                formData.append('payment_proof', paymentProofFile);
            }

            const response = await fetch('/api/orders', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${POS_CONFIG.API_TOKEN}`,
                    'Accept': 'application/json'
                },
                body: formData
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'Gagal memproses pembayaran');
            }

            if (data.success) {
                this.currentOrder = data.data;
                
                // Semua transaksi sekarang menunggu approval
                showNotification('Transaksi berhasil dibuat! Menunggu persetujuan admin/supervisor.', 'info');
                this.awaitingApproval = true;

                // Show invoice
                this.showInvoice(data.data, totals, paymentMethod);
                
                // Clear cart
                this.cartManager.clear();
                this.cartManager.setMember(null);
                
                closeModal('paymentModal');
            }

        } catch (error) {
            console.error('Payment error:', error);
            showNotification(error.message || 'Gagal memproses pembayaran', 'error');
        } finally {
            hideLoading();
        }
    }

    // Upload payment proofs for any payment method
    async uploadPaymentProofs(files, paymentMethod) {
        if (!files || files.length === 0) return [];

        const formData = new FormData();
        files.forEach((file, index) => {
            formData.append(`payment_proofs[]`, file);
        });
        formData.append('payment_method', paymentMethod);

        const response = await fetch('/api/upload/payment-proofs', {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${POS_CONFIG.API_TOKEN}`
            },
            body: formData
        });

        const data = await response.json();
        if (!response.ok || !data.success) {
            throw new Error(data.message || 'Gagal mengupload bukti pembayaran');
        }

        return data.data.urls || [];
    }

    // Show invoice
    showInvoice(orderData, totals, paymentMethod) {
        const invoiceModal = document.getElementById('invoiceModal');
        if (!invoiceModal) return;

        // Populate invoice with complete data
        this.populateInvoiceData(orderData, totals, paymentMethod);
        openModal('invoiceModal');
    }

    // Populate invoice data
    populateInvoiceData(orderData, totals, paymentMethod) {
        // Update invoice elements with complete information
        document.getElementById('invoiceNumber').textContent = orderData.invoice_number || 'INV-001';
        document.getElementById('orderNumber').textContent = orderData.order_number || 'ORD-001';
        document.getElementById('invoiceDate').textContent = new Date().toLocaleDateString('id-ID');
        
        // Customer info
        const customerInfo = document.getElementById('customerInfo');
        if (this.cartManager.selectedMember) {
            customerInfo.innerHTML = `
                <div class="text-sm">
                    <div><strong>Customer:</strong> ${this.cartManager.selectedMember.name}</div>
                    <div><strong>Kode Member:</strong> ${this.cartManager.selectedMember.member_code || '-'}</div>
                </div>
            `;
        } else {
            customerInfo.innerHTML = '<div class="text-sm text-gray-500">Customer: Umum</div>';
        }

        // Transaction type
        document.getElementById('transactionType').textContent = 
            POS_CONFIG.TRANSACTION_TYPES.find(t => t.id === this.cartManager.transactionType)?.name || 'Reguler';

        // Items list with bonus
        const itemsList = document.getElementById('invoiceItems');
        itemsList.innerHTML = this.cartManager.cart.map((item, index) => `
            <div class="flex justify-between items-center py-2 ${index > 0 ? 'border-t border-gray-100' : ''}">
                <div class="flex-1">
                    <div class="font-medium">${item.name}</div>
                    <div class="text-sm text-gray-500">
                        ${formatQuantity(item.quantity)} x ${formatCurrency(item.price, true)}
                        ${item.discount > 0 ? ` - Diskon ${formatCurrency(item.discount)}` : ''}
                    </div>
                </div>
                <div class="text-right">
                    <div class="font-medium">${formatCurrency(item.subtotal)}</div>
                </div>
            </div>
        `).join('') + 
        // Add bonus items
        (this.cartManager.bonusItems.length > 0 ? 
            this.cartManager.bonusItems.map(bonus => `
                <div class="flex justify-between items-center py-2 border-t border-gray-100 bg-green-50">
                    <div class="flex-1">
                        <div class="font-medium text-green-700">${bonus.name}</div>
                        <div class="text-sm text-green-600">${formatQuantity(bonus.quantity)} x Gratis</div>
                    </div>
                    <div class="text-right">
                        <div class="font-medium text-green-600">Rp 0</div>
                    </div>
                </div>
            `).join('') : '');

        // Totals
        document.getElementById('invoiceSubtotal').textContent = formatCurrency(totals.subtotal);
        document.getElementById('invoiceDiscount').textContent = formatCurrency(totals.totalDiscount);
        document.getElementById('invoiceTax').textContent = `${formatCurrency(totals.tax)} (${totals.taxRate}%)`;
        document.getElementById('invoiceTotal').textContent = formatCurrency(totals.grandTotal);

        // Payment info
        document.getElementById('paymentMethod').textContent = 
            POS_CONFIG.PAYMENT_METHODS.find(m => m.id === paymentMethod)?.name || paymentMethod;

        const paymentDetails = document.getElementById('paymentDetails');
        if (paymentMethod === 'cash') {
            // Get raw value from hidden input, fallback to parsing display value
            const amountReceivedRaw = document.getElementById('amountReceivedRaw')?.value;
            const amountReceived = amountReceivedRaw ? parseInt(amountReceivedRaw) : parseCurrencyInput(document.getElementById('amountReceived').value);
            const change = amountReceived - totals.grandTotal;
            paymentDetails.innerHTML = `
                <div class="text-sm space-y-1">
                    <div class="flex justify-between">
                        <span>Uang Diterima:</span>
                        <span>${formatCurrency(amountReceived)}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Kembalian:</span>
                        <span>${formatCurrency(change)}</span>
                    </div>
                </div>
            `;
        } else if (paymentMethod === 'transfer' && this.awaitingApproval) {
            paymentDetails.innerHTML = `
                <div class="text-sm text-orange-600">
                    <i data-lucide="clock" class="w-4 h-4 inline mr-1"></i>
                    Menunggu konfirmasi supervisor
                </div>
            `;
        } else {
            paymentDetails.innerHTML = '';
        }

        lucide.createIcons();
    }
}