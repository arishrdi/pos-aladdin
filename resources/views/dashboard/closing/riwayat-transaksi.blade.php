@extends('layouts.app')

@section('title', 'Riwayat Transaksi')

@section('content')

<!-- Alert Container -->
<div id="alertContainer" class="fixed top-4 right-4 z-50 space-y-3 w-80"></div>

<!-- title Page -->
<div class="mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <h1 class="text-3xl font-bold text-gray-800">Riwayat Transaksi</h1>
    </div>
</div>

<!-- Card: Outlet Info -->
<div class="bg-white rounded-md p-4 shadow-md mb-4 flex flex-col md:flex-row md:items-center md:justify-between">
    <div class="mb-3 md:mb-0 flex items-start gap-2">
        <i data-lucide="store" class="w-5 h-5 text-gray-600"></i>
        <div>
            <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">Menampilkan riwayat transaksi: <span class="outlet-name">Loading...</span></h2>
            <p class="text-sm text-gray-600">Data riwayat transaksi kas untuk <span class=" outlet-name"></span></p>
        </div>
    </div>
</div>

<!-- Table Riwayat Transaksi -->
<div class="bg-white rounded-lg shadow p-4">
    <!-- Header & Filter Row -->
    <div class="flex flex-col mb-4">
        <!-- Title and Date Filter Row -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-3">
            <h3 class="text-2xl font-bold text-gray-800">Riwayat Transaksi</h3>
            
            <div class="relative mt-2 sm:mt-0">
                <input id="transDateInput" type="text"
                    class="w-full sm:w-56 pl-10 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                    placeholder="Pilih Tanggal" />
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <i data-lucide="calendar" class="w-4 h-4 text-gray-500"></i>
                </span>
            </div>
        </div>
        
        <!-- Search Bar -->
        <div class="w-full sm:w-72 relative">
            <input type="text" id="searchInvoice"
                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                placeholder="Cari Invoice..." />
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                <i data-lucide="search" class="w-4 h-4"></i>
            </span>
        </div>
    </div>
    
    <!-- Table Content -->
    <div class="overflow-x-auto">
        <table class="w-full text-base">
            <thead class="text-left text-gray-700 border-b-2">
                <tr>
                    <th class="py-3 font-bold">Invoice</th>
                    <th class="py-3 font-bold">Waktu</th>
                    <th class="py-3 font-bold">Kasir</th>
                    <th class="py-3 font-bold">Pembayaran</th>
                    <th class="py-3 font-bold">Status</th>
                    <th class="py-3 font-bold">Total</th>
                    <th class="py-3 font-bold text-left">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 divide-y">
                <!-- Data akan diisi secara dinamis -->
                <tr>
                    <td colspan="7" class="py-8 text-center">
                        <div class="flex flex-col items-center justify-center gap-2 mx-auto">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                                 class="animate-spin text-green-500">
                                <path d="M21 12a9 9 0 1 1-6.219-8.56"/>
                            </svg>
                            <span class="text-gray-500">Memuat data transaksi...</span>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Detail Transaksi -->
<div id="modalDetail" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold">Detail Transaksi</h3>
                <button onclick="closeDetailModal()" class="text-gray-500 hover:text-gray-700">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <p class="text-gray-500">No. Invoice</p>
                    <p id="detailInvoice" class="font-medium"></p>
                </div>
                <div>
                    <p class="text-gray-500">Tanggal/Waktu</p>
                    <p id="detailDateTime" class="font-medium"></p>
                </div>
                <div>
                    <p class="text-gray-500">Metode Pembayaran</p>
                    <p id="detailPaymentMethod" class="font-medium"></p>
                </div>
                <div>
                    <p class="text-gray-500">Status</p>
                    <p id="detailStatus" class="font-medium"></p>
                </div>
            </div>
            
            <div class="mb-4">
                <h4 class="font-medium mb-2">Item Pembelian</h4>
                <div id="detailItems"></div>
            </div>
            
            <div class="border-t pt-4 space-y-2">
                <div class="flex justify-between">
                    <span>Subtotal</span>
                    <span id="detailSubtotal" class="font-medium"></span>
                </div>
                <div class="flex justify-between">
                    <span>Pajak</span>
                    <span id="detailTax" class="font-medium"></span>
                </div>
                <div class="flex justify-between">
                    <span>Diskon</span>
                    <span id="detailDiscount" class="font-medium"></span>
                </div>
                <div class="flex justify-between">
                    <span>Total Dibayar</span>
                    <span id="detailTotalPaid" class="font-medium"></span>
                </div>
                <div class="flex justify-between">
                    <span>Kembalian</span>
                    <span id="detailChange" class="font-medium"></span>
                </div>
                <div class="flex justify-between border-t pt-2 font-bold text-lg">
                    <span>Total</span>
                    <span id="detailTotal" class="text-green-600"></span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Refund -->
