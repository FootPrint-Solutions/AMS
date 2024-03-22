<link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">

<style>
    @page {
        size: A5;
    }
</style>

<div class="head">
    <div class="h1">INVOICE</div>
    {{ $data['company']['name'] }}<br>
    {{ $data['company']['address'] }}<br>
    {{ $data['company']['contact'] }} | {{ $data['company']['email'] }}
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
                    {{ $data['profile']['quotation_number'] }} -
                    {{ date('Y-m-d', strtotime($data['profile']['created_at'])) }}<br>
                    {{ $data['profile']['shop']['name'] }}<br>
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
            <th style="width: 30%">Name</th>
            <th style="width: 30%">Price (IDR)</th>
            <th style="width: 20%">Quantity</th>
            <th style="width: 20%">Total Price (IDR)</th>
        </tr>
    </thead>

    <tbody>
        {{-- Detail --}}
        @foreach ($data['profile']['batteries'] as $battery)
            <tr>
                <td>{{ $battery['battery_name'] }}</td>
                <td style="text-align: right">{{ number_format($battery['battery_price']) }}</td>
                <td style="text-align: right">{{ $battery['quantity'] }}</td>
                <td style="text-align: right">{{ number_format($battery['battery_price'] * $battery['quantity']) }}
                </td>
            </tr>
        @endforeach
    </tbody>

    <tfoot>
        <tr>
            <td colspan="2"></td>
            <th style="text-align: right">Tax</th>
            <td style="text-align: right">{{ number_format($data['profile']['tax'] * $data['profile']['total']) }}
            </td>
        </tr>

        <tr>
            <td colspan="2"></td>
            <th style="text-align: right">Discount</th>
            <td style="text-align: right">
                {{ number_format($data['profile']['discount'] * $data['profile']['total']) }}</td>
        </tr>

        <tr>
            <td colspan="2"></td>
            <th style="text-align: right">Extra Discount</th>
            <td style="text-align: right">
                {{ number_format($data['profile']['extra_discount'] * $data['profile']['total']) }}</td>
        </tr>

        <tr>
            <td colspan="3"></td>
            <td style="text-align: right">{{ number_format($data['profile']['total']) }}</td>
        </tr>
    </tfoot>
</table>

<script>
    window.onload = function() {
        window.print();
    }
</script>
