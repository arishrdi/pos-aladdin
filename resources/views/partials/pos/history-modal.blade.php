<!-- History Modal -->
<div id="historyModal" class="fixed inset-0 z-50 hidden">
    <!-- Overlay -->
    <div class="absolute w-full h-full bg-gray-900 opacity-50" onclick="tutupModal('historyModal')"></div>

    <!-- Modal Box -->
    <div class="bg-white w-[95%] md:w-11/12 md:max-w-6xl mx-auto rounded shadow-lg z-50 relative mt-10 mb-10 max-h-[90vh] flex flex-col">
        
        <!-- Header (Fixed inside modal) -->
        <div class="p-4 md:p-6 border-b sticky top-0 bg-white z-10">
            <div class="flex justify-between items-start md:items-center flex-col md:flex-row gap-2">
                <div>
                    <h2 class="text-xl md:text-2xl font-bold">Riwayat Transaksi</h2>
                    <p class="text-sm md:text-base text-gray-600">Lihat riwayat transaksi berdasarkan tanggal</p>
                </div>
                <button onclick="tutupModal('historyModal')" class="text-gray-500 hover:text-red-500 text-xl md:text-2xl">✕</button>
            </div>
        </div>

        <!-- Body Scrollable -->
        <div class="overflow-y-auto px-4 md:px-8 py-6 flex-1">
            <!-- Date Range & Search -->
            <div class="flex flex-col md:flex-row justify-between gap-4 mb-6">
                <div class="relative w-full md:w-1/2">
                    <input id="dateRange" type="text" class="border p-3 rounded w-full pl-12 text-base" placeholder="Pilih rentang tanggal" readonly />
                    <div class="absolute left-4 top-3.5 text-gray-400">
                        <!-- Calendar Icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>

                <div class="relative w-full md:w-1/2">
                    <input 
                        id="searchInvoice" 
                        type="text" 
                        placeholder="Cari transaksi berdasarkan nomor invoice..." 
                        class="border p-3 rounded w-full pl-12 text-base"
                        oninput="filterTransaksi()"
                    />
                    <div class="absolute left-4 top-3.5 text-gray-400">
                        <!-- Search Icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Summary -->
            <div class="bg-green-50 p-4 rounded mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 sm:gap-0">
                <div id="summaryText">
                    <span class="font-semibold text-lg">Total Transaksi</span><br>
                    <span class="text-gray-500 text-sm">Memuat data...</span>
                </div>
                <div class="text-green-600 font-bold text-2xl" id="totalAmount">Rp 0</div>
            </div>

            <!-- Table -->
            <div class="overflow-auto border border-gray-200 rounded-md">
                <table class="w-full text-sm md:text-base text-left">
                    <thead class="bg-gray-100 text-gray-700">
                        <tr>
                            <th class="p-3">No</th>
                            <th class="p-3">Invoice</th>
                            <th class="p-3">Waktu</th>
                            <th class="p-3">Kasir</th>
                            <th class="p-3">Pembayaran</th>
                            <th class="p-3">Status</th>
                            <th class="p-3">Total</th>
                            <th class="p-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="transactionTable">
                        <tr class="border-t border-gray-200">
                            <td colspan="8" class="text-center py-6 text-gray-500">Memuat data transaksi...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Footer (Fixed inside modal) -->
        <div class="border-t p-4 md:p-6 bg-white sticky bottom-0 z-10">
            <div class="flex justify-end">
                <button onclick="tutupModal('historyModal')" class="px-5 py-3 text-base bg-green-500 text-white rounded hover:bg-green-600 transition">Tutup</button>
            </div>
        </div>

        @include('partials.pos.modal.modal-history-transaksi')
    </div>
</div>

<!-- Include Flatpickr CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<!-- Include Flatpickr JS -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>

