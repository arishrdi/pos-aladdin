<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $data['approval_request'] }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .email-container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #e74c3c;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #e74c3c;
            margin: 0;
            font-size: 24px;
        }
        .header p {
            color: #666;
            margin: 5px 0 0 0;
            font-size: 14px;
        }
        .content {
            margin-bottom: 30px;
        }
        .greeting {
            font-size: 16px;
            margin-bottom: 20px;
        }
        .request-info {
            background-color: #fef9e7;
            border-left: 4px solid #f39c12;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .order-details {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        .detail-row:last-child {
            border-bottom: none;
            font-weight: bold;
            font-size: 16px;
        }
        .detail-label {
            font-weight: 600;
            color: #555;
        }
        .detail-value {
            color: #333;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            background: white;
        }
        .items-table th {
            background-color: #34495e;
            color: white;
            padding: 12px 8px;
            text-align: left;
            font-size: 14px;
        }
        .items-table td {
            padding: 10px 8px;
            border-bottom: 1px solid #ddd;
            font-size: 13px;
        }
        .items-table tr:hover {
            background-color: #f5f5f5;
        }
        .reason-section {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 5px;
            padding: 15px;
            margin: 15px 0;
        }
        .reason-title {
            font-weight: bold;
            color: #856404;
            margin-bottom: 8px;
        }
        .reason-text {
            color: #856404;
        }
        .action-needed {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            border-radius: 5px;
            padding: 15px;
            margin: 20px 0;
            text-align: center;
        }
        .action-needed h3 {
            color: #155724;
            margin: 0 0 10px 0;
        }
        .action-needed p {
            color: #155724;
            margin: 0;
        }
        .footer {
            border-top: 1px solid #eee;
            padding-top: 20px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        .status-completed {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .urgent {
            color: #e74c3c;
            font-weight: bold;
        }
        @media (max-width: 600px) {
            body {
                padding: 10px;
            }
            .email-container {
                padding: 20px;
            }
            .detail-row {
                flex-direction: column;
                align-items: flex-start;
            }
            .items-table {
                font-size: 11px;
            }
            .items-table th,
            .items-table td {
                padding: 6px 4px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>🔔 {{ $data['approval_request'] }}</h1>
            <p>Sistem Manajemen Point of Sale - Aladdin</p>
        </div>

        <div class="content">
            <div class="greeting">
                <p>Halo <strong>{{ $data['supervisor_name'] }}</strong>,</p>
            </div>

            <div class="request-info">
                <p><strong>{{ $data['cashier_name'] }}</strong> mengajukan permintaan <strong class="urgent">{{ strtolower($data['approval_request']) }}</strong> yang memerlukan persetujuan Anda.</p>
            </div>

            @php
                $order = $data['approval_data'];
                $requestType = $order->status === 'pending' ? 'Pembatalan' : 'Refund';
            @endphp

            <div class="order-details">
                <h3 style="margin-top: 0; color: #2c3e50;">📋 Detail Transaksi</h3>
                
                <div class="detail-row">
                    <span class="detail-label">No. Invoice:</span>
                    <span class="detail-value"><strong>{{ $order->order_number }}</strong></span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Tanggal Transaksi:</span>
                    <span class="detail-value">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Kasir:</span>
                    <span class="detail-value">{{ $order->user->name }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Outlet:</span>
                    <span class="detail-value">{{ $order->outlet->name }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Status Transaksi:</span>
                    <span class="detail-value">
                        <span class="status-badge {{ $order->status === 'completed' ? 'status-completed' : 'status-pending' }}">
                            {{ $order->status === 'completed' ? 'Selesai' : 'Pending' }}
                        </span>
                    </span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Metode Pembayaran:</span>
                    <span class="detail-value">{{ strtoupper($order->payment_method) }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Jenis Permintaan:</span>
                    <span class="detail-value"><strong class="urgent">{{ $requestType }}</strong></span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Total Transaksi:</span>
                    <span class="detail-value"><strong>Rp {{ number_format($order->total, 0, ',', '.') }}</strong></span>
                </div>
            </div>

            @if($order->items && $order->items->count() > 0)
            <div class="order-details">
                <h3 style="margin-top: 0; color: #2c3e50;">🛍️ Item Transaksi</h3>
                <table class="items-table">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th style="text-align: center;">Qty</th>
                            <th style="text-align: right;">Harga</th>
                            <th style="text-align: right;">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                        <tr>
                            <td>{{ $item->product->name ?? 'Produk tidak tersedia' }}</td>
                            <td style="text-align: center;">{{ $item->quantity }}</td>
                            <td style="text-align: right;">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                            <td style="text-align: right;">Rp {{ number_format($item->quantity * $item->price, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif

            <div class="reason-section">
                <div class="reason-title">💬 Alasan {{ $requestType }}:</div>
                <div class="reason-text">
                    <strong>{{ $order->cancellation_reason ?? 'Tidak ada alasan yang diberikan' }}</strong>
                </div>
                @if($order->cancellation_notes)
                <div style="margin-top: 10px;">
                    <div class="reason-title">📝 Keterangan Tambahan:</div>
                    <div class="reason-text">{{ $order->cancellation_notes }}</div>
                </div>
                @endif
            </div>

            <div class="action-needed">
                <h3>⚡ Tindakan Diperlukan</h3>
                <p>Silakan login ke dashboard supervisor untuk meninjau dan memproses permintaan ini.</p>
                <p><strong>Waktu pengajuan:</strong> {{ $order->cancellation_requested_at ? $order->cancellation_requested_at->format('d/m/Y H:i') : now()->format('d/m/Y H:i') }}</p>
            </div>

            <div style="background-color: #e8f4f8; border-left: 4px solid #3498db; padding: 15px; margin: 20px 0; border-radius: 5px;">
                <p style="margin: 0; color: #2980b9;">
                    <strong>💡 Catatan:</strong> 
                    @if($requestType === 'Pembatalan')
                    Pembatalan transaksi pending akan mengembalikan stok produk ke inventory tanpa mempengaruhi kas register.
                    @else
                    Refund transaksi completed akan mengembalikan stok produk dan menyesuaikan saldo kas register.
                    @endif
                </p>
            </div>
        </div>

        <div class="footer">
            <p>Email ini digenerate otomatis oleh sistem POS Aladdin.</p>
            <p>Jika Anda memiliki pertanyaan, silakan hubungi administrator sistem.</p>
            <p style="margin-top: 15px; font-size: 11px;">
                © {{ date('Y') }} IT Solution - Semua hak dilindungi
            </p>
        </div>
    </div>
</body>
</html>