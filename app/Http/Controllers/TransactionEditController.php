<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\TransactionEdit;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TransactionEditController extends Controller
{
    /**
     * Request edit for an order
     */
    public function requestEdit(Request $request, Order $order)
    {
        try {
            // Validate request data
            $validated = $request->validate([
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.quantity' => 'required|numeric|min:0.01',
                'items.*.price' => 'required|numeric|min:0',
                'items.*.discount' => 'nullable|numeric|min:0',
                'reason' => 'required|string|max:500',
                'notes' => 'nullable|string|max:1000'
            ]);

            // Check if order can be edited
            if (!$order->canBeEdited()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaksi ini tidak dapat diedit. Pastikan status transaksi memungkinkan untuk diedit dan tidak ada permintaan edit yang pending.'
                ], 400);
            }

            // Check if user has access to this order
            $user = Auth::user();
            if ($user->role === 'kasir' && $order->outlet_id !== $user->outlet_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk mengedit transaksi ini.'
                ], 403);
            }

            // Get current order data with product relationship
            $order->load('items.product');
            $originalData = [
                'subtotal' => $order->subtotal,
                'discount' => $order->discount,
                'tax' => $order->tax,
                'total' => $order->total,
                'items' => $order->items->map(function ($item) {
                    return [
                        'product_id' => $item->product_id,
                        'product' => $item->product ? $item->product->name : ($item->product ?: 'Unknown Product'),
                        'quantity' => $item->quantity,
                        'price' => $item->price,
                        'discount' => $item->discount,
                        'unit_type' => $item->product ? $item->product->unit_type : ($item->unit_type ?: 'pcs')
                    ];
                })->toArray()
            ];

            // Calculate new totals
            $newData = $this->calculateNewTotals($validated['items'], $order);
            $totalDifference = $newData['total'] - $order->total;

            // Determine edit type
            $editType = $this->determineEditType($originalData['items'], $validated['items']);

            // Create transaction edit request
            $transactionEdit = TransactionEdit::create([
                'order_id' => $order->id,
                'requested_by' => $user->id,
                'edit_type' => $editType,
                'original_data' => $originalData,
                'new_data' => $newData,
                'reason' => $validated['reason'],
                'notes' => $validated['notes'] ?? null,
                'total_difference' => $totalDifference,
                'status' => 'pending'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Permintaan edit transaksi berhasil diajukan. Menunggu persetujuan dari Finance dan Operational.',
                'data' => [
                    'edit_id' => $transactionEdit->id,
                    'total_difference' => $totalDifference,
                    'approval_status' => $transactionEdit->getDualApprovalStatus()
                ]
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get individual transaction edit details
     */
    public function show(TransactionEdit $transactionEdit)
    {
        try {
            // Load necessary relationships
            $transactionEdit->load(['order', 'requester', 'financeApprover', 'operationalApprover']);
            
            $editData = [
                'id' => $transactionEdit->id,
                'order_number' => $transactionEdit->order->order_number,
                'requester_name' => $transactionEdit->requester->name,
                'edit_type' => $transactionEdit->edit_type,
                'reason' => $transactionEdit->reason,
                'notes' => $transactionEdit->notes,
                'total_difference' => $transactionEdit->total_difference,
                'status' => $transactionEdit->status,
                'approval_status' => $transactionEdit->getDualApprovalStatus(),
                'finance_approved' => $transactionEdit->isFinanceApproved(),
                'operational_approved' => $transactionEdit->isOperationalApproved(),
                'finance_approved_by' => $transactionEdit->financeApprover ? $transactionEdit->financeApprover->name : null,
                'finance_approved_at' => $transactionEdit->finance_approved_at ? $transactionEdit->finance_approved_at->format('d/m/Y H:i') : null,
                'operational_approved_by' => $transactionEdit->operationalApprover ? $transactionEdit->operationalApprover->name : null,
                'operational_approved_at' => $transactionEdit->operational_approved_at ? $transactionEdit->operational_approved_at->format('d/m/Y H:i') : null,
                'requested_at' => $transactionEdit->created_at->format('d/m/Y H:i'),
                'original_data' => $transactionEdit->original_data,
                'new_data' => $transactionEdit->new_data
            ];

            return response()->json([
                'success' => true,
                'data' => $editData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get pending transaction edits for approval
     */
    public function getPendingEdits(Request $request)
    {
        try {
            $user = Auth::user();
            
            // Only admin can view pending edits
            if ($user->role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk melihat daftar edit yang pending.'
                ], 403);
            }

            $edits = TransactionEdit::with(['order', 'order.outlet', 'requester'])
                ->where('status', 'pending')
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            $editsData = $edits->map(function ($edit) {
                return [
                    'id' => $edit->id,
                    'order_number' => $edit->order->order_number,
                    'outlet_name' => $edit->order->outlet->name,
                    'requester_name' => $edit->requester->name,
                    'edit_type' => $edit->edit_type,
                    'reason' => $edit->reason,
                    'total_difference' => $edit->total_difference,
                    'approval_status' => $edit->getDualApprovalStatus(),
                    'finance_approved' => $edit->isFinanceApproved(),
                    'operational_approved' => $edit->isOperationalApproved(),
                    'requested_at' => $edit->created_at->format('d/m/Y H:i')
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $editsData,
                'pagination' => [
                    'current_page' => $edits->currentPage(),
                    'last_page' => $edits->lastPage(),
                    'total' => $edits->total()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Approve transaction edit (Finance)
     */
    public function approveFinance(Request $request, TransactionEdit $transactionEdit)
    {
        try {
            $user = Auth::user();
            
            // Only admin can approve
            if ($user->role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk menyetujui edit transaksi.'
                ], 403);
            }

            // Check if can be finance approved
            if (!$transactionEdit->canBeFinanceApproved()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Edit transaksi ini tidak dapat disetujui oleh Finance saat ini.'
                ], 400);
            }

            // Approve finance
            $success = $transactionEdit->approveFinance($user);
            
            if (!$success) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menyetujui edit transaksi.'
                ], 400);
            }

            $message = 'Edit transaksi berhasil disetujui oleh Finance.';
            if ($transactionEdit->isFullyApproved()) {
                $message .= ' Edit telah diterapkan pada transaksi.';
            } else {
                $message .= ' Menunggu persetujuan dari Operational.';
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'approval_status' => $transactionEdit->getDualApprovalStatus(),
                    'fully_approved' => $transactionEdit->isFullyApproved()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Approve transaction edit (Operational)
     */
    public function approveOperational(Request $request, TransactionEdit $transactionEdit)
    {
        try {
            $user = Auth::user();
            
            // Only admin can approve
            if ($user->role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk menyetujui edit transaksi.'
                ], 403);
            }

            // Check if can be operational approved
            if (!$transactionEdit->canBeOperationalApproved()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Edit transaksi ini tidak dapat disetujui oleh Operational saat ini.'
                ], 400);
            }

            // Approve operational
            $success = $transactionEdit->approveOperational($user);
            
            if (!$success) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menyetujui edit transaksi.'
                ], 400);
            }

            $message = 'Edit transaksi berhasil disetujui oleh Operational.';
            if ($transactionEdit->isFullyApproved()) {
                $message .= ' Edit telah diterapkan pada transaksi.';
            } else {
                $message .= ' Menunggu persetujuan dari Finance.';
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'approval_status' => $transactionEdit->getDualApprovalStatus(),
                    'fully_approved' => $transactionEdit->isFullyApproved()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject transaction edit
     */
    public function reject(Request $request, TransactionEdit $transactionEdit)
    {
        try {
            $validated = $request->validate([
                'reason' => 'required|string|max:500'
            ]);

            $user = Auth::user();
            
            // Only admin can reject
            if ($user->role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk menolak edit transaksi.'
                ], 403);
            }

            // Reject the edit
            $success = $transactionEdit->reject($user, $validated['reason']);
            
            if (!$success) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menolak edit transaksi.'
                ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => 'Edit transaksi berhasil ditolak.',
                'data' => [
                    'rejection_reason' => $validated['reason']
                ]
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get edit history for an order
     */
    public function getEditHistory(Order $order)
    {
        try {
            $edits = $order->transactionEdits()
                ->with(['requester', 'financeApprover', 'operationalApprover', 'rejector'])
                ->orderBy('created_at', 'desc')
                ->get();

            $editsData = $edits->map(function ($edit) {
                return [
                    'id' => $edit->id,
                    'edit_type' => $edit->edit_type,
                    'reason' => $edit->reason,
                    'notes' => $edit->notes,
                    'total_difference' => $edit->total_difference,
                    'status' => $edit->status,
                    'approval_status' => $edit->getDualApprovalStatus(),
                    'requester' => $edit->requester->name,
                    'finance_approver' => $edit->financeApprover?->name,
                    'finance_approved_at' => $edit->finance_approved_at?->format('d/m/Y H:i'),
                    'operational_approver' => $edit->operationalApprover?->name,
                    'operational_approved_at' => $edit->operational_approved_at?->format('d/m/Y H:i'),
                    'rejector' => $edit->rejector?->name,
                    'rejection_reason' => $edit->rejection_reason,
                    'rejected_at' => $edit->rejected_at?->format('d/m/Y H:i'),
                    'applied_at' => $edit->applied_at?->format('d/m/Y H:i'),
                    'requested_at' => $edit->created_at->format('d/m/Y H:i')
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $editsData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate new totals based on items
     */
    private function calculateNewTotals(array $items, Order $order)
    {
        $subtotal = 0;
        $totalDiscount = 0;
        $newItems = [];

        foreach ($items as $item) {
            $product = Product::find($item['product_id']);
            $quantity = $item['quantity'];
            $price = $item['price'];
            $discount = $item['discount'] ?? 0;

            $itemSubtotal = $quantity * $price;
            $subtotal += $itemSubtotal;
            $totalDiscount += $discount;

            $newItems[] = [
                'product_id' => $product->id,
                'product' => $product->name,
                'quantity' => $quantity,
                'price' => $price,
                'discount' => $discount,
                'unit_type' => $product->unit_type ?? 'pcs'
            ];
        }

        // Calculate tax (use same tax rate as original order)
        $taxRate = $order->subtotal > 0 ? $order->tax / $order->subtotal : 0; // Get original tax rate
        $tax = $subtotal * $taxRate;

        $total = $subtotal + $tax - $totalDiscount;

        return [
            'subtotal' => $subtotal,
            'discount' => $totalDiscount,
            'tax' => $tax,
            'total' => $total,
            'items' => $newItems
        ];
    }

    /**
     * Determine edit type based on changes
     */
    private function determineEditType(array $originalItems, array $newItems)
    {
        if (count($originalItems) !== count($newItems)) {
            return count($newItems) > count($originalItems) ? 'item_addition' : 'item_removal';
        }

        // Check for quantity changes
        foreach ($newItems as $index => $newItem) {
            if (isset($originalItems[$index])) {
                if ($originalItems[$index]['quantity'] != $newItem['quantity']) {
                    return 'quantity_adjustment';
                }
            }
        }

        return 'item_modification';
    }
}
