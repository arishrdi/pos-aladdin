<?php

namespace App\Http\Controllers;

use App\Models\PrintTemplate;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PrintTemplateController extends Controller
{
    use ApiResponse;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    // public function store(Request $request)
    // {
    //     if ($request->has('logo') && $request->logo === null) {
    //         $request->request->remove('logo');
    //     }

    //     try {
    //         $validator = Validator::make($request->all(), [
    //             'outlet_id' => 'required|exists:outlets,id',
    //             'company_name' => 'nullable|string',
    //             'company_slogan' => 'nullable|string',
    //             'footer_message' => 'nullable|string',
    //             'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10048',
    //         ]);

    //         if ($validator->fails()) {
    //             return $this->errorResponse("Error", $validator->errors());
    //         }

    //         $data = $validator->validated();

    //         $imagePath = null;

    //         if ($request->hasFile('logo')) {
    //             $path = $request->file('logo')->store('logos', 'public');
    //             $imagePath = $path;
    //         }

    //         $print = PrintTemplate::updateOrCreate(
    //             ['outlet_id' => $data['outlet_id']],
    //             [
    //                 'company_name' => $data['company_name'] ?? null,
    //                 'company_slogan' => $data['company_slogan'] ?? null,
    //                 'footer_message' => $data['footer_message'] ?? null,
    //                 'logo' => $imagePath ?: null, // Hanya update logo jika ada file baru
    //             ]
    //         );

    //         if (!$request->hasFile('logo')) {
    //             unset($print->logo);
    //         }

    //         return $this->successResponse($print, "Successfully made changes");
    //     } catch (\Throwable $th) {
    //         return $this->errorResponse('Error', $th->getMessage());
    //     }
    // }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'outlet_id' => 'required|exists:outlets,id',
                'company_name' => 'nullable|string',
                'company_slogan' => 'nullable|string',
                'footer_message' => 'nullable|string',
                'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10048',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse("Validasi gagal", $validator->errors());
            }

            $data = $validator->validated();

            $imagePath = null;
            if ($request->hasFile('logo')) {
                $imagePath = $request->file('logo')->store('logos', 'uploads');
            }

            $updateData = [
                'company_name' => $data['company_name'] ?? null,
                'company_slogan' => $data['company_slogan'] ?? null,
                'footer_message' => $data['footer_message'] ?? null,
            ];

            // Tambahkan logo hanya jika user upload file baru
            if ($imagePath) {
                $updateData['logo'] = $imagePath;
            }

            $print = PrintTemplate::updateOrCreate(
                ['outlet_id' => $data['outlet_id']],
                $updateData
            );

            return $this->successResponse($print, "Berhasil menyimpan perubahan template.");
        } catch (\Throwable $th) {
            return $this->errorResponse('Terjadi kesalahan', $th->getMessage());
        }
    }
    

    /**
     * Display the specified resource.
     */
    // public function show($outlet_id)
    // {
    //     try {
    //         $printTemplate = PrintTemplate::where('outlet_id', $outlet_id)->first();

    //         if (!$printTemplate) {
    //             return $this->successResponse(null, 'There is no print template');
    //         }

    //         $printTemplate->load('outlet');
    //         return $this->successResponse($printTemplate, 'Succesfully getting print template with outlet');
    //     } catch (\Throwable $th) {
    //         return $this->errorResponse("Error while getting print template", $th->getMessage());
    //     }
    // }

    public function show($outlet_id)
    {
        try {
            // Validasi outlet_id
            if (!$outlet_id || $outlet_id === 'undefined') {
                return $this->errorResponse('Outlet ID tidak valid');
            }

            $printTemplate = PrintTemplate::with('outlet')
                                        ->where('outlet_id', $outlet_id)
                                        ->first();

            // Jika tidak ada template, kembalikan data default
            if (!$printTemplate) {
                // Ambil data outlet untuk default
                $outlet = \App\Models\Outlet::find($outlet_id);
                
                $defaultData = [
                    'company_name' => $outlet ? $outlet->name : config('app.name', 'Kifa Bakery'),
                    'company_slogan' => 'Rajanya Roti Hajatan',
                    'footer_message' => 'Terima kasih telah berbelanja',
                    'logo' => null,
                    'logo_url' => null,
                    'outlet' => $outlet
                ];
                
                return $this->successResponse($defaultData, 'Menggunakan template default');
            }

            // Format logo URL jika ada
            if ($printTemplate->logo) {
                $printTemplate->logo_url = asset('storage/uploads/' . $printTemplate->logo);
            }

            return $this->successResponse($printTemplate, 'Template berhasil diambil');

        } catch (\Throwable $th) {
            \Log::error('Get Print Template Error: ' . $th->getMessage());
            return $this->errorResponse('Terjadi kesalahan', $th->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PrintTemplate $printTemplate)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PrintTemplate $printTemplate)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PrintTemplate $printTemplate)
    {
        //
    }
}
