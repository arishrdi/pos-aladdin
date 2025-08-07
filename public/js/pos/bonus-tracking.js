/**
 * Bonus Tracking System untuk POS
 */
class BonusTrackingManager {
    constructor() {
        this.currentFilter = 'today';
    }

    // Show bonus tracking dashboard
    showBonusTrackingDashboard() {
        const modal = document.createElement('div');
        modal.id = 'bonusTrackingModal';
        modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
        
        modal.innerHTML = `
            <div class="bg-white rounded-lg shadow-xl max-w-6xl w-full mx-4 max-h-[95vh] overflow-y-auto">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-bold text-gray-900">Dashboard Bonus Tracking</h3>
                        <button onclick="this.remove()" class="text-gray-400 hover:text-gray-600">
                            <i data-lucide="x" class="w-5 h-5"></i>
                        </button>
                    </div>

                    <!-- Summary Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                    <i data-lucide="gift" class="w-6 h-6 text-green-600"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-600">Bonus Hari Ini</p>
                                    <p class="text-2xl font-bold text-green-600" id="todayBonusCount">0</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <i data-lucide="trending-up" class="w-6 h-6 text-blue-600"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-600">Total Bonus</p>
                                    <p class="text-2xl font-bold text-blue-600" id="totalBonusCount">0</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                                    <i data-lucide="clock" class="w-6 h-6 text-yellow-600"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-600">Pending Approval</p>
                                    <p class="text-2xl font-bold text-yellow-600" id="pendingBonusCount">0</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                                    <i data-lucide="dollar-sign" class="w-6 h-6 text-purple-600"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-600">Nilai Total</p>
                                    <p class="text-lg font-bold text-purple-600" id="totalBonusValue">Rp 0</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="flex flex-wrap gap-3 mb-6">
                        <select id="bonusFilterPeriod" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                            <option value="today">Hari Ini</option>
                            <option value="week">Minggu Ini</option>
                            <option value="month">Bulan Ini</option>
                            <option value="all">Semua</option>
                        </select>
                        
                        <select id="bonusFilterOutlet" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                            <option value="all">Semua Outlet</option>
                            <!-- Will be populated dynamically -->
                        </select>
                        
                        <input type="text" id="bonusSearchInput" 
                            class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="Cari bonus...">
                            
                        <button id="refreshBonusBtn" 
                            class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 transition-colors">
                            <i data-lucide="refresh-cw" class="w-4 h-4 inline mr-2"></i>
                            Refresh
                        </button>
                        
                        <button id="exportBonusBtn" 
                            class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition-colors">
                            <i data-lucide="download" class="w-4 h-4 inline mr-2"></i>
                            Export
                        </button>
                    </div>

                    <!-- Bonus List -->
                    <div class="bg-white border border-gray-200 rounded-lg">
                        <div class="px-6 py-3 border-b border-gray-200 bg-gray-50">
                            <h4 class="text-sm font-semibold text-gray-700">Riwayat Bonus</h4>
                        </div>
                        
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50 border-b border-gray-200">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Tanggal & Waktu
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Produk
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Qty
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Alasan
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Diotorisasi
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Outlet
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody id="bonusTableBody" class="bg-white divide-y divide-gray-200">
                                    <!-- Will be populated dynamically -->
                                </tbody>
                            </table>
                        </div>
                        
                        <div id="bonusEmptyState" class="hidden p-8 text-center">
                            <i data-lucide="gift" class="w-12 h-12 mx-auto text-gray-300 mb-4"></i>
                            <p class="text-gray-500 text-lg font-medium">Tidak ada data bonus</p>
                            <p class="text-gray-400 text-sm">Bonus akan muncul setelah ditambahkan ke transaksi</p>
                        </div>
                        
                        <!-- Pagination -->
                        <div id="bonusPagination" class="px-6 py-3 border-t border-gray-200 bg-gray-50">
                            <div class="flex justify-between items-center">
                                <div class="text-sm text-gray-700">
                                    Menampilkan <span id="bonusStartIndex">0</span> - <span id="bonusEndIndex">0</span> 
                                    dari <span id="bonusTotalCount">0</span> bonus
                                </div>
                                <div class="flex space-x-2">
                                    <button id="bonusPrevPage" class="px-3 py-1 text-sm border border-gray-300 rounded hover:bg-gray-100" disabled>
                                        Sebelumnya
                                    </button>
                                    <button id="bonusNextPage" class="px-3 py-1 text-sm border border-gray-300 rounded hover:bg-gray-100" disabled>
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
        this.attachBonusTrackingEventListeners(modal);
        this.loadBonusData();
        
        lucide.createIcons();
    }

    // Load bonus data from API
    async loadBonusData() {
        try {
            const filter = document.getElementById('bonusFilterPeriod').value;
            const outletFilter = document.getElementById('bonusFilterOutlet').value;
            const searchTerm = document.getElementById('bonusSearchInput').value.toLowerCase();

            const outletId = outletFilter === 'all' ? localStorage.getItem('outlet_id') : outletFilter;
            if (!outletId) {
                console.error('Outlet ID tidak ditemukan');
                return;
            }

            // Calculate date range based on filter
            let dateFrom = null;
            let dateTo = null;
            const today = new Date();
            
            switch (filter) {
                case 'today':
                    dateFrom = today.toISOString().split('T')[0];
                    dateTo = dateFrom;
                    break;
                case 'week':
                    const weekAgo = new Date(today.getTime() - 7 * 24 * 60 * 60 * 1000);
                    dateFrom = weekAgo.toISOString().split('T')[0];
                    dateTo = today.toISOString().split('T')[0];
                    break;
                case 'month':
                    const monthAgo = new Date(today.getFullYear(), today.getMonth(), 1);
                    dateFrom = monthAgo.toISOString().split('T')[0];
                    dateTo = today.toISOString().split('T')[0];
                    break;
            }

            // Build API URL
            const token = localStorage.getItem('token') || document.querySelector('meta[name="csrf-token"]')?.content;
            let url = `/api/bonus/history?outlet_id=${outletId}`;
            if (dateFrom) url += `&date_from=${dateFrom}`;
            if (dateTo) url += `&date_to=${dateTo}`;

            const response = await fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (response.ok) {
                const data = await response.json();
                if (data.success) {
                    let bonusHistory = data.data.data || []; // Paginated response
                    
                    // Apply search filter client-side
                    if (searchTerm) {
                        bonusHistory = bonusHistory.filter(bonus => {
                            const matchProduct = bonus.bonusItems.some(item => 
                                item.product.name.toLowerCase().includes(searchTerm)
                            );
                            const matchReason = bonus.reason && bonus.reason.toLowerCase().includes(searchTerm);
                            const matchCashier = bonus.cashier && bonus.cashier.name.toLowerCase().includes(searchTerm);
                            const matchNumber = bonus.bonus_number && bonus.bonus_number.toLowerCase().includes(searchTerm);
                            
                            return matchProduct || matchReason || matchCashier || matchNumber;
                        });
                    }

                    // Update summary and render table
                    this.updateBonusSummary(bonusHistory);
                    this.renderBonusTable(bonusHistory);
                } else {
                    console.error('Failed to load bonus data:', data.message);
                    this.renderBonusTable([]);
                }
            } else {
                console.error('API request failed:', response.status);
                this.renderBonusTable([]);
            }
        } catch (error) {
            console.error('Error loading bonus data:', error);
            this.renderBonusTable([]);
        }
    }

    // Update summary cards
    updateBonusSummary(bonusData) {
        const today = new Date().toDateString();
        const todayBonus = bonusData.filter(bonus => 
            new Date(bonus.created_at).toDateString() === today
        );

        document.getElementById('todayBonusCount').textContent = todayBonus.length;
        document.getElementById('totalBonusCount').textContent = bonusData.length;
        document.getElementById('pendingBonusCount').textContent = 
            bonusData.filter(bonus => bonus.status === 'pending').length;
        
        const totalValue = bonusData.reduce((sum, bonus) => sum + (bonus.total_value || 0), 0);
        document.getElementById('totalBonusValue').textContent = formatCurrency(totalValue);
    }

    // Render bonus table
    renderBonusTable(bonusData) {
        const tbody = document.getElementById('bonusTableBody');
        const emptyState = document.getElementById('bonusEmptyState');

        if (bonusData.length === 0) {
            tbody.innerHTML = '';
            emptyState.classList.remove('hidden');
            return;
        }

        emptyState.classList.add('hidden');
        
        tbody.innerHTML = bonusData.map((bonus, index) => {
            const firstProduct = bonus.bonusItems?.[0]?.product || {};
            const productName = firstProduct.name || 'Unknown Product';
            const totalQuantity = bonus.bonusItems?.reduce((sum, item) => sum + parseFloat(item.quantity), 0) || 0;
            const cashierName = bonus.cashier?.name || 'Unknown';
            const outletName = bonus.outlet?.name || 'Unknown';
            
            return `
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm text-gray-900">
                        <div>${new Date(bonus.created_at).toLocaleDateString('id-ID')}</div>
                        <div class="text-xs text-gray-500">${new Date(bonus.created_at).toLocaleTimeString('id-ID')}</div>
                    </td>
                    <td class="px-4 py-3">
                        <div class="font-medium text-gray-900">${productName}</div>
                        <div class="text-xs text-gray-500">${bonus.type || 'Manual'}</div>
                        ${bonus.bonusItems?.length > 1 ? `<div class="text-xs text-gray-400">+${bonus.bonusItems.length - 1} lainnya</div>` : ''}
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-900">
                        ${formatQuantity(totalQuantity)}
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-900">
                        ${bonus.reason || '-'}
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-900">
                        ${cashierName}
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-900">
                        ${outletName}
                    </td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 text-xs rounded-full ${this.getStatusBadgeClass(bonus.status || 'approved')}">
                            ${this.getStatusText(bonus.status || 'approved')}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-sm">
                        <div class="flex space-x-2">
                            <button onclick="bonusTrackingManager.viewBonusDetails('${bonus.id}')" 
                                class="text-blue-600 hover:text-blue-800">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                            </button>
                            ${bonus.status === 'pending' ? `
                                <button onclick="bonusTrackingManager.approveBonusTracking('${bonus.id}')" 
                                    class="text-green-600 hover:text-green-800">
                                    <i data-lucide="check" class="w-4 h-4"></i>
                                </button>
                                <button onclick="bonusTrackingManager.rejectBonusTracking('${bonus.id}')" 
                                    class="text-red-600 hover:text-red-800">
                                    <i data-lucide="x" class="w-4 h-4"></i>
                                </button>
                            ` : ''}
                        </div>
                    </td>
                </tr>
            `;
        }).join('');

        lucide.createIcons();
    }

    // Get status badge class
    getStatusBadgeClass(status) {
        const classes = {
            pending: 'bg-yellow-100 text-yellow-800',
            approved: 'bg-green-100 text-green-800',
            rejected: 'bg-red-100 text-red-800'
        };
        return classes[status] || 'bg-gray-100 text-gray-800';
    }

    // Get status text
    getStatusText(status) {
        const texts = {
            pending: 'Pending',
            approved: 'Disetujui',
            rejected: 'Ditolak'
        };
        return texts[status] || status;
    }

    // Attach event listeners
    attachBonusTrackingEventListeners(modal) {
        // Filter changes
        modal.querySelector('#bonusFilterPeriod').addEventListener('change', () => {
            this.loadBonusData();
        });

        modal.querySelector('#bonusFilterOutlet').addEventListener('change', () => {
            this.loadBonusData();
        });

        modal.querySelector('#bonusSearchInput').addEventListener('input', debounce(() => {
            this.loadBonusData();
        }, 300));

        // Refresh button
        modal.querySelector('#refreshBonusBtn').addEventListener('click', () => {
            this.loadBonusData();
        });

        // Export button
        modal.querySelector('#exportBonusBtn').addEventListener('click', () => {
            this.exportBonusData();
        });
    }

    // View bonus details
    async viewBonusDetails(bonusId) {
        try {
            const token = localStorage.getItem('token') || document.querySelector('meta[name="csrf-token"]')?.content;
            const outletId = localStorage.getItem('outlet_id');
            
            const response = await fetch(`/api/bonus/history?outlet_id=${outletId}`, {
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            if (!response.ok) {
                console.error('Failed to fetch bonus details');
                return;
            }
            
            const data = await response.json();
            const bonus = data.data.data.find(b => b.id == bonusId);
            
            if (!bonus) {
                showNotification('Bonus tidak ditemukan', 'error');
                return;
            }

        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-60';
        
        modal.innerHTML = `
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Detail Bonus</h3>
                        <button onclick="this.closest('div').remove()" class="text-gray-400 hover:text-gray-600">
                            <i data-lucide="x" class="w-5 h-5"></i>
                        </button>
                    </div>

                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nomor Bonus</label>
                            <p class="text-sm text-gray-900">${bonus.bonus_number || '-'}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Produk Bonus</label>
                            <div class="space-y-2">
                                ${bonus.bonusItems?.map(item => `
                                    <div class="bg-gray-50 p-2 rounded">
                                        <div class="font-medium">${item.product?.name || 'Unknown'}</div>
                                        <div class="text-sm text-gray-600">Qty: ${formatQuantity(item.quantity)} • Nilai: ${formatCurrency(item.bonus_value)}</div>
                                    </div>
                                `).join('') || '<p class="text-gray-500">Tidak ada produk</p>'}
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Total Item</label>
                                <p class="text-sm text-gray-900">${bonus.total_items || 0}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Total Nilai</label>
                                <p class="text-sm text-gray-900">${formatCurrency(bonus.total_value || 0)}</p>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Alasan</label>
                            <p class="text-sm text-gray-900">${bonus.reason || '-'}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Kasir</label>
                            <p class="text-sm text-gray-900">${bonus.cashier?.name || 'Unknown'}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tanggal</label>
                            <p class="text-sm text-gray-900">${new Date(bonus.created_at).toLocaleString('id-ID')}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Outlet</label>
                            <p class="text-sm text-gray-900">${bonus.outlet?.name || 'Unknown'}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Status</label>
                            <span class="px-2 py-1 text-xs rounded-full ${this.getStatusBadgeClass(bonus.status || 'approved')}">
                                ${this.getStatusText(bonus.status || 'approved')}
                            </span>
                        </div>
                        
                        ${bonus.approved_by ? `
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Disetujui Oleh</label>
                                <p class="text-sm text-gray-900">${bonus.approved_by.name}</p>
                                <p class="text-xs text-gray-500">${new Date(bonus.approved_at).toLocaleString('id-ID')}</p>
                            </div>
                        ` : ''}
                        
                        ${bonus.rejected_by ? `
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Ditolak Oleh</label>
                                <p class="text-sm text-gray-900">${bonus.rejected_by.name}</p>
                                <p class="text-xs text-gray-500">${new Date(bonus.rejected_at).toLocaleString('id-ID')}</p>
                                ${bonus.rejection_reason ? `<p class="text-xs text-red-600">Alasan: ${bonus.rejection_reason}</p>` : ''}
                            </div>
                        ` : ''}
                    </div>

                    <div class="flex justify-end pt-4 mt-6 border-t border-gray-200">
                        <button onclick="this.closest('div').remove()" 
                            class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition-colors">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        `;

            document.body.appendChild(modal);
            lucide.createIcons();
            
        } catch (error) {
            console.error('Error loading bonus details:', error);
            showNotification('Gagal memuat detail bonus', 'error');
        }
    }

    // Export bonus data
    async exportBonusData() {
        try {
            const token = localStorage.getItem('token') || document.querySelector('meta[name="csrf-token"]')?.content;
            const outletId = localStorage.getItem('outlet_id');
            const filter = document.getElementById('bonusFilterPeriod').value;
            const searchTerm = document.getElementById('bonusSearchInput').value.toLowerCase();
            
            // Get current filtered data
            let url = `/api/bonus/history?outlet_id=${outletId}&per_page=1000`; // Get all data for export
            
            // Apply same filters as current view
            if (filter !== 'all') {
                const today = new Date();
                let dateFrom, dateTo;
                
                switch (filter) {
                    case 'today':
                        dateFrom = dateTo = today.toISOString().split('T')[0];
                        break;
                    case 'week':
                        const weekAgo = new Date(today.getTime() - 7 * 24 * 60 * 60 * 1000);
                        dateFrom = weekAgo.toISOString().split('T')[0];
                        dateTo = today.toISOString().split('T')[0];
                        break;
                    case 'month':
                        const monthAgo = new Date(today.getFullYear(), today.getMonth(), 1);
                        dateFrom = monthAgo.toISOString().split('T')[0];
                        dateTo = today.toISOString().split('T')[0];
                        break;
                }
                
                if (dateFrom) url += `&date_from=${dateFrom}`;
                if (dateTo) url += `&date_to=${dateTo}`;
            }
            
            const response = await fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            if (!response.ok) {
                throw new Error('Gagal mengambil data');
            }
            
            const data = await response.json();
            let bonusHistory = data.data.data || [];
            
            // Apply search filter client-side
            if (searchTerm) {
                bonusHistory = bonusHistory.filter(bonus => {
                    const matchProduct = bonus.bonusItems.some(item => 
                        item.product.name.toLowerCase().includes(searchTerm)
                    );
                    const matchReason = bonus.reason && bonus.reason.toLowerCase().includes(searchTerm);
                    const matchCashier = bonus.cashier && bonus.cashier.name.toLowerCase().includes(searchTerm);
                    const matchNumber = bonus.bonus_number && bonus.bonus_number.toLowerCase().includes(searchTerm);
                    
                    return matchProduct || matchReason || matchCashier || matchNumber;
                });
            }
            
            if (bonusHistory.length === 0) {
                showNotification('Tidak ada data bonus untuk diekspor', 'warning');
                return;
            }

            // Prepare CSV data
            const headers = ['Tanggal', 'Nomor Bonus', 'Produk', 'Quantity', 'Nilai', 'Alasan', 'Kasir', 'Outlet', 'Status'];
            const csvData = [headers.join(',')];
            
            bonusHistory.forEach(bonus => {
                bonus.bonusItems?.forEach(item => {
                    const row = [
                        new Date(bonus.created_at).toLocaleString('id-ID'),
                        `"${bonus.bonus_number || ''}"`,
                        `"${item.product?.name || 'Unknown'}"`,
                        item.quantity,
                        item.bonus_value,
                        `"${bonus.reason || ''}"`,
                        `"${bonus.cashier?.name || 'Unknown'}"`,
                        `"${bonus.outlet?.name || 'Unknown'}"`,
                        bonus.status || 'approved'
                    ];
                    csvData.push(row.join(','));
                });
            });

            // Download CSV
            const blob = new Blob([csvData.join('\n')], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);
            link.setAttribute('href', url);
            link.setAttribute('download', `bonus_report_${new Date().toISOString().split('T')[0]}.csv`);
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);

            showNotification('Data bonus berhasil diekspor', 'success');
            
        } catch (error) {
            console.error('Error exporting bonus data:', error);
            showNotification('Gagal mengekspor data bonus', 'error');
        }
    }

    // Approve bonus tracking
    async approveBonusTracking(bonusId) {
        try {
            const notes = prompt('Catatan persetujuan (opsional):');
            if (notes === null) return; // User cancelled
            
            const token = localStorage.getItem('token') || document.querySelector('meta[name="csrf-token"]')?.content;
            
            const response = await fetch(`/api/bonus/approve/${bonusId}`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    notes: notes
                })
            });
            
            const data = await response.json();
            
            if (response.ok && data.success) {
                showNotification('Bonus berhasil disetujui', 'success');
                this.loadBonusData(); // Reload data
            } else {
                throw new Error(data.message || 'Gagal menyetujui bonus');
            }
            
        } catch (error) {
            console.error('Error approving bonus:', error);
            showNotification('Gagal menyetujui bonus: ' + error.message, 'error');
        }
    }

    // Reject bonus tracking
    async rejectBonusTracking(bonusId) {
        try {
            const reason = prompt('Alasan penolakan:');
            if (!reason) return;
            
            const token = localStorage.getItem('token') || document.querySelector('meta[name="csrf-token"]')?.content;
            
            const response = await fetch(`/api/bonus/reject/${bonusId}`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    reason: reason
                })
            });
            
            const data = await response.json();
            
            if (response.ok && data.success) {
                showNotification('Bonus berhasil ditolak', 'info');
                this.loadBonusData(); // Reload data
            } else {
                throw new Error(data.message || 'Gagal menolak bonus');
            }
            
        } catch (error) {
            console.error('Error rejecting bonus:', error);
            showNotification('Gagal menolak bonus: ' + error.message, 'error');
        }
    }
}

// Global instance
const bonusTrackingManager = new BonusTrackingManager();