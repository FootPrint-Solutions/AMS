<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Commission Recap PIC & Technician</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 13px;
            margin: 40px;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .header {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            margin-left: 20px;
        }
        .header h2 {
            margin: 0;
            margin-right: 40px;
            font-size: 24px;
        }
        .header-details {
            font-size: 13px;
        }
        .header-details strong {
            display: block;
            margin-bottom: 4px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #777;
            padding: 4px 8px;
        }
        th.grey-bg, .grey-bg {
            background-color: #bfbfbf;
            font-weight: bold;
        }
        th {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .print-date {
            margin-top: 40px;
            font-size: 14px;
        }
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body onload="window.print()">
    @php 
        $grandTotalPIC = 0; 
        $grandTotalTechnician = 0; 
        foreach($grouped as $date => $rows) {
            foreach($rows as $rowId => $commissions) {
                $grandTotalPIC += $commissions['PIC'];
                $grandTotalTechnician += $commissions['Technician'];
            }
        }
    @endphp

    <table>
        <thead>
            <tr>
                <th colspan="3" rowspan="2" style="border: none; vertical-align: bottom; padding: 0 0 5px 0; text-align: left;">
                    <div style="display: flex; align-items: flex-end; padding-left: 5px;">
                        <h2 style="margin: 0; margin-right: 20px; font-size: 24px;">Komisi Akikita</h2>
                        <div style="font-size: 14px; font-weight: bold; line-height: 1.2;">
                            {{ trim($shopName) ?: 'All Distributor' }}<br>
                            <span style="font-weight: normal;">{{ $printMonthYear }}</span>
                        </div>
                    </div>
                </th>
                <th colspan="2" class="grey-bg text-center">Komisi</th>
            </tr>
            <tr>
                <th class="text-center" style="width: 20%;">{{ $picNameHeader }}</th>
                <th class="text-center" style="width: 20%;">{{ $technicianNameHeader }}</th>
            </tr>
            <tr>
                <th colspan="3" class="text-right" style="border-right: 1px solid #777;">Total:</th>
                <th class="text-right">Rp{{ number_format($grandTotalPIC, 2, '.', ',') }}</th>
                <th class="text-right">Rp{{ number_format($grandTotalTechnician, 2, '.', ',') }}</th>
            </tr>
            <tr>
                <th class="grey-bg text-center" style="width: 20%;">Tanggal</th>
                <th class="grey-bg text-center" style="width: 25%;">Produk</th>
                <th class="grey-bg text-center" style="width: 15%;">Antar Pasang</th>
                <th class="grey-bg"></th>
                <th class="grey-bg"></th>
            </tr>
        </thead>
        <tbody>
            @foreach($grouped as $date => $rows)
                @foreach($rows as $rowId => $commissions)
                    <tr>
                        <td class="text-center">{{ date('Y-m-d', strtotime($date)) }}</td>
                        <td class="text-center">{{ $commissions['Produk'] }}</td>
                        <td class="text-center">{{ $commissions['AntarPasang'] }}</td>
                        <td class="text-right">Rp{{ number_format($commissions['PIC'], 2, '.', ',') }}</td>
                        <td class="text-right">Rp{{ number_format($commissions['Technician'], 2, '.', ',') }}</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>

    <div class="print-date">
        {{ date('d/m/Y') }}
    </div>
</body>
</html>
