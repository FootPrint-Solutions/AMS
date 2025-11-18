@extends('template.master')

@section('content')
    {{-- @dd($data) --}}
    <link rel="stylesheet" href="{{ asset('plugins/bootstrap5-toggle/css/bootstrap5-toggle.min.css') }}">
    <style>
        /* Styling for readonly fields */
        .form-control[readonly] {
            background-color: #f8f9fa;
            border-color: #dee2e6;
        }

        /* Styling for editable discount fields */
        #total-discount {
            border-left: 4px solid #28a745;
            background-color: #f8fff9;
        }

        .discount {
            border-left: 3px solid #ffc107;
            background-color: #fffef7;
        }

        /* Summary section styling */
        .summary-section {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
        }

        .dataTables_filter {
            margin-top: 0px;
        }
    </style>

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
                    Billing
                </div>
                <br>

                <form id="quotation-form" method="POST">
                    @csrf

                    {{-- Quotation Number & Date --}}
                    <div class="row mb-5">
                        {{-- Billing Number --}}
                        <div class="col">
                            <div class="form-group local-forms">
                                <label for="sales-consignment-number">Billing Number</label>
                                <input type="text" class="form-control" id="sales-consignment-number"
                                    name="salesconsignmentnumber" placeholder="Enter Billingnumber" readonly
                                    value="{{ isset($data['billing']) ? $data['billing']->billing_number : $data['billing_number'] ?? '' }}">
                            </div>
                        </div>

                        {{-- Billing Date --}}
                        <div class="col">
                            <div class="form-group local-forms">
                                <label for="sales-consignment-date">Billing Date</label>
                                <input type="date" class="form-control" id="sales-consignment-date"
                                    name="salesconsignmentdate"
                                    value="{{ isset($data['billing']) ? \Illuminate\Support\Carbon::parse($data['billing']->date)->format('Y-m-d') : (isset($data['consignment_date']) ? \Illuminate\Support\Carbon::parse($data['consignment_date'])->format('Y-m-d') : date('Y-m-d')) }}">
                            </div>
                        </div>

                        {{-- Vendor --}}
                        <div class="col">
                            <div class="form-group local-forms">
                                <label for="vendor">Vendor <span class="login-danger">*</span>
                                    <i class="fas fa-info-circle ms-1 text-muted" data-toggle="tooltip" data-placement="top"
                                        title="This vendor data contains customer or supplier data."></i>
                                </label>
                                <select class="form-control" id="vendor" name="vendor" required>
                                </select>
                            </div>
                        </div>

                        {{-- Shops --}}
                        <div class="col">
                            <div class="form-group local-forms">
                                <label for="ship_to">Ship To <span class="login-danger">*</span>
                                    <i class="fas fa-info-circle ms-1 text-muted" data-toggle="tooltip" data-placement="top"
                                        title="This vendor data contains shop or distributor data."></i>
                                </label>
                                <select class="form-control" id="ship_to" name="ship_to" required>
                                </select>
                            </div>
                        </div>

                        {{-- Button Find Billing --}}
                        <div class="col">
                            <div class="form-group local-forms">
                                <button type="button" class="btn btn-primary" id="btn-find-billing">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            @if (isset($data['type']) && $data['type'] == 'edit' && isset($data['billing']))
                                {{-- Edit Mode: Show existing invoices --}}
                                <h6 class="mb-3">Selected Orders</h6>
                                <table class="table table-striped mt-5" id="selected-orders-table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>No</th>
                                            <th>Order Number</th>
                                            <th>Type</th>
                                            <th>Date</th>
                                            <th>Customer/Supplier Name</th>
                                            <th>Ship To</th>
                                            <th>Total</th>
                                            <th>Discount</th>
                                            <th>Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($data['billing']->consignmentBatteries as $index => $battery)
                                            @if ($battery->salesInvoice)
                                                <tr>
                                                    <td>
                                                        <button type="button"
                                                            class="btn btn-sm btn-danger delete-invoice-row">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </td>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $battery->salesInvoice->sales_invoice_number ?? 'N/A' }}</td>
                                                    <td>{{ $battery->salesInvoice->invoice_number ?? 'N/A' }}</td>
                                                    <td>{{ $battery->date ?? 'N/A' }}</td>
                                                    <td>{{ $battery->salesInvoice->customer->name ?? 'N/A' }}</td>
                                                    <td class="text-end">
                                                        {{ number_format($battery->subtotal ?? 0, 0, ',', '.') }}</td>
                                                    <td>{{ $battery->salesInvoice->customer->address ?? 'N/A' }}</td>
                                                    <td>
                                                        <input type="number" class="form-control discount"
                                                            data-id="{{ $battery->salesInvoice->id }}"
                                                            value="{{ $battery->discount ?? 0 }}" min="0"
                                                            step="0.01">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" name="notes[]"
                                                            placeholder="Enter note" value="">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control subtotal"
                                                            data-id="{{ $battery->salesInvoice->id }}"
                                                            data-original-total="{{ $battery->subtotal ?? 0 }}"
                                                            value="{{ number_format($battery->total ?? 0, 0, ',', '.') }}"
                                                            readonly>
                                                        <input type="hidden" name="invoice_ids[]"
                                                            value="{{ $battery->salesInvoice->id }}">
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                {{-- Create Mode: Empty table for dynamic content --}}
                                <table class="table table-striped mt-5" id="selected-orders-table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>No</th>
                                            <th>Sales/Purchase Order Number</th>
                                            <th>Type</th>
                                            <th>Date</th>
                                            <th>Customer/Supplier Name</th>
                                            <th>Total</th>
                                            <th>Address</th>
                                            <th>Discount</th>
                                            <th>Note</th>
                                            <th>Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            @endif
                        </div>
                    </div>


                    {{-- Summary Section --}}
                    <div class="summary-section">
                        <h6 class="mb-3">
                            Billing Summary
                            <span class="badge bg-secondary ms-2" id="discount-savings-badge" style="display: none;">
                                Total Discount: Rp <span id="total-savings">0</span>
                            </span>
                        </h6>
                        <div class="row">
                            <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                                <div class="form-group local-forms">
                                    <label for="subtotal">Subtotal (Before Discounts)</label>
                                    <input type="text" class="form-control" id="subtotal" name="subtotal" readonly>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                                <div class="form-group local-forms">
                                    <label for="item-discounts-total">Total Item Discounts</label>
                                    <input type="text" class="form-control" id="item-discounts-total"
                                        name="item_discounts_total" readonly>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                                <div class="form-group local-forms">
                                    <label for="total-discount">
                                        Discount (Overall)
                                    </label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="total-discount"
                                            name="total_discount" placeholder="Enter additional discount amount"
                                            value="0">
                                        <button class="btn btn-outline-secondary" type="button"
                                            id="reset-total-discount" title="Reset to 0">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                                <div class="form-group local-forms">
                                    <label for="grand-total">
                                        <strong>Grand Total</strong>
                                        <i class="fas fa-info-circle text-primary ms-1" data-bs-toggle="tooltip"
                                            data-bs-placement="top"
                                            title="Grand Total = Subtotal - Item Discounts - Additional Discount"></i>
                                    </label>
                                    <input type="text" class="form-control font-weight-bold" id="grand-total"
                                        name="grand_total" readonly
                                        style="font-weight: bold; font-size: 1.1em; background-color: #e8f5e8;">
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                                <div class="form-group local-forms">
                                    <label for="status">Status <span class="login-danger">*</span></label>
                                    <select class="form-control select" id="status" name="status" required>
                                        <option value="draft"
                                            {{ (isset($data['billing']) && $data['billing']->status == 'draft') || !isset($data['billing']) ? 'selected' : '' }}>
                                            Draft</option>
                                        <option value="posted"
                                            {{ isset($data['billing']) && $data['billing']->status == 'posted' ? 'selected' : '' }}>
                                            Posted</option>
                                        <option value="completed"
                                            {{ isset($data['billing']) && $data['billing']->status == 'completed' ? 'selected' : '' }}>
                                            Completed</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Additional Form Fields --}}
                    <div class="row mt-3">

                    </div>

                    {{-- Hidden Fields --}}
                    <input type="hidden" name="discount" id="discount-percentage" value="0">
                    <input type="hidden" name="discountprice" id="discount-price-hidden" value="0">
                    <input type="hidden" name="totalexpenses" id="total-expenses-hidden" value="0">

                    @if (isset($data['type']) && $data['type'] == 'edit')
                        <input type="hidden" name="_method" value="PUT">
                        <input type="hidden" name="id" value="{{ $data['billing']->id }}">
                    @endif

                    {{-- Submit Button --}}
                    <div class="row mt-4">
                        <div class="col-12 text-end">
                            <button type="button" class="btn btn-danger me-2" onclick="window.history.back()">
                                <i class="fas fa-arrow-left"></i> Back
                            </button>
                            <button type="submit" class="btn btn-success" id="btn-save-consignment">
                                <i class="fas fa-save"></i>
                                @if (isset($data['type']) && $data['type'] == 'edit')
                                    Update Billing
                                @else
                                    Save Billing
                                @endif
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>


    {{-- Modal Show Invoice --}}
    <div class="modal fade" id="modal-show-invoice" tabindex="-1" aria-labelledby="modal-show-invoice-label"
        aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-title">Select Orders</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <div class="container-fluid px-0">
                        <div class="row mb-3 align-items-end">
                            <div class="col-md-5">
                                <label for="filter-start-date" class="form-label mb-1">Start Date</label>
                                <input type="date" class="form-control" id="filter-start-date"
                                    name="filter_start_date" value="{{ date('Y-m-d', strtotime('-30 days')) }}">
                            </div>
                            <div class="col-md-5">
                                <label for="filter-end-date" class="form-label mb-1">End Date</label>
                                <input type="date" class="form-control" id="filter-end-date" name="filter_end_date"
                                    value="{{ date('Y-m-d') }}">
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle" id="table-orders">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width:40px;" class="text-center">
                                            <input type="checkbox" id="select-all-orders" class="form-check-input">
                                        </th>
                                        <th style="width:40px;" class="text-center">No</th>
                                        <th id="order-number-header">Order Number</th>
                                        <th>Date</th>
                                        <th id="customer-supplier-header">Customer/Supplier Name</th>
                                        <th>Ship To</th>
                                        <th class="text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- Data will be populated via AJAX DataTable --}}
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="btn-select-orders">Select Orders</button>
                </div>
            </div>
        </div>
    </div>


    @if (isset($data['type']) && $data['type'] == 'edit' && isset($data['billing']))
        <script>
            const isEditMode = true;
            const editDiscountPrice = {{ $data['billing']->discount_price ?? 0 }};
        </script>
    @else
        <script>
            const isEditMode = false;
            const editDiscountPrice = 0;
        </script>
    @endif

    <script>
        let ordersDataTable = null;
        const vendorSelect = $("#vendor");
        const shipToSelect = $("#ship_to");
        const btnFind = $('#btn-find-billing');
        const modalShowOrders = new bootstrap.Modal(document.getElementById('modal-show-invoice'));

        function initializeOrdersDataTable() {
            if (ordersDataTable) ordersDataTable.destroy();

            const shipToData = shipToSelect.select2('data')[0];
            const shipToType = shipToData ? shipToData.type : null;
            const shipToId = shipToData ? shipToData.id : null;

            // Update modal title and headers based on vendor type
            if (shipToType === 'customer') {
                $('#modal-title').text('Select Sales Orders');
                $('#order-number-header').text('Sales Order Number');
                $('#customer-supplier-header').text('Customer Name');
            } else if (shipToType === 'supplier') {
                $('#modal-title').text('Select Purchase Orders');
                $('#order-number-header').text('Purchase Order Number');
                $('#customer-supplier-header').text('Supplier Name');
            }

            ordersDataTable = $('#table-orders').DataTable({
                processing: true,
                serverSide: true,
                searching: true,
                paging: true,
                lengthChange: true,
                pageLength: 10,
                autoWidth: false,
                lengthMenu: [
                    [10, 25, 50, 100],
                    [10, 25, 50, 100]
                ],
                ajax: {
                    url: '/billing/orders/get',
                    type: 'POST',
                    data: function(d) {
                        d._token = '{{ csrf_token() }}';
                        d.ship_to_id = shipToId;
                        d.ship_to_type = shipToType;
                        d.start_date = $('#filter-start-date').val();
                        d.end_date = $('#filter-end-date').val();
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to load orders.'
                        });
                    }
                },
                columns: [{
                        data: 'checkbox',
                        name: 'checkbox',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'number',
                        name: 'number',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'order_number',
                        name: 'order_number',
                        className: 'text-center'
                    },
                    {
                        data: 'date',
                        name: 'date',
                        className: 'text-center'
                    },
                    {
                        data: 'customer_supplier_name',
                        name: 'customer_supplier_name'
                    },
                    {
                        data: 'shop_name',
                        name: 'shop_name'
                    },
                    {
                        data: 'total',
                        name: 'total',
                        className: 'text-end'
                    }
                ],
                order: [
                    [2, 'desc']
                ],
                language: {
                    processing: "Loading orders...",
                    emptyTable: "No orders found for selected vendor and date range",
                    zeroRecords: "No matching orders found"
                },
                drawCallback: function() {
                    updateSelectAllCheckbox();
                }
            });
        }

        function updateSelectAllCheckbox() {
            const total = $('#table-orders tbody .select-order').length;
            const checked = $('#table-orders tbody .select-order:checked').length;
            const $master = $('#select-all-orders');
            if (total === 0) {
                $master.prop({
                    indeterminate: false,
                    checked: false
                });
                return;
            }
            if (checked === total) $master.prop({
                indeterminate: false,
                checked: true
            });
            else if (checked > 0) $master.prop({
                indeterminate: true
            });
            else $master.prop({
                indeterminate: false,
                checked: false
            });
        }

        $('#select-all-orders').on('change', function() {
            const v = $(this).is(':checked');
            $('#table-orders tbody .select-order').prop('checked', v);
        });

        $('#table-orders').on('change', '.select-order', updateSelectAllCheckbox);

        btnFind.on('click', function() {
            const shipToData = shipToSelect.select2('data')[0];
            if (!shipToData || !shipToData.id) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Ship To Required',
                    text: 'Please select a Ship To first.'
                });
                return;
            }
            modalShowOrders.show();
            ordersDataTable ? ordersDataTable.ajax.reload() : initializeOrdersDataTable();
        });

        $('#filter-start-date, #filter-end-date').on('change', function() {
            if (ordersDataTable && shipToSelect.val()) ordersDataTable.ajax.reload();
        });

        shipToSelect.on('change', function() {
            if (ordersDataTable) ordersDataTable.ajax.reload();
        });

        $('#btn-select-orders').on('click', function() {
            const ids = [];
            let orderType = null;
            $('.select-order:checked').each(function() {
                ids.push($(this).data('id'));
                if (!orderType) {
                    orderType = $(this).data('type');
                }
            });
            if (ids.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'No Selection',
                    text: 'Please select at least one order.'
                });
                return;
            }

            const $btn = $(this).prop('disabled', true).html(
                '<i class="fas fa-spinner fa-spin"></i> Processing...');
            $.ajax({
                url: '/billing/orders/add-temp',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    order_ids: ids,
                    order_type: orderType
                },
                success: function(response) {
                    if (response.status === 'success') {
                        let no = 1;
                        response.data.forEach(function(order) {
                            const existingRow = $(
                                `#selected-orders-table tbody tr .subtotal[data-id="${order.id}"]`
                            );
                            if (existingRow.length === 0) {
                                const newRow = `
                                    <tr>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-danger delete-order-row">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                        <td>${no++}</td>
                                        <td>${order.order_number}</td>
                                        <td>${order.type}</td>
                                        <td>${order.date}</td>
                                        <td>${order.customer_supplier_name}</td>
                                        <td>${order.shop_name}</td>
                                        <td>
                                            <input type="number" class="form-control discount" data-id="${order.id}" value="0" min="0" step="0.01">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control subtotal" data-id="${order.id}" data-original-total="${order.total_raw}" value="${order.total_formatted}" readonly>
                                            <input type="hidden" name="invoice_ids[]" value="${order.id}">
                                        </td>
                                    </tr>
                                `;
                                $('#selected-orders-table tbody').append(newRow);
                            }
                        });
                        calculateTotals();
                        modalShowOrders.hide();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to add orders to billing.'
                    });
                },
                complete: function() {
                    $btn.prop('disabled', false).html('Select Orders');
                    $('#select-all-orders').prop('checked', false);
                }
            });
        });

        $('#selected-orders-table').on('click', '.delete-order-row', function() {
            $(this).closest('tr').remove();
            calculateTotals();
        });

        $('#selected-orders-table').on('input', '.discount', function() {
            const id = $(this).data('id');
            let disc = parseFloat($(this).val()) || 0;
            const $sub = $(`.subtotal[data-id="${id}"]`);
            const original = parseFloat($sub.attr('data-original-total')) || 0;

            if (disc < 0) {
                disc = 0;
                $(this).val(0);
                Swal.fire({
                    icon: 'warning',
                    title: 'Invalid Discount',
                    text: 'Discount cannot be negative.'
                });
            }
            if (disc > original) {
                disc = original;
                $(this).val(original);
                Swal.fire({
                    icon: 'warning',
                    title: 'Invalid Discount',
                    text: 'Discount cannot exceed the original order total.'
                });
            }

            const newSub = Math.max(0, original - disc);
            $sub.val(newSub.toLocaleString('id-ID'));
            calculateTotals();
        }).on('blur', '.discount', function() {
            let v = parseFloat($(this).val()) || 0;
            $(this).val(v);
        });

        function calculateTotals() {
            let subtotal = 0,
                rowDisc = 0,
                afterRow = 0;
            $('#selected-orders-table tbody tr').each(function() {
                const total = parseFloat($(this).find('.subtotal').attr('data-original-total')) || 0;
                const disc = parseFloat(($(this).find('.discount').val() || '0').replace(/[,.]/g, '')) || 0;
                const sub = Math.max(0, total - disc);
                subtotal += total;
                rowDisc += disc;
                afterRow += sub;
                $(this).find('.subtotal').val(sub.toLocaleString('id-ID'));
            });

            const addDisc = parseFloat($('#total-discount').val().replace(/[^\d]/g, '')) || 0;
            const grand = Math.max(0, afterRow - addDisc);
            const savings = rowDisc + addDisc;

            $('#subtotal').val(subtotal.toLocaleString('id-ID'));
            $('#item-discounts-total').val(rowDisc.toLocaleString('id-ID'));
            $('#grand-total').val(grand.toLocaleString('id-ID'));

            if (savings > 0) {
                $('#total-savings').text(savings.toLocaleString('id-ID'));
                $('#discount-savings-badge').show();
            } else {
                $('#discount-savings-badge').hide();
            }
        }

        $('#total-discount').on('input', function() {
            let v = $(this).val().replace(/[^\d]/g, '');
            $(this).val(v ? parseInt(v).toLocaleString('id-ID') : '0');
            calculateTotals();
        });

        $('#total-discount').on('blur', function() {
            // validasi terhadap subtotal setelah item discount
            let afterRow = 0;
            $('#selected-orders-table tbody tr').each(function() {
                const total = parseFloat($(this).find('.subtotal').attr('data-original-total')) || 0;
                const disc = parseFloat($(this).find('.discount').val()) || 0;
                afterRow += Math.max(0, total - disc);
            });
            let v = parseInt($(this).val().replace(/[^\d]/g, '')) || 0;
            if (v > afterRow) {
                v = afterRow;
                Swal.fire({
                    icon: 'warning',
                    title: 'Invalid Discount',
                    text: 'Total discount cannot exceed the subtotal after item discounts.'
                });
            }
            $(this).val(v.toLocaleString('id-ID'));
            calculateTotals();
        });

        $('#reset-total-discount').on('click', function() {
            $('#total-discount').val('0');
            calculateTotals();
        });

        $(document).ready(function() {
            $('[data-bs-toggle="tooltip"]').tooltip();

            // Initialize values for edit mode
            if (isEditMode) {
                $('#total-discount').val(editDiscountPrice.toLocaleString('id-ID'));
                calculateTotals();
            }
        });

        // ======= S A T U - S A T U N Y A  H A N D L E R  S U B M I T =======
        $('#quotation-form').on('submit', function(e) {
            e.preventDefault();

            if ($('#selected-orders-table tbody tr').length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'No Order Selected',
                    text: 'Please select at least one order to create billing.'
                });
                return;
            }

            const formData = new FormData(this);
            const isEdit = formData.has('_method') && formData.get('_method') === 'PUT';

            // angka diformat → balik ke numerik
            const totalDiscount = $('#total-discount').val().replace(/[,.]/g, '') || '0';
            formData.set('discountprice', totalDiscount);
            $('#discount-price-hidden').val(totalDiscount);

            const subtotal = $('#subtotal').val().replace(/[,.]/g, '') || '0';
            formData.set('subtotal', subtotal);

            const grandTotal = $('#grand-total').val().replace(/[,.]/g, '') || '0';
            formData.set('total', grandTotal);

            // totalexpenses diset 0 (sesuai hidden)
            formData.set('totalexpenses', '0');
            $('#total-expenses-hidden').val('0');

            // kirim array data orders
            const orderData = [];
            $('#selected-orders-table tbody tr').each(function() {
                const orderId = $(this).data('order-id');
                const orderType = $(this).data('order-type');
                const discount = parseFloat($(this).find('.discount').val()) || 0;
                if (orderId) {
                    orderData.push({
                        id: orderId,
                        type: orderType,
                        discount: discount
                    });
                }
            });

            formData.set('order_data', JSON.stringify(orderData));

            // Determine URL and method based on mode
            let url = '/billing/store';
            let method = 'POST';
            let actionText = 'create';
            let actioningText = 'Creating';
            let actionedText = 'created';

            if (isEdit) {
                const id = formData.get('id');
                url = `/billing/update/${id}`;
                method = 'POST';
                actionText = 'update';
                actioningText = 'Updating';
                actionedText = 'updated';

                formData.delete('_method');
            }

            $.ajax({
                url: url,
                method: method,
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function() {
                    $('#btn-save-consignment').prop('disabled', true).html(
                        `<i class="fas fa-spinner fa-spin"></i> ${actioningText}...`);
                },
                success: function(res) {
                    if (res.success) {
                        Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: `Billing ${actionedText} successfully!`
                            })
                            .then(() => {
                                window.location.href = '/billing';
                            });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: res.message || `Failed to ${actionText} Billing.`
                        });
                    }
                },
                error: function(xhr) {
                    const msg = xhr.responseJSON?.message ||
                        `Failed to ${actionText} Billing. Please try again.`;
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: msg
                    });
                },
                complete: function() {
                    const buttonText = isEdit ?
                        '<i class="fas fa-save"></i> Update Billing' :
                        '<i class="fas fa-save"></i> Save Billing';
                    $('#btn-save-billing').prop('disabled', false).html(buttonText);
                }
            });
        });

        $(document).ready(function() {
            // Initialize vendor select2
            $('#vendor').select2({
                placeholder: "Select Vendor",
                minimumInputLength: 1,
                ajax: {
                    url: "/billing/vendor/get",
                    dataType: "json",
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term
                        };
                    },
                    processResults: function(response) {
                        var items = (response && response.data) ? response.data : response;
                        return {
                            results: items.map(function(item) {
                                return {
                                    id: item.id,
                                    text: item.text || item.name || '',
                                    type: item.type,
                                    reference_type: item.reference_type || null,
                                };
                            })
                        };
                    },
                    cache: true
                },
                escapeMarkup: function(markup) {
                    return markup;
                },
                templateResult: function(repo) {
                    return repo.text;
                },
                templateSelection: function(repo) {
                    return repo.text;
                }
            });

            // Initialize ship to select2
            $('#ship_to').select2({
                placeholder: "Enter Ship To",
                minimumInputLength: 1,
                ajax: {
                    url: "/billing/shipto/get",
                    dataType: "json",
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term
                        };
                    },
                    processResults: function(response) {
                        var items = (response && response.data) ? response.data : response;
                        return {
                            results: items.map(function(item) {
                                return {
                                    id: item.id + '-' + item.reference_type,
                                    text: item.text || item.name || '',
                                    raw_id: item.id,
                                    type: item.type,
                                    reference_type: item.reference_type || null,
                                };
                            })
                        };
                    },
                    cache: true
                },
                escapeMarkup: function(markup) {
                    return markup;
                },
                templateResult: function(repo) {
                    return repo.text;
                },
                templateSelection: function(repo) {
                    return repo.text;
                }
            });
        });
    </script>
@endsection
