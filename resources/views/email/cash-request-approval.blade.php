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
            border-bottom: 3px solid #3498db;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #3498db;
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
            background-color: #e8f5e8;
            border-left: 4px solid #27ae60;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .cash-details {
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
        .proof-section {
            background-color: #f0f8ff;
            border: 1px solid #b3d9ff;
            border-radius: 5px;
            padding: 15px;
            margin: 15px 0;
        }
        .proof-title {
            font-weight: bold;
            color: #0066cc;
            margin-bottom: 8px;
        }
        .proof-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .proof-list li {
            color: #0066cc;
            margin: 5px 0;
            padding: 5px 10px;
            background-color: #e6f3ff;
            border-radius: 3px;
            font-size: 14px;
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
        .urgent {
            color: #3498db;
            font-weight: bold;
        }
        .amount-highlight {
            background-color: #27ae60;
            color: white;
            padding: 2px 8px;
            border-radius: 4px;
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
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>💰 {{ $data['approval_request'] }}</h1>
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
                $cashRequest = $data['approval_data'];
                $isAddCash = $cashRequest->type === 'add';
                $typeIcon = $isAddCash ? '➕' : '➖';
                $typeColor = $isAddCash ? '#27ae60' : '#e74c3c';
            @endphp

            <div class="cash-details">
                <h3 style="margin-top: 0; color: #2c3e50;">{{ $typeIcon }} Detail Permintaan Kas</h3>
                
                <div class="detail-row">
                    <span class="detail-label">Jenis Permintaan:</span>
                    <span class="detail-value" style="color: {{ $typeColor }}">
                        <strong>{{ $cashRequest->type_text }}</strong>
                    </span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Jumlah:</span>
                    <span class="detail-value">
                        <span class="amount-highlight" style="background-color: {{ $typeColor }}">
                            Rp {{ number_format($cashRequest->amount, 0, ',', '.') }}
                        </span>
                    </span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Kasir:</span>
                    <span class="detail-value">{{ $cashRequest->requester->name }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Outlet:</span>
                    <span class="detail-value">{{ $cashRequest->outlet->name }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Waktu Pengajuan:</span>
                    <span class="detail-value">{{ $cashRequest->created_at->format('d/m/Y H:i:s') }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Status:</span>
                    <span class="detail-value">
                        <span class="status-badge status-pending">Menunggu Persetujuan</span>
                    </span>
                </div>
            </div>

            @if($cashRequest->reason)
            <div class="reason-section">
                <div class="reason-title">💬 Alasan Permintaan:</div>
                <div class="reason-text">
                    <strong>{{ $cashRequest->reason }}</strong>
                </div>
            </div>
            @endif

            @if($cashRequest->proof_files && count($cashRequest->proof_files) > 0)
            <div class="proof-section">
                <div class="proof-title">📎 Bukti Pendukung:</div>
                <ul class="proof-list">
                    @foreach($cashRequest->proof_files as $index => $proofFile)
                    <li>📄 Bukti {{ $index + 1 }}: {{ basename($proofFile) }}</li>
                    @endforeach
                </ul>
                <p style="margin: 10px 0 0 0; font-size: 13px; color: #666;">
                    💡 File bukti dapat dilihat pada dashboard admin
                </p>
            </div>
            @endif

            <div class="action-needed">
                <h3>⚡ Tindakan Diperlukan</h3>
                <p>Silakan login ke dashboard supervisor untuk meninjau dan memproses permintaan ini.</p>
                <p><strong>ID Permintaan:</strong> #{{ $cashRequest->id }}</p>
            </div>

            <div style="background-color: #e8f4f8; border-left: 4px solid #3498db; padding: 15px; margin: 20px 0; border-radius: 5px;">
                <p style="margin: 0; color: #2980b9;">
                    <strong>💡 Catatan:</strong> 
                    @if($isAddCash)
                    Persetujuan permintaan ini akan menambahkan kas sebesar <strong>Rp {{ number_format($cashRequest->amount, 0, ',', '.') }}</strong> ke register kasir.
                    @else
                    Persetujuan permintaan ini akan mengurangi kas sebesar <strong>Rp {{ number_format($cashRequest->amount, 0, ',', '.') }}</strong> dari register kasir.
                    @endif
                    Pastikan untuk memverifikasi bukti pendukung sebelum menyetujui.
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