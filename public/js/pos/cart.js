/**
 * Cart Management untuk POS System
 */
class CartManager {
    constructor() {
        this.cart = [];
        this.selectedMember = null;
        this.transactionType = 'regular';
        this.taxType = 'non_pkp';
        this.bonusItems = []; // Array untuk menyimpan bonus items
        this.bonusRules = []; // Array untuk menyimpan bonus rules dari API
        this.availableBonuses = []; // Array untuk menyimpan bonus yang tersedia berdasarkan cart
        this.loadBonusRules(); // Load bonus rules saat inisialisasi
    }

    // Tambah item ke keranjang dengan quantity desimal
    addItem(product, quantity = 1) {
        try {
            const qty = parseQuantity(quantity);
            const existingIndex = this.cart.findIndex(item => item.id === product.id);
            
            // Validasi stok total yang tersedia
            const currentStock = product.quantity || 0;
            
            if (existingIndex >= 0) {
                const newQty = this.cart[existingIndex].quantity + qty;
                if (newQty > currentStock) {
                    showNotification('Stok tidak mencukupi', 'error');
                    return false;
                }
                this.cart[existingIndex].quantity = newQty;
                this.cart[existingIndex].subtotal = this.calculateItemSubtotal(this.cart[existingIndex]);
            } else {
                if (qty > currentStock) {
                    showNotification('Stok tidak mencukupi', 'error');
                    return false;
                }
                
                this.cart.push({
                    id: product.id,
                    name: product.name,
                    price: parseFloat(product.price) || 0,
                    quantity: qty,
                    stock: currentStock,
                    discount: 0,
                    subtotal: (parseFloat(product.price) || 0) * qty,
                    is_active: product.is_active !== false,
                    image_url: product.image_url || null,
                    bonus_qty: 0, // Quantity bonus untuk item ini
                    unit_type: product.unit_type || 'pcs' // Satuan produk
                });
            }
            
            // Check dan apply bonus
            this.checkAndApplyBonus();
            
            // Calculate available automatic bonuses
            this.calculateAvailableBonuses();
            
            // Update display
            this.updateCartDisplay();
            
            // Trigger product list update if available
            this.triggerProductListUpdate();
            
            showNotification(`${product.name} ditambahkan ke keranjang`, 'success');
            return true;
            
        } catch (error) {
            console.error('Error adding item to cart:', error);
            showNotification('Gagal menambahkan item ke keranjang', 'error');
            return false;
        }
    }

    // Update quantity item dengan desimal
    updateQuantity(index, newQuantity) {
        try {
            const qty = parseQuantity(newQuantity);
            const item = this.cart[index];
            
            if (!item) {
                console.warn('Item not found at index:', index);
                return false;
            }
            
            if (qty > item.stock) {
                showNotification(`Stok hanya tersedia ${formatQuantity(item.stock)}`, 'error');
                return false;
            }
            
            if (qty <= 0) {
                this.removeItem(index);
                return true;
            }
            
            item.quantity = qty;
            item.subtotal = this.calculateItemSubtotal(item);
            this.checkAndApplyBonus();
            this.updateCartDisplay();
            this.triggerProductListUpdate();
            return true;
            
        } catch (error) {
            console.error('Error updating quantity:', error);
            return false;
        }
    }

    // Hapus item dari keranjang
    removeItem(index) {
        this.cart.splice(index, 1);
        this.checkAndApplyBonus();
        this.updateCartDisplay();
        this.triggerProductListUpdate();
    }

    // Update discount item
    updateDiscount(index, discount) {
        const item = this.cart[index];
        if (!item) return false;
        
        const discountAmount = parseCurrencyInput(discount.toString());
        item.discount = discountAmount;
        item.subtotal = this.calculateItemSubtotal(item);
        this.updateCartDisplay();
        return true;
    }

    // Hitung subtotal item
    calculateItemSubtotal(item) {
        const basePrice = item.price * item.quantity;
        const discountAmount = item.discount || 0;
        return Math.max(0, basePrice - discountAmount);
    }

    // Add bonus item (using regular product stock)
    addBonusItem(product, quantity = 1) {
        try {
            const qty = parseQuantity(quantity);
            const existingIndex = this.bonusItems.findIndex(item => item.id === product.id);
            
            // Check available regular stock (same as regular products)
            const cartItem = this.cart.find(item => item.id === product.id);
            const reservedInCart = cartItem ? cartItem.quantity : 0;
            const existingBonus = this.bonusItems.find(item => item.id === product.id);
            const reservedInBonus = existingBonus ? existingBonus.quantity : 0;
            const totalReserved = reservedInCart + reservedInBonus;
            const availableStock = (product.quantity || 0) - totalReserved;
            
            if (existingIndex >= 0) {
                const newQty = this.bonusItems[existingIndex].quantity + qty;
                if (qty > availableStock) {
                    showNotification(`Stok tidak mencukupi. Tersedia: ${formatQuantity(availableStock)}`, 'error');
                    return false;
                }
                this.bonusItems[existingIndex].quantity = newQty;
            } else {
                if (qty > availableStock) {
                    showNotification(`Stok tidak mencukupi. Tersedia: ${formatQuantity(availableStock)}`, 'error');
                    return false;
                }
                
                this.bonusItems.push({
                    id: product.id,
                    name: product.name,
                    quantity: qty,
                    stock: product.quantity || 0,
                    image_url: product.image_url || null,
                    type: 'bonus'
                });
            }
            
            this.updateCartDisplay();
            this.triggerProductListUpdate();
            showNotification(`Bonus ${product.name} ditambahkan`, 'success');
            return true;
            
        } catch (error) {
            console.error('Error adding bonus item:', error);
            showNotification('Gagal menambahkan bonus', 'error');
            return false;
        }
    }

