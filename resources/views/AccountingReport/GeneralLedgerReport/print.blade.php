<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        font-size: 13px;
        color: #333;
    }

    .report-title {
        text-align: center;
        margin-bottom: 14px;
    }

    .report-title h2 {
        margin: 0;
        font-weight: 600;
    }

    .report-title p {
        margin: 2px 0;
        font-size: 14px;
        color: #666;
    }

    .ledger-block {
        margin-bottom: 18px;
    }

    .ledger-account-title {
        background-color: #f5f5f5;
        border: 1px solid #ddd;
        border-bottom: 0;
        padding: 8px 10px;
        font-weight: 600;
    }

    .ledger-account-title small {
        color: #666;
        font-weight: 500;
    }

    .table-ledger {
        width: 100%;
        border-collapse: collapse;
    }

    .table-ledger thead th {
        background-color: #f5f5f5;
        text-align: center;
        font-weight: 600;
        border: 1px solid #ddd;
        padding: 8px;
    }

    .table-ledger tbody td,
    .table-ledger tfoot th {
        border: 1px solid #ddd;
        padding: 6px 8px;
        vertical-align: middle;
    }

    .table-ledger tbody tr:nth-child(even) {
        background-color: #fafafa;
    }

    .table-ledger tfoot th {
        background-color: #f0f0f0;
        font-weight: bold;
    }

    .text-center {
        text-align: center;
    }

    .text-right {
        text-align: right;
    }

    .text-left {
        text-align: left;
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

        .table-ledger thead {
            background-color: #eee !important;
            -webkit-print-color-adjust: exact;
        }
    }
</style>

<div class="report-title">
    <h2>General Ledger Report</h2>
    <p>{{ $company->name ?? '' }}</p>
    <p>Chart of Account: {{ $data['chartOfAccount'] ?? 'ALL' }}</p>
    <p>Period {{ strtoupper($data['dates'] ?? '') }}</p>
</div>

@if (count($data['tables']) > 0)
    @foreach ($data['tables'] as $table)
        <div class="ledger-block">
            <div class="ledger-account-title">
                {{ $table['name'] }}
                <small>({{ $table['number'] }})</small>
            </div>

            <table class="table-ledger">
                <thead>
                    <tr>
                        <th width="12%">Date</th>
                        <th width="18%">Voucher No.</th>
                        <th>Description</th>
                        <th width="15%">Debit</th>
                        <th width="15%">Credit</th>
                        <th width="15%">Balance</th>
                    </tr>
                </thead>

                <tbody>
                    <tr>
                        <td class="text-center"></td>
                        <td class="text-center"></td>
                        <td class="text-left">Initial Balance</td>
                        <td class="text-right">0.00</td>
                        <td class="text-right">0.00</td>
                        <td class="text-right">
                            @php
                                $initialBalance =
                                    $table['initialBalance']['totalDebit'] - $table['initialBalance']['totalCredit'];
                            @endphp
                            {{ $initialBalance > 0 ? number_format($initialBalance, 2) . ' DB' : ($initialBalance < 0 ? number_format(abs($initialBalance), 2) . ' CR' : '0') }}
                        </td>
                    </tr>

                    @php
                        $dateOnce = '';
                    @endphp

                    @foreach ($table['details'] as $row)
                        <tr>
                            @if ($dateOnce !== $row['date'])
                                @php
                                    $dateOnce = $row['date'];
                                @endphp
                                <td class="text-center">{{ $row['date'] }}</td>
                            @else
                                <td class="text-center"></td>
                            @endif

                            <td class="text-center">{{ $row['number'] }}</td>
                            <td class="text-left">{{ $row['description'] }}</td>
                            <td class="text-right">{{ number_format($row['totalDebit'], 2) }}</td>
                            <td class="text-right">{{ number_format($row['totalCredit'], 2) }}</td>
                            <td class="text-right">
                                {{ $row['totalDebitBalance'] > $row['totalCreditBalance'] ? number_format($row['totalDebitBalance'], 2) . ' DB' : number_format($row['totalCreditBalance'], 2) . ' CR' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>

                <tfoot>
                    <tr>
                        <th></th>
                        <th colspan="2" class="text-center">Total</th>
                        <th class="text-right">{{ number_format($table['totalDebit'], 2) }}</th>
                        <th class="text-right">{{ number_format($table['totalCredit'], 2) }}</th>
                        <th></th>
                    </tr>
                    <tr>
                        <th></th>
                        <th colspan="2" class="text-center">Ending Balance</th>
                        <th class="text-right">0.00</th>
                        <th class="text-right">0.00</th>
                        <th class="text-right">
                            @if (isset($table['endingDebitBalance']) && isset($table['endingCreditBalance']))
                                {{ $table['endingDebitBalance'] > $table['endingCreditBalance'] ? number_format($table['endingDebitBalance'], 2) . ' DB' : number_format($table['endingCreditBalance'], 2) . ' CR' }}
                            @else
                                @php
                                    $endingBalance =
                                        $table['initialBalance']['totalDebit'] -
                                        $table['initialBalance']['totalCredit'] +
                                        ($table['totalDebit'] - $table['totalCredit']);
                                @endphp
                                {{ $endingBalance > 0 ? number_format($endingBalance, 2) . ' DB' : ($endingBalance < 0 ? number_format(abs($endingBalance), 2) . ' CR' : '0') }}
                            @endif
                        </th>
                    </tr>
                </tfoot>
            </table>
        </div>
    @endforeach
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
