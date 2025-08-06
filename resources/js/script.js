document.addEventListener('DOMContentLoaded', function() {
    // Sample product data
    const products = [
        { id: 1, name: 'Bolu Pisang', price: 17500, stock: 20, category: 'cake' },
        { id: 2, name: 'Bolu Meses', price: 16500, stock: 1, category: 'cake' },
        { id: 3, name: 'Bolu Kismis', price: 20000, stock: 0, category: 'cake' },
        { id: 4, name: 'Bolu Keju Meses', price: 25000, stock: 0, category: 'cake' },
        { id: 5, name: 'Bolu Lapis Talas', price: 30000, stock: 0, category: 'cake' },
        { id: 6, name: 'Cake Harmoni', price: 23500, stock: 0, category: 'cake' },
        { id: 7, name: 'Roti Tawar', price: 12000, stock: 15, category: 'roti' },
        { id: 8, name: 'Roti Coklat', price: 15000, stock: 8, category: 'roti' },
        { id: 9, name: 'Tart Buah', price: 45000, stock: 5, category: 'tart' },
        { id: 10, name: 'Tart Coklat', price: 50000, stock: 3, category: 'tart' },
        { id: 11, name: 'Air Mineral', price: 5000, stock: 50, category: 'minuman' },
        { id: 12, name: 'Teh Botol', price: 8000, stock: 30, category: 'minuman' }
    ];

    // Cart data
    let cart = [];
    
    // Load modals
    loadModals();
    
    // Initialize the app
    renderProducts();
    renderCart();
    
    // Event listeners
    document.getElementById('product-search').addEventListener('input', filterProducts);
    document.querySelectorAll('#category-tabs .nav-link').forEach(tab => {
        tab.addEventListener('click', function(e) {
            e.preventDefault();
            document.querySelectorAll('#category-tabs .nav-link').forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            filterProducts();
        });
    });
    
    document.getElementById('btn-payment').addEventListener('click', showPaymentModal);
    document.getElementById('btn-history').addEventListener('click', showHistoryModal);
    document.querySelector('.btn-stock').addEventListener('click', showStockModal);
    document.querySelector('.btn-income').addEventListener('click', showIncomeModal);
    document.querySelector('.btn-cashier').addEventListener('click', showCashierModal);
    
    // Functions
    function loadModals() {
        // Load modals from partials
        fetch('partials/pos/stock-modal.html')
            .then(response => response.text())
            .then(html => {
                document.getElementById('modals-container').innerHTML += html;
            });
        
        fetch('partials/pos/income-modal.html')
            .then(response => response.text())
            .then(html => {
                document.getElementById('modals-container').innerHTML += html;
            });
        
        fetch('partials/pos/cashier-modal.html')
            .then(response => response.text())
            .then(html => {
                document.getElementById('modals-container').innerHTML += html;
            });
        
        fetch('partials/pos/payment-modal.html')
            .then(response => response.text())
            .then(html => {
                document.getElementById('modals-container').innerHTML += html;
            });
        
        fetch('partials/pos/history-modal.html')
            .then(response => response.text())
            .then(html => {
                document.getElementById('modals-container').innerHTML += html;
            });
    }
    
    function renderProducts(filteredProducts = null) {
        const productsToRender = filteredProducts || products;
        const container = document.getElementById('products-container');
        container.innerHTML = '';
        
        productsToRender.forEach(product => {
            const productElement = document.createElement('div');
            productElement.className = 'col-md-12';
            productElement.innerHTML = `
                <div class="product-card d-flex justify-content-between align-items-center">
                    <div>
                        <div class="product-name">${product.name} (${product.stock})</div>
                        <div class="product-price">Rp ${product.price.toLocaleString('id-ID')}</div>
                        ${product.stock > 0 && product.stock <= 5 ? '<span class="low-stock">Produk menipis</span>' : ''}
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="product-category me-2">${product.category.toUpperCase()}</span>
                        ${product.stock > 0 ? 
                            `<button class="btn-tambah" data-id="${product.id}">
                                <i class="fas fa-plus"></i> Tambah
                            </button>` : 
                            `<button class="btn-tidak-tersedia">Habis</button>`
                        }
                    </div>
                </div>
            `;
            container.appendChild(productElement);
            
            if (product.stock > 0) {
                productElement.querySelector('.btn-tambah').addEventListener('click', () => addToCart(product.id));
            }
        });
    }
    
    function filterProducts() {
        const searchTerm = document.getElementById('product-search').value.toLowerCase();
        const activeCategory = document.querySelector('#category-tabs .nav-link.active').dataset.category;
        
        let filtered = products;
        
        // Filter by category
        if (activeCategory !== 'all') {
            filtered = filtered.filter(product => product.category === activeCategory);
        }
        
        // Filter by search term
        if (searchTerm) {
            filtered = filtered.filter(product => 
                product.name.toLowerCase().includes(searchTerm) || 
                product.category.toLowerCase().includes(searchTerm)
        )}
        
        renderProducts(filtered);
    }
    
    function addToCart(productId) {
        const product = products.find(p => p.id === productId);
        if (!product) return;
        
        const existingItem = cart.find(item => item.productId === productId);
        
        if (existingItem) {
            existingItem.quantity += 1;
        } else {
            cart.push({
                productId: product.id,
                name: product.name,
                price: product.price,
                quantity: 1,
                discount: 0
            });
        }
        
        renderCart();
    }
    
    function renderCart() {
        const container = document.getElementById('cart-items');
        container.innerHTML = '';
        
        if (cart.length === 0) {
            container.innerHTML = '<div class="text-center py-4 text-muted">Keranjang kosong</div>';
            updateCartSummary();
            return;
        }
        
        cart.forEach((item, index) => {
            const product = products.find(p => p.id === item.productId);
            if (!product) return;
            
            const subtotal = (item.price - item.discount) * item.quantity;
            
            const itemElement = document.createElement('div');
            itemElement.className = 'cart-item';
            itemElement.innerHTML = `
                <div class="row align-items-center">
                    <div class="col-5">
                        <div class="cart-item-name">${item.name}</div>
                        <div class="cart-item-price">Rp ${item.price.toLocaleString('id-ID')}</div>
                    </div>
                    <div class="col-3">
                        <div class="qty-control">
                            <button class="decrease-qty" data-index="${index}">-</button>
                            <input type="text" value="${item.quantity}" data-index="${index}">
                            <button class="increase-qty" data-index="${index}">+</button>
                        </div>
                    </div>
                    <div class="col-2 text-center">
                        <input type="text" class="discount-input" value="${item.discount}" data-index="${index}">
                    </div>
                    <div class="col-1 text-end px-0">
                        <div class="subtotal-price">${formatPrice(subtotal)}</div>
                    </div>
                    <div class="col-1 text-end">
                        <button class="delete-btn" data-index="${index}"><i class="fas fa-trash-alt"></i></button>
                    </div>
                </div>
            `;
            container.appendChild(itemElement);
            
            // Add event listeners
            itemElement.querySelector('.decrease-qty').addEventListener('click', () => updateQuantity(index, -1));
            itemElement.querySelector('.increase-qty').addEventListener('click', () => updateQuantity(index, 1));
            itemElement.querySelector('input[type="text"]').addEventListener('change', (e) => {
                const newQty = parseInt(e.target.value) || 1;
                updateQuantity(index, 0, newQty);
            });
            itemElement.querySelector('.discount-input').addEventListener('change', (e) => {
                const newDiscount = parseInt(e.target.value) || 0;
                updateDiscount(index, newDiscount);
            });
            itemElement.querySelector('.delete-btn').addEventListener('click', () => removeFromCart(index));
        });
        
        updateCartSummary();
    }
    
    function updateQuantity(index, change, newQty = null) {
        if (newQty !== null) {
            cart[index].quantity = Math.max(1, newQty);
        } else {
            cart[index].quantity = Math.max(1, cart[index].quantity + change);
        }
        renderCart();
    }
    
    function updateDiscount(index, discount) {
        cart[index].discount = Math.max(0, Math.min(cart[index].price, discount));
        renderCart();
    }
    
    function removeFromCart(index) {
        cart.splice(index, 1);
        renderCart();
    }
    
    function updateCartSummary() {
        const subtotal = cart.reduce((sum, item) => sum + (item.price - item.discount) * item.quantity, 0);
        const tax = 0; // Assuming no tax for now
        const total = subtotal + tax;
        
        document.getElementById('cart-subtotal').textContent = `Rp ${formatPrice(subtotal)}`;
        document.getElementById('cart-tax').textContent = `Rp ${formatPrice(tax)}`;
        document.getElementById('cart-total').textContent = `Rp ${formatPrice(total)}`;
    }
    
    function formatPrice(amount) {
        if (amount >= 1000) {
            return `${(amount / 1000).toFixed(0)}K`;
        }
        return amount.toLocaleString('id-ID');
    }
    
    function showStockModal() {
        const modal = new bootstrap.Modal(document.getElementById('stockModal'));
        modal.show();
    }
    
    function showIncomeModal() {
        const modal = new bootstrap.Modal(document.getElementById('incomeModal'));
        modal.show();
    }
    
    function showCashierModal() {
        const modal = new bootstrap.Modal(document.getElementById('cashierModal'));
        modal.show();
    }
    
    function showPaymentModal() {
        if (cart.length === 0) {
            alert('Keranjang kosong. Tambahkan produk terlebih dahulu.');
            return;
        }
        
        const modal = new bootstrap.Modal(document.getElementById('paymentModal'));
        modal.show();
        
        // Update payment modal with current cart total
        const total = cart.reduce((sum, item) => sum + (item.price - item.discount) * item.quantity, 0);
        document.getElementById('payment-total').textContent = `Rp ${total.toLocaleString('id-ID')}`;
        document.getElementById('payment-items').textContent = `${cart.length} item dalam transaksi`;
    }
    
    function showHistoryModal() {
        const modal = new bootstrap.Modal(document.getElementById('historyModal'));
        modal.show();
    }
    
    // Format price with thousand separators
    Number.prototype.formatPrice = function() {
        return this.toLocaleString('id-ID');
    };
});