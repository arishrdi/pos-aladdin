<?php

namespace App\Http\Controllers;

use App\Models\CashRegister;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class CashRegisterController extends Controller
{
    use ApiResponse;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $cashRegisters = CashRegister::with('outlet', 'cashRegisterTransactions')->get();
            return $this->successResponse($cashRegisters, 'Successfully get cash registers');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
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
    public function store(Request $request)
    {
        $request->validate([
            'outlet_id' => 'required|exists:outlets,id',
            'name' => 'required|string|max:255',
            'is_active' => 'boolean',
            'balance' => 'required|numeric|min:0',
        ]);

        try {
            $cashRegister = CashRegister::create($request->all());
            return $this->successResponse($cashRegister, 'Successfully create cash register');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($outlet_id)
    {
        try {
            $cashRegister = CashRegister::where('outlet_id', $outlet_id)->first();
            return $this->successResponse($cashRegister, 'Successfully get cash register');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CashRegister $cashRegister)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CashRegister $cashRegister)
    {
        $request->validate([
            'outlet_id' => 'required|exists:outlets,id',
            'name' => 'required|string|max:255',
            'is_active' => 'boolean'
        ]);
        
        try {
            $cashRegister->update($request->all());
            return $this->successResponse($cashRegister, 'Successfully update cash register');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CashRegister $cashRegister)
    {
        try {
            $cashRegister->delete();
            return $this->successResponse(null, 'Successfully delete cash register');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }
}
