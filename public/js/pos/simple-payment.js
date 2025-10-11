/**
 * Simple Payment Management for POS System using old modal
 */
class SimplePaymentManager {
    constructor(cartManager) {
        this.cartManager = cartManager;
        this.currentOrder = null;
        this.uploadedPaymentProofs = {
            cash: [],
            transfer: [],
            qris: []
        };
        this.selectedMember = null;
        this.selectedMasjid = null;
        this.currentPaymentMethod = 'cash';
        this.transactionCategory = 'lunas';
    }

    // Show payment modal
    showPaymentModal() {
        if (this.cartManager.cart.length === 0) {
            showNotification('Keranjang belanja kosong', 'warning');
            return;
        }

        const totals = this.cartManager.calculateTotals();
        this.setupPaymentModal(totals);
        openModal('paymentModal');
    }

    // Setup payment modal
    setupPaymentModal(totals) {
        // Update total display
        document.getElementById('paymentGrandTotal').textContent = formatCurrency(totals.grandTotal);

        // Update tax type display in modal
        this.updateModalTaxTypeDisplay();

        // Setup payment methods
        this.setupPaymentMethods();

        // Setup event listeners
        this.attachEventListeners(totals);

        // Clear previous uploads
        this.clearAllProofs();

        // Reset form
        document.getElementById('amountReceived').value = '';
        document.getElementById('amountReceivedRaw').value = '';
        document.getElementById('changeAmount').value = '';
        
        // Reset transaction category radio
        document.querySelector('input[name="transactionCategory"][value="lunas"]').checked = true;

        // Set initial transaction category and update sections
        this.transactionCategory = 'lunas';
        this.currentPaymentMethod = 'cash';
        this.updateAmountSections();
        
        // Initialize payment method UI
        this.handlePaymentMethodChange('cash');
    }

    // Update tax type display in modal
    updateModalTaxTypeDisplay() {
        const selectedTaxType = document.querySelector('input[name="transactionTaxType"]:checked')?.value || 'non_pkp';
        const modalTaxTypeDisplay = document.getElementById('modalTaxTypeDisplay');
        
        if (modalTaxTypeDisplay) {
            const taxRate = selectedTaxType === 'pkp' ? 11 : 0;
            const taxTypeName = selectedTaxType === 'pkp' ? 'PKP' : 'Non-PKP';
            
            modalTaxTypeDisplay.textContent = `${taxTypeName} (${taxRate}%)`;
            
            // Color coding
            if (selectedTaxType === 'pkp') {
                modalTaxTypeDisplay.className = 'text-sm px-2 py-1 rounded-full bg-blue-100 text-blue-600';
            } else {
                modalTaxTypeDisplay.className = 'text-sm px-2 py-1 rounded-full bg-green-100 text-green-600';
            }
        }
    }

    // Setup payment methods
    setupPaymentMethods() {
        const paymentMethodsContainer = document.getElementById('paymentMethods');
        const methods = [
            { id: 'cash', name: 'Tunai', icon: 'money-bill' },
            { id: 'transfer', name: 'Transfer Bank', icon: 'university' },
            { id: 'qris', name: 'QRIS', icon: 'qrcode' }
        ];

        paymentMethodsContainer.innerHTML = methods.map(method => `
            <label class="flex items-center p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 mb-2">
                <input type="radio" name="paymentMethod" value="${method.id}" class="mr-3 text-green-600" ${method.id === 'cash' ? 'checked' : ''}>
                <i class="fas fa-${method.icon} text-gray-600 mr-3"></i>
                <span class="font-medium">${method.name}</span>
            </label>
        `).join('');
    }

