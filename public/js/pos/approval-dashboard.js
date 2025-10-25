/**
 * Approval Dashboard untuk Admin/Supervisor
 */
class ApprovalDashboardManager {
    constructor() {
        this.currentFilter = 'all';
        this.currentPage = 1;
        this.itemsPerPage = 10;
    }

    // Show approval dashboard
    showApprovalDashboard() {
        const modal = document.createElement('div');
        modal.id = 'approvalDashboardModal';
        modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
        
        modal.innerHTML = `
            <div class="bg-white rounded-lg shadow-xl max-w-7xl w-full mx-4 max-h-[95vh] overflow-y-auto">
                <div class="p-6">
                    <!-- Header -->
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">Dashboard Persetujuan</h3>
                            <p class="text-sm text-gray-600">Kelola persetujuan transaksi, kas, dan bonus</p>
                        </div>
                        <button onclick="this.remove()" class="text-gray-400 hover:text-gray-600">
                            <i data-lucide="x" class="w-6 h-6"></i>
                        </button>
                    </div>

                    <!-- Summary Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                                    <i data-lucide="clock" class="w-6 h-6 text-yellow-600"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-600">Pending Total</p>
                                    <p class="text-2xl font-bold text-yellow-600" id="totalPendingCount">0</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <i data-lucide="banknote" class="w-6 h-6 text-blue-600"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-600">Transaksi Kas</p>
                                    <p class="text-2xl font-bold text-blue-600" id="pendingCashCount">0</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                    <i data-lucide="shopping-cart" class="w-6 h-6 text-green-600"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-600">Transaksi POS</p>
                                    <p class="text-2xl font-bold text-green-600" id="pendingTransactionCount">0</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                                    <i data-lucide="gift" class="w-6 h-6 text-purple-600"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-600">Bonus</p>
                                    <p class="text-2xl font-bold text-purple-600" id="pendingBonusCount">0</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filter Tabs -->
                    <div class="flex flex-wrap gap-2 mb-6 border-b border-gray-200">
                        <button class="approval-tab-btn active px-4 py-2 border-b-2 border-blue-500 text-blue-600 font-medium" data-type="all">
                            Semua <span id="allCount" class="ml-1 px-2 py-1 text-xs bg-blue-100 rounded-full">0</span>
                        </button>
                        <button class="approval-tab-btn px-4 py-2 border-b-2 border-transparent text-gray-500 hover:text-gray-700" data-type="cash">
                            Transaksi Kas <span id="cashCount" class="ml-1 px-2 py-1 text-xs bg-gray-100 rounded-full">0</span>
                        </button>
                        <button class="approval-tab-btn px-4 py-2 border-b-2 border-transparent text-gray-500 hover:text-gray-700" data-type="transaction">
                            Transaksi POS <span id="transactionCount" class="ml-1 px-2 py-1 text-xs bg-gray-100 rounded-full">0</span>
                        </button>
                        <button class="approval-tab-btn px-4 py-2 border-b-2 border-transparent text-gray-500 hover:text-gray-700" data-type="bonus">
                            Bonus <span id="bonusCount" class="ml-1 px-2 py-1 text-xs bg-gray-100 rounded-full">0</span>
                        </button>
                        <button class="approval-tab-btn px-4 py-2 border-b-2 border-transparent text-gray-500 hover:text-gray-700" data-type="refund">
                            Refund <span id="refundCount" class="ml-1 px-2 py-1 text-xs bg-gray-100 rounded-full">0</span>
                        </button>
                    </div>

                    <!-- Filters and Actions -->
                    <div class="flex flex-wrap gap-3 mb-6">
                        <select id="approvalFilterPeriod" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="today">Hari Ini</option>
                            <option value="week">Minggu Ini</option>
                            <option value="month">Bulan Ini</option>
                            <option value="all">Semua Periode</option>
                        </select>
                        
                        <select id="approvalFilterOutlet" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="all">Semua Outlet</option>
                            <!-- Will be populated dynamically -->
                        </select>
                        
                        <select id="approvalFilterPriority" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="all">Semua Prioritas</option>
                            <option value="high">High</option>
                            <option value="medium">Medium</option>
                            <option value="low">Low</option>
                        </select>
                        
                        <input type="text" id="approvalSearchInput" 
                            class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Cari berdasarkan ID, jumlah, atau keterangan...">
                            
                        <button id="refreshApprovalBtn" 
                            class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition-colors">
                            <i data-lucide="refresh-cw" class="w-4 h-4 inline mr-2"></i>
                            Refresh
                        </button>

                        <button id="bulkApproveBtn" 
                            class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 transition-colors">
                            <i data-lucide="check-check" class="w-4 h-4 inline mr-2"></i>
                            Setujui Terpilih
                        </button>
                    </div>

                    <!-- Approval List -->
                    <div class="bg-white border border-gray-200 rounded-lg">
                        <div class="px-6 py-3 border-b border-gray-200 bg-gray-50">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <input type="checkbox" id="selectAllApprovals" class="mr-3 rounded border-gray-300">
                                    <h4 class="text-sm font-semibold text-gray-700">Daftar Persetujuan</h4>
                                </div>
                                <div class="text-sm text-gray-500">
                                    <span id="approvalResultCount">0</span> item ditemukan
                                </div>
                            </div>
                        </div>
                        
                        <div id="approvalTableContainer" class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50 border-b border-gray-200">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-12">
                                            <input type="checkbox" class="rounded border-gray-300">
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Tipe & ID
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Tanggal
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Outlet
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Detail
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Jumlah/Nilai
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Bukti
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Prioritas
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody id="approvalTableBody" class="bg-white divide-y divide-gray-200">
                                    <!-- Will be populated dynamically -->
                                </tbody>
                            </table>
                        </div>
                        
                        <div id="approvalEmptyState" class="hidden p-8 text-center">
                            <i data-lucide="clipboard-check" class="w-12 h-12 mx-auto text-gray-300 mb-4"></i>
                            <p class="text-gray-500 text-lg font-medium">Tidak ada item yang memerlukan persetujuan</p>
                            <p class="text-gray-400 text-sm">Semua transaksi sudah diproses</p>
                        </div>
                        
                        <!-- Pagination -->
                        <div id="approvalPagination" class="px-6 py-3 border-t border-gray-200 bg-gray-50">
                            <div class="flex justify-between items-center">
                                <div class="text-sm text-gray-700">
                                    Menampilkan <span id="approvalStartIndex">0</span> - <span id="approvalEndIndex">0</span> 
                                    dari <span id="approvalTotalCount">0</span> item
                                </div>
                                <div class="flex space-x-2">
                                    <button id="approvalPrevPage" class="px-3 py-1 text-sm border border-gray-300 rounded hover:bg-gray-100" disabled>
                                        Sebelumnya
                                    </button>
                                    <button id="approvalNextPage" class="px-3 py-1 text-sm border border-gray-300 rounded hover:bg-gray-100" disabled>
                                        Selanjutnya
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;

        document.body.appendChild(modal);
        this.attachApprovalDashboardEventListeners(modal);
        this.loadApprovalData();
        
        lucide.createIcons({ icons });
    }

    // Load approval data from various sources
    loadApprovalData() {
        const approvalItems = [];

        // Load cash approvals
        const cashApprovals = JSON.parse(localStorage.getItem('pendingCashApprovals') || '[]');
        cashApprovals.forEach(item => {
            if (item.status === 'pending_approval') {
                approvalItems.push({
                    ...item,
                    approval_type: 'cash',
                    priority: this.calculatePriority(item)
                });
            }
        });

        // Load transaction approvals (transfer payments, etc.)
        const transactionApprovals = JSON.parse(localStorage.getItem('pendingTransactionApprovals') || '[]');
        transactionApprovals.forEach(item => {
            if (item.status === 'pending_approval') {
                approvalItems.push({
                    ...item,
                    approval_type: 'transaction',
                    priority: this.calculatePriority(item)
                });
            }
        });

        // Load bonus approvals
        const bonusHistory = JSON.parse(localStorage.getItem('bonusHistory') || '[]');
        bonusHistory.forEach(item => {
            if (item.status === 'pending' || !item.status) {
                approvalItems.push({
                    ...item,
                    approval_type: 'bonus',
                    priority: 'medium'
                });
            }
        });

        // Load refund approvals
        const refundApprovals = JSON.parse(localStorage.getItem('pendingRefundApprovals') || '[]');
        refundApprovals.forEach(item => {
            if (item.status === 'pending_approval') {
                approvalItems.push({
                    ...item,
                    approval_type: 'refund',
                    priority: 'high'
                });
            }
        });

        // Apply filters
        const filteredItems = this.applyFilters(approvalItems);

        // Update summary
        this.updateApprovalSummary(approvalItems);
        
        // Render table
        this.renderApprovalTable(filteredItems);
    }

    // Calculate priority based on item data
    calculatePriority(item) {
        const amount = item.amount || item.value || 0;
        
        if (amount > 5000000) return 'high'; // > 5 juta
        if (amount > 1000000) return 'medium'; // > 1 juta
        return 'low';
    }

    // Apply filters to approval items
    applyFilters(items) {
        const typeFilter = document.querySelector('.approval-tab-btn.active').dataset.type;
        const periodFilter = document.getElementById('approvalFilterPeriod').value;
        const outletFilter = document.getElementById('approvalFilterOutlet').value;
        const priorityFilter = document.getElementById('approvalFilterPriority').value;
        const searchTerm = document.getElementById('approvalSearchInput').value.toLowerCase();

        return items.filter(item => {
            // Type filter
            if (typeFilter !== 'all' && item.approval_type !== typeFilter) return false;

            // Period filter
            if (periodFilter !== 'all') {
                const itemDate = new Date(item.timestamp);
                const now = new Date();
                const dayStart = new Date(now.setHours(0,0,0,0));
                const weekStart = new Date(now.setDate(now.getDate() - 7));
                const monthStart = new Date(now.getFullYear(), now.getMonth(), 1);

                switch (periodFilter) {
                    case 'today':
                        if (itemDate < dayStart) return false;
                        break;
                    case 'week':
                        if (itemDate < weekStart) return false;
                        break;
                    case 'month':
                        if (itemDate < monthStart) return false;
                        break;
                }
            }

            // Outlet filter
            if (outletFilter !== 'all' && item.outlet_id != outletFilter) return false;

            // Priority filter
            if (priorityFilter !== 'all' && item.priority !== priorityFilter) return false;

            // Search filter
            if (searchTerm) {
                const searchableText = `${item.id} ${item.notes || ''} ${item.amount || 0} ${item.reason || ''}`.toLowerCase();
                if (!searchableText.includes(searchTerm)) return false;
            }

            return true;
        });
    }

    // Update approval summary
    updateApprovalSummary(items) {
        const summary = {
            total: items.length,
            cash: items.filter(i => i.approval_type === 'cash').length,
            transaction: items.filter(i => i.approval_type === 'transaction').length,
            bonus: items.filter(i => i.approval_type === 'bonus').length,
            refund: items.filter(i => i.approval_type === 'refund').length
        };

        document.getElementById('totalPendingCount').textContent = summary.total;
        document.getElementById('pendingCashCount').textContent = summary.cash;
        document.getElementById('pendingTransactionCount').textContent = summary.transaction;
        document.getElementById('pendingBonusCount').textContent = summary.bonus;

        // Update tab counters
        document.getElementById('allCount').textContent = summary.total;
        document.getElementById('cashCount').textContent = summary.cash;
        document.getElementById('transactionCount').textContent = summary.transaction;
        document.getElementById('bonusCount').textContent = summary.bonus;
        document.getElementById('refundCount').textContent = summary.refund;
    }

    // Render approval table
    renderApprovalTable(items) {
        const tbody = document.getElementById('approvalTableBody');
        const emptyState = document.getElementById('approvalEmptyState');
        const resultCount = document.getElementById('approvalResultCount');

        resultCount.textContent = items.length;

        if (items.length === 0) {
            tbody.innerHTML = '';
            emptyState.classList.remove('hidden');
            return;
        }

        emptyState.classList.add('hidden');
        
        // Sort by timestamp (newest first) and priority
        items.sort((a, b) => {
            const priorityWeight = { high: 3, medium: 2, low: 1 };
            if (priorityWeight[a.priority] !== priorityWeight[b.priority]) {
                return priorityWeight[b.priority] - priorityWeight[a.priority];
            }
            return new Date(b.timestamp) - new Date(a.timestamp);
        });

        tbody.innerHTML = items.map((item, index) => `
            <tr class="hover:bg-gray-50 ${item.priority === 'high' ? 'bg-red-25 border-l-4 border-red-400' : ''}">
                <td class="px-4 py-3">
                    <input type="checkbox" class="approval-item-checkbox rounded border-gray-300" data-id="${item.id}">
                </td>
                <td class="px-4 py-3">
                    <div class="flex items-center">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center mr-3 ${this.getTypeIconBg(item.approval_type)}">
                            <i data-lucide="${this.getTypeIcon(item.approval_type)}" class="w-4 h-4 ${this.getTypeIconColor(item.approval_type)}"></i>
                        </div>
                        <div>
                            <div class="font-medium text-gray-900">${this.getTypeLabel(item.approval_type)}</div>
                            <div class="text-xs text-gray-500">${item.id}</div>
                        </div>
                    </div>
                </td>
                <td class="px-4 py-3 text-sm text-gray-900">
                    <div>${new Date(item.timestamp).toLocaleDateString('id-ID')}</div>
                    <div class="text-xs text-gray-500">${new Date(item.timestamp).toLocaleTimeString('id-ID')}</div>
                </td>
                <td class="px-4 py-3 text-sm text-gray-900">
                    <div class="font-medium">${item.outlet_name || 'Unknown'}</div>
                    <div class="text-xs text-gray-500">ID: ${item.outlet_id}</div>
                </td>
                <td class="px-4 py-3 text-sm text-gray-900">
                    <div class="max-w-xs truncate font-medium">
                        ${this.getItemDetail(item)}
                    </div>
                    <div class="text-xs text-gray-500 max-w-xs truncate">
                        ${item.notes || item.reason || '-'}
                    </div>
                </td>
                <td class="px-4 py-3">
                    <div class="font-semibold text-gray-900">
                        ${item.amount ? formatCurrency(item.amount) : 
                          item.value ? formatCurrency(item.value) : 
                          item.quantity ? `${formatQuantity(item.quantity)}x` : '-'}
                    </div>
                </td>
                <td class="px-4 py-3">
                    ${item.proof_files && item.proof_files.length > 0 ? 
                        `<div class="flex items-center text-green-600">
                            <i data-lucide="file-check" class="w-4 h-4 mr-1"></i>
                            <span class="text-xs">${item.proof_files.length} file</span>
                        </div>` :
                        `<div class="flex items-center text-gray-400">
                            <i data-lucide="file-x" class="w-4 h-4 mr-1"></i>
                            <span class="text-xs">No proof</span>
                        </div>`
                    }
                </td>
                <td class="px-4 py-3">
                    <span class="px-2 py-1 text-xs rounded-full ${this.getPriorityBadgeClass(item.priority)}">
                        ${item.priority?.toUpperCase() || 'MEDIUM'}
                    </span>
                </td>
                <td class="px-4 py-3">
                    <div class="flex items-center space-x-2">
                        <button onclick="approvalDashboardManager.viewApprovalDetails('${item.id}', '${item.approval_type}')" 
                            class="text-blue-600 hover:text-blue-800" title="Lihat Detail">
                            <i data-lucide="eye" class="w-4 h-4"></i>
                        </button>
                        <button onclick="approvalDashboardManager.approveItem('${item.id}', '${item.approval_type}')" 
                            class="text-green-600 hover:text-green-800" title="Setujui">
                            <i data-lucide="check-circle" class="w-4 h-4"></i>
                        </button>
                        <button onclick="approvalDashboardManager.rejectItem('${item.id}', '${item.approval_type}')" 
                            class="text-red-600 hover:text-red-800" title="Tolak">
                            <i data-lucide="x-circle" class="w-4 h-4"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');

        lucide.createIcons({ icons });
    }

