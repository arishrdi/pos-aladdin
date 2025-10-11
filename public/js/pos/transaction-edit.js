/**
 * Transaction Edit Functionality
 * Handles editing transactions with dual approval workflow
 */

// Global variables
let transaksiEdit = null;
let editItems = [];

/**
 * Check if transaction can be edited
 * @param {Object} transaksi - Transaction object
 * @returns {boolean}
 */
function canEditTransaction(transaksi) {
    // Check status - only pending or completed can be edited
    if (!['pending', 'Selesai'].includes(transaksi.status)) {
        return false;
    }
    
    // Check if there's no pending edit request
    if (transaksi.pending_edit) {
        return false;
    }
    
    // Check if there's no pending cancellation
    if (transaksi.has_pending_cancellation) {
        return false;
    }
    
    return true;
}

/**
 * Open edit transaction modal
 * @param {string} nomorInvoice - Invoice number
 */
function bukaModalEditTransaksi(nomorInvoice) {
    const transaksi = semuaTransaksi.find(t => t.invoice === nomorInvoice);
    if (!transaksi) {
        alert('Transaksi tidak ditemukan');
        return;
    }

    // Check if transaction can be edited
    if (!canEditTransaction(transaksi)) {
        // Provide specific feedback based on the reason
        if (transaksi.pending_edit) {
            alert('Transaksi ini sedang dalam proses edit dan menunggu persetujuan dari Finance dan Operational. Silakan tunggu hingga proses edit selesai sebelum melakukan edit baru.');
        } else if (transaksi.has_pending_cancellation) {
            const requestType = transaksi.status === 'Selesai' ? 'refund' : 'pembatalan';
            alert(`Transaksi ini sedang dalam proses ${requestType}. Tidak dapat melakukan edit saat ada permintaan ${requestType} yang sedang diproses.`);
        } else if (!['pending', 'Selesai'].includes(transaksi.status)) {
            alert(`Transaksi dengan status "${transaksi.status}" tidak dapat diedit. Hanya transaksi dengan status "pending" atau "Selesai" yang dapat diedit.`);
        } else {
            alert('Transaksi ini tidak dapat diedit saat ini. Pastikan status transaksi memungkinkan dan tidak ada permintaan lain yang pending.');
        }
        return;
    }

    // Save transaction data
    transaksiEdit = transaksi;

    // Populate modal
    populateEditModal(transaksi);

    // Open modal
    bukaModal('editTransactionModal');
}

/**
 * Populate edit modal with transaction data
 * @param {Object} transaksi - Transaction object
 */
function populateEditModal(transaksi) {
    // Set invoice number
    document.getElementById('editInvoiceNumber').textContent = transaksi.invoice;

    // Set original total
    document.getElementById('editOriginalTotal').textContent = `Rp ${formatUang(transaksi.total)}`;

    // Clear form
    document.getElementById('editReason').value = '';
    document.getElementById('editNotes').value = '';

    // Populate items
    populateEditItems(transaksi.items);

    // console.log('Window items:', window.products);

    // Calculate initial totals
    calculateEditTotals();
}

/**
 * Populate edit items table
 * @param {Array} items - Array of items
 */
function populateEditItems(items) {

    // console.log('Populating edit items:', items);
    
    editItems = items.map(item => {
        // Get unit_type from products data based on product_id or product name
        let unitType = 'pcs'; // default fallback
        let productId = item.product_id || null;
        
        // Try to find by product_id first
        if (item.product_id && window.products) {
            const product = window.products.find(p => p.id === item.product_id);
            if (product) {
                productId = product.id;
                if (product.unit_type) {
                    unitType = product.unit_type;
                }
            }
        } 
        // If no product_id, try to find by product name as fallback
        else if (item.product && window.products) {
            const product = window.products.find(p => p.name === item.product);
            if (product) {
                productId = product.id;
                if (product.unit_type) {
                    unitType = product.unit_type;
                }
            }
        }
        
        return {
            product_id: productId,
            product: item.product,
            quantity: parseFloat(item.quantity),
            price: parseFloat(item.price),
            discount: parseFloat(item.discount || 0),
            unit_type: unitType, // Use unit_type from products data
            is_new: false
        };
    });

    renderEditItems();
}

