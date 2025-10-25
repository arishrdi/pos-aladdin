<header class="bg-white shadow-sm z-40">
    <div class="flex items-center justify-between px-4 py-3 sm:px-6 sm:py-4">
        <div class="flex items-center">
            <!-- Mobile menu button -->
            <button id="mobileMenuBtn" class="mr-4 md:hidden text-gray-500 hover:text-green-700 transition-all">
                <i data-lucide="menu" class="w-5 h-5"></i>
            </button>
            <!-- Logo for mobile -->
            <div class="md:hidden flex items-center">
                <span class="ml-2 font-bold text-lg">Aladdin Karpet</span>
            </div>
        </div>
        <div class="flex items-center space-x-3 sm:space-x-4">
            <!-- Notification button and dropdown -->
            <div class="relative">
                <button id="notificationBtn" class="p-1 rounded-full hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-700 transition-all">
                    <i data-lucide="bell" class="w-5 h-5 text-gray-500"></i>
                    <span id="notificationBadge" class="absolute top-0 right-0 h-2 w-2 rounded-full bg-red-500 hidden"></span>
                </button>
                
                <!-- Notification dropdown -->
                <div id="notificationDropdown" class="hidden absolute right-0 mt-2 w-72 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 py-1 z-50 max-h-96 overflow-y-auto">
                    <div class="px-4 py-2 border-b border-gray-100">
                        <h3 class="text-sm font-medium text-gray-900">Notifikasi</h3>
                    </div>
                    <div id="notificationList" class="divide-y divide-gray-100">
                        <!-- Notifications will be loaded here -->
                        <div class="px-4 py-3 text-center text-sm text-gray-500">
                            Memuat notifikasi...
                        </div>
                    </div>
                    <div class="px-4 py-2 border-t border-gray-100 text-center">
                        <a href="#" class="text-xs text-green-600 hover:text-green-800">Lihat Semua</a>
                    </div>
                </div>
            </div>
            
            <!-- Profile dropdown -->
            <div class="relative ml-3">
                <div>
                    <button type="button" class="flex items-center focus:outline-none" id="user-menu-button" aria-expanded="false" aria-haspopup="true">
                        <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center">
                            <i data-lucide="user" class="w-4 h-4 text-green-700"></i>
                        </div>
                        {{-- <span class="ml-2 text-sm font-medium hidden sm:inline">{{auth()->users()->name}}</span> --}}
                        <i data-lucide="chevron-down" class="ml-1 w-4 h-4 text-gray-500 hidden sm:inline transition-transform" id="userDropdownArrow"></i>
                    </button>
                </div>
                
                <!-- Dropdown menu -->
                <div
                    class="hidden absolute right-0 mt-2 w-56 rounded-xl shadow-xl bg-white ring-1 ring-black ring-opacity-5 py-2 z-50"
                    id="user-dropdown-menu"
                    role="menu"
                    aria-orientation="vertical"
                    aria-labelledby="user-menu-button"
                >
                    <form id="logout-form">
                        @csrf
                        <button 
                            type="submit" 
                            class="flex items-center gap-3 w-full px-4 py-3 text-sm text-gray-800 hover:bg-gray-100 transition-all duration-200 rounded-md"
                            role="menuitem"
                        >
                            <!-- Icon in green circle -->
                            <span class="flex items-center justify-center w-9 h-9 rounded-full border border-green-500 bg-green-50 text-green-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1" />
                                </svg>
                            </span>
                            <div class="flex flex-col items-start">
                                <span class="font-medium">Keluar</span>
                                <span class="text-xs text-gray-500">Akhiri sesi pengguna</span>
                            </div>
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>
</header>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Lucide icons
        lucide.createIcons({ icons });

        
        // Profile dropdown functionality
        const userMenuButton = document.getElementById('user-menu-button');
        const userDropdownMenu = document.getElementById('user-dropdown-menu');
        const userDropdownArrow = document.getElementById('userDropdownArrow');
        
        if (userMenuButton && userDropdownMenu) {
            userMenuButton.addEventListener('click', function(e) {
                e.stopPropagation();
                userDropdownMenu.classList.toggle('hidden');
                if (userDropdownArrow) {
                    userDropdownArrow.classList.toggle('rotate-180');
                }
            });
            
            // Close when clicking outside
            document.addEventListener('click', function(e) {
                if (!userMenuButton.contains(e.target) && !userDropdownMenu.contains(e.target)) {
                    userDropdownMenu.classList.add('hidden');
                    if (userDropdownArrow) {
                        userDropdownArrow.classList.remove('rotate-180');
                    }
                }
            });
        }

        // Notification dropdown functionality
        const notificationBtn = document.getElementById('notificationBtn');
        const notificationDropdown = document.getElementById('notificationDropdown');
        const notificationBadge = document.getElementById('notificationBadge');
        const notificationOutletSelect = document.getElementById('notificationOutletSelect');
        
        if (notificationBtn && notificationDropdown) {
            notificationBtn.addEventListener('click', async function(e) {
                e.stopPropagation();
                notificationDropdown.classList.toggle('hidden');
                
                // Load notifications when dropdown is opened
                if (!notificationDropdown.classList.contains('hidden')) {
                    await loadNotifications();
                }
            });
            
            // Close when clicking outside
            document.addEventListener('click', function(e) {
                if (!notificationBtn.contains(e.target) && !notificationDropdown.contains(e.target)) {
                    notificationDropdown.classList.add('hidden');
                }
            });
        }

        // Initialize outlet selection for notifications
        if (notificationOutletSelect) {
            // Load available outlets
            loadAvailableOutlets().then(() => {
                // Set default outlet selection
                const selectedOutletId = localStorage.getItem('selectedNotificationOutletId') || getSelectedOutletId();
                notificationOutletSelect.value = selectedOutletId;
                
                // Add event listener for outlet change
                notificationOutletSelect.addEventListener('change', function() {
                    localStorage.setItem('selectedNotificationOutletId', this.value);
                    loadNotifications();
                });
            });
        }

        connectOutletSelectionToNotifications();

        // Function to load available outlets
        async function loadAvailableOutlets() {
            try {
                const token = localStorage.getItem('token');
                if (!token) {
                    throw new Error('Token tidak ditemukan');
                }

                const response = await fetch('/api/outlets', {
                    method: 'GET',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error('Gagal memuat daftar outlet');
                }

                const data = await response.json();
                
                if (data.data && data.data.length > 0) {
                    notificationOutletSelect.innerHTML = '';
                    
                    // Add default option
                    const defaultOption = document.createElement('option');
                    defaultOption.value = '';
                    defaultOption.textContent = 'Semua Outlet';
                    notificationOutletSelect.appendChild(defaultOption);
                    
                    // Add outlet options
                    data.data.forEach(outlet => {
                        const option = document.createElement('option');
                        option.value = outlet.id;
                        option.textContent = outlet.name;
                        notificationOutletSelect.appendChild(option);
                    });
                }
            } catch (error) {
                console.error('Error loading outlets:', error);
            }
        }

        // Function to get selected outlet ID
        function getSelectedOutletId() {
            const urlParams = new URLSearchParams(window.location.search);
            const outletIdFromUrl = urlParams.get('outlet_id');
            
            if (outletIdFromUrl) {
                return outletIdFromUrl;
            }
            
            const savedOutletId = localStorage.getItem('selectedOutletId');
            
            if (savedOutletId) {
                return savedOutletId;
            }
            
            return ''; // Default to all outlets
        }

        // Function to load notifications from backend
// Modifikasi untuk fungsi loadNotifications
// Modifikasi untuk fungsi loadNotifications yang lebih defensif
async function loadNotifications() {
    const notificationList = document.getElementById('notificationList');
    if (!notificationList) return;
    
    notificationList.innerHTML = '<div class="px-4 py-3 text-center text-sm text-gray-500">Memuat notifikasi...</div>';
    
    try {
        const token = localStorage.getItem('token');
        if (!token) {
            throw new Error('Token tidak ditemukan');
        }

        // Get selected outlet ID from dropdown, fallback to global outlet selection if empty
        let outletId = '';
        
        // Periksa dengan aman apakah notificationOutletSelect ada dan punya nilai
        if (notificationOutletSelect && notificationOutletSelect.value) {
            outletId = notificationOutletSelect.value;
            
            // Hanya periksa selectedOptions jika element benar-benar ada
            // Dan gunakan try-catch untuk menghindari error
            try {
                const isAllOutlets = notificationOutletSelect.selectedOptions && 
                                    notificationOutletSelect.selectedOptions[0] &&
                                    notificationOutletSelect.selectedOptions[0].text.includes('Semua');
                                    
                // Jika bukan "Semua Outlet" dan outletId kosong, gunakan outlet global
                if (!isAllOutlets && outletId === '') {
                    outletId = getSelectedOutletId();
                }
            } catch (e) {
                console.warn('Error checking selectedOptions:', e);
                // Fallback to global outlet selection if there's an error
                if (outletId === '') {
                    outletId = getSelectedOutletId();
                }
            }
        } else {
            // Jika tidak ada dropdown, gunakan global outlet
            outletId = getSelectedOutletId();
        }
        
        // Pastikan endpoint sesuai dengan route yang ada
        const endpoint = `/api/notifications/stock-adjustments/${outletId}`;

        const response = await fetch(endpoint, {
            method: 'GET',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error('Gagal memuat notifikasi');
        }

        const data = await response.json();
        
        if (data.data && data.data.length > 0) {
            notificationList.innerHTML = '';
            data.data.forEach(adjustment => {
                const notificationItem = document.createElement('div');
                notificationItem.className = 'px-4 py-3 hover:bg-gray-50 cursor-pointer';
                notificationItem.innerHTML = `
                <a href= "/approve-stok"
                    <div class="flex items-start">
                        <div class="flex-shrink-0 pt-0.5">
                            <i data-lucide="alert-circle" class="w-5 h-5 text-green-500"></i>
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-sm font-medium text-gray-900">Penyesuaian Stok</p>
                            <p class="text-xs text-gray-500 mt-1">${adjustment.product?.name || 'Produk tidak diketahui'} - ${adjustment.quantity} unit</p>
                            <p class="text-xs text-gray-400 mt-1">${formatDate(adjustment.created_at)}</p>
                            ${!outletId ? `<p class="text-xs text-gray-500 mt-1">Outlet: ${adjustment.outlet?.name || 'Tidak diketahui'}</p>` : ''}
                        </div>
                    </div>
                </a>
                `;
                notificationList.appendChild(notificationItem);
            });

            // Update notification badge if it exists
            if (notificationBadge) {
                notificationBadge.classList.remove('hidden');
                notificationBadge.textContent = data.data.length > 9 ? '9+' : data.data.length;
            }
        } else {
            notificationList.innerHTML = '<div class="px-4 py-3 text-center text-sm text-gray-500">Tidak ada notifikasi baru</div>';
            if (notificationBadge) {
                notificationBadge.classList.add('hidden');
            }
        }

        // Refresh icons if lucide exists
        if (window.lucide) {
            window.lucide.createIcons({ icons });
        }
    } catch (error) {
        console.error('Error loading notifications:', error);
        notificationList.innerHTML = `
            <div class="px-4 py-3 text-center text-sm text-red-500">
                ${error.message}
            </div>
        `;
    }
}

// Fungsi yang diperbaiki untuk menghubungkan pemilihan outlet global dengan notifikasi
function connectOutletSelectionToNotifications() {
    // Listen for outlet changes in localStorage
    window.addEventListener('storage', function(event) {
        if (event.key === 'selectedOutletId') {
            // Jika dropdown notifikasi ada, perbarui nilainya
            if (notificationOutletSelect) {
                try {
                    const isAllOutlets = notificationOutletSelect.selectedOptions && 
                                        notificationOutletSelect.selectedOptions[0] &&
                                        notificationOutletSelect.selectedOptions[0].text === 'Semua Outlet';
                    
                    // Jika bukan "Semua Outlet", update dengan nilai outlet global
                    if (!isAllOutlets) {
                        notificationOutletSelect.value = event.newValue || '';
                        loadNotifications();
                    }
                } catch (e) {
                    console.warn('Error in connectOutletSelectionToNotifications:', e);
                    // Fallback: tetap perbarui nilai dropdown
                    notificationOutletSelect.value = event.newValue || '';
                    loadNotifications();
                }
            }
        }
    });
    
    // Juga pantau klik pada item outlet di dropdown global
    const outletListContainer = document.getElementById('outletListContainer');
    if (outletListContainer) {
        outletListContainer.addEventListener('click', function(event) {
            // Cari elemen li yang diklik
            let targetElement = event.target;
            while (targetElement && targetElement !== outletListContainer && targetElement.tagName !== 'LI') {
                targetElement = targetElement.parentElement;
            }
            
            // Jika kita mengklik item daftar outlet
            if (targetElement && targetElement.tagName === 'LI') {
                // Update notifikasi setelah penundaan singkat
                setTimeout(() => {
                    // Jika dropdown notifikasi ada
                    if (notificationOutletSelect) {
                        try {
                            const isAllOutlets = notificationOutletSelect.selectedOptions && 
                                              notificationOutletSelect.selectedOptions[0] &&
                                              notificationOutletSelect.selectedOptions[0].text === 'Semua Outlet';
                            
                            // Jika bukan "Semua Outlet", update dengan nilai outlet global
                            if (!isAllOutlets) {
                                const newOutletId = getSelectedOutletId();
                                notificationOutletSelect.value = newOutletId;
                                loadNotifications();
                            }
                        } catch (e) {
                            console.warn('Error in outlet list click handler:', e);
                            // Fallback: tetap perbarui nilai dropdown
                            const newOutletId = getSelectedOutletId();
                            notificationOutletSelect.value = newOutletId;
                            loadNotifications();
                        }
                    } else {
                        // Jika tidak ada dropdown, tetap perbarui notifikasi
                        loadNotifications();
                    }
                }, 100);
            }
        });
    }
}

        // Helper function to format date
        function formatDate(dateString) {
            const options = { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' };
            return new Date(dateString).toLocaleDateString('id-ID', options);
        }

        // Periodically check for new notifications (every 5 minutes)
        setInterval(async () => {
            if (!notificationDropdown.classList.contains('hidden')) {
                await loadNotifications();
            }
        }, 300000); // 5 minutes

        // Initial load of notifications
        loadNotifications();
    });

    document.getElementById('logout-form').addEventListener('submit', async function(e) {
        e.preventDefault();

        await fetch('/api/logout', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Accept': 'application/json'
            }
        });

        window.location.href = '/'; // Redirect after logout
    });
</script>