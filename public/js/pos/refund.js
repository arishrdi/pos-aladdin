/**
 * Refund/Cancel Management untuk POS System
 */
class RefundManager {
    constructor() {
        this.pendingRefunds = [];
    }

    // Show refund modal
    showRefundModal(orderId = null) {
        const modal = this.createRefundModal(orderId);
        document.body.appendChild(modal);
        
        if (orderId) {
            this.loadOrderForRefund(orderId);
        }
    }

    // Create refund modal
    createRefundModal(orderId) {
        const modal = document.createElement('div');
        modal.id = 'refundModal';
        modal.className = 'fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50';
        
        modal.innerHTML = `
            <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold text-gray-900">
                            ${orderId ? 'Refund Transaksi' : 'Cari Transaksi untuk Refund'}
                        </h3>
                        <button onclick="this.closest('#refundModal').remove()" class="text-gray-400 hover:text-gray-600">
                            <i data-lucide="x" class="w-5 h-5"></i>
                        </button>
                    </div>

                    ${!orderId ? this.renderOrderSearch() : ''}
                    
                    <div id="refundContent">
                        ${orderId ? this.renderRefundForm(orderId) : ''}
                    </div>
                </div>
            </div>
        `;

        this.attachRefundEventListeners(modal);
        lucide.createIcons();
        return modal;
    }

    // Render order search
    renderOrderSearch() {
        return `
            <div class="space-y-4 mb-6">
                <div class="flex space-x-3">
                    <input type="text" id="orderSearchInput" 
                        class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                        placeholder="Masukkan nomor order, invoice, atau nama customer...">
                    <button id="searchOrderBtn" 
                        class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600">
                        <i data-lucide="search" class="w-4 h-4"></i>
                    </button>
                </div>
                
                <div id="orderSearchResults" class="space-y-2 max-h-60 overflow-y-auto">
                    <!-- Search results will appear here -->
                </div>
            </div>
        `;
    }

    // Render refund form
    renderRefundForm(orderId) {
        return `
            <div id="orderDetails" class="space-y-6">
                <!-- Order info will be loaded here -->
            </div>
            
            <div id="refundForm" class="space-y-4 mt-6 pt-6 border-t">
                <h4 class="font-medium text-gray-900">Informasi Refund</h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Refund</label>
                        <select id="refundType" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                            <option value="partial">Sebagian</option>
                            <option value="full">Seluruh Transaksi</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Alasan Refund</label>
                        <select id="refundReason" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                            <option value="customer_request">Permintaan Customer</option>
                            <option value="product_defect">Produk Rusak</option>
                            <option value="wrong_item">Salah Barang</option>
                            <option value="system_error">Error System</option>
                            <option value="other">Lainnya</option>
                        </select>
                    </div>
                </div>
                
                <div id="itemSelection" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Item untuk Refund</label>
                    <div id="refundItems" class="space-y-2 max-h-40 overflow-y-auto border border-gray-200 rounded-md p-3">
                        <!-- Refund items will be listed here -->
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Tambahan</label>
                    <textarea id="refundNotes" rows="3" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                        placeholder="Jelaskan alasan refund secara detail..."></textarea>
                </div>
                
                <div class="bg-yellow-50 border border-yellow-200 rounded-md p-3">
                    <div class="flex items-start">
                        <i data-lucide="alert-triangle" class="w-5 h-5 text-yellow-600 mt-0.5 mr-2"></i>
                        <div class="text-sm text-yellow-800">
                            <p class="font-medium">Perhatian:</p>
                            <p>Refund memerlukan persetujuan dari supervisor. Customer akan menerima notifikasi setelah refund disetujui.</p>
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="document.getElementById('refundModal').remove()" 
                        class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="button" id="submitRefundBtn"
                        class="px-6 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 transition-colors">
                        Ajukan Refund
                    </button>
                </div>
            </div>
        `;
    }

