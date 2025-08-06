<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\InventoryHistory;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    use ApiResponse;


    public function listAllInventories()
    {
        $inventories = Inventory::all();
        return $this->successResponse($inventories, 'All inventory records');
    }

    public function transferStock(Request $request)
    {
        try {
            DB::beginTransaction();

            $request->validate([
                'product_id' => 'required|exists:products,id',
                'source_outlet_id' => 'required|exists:outlets,id',
                'target_outlet_id' => 'required|exists:outlets,id|different:source_outlet_id',
                'quantity' => 'required|integer|min:1',
                'user_id' => 'required|exists:users,id',
                'notes' => 'nullable|string',
            ]);

            // Get source inventory
            $sourceInventory = Inventory::where('product_id', $request->product_id)
                ->where('outlet_id', $request->source_outlet_id)
                ->first();

            if (!$sourceInventory) {
                return $this->errorResponse('Source inventory not found');
            }

            // Check if source has enough stock
            if ($sourceInventory->quantity < $request->quantity) {
                return $this->errorResponse('Not enough stock in source outlet');
            }

            // Store the quantity before for history records
            $sourceQuantityBefore = $sourceInventory->quantity;

            // Get or create target inventory
            $targetInventory = Inventory::firstOrCreate(
                [
                    'product_id' => $request->product_id,
                    'outlet_id' => $request->target_outlet_id
                ],
                [
                    'min_stock' => $sourceInventory->min_stock,
                    'quantity' => 0
                ]
            );

            // Store the quantity before for history records
            $targetQuantityBefore = $targetInventory->quantity;

            // Update quantities
            $sourceInventory->quantity -= $request->quantity;
            $targetInventory->quantity += $request->quantity;

            // Save changes
            $sourceInventory->save();
            $targetInventory->save();

            // Record in inventory history for source (reduction)
            // Untuk transfer_out (pengurangan)
            // Untuk transfer_out (pengurangan)
            // Di bagian transfer_out (pengurangan stok)
            InventoryHistory::create([
                'type' => 'adjustment',
                'product_id' => $request->product_id,
                'outlet_id' => $request->source_outlet_id,
                'user_id' => $request->user_id,
                'quantity_before' => $sourceQuantityBefore,
                'quantity_after' => $sourceInventory->quantity,
                'quantity_change' => -$request->quantity, // HARUS diisi (nilai negatif)
                'type' => 'transfer_out',
                'notes' => $request->notes ?? 'Transfer to outlet #' . $request->target_outlet_id,
            ]);

            // Di bagian transfer_in (penambahan stok)
            InventoryHistory::create([
                'type' => 'adjustment',
                'product_id' => $request->product_id,
                'outlet_id' => $request->target_outlet_id,
                'user_id' => $request->user_id,
                'quantity_before' => $targetQuantityBefore,
                'quantity_after' => $targetInventory->quantity,
                'quantity_change' => $request->quantity, // HARUS diisi (nilai positif)
                'type' => 'transfer_in',
                'notes' => $request->notes ?? 'Transfer from outlet #' . $request->source_outlet_id,
            ]);

            DB::commit();

            return $this->successResponse([
                'source_inventory' => $sourceInventory->load('product', 'outlet'),
                'target_inventory' => $targetInventory->load('product', 'outlet'),
            ], 'Stock transferred successfully');
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->errorResponse($th->getMessage());
        }
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $inventory = Inventory::all()->load('product', 'outlet', 'user');
            return $this->successResponse($inventory, 'Inventory retrieved successfully');
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage());
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
        try {
            $request->validate([
                'product_id' => 'required|exists:products,id',
                'outlet_id' => 'required|exists:outlets,id',
                'user_id' => 'required|exists:users,id',
                'min_stock' => 'required|integer',
                'quantity' => 'required|integer',
            ]);
            $inventory = Inventory::create($request->all());
            return $this->successResponse($inventory, 'Inventory created successfully');
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Inventory $inventory)
    {
        try {
            $inventory->load('product', 'outlet', 'user');
            return $this->successResponse($inventory, 'Inventory retrieved successfully');
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Inventory $inventory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Inventory $inventory)
    {
        try {
            $request->validate([
                'product_id' => 'required|exists:products,id',
                'outlet_id' => 'required|exists:outlets,id',
                'user_id' => 'required|exists:users,id',
                'min_stock' => 'required|integer',
                'quantity' => 'required|integer',
            ]);
            $inventory->update($request->all());
            return $this->successResponse($inventory, 'Inventory updated successfully');
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Inventory $inventory)
    {
        try {
            $inventory->delete();
            return $this->successResponse(null, 'Inventory deleted successfully');
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage());
        }
    }
}
