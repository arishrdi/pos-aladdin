<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LeadsService
{
    private $baseUrl = 'https://leadsaladdin.demowebjalan.com/api/v1';

    // private $token = '1|KNIQhAg8F5D1ckkVpmRKuzQb9yE8jOMQxfJ3h5Exdd558ab6';
    private $token = '2|H6Luf9x39yy3GrooayQK7SiqbMubIQcLtubHE4975cf28edd';
    
    /**
     * Cari leads berdasarkan query (nama atau nomor telepon)
     */
    public function searchLeads($query)
    {
        try {
            // Jika query adalah nomor telepon, format dulu
            if (preg_match('/^[0-9+\-\s()]+$/', $query)) {
                $formattedPhone = $this->formatPhoneForLeads($query);

                Log::info('searchLeads - phone search', [
                    'original_query' => $query,
                    'formatted_phone' => $formattedPhone,
                    'api_url' => "{$this->baseUrl}/leads/search"
                ]);

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
                Log::info('searchLeads - name search', [
                    'query' => $query,
                    'api_url' => "{$this->baseUrl}/leads/search"
                ]);

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

            Log::info('searchLeads - API response', [
                'status' => $response->status(),
                'successful' => $response->successful(),
                'body' => $response->json()
            ]);

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
     * Get lead data by lead ID
     * Menggunakan search by phone dari member data kemudian filter berdasarkan lead_id
     */
    public function getLeadById($leadId, $memberPhone = null)
    {
        try {
            Log::info('getLeadById called', [
                'lead_id' => $leadId,
                'member_phone' => $memberPhone
            ]);

            // Jika ada phone number, cari menggunakan phone
            if ($memberPhone) {
                $leads = $this->searchLeads($memberPhone);

                Log::info('searchLeads result', [
                    'lead_id' => $leadId,
                    'phone' => $memberPhone,
                    'leads_count' => count($leads),
                    'leads_data' => $leads
                ]);

                // Filter untuk mendapatkan lead dengan ID yang sesuai
                foreach ($leads as $lead) {
                    if (isset($lead['id']) && $lead['id'] == $leadId) {
                        Log::info('Lead found by ID', [
                            'lead_id' => $leadId,
                            'lead_data' => $lead
                        ]);
                        return $lead;
                    }
                }

                Log::info('Lead not found - no matching ID', [
                    'lead_id' => $leadId,
                    'searched_phone' => $memberPhone,
                    'leads_found' => count($leads)
                ]);
            }

            // Jika tidak ketemu atau tidak ada phone, return null
            return null;
        } catch (\Exception $e) {
            Log::error('Error getting lead by ID: ' . $e->getMessage());
            return null;
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