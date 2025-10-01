@extends('template.master')

@section('content')
    <link rel="stylesheet" href="{{ asset('plugins/bootstrap5-toggle/css/bootstrap5-toggle.min.css') }}">
    <style>
        #MapsAddressFinder {
            height: 400px;
            width: 100%;
            margin-bottom: 20px;
        }
    </style>

    {{-- Form --}}
    <div class="d-none d-lg-block">
        <div class="card">
            <div class="card-body">
                {{-- Title --}}
                <div class="card-title h5">
                    @if (isset($data['type']) && $data['type'] == 'edit')
                        Edit
                    @else
                        Add New
                    @endif
                    Sales Invoice
                </div>
                <br>

                {{-- Form --}}
                <form id="quotation-form">
                    @csrf

                    {{-- Quotation Number & Date --}}
                    <div class="row">
                        {{-- Sales Consignment Number --}}
                        <div class="col">
                            <div class="form-group local-forms">
                                <label for="sales-consignment-number">Sales Consignment Number</label>
                                <input type="text" class="form-control" id="sales-consignment-number"
                                    name="salesconsignmentnumber" placeholder="Enter consignment number" readonly
                                    @isset($data['profile'])
                       value="{{ $data['profile']['sales_consignment_number'] ?? $data['consignment_number'] }}"
                   @else
                       value="{{ $data['consignment_number'] ?? '' }}"
                   @endisset>
                            </div>
                        </div>

                        {{-- Sales Consignment Date --}}
                        <div class="col">
                            <div class="form-group local-forms">
                                <label for="sales-consignment-date">Sales Consignment Date</label>
                                <input type="date" class="form-control" id="sales-consignment-date"
                                    name="salesconsignmentdate"
                                    @isset($data['profile'])
                       value="{{ $data['profile']['sales_consignment_date'] ?? $data['consignment_date'] }}"
                   @else
                       value="{{ $data['consignment_date'] ?? date('Y-m-d') }}"
                   @endisset>
                            </div>
                        </div>



                    </div>

                    {{-- Sales Invoices Table --}}
                    <div class="card border-primary">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0">
                                <i class="fas fa-file-invoice mr-2"></i>
                                Selected Sales Invoices
                                @if (isset($data['grouped_data']) && count($data['grouped_data']) > 0)
                                    @php
                                        $totalInvoices = 0;
                                        foreach ($data['grouped_data'] as $distributorData) {
                                            $totalInvoices += count($distributorData['sales_invoices']);
                                        }
                                    @endphp
                                    <span class="badge badge-light">{{ $totalInvoices }} invoice(s)</span>
                                @endif
                            </h6>
                        </div>
                        <div class="card-body">
                            @if (isset($data['grouped_data']) && count($data['grouped_data']) > 0)
                                @foreach ($data['grouped_data'] as $distributorId => $distributorData)
                                    <div class="distributor-group mb-4">
                                        <div class="row mb-3">
                                            <div class="col-12">
                                                <h6 class="text-primary border-bottom pb-2">
                                                    <i class="fas fa-store mr-2"></i>
                                                    {{ $distributorData['distributor_name'] }}
                                                    <span class="badge badge-primary ml-2">
                                                        {{ count($distributorData['sales_invoices']) }} invoice(s)
                                                    </span>
                                                </h6>
                                            </div>
                                        </div>

                                        <div class="table-responsive">
                                            <table class="table table-bordered table-sm">
                                                <thead class="thead-light">
                                                    <tr class="text-center">
                                                        <th width="5%">No</th>
                                                        <th width="15%">Invoice Number</th>
                                                        <th width="10%">Date</th>
                                                        <th width="15%">Customer</th>
                                                        <th width="15%">Vehicle</th>
                                                        <th width="10%">Items</th>
                                                        <th width="15%">Subtotal</th>
                                                        <th width="10%">Status</th>
                                                        {{-- <th width="5%">Action</th> --}}
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($distributorData['sales_invoices'] as $index => $invoice)
                                                        <tr>
                                                            <td class="text-center">{{ $index + 1 }}</td>
                                                            <td>
                                                                <strong>{{ $invoice['sales_invoice_number'] }}</strong>
                                                                @if (!empty($invoice['invoice_number']))
                                                                    <br><small
                                                                        class="text-muted">{{ $invoice['invoice_number'] }}</small>
                                                                @endif
                                                            </td>
                                                            <td class="text-center">
                                                                {{ date('d/m/Y', strtotime($invoice['date'])) }}</td>
                                                            <td>
                                                                <strong>{{ $invoice['customer']['name'] ?? 'N/A' }}</strong>
                                                                @if (isset($invoice['customer']['contact']))
                                                                    <br><small
                                                                        class="text-muted">62{{ $invoice['customer']['contact'] }}</small>
                                                                @endif
                                                            </td>
                                                            <td>{{ $invoice['vehicle']['name'] ?? 'N/A' }}</td>
                                                            <td class="text-center">
                                                                @if (isset($invoice['batteries']) && count($invoice['batteries']) > 0)
                                                                    <span
                                                                        class="badge badge-info">{{ count($invoice['batteries']) }}
                                                                        items</span>
                                                                    <div class="mt-1"
                                                                        style="max-height: 80px; overflow-y: auto;">
                                                                        @foreach ($invoice['batteries'] as $battery)
                                                                            @if (isset($battery['battery']['type']) && $battery['battery']['type'] != 'regular')
                                                                                <span
                                                                                    class="badge badge-danger badge-sm d-block mb-1">{{ $battery['battery_name'] }}
                                                                                    ({{ $battery['quantity'] }})
                                                                                </span>
                                                                            @else
                                                                                <span
                                                                                    class="badge badge-secondary badge-sm d-block mb-1">{{ $battery['battery_name'] }}
                                                                                    ({{ $battery['quantity'] }})</span>
                                                                            @endif
                                                                        @endforeach
                                                                    </div>
                                                                @else
                                                                    <span class="text-muted">No items</span>
                                                                @endif
                                                            </td>
                                                            <td class="text-right">
                                                                <strong
                                                                    class="text-primary">{{ formatPrice($invoice['total'] ?? 0) }}</strong>
                                                                @if (($invoice['discount_price'] ?? 0) > 0)
                                                                    <br><small class="text-muted">Disc:
                                                                        -{{ formatPrice($invoice['discount_price']) }}</small>
                                                                @endif
                                                            </td>
                                                            <td class="text-center">
                                                                @if ($invoice['payment_status'] == 'paid')
                                                                    <span class="badge badge-success">Paid</span>
                                                                @elseif($invoice['payment_status'] == 'pending')
                                                                    <span class="badge badge-warning">Pending</span>
                                                                @else
                                                                    <span class="badge badge-danger">Unpaid</span>
                                                                @endif
                                                            </td>
                                                            {{-- <td class="text-center">
                                                                <a href="/sales-invoice/invoice/{{ $invoice['id'] }}"
                                                                    class="btn btn-outline-primary btn-sm"
                                                                    title="View Invoice" target="_blank">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
                                                            </td> --}}
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                                <tfoot class="bg-light">
                                                    <tr>
                                                        <td colspan="6" class="text-right"><strong>Distributor
                                                                Total:</strong></td>
                                                        <td class="text-right">
                                                            <strong class="text-primary">
                                                                {{ formatPrice(array_sum(array_column($distributorData['sales_invoices'], 'total'))) }}
                                                            </strong>
                                                        </td>
                                                        <td colspan="2"></td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                @endforeach

                                {{-- Grand Total --}}
                                <div class="row mt-4">
                                    <div class="col-md-6 offset-md-6">
                                        <div class="card border-success">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-6"><strong>Grand Total:</strong></div>
                                                    <div class="col-6 text-right">
                                                        <strong class="text-success h5">
                                                            @php
                                                                $grandTotal = 0;
                                                                foreach ($data['grouped_data'] as $distributorData) {
                                                                    $grandTotal += array_sum(
                                                                        array_column(
                                                                            $distributorData['sales_invoices'],
                                                                            'total',
                                                                        ),
                                                                    );
                                                                }
                                                            @endphp
                                                            {{ formatPrice($grandTotal) }}
                                                        </strong>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">No Sales Invoices Selected</h5>
                                    <p class="text-muted">Please go back to Sales Invoice list and select invoices to create
                                        consignment.</p>
                                    <a href="/sales-invoice" class="btn btn-primary">
                                        <i class="fas fa-arrow-left mr-2"></i>Back to Sales Invoice
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                    <br>

                    {{-- Consignment Details Form --}}
                    <div class="card border-info mt-4">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0">
                                <i class="fas fa-cogs mr-2"></i>
                                Consignment Details
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="payment-method">Payment Method <span
                                                class="text-danger">*</span></label>
                                        <select class="form-control" id="payment-method" name="paymentmethod" required>
                                            <option value="">Select payment method</option>
                                            @isset($data['payment_methods'])
                                                @foreach ($data['payment_methods'] as $method)
                                                    <option value="{{ $method['id'] }}">{{ $method['name'] }}</option>
                                                @endforeach
                                            @endisset
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="status">Status <span class="text-danger">*</span></label>
                                        <select class="form-control" id="status" name="status" required>
                                            <option value="draft" selected>Draft</option>
                                            <option value="posted">Posted</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="discount">Discount (%)</label>
                                        <input type="number" class="form-control" id="discount" name="discount"
                                            min="0" max="100" step="0.01" value="0"
                                            placeholder="0.00">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="discount-price-value">Discount Amount (IDR)</label>
                                        <input type="text" class="form-control" id="discount-price-value"
                                            name="discountprice" value="0" readonly>
                                    </div>
                                </div>
                            </div>

                            {{-- Summary --}}
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="d-flex justify-content-between">
                                                        <span><strong>Subtotal:</strong></span>
                                                        <span id="display-subtotal">
                                                            @php
                                                                $subtotal = 0;
                                                                if (isset($data['grouped_data'])) {
                                                                    foreach (
                                                                        $data['grouped_data']
                                                                        as $distributorData
                                                                    ) {
                                                                        $subtotal += array_sum(
                                                                            array_column(
                                                                                $distributorData['sales_invoices'],
                                                                                'subtotal',
                                                                            ),
                                                                        );
                                                                    }
                                                                }
                                                            @endphp
                                                            {{ formatPrice($subtotal) }}
                                                        </span>
                                                    </div>
                                                    <div class="d-flex justify-content-between">
                                                        <span><strong>Discount:</strong></span>
                                                        <span id="display-discount">{{ formatPrice(0) }}</span>
                                                    </div>
                                                    <div class="d-flex justify-content-between">
                                                        <span><strong>Total Expenses:</strong></span>
                                                        <span id="display-expenses">
                                                            @php
                                                                $totalExpenses = 0;
                                                                if (isset($data['grouped_data'])) {
                                                                    foreach (
                                                                        $data['grouped_data']
                                                                        as $distributorData
                                                                    ) {
                                                                        $totalExpenses += array_sum(
                                                                            array_column(
                                                                                $distributorData['sales_invoices'],
                                                                                'total_expenses',
                                                                            ),
                                                                        );
                                                                    }
                                                                }
                                                            @endphp
                                                            {{ formatPrice($totalExpenses) }}
                                                        </span>
                                                    </div>
                                                    <hr>
                                                    <div class="d-flex justify-content-between">
                                                        <span><strong class="h6">Grand Total:</strong></span>
                                                        <span id="display-grand-total" class="h6 text-primary">
                                                            @php
                                                                $grandTotal = 0;
                                                                if (isset($data['grouped_data'])) {
                                                                    foreach (
                                                                        $data['grouped_data']
                                                                        as $distributorData
                                                                    ) {
                                                                        $grandTotal += array_sum(
                                                                            array_column(
                                                                                $distributorData['sales_invoices'],
                                                                                'total',
                                                                            ),
                                                                        );
                                                                    }
                                                                }
                                                            @endphp
                                                            {{ formatPrice($grandTotal) }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    {{-- Additional info can go here --}}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Hidden Inputs for Invoice IDs --}}
                    @if (isset($data['grouped_data']))
                        @foreach ($data['grouped_data'] as $distributorData)
                            @foreach ($distributorData['sales_invoices'] as $invoice)
                                <input type="hidden" name="sales_invoice_ids[]" value="{{ $invoice['id'] }}">
                            @endforeach
                        @endforeach
                    @endif

                    <input type="hidden" id="subtotal" name="subtotal" value="{{ $subtotal ?? 0 }}">
                    <input type="hidden" id="total-expenses" name="totalexpenses" value="{{ $totalExpenses ?? 0 }}">
                    <input type="hidden" id="total" name="total" value="{{ $grandTotal ?? 0 }}">

                    {{-- Buttons --}}
                    <div class="d-flex justify-content-between mt-4">
                        <button type="reset" class="btn btn-danger" id="btn-cancel">
                            Cancel
                        </button>

                        <button type="submit" class="btn btn-success" id="btn-save" value="create">
                            Create Sales Consignment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Custom Styles --}}
    <style>
        .distributor-group {
            margin-bottom: 2rem;
        }

        .badge-sm {
            font-size: 0.75em;
        }

        .table th {
            border-top: none;
            font-weight: 600;
        }

        .table-responsive {
            border-radius: 0.375rem;
        }

        .card-header h6 {
            margin-bottom: 0;
        }
    </style>

    {{-- Form Scripts --}}
    <script>
        $(document).ready(function() {
            // Initialize select2
            $('#payment-method, #status').select2({
                width: '100%'
            });

            // Calculate discount when percentage changes
            $('#discount').on('input', function() {
                calculateDiscount();
            });

            function calculateDiscount() {
                const subtotal = {{ $subtotal ?? 0 }};
                const discountPercent = parseFloat($('#discount').val()) || 0;
                const discountAmount = subtotal * (discountPercent / 100);

                $('#discount-price-value').val(formatPrice(discountAmount));
                $('#display-discount').text(formatPrice(discountAmount));

                // Update grand total
                const totalExpenses = {{ $totalExpenses ?? 0 }};
                const grandTotal = subtotal - discountAmount + totalExpenses;
                $('#display-grand-total').text(formatPrice(grandTotal));
                $('#total').val(grandTotal);
            }

            function formatPrice(amount) {
                return new Intl.NumberFormat('id-ID').format(amount);
            }
        });

        // Form submission
        $("#quotation-form").on("submit", function(event) {
            event.preventDefault();

            Swal.fire({
                title: "Processing...",
                text: "Please wait while creating the consignment.",
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            let formData = new FormData($(this)[0]);

            $.ajax({
                url: "/sales-consignment/store",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    Swal.close();
                    if (response.success) {
                        Swal.fire({
                            title: "Success!",
                            text: response.message,
                            icon: "success",
                        }).then(() => {
                            window.location.href = "/sales-consignment";
                        });
                    } else {
                        Swal.fire({
                            title: "Error!",
                            text: response.message || "An error occurred.",
                            icon: "error",
                        });
                    }
                },
                error: function(xhr) {
                    Swal.close();
                    let errorMessage = "An error occurred while creating the consignment.";

                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }

                    Swal.fire({
                        title: "Error!",
                        text: errorMessage,
                        icon: "error",
                    });
                }
            });
        });

        $("#quotation-form").on("reset", function() {
            window.location.href = "/sales-invoice";
        });
    </script>
@endsection
