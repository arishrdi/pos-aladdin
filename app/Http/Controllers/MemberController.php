<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Traits\ApiResponse;
use App\Services\LeadsService;
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
                'gender' => 'required|string|in:male,female'
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
            return $this->errorResponse('Terjadi kesalahan saat menyimpan data. Silakan coba lagi.', $th);
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
    
    /**
     * Search members by name or phone, including leads integration
     */
    public function search(Request $request)
    {
        try {
            $query = $request->get('q', '');
            $outletId = auth()->user()->outlet_id ?? 1;
            
            $results = [];
            
            if (strlen($query) >= 2) {
                // Cari di database member lokal (termasuk yang belum punya outlet_id)
                $members = Member::where(function($q) use ($query) {
                        $q->where('name', 'LIKE', "%{$query}%")
                          ->orWhere('phone', 'LIKE', "%{$query}%")
                          ->orWhere('member_code', 'LIKE', "%{$query}%");
                    })
                    ->where(function($q) use ($outletId) {
                        $q->where('outlet_id', $outletId)
                          ->orWhereNull('outlet_id'); // Include members without outlet_id
                    })
                    ->limit(10)
                    ->get();
                
                foreach ($members as $member) {
                    $results[] = [
                        'id' => $member->id,
                        'name' => $member->name,
                        'phone' => $member->phone,
                        'identifier' => $member->getLeadIdentifier(),
                        'type' => 'member',
                        'source' => $member->isFromLead() ? 'leads' : 'local'
                    ];
                }
                
                // Log::info("Member search debug", [
                //     'query' => $query,
                //     'outletId' => $outletId,
                //     'local_members_count' => $members->count(),
                //     'total_results_before_leads' => count($results)
                // ]);
                
                // Cari juga di leads API untuk semua query
                $leadsService = new LeadsService();
                $leadDataList = $leadsService->searchLeads($query);
                
                foreach ($leadDataList as $leadData) {
                    // Cek apakah lead sudah ada di database member
                    $existingMember = Member::where('lead_id', $leadData['id'])->first();
                    
                    if (!$existingMember) {
                        $results[] = [
                            'id' => 'lead_' . $leadData['id'],
                            'name' => $leadData['customer_name'],
                            'phone' => str_replace('+', '', $leadData['customer_phone']),
                            // 'phone' => str_replace('+62', '0', $leadData['customer_phone']),
                            'identifier' => $leadData['lead_number'],
                            'type' => 'lead',
                            'source' => 'leads_api',
                            'lead_data' => $leadData,
                            'status' => $leadData['status'] ?? 'UNKNOWN',
                            'priority' => $leadData['priority'] ?? 'Normal',
                            'sapaan' => $leadData['sapaan'] ?? 'Bapak'
                        ];
                    }
                }
            }
            
            return $this->successResponse($results, 'Search results retrieved successfully');
            
        } catch (\Throwable $th) {
            return $this->errorResponse('Error searching members: ' . $th->getMessage());
        }
    }
    
}
