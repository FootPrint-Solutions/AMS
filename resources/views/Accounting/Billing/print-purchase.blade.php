@php
    $profile = $data['profile'];
    $vendor = $profile['vendor'];
    $ship_to = $profile['ship_to'];
    $current_date = \Carbon\Carbon::parse($profile['date'])->setTimezone('Asia/Jakarta')->format('d/m/Y');
    $invoices = $profile['invoices'] ?? [];

    // Gabungkan semua details dari semua invoice
    $all_details = [];
    foreach ($invoices as $invoice) {
        if (isset($invoice['invoice']['details'])) {
            $all_details = array_merge($all_details, $invoice['invoice']['details']);
        }
    }
    $total = 0;
@endphp

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
                                (+62)
                                081288279143</td>
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
                                        <th class="header text" width="53%">Nama Produk</th>
                                        <th class="header text" width="5%">Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $grouped = [];
                                        foreach ($all_details as $item) {
                                            if (($item['battery']['type'] ?? 'regular') != 'regular') {
                                                continue;
                                            }
                                            $batteryId = $item['battery']['id'] ?? null;
                                            if ($batteryId === null) {
                                                continue;
                                            }
                                            if (!isset($grouped[$batteryId])) {
                                                $grouped[$batteryId] = [
                                                    'battery_name' => $item['battery_name'],
                                                    'quantity' => 0,
                                                ];
                                            }
                                            $grouped[$batteryId]['quantity'] += $item['quantity'];
                                        }
                                        $grouped = array_values($grouped);
                                    @endphp
                                    @forelse ($grouped as $index => $item)
                                        <tr>
                                            <td class="text">{{ $index + 1 }}</td>
                                            <td class="text">{{ $item['battery_name'] }}</td>
                                            <td class="text">{{ $item['quantity'] }}</td>
                                        </tr>
                                        @php $total += $item['quantity']; @endphp
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text center">Tidak ada data baterai.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="header text"
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
                                                        <th class=" text" width="73%"></th>
                                                        <th class=" text" width="17%"
                                                            style="border: 0.5px solid black; text-align:left;">
                                                            Total</th>
                                                        <th class=" text" width="25%"
                                                            style="border: 0.5px solid black; text-align:left;"">
                                                            {{ $total }}
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
                        • Harap kirimkan invoice yang sesuai dengan spesifikasi pesanan ini ke email
                        perusahaan.
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
                        • Harap untuk segera berkabar bila terjadi kendala dalam pemenuhan order.
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
                        <div class=""><strong>Pemohon, <br>Direktur</strong></div>
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
                            <td colspan="2" class="text" style="padding: 0px;">Green Sedayu Bizpark DM5
                                Nomor
                                58,
                            </td>
                            <td class="text" colspan="2" style="padding: 0px;">Tanggal</td>
                            <td class="text" colspan="3" style="padding: 0px;">{{ $current_date }}</td>
                        </tr>
                        <tr>
                            <td colspan="2" class="text" style="padding: 0px;">Kalideres, Provinsi DKI
                                Jakarta
                            </td>
                            <td class="text" colspan="2" style="padding: 0px;">Billing No</td>
                            <td class="text" colspan="3" style="padding: 0px;">
                                {{ $data['profile']['billing_number'] }}</td>
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
                            <td class="judul" width="50%">Daftar Kode Produksi</td>
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
                                        <th class="header text" width="50%" style="text-align:left;">Nama
                                            Produk
                                        </th>
                                        <th class="header text" style="text-align:left;">Kode Produksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($all_details as $item)
                                        <tr>
                                            <td class="text">
                                                {{ isset($item['battery_sales_order']) ? $item['battery_sales_order']['battery_name'] : $item['battery_name'] }}
                                            </td>
                                            <td class="text">{{ $item['battery_production_code'] }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="text center">Tidak ada data baterai.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="header text"
                                            style="background-color: #323332; color: white;">&nbsp;</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</body>
