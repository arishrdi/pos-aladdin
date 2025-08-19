/**
 * Product Management Module
 * Handles all product-related operations including CRUD, filtering, and UI interactions
 */
const ProductManager = (() => {
    // Private variables
    let produkHapusId = null;
    let currentOutletId = localStorage.getItem('selectedOutletId') || 1;
    let currentOutletName = localStorage.getItem('currentOutletName') || 'Aladdin Karpet';

    // DOM Elements
    const elements = {
        modals: [
            "modalTambahProduk",
            "modalEditProduk",
            "modalKonfirmasiHapus",
        ],
        forms: {
            add: "tambahProdukForm",
            edit: "editProdukForm",
        },
        buttons: {
            save: "btnSimpanProduk",
            saveEdit: "btnSimpanEdit",
            cancel: "btnBatalModal",
            cancelEdit: "btnBatalEdit",
            cancelDelete: "btnBatalHapus",
            confirmDelete: "btnKonfirmasiHapus",
        },
        inputs: {
            search: "searchProduk",
            image: "gambar",
            editImage: "editGambar",
        },
        containers: {
            alert: "alertContainer",
            tableBody: "produkTableBody",
            outletCheckboxes: "outletCheckboxes",
            editOutletList: "editOutletList",
        },
    };

    async function loadProductData(outletId) {
        try {
            // Ensure outletId is valid
            outletId = outletId || getSelectedOutletId();
            if (!outletId || isNaN(outletId)) {
                outletId = 1; // Fallback to default
            }

            // Hide outlet dropdown after selection
            const outletDropdown = document.getElementById("outletDropdown");
            if (outletDropdown) outletDropdown.classList.add("hidden");

            // Get outlet name and update display
            const outletName = await getOutletName(outletId);
            updateOutletDisplay(outletId, outletName);

            // Load products for this outlet
            await loadProducts(outletId);

            // Save the selected outlet
            localStorage.setItem("selectedOutletId", outletId);
            currentOutletId = outletId;
        } catch (error) {
            console.error("Error loading product data:", error);
            showAlert("error", "Gagal memuat data produk");
        }
    }

    // Fungsi untuk mengupdate tampilan nama outlet
    function updateOutletDisplay() {
        const outletTitle = document.getElementById('currentOutletName');
        const outletDesc = document.getElementById('outletNamePlaceholder');
        
        if (outletTitle) outletTitle.textContent = currentOutletName;
        if (outletDesc) outletDesc.textContent = currentOutletName;
    }

    // Fungsi untuk mendapatkan nama outlet (bisa dari API atau object mapping)
    async function getOutletName(outletId) {
        // Check cache first
        if (window.outletCache && window.outletCache[outletId]) {
            return window.outletCache[outletId];
        }

        try {
            const response = await fetch(`/api/outlets/${outletId}`);
            const data = await response.json();

            // Cache the result
            if (!window.outletCache) window.outletCache = {};
            window.outletCache[outletId] = data.name || `Outlet ${outletId}`;

            return window.outletCache[outletId];
        } catch (error) {
            console.error("Error fetching outlet:", error);
            return `Outlet ${outletId}`;
        }
    }

    window.addEventListener("storage", function (event) {
        if (event.key === "selectedOutletId" && event.newValue) {
            const outletId = event.newValue;
            getOutletName(outletId).then((name) => {
                updateOutletDisplay(outletId, name);
                loadProductData(outletId);
            });
        }
    });

    const connectOutletSelectionToProducts = () => {
        // Serupa dengan connectOutletSelectionToHistory() di script riwayat stok
        const outletListContainer = document.getElementById(
            "outletListContainer"
        );
        if (outletListContainer) {
            outletListContainer.addEventListener("click", function (event) {
                // Logika untuk mendeteksi perubahan outlet
                setTimeout(() => {
                    currentOutletId = getSelectedOutletId();
                    loadProducts();
                }, 100);
            });
        }
    };

    const getSelectedOutletId = () => {
        // Ambil dari URL jika ada
        const urlParams = new URLSearchParams(window.location.search);
        const outletIdFromUrl = urlParams.get("outlet_id");

        if (outletIdFromUrl && !isNaN(outletIdFromUrl)) {
            return parseInt(outletIdFromUrl);
        }

        // Ambil dari localStorage
        const savedOutletId = localStorage.getItem("selectedOutletId");
        if (savedOutletId && !isNaN(savedOutletId)) {
            return parseInt(savedOutletId);
        }

        return 1; // Default outlet
    };

    // Initialize the module
    const init = () => {
        setupOutletChangeListener();
        loadProducts();
        setupModals();
        createAuthInterceptor();
        setupEventListeners();
        loadInitialData();
        initLucideIcons();

        // Preload outlets and initialize display
        preloadOutlets().then(() => {
            const outletId = getSelectedOutletId();
            loadProductData(outletId);
        });

        connectOutletSelectionToProducts();
    };
    // fungsi untuk generate barcode
    const generateBarcode = () => {
        // Generate random barcode (EAN-13 format)
        const randomBarcode = Math.floor(1000000000000 + Math.random() * 9000000000000).toString();
        return randomBarcode;
    };

    document.getElementById('generateBarcodeBtn')?.addEventListener('click', () => {
        document.getElementById('barcode').value = generateBarcode();
    });
    
    document.getElementById('generateBarcodeBtnEdit')?.addEventListener('click', () => {
        document.getElementById('editBarcode').value = generateBarcode();
    });

    function setupOutletChangeListener() {
        // Method 1: Polling localStorage setiap 500ms
        let lastOutletId = currentOutletId;
        
        setInterval(() => {
            const newOutletId = localStorage.getItem('selectedOutletId') || currentOutletId;
            if (newOutletId !== lastOutletId) {
                lastOutletId = newOutletId;
                loadProducts(newOutletId);
            }
        }, 500);
        
        // Method 2: Event listener untuk klik di dokumen
        document.addEventListener('click', (e) => {
            if (e.target.closest('#outletListContainer li')) {
                // Beri sedikit delay untuk memastikan localStorage sudah terupdate
                setTimeout(() => {
                    const newOutletId = localStorage.getItem('selectedOutletId') || currentOutletId;
                    loadProducts(newOutletId);
                }, 100);
            }
        });
    }

    async function preloadOutlets() {
        try {
            const response = await fetch("/api/outlets");
            const { data: outlets } = await response.json();

            // Create cache
            window.outletCache = {};
            outlets.forEach((outlet) => {
                window.outletCache[outlet.id] = outlet.name;
            });
        } catch (error) {
            console.error("Failed to preload outlets:", error);
        }
    }

    document
        .getElementById("outletDropdown")
        .addEventListener("change", function () {
            const outletId = this.value;
            const outletName = this.options[this.selectedIndex].text;
            updateOutletDisplay(outletId, outletName);
            loadProductData(outletId);
        });

    // Setup all modals
    const setupModals = () => {
        elements.modals.forEach((modalId) => {
            const modal = document.getElementById(modalId);
            if (!modal) return;

            modal.addEventListener("click", (e) => {
                if (e.target === modal) closeModal(modalId);
            });

            const modalContent = modal.querySelector("div[onclick]");
            if (modalContent) {
                modalContent.addEventListener("click", (e) =>
                    e.stopPropagation()
                );
            }
        });
    };

    // Create fetch interceptor for authentication
    const createAuthInterceptor = () => {
        const originalFetch = window.fetch;

        window.fetch = async (resource, options = {}) => {
            const token = localStorage.getItem("token");
            if (token) {
                options.headers = options.headers || {};
                options.headers.Authorization = `Bearer ${token}`;
                options.headers.Accept = "application/json";

                if (
                    !options.headers["Content-Type"] &&
                    !(options.body instanceof FormData)
                ) {
                    options.headers["Content-Type"] = "application/json";
                }

                options.headers["X-CSRF-TOKEN"] = document.querySelector(
                    'meta[name="csrf-token"]'
                ).content;
            }

            const response = await originalFetch(resource, options);

            if (options.headers?.Accept === "application/json") {
                const contentType = response.headers.get("content-type");
                if (contentType && contentType.includes("application/json")) {
                    return response;
                } else if (response.status !== 204) {
                    return new Response(
                        JSON.stringify({
                            message:
                                "Server did not return valid JSON response",
                        }),
                        {
                            status: 500,
                            headers: { "Content-Type": "application/json" },
                        }
                    );
                }
            }

            return response;
        };
    };

    // Setup event listeners
    const setupEventListeners = () => {

        document.addEventListener('click', (e) => {
            // Jika yang diklik bukan bagian dari dropdown
            if (!e.target.closest('.dropdown-menu') && !e.target.closest('[onclick*="toggleDropdown"]')) {
                document.querySelectorAll('.dropdown-menu').forEach(menu => {
                    menu.classList.add('hidden');
                });
            }
        });

        document.querySelectorAll('.dropdown-menu').forEach(menu => {
            menu.addEventListener('click', (e) => {
                e.stopPropagation(); // Mencegah event click menyebar ke document
            });
        });

        // fungsi untuk print dan export produk
        document
            .querySelector('[aria-label="Cetak laporan"]')
            ?.addEventListener("click", printProductReport);
        document
            .querySelector('[aria-label="Ekspor ke CSV"]')
            ?.addEventListener("click", exportProductsToCSV);
        // Form submission
        document
            .getElementById(elements.buttons.save)
            ?.addEventListener("click", (e) => {
                e.preventDefault();
                tambahProduk();
            });

        document
            .getElementById(elements.buttons.saveEdit)
            ?.addEventListener("click", (e) => {
                e.preventDefault();
                simpanPerubahanProduk();
            });

        // Cancel buttons
        document
            .getElementById(elements.buttons.cancel)
            ?.addEventListener("click", () => closeModal("modalTambahProduk"));
        document
            .getElementById(elements.buttons.cancelEdit)
            ?.addEventListener("click", () => closeModal("modalEditProduk"));
        document
            .getElementById(elements.buttons.cancelDelete)
            ?.addEventListener("click", () => {
                closeModal("modalKonfirmasiHapus");
                produkHapusId = null;
            });

        // Confirm delete button
        document
            .getElementById(elements.buttons.confirmDelete)
            ?.addEventListener("click", konfirmasiHapusProduk);

        // Search input
        document.getElementById(elements.inputs.search)?.addEventListener('input', (e) => {
            const searchTerm = e.target.value.toLowerCase();
            
            // Jika search kosong, load ulang produk outlet saat ini
            if (searchTerm.trim() === '') {
                loadProducts(currentOutletId);
            } else {
                filterProducts(searchTerm);
            }
        });

        // Unit type validation for quantity inputs
        const unitTypeSelect = document.getElementById('unitType');
        const stokInput = document.getElementById('stok');
        const editUnitTypeSelect = document.getElementById('editUnitType');
        const editStokInput = document.getElementById('editStok');

        if (unitTypeSelect && stokInput) {
            unitTypeSelect.addEventListener('change', function() {
                validateQuantityInput(stokInput, this.value);
            });
            stokInput.addEventListener('input', function() {
                validateQuantityInput(this, unitTypeSelect.value);
            });
        }

        if (editUnitTypeSelect && editStokInput) {
            editUnitTypeSelect.addEventListener('change', function() {
                validateQuantityInput(editStokInput, this.value);
            });
            editStokInput.addEventListener('input', function() {
                validateQuantityInput(this, editUnitTypeSelect.value);
            });
        }

        // Image preview handlers
        setupImagePreview(elements.inputs.image, "gambarPreview");
        setupImagePreview(elements.inputs.editImage, "editGambarPreview");
    };

    // Setup image preview for a given input
    const setupImagePreview = (inputId, previewId) => {
        document.getElementById(inputId)?.addEventListener("change", (e) => {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const preview = document.getElementById(previewId);
                    if (preview) {
                        preview.src = e.target.result;
                        preview.classList.remove("hidden");
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    };

    // Load initial data
    const loadInitialData = async () => {
        try {
            currentOutletId = getSelectedOutletId(); // Update dengan outlet yang dipilih
            await Promise.all([
                loadProducts(),
                loadKategoriOptions(),
                loadOutletCheckboxes(),
            ]);
        } catch (error) {
            showAlert("error", `Gagal memuat data awal: ${error.message}`);
        }
    };

    // Initialize Lucide icons
    const initLucideIcons = () => {
        if (window.lucide) window.lucide.createIcons();
    };

    // Show alert notification
    const showAlert = (type, message) => {
        const alertContainer = document.getElementById(
            elements.containers.alert
        );
        const alertId = `alert-${Date.now()}`;

        const alertConfig = {
            success: {
                bgColor: "bg-orange-50",
                borderColor: "border-orange-200",
                textColor: "text-orange-800",
                icon: "check-circle",
                iconColor: "text-orange-500",
            },
            error: {
                bgColor: "bg-red-50",
                borderColor: "border-red-200",
                textColor: "text-red-800",
                icon: "alert-circle",
                iconColor: "text-red-500",
            },
        };

        const config = alertConfig[type] || alertConfig.success;

        const alertElement = document.createElement("div");
        alertElement.id = alertId;
        alertElement.className = `p-4 border rounded-lg shadow-sm ${config.bgColor} ${config.borderColor} ${config.textColor} flex items-start gap-3 animate-fade-in-up`;
        alertElement.innerHTML = `
            <i data-lucide="${config.icon}" class="w-5 h-5 mt-0.5 ${config.iconColor}"></i>
            <div class="flex-1">
                <p class="text-sm font-medium">${message}</p>
            </div>
            <button onclick="ProductManager.closeAlert('${alertId}')" class="p-1 rounded-full hover:bg-gray-100">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        `;

        alertContainer.prepend(alertElement);
        initLucideIcons();

        setTimeout(() => closeAlert(alertId), 5000);
    };

    // Close alert
    const closeAlert = (id) => {
        const alert = document.getElementById(id);
        if (alert) {
            alert.classList.add("animate-fade-out");
            setTimeout(() => alert.remove(), 300);
        }
    };

    // Modal functions
    const openModal = (modalId) => {
        document.querySelectorAll('[id^="modal"]').forEach((modal) => {
            modal.classList.add("hidden");
            modal.classList.remove("flex");
        });

        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove("hidden");
            modal.classList.add("flex");
            document.body.style.overflow = "hidden";
        }
    };

    const closeModal = (modalId) => {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add("hidden");
            modal.classList.remove("flex");
            document.body.style.overflow = "";
        }
    };

    // Toggle dropdown menu
    const toggleDropdown = (button) => {
        const menu = button.nextElementSibling;
    
        // Tutup semua dropdown lainnya
        document.querySelectorAll(".dropdown-menu").forEach((m) => {
            if (m !== menu) {
                m.classList.add("hidden");
                m.classList.remove("dropdown-up", "dropdown-down");
            }
        });
    
        // Buka/tutup dropdown yang diklik
        menu.classList.toggle("hidden");
        menu.classList.remove("dropdown-up", "dropdown-down");
    
        const menuRect = menu.getBoundingClientRect();
        const buttonRect = button.getBoundingClientRect();
        const spaceBelow = window.innerHeight - buttonRect.bottom;
        const spaceAbove = buttonRect.top;
    
        if (spaceBelow < menuRect.height && spaceAbove > menuRect.height) {
            menu.classList.add("dropdown-up");
            menu.style.bottom = "100%";
            menu.style.marginBottom = "0.25rem";
            menu.style.top = "auto";
            menu.style.marginTop = "0";
        } else {
            menu.classList.add("dropdown-down");
            menu.style.top = "100%";
            menu.style.marginTop = "0.25rem";
            menu.style.bottom = "auto";
            menu.style.marginBottom = "0";
        }
    };

    // Load products from API
    async function loadProducts(outletId = currentOutletId) {
        try {
            currentOutletId = outletId;
            currentOutletName = await getOutletName(outletId);
            
            const response = await fetch(`/api/products/outlet/${outletId}`);
            const responseData = await response.json();
            
            renderProducts(responseData);
            updateOutletDisplay();
            
            // Simpan ke localStorage
            localStorage.setItem('selectedOutletId', outletId);
            localStorage.setItem('currentOutletName', currentOutletName);
            
        } catch (error) {
            console.error("Error loading products:", error);
            showAlert("error", "Gagal memuat data produk");
        }
    }

    // Render products to table
    const renderProducts = (responseData) => {
        const tbody = document.getElementById(elements.containers.tableBody);
        if (!tbody) return;

        tbody.innerHTML = "";

        const products = responseData?.data || [];

        if (products.length === 0) {
            tbody.innerHTML = `
                <tr class="border-b">
                    <td colspan="8" class="py-4 text-center text-gray-500">
                        Tidak ada data produk
                    </td>
                </tr>
            `;
            return;
        }

        products.forEach((product, index) => {
            // Handle inventory data - several possible structures
            let quantity = 0;
            let min_stock = 0;

            // Case 1: Inventory data in nested object
            if (product.inventory) {
                quantity = product.inventory.quantity || 0;
                min_stock = product.inventory.min_stock || 0;
            }
            // Case 2: Direct properties
            else if (product.quantity !== undefined) {
                quantity = product.quantity;
                min_stock = product.min_stock || 0;
            }
            // Case 3: Inventory array
            else if (
                Array.isArray(product.inventories) &&
                product.inventories.length > 0
            ) {
                const mainInventory =
                    product.inventories.find(
                        (inv) => inv.outlet_id == currentOutletId
                    ) || product.inventories[0];
                quantity = mainInventory.quantity || 0;
                min_stock = mainInventory.min_stock || 0;
            }
                
            const row = document.createElement("tr");
            row.className = "border-b hover:bg-gray-50";
            row.innerHTML = `
                <td class="py-3 px-4">${index + 1}</td>
                <td class="py-3 px-4 flex items-center space-x-3">
                    <img src="${
                        product.image_url || "/images/default-product.png"
                    }" 
                        alt="${product.name}" 
                        class="w-10 h-10 bg-gray-100 rounded object-cover" />
                    <div>
                        <p class="font-medium">${product.name || "-"}</p>
                    </div>
                </td>
                <td class="py-3 px-4">
                    ${product.barcode ? 
                        `<svg class="barcode" 
                            jsbarcode-format="CODE128"
                            jsbarcode-value="${product.barcode}"
                            jsbarcode-width="1.5"
                            jsbarcode-height="30"
                            jsbarcode-fontSize="8"
                            jsbarcode-displayValue="true"></svg>` 
                        : '-'}
                </td>
                <td class="py-3 px-4">${product.sku || "-"}</td>
                <td class="py-3 px-4">
                    <span class="inline-block px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">
                        ${product.category?.name || "Tanpa Kategori"}
                    </span>
                </td>
                <td class="py-3 px-4">Rp ${formatNumber(
                    product.price || 0
                )}</td>
                <td class="py-3 px-4">
                    <span class="inline-block px-2 py-1 bg-gray-100 text-gray-800 rounded text-xs">
                        ${product.unit_type || '-'}
                    </span>
                </td>
                <td class="py-3 px-4">
                    <div class="flex flex-col">
                        <span class="font-medium">${formatQuantityBasedOnUnit(quantity, product.unit_type)}</span>
                        <div class="text-xs text-gray-500">
                            <span>Min: ${formatQuantityBasedOnUnit(min_stock, product.unit_type)}</span><br>
                        </div>
                    </div>
                </td>
                <td class="py-3 px-4">
                    <span class="px-2.5 py-1 text-xs font-medium rounded-full ${
                        product.is_active
                            ? "bg-green-100 text-green-800"
                            : "bg-gray-100 text-gray-800"
                    }">
                        ${product.is_active ? "Aktif" : "Nonaktif"}
                    </span>
                </td>
                <td class="py-3 px-4 relative">
                    <div class="relative inline-block">
                        <button onclick="ProductManager.toggleDropdown(this)" 
                                class="p-2 hover:bg-gray-100 rounded-lg">
                            <i data-lucide="more-vertical" class="w-5 h-5 text-gray-500"></i>
                        </button>
                        <div class="dropdown-menu hidden absolute right-0 z-20 mt-1 w-40 bg-white border border-gray-200 rounded-lg shadow-xl text-sm">
                            <button onclick="ProductManager.openEditModal(${product.id})" 
                                    class="flex items-center w-full px-3 py-2 hover:bg-gray-100 text-left rounded-t-lg">
                                <i data-lucide="edit" class="w-4 h-4 mr-2 text-gray-500"></i> Edit
                            </button>
                            <button onclick="BarcodePrinter.print('${product.barcode || ''}', '${product.name || ''}')" 
                                class="flex items-center w-full px-3 py-2 hover:bg-gray-100 text-left">
                                <i data-lucide="barcode" class="w-4 h-4 mr-2 text-gray-500"></i> Cetak Barcode
                            </button>
                            <button onclick="ProductManager.hapusProduk(${product.id})" 
                                    class="flex items-center w-full px-3 py-2 hover:bg-gray-100 text-left text-red-600 rounded-b-lg">
                                <i data-lucide="trash-2" class="w-4 h-4 mr-2"></i> Hapus
                            </button>
                        </div>
                    </div>
                </td>
            `;
            tbody.appendChild(row);
        });
        
        // Initialize all barcodes after DOM is updated
        JsBarcode(".barcode").init();

        if (window.lucide) window.lucide.createIcons();
    };

    // Format number for currency display
    const formatNumber = (num) => new Intl.NumberFormat("id-ID").format(num);

    // Format quantity based on unit type
    const formatQuantityBasedOnUnit = (quantity, unitType) => {
        if (!quantity && quantity !== 0) return '-';
        
        const numValue = parseFloat(quantity);
        
        if (unitType === 'meter') {
            // For meter, show decimal if exists, otherwise show as integer
            return numValue % 1 === 0 ? numValue.toString() : numValue.toFixed(1);
        } else {
            // For pcs and unit, always show as integer
            return Math.floor(numValue).toString();
        }
    };

    // Validate quantity input based on unit type
    const validateQuantityInput = (input, unitType) => {
        if (!input || !unitType) return;
        
        const value = input.value;
        
        if (unitType === 'meter') {
            // Allow decimal for meter
            input.step = '0.1';
            input.pattern = '[0-9]+(\\.[0-9]+)?';
            // Remove any existing validation messages
            input.setCustomValidity('');
        } else {
            // Only integers for pcs and unit
            input.step = '1';
            input.pattern = '[0-9]+';
            
            // Check if current value has decimal
            if (value && value.includes('.')) {
                input.setCustomValidity('Untuk satuan pcs/unit, hanya angka bulat yang diperbolehkan');
                input.value = Math.floor(parseFloat(value));
            } else {
                input.setCustomValidity('');
            }
        }
        
        // Report validity
        input.reportValidity();
    };

    // Filter products by search term
    const filterProducts = async (searchTerm) => {
        try {
            const outletId = currentOutletId; // Gunakan outlet yang aktif
            
            // Ambil data produk hanya untuk outlet yang aktif
            const response = await fetch(`/api/products/outlet/${outletId}`);
            if (!response.ok) throw new Error("Gagal memuat data produk");
    
            const { data: products } = await response.json();
    
            const filtered = products.filter(product => 
                product.name.toLowerCase().includes(searchTerm) ||
                (product.description && product.description.toLowerCase().includes(searchTerm)) ||
                (product.sku && product.sku.toLowerCase().includes(searchTerm))
            );
    
            renderProducts({ data: filtered });
        } catch (error) {
            showAlert("error", error.message);
        }
    };

    // Add new product
    const tambahProduk = async () => {
        const btnSimpan = document.getElementById(elements.buttons.save);
        const originalText = btnSimpan.innerHTML;
        const form = document.getElementById(elements.forms.add);

        try {
            // Set loading state
            btnSimpan.disabled = true;
            btnSimpan.innerHTML = '<i data-lucide="loader-circle" class="animate-spin mr-2"></i> Menyimpan...';
            if (window.lucide) window.lucide.createIcons();

            // Prepare form data
            const formData = new FormData(form);
            !formData.get("barcode") && formData.set("barcode", generateBarcode());
            !formData.get("sku") && formData.set("sku", `SKU-${Date.now()}`);
            !formData.get("quantity") && formData.set("quantity", "0");
            !formData.get("min_stock") && formData.set("min_stock", "0");
            formData.append("outlet_id", currentOutletId.toString());

            const response = await fetch("/api/products", {
                method: "POST",
                body: formData,
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                    "Authorization": `Bearer ${localStorage.getItem("token")}`
                }
            });

            const data = await response.json();

            if (!response.ok) {
                let errorMessage = "Terjadi kesalahan";

                if (typeof data.message === 'string') {
                    errorMessage = data.message;
                } 
                else if (typeof data.error === 'string') {
                    errorMessage = data.error;
                }
                else if (data.message && typeof data.message === 'object') {
                    // Mapping field dan error ke bahasa Indonesia
                    const fieldLabels = {
                        name: "Nama Produk",
                        price: "Harga",
                        category_id: "Kategori",
                        outlet_ids: "Outlet",
                        barcode: "Barcode",
                        sku: "SKU",
                        quantity: "Jumlah",
                        min_stock: "Stok Minimal"
                    };

                    const translateError = (msg) => {
                        if (msg.includes("required")) return "wajib diisi.";
                        if (msg.includes("already been taken")) return "sudah digunakan.";
                        if (msg.includes("must be a number")) return "harus berupa angka.";
                        return msg;
                    };

                    const messages = Object.entries(data.message).map(([field, errors]) => {
                        const label = fieldLabels[field] || field;
                        const translatedErrors = errors.map(translateError).join(", ");
                        return `${label}: ${translatedErrors}`;
                    });

                    errorMessage = messages.join("<br>");
                } 
                else if (data.errors) {
                    errorMessage = Object.values(data.errors).flat().join(', ');
                }

                throw new Error(errorMessage);
            }

            // Success
            showAlert("success", data.message || "Produk berhasil ditambahkan");
            closeModal("modalTambahProduk");
            loadProducts();
            form.reset();
            
            const preview = document.getElementById("gambarPreview");
            preview && (preview.src = "") && preview.classList.add("hidden");

        } catch (error) {
            console.error("Error:", error);
            
            let userMessage = "Terjadi kesalahan saat menyimpan produk";
            
            if (error instanceof TypeError) {
                userMessage = "Gagal terhubung ke server";
            } 
            else if (error instanceof Error) {
                userMessage = error.message;
            }
            
            showAlert("error", userMessage);
            
        } finally {
            btnSimpan.disabled = false;
            btnSimpan.innerHTML = originalText;
            window.lucide?.createIcons();
        }
    };

    // Open edit modal with product data
    const openEditModal = async (productId) => {
        const btnEdit = document.querySelector(
            `[onclick="ProductManager.openEditModal(${productId})"]`
        );
        const originalText = btnEdit?.innerHTML;
    
        try {
            if (btnEdit) {
                btnEdit.disabled = true;
                btnEdit.innerHTML = '<i data-lucide="loader-circle" class="animate-spin w-4 h-4"></i>';
                if (window.lucide) window.lucide.createIcons();
            }
    
            const token = localStorage.getItem("token");
            if (!token) throw new Error("Token tidak ditemukan");
    
            // console.log("=== DEBUG: Opening edit modal for product ID:", productId);
    
            // Gunakan endpoint detail
            const productDetailResponse = await fetch(`/api/products/${productId}/detail`, {
                headers: {
                    Authorization: `Bearer ${token}`,
                    Accept: "application/json",
                },
            });
            
            if (!productDetailResponse.ok) {
                throw new Error(`Failed to fetch product detail: ${productDetailResponse.status}`);
            }
    
            const productDetailData = await productDetailResponse.json();
            const product = productDetailData.data;
    
            // console.log("=== DEBUG: Raw product data from API:", product);
            // console.log("=== DEBUG: Product outlets:", product.outlets);
            // console.log("=== DEBUG: Product outlet_ids:", product.outlet_ids);
    
            if (!product) throw new Error("Produk tidak ditemukan");
    
            // Handle inventory data - prioritize outlet pivot data for current outlet
            let quantity = 0;
            let min_stock = 0;
    
            // Case 1: Get data from outlets array based on current outlet
            if (Array.isArray(product.outlets) && product.outlets.length > 0) {
                const currentOutletData = product.outlets.find(
                    outlet => outlet.id == currentOutletId
                );
                
                if (currentOutletData && currentOutletData.pivot) {
                    quantity = currentOutletData.pivot.quantity || 0;
                    min_stock = currentOutletData.pivot.min_stock || 0;
                } else {
                    // Fallback to first outlet if current outlet not found
                    const firstOutlet = product.outlets[0];
                    if (firstOutlet && firstOutlet.pivot) {
                        quantity = firstOutlet.pivot.quantity || 0;
                        min_stock = firstOutlet.pivot.min_stock || 0;
                    }
                }
            }
            // Case 2: Fallback to inventory data if outlets not available
            else if (product.inventory) {
                quantity = product.inventory.quantity || 0;
                min_stock = product.inventory.min_stock || 0;
            }
            // Case 3: Fallback to direct properties
            else if (product.quantity !== undefined) {
                quantity = product.quantity;
                min_stock = product.min_stock || 0;
            }
            // Case 4: Fallback to inventories array
            else if (
                Array.isArray(product.inventories) &&
                product.inventories.length > 0
            ) {
                const mainInventory =
                    product.inventories.find(
                        (inv) => inv.outlet_id == currentOutletId
                    ) || product.inventories[0];
                quantity = mainInventory.quantity || 0;
                min_stock = mainInventory.min_stock || 0;
            }
    
            // Populate form fields
            document.getElementById("editProdukId").textContent = product.id;
            document.getElementById("editNamaProduk").value = product.name;
            document.getElementById('editBarcode').value = product.barcode || '';
            document.getElementById("editSkuProduk").value = product.sku || "";
            document.getElementById("editDeskripsi").value = product.description || "";
            document.getElementById("editHarga").value = product.price;
            document.getElementById("editStok").value = quantity; // Use calculated quantity from outlet pivot
            document.getElementById("editStokMinimum").value = min_stock; // Use calculated min_stock from outlet pivot
            document.getElementById("editGambarCurrent").value = product.image || "";
    
            await loadKategoriOptions();
    
            if (product.category && product.category.id) {
                const categorySelect = document.getElementById("editKategori");
                if (categorySelect && categorySelect.options.length > 0) {
                    categorySelect.value = product.category.id;
                }
            }

            // Set unit type
            const unitTypeSelect = document.getElementById("editUnitType");
            if (unitTypeSelect) {
                unitTypeSelect.value = product.unit_type || 'pcs';
            }
    
            document.getElementById("editStatus").value = product.is_active ? "active" : "inactive";
    
            const preview = document.getElementById("editGambarPreview");
            if (preview) {
                if (product.image_url) {
                    preview.src = product.image_url;
                    preview.classList.remove("hidden");
                } else {
                    preview.classList.add("hidden");
                }
            }
    
            // Ambil outlet IDs yang dipilih
            let selectedOutletIds = [];
            if (product.outlet_ids && Array.isArray(product.outlet_ids)) {
                selectedOutletIds = product.outlet_ids.map(id => id.toString());
            }
    
            // console.log("=== DEBUG: Selected outlet IDs untuk checkbox:", selectedOutletIds);
    
            await loadOutletCheckboxesForEdit(selectedOutletIds);
            openModal("modalEditProduk");
            
        } catch (error) {
            // console.error("=== ERROR: Failed to open edit modal:", error);
            showAlert("error", `Gagal memuat produk: ${error.message}`);
    
            if (error.message.includes("token") || error.message.includes("401")) {
                window.location.href = "/login";
            }
        } finally {
            if (btnEdit) {
                btnEdit.disabled = false;
                btnEdit.innerHTML = originalText;
                if (window.lucide) window.lucide.createIcons();
            }
        }
    };
    
    // Load outlet checkboxes for edit modal
    const loadOutletCheckboxesForEdit = async (selectedOutletIds = []) => {
        try {
            // console.log("=== DEBUG loadOutletCheckboxesForEdit: Input selectedOutletIds:", selectedOutletIds);
            
            const token = localStorage.getItem("token");
            const response = await fetch("/api/outlets", {
                headers: {
                    Authorization: `Bearer ${token}`,
                    Accept: "application/json",
                },
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const responseData = await response.json();
            const outlets = responseData.data || responseData;
            const container = document.getElementById("editOutletList");
    
            // console.log("=== DEBUG: All outlets from API:", outlets);
    
            if (!container) return;
    
            container.innerHTML = "";
    
            // Pastikan selectedOutletIds adalah array of strings
            const selectedIds = Array.isArray(selectedOutletIds)
                ? selectedOutletIds.map((id) => id.toString())
                : [];
    
            // console.log("=== DEBUG: Processed selectedIds:", selectedIds);
    
            outlets.forEach((outlet) => {
                const outletIdStr = outlet.id.toString();
                const isChecked = selectedIds.includes(outletIdStr);
    
                // console.log(`=== DEBUG: Outlet ${outlet.name} (ID: ${outletIdStr}) - Should be checked: ${isChecked}`);
    
                const div = document.createElement("div");
                div.className = "flex items-center gap-2 py-1";
    
                const checkbox = document.createElement("input");
                checkbox.type = "checkbox";
                checkbox.name = "outlet_ids[]";
                checkbox.value = outletIdStr;
                checkbox.id = `edit-outlet-${outletIdStr}`;
                checkbox.className =
                    "w-4 h-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500";
                checkbox.checked = isChecked;
    
                const label = document.createElement("label");
                label.htmlFor = `edit-outlet-${outletIdStr}`;
                label.className = "text-sm text-gray-700";
                label.textContent = outlet.name;
    
                div.appendChild(checkbox);
                div.appendChild(label);
                container.appendChild(div);
            });
    
            // console.log(`=== DEBUG: Loaded ${outlets.length} outlets, ${selectedIds.length} should be selected`);
            
            // Verifikasi hasil
            // const checkedBoxes = container.querySelectorAll('input[type="checkbox"]:checked');
            // console.log("=== DEBUG: Actually checked checkboxes:", checkedBoxes.length);
            // checkedBoxes.forEach(cb => console.log("=== DEBUG: Checked outlet ID:", cb.value));
            
        } catch (error) {
            // console.error("=== ERROR: Error loading outlets:", error);
            const container = document.getElementById("editOutletList");
            if (container) {
                container.innerHTML = `
                    <div class="text-red-500 text-sm py-2">
                        Gagal memuat daftar outlet: ${error.message}
                    </div>
                `;
            }
        }
    };

    // Save product changes
    const simpanPerubahanProduk = async () => {
        const btnSimpan = document.getElementById("btnSimpanEdit");
        const originalText = btnSimpan.innerHTML;
    
        try {
            btnSimpan.disabled = true;
            btnSimpan.innerHTML =
                '<i data-lucide="loader-circle" class="animate-spin mr-2"></i> Menyimpan...';
            if (window.lucide) window.lucide.createIcons();
    
            const id = document.getElementById("editProdukId").textContent;
            const formData = new FormData();
    
            const namaProduk = document.getElementById("editNamaProduk").value.trim();
            const barcodeValue = document.getElementById('editBarcode').value.trim();
            if (!barcodeValue) {
                formData.append("barcode", generateBarcode());
            } else {
                formData.append("barcode", barcodeValue);
            }
            const harga = document.getElementById("editHarga").value.trim();
            const kategori = document.getElementById("editKategori").value.trim();
            const quantity = document.getElementById("editStok").value || 0;
            const minStock = document.getElementById("editStokMinimum").value || 0;
    
            if (!namaProduk) throw new Error("Nama produk harus diisi");
            if (!harga) throw new Error("Harga harus diisi");
            if (!kategori) throw new Error("Kategori harus dipilih");
    
            formData.append("name", namaProduk);
            formData.append("sku", document.getElementById("editSkuProduk").value.trim() || `SKU-${Date.now()}`);
            formData.append("description", document.getElementById("editDeskripsi").value);
            formData.append("price", harga);
            formData.append("category_id", kategori);
            formData.append("unit_type", document.getElementById("editUnitType").value || 'meter');
            formData.append("is_active", document.getElementById("editStatus").value === "active" ? 1 : 0);
            formData.append("min_stock", minStock);
            formData.append("outlet_id", currentOutletId.toString());
    
            const selectedOutlets = [];
            const outletCheckboxes = document.querySelectorAll(
                '#editOutletList input[type="checkbox"]:checked'
            );
    
            if (outletCheckboxes && outletCheckboxes.length > 0) {
                outletCheckboxes.forEach((checkbox) => {
                    selectedOutlets.push(checkbox.value);
                });
            } else {
                const outletInputs = document.querySelectorAll(
                    '#editOutletList input[type="hidden"][name*="outlet"]'
                );
                outletInputs.forEach((input) => {
                    if (input.value) {
                        selectedOutlets.push(input.value);
                    }
                });
            }
    
            if (selectedOutlets.length === 0) {
                const outletElements = document.querySelectorAll(
                    "#editOutletList [data-outlet-id]"
                );
                outletElements.forEach((el) => {
                    selectedOutlets.push(el.dataset.outletId);
                });
    
                if (selectedOutlets.length === 0) {
                    selectedOutlets.push("1");
                }
            }
    
            selectedOutlets.forEach((outletId) => {
                formData.append("outlet_ids[]", outletId);
            });
    
            const imageInput = document.getElementById("editGambar");
            if (imageInput.files[0]) {
                formData.append("image", imageInput.files[0]);
            }
    
            const response = await fetch(`/api/products/${id}`, {
                method: "POST",
                body: formData,
                headers: {
                    "X-CSRF-TOKEN": document.querySelector(
                        'meta[name="csrf-token"]'
                    ).content,
                    Authorization: `Bearer ${localStorage.getItem("token")}`,
                },
            });
    
            const contentType = response.headers.get("content-type");
            let responseData;
    
            if (contentType && contentType.includes("application/json")) {
                responseData = await response.json();
            } else {
                const textData = await response.text();
                throw new Error(
                    `Server returned invalid response: ${textData.substring(
                        0,
                        100
                    )}`
                );
            }
    
            if (!response.ok) {
                if (response.status === 422) {
                    let errorMessage = " ";
                    
                    // Handle different error response formats
                    if (responseData.message && typeof responseData.message === 'object') {
                        // Format: {"message": {"field": ["error"]}}
                        for (const [field, errors] of Object.entries(responseData.message)) {
                            if (field === 'sku') {
                                errorMessage += "\nSKU: sudah digunakan.";
                            } else if (field === 'barcode') {
                                errorMessage += "\nBarcode: sudah digunakan.";
                            } else if (Array.isArray(errors)) {
                                errorMessage += `\n${errors.join(", ")}`;
                            }
                        }
                    } else if (responseData.errors) {
                        // Format: {"errors": {"field": ["error"]}}
                        for (const [field, errors] of Object.entries(responseData.errors)) {
                            if (field === 'sku') {
                                errorMessage += "\nSKU: sudah digunakan.";
                            } else if (field === 'barcode') {
                                errorMessage += "\nBarcode: sudah digunakan.";
                            } else if (Array.isArray(errors)) {
                                errorMessage += `\n${errors.join(", ")}`;
                            }
                        }
                    } else {
                        errorMessage = responseData.message || "Validasi gagal";
                    }
                    
                    throw new Error(errorMessage);
                }
                throw new Error(
                    responseData.message || "Gagal memperbarui produk"
                );
            }
    
            showAlert("success", "Produk berhasil diperbarui");
            closeModal("modalEditProduk");
            loadProducts();
        } catch (error) {
            showAlert("error", error.message || "Terjadi kesalahan");
        } finally {
            btnSimpan.disabled = false;
            btnSimpan.innerHTML = originalText;
            if (window.lucide) window.lucide.createIcons();
        }
    };

    // Load kategori options
    const loadKategoriOptions = async (callback) => {
        try {
            const response = await fetch("/api/categories");
            if (!response.ok) throw new Error("Gagal memuat kategori");

            const { data: categories } = await response.json();

            if (!Array.isArray(categories)) {
                throw new Error(
                    "Data kategori tidak valid. Harus berupa array."
                );
            }

            const selectTambah = document.getElementById("kategori");
            if (selectTambah) {
                selectTambah.innerHTML =
                    '<option value="">Pilih Kategori</option>';
                categories.forEach((category) => {
                    const option = document.createElement("option");
                    option.value = category.id;
                    option.textContent = category.name;
                    selectTambah.appendChild(option);
                });
            }

            const selectEdit = document.getElementById("editKategori");
            if (selectEdit) {
                selectEdit.innerHTML =
                    '<option value="">Pilih Kategori</option>';
                categories.forEach((category) => {
                    const option = document.createElement("option");
                    option.value = category.id.toString();
                    option.textContent = category.name;
                    selectEdit.appendChild(option);
                });
            }

            if (callback && typeof callback === "function") {
                callback();
            }
        } catch (error) {
            showAlert("error", "Gagal memuat daftar kategori");
        }
    };

    // Load outlet checkboxes
    const loadOutletCheckboxes = async () => {
        try {
            const response = await fetch("/api/outlets");
            const { data: outlets } = await response.json();
            const container = document.getElementById(
                elements.containers.outletCheckboxes
            );

            if (!container) return;

            container.innerHTML = "";

            outlets.forEach((outlet) => {
                const div = document.createElement("div");
                div.className = "flex items-center gap-2 mb-2";

                const checkbox = document.createElement("input");
                checkbox.type = "checkbox";
                checkbox.name = "outlet_ids[]";
                checkbox.value = outlet.id;
                checkbox.id = `outlet-${outlet.id}`;
                checkbox.className =
                    "w-4 h-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500";

                const label = document.createElement("label");
                label.htmlFor = `outlet-${outlet.id}`;
                label.className = "text-sm text-gray-700";
                label.textContent = outlet.name;

                div.appendChild(checkbox);
                div.appendChild(label);
                container.appendChild(div);
            });
        } catch (error) {
            showAlert("error", "Gagal memuat daftar outlet");
        }
    };

    // Prepare delete confirmation
    const hapusProduk = async (id) => {
        try {
            const token = localStorage.getItem("token");
            if (!token) throw new Error("Token tidak ditemukan");

            const response = await fetch(
                `/api/products/outlet/${currentOutletId}`,
                {
                    headers: {
                        Authorization: `Bearer ${token}`,
                        Accept: "application/json",
                    },
                }
            );

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(
                    errorData.message || `Error: ${response.status}`
                );
            }

            const responseData = await response.json();
            const product = responseData.data;

            if (!product) throw new Error("Data produk tidak valid");

            produkHapusId = id;
            document.getElementById("hapusNamaProduk").textContent =
                product.name;
            openModal("modalKonfirmasiHapus");
        } catch (error) {
            showAlert("error", `Gagal memuat produk: ${error.message}`);

            if (
                error.message.includes("token") ||
                error.message.includes("401")
            ) {
                window.location.href = "/login";
            }
        }
    };

    // Confirm product deletion
    const konfirmasiHapusProduk = async () => {
        try {
            const token = localStorage.getItem("token");
            if (!token) throw new Error("Token tidak ditemukan");

            const response = await fetch(`/api/products/${produkHapusId}`, {
                method: "DELETE",
                headers: {
                    Authorization: `Bearer ${token}`,
                    Accept: "application/json",
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector(
                        'meta[name="csrf-token"]'
                    ).content,
                },
            });

            const data = await response.json();

            if (!response.ok)
                throw new Error(data.message || "Gagal menghapus produk");

            showAlert("success", "Produk berhasil dihapus");
            closeModal("modalKonfirmasiHapus");
            loadProducts();
            produkHapusId = null;
        } catch (error) {
            showAlert("error", `Gagal menghapus produk: ${error.message}`);
        }
    };

    // Fungsi untuk mencetak laporan produk
    const printProductReport = async () => {
        try {
            const outletId = currentOutletId;
            const outletName = await getOutletName(outletId);
            const currentDate = new Date().toLocaleDateString("id-ID", {
                day: "numeric",
                month: "long",
                year: "numeric",
            });

            // Ambil data produk
            const response = await fetch(`/api/products/outlet/${outletId}`);
            if (!response.ok) throw new Error(`Error: ${response.status}`);

            const responseData = await response.json();
            const products = responseData?.data || [];

            // Buat konten HTML untuk print
            const printContent = `
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Laporan Produk - ${outletName}</title>
                    <style>
                        body { font-family: Arial, sans-serif; margin: 20px; }
                        .header { display: flex; align-items: center; margin-bottom: 10px; }
                        .logo { height: 50px; margin-right: 15px; }
                        .title { flex-grow: 1; }
                        h1 { font-size: 18px; margin-bottom: 5px; }
                        h2 { font-size: 16px; margin-top: 0; color: #555; }
                        hr { border: 0.5px solid #eee; margin: 10px 0; }
                        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
                        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                        th { background-color: #f2f2f2; }
                        .footer { margin-top: 20px; font-size: 12px; text-align: center; color: #777; }
                        @page { size: auto; margin: 10mm; }
                        @media print {
                            body { margin: 0; padding: 0; }
                            .no-print { display: none !important; }
                        }
                    </style>
                </head>
                <body>
                    <div class="header">
                        <img src="/images/logo.png" class="logo" alt="Logo">
                        <div class="title">
                            <h1>LAPORAN PRODUK</h1>
                            <h2>Outlet: ${outletName}</h2>
                            <h2>Dicetak pada: ${currentDate}</h2>
                        </div>
                    </div>
                    <hr>
                    
                    <p><strong>Total Produk:</strong> ${products.length}</p>
                    
                    <table>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>SKU</th>
                                <th>Nama Produk</th>
                                <th>Kategori</th>
                                <th>Stok</th>
                                <th>Min-Stok</th>
                                <th>Harga</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${products
                                .map((product, index) => {
                                    const stok =
                                        product.quantity ||
                                        product.inventory?.quantity ||
                                        product.stock?.quantity ||
                                        0;
                                    const minStok =
                                        product.min_stock ||
                                        product.inventory?.min_stock ||
                                        product.stock?.min_stock ||
                                        0;

                                    return `
                                    <tr>
                                        <td>${index + 1}</td>
                                        <td>${product.sku || "-"}</td>
                                        <td>${product.name || "-"}</td>
                                        <td>${
                                            product.category?.name ||
                                            "Tanpa Kategori"
                                        }</td>
                                        <td>${formatQuantityBasedOnUnit(stok, product.unit_type)}</td>
                                        <td>${formatQuantityBasedOnUnit(minStok, product.unit_type)}</td>
                                        <td>Rp ${formatNumber(
                                            product.price || 0
                                        )}</td>
                                        <td>${
                                            product.is_active
                                                ? "Aktif"
                                                : "Nonaktif"
                                        }</td>
                                    </tr>
                                `;
                                })
                                .join("")}
                        </tbody>
                    </table>
                    
                    <div class="footer">
                        Laporan ini dibuat otomatis oleh Sistem Manajemen Penjualan<br>
                        © ${new Date().getFullYear()} Aladdin Karpet
                    </div>
    
                    <div class="no-print" style="text-align: center; margin-top: 20px;">
                        <button onclick="window.print()" style="padding: 8px 16px; background: #4CAF50; color: white; border: none; border-radius: 4px; cursor: pointer;">
                            Cetak Sekarang
                        </button>
                        <button onclick="window.close()" style="padding: 8px 16px; background: #f44336; color: white; border: none; border-radius: 4px; cursor: pointer; margin-left: 10px;">
                            Tutup
                        </button>
                    </div>
    
                    <script>
                        // Auto print dan close setelah cetak selesai
                        window.onload = function() {
                            setTimeout(function() {
                                window.print();
                            }, 200);
                        };
    
                        window.onafterprint = function() {
                            setTimeout(function() {
                                window.close();
                            }, 200);
                        };
                    </script>
                </body>
                </html>
            `;

            // Buat iframe untuk print langsung
            const iframe = document.createElement("iframe");
            iframe.style.position = "absolute";
            iframe.style.width = "1px";
            iframe.style.height = "1px";
            iframe.style.left = "-9999px";
            iframe.style.top = "0";
            iframe.style.border = "none";
            document.body.appendChild(iframe);

            // Tulis konten ke iframe
            iframe.contentDocument.write(printContent);
            iframe.contentDocument.close();
        } catch (error) {
            showAlert("error", `Gagal mencetak laporan: ${error.message}`);
        }
    };

    // Fungsi untuk mengekspor data produk ke CSV
    const exportProductsToCSV = async () => {
        try {
            const outletId = currentOutletId;
            const outletName = await getOutletName(outletId);
            const currentDate = new Date().toLocaleString("id-ID", {
                day: "numeric",
                month: "long",
                year: "numeric",
                hour: "2-digit",
                minute: "2-digit",
            });

            // Ambil data produk
            const response = await fetch(`/api/products/outlet/${outletId}`);
            if (!response.ok) throw new Error(`Error: ${response.status}`);

            const responseData = await response.json();
            const products = responseData?.data || [];

            // Buat header CSV
            let csvContent = "LAPORAN PRODUK BERDASARKAN RENTANG TANGGAL\n";
            csvContent += `Outlet: ${outletName}\n`;
            csvContent += `Tanggal Ekspor: ${currentDate}\n\n`;
            csvContent += "DATA PRODUK\n";
            csvContent +=
                "No,SKU,Nama produk,Kategori,Min-Stok,Stok,Harga,Status\n";

            // Tambahkan data produk
            products.forEach((product, index) => {
                // Cek struktur data untuk stok dan min-stok
                const stok =
                    product.quantity ||
                    product.inventory?.quantity ||
                    product.stock?.quantity ||
                    0;
                const minStok =
                    product.min_stock ||
                    product.inventory?.min_stock ||
                    product.stock?.min_stock ||
                    0;

                csvContent += `${index + 1},"${product.sku || ""}","${
                    product.name || ""
                }","${
                    product.category?.name || "Tanpa Kategori"
                }","${formatQuantityBasedOnUnit(minStok, product.unit_type)}","${formatQuantityBasedOnUnit(stok, product.unit_type)}","Rp ${formatNumber(
                    product.price || 0
                )}","${product.is_active ? "Aktif" : "Nonaktif"}"\n`;
            });

            // Buat blob dan download
            const blob = new Blob([csvContent], {
                type: "text/csv;charset=utf-8;",
            });
            const url = URL.createObjectURL(blob);
            const link = document.createElement("a");
            link.setAttribute("href", url);
            link.setAttribute(
                "download",
                `Laporan_Produk_${outletName.replace(/\s+/g, "_")}_${new Date()
                    .toISOString()
                    .slice(0, 10)}.csv`
            );
            link.style.visibility = "hidden";
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        } catch (error) {
            showAlert("error", `Gagal mengekspor data: ${error.message}`);
        }
    };

    // Public API
    return {
        init,
        showAlert,
        closeAlert,
        openModal,
        closeModal,
        toggleDropdown,
        openEditModal,
        hapusProduk,
        konfirmasiHapusProduk,
        printProductReport,
        exportProductsToCSV,
    };
})();

// Initialize when DOM is loaded
document.addEventListener("DOMContentLoaded", ProductManager.init);
