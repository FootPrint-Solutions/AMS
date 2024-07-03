<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Work Order_{{ $workOrder->salesOrder->sales_order_number }}_AMS</title>
    <style>
        hr.dashed {
            border: none;
            border-top: 2px dashed #000;
            color: #000;
            background-color: #f4f4f4;
            height: 1px;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            background-color: #f4f4f4;
        }

        .container {
            width: 100%;
            max-width: 147mm;
            /* Set max-width to A5 width */
            height: 209mm;
            /* Set height to A5 height */
            margin: 0 auto;
            padding: 10px;
            border: 1px solid #000;
            box-sizing: border-box;
            background-color: #fff;
            overflow: hidden;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
        }

        .header h1 {
            margin: 0;
            font-size: 18px;
        }

        .header h2 {
            margin: 0;
            font-size: 14px;
            font-weight: normal;
        }

        .section {
            margin-bottom: 10px;
        }

        .section h3 {
            margin-bottom: 5px;
            font-size: 14px;
        }

        .section table {
            width: 100%;
            border-collapse: collapse;
        }

        .section table,
        .section th,
        .section td {
            border: 1px solid #000;
        }

        .section th,
        .section td {
            /* padding: 4px; */
            text-align: left;
        }

        * {
            box-sizing: border-box;
        }

        .column {
            float: left;
            width: 50%;
        }

        .row:after {
            content: "";
            display: table;
            clear: both;
        }

        @media print {
            @page {
                size: A5;
                margin: 0;
            }

            body {
                margin: 0;
            }

            .container {
                border: none;
                width: 100%;
                height: 100%;
            }
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="section">
            <div style='display:flex; justify-content:space-between; align-items:center; margin-bottom:-15px;'>
                <h2>{{ $workOrder->work_order_number }}</h2>
                <h2>Instruksi Kerja Teknisi</h2>
            </div>
            <table>
                <tr>
                    <td style='width:60%;'>Nama Partner : {{ $workOrder->salesOrder->distributorShop->name ?? '' }}</td>
                    <td>Order ID : {{ $workOrder->salesOrder->sales_order_number }}</td>
                </tr>
                <tr>
                    <td style='width:60%;'>Admin Akikita : {{ auth()->user()->name }}</td>
                    <td>Tanggal : {{ date('d-m-Y', strtotime($workOrder->salesOrder->date)) }}</td>
                </tr>
                <tr>
                    <td style='width:60%;'>Jenis Pesanan : Regular dan instalasi</td>
                    <td>Waktu Pesanan: {{ date('d-m-Y H:i:s', strtotime($workOrder->salesOrder->created_at)) }}</td>
                </tr>
            </table>

            <table style='margin-top:10px;'>
                <tr>
                    <td colspan="3" style="text-align:center; font-weight:700;">Informasi Pesanan</td>

                </tr>
                <tr>
                    <td style='width:60%;' rowspan="2">Alamat : {{ $workOrder->address }}</td>
                    <td style='width:20%;'>Nama Pelanggan :</td>
                    <td>{{ $workOrder->customer->name }}</td>
                </tr>
                <tr>
                    <td style='width:20%;'>Kendaraan : </td>
                    <td>{{ $workOrder->salesOrder->vehicle->name ?? '' }}</td>
                </tr>
            </table>
            <table style='margin-top:10px;'>
                <tr>
                    <td colspan="4" style="text-align:center; font-weight:700;">Pekerjaan</td>
                </tr>
                <tr>
                    <td style='width:1%; vertical-align:top;' rowspan="5">1. </td>
                    <td style='width:40%;'>Siapkan :</td>
                    <td style='width:10%;'>Jumlah</td>
                    <td rowspan="5" style="vertical-align:top;">Syarat Kondisi Aki:
                        <ul style="margin-top: 0px;">
                            <li>State of Health (SoH) 100%</li>
                            <li>Voltase minium 12.5V</li>
                            <li>Aki harus terlihat baru:
                                <ul>
                                    <li>Bersih</li>
                                    <li>Tidak ada Karat</li>
                                    <li>Tidak ada Kerusakan</li>
                                </ul>
                            </li>
                        </ul>
                    </td>
                </tr>
                @php $no = 1; @endphp
                @php $count = count($workOrder->batteries); @endphp

                @foreach ($workOrder->batteries as $battery)
                    <tr>
                        <td><input type='checkbox'>{{ $battery->battery_name }}</td>
                        <td>{{ $battery->quantity }}</td>
                    </tr>
                    @php $no++; @endphp
                @endforeach

                @while ($no <= 3)
                    <tr>
                        <td><input type='checkbox'>{{ '' }}</td>
                        <td>{{ '' }}</td>
                    </tr>
                    @php $no++; @endphp
                @endwhile

                <tr>
                    <td style='width:20%; text-align:end;'>Total</td>
                    <td>{{ $count }}</td>
                </tr>

                @for ($i = 0; $i < 15; $i++)
                    @if ($i < count($taskOne))
                        <tr>
                            <td style='width:1%;'>{{ $i + 1 }}. </td>
                            <td colspan="4"><input type='checkbox'>{{ $taskOne[$i]->message }}
                                {{-- loop sub task limit 3 --}}
                                @foreach ($taskOne[$i]->subDetails as $subTask)
                                    <br>
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='checkbox'>{{ $subTask->value }}
                                @endforeach
                            </td>
                        </tr>
                    @else
                        <tr>
                            <td style='width:1%;'>{{ $i + 1 }}. </td>
                            <td colspan="4"><input type='checkbox'></td>
                            <!-- Jika tidak ada data, kolom akan kosong -->
                        </tr>
                    @endif
                @endfor
            </table>

            {{-- <table style='margin-top:10px;'>
                <tr>
                    <td colspan="3">Catatan</td>

                </tr>
                <tr>
                    <td style='width:60%; padding:50px' rowspan="3" colspan="3"></td>
                </tr>
            </table> --}}

            <div class="row">
                <div class="column">
                    <table style='margin-top:10px;'>
                        <tr>
                            <td colspan="2" style="text-align:center; font-weight:700;">Tanda Tangan</td>
                        </tr>
                        <tr>
                            <td style="text-align:center; width:50%;">Admin Partner</td>
                            <td style="text-align:center;">Teknisi</td>
                        </tr>
                        <tr>
                            <td style="padding:24px; width:50%;"></td>
                            <td style="padding:24px"></td>
                        </tr>
                    </table>
                </div>
                <div class="column">
                    <table style='margin-top:10px;'>
                        <tr>
                            <td colspan="4" style="text-align:center; font-weight:700;">QR Code Lokasi</td>
                        </tr>
                        <tr>
                            <td style="text-align:center;">{{ $qrCode }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.print();
        window.onafterprint = function() {
            window.history.back();
        }
    </script>
</body>

</html>
