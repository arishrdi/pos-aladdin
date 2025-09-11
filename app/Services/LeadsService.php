<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LeadsService
{
    private $baseUrl = 'https://leadsaladdin.demowebjalan.com/api/external';
    
    /**
     * Cari leads berdasarkan query (nama atau nomor telepon)
     */
    public function searchLeads($query)
    {
        try {
            // Jika query adalah nomor telepon, format dulu
            if (preg_match('/^[0-9+\-\s()]+$/', $query)) {
                $formattedPhone = $this->formatPhoneForLeads($query);
                $response = Http::timeout(10)->get("{$this->baseUrl}/leads/by-phone", [
                    'phone' => $formattedPhone
                ]);
            } else {
                // Jika query adalah nama, gunakan endpoint search umum (jika ada)
                // Untuk sekarang kita tetap coba by-phone dengan query asli
                $response = Http::timeout(10)->get("{$this->baseUrl}/leads/by-phone", [
                    'phone' => $query
                ]);
            }
            
            if ($response->successful()) {
                $data = $response->json();
                if ($data['success'] ?? false) {
                    $leads = $data['data'] ?? [];
                    // Pastikan selalu return array, bahkan jika hanya 1 lead
                    return is_array($leads) ? $leads : [$leads];
                }
            }
            
            return [];
        } catch (\Exception $e) {
            Log::error('Error searching leads: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Cari lead berdasarkan nomor telepon (backward compatibility)
     * @deprecated Use searchLeads instead
     */
    public function findLeadByPhone($phone)
    {
        $leads = $this->searchLeads($phone);
        return !empty($leads) ? $leads[0] : null;
    }
    
    /**
     * Update status lead menjadi CROSS_SELLING
     */
    public function updateLeadStatus($leadId, $status = 'CROSS_SELLING')
    {
        try {
            $response = Http::timeout(10)->put("{$this->baseUrl}/leads/{$leadId}/status", [
                'status' => $status
            ]);
            
            if ($response->successful()) {
                $data = $response->json();
                return $data['success'] ?? false;
            }
            
            return false;
        } catch (\Exception $e) {
            Log::error('Error updating lead status: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Convert lead data menjadi member data
     */
    public function convertLeadToMember($leadData, $outletId)
    {
        $memberData = [
            'name' => $leadData['customer_name'],
            'phone' => str_replace('+62', '0', $leadData['customer_phone']), // Convert +62 ke 0
            'gender' => strtolower($leadData['sapaan']) === 'bapak' ? 'male' : 'female',
            'outlet_id' => $outletId,
            'lead_id' => $leadData['id'],
            'lead_number' => $leadData['lead_number']
        ];
        
        return $memberData;
    }
    
    /**
     * Format nomor telepon untuk pencarian leads
     */
    public function formatPhoneForLeads($phone)
    {
        // Hapus semua karakter non-digit
        $phone = preg_replace('/\D/', '', $phone);
        
        // Convert 08xx menjadi +628xx
        if (substr($phone, 0, 2) === '08') {
            return '+62' . substr($phone, 1);
        }
        
        // Convert 62xxx menjadi +62xxx
        if (substr($phone, 0, 2) === '62') {
            return '+' . $phone;
        }
        
        // Jika sudah format +62xxx
        if (substr($phone, 0, 3) === '+62') {
            return $phone;
        }
        
        return $phone;
    }
}