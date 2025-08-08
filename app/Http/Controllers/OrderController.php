<?php

namespace App\Http\Controllers;

use App\Mail\ApprovalRequest;
use App\Models\CashRegister;
use App\Models\Inventory;
use App\Models\InventoryHistory;
use App\Models\Order;
use App\Traits\ApiResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
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

        $items = json_decode($request->input('items'), true);
        $bonus_items = json_decode($request->input('bonus_items'), true);

        // Replace the request values with decoded arrays
        $request->merge([
            'items' => $items,
            'bonus_items' => $bonus_items,
        ]);

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
            'payment_proof' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120', // Wajib upload bukti (max 5MB)
        ]);

        try {
            DB::beginTransaction();

            // Handle payment proof upload (sama seperti upload gambar produk)
            $paymentProofPath = null;
            if ($request->hasFile('payment_proof')) {
                $file = $request->file('payment_proof');
                $fileName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();

                // Pastikan direktori ada
                $uploadDir = public_path('uploads/payment_proofs');
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                // Pindahkan file ke direktori public
                $file->move($uploadDir, $fileName);
                $paymentProofPath = 'payment_proofs/' . $fileName;
            }

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

            // Buat order dengan status pending untuk approval
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
                'status' => 'pending', // Status transaksi
                'approval_status' => 'pending', // Status approval (default pending)
                'payment_proof' => $paymentProofPath,
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

            // Jangan tambahkan ke kas register dulu, tunggu approval
            // Transaksi akan ditambahkan ke kas saat diapprove

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

    /**
     * Request cancellation/refund (untuk kasir)
     */
    public function requestCancellation(Request $request, $orderId)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
            'notes' => 'nullable|string|max:1000'
        ]);

        try {
            $order = Order::findOrFail($orderId);
            $user = auth()->user();

            if (!$order->canRequestCancellation()) {
                return $this->errorResponse('Transaksi ini tidak dapat dibatalkan/direfund', 400);
            }

            $success = $order->requestCancellation(
                $user,
                $request->input('reason'),
                $request->input('notes')
            );

            if (!$success) {
                return $this->errorResponse('Gagal mengajukan pembatalan/refund', 400);
            }

            $type = $order->cancellation_type;

            // Load necessary relationships for email
            $order->load(['user', 'outlet', 'items.product']);

            $supervisors = $user->outlet->supervisor;

            // Log::alert($supervisors);
            foreach ($supervisors as $supervisor) {

                if (empty($supervisor->email)) {
                    Log::warning("Supervisor {$supervisor->name} tidak punya email, skip kirim email");
                    continue;
                }

                // Determine request type for email
                $requestType = $order->status === 'pending' ? 'PEMBATALAN TRANSAKSI' : 'REFUND TRANSAKSI';

                $data = [
                    'supervisor_name' => $supervisor->name,
                    'cashier_name' => $user->name,
                    'approval_request' => $requestType,
                    'approval_data' => $order
                ];

                Log::info("Sending email to supervisor: {$supervisor->email} for {$requestType} of order {$order->order_number}");
                Mail::to($supervisor->email)->send(new ApprovalRequest($data));
            }

            return $this->successResponse($order->fresh(), "Permintaan {$type} berhasil diajukan dan menunggu persetujuan admin");
        } catch (\Exception $e) {
            return $this->errorResponse('Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Admin approve cancellation/refund
     */
    public function approveCancellation(Request $request, $orderId)
    {
        $request->validate([
            'admin_notes' => 'nullable|string|max:1000'
        ]);

        DB::beginTransaction();
        try {
            $order = Order::findOrFail($orderId);
            $user = auth()->user();

            if ($order->cancellation_status !== 'requested') {
                return $this->errorResponse('Tidak ada permintaan pembatalan/refund yang pending', 400);
            }

            $isRefund = ($order->status === 'completed');

            // Approve the cancellation request
            $order->approveCancellation($user, $request->input('admin_notes'));

            // Process the actual cancellation/refund
            $this->processCancellationRefund($order, $isRefund);

            DB::commit();

            $type = $isRefund ? 'refund' : 'pembatalan';
            return $this->successResponse($order->fresh(), "Permintaan {$type} berhasil disetujui dan diproses");
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Admin reject cancellation/refund
     */
    public function rejectCancellation(Request $request, $orderId)
    {
        $request->validate([
            'admin_notes' => 'required|string|max:1000'
        ]);

        try {
            $order = Order::findOrFail($orderId);
            $user = auth()->user();

            if ($order->cancellation_status !== 'requested') {
                return $this->errorResponse('Tidak ada permintaan pembatalan/refund yang pending', 400);
            }

            $success = $order->rejectCancellation($user, $request->input('admin_notes'));

            if (!$success) {
                return $this->errorResponse('Gagal menolak permintaan', 400);
            }

            $type = $order->cancellation_type;
            return $this->successResponse($order->fresh(), "Permintaan {$type} berhasil ditolak");
        } catch (\Exception $e) {
            return $this->errorResponse('Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Get pending cancellation requests
     */
    public function getPendingCancellations(Request $request)
    {
        try {
            $outletId = $request->query('outlet_id');

            if (!$outletId) {
                return $this->errorResponse('Outlet ID diperlukan', 400);
            }

            $orders = Order::with([
                'user:id,name',
                'outlet:id,name',
                'cancellationRequester:id,name',
                'items.product:id,name'
            ])
                ->where('outlet_id', $outletId)
                ->where('cancellation_status', 'requested')
                ->latest('cancellation_requested_at')
                ->get();

            $transformedOrders = $orders->map(function ($order) {
                return [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'cashier' => $order->user->name,
                    'outlet' => $order->outlet->name,
                    'total' => $order->total,
                    'status' => $order->status,
                    'cancellation_type' => $order->cancellation_type,
                    'cancellation_reason' => $order->cancellation_reason,
                    'cancellation_notes' => $order->cancellation_notes,
                    'requested_by' => $order->cancellationRequester->name,
                    'requested_at' => $order->cancellation_requested_at->format('d/m/Y H:i'),
                    'created_at' => $order->created_at->format('d/m/Y H:i'),
                    'items' => $order->items->map(function ($item) {
                        return [
                            'product_name' => $item->product->name,
                            'quantity' => $item->quantity,
                            'price' => $item->price,
                            'discount' => $item->discount,
                            'subtotal' => $item->subtotal
                        ];
                    })
                ];
            });

            return $this->successResponse($transformedOrders, 'Pending cancellation requests berhasil diambil');
        } catch (\Exception $e) {
            return $this->errorResponse('Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Process actual cancellation/refund after approval
     */
    private function processCancellationRefund(Order $order, bool $isRefund)
    {
        // Return stock to inventory
        foreach ($order->items as $item) {
            $inventory = Inventory::where('outlet_id', $order->outlet_id)
                ->where('product_id', $item->product_id)
                ->first();

            if ($inventory) {
                $quantityBefore = $inventory->quantity;
                $inventory->increment('quantity', $item->quantity);

                $historyType = $isRefund ? 'refund' : 'adjustment';
                $historyNotes = $isRefund
                    ? "Refund Order #{$order->order_number} - {$order->cancellation_reason}"
                    : "Pembatalan Order #{$order->order_number} - {$order->cancellation_reason}";

                InventoryHistory::create([
                    'outlet_id' => $order->outlet_id,
                    'product_id' => $item->product_id,
                    'quantity_before' => $quantityBefore,
                    'quantity_after' => $inventory->quantity,
                    'quantity_change' => $item->quantity,
                    'type' => $historyType,
                    'notes' => $historyNotes,
                    'user_id' => auth()->user()->id,
                ]);
            }
        }

        // Handle cash register adjustment only for refunds (completed orders)
        if ($isRefund) {
            $cashRegister = CashRegister::where('outlet_id', $order->outlet_id)->first();
            if ($cashRegister) {
                $cashRegister->subtractCash(
                    $order->total,
                    auth()->user()->id,
                    $order->shift_id,
                    "Refund Order #{$order->order_number} - {$order->cancellation_reason}",
                    'refund'
                );
            }
        }
    }

    /**
     * Legacy method - now redirects to request system
     * @deprecated Use requestCancellation instead
     */
    public function cancelOrder(Request $request, $orderId)
    {
        // Validasi input refund reason jika ada
        $request->validate([
            'refund_reason' => 'nullable|string|max:500',
            'processed_by' => 'nullable|string|max:100'
        ]);

        // Mulai transaksi
        DB::beginTransaction();

        try {
            $order = Order::find($orderId);

            if (!$order) {
                return $this->errorResponse('Order tidak ditemukan', 404);
            }

            // Cek apakah order sudah pernah dibatalkan
            if ($order->status === 'cancelled') {
                return $this->errorResponse('Order sudah pernah dibatalkan sebelumnya', 400);
            }

            // Untuk refund (order completed), pastikan sudah approved
            if ($order->status === 'completed' && $order->approval_status !== 'approved') {
                return $this->errorResponse('Hanya transaksi yang sudah disetujui yang dapat direfund', 400);
            }

            $isRefund = ($order->status === 'completed');
            $refundReason = $request->input('refund_reason', 'Dibatalkan melalui sistem');
            $processedBy = $request->input('processed_by', 'system');

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
                    $historyNotes = $isRefund
                        ? "Refund Order #{$order->order_number} - Alasan: {$refundReason}"
                        : "Pembatalan Order #{$order->order_number}";

                    InventoryHistory::create([
                        'outlet_id' => $order->outlet_id,
                        'product_id' => $item->product_id,
                        'quantity_before' => $quantityBefore,
                        'quantity_after' => $inventory->quantity,
                        'quantity_change' => $item->quantity, // Nilai positif karena penambahan
                        'type' => $isRefund ? 'refund' : 'adjustment',
                        'notes' => $historyNotes,
                        'user_id' => auth()->user()->id,
                    ]);
                }
            }

            // Update order dengan informasi refund/pembatalan
            $updateData = [
                'status' => 'cancelled'
            ];

            // Jika ini adalah refund, tambahkan informasi refund ke notes
            if ($isRefund) {
                $existingNotes = $order->notes ? $order->notes . '\n' : '';
                $updateData['notes'] = $existingNotes . "REFUND - {$refundReason} (Diproses oleh: {$processedBy} pada " . now()->format('d/m/Y H:i') . ")";
            }

            $order->update($updateData);

            // Handle cash register adjustment
            $cashRegister = CashRegister::where('outlet_id', $order->outlet_id)->first();
            if ($cashRegister) {
                $transactionNotes = $isRefund
                    ? "Refund Order #{$order->order_number} - {$refundReason}"
                    : "Pembatalan Order #{$order->order_number}";

                // Untuk refund (completed order), kurangi dari kas karena sudah masuk sebelumnya
                // Untuk pembatalan (pending order), tidak perlu kurangi kas karena belum masuk
                if ($isRefund) {
                    $cashRegister->subtractCash(
                        $order->total,
                        auth()->user()->id,
                        $order->shift_id,
                        $transactionNotes,
                        'refund'
                    );
                }
            }

            // Commit transaksi jika semua operasi berhasil
            DB::commit();

            $message = $isRefund
                ? 'Refund berhasil diproses. Stok produk dan kas telah disesuaikan.'
                : 'Order berhasil dibatalkan. Stok produk telah dikembalikan.';

            return $this->successResponse($order->fresh(), $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Terjadi kesalahan: ' . $e->getMessage());
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
                'user:id,name',
                'approver:id,name',
                'cancellationRequester:id,name',
                'cancellationProcessor:id,name'
            ])->has('outlet')->has('user')->latest()->get();

            // $totalDiscount = $query->where('status', 'completed')->sum('discount');
            // $grossSales = $order->where('status', 'completed')->sum('subtotal');
            $totalDiscount = $query->where('status', 'completed')->sum('discount');
            $grossSales = $query->where('status', 'completed')->sum('subtotal');

            // dd($orders);
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
                    ] : null,
                    // Data baru untuk approval system (tanpa mengubah struktur yang ada)
                    'approval_status' => $order->approval_status,
                    'payment_proof_url' => $order->payment_proof_url,
                    'approved_by' => $order->approver ? $order->approver->name : null,
                    'approved_at' => $order->approved_at ? $order->approved_at->format('d/m/Y H:i') : null,
                    'rejection_reason' => $order->rejection_reason,
                    'approval_notes' => $order->approval_notes,
                    // Data untuk cancellation/refund approval system
                    'cancellation_status' => $order->cancellation_status,
                    'cancellation_reason' => $order->cancellation_reason,
                    'cancellation_notes' => $order->cancellation_notes,
                    'cancellation_requested_by' => $order->cancellationRequester ? $order->cancellationRequester->name : null,
                    'cancellation_requested_at' => $order->cancellation_requested_at ? $order->cancellation_requested_at->format('d/m/Y H:i') : null,
                    'cancellation_processed_by' => $order->cancellationProcessor ? $order->cancellationProcessor->name : null,
                    'cancellation_processed_at' => $order->cancellation_processed_at ? $order->cancellation_processed_at->format('d/m/Y H:i') : null,
                    'cancellation_admin_notes' => $order->cancellation_admin_notes
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

    public function uploadPaymentProofs(Request $request)
    {
        return $this->successResponse($request->all(), 'ngapain?');
    }

    /**
     * Get pending orders for approval
     */
    public function getPendingOrders(Request $request)
    {
        try {
            $outletId = $request->query('outlet_id');

            if (!$outletId) {
                return $this->errorResponse('Outlet ID diperlukan', 400);
            }

            $orders = Order::with(['user:id,name', 'outlet:id,name', 'items.product:id,name', 'member:id,name,member_code'])
                ->where('outlet_id', $outletId)
                ->where('approval_status', 'pending')
                ->latest()
                ->get();

            $transformedOrders = $orders->map(function ($order) {
                return [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'cashier' => $order->user->name,
                    'outlet' => $order->outlet->name,
                    'total' => $order->total,
                    'payment_method' => $order->payment_method,
                    'payment_proof_url' => $order->payment_proof_url,
                    'status' => $order->status,
                    'approval_status' => $order->approval_status,
                    'created_at' => $order->created_at->format('d/m/Y H:i'),
                    'member' => $order->member ? [
                        'name' => $order->member->name,
                        'member_code' => $order->member->member_code
                    ] : null,
                    'items' => $order->items->map(function ($item) {
                        return [
                            'product_name' => $item->product->name,
                            'quantity' => $item->quantity,
                            'price' => $item->price,
                            'discount' => $item->discount,
                            'subtotal' => $item->subtotal
                        ];
                    })
                ];
            });

            return $this->successResponse($transformedOrders, 'Pending orders berhasil diambil');
        } catch (\Exception $e) {
            return $this->errorResponse('Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Approve order
     */
    public function approveOrder(Request $request, $orderId)
    {
        try {
            $request->validate([
                'notes' => 'nullable|string|max:500'
            ]);

            $order = Order::findOrFail($orderId);
            $user = auth()->user();

            if (!$order->canBeApproved()) {
                return $this->errorResponse('Order tidak dapat disetujui', 400);
            }

            DB::beginTransaction();

            // Approve order
            $order->approve($user, $request->input('notes'));

            // Add to cash register when approved
            $cashRegister = CashRegister::where('outlet_id', $order->outlet_id)->first();
            if ($cashRegister) {
                $cashRegister->addCash(
                    $order->total,
                    $order->user_id,
                    $order->shift_id,
                    'Penjualan POS (Approved), Invoice #' . $order->order_number,
                    'pos'
                );
            }

            // Update order status to completed
            $order->update(['status' => 'completed']);

            DB::commit();

            return $this->successResponse($order->load(['approver:id,name']), 'Order berhasil disetujui');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Gagal menyetujui order: ' . $e->getMessage());
        }
    }

    /**
     * Reject order
     */
    public function rejectOrder(Request $request, $orderId)
    {
        try {
            $request->validate([
                'reason' => 'required|string|max:500'
            ]);

            $order = Order::findOrFail($orderId);
            $user = auth()->user();

            if (!$order->canBeRejected()) {
                return $this->errorResponse('Order tidak dapat ditolak', 400);
            }

            DB::beginTransaction();

            // Reject order
            $order->reject($user, $request->input('reason'));

            // Return inventory back to stock
            foreach ($order->items as $item) {
                $inventory = Inventory::where('outlet_id', $order->outlet_id)
                    ->where('product_id', $item->product_id)
                    ->first();

                if ($inventory) {
                    $quantityBefore = $inventory->quantity;
                    $inventory->increment('quantity', $item->quantity);

                    InventoryHistory::create([
                        'outlet_id' => $order->outlet_id,
                        'product_id' => $item->product_id,
                        'quantity_before' => $quantityBefore,
                        'quantity_after' => $inventory->quantity,
                        'quantity_change' => $item->quantity,
                        'type' => 'adjustment',
                        'notes' => 'Pengembalian stok karena order ditolak, Invoice #' . $order->order_number,
                        'user_id' => $user->id,
                    ]);
                }
            }

            // Update order status to cancelled
            $order->update(['status' => 'cancelled']);

            DB::commit();

            return $this->successResponse($order->load(['approver:id,name']), 'Order berhasil ditolak');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Gagal menolak order: ' . $e->getMessage());
        }
    }
}
