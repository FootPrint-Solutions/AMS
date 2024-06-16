<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Teknisi</title>
</head>
<style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        background-color: #f4f4f4;
    }

    .report-container {
        width: 80%;
        margin: 20px auto;
        padding: 20px;
        background-color: #fff;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    h1 {
        text-align: center;
        color: #333;
    }

    .order-details,
    .address,
    .battery,
    .vehicle-condition,
    .battery-tension,
    .current-while-off,
    .notes,
    .signatures {
        margin-bottom: 20px;
    }

    strong {
        color: #333;
    }

    .input-field {
        width: 100px;
        padding: 5px;
        margin-left: 10px;
    }

    .notes-details {
        width: 100%;
        height: 100px;
        padding: 10px;
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

    .technician-signature,
    .customer-signature {
        width: 45%;
        text-align: center;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    table,
    th,
    td {
        border: 1px solid #000;
    }
</style>

<body>
    <div class="report-containezr">
        <h1>Laporan Teknisi Akikita.id</h1>
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
</body>

</html>
