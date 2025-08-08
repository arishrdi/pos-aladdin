<?php

namespace App\Http\Controllers;

use App\Models\CashRegister;
use App\Models\CashRegisterTransaction;
use App\Models\Shift;
use App\Services\CashBalanceService;
use App\Traits\ApiResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CashRegisterTransactionController extends Controller
{

    use ApiResponse;
    
    protected $cashBalanceService;

    public function __construct(CashBalanceService $cashBalanceService)
    {
        $this->cashBalanceService = $cashBalanceService;
    }
    /**
     * Display a listing of the transactions.
     * Menampilkan daftar transaksi cash register.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $source = $request->source;
            $outlet_id = $request->outlet_id;
            $date = $request->date; // Format: YYYY-MM-DD

            $transactions = CashRegisterTransaction::with(['cashRegister', 'shift', 'user'])
                ->where('source', $source)
                ->orderBy('created_at', 'desc')
                ->when($date, function ($query) use ($date) {
                    $query->whereDate('created_at', $date);
                })
                ->whereHas('cashRegister', function ($query) use ($outlet_id) {
                    $query->where('outlet_id', $outlet_id);
                })
                ->get();

            // Add opening balance info untuk hari ini
            $responseData = [
                'transactions' => $transactions,
                'opening_balance' => 0,
                'current_balance' => 0
            ];

            if ($date && $outlet_id) {
                try {
                    $responseData['opening_balance'] = $this->cashBalanceService->getOpeningBalance($outlet_id, $date);
                    $responseData['current_balance'] = $this->cashBalanceService->getCurrentBalance($outlet_id);
                } catch (\Exception $e) {
                    // If balance service fails, continue without balance info
                    \Log::warning('Failed to get balance info: ' . $e->getMessage());
                }
            }

            return $this->successResponse($responseData, 'Successfully get cash register transactions');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }



    /**
     * Store a newly created transaction in storage.
     * Menyimpan transaksi cash register baru ke database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $request->validate([
            'cash_register_id' => 'required|exists:cash_registers,id',
            'shift_id' => 'required|exists:shifts,id',
            'type' => 'required|in:add,remove',
            'amount' => 'required|numeric|min:0.01',
            'reason' => 'nullable|string'
        ]);

        try {
            $cashRegister = CashRegister::findOrFail($request->cash_register_id);
            if (!$cashRegister->is_active) {
                return $this->errorResponse('Cannot create transaction for inactive cash register');
            }

            $shift = Shift::findOrFail($request->shift_id);
            if (!$shift->is_active) {
                return $this->errorResponse('Cannot create transaction for inactive shift');
            }

            $transaction = new CashRegisterTransaction($request->all());
            $transaction->user_id = Auth::id();
            $transaction->save();

            return $this->successResponse($transaction, 'Transaction created successfully');
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage());
        }

        $cashRegister = CashRegister::findOrFail($request->cash_register_id);
        if (!$cashRegister->is_active) {
            return $this->errorResponse('Cannot create transaction for inactive cash register');
        }

        $shift = Shift::findOrFail($request->shift_id);

        $transaction = new CashRegisterTransaction($request->all());
        $transaction->user_id = Auth::id();
        $transaction->save();

        return $this->successResponse($transaction, 'Transaction created successfully');
    }

    /**
     * Display the specified transaction.
     * Menampilkan detail transaksi tertentu.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $transaction = CashRegisterTransaction::with(['cashRegister', 'shift', 'user'])->findOrFail($id);

        return $this->successResponse($transaction, 'Successfully get cash register transaction');
    }

    /**
     * Get all transactions for a specific cash register.
     * Mendapatkan semua transaksi untuk cash register tertentu.
     *
     * @param  int  $cashRegisterId
     * @return \Illuminate\Http\Response
     */
    public function getByCashRegister($cashRegisterId)
    {
        $cashRegister = CashRegister::findOrFail($cashRegisterId);
        $transactions = $cashRegister->transactions()->with(['shift', 'user'])->get();

        return $this->successResponse($transactions, 'Successfully get cash register transactions');
    }

    /**
     * Get all transactions for a specific shift.
     * Mendapatkan semua transaksi untuk shift tertentu.
     *
     * @param  int  $shiftId
     * @return \Illuminate\Http\Response
     */
    public function getByShift($shiftId)
    {
        $shift = Shift::findOrFail($shiftId);
        $transactions = $shift->cashRegisterTransactions()->with(['cashRegister', 'user'])->get();

        return $this->successResponse($transactions, 'Successfully get cash register transactions');
    }

    /**
     * Get all transactions by type (add or remove).
     * Mendapatkan semua transaksi berdasarkan tipe (add atau remove).
     *
     * @param  string  $type
     * @return \Illuminate\Http\Response
     */
    public function getByType($type)
    {
        if (!in_array($type, ['add', 'remove'])) {
            return $this->errorResponse('Invalid transaction type. Must be "add" or "remove".');
        }

        $transactions = CashRegisterTransaction::where('type', $type)
            ->with(['cashRegister', 'shift', 'user'])
            ->get();

        return $this->successResponse($transactions, 'Successfully get cash register transactions');
    }


    public function getBalance($cashRegisterId)
    {

        try {

            $cashRegister = CashRegister::findOrFail($cashRegisterId);

            $addAmount = CashRegisterTransaction::where('cash_register_id', $cashRegisterId)
                ->where('type', 'add')
                ->sum('amount');

            $removeAmount = CashRegisterTransaction::where('cash_register_id', $cashRegisterId)
                ->where('type', 'remove')
                ->sum('amount');

            $balance = $addAmount - $removeAmount;

            return $this->successResponse([
                'cash_register_id' => $cashRegisterId,
                'cash_register_name' => $cashRegister->name,
                'add_total' => $addAmount,
                'remove_total' => $removeAmount,
                'current_balance' => $balance
            ], 'Successfully get cash register transaction balance');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    // Di bawah ini yang kepake

    // menambah uang kas
    public function addCash(Request $request)
    {
        try {
            $request->validate([
                'amount' => 'required|numeric|min:0',
                'outlet_id' => 'required|exists:outlets,id',
                'reason' => 'nullable|string',
                'proof_files.*' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120' // max 5MB per file
            ]);

            $cashRegister = CashRegister::where('outlet_id', $request->outlet_id)->first();

            // Handle file uploads
            $proofFiles = [];
            if ($request->hasFile('proof_files')) {
                foreach ($request->file('proof_files') as $file) {
                    $fileName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                    
                    // Create directory if not exists
                    $uploadDir = public_path('uploads/cash_proofs');
                    if (!file_exists($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }
                    
                    // Move file to upload directory
                    $file->move($uploadDir, $fileName);
                    $proofFiles[] = 'cash_proofs/' . $fileName;
                }
            }

            $transaction = $cashRegister->addCash(
                amount: $request->amount,
                userId: $request->user()->id,
                shiftId: $request->user()->lastShift()->value('id'),
                reason: $request->reason,
                source: 'cash'
            );

            // Update transaction with proof files
            if (!empty($proofFiles)) {
                $transaction->update(['proof_files' => $proofFiles]);
            }

            return $this->successResponse($transaction->fresh(), 'Successfully add cash with proof files');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    // mengurangi uang kas
    public function subtractCash(Request $request)
    {
        try {
            $request->validate([
                'amount' => 'required|numeric|min:0',
                'outlet_id' => 'required|exists:outlets,id',
                'reason' => 'nullable|string',
                'proof_files.*' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120' // max 5MB per file
            ]);

            $cashRegister = CashRegister::where('outlet_id', $request->outlet_id)->first();

            if ($request->amount > $cashRegister->balance) {
                return $this->errorResponse('Insufficient balance');
            }

            // Handle file uploads
            $proofFiles = [];
            if ($request->hasFile('proof_files')) {
                foreach ($request->file('proof_files') as $file) {
                    $fileName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                    
                    // Create directory if not exists
                    $uploadDir = public_path('uploads/cash_proofs');
                    if (!file_exists($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }
                    
                    // Move file to upload directory
                    $file->move($uploadDir, $fileName);
                    $proofFiles[] = 'cash_proofs/' . $fileName;
                }
            }

            $transaction = $cashRegister->subtractCash(
                amount: $request->amount,
                userId: $request->user()->id,
                shiftId: $request->user()->lastShift()->value('id'),
                reason: $request->reason,
                source: 'cash'
            );

            // Update transaction with proof files
            if (!empty($proofFiles)) {
                $transaction->update(['proof_files' => $proofFiles]);
            }

            return $this->successResponse($transaction->fresh(), 'Successfully subtract cash with proof files');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    public function cashRegisterHistory(Request $request)
    {
        $outletId = $request->outlet_id;

        try {
            $transactions = CashRegisterTransaction::with(['cashRegister', 'user:id,name'])
                ->whereHas('cashRegister', function($query) use ($outletId) {
                    $query->where('outlet_id', $outletId);
                })
                ->orderBy('created_at', 'desc')
                ->get();

            // Transform data untuk include proof files URLs
            $transactions->transform(function ($transaction) {
                $data = $transaction->toArray();
                $data['proof_files_urls'] = $transaction->proof_files_urls;
                $data['has_proof_files'] = !empty($transaction->proof_files);
                return $data;
            });

            return $this->successResponse($transactions, 'Successfully getting cash history');
        } catch (\Throwable $e) {
            return $this->errorResponse($e->getMessage());
        }
    }
}