/**
 * Render edit items table
 */
function renderEditItems() {
    const tbody = document.getElementById('editItemsList');
    
    if (editItems.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-gray-500">Tidak ada item</td></tr>';
        return;
    }


    tbody.innerHTML = editItems.map((item, index) => `
        <tr class="${item.is_new ? 'bg-green-50' : ''}">
            <td class="p-3">
                <span class="font-medium">${item.product}</span>
                ${item.is_new ? '<span class="ml-2 px-2 py-1 bg-green-100 text-green-800 text-xs rounded">BARU</span>' : ''}
            </td>
            <td class="p-3 text-center">
                <div class="flex items-center justify-center space-x-1">
                    <button class="btn-decrease px-2 py-1 border border-gray-300 bg-gray-100 rounded hover:bg-gray-200 text-xs" data-index="${index}">
                        <i class="fas fa-minus text-xs"></i>
                    </button>
                    <input type="text" class="qty-input w-16 px-2 py-1 text-center text-xs border border-gray-300 rounded" 
                           value="${formatQuantity(item.quantity)}" 
                           data-index="${index}" 
                           data-unit-type="${item.unit_type || 'pcs'}"
                           ${item.unit_type === 'meter' ? 'step="0.1"' : 'step="1"'}
                           ${item.unit_type !== 'meter' ? 'pattern="[0-9]+"' : ''}>
                    <button class="btn-increase px-2 py-1 border border-gray-300 bg-gray-100 rounded hover:bg-gray-200 text-xs" data-index="${index}">
                        <i class="fas fa-plus text-xs"></i>
                    </button>
                </div>
                <div class="text-xs text-gray-500 mt-1">${item.unit_type || 'tanpa satuan'}</div>
            </td>
            <td class="p-3 text-center">
                <input type="text" value="${formatUang(item.price)}" 
                       class="price-input w-24 p-2 border rounded text-center text-sm" 
                       data-index="${index}" 
                       data-raw-value="${item.price}"
                       placeholder="0">
                <input type="hidden" class="price-raw" data-index="${index}" value="${item.price}">
            </td>
            <td class="p-3 text-center">
                <input type="text" value="${formatUang(item.discount)}" 
                       class="discount-input w-20 p-2 border rounded text-center text-sm" 
                       data-index="${index}"
                       data-raw-value="${item.discount}"
                       placeholder="0">
                <input type="hidden" class="discount-raw" data-index="${index}" value="${item.discount}">
            </td>
            <td class="p-3 text-right font-medium">
                Rp ${formatUang((item.quantity * item.price) - item.discount)}
            </td>
            <td class="p-3 text-center">
                <button onclick="removeEditItem(${index})" class="text-red-500 hover:text-red-700" title="Hapus Item">
                    <i class="fas fa-trash text-sm"></i>
                </button>
            </td>
        </tr>
    `).join('');
    
    // Attach event listeners for quantity controls
    attachQuantityEventListeners();
    
    // Attach event listeners for currency formatting
    attachCurrencyEventListeners();
}

/**
 * Attach event listeners for quantity controls
 */
