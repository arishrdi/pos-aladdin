<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
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

        /* Mobile cart item layout */
        .cart-item-mobile {
            display: none;
            flex-direction: column;
            padding: 12px;
            border-bottom: 1px solid #f3f4f6;
            gap: 8px;
        }

        .cart-item-mobile .product-info {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .cart-item-mobile .controls-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
        }

        .cart-item-mobile .qty-discount {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        @media (max-width: 768px) {
            .cart-item-grid {
                display: none;
            }
            .cart-item-mobile {
                display: flex;
            }
        }

        @media (max-width: 1024px) and (min-width: 769px) {
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
            min-height: 36px;
        }

        .discount-input {
            width: 70px;
            text-align: right;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            padding: 4px;
            min-height: 36px;
        }

        /* Touch-friendly quantity controls */
        .qty-btn {
            min-width: 36px;
            min-height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #d1d5db;
            background: #f9fafb;
            border-radius: 4px;
            cursor: pointer;
            user-select: none;
        }

        .qty-btn:hover {
            background: #f3f4f6;
        }

        .qty-btn:active {
            background: #e5e7eb;
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

        /* Mobile responsive improvements */
        @media (max-width: 768px) {
            .main-container {
                flex-direction: column;
                height: calc(100vh - 60px) !important;
            }
            
            .products-section {
                width: 100% !important;
                height: 60% !important;
                border-right: none !important;
                border-bottom: 2px solid #b3c5ba !important;
                min-height: 280px;
            }
            
            .cart-section {
                width: 100% !important;
                height: 40% !important;
                border-left: none !important;
                min-height: 300px;
                display: flex;
                flex-direction: column;
            }
            
            .navbar {
                padding: 8px 12px !important;
                min-height: 60px;
            }
            
            .navbar .flex {
                gap: 4px !important;
            }
            
            .navbar button {
                padding: 4px 6px !important;
                font-size: 11px !important;
                min-height: 32px !important;
            }
            
            .navbar button i {
                font-size: 12px !important;
                margin-right: 2px !important;
            }
            
            .navbar a {
                font-size: 16px !important;
            }
            
            /* Stack navbar buttons on very small screens */
            @media (max-width: 480px) {
                .navbar .flex.flex-wrap {
                    flex-direction: column;
                    align-items: stretch;
                }
                
                .navbar .flex.flex-wrap > * {
                    margin-bottom: 4px;
                }
            }
            
            /* Product cards mobile optimization */
            .product-card {
                padding: 12px !important;
                flex-direction: column !important;
                align-items: stretch !important;
                gap: 12px !important;
            }
            
            .product-card .flex.items-center {
                justify-content: flex-start !important;
            }
            
            .product-card .flex.items-center.space-x-3 {
                width: 100% !important;
            }
            
            .product-card button {
                width: 100% !important;
                padding: 12px !important;
                font-size: 14px !important;
            }
            
            /* Cart item mobile layout - use mobile specific layout */
            .cart-item-grid {
                display: none !important;
            }
            
            .cart-column-headers {
                display: none !important;
            }
            
            /* Mobile specific product grid */
            .product-item {
                margin-bottom: 8px !important;
            }
            
            /* Touch-friendly buttons */
            button {
                min-height: 44px !important;
                min-width: 44px !important;
            }
            
            /* Category tabs mobile */
            .category-container {
                padding: 0 !important;
            }
            
            .nav-link {
                white-space: nowrap !important;
                padding: 8px 12px !important;
                font-size: 12px !important;
            }
            
            /* Search input mobile */
            #searchInput {
                font-size: 16px !important; /* Prevents zoom on iOS */
                padding: 12px !important;
            }
            
            /* Payment section mobile */
            .payment-section {
                padding: 12px 8px !important;
                flex-shrink: 0;
                margin-top: auto;
            }
            
            .payment-section .space-y-2 > * {
                margin-bottom: 6px !important;
            }
            
            /* Tax selection mobile - make more compact */
            .payment-section .flex.space-x-3 {
                flex-direction: row !important;
                gap: 8px !important;
                justify-content: space-around;
            }
            
            .payment-section .flex.space-x-3 label {
                font-size: 12px !important;
                flex: 1;
                text-align: center;
            }
            
            /* Make tax selection area more compact */
            .payment-section .mb-4 {
                margin-bottom: 8px !important;
                padding: 8px !important;
            }
            
            /* Summary text mobile */
            .summary-item {
                font-size: 13px !important;
            }
            
            #total {
                font-size: 18px !important;
            }
            
            /* Cart items container mobile */
            .cart-items-container {
                flex: 1;
                overflow-y: auto;
                min-height: 120px;
            }
            
            /* Cart header mobile */
            .cart-header {
                padding: 8px 12px !important;
                flex-shrink: 0;
            }
            
            .cart-header h4 {
                font-size: 16px !important;
                margin: 0 !important;
            }
            
            /* Summary section mobile */
            .summary-section {
                margin-bottom: 12px !important;
            }
            
            .summary-section .flex {
                margin-bottom: 4px !important;
            }
            
            .summary-section .mb-3 {
                margin-bottom: 8px !important;
            }
            
            /* Action buttons mobile */
            .space-y-2 button {
                padding: 10px 16px !important;
                font-size: 14px !important;
            }
            
            /* Prevent horizontal scroll */
            body {
                overflow-x: hidden !important;
            }
            
            /* Mobile specific adjustments */
            .container-fluid {
                padding: 0 !important;
            }
            
            /* Ensure content fits in viewport */
            * {
                box-sizing: border-box;
            }
            
            /* Better scrolling on mobile */
            .cart-items-container, .products-list-container {
                -webkit-overflow-scrolling: touch;
            }
            
            /* Improve text readability */
            .product-name {
                line-height: 1.3 !important;
            }
            
            /* Better spacing for mobile */
            .p-4 {
                padding: 12px !important;
            }
            
            .p-5 {
                padding: 16px !important;
            }
            
            /* Force all content to be visible */
            .payment-section {
                max-height: none !important;
                overflow: visible !important;
            }
            
            /* Ensure buttons are accessible */
            .payment-section button {
                margin-bottom: 4px !important;
            }
            
            /* Compact margins for mobile */
            .mb-1 {
                margin-bottom: 2px !important;
            }
            
            .mb-2 {
                margin-bottom: 4px !important;
            }
            
            .mb-3 {
                margin-bottom: 6px !important;
            }
            
            .my-2 {
                margin-top: 4px !important;
                margin-bottom: 4px !important;
            }
        }

        /* Tablet responsive improvements */
        @media (min-width: 769px) and (max-width: 1024px) {
            .products-section {
                width: 60% !important;
            }
            
            .cart-section {
                width: 40% !important;
            }
            
            .product-card {
                padding: 14px !important;
            }
            
            .cart-item-grid {
                grid-template-columns: minmax(140px, 2fr) 90px 80px 90px 35px !important;
                gap: 8px !important;
            }
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

        /* Background colors */
        .bg-green-50 {
            background-color: #e6ede8 !important;
        }

        .bg-green-100 {
            background-color: #cdd9d1 !important;
        }

        .bg-green-200 {
            background-color: #b3c5ba !important;
        }

        .bg-green-300 {
            background-color: #99b1a3 !important;
        }

        .bg-green-400 {
            background-color: #7f9d8c !important;
        }

        .bg-green-500 {
            background-color: #354c41 !important;
        }

        /* warna utama */
        .bg-green-600 {
            background-color: #2e4238 !important;
        }

        .bg-green-700 {
            background-color: #27382f !important;
        }

        .bg-green-800 {
            background-color: #1f2f26 !important;
        }

        .bg-green-900 {
            background-color: #18241d !important;
        }

        /* Text colors */
        .text-green-50 {
            color: #e6ede8 !important;
        }

        .text-green-100 {
            color: #cdd9d1 !important;
        }

        .text-green-200 {
            color: #b3c5ba !important;
        }

        .text-green-300 {
            color: #99b1a3 !important;
        }

        .text-green-400 {
            color: #7f9d8c !important;
        }

        .text-green-500 {
            color: #354c41 !important;
        }

        .text-green-600 {
            color: #2e4238 !important;
        }

        .text-green-700 {
            color: #27382f !important;
        }

        .text-green-800 {
            color: #1f2f26 !important;
        }

        .text-green-900 {
            color: #18241d !important;
        }

        /* Border colors */
        .border-green-50 {
            border-color: #e6ede8 !important;
        }

        .border-green-100 {
            border-color: #cdd9d1 !important;
        }

        .border-green-200 {
            border-color: #b3c5ba !important;
        }

        .border-green-300 {
            border-color: #99b1a3 !important;
        }

        .border-green-400 {
            border-color: #7f9d8c !important;
        }

        .border-green-500 {
            border-color: #354c41 !important;
        }

        .border-green-600 {
            border-color: #2e4238 !important;
        }

        .border-green-700 {
            border-color: #27382f !important;
        }

        .border-green-800 {
            border-color: #1f2f26 !important;
        }

        .border-green-900 {
            border-color: #18241d !important;
        }
    </style>
</head>

<body class="bg-white font-sans overflow-x-hidden">
    <div class="container-fluid p-0">
        <!-- Enhanced Navbar -->
        <nav class="navbar bg-white shadow-sm border-b py-2 px-3">
            <div class="flex flex-col md:flex-row md:justify-between md:items-center w-full gap-2">
                <a href="#" class="text-green-500 font-bold text-xl md:text-2xl">
                    <span id="outletName">Loading ...</span>
                </a>
                <div class="flex flex-wrap gap-2 items-center">
                    {{-- <button id="btnStockModal"
                        class="px-3 py-1.5 text-sm text-black font-bold bg-green-50 border border-green-300 rounded-md hover:bg-green-100 transition-colors">
                        <i class="fas fa-box mr-1.5 text-green-500 text-base"></i> Stok
                    </button> --}}

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

        <div class="main-container flex h-[calc(100vh-60px)] overflow-hidden">
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

                <div class="cart-column-headers p-4 text-sm font-semibold text-gray-600 bg-gray-50 hidden md:block">
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
                    <!-- Tax Type Selection -->
                    <div class="mb-4 p-3 bg-gray-50 rounded-lg">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Pajak Transaksi</label>
                        <div class="flex space-x-3">
                            <label class="flex items-center cursor-pointer">
                                <input type="radio" name="transactionTaxType" value="non_pkp"
                                    class="mr-2 text-green-600" checked>
                                <span class="text-sm font-medium text-green-600">Non-PKP (0%)</span>
                            </label>
                            <label class="flex items-center cursor-pointer">
                                <input type="radio" name="transactionTaxType" value="pkp" class="mr-2 text-blue-600">
                                <span class="text-sm font-medium text-blue-600">PKP (11%)</span>
                            </label>
                        </div>
                    </div>

                    <div class="summary-section">
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
                        <div class="flex justify-between mb-2">
                            <div class="summary-item text-base text-gray-500">
                                <span id="taxLabel">Pajak (0%)</span>
                                <span id="taxTypeIndicator"
                                    class="ml-1 text-xs px-1.5 py-0.5 rounded-full bg-gray-100 text-gray-600"></span>
                            </div>
                            <div id="taxAmount" class="summary-item text-base text-gray-500">Rp 0</div>
                        </div>

                        <!-- Divider -->
                        <div class="border-t border-green-200 my-2"></div>

                        <div class="flex justify-between mb-3">
                            <div class="summary-item text-lg text-gray-800 font-bold">Total</div>
                            <div id="total" class="text-green-500 font-extrabold text-2xl">Rp 0</div>
                        </div>
                        <div class="border-t border-green-200 mb-2"></div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="space-y-2">
                        <!-- Tombol Pembayaran -->
                        <button id="btnPaymentModal"
                            class="bg-green-500 text-white border border-green-500 w-full py-4 font-semibold rounded-md text-sm hover:bg-green-600 transition-colors">
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
    @include('partials.pos.add-member-modal')
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
            outletInfo.tax_type = data.data.tax_type || 'non_pkp';
            outletInfo.qris = data.data.qris_url;
            
            // Store both PKP and NonPKP banking info
            outletInfo.pkp_banking = {
                atas_nama: data.data.pkp_atas_nama_bank,
                bank: data.data.pkp_nama_bank,
                nomor: data.data.pkp_nomor_transaksi_bank
            };
            
            outletInfo.non_pkp_banking = {
                atas_nama: data.data.non_pkp_atas_nama_bank,
                bank: data.data.non_pkp_nama_bank,
                nomor: data.data.non_pkp_nomor_transaksi_bank
            };
            
            // Set default bank account (for fallback)
            outletInfo.bank_account = {
                atas_nama: data.data.atas_nama_bank,
                bank: data.data.nama_bank,
                nomor: data.data.nomor_transaksi_bank
            };
            
            // Update tax display
            updateTaxDisplay();
        }
    } catch (error) {
        console.error('Error loading outlet info:', error);
    }
}

