@extends('layouts.app')

@section('title', 'Laporan Stok')

@section('content')

<!-- Flatpickr CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<!-- Alert Notification -->
<div id="alertContainer" class="fixed top-4 right-4 z-50 space-y-3 w-80">
    <!-- Alert akan muncul di sini secara dinamis -->
</div>

<!-- Page Title + Action -->
<div class="mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
       <h1 class="text-3xl font-bold text-gray-800">Stok Per Tanggal</h1>
        <div class="relative w-full md:w-64">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i data-lucide="search" class="w-5 h-5 text-gray-400"></i>
            </span>
            <input type="text" id="searchInput" placeholder="Pencarian..."
                class="w-full pl-10 pr-4 py-3 border rounded-lg text-base font-medium focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" />
        </div>
    </div>
</div>

<!-- Card: Stok Info + Aksi -->
<div class="bg-white rounded-md p-4 shadow-md mb-4 flex flex-col md:flex-row md:items-center md:justify-between">
    <!-- Kiri: Judul -->
    <div class="mb-3 md:mb-0 flex items-start gap-2">
        <i data-lucide="package" class="w-5 h-5 text-black mt-1"></i>
        <div>
            <h2 class="text-lg font-semibold text-gray-800" id="outletName">Menampilkan stok untuk: Loading...</h2>
            <p id="reportDate" class="text-sm text-gray-600">Data stok per tanggal <span class="font-medium">{{ date('d M Y') }}</span></p>
        </div>
    </div>
</div>

<!-- Card: Tabel Laporan Stok -->
<div class="bg-white rounded-lg shadow-lg p-6">
   <div class="mb-4">
    <h1 class="text-xl font-bold text-gray-800">Custom Stok Per Tanggal</h1>
    <p class="text-sm text-gray-600 mb-2">Lihat stok pada tanggal tertentu</p>

    <!-- Filter Tanggal -->
    <div class="mt-2">
        <label for="reportDateInput" class="block text-sm font-medium text-gray-700 mb-1">Pilih Tanggal</label>
        <div class="relative">
            <input id="reportDateInput" type="text"
                class="w-full sm:w-56 pl-10 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                placeholder="Tanggal" />
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <i data-lucide="calendar" class="w-4 h-4 text-gray-500"></i>
            </span>
        </div>
    </div>
</div>
    <div class="overflow-x-auto">
        <table class="w-full text-base">
            <thead class="text-left text-gray-700 border-b-2">
                <tr>
                    <th class="py-3 font-bold">Produk</th>
                    <th class="py-3 font-bold">Kategori</th>
                    <th class="py-3 font-bold">Stok</th>
                    <th class="py-3 font-bold">Status</th>
                </tr>
            </thead>
            <tbody id="inventoryTableBody" class="text-gray-700 divide-y">
                <!-- Data akan dirender di sini oleh JavaScript -->
                <tr>
                    <td colspan="4" class="py-4 text-center">
                        <!-- Loading indicator -->
                        <div id="loadingIndicator" class="hidden">
                            <div class="flex flex-col items-center justify-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                     class="animate-spin text-green-500">
                                    <path d="M21 12a9 9 0 1 1-6.219-8.56"/>
                                </svg>
                                <span class="text-gray-500">Mengambil data...</span>
                            </div>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Main Script - Moved to bottom of body for better loading -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
