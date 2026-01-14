<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Kwitansi - {{ $profile['billing_number'] ?? '-' }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        :root {
            --text: #111;
            --muted: #666;
            --line: #000;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial;
            color: var(--text);
            margin: 0;
            background: #fff;
        }

        /* paper A5 print */
        .page {
            width: 148mm;
            min-height: 210mm;
            margin: 0 auto;
            padding: 10mm 12mm;
        }

        /* ===== HEADER ===== */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 6mm;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 700;
            font-size: 20px;
        }

        .brand img {
            height: 50px;
        }

        .header-title {
            font-size: 40px;
            font-weight: 800;
            letter-spacing: 1px;
        }

        .hr {
            border-top: 2px solid #000;
            margin: 4mm 0 6mm;
        }

        /* ===== INFO 2 KOLOM ===== */
        .info {
            display: flex;
            justify-content: space-between;
            gap: 10mm;
            font-size: 12px;
            margin-bottom: 6mm;
        }

        .info-left {
            width: 60%;
        }

        .info-right {
            width: 40%;
            text-align: left;
        }

        .info .label {
            font-weight: 800;
            font-family: Arial;
        }

        .info .muted {
            font-family: Arial;
        }

        .info p {
            margin: 2px 0;
            line-height: 1.35;
        }

        /* ===== TABLE ===== */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            margin-top: 2mm;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px 6px;
            vertical-align: top;
        }

        th {
            font-weight: 700;
            text-align: left;
            background: #fff;
        }

        .col-qty {
            width: 12%;
            text-align: center;
        }

        .col-unit,
        .col-total {
            width: 18%;
            text-align: right;
        }

        .nowrap {
            white-space: nowrap;
        }

        /* ===== SUMMARY RIGHT ===== */
        .summary-wrap {
            width: 45%;
            margin-left: auto;
            margin-top: 6mm;
            font-size: 12px;
        }

        .summary-line {
            display: flex;
            justify-content: space-between;
            padding: 2px 0;
        }

        .summary-total {
            margin-top: 4px;
            border-top: 1px solid #000;
            padding-top: 4px;
            font-weight: 800;
            font-size: 14px;
        }

        /* ===== FOOTER ===== */
        .footer {
            margin-top: 10mm;
            font-size: 12px;
        }

        .footer-center {
            text-align: center;
            margin-top: 6mm;
            font-family: Arial;
            font-weight: 800;
        }

        .footer-center-custom {
            text-align: center;
            margin-top: 6mm;
            font-family: Arial;
        }

        .footer b {
            font-weight: 800;
        }

        .footer a {
            color: #1a0dab;
            text-decoration: underline;
            font-weight: 700;
        }

        .custom-bg-dark {
            background-color: #323332;
            color: #fff;
        }

        @media print {
            .page {
                padding: 5;
            }

            a {
                color: #000;
                text-decoration: none;
            }

            .custom-bg-dark {
                background-color: #323332 !important;
                color: #fff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>

<body>

    @php
        // ==== NORMALISASI DATA DARI getIndexData() ====
        $profile = $data['profile'] ?? ($pageData['profile'] ?? ($result['profile'] ?? null));

        $rupiah = fn($n) => number_format((float) $n, 0, ',', '.');
        $billingDate = isset($profile['date'])
            ? \Carbon\Carbon::parse($profile['date'])->setTimezone('Asia/Jakarta')
            : null;

        $items = [];
        foreach ($profile['invoices'] ?? [] as $invWrap) {
            $inv = $invWrap['invoice'] ?? [];
            foreach ($inv['details'] ?? [] as $d) {
                if (isset($d['source']) && $d['source'] == 'recycle') {
                    $qty = -1 * (float) ($d['quantity'] ?? 0);
                    $unit = -1 * (float) ($d['price_net'] ?? 0);
                    $total = -1 * (float) ($d['total'] ?? $qty * $unit);
                } else {
                    $qty = (float) ($d['quantity'] ?? 0);
                    $unit = (float) ($d['price_net'] ?? ($d['battery_price_retail'] ?? 0));
                    $total = (float) ($d['total'] ?? $qty * $unit);
                }

                $sign = 1;
                if (isset($invWrap['total']) && (float) $invWrap['total'] < 0) {
                    $sign = -1;
                }

                $items[] = [
                    'name' => $d['battery_name'] ?? 'Item',
                    'qty' => abs($qty),
                    'unit' => $unit * $sign,
                    'total' => $total * $sign,
                ];
            }
        }

        $subtotal = (float) ($profile['subtotal'] ?? 0);
        $discountPrice = (float) ($profile['discount_price'] ?? 0);
        $total = (float) ($profile['total'] ?? 0);

        $vendor = $profile['vendor'] ?? [];
        $shipTo = $profile['ship_to'] ?? [];
    @endphp

    <div class="page">

        {{-- HEADER --}}
        <div class="header">
            <div class="brand">

                <img src="https://akikita.id/img/logo-aki.png" alt="akikita">
            </div>
            <div class="header-title">KWITANSI</div>
        </div>
        <div class="hr"></div>

        {{-- INFO --}}
        <div class="info">
            <div class="info-left">
                <p class="label">
                    {{ $shipTo['name'] ?? '-' }}
                    @if (!empty($shipTo['contact']))
                        (+62 {{ $shipTo['contact'] }})
                    @endif
                </p>
                <p class="muted">{{ $shipTo['address'] ?? '-' }}</p>
            </div>

            <div class="info-right">
                <p><span class="label">Order ID:</span> {{ $profile['billing_number'] ?? '-' }}</p>
                <p><span class="label">Tanggal:</span>
                    {{ $billingDate ? $billingDate->translatedFormat('M d, Y') : '-' }}
                </p>
            </div>
        </div>

        {{-- TABLE --}}
        <table>
            <thead>
                <tr>
                    <th class="custom-bg-dark">Item</th>
                    <th class="custom-bg-dark col-qty">Jumlah</th>
                    <th class="custom-bg-dark col-unit">Harga Unit</th>
                    <th class="custom-bg-dark col-total">Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $it)
                    <tr>
                        <td style="font-family: Arial; font-weight: 800;">{{ $it['name'] }}
                        </td>
                        <td style="font-family: Arial; font-weight: 800; width: 12%;" class="nowrap">
                            {{ rtrim(rtrim(number_format($it['qty'], 2, ',', '.'), '0'), ',') }}
                        </td>
                        <td style="font-family: Arial;" class="col-unit nowrap">
                            {{ $it['unit'] < 0 ? '-' : '' }}{{ $rupiah(abs($it['unit'])) }}
                        </td>
                        <td style="font-family: Arial;" class="col-total nowrap">
                            {{ $it['total'] < 0 ? '-' : '' }}{{ $rupiah(abs($it['total'])) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align:center;color:#666;">Tidak ada item</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- SUMMARY --}}
        <div class="summary-wrap">
            <div class="summary-line">
                <div style="font-family: Arial; font-weight: 800; min-width: 110px; text-align:right;">
                    Subtotal:
                </div>
                <div class="nowrap" style="padding-left:10px;">{{ $rupiah($subtotal) }}</div>
            </div>
            <div class="summary-line">
                <div style="font-family: Arial; font-weight: 800; min-width: 110px; text-align:right;">
                    Total Diskon:
                </div>
                <div class="nowrap" style="padding-left:10px;">{{ $rupiah($discountPrice) }}</div>
            </div>
            <div class="summary-line summary-total">
                <div style="font-family: Arial; font-weight: 800; font-size: 15px; min-width: 110px; text-align:right;">
                    Total:
                </div>
                <div style="font-family: Arial; font-weight: 800; font-size: 15px; padding-left:10px;" class="nowrap">
                    {{ $rupiah($total) }}</div>
            </div>
        </div>

        {{-- FOOTER --}}
        <div class="footer">
            <div class="footer-center">
                Pembayaran bisa dilakukan via transfer ke<br>
                <b>rekening OCBC:</b><br>
                CV SERIAKITA<br>
                060800030110
            </div>

            <div class="footer-center-custom" style="margin-top:4mm;">
                Klik link di bawah untuk<br>
                <a style="font-family: Arial; font-weight: 800;" href="https://www.akikita.id/garansi" target="_blank">
                    Syarat &amp; Ketentuan Garansi Produk
                </a>
            </div>

            <div class="footer-center" style="margin-top:5mm;font-weight:800;">
                Terimakasih atas pembelian Anda!
            </div>

            <div class="footer-center" style="margin-top:3mm;">
                0822-2880-0175 &nbsp;&nbsp;
                <a href="https://www.akikita.id" target="_blank">www.akikita.id</a>
            </div>
        </div>

    </div>

    <script>
        // window.onload = () => {
        //     window.print();
        // };
    </script>

</body>

</html>
