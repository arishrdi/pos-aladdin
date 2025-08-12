@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<!-- Outlet Info -->
<div class="mb-6">
    <div class="mb-6 bg-white rounded-lg p-4 card-shadow flex items-center justify-between">
        <!-- Left side - Outlet info with icon -->
        <div class="flex items-center space-x-4">
            <!-- Store icon -->
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-store">
                <path d="m2 7 4.41-4.41A2 2 0 0 1 7.83 2h8.34a2 2 0 0 1 1.42.59L22 7"/>
                <path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"/>
                <path d="M15 22v-4a2 2 0 0 0-2-2h-2a2 2 0 0 0-2 2v4"/>
                <path d="M2 7h20"/>
                <path d="M22 7v3a2 2 0 0 1-2 2v0a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 16 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 12 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 8 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 4 12v0a2 2 0 0 1-2-2V7"/>
            </svg>
            
            <div>
                <h2 class="text-lg font-semibold text-gray-800" id="outletName">Loading...</h2>
                <p class="text-sm text-gray-600 mt-1">Data yang ditampilkan adalah untuk outlet <span id="outletNameText">loading</span>.</p>
            </div>
        </div>
        
        <!-- Right side - Controls -->
        <div class="flex items-center space-x-4">
            <!-- Comparison Mode Toggle -->
            <div class="flex items-center space-x-2">
                <span class="text-sm text-gray-600">Mode Komparasi</span>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" id="comparisonMode" class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
                </label>
            </div>
            
            <!-- Date range picker -->
            <div class="relative">
                <button id="dateRangeButton" class="flex items-center space-x-2 bg-gray-50 hover:bg-gray-100 px-3 py-2 rounded-lg transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar-days">
                    <path d="M8 2v4"/>
                    <path d="M16 2v4"/>
                    <rect width="18" height="18" x="3" y="4" rx="2"/>
                    <path d="M3 10h18"/>
                    <path d="M8 14h.01"/>
                    <path d="M12 14h.01"/>
                    <path d="M16 14h.01"/>
                    <path d="M8 18h.01"/>
                    <path d="M12 18h.01"/>
                    <path d="M16 18h.01"/>
                </svg>
                <span id="dateRangeDisplay" class="text-sm font-medium text-gray-800">Loading...</span>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-down">
                    <path d="m6 9 6 6 6-6"/>
                </svg>
            </button>
            
            <!-- Date picker dropdown -->
            <div id="datePickerDropdown" class="hidden absolute right-0 mt-2 w-64 bg-white rounded-lg shadow-lg border border-gray-200 z-10 p-4">
                <div class="flex justify-between items-center mb-4">
                    <button id="prevMonth" class="p-1 rounded-full hover:bg-gray-100">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-left">
                            <path d="m15 18-6-6 6-6"/>
                        </svg>
                    </button>
                    <h3 id="currentMonthYear" class="font-medium">Mei 2025</h3>
                    <button id="nextMonth" class="p-1 rounded-full hover:bg-gray-100">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-right">
                            <path d="m9 18 6-6-6-6"/>
                        </svg>
                    </button>
                </div>
                
                <div class="grid grid-cols-7 gap-1 text-center text-xs font-medium text-gray-500 mb-2">
                    <div>M</div>
                    <div>S</div>
                    <div>S</div>
                    <div>R</div>
                    <div>K</div>
                    <div>J</div>
                    <div>S</div>
                </div>
                
                <div id="calendarDays" class="grid grid-cols-7 gap-1 text-sm">
                    <!-- Calendar days will be generated by JavaScript -->
                </div>
                
                <div class="flex justify-between mt-4 pt-4 border-t border-gray-200">
                    <button id="cancelDateRange" class="px-3 py-1 text-sm text-gray-600 hover:bg-gray-100 rounded">Batal</button>
                    <button id="applyDateRange" class="px-3 py-1 text-sm bg-green-600 text-white rounded hover:bg-green-700">Terapkan</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Outlet Comparison Selector (Hidden by default) -->
<div id="outletComparisonSection" class="hidden mb-6">
    <div class="bg-white rounded-lg p-4 card-shadow">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Pilih Outlet untuk Komparasi</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div>
                <p class="block text-sm font-medium text-gray-700 mb-2">Outlet yang ingin dibandingkan:</p>
                <div id="outletCheckboxContainer" class="space-y-2 max-h-48 overflow-y-auto">
                    <!-- Outlet checkboxes will be populated here -->
                </div>
            </div>
            <div class="md:col-span-2 lg:col-span-2">
                <div class="bg-green-50 p-4 rounded-lg">
                    <div class="flex items-start space-x-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-green-600 mt-0.5 flex-shrink-0">
                            <circle cx="12" cy="12" r="10"/>
                            <path d="M12 16v-4"/>
                            <path d="M12 8h.01"/>
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-green-800">Cara menggunakan mode komparasi:</p>
                            <ul class="text-sm text-green-700 mt-1 space-y-1">
                                <li>• Pilih minimal 2 outlet untuk mulai komparasi</li>
                                <li>• Data akan ditampilkan dalam bentuk tabel dan chart</li>
                                <li>• Gunakan date range picker untuk mengatur periode komparasi</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-4 flex justify-between items-center">
            <p class="text-sm text-gray-600">
                <span id="selectedOutletsCount">0</span> outlet dipilih
            </p>
            <button id="startComparison" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                Mulai Komparasi
            </button>
        </div>
    </div>
</div>

