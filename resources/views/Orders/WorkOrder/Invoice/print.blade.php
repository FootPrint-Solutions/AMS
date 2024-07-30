<style>
    @media print {
        @page {
            size: A5 portrait;
            /* font-size: 0.9em; */
        }
    }

    /* * {
        font-size: 0.9em;
    } */

    #table-detail thead {
        border-bottom: double lightgray;
    }

    #table-detail-footer {
        z-index: -10;
        position: fixed;
        bottom: 0;
        left: 0;
    }

    #invoice-company-logo {
        width: 9.5em;
        height: 9.5em;
    }

    .sticky-row {
        flex-grow: 1;
        /* Allow the last row to grow and push to the bottom */
    }

    .w-100 {
        width: 100%;
    }

    .text-end {
        text-align: right;
    }

    .row {
        display: flex;
        flex-wrap: wrap;
        /* margin-right: -15px;
        margin-left: -15px; */
    }

    .col-8,
    .col-3 {
        position: relative;
        width: 100%;
        padding-right: 15px;
        padding-left: 15px;
    }

    .col-8 {
        flex: 0 0 66.666667%;
        max-width: 66.666667%;
    }

    .col-3 {
        flex: 0 0 25%;
        max-width: 25%;
    }

    .table {
        width: 100%;
        margin-bottom: 1rem;
        color: #212529;
        border-collapse: collapse;
    }

    .table th,
    .table td {
        padding: 0.75rem;
        vertical-align: top;
        border-top: 1px solid #dee2e6;
    }

    .table-sm th,
    .table-sm td {
        padding: 0.3rem;
    }

    .fst-italic {
        font-style: italic;
    }

    .my-1 {
        margin-top: 0.25rem !important;
        margin-bottom: 0.25rem !important;
    }

    .m-0 {
        margin: 0 !important;
    }
</style>

<div id="invoice-head">
    <table class="w-100">
        <tr>
            <td style="width: 50%">
                <h3>KWITANSI</h3>
                {{ $data['profile']['sales_order_number'] }}<br>
                Issued on {{ date('d M Y', strtotime($data['profile']['date'])) }}
            </td>
            <td class="text-end" style="width: 50%">
                <div class="row" style="margin-left: -20px;">
                    <div class="col-8 text-end">
                        <h3>{{ $data['company']['name'] }}</h3>
                        {{ $data['company']['address'] }}<br>
                        {{ $data['company']['contact'] }}<br>
                        {{ $data['company']['email'] }}<br>
                    </div>
                    <div class="col-3">
                        <img src="/img/logos/256x256.png" style="margin-left: -25px;" id="invoice-company-logo">
                    </div>
                </div>
            </td>
        </tr>
    </table>
</div>
<hr class="my-1">

<div id="invoice-body">
    <table class="table table-sm">
        <tr>
            <td>
                {{ $data['profile']['customer']['name'] }} (+62 {{ $data['profile']['customer']['contact'] }})<br>
                {{ $data['profile']['customer']['email'] }}<br>
                {{ $data['profile']['address'] }}
            </td>
        </tr>
    </table>

    <table class="table table-sm" id="table-detail">
        <thead>
            <tr>
                <th style="width: 2%; font-size: 1.2em">No</th>
                <th style="width: 30%; font-size: 1.2em">Production Code</th>
                <th style="width: 48%; font-size: 1.2em">Name</th>
                <th style="width: 20%; text-align: right; font-size: 1.2em">Price (IDR)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data['profile']['batteries'] as $index => $battery)
                <tr>
                    <td style="text-align: center">{{ $index + 1 }}</td>
                    <td>{{ $battery['battery_production_code'] }}</td>
                    <td>{{ $battery['battery_name'] }}</td>
                    <td style="text-align: right">{{ number_format($battery['price_net']) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2" style="width: 50%"></td>
                <th style="text-align: right; width: 30%">Subtotal</th>
                <td style="text-align: right; width: 20%">
                    {{ number_format($data['profile']['subtotal']) }}
                </td>
            </tr>
            <tr>
                <td colspan="2"></td>
                <th style="text-align: right">Discount</th>
                <td style="text-align: right">
                    {{ number_format($data['profile']['discount_price']) }}
                </td>
            </tr>
            <tr>
                <td colspan="2" class="fst-italic">{{ ucwords(convertToTerbilang($data['profile']['total'])) }}
                </td>
                <th style="text-align: right">Total</th>
                <td style="text-align: right">{{ number_format($data['profile']['total']) }}</td>
            </tr>
        </tfoot>
    </table>
</div>
