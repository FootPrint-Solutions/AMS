<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Teknisi</title>
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

        .kertas_print {
            width: 100%;
            max-width: 9.5cm;
            /* Set max-width to A5 width */
            height: 13cm;
            /* Set height to A5 height */
            margin-left: 1.2cm;
            padding: 2px;
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
                overflow: hidden;
            }
        }

        .signatures {
            margin-bottom: 20px;
        }

        .signature-line {
            border-bottom: 1px solid #000;
            margin-top: 20px;
            margin-bottom: 5px;
            width: 50%;
        }

        .signatures {
            display: flex;
            justify-content: space-between;
        }

        .customer-signature {
            width: 45%;
            text-align: center;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="kertas_print">
            <div class="section">
                <h2>Laporan Teknisi Akikita.id</h2>
                <table>
                    <tr>
                        <td>
                            <strong>Order ID: </strong>{{ $workOrder->work_order_number }}
                        </td>
                        <td>
                            <strong>Tanggal: </strong> {{ date('d-m-Y', strtotime($workOrder->salesOrder->date)) }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2"> <strong>Alamat: </strong>{{ $workOrder->address }}</td>
                    </tr>
                    <tr>
                        <td colspan="2"> <strong>Aki:</strong>
                            @foreach ($workOrder->batteries as $battery)
                                {{ $battery->battery_name }},
                            @endforeach
                        </td>
                    </tr>
                </table>
                <div class="vehicle-condition">
                    <p><strong>Kondisi Kendaraan:</strong></p>
                    <p>Apakah kendaraan dapat dinyalakan setelah pemasangan aki?</p>
                    <label><input type="radio" name="start-status" value="ya"> Ya</label>
                    <label><input type="radio" name="start-status" value="tidak"> Tidak</label>
                </div>
                <div class="battery-tension">
                    <p><strong>Ketegangan mekanisme pengisian daya aki:</strong></p>
                    __________________Volt <span style="margin-left: 80px;">(Optimal: 14V – 14.5V)</span>
                </div>
                <div class="current-while-off">
                    <p><strong>Arus listrik saat kendaraan mati:</strong></p>
                    __________________Amp <span style="margin-left: 80px;">(Optimal: 0.10A – 0.50A)</span>
                </div>
                <table style='margin-top:10px;'>
                    <p><strong>Keterangan:</strong></p>
                    <tr>
                        <td style='width:60%; padding:50px' rowspan="3" colspan="3"></td>
                    </tr>
                </table>
                <div class="signatures">
                    <div class="technician-signature">
                        <p><strong>Tanda Tangan Teknisi</strong></p>
                        <p style="margin-top: 45px;"><strong>Nama:</strong> <span class="technician-name"></span></p>
                    </div>
                    <div class="customer-signature">
                        <p><strong>Tanda Tangan Pelanggan</strong></p>
                        <p style="margin-top: 45px;"><strong>Nama:</strong> <span class="customer-name"></span></p>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>
        // window.print();
        // window.onafterprint = function() {
        //     window.history.back();
        // }
    </script>
</body>

</html>
