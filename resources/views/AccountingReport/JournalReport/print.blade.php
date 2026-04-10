<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        font-size: 13px;
        color: #333;
    }

    .report-title {
        text-align: center;
        margin-bottom: 10px;
    }

    .report-title h2 {
        margin: 0;
        font-weight: 600;
    }

    .report-title p {
        margin: 0;
        font-size: 14px;
        color: #666;
    }

    #table-journal {
        width: 100%;
        border-collapse: collapse;
    }

    #table-journal thead th {
        background-color: #f5f5f5;
        text-align: center;
        font-weight: 600;
        border: 1px solid #ddd;
        padding: 8px;
    }

    #table-journal tbody td {
        border: 1px solid #ddd;
        padding: 6px 8px;
        vertical-align: middle;
    }

    #table-journal tbody tr:nth-child(even) {
        background-color: #fafafa;
    }

    #table-journal tbody tr:hover {
        background-color: #f1f7ff;
    }

    #table-journal tfoot th {
        border: 1px solid #ddd;
        padding: 8px;
        background-color: #f0f0f0;
        font-weight: bold;
    }

    .text-center {
        text-align: center;
    }

    .text-right {
        text-align: right;
    }

    .no-data {
        text-align: center;
        font-size: 14px;
        color: #888;
        margin-top: 20px;
    }

    @media print {
        body {
            font-size: 12px;
        }

        #table-journal thead {
            background-color: #eee !important;
            -webkit-print-color-adjust: exact;
        }
    }
</style>

{{-- Title --}}
<div class="report-title">
    <h2>Journal Transaction Report</h2>
    <p>
        Period {{ $data['dateStart'] }}
        @if ($data['dateEnd'])
            - {{ $data['dateEnd'] }}
        @endif
    </p>
</div>

{{-- Data --}}
@if (count($data['reports']) > 0)
    <table id="table-journal">
        <thead>
            <tr>
                <th width="12%">Date</th>
                <th width="18%">Voucher Number</th>
                <th>Description</th>
                <th width="15%">Debit</th>
                <th width="15%">Credit</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($data['reports'] as $row)
                @php $firstDetail = true; @endphp

                @foreach ($row['details'] as $detail)
                    <tr>
                        @if ($firstDetail)
                            <td rowspan="{{ count($row['details']) }}" class="text-center">
                                {{ $row['date'] }}
                            </td>
                            <td rowspan="{{ count($row['details']) }}" class="text-center">
                                {{ $row['number'] }}
                            </td>
                            @php $firstDetail = false; @endphp
                        @endif

                        <td>{{ $detail['description'] }}</td>
                        <td class="text-right">
                            {{ number_format($detail['total_debit'], 2) }}
                        </td>
                        <td class="text-right">
                            {{ number_format($detail['total_credit'], 2) }}
                        </td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>

        <tfoot>
            <tr>
                <th colspan="3" class="text-right">TOTAL</th>
                <th class="text-right">
                    {{ number_format($data['totalDebit'], 2) }}
                </th>
                <th class="text-right">
                    {{ number_format($data['totalCredit'], 2) }}
                </th>
            </tr>
        </tfoot>
    </table>
@else
    <div class="no-data">
        No data available
    </div>
@endif

<script>
    $(function() {
        // window.print();
    });
</script>
