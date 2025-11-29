<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class TransactionHistoryExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = Order::query();

        // Apply filters
        if (isset($this->filters['outlet_id']) && $this->filters['outlet_id'] !== 'all') {
            $query->where('outlet_id', $this->filters['outlet_id']);
        } elseif (isset($this->filters['outlet_id']) && $this->filters['outlet_id'] === 'all') {
            $query->where('outlet_id', '!=', 1);
        }

        if (isset($this->filters['search']) && !empty($this->filters['search'])) {
            $query->where(function ($q) {
                $q->where('order_number', 'like', '%' . $this->filters['search'] . '%');
            });
        }

        if (isset($this->filters['member_id']) && !empty($this->filters['member_id'])) {
            $query->where('member_id', $this->filters['member_id']);
        }

        if (isset($this->filters['date_from']) && isset($this->filters['date_to'])) {
            $query->whereBetween('created_at', [
                $this->filters['date_from'],
                $this->filters['date_to'] . ' 23:59:59'
            ]);
        }

        if (isset($this->filters['status']) && !empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        if (isset($this->filters['approval_status']) && !empty($this->filters['approval_status'])) {
            $query->where('approval_status', $this->filters['approval_status']);
        }

        if (isset($this->filters['payment_method']) && !empty($this->filters['payment_method'])) {
            $query->where('payment_method', $this->filters['payment_method']);
        }

        if (isset($this->filters['transaction_category']) && !empty($this->filters['transaction_category'])) {
            $query->where('transaction_category', $this->filters['transaction_category']);
        }

        if (isset($this->filters['service_type']) && !empty($this->filters['service_type'])) {
            $query->where('service_type', $this->filters['service_type']);
        }

        return $query->with([
            'items.product' => function ($q) {
                $q->withTrashed()->select('id', 'name', 'sku', 'unit', 'unit_type', 'image');
            },
            'outlet:id,name',
            'leadsCabangOutlet:id,name',
            'dealMakerOutlet:id,name',
            'shift:id',
            'user:id,name',
            'member:id,name,member_code,phone,lead_id,address',
            'mosque:id,name,address',
            'approver:id,name',
            'financeApprover:id,name',
            'operationalApprover:id,name',
            'financeRejector:id,name',
            'operationalRejector:id,name',
            'cancellationRequester:id,name',
            'cancellationProcessor:id,name',
            'dpSettlementHistory' => function ($q) {
                $q->with('processedBy:id,name')->orderBy('processed_at', 'asc');
            }
        ])->has('outlet')->has('user')->latest()->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'No',
            'Nomor Invoice',
            'Tanggal Transaksi',
            'Waktu Transaksi',
            'Outlet',
            'Kasir',
            'Shift ID',
            'Member Code',
            'Nama Member',
            'No HP Member',
            'Alamat Member',
            'Lead ID',
            'Nama Masjid',
            'Alamat Masjid',
            'Subtotal',
            'Pajak',
            'Diskon',
            'Total',
            'Total Dibayar',
            'Sisa Saldo',
            'Kembalian',
            'Metode Pembayaran',
            'Status Transaksi',
            'Kategori Transaksi',
            'Tipe Layanan',
            'Status Approval',
            'Status Cancellation',
            'Approved By',
            'Approved At',
            'Approval Notes',
            'Rejection Reason',
            'Finance Approved By',
            'Finance Approved At',
            'Operational Approved By',
            'Operational Approved At',
            'Finance Rejected By',
            'Finance Rejected At',
            'Finance Rejection Reason',
            'Operational Rejected By',
            'Operational Rejected At',
            'Operational Rejection Reason',
            'Cancellation Reason',
            'Cancellation Notes',
            'Cancellation Requested By',
            'Cancellation Requested At',
            'Cancellation Processed By',
            'Cancellation Processed At',
            'Cancellation Admin Notes',
            'Tanggal Pemasangan',
            'Catatan Pemasangan',
            'Leads Cabang Outlet',
            'Deal Maker Outlet',
            'Catatan',
            'Payment Proof URL',
            'Contract PDF URL',
            'Item 1 - Nama',
            'Item 1 - SKU',
            'Item 1 - Qty',
            'Item 1 - Harga',
            'Item 1 - Unit Type',
            'Item 2 - Nama',
            'Item 2 - SKU',
            'Item 2 - Qty',
            'Item 2 - Harga',
            'Item 2 - Unit Type',
            'Item 3 - Nama',
            'Item 3 - SKU',
            'Item 3 - Qty',
            'Item 3 - Harga',
            'Item 3 - Unit Type',
            'Item 4 - Nama',
            'Item 4 - SKU',
            'Item 4 - Qty',
            'Item 4 - Harga',
            'Item 4 - Unit Type',
            'Item 5 - Nama',
            'Item 5 - SKU',
            'Item 5 - Qty',
            'Item 5 - Harga',
            'Item 5 - Unit Type',
            'DP Settlement 1 - Date',
            'DP Settlement 1 - Amount',
            'DP Settlement 1 - Method',
            'DP Settlement 1 - Processed By',
            'DP Settlement 2 - Date',
            'DP Settlement 2 - Amount',
            'DP Settlement 2 - Method',
            'DP Settlement 2 - Processed By',
            'DP Settlement 3 - Date',
            'DP Settlement 3 - Amount',
            'DP Settlement 3 - Method',
            'DP Settlement 3 - Processed By',
        ];
    }

    /**
     * @var Order $order
     */
    public function map($order): array
    {
        static $no = 0;
        $no++;

        $data = [
            $no,
            $order->order_number,
            $order->created_at ? $order->created_at->format('d/m/Y') : '',
            $order->created_at ? $order->created_at->format('H:i:s') : '',
            $order->outlet ? $order->outlet->name : '',
            $order->user ? $order->user->name : '',
            $order->shift_id,
            $order->member ? $order->member->member_code : '',
            $order->member ? $order->member->name : '',
            $order->member ? $order->member->phone : '',
            $order->member ? $order->member->address : '',
            $order->member ? $order->member->lead_id : '',
            $order->mosque ? $order->mosque->name : '',
            $order->mosque ? $order->mosque->address : '',
            $order->subtotal,
            $order->tax,
            $order->discount,
            $order->total,
            $order->total_paid,
            $order->remaining_balance,
            $order->change,
            $this->getPaymentMethodLabel($order->payment_method),
            $this->getStatusLabel($order->status),
            $this->getTransactionCategoryLabel($order->transaction_category),
            $this->getServiceTypeLabel($order->service_type),
            $this->getApprovalStatusLabel($order->approval_status),
            $this->getCancellationStatusLabel($order->cancellation_status),
            $order->approver ? $order->approver->name : '',
            $order->approved_at ? $order->approved_at->format('d/m/Y H:i:s') : '',
            $order->approval_notes,
            $order->rejection_reason,
            $order->financeApprover ? $order->financeApprover->name : '',
            $order->finance_approved_at ? $order->finance_approved_at->format('d/m/Y H:i:s') : '',
            $order->operationalApprover ? $order->operationalApprover->name : '',
            $order->operational_approved_at ? $order->operational_approved_at->format('d/m/Y H:i:s') : '',
            $order->financeRejector ? $order->financeRejector->name : '',
            $order->finance_rejected_at ? $order->finance_rejected_at->format('d/m/Y H:i:s') : '',
            $order->finance_rejection_reason,
            $order->operationalRejector ? $order->operationalRejector->name : '',
            $order->operational_rejected_at ? $order->operational_rejected_at->format('d/m/Y H:i:s') : '',
            $order->operational_rejection_reason,
            $order->cancellation_reason,
            $order->cancellation_notes,
            $order->cancellationRequester ? $order->cancellationRequester->name : '',
            $order->cancellation_requested_at ? $order->cancellation_requested_at->format('d/m/Y H:i:s') : '',
            $order->cancellationProcessor ? $order->cancellationProcessor->name : '',
            $order->cancellation_processed_at ? $order->cancellation_processed_at->format('d/m/Y H:i:s') : '',
            $order->cancellation_admin_notes,
            $order->installation_date ? $order->installation_date->format('d/m/Y') : '',
            $order->installation_notes,
            $order->leadsCabangOutlet ? $order->leadsCabangOutlet->name : '',
            $order->dealMakerOutlet ? $order->dealMakerOutlet->name : '',
            $order->notes,
            $order->payment_proof_url,
            $order->contract_pdf_url,
        ];

        // Add items (up to 5 items)
        for ($i = 0; $i < 5; $i++) {
            if (isset($order->items[$i])) {
                $item = $order->items[$i];
                $data[] = $item->product ? $item->product->name : '';
                $data[] = $item->product ? $item->product->sku : '';
                $data[] = $item->quantity;
                $data[] = $item->price;
                $data[] = $item->product ? $this->getUnitTypeLabel($item->product->unit_type) : '';
            } else {
                $data[] = '';
                $data[] = '';
                $data[] = '';
                $data[] = '';
                $data[] = '';
            }
        }

        // Add DP settlement history (up to 3 settlements)
        for ($i = 0; $i < 3; $i++) {
            if (isset($order->dpSettlementHistory[$i])) {
                $settlement = $order->dpSettlementHistory[$i];
                $data[] = $settlement->processed_at ? $settlement->processed_at->format('d/m/Y H:i:s') : '';
                $data[] = $settlement->amount;
                $data[] = $this->getPaymentMethodLabel($settlement->payment_method);
                $data[] = $settlement->processedBy ? $settlement->processedBy->name : '';
            } else {
                $data[] = '';
                $data[] = '';
                $data[] = '';
                $data[] = '';
            }
        }

        return $data;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as header
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4CAF50'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ],
        ];
    }

    private function getPaymentMethodLabel($method)
    {
        $labels = [
            'cash' => 'Tunai',
            'transfer' => 'Transfer',
            'qris' => 'QRIS',
            'debit' => 'Debit',
            'credit' => 'Kredit',
        ];

        return $labels[$method] ?? $method;
    }

    private function getStatusLabel($status)
    {
        $labels = [
            'pending' => 'Menunggu',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            'rejected' => 'Ditolak',
        ];

        return $labels[$status] ?? $status;
    }

    private function getApprovalStatusLabel($status)
    {
        $labels = [
            'pending' => 'Menunggu',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
        ];

        return $labels[$status] ?? $status;
    }

    private function getCancellationStatusLabel($status)
    {
        $labels = [
            'none' => 'Tidak Ada',
            'requested' => 'Diminta',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
        ];

        return $labels[$status] ?? $status;
    }

    private function getTransactionCategoryLabel($category)
    {
        $labels = [
            'lunas' => 'Lunas',
            'dp' => 'DP',
        ];

        return $labels[$category] ?? $category;
    }

    private function getServiceTypeLabel($type)
    {
        $labels = [
            'install' => 'Pemasangan',
            'pickup' => 'Ambil Sendiri',
            'delivery' => 'Pengiriman',
        ];

        return $labels[$type] ?? $type;
    }

    private function getUnitTypeLabel($type)
    {
        $labels = [
            'kirim' => 'Kirim',
            'pasang' => 'Pasang',
            'buah' => 'Buah',
            'pcs' => 'Pcs',
            'unit' => 'Unit',
        ];

        return $labels[$type] ?? $type;
    }
}
