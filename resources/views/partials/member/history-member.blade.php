<!-- Modal History Member -->
<div id="modalHistoryMember" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-6xl max-h-[90vh] flex flex-col">
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-4 border-b">
            <div class="flex items-center gap-3">
                <i data-lucide="history" class="w-6 h-6 text-green-500"></i>
                <h3 class="text-lg font-semibold text-gray-900">Riwayat Transaksi Member</h3>
            </div>
            <button onclick="closeModalHistory()" class="p-1 rounded-full hover:bg-gray-100">
                <i data-lucide="x" class="w-5 h-5 text-gray-500"></i>
            </button>
        </div>
        
        <!-- Filter Section -->
        <div class="p-4 border-b">
            <div class="flex flex-col md:flex-row md:items-center gap-4">
                <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
                        <input type="date" id="historyDateFrom" class="w-full px-3 py-2 border rounded-md">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                        <input type="date" id="historyDateTo" class="w-full px-3 py-2 border rounded-md">
                    </div>
                </div>
                <div class="flex items-end">
                    <button onclick="loadMemberHistory()" class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 flex items-center gap-2">
                        <i data-lucide="filter" class="w-4 h-4"></i> Filter
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 p-4 border-b">
            <div class="bg-green-50 p-4 rounded-lg">
                <div class="text-sm text-green-600 font-medium">jumlah Transaksi</div>
                <div id="totalOrders" class="text-2xl font-bold text-green-700">0</div>
            </div>
            <div class="bg-green-50 p-4 rounded-lg">
                <div class="text-sm text-green-600 font-medium">Total Transaksi</div>
                <div id="totalRevenue" class="text-2xl font-bold text-green-700">Rp 0</div>
            </div>
            <div class="bg-blue-50 p-4 rounded-lg">
                <div class="text-sm text-blue-600 font-medium">Rata-rata Transaksi</div>
                <div id="avgOrderValue" class="text-2xl font-bold text-blue-700">Rp 0</div>
            </div>
            <div class="bg-purple-50 p-4 rounded-lg">
                <div class="text-sm text-purple-600 font-medium">Total Item Terjual</div>
                <div id="totalItemsSold" class="text-2xl font-bold text-purple-700">0</div>
            </div>
        </div>
        
        <!-- Table Container -->
        <div class="flex-1 overflow-auto p-4">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="text-left bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 font-medium">Tanggal</th>
                            <th class="px-4 py-2 font-medium">Invoice</th>
                            <th class="px-4 py-2 font-medium">Outlet</th>
                            <th class="px-4 py-2 font-medium">Item</th>
                            <th class="px-4 py-2 font-medium">Metode Bayar</th>
                            <th class="px-4 py-2 font-medium">Status</th>
                            <th class="px-4 py-2 font-medium text-right">Total</th>
                            <th class="px-4 py-2 font-medium">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="historyTableBody" class="divide-y">
                        <!-- Data will be filled dynamically -->
                    </tbody>
                </table>
            </div>
            
            <!-- Empty State -->
            <div id="historyEmptyState" class="flex flex-col items-center justify-center py-12 text-center">
                <i data-lucide="package" class="w-12 h-12 text-gray-400 mb-4"></i>
                <h4 class="text-lg font-medium text-gray-500">Tidak ada riwayat transaksi</h4>
                <p class="text-sm text-gray-400 mt-1">Pilih rentang tanggal untuk melihat riwayat transaksi</p>
            </div>
            
            <!-- Loading State -->
            <div id="historyLoading" class="hidden flex-col items-center justify-center py-12">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-green-500 mb-4"></div>
                <p class="text-sm text-gray-500">Memuat data...</p>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Order -->
