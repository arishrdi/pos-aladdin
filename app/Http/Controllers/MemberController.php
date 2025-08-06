<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MemberController extends Controller
{

    use ApiResponse;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $members = Member::withCount('orders')->get();
            return $this->successResponse($members, 'Successfully retrieved members');
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
        DB::beginTransaction();

        try {
            $request->validate([
                'name' => 'required|string',
                'phone' => 'required|string',
                'email' => 'nullable|string|email',
                'address' => 'nullable|string',
                'gender' => 'nullable|string|in:male,female'
            ]);

            $lastMember = Member::lockForUpdate()->orderBy('id', 'desc')->first();
            $nextNumber = $lastMember ? $lastMember->id + 1 : 1;
            $memberCode = str_pad($nextNumber, 7, '0', STR_PAD_LEFT);

            $member = Member::create([
                'name' => $request->name,
                'member_code' => $memberCode,
                'phone' => $request->phone,
                'email' => $request->email,
                'address' => $request->address,
                'gender' => $request->gender
            ]);

            DB::commit();

            return $this->successResponse($member, "Member created successfully");
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->errorResponse('Terjadi kesalahan saat menyimpan data. Silakan coba lagi.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Member $member)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Member $member)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Member $member)
    {
        try {
            $request->validate([
                'name' => 'required|string',
                // 'member_code' => 'required|string',
                'phone' => 'nullable|string',
                'email' => 'nullable|string|email',
                'address' => 'nullable|string',
                'gender' => 'nullable|string|in:male,female'
            ]);

            $member->update([
                'name' => $request->name,
                'member_code' => $request->member_code,
                // 'phone' => $request->phone,
                'email' => $request->email,
                'address' => $request->address,
                'gender' => $request->gender
            ]);

            return $this->successResponse($member, "Member created successfully");
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Member $member)
    {
        try {
            $member->delete();
            return $this->successResponse(null, 'Member deleted successfully');
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage());
        }
    }
}