    // Update bonus quantity
    updateBonusQuantity(index, newQuantity) {
        try {
            const qty = parseQuantity(newQuantity);
            const item = this.bonusItems[index];
            
            if (!item) return false;
            
            // Check available stock (considering both cart and other bonus items)
            const product = window.products.find(p => p.id === item.id);
            if (!product) return false;
            
            const cartItem = this.cart.find(cartItem => cartItem.id === item.id);
            const reservedInCart = cartItem ? cartItem.quantity : 0;
            const otherBonusItems = this.bonusItems.filter((bonus, i) => bonus.id === item.id && i !== index);
            const reservedInOtherBonus = otherBonusItems.reduce((sum, bonus) => sum + bonus.quantity, 0);
            const totalReserved = reservedInCart + reservedInOtherBonus;
            const availableStock = (product.quantity || 0) - totalReserved;
            
            if (qty > availableStock) {
                showNotification(`Stok hanya tersedia ${formatQuantity(availableStock)}`, 'error');
                return false;
            }
            
            if (qty <= 0) {
                this.removeBonusItem(index);
                return true;
            }
            
            item.quantity = qty;
            this.updateCartDisplay();
            this.triggerProductListUpdate();
            return true;
            
        } catch (error) {
            console.error('Error updating bonus quantity:', error);
            return false;
        }
    }

    // Remove bonus item
    removeBonusItem(index) {
        this.bonusItems.splice(index, 1);
        this.updateCartDisplay();
        this.triggerProductListUpdate();
        showNotification('Bonus dihapus', 'info');
    }

    // Check and apply bonus (placeholder for future automatic bonus logic)
    checkAndApplyBonus() {
        // This method is currently a placeholder
        // In the future, it can contain logic for automatic bonus calculations
        // based on purchase amounts, product combinations, etc.
        // For now, all bonuses are manual via addBonusItem()
    }

    // Load bonus rules from API
    async loadBonusRules() {
        try {
            const outletId = localStorage.getItem('outlet_id');
            if (!outletId) return;

            const token = localStorage.getItem('token') || document.querySelector('meta[name="csrf-token"]')?.content;
            
            const response = await fetch(`/api/bonus/rules?outlet_id=${outletId}&type=all`, {
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (response.ok) {
                const data = await response.json();
                if (data.success) {
                    this.bonusRules = data.data;
                }
            }
        } catch (error) {
            console.error('Error loading bonus rules:', error);
        }
    }

    // Calculate available automatic bonuses based on current cart
    async calculateAvailableBonuses() {
        try {
            if (this.cart.length === 0) {
                this.availableBonuses = [];
                return;
            }

            const outletId = localStorage.getItem('outlet_id');
            if (!outletId) return;

            const token = localStorage.getItem('token') || document.querySelector('meta[name="csrf-token"]')?.content;
            
            // Prepare cart data for API
            const cartData = {
                outlet_id: parseInt(outletId),
                items: this.cart.map(item => ({
                    product_id: item.id,
                    quantity: item.quantity,
                    price: item.price
                })),
                member_id: this.selectedMember?.id || null,
                subtotal: this.calculateTotals().subtotal
            };

            const response = await fetch('/api/bonus/calculate-auto', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(cartData)
            });

            if (response.ok) {
                const data = await response.json();
                if (data.success) {
                    this.availableBonuses = data.data;
                    this.updateBonusDisplay();
                }
            }
        } catch (error) {
            console.error('Error calculating bonuses:', error);
        }
    }

    // Create manual bonus transaction
    async createManualBonus(items, reason) {
        // Validate input parameters
        if (!items || !Array.isArray(items) || items.length === 0) {
            showNotification('Data items tidak valid', 'error');
            console.error('Invalid items data:', items);
            return false;
        }

        // Reason is now optional
        if (!reason) {
            reason = 'Manual bonus';
        }

        const outletId = localStorage.getItem('outlet_id');
        if (!outletId) {
            showNotification('Outlet ID tidak ditemukan', 'error');
            return false;
        }

        const token = localStorage.getItem('token') || document.querySelector('meta[name="csrf-token"]')?.content;
        
        const bonusData = {
            outlet_id: parseInt(outletId),
            items: items.map(item => ({
                product_id: parseInt(item.product_id || item.id),
                quantity: parseFloat(item.quantity)
            })),
            reason: reason.trim(),
            member_id: this.selectedMember?.id || null
        };

        // Log the data being sent for debugging
        console.log('Sending bonus data:', bonusData);

        try {

            const response = await fetch('/api/bonus/manual', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(bonusData)
            });

            if (response.ok) {
                const data = await response.json();
                if (data.success) {
                    // Add bonus items to local cart display
                    const bonusTransaction = data.data;
                    
                    // Check if bonusItems exists and is an array (handle both camelCase and snake_case)
                    const bonusItems = bonusTransaction?.bonusItems || bonusTransaction?.bonus_items;
                    if (bonusItems && Array.isArray(bonusItems)) {
                        bonusItems.forEach(bonusItem => {
                            this.addBonusItemToDisplay(bonusItem);
                        });
                    } else {
                        console.warn('No bonus items returned from API:', bonusTransaction);
                    }

                    // Store bonus transaction reference
                    this.currentBonusTransaction = bonusTransaction;
                    
                    showNotification(`Bonus manual berhasil dibuat: ${bonusTransaction?.bonus_number || 'N/A'}`, 'success');
                    return true;
                } else {
                    showNotification(`Gagal membuat bonus: ${data.message}`, 'error');
                    return false;
                }
            } else {
                const errorData = await response.json();
                showNotification(`Gagal membuat bonus: ${errorData.message}`, 'error');
                return false;
            }
        } catch (error) {
            console.error('Error creating manual bonus:', error);
            console.error('Bonus data being sent:', JSON.stringify(bonusData, null, 2));
            showNotification('Terjadi kesalahan saat membuat bonus: ' + error.message, 'error');
            return false;
        }
    }

