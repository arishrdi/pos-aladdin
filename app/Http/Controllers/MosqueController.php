<?php

namespace App\Http\Controllers;

use App\Models\Mosque;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MosqueController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = Mosque::with('outlet');

            // Filter by outlet if provided
            if ($request->has('outlet_id')) {
                $query->where('outlet_id', $request->outlet_id);
            }

            // Search functionality
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('address', 'like', "%{$search}%");
                });
            }

            $mosques = $query->latest()->get();

            return $this->successResponse($mosques, 'Successfully retrieved mosques');
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'address' => 'required|string',
                'outlet_id' => 'nullable|exists:outlets,id'
            ]);

            // Use authenticated user's outlet if not provided
            $outletId = $request->outlet_id ?? auth()->user()->outlet_id;

            if (!$outletId) {
                return $this->errorResponse('Outlet ID is required');
            }

            $mosque = Mosque::create([
                'name' => $request->name,
                'address' => $request->address,
                'outlet_id' => $outletId
            ]);

            DB::commit();

            return $this->successResponse($mosque->load('outlet'), 'Mosque created successfully');
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->errorResponse('Terjadi kesalahan saat menyimpan data masjid. Silakan coba lagi.', $th);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Mosque $mosque)
    {
        try {
            $mosque->load('outlet');
            return $this->successResponse($mosque, 'Successfully retrieved mosque');
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Mosque $mosque)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'address' => 'required|string',
                'outlet_id' => 'nullable|exists:outlets,id'
            ]);

            $mosque->update([
                'name' => $request->name,
                'address' => $request->address,
                'outlet_id' => $request->outlet_id ?? $mosque->outlet_id
            ]);

            return $this->successResponse($mosque->load('outlet'), 'Mosque updated successfully');
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Mosque $mosque)
    {
        try {
            $mosque->delete();
            return $this->successResponse(null, 'Mosque deleted successfully');
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage());
        }
    }
}
