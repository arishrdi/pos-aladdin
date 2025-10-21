<!-- History Modal -->
<div id="historyModal" class="fixed inset-0 z-50 hidden">
    <!-- Overlay -->
    <div class="absolute w-full h-full bg-gray-900 opacity-50" onclick="tutupModal('historyModal')"></div>

    <!-- Modal Box -->
    <div
        class="bg-white w-[95%] md:w-11/12 md:max-w-6xl mx-auto rounded shadow-lg z-50 relative mt-10 mb-10 max-h-[90vh] flex flex-col">

        <!-- Header (Fixed inside modal) -->
        <div class="p-4 md:p-6 border-b sticky top-0 bg-white z-10">
            <div class="flex justify-between items-start md:items-center flex-col md:flex-row gap-2">
                <div>
                    <h2 class="text-xl md:text-2xl font-bold">Riwayat Transaksi</h2>
                    <p class="text-sm md:text-base text-gray-600">Lihat riwayat transaksi berdasarkan tanggal</p>
                </div>
                <button onclick="tutupModal('historyModal')"
                    class="text-gray-500 hover:text-red-500 text-xl md:text-2xl">✕</button>
            </div>
        </div>

        <!-- Body Scrollable -->
        <div class="overflow-y-auto px-4 md:px-8 py-6 flex-1">
            <!-- Date Range & Search -->
            <div class="flex flex-col md:flex-row justify-between gap-4 mb-6">
                <div class="relative w-full md:w-1/2">
                    <input id="dateRange" type="text" class="border p-3 rounded w-full pl-12 text-base"
                        placeholder="Pilih rentang tanggal" readonly />
                    <div class="absolute left-4 top-3.5 text-gray-400">
                        <!-- Calendar Icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>

                <div class="relative w-full md:w-1/2">
                    <input id="searchInvoice" type="text" placeholder="Cari transaksi berdasarkan nomor invoice..."
                        class="border p-3 rounded w-full pl-12 text-base" oninput="filterTransaksi()" />
                    <div class="absolute left-4 top-3.5 text-gray-400">
                        <!-- Search Icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Summary -->
            <div
                class="bg-green-50 p-4 rounded mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 sm:gap-0">
                <div id="summaryText">
                    <span class="font-semibold text-lg">Total Transaksi</span><br>
                    <span class="text-gray-500 text-sm">Memuat data...</span>
                </div>
                <div class="text-green-600 font-bold text-2xl" id="totalAmount">Rp 0</div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto border border-gray-200 rounded-md">
                <table class="min-w-max text-sm md:text-base text-left whitespace-nowrap">
                    <thead class="bg-gray-100 text-gray-700">
                        <tr>
                            <th class="p-3 min-w-[50px]">No</th>
                            <th class="p-3 min-w-[120px]">Invoice</th>
                            <th class="p-3 min-w-[140px]">Waktu</th>
                            <th class="p-3 min-w-[100px]">Kasir</th>
                            <th class="p-3 min-w-[120px]">Nama Member</th>
                            <th class="p-3 min-w-[80px]">Kategori</th>
                            <th class="p-3 min-w-[100px]">Pembayaran</th>
                            <th class="p-3 min-w-[100px]">Status</th>
                            <th class="p-3 min-w-[100px]">Total</th>
                            <th class="p-3 min-w-[100px]">Sisa Bayar</th>
                            <th class="p-3 min-w-[150px]">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="transactionTable">
                        <tr class="border-t border-gray-200">
                            <td colspan="11" class="text-center py-6 text-gray-500">Memuat data transaksi...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Footer (Fixed inside modal) -->
        <div class="border-t p-4 md:p-6 bg-white sticky bottom-0 z-10">
            <div class="flex justify-end">
                <button onclick="tutupModal('historyModal')"
                    class="px-5 py-3 text-base bg-green-500 text-white rounded hover:bg-green-600 transition">Tutup</button>
            </div>
        </div>

        @include('partials.pos.modal.modal-history-transaksi')

        <!-- Modal Refund Confirmation -->
        <div id="refundModal" class="fixed inset-0 z-50 hidden">
            <!-- Overlay -->
            <div class="absolute w-full h-full bg-gray-900 opacity-50" onclick="tutupModal('refundModal')"></div>

            <!-- Modal Box -->
            <div class="bg-white w-[90%] md:w-1/2 max-w-md mx-auto rounded shadow-lg z-60 relative mt-20">
                <!-- Header -->
                <div class="p-4 border-b">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-bold text-red-600">Konfirmasi Refund</h3>
                        <button onclick="tutupModal('refundModal')"
                            class="text-gray-500 hover:text-red-500 text-xl">✕</button>
                    </div>
                </div>

                <!-- Body -->
                <div class="p-6">
                    <div class="text-center mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-red-500 mx-auto mb-4" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                        <h4 id="refundModalTitle" class="text-lg font-semibold mb-2">Ajukan Permintaan Refund</h4>
                        <p class="text-gray-600 mb-4">
                            Permintaan untuk transaksi <span id="refundInvoice" class="font-mono font-bold"></span>
                            dengan total <span id="refundTotal" class="font-bold text-red-600"></span>
                        </p>
                    </div>

                    <!-- Alasan Refund -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Alasan Refund <span class="text-red-500">*</span>
                        </label>
                        <select id="refundReason"
                            class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                            <option value="">Pilih alasan...</option>
                            <option value="customer_request">Permintaan Pelanggan</option>
                            <option value="defective_product">Produk Rusak/Cacat</option>
                            <option value="wrong_item">Barang Salah</option>
                            <option value="system_error">Kesalahan Sistem</option>
                            <option value="other">Lainnya</option>
                        </select>
                    </div>

                    <!-- Catatan Tambahan -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Catatan Tambahan (Opsional)
                        </label>
                        <textarea id="refundNotes" rows="3"
                            class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                            placeholder="Masukkan catatan tambahan jika diperlukan..."></textarea>
                    </div>
                </div>

                <!-- Footer -->
                <div class="border-t p-4 bg-gray-50 rounded-b">
                    <div class="flex justify-end gap-3">
                        <button onclick="tutupModal('refundModal')"
                            class="px-4 py-2 text-gray-600 bg-gray-200 rounded-lg hover:bg-gray-300 transition">
                            Batal
                        </button>
                        <button onclick="prosesRefund()"
                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                            Ajukan Permintaan
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Pelunasan DP -->
        <div id="pelunasanModal" class="fixed inset-0 z-50 hidden">
            <!-- Overlay -->
            <div class="absolute w-full h-full bg-gray-900 opacity-50" onclick="tutupModal('pelunasanModal')"></div>

            <!-- Modal Box -->
            <div
                class="bg-white w-[90%] md:w-1/2 max-w-lg mx-auto rounded shadow-lg z-60 relative mt-10 mb-10 max-h-[90vh] flex flex-col">
                <!-- Header -->
                <div class="p-4 border-b flex-shrink-0">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-bold text-green-600">Pelunasan DP</h3>
                        <button onclick="tutupModal('pelunasanModal')"
                            class="text-gray-500 hover:text-red-500 text-xl">✕</button>
                    </div>
                </div>

                <!-- Body Scrollable -->
                <div class="p-6 overflow-y-auto flex-1">
                    <div class="mb-4">
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-600">Invoice:</span>
                                    <span id="pelunasanInvoice" class="font-mono font-bold block">-</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Total:</span>
                                    <span id="pelunasanTotal" class="font-bold text-green-600 block">Rp -</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Sudah Dibayar:</span>
                                    <span id="pelunasanPaid" class="font-bold block">Rp -</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Sisa Bayar:</span>
                                    <span id="pelunasanRemaining" class="font-bold text-red-600 block">Rp -</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Riwayat Pelunasan (jika ada) -->
                    <div id="riwayatPelunasanSection" class="mb-4 hidden">
                        <h4 class="text-sm font-medium text-gray-700 mb-3">Riwayat Pelunasan Sebelumnya</h4>
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-3 max-h-32 overflow-y-auto">
                            <div id="riwayatPelunasanList" class="space-y-2">
                                <!-- Riwayat akan dimuat di sini -->
                            </div>
                        </div>
                    </div>

                    <!-- Form Pelunasan -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Jumlah Pelunasan <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="jumlahPelunasan"
                            class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                            placeholder="Masukkan jumlah pelunasan...">
                        <input type="hidden" id="jumlahPelunasanRaw" name="amount_received">
                        <p class="text-xs text-gray-500 mt-1">Masukkan jumlah yang akan dibayar (maksimal sisa bayar)
                        </p>
                    </div>

                    <!-- Metode Pembayaran -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Metode Pembayaran <span class="text-red-500">*</span>
                        </label>
                        <select id="metodePembayaranPelunasan"
                            class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                            <option value="">Pilih metode pembayaran...</option>
                            <option value="cash">Tunai</option>
                            <option value="transfer">Transfer Bank</option>
                            <option value="qris">QRIS</option>
                        </select>
                    </div>

                    <!-- Upload Bukti Pembayaran (required) -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Bukti Pembayaran <span class="text-red-500">*</span>
                        </label>
                        <input type="file" id="buktiPembayaranPelunasan"
                            class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                            accept="image/*,.pdf" required>
                        <p class="text-xs text-gray-500 mt-1">Upload foto atau scan bukti pembayaran (jpg, png, pdf) -
                            maksimal 5MB</p>
                    </div>

                    <!-- Catatan -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Catatan (Opsional)
                        </label>
                        <textarea id="catatanPelunasan" rows="3"
                            class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                            placeholder="Catatan tambahan untuk pelunasan..."></textarea>
                    </div>
                </div>

                <!-- Footer -->
                <div class="border-t p-4 bg-gray-50 rounded-b flex-shrink-0">
                    <div class="flex justify-end gap-3">
                        <button onclick="tutupModal('pelunasanModal')"
                            class="px-4 py-2 text-gray-600 bg-gray-200 rounded-lg hover:bg-gray-300 transition">
                            Batal
                        </button>
                        <button onclick="prosesPelunasan()"
                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                            Proses Pelunasan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Transaksi -->
