<?php

namespace App\Http\Controllers;

use App\Models\CashBalanceSnapshot;
use App\Models\Outlet;
use App\Services\CashBalanceService;
use App\Traits\ApiResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CashReportController extends Controller
{
    use ApiResponse;

    protected $cashBalanceService;

    public function __construct(CashBalanceService $cashBalanceService)
    {
        $this->cashBalanceService = $cashBalanceService;
    }

    /**
     * Get current cash balance for outlet
     */
    public function getCurrentBalance(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'outlet_id' => 'required|exists:outlets,id'
            ]);

            if ($validator->fails()) {
                return $this->errorResponse($validator->errors()->first(), 400);
            }

            $outletId = $request->outlet_id;
            $currentBalance = $this->cashBalanceService->getCurrentBalance($outletId);
            
            // Get today's cash flow
            $today = now()->format('Y-m-d');
            $openingBalance = $this->cashBalanceService->getOpeningBalance($outletId, $today);
            $netChange = $currentBalance - $openingBalance;

            return $this->successResponse([
                'outlet_id' => $outletId,
                'current_balance' => $currentBalance,
                'opening_balance' => $openingBalance,
                'net_change' => $netChange,
                'net_change_percentage' => $openingBalance > 0 ? round(($netChange / $openingBalance) * 100, 2) : 0,
                'as_of' => now()->format('Y-m-d H:i:s')
            ], 'Current balance retrieved successfully');

        } catch (\Exception $e) {
            return $this->errorResponse('Failed to get current balance: ' . $e->getMessage());
        }
    }

    /**
     * Get balance trend for dashboard widget
     */
    public function getBalanceTrend(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'outlet_id' => 'required|exists:outlets,id',
                'days' => 'nullable|integer|min:1|max:30'
            ]);

            if ($validator->fails()) {
                return $this->errorResponse($validator->errors()->first(), 400);
            }

            $outletId = $request->outlet_id;
            $days = $request->input('days', 7);
            
            $trend = $this->cashBalanceService->getBalanceTrend($outletId, $days);

            return $this->successResponse([
                'outlet_id' => $outletId,
                'period_days' => $days,
                'trend_data' => $trend
            ], 'Balance trend retrieved successfully');

        } catch (\Exception $e) {
            return $this->errorResponse('Failed to get balance trend: ' . $e->getMessage());
        }
    }

    /**
     * Generate comprehensive cash flow report
     */
    public function getCashFlowReport(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'outlet_id' => 'required|exists:outlets,id',
                'date_from' => 'required|date',
                'date_to' => 'required|date|after_or_equal:date_from'
            ]);

            if ($validator->fails()) {
                return $this->errorResponse($validator->errors()->first(), 400);
            }

            $outletId = $request->outlet_id;
            $dateFrom = Carbon::parse($request->date_from)->format('Y-m-d');
            $dateTo = Carbon::parse($request->date_to)->format('Y-m-d');

            $report = $this->cashBalanceService->generateCashFlowReport($outletId, $dateFrom, $dateTo);

            return $this->successResponse($report, 'Cash flow report generated successfully');

        } catch (\Exception $e) {
            return $this->errorResponse('Failed to generate cash flow report: ' . $e->getMessage());
        }
    }

    /**
     * Get daily snapshots with pagination
     */
    public function getDailySnapshots(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'outlet_id' => 'required|exists:outlets,id',
                'date_from' => 'nullable|date',
                'date_to' => 'nullable|date|after_or_equal:date_from',
                'limit' => 'nullable|integer|min:1|max:100'
            ]);

            if ($validator->fails()) {
                return $this->errorResponse($validator->errors()->first(), 400);
            }

            $outletId = $request->outlet_id;
            $dateFrom = $request->date_from ? Carbon::parse($request->date_from)->format('Y-m-d') : null;
            $dateTo = $request->date_to ? Carbon::parse($request->date_to)->format('Y-m-d') : null;
            $limit = $request->input('limit', 30);

            $query = CashBalanceSnapshot::where('outlet_id', $outletId)
                ->with(['outlet:id,name', 'creator:id,name'])
                ->orderBy('date', 'desc');

            if ($dateFrom && $dateTo) {
                $query->whereBetween('date', [$dateFrom, $dateTo]);
            } elseif ($dateFrom) {
                $query->where('date', '>=', $dateFrom);
            } elseif ($dateTo) {
                $query->where('date', '<=', $dateTo);
            }

            $snapshots = $query->limit($limit)->get();

            // Add calculated fields
            $snapshots->transform(function ($snapshot) {
                $snapshot->net_change = $snapshot->net_change;
                $snapshot->total_sales = $snapshot->total_sales;
                $snapshot->formatted_opening = $snapshot->formatCurrency('opening_balance');
                $snapshot->formatted_closing = $snapshot->formatCurrency('closing_balance');
                $snapshot->formatted_net_change = $snapshot->formatCurrency('net_change');
                return $snapshot;
            });

            return $this->successResponse([
                'outlet_id' => $outletId,
                'date_range' => [
                    'from' => $dateFrom,
                    'to' => $dateTo
                ],
                'snapshots' => $snapshots,
                'count' => $snapshots->count()
            ], 'Daily snapshots retrieved successfully');

        } catch (\Exception $e) {
            return $this->errorResponse('Failed to get daily snapshots: ' . $e->getMessage());
        }
    }

    /**
     * Generate daily snapshot manually
     */
    public function generateSnapshot(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'outlet_id' => 'required|exists:outlets,id',
                'date' => 'required|date'
            ]);

            if ($validator->fails()) {
                return $this->errorResponse($validator->errors()->first(), 400);
            }

            $outletId = $request->outlet_id;
            $date = Carbon::parse($request->date)->format('Y-m-d');
            $userId = auth()->id();

            $snapshot = $this->cashBalanceService->calculateDailySnapshot($outletId, $date, $userId);

            return $this->successResponse([
                'snapshot' => $snapshot->load(['outlet:id,name', 'creator:id,name']),
                'generated_at' => now()->format('Y-m-d H:i:s')
            ], 'Snapshot generated successfully');

        } catch (\Exception $e) {
            return $this->errorResponse('Failed to generate snapshot: ' . $e->getMessage());
        }
    }

    /**
     * Reconcile cash balance with physical count
     */
    public function reconcileBalance(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'outlet_id' => 'required|exists:outlets,id',
                'date' => 'required|date',
                'physical_count' => 'required|numeric|min:0'
            ]);

            if ($validator->fails()) {
                return $this->errorResponse($validator->errors()->first(), 400);
            }

            $outletId = $request->outlet_id;
            $date = Carbon::parse($request->date)->format('Y-m-d');
            $physicalCount = (float) $request->physical_count;
            $userId = auth()->id();

            $reconciliation = $this->cashBalanceService->reconcileBalance($outletId, $date, $physicalCount, $userId);

            return $this->successResponse([
                'outlet_id' => $outletId,
                'date' => $date,
                'reconciliation' => $reconciliation,
                'reconciled_at' => now()->format('Y-m-d H:i:s'),
                'reconciled_by' => auth()->user()->name
            ], 'Cash reconciliation completed');

        } catch (\Exception $e) {
            return $this->errorResponse('Failed to reconcile balance: ' . $e->getMessage());
        }
    }

    /**
     * Get cash summary for dashboard
     */
    public function getDashboardSummary(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'outlet_id' => 'required|exists:outlets,id'
            ]);

            if ($validator->fails()) {
                return $this->errorResponse($validator->errors()->first(), 400);
            }

            $outletId = $request->outlet_id;
            $today = now()->format('Y-m-d');
            $yesterday = now()->subDay()->format('Y-m-d');

            // Today's data
            $todaySnapshot = CashBalanceSnapshot::where('outlet_id', $outletId)
                ->where('date', $today)
                ->first();

            // Yesterday's data for comparison
            $yesterdaySnapshot = CashBalanceSnapshot::where('outlet_id', $outletId)
                ->where('date', $yesterday)
                ->first();

            // Current balance
            $currentBalance = $this->cashBalanceService->getCurrentBalance($outletId);
            
            // Trend data (last 7 days)
            $trendData = $this->cashBalanceService->getBalanceTrend($outletId, 7);

            $summary = [
                'current_balance' => $currentBalance,
                'today' => [
                    'opening_balance' => $todaySnapshot?->opening_balance ?? 0,
                    'total_sales_cash' => $todaySnapshot?->total_sales_cash ?? 0,
                    'total_sales_other' => $todaySnapshot?->total_sales_other ?? 0,
                    'manual_additions' => $todaySnapshot?->manual_additions ?? 0,
                    'manual_subtractions' => $todaySnapshot?->manual_subtractions ?? 0,
                    'refunds' => $todaySnapshot?->refunds ?? 0,
                    'net_change' => $todaySnapshot?->net_change ?? 0,
                    'transactions_count' => $todaySnapshot?->transactions_count ?? 0
                ],
                'yesterday_comparison' => [
                    'closing_balance' => $yesterdaySnapshot?->closing_balance ?? 0,
                    'net_change' => $yesterdaySnapshot?->net_change ?? 0,
                    'transactions_count' => $yesterdaySnapshot?->transactions_count ?? 0
                ],
                'trend_data' => $trendData,
                'alerts' => $this->generateAlerts($outletId, $todaySnapshot, $yesterdaySnapshot)
            ];

            return $this->successResponse($summary, 'Dashboard summary retrieved successfully');

        } catch (\Exception $e) {
            return $this->errorResponse('Failed to get dashboard summary: ' . $e->getMessage());
        }
    }

    /**
     * Generate alerts for unusual activities
     */
    protected function generateAlerts($outletId, $todaySnapshot, $yesterdaySnapshot): array
    {
        $alerts = [];

        if (!$todaySnapshot) {
            $alerts[] = [
                'type' => 'warning',
                'title' => 'Missing Today\'s Snapshot',
                'message' => 'Daily snapshot belum dibuat untuk hari ini'
            ];
            return $alerts;
        }

        // Check for large variances
        if (abs($todaySnapshot->net_change) > 1000000) {
            $alerts[] = [
                'type' => $todaySnapshot->net_change > 0 ? 'info' : 'warning',
                'title' => 'Large Cash Movement',
                'message' => 'Perubahan kas besar: Rp ' . number_format(abs($todaySnapshot->net_change))
            ];
        }

        // Check for high transaction volume
        if ($todaySnapshot->transactions_count > 100) {
            $alerts[] = [
                'type' => 'info',
                'title' => 'High Transaction Volume',
                'message' => 'Volume transaksi tinggi: ' . $todaySnapshot->transactions_count . ' transaksi'
            ];
        }

        // Compare with yesterday
        if ($yesterdaySnapshot) {
            $percentageChange = $yesterdaySnapshot->closing_balance > 0 
                ? (($todaySnapshot->closing_balance - $yesterdaySnapshot->closing_balance) / $yesterdaySnapshot->closing_balance) * 100
                : 0;

            if (abs($percentageChange) > 50) {
                $alerts[] = [
                    'type' => 'warning',
                    'title' => 'Significant Balance Change',
                    'message' => 'Perubahan saldo ' . ($percentageChange > 0 ? 'naik' : 'turun') . ' ' . 
                                round(abs($percentageChange), 1) . '% dibanding kemarin'
                ];
            }
        }

        // Check for zero transactions with balance change
        if ($todaySnapshot->transactions_count == 0 && abs($todaySnapshot->net_change) > 0) {
            $alerts[] = [
                'type' => 'error',
                'title' => 'Anomaly Detected',
                'message' => 'Perubahan saldo tanpa transaksi tercatat'
            ];
        }

        return $alerts;
    }
}
