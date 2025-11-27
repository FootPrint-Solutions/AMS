@php
    $current_date = isset($data['profile']['date'])
        ? date('d F Y', strtotime($data['profile']['date']))
        : date('d F Y');
    $billing_number = isset($data['profile']['billing_number']) ? $data['profile']['billing_number'] : 'N/A';
    $vendor = isset($data['profile']['vendor']) ? $data['profile']['vendor'] : null;
    $ship_to = isset($data['profile']['ship_to']) ? $data['profile']['ship_to'] : null;
    $total = isset($data['profile']['total']) ? $data['profile']['total'] : 0;
@endphp

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kwitansi - {{ $billing_number }}</title>
    <style type="text/css">
        @media print {
            @page {
                size: A5 landscape;
                margin: 10mm;
            }

            body {
                margin: 0;
                padding: 0;
            }

            .no-print {
                display: none;
            }
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            padding: 15px;
            background: #fff;
        }

        .receipt-wrapper {
            max-width: 190mm;
            margin: 0 auto;
        }

        .receipt-container {
            border: 2.5px solid #000;
            padding: 15px 20px;
        }

        .header-box {
            border: 2px solid #000;
            padding: 10px 15px;
            margin-bottom: 15px;
            text-align: center;
        }

        .company-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 3px;
        }

        .company-info {
            font-size: 9px;
            line-height: 1.3;
        }

        .title-section {
            text-align: center;
            margin: 15px 0;
        }

        .receipt-title {
            font-size: 20px;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 5px;
        }

        .receipt-no {
            font-size: 11px;
        }

        .divider {
            border-bottom: 1px solid #000;
            margin: 12px 0;
        }

        table {
            width: 100%;
            font-size: 11px;
        }

        table tr td {
            padding: 4px 0;
            vertical-align: top;
        }

        table tr td:first-child {
            width: 140px;
            font-weight: 600;
        }

        table tr td:nth-child(2) {
            width: 15px;
            text-align: center;
        }

        .amount-box {
            border: 2px solid #000;
            padding: 12px;
            margin: 15px 0;
            background: #f8f8f8;
            text-align: center;
        }

        .amount-label {
            font-size: 11px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .amount-value {
            font-size: 18px;
            font-weight: bold;
        }

        .terbilang-box {
            border: 1.5px solid #000;
            padding: 10px;
            margin: 12px 0;
            font-size: 10px;
            font-style: italic;
        }

        .signature-area {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
            font-size: 11px;
        }

        .signature-box {
            width: 45%;
            text-align: center;
        }

        .signature-title {
            margin-bottom: 50px;
            font-weight: 600;
        }

        .signature-line {
            border-top: 1.5px solid #000;
            padding-top: 5px;
            font-weight: bold;
        }

        .no-print {
            text-align: center;
            margin: 15px 0;
        }

        .btn {
            padding: 8px 20px;
            margin: 0 5px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 13px;
            text-decoration: none;
            display: inline-block;
        }

        .btn-print {
            background: #007bff;
            color: white;
        }

        .btn-back {
            background: #6c757d;
            color: white;
        }
    </style>
</head>

<body>
    <div class="no-print">
        <button class="btn btn-print" onclick="window.print()">🖨️ Cetak</button>
        <a href="{{ route('billing.index') }}" class="btn btn-back">← Kembali</a>
    </div>

    <div class="receipt-wrapper">
        <div class="receipt-container">
            <!-- Header dengan border -->
            <div class="header-box">
                <div class="company-name">CV. SERIAKITA</div>
                <div class="company-info">
                    Green Sedayu Bizpark DM5 Nomor 58, Kalideres, Provinsi DKI Jakarta<br>
                    TEL: (+62) 081288279143 | EMAIL: management@akikita.id
                </div>
            </div>

            <!-- Judul -->
            <div class="title-section">
                <div class="receipt-title">KWITANSI</div>
                <div class="receipt-no">No: {{ $billing_number }}</div>
            </div>

            <div class="divider"></div>

            <!-- Informasi Penerima -->
            <table>
                <tr>
                    <td>Sudah terima dari</td>
                    <td>:</td>
                    <td><strong>{{ $vendor ? $vendor['name'] : '-' }}</strong></td>
                </tr>
                <tr>
                    <td>Alamat</td>
                    <td>:</td>
                    <td>{{ $vendor && isset($vendor['address']) ? $vendor['address'] : '-' }}</td>
                </tr>
                <tr>
                    <td>Telepon</td>
                    <td>:</td>
                    <td>{{ $vendor && isset($vendor['phone']) ? $vendor['phone'] : '-' }}</td>
                </tr>
                <tr>
                    <td>Tanggal</td>
                    <td>:</td>
                    <td><strong>{{ $current_date }}</strong></td>
                </tr>
            </table>

            <div class="divider"></div>

            <!-- Jumlah Uang -->
            <div class="amount-box">
                <div class="amount-label">UANG SEJUMLAH</div>
                <div class="amount-value">Rp {{ number_format($total, 0, ',', '.') }}</div>
            </div>

            <!-- Terbilang -->
            <div class="terbilang-box">
                <strong>Terbilang:</strong> <span style="text-transform: capitalize;">{{ terbilang($total) }}
                    rupiah</span>
            </div>

            <!-- Keperluan -->
            <table>
                <tr>
                    <td>Untuk pembayaran</td>
                    <td>:</td>
                    <td>
                        <strong>
                            @if (isset($data['profile']['invoices']) && count($data['profile']['invoices']) > 0)
                                @foreach ($data['profile']['invoices'] as $invoice)
                                    {{ $invoice['order_number'] ?? 'N/A' }}{{ !$loop->last ? ', ' : '' }}
                                @endforeach
                            @else
                                Billing {{ $billing_number }}
                            @endif
                        </strong>
                    </td>
                </tr>
            </table>

            <!-- Tanda Tangan -->
            <div class="signature-area">
                <div class="signature-box">
                    <div class="signature-title">Yang Menerima,</div>
                    <div class="signature-line">{{ $vendor ? $vendor['name'] : '________________' }}</div>
                </div>
                <div class="signature-box">
                    <div class="signature-title">Yang Menyerahkan,</div>
                    <div class="signature-line">CV. SERIAKITA</div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