<div id="editTransactionModal" class="fixed inset-0 z-50 hidden">
    <!-- Overlay -->
    <div class="absolute w-full h-full bg-gray-900 opacity-50" onclick="tutupModal('editTransactionModal')"></div>

    <!-- Modal Box -->
    <div class="bg-white w-[95%] md:w-11/12 md:max-w-4xl mx-auto rounded shadow-lg z-50 relative mt-10 mb-10 max-h-[90vh] flex flex-col">
        <!-- Header -->
        <div class="p-4 md:p-6 border-b sticky top-0 bg-white z-10">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-xl font-bold text-blue-600">Edit Transaksi</h3>
                    <p class="text-sm text-gray-600">Invoice: <span id="editInvoiceNumber" class="font-mono font-bold"></span></p>
                </div>
                <button onclick="tutupModal('editTransactionModal')" class="text-gray-500 hover:text-red-500 text-xl">✕</button>
            </div>
        </div>

        <!-- Body -->
        <div class="p-6 overflow-y-auto flex-1">
            <!-- Current Items -->
            <div class="mb-6">
                <h4 class="text-lg font-semibold mb-3">Item Saat Ini</h4>
                <div class="overflow-x-auto border border-gray-200 rounded-md">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="p-3 text-left">Produk</th>
                                <th class="p-3 text-center">Qty</th>
                                <th class="p-3 text-center">Harga</th>
                                <th class="p-3 text-center">Diskon</th>
                                <th class="p-3 text-right">Subtotal</th>
                                <th class="p-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="editItemsList">
                            <!-- Items will be populated here -->
                        </tbody>
                    </table>
                </div>
                
                <!-- Add Item Button -->
                {{-- <button onclick="tambahItemBaru()" class="mt-3 px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 transition-colors">
                    <i class="fas fa-plus mr-2"></i>Tambah Item
                </button> --}}
            </div>

            <!-- Summary -->
            <div class="mb-6 bg-gray-50 p-4 rounded-lg">
                <h4 class="text-lg font-semibold mb-3">Ringkasan</h4>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-600">Subtotal Baru:</span>
                        <span id="editSubtotal" class="font-bold ml-2">Rp 0</span>
                    </div>
                    <div>
                        <span class="text-gray-600">Total Baru:</span>
                        <span id="editTotal" class="font-bold ml-2">Rp 0</span>
                    </div>
                    <div>
                        <span class="text-gray-600">Total Lama:</span>
                        <span id="editOriginalTotal" class="font-bold ml-2">Rp 0</span>
                    </div>
                    <div>
                        <span class="text-gray-600">Selisih:</span>
                        <span id="editDifference" class="font-bold ml-2">Rp 0</span>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <div class="space-y-4">
                <!-- Reason -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Alasan Edit <span class="text-red-500">*</span>
                    </label>
                    <select id="editReason" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="">Pilih alasan...</option>
                        <option value="quantity_adjustment">Penyesuaian Jumlah di Lokasi</option>
                        <option value="customer_request">Permintaan Tambahan Customer</option>
                        <option value="measurement_correction">Koreksi Pengukuran</option>
                        <option value="item_change">Perubahan Item</option>
                        <option value="other">Lainnya</option>
                    </select>
                </div>

                <!-- Notes -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Catatan Tambahan
                    </label>
                    <textarea id="editNotes" rows="3" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Jelaskan detail perubahan yang dilakukan..."></textarea>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="border-t p-4 bg-gray-50 rounded-b flex-shrink-0">
            <div class="flex justify-end gap-3">
                <button onclick="tutupModal('editTransactionModal')" class="px-4 py-2 text-gray-600 bg-gray-200 rounded-lg hover:bg-gray-300 transition">
                    Batal
                </button>
                <button onclick="submitTransactionEdit()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Ajukan Edit
                </button>
            </div>
        </div>
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
                    <td colspan="11" class="text-center py-6 text-gray-500">Sedang memuat data...</td>
                </tr>
            `;

            // Membuat URL endpoint dengan parameter
            let url = '/api/orders/history';
            const params = new URLSearchParams();

            let dateFrom, dateTo;

            if (tanggalMulai && tanggalSampai) {
                dateFrom = tanggalMulai;
                dateTo = tanggalSampai;
            } else {
                const { start, end } = getHariIni();
                dateFrom = start;
                dateTo = end;
            }

            params.append('date_from', formatTanggalUntukBackend(dateFrom));
            params.append('date_to', formatTanggalUntukBackend(dateTo));

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
            
            // Debug log untuk API response
            console.log('POS API Response:', data);
            
            // Jika sukses, proses data
            if (data.success) {
                // Konversi order_number menjadi invoice untuk konsistensi
                semuaTransaksi = data.data.orders.map(transaksi => {
                    // Debug log untuk transaksi dengan bonus
                    if (transaksi.bonus_items && transaksi.bonus_items.length > 0) {
                        console.log('Transaction with bonus:', transaksi.order_number, transaksi.bonus_items);
                    }

                    return {
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
                    transaction_category: transaksi.transaction_category,
                    // Tambahkan properti lain yang dibutuhkan
                    subtotal: parseFloat(transaksi.subtotal || 0),
                    tax: parseFloat(transaksi.tax || 0),
                    discount: parseFloat(transaksi.discount || 0),
                    total_paid: parseFloat(transaksi.total_paid || 0),
                    remaining_balance: parseFloat(transaksi.remaining_balance || 0),
                    change: parseFloat(transaksi.change || 0),
                    // Tambahkan status cancellation
                    cancellation_request: transaksi.cancellation_request || null,
                    has_pending_cancellation: transaksi.has_pending_cancellation || false,
                    // Tambahkan status edit
                    pending_edit: transaksi.pending_edit || false,
                    has_edit_history: transaksi.has_edit_history || false,
                    // Tambahkan bonus items
                    bonus_items: transaksi.bonus_items || [],
                    // Tambahkan carpet service information
                    service_type: transaksi.service_type || null,
                    installation_date: transaksi.installation_date || null,
                    installation_notes: transaksi.installation_notes || null,
                    // Tambahkan outlet collaboration information
                    leads_cabang_outlet: transaksi.leads_cabang_outlet || null,
                    deal_maker_outlet: transaksi.deal_maker_outlet || null,
                    // Tambahkan dual approval rejection data
                    is_finance_rejected: transaksi.is_finance_rejected || false,
                    finance_rejected_by: transaksi.finance_rejected_by || null,
                    finance_rejected_at: transaksi.finance_rejected_at || null,
                    finance_rejection_reason: transaksi.finance_rejection_reason || null,
                    is_operational_rejected: transaksi.is_operational_rejected || false,
                    operational_rejected_by: transaksi.operational_rejected_by || null,
                    operational_rejected_at: transaksi.operational_rejected_at || null,
                    operational_rejection_reason: transaksi.operational_rejection_reason || null
                    };
                });
                
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
                    <td colspan="11" class="text-center py-6 text-red-500">${error.message}</td>
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
                    <td class="p-2 border">${transaksi.member ? transaksi.member.name : '-'}</td>
                    <td class="p-2 border">${transaksi.transaction_category}</td>
                    <td class="p-2 border">${transaksi.pembayaran}</td>
                    <td class="p-2 border">
                        <div class="flex flex-col gap-1">
                            <span class="px-2 py-1 rounded-full text-xs ${getClassStatus(transaksi.status)}">
                                ${transaksi.status}
                            </span>
                            ${transaksi.pending_edit ? `
                                <span class="px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-800">
                                    Menunggu Edit
                                </span>
                            ` : ''}
                            ${transaksi.has_pending_cancellation ? `
                                <span class="px-2 py-1 rounded-full text-xs bg-orange-100 text-orange-800">
                                    Menunggu ${transaksi.status === 'Selesai' ? 'Refund' : 'Pembatalan'}
                                </span>
                            ` : ''}
                        </div>
                    </td>
                    <td class="p-2 border font-medium">Rp ${formatUang(transaksi.total)}</td>
                    <td class="p-2 border font-medium ${transaksi.remaining_balance > 0 ? 'text-red-600' : 'text-green-600'}">
                        ${transaksi.remaining_balance > 0 ? `Rp ${formatUang(transaksi.remaining_balance)}` : 'Lunas'}
                    </td>
                    <td class="p-2 border">
                        <div class="flex gap-2 justify-center">
                            <button onclick="lihatDetail('${transaksi.invoice}')" class="text-green-500 hover:text-green-700" title="Lihat Detail">
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
                            ${canEditTransaction(transaksi) ? `
                            <button onclick="bukaModalEditTransaksi('${transaksi.invoice}')" class="text-blue-500 hover:text-blue-700" title="Edit Transaksi">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </button>
                            ` : ''}
                            ${transaksi.pending_edit ? `
                                <div class="flex items-center gap-1 px-2 py-1 bg-blue-50 border border-blue-200 rounded text-xs text-blue-700" title="Edit sedang menunggu approval">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Edit Pending
                                </div>
                            ` : ''}
                            ${transaksi.has_pending_cancellation ? `
                                <div class="flex items-center gap-1 px-2 py-1 bg-orange-50 border border-orange-200 rounded text-xs text-orange-700" title="Sedang diproses oleh admin">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    ${transaksi.status === 'Selesai' ? 'Refund' : 'Pembatalan'} Diproses
                                </div>
                            ` : ''}
                            ${!transaksi.has_pending_cancellation && transaksi.status === 'Selesai' ? `
                            <button onclick="bukaModalRefund('${transaksi.invoice}')" class="text-red-500 hover:text-red-700" title="Ajukan Refund">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z" />
                                </svg>
                            </button>
                            ` : ''}
                            ${!transaksi.has_pending_cancellation && transaksi.status === 'pending' ? `
                            <button onclick="bukaModalRefund('${transaksi.invoice}')" class="text-orange-500 hover:text-orange-700" title="Ajukan Pembatalan">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </button>
                            ` : ''}
                            ${transaksi.transaction_category === 'dp' && transaksi.remaining_balance > 0 ? `
                            <button onclick="bukaModalPelunasan('${transaksi.invoice}')" class="px-3 py-1 bg-green-500 hover:bg-green-600 text-white text-xs rounded transition-colors" title="Lunasi DP">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                </svg>
                            </button>
                            ` : ''}
                        </div>
                    </td>
                </tr>
            `).join("")
            : `<tr><td colspan="11" class="text-center py-4 text-gray-500">Tidak ada transaksi yang sesuai.</td></tr>`;
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
        // Helper function to format date object to Indonesian format
        const formatDateObject = (date) => {
            const day = date.getDate().toString().padStart(2, '0');
            const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
                              'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            const month = monthNames[date.getMonth()];
            const year = date.getFullYear();
            const hour = date.getHours().toString().padStart(2, '0');
            const minute = date.getMinutes().toString().padStart(2, '0');
            
            return `${day} ${month} ${year}, ${hour}.${minute}`;
        };

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
                    
                    return formatDateObject(date);
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
                
                return formatDateObject(date);
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

        // console.log("Semua data: ", safeTransaction)

         const template2 =  `
        <!DOCTYPE html>
<html>
<head>
    <title>Struk Thermal 80mm</title>
    <meta charset="UTF-8">
    <style>
        /* Reset dan base styling untuk thermal printer 80mm */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Courier New', monospace;
            font-weight: normal;
            line-height: 1.2;
        }
        
        body {
            width: 80mm; /* Lebar maksimum untuk thermal 80mm */
            max-width: 80mm;
            font-size: 14px; /* Ukuran font lebih besar untuk 80mm */
            padding: 3mm;
            color: #000;
        }
        
        /* Header styling */
        .receipt-header {
            text-align: center;
            margin-bottom: 5px;
            padding-bottom: 5px;
            border-bottom: 1px dashed #000;
        }
        
        .logo-container {
            width: 60px;
            height: 60px;
            margin: 0 auto 5px auto;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .logo {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            filter: grayscale(100%) contrast(200%);
            -webkit-filter: grayscale(100%) contrast(200%);
        }
        
        .company-name {
            font-size: 16px;
            font-weight: normal;
            margin-bottom: 3px;
        }
        
        .company-info {
            font-size: 12px;
            line-height: 1.2;
        }
        
        /* Divider */
        .divider {
            border-top: 1px dashed #000;
            margin: 5px 0;
        }
        
        .divider-thin {
            border-top: 1px solid #000;
            margin: 3px 0;
        }
        
        /* Transaction info */
        .transaction-info {
            margin-bottom: 5px;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
        }
        
        .info-label {
            font-weight: normal;;
        }
        
        /* Items list */
        .items-list {
            margin: 5px 0;
        }
        
        .item-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 4px;
            font-size: 13px;
        }
        
        .item-name {
            flex: 2;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .item-price {
            flex: 1;
            text-align: right;
        }
        
        /* Totals */
        .totals {
            margin-top: 5px;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
        }
        
        .grand-total {
            font-weight: normal;
            font-size: 15px;
            margin-top: 6px;
            padding-top: 4px;
            border-top: 1px dashed #000;
        }
        
        /* Payment info */
        .payment-info {
            margin-top: 5px;
        }
        
        /* Footer */
        .receipt-footer {
            margin-top: 10px;
            text-align: center;
            font-size: 12px;
            line-height: 1.3;
        }
        
        /* Utilities */
        .text-center {
            text-align: center;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-bold {
            font-weight: normal;;
        }
        
        .text-small {
            font-size: 12px;
        }
        
        /* Menghindari page break di tempat tidak tepat */
        @media print {
            .avoid-break {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <!-- Header dengan logo -->
    <div class="receipt-header avoid-break">
        <div class="logo-container">
            <!-- <img src="${logoPath}" 
                alt="Logo Toko" 
                class="logo"
                onerror="this.style.display='none'"> -->
            <img class="logo" src="images/logo.png" >
        </div>
        <div class="company-name">${templateData.company_name || outletData.name || 'TOKO ANDA'}</div>
        <div class="company-info">
            ${templateData.company_slogan || ''}<br>
            ${outletData.address ? `${outletData.address}` : ''}<br>
            ${outletData.phone ? `Telp: ${outletData.phone}` : ''}
            ${outletData.email ? `Email: ${outletData.email}` : ''}<br>
            www.aladdinkarpet.com<br>
            www.gudangkarpetmasjid.com
        </div>
    </div>
    
    <div class="text-center">
        <div class="info-row text-bold">STRUK PEMBAYARAN</div>
    </div>
    <!-- <div class="divider-thin"></div> -->
     <div class="divider"></div>
    
    <!-- Info transaksi -->
    <div class="transaction-info avoid-break">
        <div class="info-row">
            <span class="info-label">Invoice:</span>
            <span>${safeTransaction.invoice}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Order:</span>
            <span>${safeTransaction.id}</span>
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
    
    <!-- <div class="divider"></div> -->
    <div class="divider"></div>
    
    
    <!-- Daftar item -->
    <div class="items-list avoid-break">
        ${safeTransaction.items.length > 0 
            ? safeTransaction.items.map(item => {
                const safeItem = {
                    ...item,
                    quantity: safeNumber(item.quantity),
                    price: safeNumber(item.price),
                    discount: safeNumber(item.discount),
                    product: item.product || 'Produk',
                    unit_type: item.unit_type || 'pcs'
                };
                
                // Potong nama produk jika terlalu panjang
                const productName = safeItem.product.length > 20 
                    ? safeItem.product.substring(0, 17) + '...' 
                    : safeItem.product;
                
                // Format quantity based on unit_type
                const quantityDisplay = formatQuantityForDisplay(safeItem.quantity, safeItem.unit_type);
                
                return `
                    <div class="item-row">
                        <div class="item-name">
                            ${quantityDisplay}${productName}
                        </div>
                        <div class="item-price">
                            Rp ${formatCurrency(safeItem.price * safeItem.quantity)}
                        </div>
                    </div>
                    ${safeItem.discount > 0 ? `
                    <div class="item-row text-small">
                        <div class="item-name">   Diskon</div>
                        <div class="item-price">-Rp ${formatCurrency(safeItem.discount)}</div>
                    </div>
                    ` : ''}
                `;
            }).join('')
            : '<div class="text-center">Tidak ada item</div>'
        }
    </div>
    
    <!-- Bonus items section -->
    ${safeTransaction.bonus_items && safeTransaction.bonus_items.length > 0 ? `
    <!-- <div class="divider"></div> -->
    <div class="divider"></div>
    
    <div class="items-list avoid-break">
        <div class="text-center text-bold" style="margin-bottom: 3px;">BONUS ITEMS</div>
        ${safeTransaction.bonus_items.map(bonusItem => {
            const safeBonusItem = {
                ...bonusItem,
                quantity: safeNumber(bonusItem.quantity),
                product: bonusItem.product || bonusItem.product_name || 'Bonus Item',
                unit_type: bonusItem.unit_type || 'pcs'
            };
            
            // Potong nama produk jika terlalu panjang
            const productName = safeBonusItem.product.length > 20 
                ? safeBonusItem.product.substring(0, 17) + '...' 
                : safeBonusItem.product;
            
            return `
                <div class="item-row">
                    <div class="item-name">
                        ${formatQuantityForDisplay(safeBonusItem.quantity, safeBonusItem.unit_type)}${productName}
                    </div>
                    <div class="item-price">
                        GRATIS
                    </div>
                </div>
            `;
        }).join('')}
    </div>
    ` : ''}
    
    <!-- <div class="divider"></div> -->
    <div class="divider"></div>
    
    <!-- Total pembelian -->
    <div class="totals avoid-break">
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
    <div class="payment-info avoid-break">
        <div class="total-row">
            <span>Metode Bayar:</span>
            <span>${safeTransaction.pembayaran === "cash" ? "TUNAI" : 
                safeTransaction.pembayaran === "qris" ? "QRIS" : 
                (safeTransaction.pembayaran || 'TIDAK DIKETAHUI').toUpperCase()}</span>
        </div>
        <div class="total-row">
            <span>Transaksi:</span>
            <span>${safeTransaction.transaction_category.toUpperCase()}</span>
        </div>
        
        ${safeTransaction.transaction_category.toLowerCase() === 'dp' ? `
        <div class="total-row">
            <span>Uang Muka (DP):</span>
            <span>Rp ${formatCurrency(safeTransaction.total_paid)}</span>
        </div>
        <div class="total-row">
            <span>Sisa Bayar:</span>
            <span>Rp ${formatCurrency(safeTransaction.remaining_balance || (safeTransaction.total - safeTransaction.total_paid))}</span>
        </div>
        ` : `
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
        `}
    </div>
    
    ${safeTransaction.member ? `
    <!-- <div class="divider"></div> -->
    <div class="divider"></div>
    <div class="avoid-break">
        <div class="info-row">
            <span class="info-label">Tuan:</span>
            <span class="text-bold">${safeTransaction.member.name || ''}</span>
        </div>
        ${safeTransaction.member.address ? `
        <div class="info-row">
            <span class="info-label">Alamat:</span>
            <span class="text-small">${safeTransaction.member.address.length > 30 ? safeTransaction.member.address.substring(0, 27) + '...' : safeTransaction.member.address}</span>
        </div>
        ` : ''}
        ${safeTransaction.member.phone ? `
        <div class="info-row">
            <span class="info-label">No. HP:</span>
            <span>${safeTransaction.member.phone}</span>
        </div>
        ` : ''}
    </div>
    ` : ''}

    ${safeTransaction.service_type ? `
    <!-- <div class="divider"></div> -->
    <div class="divider"></div>
    <div class="avoid-break">
        <div class="text-center text-bold" style="margin-bottom: 3px;">LAYANAN KARPET MASJID</div>
        <div class="info-row">
            <span class="info-label">Jenis Layanan:</span>
            <span>${safeTransaction.service_type === 'potong_obras_kirim' ? 'Potong, Obras & Kirim' : 'Pasang di Tempat'}</span>
        </div>
        ${safeTransaction.installation_date ? `
        <div class="info-row">
            <span class="info-label">Estimasi Pasang:</span>
            <span>${new Date(safeTransaction.installation_date).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })}</span>
        </div>
        ` : ''}
        ${safeTransaction.installation_notes ? `
        <div class="info-row" style="margin-top: 2px;">
            <span class="info-label">Catatan:</span>
        </div>
        <div class="text-small" style="margin-top: 1px; line-height: 1.1;">
            ${safeTransaction.installation_notes.length > 50 ? safeTransaction.installation_notes.substring(0, 47) + '...' : safeTransaction.installation_notes}
        </div>
        ` : ''}
    </div>
    ` : ''}
    
    <!-- Footer -->
    <!-- <div class="divider"></div> -->
    <div class="divider"></div>
    <div class="avoid-break">
        <div class="info-row">
            <span class="info-label">Tanda Terima,</span>
            <span class="info-label">Hormat kami,</span>
        </div>
        <br>
        <br>
        <div class="info-row" style="margin-top: 20px;">
            <span>(............)</span>
            <span>(............)</span>
        </div>
    </div>
    <div class="receipt-footer">
        ${templateData.footer_message || 'Terima kasih telah berbelanja'}<br>
        Barang yang sudah dibeli tidak dapat ditukar<br>
        ${new Date().getFullYear()} © ${templateData.company_name || outletData.name || 'TOKO ANDA'}
    </div>
</body>
</html>
        `
        
        
        return template2;

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
        // console.log("Lihat detail transaksi:", t);
        if (!t) return alert('Transaksi tidak ditemukan');
        
        // Debug log untuk melihat data transaksi
        console.log('Transaction data:', t);
        console.log('Bonus items:', t.bonus_items);

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
        
        // Cancellation status
        const cancellationStatusEl = document.getElementById('modalCancellationStatus');
        const cancellationTextEl = document.getElementById('modalCancellationText');
        
        if (t.has_pending_cancellation && cancellationStatusEl && cancellationTextEl) {
            const requestType = t.status === 'Selesai' ? 'Refund' : 'Pembatalan';
            cancellationTextEl.textContent = `Menunggu ${requestType}`;
            cancellationStatusEl.classList.remove('hidden');
        } else if (cancellationStatusEl) {
            cancellationStatusEl.classList.add('hidden');
        }

        // Items
        const itemsEl = document.getElementById('modalItems');
        if (t.items && t.items.length > 0) {
            itemsEl.innerHTML = t.items.map(i => {
                // Format quantity based on unit_type
                const quantityDisplay = formatQuantityForDisplay(i.quantity || 0, i.unit_type);
                
                return `
                    <tr>
                        <td class="px-3 py-2">${i.product || '-'}</td>
                        <td class="px-3 py-2">${quantityDisplay}</td>
                        <td class="px-3 py-2">Rp ${formatUang(safeNumber(i.price))}</td>
                        <td class="px-3 py-2">Rp ${formatUang(safeNumber(i.price) * safeNumber(i.quantity))}</td>
                    </tr>
                `;
            }).join('');
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

        // Bonus Items
        const bonusSection = document.getElementById('modalBonusSection');
        const bonusItemsEl = document.getElementById('modalBonusItems');
        
        if (t.bonus_items && t.bonus_items.length > 0) {
            bonusItemsEl.innerHTML = t.bonus_items.map(bonusItem => `
                <div class="flex justify-between items-center py-2 border-b border-green-200 last:border-b-0">
                    <div>
                        <p class="font-medium text-green-700">
                            <i class="fas fa-gift mr-1"></i>
                            ${bonusItem.product || bonusItem.product_name || '-'}
                        </p>
                        <p class="text-sm text-green-600">${formatQuantityWithUnit(bonusItem.quantity || 0, bonusItem.unit_type || 'tanpa satuan')} bonus</p>
                    </div>
                    <div class="text-sm text-green-600 font-medium">
                        GRATIS
                    </div>
                </div>
            `).join('');
            bonusSection.classList.remove('hidden');
        } else {
            bonusSection.classList.add('hidden');
        }

        // Carpet Service Information
        const carpetServiceSection = document.getElementById('modalCarpetServiceSection');
        const serviceTypeInfo = document.getElementById('modalServiceTypeInfo');
        const installationDateInfo = document.getElementById('modalInstallationDateInfo');
        const installationNotesInfo = document.getElementById('modalInstallationNotesInfo');

        let showCarpetServiceSection = false;

        // Show service type if available
        if (t.service_type) {
            const serviceTypeText = t.service_type === 'potong_obras_kirim' ? 'Potong, Obras & Kirim' : 'Pasang di Tempat';
            document.getElementById('modalServiceType').textContent = serviceTypeText;
            serviceTypeInfo.classList.remove('hidden');
            showCarpetServiceSection = true;
        } else {
            serviceTypeInfo.classList.add('hidden');
        }

        // Show installation date if available
        if (t.installation_date) {
            const installationDate = new Date(t.installation_date);
            const formattedDate = installationDate.toLocaleDateString('id-ID', {
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            });
            document.getElementById('modalInstallationDate').textContent = formattedDate;
            installationDateInfo.classList.remove('hidden');
            showCarpetServiceSection = true;
        } else {
            installationDateInfo.classList.add('hidden');
        }

        // Show installation notes if available
        if (t.installation_notes) {
            document.getElementById('modalInstallationNotes').textContent = t.installation_notes;
            installationNotesInfo.classList.remove('hidden');
            showCarpetServiceSection = true;
        } else {
            installationNotesInfo.classList.add('hidden');
        }

        // Handle leads cabang and deal maker information
        const leadsCabangInfo = document.getElementById('modalLeadsCabangInfo');
        const dealMakerInfo = document.getElementById('modalDealMakerInfo');

        // Show leads cabang if available
        if (t.leads_cabang_outlet && t.leads_cabang_outlet.name) {
            document.getElementById('modalLeadsCabang').textContent = t.leads_cabang_outlet.name;
            leadsCabangInfo.classList.remove('hidden');
            showCarpetServiceSection = true;
        } else {
            leadsCabangInfo.classList.add('hidden');
        }

        // Show deal maker if available
        if (t.deal_maker_outlet && t.deal_maker_outlet.name) {
            document.getElementById('modalDealMaker').textContent = 'BC-' + t.deal_maker_outlet.name;
            dealMakerInfo.classList.remove('hidden');
            showCarpetServiceSection = true;
        } else {
            dealMakerInfo.classList.add('hidden');
        }

        // Show/hide carpet service section
        if (showCarpetServiceSection) {
            carpetServiceSection.classList.remove('hidden');
        } else {
            carpetServiceSection.classList.add('hidden');
        }

        // Handle Finance Rejection Information
        const financeRejectionSection = document.getElementById('modalFinanceRejectionSection');
        if (t.is_finance_rejected && t.finance_rejected_by && t.finance_rejected_at) {
            document.getElementById('modalFinanceRejectedBy').textContent = t.finance_rejected_by || '-';
            document.getElementById('modalFinanceRejectedAt').textContent = formatWaktu(t.finance_rejected_at) || '-';
            document.getElementById('modalFinanceRejectionReason').textContent = t.finance_rejection_reason || 'Tidak ada alasan yang diberikan';
            financeRejectionSection.classList.remove('hidden');
        } else {
            financeRejectionSection.classList.add('hidden');
        }

        // Handle Operational Rejection Information
        const operationalRejectionSection = document.getElementById('modalOperationalRejectionSection');
        if (t.is_operational_rejected && t.operational_rejected_by && t.operational_rejected_at) {
            document.getElementById('modalOperationalRejectedBy').textContent = t.operational_rejected_by || '-';
            document.getElementById('modalOperationalRejectedAt').textContent = formatWaktu(t.operational_rejected_at) || '-';
            document.getElementById('modalOperationalRejectionReason').textContent = t.operational_rejection_reason || 'Tidak ada alasan yang diberikan';
            operationalRejectionSection.classList.remove('hidden');
        } else {
            operationalRejectionSection.classList.add('hidden');
        }

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

    // Format quantity with unit type
    function formatQuantityWithUnit(qty, unitType) {
        // Convert to number first
        const numQty = parseFloat(qty);
        if (isNaN(numQty) || numQty === 0) return '0 pcs';
        
        const formattedQty = numQty % 1 === 0 ? numQty.toString() : numQty.toFixed(1);
        const unit = unitType || 'pcs';
        
        return `${formattedQty} ${unit}`;
    }

    // Format quantity for display (with multiplier if needed)
    function formatQuantityForDisplay(qty, unitType) {
        // Convert to number first
        const numQty = parseFloat(qty);
        if (isNaN(numQty) || numQty === 0) return '';
        
        const hideQuantity = ['pasang', 'kirim'].includes(unitType);
        if (hideQuantity) return '';
        
        const formattedQty = numQty % 1 === 0 ? numQty.toString() : numQty.toFixed(1);
        return `${formattedQty}x `;
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

    // Variabel global untuk menyimpan data transaksi yang akan di-refund
    let transaksiRefund = null;

    // Fungsi untuk membuka modal refund
    function bukaModalRefund(nomorInvoice) {
        const transaksi = semuaTransaksi.find(t => t.invoice === nomorInvoice);
        if (!transaksi) {
            alert('Transaksi tidak ditemukan');
            return;
        }

        // Cek apakah sudah ada permintaan cancellation yang sedang diproses
        if (transaksi.has_pending_cancellation) {
            const requestType = transaksi.status === 'Selesai' ? 'refund' : 'pembatalan';
            alert(`Permintaan ${requestType} untuk transaksi ini sedang diproses oleh admin. Silakan tunggu hingga proses selesai.`);
            return;
        }

        // Cek apakah transaksi dapat diminta pembatalan/refund
        if (!['Selesai', 'pending'].includes(transaksi.status)) {
            alert('Hanya transaksi dengan status "Selesai" atau "Pending" yang dapat diminta pembatalan/refund');
            return;
        }

        // Simpan data transaksi untuk diproses
        transaksiRefund = transaksi;

        // Determine request type
        const requestType = transaksi.status === 'pending' ? 'Pembatalan' : 'Refund';
        
        // Update modal title
        document.getElementById('refundModalTitle').textContent = `Ajukan Permintaan ${requestType}`;

        // Isi data ke modal
        document.getElementById('refundInvoice').textContent = transaksi.invoice;
        document.getElementById('refundTotal').textContent = `Rp ${formatUang(transaksi.total)}`;
        
        // Reset form
        document.getElementById('refundReason').value = '';
        document.getElementById('refundNotes').value = '';

        // Buka modal
        bukaModal('refundModal');
    }

    // Fungsi untuk memproses refund
    async function prosesRefund() {
        if (!transaksiRefund) {
            alert('Data transaksi tidak ditemukan');
            return;
        }

        // Validasi input
        const reason = document.getElementById('refundReason').value;
        const notes = document.getElementById('refundNotes').value;

        if (!reason) {
            alert('Silakan pilih alasan');
            document.getElementById('refundReason').focus();
            return;
        }

        // Determine request type
        const requestType = transaksiRefund.status === 'pending' ? 'pembatalan' : 'refund';

        // Konfirmasi final
        const confirmMessage = `Anda akan mengajukan permintaan ${requestType} untuk:\\n\\n` +
                               `Invoice: ${transaksiRefund.invoice}\\n` +
                               `Total: Rp ${formatUang(transaksiRefund.total)}\\n\\n` +
                               `Apakah Anda yakin ingin melanjutkan?`;
        
        if (!confirm(confirmMessage)) {
            return;
        }

        try {
            // Disable tombol untuk mencegah double click
            const refundButton = document.querySelector('#refundModal button[onclick="prosesRefund()"]');
            const originalText = refundButton.textContent;
            refundButton.disabled = true;
            refundButton.textContent = 'Memproses...';

            // Ambil token dari localStorage atau meta tag
            const token = localStorage.getItem('token') || document.querySelector('meta[name="csrf-token"]').content;

            // Siapkan data permintaan
            const requestData = {
                reason: reason,
                notes: notes || null
            };

            // Kirim request ke backend
            const response = await fetch(`/api/orders/cancellation/request/${transaksiRefund.id}`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Authorization': `Bearer ${token}`,
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify(requestData)
            });

            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.message || `HTTP Error: ${response.status}`);
            }

            if (result.success) {
                // Tutup modal
                tutupModal('refundModal');
                
                // Tampilkan pesan sukses
                const requestType = transaksiRefund.status === 'pending' ? 'pembatalan' : 'refund';
                alert(`Permintaan ${requestType} berhasil diajukan!\\n\\nInvoice: ${transaksiRefund.invoice}\\nJumlah: Rp ${formatUang(transaksiRefund.total)}\\n\\nPermintaan akan diproses oleh admin.`);
                
                // Refresh data transaksi
                const hariIni = new Date();
                await ambilDataTransaksi(hariIni, hariIni);
                
                // Reset data transaksi
                transaksiRefund = null;
            } else {
                throw new Error(result.message || 'Gagal mengajukan permintaan');
            }

        } catch (error) {
            console.error('Error processing request:', error);
            alert(`Gagal mengajukan permintaan: ${error.message}`);
        } finally {
            // Restore tombol
            const refundButton = document.querySelector('#refundModal button[onclick="prosesRefund()"]');
            if (refundButton) {
                refundButton.disabled = false;
                refundButton.textContent = 'Ajukan Permintaan';
            }
        }
    }

    // Variabel global untuk menyimpan data transaksi DP yang akan dilunasi
    let transaksiPelunasan = null;

    // Fungsi untuk membuka modal pelunasan DP
    async function bukaModalPelunasan(nomorInvoice) {
        const transaksi = semuaTransaksi.find(t => t.invoice === nomorInvoice);
        if (!transaksi) {
            alert('Transaksi tidak ditemukan');
            return;
        }

        // Validasi apakah transaksi DP dan masih ada sisa bayar
        if (transaksi.transaction_category !== 'dp') {
            alert('Hanya transaksi DP yang dapat dilunasi');
            return;
        }

        if (transaksi.remaining_balance <= 0) {
            alert('Transaksi DP ini sudah lunas');
            return;
        }

        // Simpan data transaksi untuk diproses
        transaksiPelunasan = transaksi;

        // Isi data ke modal
        document.getElementById('pelunasanInvoice').textContent = transaksi.invoice;
        document.getElementById('pelunasanTotal').textContent = `Rp ${formatUang(transaksi.total)}`;
        document.getElementById('pelunasanPaid').textContent = `Rp ${formatUang(transaksi.total_paid)}`;
        document.getElementById('pelunasanRemaining').textContent = `Rp ${formatUang(transaksi.remaining_balance)}`;
        
        // Set nilai default jumlah pelunasan ke sisa bayar dengan format
        const formattedRemaining = transaksi.remaining_balance.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        document.getElementById('jumlahPelunasan').value = formattedRemaining;
        document.getElementById('jumlahPelunasanRaw').value = transaksi.remaining_balance;
        
        // Reset form
        document.getElementById('metodePembayaranPelunasan').value = '';
        document.getElementById('buktiPembayaranPelunasan').value = '';
        document.getElementById('catatanPelunasan').value = '';

        // Muat riwayat pelunasan
        await muatRiwayatPelunasan(transaksi.id);

        // Buka modal
        bukaModal('pelunasanModal');
    }

    // Fungsi untuk memuat riwayat pelunasan DP
    async function muatRiwayatPelunasan(orderId) {
        try {
            const token = localStorage.getItem('token') || document.querySelector('meta[name="csrf-token"]').content;
            
            const response = await fetch(`/api/orders/${orderId}/settlement-history`, {
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Authorization': `Bearer ${token}`
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP Error: ${response.status}`);
            }

            const result = await response.json();
            
            if (result.success && result.data.settlement_history.length > 0) {
                tampilkanRiwayatPelunasan(result.data.settlement_history);
            } else {
                // Sembunyikan section riwayat jika tidak ada data
                document.getElementById('riwayatPelunasanSection').classList.add('hidden');
            }
        } catch (error) {
            console.error('Error loading settlement history:', error);
            // Sembunyikan section riwayat jika error
            document.getElementById('riwayatPelunasanSection').classList.add('hidden');
        }
    }

    // Fungsi untuk menampilkan riwayat pelunasan
    function tampilkanRiwayatPelunasan(riwayat) {
        const listElement = document.getElementById('riwayatPelunasanList');
        const sectionElement = document.getElementById('riwayatPelunasanSection');
        
        if (riwayat.length === 0) {
            sectionElement.classList.add('hidden');
            return;
        }

        listElement.innerHTML = riwayat.map(item => `
            <div class="flex justify-between items-center py-2 px-3 bg-white border border-gray-200 rounded text-xs">
                <div class="flex-1">
                    <div class="font-medium text-gray-800">
                        ${item.processed_at} - Rp ${formatUang(item.amount)}
                    </div>
                    <div class="text-gray-600">
                        ${item.payment_method.toUpperCase()} • ${item.processed_by}
                        ${item.is_final_payment ? ' • <span class="text-green-600 font-medium">LUNAS</span>' : ''}
                    </div>
                    ${item.notes ? `<div class="text-gray-500 mt-1">${item.notes}</div>` : ''}
                </div>
                ${item.payment_proof_url ? `
                <a href="${item.payment_proof_url}" target="_blank" class="ml-2 text-blue-500 hover:text-blue-700" title="Lihat Bukti Pembayaran">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </a>
                ` : ''}
            </div>
        `).join('');

        sectionElement.classList.remove('hidden');
    }

    // Fungsi untuk memproses pelunasan DP
    async function prosesPelunasan() {
        if (!transaksiPelunasan) {
            alert('Data transaksi tidak ditemukan');
            return;
        }

        // Validasi input
        const jumlahPelunasan = parseFloat(document.getElementById('jumlahPelunasan').value);
        const metodePembayaran = document.getElementById('metodePembayaranPelunasan').value;
        const buktiPembayaran = document.getElementById('buktiPembayaranPelunasan').files[0];
        const catatan = document.getElementById('catatanPelunasan').value;

        // Validasi jumlah pelunasan
        const rawAmount = document.getElementById('jumlahPelunasanRaw').value;
        const validatedAmount = rawAmount ? parseFloat(rawAmount) : parseFloat(document.getElementById('jumlahPelunasan').value.replace(/[^\d]/g, ''));
        
        if (!validatedAmount || validatedAmount <= 0) {
            alert('Silakan masukkan jumlah pelunasan yang valid');
            document.getElementById('jumlahPelunasan').focus();
            return;
        }

        if (validatedAmount > transaksiPelunasan.remaining_balance) {
            alert(`Jumlah pelunasan tidak boleh melebihi sisa bayar (Rp ${formatUang(transaksiPelunasan.remaining_balance)})`);
            document.getElementById('jumlahPelunasan').focus();
            return;
        }

        // Validasi metode pembayaran
        if (!metodePembayaran) {
            alert('Silakan pilih metode pembayaran');
            document.getElementById('metodePembayaranPelunasan').focus();
            return;
        }

        // Validasi bukti pembayaran (required)
        if (!buktiPembayaran) {
            alert('Silakan upload bukti pembayaran');
            document.getElementById('buktiPembayaranPelunasan').focus();
            return;
        }

        // Konfirmasi pelunasan
        const confirmMessage = `Anda akan melakukan pelunasan DP:\\n\\n` +
                               `Invoice: ${transaksiPelunasan.invoice}\\n` +
                               `Jumlah Pelunasan: Rp ${formatUang(validatedAmount)}\\n` +
                               `Metode Pembayaran: ${metodePembayaran.toUpperCase()}\\n` +
                               `Sisa setelah pelunasan: Rp ${formatUang(transaksiPelunasan.remaining_balance - validatedAmount)}\\n\\n` +
                               `Apakah Anda yakin ingin melanjutkan?`;
        
        if (!confirm(confirmMessage)) {
            return;
        }

        try {
            // Disable tombol untuk mencegah double click
            const pelunasanButton = document.querySelector('#pelunasanModal button[onclick="prosesPelunasan()"]');
            const originalText = pelunasanButton.textContent;
            pelunasanButton.disabled = true;
            pelunasanButton.textContent = 'Memproses...';

            // Ambil token dari localStorage
            const token = localStorage.getItem('token');
            if (!token) {
                throw new Error('Token tidak ditemukan. Silakan login ulang.');
            }

            // Siapkan FormData untuk upload file
            const formData = new FormData();
            formData.append('amount_received', validatedAmount);
            formData.append('payment_method', metodePembayaran);
            formData.append('payment_proof', buktiPembayaran);
            if (catatan) {
                formData.append('notes', catatan);
            }

            // Kirim request ke backend
            const response = await fetch(`/api/orders/${transaksiPelunasan.id}/settle`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });

            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.message || `HTTP Error: ${response.status}`);
            }

            if (result.success) {
                // Tutup modal
                tutupModal('pelunasanModal');
                
                // Tampilkan pesan sukses
                const sisaBayar = transaksiPelunasan.remaining_balance - validatedAmount;
                const statusLunas = sisaBayar <= 0 ? 'LUNAS' : `Sisa: Rp ${formatUang(sisaBayar)}`;
                
                alert(`Pelunasan DP berhasil!\\n\\n` +
                      `Invoice: ${transaksiPelunasan.invoice}\\n` +
                      `Jumlah Dibayar: Rp ${formatUang(validatedAmount)}\\n` +
                      `Status: ${statusLunas}\\n\\n` +
                      `Terima kasih!`);
                
                // Refresh data transaksi
                const hariIni = new Date();
                await ambilDataTransaksi(hariIni, hariIni);
                
                // Reset data transaksi
                transaksiPelunasan = null;
            } else {
                throw new Error(result.message || 'Gagal memproses pelunasan');
            }

        } catch (error) {
            console.error('Error processing settlement:', error);
            alert(`Gagal memproses pelunasan: ${error.message}`);
        } finally {
            // Restore tombol
            const pelunasanButton = document.querySelector('#pelunasanModal button[onclick="prosesPelunasan()"]');
            if (pelunasanButton) {
                pelunasanButton.disabled = false;
                pelunasanButton.textContent = 'Proses Pelunasan';
            }
        }
    }

    // Setup currency formatting for Jumlah Pelunasan field
    document.addEventListener('DOMContentLoaded', function() {
        const jumlahPelunasanInput = document.getElementById('jumlahPelunasan');
        if (jumlahPelunasanInput) {
            // Format on input
            jumlahPelunasanInput.addEventListener('input', function() {
                const rawValue = this.value.replace(/[^\d]/g, '');
                
                // Update hidden field with raw value
                const hiddenInput = document.getElementById('jumlahPelunasanRaw');
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
            jumlahPelunasanInput.addEventListener('paste', function() {
                setTimeout(() => {
                    const rawValue = this.value.replace(/[^\d]/g, '');
                    
                    const hiddenInput = document.getElementById('jumlahPelunasanRaw');
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
            jumlahPelunasanInput.addEventListener('focus', function() {
                this.select();
            });
            
            // Re-format on blur
            jumlahPelunasanInput.addEventListener('blur', function() {
                const rawValue = this.value.replace(/[^\d]/g, '');
                
                const hiddenInput = document.getElementById('jumlahPelunasanRaw');
                if (hiddenInput) {
                    hiddenInput.value = rawValue;
                }
                
                if (rawValue) {
                    const formatted = rawValue.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                    this.value = formatted;
                } else {
                    this.value = '';
                }
            });
        }
    });

    // ========== TRANSACTION EDIT FUNCTIONS ==========
    // Functions are loaded from /js/pos/transaction-edit.js
</script>