<?php

namespace App\Http\Controllers;

use App\Models\CashRegister;
use App\Models\Outlet;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OutletController extends Controller
{
    use ApiResponse;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $user = $request->user();
            
            if ($user->role === 'admin') {
                $outlets = Outlet::all();
            } elseif ($user->role === 'supervisor') {
                // Get outlets assigned to supervisor via many-to-many relationship
                $outlets = $user->outlets;
            } elseif ($user->role === 'kasir') {
                // Get only the outlet assigned to kasir
                $outlets = $user->outlet ? collect([$user->outlet]) : collect([]);
            } else {
                $outlets = collect([]);
            }
            
            return $this->successResponse($outlets, 'Outlets retrieved successfully');
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage());
        }
    }

    public function allOutlets()
    {
        try {
            $outlets = Outlet::all();
            return $this->successResponse($outlets, 'All outlets retrieved successfully');
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'address' => 'required|string|max:255',
                'phone' => 'required|string|max:255',
                'email' => 'required|string|email|max:255',
                'tax' => 'nullable|numeric|min:0',
                'tax_type' => 'required|in:pkp,non_pkp',
                'qris' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10048',
                'atas_nama_bank' => 'nullable|string|max:255',
                'nama_bank' => 'nullable|string|max:255',
                'nomor_transaksi_bank' => 'nullable|integer',
                'pkp_atas_nama_bank' => 'required|string|max:255',
                'pkp_nama_bank' => 'required|string|max:255',
                'pkp_nomor_transaksi_bank' => 'required|string',
                'non_pkp_atas_nama_bank' => 'required|string|max:255',
                'non_pkp_nama_bank' => 'required|string|max:255',
                'non_pkp_nomor_transaksi_bank' => 'required|string',
                'target_tahunan' => 'nullable|numeric|min:0',
                'target_bulanan' => 'nullable|numeric|min:0',
            ]);
    
            DB::beginTransaction();
    
            $outletData = [
                'name' => $request->name,
                'address' => $request->address,
                'phone' => $request->phone,
                'email' => $request->email,
                'tax' => $request->tax,
                'tax_type' => $request->tax_type,
                'atas_nama_bank' => $request->atas_nama_bank,
                'nama_bank' => $request->nama_bank,
                'nomor_transaksi_bank' => $request->nomor_transaksi_bank,
                'pkp_atas_nama_bank' => $request->pkp_atas_nama_bank,
                'pkp_nama_bank' => $request->pkp_nama_bank,
                'pkp_nomor_transaksi_bank' => $request->pkp_nomor_transaksi_bank,
                'non_pkp_atas_nama_bank' => $request->non_pkp_atas_nama_bank,
                'non_pkp_nama_bank' => $request->non_pkp_nama_bank,
                'non_pkp_nomor_transaksi_bank' => $request->non_pkp_nomor_transaksi_bank,
                'target_tahunan' => $request->target_tahunan,
                'target_bulanan' => $request->target_bulanan,
            ];
    
            // Hanya tambahkan qris jika ada file
            if ($request->hasFile('qris')) {
                $outletData['qris'] = $request->file('qris')->store('qris', 'uploads');
            }
    
            $outlet = Outlet::create($outletData);
            
            CashRegister::create([
                'outlet_id' => $outlet->id,
                'balance' => 0,
                'is_active' => true,
            ]);
            
            DB::commit();
            return $this->successResponse($outlet, 'Outlet created successfully');
        } catch (ValidationException $th) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terdapat kesalahan validasi pada form',
                'data' => $th->errors()
            ], 422);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan outlet: ' . $th->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Outlet $outlet)
    {
        try {
            $outlet->load([
                'users',
                'products',
                // 'shifts',
                'orders' => function ($query) {
                    $query->latest()->take(10);
                },
                // 'inventory'
            ]);

            return $this->successResponse($outlet, 'Outlet retrieved successfully');
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Outlet $outlet)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'address' => 'nullable|string|max:255',
                'phone' => 'nullable|string|max:255',
                'email' => 'nullable|string|email|max:255',
                'is_active' => 'required|boolean',
                'tax' => 'nullable|numeric|min:0',
                'tax_type' => 'required|in:pkp,non_pkp',
                'qris' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10048',
                'atas_nama_bank' => 'nullable|string|max:255',
                'nama_bank' => 'nullable|string|max:255',
                'nomor_transaksi_bank' => 'nullable|integer',
                'pkp_atas_nama_bank' => 'required|string|max:255',
                'pkp_nama_bank' => 'required|string|max:255',
                'pkp_nomor_transaksi_bank' => 'required|string',
                'non_pkp_atas_nama_bank' => 'required|string|max:255',
                'non_pkp_nama_bank' => 'required|string|max:255',
                'non_pkp_nomor_transaksi_bank' => 'required|string',
                'target_tahunan' => 'nullable|numeric|min:0',
                'target_bulanan' => 'nullable|numeric|min:0',
            ]);
    
            $updateData = [
                'name' => $request->name,
                'address' => $request->address,
                'phone' => $request->phone,
                'email' => $request->email,
                'tax' => $request->tax,
                'tax_type' => $request->tax_type,
                'is_active' => $request->is_active,
                'atas_nama_bank' => $request->atas_nama_bank,
                'nama_bank' => $request->nama_bank,
                'nomor_transaksi_bank' => $request->nomor_transaksi_bank,
                'pkp_atas_nama_bank' => $request->pkp_atas_nama_bank,
                'pkp_nama_bank' => $request->pkp_nama_bank,
                'pkp_nomor_transaksi_bank' => $request->pkp_nomor_transaksi_bank,
                'non_pkp_atas_nama_bank' => $request->non_pkp_atas_nama_bank,
                'non_pkp_nama_bank' => $request->non_pkp_nama_bank,
                'non_pkp_nomor_transaksi_bank' => $request->non_pkp_nomor_transaksi_bank,
                'target_tahunan' => $request->target_tahunan,
                'target_bulanan' => $request->target_bulanan,
            ];
    
            // Hanya update qris jika ada file baru
            if ($request->hasFile('qris')) {
                // Hapus file lama jika ada
                if ($outlet->qris) {
                    Storage::disk('uploads')->delete($outlet->qris);
                }
                $updateData['qris'] = $request->file('qris')->store('qris', 'uploads');
            }
    
            $outlet->update($updateData);
            
            return $this->successResponse($outlet, 'Outlet updated successfully');
        } catch (ValidationException $th) {
            return response()->json([
                'success' => false,
                'message' => 'Terdapat kesalahan validasi pada form',
                'data' => $th->errors()
            ], 422);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengupdate outlet: ' . $th->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
public function destroy(Outlet $outlet)
{
    try {
        // Cek outlet ID yang akan dihapus
        \Log::info('Attempting to delete outlet ID: ' . $outlet->id);
        
        $outletId = $outlet->id;
        $deleted = $outlet->delete();

        if (!$deleted) {
            \Log::error("Gagal menghapus outlet ID: {$outletId}");
            return response()->json(['message' => 'Outlet tidak dapat dihapus'], 400);
        }

        \Log::info("Outlet ID: {$outletId} berhasil dihapus");
        return response()->json(['message' => 'Outlet berhasil dihapus'], 200);
    } catch (\Throwable $th) {
        \Log::error("Error saat menghapus outlet: " . $th->getMessage());
        return response()->json(['message' => 'Gagal menghapus outlet'], 500);
    }
}


 }
