<?php

// namespace App\Http\Controllers\API;
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\MonthlyInventoryReport;
use App\Models\MonthlyReport;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\InventoryHistory;
use App\Models\Outlet;
use App\Models\Product;
use App\Models\Shift;
use App\Traits\ApiResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    use ApiResponse;
    /**
     * Laporan penjualan harian berdasarkan outlet
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Outlet  $outlet
     * @return \Illuminate\Http\Response
     */

    public function dailySales(Request $request, Outlet $outlet)
    {
        // Validasi input
        $request->validate([
            'start_date' => 'required|date_format:Y-m-d',
            'end_date' => 'required|date_format:Y-m-d',
        ]);

        // Konversi string tanggal menjadi instance Carbon
        $startDate = Carbon::createFromFormat('Y-m-d', $request->start_date);
        $endDate = Carbon::createFromFormat('Y-m-d', $request->end_date);

        // Ambil data order dengan relasi items dan produk
        $orders = Order::where('outlet_id', $outlet->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->with(['items.product.category', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Hitung summary
        $totalSales = $orders->sum('total');
        $totalItems = $orders->flatMap(function ($order) {
            return $order->items;
        })->sum('quantity');
        $totalOrders = $orders->count();
        $averageOrderValue = $totalOrders > 0 ? $totalSales / $totalOrders : 0;

        // Format response data
        $formattedOrders = $orders->map(function ($order) {
            return [
                'order_id' => $order->order_number,
                'order_time' => $order->created_at->format('H:i:s'),
                'total' => $order->total,
                'payment_method' => $order->payment_method,
                'tax' => $order->tax,
                'cashier' => $order->user->name ?? 'Unknown',
                'items' => $order->items->map(function ($item) {
                    return [
                        'product_id' => $item->product_id,
                        'product_name' => $item->product->name,
                        'category' => $item->product->category->name ?? 'Uncategorized',
                        'sku' => $item->product->sku,
                        'unit' => $item->product->unit,
                        'quantity' => $item->quantity,
                        'unit_price' => $item->price,
                        'subtotal' => $item->subtotal,
                    ];
                })
            ];
        });

        return response()->json([
            'status' => true,
            'data' => [
                'date_from' => $startDate->format('Y-m-d'),
                'date_to' => $endDate->format('Y-m-d'),
                'outlet' => $outlet->name,
                'summary' => [
                    'total_sales' => $totalSales,
                    'total_orders' => $totalOrders,
                    'total_items' => $totalItems,
                    'average_order_value' => round($averageOrderValue, 2),
                ],
                'orders' => $formattedOrders
            ]
        ]);
    }


    // public function dailySales(Request $request, Outlet $outlet)
    // {
    //     $request->validate([
    //         'start_date' => 'required|date_format:Y-m-d',
    //         'end_date' => 'required|date_format:Y-m-d',
    //     ]);

    //     $startDate = $request->start_date;
    //     $endDate = $request->end_date;

    //     // $date = $request->date ? Carbon::parse($request->date) : Carbon::today();

    //     // Ambil data order dengan relasi items dan produk
    //     $orders = Order::where('outlet_id', $outlet->id)
    //         // ->whereDate('created_at', $date)
    //         ->whereBetween('created_at', [$startDate, $endDate])
    //         ->where('status', 'completed')
    //         ->with(['items.product.category'])
    //         ->orderBy('created_at', 'desc')
    //         ->get();

    //     // Hitung summary
    //     $totalSales = $orders->sum('total');
    //     $totalItems = $orders->flatMap(function ($order) {
    //         return $order->items;
    //     })->sum('quantity');
    //     $totalOrders = $orders->count();
    //     $averageOrderValue = $totalOrders > 0 ? $totalSales / $totalOrders : 0;

    //     // Format response data
    //     $formattedOrders = $orders->map(function ($order) {
    //         return [
    //             'order_id' => $order->order_number,
    //             'order_time' => $order->created_at->format('H:i:s'),
    //             'total' => $order->total,
    //             'payment_method' => $order->payment_method,
    //             'tax' => $order->tax,
    //             'cashier' => $order->user->name ?? 'Unknown',
    //             'items' => $order->items->map(function ($item) {
    //                 return [
    //                     'product_id' => $item->product_id,
    //                     'product_name' => $item->product->name,
    //                     'category' => $item->product->category->name ?? 'Uncategorized',
    //                     'sku' => $item->product->sku,
    //                     'unit' => $item->product->unit,
    //                     'quantity' => $item->quantity,
    //                     'unit_price' => $item->price,
    //                     'subtotal' => $item->subtotal,
    //                 ];
    //             })
    //         ];
    //     });

    //     return response()->json([
    //         'status' => true,
    //         'data' => [
    //             'date_from' => $startDate->format('Y-m-d'),
    //             'date_to' => $endDate->format('Y-m-d'),
    //             'outlet' => $outlet->name,
    //             'summary' => [
    //                 'total_sales' => $totalSales,
    //                 'total_orders' => $totalOrders,
    //                 'total_items' => $totalItems,
    //                 'average_order_value' => round($averageOrderValue, 2),
    //             ],
    //             'orders' => $formattedOrders
    //         ]
    //     ]);
    // }

    /**
     * Laporan penjualan bulanan berdasarkan outlet
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Outlet  $outlet
     * @return \Illuminate\Http\Response
     */
    public function listProductsByDateRange(Request $request, Outlet $outlet)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();

        // Get all products sold within the date range
        $products = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->select(
                'products.id',
                'products.sku as sku',
                // 'orders.order_number as order_number',
                'products.name as product_name',
                'categories.name as category_name',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.subtotal) as total_sales'),
                DB::raw('COUNT(DISTINCT orders.id) as order_count')
            )
            ->where('orders.outlet_id', $outlet->id)
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->where('orders.status', 'completed')
            ->groupBy('products.id', 'products.name', 'products.sku', 'categories.name')
            ->orderBy('total_sales', 'desc')
            ->get();

        // Calculate summary statistics
        $totalSales = $products->sum('total_sales');
        $totalQuantity = $products->sum('total_quantity');
        $totalOrders = DB::table('orders')
            ->where('outlet_id', $outlet->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->count();
        $averageOrderValue = $totalOrders > 0 ? $totalSales / $totalOrders : 0;

        // Add percentage contribution to each product
        $productsWithPercentage = $products->map(function ($product) use ($totalSales) {
            $product->sales_percentage = $totalSales > 0
                ? round(($product->total_sales / $totalSales) * 100, 2)
                : 0;

            $product->average_order_value = $product->order_count > 0
                ? round($product->total_sales / $product->order_count, 2)
                : 0;

            return $product;
        });

        return response()->json([
            'status' => true,
            'data' => [
                'date_range' => [
                    'start_date' => $startDate->format('Y-m-d'),
                    'end_date' => $endDate->format('Y-m-d'),
                ],
                'outlet' => $outlet->name,
                'summary' => [
                    'total_sales' => $totalSales,
                    'total_quantity' => $totalQuantity,
                    'total_orders' => $totalOrders,
                    'average_order_value' => $averageOrderValue,
                ],
                'products' => $productsWithPercentage
            ]
        ]);
    }

    /**
     * Laporan inventory bulanan berdasarkan outlet
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Outlet  $outlet
     * @return \Illuminate\Http\Response
     */
    public function monthlyInventory(Request $request, Outlet $outlet)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();

        $products = Product::whereHas('inventory', function ($q) use ($outlet) {
            $q->where('outlet_id', $outlet->id);
        })->get();

        $results = [];

        foreach ($products as $product) {
            $inventory = Inventory::where('outlet_id', $outlet->id)
                ->where('product_id', $product->id)
                ->first();

            // Hitung transaksi dengan tanda yang benar
            $sales = InventoryHistory::where('outlet_id', $outlet->id)
                ->where('product_id', $product->id)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->where('type', 'sale')
                ->sum('quantity_change'); // seharusnya negatif

            $purchases = InventoryHistory::where('outlet_id', $outlet->id)
                ->where('product_id', $product->id)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->where('type', 'purchase')
                ->sum('quantity_change'); // seharusnya positif

            $adjustments = InventoryHistory::where('outlet_id', $outlet->id)
                ->where('product_id', $product->id)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->where('type', 'adjustment')
                ->sum('quantity_change');

            $results[] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'opening_stock' => $inventory->quantity - $purchases + abs($sales) - $adjustments,
                'closing_stock' => $inventory->quantity,
                'sales_quantity' => abs($sales),
                'purchase_quantity' => $purchases,
                'adjustment_quantity' => $adjustments
            ];
        }

        return response()->json([
            'status' => true,
            'data' => [
                'outlet' => $outlet->name,
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'inventory' => $results
            ]
        ]);
    }

    // public function inventoryReportOld(Request $request, Outlet $outlet)
    // {
    //     $request->validate([
    //         'start_date' => 'required|date',
    //         'end_date' => 'required|date|after_or_equal:start_date',
    //     ]);

    //     $startDate = Carbon::parse($request->start_date)->startOfDay();
    //     $endDate = Carbon::parse($request->end_date)->endOfDay();
    //     $previousDay = $startDate->copy()->subDay();

    //     $products = Product::whereHas('inventory', function ($q) use ($outlet) {
    //         $q->where('outlet_id', $outlet->id);
    //     })->get();

    //     $results = [];

    //     foreach ($products as $product) {
    //         // 1. Hitung SALDO AWAL (stok akhir hari sebelumnya)
    //         $previousInventory = InventoryHistory::where('outlet_id', $outlet->id)
    //             ->where('product_id', $product->id)
    //             ->whereDate('created_at', '<=', $previousDay)
    //             ->latest('created_at')
    //             ->first();

    //         $openingStock = $previousInventory ? $previousInventory->quantity_after : 0;

    //         // 2. Hitung STOCK MASUK (pembelian + adjustment plus)
    //         $incomingStock = InventoryHistory::where('outlet_id', $outlet->id)
    //             ->where('product_id', $product->id)
    //             ->whereBetween('created_at', [$startDate, $endDate])
    //             ->where(function ($query) {
    //                 $query->where('type', 'purchase')
    //                     ->orWhere('type', 'transfer_in')
    //                     ->orWhere('type', 'shipment')
    //                     ->orWhere('type', 'other') // Tambahkan transfer_in
    //                     ->orWhere(function ($q) {
    //                         $q->where('type', 'adjustment')
    //                             ->where('quantity_change', '>', 0);
    //                     });
    //             })
    //             ->sum('quantity_change');

    //         // 3. Hitung STOCK KELUAR (penjualan + adjustment minus + transfer_out)
    //         $outgoingStock = InventoryHistory::where('outlet_id', $outlet->id)
    //             ->where('product_id', $product->id)
    //             ->whereBetween('created_at', [$startDate, $endDate])
    //             ->where(function ($query) {
    //                 $query->where('type', 'sale')
    //                     ->orWhere('type', 'transfer_out') // Tambahkan transfer_out
    //                     ->orWhere(function ($q) {
    //                         $q->where('type', 'adjustment')
    //                             ->where('quantity_change', '<', 0);
    //                     });
    //             })
    //             ->sum('quantity_change');

    //         $outgoingStock = abs($outgoingStock); // Convert to positive number

    //         // 4. Hitung STOCK AKHIR
    //         $closingStock = $openingStock + $incomingStock - $outgoingStock;

    //         // 5. Dapatkan stok aktual terakhir
    //         $currentInventory = Inventory::where('outlet_id', $outlet->id)
    //             ->where('product_id', $product->id)
    //             ->first();

    //         $results[] = [
    //             'product_id' => $product->id,
    //             'product_name' => $product->name,
    //             'product_code' => $product->sku,
    //             'unit' => $product->unit,
    //             'saldo_awal' => $openingStock,
    //             'stock_masuk' => $incomingStock,
    //             'stock_keluar' => $outgoingStock,
    //             'stock_akhir' => $closingStock,
    //             'stock_aktual' => $currentInventory ? $currentInventory->quantity : 0,
    //             'selisih' => ($currentInventory ? $currentInventory->quantity : 0) - $closingStock
    //         ];
    //     }

    //     return response()->json([
    //         'status' => true,
    //         'data' => [
    //             'outlet' => $outlet->name,
    //             'periode' => [
    //                 'start_date' => $startDate->format('Y-m-d'),
    //                 'end_date' => $endDate->format('Y-m-d'),
    //             ],
    //             'products' => $results,
    //             'summary' => [
    //                 'total_saldo_awal' => collect($results)->sum('saldo_awal'),
    //                 'total_stock_masuk' => collect($results)->sum('stock_masuk'),
    //                 'total_stock_keluar' => collect($results)->sum('stock_keluar'),
    //                 'total_stock_akhir' => collect($results)->sum('stock_akhir'),
    //             ]
    //         ]
    //     ]);
    // }

    // public function inventoryReportBaru(Request $request, Outlet $outlet)
    // {
    //     $request->validate([
    //         'start_date' => 'required|date',
    //         'end_date' => 'required|date|after_or_equal:start_date',
    //     ]);

    //     $startDate = Carbon::parse($request->start_date)->startOfDay();
    //     $endDate = Carbon::parse($request->end_date)->endOfDay();
    //     $previousDay = $startDate->copy()->subDay();

    //     $products = Product::whereHas('inventory', function ($q) use ($outlet) {
    //         $q->where('outlet_id', $outlet->id);
    //     })->get();

    //     $results = [];

    //     foreach ($products as $product) {
    //         // 1. Hitung SALDO AWAL (stok akhir hari sebelumnya)
    //         $previousInventory = InventoryHistory::where('outlet_id', $outlet->id)
    //             ->where('product_id', $product->id)
    //             ->whereDate('created_at', '<=', $previousDay)
    //             ->latest('created_at')
    //             ->first();

    //         $openingStock = $previousInventory ? $previousInventory->quantity_after : 0;

    //         // 2. Hitung STOCK MASUK (pembelian + adjustment plus)
    //         $incomingStock = InventoryHistory::where('outlet_id', $outlet->id)
    //             ->where('product_id', $product->id)
    //             ->whereBetween('created_at', [$startDate, $endDate])
    //             ->where(function ($query) {
    //                 $query->where('type', 'purchase')
    //                     ->orWhere('type', 'transfer_in')
    //                     ->orWhere('type', 'shipment')
    //                     ->orWhere('type', 'other') // Tambahkan transfer_in
    //                     ->orWhere(function ($q) {
    //                         $q->where('type', 'adjustment')
    //                             ->where('quantity_change', '>', 0);
    //                     });
    //             })
    //             ->sum('quantity_change');

    //         // 3. Hitung STOCK KELUAR (penjualan + adjustment minus + transfer_out)
    //         $outgoingStock = InventoryHistory::where('outlet_id', $outlet->id)
    //             ->where('product_id', $product->id)
    //             ->whereBetween('created_at', [$startDate, $endDate])
    //             ->where(function ($query) {
    //                 $query->where('type', 'sale')
    //                     ->orWhere('type', 'transfer_out') // Tambahkan transfer_out
    //                     ->orWhere(function ($q) {
    //                         $q->where('type', 'adjustment')
    //                             ->where('quantity_change', '<', 0);
    //                     });
    //             })
    //             ->sum('quantity_change');

    //         $outgoingStock = abs($outgoingStock); // Convert to positive number

    //         //saldo akhir
    //         $saldoAkhir = $openingStock + $incomingStock - $outgoingStock;

    //         // 4. Hitung STOCK AKHIR
    //         $closingStock = $saldoAkhir + $incomingStock - $outgoingStock;


    //         // 5. Dapatkan stok aktual terakhir
    //         $currentInventory = Inventory::where('outlet_id', $outlet->id)
    //             ->where('product_id', $product->id)
    //             ->first();

    //         $results[] = [
    //             'product_id' => $product->id,
    //             'product_name' => $product->name,
    //             'product_code' => $product->sku,
    //             'unit' => $product->unit,
    //             'saldo_awal' => $openingStock,
    //             'stock_masuk' => $incomingStock,
    //             'stock_keluar' => $outgoingStock,
    //             'stock_akhir' => $closingStock,
    //             'saldo_akhir' => $saldoAkhir,
    //             'stock_aktual' => $currentInventory ? $currentInventory->quantity : 0,
    //             'selisih' => ($currentInventory ? $currentInventory->quantity : 0) - $closingStock
    //         ];
    //     }

    //     return response()->json([
    //         'status' => true,
    //         'data' => [
    //             'outlet' => $outlet->name,
    //             'periode' => [
    //                 'start_date' => $startDate->format('Y-m-d'),
    //                 'end_date' => $endDate->format('Y-m-d'),
    //             ],
    //             'products' => $results,
    //             'summary' => [
    //                 'total_saldo_awal' => collect($results)->sum('saldo_awal'),
    //                 'total_stock_masuk' => collect($results)->sum('stock_masuk'),
    //                 'total_stock_keluar' => collect($results)->sum('stock_keluar'),
    //                 'total_stock_akhir' => collect($results)->sum('stock_akhir'),
    //             ]
    //         ]
    //     ]);
    // }

    public function inventoryReport(Request $request, Outlet $outlet)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();
        $previousDay = $startDate->copy()->subDay();

        // Ambil produk yang memiliki inventory di outlet ini
        $products = Product::whereHas('inventory', function ($q) use ($outlet) {
            $q->where('outlet_id', $outlet->id);
        })->get();

        $results = [];

        foreach ($products as $product) {
            // 1. SALDO AWAL - Saldo akhir tanggal sebelumnya
            $previousInventory = InventoryHistory::where('outlet_id', $outlet->id)
                ->where('product_id', $product->id)
                ->whereDate('created_at', '<=', $previousDay)
                ->latest('created_at')
                ->first();

            $currentInventory = Inventory::where('outlet_id', $outlet->id)
                ->where('product_id', $product->id)
                ->first();

            // Cek apakah produk baru (tidak ada history sebelum start_date)
            $isNewProduct = !InventoryHistory::where('product_id', $product->id)
                ->where('outlet_id', $outlet->id)
                ->whereDate('created_at', '<', $startDate)
                ->exists();

            $saldoAwal = $isNewProduct ? 0 : ($previousInventory ? $previousInventory->quantity_after : ($currentInventory ? $currentInventory->quantity : 0));

            // 2. STOCK MASUK - Kiriman pabrik, pembelian, dan adjustment plus
            $stockMasuk = InventoryHistory::where('outlet_id', $outlet->id)
                ->where('product_id', $product->id)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->where(function ($query) {
                    $query->where('type', 'purchase')           // Pembelian
                        ->orWhere('type', 'transfer_in')        // Transfer masuk
                        ->orWhere('type', 'shipment')           // Kiriman pabrik
                        ->orWhere('type', 'other')              // Lainnya (jika positif)
                        ->orWhere(function ($q) {
                            $q->where('type', 'adjustment')
                                ->where('quantity_change', '>', 0); // Adjustment plus
                        });
                })
                ->sum('quantity_change');

            // 3. STOCK KELUAR - Penjualan dan adjustment minus
            $stockKeluarPositive = InventoryHistory::where('outlet_id', $outlet->id)
                ->where('product_id', $product->id)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->where(function ($query) {
                    $query->where('type', 'sale')              // Penjualan
                        ->orWhere('type', 'transfer_out');      // Transfer keluar
                })
                ->sum('quantity_change');

            $stockKeluarNegative = InventoryHistory::where('outlet_id', $outlet->id)
                ->where('product_id', $product->id)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->where('type', 'adjustment')
                ->where('quantity_change', '<', 0)             // Adjustment minus
                ->sum('quantity_change');

            // Total stock keluar (konversi ke positif)
            $stockKeluar = abs($stockKeluarPositive) + abs($stockKeluarNegative);

            // 4. SALDO AKHIR = SALDO AWAL + STOCK MASUK - STOCK KELUAR
            $saldoAkhir = $saldoAwal + $stockMasuk - $stockKeluar;

            // 5. Stock aktual saat ini
            $stockAktual = $currentInventory ? $currentInventory->quantity : 0;
            
            // 6. Selisih antara stock aktual dengan saldo akhir
            $selisih = $stockAktual - $saldoAkhir;

            $results[] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'product_code' => $product->sku,
                'unit' => $product->unit,
                'saldo_awal' => $saldoAwal,
                'stock_masuk' => $stockMasuk,
                'stock_keluar' => $stockKeluar,
                'stock_akhir' => $saldoAkhir,
                'stock_aktual' => $stockAktual,
                'selisih' => $selisih,
                'status_stok' => $saldoAkhir < 0 ? 'Negatif' : ($selisih != 0 ? 'Tidak Sesuai' : 'Normal')
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'outlet' => $outlet->name,
                'periode' => [
                    'start_date' => $startDate->format('Y-m-d'),
                    'end_date' => $endDate->format('Y-m-d'),
                ],
                'products' => $results,
                'summary' => [
                    'total_saldo_awal' => collect($results)->sum('saldo_awal'),
                    'total_stock_masuk' => collect($results)->sum('stock_masuk'),
                    'total_stock_keluar' => collect($results)->sum('stock_keluar'),
                    'total_stock_akhir' => collect($results)->sum('stock_akhir'),
                    'total_selisih' => collect($results)->sum('selisih'),
                    'produk_stok_negatif' => collect($results)->where('saldo_akhir', '<', 0)->count(),
                    'produk_tidak_sesuai' => collect($results)->where('selisih', '!=', 0)->count()
                ]
            ]
        ]);
    }


    /**
     * Laporan inventory berdasarkan tanggal
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Outlet  $outlet
     * @return \Illuminate\Http\Response
     */
    public function inventoryByDate(Request $request, Outlet $outlet)
    {
        $request->validate([
            'date' => 'required|date_format:Y-m-d',
        ]);

        try {
            $date = Carbon::parse($request->date);
            $isToday = $date->isToday();

            // dd($date->toDateString(), Carbon::today()->toDateString());
            // dd($re);


            if ($isToday) {
                // Jika ini adalah stok hari ini, langsung ambil dari tabel inventory (realtime)
                $inventoryItems = Inventory::where('outlet_id', $outlet->id)
                    ->with(['product.category']) // Pastikan relasi category dimuat untuk menghindari query N+1
                    ->get();

                $totalValue = 0;
                $formattedItems = $inventoryItems->map(function ($item) use (&$totalValue) {
                    $value = $item->quantity * $item->product->price;
                    $totalValue += $value;

                    return [
                        'product_id' => $item->product_id,
                        'product_name' => $item->product->name,
                        'sku' => $item->product->sku,
                        'category' => $item->product->category->name,
                        'quantity' => $item->quantity,
                        'min_stock' => $item->min_stock,
                        'price' => $item->product->price,
                        'value' => $value,
                    ];
                });

                return $this->successResponse([
                    'date' => $date->format('Y-m-d'),
                    'outlet' => $outlet->name,
                    'is_realtime' => true,
                    'inventory_items' => $formattedItems,
                    'total_value' => $totalValue,
                ]);
            } else {
                // Jika ini adalah tanggal lampau, hitung dari riwayat stok
                $products = Product::whereHas('inventory', function ($query) use ($outlet) {
                    $query->where('outlet_id', $outlet->id);
                })->with('category')->get();

                $inventoryItems = [];
                $totalValue = 0;

                foreach ($products as $product) {
                    // Ambil histori inventory sampai tanggal tertentu
                    $lastHistory = DB::table('inventory_histories')
                        ->where('outlet_id', $outlet->id)
                        ->where('product_id', $product->id)
                        ->whereDate('created_at', '<=', $date)
                        ->orderBy('created_at', 'desc')
                        ->first();

                    if ($lastHistory) {
                        $quantity = $lastHistory->quantity_after;
                        $value = $quantity * $product->price;
                        $totalValue += $value;

                        $inventoryItems[] = [
                            'product_id' => $product->id,
                            'product_name' => $product->name,
                            'sku' => $product->sku,
                            'category' => $product->category->name,
                            'quantity' => $quantity,
                            'price' => $product->price,
                            'value' => $value,
                        ];
                    }
                }

                return $this->successResponse([
                    'date' => $date->format('Y-m-d'),
                    'outlet' => $outlet->name,
                    'is_realtime' => false,
                    'inventory_items' => $inventoryItems,
                    'total_value' => $totalValue,
                ]);
            }
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage());
        }
    }
    /**
     * Laporan shift berdasarkan outlet
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Outlet  $outlet
     * @return \Illuminate\Http\Response
     */
    public function shiftReport(Request $request, Outlet $outlet)
    {
        $request->validate([
            'start_date' => 'nullable|date_format:Y-m-d',
            'end_date' => 'nullable|date_format:Y-m-d',
            'cashier_id' => 'nullable|exists:users,id',
        ]);

        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::today()->subDays(7);
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::today()->endOfDay();

        $query = Shift::where('outlet_id', $outlet->id)
            ->whereBetween('start_time', [$startDate, $endDate])
            ->with(['user']);

        if ($request->cashier_id) {
            $query->where('user_id', $request->cashier_id);
        }

        $shifts = $query->orderBy('start_time', 'desc')->get();

        $totalStartingCash = $shifts->sum('starting_cash');
        $totalEndingCash = $shifts->sum('ending_cash');
        $totalExpectedCash = $shifts->sum('expected_cash');
        $totalDifference = $shifts->sum('cash_difference');

        // Ambil order per shift
        $shiftData = $shifts->map(function ($shift) {
            $orders = Order::where('shift_id', $shift->id)
                ->where('status', 'completed')
                ->get();

            $totalSales = $orders->sum('total');
            $orderCount = $orders->count();

            return [
                'id' => $shift->id,
                'cashier' => $shift->user->name,
                'start_time' => $shift->start_time,
                'end_time' => $shift->end_time,
                'duration' => $shift->end_time ? Carbon::parse($shift->start_time)->diffInHours(Carbon::parse($shift->end_time)) : null,
                'starting_cash' => $shift->starting_cash,
                'ending_cash' => $shift->ending_cash,
                'expected_cash' => $shift->expected_cash,
                'difference' => $shift->cash_difference,
                'status' => $shift->is_closed ? 'Closed' : 'Open',
                'sales' => $totalSales,
                'orders' => $orderCount,
            ];
        });

        return response()->json([
            'status' => true,
            'data' => [
                'outlet' => $outlet->name,
                'period' => [
                    'start_date' => $startDate->format('Y-m-d'),
                    'end_date' => $endDate->format('Y-m-d'),
                ],
                'summary' => [
                    'total_shifts' => $shifts->count(),
                    'total_starting_cash' => $totalStartingCash,
                    'total_ending_cash' => $totalEndingCash,
                    'total_expected_cash' => $totalExpectedCash,
                    'total_difference' => $totalDifference,
                ],
                'shifts' => $shiftData,
            ]
        ]);
    }

    /**
     * Dashboard summary berdasarkan outlet
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Outlet  $outlet
     * @return \Illuminate\Http\Response
     */
    public function dashboardSummaryOld(Request $request, Outlet $outlet)
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        $thisMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();
        $endOfYesterday = Carbon::yesterday()->endOfDay();

        // Sales hari ini
        $todaySales = Order::where('outlet_id', $outlet->id)
            ->whereBetween('created_at', [$today->startOfDay(), $today->endOfDay()])
            ->where('status', 'completed')
            ->sum('total');

        // Sales kemarin
        $yesterdaySales = Order::where('outlet_id', $outlet->id)
            ->whereBetween('created_at', [$yesterday->startOfDay(), $yesterday->endOfDay()])
            ->where('status', 'completed')
            ->sum('total');

        // Persentase perubahan
        $salesChange = $yesterdaySales > 0
            ? (($todaySales - $yesterdaySales) / $yesterdaySales) * 100
            : ($todaySales > 0 ? 100 : 0);

        // Order hari ini
        $todayOrders = Order::where('outlet_id', $outlet->id)
            ->whereDate('created_at', $today)
            ->where('status', 'completed')
            ->count();

        // Sales bulan ini
        $thisMonthSales = Order::where('outlet_id', $outlet->id)
            ->where('created_at', '>=', $thisMonth)
            ->where('status', 'completed')
            ->sum('total');

        // Sales bulan lalu
        $lastMonthSales = Order::where('outlet_id', $outlet->id)
            ->whereBetween('created_at', [$lastMonth, $thisMonth->copy()->subDay()])
            ->where('status', 'completed')
            ->sum('total');

        // Monthly change
        $monthlySalesChange = $lastMonthSales > 0
            ? (($thisMonthSales - $lastMonthSales) / $lastMonthSales) * 100
            : ($thisMonthSales > 0 ? 100 : 0);

        // Top 5 produk hari ini
        $topProducts = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select(
                'products.name',
                DB::raw('SUM(order_items.quantity) as quantity'),
                DB::raw('SUM(order_items.subtotal) as total')
            )
            ->where('orders.outlet_id', $outlet->id)
            ->whereDate('orders.created_at', $today)
            ->where('orders.status', 'completed')
            ->groupBy('products.name')
            ->orderByDesc('quantity')
            ->limit(5)
            ->get();

        // Stok yang perlu perhatian (low stock)
        $lowStock = Inventory::where('outlet_id', $outlet->id)
            ->where('quantity', '<', $outlet->min_stock)
            ->with('product')
            ->get()
            ->map(function ($item) {
                return [
                    'product_name' => $item->product->name,
                    'quantity' => $item->quantity,
                    'min_stock' => 10,
                ];
            });

        // Shift aktif
        $activeShift = Shift::where('outlet_id', $outlet->id)
            ->where('is_closed', false)
            ->with('user')
            ->first();

        return response()->json([
            'status' => true,
            'data' => [
                'outlet' => $outlet->name,
                'date' => $today->format('Y-m-d'),
                'sales' => [
                    'today' => $todaySales,
                    'yesterday' => $yesterdaySales,
                    'change_percentage' => round($salesChange, 2),
                    'this_month' => $thisMonthSales,
                    'last_month' => $lastMonthSales,
                    'monthly_change_percentage' => round($monthlySalesChange, 2),
                ],
                'orders_today' => $todayOrders,
                'top_products' => $topProducts,
                'low_stock_items' => $lowStock,
                'active_shift' => $activeShift ? [
                    'cashier' => $activeShift->user->name,
                    'started_at' => $activeShift->start_time,
                    'duration' => Carbon::parse($activeShift->start_time)->diffForHumans(null, true), // contoh: "3 hours"
                ] : null,
            ]
        ]);
    }

    public function dashboardSummary(Request $request, Outlet $outlet)
    {
        try {
            // Validate request parameters
            $request->validate([
                'start_date' => 'nullable|date_format:Y-m-d',
                'end_date' => 'nullable|date_format:Y-m-d',
            ]);

            // Set dates based on request or defaults
            $today = Carbon::today();
            $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::today();
            $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::today()->endOfDay();
            $yesterday = $startDate->copy()->subDay();
            $thisMonth = Carbon::now()->startOfMonth();
            $lastMonth = Carbon::now()->subMonth()->startOfMonth();

            // Get start and end of week
            $startOfWeek = Carbon::now()->startOfWeek();
            $endOfWeek = Carbon::now()->endOfWeek();

            // Data untuk response
            $responseData = [
                'outlet' => $outlet->name,
                'cash' => $outlet->cashRegisters->balance,
                'period' => [
                    'start_date' => $startDate->format('Y-m-d'),
                    'end_date' => $endDate->format('Y-m-d'),
                ],
                'summary' => [],
                'sales' => [],
                'daily_sales' => [],
                'category_sales' => [],
                'payment_method_sales' => [],
                'top_products' => [],
                'low_stock_items' => [],
                'active_shift' => null,
            ];

            try {
                // Daily sales data for selected period
                $sales = Order::where('outlet_id', $outlet->id)
                    ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
                    ->where('status', 'completed')
                    ->get();

                $totalSales = $sales->sum('total');
                $totalItems = OrderItem::whereIn('order_id', $sales->pluck('id'))->sum('quantity');
                $totalOrders = $sales->count();
                $averageOrderValue = $totalOrders > 0 ? $totalSales / $totalOrders : 0;

                $responseData['summary'] = [
                    'total_sales' => $totalSales,
                    'total_orders' => $totalOrders,
                    'total_items' => $totalItems,
                    'average_order_value' => $averageOrderValue,
                ];

                // Previous period comparison
                $previousPeriodLength = $endDate->diffInDays($startDate) + 1;
                $previousPeriodStart = $startDate->copy()->subDays($previousPeriodLength);
                $previousPeriodEnd = $startDate->copy()->subDay();

                $previousPeriodSales = Order::where('outlet_id', $outlet->id)
                    ->whereBetween('created_at', [$previousPeriodStart, $previousPeriodEnd])
                    ->where('status', 'completed')
                    ->sum('total');

                $salesChange = $previousPeriodSales > 0
                    ? (($totalSales - $previousPeriodSales) / $previousPeriodSales) * 100
                    : ($totalSales > 0 ? 100 : 0);

                // Monthly sales data
                $thisMonthSales = Order::where('outlet_id', $outlet->id)
                    ->whereMonth('created_at', $today->month)
                    ->whereYear('created_at', $today->year)
                    ->where('status', 'completed')
                    ->sum('total');

                $lastMonthSales = Order::where('outlet_id', $outlet->id)
                    ->whereMonth('created_at', $lastMonth->month)
                    ->whereYear('created_at', $lastMonth->year)
                    ->where('status', 'completed')
                    ->sum('total');

                $monthlySalesChange = $lastMonthSales > 0
                    ? (($thisMonthSales - $lastMonthSales) / $lastMonthSales) * 100
                    : ($thisMonthSales > 0 ? 100 : 0);

                $responseData['sales'] = [
                    'current_period' => $totalSales,
                    'previous_period' => $previousPeriodSales,
                    'change_percentage' => round($salesChange, 2),
                    'this_month' => $thisMonthSales,
                    'last_month' => $lastMonthSales,
                    'monthly_change_percentage' => round($monthlySalesChange, 2),
                ];

                // Daily sales data for selected period
                $dailySales = Order::where('outlet_id', $outlet->id)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->where('status', 'completed')
                    ->get();

                $dailySalesData = [];

                // Get all dates between start and end date
                $period = \Carbon\CarbonPeriod::create($startDate, $endDate);

                // Initialize data for each day
                foreach ($period as $date) {
                    $dayName = $date->format('Y-m-d');
                    $dailySalesData[$dayName] = [
                        'orders' => 0,
                        'sales' => 0,
                        'items' => 0,
                        'average_order' => 0,
                    ];
                }

                // Fill in the actual data
                foreach ($dailySales as $order) {
                    $dayName = Carbon::parse($order->created_at)->format('Y-m-d');
                    $dayItems = OrderItem::where('order_id', $order->id)->sum('quantity');

                    $dailySalesData[$dayName]['orders']++;
                    $dailySalesData[$dayName]['sales'] += $order->total;
                    $dailySalesData[$dayName]['items'] += $dayItems;
                }

                // Calculate averages
                foreach ($dailySalesData as &$data) {
                    $data['average_order'] = $data['orders'] > 0 ?
                        round($data['sales'] / $data['orders'], 2) : 0;
                    $data['sales'] = round($data['sales'], 2);
                }

                $responseData['daily_sales'] = $dailySalesData;

                // Category sales data
                $responseData['category_sales'] = DB::table('order_items')
                    ->join('orders', 'order_items.order_id', '=', 'orders.id')
                    ->join('products', 'order_items.product_id', '=', 'products.id')
                    ->join('categories', 'products.category_id', '=', 'categories.id')
                    ->select(
                        'categories.name',
                        DB::raw('SUM(order_items.quantity) as total_quantity'),
                        DB::raw('SUM(order_items.subtotal) as total_sales')
                    )
                    ->where('orders.outlet_id', $outlet->id)
                    ->whereBetween('orders.created_at', [$startDate, $endDate])
                    ->where('orders.status', 'completed')
                    ->groupBy('categories.name')
                    ->get();

                // Payment method sales data
                $responseData['payment_method_sales'] = $sales->groupBy('payment_method')
                    ->map(function ($items) {
                        return [
                            'count' => $items->count(),
                            'total' => $items->sum('total'),
                        ];
                    });

                // Top products data
                $responseData['top_products'] = DB::table('order_items')
                    ->join('orders', 'order_items.order_id', '=', 'orders.id')
                    ->join('products', 'order_items.product_id', '=', 'products.id')
                    ->select(
                        'products.name',
                        DB::raw('SUM(order_items.quantity) as quantity'),
                        DB::raw('SUM(order_items.subtotal) as total')
                    )
                    ->where('orders.outlet_id', $outlet->id)
                    ->whereBetween('orders.created_at', [$startDate, $endDate])
                    ->where('orders.status', 'completed')
                    ->groupBy('products.name')
                    ->orderByDesc('quantity')
                    ->limit(5)
                    ->get();

                // Low stock items
                $minStock = $outlet->min_stock ?? 10;
                $responseData['low_stock_items'] = Inventory::where('outlet_id', $outlet->id)
                    ->where('quantity', '<', $minStock)
                    ->with('product')
                    ->get()
                    ->map(function ($item) use ($minStock) {
                        return [
                            'product_name' => $item->product->name,
                            'quantity' => $item->quantity,
                            'min_stock' => $minStock,
                        ];
                    });

                // Active shift
                $activeShift = Shift::where('outlet_id', $outlet->id)
                    ->with('user')
                    ->first();

                if ($activeShift) {
                    $responseData['active_shift'] = [
                        'cashier' => $activeShift->user->name,
                        'started_at' => $activeShift->start_time,
                        'duration' => Carbon::parse($activeShift->start_time)->diffForHumans(null, true),
                    ];
                }

                return $this->successResponse($responseData, 'Successfully getting dashboard data');
            } catch (\Exception $e) {
                \Log::error('Error in data gathering: ' . $e->getMessage());
                return $this->errorResponse('Error in data gathering', $e->getMessage());
            }
        } catch (\Exception $e) {
            \Log::error('Daily sales error: ' . $e->getMessage());
            return $this->errorResponse('Error in data gathering', $e->getMessage());
        }
    }

    /**
     * Laporan penjualan per kategori berdasarkan rentang tanggal
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Outlet  $outlet
     * @return \Illuminate\Http\Response
     */
    public function salesByCategory(Request $request, Outlet $outlet)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();

        // Ambil semua kategori yang memiliki penjualan
        $categories = DB::table('categories')
            ->join('products', 'categories.id', '=', 'products.category_id')
            ->join('order_items', 'products.id', '=', 'order_items.product_id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->select(
                'categories.id as category_id',
                'categories.name as category_name'
            )
            ->where('orders.outlet_id', $outlet->id)
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->where('orders.status', 'completed')
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('categories.name')
            ->get();

        // Hitung total penjualan keseluruhan untuk persentase
        $totalSales = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.outlet_id', $outlet->id)
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->where('orders.status', 'completed')
            ->sum('order_items.subtotal');

        $result = [];
        $totalQuantityAll = 0;
        $totalSalesAll = 0;
        $totalOrdersAll = 0;

        foreach ($categories as $category) {
            // Ambil data produk dalam kategori ini
            $products = DB::table('products')
                ->join('order_items', 'products.id', '=', 'order_items.product_id')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->select(
                    'products.id as product_id',
                    'products.name as product_name',
                    'products.sku as product_sku',
                    'products.unit as product_unit',
                    DB::raw('SUM(order_items.quantity) as total_quantity'),
                    DB::raw('SUM(order_items.subtotal) as total_sales'),
                    DB::raw('COUNT(DISTINCT orders.id) as order_count')
                )
                ->where('orders.outlet_id', $outlet->id)
                ->where('products.category_id', $category->category_id)
                ->whereBetween('orders.created_at', [$startDate, $endDate])
                ->where('orders.status', 'completed')
                ->groupBy('products.id', 'products.name', 'products.sku', 'products.unit')
                ->orderBy('total_sales', 'desc')
                ->get();

            // Hitung total untuk kategori ini
            $categoryTotalQuantity = $products->sum('total_quantity');
            $categoryTotalSales = $products->sum('total_sales');
            $categoryOrderCount = $products->sum('order_count');

            // Tambahkan ke total keseluruhan
            $totalQuantityAll += $categoryTotalQuantity;
            $totalSalesAll += $categoryTotalSales;
            $totalOrdersAll += $categoryOrderCount;

            // Format data kategori
            $result[] = [
                'category_id' => $category->category_id,
                'category_name' => $category->category_name,
                'total_quantity' => $categoryTotalQuantity,
                'total_sales' => $categoryTotalSales,
                'order_count' => $categoryOrderCount,
                'sales_percentage' => $totalSales > 0 ? round(($categoryTotalSales / $totalSales) * 100, 2) : 0,
                'products' => $products->map(function ($product) use ($categoryTotalSales) {
                    return [
                        'product_id' => $product->product_id,
                        'product_name' => $product->product_name,
                        'product_sku' => $product->product_sku,
                        'quantity' => $product->total_quantity,
                        'sales' => $product->total_sales,
                        'product_unit' => $product->product_unit,
                        'order_count' => $product->order_count,
                        'sales_percentage' => $categoryTotalSales > 0
                            ? round(($product->total_sales / $categoryTotalSales) * 100, 2)
                            : 0,
                    ];
                })
            ];
        }

        return response()->json([
            'status' => true,
            'data' => [
                'date_range' => [
                    'start_date' => $startDate->format('Y-m-d'),
                    'end_date' => $endDate->format('Y-m-d'),
                ],
                'outlet' => $outlet->name,
                'summary' => [
                    'total_categories' => count($result),
                    'total_products' => array_reduce($result, function ($carry, $item) {
                        return $carry + count($item['products']);
                    }, 0),
                    'total_quantity' => $totalQuantityAll,
                    'total_sales' => $totalSalesAll,
                    'total_orders' => $totalOrdersAll,
                ],
                'categories' => $result
            ]
        ]);
    }

    public function inventoryApprovals(Request $request, Outlet $outlet)
    {
        $request->validate([
            'start_date' => 'required|date_format:Y-m-d',
            'end_date' => 'required|date_format:Y-m-d',
        ]);

        $startDate = $request->start_date;
        $endDate = $request->end_date . ' 23:59:59';

        // Query untuk data approved dan rejected sekaligus dengan eager loading
        $histories = InventoryHistory::with(['product', 'approver'])
            ->where('outlet_id', $outlet->id)
            ->whereIn('status', ['approved', 'rejected'])
            ->whereNotNull('approved_at')
            ->whereBetween('approved_at', [$startDate, $endDate])
            ->orderBy('approved_at', 'desc')
            ->get();

        // Pisahkan data berdasarkan status
        $approved = $histories->where('status', 'approved')->values();
        $rejected = $histories->where('status', 'rejected')->values();

        return response()->json([
            'approved' => $approved,
            'rejected' => $rejected
        ]);
    }

    public function listProductByMember(Request $request, Outlet $outlet)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();

        // Query untuk mendapatkan penjualan per member
        $members = DB::table('orders')
            ->leftJoin('members', 'orders.member_id', '=', 'members.id')
            ->select(
                'members.id as member_id',
                DB::raw('COALESCE(members.name, "Member Umum") as member_name'),
                DB::raw('COUNT(DISTINCT orders.id) as total_orders'),
                DB::raw('SUM(orders.total) as total_spent')
            )
            ->where('orders.outlet_id', $outlet->id)
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->where('orders.status', 'completed')
            ->groupBy('members.id', 'members.name')
            ->orderBy('total_spent', 'desc')
            ->get();

        // Query untuk mendapatkan produk yang dibeli setiap member
        $memberProducts = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->leftJoin('members', 'orders.member_id', '=', 'members.id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->select(
                'members.id as member_id',
                'products.id as product_id',
                'products.name as product_name',
                'products.sku as product_sku',
                'categories.name as category_name',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.subtotal) as total_spent')
            )
            ->where('orders.outlet_id', $outlet->id)
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->where('orders.status', 'completed')
            ->groupBy('members.id', 'products.id', 'products.name', 'products.sku', 'categories.name')
            ->get();

        // Kelompokkan produk berdasarkan member
        $memberProductsGrouped = $memberProducts->groupBy('member_id');

        // Gabungkan data member dengan produk yang dibeli
        $membersWithProducts = $members->map(function ($member) use ($memberProductsGrouped) {
            $products = $memberProductsGrouped->get($member->member_id, collect());

            $member->products = $products->map(function ($product) {
                return [
                    'product_id' => $product->product_id,
                    'product_name' => $product->product_name,
                    'sku' => $product->product_sku,
                    'category' => $product->category_name,
                    'quantity' => $product->total_quantity,
                    'total_spent' => $product->total_spent
                ];
            });

            return $member;
        });

        // Hitung statistik summary
        $totalSales = $members->sum('total_spent');
        $totalOrders = DB::table('orders')
            ->where('outlet_id', $outlet->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->count();
        $averageOrderValue = $totalOrders > 0 ? $totalSales / $totalOrders : 0;

        return response()->json([
            'status' => true,
            'data' => [
                'date_range' => [
                    'start_date' => $startDate->format('Y-m-d'),
                    'end_date' => $endDate->format('Y-m-d'),
                ],
                'outlet' => $outlet->name,
                'summary' => [
                    'total_sales' => $totalSales,
                    'total_orders' => $totalOrders,
                    'average_order_value' => $averageOrderValue,
                    'total_members' => $members->count()
                ],
                'members' => $membersWithProducts
            ]
        ]);
    }
}
