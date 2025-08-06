    <style>
    
.modal-container {
    font-size: 0.875rem; /* text-sm */
    padding: 1rem;
    max-height: calc(100vh - 4rem);
    overflow-y: auto;
}

.modal-content {
    padding: 1rem;
}

    .member-dropdown-container {
        position: relative;
    }

    .dropdown-list {
        z-index: 50;
    }

    .member-item {
        padding: 8px 12px;
        cursor: pointer;
        border-bottom: 1px solid #f3f4f6;
    }

    .member-item:last-child {
        border-bottom: none;
    }

    .member-item:hover {
        background-color: #f9fafb;
    }

    .member-item.active {
        background-color: #f3f4f6;
    }
</style>

    <!-- Payment Modal -->
<div class="modal-container bg-white w-full max-w-sm mx-auto rounded shadow-lg z-50 relative text-sm p-4">
    <div id="paymentModal" class="modal hidden fixed inset-0 z-50 flex items-center justify-center overflow-y-auto">
        <div class="modal-overlay absolute w-full h-full bg-gray-900 opacity-50"></div>
        <div class="modal-container bg-white w-full max-w-md mx-auto rounded shadow-lg z-50 overflow-y-auto relative my-16">
            <div class="modal-content py-4 text-left px-6">
                <div class="flex justify-between items-center pb-3">
                    <h3 class="text-xl font-bold text-gray-800">Pembayaran</h3>
                    <button class="modal-close cursor-pointer z-50" onclick="closeModal('paymentModal')">
                        <i class="fas fa-times text-gray-500 hover:text-gray-700"></i>
                    </button>
                </div>
                
                <div class="mb-4">
                    {{-- <div class="flex justify-between mb-2">
                        <span class="text-gray-700">Subtotal:</span>
                        <span id="paymentSubtotal" class="font-bold">Rp 0</span>
                    </div>
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-700">Diskon:</span>
                        <span id="paymentDiscount" class="font-bold">Rp 0</span>
                    </div>
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-700">Pajak:</span>
                        <span id="paymentTax" class="text-gray-700">Rp 0</span>
                    </div> --}}
                    <div class="flex justify-between mb-4">
                        <span class="text-gray-700">Total Pembayaran:</span>
                        <span id="paymentGrandTotal" class="text-green-500 font-bold text-lg">Rp 0</span>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="paymentMethod">
                            Metode Pembayaran
                        </label>
                        <div id="paymentMethods">
                            <!-- Payment methods will be added here -->
                        </div>
                        <div id="qrisDetails" class="hidden mt-4">
                            <div class="bg-gray-50 p-4 rounded-lg text-center">
                                <h3 class="font-medium mb-2">Pembayaran QRIS</h3>
                                <div id="qrisImageContainer" class="flex justify-center">
                                    <!-- Gambar QRIS akan dimuat di sini -->
                                </div>
                                <p class="text-sm text-gray-500 mt-2">Scan QR code di atas untuk melakukan pembayaran</p>
                            </div>
                        </div>
                        <div id="transferDetails" class="hidden mt-4">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h3 class="font-medium mb-2">Informasi Rekening Bank</h3>
                                <p class="text-sm">Nama Pemilik: <span id="bankAccountName">-</span></p>
                                <p class="text-sm">Nama Bank: <span id="bankName">-</span></p>
                                <p class="text-sm">Nomor Rekening: <span id="bankAccountNumber">-</span></p>
                            </div>
                        </div>
                    </div>
                    
                    <div id="cashPaymentSection" class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="amountReceived">
                            Jumlah Uang Diterima
                        </label>
                        <input type="text" id="amountReceived" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-green-500" placeholder="Rp 0">
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="changeAmount">
                            Kembalian
                        </label>
                        <input type="text" id="changeAmount" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100" readonly>
                    </div>

                    <!-- Member Search -->
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="memberDropdown">
                            Member
                        </label>
                        <div class="member-dropdown-container relative">
                            <div class="flex items-center relative">
                                <input
                                    id="memberSearch"
                                    type="text"
                                    class="w-full px-3 py-2 text-sm rounded-md border border-gray-300 focus:border-green-500 focus:ring-1 focus:ring-green-500 placeholder-gray-400"
                                    placeholder="Cari member (nama/kode)"
                                    autocomplete="off"
                                >
                                <div class="absolute right-0 top-0 h-full flex items-center pr-3">
                                    <i data-lucide="chevron-down" class="w-4 h-4 text-gray-500"></i>
                                </div>
                            </div>
                            <div id="memberDropdownList" class="dropdown-list absolute z-30 mt-1 w-full bg-white border border-gray-300 rounded-md shadow-lg hidden">
                                <div id="memberResults" class="max-h-48 overflow-y-auto p-1">
                                    <!-- Results will appear here -->
                                </div>
                            </div>
                            <div id="selectedMember" class="mt-2 hidden">
                                <div class="flex justify-between items-center bg-green-50 p-2 rounded">
                                    <span id="memberName" class="font-medium"></span>
                                    <button id="removeMember" class="text-red-500">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="notes">
                            Catatan (Opsional)
                        </label>
                        <textarea id="notes" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-green-500" rows="2"></textarea>
                    </div> --}}
                </div>
                
                <div class="flex justify-end pt-2">
                    <button id="btnProcessPayment" class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600 transition-colors">
                        Proses Pembayaran
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>