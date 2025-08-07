/**
 * Konfigurasi POS System
 */
const POS_CONFIG = {
    // API Configuration
    API_TOKEN: localStorage.getItem('token') || '',
    
    // Timeout Settings
    SCAN_THRESHOLD: 100,
    MIN_BARCODE_LENGTH: 3,
    SEARCH_DELAY: 300,
    
    // UI Settings
    TOAST_DURATION: 3000,
    
    // Business Rules
    DEFAULT_TAX_RATE: 0,
    MIN_STOCK_WARNING: 5,
    
    // Payment Methods
    PAYMENT_METHODS: [
        { id: 'cash', name: 'Tunai', icon: 'wallet' },
        { id: 'qris', name: 'QRIS', icon: 'qr-code' },
        { id: 'transfer', name: 'Transfer Bank', icon: 'banknote' }
    ],
    
    // Transaction Types
    TRANSACTION_TYPES: [
        { id: 'regular', name: 'Reguler' },
        { id: 'dp', name: 'DP (Down Payment)' },
        { id: 'pelunasan', name: 'Pelunasan' },
        { id: 'refund', name: 'Refund' }
    ],
    
    // Tax Types
    TAX_TYPES: [
        { id: 'non_pkp', name: 'Non PKP', rate: 0 },
        { id: 'pkp', name: 'PKP', rate: 11 }
    ]
};

// Outlet Information
let outletInfo = {
    id: parseInt(localStorage.getItem('outlet_id')) || 1,
    name: localStorage.getItem('outlet_name') || 'Aladdin Karpet',
    tax: 0,
    qris: null,
    bank_account: null,
    shift_id: parseInt(localStorage.getItem('shift_id')) || null,
    tax_type: 'non_pkp'
};