<!-- Stats Cards - Row 1 -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
    <!-- Total Penjualan -->
    <div class="bg-white rounded-lg p-4 card-shadow">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-sm text-gray-500">Total Penjualan</p>
                <p class="text-xl font-bold" id="totalSales">Loading...</p>
            </div>
            <div class="bg-green-100 p-2 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-green-500">
                    <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                    <line x1="3" y1="6" x2="21" y2="6"></line>
                    <path d="M16 10a4 4 0 0 1-8 0"></path>
                </svg>
            </div>
        </div>
    </div>
    
    <!-- Transaksi -->
    <div class="bg-white rounded-lg p-4 card-shadow">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-sm text-gray-500">Transaksi</p>
                <p class="text-xl font-bold" id="totalOrders">Loading...</p>
            </div>
            <div class="bg-green-100 p-2 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-green-500">
                    <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                    <line x1="1" y1="10" x2="23" y2="10"></line>
                </svg>
            </div>
        </div>
    </div>
    
    <!-- Total Item Terjual -->
    <div class="bg-white rounded-lg p-4 card-shadow">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-sm text-gray-500">Total Item Terjual</p>
                <p class="text-xl font-bold" id="totalItems">Loading...</p>
            </div>
            <div class="bg-green-100 p-2 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-green-500">
                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                    <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                    <line x1="12" y1="22.08" x2="12" y2="12"></line>
                </svg>
            </div>
        </div>
    </div>
    
    <!-- Average Order Value -->
    <div class="bg-white rounded-lg p-4 card-shadow">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-sm text-gray-500">Rata-rata Nilai Order</p>
                <p class="text-xl font-bold" id="averageOrder">Loading...</p>
            </div>
            <div class="bg-purple-100 p-2 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-purple-500">
                    <line x1="12" y1="1" x2="12" y2="23"></line>
                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                </svg>
            </div>
        </div>
    </div>
</div>

<!-- Stats Cards - Row 2: New Metrics -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <!-- Total Diskon -->
    <div class="bg-white rounded-lg p-4 card-shadow">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-sm text-gray-500">Total Diskon</p>
                <p class="text-xl font-bold text-orange-600" id="totalDiscount">Loading...</p>
            </div>
            <div class="bg-orange-100 p-2 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-orange-500">
                    <path d="M9 12l2 2 4-4"></path>
                    <path d="M21 12c.552 0 1-.448 1-1V5a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v6c0 .552.448 1 1 1h18z"></path>
                    <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-7"></path>
                </svg>
            </div>
        </div>
    </div>
    
    <!-- Total Bonus -->
    <div class="bg-white rounded-lg p-4 card-shadow">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-sm text-gray-500">Total Bonus Keluar</p>
                <p class="text-xl font-bold text-yellow-600" id="totalBonus">Loading...</p>
            </div>
            <div class="bg-yellow-100 p-2 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-yellow-500">
                    <path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"></path>
                    <path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"></path>
                    <path d="M4 22h16"></path>
                    <path d="M10 14.66V17c0 .55.47.98.97 1.21C11.25 18.48 11.61 18.78 12 19c.39-.22.75-.52 1.03-.79.5-.23.97-.66.97-1.21v-2.34"></path>
                    <path d="M18 2H6v7a6 6 0 0 0 12 0V2z"></path>
                </svg>
            </div>
        </div>
    </div>
    
    <!-- Transaksi Cancel -->
    <div class="bg-white rounded-lg p-4 card-shadow">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-sm text-gray-500">Transaksi Cancel</p>
                <p class="text-xl font-bold text-red-600" id="totalCancelled">Loading...</p>
            </div>
            <div class="bg-red-100 p-2 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-red-500">
                    <path d="m21 21-6-6m6 6v-4.8m0 4.8h-4.8"></path>
                    <path d="M3 16.2V21m0 0h4.8M3 21l6-6"></path>
                    <path d="M21 7.8V3m0 0h-4.8M21 3l-6 6"></path>
                    <path d="M3 7.8V3m0 0h4.8M3 3l6 6"></path>
                </svg>
            </div>
        </div>
    </div>
    
    <!-- Transaksi Refund -->
    <div class="bg-white rounded-lg p-4 card-shadow">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-sm text-gray-500">Transaksi Refund</p>
                <p class="text-xl font-bold text-indigo-600" id="totalRefunded">Loading...</p>
            </div>
            <div class="bg-indigo-100 p-2 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-indigo-500">
                    <path d="M3 7v6h6"></path>
                    <path d="M21 17a9 9 0 0 0-9-9 9 9 0 0 0-6 2.3L3 13"></path>
                </svg>
            </div>
        </div>
    </div>
</div>

<!-- Two Columns -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Left Column -->
    <div class="lg:col-span-2">
    <!-- Overview with Bar Chart -->
    <div class="bg-white rounded-lg p-4 card-shadow mb-6">
        <h3 class="font-semibold text-gray-800 mb-4">Overview</h3>
        
        <!-- Single Outlet View -->
        <div id="singleOutletOverview">
            <p class="text-sm text-gray-600 mb-2">Data penjualan untuk <span class="outlet-name">loading</span></p>
            <p class="text-xl font-bold text-green-500 mb-4" id="totalSalesOverview">Loading...</p>
        </div>
        
        <!-- Comparison Mode View -->
        <div id="comparisonOverview" class="hidden">
            <p class="text-sm text-gray-600 mb-2">Perbandingan penjualan harian antar outlet</p>
            <p class="text-xl font-bold text-green-500 mb-4" id="comparisonTotalSales">Pilih outlet untuk melihat perbandingan</p>
        </div>
        
        <!-- Bar Chart Container -->
        <div class="relative h-64">
            <canvas id="salesChart"></canvas>
        </div>
    </div>