function attachQuantityEventListeners() {
    // Quantity input change handlers
    document.querySelectorAll('.qty-input').forEach(input => {
        input.addEventListener('change', (e) => {
            const index = parseInt(e.target.getAttribute('data-index'));
            const newQty = e.target.value;
            const unitType = e.target.getAttribute('data-unit-type') || 'pcs';
            
            // Validate and update quantity
            if (validateQuantityInput(e.target, unitType, newQty)) {
                if (!updateItemQuantity(index, newQty)) {
                    e.target.value = formatQuantity(editItems[index]?.quantity || 1);
                }
            } else {
                e.target.value = formatQuantity(editItems[index]?.quantity || 1);
            }
        });
    });

    // Increase quantity buttons
    document.querySelectorAll('.btn-increase').forEach(button => {
        button.addEventListener('click', (e) => {
            e.preventDefault();
            const index = parseInt(e.target.closest('button').getAttribute('data-index'));
            if (editItems[index]) {
                const currentQty = editItems[index].quantity;
                const unitType = editItems[index].unit_type || 'pcs';
                const increment = unitType === 'meter' ? 0.1 : 1;
                updateItemQuantity(index, currentQty + increment);
            }
        });
    });

    // Decrease quantity buttons
    document.querySelectorAll('.btn-decrease').forEach(button => {
        button.addEventListener('click', (e) => {
            e.preventDefault();
            const index = parseInt(e.target.closest('button').getAttribute('data-index'));
            if (editItems[index]) {
                const currentQty = editItems[index].quantity;
                const unitType = editItems[index].unit_type || 'pcs';
                const decrement = unitType === 'meter' ? 0.1 : 1;
                const minQty = unitType === 'meter' ? 0.1 : 1;
                updateItemQuantity(index, Math.max(minQty, currentQty - decrement));
            }
        });
    });
}

/**
 * Attach event listeners for currency formatting
 */
function attachCurrencyEventListeners() {
    // Price input formatting
    document.querySelectorAll('.price-input').forEach(input => {
        // Format on input
        input.addEventListener('input', function() {
            const rawValue = this.value.replace(/[^\d]/g, '');
            const index = this.getAttribute('data-index');
            
            // Update hidden field with raw value
            const hiddenInput = document.querySelector(`.price-raw[data-index="${index}"]`);
            if (hiddenInput) {
                hiddenInput.value = rawValue;
            }
            
            // Format display value
            if (rawValue) {
                const formatted = rawValue.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                this.value = formatted;
            } else {
                this.value = '';
            }
        });
        
        // Format on paste
        input.addEventListener('paste', function() {
            setTimeout(() => {
                const rawValue = this.value.replace(/[^\d]/g, '');
                const index = this.getAttribute('data-index');
                
                const hiddenInput = document.querySelector(`.price-raw[data-index="${index}"]`);
                if (hiddenInput) {
                    hiddenInput.value = rawValue;
                }
                
                if (rawValue) {
                    const formatted = rawValue.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                    this.value = formatted;
                } else {
                    this.value = '';
                }
            }, 10);
        });
        
        // Keep formatting on focus but select all for easy replacement
        input.addEventListener('focus', function() {
            this.select();
        });
        
        // Update item price on blur
        input.addEventListener('blur', function() {
            const rawValue = this.value.replace(/[^\d]/g, '');
            const index = parseInt(this.getAttribute('data-index'));
            
            // Update hidden field with raw value
            const hiddenInput = document.querySelector(`.price-raw[data-index="${index}"]`);
            if (hiddenInput) {
                hiddenInput.value = rawValue;
            }
            
            // Format display value
            if (rawValue) {
                const formatted = rawValue.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                this.value = formatted;
                // Update item price
                updateItemPrice(index, rawValue);
            } else {
                this.value = '';
                updateItemPrice(index, 0);
            }
        });
    });

    // Discount input formatting
    document.querySelectorAll('.discount-input').forEach(input => {
        // Format on input
        input.addEventListener('input', function() {
            const rawValue = this.value.replace(/[^\d]/g, '');
            const index = this.getAttribute('data-index');
            
            // Update hidden field with raw value
            const hiddenInput = document.querySelector(`.discount-raw[data-index="${index}"]`);
            if (hiddenInput) {
                hiddenInput.value = rawValue;
            }
            
            // Format display value
            if (rawValue) {
                const formatted = rawValue.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                this.value = formatted;
            } else {
                this.value = '';
            }
        });
        
        // Format on paste
        input.addEventListener('paste', function() {
            setTimeout(() => {
                const rawValue = this.value.replace(/[^\d]/g, '');
                const index = this.getAttribute('data-index');
                
                const hiddenInput = document.querySelector(`.discount-raw[data-index="${index}"]`);
                if (hiddenInput) {
                    hiddenInput.value = rawValue;
                }
                
                if (rawValue) {
                    const formatted = rawValue.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                    this.value = formatted;
                } else {
                    this.value = '';
                }
            }, 10);
        });
        
        // Keep formatting on focus but select all for easy replacement
        input.addEventListener('focus', function() {
            this.select();
        });
        
        // Update item discount on blur
        input.addEventListener('blur', function() {
            const rawValue = this.value.replace(/[^\d]/g, '');
            const index = parseInt(this.getAttribute('data-index'));
            
            // Update hidden field with raw value
            const hiddenInput = document.querySelector(`.discount-raw[data-index="${index}"]`);
            if (hiddenInput) {
                hiddenInput.value = rawValue;
            }
            
            // Format display value
            if (rawValue) {
                const formatted = rawValue.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                this.value = formatted;
                // Update item discount
                updateItemDiscount(index, rawValue);
            } else {
                this.value = '';
                updateItemDiscount(index, 0);
            }
        });
    });
}

