@extends('template.master')

@section('content')
<div class="card">
    <div class="card-body">
        {{-- Title --}}
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Purchase Order Details</h3>
                </div>
                <div class="col-auto text-end float-end ms-auto download-grp">
                    <a href="/purchase-order/edit/{{ $purchaseOrder->id }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="/purchase-order" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>
        </div>
        <br>

        {{-- Purchase Order Information --}}
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Purchase Order Information</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>PO Number:</strong></td>
                                <td>{{ $purchaseOrder->purchase_order_number }}</td>
                            </tr>
                            <tr>
                                <td><strong>Invoice Number:</strong></td>
                                <td>{{ $purchaseOrder->invoice_number ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Date:</strong></td>
                                <td>{{ date('d-m-Y', strtotime($purchaseOrder->date)) }}</td>
                            </tr>
                            <tr>
                                <td><strong>Payment Status:</strong></td>
                                <td>
                                    <span class="badge badge-{{ $purchaseOrder->payment_status == 'paid' ? 'success' : ($purchaseOrder->payment_status == 'partial' ? 'warning' : 'secondary') }}">
                                        {{ ucfirst($purchaseOrder->payment_status) }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Status:</strong></td>
                                <td>
                                    <span class="badge badge-{{ $purchaseOrder->status == 'completed' ? 'success' : ($purchaseOrder->status == 'posted' ? 'warning' : 'secondary') }}">
                                        {{ ucfirst($purchaseOrder->status) }}
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Supplier Information</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Name:</strong></td>
                                <td>{{ $purchaseOrder->supplier->name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Contact:</strong></td>
                                <td>{{ $purchaseOrder->supplier->contact }}</td>
                            </tr>
                            <tr>
                                <td><strong>Email:</strong></td>
                                <td>{{ $purchaseOrder->supplier->email ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Address:</strong></td>
                                <td>{{ $purchaseOrder->address ?? $purchaseOrder->supplier->address }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Battery Details --}}
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title">Battery Details</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Battery Name</th>
                                <th>Retail Price</th>
                                <th>Tax (%)</th>
                                <th>Tax Price</th>
                                <th>Discount</th>
                                <th>Net Price</th>
                                <th>Quantity</th>
                                <th>Total</th>
                                <th>Production Code</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($purchaseOrder->batteries as $index => $battery)
                            <tr class="{{ $battery->battery->type == 'recycle' ? 'bg-danger text-white' : '' }}">
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $battery->battery_name }}</td>
                                <td class="text-end">Rp {{ number_format($battery->battery_price_retail, 0, ',', '.') }}</td>
                                <td class="text-center">{{ $battery->tax }}%</td>
                                <td class="text-end">Rp {{ number_format($battery->tax_price, 0, ',', '.') }}</td>
                                <td class="text-end">Rp {{ number_format($battery->discount_price, 0, ',', '.') }}</td>
                                <td class="text-end">Rp {{ number_format($battery->price_net, 0, ',', '.') }}</td>
                                <td class="text-center">{{ $battery->quantity }}</td>
                                <td class="text-end">Rp {{ number_format($battery->price_net * $battery->quantity, 0, ',', '.') }}</td>
                                <td class="text-center">{{ $battery->battery_production_code ?? '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="8" class="text-end"><strong>Subtotal:</strong></td>
                                <td class="text-end"><strong>Rp {{ number_format($purchaseOrder->subtotal, 0, ',', '.') }}</strong></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="8" class="text-end"><strong>Discount:</strong></td>
                                <td class="text-end"><strong>Rp {{ number_format($purchaseOrder->discount_price, 0, ',', '.') }}</strong></td>
                                <td></td>
                            </tr>
                            <tr class="table-active">
                                <td colspan="8" class="text-end"><strong>Total:</strong></td>
                                <td class="text-end"><strong>Rp {{ number_format($purchaseOrder->total, 0, ',', '.') }}</strong></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection