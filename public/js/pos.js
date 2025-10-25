let processPaymentHandler;

document.addEventListener('DOMContentLoaded', function () {
  // Initialize Lucide icons
  lucide.createIcons({ icons });

  // Initialize cart and product data
  let cart = [];
  let products = [];
  let categories = [];
  let outletInfo = {
    id: parseInt(localStorage.getItem('outlet_id')) || 1,
    tax: 0,
    qris: null,
    bank_account: null,
    shift_id: parseInt(localStorage.getItem('shift_id')) || null
  };
  let selectedMember = null;
  // Variabel untuk deteksi scan
  let scanTimeout;
  let lastScanTime = 0;
  const SCAN_THRESHOLD = 100; // waktu maksimal antara karakter scan (ms)
  const MIN_BARCODE_LENGTH = 3; // panjang minimal barcode
  let barcodeScanInProgress = false;
  let barcodeBuffer = '';
  let barcodeCompleteTimeout = null;

  const outletName = localStorage.getItem('outlet_name') || 'Aladdin Karpet';

  // Set nilai ke elemen
  document.getElementById('outletName').textContent = outletName;

  // DOM elements
  const searchInput = document.getElementById('searchInput');
  const categoryTabs = document.getElementById('categoryTabs');
  const productsContainer = document.getElementById('productsContainer');
  const cartItemsContainer = document.getElementById('cartItems');
  const emptyCartElement = document.getElementById('emptyCart');
  const subtotalElement = document.getElementById('subtotal');
  const totalDiscountElement = document.getElementById('totalDiscount');
  const taxAmountElement = document.getElementById('taxAmount');
  const totalElement = document.getElementById('total');

  // Member search elements
  const memberSearchInput = document.getElementById('memberSearch');
  const memberDropdownList = document.getElementById('memberDropdownList');
  const memberResultsContainer = document.getElementById('memberResults');
  const selectedMemberContainer = document.getElementById('selectedMember');
  const memberNameElement = document.getElementById('memberName');
  const removeMemberButton = document.getElementById('removeMember');

  // Payment modal elements
  const paymentSubtotalElement = document.getElementById('paymentSubtotal');
  const paymentDiscountElement = document.getElementById('paymentDiscount');
  const paymentTaxElement = document.getElementById('paymentTax');
  const paymentGrandTotalElement = document.getElementById('paymentGrandTotal');
  const amountReceivedInput = document.getElementById('amountReceived');
  const changeAmountInput = document.getElementById('changeAmount');
  const paymentMethodsContainer = document.getElementById('paymentMethods');
  const cashPaymentSection = document.getElementById('cashPaymentSection');
  const notesInput = document.getElementById('notes');

  // Invoice elements
  const invoiceNumberElement = document.getElementById('invoiceNumber');
  const invoiceDateElement = document.getElementById('invoiceDate');
  const invoiceMemberElement = document.getElementById('invoiceMember');
  const memberNameDisplayElement = document.getElementById('memberNameDisplay');
  const memberCodeDisplayElement = document.getElementById('memberCodeDisplay');
  const invoiceItemsContainer = document.getElementById('invoiceItems');
  const invoiceSubtotalElement = document.getElementById('invoiceSubtotal');
  const invoiceDiscountElement = document.getElementById('invoiceDiscount');
  const invoiceTaxElement = document.getElementById('invoiceTax');
  const invoiceGrandTotalElement = document.getElementById('invoiceGrandTotal');
  const invoicePaymentMethodElement = document.getElementById('invoicePaymentMethod');
  const invoiceCashElement = document.getElementById('invoiceCash');
  const invoiceChangeElement = document.getElementById('invoiceChange');
  const invoiceCashRow = document.getElementById('invoiceCashRow');
  const invoiceChangeRow = document.getElementById('invoiceChangeRow');

  // API Configuration
  // const API_BASE_URL = 'https://new.tokokifa.com';
  const API_TOKEN = localStorage.getItem('token') || '';

  // Format currency input
  function formatCurrencyInput(value) {
    let num = value.replace(/[^0-9]/g, '');
    num = parseInt(num) || 0;
    return num.toLocaleString('id-ID');
  }

  // Parse currency input to number
  function parseCurrencyInput(value) {
    return parseInt(value.replace(/[^0-9]/g, '')) || 0;
  }

  // Format currency display
  function formatCurrency(num) {
    return 'Rp ' + num.toLocaleString('id-ID');
  }

  // Kontrol tampilan dropdown
  function showMemberDropdown() {
    memberDropdownList.classList.remove('hidden');
  }

  function hideMemberDropdown() {
    memberDropdownList.classList.add('hidden');
  }

  // Show SweetAlert notification
  function showNotification(message, type = 'success') {
    const Toast = Swal.mixin({
      toast: true,
      position: 'top-end',
      showConfirmButton: false,
      timer: 3000,
      timerProgressBar: true,
      didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer)
        toast.addEventListener('mouseleave', Swal.resumeTimer)
      }
    });

    let icon = 'check-circle';
    let background = '#F97316';

    if (type === 'error') {
      icon = 'x-circle';
      background = '#EF4444';
    } else if (type === 'warning') {
      icon = 'alert-circle';
      background = '#F59E0B';
    } else if (type === 'info') {
      icon = 'info';
      background = '#3B82F6';
    }

    Toast.fire({
      iconHtml: `<i data-lucide="${icon}" class="text-white"></i>`,
      title: message,
      background: background,
      color: 'white',
      iconColor: 'white'
    });

    lucide.createIcons({ icons });
  }

  // Modal functions
  function openModal(id) {
    document.getElementById(id).classList.remove('hidden');
  }

  function closeModal(id) {
    document.getElementById(id).classList.add('hidden');
  }

  // Fungsi untuk menangani scan barcode
  async function handleBarcodeScan(barcode) {
    try {
      searchInput.value = ''; // Kosongkan input

      const response = await fetch(`/api/outlets/${outletInfo.id}/products/barcode/${barcode}`, {
        method: 'GET',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${API_TOKEN}`,
          'Accept': 'application/json'
        },
        credentials: 'include'
      });

      if (!response.ok) {
        throw new Error('Produk tidak ditemukan');
      }

      const result = await response.json();

      console.log("Scan produk: ", result)

      if (!result.success || !result.data) {
        throw new Error(result.message || 'Data produk tidak valid');
      }

      const product = result.data;

      // CEK STATUS PRODUK (PERUBAHAN UTAMA)
      if (!product.is_active) {
        throw new Error('Produk ini tidak aktif dan tidak dapat dijual');
      }

      const productPrice = parseFloat(product.price) || 0;
      const productStock = product.inventory ? (parseInt(product.inventory.quantity) || 0) : 0;

      if (productStock <= 0) {
        throw new Error('Stok produk habis');
      }

      const existingItem = cart.find(item => item.id === product.id);

      if (existingItem) {
        if (existingItem.quantity + 1 > productStock) {
          throw new Error('Stok tidak mencukupi');
        }
        existingItem.quantity += 1;
        existingItem.subtotal = calculateItemSubtotal(existingItem);
      } else {
        cart.push({
          id: product.id,
          name: product.name,
          price: productPrice,
          quantity: 1,
          stock: productStock,
          discount: 0,
          subtotal: productPrice,
          is_active: product.is_active // Tambahkan status aktif ke item cart
        });
      }

      syncProductStock();
      updateCart();
      searchInput.focus();
      showNotification(`${product.name} ditambahkan ke keranjang`, 'success');

    } catch (error) {
      console.error('Error handling barcode scan:', error);
      showNotification(error.message || 'Gagal memproses barcode', 'error');
      searchInput.focus();
    }
  }

  // Modifikasi event listener searchInput yang sudah ada
  searchInput.addEventListener('input', function (e) {
    const value = e.target.value.trim();
    const now = Date.now();
    const timeSinceLastChar = now - lastScanTime;

    // Clear timeout sebelumnya
    clearTimeout(scanTimeout);
    clearTimeout(barcodeCompleteTimeout);

    if (!value) {
      barcodeScanInProgress = false;
      barcodeBuffer = '';
      return;
    }

    // Deteksi apakah ini awal dari scan barcode
    if (!barcodeScanInProgress && timeSinceLastChar < SCAN_THRESHOLD) {
      barcodeScanInProgress = true;
      barcodeBuffer = value;
    } else if (barcodeScanInProgress) {
      // Jika scan sedang berlangsung, tambahkan ke buffer
      // (tidak perlu, karena nilai lengkap sudah ada di value)
      barcodeBuffer = value;

      // Set timeout untuk menandai bahwa scan sudah selesai
      barcodeCompleteTimeout = setTimeout(() => {
        if (barcodeBuffer.length >= MIN_BARCODE_LENGTH) {
          // Proses barcode lengkap
          handleBarcodeScan(barcodeBuffer);
          searchInput.value = '';  // Kosongkan input setelah diproses
        }

        // Reset status scan
        barcodeScanInProgress = false;
        barcodeBuffer = '';
      }, 50); // Waktu tunggu singkat untuk memastikan semua karakter sudah masuk
    } else {
      // Ini adalah input manual biasa (pencarian)
      scanTimeout = setTimeout(() => {
        const activeCategory = document.querySelector('#categoryTabs .nav-link.active')?.getAttribute('data-category') || 'all';
        renderProducts(activeCategory, value);
      }, 300);
    }

    lastScanTime = now;
  });

  // Tambahkan juga handler untuk keypress Enter (untuk scanner yang mengirim Enter)

  // Fetch current shift
  async function fetchCurrentShift() {
    try {
      const response = await fetch(`/api/shifts/${outletInfo.shift_id}`, {
        method: 'GET',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${API_TOKEN}`,
          'Accept': 'application/json'
        },
        credentials: 'include'
      });

      if (!response.ok) {
        throw new Error(`HTTP error! Status: ${response.status}`);
      }

      const data = await response.json();

      if (data.success && data.data) {
        outletInfo.shift_id = data.data.id;
      }
    } catch (error) {
      console.error('Error fetching current shift:', error);
    }
  }

  // Fetch outlet information
  async function fetchOutletInfo() {
    try {
      const response = await fetch(`/api/outlets/${outletInfo.id}`, {
        method: 'GET',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${API_TOKEN}`,
          'Accept': 'application/json'
        },
        credentials: 'include'
      });

      if (!response.ok) {
        throw new Error(`HTTP error! Status: ${response.status}`);
      }

      const data = await response.json();

      if (data.success) {
        outletInfo.tax = data.data.tax || 0;
        outletInfo.qris = data.data.qris_url;
        outletInfo.bank_account = {
          atas_nama: data.data.atas_nama_bank,
          bank: data.data.nama_bank,
          nomor: data.data.nomor_transaksi_bank
        };

        // Update tax display
        taxAmountElement.textContent = `Pajak (${outletInfo.tax}%)`;

        // Get current shift
        await fetchCurrentShift();
      }
    } catch (error) {
      console.error('Error fetching outlet info:', error);
    }
  }

  // Fetch products from API
  async function fetchProducts() {
    try {
      if (!API_TOKEN) {
        throw new Error('Token tidak ditemukan');
      }

      const response = await fetch(`/api/products/outlet/${outletInfo.id}`, {
        method: 'GET',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${API_TOKEN}`,
          'Accept': 'application/json'
        },
        credentials: 'include'
      });

      if (response.status === 401) {
        localStorage.removeItem('token');
        window.location.href = '/login';
        return;
      }

      if (!response.ok) {
        throw new Error(`HTTP error! Status: ${response.status}`);
      }

      const data = await response.json();


      if (data.success) {
        products = data.data.map(product => ({
          id: product.id,
          name: product.name,
          barcode: product.barcode,
          price: parseFloat(product.price),
          quantity: product.quantity,
          min_stock: product.min_stock,
          category: product.category || { name: 'uncategorized' },
          outlets: product.outlets
        }));

        organizeProductsByCategory();
        renderProducts();
        renderCategories();

        // Store products in localStorage
        const storageData = {
          timestamp: new Date().getTime(),
          products: products
        };
        localStorage.setItem('posProducts', JSON.stringify(storageData));
      } else {
        throw new Error(data.message || 'Failed to load products');
      }
    } catch (error) {
      console.error('Error fetching products:', error);
      loadProductsFromLocalStorage();
    }
  }

  // Load products from localStorage
  function loadProductsFromLocalStorage() {
    try {
      const storedData = localStorage.getItem('posProducts');
      if (storedData) {
        const parsedData = JSON.parse(storedData);

        if (parsedData.products && Array.isArray(parsedData.products)) {
          products = parsedData.products;
        } else if (Array.isArray(parsedData)) {
          products = parsedData;
        }

        if (products.length > 0) {
          organizeProductsByCategory();
          renderProducts();
          renderCategories();
          return true;
        }
      }
      return false;
    } catch (error) {
      console.error('Error loading products from localStorage:', error);
      return false;
    }
  }

  // Organize products by category
  function organizeProductsByCategory() {
    const uniqueCategories = [...new Set(products.map(product =>
      product.category && product.category.name ?
        product.category.name.toLowerCase() : 'uncategorized'
    ))];
    categories = ['all', ...uniqueCategories];
  }

  // Fungsi untuk sinkronkan stok produk setelah perubahan di keranjang
  function syncProductStock() {
    // Update stok produk di array products berdasarkan isi keranjang
    products.forEach(product => {
      const cartItem = cart.find(item => item.id === product.id);
      if (cartItem) {
        // Hitung stok tersedia (stok asli - yang ada di keranjang)
        product.availableStock = product.quantity - cartItem.quantity;
      } else {
        product.availableStock = product.quantity;
      }
    });

    // Render ulang produk untuk update tampilan stok
    const activeCategory = document.querySelector('#categoryTabs .nav-link.active')?.getAttribute('data-category') || 'all';
    const searchTerm = searchInput.value;
    renderProducts(activeCategory, searchTerm);
  }

  // Fungsi untuk menambah quantity item di keranjang
  function increaseQuantity(index) {
    const item = cart[index];
    const product = products.find(p => p.id === item.id);

    if (!product) return;

    // Hitung stok tersedia
    const availableStock = product.quantity - (item.quantity || 0);

    if (availableStock <= 0) {
      showNotification('Stok produk habis', 'error');
      return;
    }

    item.quantity += 1;
    item.subtotal = calculateItemSubtotal(item);

    // Sinkronkan stok
    syncProductStock();
    updateCart();
  }

  // Fungsi untuk mengurangi quantity item di keranjang
  function decreaseQuantity(index) {
    const item = cart[index];

    if (item.quantity > 1) {
      item.quantity -= 1;
      item.subtotal = calculateItemSubtotal(item);

      // Sinkronkan stok
      syncProductStock();
      updateCart();
    } else {
      // Jika quantity = 1, hapus item dari keranjang
      cart.splice(index, 1);
      syncProductStock();
      updateCart();
    }
  }

  // Render categories to the tabs
  function renderCategories() {
    categoryTabs.innerHTML = '';

    categories.forEach(category => {
      const categoryName = category === 'all' ? 'Semua' :
        category === 'uncategorized' ? 'Lainnya' :
          category.charAt(0).toUpperCase() + category.slice(1);
      const isActive = category === 'all';

      const tabItem = document.createElement('li');
      tabItem.className = 'inline-flex';

      tabItem.innerHTML = `
                    <a href="#" data-category="${category}" class="nav-link ${isActive ? 'active' : ''} px-3 py-1.5 text-xs font-medium rounded-full mr-2 ${isActive ? 'bg-green-500 text-white border-green-400' : 'bg-white text-gray-700 border-gray-200 hover:bg-gray-100'} border shadow-sm transition-all duration-200">
                        ${categoryName}
                    </a>
                `;

      categoryTabs.appendChild(tabItem);
    });

    // Add event listeners to category tabs
    document.querySelectorAll('#categoryTabs .nav-link').forEach(tab => {
      tab.addEventListener('click', function (e) {
        e.preventDefault();

        // Update active tab
        document.querySelectorAll('#categoryTabs .nav-link').forEach(t => {
          t.classList.remove('active', 'bg-green-500', 'text-white', 'border-green-400');
          t.classList.add('bg-white', 'text-gray-700', 'border-gray-200');
        });

        this.classList.remove('bg-white', 'text-gray-700', 'border-gray-200');
        this.classList.add('active', 'bg-green-500', 'text-white', 'border-green-400');

        const category = this.getAttribute('data-category');
        const searchTerm = searchInput.value;
        renderProducts(category, searchTerm);
      });
    });
  }

  // Render products to the DOM
  function renderProducts(filterCategory = 'all', searchTerm = '') {
    productsContainer.innerHTML = '';

    if (!products || products.length === 0) {
      productsContainer.innerHTML = `
                    <div class="empty-cart p-8 text-center">
                        <i data-lucide="package-x" class="w-12 h-12 mx-auto text-gray-300"></i>
                        <p class="text-gray-500 text-lg font-medium mt-4">Tidak ada produk tersedia</p>
                        <p class="text-gray-400 text-sm mt-1">Silakan perbarui data produk</p>
                    </div>
                `;
      lucide.createIcons({ icons });
      return;
    }

    const filteredProducts = products.filter(product => {
      const productCategory = product.category && product.category.name ?
        product.category.name.toLowerCase() : 'uncategorized';

      const matchesCategory = filterCategory === 'all' || productCategory === filterCategory;
      const matchesSearch =
        product.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
        (product.barcode && product.barcode.toLowerCase().includes(searchTerm.toLowerCase()));

      return matchesCategory && matchesSearch;
    });

    if (filteredProducts.length === 0) {
      productsContainer.innerHTML = `
                    <div class="empty-cart p-8 text-center">
                        <i data-lucide="search-x" class="w-12 h-12 mx-auto text-gray-300"></i>
                        <p class="text-gray-500 text-lg font-medium mt-4">Produk tidak ditemukan</p>
                        <p class="text-gray-400 text-sm mt-1">Coba kata kunci atau kategori lain</p>
                    </div>
                `;
      lucide.createIcons({ icons });
      return;
    }

    filteredProducts.forEach((product, index) => {
      const productElement = document.createElement('div');
      productElement.className = 'product-item mb-3';

      const categoryName = product.category && product.category.name ?
        product.category.name : 'Uncategorized';

      productElement.setAttribute('data-category', categoryName.toLowerCase());
      productElement.setAttribute('data-name', product.name.toLowerCase());

      // Hitung stok yang tersisa (total stok - yang sudah di keranjang - yang di bonus)
      const cartItem = cart.find(item => item.id === product.id);
      const reservedInCart = cartItem ? cartItem.quantity : 0;
      
      // Hitung reserved quantity dari CartManager jika tersedia
      let reservedInBonus = 0;
      if (typeof window.cartManager !== 'undefined' && window.cartManager.getReservedQuantity) {
        const totalReserved = window.cartManager.getReservedQuantity(product.id);
        reservedInBonus = totalReserved - reservedInCart; // karena total sudah termasuk cart
      } else {
        // Fallback jika CartManager tidak tersedia
        reservedInBonus = 0;
      }
      
      const availableStock = (product.quantity || 0) - reservedInCart - reservedInBonus;

      // Tampilkan berbeda untuk produk tidak aktif
      if (product.is_active === false) {
        productElement.innerHTML = `
                <div class="product-card flex justify-between items-center p-4 bg-gray-100 border border-gray-300 rounded-lg opacity-75">
                    <div>
                        <div class="product-name text-base font-medium text-gray-500 line-through">${product.name}</div>
                        <div class="text-xs bg-red-100 text-red-800 px-2 py-1 rounded-full mb-1 inline-block">TIDAK AKTIF</div>
                        <div class="product-price text-gray-500 font-semibold text-base">Rp ${parseFloat(product.price).toLocaleString('id-ID')}</div>
                    </div>
                    <button class="bg-gray-300 text-gray-600 border border-gray-400 rounded px-4 py-2 text-sm w-24 cursor-not-allowed" disabled>
                        <i data-lucide="x-circle" class="w-4 h-4 inline mr-1"></i> Tidak Aktif
                    </button>
                </div>
            `;
        productsContainer.appendChild(productElement);
        return;
      }

      const isOutOfStock = availableStock <= 0;
      const isLowStock = availableStock > 0 && availableStock <= (product.min_stock || 5);

      productElement.innerHTML = `
            <div class="product-card flex justify-between items-center p-4 bg-white border border-gray-200 rounded-lg hover:shadow-sm transition-all">
                <div>
                    <div class="product-name text-base font-medium">${product.name} (${availableStock})</div>
                    <div class="product-price text-green-500 font-semibold text-base">Rp ${parseFloat(product.price).toLocaleString('id-ID')}</div>
                    ${isLowStock ? '<span class="low-stock bg-yellow-100 px-2 py-1 rounded text-sm text-yellow-800 font-medium mt-1 inline-block">Produk menipis</span>' : ''}
                </div>
                <div class="flex items-center">
                    <span class="product-category text-xs bg-gray-100 text-gray-600 px-2.5 py-1 rounded-full mr-3">
                        ${categoryName.toUpperCase()}
                    </span>
                    ${isOutOfStock ?
          '<button class="bg-gray-100 text-gray-500 border border-gray-300 rounded px-4 py-2 text-sm w-24">Habis</button>' :
          `<button class="btn-add-to-cart bg-green-500 text-white border-none rounded px-4 py-2 text-sm flex items-center justify-center w-24 hover:bg-green-600 transition-colors">
                            <i data-lucide="plus" class="w-4 h-4 mr-1"></i> Tambah
                        </button>`
        }
                </div>
            </div>
        `;

      productsContainer.appendChild(productElement);
    });

    // Refresh Lucide icons
    lucide.createIcons({ icons });

    // Add event listeners to all "Add to Cart" buttons
    document.querySelectorAll('.btn-add-to-cart').forEach(button => {
      button.addEventListener('click', function () {
        const productCard = this.closest('.product-card');
        const productName = productCard.querySelector('.product-name').textContent.split(' (')[0];
        const product = products.find(p => p.name === productName);

        if (!product || product.is_active === false) {
          showNotification('Produk tidak tersedia', 'error');
          return;
        }

        // Hitung stok yang tersedia (termasuk reserved untuk bonus)
        const cartItem = cart.find(item => item.id === product.id);
        const reservedInCart = cartItem ? cartItem.quantity : 0;
        
        let reservedInBonus = 0;
        if (typeof window.cartManager !== 'undefined' && window.cartManager.getReservedQuantity) {
          const totalReserved = window.cartManager.getReservedQuantity(product.id);
          reservedInBonus = totalReserved - reservedInCart;
        }
        
        const availableStock = (product.quantity || 0) - reservedInCart - reservedInBonus;

        // Validasi stok
        if (availableStock <= 0) {
          showNotification('Stok produk habis', 'error');
          return;
        }

        const existingItem = cart.find(item => item.id === product.id);

        if (existingItem) {
          // Cek apakah penambahan melebihi stok yang tersedia
          if (existingItem.quantity + 1 > product.quantity) {
            showNotification('Stok tidak mencukupi', 'error');
            return;
          }
          existingItem.quantity += 1;
          existingItem.subtotal = calculateItemSubtotal(existingItem);
        } else {
          cart.push({
            id: product.id,
            name: product.name,
            price: product.price,
            quantity: 1,
            stock: product.quantity,
            discount: 0,
            subtotal: product.price,
            is_active: product.is_active
          });
        }

        // Update tampilan keranjang
        updateCart();

        // Render ulang produk untuk update stok secara instan
        const activeFilter = document.querySelector('#categoryTabs .nav-link.active')?.getAttribute('data-category') || 'all';
        const currentSearch = document.getElementById('searchProduct')?.value || '';
        renderProducts(activeFilter, currentSearch);
      });
    });
  }

  // Fungsi untuk menghitung total quantity di cart
  function calculateTotalQty() {
    return cart.reduce((total, item) => total + (item.quantity || 0), 0);
  }

  // Update cart display
  function updateCart() {
    cartItemsContainer.innerHTML = '';

    let rawSubtotal = 0;
    let totalDiscount = 0;
    let orderSubtotal = 0;
    let tax = 0;
    let grandTotal = 0;
    let totalQty = calculateTotalQty();

    if (cart.length === 0) {
      emptyCartElement.classList.remove('hidden');
      cartItemsContainer.appendChild(emptyCartElement);
      subtotalElement.textContent = 'Rp 0';
      totalDiscountElement.textContent = 'Rp 0';
      taxAmountElement.textContent = 'Rp 0';
      totalElement.textContent = 'Rp 0';
      document.getElementById('totalQty').textContent = '0';
      return;
    } else {
      emptyCartElement.classList.add('hidden');
    }

    cart.forEach((item, index) => {
      // Pastikan semua properti ada
      const itemPrice = item.price || 0;
      const itemQuantity = item.quantity || 0;
      const itemDiscount = item.discount || 0;

      const itemTotal = itemPrice * itemQuantity;
      const itemSubtotal = itemTotal - itemDiscount;

      rawSubtotal += itemTotal;
      totalDiscount += itemDiscount;
      orderSubtotal += itemSubtotal;

      item.subtotal = itemSubtotal;

      const cartItemElement = document.createElement('div');
      cartItemElement.className = 'cart-item hover:bg-gray-50';


      cartItemElement.innerHTML = `
                        <div class="cart-item-grid">
                            <div class="product-info">
                                <div class="product-name font-medium text-gray-800">${item.name}</div>
                                <div class="product-price text-sm text-gray-500">Rp ${item.price.toLocaleString('id-ID')}</div>
                            </div>
                            
                            <div class="quantity-control">
                                <div class="qty-control">
                                    <button class="btn-decrease px-2 py-1 border border-gray-300 bg-gray-100 rounded hover:bg-gray-200" data-index="${index}">
                                        <i data-lucide="minus" class="w-3 h-3"></i>
                                    </button>
                                    <input type="text" class="qty-input" value="${item.quantity}" data-index="${index}">
                                    <button class="btn-increase px-2 py-1 border border-gray-300 bg-gray-100 rounded hover:bg-gray-200" data-index="${index}">
                                        <i data-lucide="plus" class="w-3 h-3"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="discount-control">
                                <input type="text" class="discount-input" value="Rp ${item.discount.toLocaleString('id-ID')}" data-index="${index}" placeholder="Rp 0">
                            </div>
                            
                            <div class="subtotal text-right font-medium">
                                Rp ${itemSubtotal.toLocaleString('id-ID')}
                            </div>
                            
                            <div class="delete-btn">
                                <button class="btn-remove text-gray-400 hover:text-red-500" data-index="${index}">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </div>
                    `;

      cartItemsContainer.appendChild(cartItemElement);
    });

    // Update total qty display
    document.getElementById('totalQty').textContent = totalQty.toString();

    // Calculate tax and grand total
    tax = orderSubtotal * (outletInfo.tax / 100);
    grandTotal = orderSubtotal + tax;

    subtotalElement.textContent = `Rp ${rawSubtotal.toLocaleString('id-ID')}`;
    totalDiscountElement.textContent = `Rp ${totalDiscount.toLocaleString('id-ID')}`;
    taxAmountElement.textContent = `Rp ${tax.toLocaleString('id-ID')}`;
    totalElement.textContent = `Rp ${grandTotal.toLocaleString('id-ID')}`;

    lucide.createIcons({ icons });

    document.querySelectorAll('.qty-input').forEach(input => {
      input.addEventListener('change', function () {
        const index = parseInt(this.getAttribute('data-index'));
        const newQty = parseInt(this.value) || 1;
        const item = cart[index];

        if (newQty > item.stock) {
          showNotification(`Stok hanya tersedia ${item.stock}`, 'error');
          this.value = item.quantity;
          return;
        }

        cart[index].quantity = newQty;
        cart[index].subtotal = calculateItemSubtotal(cart[index]);

        syncProductStock();

        updateCart();
      });
    });

    // Modifikasi event listener untuk btn-increase
    document.querySelectorAll('.btn-increase').forEach(button => {
      button.addEventListener('click', function () {
        const index = parseInt(this.getAttribute('data-index'));
        const item = cart[index];

        if (item.quantity + 1 > item.stock) {
          showNotification('Stok tidak mencukupi', 'error');
          return;
        }

        item.quantity += 1;
        item.subtotal = calculateItemSubtotal(item);

        // Sinkronkan stok produk setelah menambah quantity
        syncProductStock();

        updateCart();
      });
    });

    // Modifikasi event listener untuk btn-decrease
    document.querySelectorAll('.btn-decrease').forEach(button => {
      button.addEventListener('click', function () {
        const index = parseInt(this.getAttribute('data-index'));
        if (cart[index].quantity > 1) {
          cart[index].quantity -= 1;
          cart[index].subtotal = calculateItemSubtotal(cart[index]);

          // Sinkronkan stok produk setelah mengurangi quantity
          syncProductStock();

          updateCart();
        }
      });
    });

    // Modifikasi event listener untuk btn-remove
    document.querySelectorAll('.btn-remove').forEach(button => {
      button.addEventListener('click', function () {
        const index = parseInt(this.getAttribute('data-index'));
        cart.splice(index, 1);

        // Sinkronkan stok produk setelah menghapus item dari cart
        syncProductStock();

        updateCart();
      });
    });

    // Add event listeners to discount inputs
    document.querySelectorAll('.discount-input').forEach(input => {
      // Format on blur
      input.addEventListener('blur', function () {
        const formattedValue = formatCurrencyInput(this.value);
        this.value = formattedValue ? `Rp ${formattedValue}` : 'Rp 0';

        const index = parseInt(this.getAttribute('data-index'));
        const discount = parseCurrencyInput(this.value);
        cart[index].discount = discount;
        cart[index].subtotal = calculateItemSubtotal(cart[index]);
        updateCart();
      });

      // Remove formatting on focus for easier editing
      input.addEventListener('focus', function () {
        const numValue = parseCurrencyInput(this.value);
        this.value = numValue.toString();
      });

      // Handle change event
      input.addEventListener('change', function () {
        const index = parseInt(this.getAttribute('data-index'));
        const discount = parseCurrencyInput(this.value);
        cart[index].discount = discount;
        cart[index].subtotal = calculateItemSubtotal(cart[index]);
        updateCart();
      });
    });
  }

  function calculateItemSubtotal(item) {
    const basePrice = item.price * item.quantity;
    const discountAmount = item.discount || 0;
    return Math.max(0, basePrice - discountAmount);
  }

  // Search members
  memberSearchInput.addEventListener('input', function () {
    const query = this.value.trim();

    if (query.length < 2) {
      memberResultsContainer.innerHTML = '';
      hideMemberDropdown();
      return;
    }

    // Fetch from the main members endpoint
    fetch(`/api/members`, {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${API_TOKEN}`,
        'Accept': 'application/json'
      },
      credentials: 'include'
    })
      .then(response => response.json())
      .then(data => {
        if (data.success && data.data.length > 0) {
          // Filter members based on the search query
          const filteredMembers = data.data.filter(member =>
            member.name.toLowerCase().includes(query.toLowerCase()) ||
            (member.member_code && member.member_code.toLowerCase().includes(query.toLowerCase()))
          );

          memberResultsContainer.innerHTML = '';

          if (filteredMembers.length > 0) {
            filteredMembers.forEach(member => {
              const memberItem = document.createElement('div');
              memberItem.className = 'member-item';
              memberItem.innerHTML = `
                                    <div class="font-medium">${member.name}</div>
                                    <div class="text-sm text-gray-500">${member.member_code || 'No Code'}</div>
                                `;

              memberItem.addEventListener('click', function () {
                selectMember(member);
                hideMemberDropdown();
              });

              memberResultsContainer.appendChild(memberItem);
            });

            showMemberDropdown();
          } else {
            memberResultsContainer.innerHTML = '<div class="member-item text-gray-500">Tidak ditemukan</div>';
            showMemberDropdown();
          }
        } else {
          memberResultsContainer.innerHTML = '<div class="member-item text-gray-500">Tidak ditemukan</div>';
          showMemberDropdown();
        }
      })
      .catch(error => {
        console.error('Error fetching members:', error);
        memberResultsContainer.innerHTML = '<div class="member-item text-gray-500">Terjadi kesalahan</div>';
        showMemberDropdown();
      });
  });

  // Select member
  function selectMember(member) {
    selectedMember = member;
    memberNameElement.textContent = `${member.name} (${member.member_code})`;
    selectedMemberContainer.classList.remove('hidden');
    memberSearchInput.value = '';
    hideMemberDropdown();
  }

  // Remove member
  removeMemberButton.addEventListener('click', function () {
    selectedMember = null;
    selectedMemberContainer.classList.add('hidden');
  });

  memberSearchInput.addEventListener('click', function () {
    if (this.value.trim().length >= 2) {
      showMemberDropdown();
    }
  });

  // Tampilkan dropdown ketika input mendapatkan fokus
  memberSearchInput.addEventListener('focus', function () {
    if (this.value.trim().length >= 2) {
      showMemberDropdown();
    }
  });

  // Tutup dropdown ketika klik di luar dropdown
  document.addEventListener('click', function (event) {
    if (!memberSearchInput.contains(event.target) && !memberDropdownList.contains(event.target)) {
      hideMemberDropdown();
    }
  });

  // Navigasi dropdown dengan keyboard
  memberSearchInput.addEventListener('keydown', function (event) {
    const items = memberResultsContainer.querySelectorAll('.member-item');
    const activeItem = memberResultsContainer.querySelector('.member-item.active');

    if (items.length === 0) return;

    if (event.key === 'ArrowDown') {
      event.preventDefault();

      if (!activeItem) {
        items[0].classList.add('active');
      } else {
        const nextItem = activeItem.nextElementSibling;
        if (nextItem) {
          activeItem.classList.remove('active');
          nextItem.classList.add('active');
        }
      }
    } else if (event.key === 'ArrowUp') {
      event.preventDefault();

      if (!activeItem) {
        items[items.length - 1].classList.add('active');
      } else {
        const prevItem = activeItem.previousElementSibling;
        if (prevItem) {
          activeItem.classList.remove('active');
          prevItem.classList.add('active');
        }
      }
    } else if (event.key === 'Enter') {
      if (activeItem) {
        event.preventDefault();

        // Simulate click on the active item
        activeItem.click();
      }
    } else if (event.key === 'Escape') {
      hideMemberDropdown();
    }
  });

  // Render payment methods
  function renderPaymentMethods() {
    paymentMethodsContainer.innerHTML = '';

    const methods = [
      { id: 'cash', name: 'Tunai', icon: 'wallet' },
      { id: 'qris', name: 'QRIS', icon: 'qr-code' },
      { id: 'transfer', name: 'Transfer Bank', icon: 'banknote' }
    ];

    methods.forEach(method => {
      const methodElement = document.createElement('div');
      methodElement.className = 'payment-method';
      methodElement.innerHTML = `
                        <div class="flex items-center">
                            <i data-lucide="${method.icon}" class="w-5 h-5 mr-3 text-gray-600"></i>
                            <span class="font-medium">${method.name}</span>
                        </div>
                    `;

      methodElement.addEventListener('click', function () {
        document.querySelectorAll('.payment-method').forEach(m => {
          m.classList.remove('selected');
        });
        this.classList.add('selected');
        document.getElementById('paymentMethod').value = method.id;

        // Handle cash section
        if (method.id === 'cash') {
          cashPaymentSection.style.display = 'block';
          document.getElementById('transferDetails').classList.add('hidden');
          document.getElementById('qrisDetails').classList.add('hidden');
        } else {
          cashPaymentSection.style.display = 'none';
          amountReceivedInput.value = '';
          changeAmountInput.value = '';
        }

        // Handle transfer details
        if (method.id === 'transfer') {
          document.getElementById('transferDetails').classList.remove('hidden');
          document.getElementById('qrisDetails').classList.add('hidden');

          document.getElementById('bankAccountName').textContent =
            outletInfo.bank_account?.atas_nama || '-';
          document.getElementById('bankName').textContent =
            outletInfo.bank_account?.bank || '-';
          document.getElementById('bankAccountNumber').textContent =
            outletInfo.bank_account?.nomor || '-';
        }
        // Handle QRIS details
        else if (method.id === 'qris') {
          document.getElementById('transferDetails').classList.add('hidden');
          document.getElementById('qrisDetails').classList.remove('hidden');

          const qrisContainer = document.getElementById('qrisImageContainer');
          qrisContainer.innerHTML = '';

          if (outletInfo.qris) {
            const img = document.createElement('img');
            img.src = outletInfo.qris;
            img.alt = 'QRIS Payment';
            img.className = 'w-48 h-48 object-contain';
            qrisContainer.appendChild(img);
          } else {
            qrisContainer.innerHTML = `
                                    <div class="text-center py-8 text-gray-400">
                                        <i data-lucide="image-off" class="w-12 h-12 mx-auto mb-2"></i>
                                        <p>QR Code tidak tersedia</p>
                                    </div>
                                `;
            lucide.createIcons({ icons });
          }
        }
        else {
          document.getElementById('transferDetails').classList.add('hidden');
          document.getElementById('qrisDetails').classList.add('hidden');
        }
      });

      paymentMethodsContainer.appendChild(methodElement);
    });

    // Add hidden input for selected payment method
    const methodInput = document.createElement('input');
    methodInput.type = 'hidden';
    methodInput.id = 'paymentMethod';
    methodInput.value = '';
    paymentMethodsContainer.appendChild(methodInput);

    lucide.createIcons({ icons });
  }

  function setPaymentButtonState(isProcessing) {
    const button = document.getElementById('btnProcessPayment');

    if (isProcessing) {
      // Disable button dan ubah tampilan
      button.disabled = true;
      button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...';
      button.classList.remove('hover:bg-green-600', 'bg-green-500');
      button.classList.add('bg-gray-400', 'cursor-not-allowed');
    } else {
      // Enable button dan kembalikan tampilan normal
      button.disabled = false;
      button.innerHTML = 'Proses Pembayaran';
      button.classList.remove('bg-gray-400', 'cursor-not-allowed');
      button.classList.add('bg-green-500', 'hover:bg-green-600');
    }
  }

  function setupPaymentButton() {
    // Remove old event listener if it exists
    const btn = document.getElementById('btnProcessPayment');
    btn.removeEventListener('click', processPaymentHandler);

    // Add new event listener
    btn.addEventListener('click', processPaymentHandler);
  }

  // Show payment modal
  function showPaymentModal() {
    if (cart.length === 0) {
      showNotification('Keranjang belanja kosong', 'warning');
      return;
    }

    // Calculate totals
    const rawSubtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    const totalDiscount = cart.reduce((sum, item) => sum + (item.discount || 0), 0);
    const orderSubtotal = rawSubtotal - totalDiscount;
    const tax = orderSubtotal * (outletInfo.tax / 100);
    const grandTotal = orderSubtotal + tax;

    paymentGrandTotalElement.textContent = formatCurrency(grandTotal);

    // Reset payment inputs
    amountReceivedInput.value = '';
    changeAmountInput.value = '';
    notesInput.value = '';

    // Render payment methods
    renderPaymentMethods();

    // Add event listener for amount received input
    amountReceivedInput.addEventListener('input', function () {
      const received = parseCurrencyInput(this.value);
      const change = received - grandTotal;

      if (change >= 0) {
        changeAmountInput.value = formatCurrency(change);
      } else {
        changeAmountInput.value = 'Rp 0';
      }
    });

    // CRITICAL FIX: Clone and replace the payment button to remove all event listeners
    const oldButton = document.getElementById('btnProcessPayment');
    const newButton = oldButton.cloneNode(true);
    oldButton.parentNode.replaceChild(newButton, oldButton);

    // Pastikan button dalam state normal saat modal dibuka
    setPaymentButtonState(false);

    // Add the event listener to the fresh button dengan disable functionality
    document.getElementById('btnProcessPayment').addEventListener('click', async function () {
      // Disable button saat mulai proses
      setPaymentButtonState(true);

      try {
        // Panggil function processPayment
        await processPayment(grandTotal);

      } catch (error) {
        // Jika terjadi error, tampilkan notifikasi
        console.error('Payment error:', error);
        showNotification('Terjadi kesalahan saat memproses pembayaran', 'error');

      } finally {
        // Enable kembali button setelah selesai (baik sukses atau error)
        setPaymentButtonState(false);
      }
    });

    openModal('paymentModal');
  }

  async function processPayment(grandTotal) {
    const paymentMethod = document.getElementById('paymentMethod').value;
    const amountReceived = parseCurrencyInput(amountReceivedInput.value);
    const notes = notesInput.value;

    if (!paymentMethod) {
      showNotification('Pilih metode pembayaran terlebih dahulu', 'warning');
      return;
    }

    if (paymentMethod === 'cash' && amountReceived < grandTotal) {
      showNotification('Jumlah uang tidak mencukupi', 'warning');
      return;
    }

    try {
      // Prepare transaction data
      const transactionData = {
        outlet_id: outletInfo.id,
        shift_id: outletInfo.shift_id,
        items: cart.map(item => ({
          product_id: item.id,
          quantity: item.quantity,
          discount: item.discount,
          price: item.price
        })),
        payment_method: paymentMethod,
        notes: notes,
        tax: outletInfo.tax,
        discount: cart.reduce((sum, item) => sum + (item.discount || 0), 0),
        member_id: selectedMember ? selectedMember.id : null
      };

      // For cash payments, include amount received
      if (paymentMethod === 'cash') {
        transactionData.total_paid = amountReceived;
      }

      // Send transaction to server
      const response = await fetch(`/api/orders`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${API_TOKEN}`,
          'Accept': 'application/json'
        },
        body: JSON.stringify(transactionData),
        credentials: 'include'
      });

      if (!response.ok) {
        const errorData = await response.json().catch(() => ({}));
        throw new Error(errorData.message || `HTTP error! Status: ${response.status}`);
      }

      const data = await response.json();
      console.log('Payment response:', data); // Debugging

      if (data.success) {
        // Simpan data transaksi ke variabel global
        currentOrder = {
          id: data.data.id, // Pastikan ini sesuai dengan struktur response API
          items: cart,
          payment_method: paymentMethod,
          total: grandTotal,
          data: data.data,
          outlet: outletInfo  // Simpan seluruh data response
        };

        // Debugging
        console.log('Current order stored:', currentOrder);

        // Show invoice
        showInvoice(data.data, amountReceived, paymentMethod);

        // Clear cart
        cart = [];
        updateCart();

        // Clear member
        selectedMember = null;
        selectedMemberContainer.classList.add('hidden');

        // Close payment modal
        closeModal('paymentModal');
      } else {
        throw new Error(data.message || 'Failed to process payment');
      }
    } catch (error) {
      console.error('Error processing payment:', error);
      showNotification(error.message || 'Gagal memproses pembayaran', 'error');
    }
  }

  function showLoading(message) {
    // Remove existing loading if any
    const existing = document.querySelector('.loading-overlay');
    if (existing) {
      existing.remove();
    }

    const overlay = document.createElement('div');
    overlay.className = 'loading-overlay fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
    overlay.innerHTML = `
                    <div class="bg-white p-6 rounded-lg shadow-lg text-center max-w-sm mx-4">
                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mx-auto mb-4"></div>
                        <p class="text-gray-700 font-medium">${message}</p>
                    </div>
                `;
    document.body.appendChild(overlay);
    return overlay;
  }

  function hideLoading(overlay) {
    if (overlay && overlay.parentNode) {
      overlay.parentNode.removeChild(overlay);
    }

    // Fallback: remove any loading overlay
    const existingLoading = document.querySelector('.loading-overlay');
    if (existingLoading) {
      existingLoading.remove();
    }
  }

  // Show invoice
  function showInvoice(order, amountReceived, paymentMethod) {

    // Open invoice modal
    openModal('successPaymentModal');
  }

  // Print invoice
  function printInvoice() {
    window.print();
  }

  // Event listeners
  searchInput.addEventListener('input', function () {
    const searchTerm = this.value;
    const activeCategory = document.querySelector('#categoryTabs .nav-link.active')?.getAttribute('data-category') || 'all';
    renderProducts(activeCategory, searchTerm);
  });

  document.getElementById('btnPaymentModal').addEventListener('click', showPaymentModal);

  // Initialize
  window.clearCart = function () {
    cart = [];
    updateCart();
    showNotification('Keranjang telah dikosongkan', 'info');
  };

  window.refreshProducts = function () {
    fetchProducts();
  };

  // Load data
  fetchOutletInfo();
  updateCart();
  loadProductsFromLocalStorage();
  fetchProducts();
});

