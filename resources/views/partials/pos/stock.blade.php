<!-- Stock Adjustment Modal -->
<div id="stockModal" class="modal fixed inset-0 z-50 hidden">
    <div class="modal-overlay absolute w-full h-full bg-gray-900 opacity-50"></div>
    <div class="modal-container bg-white w-11/12 md:max-w-3xl mx-auto rounded-lg shadow-lg z-50 overflow-y-auto relative top-1/2 transform -translate-y-1/2">
        <div class="modal-content py-4 text-left px-6">
            <div class="flex justify-between items-center pb-3">
                <p class="text-xl font-bold">Sesuaikan Stok</p>
                <button onclick="closeModal('stockModal')" class="modal-close cursor-pointer z-50 text-gray-500 hover:text-gray-700">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <p class="text-base text-gray-600 mb-4">Sesuaikan stok produk. Perubahan memerlukan persetujuan admin</p>

            <!-- Tabs -->
            <div class="flex bg-gray-100 rounded-lg p-1 mb-6 w-fit">
                <button id="adjustTab" class="tab-button active px-6 py-2 rounded-lg font-medium text-base text-green-500 bg-white shadow">Sesuaikan</button>
                <button id="historyTab" class="tab-button px-6 py-2 rounded-lg font-medium text-base text-gray-500 hover:text-gray-700">Riwayat</button>
            </div>

            <!-- Adjust Content -->
            <div id="adjustContent" class="tab-content">
                <div class="mb-6">
                    <div class="mb-4">
                        <label class="block text-base font-medium text-gray-700 mb-2">Nama Produk</label>
                        <div class="relative">
                            <select 
                                id="product_id" 
                                class="hidden" <!-- Sembunyikan select asli -->
                            >
                                <option value="">Pilih produk</option>
                            </select>
                            
                            <!-- Custom dropdown trigger -->
                            <div 
                                id="productDropdownTrigger" 
                                class="w-full px-4 py-2.5 text-base border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 bg-white flex justify-between items-center cursor-pointer"
                                onclick="toggleProductDropdown()"
                            >
                                <span id="selectedProductText">Pilih produk</span>
                                <i data-lucide="chevron-down" class="w-5 h-5 text-gray-400"></i>
                            </div>
                            
                            <!-- Custom dropdown content -->
                            <div 
                                id="productDropdown" 
                                class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-96 overflow-hidden hidden"
                            >
                                <!-- Search box -->
                                <div class="p-2 border-b">
                                    <div class="relative">
                                        <input 
                                            type="text" 
                                            id="productSearch" 
                                            class="w-full pl-10 pr-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500" 
                                            placeholder="Cari produk..."
                                            oninput="filterDropdownProducts()"
                                        >
                                        <div class="absolute left-3 top-2.5 text-gray-400">
                                            <i data-lucide="search" class="w-4 h-4"></i>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Product list -->
                                <div id="productList" class="overflow-y-auto max-h-80">
                                    <div class="p-4 text-center text-gray-500">
                                        <i data-lucide="loader" class="w-5 h-5 animate-spin mx-auto"></i> Memuat produk...
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-base font-medium text-gray-700 mb-2">Nilai + / -</label>
                            <input type="number" step="0.1" id="quantity_change" class="w-full px-4 py-2.5 text-base border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500" placeholder="Masukkan nilai (contoh: 5 atau 2.5)">
                        </div>
                        <div>
                            <label class="block text-base font-medium text-gray-700 mb-2">Tipe</label>
                            <div class="relative">
                                <select id="adjust_type" class="w-full px-4 py-2.5 text-base border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 appearance-none bg-white pr-10">
                                    <option value="">Pilih tipe</option>
                                    <option value="shipment">Kiriman Pabrik</option>
                                    <option value="purchase">Pembelian</option>
                                    <option value="sale">Penjualan</option>
                                    <option value="adjustment">Penyesuaian</option>
                                    <option value="other">Lainnya</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <i data-lucide="chevron-down" class="w-5 h-5 text-gray-400"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-base font-medium text-gray-700 mb-2">Keterangan</label>
                        <textarea id="notes" class="w-full px-4 py-2.5 text-base border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500" rows="3" placeholder="Masukkan keterangan (opsional)"></textarea>
                    </div>
                </div>

                <div class="flex justify-end pt-2 space-x-4">
                    <button onclick="closeModal('stockModal')" class="px-6 py-2.5 text-base bg-gray-300 rounded-lg hover:bg-gray-400 flex items-center gap-2">
                        <i data-lucide="x" class="w-4 h-4"></i> Batal
                    </button>
                    <button onclick="submitStockAdjustment()" class="px-6 py-2.5 text-base bg-green-500 text-white rounded-lg hover:bg-green-600 flex items-center gap-2">
                        <i data-lucide="check" class="w-4 h-4"></i> Sesuaikan Stok
                    </button>
                </div>
            </div>

            <!-- History Content -->
            <div id="historyContent" class="tab-content hidden">
                <div class="mb-4">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-700">Riwayat Penyesuaian Stok</h3>
                        
                        <div class="flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-2 mt-2 md:mt-0">
                            <div class="flex items-center">
                                <label class="text-sm font-medium text-gray-700 mr-2">Dari:</label>
                                <div class="relative">
                                    <input type="date" id="date_from" class="px-3 py-1.5 text-sm border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500">
                                </div>
                            </div>
                            <div class="flex items-center">
                                <label class="text-sm font-medium text-gray-700 mr-2">Sampai:</label>
                                <div class="relative">
                                    <input type="date" id="date_to" class="px-3 py-1.5 text-sm border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500">
                                </div>
                            </div>
                            <button onclick="loadInventoryHistory()" class="px-4 py-1.5 text-sm bg-green-500 text-white rounded-lg hover:bg-green-600 flex items-center gap-1">
                                <i data-lucide="filter" class="w-4 h-4"></i> Filter
                            </button>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white">
                        <thead>
                            <tr class="border-b">
                                <th class="px-4 py-3 text-left text-base font-medium text-gray-700">Tanggal</th>
                                <th class="px-4 py-3 text-left text-base font-medium text-gray-700">Nama Produk</th>
                                <th class="px-4 py-3 text-left text-base font-medium text-gray-700">Perubahan</th>
                                <th class="px-4 py-3 text-left text-base font-medium text-gray-700">Tipe</th>
                                <th class="px-4 py-3 text-left text-base font-medium text-gray-700">Keterangan</th>
                                <th class="px-4 py-3 text-left text-base font-medium text-gray-700">Status</th>
                            </tr>
                        </thead>
                        <tbody id="historyTableBody">
                            <tr class="border-b">
                                <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                    <div class="flex justify-center items-center gap-2">
                                        <i data-lucide="loader" class="w-5 h-5 animate-spin text-gray-400"></i>
                                        Memuat data...
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Notification Container -->
<div id="notification-container" class="fixed top-4 right-4 z-50 space-y-3 w-80"></div>

