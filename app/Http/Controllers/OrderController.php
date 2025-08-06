<?php

namespace App\Http\Controllers;

use App\Models\CashRegister;
use App\Models\Inventory;
use App\Models\InventoryHistory;
use App\Models\Order;
use App\Traits\ApiResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    use ApiResponse;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function storeOld(Request $request)
    {
        $request->validate([
            'outlet_id' => 'required|exists:outlets,id',
            'shift_id' => 'required|exists:shifts,id',
            'items' => 'required|array', // Array of items
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,card,transfer,other',
            'notes' => 'nullable|string',
            'total_paid' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
        ]);


        try {
            DB::beginTransaction();
            $subtotal = collect($request->items)->sum(function ($item) {
                return $item['quantity'] * $item['price'];
            });

            // Hitung total (subtotal + tax - discount)
            $tax = $request->tax ?? 0;
            $discount = $request->discount ?? 0;
            $total = $subtotal + $tax - $discount;
            $change = $request->total_paid - $total;
            // Buat order
            $order = Order::create([
                'order_number' => 'INV-' . time() . '-' . Str::random(6),
                'outlet_id' => $request->outlet_id,
                'user_id' => $request->user()->id,
                'shift_id' => $request->shift_id,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'discount' => $discount,
                'total' => $total,
                'total_paid' => $request->total_paid ?? $total,
                'change' => $change,
                'payment_method' => $request->payment_method,
                'status' => 'pending',
                'notes' => $request->notes,
            ]);

            foreach ($request->items as $item) {
                $order->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);

                $inventory = Inventory::where('outlet_id', $request->outlet_id)
                    ->where('product_id', $item['product_id'])
                    ->first();

                if ($inventory) {
                    $quantityBefore = $inventory->quantity;
                    $inventory->quantity -= $item['quantity']; // Kurangi stok
                    $inventory->save();

                    InventoryHistory::create([
                        'outlet_id' => $request->outlet_id,
                        'product_id' => $item['product_id'],
                        'quantity_before' => $quantityBefore,
                        'quantity_after' => $inventory->quantity,
                        'quantity_change' => -$item['quantity'], // Nilai minus karena pengurangan
                        'type' => 'sale',
                        'notes' => 'Penjualan melalui POS, Invoice #' . $order->order_number,
                        'user_id' => $request->user()->id,
                    ]);
                }
            }

            $order->update(['status' => 'completed']);

            DB::commit();

            return $this->successResponse($order, 'Order berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        
        // dd($request->all());
        
        $request->validate([
            'outlet_id' => 'required|exists:outlets,id',
            'shift_id' => 'required|exists:shifts,id',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.discount' => 'required|numeric|min:0',
            'items.*.price' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,qris,transfer',
            'notes' => 'nullable|string',
            'total_paid' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'discount' => 'required|numeric|min:0',
            'member_id' => 'nullable|exists:members,id',
        ]);

        try {
            DB::beginTransaction();

            // 1. Hitung subtotal awal (tanpa diskon)
            $rawSubtotal = collect($request->items)->sum(function ($item) {
                return $item['quantity'] * $item['price'];
            });

            // 2. Hitung total diskon item (pastikan ini nilai NOMINAL, bukan persentase)
            $itemDiscountTotal = collect($request->items)->sum(function ($item) {
                return floatval($item['discount'] ?? 0);
            });

            // 3. Batasi diskon agar tidak melebihi subtotal
            $totalDiscount = min($itemDiscountTotal, $rawSubtotal);

            // 4. Hitung subtotal setelah diskon
            $orderSubtotal = $rawSubtotal - $totalDiscount;

            // 5. Tambahkan pajak
            $tax = floatval($request->tax ?? 0);

            // 6. Hitung total akhir (tidak boleh negatif)
            $total = max(0, $orderSubtotal + $tax);

            // 7. Hitung kembalian
            $totalPaid = floatval($request->total_paid ?? 0);
            if ($request->payment_method === 'qris' || $request->payment_method === 'transfer') {
                $totalPaid = $total;
                $change = 0;
            } else {
                $change = $totalPaid - $total;
            }

            // Buat order
            $order = Order::create([
                'order_number' => 'INV-' . time() . '-' . strtoupper(Str::random(6)),
                'outlet_id' => $request->outlet_id,
                'user_id' => $request->user()->id,
                'shift_id' => $request->shift_id,
                'subtotal' => $rawSubtotal,
                'tax' => $tax,
                'discount' => $totalDiscount,
                'total' => $total,
                'total_paid' => $totalPaid,
                'change' => $change,
                'payment_method' => $request->payment_method,
                'status' => 'pending',
                'notes' => $request->notes,
                'member_id' => $request->member_id
            ]);

            // Buat order items
            foreach ($request->items as $item) {
                $itemTotal = $item['quantity'] * $item['price'];
                $itemDiscount = min(floatval($item['discount'] ?? 0), $itemTotal);
                $subtotal = $itemTotal - $itemDiscount;

                $order->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'discount' => $itemDiscount,
                    'subtotal' => $subtotal, // Ini HARUS positif
                ]);


                // Update inventory (tidak diubah)
                $inventory = Inventory::where('outlet_id', $request->outlet_id)
                    ->where('product_id', $item['product_id'])
                    ->first();

                if ($inventory) {
                    $quantityBefore = $inventory->quantity;
                    $inventory->decrement('quantity', $item['quantity']);

                    InventoryHistory::create([
                        'outlet_id' => $request->outlet_id,
                        'product_id' => $item['product_id'],
                        'quantity_before' => $quantityBefore,
                        'quantity_after' => $inventory->quantity,
                        'quantity_change' => -$item['quantity'],
                        'type' => 'sale',
                        'notes' => 'Penjualan POS, Invoice #' . $order->order_number,
                        'user_id' => $request->user()->id,
                    ]);
                }
            }

            // Tidak diubah
            $cashRegister = CashRegister::where('outlet_id', $request->outlet_id)->first();
            $cashRegister->addCash($total, $request->user()->id, $request->shift_id, 'Penjualan POS, Invoice #' . $order->order_number, 'pos');

            $order->update(['status' => 'completed']);

            DB::commit();

            $data = [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'outlet' => $order->outlet->name,
                'user' => $order->user->name,
                'total' => $order->total,
                'status' => $order->status,

                'subtotal' => $order->subtotal,
                'tax' => $order->tax,
                'discount' => $order->discount,
                'total_paid' => $order->total_paid,
                'change' => $order->change,

                'payment_method' => $order->payment_method,
                'created_at' => $order->created_at->format('d/m/Y H:i'),
                'items' => $order->items->map(function ($item) {
                    return [
                        'product' => $item->product->name,
                        'quantity' => $item->quantity,
                        'price' => $item->price,
                        'discount' => $item->discount,
                        'total' => $item->quantity * $item->price
                    ];
                }),
                'member' => $order->member ? [
                    'name' => $order->member->name,
                    'member_code' => $order->member->member_code
                ] : null
            ];

            return $this->successResponse($data, "Succesfully created order");
            // return $this->successResponse($order->load(['items.product', 'user']), 'Order berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        //
    }

    public function cancelOrder($orderId)
    {
        // Mulai transaksi
        DB::beginTransaction();

        try {
            $order = Order::find($orderId);

            if (!$order) {
                return $this->errorResponse('Order tidak ditemukan', 404);
            }

            // Kembalikan stok produk
            foreach ($order->items as $item) {
                $inventory = Inventory::where('outlet_id', $order->outlet_id)
                    ->where('product_id', $item->product_id)
                    ->first();

                if ($inventory) {
                    $quantityBefore = $inventory->quantity;
                    $inventory->quantity += $item->quantity; // Tambahkan stok kembali
                    $inventory->save();

                    // Catat riwayat perubahan stok
                    InventoryHistory::create([
                        'outlet_id' => $order->outlet_id,
                        'product_id' => $item->product_id,
                        'quantity_before' => $quantityBefore,
                        'quantity_after' => $inventory->quantity,
                        'quantity_change' => $item->quantity, // Nilai positif karena penambahan
                        'type' => 'sale',
                        'notes' => 'Pembatalan Order #' . $order->order_number,
                        'user_id' => $order->user_id,
                    ]);
                }
            }

            // Update status order menjadi cancelled
            $order->update(['status' => 'cancelled']);

            $cashRegister = CashRegister::where('outlet_id', $order->outlet_id)->first();
            $cashRegister->subtractCash($order->total, $order->user_id, $order->shift_id, 'Pembatalan Order #' . $order->order_number, 'pos');

            // Commit transaksi jika semua operasi berhasil
            DB::commit();

            return $this->successResponse($order, 'Order berhasil dibatalkan');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage());
        }
    }

    public function oneMonthRevenue($outletId)
    {
        try {
            $from = Carbon::now()->startOfMonth();
            $to = Carbon::now()->endOfMonth();

            $total = Order::where('status', 'completed')
                ->where('outlet_id', $outletId)
                ->whereBetween('created_at', [
                    $from,
                    $to
                ])
                ->sum('total');

            $data = [
                'from' => $from->format('d/m/Y'),
                'to' => $to->format('d/m/Y'),
                'total' => $total
            ];

            return $this->successResponse($data, 'Succesfully getting one month revenue');
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage());
        }
    }

    // public function orderHistory(Request $request)
    // {
    //     try {
    //         $validator = Validator::make($request->query(), [
    //             'outlet_id' => 'nullable|exists:outlets,id',
    //             'member_id' => 'nullable|exists:members,id',
    //             'date_from' => 'nullable|date',
    //             'date_to' => 'nullable|date|after_or_equal:date_from',
    //         ]);

    //         if ($validator->fails()) {
    //             return $this->errorResponse($validator->errors(), 422);
    //         }

    //         $query = Order::query();

    //         if ($request->filled('outlet_id')) {
    //             $query->where('outlet_id', $request->outlet_id);
    //         }

    //         if ($request->filled('member_id')) {
    //             $query->where('member_id', $request->member_id);
    //         }

    //         if ($request->filled('date_from') && $request->filled('date_to')) {
    //             $query->whereBetween('created_at', [
    //                 $request->date_from,
    //                 $request->date_to . ' 23:59:59'
    //             ]);
    //         }

    //         $totalOrders = $query->count();
    //         $totalRevenue = (clone $query)->where('status', 'completed')->sum('total');

    //         $orders = $query->with([
    //             'items.product:id,name,sku',
    //             'outlet:id,name',
    //             'shift:id',
    //             'user:id,name'
    //         ])->has('outlet')->has('user')->latest()->get();

    //         // Transformasi respons
    //         $orders->transform(function ($order) {
    //             return [
    //                 'id' => $order->id,
    //                 'order_number' => $order->order_number,
    //                 'outlet' => $order->outlet->name,
    //                 'user' => $order->user->name,
    //                 'total' => $order->total,
    //                 'status' => $order->status,

    //                 'subtotal' => $order->subtotal,
    //                 'tax' => $order->tax,
    //                 'discount' => $order->discount,
    //                 'total_paid' => $order->total_paid,
    //                 'change' => $order->change,

    //                 'payment_method' => $order->payment_method,
    //                 'created_at' => $order->created_at->format('d/m/Y H:i'),
    //                 'items' => $order->items->map(function ($item) {
    //                     return [
    //                         'product' => $item->product->name,
    //                         'sku' => $item->product->sku,
    //                         'unit' => $item->product->unit,
    //                         'quantity' => $item->quantity,
    //                         'price' => $item->price,
    //                         'discount' => $item->discount,
    //                         'total' => $item->quantity * $item->price
    //                     ];
    //                 }),
    //                 'member' => $order->member ? [
    //                     'name' => $order->member->name,
    //                     'member_code' => $order->member->member_code
    //                 ] : null
    //             ];
    //         });

    //         // Tambahkan informasi total ke dalam respons
    //         $response = [
    //             'date_from' => date('d-m-Y', strtotime($request->date_from)),
    //             'date_to' => date('d-m-Y', strtotime($request->date_to)),
    //             'total_orders' => $totalOrders,
    //             'total_revenue' => $totalRevenue,
    //             'orders' => $orders
    //         ];

    //         return $this->successResponse($response, 'Riwayat order berhasil diambil');
    //     } catch (\Exception $e) {
    //         return $this->errorResponse('Terjadi kesalahan: ' . $e->getMessage());
    //     }
    // }

    public function orderHistory(Request $request)
    {
        // knnnnninini
        try {
            $validator = Validator::make($request->query(), [
                'outlet_id' => 'nullable|exists:outlets,id',
                'member_id' => 'nullable|exists:members,id',
                'date_from' => 'nullable|date',
                'date_to' => 'nullable|date|after_or_equal:date_from',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse($validator->errors(), 422);
            }

            $query = Order::query();

            if ($request->filled('outlet_id')) {
                $query->where('outlet_id', $request->outlet_id);
            }

            if ($request->filled('member_id')) {
                $query->where('member_id', $request->member_id);
            }

            if ($request->filled('date_from') && $request->filled('date_to')) {
                $query->whereBetween('created_at', [
                    $request->date_from,
                    $request->date_to . ' 23:59:59'
                ]);
            }

            $totalOrders = $query->count();
            $totalRevenue = (clone $query)->where('status', 'completed')->sum('total');

            // Hitung total item yang terjual
            $totalItemsSold = 0;
            $completedOrdersQuery = (clone $query)->where('status', 'completed')->with(['items.product' => function ($q) {
                $q->withTrashed()->select('id', 'name', 'sku', 'unit');
            }]);
            $completedOrders = $completedOrdersQuery->get();

            foreach ($completedOrders as $order) {
                $totalItemsSold += $order->items->sum('quantity');
            }

            // Hitung rata-rata penjualan
            $averageOrderValue = $totalOrders > 0 ? ($totalRevenue / $totalOrders) : 0;

            $orders = $query->with([
                'items.product' => function ($q) {
                    $q->withTrashed()->select('id', 'name', 'sku', 'unit');
                },
                'outlet:id,name',
                'shift:id',
                'user:id,name'
            ])->has('outlet')->has('user')->latest()->get();

            // $totalDiscount = $query->where('status', 'completed')->sum('discount');
            // $grossSales = $order->where('status', 'completed')->sum('subtotal');
            $totalDiscount = $query->where('status', 'completed')->sum('discount');
            $grossSales = $query->where('status', 'completed')->sum('subtotal');

            // Transformasi respons
            $orders->transform(function ($order) {
                return [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'outlet' => $order->outlet->name,
                    'user' => $order->user->name,
                    'total' => $order->total,
                    'status' => $order->status,
                    'subtotal' => $order->subtotal,
                    'tax' => $order->tax,
                    'discount' => $order->discount,
                    'total_paid' => $order->total_paid,
                    'change' => $order->change,
                    'payment_method' => $order->payment_method,
                    'created_at' => $order->created_at->format('d/m/Y H:i'),
                    'items' => $order->items->map(function ($item) {
                        return [
                            'product' => $item->product ? $item->product->name : 'Produk tidak tersedia',
                            'sku' => $item->product ? $item->product->sku : '',
                            'unit' => $item->product ? ($item->product->unit ?? 'pcs') : 'pcs',
                            'quantity' => $item->quantity,
                            'price' => $item->price,
                            'discount' => $item->discount,
                            'total' => $item->quantity * $item->price
                        ];
                    }),
                    'member' => $order->member ? [
                        'name' => $order->member->name,
                        'member_code' => $order->member->member_code
                    ] : null
                ];
            });

            $response = [
                'date_from' => $request->date_from ? date('d-m-Y', strtotime($request->date_from)) : null,
                'date_to' => $request->date_to ? date('d-m-Y', strtotime($request->date_to)) : null,
                'total_orders' => $totalOrders,
                'total_revenue' => $totalRevenue,
                'average_order_value' => round($averageOrderValue, 2),
                'total_discount' => $totalDiscount,
                'total_items_sold' => $totalItemsSold,
                'gross_sales' => $grossSales,
                'orders' => $orders
            ];

            return $this->successResponse($response, 'Riwayat order berhasil diambil');
        } catch (\Exception $e) {
            return $this->errorResponse('Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function orderHistoryAdmin(Request $request)
    {
        try {
            // Validasi parameter query (hapus validasi date dan per_page)
            $validator = Validator::make($request->query(), [
                'outlet_id' => 'nullable|exists:outlets,id',
                'status' => 'nullable|in:pending,completed,canceled',
                'search' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse($validator->errors(), 422);
            }

            $user = $request->user();

            // Query dasar
            $query = Order::query();

            // Filter tambahan
            if ($request->filled('outlet_id')) {
                $query->where('outlet_id', $request->outlet_id);
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('search')) {
                $searchTerm = '%' . $request->search . '%';
                $query->where('order_number', 'like', $searchTerm);
            }

            // Hitung total jumlah pesanan dan total pendapatan
            $totalOrders = $query->count();
            $totalRevenue = $query->sum('total');

            // Ambil semua hasil (tanpa pagination)
            $orders = $query->with([
                'items.product:id,name,sku',
                'outlet:id,name',
                'shift:id',
                'user:id,name'
            ])->latest()->get();

            // Transformasi data
            $orders = $orders->map(function ($order) {
                return [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'outlet' => $order->outlet->name,
                    'user' => $order->user->name,
                    'total' => $order->total,
                    'status' => $order->status,

                    'subtotal' => $order->subtotal,
                    'tax' => $order->tax,
                    'discount' => $order->discount,
                    'total_paid' => $order->total_paid,
                    'change' => $order->change,

                    'payment_method' => $order->payment_method,
                    'created_at' => $order->created_at->format('d/m/Y H:i'),
                    'items' => $order->items->map(function ($item) {
                        return [
                            'product' => $item->product->name,
                            'quantity' => $item->quantity,
                            'price' => $item->price,
                            'total' => $item->quantity * $item->price
                        ];
                    })
                ];
            });

            $response = [
                'total_orders' => $totalOrders,
                'total_revenue' => $totalRevenue,
                'orders' => $orders
            ];

            return $this->successResponse($response, 'Riwayat order berhasil diambil');
        } catch (\Exception $e) {
            return $this->errorResponse('Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