    // Get type-specific styling and labels
    getTypeIcon(type) {
        const icons = {
            cash: 'banknote',
            transaction: 'shopping-cart',
            bonus: 'gift',
            refund: 'rotate-ccw'
        };
        return icons[type] || 'help-circle';
    }

    getTypeIconBg(type) {
        const colors = {
            cash: 'bg-blue-100',
            transaction: 'bg-green-100',
            bonus: 'bg-purple-100',
            refund: 'bg-red-100'
        };
        return colors[type] || 'bg-gray-100';
    }

    getTypeIconColor(type) {
        const colors = {
            cash: 'text-blue-600',
            transaction: 'text-green-600',
            bonus: 'text-purple-600',
            refund: 'text-red-600'
        };
        return colors[type] || 'text-gray-600';
    }

    getTypeLabel(type) {
        const labels = {
            cash: 'Transaksi Kas',
            transaction: 'Transaksi POS',
            bonus: 'Bonus Manual',
            refund: 'Refund'
        };
        return labels[type] || 'Unknown';
    }

    getItemDetail(item) {
        switch (item.approval_type) {
            case 'cash':
                return `${item.type === 'add' ? 'Tambah' : item.type === 'withdraw' ? 'Ambil' : 'Transfer'} Kas`;
            case 'bonus':
                return `${item.product_name}`;
            case 'transaction':
                return `Transfer Payment`;
            case 'refund':
                return `Refund Order`;
            default:
                return '-';
        }
    }

