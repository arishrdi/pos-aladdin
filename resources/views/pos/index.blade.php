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
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        @keyframes fadeOut {
            from { opacity: 1; }
            to { opacity: 0; }
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
            #invoice-print, #invoice-print * {
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
        .bg-green-50 { background-color: #f0fdf4 !important; }
        .bg-green-100 { background-color: #dcfce7 !important; }
        .bg-green-200 { background-color: #bbf7d0 !important; }
        .bg-green-300 { background-color: #86efac !important; }
        .bg-green-400 { background-color: #4ade80 !important; }
        .bg-green-500 { background-color: #22c55e !important; }
        .bg-green-600 { background-color: #16a34a !important; }
        .bg-green-700 { background-color: #15803d !important; }
        .bg-green-800 { background-color: #166534 !important; }
        .bg-green-900 { background-color: #14532d !important; }
        
        /* Override green text with green */
        .text-green-50 { color: #f0fdf4 !important; }
        .text-green-100 { color: #dcfce7 !important; }
        .text-green-200 { color: #bbf7d0 !important; }
        .text-green-300 { color: #86efac !important; }
        .text-green-400 { color: #4ade80 !important; }
        .text-green-500 { color: #22c55e !important; }
        .text-green-600 { color: #16a34a !important; }
        .text-green-700 { color: #15803d !important; }
        .text-green-800 { color: #166534 !important; }
        .text-green-900 { color: #14532d !important; }
        
        /* Override green borders with green */
        .border-green-50 { border-color: #f0fdf4 !important; }
        .border-green-100 { border-color: #dcfce7 !important; }
        .border-green-200 { border-color: #bbf7d0 !important; }
        .border-green-300 { border-color: #86efac !important; }
        .border-green-400 { border-color: #4ade80 !important; }
        .border-green-500 { border-color: #22c55e !important; }
        .border-green-600 { border-color: #16a34a !important; }
        .border-green-700 { border-color: #15803d !important; }
        .border-green-800 { border-color: #166534 !important; }
        .border-green-900 { border-color: #14532d !important; }
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
                   <button id="btnStockModal" class="px-3 py-1.5 text-sm text-black font-bold bg-green-50 border border-green-300 rounded-md hover:bg-green-100 transition-colors">
                        <i class="fas fa-box mr-1.5 text-green-500 text-base"></i> Stok
                    </button>

                    <button id="btnIncomeModal" class="px-3 py-1.5 text-sm text-black font-bold bg-green-50 border border-green-300 rounded-md hover:bg-green-100 transition-colors">
                        <i class="fas fa-money-bill mr-1.5 text-green-500 text-base"></i> Rp 0
                    </button>

                    <button id="btnCashierModal" class="px-3 py-1.5 text-sm text-black font-bold bg-green-50 border border-green-300 rounded-md hover:bg-green-100 transition-colors">
                        <i class="fas fa-cash-register mr-1.5 text-green-500 text-base"></i> Kas kasir
                    </button>
                    
                  <button class="px-5 py-2.5 text-base text-black font-bold rounded-md hover:bg-green-50 transition-colors">
                    <i class="fas fa-user mr-2 text-green-500 text-base"></i>
                    <span id="userLabel" class="font-medium">Loading...</span>
                    </button>

                    <button id="logoutButton" class="px-3 py-1.5 text-sm text-black font-bold border border-green-300 rounded-md hover:bg-green-100 transition-colors">
                        <i class="fas fa-sign-out-alt mr-1.5 text-green-500 text-lg"></i>
                    </button>

                </div>
            </div>
        </nav>

        <div class="main-container flex h-[calc(100vh-68px)] overflow-hidden">
            <!-- Products Section -->
            <div class="products-section w-2/3 bg-white flex flex-col border-r-2 border-green-200">
                <!-- Search and Categories Section -->
                <div class="p-4">
                    <div class="search-bar mb-3">
                        <input
                            id="searchInput"
                            type="text"
                            class="w-full px-3 py-2 text-sm rounded-md border border-green-300 focus:border-green-500 focus:ring-1 focus:ring-green-500 placeholder-gray-400 transition-all duration-200"
                            placeholder="Cari produk atau scan barcode..."
                            autofocus
                        >
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
            <div class="cart-section w-1/3 bg-white flex flex-col overflow-hidden border-l-2 border-green-200">
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

                <div id="cartItems" class="cart-items-container">
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
                    <!-- Tombol Pembayaran -->
                    <button id="btnPaymentModal" class="bg-green-500 text-white border border-green-500 w-full py-2 font-semibold rounded-md text-sm mb-3 hover:bg-green-600 transition-colors">
                        <i class="fas fa-money-bill-wave mr-2"></i> Pembayaran
                    </button>

                    <!-- Tombol Riwayat Transaksi -->
                    <button id="btnHistoryModal" class="border border-gray-300 w-full py-2 text-sm rounded-md bg-white hover:bg-gray-50 transition-colors">
                        <i class="fas fa-history mr-2"></i> Riwayat Transaksi
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Include other modals -->
    @include('partials.pos.payment-modal')
    @include('partials.pos.invoice-modal')
    @include('partials.pos.cashier-modal')
    @include('partials.pos.history-modal')
    @include('partials.pos.income-modal')
    @include('partials.pos.stock')

<script src="/js/pos.js"></script>

</body>
</html>