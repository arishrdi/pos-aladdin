<?php

namespace App\Http\Controllers;

use App\Models\CashRequest;
use App\Models\CashRegister;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Mail\CashRequestApproval;

class CashRequestController extends Controller
{
    use ApiResponse;

    /**
     * Kasir membuat permintaan tambah/kurang kas
     */
    public function requestCash(Request $request)
    {
        $request->validate([
            'type' => 'required|in:add,subtract',
            'amount' => 'required|numeric|min:0.01',
            'outlet_id' => 'required|exists:outlets,id',
            'reason' => 'nullable|string|max:1000',
            'proof_files.*' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120' // max 5MB per file
        ]);

        try {
            $user = auth()->user();

            // Untuk subtract, cek apakah saldo mencukupi
            if ($request->type === 'subtract') {
                $cashRegister = CashRegister::where('outlet_id', $request->outlet_id)->first();
                if (!$cashRegister || $request->amount > $cashRegister->balance) {
                    return $this->errorResponse('Saldo kas tidak mencukupi untuk permintaan ini', 400);
                }
            }

            // Handle file uploads
            $proofFiles = [];
            if ($request->hasFile('proof_files')) {
                foreach ($request->file('proof_files') as $file) {
                    $fileName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                    
                    // Create directory if not exists
                    $uploadDir = public_path('uploads/cash_requests');
                    if (!file_exists($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }
                    
                    // Move file to upload directory
                    $file->move($uploadDir, $fileName);
                    $proofFiles[] = 'cash_requests/' . $fileName;
                }
            }

            // Create cash request
            $cashRequest = CashRequest::create([
                'outlet_id' => $request->outlet_id,
                'requested_by' => $user->id,
                'type' => $request->type,
                'amount' => $request->amount,
                'reason' => $request->reason,
                'proof_files' => $proofFiles,
                'status' => 'pending',
            ]);

            // Load relationships for email
            $cashRequest->load(['requester', 'outlet']);

            // Send email to supervisors
            $supervisors = $user->outlet->supervisor ?? collect();
            
            if ($supervisors->isEmpty()) {
                Log::warning("No supervisors found for outlet {$user->outlet->name}");
            }
            
            foreach ($supervisors as $supervisor) {
                if (empty($supervisor->email)) {
                    Log::warning("Supervisor {$supervisor->name} tidak punya email, skip kirim email");
                    continue;
                }

                $requestType = $request->type === 'add' ? 'PERMINTAAN TAMBAH KAS' : 'PERMINTAAN KURANG KAS';

                $data = [
                    'supervisor_name' => $supervisor->name,
                    'cashier_name' => $user->name,
                    'approval_request' => $requestType,
                    'approval_data' => $cashRequest
                ];

                Log::info("Sending cash request email to supervisor: {$supervisor->email} for {$requestType}");
                Mail::to($supervisor->email)->send(new CashRequestApproval($data));
            }

            $typeText = $request->type === 'add' ? 'Tambah Kas' : 'Kurang Kas';
            
            return $this->successResponse($cashRequest->fresh(['requester', 'outlet']), 
                "Permintaan {$typeText} berhasil diajukan dan menunggu persetujuan admin");
        } catch (\Exception $e) {
            return $this->errorResponse('Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Get pending cash requests untuk admin/supervisor
     */
    public function getPendingRequests(Request $request)
    {
        try {
            $outletId = $request->query('outlet_id');

            if (!$outletId) {
                return $this->errorResponse('Outlet ID diperlukan', 400);
            }

            $requests = CashRequest::with([
                'requester:id,name',
                'outlet:id,name'
            ])
            ->where('outlet_id', $outletId)
            ->where('status', 'pending')
            ->latest()
            ->get();

            $transformedRequests = $requests->map(function ($request) {
                return [
                    'id' => $request->id,
                    'type' => $request->type,
                    'type_text' => $request->type_text,
                    'amount' => $request->amount,
                    'reason' => $request->reason,
                    'requester' => $request->requester->name,
                    'outlet' => $request->outlet->name,
                    'requested_at' => $request->created_at->format('d/m/Y H:i'),
                    'proof_files_urls' => $request->proof_files_urls,
                    'has_proof_files' => !empty($request->proof_files)
                ];
            });

            return $this->successResponse($transformedRequests, 'Pending cash requests berhasil diambil');
        } catch (\Exception $e) {
            return $this->errorResponse('Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Admin approve cash request
     */
    public function approveRequest(Request $request, $requestId)
    {
        $request->validate([
            'admin_notes' => 'nullable|string|max:1000'
        ]);

        DB::beginTransaction();
        try {
            $cashRequest = CashRequest::findOrFail($requestId);
            $user = auth()->user();

            if (!$cashRequest->canBeApproved()) {
                return $this->errorResponse('Permintaan ini tidak dapat disetujui', 400);
            }

            // For subtract requests, check current balance again
            if ($cashRequest->type === 'subtract') {
                $cashRegister = CashRegister::where('outlet_id', $cashRequest->outlet_id)->first();
                if (!$cashRegister || $cashRequest->amount > $cashRegister->balance) {
                    return $this->errorResponse('Saldo kas saat ini tidak mencukupi untuk permintaan ini', 400);
                }
            }

            // Approve the request
            $cashRequest->approve($user, $request->input('admin_notes'));

            // Execute the cash transaction
            $cashRegister = CashRegister::where('outlet_id', $cashRequest->outlet_id)->first();
            
            if ($cashRequest->type === 'add') {
                $transaction = $cashRegister->addCash(
                    amount: $cashRequest->amount,
                    userId: $cashRequest->requested_by,
                    shiftId: null, // admin approval doesn't need shift
                    reason: "Request approved: " . ($cashRequest->reason ?? 'No reason'),
                    source: 'cash'
                );
            } else {
                $transaction = $cashRegister->subtractCash(
                    amount: $cashRequest->amount,
                    userId: $cashRequest->requested_by,
                    shiftId: null,
                    reason: "Request approved: " . ($cashRequest->reason ?? 'No reason'),
                    source: 'cash'
                );
            }

            // Update transaction with original proof files from request
            if (!empty($cashRequest->proof_files)) {
                $transaction->update(['proof_files' => $cashRequest->proof_files]);
            }

            DB::commit();

            $typeText = $cashRequest->type === 'add' ? 'Tambah Kas' : 'Kurang Kas';
            return $this->successResponse($cashRequest->fresh(['requester', 'processor']), 
                "Permintaan {$typeText} berhasil disetujui dan diproses");
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Admin reject cash request
     */
    public function rejectRequest(Request $request, $requestId)
    {
        $request->validate([
            'admin_notes' => 'required|string|max:1000'
        ]);

        try {
            $cashRequest = CashRequest::findOrFail($requestId);
            $user = auth()->user();

            if (!$cashRequest->canBeRejected()) {
                return $this->errorResponse('Permintaan ini tidak dapat ditolak', 400);
            }

            $cashRequest->reject($user, $request->input('admin_notes'));

            $typeText = $cashRequest->type === 'add' ? 'Tambah Kas' : 'Kurang Kas';
            return $this->successResponse($cashRequest->fresh(['requester', 'processor']), 
                "Permintaan {$typeText} berhasil ditolak");
        } catch (\Exception $e) {
            return $this->errorResponse('Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Get all cash requests (for history)
     */
    public function getRequests(Request $request)
    {
        try {
            $outletId = $request->query('outlet_id');
            $status = $request->query('status');

            if (!$outletId) {
                return $this->errorResponse('Outlet ID diperlukan', 400);
            }

            $query = CashRequest::with([
                'requester:id,name',
                'outlet:id,name',
                'processor:id,name'
            ])
            ->where('outlet_id', $outletId);

            if ($status) {
                $query->where('status', $status);
            }

            $requests = $query->latest()->get();

            $transformedRequests = $requests->map(function ($request) {
                return [
                    'id' => $request->id,
                    'type' => $request->type,
                    'type_text' => $request->type_text,
                    'amount' => $request->amount,
                    'reason' => $request->reason,
                    'status' => $request->status,
                    'requester' => $request->requester->name,
                    'outlet' => $request->outlet->name,
                    'processor' => $request->processor ? $request->processor->name : null,
                    'admin_notes' => $request->admin_notes,
                    'requested_at' => $request->created_at->format('d/m/Y H:i'),
                    'processed_at' => $request->processed_at ? $request->processed_at->format('d/m/Y H:i') : null,
                    'proof_files_urls' => $request->proof_files_urls,
                    'has_proof_files' => !empty($request->proof_files)
                ];
            });

            return $this->successResponse($transformedRequests, 'Cash requests berhasil diambil');
        } catch (\Exception $e) {
            return $this->errorResponse('Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
