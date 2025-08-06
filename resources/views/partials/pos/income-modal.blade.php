<!-- Income Modal - Connected with Backend -->
<div id="incomeModal" class="modal fixed inset-0 z-50 hidden">
    <div class="modal-overlay absolute w-full h-full bg-gray-900 opacity-50"></div>
    <div class="modal-container bg-white w-11/12 md:max-w-md mx-auto rounded-lg shadow-lg z-50 overflow-y-auto relative top-1/2 transform -translate-y-1/2">
        <!-- green accent header bar -->
        <div class="bg-green-500 h-2 rounded-t-lg"></div>
        
        <div class="modal-content py-6 text-left px-6">
            <!-- Header -->
            <div class="flex justify-between items-center pb-4 border-b">
                <p class="text-xl font-bold text-gray-800 flex items-center">
                    <i class="fas fa-chart-line mr-2 text-green-500"></i>
                    Pendapatan Bulanan
                </p>
                <button onclick="closeModal('incomeModal')" class="modal-close cursor-pointer z-50 hover:bg-gray-100 rounded-full p-2 transition-colors">
                    <i class="fas fa-times text-gray-500"></i>
                </button>
            </div>
            
            <!-- Content -->
            <div class="my-6">
                <div id="incomeDataContainer" class="space-y-6">
                    <!-- Loading state -->
                    <div class="flex justify-center items-center py-8">
                        <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-green-500"></div>
                    </div>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="flex justify-end pt-4 border-t">
                <button onclick="closeModal('incomeModal')" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 text-sm font-medium rounded-lg transition-colors duration-300">
                    Tutup
                </button>
                <button onclick="exportRevenueData()" class="ml-2 px-4 py-2 bg-green-500 hover:bg-green-600 text-white text-sm font-medium rounded-lg transition-colors duration-300">
                    <i class="fas fa-download mr-1"></i> Ekspor
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // Format currency ke Rupiah
    function formatRupiah(amount) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(amount);
    }

    // Format tanggal dari format "dd/mm/yyyy" ke string yang lebih ramah
    function formatDateIndo(dateString) {
        if (!dateString) return '-';
        const parts = dateString.split('/');
        if (parts.length !== 3) return dateString;
        
        const day = parts[0];
        const month = parts[1];
        const year = parts[2];
        
        const monthNames = [
            "Januari", "Februari", "Maret", "April", "Mei", "Juni",
            "Juli", "Agustus", "September", "Oktober", "November", "Desember"
        ];
        
        return `${day} ${monthNames[parseInt(month) - 1]} ${year}`;
    }

    // Global variable untuk menyimpan data revenue untuk keperluan ekspor
    let currentRevenueData = null;

    // Function untuk menampilkan modal pendapatan
    function showIncomeModal() {
        const outletId = localStorage.getItem('outlet_id');

        if (!outletId) {
            console.error('outlet_id not found in localStorage');
            showAuthError('Outlet tidak terdeteksi, silakan login ulang');
            return;
        }
        
        // Tampilkan modal
        const modal = document.getElementById('incomeModal');
        if (!modal) {
            console.error('Modal element not found!');
            return;
        }
        modal.classList.remove('hidden');
        
        // Tampilkan loading state
        const container = document.getElementById('incomeDataContainer');
        if (!container) {
            console.error('incomeDataContainer element not found!');
            return;
        }
        
        container.innerHTML = `
            <div class="flex justify-center items-center py-8">
                <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-green-500"></div>
            </div>
        `;

        // Get revenue data from the API
        fetchRevenueData(outletId || getCurrentOutletId());
    }

    // Function untuk mendapatkan outletId dari berbagai sumber
