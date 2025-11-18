@foreach ($orders as $index => $order)
<tr data-order-id="{{ $order['id'] }}" data-order-type="{{ $order['type'] }}">
    <td>
        <button type="button" class="btn btn-sm btn-danger delete-order-row">
            <i class="fas fa-trash"></i>
        </button>
    </td>
    <td>{{ $index + 1 }}</td>
    <td>{{ $order['order_number'] }}</td>
    <td>
        @if ($order['type'] === 'sales_order')
        <span class="badge bg-primary">Sales Order</span>
        @else
        <span class="badge bg-success">Purchase Order</span>
        @endif
    </td>
    <td>{{ $order['date'] }}</td>
    <td>{{ $order['customer_supplier_name'] }}</td>
    <td>{{ $order['shop_name'] }}</td>
    <td>{{ $order['formatted_total'] }}</td>
    <td>
        <input type="number" class="form-control discount" data-id="{{ $order['id'] }}"
            min="0" max="{{ $order['total'] }}" value="0" step="0.01">
    </td>
    <td>
        <input type="text" class="form-control subtotal" data-id="{{ $order['id'] }}"
            data-original-total="{{ $order['total'] }}" value="{{ $order['formatted_total'] }}" readonly>
    </td>
</tr>
@endforeach