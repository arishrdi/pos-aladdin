
<!-- Payment Process Modal -->
<div id="paymentModal" class="fixed inset-0 z-40 flex items-center justify-center bg-black bg-opacity-50 hidden">
    <div class="bg-white rounded-lg shadow-lg max-w-md w-full p-6 text-center relative">
        <h3 class="text-lg font-semibold mb-4">Pilih Metode Pembayaran</h3>
        
        <!-- Payment summary - dynamically filled -->
        <div id="paymentSummary" class="text-left mb-4">
            <!-- Will be filled with order summary -->
        </div>
        
        <!-- Payment methods -->
        <div class="grid grid-cols-2 gap-4 mb-6">
            <button onclick="selectPaymentMethod('cash')" class="border p-3 rounded hover:bg-gray-100">
                Cash
            </button>
            <button onclick="selectPaymentMethod('transfer')" class="border p-3 rounded hover:bg-gray-100">
                Transfer
            </button>
        </div>
        
        <!-- Action buttons -->
        <div class="flex justify-end gap-3">
            <button onclick="closeModal('paymentModal')" class="bg-gray-200 px-4 py-2 rounded">
                Batal
            </button>
            <button id="processPaymentBtn" onclick="processPayment()" class="bg-blue-500 text-white px-4 py-2 rounded">
                Bayar
            </button>
        </div>
    </div>
</div>

<!-- Success Payment Modal -->
<div id="successPaymentModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
    <div class="bg-white rounded-lg shadow-lg max-w-sm w-full p-6 text-center relative">
        <!-- Icon Success -->
        <div class="flex justify-center mb-4">
            <div class="bg-green-100 rounded-full p-3">
                <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" stroke-width="2" 
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
            </div>
        </div>

        <!-- Text Success -->
        <h3 class="text-lg font-semibold text-green-600 mb-1">Pembayaran Berhasil!</h3>
        <p class="text-gray-600 text-sm mb-4">Transaksi telah berhasil diselesaikan</p>
        
        <!-- Countdown Timer -->
        <div id="countdownTimer" class="text-sm text-gray-500 mb-4 hidden">
            Halaman akan refresh dalam <span id="countdownSeconds" class="font-bold text-red-500">5</span> detik
        </div>

        <!-- Buttons -->
        <div class="flex justify-center gap-3">
            <button onclick="cetakInvoice()" class="bg-green-500 hover:bg-green-600 text-white font-semibold px-4 py-2 rounded">
                Cetak Struk
            </button>
            <button onclick="closeModalWithRefresh('successPaymentModal')" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-100">
                Tutup
            </button>
        </div>
    </div>
</div>