    // Attach event listeners
    attachRefundEventListeners(modal) {
        // Search orders
        const searchBtn = modal.querySelector('#searchOrderBtn');
        if (searchBtn) {
            searchBtn.addEventListener('click', () => this.searchOrders(modal));
        }

        // Search on enter
        const searchInput = modal.querySelector('#orderSearchInput');
        if (searchInput) {
            searchInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') this.searchOrders(modal);
            });
        }

        // Refund type change
        const refundType = modal.querySelector('#refundType');
        if (refundType) {
            refundType.addEventListener('change', (e) => {
                this.toggleItemSelection(modal, e.target.value === 'partial');
            });
        }

        // Submit refund
        const submitBtn = modal.querySelector('#submitRefundBtn');
        if (submitBtn) {
            submitBtn.addEventListener('click', () => this.submitRefund(modal));
        }
    }

    // Search orders
    async searchOrders(modal) {
        const query = modal.querySelector('#orderSearchInput').value.trim();
        if (!query) {
            showNotification('Masukkan kata kunci pencarian', 'warning');
            return;
        }

        const resultsContainer = modal.querySelector('#orderSearchResults');
        resultsContainer.innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin"></i> Mencari...</div>';

        try {
            const response = await fetch(`/api/orders/search?q=${encodeURIComponent(query)}`, {
                headers: {
                    'Authorization': `Bearer ${POS_CONFIG.API_TOKEN}`,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();
            
            if (data.success && data.data.length > 0) {
                this.renderOrderSearchResults(resultsContainer, data.data, modal);
            } else {
                resultsContainer.innerHTML = '<div class="text-center py-4 text-gray-500">Tidak ada transaksi ditemukan</div>';
            }
        } catch (error) {
            console.error('Search error:', error);
            resultsContainer.innerHTML = '<div class="text-center py-4 text-red-500">Terjadi kesalahan saat mencari</div>';
        }
    }

    // Render search results
    renderOrderSearchResults(container, orders, modal) {
        container.innerHTML = orders.map(order => `
            <div class="order-result border border-gray-200 rounded-md p-3 cursor-pointer hover:bg-gray-50"
                 data-order-id="${order.id}">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <div class="flex items-center space-x-2 mb-1">
                            <span class="font-medium">${order.invoice_number || order.order_number}</span>
                            <span class="px-2 py-1 text-xs rounded-full ${this.getStatusBadgeClass(order.status)}">
                                ${this.getStatusText(order.status)}
                            </span>
                        </div>
                        <div class="text-sm text-gray-600">
                            <div>Tanggal: ${new Date(order.created_at).toLocaleDateString('id-ID')}</div>
                            <div>Customer: ${order.member?.name || 'Umum'}</div>
                            <div>Total: ${formatCurrency(order.total)}</div>
                        </div>
                    </div>
                    <button class="text-green-600 hover:text-green-800">
                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>
        `).join('');

        // Add click listeners
        container.querySelectorAll('.order-result').forEach(result => {
            result.addEventListener('click', () => {
                const orderId = result.dataset.orderId;
                this.loadOrderForRefund(orderId, modal);
            });
        });
    }

    // Get status badge class
    getStatusBadgeClass(status) {
        const classes = {
            completed: 'bg-green-100 text-green-800',
            pending: 'bg-yellow-100 text-yellow-800',
            cancelled: 'bg-red-100 text-red-800'
        };
        return classes[status] || 'bg-gray-100 text-gray-800';
    }

    // Get status text
    getStatusText(status) {
        const texts = {
            completed: 'Selesai',
            pending: 'Pending',
            cancelled: 'Dibatalkan'
        };
        return texts[status] || status;
    }

    // Load order for refund
    async loadOrderForRefund(orderId, modal = null) {
        if (!modal) {
            modal = document.getElementById('refundModal');
        }

        const loadingOverlay = showLoading('Memuat detail transaksi...');

        try {
            const response = await fetch(`/api/orders/${orderId}/details`, {
                headers: {
                    'Authorization': `Bearer ${POS_CONFIG.API_TOKEN}`,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();
            
            if (data.success) {
                this.renderOrderDetails(modal, data.data);
                modal.querySelector('#refundContent').innerHTML = this.renderRefundForm(orderId);
                this.attachRefundEventListeners(modal);
                lucide.createIcons();
            } else {
                throw new Error(data.message || 'Gagal memuat detail transaksi');
            }
        } catch (error) {
            console.error('Load order error:', error);
            showNotification(error.message || 'Gagal memuat detail transaksi', 'error');
        } finally {
            hideLoading();
        }
    }

    // Render order details
    renderOrderDetails(modal, order) {
        const orderDetails = modal.querySelector('#orderDetails') || modal.querySelector('#refundContent');
        
        orderDetails.innerHTML = `
            <div class="bg-gray-50 rounded-lg p-4">
                <h4 class="font-medium text-gray-900 mb-3">Detail Transaksi</h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div class="space-y-2">
                        <div class="text-sm"><strong>No. Invoice:</strong> ${order.invoice_number}</div>
                        <div class="text-sm"><strong>No. Order:</strong> ${order.order_number}</div>
                        <div class="text-sm"><strong>Tanggal:</strong> ${new Date(order.created_at).toLocaleDateString('id-ID')}</div>
                    </div>
                    <div class="space-y-2">
                        <div class="text-sm"><strong>Customer:</strong> ${order.member?.name || 'Umum'}</div>
                        <div class="text-sm"><strong>Kasir:</strong> ${order.user?.name || '-'}</div>
                        <div class="text-sm"><strong>Status:</strong> 
                            <span class="px-2 py-1 text-xs rounded-full ${this.getStatusBadgeClass(order.status)}">
                                ${this.getStatusText(order.status)}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="border-t pt-3">
                    <h5 class="font-medium mb-2">Items:</h5>
                    <div class="space-y-2">
                        ${order.items.map((item, index) => `
                            <div class="flex justify-between items-center py-2 ${index > 0 ? 'border-t border-gray-200' : ''}">
                                <div class="flex-1">
                                    <div class="font-medium">${item.product?.name || 'Produk Tidak Dikenal'}</div>
                                    <div class="text-sm text-gray-500">
                                        ${formatQuantity(item.quantity)} x ${formatCurrency(item.price, true)}
                                        ${item.discount > 0 ? ` - Diskon ${formatCurrency(item.discount)}` : ''}
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="font-medium">${formatCurrency(item.subtotal || (item.price * item.quantity - (item.discount || 0)))}</div>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                    
                    <div class="border-t pt-3 mt-3">
                        <div class="flex justify-between text-sm">
                            <span>Subtotal:</span>
                            <span>${formatCurrency(order.subtotal)}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span>Diskon:</span>
                            <span>-${formatCurrency(order.discount)}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span>Pajak:</span>
                            <span>${formatCurrency(order.tax)}</span>
                        </div>
                        <div class="flex justify-between font-semibold text-lg border-t pt-2 mt-2">
                            <span>Total:</span>
                            <span class="text-green-600">${formatCurrency(order.total)}</span>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Store order data for later use
        modal.dataset.orderData = JSON.stringify(order);
    }

    // Toggle item selection
    toggleItemSelection(modal, show) {
        const itemSelection = modal.querySelector('#itemSelection');
        if (!itemSelection) return;

        if (show) {
            itemSelection.classList.remove('hidden');
            this.renderRefundItems(modal);
        } else {
            itemSelection.classList.add('hidden');
        }
    }

    // Render refund items
    renderRefundItems(modal) {
        const orderData = JSON.parse(modal.dataset.orderData || '{}');
        if (!orderData.items) return;

        const refundItemsContainer = modal.querySelector('#refundItems');
        
        refundItemsContainer.innerHTML = orderData.items.map((item, index) => `
            <label class="flex items-center justify-between p-2 border border-gray-200 rounded">
                <div class="flex items-center">
                    <input type="checkbox" class="refund-item-checkbox mr-3" 
                           data-item-index="${index}" value="${item.id}">
                    <div class="flex-1">
                        <div class="font-medium">${item.product?.name || 'Produk Tidak Dikenal'}</div>
                        <div class="text-sm text-gray-500">
                            ${formatQuantity(item.quantity)} x ${formatCurrency(item.price, true)}
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <input type="number" step="0.1" min="0" max="${item.quantity}" 
                           class="refund-qty-input w-20 px-2 py-1 border border-gray-300 rounded text-sm disabled:bg-gray-100"
                           placeholder="Qty" disabled>
                    <div class="text-sm font-medium w-20 text-right">
                        ${formatCurrency(item.subtotal || (item.price * item.quantity - (item.discount || 0)))}
                    </div>
                </div>
            </label>
        `).join('');

        // Add event listeners
        refundItemsContainer.querySelectorAll('.refund-item-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', (e) => {
                const qtyInput = e.target.closest('label').querySelector('.refund-qty-input');
                qtyInput.disabled = !e.target.checked;
                
                if (e.target.checked) {
                    const itemIndex = e.target.dataset.itemIndex;
                    qtyInput.value = formatQuantity(orderData.items[itemIndex].quantity);
                } else {
                    qtyInput.value = '';
                }
            });
        });
    }

    // Submit refund
    async submitRefund(modal) {
        const orderData = JSON.parse(modal.dataset.orderData || '{}');
        const refundType = modal.querySelector('#refundType').value;
        const refundReason = modal.querySelector('#refundReason').value;
        const refundNotes = modal.querySelector('#refundNotes').value;

        // Validation
        if (!refundReason) {
            showNotification('Pilih alasan refund', 'warning');
            return;
        }

        if (!refundNotes.trim()) {
            showNotification('Masukkan catatan refund', 'warning');
            return;
        }

        let refundItems = [];
        
        if (refundType === 'partial') {
            const selectedItems = modal.querySelectorAll('.refund-item-checkbox:checked');
            if (selectedItems.length === 0) {
                showNotification('Pilih minimal satu item untuk refund', 'warning');
                return;
            }

            refundItems = Array.from(selectedItems).map(checkbox => {
                const itemIndex = checkbox.dataset.itemIndex;
                const qtyInput = checkbox.closest('label').querySelector('.refund-qty-input');
                const originalItem = orderData.items[itemIndex];
                const refundQty = parseQuantity(qtyInput.value);

                return {
                    order_item_id: originalItem.id,
                    product_id: originalItem.product_id,
                    quantity: refundQty,
                    price: originalItem.price,
                    subtotal: originalItem.price * refundQty - (originalItem.discount * (refundQty / originalItem.quantity))
                };
            });
        }

        const loadingOverlay = showLoading('Mengajukan refund...');

        try {
            const refundData = {
                order_id: orderData.id,
                type: refundType,
                reason: refundReason,
                notes: refundNotes,
                items: refundItems,
                requested_by: 'kasir', // or get from current user
                status: 'pending_approval'
            };

            const response = await fetch('/api/refunds', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${POS_CONFIG.API_TOKEN}`,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(refundData)
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'Gagal mengajukan refund');
            }

            if (data.success) {
                showNotification('Refund berhasil diajukan! Menunggu persetujuan supervisor.', 'success');
                modal.remove();
                
                // Refresh transactions list if exists
                if (window.refreshTransactionHistory) {
                    window.refreshTransactionHistory();
                }
            }

        } catch (error) {
            console.error('Refund error:', error);
            showNotification(error.message || 'Gagal mengajukan refund', 'error');
        } finally {
            hideLoading();
        }
    }

    // Show refund management modal for supervisor
    showRefundManagementModal() {
        const modal = document.createElement('div');
        modal.id = 'refundManagementModal';
        modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
        
        modal.innerHTML = `
            <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold text-gray-900">Manajemen Refund</h3>
                        <button onclick="this.remove()" class="text-gray-400 hover:text-gray-600">
                            <i data-lucide="x" class="w-5 h-5"></i>
                        </button>
                    </div>

                    <div class="mb-4">
                        <div class="flex space-x-3">
                            <select id="refundStatusFilter" class="px-3 py-2 border border-gray-300 rounded-md">
                                <option value="">Semua Status</option>
                                <option value="pending_approval">Menunggu Persetujuan</option>
                                <option value="approved">Disetujui</option>
                                <option value="rejected">Ditolak</option>
                            </select>
                            <button id="loadRefundsBtn" class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600">
                                Muat Data
                            </button>
                        </div>
                    </div>

                    <div id="refundsList" class="space-y-4">
                        <!-- Refunds will be loaded here -->
                    </div>
                </div>
            </div>
        `;

        document.body.appendChild(modal);
        
        // Attach event listeners
        modal.querySelector('#loadRefundsBtn').addEventListener('click', () => {
            this.loadRefundsForApproval(modal);
        });

        // Load initial data
        this.loadRefundsForApproval(modal);
        
        lucide.createIcons();
        return modal;
    }

    // Load refunds for approval
    async loadRefundsForApproval(modal) {
        const status = modal.querySelector('#refundStatusFilter').value;
        const refundsList = modal.querySelector('#refundsList');
        
        refundsList.innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin"></i> Memuat data...</div>';

        try {
            const url = `/api/refunds${status ? `?status=${status}` : ''}`;
            const response = await fetch(url, {
                headers: {
                    'Authorization': `Bearer ${POS_CONFIG.API_TOKEN}`,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();
            
            if (data.success && data.data.length > 0) {
                this.renderRefundsList(refundsList, data.data);
            } else {
                refundsList.innerHTML = '<div class="text-center py-8 text-gray-500">Tidak ada data refund</div>';
            }
        } catch (error) {
            console.error('Load refunds error:', error);
            refundsList.innerHTML = '<div class="text-center py-4 text-red-500">Terjadi kesalahan saat memuat data</div>';
        }
    }

    // Render refunds list
    renderRefundsList(container, refunds) {
        container.innerHTML = refunds.map(refund => `
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex justify-between items-start mb-3">
                    <div class="flex-1">
                        <div class="flex items-center space-x-2 mb-1">
                            <span class="font-medium">Refund #${refund.id}</span>
                            <span class="px-2 py-1 text-xs rounded-full ${this.getRefundStatusBadgeClass(refund.status)}">
                                ${this.getRefundStatusText(refund.status)}
                            </span>
                        </div>
                        <div class="text-sm text-gray-600 space-y-1">
                            <div>Order: ${refund.order?.invoice_number || refund.order?.order_number}</div>
                            <div>Customer: ${refund.order?.member?.name || 'Umum'}</div>
                            <div>Tipe: ${refund.type === 'full' ? 'Seluruh Transaksi' : 'Sebagian'}</div>
                            <div>Alasan: ${this.getRefundReasonText(refund.reason)}</div>
                            <div>Tanggal: ${new Date(refund.created_at).toLocaleDateString('id-ID')}</div>
                        </div>
                    </div>
                    
                    <div class="text-right">
                        <div class="font-semibold text-lg">${formatCurrency(refund.amount || 0)}</div>
                        ${refund.status === 'pending_approval' ? `
                            <div class="flex space-x-2 mt-2">
                                <button onclick="refundManager.approveRefund(${refund.id})" 
                                    class="px-3 py-1 text-xs bg-green-500 text-white rounded hover:bg-green-600">
                                    Setujui
                                </button>
                                <button onclick="refundManager.rejectRefund(${refund.id})" 
                                    class="px-3 py-1 text-xs bg-red-500 text-white rounded hover:bg-red-600">
                                    Tolak
                                </button>
                            </div>
                        ` : ''}
                    </div>
                </div>
                
                ${refund.notes ? `
                    <div class="bg-gray-50 rounded p-3 text-sm">
                        <strong>Catatan:</strong> ${refund.notes}
                    </div>
                ` : ''}
                
                ${refund.items && refund.items.length > 0 ? `
                    <div class="mt-3 pt-3 border-t">
                        <h5 class="font-medium text-sm mb-2">Items yang direfund:</h5>
                        <div class="space-y-1 text-sm">
                            ${refund.items.map(item => `
                                <div class="flex justify-between">
                                    <span>${item.product?.name || 'Produk'} (${formatQuantity(item.quantity)}x)</span>
                                    <span>${formatCurrency(item.subtotal)}</span>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                ` : ''}
            </div>
        `).join('');
    }

    // Get refund status badge class
    getRefundStatusBadgeClass(status) {
        const classes = {
            pending_approval: 'bg-yellow-100 text-yellow-800',
            approved: 'bg-green-100 text-green-800',
            rejected: 'bg-red-100 text-red-800'
        };
        return classes[status] || 'bg-gray-100 text-gray-800';
    }

    // Get refund status text
    getRefundStatusText(status) {
        const texts = {
            pending_approval: 'Menunggu Persetujuan',
            approved: 'Disetujui',
            rejected: 'Ditolak'
        };
        return texts[status] || status;
    }

    // Get refund reason text
    getRefundReasonText(reason) {
        const texts = {
            customer_request: 'Permintaan Customer',
            product_defect: 'Produk Rusak',
            wrong_item: 'Salah Barang',
            system_error: 'Error System',
            other: 'Lainnya'
        };
        return texts[reason] || reason;
    }

    // Approve refund
    async approveRefund(refundId) {
        if (!confirm('Apakah Anda yakin ingin menyetujui refund ini?')) {
            return;
        }

        const loadingOverlay = showLoading('Menyetujui refund...');

        try {
            const response = await fetch(`/api/refunds/${refundId}/approve`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${POS_CONFIG.API_TOKEN}`,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    approved_by: 'supervisor', // get from current user
                    approval_notes: 'Disetujui oleh supervisor'
                })
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'Gagal menyetujui refund');
            }

            if (data.success) {
                showNotification('Refund berhasil disetujui', 'success');
                // Reload the refunds list
                const modal = document.getElementById('refundManagementModal');
                if (modal) {
                    this.loadRefundsForApproval(modal);
                }
            }

        } catch (error) {
            console.error('Approve refund error:', error);
            showNotification(error.message || 'Gagal menyetujui refund', 'error');
        } finally {
            hideLoading();
        }
    }

    // Reject refund
    async rejectRefund(refundId) {
        const reason = prompt('Masukkan alasan penolakan:');
        if (!reason) return;

        const loadingOverlay = showLoading('Menolak refund...');

        try {
            const response = await fetch(`/api/refunds/${refundId}/reject`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${POS_CONFIG.API_TOKEN}`,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    rejected_by: 'supervisor', // get from current user
                    rejection_reason: reason
                })
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'Gagal menolak refund');
            }

            if (data.success) {
                showNotification('Refund berhasil ditolak', 'success');
                // Reload the refunds list
                const modal = document.getElementById('refundManagementModal');
                if (modal) {
                    this.loadRefundsForApproval(modal);
                }
            }

        } catch (error) {
            console.error('Reject refund error:', error);
            showNotification(error.message || 'Gagal menolak refund', 'error');
        } finally {
            hideLoading();
        }
    }
}

// Global instance
const refundManager = new RefundManager();