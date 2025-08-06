@extends('layouts.app')

@section('title', 'Manajemen Produk')

@section('content')
<!-- Notification container -->
<div id="alertContainer" class="fixed top-4 right-4 z-[1000] space-y-3 w-80">
    <!-- Alerts will appear here dynamically -->
</div>

@include('partials.produk.modal-konfirmasi-hapus')
@include('partials.produk.modal-tambah-produk')
@include('partials.produk.modal-edit-produk')

<!-- Page Title + Action -->
<div class="mb-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-800">Manajemen Produk</h1>
        <button onclick="console.log('Opening modal'); ProductManager.openModal('modalTambahProduk')" class="px-5 py-3 text-base font-medium text-white bg-green-500 rounded-lg hover:bg-green-600 shadow">
            + Tambah Produk
        </button>
    </div>
</div>

<!-- Card: Outlet Info + Aksi -->
<div class="bg-white rounded-md p-4 shadow-md mb-4 flex flex-col md:flex-row md:items-center md:justify-between">
    <div class="mb-4 md:mb-0 flex items-start gap-2">
        <i data-lucide="store" class="w-5 h-5 text-gray-600"></i>
        <div>
            <h2 class="text-lg font-semibold text-gray-800">
                Outlet Aktif: <span id="currentOutletName">Loading ...</span>
            </h2>
            <p class="text-sm text-gray-600" id="activeOutletDesc">
                Data yang ditampilkan adalah untuk outlet <span id="outletNamePlaceholder">Loading ...</span>.
            </p>
        </div>
    </div>
    <div class="flex items-center space-x-2">
        <div class="flex items-center space-x-2">
            <button onclick="ProductManager.printProductReport()" class="flex items-center px-4 py-2 text-sm font-medium bg-white border rounded shadow hover:bg-gray-50">
                <i data-lucide="printer" class="w-4 h-4 mr-2"></i> Cetak
            </button>
            <button onclick="ProductManager.exportProductsToCSV()" class="flex items-center px-4 py-2 text-sm font-medium bg-white border rounded shadow hover:bg-gray-50">
                <i data-lucide="download" class="w-4 h-4 mr-2"></i> Ekspor
            </button>
        </div>
    </div>
</div>

<!-- Card: Tabel Produk -->
<div class="bg-white rounded-lg shadow p-4">
    <!-- Header Table: Search -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4">
        <input 
            type="text" 
            placeholder="Pencarian...." 
            class="w-full md:w-1/3 border rounded px-3 py-2 text-sm mb-2 md:mb-0" 
            id="searchProduk"
        />
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full text-base">
            <thead class="text-left text-base text-gray-600 border-b">
                <tr>
                    <th class="py-2 font-semibold">No.</th>
                    <th class="py-2 font-semibold">Nama Produk</th>
                    <th class="py-2 font-semibold">Bar Code</th>
                    <th class="py-2 font-semibold">SKU</th>
                    <th class="py-2 font-semibold">Kategori</th>
                    <th class="py-2 font-semibold">Harga</th>
                    <th class="py-2 font-semibold">Stok</th>
                    <th class="py-2 font-semibold">Status</th>
                    <th class="py-2 font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-700" id="produkTableBody">
                <!-- Loading indicator -->
                <tr class="border-b">
                    <td colspan="9" class="py-8 text-center">
                        <div class="flex flex-col items-center justify-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                 class="animate-spin text-green-500">
                                <path d="M21 12a9 9 0 1 1-6.219-8.56"/>
                            </svg>
                            <span class="text-gray-500">Memuat data produk...</span>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<style>
    /* Animation styles */
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

<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
<script src="{{ asset('js/produk/barcodePrinter.js') }}"></script>
<script src="{{asset('js/produk/produk.js')}}"></script>
@endsection