/**
 * Validate quantity input based on unit type
 */
function validateQuantityInput(input, unitType, value) {
    const qty = parseQuantityByUnitType(value, unitType);
    
    if (unitType === 'meter') {
        return qty >= 0.1;
    } else {
        return qty >= 1 && Number.isInteger(qty);
    }
}

/**
 * Parse quantity based on unit type
 */
function parseQuantityByUnitType(value, unitType) {
    const qty = parseFloat(value) || 0;
    
    if (unitType === 'meter') {
        return Math.round(qty * 10) / 10; // Round to 1 decimal place
    } else {
        return Math.round(qty); // Round to integer
    }
}

/**
 * Format quantity for display
 */
function formatQuantity(quantity) {
    if (quantity % 1 === 0) {
        return quantity.toString(); // Return as integer string if whole number
    } else {
        return quantity.toFixed(1); // Return with 1 decimal place
    }
}

/**
 * Update item quantity
 * @param {number} index - Item index
 * @param {number} value - New quantity value
 */
function updateItemQuantity(index, value) {
    const item = editItems[index];
    let newQuantity = parseFloat(value) || 0;
    
    // Validate quantity based on unit_type - sama seperti di cart.js
    if (item.unit_type === 'meter') {
        // Allow decimal for meter (e.g., 10.4)
        if (newQuantity < 0.1) {
            newQuantity = 0.1;
        }
        // Round to 1 decimal place for meter (sama seperti cart.js)
        newQuantity = Math.round(newQuantity * 10) / 10;
    } else {
        // Only integer for other unit types (e.g., pcs, box, etc.)
        if (newQuantity < 1) {
            newQuantity = 1;
        }
        // Round to integer for non-meter units
        newQuantity = Math.round(newQuantity);
    }
    
    editItems[index].quantity = newQuantity;
    renderEditItems();
    calculateEditTotals();
    return true;
}

/**
 * Update item price
 * @param {number} index - Item index
 * @param {string} value - New price value
 */
function updateItemPrice(index, value) {
    editItems[index].price = parseFloat(value) || 0;
    renderEditItems();
    calculateEditTotals();
}

/**
 * Update item discount
 * @param {number} index - Item index
 * @param {string} value - New discount value
 */
function updateItemDiscount(index, value) {
    editItems[index].discount = parseFloat(value) || 0;
    renderEditItems();
    calculateEditTotals();
}

/**
 * Remove edit item
 * @param {number} index - Item index
 */
function removeEditItem(index) {
    if (confirm('Apakah Anda yakin ingin menghapus item ini?')) {
        editItems.splice(index, 1);
        renderEditItems();
        calculateEditTotals();
    }
}

/**
 * Calculate edit totals
 */