// Update tax display based on outlet tax type (initial load)
function updateTaxDisplay() {
    // Set initial tax display based on default selection (Non-PKP)
    updateTaxDisplayFromSelection();
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
            (product.category?.name || 'uncategorized') === filterCategory;
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
        const isOutOfStock = false; // Allow selling products even with 0 stock
        
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
                            <div class="text-sm text-gray-500">
                                <!-- <span>Stok: ${formatQuantity(availableStock)}</span> -->
                                <span class="ml-2 px-1.5 py-0.5 bg-gray-100 text-gray-600 rounded text-xs">${product.unit_type || 'pcs'}</span>
                            </div>
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

    // Tax type selection
    document.querySelectorAll('input[name="transactionTaxType"]').forEach(radio => {
        radio.addEventListener('change', () => {
            updateTaxDisplayFromSelection();
            if (window.cartManager) {
                window.cartManager.updateCartDisplay();
            }
        });
    });
}

// Update tax display based on selected tax type
function updateTaxDisplayFromSelection() {
    const taxLabel = document.getElementById('taxLabel');
    const taxTypeIndicator = document.getElementById('taxTypeIndicator');
    
    if (taxLabel && taxTypeIndicator) {
        const selectedTaxType = document.querySelector('input[name="transactionTaxType"]:checked')?.value || 'non_pkp';
        const taxRate = selectedTaxType === 'pkp' ? 11 : 0;
        const taxTypeName = selectedTaxType === 'pkp' ? 'PKP' : 'Non-PKP';
        
        taxLabel.textContent = `Pajak (${taxRate}%)`;
        taxTypeIndicator.textContent = taxTypeName;
        
        // Color coding for tax type
        if (selectedTaxType === 'pkp') {
            taxTypeIndicator.className = 'ml-1 text-xs px-1.5 py-0.5 rounded-full bg-blue-100 text-blue-600';
        } else {
            taxTypeIndicator.className = 'ml-1 text-xs px-1.5 py-0.5 rounded-full bg-green-100 text-green-600';
        }
    }
}
    </script>

</body>

</html>