</div>
    
    <!-- Right Column -->
    <div class="lg:col-span-1">
        <!-- Penjualan Terlaris -->
        <div class="bg-white rounded-lg p-4 card-shadow mb-6">
            <h3 class="font-semibold text-gray-800 mb-4">Penjualan Terlaris</h3>
            <p class="text-sm text-gray-600 mb-2">Produk terlaris</p>
            
            <div class="space-y-3" id="topProductsList">
                <!-- Top products will be inserted here -->
                <div class="flex justify-between items-center">
                    <div>
                        <p class="font-medium">Loading...</p>
                        <p class="text-sm text-gray-500">Qty: -</p>
                    </div>
                    <p class="font-bold text-green-500">Rp -</p>
                </div>
            </div>
        </div>

        <!-- Produk Bonus Terlaris -->
        <div class="bg-white rounded-lg p-4 card-shadow">
            <h3 class="font-semibold text-gray-800 mb-4">Produk Bonus Terlaris</h3>
            <p class="text-sm text-gray-600 mb-2">Bonus yang paling banyak keluar</p>
            
            <div class="space-y-3" id="topBonusProductsList">
                <!-- Top bonus products will be inserted here -->
                <div class="flex justify-between items-center">
                    <div>
                        <p class="font-medium">Loading...</p>
                        <p class="text-sm text-gray-500">Qty: -</p>
                    </div>
                    <div class="flex items-center">
                        <div class="bg-yellow-100 text-yellow-600 px-2 py-1 rounded-full text-xs font-semibold">
                            BONUS
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Outlet Comparison Results (Hidden by default) -->
<div id="comparisonResults" class="hidden mb-6">
    <div class="bg-white rounded-lg p-4 card-shadow">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Komparasi Antar Outlet</h3>
            <button id="closeComparison" class="text-gray-500 hover:text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>
        
        <!-- Comparison Table -->
        <div class="overflow-x-auto mb-6">
            <table class="min-w-full divide-y divide-gray-200" id="comparisonTable">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Outlet</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Penjualan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Transaksi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item Terjual</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rata-rata Order</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Diskon</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Bonus</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Transaksi Cancel</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Transaksi Refund</th>
                    </tr>
                </thead>
                <tbody id="comparisonTableBody" class="bg-white divide-y divide-gray-200">
                    <!-- Table rows will be populated here -->
                </tbody>
            </table>
        </div>
        
        <!-- Comparison Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Sales Comparison Chart -->
            <div>
                <h4 class="text-md font-semibold text-gray-800 mb-4">Perbandingan Total Penjualan</h4>
                <div class="relative h-64">
                    <canvas id="comparisonSalesChart"></canvas>
                </div>
            </div>
            
            <!-- Transactions Comparison Chart -->
            <div>
                <h4 class="text-md font-semibold text-gray-800 mb-4">Perbandingan Jumlah Transaksi</h4>
                <div class="relative h-64">
                    <canvas id="comparisonTransactionsChart"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Additional Comparison Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Cancel Transactions Comparison Chart -->
            <div>
                <h4 class="text-md font-semibold text-gray-800 mb-4">Perbandingan Transaksi Cancel</h4>
                <div class="relative h-64">
                    <canvas id="comparisonCancelChart"></canvas>
                </div>
            </div>
            
            <!-- Refund Transactions Comparison Chart -->
            <div>
                <h4 class="text-md font-semibold text-gray-800 mb-4">Perbandingan Transaksi Refund</h4>
                <div class="relative h-64">
                    <canvas id="comparisonRefundChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM fully loaded. Initializing dashboard...');
        
        // Connect the outlet dropdown to dashboard updates
        connectOutletDropdownToDashboard();
        
        // Also hook into the loadOutletsFromAPI function to ensure it triggers dashboard updates
        const originalLoadOutletsFromAPI = window.loadOutletsFromAPI;
        
        if (typeof originalLoadOutletsFromAPI === 'function') {
            window.loadOutletsFromAPI = async function() {
                // Call the original function
                await originalLoadOutletsFromAPI();
                
                // After outlets are loaded, make sure we're looking at the selected outlet
                fetchDashboardData();
            };
        }
        
        // Fetch dashboard data with current outlet ID from localStorage
        fetchDashboardData();
        
        // Date Range Picker Functionality
        initDatePicker();
        
        // Initialize comparison functionality
        initializeComparison();
    });
        
        // Function to fetch dashboard data
    function fetchDashboardData() {
        // Get current outlet ID from localStorage
        const outletId = getSelectedOutletId();
        
        // Get current date range from URL or use default (first day of current month to today)
        const urlParams = new URLSearchParams(window.location.search);
        
        // Get today's date and first day of current month
        const today = new Date();
        const firstDayOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);
        
        // Format dates as YYYY-MM-DD
        const formatYMD = (date) => {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        };
        
        // Use URL parameters if available, otherwise use defaults
        const startDate = urlParams.get('start_date') || formatYMD(firstDayOfMonth);
        const endDate = urlParams.get('end_date') || formatYMD(today);
        
        // Update date display
        updateDateDisplay(startDate, endDate);
        
        console.log(`Fetching dashboard data for outlet ID: ${outletId} from ${startDate} to ${endDate}`);
        
        // Make API request with dynamic outlet ID
        fetch(`/api/reports/dashboard-summary/${outletId}?start_date=${startDate}&end_date=${endDate}`, {
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('token')}`,
                'Accept': 'application/json'
            }
        })
            .then(response => {
                // Check if response is ok
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    updateDashboard(data.data);
                } else {
                    console.error('Error fetching dashboard data:', data.message);
                    useDummyData();
                }
            })
            .catch(error => {
                console.error('Failed to fetch dashboard data:', error);
                // Use dummy data if API call fails
                useDummyData();
            });
    }
        
    // Function to get the currently selected outlet ID - modified to match your localStorage key
    function getSelectedOutletId() {
        // First check if outlet_id is in URL parameters (highest priority)
        const urlParams = new URLSearchParams(window.location.search);
        const outletIdFromUrl = urlParams.get('outlet_id');
        
        if (outletIdFromUrl) {
            return outletIdFromUrl;
        }
        
        // Then check localStorage for selected outlet - using your existing localStorage key
        const savedOutletId = localStorage.getItem('selectedOutletId');
        
        if (savedOutletId) {
            return savedOutletId;
        }
        
        // Default to outlet ID 1 if nothing is found
        return 1;
    }

    // This function connects your existing outlet dropdown with the dashboard refresh
    function connectOutletDropdownToDashboard() {
        // Get the outlet list container and monitor when users click on outlets
        const outletListContainer = document.getElementById('outletListContainer');
        
        if (outletListContainer) {
            // Use event delegation to catch all outlet item clicks
            outletListContainer.addEventListener('click', function(event) {
                // Find the clicked li element (may be the span or icon inside)
                let targetElement = event.target;
                while (targetElement && targetElement !== outletListContainer && targetElement.tagName !== 'LI') {
                    targetElement = targetElement.parentElement;
                }
                
                // If we clicked on an outlet list item
                if (targetElement && targetElement.tagName === 'LI') {
                    // Dashboard will be updated by your existing code setting localStorage
                    // and calling loadProductData, but we'll add an additional hook
                    
                    // The dashboard should update after a short delay to allow your
                    // existing code to complete
                    setTimeout(() => {
                        fetchDashboardData();
                    }, 100);
                }
            });
        }
        
        // Also modify your outlet dropdown button behavior if available
        const outletDropdownButton = document.getElementById('outletDropdownButton');
        if (outletDropdownButton) {
            // Ensure the dashboard updates every time the outlet is changed
            const originalClickHandler = outletDropdownButton.onclick;
            outletDropdownButton.onclick = function(event) {
                if (originalClickHandler) {
                    originalClickHandler.call(this, event);
                }
                
                // Additional hook to make sure dashboard gets updated
                setTimeout(() => {
                    const selectedOutletId = localStorage.getItem('selectedOutletId');
                    if (selectedOutletId) {
                        fetchDashboardData();
                    }
                }, 200);
            };
        }
    }
    
    // Function to update dashboard with API data
    function updateDashboard(data) {
        // Update outlet info
        document.getElementById('outletName').textContent = data.outlet;
        document.getElementById('outletNameText').textContent = data.outlet;
        document.querySelectorAll('.outlet-name').forEach(el => {
            el.textContent = data.outlet;
        });
        
        // Update summary stats
        document.getElementById('totalSales').textContent = formatCurrency(data.summary.total_sales);
        document.getElementById('totalOrders').textContent = data.summary.total_orders;
        document.getElementById('totalItems').textContent = data.summary.total_items + ' Item';
        document.getElementById('averageOrder').textContent = formatCurrency(data.summary.average_order_value);
        document.getElementById('totalSalesOverview').textContent = formatCurrency(data.sales.current_period);
        
        // Update new metrics
        document.getElementById('totalDiscount').textContent = formatCurrency(data.summary.total_discount || 0);
        document.getElementById('totalBonus').textContent = formatCurrency(data.summary.total_bonus_value || 0);
        document.getElementById('totalCancelled').textContent = formatCurrency(data.summary.total_cancelled || 0);
        document.getElementById('totalRefunded').textContent = formatCurrency(data.summary.total_refunded || 0);
        
        // Update top products
        updateTopProducts(data.top_products);
        
        // Update top bonus products
        updateTopBonusProducts(data.top_bonus_products || []);
        
        // Update chart
        updateSalesChart(data.daily_sales);
    }
    
    // Format currency as IDR
    function formatCurrency(value) {
        return 'Rp ' + parseFloat(value).toLocaleString('id-ID');
    }
    
    // Format date for display
    function formatDate(dateString) {
        const date = new Date(dateString);
        const day = date.getDate();
        const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
                           'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        const month = monthNames[date.getMonth()];
        const year = date.getFullYear();
        
        return `${day} ${month} ${year}`;
    }
    
    // Update date display
    function updateDateDisplay(startDate, endDate) {
        const formattedStartDate = formatDate(startDate);
        const formattedEndDate = formatDate(endDate);
        document.getElementById('dateRangeDisplay').textContent = `${formattedStartDate} - ${formattedEndDate}`;
    }
    
    // Update top products list
    function updateTopProducts(products) {
        const container = document.getElementById('topProductsList');
        container.innerHTML = '';
        
        if (products.length === 0) {
            container.innerHTML = `
                <div class="flex justify-center items-center py-4">
                    <p class="text-gray-500">Tidak ada produk terjual dalam periode ini</p>
                </div>
            `;
            return;
        }
        
        products.forEach(product => {
            const productElement = document.createElement('div');
            productElement.className = 'flex justify-between items-center';
            productElement.innerHTML = `
                <div>
                    <p class="font-medium">${product.name}</p>
                    <p class="text-sm text-gray-500">Qty: ${product.quantity}</p>
                </div>
                <p class="font-bold text-green-500">${formatCurrency(product.total)}</p>
            `;
            container.appendChild(productElement);
        });
    }
    
    // Update top bonus products list
    function updateTopBonusProducts(bonusProducts) {
        const container = document.getElementById('topBonusProductsList');
        container.innerHTML = '';
        
        if (bonusProducts.length === 0) {
            container.innerHTML = `
                <div class="flex justify-center items-center py-4">
                    <p class="text-gray-500">Tidak ada bonus keluar dalam periode ini</p>
                </div>
            `;
            return;
        }
        
        bonusProducts.forEach(product => {
            const productElement = document.createElement('div');
            productElement.className = 'flex justify-between items-center';
            productElement.innerHTML = `
                <div>
                    <p class="font-medium">${product.name}</p>
                    <p class="text-sm text-gray-500">Qty: ${product.bonus_quantity || 0}</p>
                </div>
                <div class="flex items-center">
                    <div class="bg-yellow-100 text-yellow-600 px-2 py-1 rounded-full text-xs font-semibold">
                        BONUS
                    </div>
                </div>
            `;
            container.appendChild(productElement);
        });
    }
    
    // Update sales chart
    function updateSalesChart(dailySales) {
        // Get the canvas element
        const ctx = document.getElementById('salesChart').getContext('2d');
        
        // If there's an existing chart, destroy it
        if (window.salesChartInstance) {
            window.salesChartInstance.destroy();
        }
        
        // Extract dates and sales values
        const dates = Object.keys(dailySales).sort();
        const salesData = dates.map(date => dailySales[date].sales);
        
        // Format dates for display
        const formattedDates = dates.map(date => {
            const d = new Date(date);
            const dayNames = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
            return dayNames[d.getDay()] + ', ' + d.getDate();
        });
        
        // Create the bar chart
        window.salesChartInstance = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: formattedDates,
                datasets: [{
                    label: 'Penjualan (Rp)',
                    data: salesData,
                    backgroundColor: 'rgba(249, 115, 22, 0.7)', // green color with transparency
                    borderColor: 'rgba(249, 115, 22, 1)', // Solid green for border
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                if (value >= 1000) {
                                    return 'Rp ' + (value / 1000) + 'k';
                                }
                                return 'Rp ' + value;
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Rp ' + context.raw.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });
    }
    
    // Initialize date picker
    function initDatePicker() {
        const dateRangeButton = document.getElementById('dateRangeButton');
        const datePickerDropdown = document.getElementById('datePickerDropdown');
        const currentMonthYear = document.getElementById('currentMonthYear');
        const calendarDays = document.getElementById('calendarDays');
        const prevMonth = document.getElementById('prevMonth');
        const nextMonth = document.getElementById('nextMonth');
        const cancelDateRange = document.getElementById('cancelDateRange');
        const applyDateRange = document.getElementById('applyDateRange');
        
        let currentDate = new Date();
        let startDate = null;
        let endDate = null;
        
        // Get current date range from URL or use default
        const urlParams = new URLSearchParams(window.location.search);
        const startDateParam = urlParams.get('start_date');
        const endDateParam = urlParams.get('end_date');
        
        if (startDateParam && endDateParam) {
            startDate = new Date(startDateParam);
            endDate = new Date(endDateParam);
        } else {
            // Default to beginning of month until today
            startDate = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1); // First day of current month
            endDate = new Date(); // Today
        }
        
        // Toggle dropdown
        dateRangeButton.addEventListener('click', function() {
            datePickerDropdown.classList.toggle('hidden');
            if (!datePickerDropdown.classList.contains('hidden')) {
                renderCalendar(currentDate);
            }
        });
        
        // Navigation between months
        prevMonth.addEventListener('click', function() {
            currentDate.setMonth(currentDate.getMonth() - 1);
            renderCalendar(currentDate);
        });
        
        nextMonth.addEventListener('click', function() {
            currentDate.setMonth(currentDate.getMonth() + 1);
            renderCalendar(currentDate);
        });
        
        // Render calendar
        function renderCalendar(date) {
            const year = date.getFullYear();
            const month = date.getMonth();
            
            currentMonthYear.textContent = new Intl.DateTimeFormat('id-ID', { 
                month: 'long', 
                year: 'numeric' 
            }).format(date);
            
            const firstDay = new Date(year, month, 1);
            const lastDay = new Date(year, month + 1, 0);
            const daysInMonth = lastDay.getDate();
            
            // Adjust for Indonesian calendar (Monday = 0)
            let startingDay = firstDay.getDay() - 1;
            if (startingDay < 0) startingDay = 6; // Sunday becomes 6
            
            calendarDays.innerHTML = '';
            
            // Previous month's days
            const prevMonthLastDay = new Date(year, month, 0).getDate();
            for (let i = 0; i < startingDay; i++) {
                const day = document.createElement('div');
                day.className = 'text-gray-400 p-1';
                day.textContent = prevMonthLastDay - startingDay + i + 1;
                calendarDays.appendChild(day);
            }
            
            // Current month's days
            for (let i = 1; i <= daysInMonth; i++) {
                const day = document.createElement('div');
                day.className = 'p-1 rounded-full cursor-pointer hover:bg-gray-100 text-center';
                day.textContent = i;
                
                const currentDay = new Date(year, month, i);
                
                // Highlight selected range
                if (startDate && endDate && currentDay >= startDate && currentDay <= endDate) {
                    day.className += ' bg-green-100 text-green-600';
                } else if (startDate && !endDate && currentDay.getTime() === startDate.getTime()) {
                    day.className += ' bg-green-600 text-white';
                }
                
                day.addEventListener('click', function() {
                    selectDate(currentDay);
                });
                
                calendarDays.appendChild(day);
            }
            
            // Next month's days
            const totalCells = startingDay + daysInMonth;
            const remainingCells = 42 - totalCells; // 6 weeks
            for (let i = 1; i <= remainingCells; i++) {
                const day = document.createElement('div');
                day.className = 'text-gray-400 p-1 text-center';
                day.textContent = i;
                calendarDays.appendChild(day);
            }
        }
        
        // Select date range
        function selectDate(date) {
            if (!startDate || (startDate && endDate)) {
                startDate = date;
                endDate = null;
            } else if (date > startDate) {
                endDate = date;
            } else {
                endDate = startDate;
                startDate = date;
            }
            
            renderCalendar(currentDate);
        }
        
        // Apply date range
        applyDateRange.addEventListener('click', function() {
            if (startDate && endDate) {
                const formattedStartDate = formatYMD(startDate);
                const formattedEndDate = formatYMD(endDate);
                
                // Update URL and reload page
                window.location.href = `?start_date=${formattedStartDate}&end_date=${formattedEndDate}`;
            }
            
            datePickerDropdown.classList.add('hidden');
        });
        
        // Format date as YYYY-MM-DD
        function formatYMD(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }
        
        // Cancel selection
        cancelDateRange.addEventListener('click', function() {
            datePickerDropdown.classList.add('hidden');
        });
        
        // Initial date display update
        updateDateDisplay(formatYMD(startDate), formatYMD(endDate));
    }

    // Outlet Comparison Functionality
    let comparisonMode = false;
    let selectedOutlets = [];
    let comparisonSalesChart = null;
    let comparisonTransactionsChart = null;
    let comparisonCancelChart = null;
    let comparisonRefundChart = null;

    // Initialize comparison functionality
    function initializeComparison() {
        const comparisonToggle = document.getElementById('comparisonMode');
        const comparisonSection = document.getElementById('outletComparisonSection');
        const startComparisonBtn = document.getElementById('startComparison');
        const closeComparisonBtn = document.getElementById('closeComparison');

        // Toggle comparison mode
        comparisonToggle.addEventListener('change', function() {
            comparisonMode = this.checked;
            if (comparisonMode) {
                comparisonSection.classList.remove('hidden');
                loadOutletsForComparison();
                hideSingleOutletView();
                switchToComparisonOverview();
            } else {
                comparisonSection.classList.add('hidden');
                document.getElementById('comparisonResults').classList.add('hidden');
                showSingleOutletView();
                switchToSingleOutletOverview();
                resetComparison();
            }
        });

        // Start comparison button
        startComparisonBtn.addEventListener('click', function() {
            if (selectedOutlets.length >= 2) {
                fetchComparisonData();
            }
        });

        // Close comparison results
        closeComparisonBtn.addEventListener('click', function() {
            document.getElementById('comparisonResults').classList.add('hidden');
        });
    }

    function getTokenFromStorage() {
        return localStorage.getItem('token')
    }

    // Load outlets for comparison
    async function loadOutletsForComparison() {
        try {
            // Get token with fallback
            let token = '';
            if (typeof getTokenFromStorage === 'function') {
                token = getTokenFromStorage();
            } else if (localStorage.getItem('token')) {
                token = localStorage.getItem('token');
            }
            
            console.log('Using token for outlets API:', token ? 'Token present' : 'No token'); // Debug log
            
            const headers = {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            };
            
            if (token) {
                headers['Authorization'] = `Bearer ${token}`;
            }
            
            const response = await fetch('/api/outlets', { headers });

            if (!response.ok) {
                throw new Error('Failed to fetch outlets');
            }

            const result = await response.json();
            console.log('Outlets API response:', result); // Debug log
            
            // Handle different possible response structures
            let outletsData = [];
            if (result.data) {
                outletsData = result.data;
            } else if (result.outlets) {
                outletsData = result.outlets;
            } else if (Array.isArray(result)) {
                outletsData = result;
            }
            
            console.log('Outlets data to populate:', outletsData); // Debug log
            populateOutletCheckboxes(outletsData);
        } catch (error) {
            console.error('Error loading outlets for comparison:', error);
            alert('Gagal memuat data outlet: ' + error.message);
        }
    }

    // Populate outlet checkboxes
    function populateOutletCheckboxes(outlets) {
        const container = document.getElementById('outletCheckboxContainer');
        container.innerHTML = '';

        console.log('Populating checkboxes with outlets:', outlets); // Debug log
        
        if (!outlets || outlets.length === 0) {
            container.innerHTML = '<p class="text-gray-500 text-sm">Tidak ada outlet tersedia</p>';
            return;
        }

        outlets.forEach(outlet => {
            console.log('Processing outlet:', outlet); // Debug log
            
            if (!outlet.id || !outlet.name) {
                console.warn('Invalid outlet data:', outlet);
                return;
            }
            
            const checkboxDiv = document.createElement('div');
            checkboxDiv.className = 'flex items-center py-1';
            checkboxDiv.innerHTML = `
                <input type="checkbox" id="outlet_${outlet.id}" value="${outlet.id}" 
                       class="outlet-checkbox h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                <label for="outlet_${outlet.id}" class="ml-2 block text-sm text-gray-700 cursor-pointer">
                    ${outlet.name}
                </label>
            `;
            container.appendChild(checkboxDiv);
            console.log("Container with checkbox outlet", container)
        });

        console.log('Checkboxes created. Total:', outlets.length); // Debug log

        // Add event listeners to checkboxes
        document.querySelectorAll('.outlet-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', updateSelectedOutlets);
        });
        
        // Initialize counter
        updateSelectedOutlets();
    }

    // Update selected outlets
    function updateSelectedOutlets() {
        selectedOutlets = [];
        const checkedBoxes = document.querySelectorAll('.outlet-checkbox:checked');
        console.log('Checked checkboxes found:', checkedBoxes.length); // Debug log
        
        checkedBoxes.forEach(checkbox => {
            const outletId = parseInt(checkbox.value);
            console.log('Adding outlet ID to selection:', outletId); // Debug log
            selectedOutlets.push(outletId);
        });

        console.log('Selected outlets:', selectedOutlets); // Debug log
        
        const countElement = document.getElementById('selectedOutletsCount');
        const buttonElement = document.getElementById('startComparison');
        
        if (countElement) {
            countElement.textContent = selectedOutlets.length;
        }
        
        if (buttonElement) {
            buttonElement.disabled = selectedOutlets.length < 2;
        }
    }

    // Hide single outlet view
    function hideSingleOutletView() {
        const elementsToHide = [
            'div.grid.grid-cols-1.md\\:grid-cols-4.gap-4.mb-4', // Stats cards row 1
            'div.grid.grid-cols-1.md\\:grid-cols-4.gap-4.mb-6', // Stats cards row 2
            'div.grid.grid-cols-1.md\\:grid-cols-2.gap-6.mb-6', // Products and charts
        ];
        
        // Hide main dashboard sections (this is simplified, you may need to adjust selectors)
        document.querySelectorAll('.grid').forEach(grid => {
            if (grid.classList.contains('md:grid-cols-4') || grid.classList.contains('md:grid-cols-2')) {
                // grid.style.display = 'none';
            }
        });
    }

    // Show single outlet view
    function showSingleOutletView() {
        document.querySelectorAll('.grid').forEach(grid => {
            if (grid.classList.contains('md:grid-cols-4') || grid.classList.contains('md:grid-cols-2')) {
                grid.style.display = 'grid';
            }
        });
    }

    // Reset comparison
    function resetComparison() {
        selectedOutlets = [];
        document.querySelectorAll('.outlet-checkbox').forEach(checkbox => {
            checkbox.checked = false;
        });
        updateSelectedOutlets();
        
        if (comparisonSalesChart) {
            comparisonSalesChart.destroy();
            comparisonSalesChart = null;
        }
        if (comparisonTransactionsChart) {
            comparisonTransactionsChart.destroy();
            comparisonTransactionsChart = null;
        }
        if (comparisonCancelChart) {
            comparisonCancelChart.destroy();
            comparisonCancelChart = null;
        }
        if (comparisonRefundChart) {
            comparisonRefundChart.destroy();
            comparisonRefundChart = null;
        }
    }
    
    // Switch to comparison overview
    function switchToComparisonOverview() {
        document.getElementById('singleOutletOverview').classList.add('hidden');
        document.getElementById('comparisonOverview').classList.remove('hidden');
        
        // Clear the existing chart for comparison view
        if (window.salesChartInstance) {
            window.salesChartInstance.destroy();
            window.salesChartInstance = null;
        }
    }
    
    // Switch to single outlet overview
    function switchToSingleOutletOverview() {
        document.getElementById('comparisonOverview').classList.add('hidden');
        document.getElementById('singleOutletOverview').classList.remove('hidden');
        
        // Refresh single outlet data
        fetchDashboardData();
    }

    // Fetch comparison data
    async function fetchComparisonData() {
        if (selectedOutlets.length < 2) {
            alert('Pilih minimal 2 outlet untuk komparasi');
            return;
        }

        try {
            const urlParams = new URLSearchParams(window.location.search);
            
            // Use same default date range as main dashboard - first day of current month to today
            const today = new Date();
            const firstDayOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);
            
            const formatYMD = (date) => {
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            };
            
            const startDate = urlParams.get('start_date') || formatYMD(firstDayOfMonth);
            const endDate = urlParams.get('end_date') || formatYMD(today);

            const queryParams = new URLSearchParams({
                outlet_ids: selectedOutlets.join(','),
                start_date: startDate,
                end_date: endDate
            });

            console.log('Fetching comparison data with params:', {
                outlet_ids: selectedOutlets.join(','),
                start_date: startDate,
                end_date: endDate
            });

            const response = await fetch(`/api/reports/comparison?${queryParams}`, {
                headers: {
                    'Authorization': `Bearer ${getTokenFromStorage()}`,
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                const errorText = await response.text();
                console.error('API response error:', response.status, errorText);
                throw new Error(`API Error: ${response.status} - ${errorText}`);
            }

            const result = await response.json();
            console.log('Comparison API response:', result);
            
            if (result.success && result.data) {
                displayComparisonResults(result.data);
            } else {
                console.error('API returned unsuccessful response:', result);
                alert('API mengembalikan respons tidak berhasil: ' + (result.message || 'Unknown error'));
            }
        } catch (error) {
            console.error('Error fetching comparison data:', error);
            alert('Gagal memuat data komparasi: ' + error.message);
        }
    }

    // Display comparison results
    function displayComparisonResults(data) {
        console.log('Displaying comparison results:', data);
        document.getElementById('comparisonResults').classList.remove('hidden');
        
        // Update Overview section with comparison data
        updateComparisonOverview(data);
        
        // Update Overview chart with comparison data
        updateOverviewComparisonChart(data);
        
        // Populate comparison table
        populateComparisonTable(data);
        
        // Create comparison charts
        createComparisonCharts(data);
    }

    // Populate comparison table
    function populateComparisonTable(data) {
        const tbody = document.getElementById('comparisonTableBody');
        tbody.innerHTML = '';

        console.log('Populating comparison table with data:', data);

        if (!data || data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="9" class="px-6 py-4 text-center text-gray-500">Tidak ada data untuk ditampilkan</td></tr>';
            return;
        }

        data.forEach((outlet, index) => {
            console.log(`Processing outlet ${index + 1}:`, outlet);
            const row = document.createElement('tr');
            row.innerHTML = `
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${outlet.outlet_name || 'N/A'}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${formatCurrency(outlet.total_sales || 0)}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${outlet.total_orders || 0}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${outlet.total_items || 0}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${formatCurrency(outlet.average_order_value || 0)}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${formatCurrency(outlet.total_discount || 0)}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${formatCurrency(outlet.total_bonus_value || 0)}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600 font-medium">${formatCurrency(outlet.total_cancelled || 0)}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-orange-600 font-medium">${formatCurrency(outlet.total_refunded || 0)}</td>
            `;
            tbody.appendChild(row);
        });
    }

    // Create comparison charts
    function createComparisonCharts(data) {
        const outlets = data.map(d => d.outlet_name);
        const salesData = data.map(d => d.total_sales);
        const transactionsData = data.map(d => d.total_orders);
        const cancelData = data.map(d => d.total_cancelled || 0);
        const refundData = data.map(d => d.total_refunded || 0);

        // Destroy existing charts
        if (comparisonSalesChart) comparisonSalesChart.destroy();
        if (comparisonTransactionsChart) comparisonTransactionsChart.destroy();
        if (comparisonCancelChart) comparisonCancelChart.destroy();
        if (comparisonRefundChart) comparisonRefundChart.destroy();

        // Sales comparison chart
        const salesCtx = document.getElementById('comparisonSalesChart').getContext('2d');
        comparisonSalesChart = new Chart(salesCtx, {
            type: 'bar',
            data: {
                labels: outlets,
                datasets: [{
                    label: 'Total Penjualan',
                    data: salesData,
                    backgroundColor: 'rgba(59, 130, 246, 0.8)',
                    borderColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return formatCurrency(value);
                            }
                        }
                    }
                }
            }
        });

        // Transactions comparison chart
        const transactionsCtx = document.getElementById('comparisonTransactionsChart').getContext('2d');
        comparisonTransactionsChart = new Chart(transactionsCtx, {
            type: 'bar',
            data: {
                labels: outlets,
                datasets: [{
                    label: 'Jumlah Transaksi',
                    data: transactionsData,
                    backgroundColor: 'rgba(16, 185, 129, 0.8)',
                    borderColor: 'rgba(16, 185, 129, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Cancel transactions comparison chart
        const cancelCtx = document.getElementById('comparisonCancelChart').getContext('2d');
        comparisonCancelChart = new Chart(cancelCtx, {
            type: 'bar',
            data: {
                labels: outlets,
                datasets: [{
                    label: 'Transaksi Cancel',
                    data: cancelData,
                    backgroundColor: 'rgba(239, 68, 68, 0.8)',
                    borderColor: 'rgba(239, 68, 68, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return formatCurrency(value);
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `${context.dataset.label}: ${formatCurrency(context.raw)}`;
                            }
                        }
                    }
                }
            }
        });

        // Refund transactions comparison chart
        const refundCtx = document.getElementById('comparisonRefundChart').getContext('2d');
        comparisonRefundChart = new Chart(refundCtx, {
            type: 'bar',
            data: {
                labels: outlets,
                datasets: [{
                    label: 'Transaksi Refund',
                    data: refundData,
                    backgroundColor: 'rgba(249, 115, 22, 0.8)',
                    borderColor: 'rgba(249, 115, 22, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return formatCurrency(value);
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `${context.dataset.label}: ${formatCurrency(context.raw)}`;
                            }
                        }
                    }
                }
            }
        });
    }
    
    // Update comparison overview section
    function updateComparisonOverview(data) {
        const totalSalesAllOutlets = data.reduce((sum, outlet) => sum + (outlet.total_sales || 0), 0);
        const comparisonTotalSales = document.getElementById('comparisonTotalSales');
        
        if (comparisonTotalSales) {
            comparisonTotalSales.textContent = `Total Gabungan: ${formatCurrency(totalSalesAllOutlets)}`;
        }
    }
    
    // Update overview chart with comparison data - daily comparison
    function updateOverviewComparisonChart(data) {
        const ctx = document.getElementById('salesChart').getContext('2d');
        
        // Destroy existing chart
        if (window.salesChartInstance) {
            window.salesChartInstance.destroy();
        }
        
        // Get all unique dates from all outlets
        const allDates = new Set();
        data.forEach(outlet => {
            if (outlet.daily_sales) {
                Object.keys(outlet.daily_sales).forEach(date => allDates.add(date));
            }
        });
        
        // Sort dates
        const sortedDates = Array.from(allDates).sort();
        
        // Format dates for display
        const formattedDates = sortedDates.map(date => {
            const d = new Date(date);
            const dayNames = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
            return dayNames[d.getDay()] + ', ' + d.getDate();
        });
        
        // Define colors for different outlets
        const colors = [
            { bg: 'rgba(59, 130, 246, 0.8)', border: 'rgba(59, 130, 246, 1)' },   // green
            { bg: 'rgba(16, 185, 129, 0.8)', border: 'rgba(16, 185, 129, 1)' },   // Green  
            { bg: 'rgba(249, 115, 22, 0.8)', border: 'rgba(249, 115, 22, 1)' },   // Orange
            { bg: 'rgba(139, 92, 246, 0.8)', border: 'rgba(139, 92, 246, 1)' },   // Purple
            { bg: 'rgba(236, 72, 153, 0.8)', border: 'rgba(236, 72, 153, 1)' },   // Pink
            { bg: 'rgba(34, 197, 94, 0.8)', border: 'rgba(34, 197, 94, 1)' },     // Emerald
        ];
        
        // Create datasets for each outlet
        const datasets = data.map((outlet, index) => {
            const outletDailySales = sortedDates.map(date => {
                return outlet.daily_sales && outlet.daily_sales[date] ? outlet.daily_sales[date].sales : 0;
            });
            
            const color = colors[index % colors.length];
            
            return {
                label: outlet.outlet_name || `Outlet ${outlet.outlet_id}`,
                data: outletDailySales,
                backgroundColor: color.bg,
                borderColor: color.border,
                borderWidth: 1
            };
        });
        
        // Create daily comparison chart
        window.salesChartInstance = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: formattedDates,
                datasets: datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                if (value >= 1000000) {
                                    return 'Rp ' + (value / 1000000).toFixed(1) + 'M';
                                } else if (value >= 1000) {
                                    return 'Rp ' + (value / 1000).toFixed(0) + 'k';
                                }
                                return 'Rp ' + value;
                            }
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Tanggal'
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `${context.dataset.label}: ${formatCurrency(context.raw)}`;
                            }
                        }
                    },
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 20
                        }
                    },
                    title: {
                        display: true,
                        text: 'Perbandingan Penjualan Harian Antar Outlet',
                        font: {
                            size: 14,
                            weight: 'bold'
                        }
                    }
                },
                interaction: {
                    mode: 'index',
                    intersect: false
                }
            }
        });
    }

</script>
@endsection