<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\BonusRule;
use App\Models\BonusTransaction;
use App\Models\BonusItem;
use App\Models\Product;
use App\Models\Order;
use Carbon\Carbon;

class BonusController extends Controller
{
    /**
     * Get bonus rules for outlet
     */
    public function getBonusRules(Request $request): JsonResponse
    {
        try {
            $outletId = $request->query('outlet_id');
            $type = $request->query('type', 'all'); // all, automatic, manual
            
            if (!$outletId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Outlet ID diperlukan'
                ], 400);
            }

            $query = BonusRule::active()
                              ->valid()
                              ->forOutlet($outletId)
                              ->with(['product', 'category', 'bonusProduct', 'outlet']);

            if ($type === 'automatic') {
                $query->automatic();
            } elseif ($type === 'manual') {
                $query->manual();
            }

            $rules = $query->orderBy('created_at', 'desc')->get();

            return response()->json([
                'success' => true,
                'data' => $rules,
                'message' => 'Bonus rules berhasil diambil'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil bonus rules: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create manual bonus transaction
     */
    public function createManualBonus(Request $request): JsonResponse
    {

        // dd($request->all());
        try {
            // Log the incoming request for debugging
            // \Log::info('Manual bonus request data:', $request->all());
            
            $validator = Validator::make($request->all(), [
                'outlet_id' => 'required|exists:outlets,id',
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.quantity' => 'required|numeric|min:0.01',
                'reason' => 'nullable|string|max:500',
                'member_id' => 'nullable|exists:members,id',
                'bonus_rule_id' => 'nullable|exists:bonus_rules,id',
                'order_id' => 'nullable|exists:orders,id'
            ]);

            if ($validator->fails()) {
                // \Log::error('Manual bonus validation failed:', [
                //     'errors' => $validator->errors(),
                //     'input' => $request->all()
                // ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = auth()->user();
            $data = $validator->validated();

            // Log the validated data
            // \Log::info('Validated bonus data:', $data);

            // Check stock availability for all items
            if (!isset($data['items']) || !is_array($data['items'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Items data tidak valid'
                ], 400);
            }

            foreach ($data['items'] as $item) {
                $product = Product::find($item['product_id']);
                if (!$product) {
                    return response()->json([
                        'success' => false,
                        'message' => "Produk tidak ditemukan: {$item['product_id']}"
                    ], 404);
                }

                // Check available stock (considering reserved in cart and other pending bonuses)
                $availableStock = $this->getAvailableStock($product->id, $data['outlet_id']);
                if ($item['quantity'] > $availableStock) {
                    return response()->json([
                        'success' => false,
                        'message' => "Stok tidak mencukupi untuk {$product->name}. Tersedia: {$availableStock}"
                    ], 400);
                }
            }

            DB::beginTransaction();
            
            // Get or create default manual bonus rule
            $bonusRule = null;
            if (isset($data['bonus_rule_id'])) {
                $bonusRule = BonusRule::find($data['bonus_rule_id']);
            } else {
                $bonusRule = $this->getDefaultManualBonusRule($data['outlet_id']);
            }

            // Create bonus transaction
            $bonusTransaction = BonusTransaction::create([
                'bonus_rule_id' => $bonusRule?->id,
                'outlet_id' => $data['outlet_id'],
                'order_id' => $data['order_id'] ?? null,
                'member_id' => $data['member_id'] ?? null,
                'cashier_id' => $user->id,
                'authorized_by' => $user->id,
                'type' => 'manual',
                'status' => ($bonusRule && $bonusRule->requires_approval) ? 'pending' : 'approved',
                'reason' => $data['reason'] ?? 'Manual bonus',
                'expired_at' => $bonusRule?->valid_until
            ]);

            // Create bonus items
            $totalValue = 0;
            foreach ($data['items'] as $item) {
                $product = Product::find($item['product_id']);
                $bonusValue = $item['quantity'] * $product->price;
                $totalValue += $bonusValue;

                BonusItem::create([
                    'bonus_transaction_id' => $bonusTransaction->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'product_price' => $product->price,
                    'bonus_value' => $bonusValue,
                    'status' => ($bonusRule && $bonusRule->requires_approval) ? 'pending' : 'approved'
                ]);
            }

            // Update transaction totals
            $bonusTransaction->update([
                'total_value' => $totalValue,
                'total_items' => count($data['items'])
            ]);

            // Reduce stock if auto-approved
            if ($bonusTransaction->status === 'approved') {
                $bonusTransaction->reduceInventoryStock();
            }

            DB::commit();

            // Load relationships for response
            $bonusTransaction->load(['bonusItems.product', 'member', 'cashier', 'outlet']);

            return response()->json([
                'success' => true,
                'data' => $bonusTransaction,
                'message' => 'Bonus manual berhasil dibuat'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat bonus: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate automatic bonus for cart items
     */
    public function calculateAutomaticBonus(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'outlet_id' => 'required|exists:outlets,id',
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.quantity' => 'required|numeric|min:0.01',
                'items.*.price' => 'required|numeric|min:0',
                'member_id' => 'nullable|exists:members,id',
                'subtotal' => 'required|numeric|min:0'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = $validator->validated();
            $automaticRules = BonusRule::getAutomaticRulesForOutlet($data['outlet_id']);
            $applicableRules = [];

            foreach ($automaticRules as $rule) {
                if ($this->checkBonusRuleApplicable($rule, $data)) {
                    $applicableRules[] = [
                        'rule' => $rule,
                        'bonus_items' => $this->calculateBonusItems($rule, $data)
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'data' => $applicableRules,
                'message' => 'Perhitungan bonus otomatis berhasil'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghitung bonus: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get bonus history for outlet
     */
    public function getBonusHistory(Request $request): JsonResponse
    {
        try {
            $outletId = $request->query('outlet_id');
            $dateFrom = $request->query('date_from');
            $dateTo = $request->query('date_to');
            $status = $request->query('status');
            $type = $request->query('type');
            $memberId = $request->query('member_id');
            
            if (!$outletId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Outlet ID diperlukan'
                ], 400);
            }

            $query = BonusTransaction::forOutlet($outletId)
                                   ->with(['bonusRule', 'member', 'cashier', 'approvedBy', 'bonusItems.product']);

            if ($dateFrom) {
                $query->whereDate('created_at', '>=', $dateFrom);
            }

            if ($dateTo) {
                $query->whereDate('created_at', '<=', $dateTo);
            }

            if ($status) {
                $query->where('status', $status);
            }

            if ($type) {
                $query->where('type', $type);
            }

            if ($memberId) {
                $query->where('member_id', $memberId);
            }

            $bonusTransactions = $query->orderBy('created_at', 'desc')->paginate(20);

            return response()->json([
                'success' => true,
                'data' => $bonusTransactions,
                'message' => 'History bonus berhasil diambil'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil history: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get pending bonuses for approval
     */
    public function getPendingBonuses(Request $request): JsonResponse
    {
        try {
            $outletId = $request->query('outlet_id');
            
            if (!$outletId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Outlet ID diperlukan'
                ], 400);
            }

            $pendingBonuses = BonusTransaction::getPendingForOutlet($outletId);

            return response()->json([
                'success' => true,
                'data' => $pendingBonuses,
                'message' => 'Pending bonuses berhasil diambil'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil pending bonuses: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Approve bonus transaction
     */
    public function approveBonus(Request $request, int $id): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'notes' => 'nullable|string|max:500'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $bonusTransaction = BonusTransaction::findOrFail($id);
            $user = auth()->user();

            if (!$bonusTransaction->canBeApproved()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bonus tidak dapat disetujui'
                ], 400);
            }

            DB::beginTransaction();

            $bonusTransaction->approve($user, $request->input('notes'));
            
            // Also approve all bonus items
            $bonusTransaction->bonusItems()->pending()->update(['status' => 'approved']);

            DB::commit();

            $bonusTransaction->load(['bonusItems.product', 'member', 'cashier', 'approvedBy']);

            return response()->json([
                'success' => true,
                'data' => $bonusTransaction,
                'message' => 'Bonus berhasil disetujui'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyetujui bonus: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject bonus transaction
     */
    public function rejectBonus(Request $request, int $id): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'reason' => 'required|string|max:500'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $bonusTransaction = BonusTransaction::findOrFail($id);
            $user = auth()->user();

            if (!$bonusTransaction->canBeRejected()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bonus tidak dapat ditolak'
                ], 400);
            }

            DB::beginTransaction();

            $bonusTransaction->reject($user, $request->input('reason'));
            
            // Also reject all bonus items
            $bonusTransaction->bonusItems()->pending()->update(['status' => 'rejected']);

            DB::commit();

            $bonusTransaction->load(['bonusItems.product', 'member', 'cashier', 'approvedBy']);

            return response()->json([
                'success' => true,
                'data' => $bonusTransaction,
                'message' => 'Bonus berhasil ditolak'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menolak bonus: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get bonus statistics for outlet
     */
    public function getBonusStats(Request $request): JsonResponse
    {
        try {
            $outletId = $request->query('outlet_id');
            $period = $request->query('period', 'today'); // today, week, month, year
            
            if (!$outletId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Outlet ID diperlukan'
                ], 400);
            }

            $query = BonusTransaction::forOutlet($outletId);

            // Apply date filter based on period
            switch ($period) {
                case 'today':
                    $query->whereDate('created_at', today());
                    break;
                case 'week':
                    $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'month':
                    $query->whereMonth('created_at', now()->month)
                          ->whereYear('created_at', now()->year);
                    break;
                case 'year':
                    $query->whereYear('created_at', now()->year);
                    break;
            }

            $stats = [
                'total_bonus_transactions' => $query->count(),
                'approved_bonuses' => $query->clone()->approved()->count(),
                'pending_bonuses' => $query->clone()->pending()->count(),
                'rejected_bonuses' => $query->clone()->rejected()->count(),
                'total_bonus_value' => $query->clone()->approved()->sum('total_value'),
                'total_bonus_items' => $query->clone()->approved()->sum('total_items'),
                'automatic_bonuses' => $query->clone()->automatic()->approved()->count(),
                'manual_bonuses' => $query->clone()->manual()->approved()->count(),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats,
                'message' => 'Statistik bonus berhasil diambil'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil statistik: ' . $e->getMessage()
            ], 500);
        }
    }

    // Private helper methods

    private function getAvailableStock(int $productId, int $outletId): float
    {
        // Get the actual inventory stock for this product at this outlet
        $inventory = \App\Models\Inventory::where('product_id', $productId)
                                         ->where('outlet_id', $outletId)
                                         ->first();
        
        if (!$inventory) {
            return 0;
        }

        // Get reserved stock from pending and approved bonus items
        $reservedInBonus = BonusItem::whereHas('bonusTransaction', function ($query) use ($outletId) {
            $query->where('outlet_id', $outletId)->whereIn('status', ['pending', 'approved']);
        })->where('product_id', $productId)->sum('quantity');

        return max(0, $inventory->quantity - $reservedInBonus);
    }

    private function getDefaultManualBonusRule(int $outletId): ?BonusRule
    {
        return BonusRule::where('outlet_id', $outletId)
                       ->where('type', 'manual')
                       ->where('name', 'Default Manual Bonus')
                       ->first();
    }

    private function checkBonusRuleApplicable(BonusRule $rule, array $cartData): bool
    {
        switch ($rule->trigger_type) {
            case 'minimum_purchase':
                return $cartData['subtotal'] >= $rule->trigger_value;
                
            case 'product_quantity':
                if ($rule->product_id) {
                    $productQty = collect($cartData['items'])
                        ->where('product_id', $rule->product_id)
                        ->sum('quantity');
                    return $productQty >= $rule->trigger_value;
                }
                break;
                
            case 'category_purchase':
                if ($rule->category_id) {
                    $categoryAmount = collect($cartData['items'])
                        ->filter(function ($item) use ($rule) {
                            $product = Product::find($item['product_id']);
                            return $product && $product->category_id == $rule->category_id;
                        })
                        ->sum(function ($item) {
                            return $item['quantity'] * $item['price'];
                        });
                    return $categoryAmount >= $rule->trigger_value;
                }
                break;
        }
        
        return false;
    }

    private function calculateBonusItems(BonusRule $rule, array $cartData): array
    {
        $bonusItems = [];
        
        if ($rule->bonus_type === 'product' && $rule->bonus_product_id) {
            $bonusProduct = Product::find($rule->bonus_product_id);
            if ($bonusProduct) {
                $bonusItems[] = [
                    'product_id' => $bonusProduct->id,
                    'product_name' => $bonusProduct->name,
                    'quantity' => $rule->bonus_quantity,
                    'product_price' => $bonusProduct->price,
                    'bonus_value' => $rule->bonus_quantity * $bonusProduct->price
                ];
            }
        }
        
        return $bonusItems;
    }
}