<script>
    // Global variables to track current transaction
    let currentOrder = {
        id: null,             // Will be set when order is created
        items: [],            // Cart items
        payment_method: null, // Selected payment method
        total: 0              // Order total
    };
    
    // Variable untuk tracking countdown timer
    let countdownInterval = null;
    let refreshTimeout = null;
    
    // Base URL for API calls - modify this to match your environment
    const BASE_URL = window.location.origin;
    
    // Function to show payment modal with cart details
    function showPaymentModal(cartItems, total) {
        // Update current order with cart items and total
        currentOrder.items = cartItems;
        currentOrder.total = total;
        
        // Update payment summary in modal
        const summaryHTML = `
            <div class="mb-3">
                <div class="font-semibold">Ringkasan Pembelian:</div>
                <div class="text-sm">Total Item: ${cartItems.length}</div>
                <div class="text-lg font-bold">Total: Rp ${formatCurrency(total)}</div>
            </div>
        `;
        
        document.getElementById('paymentSummary').innerHTML = summaryHTML;
        
        // Show payment modal
        const modal = document.getElementById('paymentModal');
        modal.classList.remove('hidden');
    }
    
    // Function to select payment method
    function selectPaymentMethod(method) {
        currentOrder.payment_method = method;
        
        // Highlight selected payment method (optional UI enhancement)
        const buttons = document.querySelectorAll('#paymentModal button');
        buttons.forEach(btn => {
            if (btn.innerText.toLowerCase().includes(method)) {
                btn.classList.add('bg-blue-100', 'border-blue-500', 'border-2');
            } else {
                btn.classList.remove('bg-blue-100', 'border-blue-500', 'border-2');
            }
        });
    }
    
    // Function to process payment
    async function processPayment() {
        try {
            // Validate payment method
            if (!currentOrder.payment_method) {
                alert('Silakan pilih metode pembayaran');
                return;
            }
            
            // Get auth token
            const token = localStorage.getItem('token') || document.querySelector('meta[name="csrf-token"]')?.content;
            if (!token) {
                throw new Error('Token autentikasi tidak ditemukan');
            }
            
            // Get outlet ID from localStorage or elsewhere in your app
            const outletId = localStorage.getItem('outlet_id');
            if (!outletId) {
                throw new Error('Outlet ID tidak ditemukan');
            }
            
            // Prepare order data
            const orderData = {
                outlet_id: outletId,
                items: currentOrder.items,
                payment_method: currentOrder.payment_method,
                // Add other necessary fields based on your API requirements
            };
            
            console.log('Submitting order:', orderData);
            
            // Submit order to API
            const response = await fetch(`${BASE_URL}/api/orders`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                body: JSON.stringify(orderData)
            });
            
            if (!response.ok) {
                throw new Error(`Server responded with status: ${response.status}`);
            }
            
            const result = await response.json();
            console.log('Order creation response:', result);
            
            if (!result.success) {
                throw new Error(result.message || 'Gagal membuat order');
            }
            
            // Store the created order ID and data for receipt printing
            if (result && result.data && result.data.id) {
                currentOrder.id = result.data.id;
                currentOrder.data = result.data; // Store the full order data
                console.log('Order ID stored:', currentOrder.id);
            } else {
                throw new Error('Order ID not received from server');
            }
            
            // Close payment modal
            closeModal('paymentModal');
            
            // Show success modal
            const successModal = document.getElementById('successPaymentModal');
            successModal.classList.remove('hidden');
            
            // Start auto refresh timer setelah modal sukses muncul
            startAutoRefreshTimer();
            
        } catch (error) {
            console.error('Payment processing error:', error);
            alert(`Gagal memproses pembayaran: ${error.message}`);
        }
    }
    
    // Function to close modal
    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) modal.classList.add('hidden');
    }
    
    // Function to close modal with immediate refresh
    function closeModalWithRefresh(modalId) {
        // Stop any existing timers
        clearCountdownTimer();
        
        // Close modal
        closeModal(modalId);
        
        // Show loading message (optional)
        const loadingDiv = document.createElement('div');
        loadingDiv.innerHTML = `
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
                <div class="bg-white rounded-lg p-6 text-center">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500 mx-auto mb-3"></div>
                    <p class="text-gray-600">Memuat ulang halaman...</p>
                </div>
            </div>
        `;
        document.body.appendChild(loadingDiv);
        
        // Refresh setelah delay singkat
        setTimeout(() => {
            window.location.reload();
        }, 1000);
    }
    
    // Function untuk memulai countdown timer auto refresh
    function startAutoRefreshTimer() {
        let seconds = 5;
        
        // Show countdown timer
        const timerDiv = document.getElementById('countdownTimer');
        const secondsSpan = document.getElementById('countdownSeconds');
        
        if (timerDiv && secondsSpan) {
            timerDiv.classList.remove('hidden');
            secondsSpan.textContent = seconds;
            
            // Update countdown setiap detik
            countdownInterval = setInterval(() => {
                seconds--;
                secondsSpan.textContent = seconds;
                
                if (seconds <= 0) {
                    clearInterval(countdownInterval);
                }
            }, 1000);
        }
        
        // Set timeout untuk refresh halaman
        refreshTimeout = setTimeout(() => {
            // Clear any existing intervals
            clearCountdownTimer();
            
            // Refresh halaman
            window.location.reload();
        }, 5000); // 5 detik
    }
    
    // Function untuk membersihkan timer
    function clearCountdownTimer() {
        if (countdownInterval) {
            clearInterval(countdownInterval);
            countdownInterval = null;
        }
        if (refreshTimeout) {
            clearTimeout(refreshTimeout);
            refreshTimeout = null;
        }
        
        // Hide countdown timer
        const timerDiv = document.getElementById('countdownTimer');
        if (timerDiv) {
            timerDiv.classList.add('hidden');
        }
    }

    // Fungsi untuk mengambil template struk dari database
    async function fetchReceiptTemplate() {
        try {
            // Get auth token
            const token = localStorage.getItem('token') || document.querySelector('meta[name="csrf-token"]')?.content;
            if (!token) {
                throw new Error('Token autentikasi tidak ditemukan');
            }
            
            // Get outlet ID
            const outletId = localStorage.getItem('outlet_id');
            if (!outletId) {
                throw new Error('Outlet ID tidak ditemukan');
            }
            
            console.log('Fetching receipt template for outlet ID:', outletId);
            
            // Fetch template from API
            const response = await fetch(`${BASE_URL}/api/print-template/${outletId}`, {
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Authorization': `Bearer ${token}`
                }
            });
            
            if (!response.ok) {
                throw new Error(`Gagal mengambil template cetak: ${response.status}`);
            }
            
            const responseData = await response.json();
            
            if (!responseData.success) {
                throw new Error(responseData.message || 'Gagal memuat template cetak');
            }
            
            console.log('Receipt template fetched successfully:', responseData.data);
            return responseData.data;
            
        } catch (error) {
            console.error('Error fetching receipt template:', error);
            // Return default template when fetch fails
            return {
                company_name: 'Aladdin Karpet',
                company_slogan: 'Roti dan Kue Terbaik',
                logo_url: '/images/logo.png',
                footer_message: 'Terima kasih telah berbelanja',
                outlet: {
                    name: 'Aladdin Karpet',
                    address: 'Jl. Contoh No. 123',
                    phone: '0812-3456-7890'
                }
            };
        }
    }

    // Function to print receipt dengan auto refresh setelah cetak
    async function cetakInvoice() {
        try {
            // Stop countdown timer sementara saat mencetak
            clearCountdownTimer();
            
            // Periksa apakah currentOrder ada
            if (!currentOrder || !currentOrder.data) {
                throw new Error('Data transaksi tidak tersedia');
            }

            // Ambil data dari currentOrder yang sudah disimpan
            const order = currentOrder.data;
            
            // Fetch receipt template from database
            const templateData = await fetchReceiptTemplate();

            // Buat window cetak
            const printWindow = window.open('', '_blank', 'width=400,height=600');
            if (!printWindow) {
                throw new Error('Popup cetak gagal dibuka. Periksa izin browser.');
            }

            // Generate HTML struk dengan template dari database
            const receiptHTML = generateReceiptWithTemplate(order, templateData);
            printWindow.document.open();
            printWindow.document.write(receiptHTML);
            printWindow.document.close();
            
            // Cetak setelah window siap
            printWindow.onload = () => {
                printWindow.print();
                
                // Auto-close window after print dialog is closed or cancelled
                const mediaQueryList = printWindow.matchMedia('print');
                mediaQueryList.addEventListener('change', (mql) => {
                    if (!mql.matches) {
                        // Print dialog was closed
                        printWindow.close();
                        
                        // Mulai proses refresh setelah print selesai
                        startPrintRefreshProcess();
                    }
                }, { once: true });
                
                // Safety timeout to close the window if event listener fails
                setTimeout(() => {
                    try {
                        if (!printWindow.closed) {
                            printWindow.close();
                            startPrintRefreshProcess();
                        }
                    } catch (e) {
                        console.log('Window may already be closed');
                        startPrintRefreshProcess();
                    }
                }, 4000);
            };
            
        } catch (error) {
            console.error('Error printing receipt:', error);
            alert(`Gagal mencetak struk: ${error.message}`);
            
            // Restart countdown timer jika error
            startAutoRefreshTimer();
        }
    }
    
    // Function untuk memulai proses refresh setelah cetak
    function startPrintRefreshProcess() {
        // Close success modal
        closeModal('successPaymentModal');
        
        // Show loading with countdown
        const loadingDiv = document.createElement('div');
        loadingDiv.id = 'printRefreshLoading';
        loadingDiv.innerHTML = `
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
                <div class="bg-white rounded-lg p-6 text-center max-w-sm w-full mx-4">
                    <div class="bg-green-100 rounded-full p-3 w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                        <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-green-600 mb-2">Struk Berhasil Dicetak!</h3>
                    <p class="text-gray-600 text-sm mb-4">Halaman akan refresh dalam <span id="printCountdownSeconds" class="font-bold text-red-500">5</span> detik</p>
                    <div class="flex justify-center">
                        <button onclick="refreshNow()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded text-sm">
                            Refresh Sekarang
                        </button>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(loadingDiv);
        
        // Start countdown
        let seconds = 5;
        const secondsSpan = document.getElementById('printCountdownSeconds');
        
        const printCountdownInterval = setInterval(() => {
            seconds--;
            if (secondsSpan) {
                secondsSpan.textContent = seconds;
            }
            
            if (seconds <= 0) {
                clearInterval(printCountdownInterval);
            }
        }, 1000);
        
        // Auto refresh after 5 seconds
        setTimeout(() => {
            clearInterval(printCountdownInterval);
            window.location.reload();
        }, 5000);
    }
    
    // Function untuk refresh immediate
    function refreshNow() {
        window.location.reload();
    }

    // Function to generate receipt HTML with template
    function generateReceiptWithTemplate(order, templateData) {
        // Format tanggal
        const formatDate = (dateString) => {
            if (!dateString) return 'Tanggal tidak tersedia';
            try {
                // Normalisasi string tanggal
                let normalized = dateString.trim();

                normalized = normalized.replace(/\\\//g, '/');
                
                // Cek apakah format DD/MM/YYYY HH:mm atau DD/MM/YYYY
                const datePattern = /^(\d{1,2})\/(\d{1,2})\/(\d{4})(?:\s+(\d{1,2}):(\d{1,2}))?/;
                const match = normalized.match(datePattern);
                
                if (match) {
                    const [, day, month, year, hour = '00', minute = '00'] = match;
                    
                    // Parse ke Date object dengan format ISO (YYYY-MM-DD)
                    const date = new Date(
                        parseInt(year), 
                        parseInt(month) - 1, 
                        parseInt(day), 
                        parseInt(hour), 
                        parseInt(minute)
                    );
                    
                    // Validasi apakah tanggal valid
                    if (isNaN(date.getTime())) {
                        throw new Error('Invalid date');
                    }
                    
                    return date.toLocaleString('id-ID', {
                        day: '2-digit',
                        month: 'long',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit',
                        timeZone: 'Asia/Jakarta'
                    });
                }
                
                // Jika bukan format DD/MM/YYYY, coba parsing biasa
                normalized = normalized.includes('T') ? normalized : normalized.replace(' ', 'T');
                
                // Tambahkan timezone jika belum ada
                if (!/[+-]\d{2}:\d{2}$/.test(normalized)) {
                    normalized += '+07:00'; // Asumsi waktu dalam WIB
                }
                
                const date = new Date(normalized);
                
                if (isNaN(date.getTime())) {
                    throw new Error('Invalid date format');
                }
                
                return date.toLocaleString('id-ID', {
                    day: '2-digit',
                    month: 'long',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                    timeZone: 'Asia/Jakarta'
                });
            } catch (e) {
                console.error('Error formatting date:', e, 'Input:', dateString);
                return 'Tanggal tidak valid';
            }
        };

        // Helper function untuk menangani nilai yang mungkin undefined/null
        const safeNumber = (value) => {
            const num = parseFloat(value);
            return isNaN(num) ? 0 : num;
        };

        // Format mata uang dengan penanganan error
        const formatCurrency = (value) => {
            return safeNumber(value).toLocaleString('id-ID');
        };

        // Get outlet data from template or use defaults
        const outletData = templateData.outlet || {
            name: templateData.company_name || 'Aladdin Karpet UHUY',
            address: '',
            phone: '',
            tax: 0
        };

        // Use logo from template or default
        const logoPath = templateData.logo_url || 'public/images/logo.png';
        
        // Data order yang aman
        const safeOrder = {
            ...order,
            subtotal: safeNumber(order.subtotal),
            discount: safeNumber(order.discount),
            tax: safeNumber(order.tax),
            total: safeNumber(order.total),
            total_paid: safeNumber(order.total_paid || order.total),
            change: safeNumber(order.change || 0),
            items: order.items || [],
            payment_method: order.payment_method || 'cash',
            created_at: order.created_at || new Date().toISOString(),
            order_number: order.order_number || 'TANPA-NOMOR',
            user: order.user || 'Kasir'
        };

        return `
            <!DOCTYPE html>
            <html>
            <head>
                <title>Struk #${safeOrder.order_number}</title>
                <meta charset="UTF-8">
                <style>
                    /* Reset dan base styling */
                    * {
                        font-weight: 'bold';
                        font-family: 'Courier New', monospace;
                    }
                    
                    body {
                        font-weight: 'bold';
                        font-size: 18px;
                        color: #000;
                    }
                    
                    /* Header styling */
                    .receipt-header {
                        display: flex;
                        align-items: center;
                        gap: 10px;
                        margin-bottom: 15px;
                        padding-bottom: 10px;
                        border-bottom: 1px dashed #ccc;
                    }
                    
                    .logo-container {
                        width: 70px;
                        height: 70px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        padding: 3px;  
                        background-color: white; 
                    }
                    
                    .logo {
                        max-width: 100%;
                        max-height: 100%;
                        object-fit: contain;
                        filter: grayscale(100%) contrast(300%);
                        -webkit-filter: grayscale(100%) contrast(300%);
                    }
                    
                    .header-text {
                        flex: 1;
                        font-weight: bold;
                        font-size: 18px;
                        text-align: right;
                    }
                    
                    .company-name {
                        font-weight: bold;
                        font-size: 18px;
                        margin-bottom: 3px;
                    }
                    
                    .company-info {
                        font-size: 18px;
                        font-weight: bold;
                        line-height: 1.3;
                    }
                    
                    /* Divider */
                    .divider {
                        border-top: 1px dashed #000;
                        margin: 8px 0;
                    }
                    
                    /* Transaction info */
                    .transaction-info {
                        margin-bottom: 10px;
                        font-weight: bold;
                    }
                    
                    .info-row {
                        display: flex;
                        justify-content: space-between;
                        margin-bottom: 3px;
                    }
                    
                    .info-label {
                        font-weight: bold;
                    }
                    
                    /* Items list */
                    .items-list {
                        margin: 10px 0;
                    }
                    
                    .item-row {
                        display: flex;
                        justify-content: space-between;
                        margin-bottom: 5px;
                    }
                    
                    .item-name {
                        font-weight: bold;
                        font-size: 18px;
                        flex: 2;
                    }
                    
                    .item-price {
                        flex: 1;
                        font-weight: bold;
                        text-align: right;
                    }
                    
                    /* Totals */
                    .totals {
                        font-weight: bold;
                        margin-top: 10px;
                    }
                    
                    .total-row {
                        display: flex;
                        justify-content: space-between;
                        margin-bottom: 5px;
                    }
                    
                    .grand-total {
                        font-weight: bold;
                        font-size: 20px;
                        margin-top: 8px;
                        padding-top: 5px;
                        border-top: 1px dashed #000;
                    }
                    
                    /* Payment info */
                    .payment-info {
                        font-weight: bold;
                        margin-top: 10px;
                    }
                    
                    /* Footer */
                    .receipt-footer {
                        font-weight: bold;
                        margin-top: 15px;
                        text-align: center;
                        font-size: 12px;
                        line-height: 1.4;
                    }
                    
                    /* Utilities */
                    .text-center {
                        text-align: center;
                    }
                    
                    .text-right {
                        text-align: right;
                    }
                    
                    .text-bold {
                        font-weight: bold;
                    }
                    @media print {
                        .logo {
                            -webkit-filter: grayscale(100%);
                            filter: grayscale(100%);
                        }
                    }
                </style>
            </head>
            <body>
                <!-- Header dengan logo -->
                <div class="receipt-header">
                    <div class="logo-container">
                        <img src="${logoPath}" 
                            alt="Logo Toko" 
                            class="logo"
                            onerror="this.style.display='none'"
                            style="width: auto; height: auto; max-width: 65px; max-height: 65px;">
                    </div>
                    <div class="header-text">
                        <div class="company-name">${templateData.company_name || outletData.name || 'TOKO ANDA'}</div>
                        <div class="company-info">
                            ${templateData.company_slogan || ''}
                            ${outletData.address ? `<br>${outletData.address}` : ''}
                            ${outletData.phone ? `<br>Telp: ${outletData.phone}` : ''}
                        </div>
                    </div>
                </div>
                
                <!-- Info transaksi -->
                <div class="transaction-info">
                    <div class="info-row">
                        <span class="info-label">No. Order:</span>
                        <span>${safeOrder.order_number}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Tanggal:</span>
                        <span>${formatDate(safeOrder.created_at)}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Kasir:</span>
                        <span>${safeOrder.user}</span>
                    </div>
                </div>
                
                <div class="divider"></div>
                
                <!-- Daftar item -->
                <div class="items-list">
                    ${safeOrder.items.length > 0 
                        ? safeOrder.items.map(item => {
                            const safeItem = {
                                ...item,
                                quantity: safeNumber(item.quantity),
                                price: safeNumber(item.price),
                                discount: safeNumber(item.discount),
                                product: item.product || 'Produk'
                            };
                            
                            return `
                                <div class="item-row">
                                    <div class="item-name">
                                        ${safeItem.quantity}x ${safeItem.product}
                                    </div>
                                    <div class="item-price">
                                        Rp ${formatCurrency(safeItem.price * safeItem.quantity)}
                                        ${safeItem.discount > 0 ? `<br><small>Diskon: -Rp ${formatCurrency(safeItem.discount)}</small>` : ''}
                                    </div>
                                </div>
                            `;
                        }).join('')
                        : '<div class="text-center">Tidak ada item</div>'
                    }
                </div>
                
                <div class="divider"></div>
                
                <!-- Total pembelian -->
                <div class="totals">
                    <div class="total-row">
                        <span>Subtotal:</span>
                        <span>Rp ${formatCurrency(safeOrder.subtotal)}</span>
                    </div>
                    
                    ${safeOrder.discount > 0 ? `
                    <div class="total-row">
                        <span>Diskon:</span>
                        <span>- Rp ${formatCurrency(safeOrder.discount)}</span>
                    </div>
                    ` : ''}
                    
                    ${safeOrder.tax > 0 ? `
                    <div class="total-row">
                        <span>Pajak:</span>
                        <span>${formatCurrency(safeOrder.tax)}%</span>
                    </div>
                    ` : ''}
                    
                    <div class="total-row grand-total">
                        <span>TOTAL:</span>
                        <span>Rp ${formatCurrency(safeOrder.total)}</span>
                    </div>
                </div>
                
                <!-- Info pembayaran -->
                <div class="payment-info">
                    <div class="total-row">
                        <span>Metode Bayar:</span>
                        <span>${safeOrder.payment_method.toUpperCase()}</span>
                    </div>
                    
                    ${safeOrder.payment_method === 'cash' ? `
                    <div class="total-row">
                        <span>Dibayar:</span>
                        <span>Rp ${formatCurrency(safeOrder.total_paid)}</span>
                    </div>
                    <div class="total-row">
                        <span>Kembalian:</span>
                        <span>Rp ${formatCurrency(safeOrder.change)}</span>
                    </div>
                    ` : ''}
                </div>
                
                ${safeOrder.member ? `
                <div class="divider"></div>
                <div class="info-row">
                    <span class="info-label">Member:</span>
                    <span>${safeOrder.member.name || ''} (${safeOrder.member.member_code || ''})</span>
                </div>
                ` : ''}

                ${safeOrder.service_type ? `
                <div class="divider"></div>
                <div class="text-center" style="font-weight: bold; margin-bottom: 8px;">LAYANAN KARPET MASJID</div>
                <div class="info-row">
                    <span class="info-label">Jenis Layanan:</span>
                    <span>${safeOrder.service_type === 'potong_obras_kirim' ? 'Potong, Obras & Kirim' : 'Pasang di Tempat'}</span>
                </div>
                ${safeOrder.installation_date ? `
                <div class="info-row">
                    <span class="info-label">Estimasi Pemasangan:</span>
                    <span>${new Date(safeOrder.installation_date).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })}</span>
                </div>
                ` : ''}
                ${safeOrder.installation_notes ? `
                <div class="info-row" style="margin-top: 5px;">
                    <span class="info-label">Rincian Pemasangan:</span>
                </div>
                <div style="margin-top: 3px; font-size: 14px; line-height: 1.3;">
                    ${safeOrder.installation_notes}
                </div>
                ` : ''}
                ` : ''}
                
                <!-- Footer -->
                <div class="divider"></div>
                <div class="receipt-footer">
                    ${templateData.footer_message || 'Terima kasih telah berbelanja'}<br>
                    Barang yang sudah dibeli tidak dapat ditukar<br>
                    ${new Date().getFullYear()} © ${templateData.company_name || outletData.name || 'TOKO ANDA'}
                </div>
            </body>
            </html>
        `;
    }

    // Format currency function
    function formatCurrency(num) {
        return parseFloat(num).toLocaleString('id-ID');
    }

    // Example of how to start the payment process (call this from your product selection UI)
    function startCheckout(cartItems, total) {
        showPaymentModal(cartItems, total);
    }
    
    // Event listener untuk membersihkan timer jika user menutup halaman
    window.addEventListener('beforeunload', function() {
        clearCountdownTimer();
    });
    
    // Event listener untuk keyboard shortcuts (optional)
    document.addEventListener('keydown', function(event) {
        // ESC key untuk close modal
        if (event.key === 'Escape') {
            const paymentModal = document.getElementById('paymentModal');
            const successModal = document.getElementById('successPaymentModal');
            
            if (!paymentModal.classList.contains('hidden')) {
                closeModal('paymentModal');
            } else if (!successModal.classList.contains('hidden')) {
                closeModalWithRefresh('successPaymentModal');
            }
        }
        
        // Enter key untuk refresh now (jika loading refresh sedang aktif)
        if (event.key === 'Enter' && document.getElementById('printRefreshLoading')) {
            refreshNow();
        }
    });
</script>