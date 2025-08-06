<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    use ApiResponse;

    public function indexOLD()
    {
        try {
            $categories = Category::withCount('products')->get();
            return $this->successResponse($categories, 'Categories retrieved successfully');
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage());
        }
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $categories = Category::with(['products.inventory'])->get()
                ->map(function ($category) {
                    $category->total_inventory_quantity = $category->products->sum(function ($product) {
                        // Akses relasi inventory (bukan inventories)
                        return $product->inventory ? $product->inventory->quantity : 0;
                    });
                    return $category;
                });
    
            return response()->json([
                'success' => true,
                'data' => $categories
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memuat data kategori'
            ], 500);
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
                'description' => 'required|string|max:255',
            ]);
            $category = Category::create($request->all());
            return $this->successResponse($category, 'Category created successfully');
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        try {
            return $this->successResponse($category, 'Category retrieved successfully');
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string|max:255',
            ]);
            $category->update($request->all());
            return $this->successResponse($category, 'Category updated successfully');
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        try {
            // Cek apakah kategori masih memiliki produk
            if ($category->products()->count() > 0) {
                return $this->errorResponse('Category cannot be deleted because it still has associated products. Please delete or move the products first.', 422);
            }
    
            DB::beginTransaction();
            
            // Soft delete semua produk terkait
            $category->products()->delete();
            
            // Soft delete kategori
            $category->delete();
            
            DB::commit();
            
            return $this->successResponse(null, 'Category and related products have been archived');
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->errorResponse('Failed to archive category: ' . $th->getMessage());
        }
    }
}
