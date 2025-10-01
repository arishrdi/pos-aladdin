<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LeadsService
{
    private $baseUrl = 'https://leadsaladdin.demowebjalan.com/api/v1';

    private $token = '1|KNIQhAg8F5D1ckkVpmRKuzQb9yE8jOMQxfJ3h5Exdd558ab6';
    
    /**
     * Cari leads berdasarkan query (nama atau nomor telepon)
     */
    public function searchLeads($query)
    {
        try {
            // Jika query adalah nomor telepon, format dulu
            if (preg_match('/^[0-9+\-\s()]+$/', $query)) {
                $formattedPhone = $this->formatPhoneForLeads($query);
                $response = Http::timeout(10)
                    ->withHeaders([
                        'Authorization' => 'Bearer ' . $this->token,
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json'
                    ])
                    ->get("{$this->baseUrl}/leads/search", [
                        'phone' => $formattedPhone,
                        'limit' => 10
                    ]);
            } else {
                // Untuk pencarian nama, tetap gunakan parameter phone
                // karena API hanya menyediakan search by phone
                $response = Http::timeout(10)
                    ->withHeaders([
                        'Authorization' => 'Bearer ' . $this->token,
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json'
                    ])
                    ->get("{$this->baseUrl}/leads/search", [
                        'phone' => $query,
                        'limit' => 10
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
     * Update status lead menjadi CUSTOMER atau status lainnya
     */
    public function updateLeadStatus($leadId, $status = 'CUSTOMER')
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->token,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ])
                ->patch("{$this->baseUrl}/leads/{$leadId}/status", [
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
            'name' => $leadData['nama_pelanggan'] ?? $leadData['customer_name'] ?? '',
            'phone' => str_replace('+62', '0', $leadData['no_whatsapp'] ?? $leadData['customer_phone'] ?? ''), // Convert +62 ke 0
            'gender' => 'male', // Default gender karena tidak ada info gender di API baru
            'outlet_id' => $outletId,
            'lead_id' => $leadData['id'],
            'address' => $leadData['alamat'] ?? '',
            'mosque_name' => $leadData['nama_masjid_instansi'] ?? ''
        ];
        
        return $memberData;
    }
    
    /**
     * Check API health status
     */
    public function checkApiHealth()
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->token,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ])
                ->get("{$this->baseUrl}/health");
            
            if ($response->successful()) {
                $data = $response->json();
                return $data['success'] ?? false;
            }
            
            return false;
        } catch (\Exception $e) {
            Log::error('Error checking leads API health: ' . $e->getMessage());
            return false;
        }
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