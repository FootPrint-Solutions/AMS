<link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">

<style>
    @media print {
        @page {
            size: A5 portrait;
            font-size: 0.9em;
        }
    }

    * {
        font-size: 0.9em;
    }

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
</style>

{{-- HEAD --}}
<div id="invoice-head">
    <table class="w-100">
        <tr>
            {{-- Invoice Information --}}
            <td style="width: 50%">
                <h3>KWITANSI</h3>
                {{ $data['profile']['sales_order_number'] }}<br>
                Issued on {{ date('d M Y', strtotime($data['profile']['date'])) }}
            </td>

            {{-- Company Profile --}}
            <td class="text-end" style="width: 50%">
                <div class="row">
                    <div class="col-9 text-end">
                        <h3>{{ $data['company']['name'] }}</h3>
                        {{ $data['company']['address'] }}<br>
                        {{ $data['company']['contact'] }}<br>
                        {{ $data['company']['email'] }}<br>
                    </div>

                    <div class="col-3">
                        <img src="/img/logos/256x256.png" id="invoice-company-logo">
                    </div>
                </div>
            </td>
        </tr>
    </table>
</div>
<hr class="my-1">

{{-- BODY --}}
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

    {{-- Detail Table --}}
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
            {{-- Detail --}}
            @foreach ($data['profile']['batteries'] as $index => $battery)
                <tr>
                    <td style="text-align: center">{{ $index + 1 }}</td>
                    <td>{{ $battery['battery_production_code'] }}</td>
                    <td>{{ $battery['battery_name'] }}</td>
                    <td style="text-align: right">{{ number_format($battery['price_net']) }}</td>
                </tr>
            @endforeach
        </tbody>
        <hr class="m-0">

        <table class="table table-sm" id="table-detail-footer">
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
    </table>
</div>