<div id="modalDetailOrder" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] flex flex-col">
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-4 border-b">
            <div class="flex items-center gap-3">
                <i data-lucide="receipt" class="w-6 h-6 text-green-500"></i>
                <h3 class="text-lg font-semibold text-gray-900">Detail Transaksi</h3>
            </div>
            <button onclick="closeModalDetailOrder()" class="p-1 rounded-full hover:bg-gray-100">
                <i data-lucide="x" class="w-5 h-5 text-gray-500"></i>
            </button>
        </div>
        
        <!-- Order Info -->
        <div class="p-4 border-b">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <div class="text-sm text-gray-500">Nomor Invoice</div>
                    <div id="detailInvoiceNumber" class="font-medium">-</div>
                </div>
                <div>
                    <div class="text-sm text-gray-500">Tanggal</div>
                    <div id="detailOrderDate" class="font-medium">-</div>
                </div>
                <div>
                    <div class="text-sm text-gray-500">Outlet</div>
                    <div id="detailOutlet" class="font-medium">-</div>
                </div>
                <div>
                    <div class="text-sm text-gray-500">Kasir</div>
                    <div id="detailCashier" class="font-medium">-</div>
                </div>
                <div>
                    <div class="text-sm text-gray-500">Status</div>
                    <div id="detailStatus" class="font-medium">-</div>
                </div>
                <div>
                    <div class="text-sm text-gray-500">Metode Pembayaran</div>
                    <div id="detailPaymentMethod" class="font-medium">-</div>
                </div>
            </div>
        </div>
        
        <!-- Items List -->
        <div class="flex-1 overflow-auto p-4">
            <h4 class="font-medium text-gray-700 mb-3">Item Pembelian</h4>
            <div id="detailItemsList" class="space-y-4">
                <!-- Items will be filled dynamically -->
            </div>
        </div>
        
        <!-- Order Summary -->
        <div class="p-4 border-t bg-gray-50">
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-gray-600">Subtotal</span>
                    <span id="detailSubtotal" class="font-medium">Rp 0</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Diskon</span>
                    <span id="detailDiscount" class="font-medium">Rp 0</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Pajak</span>
                    <span id="detailTax" class="font-medium">Rp 0</span>
                </div>
                <div class="flex justify-between border-t pt-2">
                    <span class="text-gray-800 font-semibold">Total</span>
                    <span id="detailTotal" class="text-green-600 font-bold">Rp 0</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Total Dibayar</span>
                    <span id="detailTotalPaid" class="font-medium">Rp 0</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Kembalian</span>
                    <span id="detailChange" class="font-medium">Rp 0</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Global variables for history modal
    let currentMemberId = null;
    let currentOrderDetail = null;
    
    // Open history modal
    function historyMember(memberId) {
        currentMemberId = memberId;
        
        // Set default dates (today)
        const today = new Date();
        const formattedToday = today.toISOString().split('T')[0];
        document.getElementById('historyDateFrom').value = formattedToday;
        document.getElementById('historyDateTo').value = formattedToday;
        
        // Open modal
        const modal = document.getElementById('modalHistoryMember');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        
        // Load initial data
        loadMemberHistory();
    }
    
    // Close history modal
    function closeModalHistory() {
        const modal = document.getElementById('modalHistoryMember');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        currentMemberId = null;
    }
    
    // Close detail order modal
    function closeModalDetailOrder() {
        const modal = document.getElementById('modalDetailOrder');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        currentOrderDetail = null;
    }
    
    // Fungsi untuk memuat riwayat member (yang sudah ada)
    async function loadMemberHistory() {
        if (!currentMemberId) return;
        
        const dateFrom = document.getElementById('historyDateFrom').value;
        const dateTo = document.getElementById('historyDateTo').value;
        
        if (!dateFrom || !dateTo) {
            showAlert('error', 'Harap pilih rentang tanggal');
            return;
        }
        
        // Show loading state
        document.getElementById('historyLoading').classList.remove('hidden');
        document.getElementById('historyEmptyState').classList.add('hidden');
        document.getElementById('historyTableBody').innerHTML = '';
        
        try {
            const token = localStorage.getItem('token');
            const response = await fetch(`/api/orders/history?date_from=${dateFrom}&date_to=${dateTo}&member_id=${currentMemberId}`, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });
            
            const data = await response.json();
            
            if (response.ok) {
                // Simpan data di localStorage untuk digunakan nanti
                localStorage.setItem('memberHistoryData', JSON.stringify(data.data));
                
                renderHistoryData(data.data);
            } else {
                throw new Error(data.message || 'Gagal memuat riwayat transaksi');
            }
        } catch (error) {
            console.error('Error loading member history:', error);
            showAlert('error', error.message);
            
            // Show empty state
            document.getElementById('historyEmptyState').classList.remove('hidden');
        } finally {
            document.getElementById('historyLoading').classList.add('hidden');
        }
    }

    // Fungsi untuk merender data riwayat (yang sudah ada)
    // function renderHistoryData(data) {
    //     const tableBody = document.getElementById('historyTableBody');
    //     const emptyState = document.getElementById('historyEmptyState');
        
    //     if (!data.orders || data.orders.length === 0) {
    //         tableBody.innerHTML = '';
    //         emptyState.classList.remove('hidden');
            
    //         // Update summary cards
    //         document.getElementById('totalOrders').textContent = '0';
    //         document.getElementById('totalRevenue').textContent = 'Rp 0';
    //         document.getElementById('avgOrderValue').textContent = 'Rp 0';
    //         document.getElementById('totalItemsSold').textContent = '0';
    //         return;
    //     }
        
    //     emptyState.classList.add('hidden');
    //     tableBody.innerHTML = '';
        
    //     // Update summary cards
    //     document.getElementById('totalOrders').textContent = data.total_orders;
    //     document.getElementById('totalRevenue').textContent = formatCurrency(data.total_revenue);
    //     document.getElementById('avgOrderValue').textContent = formatCurrency(data.average_order_value);
    //     document.getElementById('totalItemsSold').textContent = data.total_items_sold;
        
    //     // Render each order
    //     data.orders.forEach(order => {
    //         const row = document.createElement('tr');
    //         row.className = 'hover:bg-gray-50';
            
    //         // Calculate total items
    //         const totalItems = order.items.reduce((sum, item) => sum + parseInt(item.quantity), 0);
            
    //         row.innerHTML = `
    //             <td class="px-4 py-3 whitespace-nowrap">${order.created_at}</td>
    //             <td class="px-4 py-3 whitespace-nowrap">${order.order_number}</td>
    //             <td class="px-4 py-3 whitespace-nowrap">${order.outlet}</td>
    //             <td class="px-4 py-3 whitespace-nowrap">${totalItems} item</td>
    //             <td class="px-4 py-3 whitespace-nowrap">
    //                 <span class="capitalize">${order.payment_method}</span>
    //             </td>
    //             <td class="px-4 py-3 whitespace-nowrap">
    //                 <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${
    //                     order.status === 'completed' ? 'bg-green-100 text-green-800' : 
    //                     order.status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
    //                     'bg-red-100 text-red-800'
    //                 }">
    //                     ${order.status === 'completed' ? 'Selesai' : 
    //                     order.status === 'pending' ? 'Pending' : 'Dibatalkan'}
    //                 </span>
    //             </td>
    //             <td class="px-4 py-3 whitespace-nowrap text-right font-medium">${formatCurrency(order.total)}</td>
    //             <td class="px-4 py-3 whitespace-nowrap text-right">
    //                 <button onclick="showOrderDetail(${order.id})" class="text-green-600 hover:text-green-800 flex items-center gap-1">
    //                     <i data-lucide="eye" class="w-4 h-4"></i> Detail
    //                 </button>
    //             </td>
    //         `;
            
    //         tableBody.appendChild(row);
    //     });
        
    //     // Refresh Lucide icons
    //     if (window.lucide) window.lucide.createIcons({ icons });
    // }
    
    // Render history data to table
    function renderHistoryData(data) {
        const tableBody = document.getElementById('historyTableBody');
        const emptyState = document.getElementById('historyEmptyState');
        
        if (!data.orders || data.orders.length === 0) {
            tableBody.innerHTML = '';
            emptyState.classList.remove('hidden');
            
            // Update summary cards
            document.getElementById('totalOrders').textContent = '0';
            document.getElementById('totalRevenue').textContent = 'Rp 0';
            document.getElementById('avgOrderValue').textContent = 'Rp 0';
            document.getElementById('totalItemsSold').textContent = '0';
            return;
        }
        
        emptyState.classList.add('hidden');
        tableBody.innerHTML = '';
        
        // Update summary cards
        document.getElementById('totalOrders').textContent = data.total_orders;
        document.getElementById('totalRevenue').textContent = formatCurrency(data.total_revenue);
        document.getElementById('avgOrderValue').textContent = formatCurrency(data.average_order_value);
        document.getElementById('totalItemsSold').textContent = data.total_items_sold;
        
        // Render each order
        data.orders.forEach(order => {
            const row = document.createElement('tr');
            row.className = 'hover:bg-gray-50';
            
            // Calculate total items
            const totalItems = order.items.reduce((sum, item) => sum + parseInt(item.quantity), 0);
            
            row.innerHTML = `
                <td class="px-4 py-3 whitespace-nowrap">${order.created_at}</td>
                <td class="px-4 py-3 whitespace-nowrap">${order.order_number}</td>
                <td class="px-4 py-3 whitespace-nowrap">${order.outlet}</td>
                <td class="px-4 py-3 whitespace-nowrap">${totalItems} item</td>
                <td class="px-4 py-3 whitespace-nowrap">
                    <span class="capitalize">${order.payment_method}</span>
                </td>
                <td class="px-4 py-3 whitespace-nowrap">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${
                        order.status === 'completed' ? 'bg-green-100 text-green-800' : 
                        order.status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                        'bg-red-100 text-red-800'
                    }">
                        ${order.status === 'completed' ? 'Selesai' : 
                          order.status === 'pending' ? 'Pending' : 'Dibatalkan'}
                    </span>
                </td>
                <td class="px-4 py-3 whitespace-nowrap text-right font-medium">${formatCurrency(order.total)}</td>
                <td class="px-4 py-3 whitespace-nowrap text-right">
                    <button onclick="showOrderDetail(${order.id})" class="text-green-600 hover:text-green-800 flex items-center gap-1">
                        <i data-lucide="eye" class="w-4 h-4"></i> Detail
                    </button>
                </td>
            `;
            
            tableBody.appendChild(row);
        });
        
        // Refresh Lucide icons
        if (window.lucide) window.lucide.createIcons({ icons });
    }
    
    // Fungsi untuk menampilkan detail order
    function showOrderDetail(orderId) {
        try {
            // Cari order dari data yang sudah ada di riwayat
            const historyData = JSON.parse(localStorage.getItem('memberHistoryData'));
            const order = historyData?.orders?.find(x => x.id == orderId);
            
            if (!order) {
                throw new Error('Transaksi tidak ditemukan');
            }

            renderOrderDetailImproved(order);
            
            // Buka modal
            const modal = document.getElementById('modalDetailOrder');
            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }
        } catch (error) {
            console.error('Error:', error);
            showAlert('error', error.message);
        }
    }
    
    // Fungsi untuk merender detail order
    function renderOrderDetailImproved(order) {
        // Helper function untuk handle number
        const safeNumber = (value) => {
            if (value === null || value === undefined) return 0;
            if (typeof value === 'number') return isNaN(value) ? 0 : value;
            if (typeof value === 'string') {
                const num = parseFloat(value.replace(/[^0-9.-]/g, ''));
                return isNaN(num) ? 0 : num;
            }
            return 0;
        };

        // Format uang
        const formatUang = (value) => {
            return 'Rp ' + safeNumber(value).toLocaleString('id-ID');
        };

        // Update UI
        document.getElementById('detailInvoiceNumber').textContent = order.order_number || '-';
        document.getElementById('detailOrderDate').textContent = order.created_at || '-';
        
        // Status
        const statusMap = {
            'completed': 'Selesai',
            'pending': 'Pending',
            'cancelled': 'Dibatalkan'
        };
        const statusText = statusMap[order.status] || order.status || '-';
        document.getElementById('detailStatus').textContent = statusText;

        // Items
        const itemsContainer = document.getElementById('detailItemsList');
        itemsContainer.innerHTML = '';
        
        if (order.items && order.items.length > 0) {
            order.items.forEach(item => {
                const itemEl = document.createElement('div');
                itemEl.className = 'flex justify-between items-start border-b pb-3';
                itemEl.innerHTML = `
                    <div>
                        <div class="font-medium">${item.product || '-'}</div>
                        <div class="text-sm text-gray-500">${item.sku || ''} • ${item.quantity || 0} ${item.unit || ''}</div>
                    </div>
                    <div class="text-right">
                        <div class="font-medium">${formatUang(item.price)}</div>
                        <div class="text-sm">${item.quantity || 0} × ${formatUang(item.price)}</div>
                    </div>
                `;
                itemsContainer.appendChild(itemEl);
            });
        } else {
            itemsContainer.innerHTML = '<div class="text-center py-4 text-gray-500">Tidak ada item</div>';
        }

        // Summary
        document.getElementById('detailSubtotal').textContent = formatUang(order.subtotal);
        document.getElementById('detailDiscount').textContent = formatUang(order.discount);
        document.getElementById('detailTax').textContent = formatUang(order.tax);
        document.getElementById('detailTotal').textContent = formatUang(order.total);
        document.getElementById('detailTotalPaid').textContent = formatUang(order.total_paid || 0);
        document.getElementById('detailChange').textContent = formatUang(order.change || 0);
        
        // Payment method
        const paymentMethod = order.payment_method === 'cash' ? 'Tunai' : 
                            order.payment_method === 'transfer' ? 'Transfer' : 
                            order.payment_method || '-';
        document.getElementById('detailPaymentMethod').textContent = paymentMethod;
        
        // Outlet dan kasir
        document.getElementById('detailOutlet').textContent = order.outlet || '-';
        document.getElementById('detailCashier').textContent = order.user || '-';
    }
    
    // Helper function to format currency
    function formatCurrency(amount) {
        if (isNaN(amount)) amount = 0;
        return 'Rp ' + parseFloat(amount).toLocaleString('id-ID');
    }
</script>