<script>
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
                const outletNameElement = document.getElementById('outletName');
                if (outletNameElement) {
                    outletNameElement.textContent = `Menampilkan stok untuk: ${data.name}`;
                }
            }
        } catch (error) {
            console.error('Failed to fetch outlet details:', error);
        }
    }// Variabel global untuk menyimpan data inventory
    let inventoryData = [];
    let currentOutletId = null;
    let currentDate = null;

    async function loadProductData(outletId) {
        try {
            // Sembunyikan dropdown outlet setelah memilih
            const outletDropdown = document.getElementById('outletDropdown');
            if (outletDropdown) outletDropdown.classList.add('hidden');
            
            // Ambil tanggal terpilih atau gunakan hari ini
            const datePicker = document.getElementById('reportDateInput');
            const selectedDate = datePicker ? datePicker.value : new Date().toISOString().split('T')[0];
            
            // Panggil fungsi yang sudah ada untuk memuat data inventory
            fetchInventoryData(selectedDate);
            
            // Jika perlu menyimpan outlet yang dipilih
            localStorage.setItem('selectedOutletId', outletId);
            currentOutletId = outletId;
            
        } catch (error) {
            console.error('Error loading product data:', error);
            showAlert('error', 'Gagal memuat data produk');
        }
    }

    function getSelectedOutletId() {
        // Cek URL parameter terlebih dahulu
        const urlParams = new URLSearchParams(window.location.search);
        const outletIdFromUrl = urlParams.get('outlet_id');
        if (outletIdFromUrl) {
            localStorage.setItem('selectedOutletId', outletIdFromUrl);
            return outletIdFromUrl;
        }
        
        // Jika tidak ada di URL, ambil dari localStorage
        const savedOutletId = localStorage.getItem('selectedOutletId');
        if (savedOutletId) return savedOutletId;
        
        // Jika tidak ada keduanya, gunakan default
        return 1; // Default outlet
    }

    const datePicker = flatpickr("#reportDateInput", {
        dateFormat: "Y-m-d",
        defaultDate: "today",
        onChange: function(selectedDates, dateStr) {
            fetchInventoryData(dateStr); // Selalu panggil fetch dengan tanggal baru
        }
    });

    // Fungsi untuk mengambil data outlet dari API
    function fetchOutlets() {
        console.log("Fetching available outlets");
        
        fetch('/api/outlets', {
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('token')}`,
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                renderOutletSelector(data.data);
            } else {
                showAlert('error', 'Gagal mengambil daftar outlet: ' + (data.message || 'Terjadi kesalahan'));
            }
        })
        .catch(error => {
            console.error('Error fetching outlets:', error);
            showAlert('error', 'Gagal mengambil daftar outlet: ' + error.message);
        });
    }

    function handleOutletChange() {
        const outletId = getSelectedOutletId();
        const datePicker = document.getElementById('reportDateInput');
        const date = datePicker ? datePicker.value : new Date().toISOString().split('T')[0];
        
        // Jika outlet berubah, fetch data baru
        if (outletId !== currentOutletId) {
            currentOutletId = outletId;
            fetchInventoryData(date);
        }
    }

    // Render selector outlet
    function renderOutletSelector(outlets) {
        const selectorContainer = document.getElementById('outletSelectorContainer');
        if (!selectorContainer) return;
        
        const currentOutletId = getSelectedOutletId();
        
        // Buat dropdown
        const select = document.createElement('select');
        select.id = 'outletSelector';
        select.className = 'block w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500';
        
        outlets.forEach(outlet => {
            const option = document.createElement('option');
            option.value = outlet.id;
            option.textContent = outlet.name;
            option.selected = outlet.id.toString() === currentOutletId.toString();
            select.appendChild(option);
        });
        
        // Clear dan tambahkan elemen baru
        selectorContainer.innerHTML = '';
        selectorContainer.appendChild(select);
        
        // Tambahkan event listener untuk perubahan outlet
        select.addEventListener('change', function() {
            const selectedOutletId = this.value;
            localStorage.setItem('selectedOutletId', selectedOutletId);
            
            // Refetch data inventory dengan outlet baru
            const datePicker = document.getElementById('reportDateInput');
            const currentDate = datePicker ? datePicker.value : new Date().toISOString().split('T')[0];
            fetchInventoryData(currentDate);
            
            // Broadcast change event untuk komponen lain
            const event = new Event('outletChanged');
            window.dispatchEvent(event);
        });
    }

    // Fungsi untuk mengambil data dari API
    function fetchInventoryData(date) {
        console.log("Fetching inventory data for date:", date);
        
        // Pastikan date selalu ada nilainya
        date = date || new Date().toISOString().split('T')[0];
        const outletId = getSelectedOutletId();
        
        // Update state terbaru
        currentDate = date;
        currentOutletId = outletId;
        
        // Handle loading indicator lebih aman
        const loadingIndicator = document.getElementById('loadingIndicator');
        if (loadingIndicator) {
            loadingIndicator.classList.remove('hidden');
        }
        
        const apiUrl = `/api/reports/inventory-by-date/${outletId}?date=${date}`;
        console.log('API URL:', apiUrl);
        
        fetch(apiUrl, {
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('token')}`,
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            // Sembunyikan loading indicator jika ada
            if (loadingIndicator) {
                loadingIndicator.classList.add('hidden');
            }
            
            if (data.success) {
                inventoryData = data.data.inventory_items;
                updateOutletInfo(data.data);
                renderInventoryTable(inventoryData);
                // showAlert('success', `Data stok berhasil diperbarui`);
            } else {
                showAlert('error', 'Gagal mengambil data: ' + (data.message || 'Terjadi kesalahan'));
            }
        })
        .catch(error => {
            // Sembunyikan loading indicator jika ada
            if (loadingIndicator) {
                loadingIndicator.classList.add('hidden');
            }
            console.error('Error fetching data:', error);
            showAlert('error', 'Gagal mengambil data: ' + error.message);
        });
    }

    // Update informasi outlet dan tanggal
    function updateOutletInfo(data) {
        const outletId = getSelectedOutletId();
        
        // Coba ambil data outlet secara langsung jika kita tidak memiliki nama outlet
        if (!data.outlet_name && !outletName[outletId]) {
            // Jalankan fetch outlet details untuk update nama
            updateOutletInfoFromSelection();
        }
        
        // Format tanggal menjadi lebih readable
        const formattedDate = new Date(data.date).toLocaleDateString('id-ID', {
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        });
        
        const reportDateElement = document.getElementById('reportDate');
        if (reportDateElement) {
            reportDateElement.innerHTML = `Data stok per tanggal <span class="font-medium">${formattedDate}</span>`;
            
            // Tambahkan indikator real-time jika perlu
            if (data.is_realtime) {
                reportDateElement.innerHTML += ' <span class="ml-1 px-2 py-0.5 bg-green-100 text-green-800 text-xs rounded-full">Realtime</span>';
            }
        }
    }

    // Connect to outlet selection dropdown
    function connectOutletSelectionToInventory() {
        // Listen for custom event
        window.addEventListener('outletChanged', function() {
            const datePicker = document.getElementById('reportDateInput');
            const currentDate = datePicker ? datePicker.value : new Date().toISOString().split('T')[0];
            fetchInventoryData(currentDate);
        });
        
        // Listen for localStorage changes (for multi-tab support)
        window.addEventListener('storage', function(event) {
            if (event.key === 'selectedOutletId') {
                const datePicker = document.getElementById('reportDateInput');
                const currentDate = datePicker ? datePicker.value : new Date().toISOString().split('T')[0];
                fetchInventoryData(currentDate);
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
                    // Update inventory after a short delay to allow your existing code to complete
                    setTimeout(() => {
                        const datePicker = document.getElementById('reportDateInput');
                        const currentDate = datePicker ? datePicker.value : new Date().toISOString().split('T')[0];
                        
                        fetchInventoryData(currentDate);
                    }, 100);
                }
            });
        }
    }

    // Render tabel inventori
    function renderInventoryTable(items) {
        const tableBody = document.getElementById('inventoryTableBody');
        
        // Clear existing content
        tableBody.innerHTML = '';
        
        if (items.length === 0) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="4" class="py-4 text-center text-gray-500">
                        Tidak ada data stok untuk ditampilkan
                    </td>
                </tr>
            `;
            return;
        }
        
        // Render each inventory item
        items.forEach(item => {
            // Tentukan status berdasarkan stok
            let statusClass, statusText;
            
            if (item.quantity <= 0) {
                statusClass = 'bg-red-100 text-red-700';
                statusText = 'Habis';
            } else if (item.quantity < 20) {
                statusClass = 'bg-green-100 text-green-700';
                statusText = 'Stok Rendah';
            } else {
                statusClass = 'bg-green-100 text-green-700';
                statusText = 'Aman';
            }
            
            // Gunakan icon default untuk semua kategori
            const iconName = 'package';
            
            const row = document.createElement('tr');
            row.innerHTML = `
                <td class="py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-md bg-green-100 flex items-center justify-center">
                            <i data-lucide="${iconName}" class="w-5 h-5 text-green-500"></i>
                        </div>
                        <div>
                            <span class="font-semibold block">${item.product_name}</span>
                            <span class="text-xs text-gray-500">${item.sku}</span>
                        </div>
                    </div>
                </td>
                <td class="py-4 font-medium">${item.category}</td>
                <td class="py-4 font-bold text-green-600">${item.quantity}</td>
                <td class="py-4">
                    <span class="px-3 py-1 text-xs font-bold ${statusClass} rounded-full">${statusText}</span>
                </td>
            `;
            
            tableBody.appendChild(row);
        });
        
        // Reinitialize Lucide icons for the new content
        if (window.lucide) {
            window.lucide.createIcons();
        }
    }

    // Fungsi search/filter
    function setupSearch() {
        const searchInput = document.getElementById('searchInput');
        if (!searchInput) return;
        
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            
            if (!inventoryData.length) return;
            
            if (searchTerm === '') {
                renderInventoryTable(inventoryData);
                return;
            }
            
            const filteredItems = inventoryData.filter(item => 
                item.product_name.toLowerCase().includes(searchTerm) || 
                item.sku.toLowerCase().includes(searchTerm) ||
                item.category.toLowerCase().includes(searchTerm)
            );
            
            renderInventoryTable(filteredItems);
        });
    }

    // Tampilkan alert/notifikasi
    function showAlert(type, message) {
        const alertContainer = document.getElementById('alertContainer');
        if (!alertContainer) {
            console.error('Alert container not found!');
            return;
        }
        
        const alertId = 'alert-' + Date.now();
        
        const alertTypes = {
            success: {
                bgColor: 'bg-green-100',
                textColor: 'text-green-800',
                icon: 'check-circle'
            },
            error: {
                bgColor: 'bg-red-100',
                textColor: 'text-red-800',
                icon: 'x-circle'
            },
            warning: {
                bgColor: 'bg-yellow-100',
                textColor: 'text-yellow-800',
                icon: 'alert-circle'
            },
            info: {
                bgColor: 'bg-blue-100',
                textColor: 'text-blue-800',
                icon: 'info'
            }
        };
        
        const alertType = alertTypes[type] || alertTypes.info;
        
        const alertHTML = `
            <div id="${alertId}" class="rounded-lg p-4 ${alertType.bgColor} ${alertType.textColor} flex items-start shadow-md transform transition-all duration-300 ease-in-out opacity-0 translate-x-full">
                <i data-lucide="${alertType.icon}" class="w-5 h-5 mr-3 mt-0.5 flex-shrink-0"></i>
                <div class="flex-grow">
                    <p class="font-medium">${message}</p>
                </div>
                <button onclick="this.parentElement.remove()" class="ml-3 flex-shrink-0">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        `;
        
        alertContainer.insertAdjacentHTML('beforeend', alertHTML);
        
        // Animate in
        setTimeout(() => {
            const alertEl = document.getElementById(alertId);
            alertEl.classList.remove('opacity-0', 'translate-x-full');
            
            // Initialize Lucide icons for the alert
            if (window.lucide) {
                window.lucide.createIcons({
                    attrs: {
                        class: ["stroke-current"]
                    }
                });
            }
            
            // Auto dismiss after 5 seconds
            setTimeout(() => {
                if (alertEl && alertEl.parentNode) {
                    alertEl.classList.add('opacity-0', 'translate-x-full');
                    setTimeout(() => {
                        if (alertEl && alertEl.parentNode) {
                            alertEl.remove();
                        }
                    }, 300);
                }
            }, 5000);
        }, 10);
    }

    // Inisialisasi ketika DOM siap
    document.addEventListener('DOMContentLoaded', function() {
        console.log("DOM fully loaded");
        
        // Fetch daftar outlet terlebih dahulu
        fetchOutlets();

        // Tambahkan event listener untuk selector outlet setelah di-render
        document.getElementById('outletSelectorContainer')?.addEventListener('change', function(e) {
            if (e.target && e.target.id === 'outletSelector') {
                handleOutletChange();
            }
        });
        
        // Pastikan elemen reportDateInput ada
        const dateInput = document.getElementById('reportDateInput');
        if (dateInput) {
            console.log("Date input found:", dateInput);
            
            // Inisialisasi flatpickr secara eksplisit
            const datePicker = flatpickr("#reportDateInput", {
                dateFormat: "Y-m-d",
                defaultDate: "today",
                onChange: function(selectedDates, dateStr) {
                    console.log("Date changed to:", dateStr);
                    fetchInventoryData(dateStr);
                },
                locale: {
                    firstDayOfWeek: 1
                }
            });
            
            console.log("Flatpickr instance:", datePicker);
            
            // Ambil tanggal hari ini dalam format yang benar
            const today = new Date().toISOString().split('T')[0];
            console.log("Today's date for API:", today);
            
            // Fetch data awal dengan tanggal hari ini
            fetchInventoryData(today);
        } else {
            console.error("Date input element not found!");
        }
        
        // Setup search functionality
        setupSearch();
        connectOutletSelectionToInventory();
        
        // Check URL for outlet_id parameter on load
        const urlParams = new URLSearchParams(window.location.search);
        const outletIdFromUrl = urlParams.get('outlet_id');
        if (outletIdFromUrl) {
            console.log("Found outlet_id in URL:", outletIdFromUrl);
            localStorage.setItem('selectedOutletId', outletIdFromUrl);
            
            // Trigger event for other components
            const event = new Event('outletChanged');
            window.dispatchEvent(event);
        }
    });
</script>

@endsection