    // Add bonus item to display (from API response)
    addBonusItemToDisplay(bonusItem) {
        const existingIndex = this.bonusItems.findIndex(item => item.id === bonusItem.product_id);
        
        if (existingIndex >= 0) {
            this.bonusItems[existingIndex].quantity += bonusItem.quantity;
        } else {
            this.bonusItems.push({
                id: bonusItem.product_id,
                name: bonusItem.product.name,
                quantity: bonusItem.quantity,
                stock: bonusItem.product.quantity || 0,
                image_url: bonusItem.product.image_url || null,
                type: 'bonus',
                bonus_transaction_id: bonusItem.bonus_transaction_id,
                status: bonusItem.status
            });
        }
        
        this.updateCartDisplay();
        this.triggerProductListUpdate();
    }

    // Update bonus display in UI
    updateBonusDisplay() {
        // This method will update the UI to show available automatic bonuses
        // Can be called after calculateAvailableBonuses()
        if (this.availableBonuses.length > 0) {
            // Show bonus notification or update bonus section in UI
            console.log('Available bonuses:', this.availableBonuses);
            
            // Could add visual indicator for available bonuses
            this.showAvailableBonusesNotification();
        }
    }

    // Show notification for available bonuses
    showAvailableBonusesNotification() {
        if (this.availableBonuses.length > 0) {
            const bonusCount = this.availableBonuses.length;
            const message = `${bonusCount} bonus tersedia untuk cart ini!`;
            
            // Create a temporary notification element
            const notification = document.createElement('div');
            notification.className = 'bonus-notification bg-green-100 border border-green-400 text-green-700 px-3 py-2 rounded mb-2 text-sm';
            notification.innerHTML = `
                <div class="flex items-center justify-between">
                    <span><i class="fas fa-gift mr-2"></i>${message}</span>
                    <button onclick="this.parentElement.parentElement.remove()" class="text-green-500 hover:text-green-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            
            // Add to cart header or appropriate location
            const cartHeader = document.querySelector('.cart-header');
            if (cartHeader) {
                cartHeader.appendChild(notification);
                
                // Auto remove after 5 seconds
                setTimeout(() => {
                    if (notification.parentElement) {
                        notification.remove();
                    }
                }, 5000);
            }
        }
    }

    // Get bonus history from API
    async getBonusHistory(dateFrom = null, dateTo = null) {
        try {
            const outletId = localStorage.getItem('outlet_id');
            if (!outletId) return [];

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
                    return data.data.data || []; // Paginated response
                }
            }
            return [];
        } catch (error) {
            console.error('Error fetching bonus history:', error);
            return [];
        }
    }

    // Hitung total
    calculateTotals() {
        const subtotal = this.cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        const totalDiscount = this.cart.reduce((sum, item) => sum + (item.discount || 0), 0);
        const orderSubtotal = subtotal - totalDiscount;
        
        // Hitung pajak berdasarkan pilihan kasir (transaction tax type)
        const selectedTaxType = document.querySelector('input[name="transactionTaxType"]:checked')?.value || 'non_pkp';
        const taxRate = selectedTaxType === 'pkp' ? 11 : 0;
        const tax = orderSubtotal * (taxRate / 100);
        const grandTotal = orderSubtotal + tax;
        
        const totalQuantity = this.cart.reduce((sum, item) => sum + item.quantity, 0);

        return {
            subtotal,
            totalDiscount,
            orderSubtotal,
            tax,
            taxRate,
            grandTotal,
            totalQuantity
        };
    }

    // Update tampilan cart
    updateCartDisplay() {
        console.log('Updating cart display, cart length:', this.cart.length);
        
        const cartContainer = document.getElementById('cartItems');
        const emptyCart = document.getElementById('emptyCart');
        
        if (!cartContainer) {
            console.error('Cart container not found!');
            return;
        }
        
        if (this.cart.length === 0) {
            if (emptyCart) {
                emptyCart.classList.remove('hidden');
                cartContainer.innerHTML = '';
                cartContainer.appendChild(emptyCart);
            } else {
                cartContainer.innerHTML = `
                    <div class="empty-cart p-8 text-center">
                        <i class="fas fa-shopping-cart text-gray-300 text-5xl mb-4"></i>
                        <p class="text-gray-500 text-lg font-medium">Keranjang kosong</p>
                        <p class="text-gray-400 text-sm mt-1">Tambahkan produk ke keranjang</p>
                    </div>
                `;
            }
            this.updateTotalsDisplay({ subtotal: 0, totalDiscount: 0, tax: 0, grandTotal: 0, totalQuantity: 0 });
            return;
        }
        
        if (emptyCart) {
            emptyCart.classList.add('hidden');
        }
        cartContainer.innerHTML = '';

        this.cart.forEach((item, index) => {
            const cartItemElement = this.createCartItemElement(item, index);
            cartContainer.appendChild(cartItemElement);
        });

        // Tampilkan bonus items jika ada (simplified version)
        if (this.bonusItems.length > 0) {
            const bonusSection = document.createElement('div');
            bonusSection.className = 'bonus-section border-t border-green-200 mt-2';
            bonusSection.innerHTML = `
                <div class="bonus-header p-3 bg-green-50 flex justify-between items-center">
                    <div class="flex items-center text-green-600 font-semibold">
                        <i data-lucide="gift" class="w-4 h-4 mr-2"></i>
                        Bonus Items (${this.bonusItems.length})
                    </div>
                    <button class="btn-show-bonus-modal text-xs px-2 py-1 bg-green-500 text-white rounded hover:bg-green-600">
                        <i data-lucide="plus" class="w-3 h-3 inline mr-1"></i>
                        Tambah
                    </button>
                </div>
                <div class="bonus-items-list">
                    ${this.bonusItems.map((bonus, index) => `
                        <div class="bonus-item-grid p-3 border-b border-green-100">
                            <div class="grid grid-cols-12 gap-2 items-center">
                                <!-- Product Info (col-span-5) -->
                                <div class="col-span-5">
                                    <div class="flex items-center space-x-2">
                                        ${bonus.image_url ? 
                                            `<img src="${bonus.image_url}" alt="${bonus.name}" class="w-6 h-6 rounded object-cover">` : 
                                            '<div class="w-6 h-6 bg-green-200 rounded flex items-center justify-center"><i data-lucide="gift" class="w-3 h-3 text-green-600"></i></div>'
                                        }
                                        <div class="flex-1 min-w-0">
                                            <div class="font-medium text-green-700 text-sm truncate">${bonus.name}</div>
                                            <div class="text-xs text-green-500">Bonus</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Quantity Control (col-span-2) -->
                                <div class="col-span-2">
                                    <div class="flex items-center justify-center space-x-1">
                                        <!-- <button class="btn-decrease-bonus px-1 py-1 border border-green-300 bg-green-100 rounded hover:bg-green-200 text-xs" data-index="${index}"> -->
                                        <!--     <i data-lucide="minus" class="w-3 h-3"></i> -->
                                        <!-- </button> -->
                                        <input type="text" class="bonus-qty-input w-12 px-1 py-1 text-center text-xs border border-green-300 rounded" value="${formatQuantity(bonus.quantity)}" data-index="${index}" readonly> 
                                        <!-- <span class="bonus-qty-input w-12 px-1 py-1 text-center text-xs border border-green-300 rounded" data-index="${index}">${formatQuantity(bonus.quantity)}</span> -->
                                        <!-- <button class="btn-increase-bonus px-1 py-1 border border-green-300 bg-green-100 rounded hover:bg-green-200 text-xs" data-index="${index}"> -->
                                        <!--     <i data-lucide="plus" class="w-3 h-3"></i> -->
                                        <!-- </button> -->
                                    </div>
                                    <!-- <div class="text-xs text-center text-green-500 mt-1">Stok: ${formatQuantity(bonus.stock)}</div> -->
                                </div>

                                <div class="col-span-3">
                                </div>
                                
                                <!-- Remove Button (col-span-2) -->
                                <div class="col-span-2 text-right">
                                    <button class="btn-remove-bonus text-red-400 hover:text-red-600 text-xs" data-index="${index}">
                                        <i data-lucide="trash-2" class="w-5 h-5"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    `).join('')}
                </div>
            `;
            cartContainer.appendChild(bonusSection);
        } else {
            // Show add bonus button even when no bonus items
            const addBonusSection = document.createElement('div');
            addBonusSection.className = 'add-bonus-section border-t border-gray-200 mt-2';
            addBonusSection.innerHTML = `
                <div class="p-3 text-center">
                    <button class="btn-show-bonus-modal text-xs px-3 py-2 border border-green-300 text-green-600 rounded hover:bg-green-50 transition-colors">
                        <i data-lucide="gift" class="w-3 h-3 inline mr-1"></i>
                        Tambah Bonus
                    </button>
                </div>
            `;
            cartContainer.appendChild(addBonusSection);
        }

        const totals = this.calculateTotals();
        this.updateTotalsDisplay(totals);
        
        lucide.createIcons();
        this.attachCartEventListeners();
    }

    // Buat elemen cart item
    createCartItemElement(item, index) {
        const element = document.createElement('div');
        element.className = 'cart-item hover:bg-gray-50';
        
        element.innerHTML = `
            <div class=" p-3 border-b border-gray-100 w-full">
                <div class="grid grid-cols-12 gap-2 items-center w-full" style="width: 100%">
                    <!-- Product Info (col-span-5) -->
                    <div class="col-span-5">
                        <div class="flex items-center space-x-2">
                            ${item.image_url ? 
                                `<img src="${item.image_url}" alt="${item.name}" class="w-8 h-8 rounded object-cover">` : 
                                '<div class="w-8 h-8 bg-gray-200 rounded flex items-center justify-center"><i data-lucide="package" class="w-4 h-4 text-gray-400"></i></div>'
                            }
                            <div class="flex-1 min-w-0">
                                <div class="product-name font-medium text-gray-800 text-sm truncate">${item.name}</div>
                                <div class="product-price text-xs text-gray-500">${formatCurrency(item.price, true)}</div>
                                ${item.bonus_qty > 0 ? 
                                    `<div class="text-xs text-green-600">+${formatQuantity(item.bonus_qty)} bonus</div>` : 
                                    ''
                                }
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quantity Control (col-span-2) -->
                    <div class="col-span-2">
                        <div class="flex items-center justify-center space-x-1">
                            <button class="btn-decrease px-1 py-1 border border-gray-300 bg-gray-100 rounded hover:bg-gray-200 text-xs" data-index="${index}">
                                <i data-lucide="minus" class="w-3 h-3"></i>
                            </button>
                            <input type="text" class="qty-input w-12 px-1 py-1 text-center text-xs border border-gray-300 rounded" value="${formatQuantity(item.quantity)}" data-index="${index}" data-unit-type="${item.unit_type || 'pcs'}"${item.unit_type === 'meter' ? ' step="0.1"' : ' step="1"'}${item.unit_type !== 'meter' ? ' pattern="[0-9]+"' : ''}>
                            <button class="btn-increase px-1 py-1 border border-gray-300 bg-gray-100 rounded hover:bg-gray-200 text-xs" data-index="${index}">
                                <i data-lucide="plus" class="w-3 h-3"></i>
                            </button>
                        </div>
                        <div class="text-xs text-center text-gray-500 mt-1">${item.unit_type || 'pcs'}</div>
                    </div>
                    
                    <!-- Discount (col-span-3) -->
                    <div class="col-span-3">
                        <input type="text" class="discount-input w-full px-2 py-1 text-xs border border-gray-300 rounded text-center" value="${formatCurrency(item.discount)}" data-index="${index}" placeholder="Rp 0">
                    </div>
                    
                    <!-- Subtotal (col-span-2) -->
                    <div class="col-span-2 text-right">
                        <div class="font-medium text-sm">
                            ${formatCurrency(item.subtotal)}
                        </div>
                        <button class="btn-remove text-gray-400 hover:text-red-500 text-xs mt-1" data-index="${index}">
                            <i data-lucide="trash-2" class="w-3 h-3"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        return element;
    }

    // Update tampilan total
    updateTotalsDisplay(totals) {
        document.getElementById('subtotal').textContent = formatCurrency(totals.subtotal);
        document.getElementById('totalDiscount').textContent = formatCurrency(totals.totalDiscount);
        document.getElementById('taxAmount').textContent = formatCurrency(totals.tax);
        document.getElementById('total').textContent = formatCurrency(totals.grandTotal);
        document.getElementById('totalQty').textContent = formatQuantity(totals.totalQuantity);
        
        // Update tax label
        const taxLabel = document.querySelector('.summary-item:has(+ #taxAmount)');
        if (taxLabel) {
            taxLabel.textContent = `Pajak (${totals.taxRate}%)`;
        }
    }

    // Attach event listeners untuk cart items
    attachCartEventListeners() {
        try {
            // Quantity inputs
            document.querySelectorAll('#cartItems .qty-input').forEach(input => {
                // Remove existing listeners
                input.removeEventListener('change', this.handleQuantityChange);
                
                // Add new listener
                input.addEventListener('change', (e) => {
                    const index = parseInt(e.target.getAttribute('data-index'));
                    const newQty = e.target.value;
                    const unitType = e.target.getAttribute('data-unit-type') || 'pcs';
                    
                    // Validate quantity based on unit type
                    if (this.validateQuantityInput(e.target, unitType, newQty)) {
                        if (!this.updateQuantity(index, newQty)) {
                            e.target.value = formatQuantity(this.cart[index]?.quantity || 1);
                        }
                    } else {
                        e.target.value = formatQuantity(this.cart[index]?.quantity || 1);
                    }
                });

                // Add input listener for real-time validation
                input.addEventListener('input', (e) => {
                    const unitType = e.target.getAttribute('data-unit-type') || 'pcs';
                    this.validateQuantityInput(e.target, unitType, e.target.value);
                });
            });

            // Increase buttons
            document.querySelectorAll('#cartItems .btn-increase').forEach(button => {
                button.addEventListener('click', (e) => {
                    e.preventDefault();
                    const index = parseInt(e.target.closest('button').getAttribute('data-index'));
                    if (this.cart[index]) {
                        const currentQty = this.cart[index].quantity;
                        const unitType = this.cart[index].unit_type || 'pcs';
                        const increment = unitType === 'meter' ? 0.1 : 1;
                        this.updateQuantity(index, currentQty + increment);
                    }
                });
            });

            // Decrease buttons  
            document.querySelectorAll('#cartItems .btn-decrease').forEach(button => {
                button.addEventListener('click', (e) => {
                    e.preventDefault();
                    const index = parseInt(e.target.closest('button').getAttribute('data-index'));
                    if (this.cart[index]) {
                        const currentQty = this.cart[index].quantity;
                        const unitType = this.cart[index].unit_type || 'pcs';
                        const decrement = unitType === 'meter' ? 0.1 : 1;
                        this.updateQuantity(index, Math.max(0, currentQty - decrement));
                    }
                });
            });

            // Remove buttons
            document.querySelectorAll('#cartItems .btn-remove').forEach(button => {
                button.addEventListener('click', (e) => {
                    e.preventDefault();
                    const index = parseInt(e.target.closest('button').getAttribute('data-index'));
                    if (confirm('Hapus item dari keranjang?')) {
                        this.removeItem(index);
                    }
                });
            });

            // Discount inputs
            document.querySelectorAll('#cartItems .discount-input').forEach(input => {
                input.addEventListener('blur', (e) => {
                    const index = parseInt(e.target.getAttribute('data-index'));
                    const discount = e.target.value;
                    this.updateDiscount(index, discount);
                });

                input.addEventListener('focus', (e) => {
                    const index = parseInt(e.target.getAttribute('data-index'));
                    if (this.cart[index]) {
                        const currentDiscount = this.cart[index].discount || 0;
                        e.target.value = currentDiscount.toString();
                    }
                });
            });

            // Bonus management buttons
            document.querySelectorAll('#cartItems .btn-show-bonus-modal').forEach(button => {
                button.addEventListener('click', (e) => {
                    e.preventDefault();
                    this.showBonusSelectionModal();
                });
            });

            // Bonus quantity controls
            document.querySelectorAll('#cartItems .bonus-qty-input').forEach(input => {
                input.addEventListener('change', (e) => {
                    const index = parseInt(e.target.getAttribute('data-index'));
                    const newQty = e.target.value;
                    if (!this.updateBonusQuantity(index, newQty)) {
                        e.target.value = formatQuantity(this.bonusItems[index]?.quantity || 1);
                    }
                });
            });

            document.querySelectorAll('#cartItems .btn-increase-bonus').forEach(button => {
                button.addEventListener('click', (e) => {
                    e.preventDefault();
                    const index = parseInt(e.target.closest('button').getAttribute('data-index'));
                    if (this.bonusItems[index]) {
                        const currentQty = this.bonusItems[index].quantity;
                        this.updateBonusQuantity(index, currentQty + 1);
                    }
                });
            });

            document.querySelectorAll('#cartItems .btn-decrease-bonus').forEach(button => {
                button.addEventListener('click', (e) => {
                    e.preventDefault();
                    const index = parseInt(e.target.closest('button').getAttribute('data-index'));
                    if (this.bonusItems[index]) {
                        const currentQty = this.bonusItems[index].quantity;
                        this.updateBonusQuantity(index, Math.max(0, currentQty - 1));
                    }
                });
            });

            document.querySelectorAll('#cartItems .btn-remove-bonus').forEach(button => {
                button.addEventListener('click', (e) => {
                    e.preventDefault();
                    const index = parseInt(e.target.closest('button').getAttribute('data-index'));
                    if (confirm('Hapus bonus ini?')) {
                        this.removeBonusItem(index);
                    }
                });
            });
            
        } catch (error) {
            console.error('Error attaching cart event listeners:', error);
        }
    }

    // Show modal untuk pilih produk bonus
    showBonusSelectionModal() {
        const modal = document.createElement('div');
        modal.id = 'bonusSelectionModal';
        modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
        
        modal.innerHTML = `
            <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-[80vh] overflow-y-auto">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Pilih Produk Bonus</h3>
                        <button onclick="this.closest('#bonusSelectionModal').remove()" class="text-gray-400 hover:text-gray-600">
                            <i data-lucide="x" class="w-5 h-5"></i>
                        </button>
                    </div>

                    <div class="mb-4">
                        <input type="text" id="bonusSearchInput" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="Cari produk bonus...">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Alasan Bonus (Opsional)</label>
                        <select id="bonusReasonSelect" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                            <option value="">Pilih alasan...</option>
                            <option value="customer_complaint">Keluhan Pelanggan</option>
                            <option value="promotional">Promosi Khusus</option>
                            <option value="goodwill">Goodwill</option>
                            <option value="staff_error">Kesalahan Staff</option>
                            <option value="product_defect">Produk Cacat</option>
                            <option value="loyalty_reward">Reward Loyalitas</option>
                            <option value="other">Lainnya</option>
                        </select>
                        <textarea id="bonusReasonCustom" class="w-full px-3 py-2 border border-gray-300 rounded-md mt-2 hidden" 
                                  placeholder="Masukkan alasan khusus..." rows="2"></textarea>
                    </div>

                    <div id="bonusProductsList" class="space-y-2 max-h-96 overflow-y-auto">
                        <!-- Products will be populated here -->
                    </div>
                </div>
            </div>
        `;

        document.body.appendChild(modal);
        this.populateBonusProducts(modal);
        
        // Search functionality
        modal.querySelector('#bonusSearchInput').addEventListener('input', (e) => {
            this.populateBonusProducts(modal, e.target.value);
        });

        // Reason select functionality
        modal.querySelector('#bonusReasonSelect').addEventListener('change', (e) => {
            const customTextarea = modal.querySelector('#bonusReasonCustom');
            if (e.target.value === 'other') {
                customTextarea.classList.remove('hidden');
            } else {
                customTextarea.classList.add('hidden');
            }
        });

        lucide.createIcons();
    }

    // Populate bonus products list
    populateBonusProducts(modal, searchTerm = '') {
        const bonusProductsList = modal.querySelector('#bonusProductsList');
        
        // Filter products that have regular stock available
        const availableProducts = window.products.filter(product => {
            const matchSearch = product.name.toLowerCase().includes(searchTerm.toLowerCase());
            
            // Calculate available stock (considering both cart and existing bonus items)
            const cartItem = this.cart.find(item => item.id === product.id);
            const reservedInCart = cartItem ? cartItem.quantity : 0;
            const existingBonus = this.bonusItems.find(item => item.id === product.id);
            const reservedInBonus = existingBonus ? existingBonus.quantity : 0;
            const totalReserved = reservedInCart + reservedInBonus;
            const availableStock = (product.quantity || 0) - totalReserved;
            
            return matchSearch && availableStock > 0;
        });

        if (availableProducts.length === 0) {
            bonusProductsList.innerHTML = `
                <div class="text-center py-8 text-gray-500">
                    <i data-lucide="gift-off" class="w-12 h-12 mx-auto mb-4 text-gray-300"></i>
                    <p>Tidak ada produk tersedia untuk bonus</p>
                    <p class="text-sm mt-2">Semua produk sudah habis atau sudah ada di keranjang</p>
                </div>
            `;
            lucide.createIcons();
            return;
        }

        bonusProductsList.innerHTML = availableProducts.map(product => {
            // Calculate available stock for this product
            const cartItem = this.cart.find(item => item.id === product.id);
            const reservedInCart = cartItem ? cartItem.quantity : 0;
            const existingBonus = this.bonusItems.find(item => item.id === product.id);
            const reservedInBonus = existingBonus ? existingBonus.quantity : 0;
            const totalReserved = reservedInCart + reservedInBonus;
            const availableStock = (product.quantity || 0) - totalReserved;
            
            return `
                <div class="product-bonus-item border border-gray-200 rounded-lg p-4 hover:bg-gray-50">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center space-x-3">
                            ${product.image_url ? 
                                `<img src="${product.image_url}" alt="${product.name}" class="w-12 h-12 rounded object-cover">` :
                                '<div class="w-12 h-12 bg-gray-200 rounded flex items-center justify-center"><i data-lucide="package" class="w-6 h-6 text-gray-400"></i></div>'
                            }
                            <div>
                                <div class="font-medium text-gray-900">${product.name}</div>
                                <div class="text-sm text-green-600">Tersedia: ${formatQuantity(availableStock)}</div>
                                <div class="text-xs text-gray-500">${(product.category?.name || 'UNCATEGORIZED').toUpperCase()}</div>
                                ${reservedInCart > 0 ? `<div class="text-xs text-blue-500">Di keranjang: ${formatQuantity(reservedInCart)}</div>` : ''}
                                ${reservedInBonus > 0 ? `<div class="text-xs text-green-500">Bonus: ${formatQuantity(reservedInBonus)}</div>` : ''}
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <input type="number" class="bonus-qty-select w-20 px-2 py-1 text-sm border border-gray-300 rounded text-center" 
                                   min="0.01" step="0.01" max="${availableStock}" value="1" placeholder="Qty">
                            <button class="btn-add-bonus-product px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 text-sm" 
                                    data-product-id="${product.id}" ${availableStock <= 0 ? 'disabled' : ''}>
                                ${availableStock <= 0 ? 'Habis' : 'Tambah'}
                            </button>
                        </div>
                    </div>
                </div>
            `;
        }).join('');

        // Add event listeners for add buttons
        bonusProductsList.querySelectorAll('.btn-add-bonus-product').forEach(button => {
            button.addEventListener('click', async (e) => {
                e.preventDefault();
                
                const productId = parseInt(e.target.dataset.productId);
                const product = window.products.find(p => p.id === productId);
                const qtyInput = e.target.parentElement.querySelector('.bonus-qty-select');
                const quantity = parseFloat(qtyInput.value) || 1;
                
                // Get reason from modal
                const reasonSelect = modal.querySelector('#bonusReasonSelect');
                const customTextarea = modal.querySelector('#bonusReasonCustom');
                
                let reason = reasonSelect.value;
                if (!reason) {
                    reason = 'Manual bonus'; // Default reason if not selected
                }
                
                if (reason === 'other') {
                    const customReason = customTextarea.value.trim();
                    if (!customReason) {
                        showNotification('Silakan masukkan alasan khusus', 'error');
                        customTextarea.focus();
                        return;
                    }
                    reason = customReason;
                } else {
                    // Convert reason code to readable text
                    const reasonTexts = {
                        'customer_complaint': 'Keluhan Pelanggan',
                        'promotional': 'Promosi Khusus', 
                        'goodwill': 'Goodwill',
                        'staff_error': 'Kesalahan Staff',
                        'product_defect': 'Produk Cacat',
                        'loyalty_reward': 'Reward Loyalitas'
                    };
                    reason = reasonTexts[reason] || reason;
                }
                
                if (product && quantity > 0) {
                    // Disable button during API call
                    e.target.disabled = true;
                    e.target.textContent = 'Memproses...';
                    
                    const items = [{
                        product_id: productId,
                        quantity: quantity
                    }];
                    
                    const success = await this.createManualBonus(items, reason);
                    if (success) {
                        modal.remove();
                    } else {
                        // Re-enable button if failed
                        e.target.disabled = false;
                        e.target.textContent = 'Tambah';
                    }
                }
            });
        });

        lucide.createIcons();
    }

    // Trigger product list update (realtime stock update)
    triggerProductListUpdate() {
        if (typeof renderProducts === 'function') {
            const activeCategory = document.querySelector('#categoryTabs .nav-link.active')?.getAttribute('data-category') || 'all';
            const searchTerm = document.getElementById('searchInput')?.value || '';
            renderProducts(activeCategory, searchTerm);
        }
    }

    // Get total reserved quantity for a product (cart + bonus)
    getReservedQuantity(productId) {
        // Regular cart quantity
        const cartItem = this.cart.find(item => item.id === productId);
        const cartQuantity = cartItem ? cartItem.quantity : 0;
        
        // Bonus items quantity 
        const bonusQuantity = this.bonusItems.filter(item => item.id === productId)
                                           .reduce((sum, item) => sum + item.quantity, 0);
        
        return cartQuantity + bonusQuantity;
    }

    // Get available stock for a product (total stock - reserved in cart - reserved in bonus)
    getAvailableStock(product) {
        const totalStock = product.quantity || 0;
        const reservedQuantity = this.getReservedQuantity(product.id);
        return Math.max(0, totalStock - reservedQuantity);
    }

    // Clear cart
    clear() {
        this.cart = [];
        this.bonusItems = [];
        this.updateCartDisplay();
        this.triggerProductListUpdate();
    }

    // Set member
    setMember(member) {
        this.selectedMember = member;
    }

    // Set transaction type
    setTransactionType(type) {
        this.transactionType = type;
    }

    // Set tax type
    setTaxType(type) {
        this.taxType = type;
        this.updateCartDisplay(); // Recalculate with new tax
    }

    // Get cart data untuk API
    getCartData() {
        return {
            items: this.cart.map(item => ({
                product_id: item.id,
                quantity: item.quantity,
                price: item.price,
                discount: item.discount || 0
            })),
            bonus_items: this.bonusItems.map(item => ({
                product_id: item.id,
                quantity: item.quantity,
                type: 'bonus'
            })),
            member: this.selectedMember,
            transaction_type: this.transactionType,
            tax_type: this.taxType,
            totals: this.calculateTotals()
        };
    }

    // Validate quantity input based on unit type
    validateQuantityInput(input, unitType, value) {
        if (!input || !unitType || !value) return true;
        
        const numValue = parseFloat(value);
        
        if (isNaN(numValue) || numValue < 0) {
            input.setCustomValidity('Harus berupa angka positif');
            return false;
        }
        
        if (unitType === 'meter') {
            // Allow decimal for meter
            input.setCustomValidity('');
            return true;
        } else {
            // Only integers for pcs and unit
            if (value.includes('.') || numValue !== Math.floor(numValue)) {
                input.setCustomValidity('Untuk satuan pcs/unit, hanya angka bulat yang diperbolehkan');
                return false;
            } else {
                input.setCustomValidity('');
                return true;
            }
        }
    }
}