    getPriorityBadgeClass(priority) {
        const classes = {
            high: 'bg-red-100 text-red-800',
            medium: 'bg-yellow-100 text-yellow-800',
            low: 'bg-gray-100 text-gray-800'
        };
        return classes[priority] || classes.medium;
    }

    // Attach event listeners
    attachApprovalDashboardEventListeners(modal) {
        // Tab switching
        modal.querySelectorAll('.approval-tab-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                this.switchApprovalTab(modal, e.target.dataset.type);
            });
        });

        // Filter changes
        modal.querySelector('#approvalFilterPeriod').addEventListener('change', () => {
            this.loadApprovalData();
        });

        modal.querySelector('#approvalFilterOutlet').addEventListener('change', () => {
            this.loadApprovalData();
        });

        modal.querySelector('#approvalFilterPriority').addEventListener('change', () => {
            this.loadApprovalData();
        });

        modal.querySelector('#approvalSearchInput').addEventListener('input', debounce(() => {
            this.loadApprovalData();
        }, 300));

        // Refresh button
        modal.querySelector('#refreshApprovalBtn').addEventListener('click', () => {
            this.loadApprovalData();
        });

        // Bulk approve
        modal.querySelector('#bulkApproveBtn').addEventListener('click', () => {
            this.bulkApprove();
        });

        // Select all checkbox
        modal.querySelector('#selectAllApprovals').addEventListener('change', (e) => {
            const checkboxes = modal.querySelectorAll('.approval-item-checkbox');
            checkboxes.forEach(cb => cb.checked = e.target.checked);
        });
    }

    // Switch approval tab
    switchApprovalTab(modal, activeType) {
        // Update tab buttons
        modal.querySelectorAll('.approval-tab-btn').forEach(btn => {
            btn.classList.remove('active', 'border-blue-500', 'text-blue-600');
            btn.classList.add('border-transparent', 'text-gray-500');
        });

        modal.querySelector(`[data-type="${activeType}"]`).classList.add('active', 'border-blue-500', 'text-blue-600');
        modal.querySelector(`[data-type="${activeType}"]`).classList.remove('border-transparent', 'text-gray-500');

        this.loadApprovalData();
    }

    // View approval details
    viewApprovalDetails(itemId, type) {
        // Implementation for viewing detailed approval information
        console.log('View approval details:', itemId, type);
        // This would show a detailed modal with all item information, proof files, etc.
    }

    // Approve individual item
    approveItem(itemId, type) {
        if (!confirm('Apakah Anda yakin ingin menyetujui item ini?')) return;

        // Update the item status based on type
        this.updateItemStatus(itemId, type, 'approved');
        
        showNotification('Item berhasil disetujui', 'success');
        this.loadApprovalData();
    }

    // Reject individual item
    rejectItem(itemId, type) {
        const reason = prompt('Masukkan alasan penolakan:');
        if (!reason) return;

        // Update the item status with rejection reason
        this.updateItemStatus(itemId, type, 'rejected', reason);
        
        showNotification('Item berhasil ditolak', 'info');
        this.loadApprovalData();
    }

    // Update item status in localStorage
    updateItemStatus(itemId, type, status, reason = null) {
        const storageKeys = {
            cash: 'pendingCashApprovals',
            transaction: 'pendingTransactionApprovals',
            bonus: 'bonusHistory',
            refund: 'pendingRefundApprovals'
        };

        const storageKey = storageKeys[type];
        if (!storageKey) return;

        let items = JSON.parse(localStorage.getItem(storageKey) || '[]');
        const itemIndex = items.findIndex(item => item.id === itemId);
        
        if (itemIndex === -1) return;

        items[itemIndex].status = status;
        items[itemIndex].processed_at = new Date();
        items[itemIndex].processed_by = 'current_supervisor'; // TODO: get from auth

        if (reason) {
            items[itemIndex].rejection_reason = reason;
        }

        localStorage.setItem(storageKey, JSON.stringify(items));
    }

    // Bulk approve selected items
    bulkApprove() {
        const selectedItems = document.querySelectorAll('.approval-item-checkbox:checked');
        
        if (selectedItems.length === 0) {
            showNotification('Pilih minimal satu item untuk disetujui', 'warning');
            return;
        }

        if (!confirm(`Apakah Anda yakin ingin menyetujui ${selectedItems.length} item terpilih?`)) return;

        selectedItems.forEach(checkbox => {
            const itemId = checkbox.dataset.id;
            const row = checkbox.closest('tr');
            const typeElement = row.querySelector('[data-lucide]').closest('div').nextElementSibling.querySelector('.font-medium');
            
            // Determine type from the row data
            let type = 'cash'; // Default fallback
            if (typeElement.textContent.includes('Bonus')) type = 'bonus';
            else if (typeElement.textContent.includes('POS')) type = 'transaction';
            else if (typeElement.textContent.includes('Refund')) type = 'refund';

            this.updateItemStatus(itemId, type, 'approved');
        });

        showNotification(`${selectedItems.length} item berhasil disetujui`, 'success');
        this.loadApprovalData();
    }
}

// Global instance
const approvalDashboardManager = new ApprovalDashboardManager();