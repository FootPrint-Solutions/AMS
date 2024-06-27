{{-- create view detail work order --}}

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header text-white">
                <h4 class="card-title mb-0">Detail Work Order</h4>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="row mb-3">
                            <div class="col-md-4 font-weight-bold">Work Order Number:</div>
                            <div class="col-md-8">{{ $workOrder->work_order_number }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 font-weight-bold">Date:</div>
                            <div class="col-md-8">{{ $workOrder->date }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 font-weight-bold">Sales Order Number:</div>
                            <div class="col-md-8">{{ $workOrder->salesOrder->sales_order_number }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 font-weight-bold">Customer:</div>
                            <div class="col-md-8">{{ $workOrder->salesOrder->customer->name }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 font-weight-bold">Discount:</div>
                            <div class="col-md-8">{{ $workOrder->discount }}%</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 font-weight-bold">Extra Discount:</div>
                            <div class="col-md-8">{{ $workOrder->extra_discount }}%</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 font-weight-bold">Total:</div>
                            <div class="col-md-8">{{ number_format($workOrder->total, 0, ',', '.') }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 font-weight-bold">Status:</div>
                            <div class="col-md-8">{{ $workOrder->status }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row mb-3">
                            <div class="col-md-4 font-weight-bold">Address:</div>
                            <div class="col-md-8">{{ $workOrder->address }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 font-weight-bold">Location:</div>
                            <div class="col-md-8">
                                <p class="mb-0">Latitude: {{ $workOrder->latitude }}</p>
                                <p class="mb-0">Longitude: {{ $workOrder->longitude }}</p>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 font-weight-bold">Image:</div>
                            <div class="col-md-8">
                                @if (!$workOrder->image)
                                    <img src="{{ Storage::url($workOrder->image) }}" alt="Image" class="img-fluid">
                                @else
                                    <p class="mb-0">No Image</p>
                                @endif
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 font-weight-bold">Attachment File:</div>
                            <div class="col-md-8">
                                @if ($workOrder->attachment_file)
                                    <a href="{{ Storage::url($workOrder->attachment_file) }}" target="_blank"
                                        class="btn btn-sm btn-info">Download</a>
                                @else
                                    <p class="mb-0">No Attachment File</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-lg-12">
                        <h4 class="mb-3">Batteries</h4>
                        <hr>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="bg-secondary text-white">
                                    <tr>
                                        <th>#</th>
                                        <th>Battery Name</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($workOrder->batteries as $key => $battery)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $battery->battery_name }}</td>
                                            <td>{{ number_format($battery->battery_price, 0, ',', '.') }}</td>
                                            <td>{{ $battery->quantity }}</td>
                                            <td>{{ number_format($battery->battery_price * $battery->quantity, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <h4 class="mb-3">Payment</h4>
                        <hr>
                        <div class="row mb-3">
                            <div class="col-md-4 font-weight-bold">Payment Status:</div>
                            <div class="col-md-8">{{ $workOrder->salesOrder->payment_status }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 font-weight-bold">Payment Method:</div>
                            <div class="col-md-8">{{ $workOrder->salesOrder->paymentMethod->name }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 font-weight-bold">Payment Link:</div>
                            <div class="col-md-8">
                                {{--  jika payment link tidak null --}}
                                @if ($workOrder->salesOrder->midtrans_payment_link)
                                    <a href="{{ $workOrder->salesOrder->midtrans_payment_link }}" target="_blank"
                                        class="btn btn-sm btn-primary">Payment Link</a>
                                @else
                                    <p class="mb-0">No Payment Link</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
