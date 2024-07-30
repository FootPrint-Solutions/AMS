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
            margin-top: 0px;
            font-size: 14px;
        }

        .table-order {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #000;
            /* padding: 4px; */
            text-align: left;
        }

        .table-order td {
            border: 1px solid #000;
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
            border: 1px solid #000;
            margin-top: 10px;
        }

        .customer-signature {
            width: 45%;
            text-align: center;
        }

        .text-center {
            text-align: center;
        }

        .mt-5 {
            margin-top: 5px;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .text-bukti {
            margin-top: 0px;
            margin-bottom: 0px;
        }

        .qr-code {
            margin-top: -80px;
            margin-left: 266px;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="kertas_print">
            <div class="section">
                <img src="{{ asset('img/akikita-tech-report.png') }}" style="width: 190px;">
                <h3>
                    <table style="width: 60%; border: 0px solid #000;">
                        <tr>
                            <td>WA</td>
                            <td>:</td>
                            <td>082228800175</td>
                        </tr>
                        <tr>
                            <td>Email</td>
                            <td>:</td>
                            <td>akikita.id@gmail.com </td>
                        </tr>
                        <tr>
                            <td>Website</td>
                            <td>:</td>
                            <td>www.akikita.id </td>
                        </tr>
                    </table>
                </h3>
                <div class="qr-code">
                    {{ $qrCode }}
                </div>
                <h1 class="text-right text-bukti">Bukti Instalasi</h1>
                <table class="table-order">
                    <tr>
                        <td>
                            <strong>Order ID</strong>
                        </td>
                        <td>
                            {{ $workOrder->work_order_number }}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <strong>Tanggal</strong>
                        </td>
                        <td>
                            {{ date('d-m-Y', strtotime($workOrder->salesOrder->date)) }}
                        </td>
                    </tr>
                    <tr>
                        <td> <strong>Alamat</strong></td>
                        <td>
                            {{ $workOrder->address }}
                        </td>
                    </tr>
                    <tr>
                        <td> <strong>Nama Unit:</strong>
                        </td>
                        <td>
                            @foreach ($workOrder->batteries as $battery)
                                {{ $battery->battery_name }},
                            @endforeach
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <strong>
                                Catatan
                            </strong>
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <td colspan="2" style='width:50%; padding:50px' rowspan="3" colspan="3"></td>
                    </tr>
                </table>
                <table class="table-order text-center mt-5">
                    <tr>
                        <td colspan="2"><strong>Tanda Tangan</strong></td>
                    </tr>
                    <tr>
                        <td><strong>Teknisi</strong></td>
                        <td><strong>Penerima</strong></td>
                    </tr>
                    <tr>
                        <td style='width:50%; padding:30px'></td>
                        <td style='width:50%; padding:30px'></td>
                    </tr>
                    <tr class="text-left">
                        <td><strong>Nama: </strong></td>
                        <td><strong>Nama: </strong></td>
                    </tr>
                </table>

                <h3 class="text-center">Terima kasih telah memilih Akikita.id</h3>
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
