@php
    $current_date = isset($data['profile']['date'])
        ? date('m/d/Y', strtotime($data['profile']['date']))
        : date('m/d/Y');
    $billing_number = isset($data['profile']['billing_number']) ? $data['profile']['billing_number'] : 'N/A';
@endphp

{{-- @dd($data) --}}

<style type="text/css">
    @media print {
        @page {
            size: A4 portrait;
        }

        .header {
            background-color: #323332 !important;
            color: white !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
    }

    html,
    body {
        padding: 15px;
    }

    body {
        display: flex;
        flex-direction: column;
        width: 210mm;
        height: auto;
        margin: 0 auto;
    }

    .content {
        flex: 1;
    }

    .header {
        border: 0.5px solid black;
        background-color: #323332;
        color: white;
    }

    .footer {
        padding: 3px;
    }

    .right {
        float: right;
        padding-right: 15px;
        /* Add padding to the right */
    }

    .center {
        text-align: center;
    }

    .judul {
        font-size: 12pt;
        font-family: Arial, sans-serif;
        font-weight: bold;
    }

    .judul2 {
        font-size: 12pt;
        font-family: Arial, sans-serif;
        font-weight: bold;
    }

    .text {
        font-size: 11pt;
        font-family: Arial, sans-serif;
        padding: 4px;
    }

    .judulAlamat {
        font-size: 9pt;
        font-family: Arial, sans-serif;
        padding: 0px;
    }

    .pagebreaking {
        page-break-inside: avoid;
        page-break-after: auto
    }

    * {
        font-family: Arial, sans-serif;
    }
</style>

<body>
    <div id="printini" class="content">
        <div id="header0">
            <div class="pagebreaking">
                <div style="height:10px;"></div>
                <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
                    <tbody>
                        <tr>
                            <td colspan="2" class="judul">CV. SERIAKITA</td>
                            <td colspan="5" class="judul2">Invoice Billing</td>
                        </tr>
                        <tr>
                            <td colspan="2" class="text"></td>
                            <td colspan="5" class="text"></td>
                        </tr>
                        <tr>
                            <td colspan="2" class="text" style="padding: 0px;">Green Sedayu Bizpark DM5 Nomor 58,
                            </td>
                            <td class="text" colspan="2" style="padding: 0px;">Tanggal</td>
                            <td class="text" colspan="3" style="padding: 0px;">{{ $current_date }}</td>
                        </tr>
                        <tr>
                            <td colspan="2" class="text" style="padding: 0px;">Kalideres, Provinsi DKI Jakarta</td>
                            <td class="text" colspan="2" style="padding: 0px;">Billing No</td>
                            <td class="text" colspan="3" style="padding: 0px;">{{ $billing_number }}</td>
                        </tr>
                        <tr>
                            <td colspan="7" class="text" style="padding: 0px;"><span class="judul">TEL</span>:
                                (+62) 081288279143</td>
                        </tr>
                        <tr>
                            <td colspan="7" class="text" style="padding: 0px;"><span class="judul">EMAIL</span>:
                                management@akikita.id
                            </td>
                        </tr>
                    </tbody>
                </table>
                <br><br>
                <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
                    <tbody>
                        <tr>
                            <td class="judul" width="50%">Vendor</td>
                            <td class="judul">Ship To</td>
                        </tr>
                        <tr>
                            <td class="judul">
                                {{ isset($data['profile']['vendor']) ? $data['profile']['vendor']['name'] : 'N/A' }}
                            </td>
                            <td class="judul">
                                {{ isset($data['profile']['ship_to']) ? $data['profile']['ship_to']['name'] : 'N/A' }}
                            </td>
                        </tr>
                        <tr>
                            <td class="text" style="padding: 0px;">
                                {{ isset($data['profile']['vendor']) ? $data['profile']['vendor']['address'] : '' }}
                            </td>
                            <td class="text" rowspan="3" style="padding:0px;">
                                {{ isset($data['profile']['ship_to']) ? $data['profile']['ship_to']['address'] : '' }}
                            </td>
                        </tr>
                        <tr>
                            <td class="text" style="padding: 0px;">
                                {{ isset($data['profile']['vendor']) && array_key_exists('phone', $data['profile']['vendor']) ? $data['profile']['vendor']['phone'] : '' }}
                            </td>
                        </tr>
                        <tr>
                            <td class="text" style="padding: 0px;">
                                {{ isset($data['profile']['vendor']) ? $data['profile']['vendor']['email'] : '' }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <br>
        <div id="detail0">
            <table border="0" height="100px;" width="100%;" cellpadding="0" cellspacing="0">
                <tbody>
                    <tr valign="top" width="100%;">
                        <td width="100%;" colspan="7">
                            <table border="1" width="100%;" cellpadding="0" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th class="header text" width="55%">Catatan Tambahan</th>
                                        <th class="header text" width="15%" style="text-align:left;">Jadwal
                                            Pengiriman</th>
                                        <th class="header text" width="25%">Pemohon</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text"></td>
                                        <td class="text">{{ $current_date }}</td>
                                        <td class="text center">Enzo Tjandra</td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </tbody>
            </table>
            <table border="0" height="" width="100%;" cellpadding="0" cellspacing="0">
                <tbody>
                    <tr valign="top" width="100%;">
                        <td width="100%;" colspan="7">
                            <table border="1" width="100%;" cellpadding="0" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th class="header text" width="5%">No.</th>
                                        <th class="header text" width="30%">Invoice Number</th>
                                        <th class="header text" width="20%">Subtotal</th>
                                        <th class="header text" width="10%">Qty</th>
                                        <th class="header text" width="15%">Discount</th>
                                        <th class="header text" width="20%">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $grandTotal = 0;
                                    @endphp
                                    @if (!empty($data['profile']['invoices']))
                                        @foreach ($data['profile']['invoices'] as $index => $invoice)
                                            @php
                                                $qty = 0;
                                                if (!empty($invoice['invoice']['details'])) {
                                                    foreach ($invoice['invoice']['details'] as $detail) {
                                                        $qty += $detail['quantity'] ?? 0;
                                                    }
                                                }
                                            @endphp
                                            <tr>
                                                <td class="text">
                                                    {{ $index + 1 }}
                                                </td>
                                                <td class="text">
                                                    {{ $invoice['invoice_number'] ?? 'N/A' }}
                                                </td>
                                                <td class="text">
                                                    Rp {{ number_format($invoice['subtotal'] ?? 0, 0, ',', '.') }}
                                                </td>
                                                <td class="text">
                                                    {{ number_format($qty, 0, ',', '.') }}
                                                </td>
                                                <td class="text">
                                                    Rp
                                                    {{ number_format($invoice['discount_price'] ?? 0, 0, ',', '.') }}
                                                </td>
                                                <td class="text">
                                                    Rp {{ number_format($invoice['total'] ?? 0, 0, ',', '.') }}
                                                </td>
                                            </tr>
                                            @php
                                                $grandTotal += $invoice['total'] ?? 0;
                                            @endphp
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="7" class="text center">Tidak ada data invoice.</td>
                                        </tr>
                                    @endif
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="6" class="header text"
                                            style="background-color: #323332; color: white;">&nbsp;</td>
                                    </tr>
                                </tfoot>
                            </table>
                            <table border="0" height="100px;" width="100%;" cellpadding="0" cellspacing="0">
                                <tbody>
                                    <tr valign="top" width="100%;">
                                        <td width="100%;" colspan="7">
                                            <table border="0" width="100%;" cellpadding="0" cellspacing="0">
                                                <thead>
                                                    <tr>
                                                        <th class=" text" width="45%"></th>
                                                        <th class=" text" width="17%"
                                                            style="border: 0.5px solid black; text-align:left;">
                                                            Subtotal</th>
                                                        <th class=" text" width="20%"
                                                            style="border: 0.5px solid black; text-align:left;">
                                                            Rp
                                                            {{ number_format($data['profile']['subtotal'] ?? 0, 0, ',', '.') }}
                                                        </th>
                                                    </tr>
                                                    <tr>
                                                        <th class=" text" width="45%"></th>
                                                        <th class=" text" width="17%"
                                                            style="border: 0.5px solid black; text-align:left;">
                                                            Discount</th>
                                                        <th class=" text" width="20%"
                                                            style="border: 0.5px solid black; text-align:left;">
                                                            Rp
                                                            {{ number_format($data['profile']['discount_price'] ?? 0, 0, ',', '.') }}
                                                        </th>
                                                    </tr>
                                                    <tr>
                                                        <th class=" text" width="45%"></th>
                                                        <th class=" text" width="17%"
                                                            style="border: 0.5px solid black; text-align:left;">
                                                            Grand Total</th>
                                                        <th class=" text" width="20%"
                                                            style="border: 0.5px solid black; text-align:left;">
                                                            Rp
                                                            {{ number_format($data['profile']['total'] ?? 0, 0, ',', '.') }}
                                                        </th>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div id=" footer0" class="footer">
        <table width="100%">
            <tbody>
                <tr>
                    <td class="footer" colspan="7"></td>
                </tr>
                <tr>
                    <td class="text" colspan="2"><strong>Syarat & Ketentuan</strong></td>
                    <td class="text" colspan="3">
                        <div class="right"></div>
                    </td>
                    <td class="text" colspan="2">
                        <div class="right"></div>
                    </td>
                </tr>
                <tr>
                    <td class="text" colspan="4" style="padding:0px;">
                        • Invoice ini merupakan dokumen resmi untuk penagihan.
                    </td>
                    <td class="text">
                        <div class="right"></div>
                    </td>
                    <td class="text" colspan="2">
                        <div class="right"></div>
                    </td>
                </tr>
                <tr>
                    <td class="text" colspan="4" style="padding:0px;">
                        • Pembayaran harus dilakukan sesuai dengan termin yang telah disepakati.
                    </td>
                    <td class="text">
                        <div class="right"></div>
                    </td>
                    <td class="text" colspan="2">
                        <div class="right"></div>
                    </td>
                </tr>
                <tr>
                    <td class="text" colspan="4" style="padding:0px;">

                    </td>
                    <td class="text">
                        <div class="right"></div>
                    </td>
                    <td class="text" colspan="2">
                        <div class="right"></div>
                    </td>
                </tr>
                <tr>
                    <td class="text" colspan="4">
                        <div class=""><strong>Disetujui Oleh, <br>Direktur</strong></div>
                    </td>

                </tr>
                <tr>
                    <td class="text" colspan="4"></td>
                    <td class="text">
                        <div class="right"></div>
                    </td>
                    <td class="text" colspan="2">
                        <div class="right"></div>
                    </td>
                </tr>
                <tr>
                    <td class="text" colspan="2" style="padding:0px;">
                        <div class="">
                            <img src="{{ asset('/img/purchase-order/signature.jpg') }}" alt=""
                                style="width: 100px;"><br>
                            <strong>Enzo Tjandra</strong>
                        </div>
                        <div class="">_______________</div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- page break --}}
    <div style="page-break-before: always;"></div>
    {{-- end page break --}}

    <br><br>
    <div id="printini" class="content">
        <div id="header0">
            <div class="pagebreaking">
                <div style="height:10px;"></div>
                <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
                    <tbody>
                        <tr>
                            <td colspan="2" class="judul">CV. SERIAKITA</td>
                            <td colspan="5" class="judul2">Invoice Billing</td>
                        </tr>
                        <tr>
                            <td colspan="2" class="text"></td>
                            <td colspan="5" class="text"></td>
                        </tr>
                        <tr>
                            <td colspan="2" class="text" style="padding: 0px;">Green Sedayu Bizpark DM5 Nomor
                                58,
                            </td>
                            <td class="text" colspan="2" style="padding: 0px;">Tanggal</td>
                            <td class="text" colspan="3" style="padding: 0px;">{{ $current_date }}</td>
                        </tr>
                        <tr>
                            <td colspan="2" class="text" style="padding: 0px;">Kalideres, Provinsi DKI Jakarta
                            </td>
                            <td class="text" colspan="2" style="padding: 0px;">Billing No</td>
                            <td class="text" colspan="3" style="padding: 0px;">{{ $profile['billing_number'] }}
                            </td>
                        </tr>
                        <tr>
                            <td colspan="7" class="text" style="padding: 0px;"><span class="judul">TEL</span>:
                                (+62) 081288279143</td>
                        </tr>
                        <tr>
                            <td colspan="7" class="text" style="padding: 0px;"><span
                                    class="judul">EMAIL</span>:
                                management@akikita.id
                            </td>
                        </tr>
                    </tbody>
                </table>
                <br><br>
                <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
                    <tbody>
                        <tr>
                            <td class="judul" width="50%">Detail Produk</td>
                            <td class="judul"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div id="detail0">
            <table border="0" height="" width="100%;" cellpadding="0" cellspacing="0">
                <tbody>
                    <tr valign="top" width="100%;">
                        <td width="100%;" colspan="7">
                            <table border="1" width="100%;" cellpadding="0" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th class="header text" width="5%" style="text-align:left;">No.</th>
                                        <th class="header text" width="35%" style="text-align:left;">Nama Produk
                                        </th>
                                        <th class="header text" width="10%" style="text-align:left;">Qty</th>
                                        <th class="header text" width="15%" style="text-align:left;">Harga</th>
                                        <th class="header text" width="20%" style="text-align:left;">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $productNo = 1;
                                        $grandTotalProducts = 0;
                                    @endphp
                                    @if (!empty($data['profile']['invoices']))
                                        @foreach ($data['profile']['invoices'] as $invoice)
                                            @if (!empty($invoice['invoice']['details']))
                                                @foreach ($invoice['invoice']['details'] as $detail)
                                                    @php
                                                        $qty = $detail['quantity'] ?? 0;
                                                        $price = $detail['battery_price_retail'] ?? 0;
                                                        $total = $qty * $price;

                                                        $totalInvoice = $invoice['total'] ?? 0;
                                                        if (($totalInvoice ?? 0) < 0) {
                                                            $qty = $qty;
                                                            $price = -abs($price);
                                                            $total = -abs($total);
                                                        }
                                                    @endphp
                                                    <tr>
                                                        <td class="text">{{ $productNo++ }}</td>
                                                        <td class="text">{{ $detail['battery_name'] ?? 'N/A' }}</td>
                                                        <td class="text">
                                                            {{ number_format($qty, 0, ',', '.') }}
                                                        </td>
                                                        <td class="text">Rp
                                                            {{ number_format($price, 0, ',', '.') }}
                                                        </td>
                                                        <td class="text">Rp
                                                            {{ number_format($total, 0, ',', '.') }}
                                                        </td>
                                                    </tr>
                                                    @php
                                                        $grandTotalProducts += $total;
                                                    @endphp
                                                @endforeach
                                            @endif
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="6" class="text center">Tidak ada data produk.</td>
                                        </tr>
                                    @endif
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="6" class="header text"
                                            style="background-color: #323332; color: white;">&nbsp;</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        @if (Str::startsWith($billing_number, 'SB'))
            <br>
            <div style="text-align:center; font-size:18px; font-weight:bold; margin-top:20px;">
                Pembayaran bisa dilakukan via transfer ke rekening OCBC:<br>
                <span style="font-size:20px;">CV SERIAKITA</span><br>
                <span style="font-size:20px;">060800030110</span><br><br>
                Klik link di bawah untuk<br>
                <a href="https://akikita.id/syarat-garansi" target="_blank"
                    style="color:blue; text-decoration:underline;">
                    Syarat & Ketentuan Garansi Produk
                </a>
                <br><br>
                Terimakasih atas pembelian Anda!<br><br>
                <span style="font-size:16px;">
                    <img src="https://img.icons8.com/ios-filled/20/000000/whatsapp.png"
                        style="vertical-align:middle;" /> 082228800175
                </span>
                &nbsp;&nbsp;
                <a href="https://www.akikita.id" target="_blank">www.akikita.id</a>
            </div>
        @endif
    </div>
</body>

<script>
    window.onload = () => {
        window.print();
    };
</script>
