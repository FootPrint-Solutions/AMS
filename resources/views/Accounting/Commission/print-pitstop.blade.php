<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Commission Recap Pitstop</title>
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
            margin-left: 40px;
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
        th, .total-row th {
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
    <div class="header">
        <h2>Komisi Akikita</h2>
        <div class="header-details">
            <strong>Bulan / Tahun</strong>
            {{ $printMonthYear }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 25%;">Tanggal</th>
                <th style="width: 45%;">Product Name</th>
                <th style="width: 30%;">Commission</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $grandTotal = 0; 
                $rowCount = 0;
            @endphp
            @foreach($grouped as $date => $products)
                @foreach($products as $productName => $commission)
                    @php 
                        $grandTotal += $commission; 
                        $rowCount++;
                    @endphp
                    <tr>
                        <td class="text-center">{{ date('Y-m-d', strtotime($date)) }}</td>
                        <td>{{ $productName }}</td>
                        <td class="text-right">Rp{{ number_format($commission, 2, '.', ',') }}</td>
                    </tr>
                @endforeach
            @endforeach
            
        </tbody>
        <tfoot>
            <tr class="total-row">
                <th colspan="2" class="text-right">Total:</th>
                <th class="text-right">Rp{{ number_format($grandTotal, 2, '.', ',') }}</th>
            </tr>
        </tfoot>
    </table>

    <div class="print-date">
        {{ date('d/m/Y') }}
    </div>
</body>
</html>
