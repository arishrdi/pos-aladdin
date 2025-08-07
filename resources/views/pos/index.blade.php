<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aladdin Karpet - POS System</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SweetAlert2 -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes fadeOut {
            from {
                opacity: 1;
            }

            to {
                opacity: 0;
            }
        }

        .empty-cart {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: #9CA3AF;
        }

        .empty-cart i {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .cart-items-container {
            overflow-y: auto;
            flex-grow: 1;
        }

        .cart-item-grid {
            display: grid;
            grid-template-columns: minmax(150px, 2fr) 120px 80px 100px 40px;
            gap: 10px;
            align-items: center;
            padding: 12px 16px;
            border-bottom: 1px solid #f3f4f6;
        }

        @media (max-width: 1024px) {
            .cart-item-grid {
                grid-template-columns: minmax(120px, 2fr) 100px 70px 90px 40px;
            }
        }

        .qty-control {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .qty-input {
            width: 40px;
            text-align: center;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            padding: 4px;
        }

        .discount-input {
            width: 70px;
            text-align: right;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            padding: 4px;
        }

        /* New styles for sticky cart footer */
        .cart-section {
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .payment-section {
            margin-top: auto;
            background: white;
        }

        /* Scrollable products */
        .products-list-container {
            overflow-y: auto;
            flex-grow: 1;
        }

        /* Payment method selection */
        .payment-method {
            border: 1px solid #d1d5db;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 8px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .payment-method:hover {
            border-color: #F97316;
        }

        .payment-method.selected {
            border-color: #F97316;
            background-color: #FFF7ED;
        }

        /* Print styles for invoice */
        @media print {
            body * {
                visibility: hidden;
            }

            #invoice-print,
            #invoice-print * {
                visibility: visible;
            }

            #invoice-print {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
        }

        /* Member search dropdown */
        .member-search-container {
            position: relative;
        }

        .member-results {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            max-height: 200px;
            overflow-y: auto;
            z-index: 10;
            display: none;
        }

        .member-item {
            padding: 8px 12px;
            cursor: pointer;
            border-bottom: 1px solid #f3f4f6;
        }

        .member-item:hover {
            background-color: #f9fafb;
        }

        .member-item.active {
            background-color: #F97316;
            color: white;
        }

        /* Override green backgrounds with green */
        .bg-green-50 {
            background-color: #f0fdf4 !important;
        }

        .bg-green-100 {
            background-color: #dcfce7 !important;
        }

        .bg-green-200 {
            background-color: #bbf7d0 !important;
        }

        .bg-green-300 {
            background-color: #86efac !important;
        }

        .bg-green-400 {
            background-color: #4ade80 !important;
        }

        .bg-green-500 {
            background-color: #22c55e !important;
        }

        .bg-green-600 {
            background-color: #16a34a !important;
        }

        .bg-green-700 {
            background-color: #15803d !important;
        }

        .bg-green-800 {
            background-color: #166534 !important;
        }

        .bg-green-900 {
            background-color: #14532d !important;
        }

        /* Override green text with green */
        .text-green-50 {
            color: #f0fdf4 !important;
        }

        .text-green-100 {
            color: #dcfce7 !important;
        }

        .text-green-200 {
            color: #bbf7d0 !important;
        }

        .text-green-300 {
            color: #86efac !important;
        }

        .text-green-400 {
            color: #4ade80 !important;
        }

        .text-green-500 {
            color: #22c55e !important;
        }

        .text-green-600 {
            color: #16a34a !important;
        }

        .text-green-700 {
            color: #15803d !important;
        }

        .text-green-800 {
            color: #166534 !important;
        }

        .text-green-900 {
            color: #14532d !important;
        }

        /* Override green borders with green */
        .border-green-50 {
            border-color: #f0fdf4 !important;
        }

        .border-green-100 {
            border-color: #dcfce7 !important;
        }

        .border-green-200 {
            border-color: #bbf7d0 !important;
        }

        .border-green-300 {
            border-color: #86efac !important;
        }

        .border-green-400 {
            border-color: #4ade80 !important;
        }

        .border-green-500 {
            border-color: #22c55e !important;
        }

        .border-green-600 {
            border-color: #16a34a !important;
        }

        .border-green-700 {
            border-color: #15803d !important;
        }

        .border-green-800 {
            border-color: #166534 !important;
        }

        .border-green-900 {
            border-color: #14532d !important;
        }
    </style>
</head>

<body class="bg-white font-sans overflow-x-hidden">
    <div class="container-fluid p-0">
        <!-- Enhanced Navbar -->
        <nav class="navbar bg-white shadow-sm border-b py-4 px-5">
            <div class="flex flex-col md:flex-row md:justify-between md:items-center w-full gap-3">
                <a href="#" class="text-green-500 font-bold text-xl md:text-2xl">
                    <span id="outletName">Loading ...</span>
                </a>
                <div class="flex flex-wrap gap-2 items-center">
                    <button id="btnStockModal"
                        class="px-3 py-1.5 text-sm text-black font-bold bg-green-50 border border-green-300 rounded-md hover:bg-green-100 transition-colors">
                        <i class="fas fa-box mr-1.5 text-green-500 text-base"></i> Stok
                    </button>

                    <button id="btnIncomeModal"
                        class="px-3 py-1.5 text-sm text-black font-bold bg-green-50 border border-green-300 rounded-md hover:bg-green-100 transition-colors">
                        <i class="fas fa-money-bill mr-1.5 text-green-500 text-base"></i> Rp 0
                    </button>

                    <button id="btnCashierModal"
                        class="px-3 py-1.5 text-sm text-black font-bold bg-green-50 border border-green-300 rounded-md hover:bg-green-100 transition-colors">
                        <i class="fas fa-cash-register mr-1.5 text-green-500 text-base"></i> Kas kasir
                    </button>

                    <button
                        class="px-5 py-2.5 text-base text-black font-bold rounded-md hover:bg-green-50 transition-colors">
                        <i class="fas fa-user mr-2 text-green-500 text-base"></i>
                        <span id="userLabel" class="font-medium">Loading...</span>
                    </button>

                    <button id="logoutButton"
                        class="px-3 py-1.5 text-sm text-black font-bold border border-green-300 rounded-md hover:bg-green-100 transition-colors">
                        <i class="fas fa-sign-out-alt mr-1.5 text-green-500 text-lg"></i>
                    </button>

                </div>
            </div>
        </nav>

        <div class="main-container flex h-[calc(100vh-68px)] overflow-hidden">
            <!-- Products Section -->
            <div class="products-section w-3/5 bg-white flex flex-col border-r-2 border-green-200">
                <!-- Search and Categories Section -->
                <div class="p-4">
                    <div class="search-bar mb-3">
                        <input id="searchInput" type="text"
                            class="w-full px-3 py-2 text-sm rounded-md border border-green-300 focus:border-green-500 focus:ring-1 focus:ring-green-500 placeholder-gray-400 transition-all duration-200"
                            placeholder="Cari produk atau scan barcode..." autofocus>
                    </div>

                    <div class="category-container overflow-x-auto whitespace-nowrap pb-1 mb-2">
                        <ul id="categoryTabs" class="nav flex-nowrap">
                            <!-- Categories will be dynamically added here -->
                        </ul>
                    </div>
                </div>

                <hr class="border-t border-green-500 opacity-30 my-0">

                <!-- Products List -->
                <div id="productsContainer" class="products-list-container p-4">
                    <div class="empty-cart p-8 text-center">
                        <i class="fas fa-spinner fa-spin text-gray-300"></i>
                        <p class="text-gray-500 text-lg font-medium">Memuat produk...</p>
                    </div>
                </div>
            </div>

            <!-- Cart Section -->
            <div class="cart-section w-2/5 bg-white flex flex-col overflow-hidden border-l-2 border-green-200">
                <div class="cart-header p-4 border-b-2 border-green-200">
                    <h4 class="text-lg m-0 flex items-center font-semibold">
                        <i class="fas fa-shopping-cart text-green-500 mr-3"></i> Keranjang
                    </h4>
                </div>

                <div class="cart-column-headers p-4 text-sm font-semibold text-gray-600 bg-gray-50">
                    <div class="grid grid-cols-12">
                        <div class="col-span-5">Produk</div>
                        <div class="col-span-2 text-center">Qty</div>
                        <div class="col-span-3 text-center">Diskon</div>
                        <div class="col-span-2 text-right">Subtotal</div>
                    </div>
                </div>

                <div id="cartItems" class="cart-items-container w-full">
                    <!-- Empty cart state -->
                    <div id="emptyCart" class="empty-cart p-8 text-center">
                        <i class="fas fa-shopping-cart text-gray-300"></i>
                        <p class="text-gray-500 text-lg font-medium">Keranjang kosong</p>
                        <p class="text-gray-400 text-sm mt-1">Tambahkan produk ke keranjang</p>
                    </div>
                </div>

                <!-- Payment Section - Now sticks to bottom -->
                <div class="payment-section p-5 border-t border-green-200">
                    <div class="flex justify-between mb-1">
                        <div class="summary-item text-base text-gray-700">Subtotal</div>
                        <div id="subtotal" class="summary-item text-base text-gray-700">Rp 0</div>
                    </div>
                    <div class="flex justify-between mb-1">
                        <div class="summary-item text-base text-gray-700">Diskon</div>
                        <div id="totalDiscount" class="summary-item text-base text-gray-700">Rp 0</div>
                    </div>
                    <div class="flex justify-between mb-1">
                        <div class="summary-item text-base text-gray-700">Subtotal Qty</div>
                        <div id="totalQty" class="summary-item text-base text-gray-700">0</div>
                    </div>
                    <div class="flex justify-between mb-3">
                        <div class="summary-item text-base text-gray-500">Pajak (0%)</div>
                        <div id="taxAmount" class="summary-item text-base text-gray-500">Rp 0</div>
                    </div>

                    <!-- Divider -->
                    <div class="border-t border-green-200 my-3"></div>

                    <div class="flex justify-between mb-5">
                        <div class="summary-item text-lg text-gray-800 font-bold">Total</div>
                        <div id="total" class="text-green-500 font-extrabold text-2xl">Rp 0</div>
                    </div>
                    <div class="border-t border-green-200 my-3 mb-3"></div>

                    <!-- Action Buttons -->
                    <div class="space-y-2">
                        <!-- Tombol Pembayaran -->
                        <button id="btnPaymentModal"
                            class="bg-green-500 text-white border border-green-500 w-full py-2 font-semibold rounded-md text-sm hover:bg-green-600 transition-colors">
                            <i class="fas fa-money-bill-wave mr-2"></i> Pembayaran
                        </button>

                        <!-- Tombol Riwayat Transaksi -->
                        <button id="btnHistoryModal"
                            class="border border-gray-300 w-full py-2 text-sm rounded-md bg-white hover:bg-gray-50 transition-colors">
                            <i class="fas fa-history mr-2"></i> Riwayat Transaksi
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include other modals -->
    @include('partials.pos.payment-modal')
    @include('partials.pos.cashier-modal')
    @include('partials.pos.history-modal')
    @include('partials.pos.income-modal')
    @include('partials.pos.stock')

    <!-- Load new modular JS files -->
    <script src="/js/pos/config.js"></script>
    <script src="/js/pos/utils.js"></script>
    <script src="/js/pos/cart.js"></script>
    <script src="/js/pos/simple-payment.js"></script>
    <script src="/js/pos/refund.js"></script>

    <!-- Main POS App -->
    <script>
        // Initialize POS Application
document.addEventListener('DOMContentLoaded', function () {
    // Initialize Lucide icons
    lucide.createIcons();

    // Wait for all dependencies to load
    if (typeof CartManager === 'undefined' || typeof SimplePaymentManager === 'undefined') {
        console.error('Required classes not loaded');
        return;
    }

    // Initialize global objects
    window.cartManager = new CartManager();
    window.simplePaymentManager = new SimplePaymentManager(window.cartManager);
    window.refundManager = new RefundManager();

    // Set outlet name in header
    document.getElementById('outletName').textContent = outletInfo.name;

    // Initialize app data
    initializePOSApp();
});

// Initialize POS Application
async function initializePOSApp() {
    try {
        // Load user info
        await loadUserInfo();
        
        // Load outlet info
        await loadOutletInfo();
        
        // Load products
        await loadProducts();
        
        // Attach event listeners
        attachEventListeners();
        
    } catch (error) {
        console.error('Failed to initialize POS app:', error);
        showNotification('Gagal menginisialisasi aplikasi', 'error');
    }
}

// Load user info
async function loadUserInfo() {
    try {
        const response = await fetch('/api/me', {
            headers: {
                'Authorization': `Bearer ${POS_CONFIG.API_TOKEN}`,
                'Accept': 'application/json'
            }
        });

        if (response.status === 401) {
            localStorage.removeItem('token');
            window.location.href = '/login';
            return;
        }

        const data = await response.json();
        if (data.success && data.data) {
            document.getElementById('userLabel').textContent = data.data.name || 'User';
        }
    } catch (error) {
        console.error('Error loading user info:', error);
    }
}

// Load outlet info
async function loadOutletInfo() {
    try {
        const response = await fetch(`/api/outlets/${outletInfo.id}`, {
            headers: {
                'Authorization': `Bearer ${POS_CONFIG.API_TOKEN}`,
                'Accept': 'application/json'
            }
        });

        const data = await response.json();
        if (data.success) {
            outletInfo.tax = data.data.tax || 0;
            outletInfo.qris = data.data.qris_url;
            outletInfo.bank_account = {
                atas_nama: data.data.atas_nama_bank,
                bank: data.data.nama_bank,
                nomor: data.data.nomor_transaksi_bank
            };
        }
    } catch (error) {
        console.error('Error loading outlet info:', error);
    }
}

// Load products
async function loadProducts() {
    try {
        const response = await fetch(`/api/products/outlet/${outletInfo.id}`, {
            headers: {
                'Authorization': `Bearer ${POS_CONFIG.API_TOKEN}`,
                'Accept': 'application/json'
            }
        });

        const data = await response.json();
        if (data.success) {
            // Use products as-is from backend
            // Bonus will use regular product stock (product.quantity)
            window.products = data.data;
            renderCategories();
            renderProducts();
        }
    } catch (error) {
        console.error('Error loading products:', error);
        showNotification('Gagal memuat produk', 'error');
    }
}

// Render categories
function renderCategories() {
    const categories = ['all', ...new Set(window.products.map(p => p.category?.name || 'uncategorized'))];
    const categoryTabs = document.getElementById('categoryTabs');
    
    categoryTabs.innerHTML = categories.map((category, index) => {
        const categoryName = category === 'all' ? 'Semua' : 
                           category === 'uncategorized' ? 'Lainnya' :
                           category.charAt(0).toUpperCase() + category.slice(1);
        const isActive = index === 0;
        
        return `
            <li class="inline-flex">
                <a href="#" data-category="${category}" 
                   class="nav-link ${isActive ? 'active bg-green-500 text-white border-green-400' : 'bg-white text-gray-700 border-gray-200 hover:bg-gray-100'} 
                          px-3 py-1.5 text-xs font-medium rounded-full mr-2 border shadow-sm transition-all duration-200">
                    ${categoryName}
                </a>
            </li>
        `;
    }).join('');
    
    // Add click handlers
    categoryTabs.addEventListener('click', (e) => {
        if (e.target.classList.contains('nav-link')) {
            e.preventDefault();
            
            // Update active state
            categoryTabs.querySelectorAll('.nav-link').forEach(tab => {
                tab.classList.remove('active', 'bg-green-500', 'text-white', 'border-green-400');
                tab.classList.add('bg-white', 'text-gray-700', 'border-gray-200');
            });
            
            e.target.classList.remove('bg-white', 'text-gray-700', 'border-gray-200');
            e.target.classList.add('active', 'bg-green-500', 'text-white', 'border-green-400');
            
            const category = e.target.getAttribute('data-category');
            renderProducts(category);
        }
    });
}

// Render products
function renderProducts(filterCategory = 'all', searchTerm = '') {
    const productsContainer = document.getElementById('productsContainer');
    
    let filteredProducts = window.products.filter(product => {
        const categoryMatch = filterCategory === 'all' || 
            (product.category?.name || 'uncategorized').toLowerCase() === filterCategory;
        const searchMatch = product.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
            (product.barcode && product.barcode.toLowerCase().includes(searchTerm.toLowerCase()));
        return categoryMatch && searchMatch;
    });

    if (filteredProducts.length === 0) {
        productsContainer.innerHTML = `
            <div class="empty-cart p-8 text-center">
                <i data-lucide="search-x" class="w-12 h-12 mx-auto text-gray-300"></i>
                <p class="text-gray-500 text-lg font-medium mt-4">Produk tidak ditemukan</p>
            </div>
        `;
        lucide.createIcons();
        return;
    }

    productsContainer.innerHTML = filteredProducts.map(product => {
        const cartItem = window.cartManager.cart.find(item => item.id === product.id);
        const reservedInCart = cartItem ? cartItem.quantity : 0;
        const availableStock = (product.quantity || 0) - reservedInCart;
        const isOutOfStock = availableStock <= 0;
        
        return `
            <div class="product-item mb-3">
                <div class="product-card flex justify-between items-center p-4 bg-white border border-gray-200 rounded-lg hover:shadow-sm transition-all">
                    <div class="flex items-center space-x-3">
                        ${product.image_url ? 
                            `<img src="${product.image_url}" alt="${product.name}" class="w-12 h-12 rounded object-cover">` :
                            '<div class="w-12 h-12 bg-gray-200 rounded flex items-center justify-center"><i data-lucide="package" class="w-6 h-6 text-gray-400"></i></div>'
                        }
                        <div>
                            <div class="product-name text-base font-medium">${product.name}</div>
                            <div class="product-price text-green-500 font-semibold text-base">${formatCurrency(product.price, true)}</div>
                            <div class="text-sm text-gray-500">Stok: ${formatQuantity(availableStock)}</div>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <span class="product-category text-xs bg-gray-100 text-gray-600 px-2.5 py-1 rounded-full">
                            ${(product.category?.name || 'UNCATEGORIZED').toUpperCase()}
                        </span>
                        ${isOutOfStock ?
                            '<button class="bg-gray-100 text-gray-500 border border-gray-300 rounded px-4 py-2 text-sm w-24" disabled>Habis</button>' :
                            `<button class="btn-add-to-cart bg-green-500 text-white border-none rounded px-4 py-2 text-sm flex items-center justify-center w-24 hover:bg-green-600 transition-colors" data-product-id="${product.id}">
                                <i data-lucide="plus" class="w-4 h-4 mr-1"></i> Tambah
                            </button>`
                        }
                    </div>
                </div>
            </div>
        `;
    }).join('');

    // Add event listeners
    productsContainer.querySelectorAll('.btn-add-to-cart').forEach(button => {
        button.addEventListener('click', (e) => {
            const productId = parseInt(e.target.closest('button').dataset.productId);
            const product = window.products.find(p => p.id === productId);
            if (product) {
                const success = window.cartManager.addItem(product);
                if (success) {
                    // Re-render products to update stock display
                    const activeCategory = document.querySelector('#categoryTabs .nav-link.active')?.getAttribute('data-category') || 'all';
                    const searchTerm = document.getElementById('searchInput').value;
                    renderProducts(activeCategory, searchTerm);
                }
            }
        });
    });

    lucide.createIcons();
}

// Attach event listeners
function attachEventListeners() {
    // Search input
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', debounce((e) => {
            const activeCategory = document.querySelector('#categoryTabs .nav-link.active')?.getAttribute('data-category') || 'all';
            renderProducts(activeCategory, e.target.value);
        }, POS_CONFIG.SEARCH_DELAY));
    }

    // Payment modal
    const btnPaymentModal = document.getElementById('btnPaymentModal');
    if (btnPaymentModal) {
        btnPaymentModal.addEventListener('click', () => {
            window.simplePaymentManager.showPaymentModal();
        });
    }

    // Refund modal
    const btnRefundModal = document.getElementById('btnRefundModal');
    if (btnRefundModal) {
        btnRefundModal.addEventListener('click', () => {
            window.refundManager.showRefundModal();
        });
    }

    // Logout button
    const logoutButton = document.getElementById('logoutButton');
    if (logoutButton) {
        logoutButton.addEventListener('click', async () => {
            try {
                await fetch('/api/logout', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${POS_CONFIG.API_TOKEN}`
                    }
                });
                localStorage.removeItem('token');
                window.location.href = '/';
            } catch (error) {
                console.error('Logout error:', error);
            }
        });
    }

    // Cash management button - use existing modal
    const btnCashierModal = document.getElementById('btnCashierModal');
    if (btnCashierModal) {
        btnCashierModal.addEventListener('click', (e) => {
            e.preventDefault();
            const modal = document.getElementById('cashierModal');
            if (modal) {
                modal.classList.remove('hidden');
                // Fetch cash balance when modal opens
                if (typeof fetchCashBalance === 'function') {
                    fetchCashBalance();
                }
            } else {
                console.error('Cash modal not found');
            }
        });
    }

    // Other modal buttons
    const btnHistoryModal = document.getElementById('btnHistoryModal');
    if (btnHistoryModal) {
        btnHistoryModal.addEventListener('click', () => openModal('historyModal'));
    }

    const btnStockModal = document.getElementById('btnStockModal');
    if (btnStockModal) {
        btnStockModal.addEventListener('click', () => openModal('stockModal'));
    }

    const btnIncomeModal = document.getElementById('btnIncomeModal');
    if (btnIncomeModal) {
        btnIncomeModal.addEventListener('click', () => openModal('incomeModal'));
    }
}
    </script>

</body>

</html>