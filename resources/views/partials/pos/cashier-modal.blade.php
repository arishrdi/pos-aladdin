<!-- Alert Container -->
<div id="alertContainer" class="fixed top-5 right-5 z-[9999] space-y-3"></div>
<script src="https://unpkg.com/lucide@latest"></script>

<!-- Kas Kasir Modal -->
<div id="cashierModal" class="modal fixed inset-0 z-50 hidden">
    <div class="modal-overlay absolute w-full h-full bg-gray-900 opacity-50" onclick="closeModal('cashierModal')"></div>
    <div class="modal-container bg-white w-11/12 md:max-w-sm mx-auto rounded-lg shadow-lg z-50 overflow-y-auto relative top-1/2 transform -translate-y-1/2">
        <div class="modal-content py-6 px-6 text-center">
            <p class="text-sm text-gray-500 font-semibold mb-1">Kas Kasir</p>
            <div id="kasTotal" class="text-3xl font-bold text-green-500 mb-4">Rp 0</div>
            <div id="cashRegisterName" class="text-sm text-gray-500 mb-4"></div>
            <hr class="mb-4 border-green-200">

            <div class="flex justify-center gap-3">
                <button onclick="openModal('tambahKasModal')" class="flex items-center gap-1 border border-green-400 text-green-500 px-4 py-2 rounded hover:bg-green-50 transition">
                    <span class="text-green-500 font-bold">+</span>
                    Tambah Kas
                </button>
                <button onclick="openModal('withdrawModal')" class="flex items-center gap-1 border border-green-400 text-green-500 px-4 py-2 rounded hover:bg-green-50 transition">
                    <span class="text-red-500 font-bold">−</span>
                    Ambil Kas
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Kas -->
<div id="tambahKasModal" class="modal fixed inset-0 z-50 hidden">
    <div class="modal-overlay absolute w-full h-full bg-gray-900 opacity-50" onclick="closeModal('tambahKasModal')"></div>
    <div class="modal-container bg-white w-11/12 md:max-w-sm mx-auto rounded-lg shadow-lg z-50 overflow-y-auto relative top-1/2 transform -translate-y-1/2">
        <div class="modal-content py-6 px-6">
            <div class="flex justify-between items-center mb-2">
                <h2 class="text-lg font-semibold">Tambah Kas</h2>
                <button onclick="closeModal('tambahKasModal')">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <p class="text-sm text-gray-600 mb-4">Masukkan jumlah kas yang akan ditambahkan ke mesin kasir.</p>

            <form onsubmit="submitTambahKas(event)">
                <div class="mb-4 text-left">
                    <label class="block text-sm mb-1 font-medium">Jumlah</label>
                    <input type="number" id="inputJumlah" class="w-full border border-green-300 rounded px-3 py-2 focus:outline-none focus:ring focus:border-green-500" placeholder="Masukkan jumlah" required>
                </div>
                <div class="mb-4 text-left">
                    <label class="block text-sm mb-1 font-medium">Catatan</label>
                    <input type="text" id="inputCatatan" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring" placeholder="Tambahkan catatan">
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeModal('tambahKasModal')" class="px-4 py-2 border border-green-400 text-green-500 rounded hover:bg-green-50">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">Tambah Kas</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Ambil Kas Modal -->
<div id="withdrawModal" class="modal fixed inset-0 z-50 hidden">
    <div class="modal-overlay absolute w-full h-full bg-gray-900 opacity-50" onclick="closeModal('withdrawModal')"></div>
    <div class="modal-container bg-white w-11/12 md:max-w-md mx-auto rounded-lg shadow-lg z-50 overflow-y-auto relative top-1/2 transform -translate-y-1/2">
        <div class="modal-content py-6 px-6">
            <div class="flex justify-between items-center pb-3">
                <p class="text-lg font-bold">Ambil Kas</p>
                <button onclick="closeModal('withdrawModal')" class="modal-close cursor-pointer z-50">
                    <i class="lucide lucide-x"></i>
                </button>
            </div>
            <p class="text-sm text-gray-500 mb-4">
                Masukkan jumlah kas yang akan diambil dari mesin kasir.
            </p>
            <form id="withdrawForm" onsubmit="submitWithdrawKas(event)">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="withdrawAmount">Jumlah</label>
                    <input id="withdrawAmount" name="withdrawAmount" type="number" placeholder="Masukkan jumlah" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring focus:border-green-500" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="withdrawNote">Catatan</label>
                    <input id="withdrawNote" name="withdrawNote" type="text" placeholder="Tambahkan catatan" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring focus:border-green-500">
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeModal('withdrawModal')" class="px-4 py-2 border border-green-400 text-green-500 rounded-lg hover:bg-green-50">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600">Ambil Kas</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Initialize Lucide icons
    lucide.createIcons();
    
    let currentCashBalance = 0;
    let currentCashRegisterId = null;
    let currentOutletId = 1; // Default outlet ID

    // Format currency to IDR
    function formatRupiah(amount) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(amount);
    }

    // Get current outlet ID
    function getCurrentOutletId() {
        // Try to get from data attribute
        const outletElement = document.querySelector('[data-outlet-id]');
        if (outletElement && outletElement.dataset.outletId) {
            return outletElement.dataset.outletId;
        }
        
        // Try to get from URL
        const urlParams = new URLSearchParams(window.location.search);
        const outletParam = urlParams.get('outlet_id');
        if (outletParam) {
            return outletParam;
        }
        
        // Try to get from localStorage
        const storedOutletId = localStorage.getItem('outletId');
        if (storedOutletId) {
            return storedOutletId;
        }
        
        return currentOutletId; // default
    }

    // Update cash display
    function updateCashDisplay(balance, name = '') {
        document.getElementById('kasTotal').innerText = formatRupiah(balance);
        if (name) {
            document.getElementById('cashRegisterName').innerText = name;
        }
    }

    // Fetch current cash balance from API
    async function fetchCashBalance() {
        try {
            const token = localStorage.getItem('token');
            if (!token) {
                showAuthError();
                return;
            }

            const outletId = getCurrentOutletId();
            
            // Get cash register for this outlet
            const registerResponse = await fetch(`/api/cash-registers/${outletId}`, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json'
                }
            });

            if (!registerResponse.ok) {
                throw new Error(`HTTP error! Status: ${registerResponse.status}`);
            }

            const registerResult = await registerResponse.json();
            
            if (!registerResult.success || !registerResult.data) {
                throw new Error('Cash register not found for this outlet');
            }

            currentCashRegisterId = registerResult.data.id;
            currentCashBalance = parseFloat(registerResult.data.balance) || 0;
            
            updateCashDisplay(currentCashBalance, `Kasir #${currentCashRegisterId}`);
        } catch (error) {
            console.error('Error fetching cash balance:', error);
            showAlert({
                title: 'Error',
                message: 'Gagal memuat saldo kas: ' + error.message,
                type: 'error'
            });
        }
    }

    // Modal functions
    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
    }

    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
    }

    // Show authentication error
    function showAuthError(message = 'Anda perlu login untuk mengakses fitur ini') {
        showAlert({
            title: 'Akses Ditolak',
            message: message,
            type: 'error'
        });
    }

    // Tambah Kas function
    async function submitTambahKas(e) {
        e.preventDefault();

        const token = localStorage.getItem('token');
        if (!token) {
            showAuthError();
            return;
        }

        const amount = parseFloat(document.getElementById('inputJumlah').value);
        const reason = document.getElementById('inputCatatan').value;
        const outletId = getCurrentOutletId();

        if (isNaN(amount) || amount <= 0) {
            showAlert({
                title: 'Gagal',
                message: 'Jumlah tidak valid',
                type: 'error'
            });
            return;
        }

        try {
            const response = await fetch('/api/cash-register-transactions/add-cash', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    amount: amount,
                    outlet_id: outletId,
                    reason: reason
                })
            });

            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.message || 'Gagal menambahkan kas');
            }

            if (result.success) {
                showAlert({
                    title: 'Berhasil',
                    message: 'Kas berhasil ditambahkan: ' + formatRupiah(amount),
                    type: 'success'
                });
                
                // Refresh cash balance
                await fetchCashBalance();
                closeModal('tambahKasModal');
                e.target.reset();
            } else {
                throw new Error(result.message || 'Gagal menambahkan kas');
            }
        } catch (error) {
            console.error('Error adding cash:', error);
            showAlert({
                title: 'Gagal',
                message: error.message || 'Terjadi kesalahan saat menambahkan kas',
                type: 'error'
            });
        }
    }

    // Ambil Kas function
    async function submitWithdrawKas(e) {
        e.preventDefault();

        const token = localStorage.getItem('token');
        if (!token) {
            showAuthError();
            return;
        }

        const amount = parseFloat(document.getElementById('withdrawAmount').value);
        const reason = document.getElementById('withdrawNote').value;
        const outletId = getCurrentOutletId();

        if (isNaN(amount) || amount <= 0) {
            showAlert({
                title: 'Gagal',
                message: 'Jumlah tidak valid',
                type: 'error'
            });
            return;
        }

        if (amount > currentCashBalance) {
            showAlert({
                title: 'Gagal',
                message: 'Saldo tidak mencukupi',
                type: 'error'
            });
            return;
        }

        try {
            const response = await fetch('/api/cash-register-transactions/subtract-cash', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    amount: amount,
                    outlet_id: outletId,
                    reason: reason
                })
            });

            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.message || 'Gagal mengambil kas');
            }

            if (result.success) {
                showAlert({
                    title: 'Berhasil',
                    message: 'Kas berhasil diambil: ' + formatRupiah(amount),
                    type: 'success'
                });
                
                // Refresh cash balance
                await fetchCashBalance();
                closeModal('withdrawModal');
                e.target.reset();
            } else {
                throw new Error(result.message || 'Gagal mengambil kas');
            }
        } catch (error) {
            console.error('Error withdrawing cash:', error);
            showAlert({
                title: 'Gagal',
                message: error.message || 'Terjadi kesalahan saat mengambil kas',
                type: 'error'
            });
        }
    }

    // Modern Alert function
    function showAlert({ title, message, type = 'success' }) {
        const alertContainer = document.getElementById("alertContainer");
        
        const alert = document.createElement("div");
        alert.className = `relative flex items-start p-4 pr-12 rounded-lg shadow-md overflow-hidden ${ 
            type === 'success' ? 'bg-green-50 border border-green-100' : 
            type === 'error' ? 'bg-red-50 border border-red-100' :
            'bg-blue-50 border border-blue-100'
        }`;
        
        // Icon container
        const iconContainer = document.createElement("div");
        iconContainer.className = `flex-shrink-0 p-2 rounded-full ${
            type === 'success' ? 'bg-green-100 text-green-600' : 
            type === 'error' ? 'bg-red-100 text-red-600' :
            'bg-blue-100 text-blue-600'
        }`;
        
        const icon = document.createElement("i");
        icon.className = `lucide lucide-${
            type === 'success' ? 'check-circle' : 
            type === 'error' ? 'x-circle' :
            'info'
        } w-5 h-5`;
        iconContainer.appendChild(icon);
        
        // Text container
        const textContainer = document.createElement("div");
        textContainer.className = "ml-3";
        
        const titleElement = document.createElement("h3");
        titleElement.className = `text-sm font-medium ${
            type === 'success' ? 'text-green-800' : 
            type === 'error' ? 'text-red-800' :
            'text-blue-800'
        }`;
        titleElement.textContent = title;
        
        const messageElement = document.createElement("div");
        messageElement.className = `mt-1 text-sm ${
            type === 'success' ? 'text-green-700' : 
            type === 'error' ? 'text-red-700' :
            'text-blue-700'
        }`;
        messageElement.textContent = message;
        
        textContainer.appendChild(titleElement);
        textContainer.appendChild(messageElement);
        
        // Close button
        const closeButton = document.createElement("button");
        closeButton.className = "absolute top-3 right-3 p-1 rounded-full hover:bg-gray-100";
        closeButton.innerHTML = '<i class="lucide lucide-x w-4 h-4 text-gray-500"></i>';
        closeButton.onclick = () => alert.remove();
        
        // Progress bar
        const progressBar = document.createElement("div");
        progressBar.className = `absolute bottom-0 left-0 h-1 ${
            type === 'success' ? 'bg-green-500' : 
            type === 'error' ? 'bg-red-500' :
            'bg-blue-500'
        }`;
        progressBar.style.width = '100%';
        progressBar.style.animation = 'progress 3s linear forwards';
        
        alert.appendChild(iconContainer);
        alert.appendChild(textContainer);
        alert.appendChild(closeButton);
        alert.appendChild(progressBar);
        
        alertContainer.insertBefore(alert, alertContainer.firstChild);
        
        // Auto remove after 3 seconds
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transition = 'opacity 0.5s ease';
            setTimeout(() => alert.remove(), 500);
        }, 3000);
    }

    // Add animation for progress bar
    const style = document.createElement('style');
    style.textContent = `
        @keyframes progress {
            from { width: 100%; }
            to { width: 0%; }
        }
    `;
    document.head.appendChild(style);

    // Initialize when page loads
    document.addEventListener('DOMContentLoaded', function() {
        // Fetch initial cash balance
        fetchCashBalance();
        
        // Set up form submit handlers
        document.getElementById('tambahKasForm')?.addEventListener('submit', submitTambahKas);
        // document.getElementById('withdrawForm')?.addEventListener('submit', submitWithdrawKas);
        
        // Set up click handlers
        const btnCashier = document.getElementById('btnCashier');
        if (btnCashier) {
            btnCashier.addEventListener('click', function() {
                const token = localStorage.getItem('token');
                if (!token) {
                    showAuthError();
                    return;
                }
                openModal('cashierModal');
            });
        }
    });
</script>