    // Attach event listeners
    attachEventListeners(totals) {
        // Payment method change
        document.querySelectorAll('input[name="paymentMethod"]').forEach(radio => {
            radio.addEventListener('change', (e) => {
                this.handlePaymentMethodChange(e.target.value);
            });
        });

        // Transaction category change
        document.querySelectorAll('input[name="transactionCategory"]').forEach(radio => {
            radio.addEventListener('change', (e) => {
                this.transactionCategory = e.target.value;
                this.updateAmountSections();
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

        // File upload listeners
        this.attachFileUploadListeners();

        // Member search
        this.attachMemberSearchListeners();

        // Masjid search
        this.attachMasjidSearchListeners();

        // Process payment button
        const processBtn = document.getElementById('btnProcessPayment');
        if (processBtn) {
            processBtn.onclick = () => this.processPayment(totals);
        }
    }

    // Handle payment method change
    handlePaymentMethodChange(method) {
        this.currentPaymentMethod = method;

        // Hide all sections first
        document.getElementById('qrisDetails').classList.add('hidden');
        document.getElementById('transferDetails').classList.add('hidden');
        document.getElementById('uploadProofSection').classList.add('hidden');

        // Hide all upload sections
        document.getElementById('cashProofUploadSection').classList.add('hidden');
        document.getElementById('transferProofUploadSection').classList.add('hidden');
        document.getElementById('qrisProofUploadSection').classList.add('hidden');

        // Show relevant sections
        switch (method) {
            case 'cash':
                document.getElementById('uploadProofSection').classList.remove('hidden');
                document.getElementById('cashProofUploadSection').classList.remove('hidden');
                break;
            case 'transfer':
                document.getElementById('transferDetails').classList.remove('hidden');
                document.getElementById('uploadProofSection').classList.remove('hidden');
                document.getElementById('transferProofUploadSection').classList.remove('hidden');
                this.loadBankInfo();
                break;
            case 'qris':
                document.getElementById('qrisDetails').classList.remove('hidden');
                document.getElementById('uploadProofSection').classList.remove('hidden');
                document.getElementById('qrisProofUploadSection').classList.remove('hidden');
                this.loadQRISInfo();
                break;
        }

        // Update amount sections based on current category and method
        this.updateAmountSections();
    }

    // Update amount received and change sections based on transaction category and payment method
    updateAmountSections() {
        const amountSection = document.getElementById('amountReceivedSection');
        const changeSection = document.getElementById('changeSection');
        const amountLabel = document.getElementById('amountReceivedLabel');

        if (!amountSection || !changeSection || !amountLabel) return;

        if (this.transactionCategory === 'dp') {
            // For DP: Always show amount received field, hide change field
            amountSection.style.display = 'block';
            changeSection.style.display = 'none';
            amountLabel.textContent = 'Jumlah DP Diterima';
        } else {
            // For Lunas: Show based on payment method
            if (this.currentPaymentMethod === 'cash') {
                // Cash payment: show both amount and change
                amountSection.style.display = 'block';
                changeSection.style.display = 'block';
                amountLabel.textContent = 'Jumlah Uang Diterima';
            } else {
                // Non-cash payment: hide both
                amountSection.style.display = 'none';
                changeSection.style.display = 'none';
            }
        }
    }

    // Load bank info based on selected tax type
    loadBankInfo() {
        // Get selected tax type from cart
        const selectedTaxType = document.querySelector('input[name="transactionTaxType"]:checked')?.value || 'non_pkp';
        
        let bankingInfo;
        if (selectedTaxType === 'pkp' && outletInfo.pkp_banking) {
            bankingInfo = {
                atas_nama: outletInfo.pkp_banking.atas_nama,
                bank: outletInfo.pkp_banking.bank,
                nomor: outletInfo.pkp_banking.nomor,
                tax_type: 'PKP',
                tax_rate: 11
            };
        } else if (selectedTaxType === 'non_pkp' && outletInfo.non_pkp_banking) {
            bankingInfo = {
                atas_nama: outletInfo.non_pkp_banking.atas_nama,
                bank: outletInfo.non_pkp_banking.bank,
                nomor: outletInfo.non_pkp_banking.nomor,
                tax_type: 'Non-PKP',
                tax_rate: 0
            };
        } else {
            // Fallback to default banking info
            bankingInfo = {
                atas_nama: outletInfo.bank_account?.atas_nama || '-',
                bank: outletInfo.bank_account?.bank || '-',
                nomor: outletInfo.bank_account?.nomor || '-',
                tax_type: selectedTaxType === 'pkp' ? 'PKP' : 'Non-PKP',
                tax_rate: selectedTaxType === 'pkp' ? 11 : 0
            };
        }
        
        // Update banking info display
        document.getElementById('bankAccountName').textContent = bankingInfo.atas_nama;
        document.getElementById('bankName').textContent = bankingInfo.bank;
        document.getElementById('bankAccountNumber').textContent = bankingInfo.nomor;
        
        // Update tax type indicator
        const taxTypeIndicator = document.getElementById('bankTaxTypeIndicator');
        const taxInfoText = document.getElementById('taxInfoText');
        
        if (taxTypeIndicator && taxInfoText) {
            taxTypeIndicator.textContent = `${bankingInfo.tax_type} (${bankingInfo.tax_rate}%)`;
            
            // Color coding for tax type
            if (bankingInfo.tax_type === 'PKP') {
                taxTypeIndicator.className = 'text-xs px-2 py-1 rounded-full bg-blue-100 text-blue-600';
                taxInfoText.innerHTML = `
                    <i class="fas fa-info-circle mr-1"></i>
                    Transfer ke rekening PKP (Pajak ${bankingInfo.tax_rate}%) - Pastikan untuk mencantumkan keterangan pajak
                `;
            } else {
                taxTypeIndicator.className = 'text-xs px-2 py-1 rounded-full bg-green-100 text-green-600';
                taxInfoText.innerHTML = `
                    <i class="fas fa-info-circle mr-1"></i>
                    Transfer ke rekening Non-PKP (${bankingInfo.tax_rate}% pajak) - Transaksi bebas pajak
                `;
            }
        }
    }

    // Load QRIS info
    loadQRISInfo() {
        const container = document.getElementById('qrisImageContainer');
        if (outletInfo.qris) {
            container.innerHTML = `<img src="${outletInfo.qris}" alt="QRIS" class="w-48 h-48 mx-auto">`;
        } else {
            container.innerHTML = `
                <div class="w-48 h-48 mx-auto bg-gray-100 rounded flex items-center justify-center">
                    <div class="text-gray-400 text-center">
                        <i class="fas fa-qrcode text-4xl mb-2"></i>
                        <p>QR Code tidak tersedia</p>
                    </div>
                </div>
            `;
        }
    }

    // Attach file upload listeners
    attachFileUploadListeners() {
        ['cash', 'transfer', 'qris'].forEach(method => {
            const input = document.getElementById(`${method}ProofUpload`);
            if (input) {
                input.addEventListener('change', (e) => {
                    this.handlePaymentProofUpload(e, method);
                });
            }
        });
    }

    // Handle payment proof upload
    handlePaymentProofUpload(event, paymentMethod) {
        const files = event.target.files;
        if (!files || files.length === 0) return;

        const validFiles = [];

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
            this.uploadedPaymentProofs[paymentMethod] = validFiles;
            this.showPaymentProofPreview(validFiles, paymentMethod);
            showNotification(`${validFiles.length} file berhasil diupload`, 'success');
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
                <button type="button" class="text-red-500 hover:text-red-700" onclick="window.simplePaymentManager.removePaymentProofFile('${paymentMethod}', ${index})">
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

    // Clear all proofs
    clearAllProofs() {
        this.uploadedPaymentProofs = { cash: [], transfer: [], qris: [] };
        ['cash', 'transfer', 'qris'].forEach(method => {
            const preview = document.getElementById(`${method}ProofPreview`);
            if (preview) preview.innerHTML = '';
            const input = document.getElementById(`${method}ProofUpload`);
            if (input) input.value = '';
        });
    }

    // Attach member search listeners
    attachMemberSearchListeners() {
        const memberSearch = document.getElementById('memberSearch');
        const memberDropdown = document.getElementById('memberDropdownList');
        const memberResults = document.getElementById('memberResults');

        if (!memberSearch) return;

        const debouncedSearch = debounce(async (query) => {
            if (query.length < 2) {
                memberDropdown.classList.add('hidden');
                return;
            }

            try {
                const response = await fetch(`/api/members/search?q=${encodeURIComponent(query)}`, {
                    headers: {
                        'Authorization': `Bearer ${POS_CONFIG.API_TOKEN}`,
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();
                if (data.success && data.data.length > 0) {
                    this.renderMemberResults(data.data, memberResults);
                    memberDropdown.classList.remove('hidden');
                } else {
                    memberResults.innerHTML = '<div class="p-3 text-gray-500 text-sm">Tidak ada member atau leads ditemukan</div>';
                    memberDropdown.classList.remove('hidden');
                }
            } catch (error) {
                console.error('Error searching members:', error);
                memberResults.innerHTML = '<div class="p-3 text-red-500 text-sm">Error pencarian</div>';
                memberDropdown.classList.remove('hidden');
            }
        }, 300);

        memberSearch.addEventListener('input', (e) => {
            debouncedSearch(e.target.value.trim());
        });
    }

    // Render member results
    renderMemberResults(members, container) {
        container.innerHTML = members.map(member => {
            const isLead = member.type === 'lead';
            const badge = isLead ? '<span class="text-xs bg-blue-100 text-blue-600 px-2 py-1 rounded ml-2">Lead</span>' : '';
            
            return `
                <div class="member-item" onclick="window.simplePaymentManager.selectMember(${JSON.stringify(member).replace(/"/g, '&quot;')})">
                    <div class="flex justify-between items-center">
                        <div>
                            <div class="font-medium text-gray-800">${member.name}${badge}</div>
                            <div class="text-sm text-gray-500">${member.phone}</div>
                        </div>
                    </div>
                </div>
            `;
        }).join('');
    }

    // Select member
    selectMember(member) {
        this.selectedMember = member;
        document.getElementById('memberName').textContent = member.name;
        document.getElementById('memberCode').textContent = member.phone || member.identifier || member.member_code || 'No Code';
        document.getElementById('selectedMember').classList.remove('hidden');
        document.getElementById('memberSearch').value = '';
        document.getElementById('memberDropdownList').classList.add('hidden');

        // Remove member button
        document.getElementById('removeMember').onclick = () => {
            this.selectedMember = null;
            document.getElementById('selectedMember').classList.add('hidden');
        };
    }

    // Attach masjid search listeners
    attachMasjidSearchListeners() {
        const masjidSearch = document.getElementById('masjidSearchPayment');
        const masjidDropdown = document.getElementById('masjidDropdownListPayment');
        const masjidResults = document.getElementById('masjidResultsPayment');

        if (!masjidSearch) return;

        const debouncedSearch = debounce(async (query) => {
            if (query.length < 2) {
                masjidDropdown.classList.add('hidden');
                return;
            }

            try {
                const response = await fetch(`/api/mosques?search=${query}`, {
                    headers: {
                        'Authorization': `Bearer ${POS_CONFIG.API_TOKEN}`,
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();
                if (data.success && data.data.length > 0) {
                    this.renderMasjidResults(data.data, masjidResults);
                    masjidDropdown.classList.remove('hidden');
                } else {
                    masjidResults.innerHTML = '<div class="p-3 text-gray-500 text-sm">Tidak ada masjid ditemukan</div>';
                    masjidDropdown.classList.remove('hidden');
                }
            } catch (error) {
                console.error('Error searching mosques:', error);
            }
        }, 300);

        masjidSearch.addEventListener('input', (e) => {
            debouncedSearch(e.target.value.trim());
        });

        // Hide dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.masjid-dropdown-container')) {
                masjidDropdown.classList.add('hidden');
            }
        });
    }

    // Render masjid results
    renderMasjidResults(mosques, container) {
        container.innerHTML = mosques.map(mosque => `
            <div class="member-item" onclick="window.simplePaymentManager.selectMasjid(${JSON.stringify(mosque).replace(/"/g, '&quot;')})">
                <div class="font-medium text-gray-800">${mosque.name}</div>
                <div class="text-sm text-gray-500">${mosque.address}</div>
            </div>
        `).join('');
    }

    // Select masjid
    selectMasjid(mosque) {
        this.selectedMasjid = mosque;
        document.getElementById('masjidNamePayment').textContent = mosque.name;
        document.getElementById('masjidAddressPayment').textContent = mosque.address;
        document.getElementById('selectedMasjidPayment').classList.remove('hidden');
        document.getElementById('masjidSearchPayment').value = '';
        document.getElementById('masjidDropdownListPayment').classList.add('hidden');

        // Remove masjid button
        document.getElementById('removeMasjidPayment').onclick = () => {
            this.selectedMasjid = null;
            document.getElementById('selectedMasjidPayment').classList.add('hidden');
        };
    }

    // Process payment
    async processPayment(totals) {
    const formData = new FormData();

    const paymentMethod = this.currentPaymentMethod;
    // Get raw value from hidden input, fallback to parsing display value
    const amountReceivedRaw = document.getElementById('amountReceivedRaw')?.value;
    const amountReceived = amountReceivedRaw ? parseInt(amountReceivedRaw) : parseCurrencyInput(document.getElementById('amountReceived')?.value || '0');

    // Validation
    if (this.transactionCategory === 'lunas') {
        if (paymentMethod === 'cash' && amountReceived < totals.grandTotal) {
            showNotification('Jumlah uang tidak mencukupi', 'warning');
            return;
        }
    } else if (this.transactionCategory === 'dp') {
        // Untuk DP: validasi input amount untuk semua metode pembayaran
        if (amountReceived <= 0) {
            showNotification('Masukkan jumlah DP yang valid', 'warning');
            return;
        }
        if (amountReceived > totals.grandTotal) {
            showNotification('Jumlah DP tidak boleh melebihi total pembayaran', 'warning');
            return;
        }
    }

    if (paymentMethod === 'transfer' && this.uploadedPaymentProofs.transfer.length === 0) {
        showNotification('Upload bukti transfer terlebih dahulu', 'warning');
        return;
    }

    const loadingOverlay = showLoading('Memproses pembayaran...');

    try {
        const cartData = this.cartManager.getCartData();
        console.log('Cart data with service:', cartData); // Debug log
        
        // Prepare transaction data as FormData
        formData.append('outlet_id', outletInfo.id);
        formData.append('shift_id', outletInfo.shift_id);
        formData.append('items', JSON.stringify(cartData.items));
        formData.append('bonus_items', JSON.stringify(cartData.bonus_items));
        
        // Add order_id to bonus items for tracking
        if (cartData.bonus_items.length > 0) {
            // Will be filled after order creation
            formData.append('should_link_bonus_to_order', 'true');
        }
        formData.append('payment_method', paymentMethod);
        formData.append('transaction_category', this.transactionCategory);
        // Get selected tax type from cart
        const selectedTaxType = document.querySelector('input[name="transactionTaxType"]:checked')?.value || 'non_pkp';
        
        formData.append('tax', totals.tax);
        formData.append('tax_type', selectedTaxType);
        formData.append('discount', totals.totalDiscount);
        // Handle member or lead
        if (this.selectedMember) {
            if (this.selectedMember.type === 'lead') {
                // Send lead data for member creation during transaction
                // FormData akan otomatis serialize object menjadi format yang benar
                Object.keys(this.selectedMember.lead_data).forEach(key => {
                    formData.append(`lead_data[${key}]`, this.selectedMember.lead_data[key]);
                });
            } else {
                // Regular member
                formData.append('member_id', this.selectedMember.id);
            }
        }
        formData.append('mosque_id', this.selectedMasjid?.id || '');
        formData.append('transaction_type', this.cartManager.transactionType);
        formData.append('order_number', generateOrderNumber());
        formData.append('invoice_number', generateInvoiceNumber(outletInfo.name.toUpperCase()));
        formData.append('total', totals.grandTotal);
        
        // Add carpet service data
        if (cartData.service_type) {
            formData.append('service_type', cartData.service_type);
        }
        if (cartData.installation_date) {
            formData.append('installation_date', cartData.installation_date);
        }
        if (cartData.installation_notes) {
            formData.append('installation_notes', cartData.installation_notes);
        }
        if (cartData.leads_cabang_outlet_id) {
            formData.append('leads_cabang_outlet_id', cartData.leads_cabang_outlet_id);
        }
        if (cartData.deal_maker_outlet_id) {
            formData.append('deal_maker_outlet_id', cartData.deal_maker_outlet_id);
        }

        // Handle payment amounts
        if (this.transactionCategory === 'dp') {
            // Untuk DP: selalu gunakan jumlah yang diinput user
            formData.append('total_paid', amountReceived);
            formData.append('remaining_balance', totals.grandTotal - amountReceived);
            formData.append('change', '0');
            formData.append('status', 'partial');
        } else {
            // Untuk Lunas: logika berbeda berdasarkan metode pembayaran
            if (paymentMethod === 'cash') {
                formData.append('total_paid', amountReceived);
                formData.append('change', amountReceived - totals.grandTotal);
                formData.append('remaining_balance', '0');
            } else {
                // Non-cash lunas = bayar penuh
                formData.append('total_paid', totals.grandTotal);
                formData.append('change', '0');
                formData.append('remaining_balance', '0');
            }
        }

        // Handle payment proofs
        const paymentProofs = this.uploadedPaymentProofs[`${paymentMethod}`];
        if (paymentProofs && paymentProofs.length > 0) {
            // Append the actual File object to FormData
            formData.append('payment_proof', paymentProofs[0]);
            
            if (paymentMethod === 'transfer') {
                formData.append('status', 'pending_approval');
            }
        }

        // Handle contract PDF for DP transactions
        if (this.transactionCategory === 'dp') {
            const contractFile = document.getElementById('akadJualBeliUpload')?.files[0];
            if (contractFile) {
                formData.append('contract_pdf', contractFile);
            }
        }

        const response = await fetch('/api/orders', {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${POS_CONFIG.API_TOKEN}`,
                'Accept': 'application/json'
            },
            // Remove Content-Type header - let browser set it with boundary
            body: formData
        });

        const data = await response.json();

        console.log("Proses payment", data)

        if (!response.ok) {
            throw new Error(data.message || 'Gagal memproses pembayaran');
        }

        if (data.success) {
            this.currentOrder = data.data;
            
            if (paymentMethod === 'transfer') {
                const categoryText = this.transactionCategory === 'dp' ? 'DP' : 'Pembayaran';
                showNotification(`${categoryText} berhasil! Menunggu konfirmasi supervisor.`, 'info');
            } else {
                if (this.transactionCategory === 'dp') {
                    const remaining = formatCurrency(totals.grandTotal - amountReceived);
                    showNotification(`DP berhasil diproses! Sisa pembayaran: ${remaining}`, 'success');
                } else {
                    showNotification('Pembayaran berhasil!', 'success');
                }
            }

            // Clear cart and close modal
            this.cartManager.clear();
            this.cartManager.setMember(null);
            this.selectedMember = null;
            this.selectedMasjid = null;
            closeModal('paymentModal');
            
            // Reload products to get updated stock quantities
            if (typeof loadProducts === 'function') {
                loadProducts();
            }
            
            // Refresh transaction history data
            if (typeof ambilDataTransaksi === 'function') {
                const hariIni = new Date();
                ambilDataTransaksi(hariIni, hariIni);
            }
        }

    } catch (error) {
        console.error('Payment error:', error);
        showNotification(error.message || 'Gagal memproses pembayaran', 'error');
    } finally {
        hideLoading();
    }
}

    // Upload payment proofs
    async uploadPaymentProofs(files, paymentMethod) {
        if (!files || files.length === 0) return [];

        const formData = new FormData();
        files.forEach((file) => {
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
}