<script>
     // Variabel global untuk menyimpan data transaksi
    let semuaTransaksi = [];
    let sedangMemuat = false;

    // Inisialisasi date range picker
    const dateRangePicker = flatpickr("#dateRange", {
        mode: "range",
        dateFormat: "d M Y",
        locale: "id",
        defaultDate: [new Date(), new Date()], // Set default tanggal hari ini
        onChange: function(tanggalTerpilih) {
            if (tanggalTerpilih.length === 2) {
                ambilDataTransaksi(tanggalTerpilih[0], tanggalTerpilih[1]);
            } else {
                ambilDataTransaksi();
            }
        }
    });

    function getHariIni() {
        const hariIni = new Date();
        return {
            start: new Date(hariIni.getFullYear(), hariIni.getMonth(), hariIni.getDate()),
            end: new Date(hariIni.getFullYear(), hariIni.getMonth(), hariIni.getDate(), 23, 59, 59)
        };
    }

    // Fungsi untuk mengambil data transaksi dari backend
    async function ambilDataTransaksi(tanggalMulai = null, tanggalSampai = null) {
        try {
            sedangMemuat = true;
            document.getElementById("transactionTable").innerHTML = `
                <tr class="border-t border-gray-200">
                    <td colspan="8" class="text-center py-6 text-gray-500">Sedang memuat data...</td>
                </tr>
            `;

            // Membuat URL endpoint dengan parameter
            let url = '/api/orders/history';
            const params = new URLSearchParams();
            const { start, end } = getHariIni();
            params.append('date_from', formatTanggalUntukBackend(start));
            params.append('date_to', formatTanggalUntukBackend(end));
            
            if (!tanggalMulai && !tanggalSampai) {
                const hariIni = new Date();
                tanggalMulai = hariIni;
                tanggalSampai = hariIni;
            }

            const outletId = localStorage.getItem('outlet_id');
            if (outletId) {
                params.append('outlet_id', outletId);
            }
            
            if (params.toString()) {
                url += `?${params.toString()}`;
            }

            // Ambil token dari localStorage atau meta tag
            const token = localStorage.getItem('token') || document.querySelector('meta[name="csrf-token"]').content;

            // Mengambil data dari backend
            const response = await fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Authorization': `Bearer ${token}`
                }
            });

            // Cek jika response tidak OK
            if (!response.ok) {
                throw new Error(`Gagal mengambil data: ${response.status} ${response.statusText}`);
            }

            const data = await response.json();
            
            // Jika sukses, proses data
            if (data.success) {
                // Konversi order_number menjadi invoice untuk konsistensi
                semuaTransaksi = data.data.orders.map(transaksi => ({
                    id: transaksi.id,
                    invoice: transaksi.order_number,  // Menggunakan order_number sebagai invoice
                    waktu: transaksi.created_at,      // Menggunakan created_at sebagai waktu
                    kasir: transaksi.user,
                    pembayaran: transaksi.payment_method,
                    status: transaksi.status === 'completed' ? 'Selesai' : 
                            transaksi.status === 'canceled' ? 'Dibatalkan' : transaksi.status,
                    total: parseFloat(transaksi.total),
                    date: transaksi.created_at.split(' ')[0],
                    items: transaksi.items,
                    outlet: transaksi.outlet,
                    outlet_id: transaksi.outlet_id,
                    member: transaksi.member,
                    // Tambahkan properti lain yang dibutuhkan
                    subtotal: parseFloat(transaksi.subtotal || 0),
                    tax: parseFloat(transaksi.tax || 0),
                    discount: parseFloat(transaksi.discount || 0),
                    total_paid: parseFloat(transaksi.total_paid || 0),
                    change: parseFloat(transaksi.change || 0),
                }));
                
                perbaruiTampilanTransaksi();
                perbaruiRingkasan({
                    date_from: data.data.date_from,
                    date_to: data.data.date_to,
                    total_orders: data.data.total_orders,
                    total_revenue: parseFloat(data.data.total_revenue),
                    total_discount: parseFloat(data.data.total_discount),
                    average_order_value: data.data.average_order_value,
                    total_items_sold: data.data.total_items_sold,
                    gross_sales: parseFloat(data.data.gross_sales),
                });
            } else {
                throw new Error(data.message || 'Gagal memuat data transaksi');
            }
        } catch (error) {
            console.error('Error:', error);
            document.getElementById("transactionTable").innerHTML = `
                <tr class="border-t border-gray-200">
                    <td colspan="8" class="text-center py-6 text-red-500">${error.message}</td>
                </tr>
            `;
            document.getElementById("summaryText").innerHTML = `
                <span class="font-semibold text-lg">Gagal memuat data</span><br>
                <span class="text-gray-500 text-sm">${error.message}</span>
            `;
        } finally {
            sedangMemuat = false;
        }
    }

    // Fungsi untuk memfilter transaksi berdasarkan pencarian
    function filterTransaksi() {
        if (sedangMemuat) return;
        perbaruiTampilanTransaksi();
    }

    // Fungsi utama untuk memperbarui tampilan tabel transaksi
    function perbaruiTampilanTransaksi() {
        const tabelBody = document.getElementById("transactionTable");
        const kataKunciPencarian = document.getElementById("searchInvoice").value.toLowerCase();
        
        // Filter transaksi berdasarkan kata kunci
        const transaksiTertampil = semuaTransaksi.filter(transaksi => {
            if (kataKunciPencarian && !transaksi.invoice.toLowerCase().includes(kataKunciPencarian)) {
                return false;
            }
            return true;
        });

        // Perbarui isi tabel
        tabelBody.innerHTML = transaksiTertampil.length
            ? transaksiTertampil.map((transaksi, index) => `
                <tr class="hover:bg-gray-50">
                    <td class="p-2 border">${index + 1}</td>
                    <td class="p-2 border font-mono">${transaksi.invoice}</td>
                    <td class="p-2 border">${formatWaktu(transaksi.waktu)}</td>
                    <td class="p-2 border">${transaksi.kasir}</td>
                    <td class="p-2 border">${transaksi.pembayaran}</td>
                    <td class="p-2 border">
                        <span class="px-2 py-1 rounded-full text-xs ${getClassStatus(transaksi.status)}">
                            ${transaksi.status}
                        </span>
                    </td>
                    <td class="p-2 border font-medium">Rp ${formatUang(transaksi.total)}</td>
                    <td class="p-2 border">
                        <button onclick="lihatDetail('${transaksi.invoice}')" class="text-blue-500 hover:text-blue-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                        <button onclick="cetakStruk('${transaksi.invoice}')" class="text-green-500 hover:text-green-700" title="Cetak Struk">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                        </button>
                    </td>
                </tr>
            `).join("")
            : `<tr><td colspan="8" class="text-center py-4 text-gray-500">Tidak ada transaksi yang sesuai.</td>`;
    }

    // Fungsi untuk mencetak struk
    async function cetakStruk(nomorInvoice) {
        try {
            const transaksi = semuaTransaksi.find(t => t.invoice === nomorInvoice);
            if (!transaksi) {
                throw new Error('Transaksi tidak ditemukan');
            }
            
            // Debug untuk memeriksa struktur data transaksi
            console.log('DEBUG TRANSAKSI:', transaksi);

            // Ambil token dari localStorage atau meta tag
            const token = localStorage.getItem('token') || document.querySelector('meta[name="csrf-token"]').content;

            // Periksa dan ambil outlet_id dengan lebih teliti
            // Coba dapatkan dari objek transaksi terlebih dahulu
            let outletId = null;
            
            if (transaksi.outlet && transaksi.outlet.id) {
                // Jika ada objek outlet dengan id
                outletId = transaksi.outlet.id;
            } else if (transaksi.outlet_id) {
                // Jika outlet_id tersedia langsung
                outletId = transaksi.outlet_id;
            } else {
                // Fallback ke localStorage jika tidak ada di transaksi
                outletId = localStorage.getItem('outlet_id');
            }

            console.log('DEBUG outletId yang digunakan:', outletId);

            if (!outletId) {
                throw new Error('Outlet ID tidak ditemukan. Pastikan Anda telah memilih outlet.');
            }

            // Ambil data template cetak dari endpoint
            const response = await fetch(`/api/print-template/${outletId}`, {
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

            const templateData = responseData.data;

            // Buka jendela cetak
            const printWindow = window.open('', '_blank', 'width=400,height=600');

            if (printWindow) {
                const receiptContent = generateReceiptContent(transaksi, templateData);
                
                printWindow.document.open();
                printWindow.document.write(receiptContent);
                printWindow.document.close();

                printWindow.onload = function() {
                    printWindow.print();
                    // printWindow.close(); // Opsional: tutup setelah cetak
                };
            } else {
                throw new Error('Gagal membuka jendela cetak. Periksa pengaturan popup browser Anda.');
            }
        } catch (error) {
            console.error('Error:', error);
            alert(`Gagal mencetak struk: ${error.message}`);
        }
    }

    function generateReceiptContent(transaction, templateData) {
        // Format tanggal dengan lebih baik
        const formatDate = (dateString) => {
            if (!dateString) return 'Tanggal tidak tersedia';
            try {
                // Normalisasi string tanggal
                let normalized = dateString.trim();
                
                // Hapus escape characters
                normalized = normalized.replace(/\\\//g, '/');
                
                // Cek apakah format DD/MM/YYYY HH:mm atau DD/MM/YYYY
                const datePattern = /^(\d{1,2})\/(\d{1,2})\/(\d{4})(?:\s+(\d{1,2}):(\d{1,2}))?/;
                const match = normalized.match(datePattern);
                
                if (match) {
                    const [, day, month, year, hour = '00', minute = '00'] = match;
                    
                    // Parse ke Date object dengan format ISO (YYYY-MM-DD)
                    // JavaScript Date menggunakan month 0-based, jadi kurangi 1
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
                // Ganti spasi dengan 'T' untuk format ISO
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
            name: templateData.company_name || 'Toko Saya',
            address: '',
            phone: '',
            tax: 0
        };

        // Use logo from template or default
        const logoPath = templateData.logo_url || '/images/logo.png';

        // Data transaksi yang aman
        const safeTransaction = {
            ...transaction,
            subtotal: safeNumber(transaction.subtotal),
            discount: safeNumber(transaction.discount),
            tax: safeNumber(transaction.tax),
            total: safeNumber(transaction.total),
            total_paid: safeNumber(transaction.total_paid || transaction.total),
            change: safeNumber(transaction.change || 0),
            items: transaction.items || [],
            pembayaran: transaction.pembayaran || 'cash',
            waktu: transaction.waktu || new Date().toISOString(),
            invoice: transaction.invoice || 'NO-INVOICE',
            kasir: transaction.kasir || 'Kasir'
        };
        // Pastikan properti-properti yang dibutuhkan ada, atau beri nilai default
        const subtotal = safeNumber(transaction.subtotal);
        const discount = safeNumber(transaction.discount);
        const tax = safeNumber(transaction.tax);
        const total = safeNumber(transaction.total);
        const total_paid = safeNumber(transaction.total_paid);
        const change = safeNumber(transaction.change);
        const logoUrl = templateData.logo_url || '/images/logo.png';


        console.log("DEBUG nilai-nilai transaksi:", {
            subtotal: safeTransaction.subtotal, 
            discount: safeTransaction.discount, 
            tax: safeTransaction.tax, 
            total: safeTransaction.total, 
            total_paid: safeTransaction.total_paid, 
            change: safeTransaction.change,
            items: safeTransaction.items
        });

        return `
            <!DOCTYPE html>
            <html>
            <head>
                <title>Struk Transaksi #${safeTransaction.invoice}</title>
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
                        width: 70px;  /* Sedikit lebih besar untuk thermal printer */
                        height: 70px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        padding: 3px;  /* Padding untuk mencegah clipping */
                        background-color: white;  /* Pastikan background putih */
                    }
                    
                    .logo {
                        max-width: 100%;
                        max-height: 100%;
                        object-fit: contain;
                        filter: grayscale(100%) contrast(200%);  /* Buat hitam putih dengan kontras tinggi */
                        -webkit-filter: grayscale(100%) contrast(200%);
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
                
                <div class="text-center">
                    <div class="info-row">STRUK PEMBAYARAN</div>
                </div>
                <div class="divider"></div>
                
                <!-- Info transaksi -->
                <div class="transaction-info">
                    <div class="info-row">
                        <span class="info-label">No. Invoice:</span>
                        <span>${safeTransaction.invoice}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Tanggal:</span>
                        <span>${formatDate(safeTransaction.waktu)}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Kasir:</span>
                        <span>${safeTransaction.kasir}</span>
                    </div>
                </div>
                
                <div class="divider"></div>
                
                <!-- Daftar item -->
                <div class="items-list">
                    ${safeTransaction.items.length > 0 
                        ? safeTransaction.items.map(item => {
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
                        <span>Rp ${formatCurrency(safeTransaction.subtotal)}</span>
                    </div>
                    
                    ${safeTransaction.discount > 0 ? `
                    <div class="total-row">
                        <span>Diskon:</span>
                        <span>- Rp ${formatCurrency(safeTransaction.discount)}</span>
                    </div>
                    ` : ''}
                    
                    ${safeTransaction.tax > 0 ? `
                    <div class="total-row">
                        <span>Pajak:</span>
                        <span>Rp ${formatCurrency(safeTransaction.tax)}</span>
                    </div>
                    ` : ''}
                    
                    <div class="total-row grand-total">
                        <span>TOTAL:</span>
                        <span>Rp ${formatCurrency(safeTransaction.total)}</span>
                    </div>
                </div>
                
                <!-- Info pembayaran -->
                <div class="payment-info">
                    <div class="total-row">
                        <span>Metode Bayar:</span>
                        <span>${safeTransaction.pembayaran === "cash" ? "TUNAI" : 
                            safeTransaction.pembayaran === "qris" ? "QRIS" : 
                            (safeTransaction.pembayaran || 'TIDAK DIKETAHUI').toUpperCase()}</span>
                    </div>
                    
                    ${safeTransaction.pembayaran === 'cash' ? `
                    <div class="total-row">
                        <span>Dibayar:</span>
                        <span>Rp ${formatCurrency(safeTransaction.total_paid)}</span>
                    </div>
                    <div class="total-row">
                        <span>Kembalian:</span>
                        <span>Rp ${formatCurrency(safeTransaction.change)}</span>
                    </div>
                    ` : ''}
                </div>
                
                ${safeTransaction.member ? `
                <div class="divider"></div>
                <div class="info-row">
                    <span class="info-label">Member:</span>
                    <span>${safeTransaction.member.name || ''} (${safeTransaction.member.member_code || ''})</span>
                </div>
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

    // Fungsi untuk memperbarui ringkasan
    function perbaruiRingkasan(r) {
        document.getElementById("totalAmount")
            .textContent = "Rp " + formatUang(r.total_revenue);
        
        let teksTanggal = "Semua Transaksi";
        if (r.date_from && r.date_to) {
            teksTanggal = `${r.date_from} – ${r.date_to}`;
        }

        document.getElementById("summaryText").innerHTML = `
            <span class="font-semibold text-lg">Total Transaksi ${teksTanggal}</span><br>
            <span class="text-gray-500 text-sm">${r.total_orders} transaksi</span>
        `;
    }

    function bukaModal(id) {
        document.getElementById(id).classList.remove('hidden');
    }
        
    function tutupModal(id) {
        document.getElementById(id).classList.add('hidden');
    }

    function lihatDetail(nomorInvoice) {
        const t = semuaTransaksi.find(x => x.invoice === nomorInvoice);
        if (!t) return alert('Transaksi tidak ditemukan');

        // Helper function yang lebih baik untuk handle number
        const safeNumber = (value) => {
            if (value === null || value === undefined) return 0;
            if (typeof value === 'number') return isNaN(value) ? 0 : value;
            if (typeof value === 'string') {
                // Handle string dengan % (persen)
                if (value.includes('%')) {
                    const num = parseFloat(value.replace('%', '').trim());
                    return isNaN(num) ? 0 : num;
                }
                // Handle string angka biasa
                const num = parseFloat(value.replace(/[^0-9.-]/g, ''));
                return isNaN(num) ? 0 : num;
            }
            return 0;
        };

        // Header
        document.getElementById('modalInvoice').textContent = t.invoice || t.order_number || '-';
        document.getElementById('modalDate').textContent = formatWaktu(t.waktu || t.created_at || '');
        
        // Status
        const statusEl = document.getElementById('modalStatus');
        statusEl.textContent = t.status || '-';
        statusEl.className = `inline-block px-3 py-1 rounded-full text-xs font-medium ${getClassStatus(t.status || '')}`;

        // Items
        const itemsEl = document.getElementById('modalItems');
        if (t.items && t.items.length > 0) {
            itemsEl.innerHTML = t.items.map(i => `
                <tr>
                    <td class="px-3 py-2">${i.product || '-'}</td>
                    <td class="px-3 py-2">${i.quantity || 0}x</td>
                    <td class="px-3 py-2">Rp ${formatUang(safeNumber(i.price))}</td>
                    <td class="px-3 py-2">Rp ${formatUang(safeNumber(i.price) * safeNumber(i.quantity))}</td>
                </tr>
            `).join('');
        } else {
            itemsEl.innerHTML = '<tr><td colspan="4" class="px-3 py-2 text-center text-gray-500">Tidak ada item</td></tr>';
        }

        // Hitung subtotal jika tidak ada
        const subtotal = t.subtotal !== undefined ? safeNumber(t.subtotal) : 
                        t.items?.reduce((sum, item) => sum + (safeNumber(item.price) * safeNumber(item.quantity)), 0) || 0;

        // Handle tax (persen atau nominal)
        let taxValue = 0;
        if (typeof t.tax === 'string' && t.tax.includes('%')) {
            // Jika tax dalam persen
            const taxPercent = safeNumber(t.tax); // sudah dihandle di safeNumber
            taxValue = subtotal * (taxPercent / 100);
        } else {
            // Jika tax nominal langsung
            taxValue = safeNumber(t.tax);
        }

        // Handle discount
        const discountValue = safeNumber(t.discount);

        // Hitung total jika tidak ada
        const total = t.total !== undefined ? safeNumber(t.total) : subtotal + taxValue - discountValue;

        // Tampilkan nilai
        document.getElementById('modalSubtotal').textContent = `Rp ${formatUang(subtotal)}`;
        document.getElementById('modalTax').textContent = `Rp ${formatUang(taxValue)}`;
        document.getElementById('modalDiscount').textContent = `Rp ${formatUang(discountValue)}`;
        document.getElementById('modalTotal').textContent = `Rp ${formatUang(total)}`;

        // Pembayaran
        const pembayaranEl = document.getElementById('modalPaymentMethod');
        if (pembayaranEl) {
            pembayaranEl.textContent = (t.pembayaran || t.payment_method || '-').toUpperCase();
        }
        
        // Total paid dan kembalian
        const totalPaidEl = document.getElementById('modalTotalPaid');
        const changeEl = document.getElementById('modalChange');
        if (totalPaidEl) totalPaidEl.textContent = `Rp ${formatUang(safeNumber(t.total_paid))}`;
        if (changeEl) changeEl.textContent = `Rp ${formatUang(safeNumber(t.change))}`;

        bukaModal('transactionModal');
    }
    
    // Format tanggal untuk backend (YYYY-MM-DD)
    function formatTanggalUntukBackend(tanggal) {
        const tahun = tanggal.getFullYear();
        const bulan = String(tanggal.getMonth() + 1).padStart(2, '0');
        const hari = String(tanggal.getDate()).padStart(2, '0');
        return `${tahun}-${bulan}-${hari}`;
    }

    // Format waktu tampilan (DD Bulan YYYY, HH:MM)
    function formatWaktu(waktuStr) {
        if (!waktuStr) return '';
        
        // Format dari backend: "d/m/Y H:i" (15/05/2025 14:30)
        const [tanggal, jam] = waktuStr.split(' ');
        const [hari, bulan, tahun] = tanggal.split('/');
        
        const namaBulan = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];
        
        return `${parseInt(hari)} ${namaBulan[parseInt(bulan) - 1]} ${tahun}, ${jam}`;
    }

    // Format uang (1.000.000)
    function formatUang(jumlah) {
        return jumlah ? jumlah.toLocaleString('id-ID') : '0';
    }

    // Dapatkan class CSS berdasarkan status
    function getClassStatus(status) {
        switch (status.toLowerCase()) {
            case 'selesai': return 'bg-green-100 text-green-800';
            case 'dibatalkan': return 'bg-red-100 text-red-800';
            case 'pending': return 'bg-yellow-100 text-yellow-800';
            default: return 'bg-gray-100 text-gray-800';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const hariIni = new Date();
        ambilDataTransaksi(hariIni, hariIni);
    });

    // Event listener untuk modal
    document.addEventListener('DOMContentLoaded', function() {
        // Pastikan CSRF token ada
        if (!document.querySelector('meta[name="csrf-token"]')) {
            const meta = document.createElement('meta');
            meta.name = 'csrf-token';
            meta.content = '{{ csrf_token() }}';
            document.head.appendChild(meta);
        }
    });
</script>