<div id="modalRefund" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl p-6 w-96">
        <div class="flex items-start gap-4">
            <div class="flex-shrink-0 p-2 bg-red-100 rounded-full">
                <i data-lucide="alert-triangle" class="w-6 h-6 text-red-600"></i>
            </div>
            <div class="flex-1">
                <h3 id="modalRefundTitle" class="text-lg font-semibold text-gray-900">Konfirmasi Refund</h3>
                <div class="mt-2">
                    <p class="text-sm text-gray-600">Anda yakin ingin melakukan refund untuk transaksi ini?</p>
                    <p id="refundInvoiceText" class="text-sm font-medium mt-1"></p>
                    <div class="mt-3 p-3 bg-yellow-50 rounded-md">
                        <p class="text-xs text-yellow-700">Transaksi yang direfund tidak dapat dikembalikan. Pastikan produk telah dikembalikan sebelum melakukan refund.</p>
                    </div>
                </div>
                <div class="mt-4 flex justify-end gap-3">
                    <button onclick="closeRefundModal()" type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none">
                        Batal
                    </button>
                    <button onclick="processRefund()" type="button" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md shadow-sm hover:bg-red-700 focus:outline-none">
                        Konfirmasi Refund
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
<script>
    let transactionsCache = [];
    // Script utama untuk halaman Riwayat Transaksi
    document.addEventListener('DOMContentLoaded', () => {
        // Cek jika token ada di localStorage
        if (!localStorage.getItem('token')) {
            window.location.href = '/login';
            return;
        }

        // Inisialisasi flatpickr untuk filter tanggal
        flatpickr("#transDateInput", {
            dateFormat: "d/m/Y",
            maxDate: "today",
            locale: "id",
            onChange: function(selectedDates, dateStr) {
                if (selectedDates.length > 0) {
                    // Pastikan tanggal dikirim dalam format YYYY-MM-DD dengan timezone yang benar
                    const date = formatDateForAPI(selectedDates[0]);
                    fetchTransactionHistory(date);
                } else {
                    // Jika tidak ada tanggal terpilih, tampilkan semua transaksi
                    fetchTransactionHistory(null);
                }
            }
        });

        // Load data awal
        fetchTransactionHistory();
        
        // Pencarian
        document.getElementById('searchInvoice').addEventListener('input', (e) => {
            const searchTerm = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const invoice = row.cells[0]?.textContent?.toLowerCase() || '';
                row.style.display = invoice.includes(searchTerm) ? '' : 'none';
            });
        });

        // Connect outlet selection to transaction history updates
        connectOutletSelectionToHistory();

        // Refresh Lucide icons
        if (window.lucide) window.lucide.createIcons();
    });

    // Function to get currently selected outlet ID - sama seperti di riwayat stok
    function getSelectedOutletId() {
        // First check URL parameters
        const urlParams = new URLSearchParams(window.location.search);
        const outletIdFromUrl = urlParams.get('outlet_id');
        
        if (outletIdFromUrl) {
            return outletIdFromUrl;
        }
        
        // Then check localStorage
        const savedOutletId = localStorage.getItem('selectedOutletId');
        
        if (savedOutletId) {
            return savedOutletId;
        }
        
        // Default to outlet ID 1 if nothing is found
        return 1;
    }

    // Connect to outlet selection dropdown
    function connectOutletSelectionToHistory() {
        // Listen for outlet changes in localStorage
        window.addEventListener('storage', function(event) {
            if (event.key === 'selectedOutletId') {
                // Get current date if available
                const datePicker = document.getElementById('transDateInput');
                let date = null;
                if (datePicker && datePicker._flatpickr && datePicker._flatpickr.selectedDates.length > 0) {
                    date = formatDateForAPI(datePicker._flatpickr.selectedDates[0]);
                }
                
                // Reload history with new outlet
                fetchTransactionHistory(date);
            }
        });
        
        // Also watch for clicks on outlet items in dropdown
        const outletListContainer = document.getElementById('outletListContainer');
        if (outletListContainer) {
            outletListContainer.addEventListener('click', function(event) {
                // Find the clicked li element
                let targetElement = event.target;
                while (targetElement && targetElement !== outletListContainer && targetElement.tagName !== 'LI') {
                    targetElement = targetElement.parentElement;
                }
                
                // If we clicked on an outlet list item
                if (targetElement && targetElement.tagName === 'LI') {
                    // Update history after a short delay to allow your existing code to complete
                    setTimeout(() => {
                        const datePicker = document.getElementById('transDateInput');
                        let date = null;
                        if (datePicker && datePicker._flatpickr && datePicker._flatpickr.selectedDates.length > 0) {
                            date = formatDateForAPI(datePicker._flatpickr.selectedDates[0]);
                        }
                        
                        fetchTransactionHistory(date);
                    }, 100);
                }
            });
        }
    }

    // Update outlet info when data is loaded
    function updateOutletInfo(data) {
        if (data && data.outlet) {
            const outletElements = document.querySelectorAll('.outlet-name');
            outletElements.forEach(el => {
                el.textContent = `${data.outlet.name}`;
            });
            
            const addressElements = document.querySelectorAll('.outlet-address');
            addressElements.forEach(el => {
                el.textContent = data.outlet.address || '';
            });
        } else {
            // No outlet info in data, fetch it separately
            updateOutletInfoFromSelection();
        }
    }

    // Update outlet info when no data is available
    async function updateOutletInfoFromSelection() {
        try {
            const outletId = getSelectedOutletId();
            const response = await fetch(`/api/outlets/${outletId}`, {
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('token')}`,
                    'Accept': 'application/json'
                }
            });
            
            const { data, success } = await response.json();
            
            if (success && data) {
                const outletElements = document.querySelectorAll('.outlet-name');
                outletElements.forEach(el => {
                    el.textContent = `${data.name}`;
                });
                
                const addressElements = document.querySelectorAll('.outlet-address');
                addressElements.forEach(el => {
                    el.textContent = data.address || '';
                });
            }
        } catch (error) {
            console.error('Failed to fetch outlet details:', error);
        }
    }

    // Fungsi untuk fetch data transaksi - dimodifikasi untuk menyertakan outlet_id
    async function fetchTransactionHistory(date = null) {
        try {
            const token = localStorage.getItem('token');
            if (!token) {
                window.location.href = '/login';
                return;
            }

            // Get the current outlet ID
            const outletId = getSelectedOutletId();
            
            // Format parameter tanggal seperti di versi lama
            const params = new URLSearchParams();
            if (date) {
                // Gunakan tanggal yang sama untuk date_from dan date_to
                // untuk menampilkan transaksi pada hari yang dipilih saja
                params.append('date_from', date);
                params.append('date_to', date);
            }
            
            // Tambahkan outlet_id ke parameters
            params.append('outlet_id', outletId);
            
            // Fetch data dari endpoint dengan token authorization
            const response = await fetch(`/api/orders/history?${params.toString()}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Authorization': `Bearer ${token}`
                }
            });
            
            if (!response.ok) {
                if (response.status === 401) {
                    // Jika unauthorized, redirect ke login
                    window.location.href = '/login';
                    return;
                }
                throw new Error('Gagal mengambil data');
            }
            
            const result = await response.json();
            
            // Update outlet info if available
            if (result.data && result.data.outlet) {
                updateOutletInfo(result.data);
            } else {
                updateOutletInfoFromSelection();
            }
            
            // Pastikan kita mengakses data.orders dari response
            if (result.data && Array.isArray(result.data.orders)) {
                // Store in global cache
                transactionsCache = result.data.orders;
                renderTransactionData(result.data.orders);
            } else if (result.orders) {
                // Alternatif jika struktur data berbeda
                transactionsCache = result.orders;
                renderTransactionData(result.orders);
            } else {
                // Jika tidak ada data
                transactionsCache = [];
                renderTransactionData([]);
            }
            
        } catch (error) {
            console.error('Error:', error);
            showAlert('error', error.message);
        }
    }


    // Fungsi untuk render data ke tabel
    function renderTransactionData(transactions) {
        const tbody = document.querySelector('tbody');
        tbody.innerHTML = '';
        
        console.log("Data transaksi untuk dirender:", transactions); // Untuk debugging
        
        if (!transactions || transactions.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="7" class="py-4 text-center text-gray-500">
                        Tidak ada transaksi pada tanggal ini.
                    </td>
                </tr>
            `;
            return;
        }
        
        transactions.forEach(transaction => {
            const row = document.createElement('tr');
            row.className = 'border-b hover:bg-gray-50';
            row.innerHTML = `
                <td class="py-4">${transaction.order_number}</td>
                <td class="py-4">${formatDateTime(transaction.created_at)}</td>
                <td class="py-4">${transaction.user || 'Kasir'}</td>
                <td class="py-4">
                    <span class="px-2 py-1 ${getPaymentBadgeClass(transaction.payment_method)} rounded-full text-xs">
                        ${getPaymentMethodText(transaction.payment_method)}
                    </span>
                </td>
                <td class="py-4">
                    <span class="px-2 py-1 ${transaction.status === 'completed' ? 'text-green-600' : 'text-red-500'} font-bold text-xs">
                        ${transaction.status === 'completed' ? 'Selesai' : 'Dibatalkan'}
                    </span>
                </td>
                <td class="py-4 font-semibold">${formatCurrency(transaction.total)}</td>
                <td class="py-4 flex space-x-2">
                    <a href="#" onclick="openDetailModal('${transaction.id}')" class="text-gray-600 hover:text-green-600">
                        <i data-lucide="eye" class="w-5 h-5"></i>
                    </a>
                    ${transaction.status === 'completed' ? `
                    <a href="#" onclick="openRefundModal('${transaction.order_number}', '${transaction.id}')" class="text-gray-600 hover:text-red-600">
                        <i data-lucide="rotate-ccw" class="w-5 h-5"></i>
                    </a>
                    ` : ''}
                </td>
            `;
            tbody.appendChild(row);
        });
        
        if (window.lucide) window.lucide.createIcons();
    }

    // Fungsi untuk fetch detail transaksi
    function getTransactionDetail(orderId) {
        // Find order in cached data
        const detail = transactionsCache.find(order => order.id == orderId);
        
        if (!detail) {
            return null;
        }
        
        return detail;
    }

    // Fungsi untuk modal detail
    async function openDetailModal(orderId) {
        try {
            console.log(`Opening detail modal for order ID: ${orderId}`);
            
            const transaction = transactionsCache.find(t => t.id == orderId);
            if (!transaction) {
                showAlert('error', 'Detail transaksi tidak ditemukan');
                return;
            }

            // Daftar element yang diperlukan
            const elements = {
                invoice: document.getElementById('detailInvoice'),
                dateTime: document.getElementById('detailDateTime'),
                paymentMethod: document.getElementById('detailPaymentMethod'),
                status: document.getElementById('detailStatus'),
                total: document.getElementById('detailTotal'),
                subtotal: document.getElementById('detailSubtotal'),
                tax: document.getElementById('detailTax'),
                discount: document.getElementById('detailDiscount'),
                totalPaid: document.getElementById('detailTotalPaid'),
                change: document.getElementById('detailChange'),
                items: document.getElementById('detailItems')
            };

            // Validasi element
            for (const [key, element] of Object.entries(elements)) {
                if (!element) {
                    console.error(`Element not found: ${key}`);
                    throw new Error(`Element ${key} tidak ditemukan`);
                }
            }

            // Isi data
            elements.invoice.textContent = transaction.order_number;
            elements.dateTime.textContent = formatDateTime(transaction.created_at);
            elements.paymentMethod.textContent = getPaymentMethodText(transaction.payment_method);
            elements.status.textContent = transaction.status === 'completed' ? 'Selesai' : 'Dibatalkan';
            elements.total.textContent = formatCurrency(transaction.total);
            elements.subtotal.textContent = formatCurrency(transaction.subtotal);
            elements.tax.textContent = formatCurrency(transaction.tax);
            elements.discount.textContent = formatCurrency(transaction.discount);
            elements.totalPaid.textContent = formatCurrency(transaction.total_paid);
            elements.change.textContent = formatCurrency(transaction.change);

            // Isi items
            elements.items.innerHTML = '';
            if (transaction.items?.length > 0) {
                transaction.items.forEach(item => {
                    const itemElement = document.createElement('div');
                    itemElement.className = 'border-b py-2';
                    itemElement.innerHTML = `
                        <div class="flex justify-between">
                            <div>
                                <p class="font-medium">${item.product}</p>
                                <p class="text-sm text-gray-500">${item.quantity} × ${formatCurrency(item.price)}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-medium">${formatCurrency(item.total)}</p>
                                ${item.discount > 0 ? `<p class="text-sm text-red-500">Diskon: ${formatCurrency(item.discount)}</p>` : ''}
                            </div>
                        </div>
                    `;
                    elements.items.appendChild(itemElement);
                });
            } else {
                elements.items.innerHTML = '<p class="text-gray-500 py-4">Tidak ada item</p>';
            }

            // Tampilkan modal
            const modal = document.getElementById('modalDetail');
            if (!modal) {
                throw new Error('Modal detail tidak ditemukan');
            }
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            
            if (window.lucide) {
                window.lucide.createIcons();
            }

        } catch (error) {
            console.error('Error in openDetailModal:', error);
            showAlert('error', 'Gagal memuat detail transaksi: ' + error.message);
        }
    }

    // Fungsi untuk menutup modal detail
    function closeDetailModal() {
        const modal = document.getElementById('modalDetail');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    // Fungsi untuk modal refund (ubah teks menjadi pembatalan)
    function openRefundModal(invoiceNumber, orderId) {
        try {
            // Pastikan modal dan elemen-elemennya ada
            const modal = document.getElementById('modalRefund');
            const invoiceTextEl = document.getElementById('refundInvoiceText');
            const modalTitleEl = document.getElementById('modalRefundTitle');
            const confirmButton = modal?.querySelector('button:last-child');
            
            if (!modal || !invoiceTextEl || !modalTitleEl || !confirmButton) {
                throw new Error('Elemen modal tidak ditemukan. Pastikan struktur HTML benar.');
            }

            // Isi data ke modal
            invoiceTextEl.textContent = `Invoice: ${invoiceNumber}`;
            modalTitleEl.textContent = 'Konfirmasi Pembatalan Order';
            
            // Simpan orderId di data attribute
            modal.dataset.orderId = orderId;
            
            // Ubah teks tombol konfirmasi
            confirmButton.textContent = 'Konfirmasi Pembatalan';
            
            // Tampilkan modal
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            
        } catch (error) {
            console.error('Error in openRefundModal:', error);
            showAlert('error', 'Gagal membuka modal pembatalan: ' + error.message);
        }
    }

    function closeRefundModal() {
        document.getElementById('modalRefund').classList.add('hidden');
        document.getElementById('modalRefund').classList.remove('flex');
    }

    async function processRefund() {
        const orderId = document.getElementById('modalRefund').dataset.orderId;
        try {
            const token = localStorage.getItem('token');
            if (!token) {
                window.location.href = '/login';
                return;
            }

            // Tampilkan loading state
            const confirmButton = document.querySelector('#modalRefund button:last-child');
            const originalButtonText = confirmButton.innerHTML;
            confirmButton.innerHTML = `
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Memproses...
            `;
            confirmButton.disabled = true;

            // Menggunakan endpoint cancelOrder yang sudah ada
            const response = await fetch(`/api/orders/cancel/${orderId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    'Authorization': `Bearer ${token}`
                }
            });

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || `Gagal memproses pembatalan (Status: ${response.status})`);
            }

            const result = await response.json();
            showAlert('success', 'Pembatalan berhasil diproses');
            closeRefundModal();
            fetchTransactionHistory(); // Refresh data
        } catch (error) {
            console.error('Error:', error);
            showAlert('error', error.message);
        } finally {
            // Kembalikan tombol ke state awal
            const confirmButton = document.querySelector('#modalRefund button:last-child');
            confirmButton.innerHTML = 'Konfirmasi Pembatalan';
            confirmButton.disabled = false;
        }
    }

    // Fungsi untuk menampilkan alert
    function showAlert(type, message) {
        const alertContainer = document.getElementById('alertContainer');
        const alertId = 'alert-' + Date.now();
        const alert = document.createElement('div');
        alert.id = alertId;
        alert.className = `p-4 rounded-md shadow-md animate-fade-in-up ${type === 'error' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800'}`;
        alert.innerHTML = `
            <div class="flex items-center gap-2">
                <i data-lucide="${type === 'error' ? 'alert-circle' : 'check-circle'}" class="w-5 h-5"></i>
                <span>${message}</span>
                <button onclick="document.getElementById('${alertId}').classList.add('animate-fade-out'); setTimeout(() => document.getElementById('${alertId}').remove(), 300)" class="ml-auto">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        `;
        alertContainer.appendChild(alert);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (document.getElementById(alertId)) {
                document.getElementById(alertId).classList.add('animate-fade-out');
                setTimeout(() => {
                    if (document.getElementById(alertId)) {
                        document.getElementById(alertId).remove();
                    }
                }, 300);
            }
        }, 5000);
        
        // Refresh ikon Lucide
        if (window.lucide) window.lucide.createIcons();
    }

    // Helper untuk format tanggal
    function formatDateTime(dateString) {
        if (!dateString) return '-';
        
        // Jika format sudah "DD/MM/YYYY HH:mm" seperti dari API
        if (dateString.match(/^\d{2}\/\d{2}\/\d{4} \d{2}:\d{2}$/)) {
            return dateString; // Return langsung karena format sudah sesuai
        }
        
        try {
            const date = new Date(dateString);
            return date.toLocaleDateString('id-ID', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            }).replace(',', '');
        } catch (error) {
            console.error('Error formatting date:', error);
            return dateString;
        }
    }

    // Helper untuk format mata uang
    function formatCurrency(amount) {
        try {
            return 'Rp ' + Number(amount || 0).toLocaleString('id-ID');
        } catch (error) {
            console.error('Error formatting currency:', error);
            return 'Rp 0';
        }
    }

    // Helper untuk tampilan payment method
    function getPaymentMethodText(method) {
        const methods = {
            'cash': 'Tunai',
            'qris': 'QRIS',
            'transfer': 'Transfer'
        };
        return methods[method] || method || 'Tidak diketahui';
    }

    function getPaymentBadgeClass(method) {
        const classes = {
            'cash': 'bg-green-100 text-green-800 border-green-200',
            'qris': 'bg-green-100 text-green-800 border-green-200',
            'transfer': 'bg-purple-100 text-purple-800 border-purple-200'
        };
        return classes[method] || 'bg-gray-100 text-gray-800';
    }

    // Format tanggal untuk API (YYYY-MM-DD)
    function formatDateForAPI(date) {
        // Ambil tanggal dalam timezone lokal user
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        
        // Format dalam YYYY-MM-DD
        return `${year}-${month}-${day}`;
    }
</script>

<style>
    /* Animasi untuk alert */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @keyframes fadeOut {
        from {
            opacity: 1;
            transform: translateY(0);
        }
        to {
            opacity: 0;
            transform: translateY(10px);
        }
    }
    
    .animate-fade-in-up {
        animation: fadeInUp 0.3s ease-out forwards;
    }
    
    .animate-fade-out {
        animation: fadeOut 0.3s ease-out forwards;
    }
</style>

@endsection