function calculateEditTotals() {
    let subtotal = 0;
    let totalDiscount = 0;

    editItems.forEach(item => {
        subtotal += (item.quantity * item.price);
        totalDiscount += item.discount;
    });

    // Calculate tax (same rate as original)
    const originalSubtotal = transaksiEdit.subtotal;
    const originalTax = transaksiEdit.tax;
    const taxRate = originalSubtotal > 0 ? originalTax / originalSubtotal : 0;
    const tax = subtotal * taxRate;

    const total = subtotal + tax - totalDiscount;
    const difference = total - transaksiEdit.total;

    // Update display
    document.getElementById('editSubtotal').textContent = `Rp ${formatUang(subtotal)}`;
    document.getElementById('editTotal').textContent = `Rp ${formatUang(total)}`;
    
    const diffElement = document.getElementById('editDifference');
    diffElement.textContent = `Rp ${formatUang(Math.abs(difference))}`;
    diffElement.className = `font-bold ml-2 ${difference >= 0 ? 'text-green-600' : 'text-red-600'}`;
    
    if (difference > 0) {
        diffElement.textContent = `+Rp ${formatUang(difference)}`;
    } else if (difference < 0) {
        diffElement.textContent = `-Rp ${formatUang(Math.abs(difference))}`;
    } else {
        diffElement.textContent = `Rp 0`;
        diffElement.className = 'font-bold ml-2 text-gray-600';
    }
}

/**
 * Submit transaction edit
 */
async function submitTransactionEdit() {
    try {
        // Validate form
        const reason = document.getElementById('editReason').value;
        const notes = document.getElementById('editNotes').value;

        if (!reason) {
            alert('Silakan pilih alasan edit');
            document.getElementById('editReason').focus();
            return;
        }

        if (editItems.length === 0) {
            alert('Transaksi harus memiliki minimal 1 item');
            return;
        }

        // Validate all items
        for (let i = 0; i < editItems.length; i++) {
            const item = editItems[i];
            if (item.quantity <= 0) {
                alert(`Quantity item "${item.product}" harus lebih dari 0`);
                return;
            }
            if (item.price < 0) {
                alert(`Harga item "${item.product}" tidak boleh negatif`);
                return;
            }
        }

        // Prepare edit data
        const editData = {
            items: editItems.map(item => ({
                product_id: item.product_id,
                quantity: item.quantity,
                price: item.price,
                discount: item.discount
            })),
            reason: reason,
            notes: notes || null
        };

        // Submit edit request
        const success = await submitEditRequest(editData);
        
        if (success) {
            // Close modal
            tutupModal('editTransactionModal');
        }

    } catch (error) {
        console.error('Error submitting edit:', error);
        alert('Terjadi kesalahan saat mengajukan edit: ' + error.message);
    }
}

/**
 * Submit edit request to API
 * @param {Object} editData - Edit data to submit
 * @returns {boolean} Success status
 */
async function submitEditRequest(editData) {
    try {
        if (!transaksiEdit) {
            throw new Error('Data transaksi tidak ditemukan');
        }

        const token = localStorage.getItem('token') || document.querySelector('meta[name="csrf-token"]').content;

        const response = await fetch(`/api/orders/${transaksiEdit.id}/request-edit`, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'Authorization': `Bearer ${token}`
            },
            body: JSON.stringify(editData)
        });

        const result = await response.json();

        if (!response.ok) {
            throw new Error(result.message || `HTTP Error: ${response.status}`);
        }

        if (result.success) {
            alert('Permintaan edit transaksi berhasil diajukan! Menunggu persetujuan dari Finance dan Operational.');
            
            // Refresh transaction data
            const hariIni = new Date();
            await ambilDataTransaksi(hariIni, hariIni);
            
            // Reset transaction data
            transaksiEdit = null;
            
            return true;
        } else {
            throw new Error(result.message || 'Gagal mengajukan permintaan edit');
        }

    } catch (error) {
        console.error('Error submitting edit request:', error);
        alert(`Gagal mengajukan permintaan edit: ${error.message}`);
        return false;
    }
}