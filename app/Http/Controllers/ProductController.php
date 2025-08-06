<?php

namespace App\Http\Controllers;

use App\Models\Outlet;
use App\Models\Product;
use Milon\Barcode\DNS1D;
use App\Models\Inventory;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Models\InventoryHistory;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
// use Illuminate\Container\Attributes\Log;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    use ApiResponse;
    /**
     * Display a listing of the resource.
     */
    public function indexOld()
    {
        try {
            $products = Product::with('outlets')->get();
            return $this->successResponse($products, 'Products retrieved successfully');
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage());
        }
    }

    public function index()
    {
        try {
            $products = Product::with([
                'category:id,name',
                'inventory',
                'inventory.outlet:id,name'
            ])->get();
    
            return response()->json([
                'status' => true,
                'message' => 'Daftar produk dengan kategori dan inventory',
                'data' => $products
            ]);
        } catch (\Throwable $th) {
            Log::error('Product index error: '.$th->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat memuat data produk'
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function storeOld(Request $request)
    {

        if ($request->has('image') && $request->image === null) {
            $request->request->remove('image');
        }

        // dd($request->all);

        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'sku' => 'required|string|max:255',
                'description' => 'nullable|string|max:255',
                'price' => 'required|numeric',
                'category_id' => 'required|exists:categories,id',
                'image' => 'nullable|sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:10048',
                'is_active' => 'required|boolean',
                'outlet_ids' => 'required|array',
                'outlet_ids.*' => 'exists:outlets,id',
                'quantity' => 'required|numeric',
                'min_stock' => 'required|numeric',
            ]);

            DB::beginTransaction();
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('products', 'uploads');
                $imagePath = $path;
            } else {
                $imagePath = null;
            }

            $product = Product::create([
                'name' => $request->name,
                'sku' => $request->sku,
                'description' => $request->description,
                'price' => $request->price,
                'category_id' => $request->category_id,
                'image' => $imagePath,
                'is_active' => $request->is_active,
            ]);

            foreach ($request->outlet_ids as $outletId) {
                Inventory::create([
                    'product_id' => $product->id,
                    'outlet_id' => $outletId,
                    'quantity' => $request->quantity,
                    'min_stock' => $request->min_stock,
                ]);
                
                InventoryHistory::create([
                    'product_id' => $product->id,
                    'outlet_id' => $outletId,
                    'quantity_change' => $request->quantity,
                    'quantity_before' => 0,
                    'quantity_after' => $request->quantity,
                    'type' => 'adjustment',
                    'notes' => 'Stok awal produk baru',
                    'user_id' => $request->user()->id,
                ]);
            }

            DB::commit();

            return $this->successResponse($product, 'Product created successfully');
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->errorResponse($th->getMessage());
        // } catch (\Illuminate\Validation\ValidationException $e) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Validasi gagal',
        //         'errors' => collect($e->errors())->map(function ($messages, $field) {
        //             return [
        //                 'field' => $field,
        //                 'messages' => $messages
        //             ];
        //         })->values()->all()
        //     ], 422);
        }
    }

    public function store(Request $request)
    {
        if ($request->has('image') && $request->image === null) {
            $request->request->remove('image');
        }
    
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'sku' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('products', 'sku')->whereNull('deleted_at')
                ],
                'barcode' => [
                    'nullable',
                    'string',
                    'max:255',
                    Rule::unique('products', 'barcode')->whereNull('deleted_at')
                ],
                'description' => 'nullable|string|max:255',
                'price' => 'required|numeric',
                'category_id' => 'required|exists:categories,id',
                'image' => 'nullable|sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'is_active' => 'required|boolean',
                'outlet_ids' => 'required|array',
                'outlet_ids.*' => 'exists:outlets,id',
                'quantity' => 'required|numeric',
                'min_stock' => 'required|numeric',
            ]);
    
            // Validasi manual untuk SKU dan barcode
            if ($request->sku && Product::where('sku', $request->sku)->exists()) {
                throw ValidationException::withMessages([
                    'sku' => 'SKU sudah digunakan oleh produk aktif'
                ]);
            }
    
            if ($request->barcode && Product::where('barcode', $request->barcode)->exists()) {
                throw ValidationException::withMessages([
                    'barcode' => 'Barcode sudah digunakan oleh produk aktif'
                ]);
            }
    
            DB::beginTransaction();
            
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('products', 'uploads');
                $imagePath = $path;
            } else {
                $imagePath = null;
            }
    
            // Generate SKU dan barcode jika tidak diisi
            $sku = $request->sku ?? $this->generateUniqueSku();
            $barcode = $request->barcode ?? $this->generateUniqueBarcode();
    
            $product = Product::create([
                'name' => $request->name,
                'sku' => $sku,
                'barcode' => $barcode,
                'description' => $request->description,
                'price' => $request->price,
                'category_id' => $request->category_id,
                'image' => $imagePath,
                'is_active' => $request->is_active,
            ]);
    
            foreach ($request->outlet_ids as $outletId) {
                Inventory::create([
                    'product_id' => $product->id,
                    'outlet_id' => $outletId,
                    'quantity' => $request->quantity,
                    'min_stock' => $request->min_stock,
                ]);
                
                InventoryHistory::create([
                    'product_id' => $product->id,
                    'outlet_id' => $outletId,
                    'quantity_change' => $request->quantity,
                    'quantity_before' => 0,
                    'quantity_after' => $request->quantity,
                    'type' => 'adjustment',
                    'notes' => 'Stok awal produk baru',
                    'user_id' => $request->user()->id,
                ]);
            }
    
            DB::commit();
    
            return $this->successResponse($product, 'Product created successfully');
        } catch (\Throwable $th) {
            DB::rollBack();
            if ($th instanceof \Illuminate\Validation\ValidationException) {
                return $this->errorResponse($th->errors(), 'Validation failed', 422);
            }
            return $this->errorResponse($th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        try {
            return $this->successResponse($product->load(['category', 'inventory']), 'Product retrieved successfully');
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'sku' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('products', 'sku')->ignore($product->id)->whereNull('deleted_at')
                ],
                'barcode' => [
                    'nullable',
                    'string',
                    'max:255',
                    Rule::unique('products', 'barcode')->ignore($product->id)->whereNull('deleted_at')
                ],
                'description' => 'nullable|string|max:255',
                'price' => 'required|numeric',
                'category_id' => 'required|exists:categories,id',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10048',
                'is_active' => 'required|boolean',
                'outlet_ids' => 'required|array',
                'outlet_ids.*' => 'exists:outlets,id',
                // 'quantity' => 'required|numeric',
                'min_stock' => 'required|numeric',
            ]);
    
            // Validasi manual untuk SKU dan barcode
            if ($request->sku && Product::where('sku', $request->sku)->where('id', '!=', $product->id)->exists()) {
                throw ValidationException::withMessages([
                    'sku' => 'SKU sudah digunakan oleh produk aktif'
                ]);
            }
    
            if ($request->barcode && Product::where('barcode', $request->barcode)->where('id', '!=', $product->id)->exists()) {
                throw ValidationException::withMessages([
                    'barcode' => 'Barcode sudah digunakan oleh produk aktif'
                ]);
            }
    
            DB::beginTransaction();
            
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('products', 'uploads');
            } else {
                $imagePath = $product->image;
            }
    
            $product->update([
                'name' => $request->name,
                'sku' => $request->sku,
                'barcode' => $request->barcode,
                'description' => $request->description,
                'price' => $request->price,
                'category_id' => $request->category_id,
                'image' => $imagePath,
                'is_active' => $request->is_active,
            ]);
    
            foreach ($request->outlet_ids as $outletId) {
                Inventory::updateOrCreate(
                    [
                        'product_id' => $product->id,
                        'outlet_id' => $outletId
                    ],
                    [
                        'quantity' => Inventory::where('product_id', $product->id)
                        ->where('outlet_id', $outletId)
                        ->value('quantity') ?? 0,
                        'min_stock' => $request->min_stock
                    ]
                );
            }
    
            Inventory::where('product_id', $product->id)
                ->whereNotIn('outlet_id', $request->outlet_ids)
                ->delete();
    
            DB::commit();
    
            return $this->successResponse($product, 'Product updated successfully');
        } catch (\Throwable $th) {
            DB::rollBack();
            if ($th instanceof \Illuminate\Validation\ValidationException) {
                return $this->errorResponse($th->errors(), 'Validation failed', 422);
            }
            return $this->errorResponse($th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        try {
            DB::beginTransaction();
            Inventory::where('product_id', $product->id)->delete();
            $product->delete();
            DB::commit();
            return $this->successResponse(null, 'Product deleted successfully');
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->errorResponse($th->getMessage());
        }
    }

   /**
     * Get product detail with all outlets for edit form
     */
    public function getProductDetail(Product $product)
    {
        try {
            $productData = $product->load([
                'category:id,name',
                'outlets:id,name', // Ini hanya load outlet yang BENAR-BENAR dimiliki produk
                'inventory' => function($query) {
                    $query->select('id', 'product_id', 'outlet_id', 'quantity', 'min_stock');
                }
            ]);

            // Ambil hanya outlet yang benar-benar memiliki produk ini
            $selectedOutletIds = $productData->outlets->pluck('id')->toArray();

            // Format response untuk frontend
            $response = [
                'id' => $productData->id,
                'name' => $productData->name,
                'sku' => $productData->sku,
                'barcode' => $productData->barcode,
                'description' => $productData->description,
                'price' => $productData->price,
                'image' => $productData->image,
                'image_url' => $productData->image_url,
                'is_active' => $productData->is_active,
                'category' => $productData->category,
                'outlets' => $productData->outlets, // Hanya outlet yang dipilih
                'inventory' => $productData->inventory,
                'outlet_ids' => $selectedOutletIds, // Hanya ID outlet yang dipilih
                'quantity' => $productData->inventory->first()->quantity ?? 0,
                'min_stock' => $productData->inventory->first()->min_stock ?? 0,
            ];

            Log::info('Product detail loaded', [
                'product_id' => $product->id,
                'selected_outlets' => $selectedOutletIds,
                'total_outlets_for_product' => count($selectedOutletIds)
            ]);

            return $this->successResponse($response, 'Product detail retrieved successfully');
        } catch (\Throwable $th) {
            Log::error('Error loading product detail', [
                'product_id' => $product->id,
                'error' => $th->getMessage()
            ]);
            return $this->errorResponse($th->getMessage());
        }
    }

    public function getOutletProducts(Request $request, $outletId)
    {
        try {
            $user = $request->user(); 
            $outlet = Outlet::findOrFail($outletId);

            $isCashier = strtolower($user->role) === 'kasir';

            $products = $outlet->products()
                ->with([
                    'category',
                    'outlets',
                    'inventoryHistory' => function ($query) {
                        $query->select('id', 'product_id', 'quantity_before', 'quantity_after', 'quantity_change', 'type')
                            ->latest()
                            ->limit(1);
                    }
                ])
                ->when($isCashier, function ($query) {
                    $query->where('is_active', true);
                })
                ->get()
                ->map(function ($product) use ($outlet) {
                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'sku' => $product->sku,
                        'barcode' => $product->barcode,
                        'description' => $product->description,
                        'price' => $product->price,
                        'image_url' => $product->image_url,
                        'is_active' => $product->is_active,
                        'category' => [
                            'id' => $product->category->id,
                            'name' => $product->category->name,
                        ],
                        'min_stock' => $product->pivot->min_stock ?? null,
                        'quantity' => $product->pivot->quantity ?? 0,
                        'outlets' => $product->outlets
                            ->filter(function ($o) use ($outlet) {
                                return $o->id === $outlet->id;
                            })
                            ->values()
                            ->map(function ($o) {
                                return [
                                    'id' => $o->id,
                                    'name' => $o->name,
                                    'qris_url' => $o->qris_url,
                                    'tax' => $o->tax,
                                ];
                            }),
                        'inventory_history' => $product->inventoryHistory->map(function ($history) {
                            return [
                                'quantity_before' => $history->quantity_before,
                                'quantity_after' => $history->quantity_after,
                                'quantity_change' => $history->quantity_change,
                                'type' => $history->type,
                            ];
                        }),
                    ];
                });

            return $this->successResponse($products, 'Products retrieved successfully');
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage());
        }
    }
    
    public function getOutletProductsPOS(Request $request, $outletId)
    {
        try {
            $user = $request->user();
            $outlet = Outlet::with('cashRegisters')->findOrFail($outletId);

            $isCashier = strtolower($user->role) === 'kasir';

            $products = $outlet->products()
                ->with(['category', 'outlets'])
                ->when($isCashier, function ($query) {
                    $query->where('is_active', true);
                })
                ->orderBy('name', 'asc')
                ->get()
                ->map(function ($product) use ($outlet) {
                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'sku' => $product->sku,
                        'barcode' => $product->barcode,
                        'description' => $product->description,
                        'price' => $product->price,
                        'image' => asset('storage/' . $product->image),
                        'is_active' => $product->is_active,
                        'category' => [
                            'id' => $product->category->id,
                            'name' => $product->category->name,
                        ],
                        'min_stock' => $product->pivot->min_stock ?? null,
                        'quantity' => $product->pivot->quantity ?? 0,
                    ];
                });

            $outletData = [
                'id' => $outlet->id,
                'name' => $outlet->name,
                'address' => $outlet->address,
                'phone' => $outlet->phone,
                'email' => $outlet->email,
                'tax' => $outlet->tax,
                'qris_url' => $outlet->qris ? asset('storage/' . $outlet->qris) : null,
                'is_active' => $outlet->is_active,
            ];

            return $this->successResponse([
                'products' => $products,
                'outlet' => $outletData
            ], 'Products and outlet data retrieved successfully');
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage());
        }
    }

    public function findByBarcode(Request $request, $barcode)
    {
        try {

            $product = Product::with(['category', 'inventory'])
                ->where('barcode', $barcode)
                ->first();

            if (!$product) {
                return $this->errorResponse('Product not found', 404);
            }

            return $this->successResponse($product, 'Product found');
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage());
        }
    }
   
    public function posFindByBarcode(Request $request, $outletId, $barcode)
    {
        try {
            // Validasi barcode (opsional - bisa diaktifkan jika diperlukan)
            if (empty($barcode) || strlen($barcode) < 3) {
                return $this->errorResponse('Invalid barcode format', 400);
            }

            // Method 1: Menggunakan many-to-many dengan pivot table
            $product = Outlet::findOrFail($outletId)
                ->products()
                ->where('products.barcode', $barcode)
                ->where('products.is_active', true)
                ->wherePivot('quantity', '>', 0)
                ->withPivot('quantity')
                ->with(['category'])
                ->first();

            // Jika Method 1 tidak berhasil, coba Method 2
            // if (!$product) {
            //     $product = Product::where('barcode', $barcode)
            //         ->where('is_active', true)
            //         ->whereHas('outlets', function($query) use ($outletId) {
            //             $query->where('outlets.id', $outletId)
            //                 ->where('outlet_product.quantity', '>', 0);
            //         })
            //         ->with(['category', 'outlets' => function($query) use ($outletId) {
            //             $query->where('outlets.id', $outletId);
            //         }])
            //         ->first();
            // }

            if (!$product) {
                // Log untuk debugging
                Log::warning('Product not found or out of stock', [
                    'barcode' => $barcode,
                    'outlet_id' => $outletId,
                    'user_id' => auth()->id()
                ]);
                
                return $this->errorResponse('Product not found or out of stock', 404);
            }

            // Ambil quantity dari pivot table
            $stock = 0;
            if ($product->pivot) {
                // Dari Method 1
                $stock = $product->pivot->quantity;
            } elseif ($product->outlets && $product->outlets->count() > 0) {
                // Dari Method 2
                $stock = $product->outlets->first()->pivot->quantity;
            }

            // Validasi stock
            if ($stock <= 0) {
                return $this->errorResponse('Product is out of stock', 404);
            }

            // Log sukses
            Log::info('Barcode scanned successfully', [
                'barcode' => $barcode,
                'product_id' => $product->id,
                'outlet_id' => $outletId,
                'stock' => $stock,
                'user_id' => auth()->id()
            ]);

            return $this->successResponse([
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'barcode' => $product->barcode,
                'stock' => $stock,
                'min_stock' => $product->min_stock,
                'is_active' => $product->is_active,
                'category' => $product->category ? $product->category->name : 'Uncategorized',
                'image_url' => $product->image_url ? asset('storage/'.$product->image_url) : null,
                'inventory' => [
                    'quantity' => $stock
                ]
            ], 'Product found');

        } catch (ModelNotFoundException $e) {
            Log::error('Outlet not found', [
                'outlet_id' => $outletId,
                'error' => $e->getMessage()
            ]);
            return $this->errorResponse('Outlet not found', 404);
        } catch (\Exception $e) {
            Log::error('Server error in posFindByBarcode', [
                'barcode' => $barcode,
                'outlet_id' => $outletId,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return $this->errorResponse('Server error occurred', 500);
        }
    }

    public function generateBarcode()
    {
        try {
            $barcode = $this->generateUniqueBarcode();
            return $this->successResponse(['barcode' => $barcode], 'Barcode generated');
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage());
        }
    }

    /**
     * Helper function to generate unique barcode
     */
    private function generateUniqueBarcode()
    {
        do {
            $barcode = rand(100000000, 999999999); // EAN-13 style
        } while (Product::where('barcode', $barcode)->exists());

        return $barcode;
    }

    public function generateBarcodeImage($code)
    {
        try {
            $barcode = new DNS1D();
            $barcode->setStorPath(public_path('barcodes'));

            $barcodeDir = public_path('barcodes');
            if (!File::exists($barcodeDir)) {
                File::makeDirectory($barcodeDir, 0755, true);
            }

            $barcodeImage = $barcode->getBarcodePNG($code, 'C39');
            $filePath = public_path("barcodes/{$code}.png");
            File::put($filePath, base64_decode($barcodeImage));
            
            return response()->json([
                'status' => true,
                'message' => 'Barcode image generated successfully',
                'image_url' => asset("barcodes/{$code}.png")
            ]);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage());
        }
    }
}