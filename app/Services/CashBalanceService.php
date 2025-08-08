<?php

namespace App\Services;

use App\Models\CashBalanceSnapshot;
use App\Models\CashRegister;
use App\Models\CashRegisterTransaction;
use App\Models\Order;
use App\Models\Outlet;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CashBalanceService
{
    /**
     * Record transaksi penjualan cash ke cash register
     */
    public function recordDailySalesTransaction(Order $order): CashRegisterTransaction
    {
        if ($order->payment_method !== 'cash') {
            throw new \InvalidArgumentException('Order must have cash payment method');
        }

        $cashRegister = CashRegister::where('outlet_id', $order->outlet_id)->first();
        if (!$cashRegister) {
            throw new \Exception("Cash register not found for outlet {$order->outlet_id}");
        }

        // Create transaction record
        $transaction = $cashRegister->addCash(
            amount: $order->total,
            userId: $order->user_id,
            shiftId: $order->shift_id,
            reason: "Penjualan POS - Order #{$order->id}",
            source: 'pos'
        );

        Log::info("Sales transaction recorded", [
            'order_id' => $order->id,
            'amount' => $order->total,
            'transaction_id' => $transaction->id
        ]);

        return $transaction;
    }

    /**
     * Calculate dan buat daily snapshot untuk outlet tertentu
     */
    public function calculateDailySnapshot(int $outletId, string $date, int $createdBy = null): CashBalanceSnapshot
    {
        $dateObj = Carbon::parse($date);
        
        // Get opening balance dari snapshot hari sebelumnya atau dari cash register
        $openingBalance = $this->getOpeningBalance($outletId, $date);
        
        // Calculate semua komponen cash flow untuk hari ini
        $cashFlow = $this->calculateDailyCashFlow($outletId, $date);
        
        // Calculate closing balance
        $closingBalance = $openingBalance 
            + $cashFlow['total_sales_cash']
            + $cashFlow['manual_additions'] 
            - $cashFlow['manual_subtractions']
            - $cashFlow['refunds'];

        // Create atau update snapshot
        $snapshot = CashBalanceSnapshot::updateOrCreate(
            [
                'outlet_id' => $outletId,
                'date' => $dateObj->format('Y-m-d')
            ],
            [
                'opening_balance' => $openingBalance,
                'closing_balance' => $closingBalance,
                'total_sales_cash' => $cashFlow['total_sales_cash'],
                'total_sales_other' => $cashFlow['total_sales_other'],
                'manual_additions' => $cashFlow['manual_additions'],
                'manual_subtractions' => $cashFlow['manual_subtractions'],
                'refunds' => $cashFlow['refunds'],
                'transactions_count' => $cashFlow['transactions_count'],
                'created_by' => $createdBy
            ]
        );

        Log::info("Daily snapshot calculated", [
            'outlet_id' => $outletId,
            'date' => $date,
            'opening' => $openingBalance,
            'closing' => $closingBalance,
            'transactions' => $cashFlow['transactions_count']
        ]);

        return $snapshot;
    }

    /**
     * Get opening balance untuk tanggal tertentu
     */
    public function getOpeningBalance(int $outletId, string $date): float
    {
        $dateObj = Carbon::parse($date);
        $previousDate = $dateObj->subDay()->format('Y-m-d');
        
        // Cari snapshot hari sebelumnya
        $previousSnapshot = CashBalanceSnapshot::where('outlet_id', $outletId)
            ->where('date', $previousDate)
            ->first();
            
        if ($previousSnapshot) {
            return $previousSnapshot->closing_balance;
        }
        
        // Jika tidak ada snapshot sebelumnya, gunakan balance current dari cash register
        $cashRegister = CashRegister::where('outlet_id', $outletId)->first();
        return $cashRegister ? $cashRegister->balance : 0;
    }

    /**
     * Get closing balance untuk tanggal tertentu
     */
    public function getClosingBalance(int $outletId, string $date): float
    {
        $snapshot = CashBalanceSnapshot::where('outlet_id', $outletId)
            ->where('date', $date)
            ->first();
            
        if ($snapshot) {
            return $snapshot->closing_balance;
        }
        
        // Calculate on-the-fly jika snapshot belum ada
        $openingBalance = $this->getOpeningBalance($outletId, $date);
        $cashFlow = $this->calculateDailyCashFlow($outletId, $date);
        
        return $openingBalance 
            + $cashFlow['total_sales_cash']
            + $cashFlow['manual_additions'] 
            - $cashFlow['manual_subtractions']
            - $cashFlow['refunds'];
    }

    /**
     * Calculate cash flow components untuk hari tertentu
     */
    protected function calculateDailyCashFlow(int $outletId, string $date): array
    {
        $dateObj = Carbon::parse($date);
        $startOfDay = $dateObj->startOfDay();
        $endOfDay = $dateObj->endOfDay();

        // Sales cash dari orders yang completed
        $salesCash = Order::where('outlet_id', $outletId)
            ->where('payment_method', 'cash')
            ->where('status', 'completed')
            ->whereBetween('updated_at', [$startOfDay, $endOfDay])
            ->sum('total');

        // Sales non-cash dari orders yang completed
        $salesOther = Order::where('outlet_id', $outletId)
            ->where('payment_method', '!=', 'cash')
            ->where('status', 'completed')
            ->whereBetween('updated_at', [$startOfDay, $endOfDay])
            ->sum('total');

        // Manual cash operations dari cash register transactions
        $cashRegisterIds = CashRegister::where('outlet_id', $outletId)->pluck('id');
        
        $manualAdditions = CashRegisterTransaction::whereIn('cash_register_id', $cashRegisterIds)
            ->where('type', 'add')
            ->where('source', 'cash')
            ->whereBetween('created_at', [$startOfDay, $endOfDay])
            ->sum('amount');

        $manualSubtractions = CashRegisterTransaction::whereIn('cash_register_id', $cashRegisterIds)
            ->where('type', 'remove')
            ->where('source', 'cash')
            ->whereBetween('created_at', [$startOfDay, $endOfDay])
            ->sum('amount');

        // Refunds dari cash register transactions
        $refunds = CashRegisterTransaction::whereIn('cash_register_id', $cashRegisterIds)
            ->where('source', 'refund')
            ->whereBetween('created_at', [$startOfDay, $endOfDay])
            ->sum('amount');

        // Total transaction count
        $transactionsCount = CashRegisterTransaction::whereIn('cash_register_id', $cashRegisterIds)
            ->whereBetween('created_at', [$startOfDay, $endOfDay])
            ->count();

        return [
            'total_sales_cash' => $salesCash,
            'total_sales_other' => $salesOther,
            'manual_additions' => $manualAdditions,
            'manual_subtractions' => $manualSubtractions,
            'refunds' => $refunds,
            'transactions_count' => $transactionsCount
        ];
    }

    /**
     * Reconcile balance dengan physical count
     */
    public function reconcileBalance(int $outletId, string $date, float $physicalCount, int $userId): array
    {
        $systemBalance = $this->getClosingBalance($outletId, $date);
        $variance = $physicalCount - $systemBalance;
        
        if (abs($variance) > 0.01) { // Tolerance 1 cent
            // Create adjustment transaction jika ada variance
            $cashRegister = CashRegister::where('outlet_id', $outletId)->first();
            
            if ($variance > 0) {
                $transaction = $cashRegister->addCash(
                    amount: $variance,
                    userId: $userId,
                    shiftId: null,
                    reason: "Reconciliation adjustment - Date: {$date}",
                    source: 'other'
                );
            } else {
                $transaction = $cashRegister->subtractCash(
                    amount: abs($variance),
                    userId: $userId,
                    shiftId: null,
                    reason: "Reconciliation adjustment - Date: {$date}",
                    source: 'other'
                );
            }
            
            Log::warning("Cash reconciliation variance detected", [
                'outlet_id' => $outletId,
                'date' => $date,
                'system_balance' => $systemBalance,
                'physical_count' => $physicalCount,
                'variance' => $variance,
                'adjustment_transaction_id' => $transaction->id ?? null
            ]);
        }

        return [
            'system_balance' => $systemBalance,
            'physical_count' => $physicalCount,
            'variance' => $variance,
            'is_reconciled' => abs($variance) <= 0.01
        ];
    }

    /**
     * Generate cash flow report untuk date range
     */
    public function generateCashFlowReport(int $outletId, string $dateFrom, string $dateTo): array
    {
        $snapshots = CashBalanceSnapshot::where('outlet_id', $outletId)
            ->whereBetween('date', [$dateFrom, $dateTo])
            ->orderBy('date')
            ->get();

        $totalSalesCash = $snapshots->sum('total_sales_cash');
        $totalSalesOther = $snapshots->sum('total_sales_other');
        $totalManualAdditions = $snapshots->sum('manual_additions');
        $totalManualSubtractions = $snapshots->sum('manual_subtractions');
        $totalRefunds = $snapshots->sum('refunds');
        $totalTransactions = $snapshots->sum('transactions_count');

        $startingBalance = $snapshots->first()?->opening_balance ?? 0;
        $endingBalance = $snapshots->last()?->closing_balance ?? 0;
        $netChange = $endingBalance - $startingBalance;

        return [
            'outlet_id' => $outletId,
            'period' => [
                'from' => $dateFrom,
                'to' => $dateTo
            ],
            'summary' => [
                'starting_balance' => $startingBalance,
                'ending_balance' => $endingBalance,
                'net_change' => $netChange,
                'total_sales_cash' => $totalSalesCash,
                'total_sales_other' => $totalSalesOther,
                'manual_additions' => $totalManualAdditions,
                'manual_subtractions' => $totalManualSubtractions,
                'refunds' => $totalRefunds,
                'transactions_count' => $totalTransactions
            ],
            'daily_snapshots' => $snapshots->toArray()
        ];
    }

    /**
     * Get current real-time balance
     */
    public function getCurrentBalance(int $outletId): float
    {
        $cashRegister = CashRegister::where('outlet_id', $outletId)->first();
        return $cashRegister ? $cashRegister->balance : 0;
    }

    /**
     * Get balance trend untuk dashboard widget
     */
    public function getBalanceTrend(int $outletId, int $days = 7): array
    {
        $endDate = now()->format('Y-m-d');
        $startDate = now()->subDays($days - 1)->format('Y-m-d');
        
        $snapshots = CashBalanceSnapshot::where('outlet_id', $outletId)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date')
            ->get();

        return $snapshots->map(function ($snapshot) {
            return [
                'date' => $snapshot->date,
                'closing_balance' => $snapshot->closing_balance,
                'total_sales' => $snapshot->total_sales_cash + $snapshot->total_sales_other,
                'net_change' => $snapshot->closing_balance - $snapshot->opening_balance
            ];
        })->toArray();
    }
}