// Logout function
document.getElementById('logoutButton').addEventListener('click', function () {
  fetch('/api/logout', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Authorization': 'Bearer ' + localStorage.getItem('token')
    },
    credentials: 'include'
  })
    .then(res => {
      if (!res.ok) {
        throw new Error(`Logout failed with status ${res.status}`);
      }
      return res.json();
    })
    .then(() => {
      localStorage.removeItem('token');
      window.location.href = '/';
    })
    .catch(err => {
      console.error('Logout error:', err);
      showNotification('Gagal logout!', 'error');
    });
});

//history-modal
document.getElementById('btnHistoryModal').addEventListener('click', function () {
  openModal('historyModal');

});

document.getElementById('btnStockModal').addEventListener('click', function () {
  openModal('stockModal');

});

document.getElementById('btnIncomeModal').addEventListener('click', function () {
  openModal('incomeModal');

});

document.getElementById('btnCashierModal').addEventListener('click', function () {
  openModal('cashierModal');

});

// Load user data
document.addEventListener("DOMContentLoaded", function () {
  const token = localStorage.getItem("token");
  const userLabel = document.getElementById("userLabel");

  if (!token) {
    console.warn("Token tidak ditemukan di localStorage.");
    userLabel.innerText = "Belum login";
    return;
  }

  fetch("/api/me", {
    method: "GET",
    headers: {
      "Content-Type": "application/json",
      "Authorization": `Bearer ${token}`
    },
    credentials: 'include'
  })
    .then(response => {
      if (response.status === 401) {
        localStorage.removeItem('token');
        window.location.href = '/login';
        return;
      }

      if (!response.ok) {
        throw new Error(`HTTP status ${response.status}`);
      }
      return response.json();
    })
    .then(data => {
      if (data.success && data.data) {
        const user = data.data;
        userLabel.innerText = user.name || user.email || "Pengguna";
      } else {
        userLabel.innerText = "User tidak ditemukan";
      }
    })
    .catch(error => {
      console.error("Error saat ambil data user:", error);
      userLabel.innerText = "Gagal ambil user";
    });
});