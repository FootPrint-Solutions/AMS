<link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">

{{-- @dd($data['profile']) --}}

<style>
    @media print {
        @page {
            size: A4 landscape;
            margin: 0%;
        }
    }

    #invoice-company-logo {
        width: 8em;
        height: 8em;
    }
</style>

<table>
    <tr>
        <td style="width: 50%;">
            <div class="container">
                <div class="head">
                    <div class="row">
                        <div class="col">
                            <div class="h1">INVOICE</div>
                            {{ $data['company']['name'] }}<br>
                            {{ $data['company']['address'] }}<br>
                            {{ $data['company']['contact'] }} | {{ $data['company']['email'] }}
                        </div>

                        <div class="col text-end">
                            <img src="/img/logos/logo.png" id="invoice-company-logo">
                        </div>
                    </div>
                </div>
                <hr>

                <table class="table">
                    <thead class="text-center">
                        <tr>
                            <th style="width: 33.3%">Customer</th>
                            <th style="width: 33.3%">Ship To</th>
                            <th style="width: 33.3%">Information</th>
                        </tr>
                    </thead>

                    <tbody>
                        {{-- DETAIL --}}
                        <tr>
                            <td>
                                <div class="container">
                                    {{ $data['profile']['customer']['name'] }}<br>
                                    +62 {{ $data['profile']['customer']['contact'] }}<br>
                                    {{ $data['profile']['customer']['email'] }}<br>
                                    {{ $data['profile']['customer']['address'] }}<br>
                                </div>
                            </td>

                            <td>
                                <div class="container">
                                    {{ $data['profile']['customer']['name'] }}<br>
                                    +62 {{ $data['profile']['customer']['contact'] }}<br>
                                    {{ $data['profile']['customer']['email'] }}<br>
                                    {{ $data['profile']['address'] }}<br>
                                </div>
                            </td>

                            <td>
                                <div class="container">
                                    {{ $data['profile']['sales_order_number'] }} -
                                    {{ date('Y-m-d', strtotime($data['profile']['created_at'])) }}<br>
                                    {{ $data['profile']['shop']['name'] ?? '-' }}<br>
                                    {{ $data['profile']['customer']['contact'] }}<br>
                                </div>
                            </td>
                        </tr>
                        </thead>
                </table>
                <br>

                {{-- Detail Table --}}
                <table class="table">
                    <thead class="text-center">
                        <tr>
                            <th style="width: 50%">Name</th>
                            <th style="width: 30%">Production Code</th>
                            <th style="width: 20%">Price (IDR)</th>
                        </tr>
                    </thead>

                    <tbody>
                        {{-- Detail --}}
                        @foreach ($data['profile']['batteries'] as $battery)
                            <tr>
                                <td>{{ $battery['battery_name'] }}</td>
                                <td>{{ $battery['battery_production_code'] }}</td>
                                <td style="text-align: right">{{ number_format($battery['battery_price']) }}</td>
                            </tr>
                        @endforeach
                    </tbody>

                    <tfoot>
                        <tr>
                            <td></td>
                            <th style="text-align: right">Tax</th>
                            <td style="text-align: right">
                                {{ number_format(($data['profile']['subtotal'] * $data['profile']['tax']) / 100) }}
                            </td>
                        </tr>

                        <tr>
                            <td></td>
                            <th style="text-align: right">Discount</th>
                            <td style="text-align: right">
                                {{ number_format(($data['profile']['subtotal'] * $data['profile']['discount']) / 100) }}
                            </td>
                        </tr>

                        <tr>
                            <td></td>
                            <th style="text-align: right">Extra Discount</th>
                            <td style="text-align: right">
                                {{ number_format(($data['profile']['subtotal'] * $data['profile']['extra_discount']) / 100) }}
                            </td>
                        </tr>

                        <tr>
                            <td colspan="2"></td>
                            <td style="text-align: right">{{ number_format($data['profile']['total']) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </td>
        <td style="width: 50%;"></td>
    </tr>
</table>



<script>
    window.onload = function() {
        window.print();
    }
</script>
