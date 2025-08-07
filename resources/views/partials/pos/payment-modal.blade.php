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
                    <div class="flex justify-between mb-4">
                        <span class="text-gray-700">Total Pembayaran:</span>
                        <span id="paymentGrandTotal" class="text-green-500 font-bold text-lg">Rp 0</span>
                    </div>
                    
                    <!-- Kategori Transaksi -->
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Kategori Transaksi
                        </label>
                        <div class="flex space-x-4">
                            <label class="flex items-center">
                                <input type="radio" name="transactionCategory" value="lunas" class="mr-2 text-green-600" checked>
                                <span class="text-sm">Lunas</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="transactionCategory" value="dp" class="mr-2 text-green-600">
                                <span class="text-sm">DP (Uang Muka)</span>
                            </label>
                        </div>
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

                        <!-- Upload Bukti Pembayaran -->
                        <div id="uploadProofSection" class="hidden mt-4">
                            <div class="mb-4">
                                <!-- Cash Proof Upload -->
                                <div id="cashProofUploadSection" class="hidden">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">
                                        Upload Bukti Pembayaran Cash (Opsional)
                                    </label>
                                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center bg-gray-50">
                                        <input type="file" id="cashProofUpload" multiple accept="image/*,.pdf" class="hidden">
                                        <div class="cursor-pointer" onclick="document.getElementById('cashProofUpload').click()">
                                            <i class="fas fa-cloud-upload-alt text-2xl text-gray-400 mb-2"></i>
                                            <p class="text-sm text-gray-600">Klik untuk upload bukti pembayaran</p>
                                            <p class="text-xs text-gray-500 mt-1">JPG, PNG, PDF • Maks 5MB per file</p>
                                        </div>
                                    </div>
                                    <div id="cashProofPreview" class="mt-2 space-y-1">
                                        <!-- File previews will appear here -->
                                    </div>
                                </div>

                                <!-- Transfer Proof Upload -->
                                <div id="transferProofUploadSection" class="hidden">
                                    <label class="block text-sm font-bold mb-2 text-red-600">
                                        Upload Bukti Transfer (Wajib) *
                                    </label>
                                    <div class="border-2 border-dashed border-red-300 rounded-lg p-4 text-center bg-red-50">
                                        <input type="file" id="transferProofUpload" multiple accept="image/*,.pdf" class="hidden" required>
                                        <div class="cursor-pointer" onclick="document.getElementById('transferProofUpload').click()">
                                            <i class="fas fa-cloud-upload-alt text-2xl text-red-500 mb-2"></i>
                                            <p class="text-sm font-medium text-red-600">Klik untuk upload bukti transfer</p>
                                            <p class="text-xs text-red-500 mt-1">JPG, PNG, PDF • Maks 5MB per file</p>
                                        </div>
                                    </div>
                                    <div id="transferProofPreview" class="mt-2 space-y-1">
                                        <!-- File previews will appear here -->
                                    </div>
                                </div>

                                <!-- QRIS Proof Upload -->
                                <div id="qrisProofUploadSection" class="hidden">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">
                                        Upload Bukti Pembayaran QRIS (Opsional)
                                    </label>
                                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center bg-gray-50">
                                        <input type="file" id="qrisProofUpload" multiple accept="image/*,.pdf" class="hidden">
                                        <div class="cursor-pointer" onclick="document.getElementById('qrisProofUpload').click()">
                                            <i class="fas fa-cloud-upload-alt text-2xl text-gray-400 mb-2"></i>
                                            <p class="text-sm text-gray-600">Klik untuk upload bukti pembayaran QRIS</p>
                                            <p class="text-xs text-gray-500 mt-1">JPG, PNG, PDF • Maks 5MB per file</p>
                                        </div>
                                    </div>
                                    <div id="qrisProofPreview" class="mt-2 space-y-1">
                                        <!-- File previews will appear here -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Amount Received Section - Always show for DP, only for cash when Lunas -->
                    <div id="amountReceivedSection" class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="amountReceived">
                            <span id="amountReceivedLabel">Jumlah Uang Diterima</span>
                        </label>
                        <input type="text" id="amountReceived" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-green-500" placeholder="Rp 0">
                    </div>
                    
                    <!-- Change Section - Only show for cash payments when Lunas -->
                    <div id="changeSection" class="mb-4">
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