function getCurrentOutletId() {
    // Ambil outlet_id dari localStorage
    const storedOutletId = localStorage.getItem('outlet_id');
    
    // Jika tidak ada, gunakan default 1 atau handle error
    if (!storedOutletId) {
        console.warn('outlet_id not found in localStorage, using default');
        return 1; // atau bisa throw error jika diperlukan
    }
    
    return parseInt(storedOutletId);
}

    // Function untuk mengambil data revenue dari API
    function fetchRevenueData(outletId) {
        // Get token from localStorage
        const token = localStorage.getItem('token');
        if (!token) {
            console.error('No token found in localStorage');
            showAuthError();
            return;
        }
        
        // URL API dari backend Anda
        const apiUrl = `/api/orders/revenue/${outletId}`;
        console.log('API URL:', apiUrl);
        
        fetch(apiUrl, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json'
            }
        })
            .then(response => {
                console.log('API Response status:', response.status);
                if (response.status === 401) {
                    // Token expired or invalid
                    showAuthError('Sesi telah berakhir, silakan login kembali');
                    throw new Error('Unauthorized');
                }
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.json();
            })
            .then(result => {
                console.log('API Response data:', result);
                
                if (!result.success && !result.data) {
                    throw new Error(result.message || 'Server returned error response');
                }
                
                // Render data ke UI
                renderRevenueData(result.data);
                
                // Update button text with revenue amount
                updateRevenueButton(result.data.total);
            })
            .catch(error => {
                console.error('Error fetching revenue data:', error);
                
                const container = document.getElementById('incomeDataContainer');
                if (error.message === 'Unauthorized') {
                    // Already handled in the response check
                    return;
                }
                
                container.innerHTML = `
                    <div class="bg-red-50 text-red-800 p-4 rounded-lg text-center">
                        <i class="fas fa-exclamation-circle text-2xl mb-2"></i>
                        <p class="font-medium">Gagal memuat data pendapatan</p>
                        <p class="text-sm text-red-600 mt-1">${error.message}</p>
                        <div class="mt-3 px-4 py-2 bg-green-100 text-green-800 text-sm rounded-lg">
                            <p class="font-medium">Informasi Debug:</p>
                            <p class="text-xs mt-1">URL: ${apiUrl}</p>
                            <p class="text-xs">Periksa konsol browser (F12) untuk detail lebih lanjut</p>
                        </div>
                        <button onclick="showIncomeModal('${outletId}')" class="mt-3 px-4 py-2 bg-red-100 hover:bg-red-200 text-red-800 text-sm font-medium rounded-lg transition-colors duration-300">
                            <i class="fas fa-redo mr-1"></i> Coba Lagi
                        </button>
                    </div>
                `;
            });
    }

    // Show authentication error message
    function showAuthError(message = 'Anda perlu login untuk mengakses data ini') {
        const container = document.getElementById('incomeDataContainer');
        container.innerHTML = `
            <div class="bg-red-50 text-red-800 p-4 rounded-lg text-center">
                <i class="fas fa-lock text-2xl mb-2"></i>
                <p class="font-medium">Akses Ditolak</p>
                <p class="text-sm text-red-600 mt-1">${message}</p>
                <button onclick="redirectToLogin()" class="mt-3 px-4 py-2 bg-red-100 hover:bg-red-200 text-red-800 text-sm font-medium rounded-lg transition-colors duration-300">
                    <i class="fas fa-sign-in-alt mr-1"></i> Login
                </button>
            </div>
        `;
    }

    // Redirect to login page
    function redirectToLogin() {
        window.location.href = '/login'; // Adjust to your login page URL
    }

    // Function untuk update button revenue di halaman utama
    function updateRevenueButton(amount) {
        const btnIncomeModal = document.getElementById('btnIncomeModal');
        if (btnIncomeModal) {
            // Cari icon dalam button dan pertahankan
            const icon = btnIncomeModal.querySelector('i');
            const iconHtml = icon ? icon.outerHTML + ' ' : '<i class="fas fa-money-bill mr-1.5 text-green-500 text-base"></i> ';
            
            // Update text button dengan jumlah pendapatan
            btnIncomeModal.innerHTML = iconHtml + formatRupiah(amount);
        }
    }

    // Function untuk render data revenue ke UI
    function renderRevenueData(revenueData) {
        console.log('Rendering revenue data:', revenueData);
        
        // Simpan data untuk kemungkinan ekspor
        currentRevenueData = revenueData;
        
        const container = document.getElementById('incomeDataContainer');
        if (!container) {
            console.error('incomeDataContainer element not found during render!');
            return;
        }
        
        const total = parseFloat(revenueData.total);
        
        // Hitung rata-rata per hari
        const fromDate = parseDate(revenueData.from);
        const toDate = parseDate(revenueData.to);
        const daysDiff = Math.ceil((toDate - fromDate) / (1000 * 60 * 60 * 24)) + 1;
        const avgPerDay = total / daysDiff;
        
        // Tampilkan data di modal dengan UI yang lebih modern dengan aksen oranye
        container.innerHTML = `
            <div class="relative overflow-hidden bg-gradient-to-r from-green-50 to-green-100 rounded-lg p-6">
                <!-- Ornament circles -->
                <div class="absolute -right-6 -top-6 w-24 h-24 rounded-full bg-green-200 opacity-50"></div>
                <div class="absolute -left-4 -bottom-4 w-16 h-16 rounded-full bg-green-200 opacity-30"></div>
                
                <div class="relative z-10 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 mb-4 rounded-full bg-green-500 text-white">
                        <i class="fas fa-wallet text-2xl"></i>
                    </div>
                    <h3 class="text-3xl font-bold text-gray-800">${formatRupiah(total)}</h3>
                    <p class="text-sm text-gray-600 mt-1">Total Pendapatan Bulan Ini</p>
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-4 mt-4">
                <div class="bg-white border border-green-100 rounded-lg p-4 text-center shadow-sm hover:shadow transition-shadow duration-300">
                    <div class="text-green-500 mb-1"><i class="fas fa-calendar-day"></i></div>
                    <h4 class="text-lg font-semibold text-gray-700">${formatRupiah(avgPerDay)}</h4>
                    <p class="text-xs text-gray-500">Rata-rata per hari</p>
                </div>
                <div class="bg-white border border-green-100 rounded-lg p-4 text-center shadow-sm hover:shadow transition-shadow duration-300">
                    <div class="text-green-500 mb-1"><i class="fas fa-calendar-week"></i></div>
                    <h4 class="text-lg font-semibold text-gray-700">${daysDiff} hari</h4>
                    <p class="text-xs text-gray-500">Periode</p>
                </div>
            </div>
            
            <div class="mt-4 bg-white border border-gray-100 rounded-lg p-4 shadow-sm">
                <div class="flex items-center mb-2">
                    <div class="w-2 h-2 rounded-full bg-green-500 mr-2"></div>
                    <span class="text-sm font-medium text-gray-700">Detail Periode</span>
                </div>
                <div class="flex justify-between items-center text-sm text-gray-600 pl-4">
                    <span>Tanggal</span>
                    <span class="font-medium">${formatDateIndo(revenueData.from)} - ${formatDateIndo(revenueData.to)}</span>
                </div>
            </div>
            
            <!-- Ornamental person icon -->
            <div class="flex justify-end mt-2 text-green-300">
                <i class="fas fa-user-tie text-3xl"></i>
            </div>
        `;
    }

    // Function untuk parsing tanggal dari format "dd/mm/yyyy"
    function parseDate(dateString) {
        if (!dateString) return new Date();
        const parts = dateString.split('/');
        if (parts.length !== 3) return new Date();
        
        // Note: Months are 0-indexed in JavaScript Date
        return new Date(parts[2], parts[1] - 1, parts[0]);
    }

    // Function untuk menutup modal
    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (!modal) {
            console.error(`Modal with id ${modalId} not found!`);
            return;
        }
        modal.classList.add('hidden');
    }

    // Function untuk ekspor data pendapatan (CSV)
    function exportRevenueData() {
        if (!currentRevenueData) {
            console.error('No revenue data to export');
            return;
        }
        
        const { from, to, total } = currentRevenueData;
        const csvContent = `Data,Nilai\nPeriode,${from} - ${to}\nTotal Pendapatan,${total}\n`;
        
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        
        const link = document.createElement('a');
        link.setAttribute('href', url);
        link.setAttribute('download', `pendapatan_${from.replace(/\//g, '-')}_${to.replace(/\//g, '-')}.csv`);
        link.style.visibility = 'hidden';
        
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    // Initialize event listeners when document is ready
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Document ready, initializing income modal');
        
        // Check if modal exists
        const modal = document.getElementById('incomeModal');
        if (!modal) {
            console.error('Income modal element not found on page load!');
        } else {
            console.log('Income modal found and ready');
        }

        if (!localStorage.getItem('outlet_id')) {
            console.warn('Setting default outlet_id');
            localStorage.setItem('outlet_id', '1');
        }

        // Load initial data
        const outletId = localStorage.getItem('outlet_id');
        if (outletId) {
            fetchRevenueData(parseInt(outletId));
        }
        
        // Initialize click handler for income button
        const btnIncomeModal = document.getElementById('btnIncomeModal');
        if (btnIncomeModal) {
            console.log('Income button found, adding click handler');
            btnIncomeModal.addEventListener('click', function() {
                // Check token before showing modal
                const token = localStorage.getItem('token');
                if (!token) {
                    showAuthError();
                    return;
                }
                showIncomeModal();
            });
            
            // Pre-load revenue data to update the button on page load (optional)
            const token = localStorage.getItem('token');
            if (token) {
                fetchRevenueData(getCurrentOutletId());
            }
        } else {
            console.error('Income button not found on page load!');
        }
    });
</script>