<!-- Include Lucide Icons -->


<script>
    // Initialize Lucide Icons
    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons({ icons });
    });

    // Modern notification function
    function showNotification(type, title, message) {
        const container = document.getElementById('notification-container');
        const icons = {
            success: 'check-circle',
            error: 'x-circle',
            warning: 'alert-triangle',
            info: 'info'
        };
        const colors = {
            success: 'bg-green-100 border-green-500 text-green-700',
            error: 'bg-red-100 border-red-500 text-red-700',
            warning: 'bg-yellow-100 border-yellow-500 text-yellow-700',
            info: 'bg-blue-100 border-blue-500 text-blue-700'
        };
        
        const notification = document.createElement('div');
        notification.className = `p-4 rounded-lg border-l-4 ${colors[type]} shadow-lg transform transition-all duration-300 ease-in-out translate-x-96 opacity-0`;
        notification.innerHTML = `
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i data-lucide="${icons[type]}" class="w-5 h-5 mt-0.5"></i>
                </div>
                <div class="ml-3 w-0 flex-1 pt-0.5">
                    <p class="font-medium">${title}</p>
                    <p class="mt-1 text-sm">${message}</p>
                </div>
                <div class="ml-4 flex-shrink-0 flex">
                    <button class="rounded-md focus:outline-none" onclick="this.parentElement.parentElement.parentElement.remove()">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>
        `;
        
        container.appendChild(notification);
        setTimeout(() => {
            notification.classList.remove('translate-x-96', 'opacity-0');
            notification.classList.add('translate-x-0', 'opacity-100');
        }, 10);
        
        setTimeout(() => {
            notification.classList.add('translate-x-96', 'opacity-0');
            setTimeout(() => notification.remove(), 300);
        }, 5000);
        
        lucide.createIcons({ icons });
    }

    // Function to override openModal - will be called after all scripts loaded
    function setupModalOverride() {
        console.log('Setting up modal override...');
        const originalOpenModal = window.openModal;
        console.log('Original openModal before override:', originalOpenModal);
        
        window.openModal = function(modalId) {
            console.log('OVERRIDDEN openModal called with:', modalId);
            
            // Call original function first
            if (originalOpenModal) {
                console.log('Calling original openModal');
                originalOpenModal(modalId);
            } else {
                console.log('No original openModal, showing modal directly');
                document.getElementById(modalId).classList.remove('hidden');
            }
            
            // Add stock modal specific logic
            if (modalId === 'stockModal') {
                console.log('Stock modal opened, initializing...');
                console.log('About to call setDefaultDates...');
                setDefaultDates();
                console.log('About to call loadProducts...');
                console.log('loadStockProducts function exists:', typeof loadStockProducts);
                console.log('loadStockProducts function:', loadStockProducts);
                try {
                    if (typeof loadStockProducts === 'function') {
                        loadStockProducts();
                        console.log('loadStockProducts called successfully');
                    } else {
                        console.error('loadStockProducts is not a function!');
                    }
                } catch (error) {
                    console.error('Error calling loadStockProducts:', error);
                }
                
                console.log('Checking if history tab is visible...');
                const historyContent = document.getElementById('historyContent');
                console.log('historyContent element:', historyContent);
                
                if (historyContent && !historyContent.classList.contains('hidden')) {
                    console.log('History tab is visible, loading inventory history...');
                    loadInventoryHistory();
                } else {
                    console.log('History tab is hidden or not found');
                }
            }
        };
        
        console.log('Override complete. New openModal:', window.openModal);
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
    }
    
    // Tab switching functionality
    document.addEventListener('DOMContentLoaded', function() {
        const adjustTab = document.getElementById('adjustTab');
        const historyTab = document.getElementById('historyTab');
        
        if (adjustTab) {
            adjustTab.addEventListener('click', function () {
                this.classList.add('active', 'text-green-500', 'bg-white', 'shadow');
                document.getElementById('historyTab').classList.remove('active', 'text-green-500', 'bg-white', 'shadow');
                document.getElementById('historyTab').classList.add('text-gray-500');
                document.getElementById('adjustContent').classList.remove('hidden');
                document.getElementById('historyContent').classList.add('hidden');
            });
        }
        
        if (historyTab) {
            historyTab.addEventListener('click', function () {
                this.classList.add('active', 'text-green-500', 'bg-white', 'shadow');
                document.getElementById('adjustTab').classList.remove('active', 'text-green-500', 'bg-white', 'shadow');
                document.getElementById('adjustTab').classList.add('text-gray-500');
                document.getElementById('historyContent').classList.remove('hidden');
                document.getElementById('adjustContent').classList.add('hidden');
                
                loadInventoryHistory();
            });
        }
    });
    
    // Get outlet ID from local storage or parent component
    function getOutletId() {
        // Try multiple localStorage keys in order of preference
        let outletId = localStorage.getItem('outlet_id') || 
                      localStorage.getItem('selectedOutletId');
        
        // If still no outlet ID, try to get from global POS config
        if (!outletId && typeof outletInfo !== 'undefined' && outletInfo.id) {
            outletId = outletInfo.id;
        }
        
        // Last resort: default to outlet 1
        if (!outletId) {
            outletId = '1';
            localStorage.setItem('outlet_id', '1'); // Set default
        }
        
        console.log('Got outlet ID:', outletId);
        return outletId;
    }
    
    // Get auth token from localStorage
    function getToken() {
        // Try different possible token keys
        let token = localStorage.getItem('token') || 
                   localStorage.getItem('auth_token') ||
                   localStorage.getItem('access_token');
        
        // Try to get from global POS config
        if (!token && typeof POS_CONFIG !== 'undefined' && POS_CONFIG.API_TOKEN) {
            token = POS_CONFIG.API_TOKEN;
        }
        
        if (!token) {
            console.error('No authentication token found');
            showNotification('error', 'Error', 'Anda tidak terautentikasi. Silakan login ulang.');
            return null;
        }
        
        console.log('Got token (first 10 chars):', token.substring(0, 10) + '...');
        return token;
    }

    let allProducts = [];

    // Format quantity to handle decimal display
    function formatQuantity(quantity) {
        const num = parseFloat(quantity || 0);
        // Show decimal if it has decimal places, otherwise show as integer
        return num % 1 === 0 ? num.toString() : num.toFixed(1);
    }

    function loadStockProducts() {
        console.log('=== loadStockProducts function called ===');
        console.log('loadStockProducts function is running...');
        try {
            const outletId = getOutletId();
            const token = getToken();

            console.log('Loading products for outlet:', outletId);
            console.log('Token available:', !!token);
        
        if (!outletId || !token) {
            console.error('Missing outlet ID or token:', { outletId, hasToken: !!token });
            const productList = document.getElementById('productList');
            productList.innerHTML = '<div class="p-4 text-center text-red-500">Error: Missing outlet ID or authentication token</div>';
            return;
        }
        
        const productList = document.getElementById('productList');
        productList.innerHTML = '<div class="p-4 text-center text-gray-500"><i data-lucide="loader" class="w-5 h-5 animate-spin mx-auto"></i> Memuat produk...</div>';
        lucide.createIcons({ icons });
    
        const apiUrl = `/api/products/outlet/${outletId}`;
        console.log('API URL:', apiUrl);
        
        fetch(apiUrl, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`
            }
        })
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response headers:', Object.fromEntries(response.headers.entries()));
            
            if (!response.ok) {
                return response.text().then(text => {
                    console.error('Error response text:', text);
                    throw new Error(`HTTP error! Status: ${response.status} - ${text}`);
                });
            }
            return response.json();
        })
        .then(data => {
            console.log('API Response data:', data);
            
            if (data.success && data.data) {
                allProducts = Array.isArray(data.data) ? 
                    data.data.sort((a, b) => a.name.localeCompare(b.name)) : 
                    [];
                
                console.log('Processed products:', allProducts.length);
                
                const selectElement = document.getElementById('product_id');
                selectElement.innerHTML = '<option value="">Pilih produk</option>';
                
                if (allProducts.length === 0) {
                    productList.innerHTML = '<div class="p-4 text-center text-gray-500">Tidak ada produk di outlet ini</div>';
                    return;
                }
                
                allProducts.forEach(product => {
                    const option = document.createElement('option');
                    option.value = product.id;
                    option.textContent = `${product.name} (${product.sku || 'No SKU'}) - Stok: ${formatQuantity(product.quantity || 0)}`;
                    selectElement.appendChild(option);
                });
                
                filterDropdownProducts();
            } else {
                console.error('API returned unsuccessful response:', data);
                showNotification('error', 'Error', data.message || 'Format data tidak sesuai');
                productList.innerHTML = '<div class="p-4 text-center text-gray-500">Gagal memuat produk</div>';
            }
        })
        .catch(error => {
            console.error('Error fetching products:', error);
            showNotification('error', 'Error', 'Gagal memuat daftar produk: ' + error.message);
            productList.innerHTML = '<div class="p-4 text-center text-red-500">Error memuat produk: ' + error.message + '</div>';
        });
        
        } catch (error) {
            console.error('Error in loadStockProducts function:', error);
            const productList = document.getElementById('productList');
            if (productList) {
                productList.innerHTML = '<div class="p-4 text-center text-red-500">JavaScript Error: ' + error.message + '</div>';
            }
        }
        console.log('=== loadStockProducts function completed ===');
    }

    function filterDropdownProducts() {
        const searchTerm = document.getElementById('productSearch').value.toLowerCase();
        const productList = document.getElementById('productList');
        
        if (allProducts.length === 0) {
            productList.innerHTML = '<div class="p-4 text-center text-gray-500">Tidak ada produk</div>';
            return;
        }
        
        const filteredProducts = allProducts.filter(product => 
            product.name.toLowerCase().includes(searchTerm) || 
            (product.sku && product.sku.toLowerCase().includes(searchTerm))
        );
        
        if (filteredProducts.length === 0) {
            productList.innerHTML = '<div class="p-4 text-center text-gray-500">Produk tidak ditemukan</div>';
            return;
        }
        
        let html = '';
        filteredProducts.forEach(product => {
            html += `
                <div 
                    class="px-4 py-3 hover:bg-gray-100 cursor-pointer border-b border-gray-100"
                    onclick="selectProduct('${product.id}', '${product.name} (${product.sku || 'No SKU'}) - Stok: ${formatQuantity(product.quantity || 0)}')"
                >
                    <div class="font-medium">${product.name}</div>
                    <div class="text-sm text-gray-500 flex justify-between mt-1">
                        <span>${product.sku || 'No SKU'}</span>
                        <span>Stok: ${formatQuantity(product.quantity || 0)}</span>
                    </div>
                </div>
            `;
        });
        
        productList.innerHTML = html;
    }

    function toggleProductDropdown() {
        const dropdown = document.getElementById('productDropdown');
        if (dropdown.classList.contains('hidden')) {
            dropdown.classList.remove('hidden');
            document.getElementById('productSearch').focus();
        } else {
            dropdown.classList.add('hidden');
        }
    }

    function selectProduct(productId, productText) {
        document.getElementById('product_id').value = productId;
        document.getElementById('selectedProductText').textContent = productText;
        document.getElementById('productDropdown').classList.add('hidden');
        document.getElementById('productSearch').value = '';
        
        // Set validation based on selected product's unit type
        const selectedProduct = allProducts.find(p => p.id == productId);
        const quantityInput = document.getElementById('quantity_change');
        
        if (selectedProduct && quantityInput) {
            const unitType = selectedProduct.unit_type || 'pcs';
            if (unitType === 'meter') {
                quantityInput.step = '0.1';
                quantityInput.setAttribute('data-unit-type', 'meter');
            } else {
                quantityInput.step = '1';
                quantityInput.setAttribute('data-unit-type', unitType);
            }
            
            // Add validation event listener
            quantityInput.removeEventListener('input', validateStockQuantity);
            quantityInput.addEventListener('input', validateStockQuantity);
        }
        
        filterDropdownProducts();
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        const dropdown = document.getElementById('productDropdown');
        const trigger = document.getElementById('productDropdownTrigger');
        
        if (!dropdown.contains(event.target) && !trigger.contains(event.target)) {
            dropdown.classList.add('hidden');
        }
    });

    // Format date to YYYY-MM-DD
    function formatDate(date) {
        const d = new Date(date);
        let month = '' + (d.getMonth() + 1);
        let day = '' + d.getDate();
        const year = d.getFullYear();

        if (month.length < 2) month = '0' + month;
        if (day.length < 2) day = '0' + day;

        return [year, month, day].join('-');
    }

    // Set default dates (last month to today)
    function setDefaultDates() {
        const today = new Date();
        const lastMonth = new Date(today);
        lastMonth.setMonth(today.getMonth() - 1);
        
        document.getElementById('date_from').value = formatDate(lastMonth);
        document.getElementById('date_to').value = formatDate(today);
    }

    function loadInventoryHistory() {
        console.log('loadInventoryHistory called');
        const outletId = getOutletId();
        const token = getToken();
        
        console.log('Checking date inputs...');
        const dateFromElement = document.getElementById('date_from');
        const dateToElement = document.getElementById('date_to');
        
        console.log('Date from element:', dateFromElement);
        console.log('Date to element:', dateToElement);
        
        const dateFrom = dateFromElement?.value;
        const dateTo = dateToElement?.value;
        
        console.log('Date from value:', dateFrom);
        console.log('Date to value:', dateTo);
        
        // Validate dates
        if (!dateFrom || !dateTo) {
            console.log('Missing dates, showing notification');
            showNotification('warning', 'Peringatan', 'Silakan pilih rentang tanggal');
            return;
        }
        
        if (new Date(dateFrom) > new Date(dateTo)) {
            console.log('Invalid date range');
            showNotification('warning', 'Peringatan', 'Tanggal awal tidak boleh lebih besar dari tanggal akhir');
            return;
        }
        
        if (!outletId || !token) {
            console.log('Missing outlet ID or token');
            return;
        }
        
        console.log('All validations passed, proceeding with fetch...');
        
        const historyTableBody = document.getElementById('historyTableBody');
        historyTableBody.innerHTML = `
            <tr class="border-b">
                <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                    <div class="flex justify-center items-center gap-2">
                        <i data-lucide="loader" class="w-5 h-5 animate-spin text-gray-400"></i>
                        Memuat data...
                    </div>
                </td>
            </tr>
        `;
        
        // Add time to date range to cover the entire day
        const fromDate = new Date(dateFrom);
        fromDate.setHours(0, 0, 0, 0);
        
        const toDate = new Date(dateTo);
        toDate.setHours(23, 59, 59, 999);
        
        // Format dates for API
        const formattedFrom = fromDate.toISOString();
        const formattedTo = toDate.toISOString();
        
        const apiUrl = `/api/adjust-inventory/${outletId}?date_from=${encodeURIComponent(formattedFrom)}&date_to=${encodeURIComponent(formattedTo)}`;
        console.log('Making fetch request to:', apiUrl);
        console.log('Request headers:', {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token.substring(0, 10)}...`
        });
        
        fetch(apiUrl, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            historyTableBody.innerHTML = '';
            
            if (data.success && data.data && data.data.length > 0) {
                // Filter data by date range on client side as additional check
                const filteredData = data.data.filter(item => {
                    const itemDate = new Date(item.created_at);
                    return itemDate >= fromDate && itemDate <= toDate;
                });
                
                if (filteredData.length === 0) {
                    showNoDataMessage(historyTableBody);
                    return;
                }
                
                filteredData.forEach(history => {
                    // Skip records without product data or with invalid data
                    if (!history.product || !history.product.name) {
                        console.warn('Skipping record with missing product data:', history);
                        return;
                    }
                    
                    const row = document.createElement('tr');
                    row.className = 'border-b hover:bg-gray-50';
                    
                    // Format date
                    const date = new Date(history.created_at);
                    const formattedDate = date.toLocaleDateString('id-ID', {
                        day: '2-digit',
                        month: 'long',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                    
                    // Map status
                    let statusText = 'Menunggu';
                    let statusClass = 'text-yellow-600';
                    let statusIcon = 'clock';
                    
                    if (history.status === 'approved') {
                        statusText = 'Disetujui';
                        statusClass = 'text-green-600';
                        statusIcon = 'check-circle';
                    } else if (history.status === 'rejected') {
                        statusText = 'Ditolak';
                        statusClass = 'text-red-600';
                        statusIcon = 'x-circle';
                    }
                    
                    // Map type
                    const typeMap = {
                        'shipment': 'Kiriman',
                        'purchase': 'Pembelian',
                        'sale': 'Penjualan',
                        'adjustment': 'Penyesuaian',
                        'other': 'Lainnya'
                    };
                    
                    row.innerHTML = `
                        <td class="px-4 py-3 text-sm">${formattedDate}</td>
                        <td class="px-4 py-3 text-sm">${history.product.name}</td>
                        <td class="px-4 py-3 text-sm ${history.quantity_change > 0 ? 'text-green-600' : 'text-red-600'} font-medium">
                            ${history.quantity_change > 0 ? '+' : ''}${formatQuantity(history.quantity_change)}
                        </td>
                        <td class="px-4 py-3 text-sm">${typeMap[history.type] || history.type}</td>
                        <td class="px-4 py-3 text-sm">${history.notes || '-'}</td>
                        <td class="px-4 py-3 text-sm ${statusClass} flex items-center gap-1">
                            <i data-lucide="${statusIcon}" class="w-4 h-4"></i> ${statusText}
                        </td>
                    `;
                    
                    historyTableBody.appendChild(row);
                });
                
                lucide.createIcons({ icons });
            } else {
                showNoDataMessage(historyTableBody);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            historyTableBody.innerHTML = `
                <tr class="border-b">
                    <td colspan="6" class="px-4 py-8 text-center text-red-500">
                        <div class="flex flex-col items-center gap-2">
                            <i data-lucide="alert-circle" class="w-8 h-8"></i>
                            <span>Terjadi kesalahan saat memuat data. Silakan coba lagi.</span>
                        </div>
                    </td>
                </tr>
            `;
            
            lucide.createIcons({ icons });
        });
    }

    function showNoDataMessage(container) {
        container.innerHTML = `
            <tr class="border-b">
                <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                    <div class="flex flex-col items-center gap-2">
                        <i data-lucide="inbox" class="w-8 h-8 text-gray-400"></i>
                        <span>Tidak ada data penyesuaian stok untuk periode yang dipilih.</span>
                    </div>
                </td>
            </tr>
        `;
        lucide.createIcons({ icons });
    }

    function submitStockAdjustment() {
        const outletId = getOutletId();
        const token = getToken();
        const productId = document.getElementById('product_id').value;
        const quantityChange = document.getElementById('quantity_change').value;
        const adjustType = document.getElementById('adjust_type').value;
        const notes = document.getElementById('notes').value;
        
        if (!outletId || !token) return;
        
        // Validate inputs
        if (!productId) {
            showNotification('warning', 'Peringatan', 'Silakan pilih produk');
            return;
        }
        
        if (!quantityChange) {
            showNotification('warning', 'Peringatan', 'Silakan masukkan nilai perubahan stok');
            return;
        }
        
        if (!adjustType) {
            showNotification('warning', 'Peringatan', 'Silakan pilih tipe penyesuaian');
            return;
        }
        
        // Show loading state
        const submitButton = document.querySelector('#adjustContent button:last-child');
        const originalText = submitButton.innerHTML;
        submitButton.innerHTML = '<i data-lucide="loader" class="w-4 h-4 animate-spin"></i> Memproses...';
        submitButton.disabled = true;
        
        fetch('/api/adjust-inventory', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`
            },
            body: JSON.stringify({
                outlet_id: outletId,
                product_id: productId,
                quantity_change: parseFloat(quantityChange),
                type: adjustType,
                notes: notes
            })
        })
        .then(response => response.json())
        .then(data => {
            submitButton.innerHTML = originalText;
            submitButton.disabled = false;
            
            if (data.success) {
                // Show success notification
                showNotification('success', 'Berhasil', data.message || 'Penyesuaian stok berhasil disimpan');
                
                // Close the modal first
                closeModal('stockModal');
                
                // Auto refresh the page after a short delay to allow notification to be seen
                setTimeout(() => {
                    window.location.reload();
                }, 1500); // 1.5 second delay to show the success notification
                
            } else {
                showNotification('error', 'Error', data.message || 'Terjadi kesalahan');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            submitButton.innerHTML = originalText;
            submitButton.disabled = false;
            showNotification('error', 'Error', 'Terjadi kesalahan saat menyimpan penyesuaian stok');
        });
    }

    // Validate stock quantity based on unit type
    function validateStockQuantity() {
        const input = document.getElementById('quantity_change');
        const unitType = input.getAttribute('data-unit-type') || 'pcs';
        const value = input.value;
        
        if (!value) return;
        
        const numValue = parseFloat(value);
        
        if (isNaN(numValue)) {
            input.setCustomValidity('Masukkan angka yang valid');
            return;
        }
        
        if (unitType === 'meter') {
            // Allow decimal for meter (including negative values)
            input.setCustomValidity('');
        } else {
            // Only integers for pcs and unit (including negative values)
            if (value.includes('.') && numValue !== Math.floor(numValue)) {
                input.setCustomValidity('Untuk satuan pcs/unit, hanya angka bulat yang diperbolehkan');
                input.value = Math.floor(numValue);
                return;
            } else {
                input.setCustomValidity('');
            }
        }
    }

    // Initialize when page loads
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Stock modal script loaded');
        console.log('window.openModal function:', window.openModal);
        lucide.createIcons({ icons });
    });
    
    // Also check when window loads (in case DOMContentLoaded already fired)
    window.addEventListener('load', function() {
        console.log('Window loaded, checking openModal function:', window.openModal);
        
        // Make loadStockProducts available globally for manual testing
        window.testLoadStockProducts = loadStockProducts;
        console.log('You can test loadStockProducts by running: window.testLoadStockProducts()');
        
        // Setup modal override after a short delay to ensure all scripts are loaded
        setTimeout(function() {
            console.log('Setting up modal override after delay...');
            setupModalOverride();
        }, 100);
    });
</script>

<style>
    .tab-button.active {
        background-color: white;
        color: #f97316;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .tab-button:not(.active):hover {
        background-color: #f3f4f6;
    }
    
    select {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='%239ca3af' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 0.75rem center;
        background-size: 1em;
    }
    
    #productDropdown {
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }

    #productList {
        scrollbar-width: thin;
        scrollbar-color: #f97316 #f1f1f1;
    }

    #productList::-webkit-scrollbar {
        width: 6px;
    }

    #productList::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    #productList::-webkit-scrollbar-thumb {
        background-color: #f97316;
        border-radius: 6px;
    }

    #productDropdownTrigger {
        transition: all 0.2s ease;
    }

    #productDropdownTrigger:hover {
        border-color: #f97316;
    }
</style>