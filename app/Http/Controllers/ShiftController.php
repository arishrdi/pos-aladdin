<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    use ApiResponse;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $shifts = Shift::all();
            return $this->successResponse($shifts, 'Shifts retrieved successfully');
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(Request $request)
    // {
    //     try {
    //         $request->validate([
    //             'outlet_id' => 'required|exists:outlets,id',
    //             'user_id' => 'required|exists:users,id',
    //             'start_time' => 'required|date_format:H:i:s', // Format 24-hour (e.g., "14:30:00")
    //             'end_time' => 'required|date_format:H:i:s|after:start_time',
    //         ]);

    //         $shift = Shift::create($request->all());
    //         return $this->successResponse($shift, 'Shift created successfully');
    //     } catch (\Throwable $th) {
    //         return $this->errorResponse($th->getMessage());
    //     }
    // }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'outlet_id' => 'required|exists:outlets,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time', // end_time harus setelah start_time
        ]);

        try {
            $existingShift = Shift::where('user_id', $request->user_id)->first();

            if ($existingShift) {
                $existingShift->update([
                    'outlet_id' => $request->outlet_id,
                    'start_time' => $request->start_time,
                    'end_time' => $request->end_time,
                ]);
                return $this->successResponse($existingShift, 'Update Shift Success');
            }

            $shift = Shift::create([
                'user_id' => $request->user_id,
                'outlet_id' => $request->outlet_id,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
            ]);

            return $this->successResponse($shift, 'Create Shift Success');
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage());
        }
    }



    /**
     * Display the specified resource.
     */
    public function show(Shift $shift)
    {
        try {
            $shift->load('outlet', 'user');
            return $this->successResponse($shift, 'Shift retrieved successfully');
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Shift $shift)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Shift $shift)
    {
        try {
            $request->validate([
                'outlet_id' => 'required|exists:outlets,id',
                'user_id' => 'required|exists:users,id',
                'start_time' => 'required|date',
                'end_time' => 'required|date',
                'starting_cash' => 'required|numeric',
                'ending_cash' => 'required|numeric',
                'expected_cash' => 'required|numeric',
                'cash_difference' => 'required|numeric',
                'notes' => 'required|string',
                'is_closed' => 'required|boolean',
            ]);

            $shift->update($request->all());
            return $this->successResponse($shift, 'Shift updated successfully');
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Shift $shift)
    {
        try {
            $shift->delete();
            return $this->successResponse(null, 'Shift deleted successfully');
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage());
        }
    }
}
