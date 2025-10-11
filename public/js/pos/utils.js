/**
 * Utility Functions untuk POS System
 */

// Format currency dengan dukungan desimal
function formatCurrency(num, withDecimal = false) {
    if (withDecimal) {
        return 'Rp ' + parseFloat(num).toLocaleString('id-ID', {
            minimumFractionDigits: 1,
            maximumFractionDigits: 2
        });
    }
    return 'Rp ' + Math.round(num).toLocaleString('id-ID');
}

// Format currency input
function formatCurrencyInput(value) {
    let num = value.replace(/[^0-9.,]/g, '');
    num = parseFloat(num) || 0;
    return num.toLocaleString('id-ID');
}

// Parse currency input to number dengan support desimal
function parseCurrencyInput(value) {
    if (typeof value !== 'string') return 0;
    // Hapus semua karakter kecuali angka, titik, dan koma
    let cleanValue = value.replace(/[^0-9.,]/g, '');
    // Ganti koma dengan titik untuk parsing desimal
    cleanValue = cleanValue.replace(',', '.');
    return parseFloat(cleanValue) || 0;
}

// Parse quantity dengan dukungan desimal
function parseQuantity(value) {
    if (typeof value !== 'string' && typeof value !== 'number') return 1;
    const cleanValue = String(value).replace(/[^0-9.,]/g, '').replace(',', '.');
    const parsed = parseFloat(cleanValue);
    return isNaN(parsed) || parsed <= 0 ? 1 : parsed;
}

// Parse quantity berdasarkan unit type
function parseQuantityByUnitType(value, unitType) {
    if (typeof value !== 'string' && typeof value !== 'number') return 1;
    const cleanValue = String(value).replace(/[^0-9.,]/g, '').replace(',', '.');
    const parsed = parseFloat(cleanValue);
    
    if (isNaN(parsed) || parsed <= 0) return 1;
    
    // Untuk meter, izinkan desimal
    if (unitType === 'meter') {
        return parsed;
    }
    
    // Untuk pcs/unit, bulatkan ke integer
    return Math.floor(parsed);
}

// Format quantity untuk display
function formatQuantity(qty) {
    const parsed = parseFloat(qty);
    return parsed % 1 === 0 ? parsed.toString() : parsed.toFixed(1);
}

// Show notification dengan SweetAlert
function showNotification(message, type = 'success') {
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: POS_CONFIG.TOAST_DURATION,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });

    const iconMap = {
        success: { icon: 'check-circle', bg: '#22c55e' },
        error: { icon: 'x-circle', bg: '#ef4444' },
        warning: { icon: 'alert-circle', bg: '#f59e0b' },
        info: { icon: 'info', bg: '#3b82f6' }
    };

    const config = iconMap[type] || iconMap.success;

    Toast.fire({
        iconHtml: `<i data-lucide="${config.icon}" class="text-white"></i>`,
        title: message,
        background: config.bg,
        color: 'white',
        iconColor: 'white'
    });

    lucide.createIcons();
}

// Generate invoice number dengan format: OUTLET-YYYYMMDD-XXXX
function generateInvoiceNumber(outletCode = 'JOGJA1') {
    const now = new Date();
    const dateStr = now.toISOString().slice(0, 10).replace(/-/g, '');
    const randomNum = Math.floor(Math.random() * 9999).toString().padStart(4, '0');
    return `${outletCode}-${dateStr}-${randomNum}`;
}

// Generate order number (urut untuk semua cabang)
function generateOrderNumber() {
    const now = new Date();
    const timestamp = now.getTime().toString().slice(-8);
    return `ORD-${timestamp}`;
}

// Debounce function
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Loading overlay
function showLoading(message = 'Memproses...') {
    const existing = document.querySelector('.loading-overlay');
    if (existing) existing.remove();

    const overlay = document.createElement('div');
    overlay.className = 'loading-overlay fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
    overlay.innerHTML = `
        <div class="bg-white p-6 rounded-lg shadow-lg text-center max-w-sm mx-4">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-500 mx-auto mb-4"></div>
            <p class="text-gray-700 font-medium">${message}</p>
        </div>
    `;
    document.body.appendChild(overlay);
    return overlay;
}

function hideLoading() {
    const overlay = document.querySelector('.loading-overlay');
    if (overlay) overlay.remove();
}

// Modal functions
function openModal(id) {
    document.getElementById(id).classList.remove('hidden');
}

function closeModal(id) {
    document.getElementById(id).classList.add('hidden');
}

// Validate barcode scan
function isBarcodeScan(input, timeDiff) {
    return input.length >= POS_CONFIG.MIN_BARCODE_LENGTH && 
           timeDiff < POS_CONFIG.SCAN_THRESHOLD;
}