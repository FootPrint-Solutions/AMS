@extends('template.master')

@section('content')
<div class="d-none d-lg-block">
    <div class="card">
        <div class="card-body">
            {{-- Title --}}
            <div class="card-title h5">
                <i class="fas fa-shipping-fast text-primary"></i>
                Sales Consignment Details
                <small class="text-muted d-block">{{ $salesConsignment->sales_consignment_number }}</small>
            </div>
            <hr>

            {{-- Consignment Info --}}
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card border-primary">
                        <div class="card-header bg-primary text-white">
                            <i class="fas fa-info-circle mr-2"></i>Consignment Information
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Consignment Number:</th>
                                    <td>{{ $salesConsignment->sales_consignment_number }}</td>
                                </tr>
                                <tr>
                                    <th>Date:</th>
                                    <td>{{ formatDate($salesConsignment->date) }}</td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td>
                                        @if($salesConsignment->status == 'draft')
                                        <span class="badge badge-secondary">Draft</span>
                                        @elseif($salesConsignment->status == 'posted')
                                        <span class="badge badge-success">Posted</span>
                                        @else
                                        <span class="badge badge-info">Completed</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Payment Status:</th>
                                    <td>
                                        @if($salesConsignment->payment_status == 'paid')
                                        <span class="badge badge-success">Paid</span>
                                        @elseif($salesConsignment->payment_status == 'pending')
                                        <span class="badge badge-warning">Pending</span>
                                        @else
                                        <span class="badge badge-danger">Failed</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card border-info">
                        <div class="card-header bg-info text-white">
                            <i class="fas fa-calculator mr-2"></i>Financial Summary
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Subtotal:</th>
                                    <td class="text-right">{{ formatPrice($salesConsignment->subtotal) }}</td>
                                </tr>
                                <tr>
                                    <th>Discount ({{ $salesConsignment->discount }}%):</th>
                                    <td class="text-right">-{{ formatPrice($salesConsignment->discount_price) }}</td>
                                </tr>
                                <tr>
                                    <th>Total Expenses:</th>
                                    <td class="text-right">{{ formatPrice($salesConsignment->total_expenses) }}</td>
                                </tr>
                                <tr class="border-top">
                                    <th><strong>Total:</strong></th>
                                    <td class="text-right"><strong class="text-primary">{{ formatPrice($salesConsignment->total) }}</strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sales Invoices --}}
            <div class="card border-success mb-4">
                <div class="card-header bg-success text-white">
                    <i class="fas fa-file-invoice mr-2"></i>
                    Sales Invoices ({{ $salesConsignment->salesInvoices->count() }} items)
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Invoice Number</th>
                                    <th>Date</th>
                                    <th>Customer</th>
                                    <th>Vehicle</th>
                                    <th>Distributor</th>
                                    <th>Items</th>
                                    <th class="text-right">Total</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($salesConsignment->salesInvoices as $index => $invoice)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <strong>{{ $invoice->sales_invoice_number }}</strong>
                                        @if($invoice->invoice_number)
                                        <br><small class="text-muted">{{ $invoice->invoice_number }}</small>
                                        @endif
                                    </td>
                                    <td>{{ formatDate($invoice->date) }}</td>
                                    <td>{{ $invoice->customer->name ?? 'N/A' }}</td>
                                    <td>{{ $invoice->vehicle->name ?? 'N/A' }}</td>
                                    <td>{{ $invoice->shop->distributor->name ?? 'N/A' }}</td>
                                    <td>
                                        @if($invoice->batteries->count() > 0)
                                        @foreach($invoice->batteries as $battery)
                                        <span class="badge badge-secondary mr-1 mb-1">
                                            {{ $battery->battery_name }} ({{ $battery->quantity }})
                                        </span>
                                        @endforeach
                                        @else
                                        <span class="text-muted">No items</span>
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        <strong>{{ formatPrice($invoice->total) }}</strong>
                                    </td>
                                    <td>
                                        <a href="/sales-invoice/invoice/{{ $invoice->id }}"
                                            class="btn btn-sm btn-outline-primary"
                                            title="View Invoice" target="_blank">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="row">
                <div class="col-md-12">
                    <div class="btn-group" role="group">
                        <a href="/sales-consignment" class="btn btn-secondary">
                            <i class="fas fa-arrow-left mr-2"></i>Back to List
                        </a>

                        @if($salesConsignment->status === 'draft')
                        <button type="button" class="btn btn-success ml-2" onclick="postConsignment()">
                            <i class="fas fa-check mr-2"></i>Post Consignment
                        </button>

                        <button type="button" class="btn btn-danger ml-2" onclick="deleteConsignment()">
                            <i class="fas fa-trash mr-2"></i>Delete Consignment
                        </button>
                        @endif

                        <button type="button" class="btn btn-primary ml-2" onclick="printConsignment()">
                            <i class="fas fa-print mr-2"></i>Print Consignment
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Scripts --}}
<script>
    function postConsignment() {
        Swal.fire({
            title: 'Post Consignment',
            text: 'Are you sure you want to post this consignment?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, Post it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/sales-consignment/post',
                    type: 'PUT',
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: {
                            {
                                $salesConsignment - > id
                            }
                        }
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Success!', response.message, 'success').then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Error!', response.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = xhr.responseJSON?.message || 'An error occurred';
                        Swal.fire('Error!', errorMessage, 'error');
                    }
                });
            }
        });
    }

    function deleteConsignment() {
        Swal.fire({
            title: 'Delete Consignment',
            text: 'Are you sure you want to delete this consignment? This action cannot be undone!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, Delete it!',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#d33'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/sales-consignment/destroy',
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: {
                            {
                                $salesConsignment - > id
                            }
                        }
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Deleted!', response.message, 'success').then(() => {
                                window.location.href = '/sales-consignment';
                            });
                        } else {
                            Swal.fire('Error!', response.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = xhr.responseJSON?.message || 'An error occurred';
                        Swal.fire('Error!', errorMessage, 'error');
                    }
                });
            }
        });
    }

    function printConsignment() {
        window.open(`/sales-consignment/print/{{ $salesConsignment->id }}`, '_blank');
    }
</script>
@endsection