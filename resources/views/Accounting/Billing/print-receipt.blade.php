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
            font-family: Arial, Helvetica, sans-serif;
            color: var(--text);
            margin: 0;
            background: #fff;
        }

        /* paper A4 print */
        .page {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            padding: 12mm 14mm;
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
            height: 28px;
            /* sesuaikan logo */
        }

        .header-title {
            font-size: 22px;
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
            font-weight: 700;
        }

        .info .muted {
            color: var(--muted);
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
        }

        .footer b {
            font-weight: 800;
        }

        .footer a {
            color: #1a0dab;
            /* biru link seperti contoh */
            text-decoration: underline;
            font-weight: 700;
        }

        @media print {
            .page {
                padding: 0;
            }

            a {
                color: #000;
                text-decoration: none;
            }
        }
    </style>
</head>

<body>

    @php
        // ==== NORMALISASI DATA DARI getIndexData() ====
       $profile = $data['profile'] ?? $pageData['profile'] ?? $result['profile'] ?? null;


        $rupiah = fn($n) => number_format((float) $n, 0, ',', '.');
        $billingDate = isset($profile['date']) ? \Carbon\Carbon::parse($profile['date']) : null;

        $items = [];
        foreach ($profile['invoices'] ?? [] as $invWrap) {
            $inv = $invWrap['invoice'] ?? [];
            foreach ($inv['details'] ?? [] as $d) {
                $qty = (float) ($d['quantity'] ?? 0);
                $unit = (float) ($d['price_net'] ?? ($d['battery_price_retail'] ?? 0));

                $sign = 1;
                if (isset($invWrap['total']) && (float) $invWrap['total'] < 0) {
                    $sign = -1;
                }

                $items[] = [
                    'name' => $d['battery_name'] ?? 'Item',
                    'qty' => $qty,
                    'unit' => $unit * $sign,
                    'total' => $qty * $unit * $sign,
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
                <p><span class="label">Vendor:</span> {{ $vendor['name'] ?? '-' }}</p>
            </div>
        </div>

        {{-- TABLE --}}
        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th class="col-qty">Jumlah</th>
                    <th class="col-unit">Harga Unit</th>
                    <th class="col-total">Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $it)
                    <tr>
                        <td>{{ $it['name'] }}</td>
                        <td class="col-qty nowrap">
                            {{ rtrim(rtrim(number_format($it['qty'], 2, ',', '.'), '0'), ',') }}
                        </td>
                        <td class="col-unit nowrap">
                            {{ $it['unit'] < 0 ? '-' : '' }}{{ $rupiah(abs($it['unit'])) }}
                        </td>
                        <td class="col-total nowrap">
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
                <div>Subtotal:</div>
                <div class="nowrap">{{ $rupiah($subtotal) }}</div>
            </div>
            <div class="summary-line">
                <div>Total Diskon:</div>
                <div class="nowrap">{{ $rupiah($discountPrice) }}</div>
            </div>
            <div class="summary-line summary-total">
                <div>Total:</div>
                <div class="nowrap">{{ $rupiah($total) }}</div>
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

            <div class="footer-center" style="margin-top:4mm;">
                Klik link di bawah untuk<br>
                <a href="https://www.akikita.id/garansi" target="_blank">
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
