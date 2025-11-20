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

                <form id="quotation-form" method="POST"
                    action="{{ isset($data['type']) && $data['type'] == 'edit' ? route('billing.update', $data['billing']->id) : route('billing.store') }}">
                    @csrf

                    {{-- Quotation Number & Date --}}
                    <div class="row mb-5">
                        {{-- Billing Number --}}
                        <div class="col">
                            <div class="form-group local-forms">
                                <label for="sales-consignment-number">Billing Number</label>
                                <input type="text" class="form-control" id="sales-consignment-number"
                                    name="billingnumber" placeholder="Enter Billingnumber" readonly
                                    value="{{ isset($data['billing']) ? $data['billing']->billing_number : $data['billing_number'] ?? '' }}">
                            </div>
                        </div>

                        {{-- Billing Date --}}
                        <div class="col">
                            <div class="form-group local-forms">
                                <label for="sales-consignment-date">Billing Date</label>
                                <input type="date" class="form-control" id="sales-consignment-date" name="billingdate"
                                    value="{{ isset($data['billing']) ? \Illuminate\Support\Carbon::parse($data['billing']->date)->format('Y-m-d') : (isset($data['consignment_date']) ? \Illuminate\Support\Carbon::parse($data['consignment_date'])->format('Y-m-d') : date('Y-m-d')) }}">
                            </div>
                        </div>

                        {{-- Vendor --}}
                        <div class="col">
                            <div class="form-group local-forms">
                                <label for="vendor">Vendor <span class="login-danger">*</span>
                                    <i class="fas fa-info-circle ms-1 text-muted" data-toggle="tooltip" data-placement="top"
                                        title="This vendor data contains shop data."></i>
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
                                        title="This vendor data contains customer or supplier data."></i>
                                </label>
                                <select class="form-control" id="ship_to" name="ship_to" required>
                                </select>
                            </div>
                        </div>

                        {{-- Button Find Billing --}}
                        @if (!(isset($data['type']) && $data['type'] == 'edit'))
                            <div class="col">
                                <div class="form-group local-forms">
                                    <button type="button" class="btn btn-primary" id="btn-find-billing">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="row mt-4">

                        {{-- Create Mode: Empty table for dynamic content --}}
                        <div class="table-responsive">
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
                                        <th style="min-width: 300px; width: 30%;">Discount</th>
                                        <th style="min-width: 300px; width: 30%;">Note</th>
                                        <th style="min-width: 300px; width: 30%;">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
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
                                        <input type="text" class="form-control" id="total-discount" name="total_discount"
                                            placeholder="Enter additional discount amount" value="0">
                                        <button class="btn btn-outline-secondary" type="button" id="reset-total-discount"
                                            title="Reset to 0">
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
                    <input type="hidden" name="status" value="draft">
                    <input type="hidden" name="subtotal" id="subtotal-hidden">
                    <input type="hidden" name="total" id="total-hidden">

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
            const billingId = {{ $data['billing']->id }};
        </script>
    @else
        <script>
            const isEditMode = false;
            const editDiscountPrice = 0;
            const billingId = null;
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

                    $('#table-orders tbody tr').off('click.rowCheckbox').on('click.rowCheckbox', function(e) {
                        if ($(e.target).is('input[type="checkbox"]')) return;

                        const $checkbox = $(this).find('.select-order');
                        if ($checkbox.length) {
                            $checkbox.prop('checked', !$checkbox.prop('checked')).trigger('change');
                        }
                    });
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
                                        <td>
                                            ${order.type === 'sales_order' ? 'Sales' : order.type === 'purchase_order' ? 'Purchase' : (order.type || '')}
                                            <input type="hidden" name="order_types[]" value="${order.type}">
                                            <input type="hidden" name="order_sources[]" value="${order.source}">
                                            <input type="hidden" name="order_numbers[]" value="${order.order_number}">
                                        </td>
                                        <td>${order.date}</td>
                                        <td>${order.customer_supplier_name}</td>
                                        <td class="text-end">Rp ${order.formatted_total}</td>
                                        <td>${order.shop_name}</td>
                                        <td>
                                            <input type="number" class="form-control discount" data-id="${order.id}" value="0" min="0" step="0.01">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" name="notes[]" placeholder="Enter note" value="">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control subtotal" data-id="${order.id}" data-original-total="${order.total}" value="${order.total_formatted}" readonly>
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

            // Update hidden fields
            $('#subtotal-hidden').val(subtotal);
            $('#total-hidden').val(grand);

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
                // Load existing billing data
                loadBillingData();
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

            const subtotal = $('#subtotal-hidden').val() || '0';
            formData.set('subtotal', subtotal);

            const grandTotal = $('#total-hidden').val() || '0';
            formData.set('total', grandTotal);

            // totalexpenses diset 0 (sesuai hidden)
            formData.set('totalexpenses', '0');
            $('#total-expenses-hidden').val('0');

            // kirim array data orders
            const orderTypes = [];
            const orderSources = [];
            const invoiceIds = [];
            const orderNumbers = [];
            const notes = [];
            const discounts = [];
            const subtotals = [];

            $('#selected-orders-table tbody tr').each(function() {
                const orderId = $(this).data('order-id');
                const orderType = $(this).data('order-type');
                const orderSource = $(this).data('order-source');
                const orderNumber = $(this).data('order-number');
                const note = $(this).find('input[name="notes[]"]').val() || '';
                const discount = $(this).find('.discount').val().replace(/[,.]/g, '') || '0';
                const subtotal = $(this).find('.subtotal').val().replace(/[,.]/g, '') || '0';

                if (orderId) {
                    invoiceIds.push(orderId);
                    orderTypes.push(orderType);
                    orderSources.push(orderSource);
                    orderNumbers.push(orderNumber);
                    notes.push(note);
                    discounts.push(discount);
                    subtotals.push(subtotal);
                }
            });

            // Set arrays in FormData
            formData.delete('order_types');
            formData.delete('order_sources');
            formData.delete('invoice_ids');
            formData.delete('order_numbers');
            formData.delete('notes');
            formData.delete('discounts');
            formData.delete('subtotals');

            orderTypes.forEach((type, index) => {
                formData.append('order_types[]', type);
            });
            orderSources.forEach((source, index) => {
                formData.append('order_sources[]', source);
            });
            invoiceIds.forEach((id, index) => {
                formData.append('invoice_ids[]', id);
            });
            orderNumbers.forEach((number, index) => {
                formData.append('order_numbers[]', number);
            });
            notes.forEach((note, index) => {
                formData.append('notes[]', note);
            });
            discounts.forEach((discount, index) => {
                formData.append('discounts[]', discount);
            });
            subtotals.forEach((subtotal, index) => {
                formData.append('subtotals[]', subtotal);
            });

            // Determine URL and method based on mode
            let url = $('#quotation-form').attr('action') || '/billing/store';
            let method = 'POST';
            let actionText = 'create';
            let actioningText = 'Creating';
            let actionedText = 'created';

            if (isEdit) {
                actionText = 'update';
                actioningText = 'Updating';
                actionedText = 'updated';
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
                    if (res.status === 'success') {
                        Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: res.message || `Billing ${actionedText} successfully!`
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
                    $('#btn-save-consignment').prop('disabled', false).html(buttonText);
                }
            });
        });

        // Function to load billing data for edit mode
        function loadBillingData() {
            if (!billingId) return;

            $.ajax({
                url: '/billing/data/' + billingId,
                method: 'GET',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.status === 'success' && response.data) {
                        const billing = response.data;

                        // Populate form fields
                        $('#billingnumber').val(billing.billing_number);
                        $('#billingdate').val(billing.date);

                        // Set vendor
                        if (billing.vendor && billing.vendor.id && billing.vendor.name) {
                            const vendorOption = new Option(billing.vendor.name, billing.vendor.id, true, true);
                            $('#vendor').append(vendorOption).trigger('change');
                        }

                        // Set ship to
                        if (billing.ship_to && billing.ship_to.id && billing.ship_to.name && billing.ship_to
                            .type) {
                            const shipToValue = billing.ship_to.id + '-' + billing.ship_to.type;
                            const shipToOption = new Option(billing.ship_to.name, shipToValue, true, true);
                            $('#ship_to').append(shipToOption).trigger('change');
                        }

                        // Populate selected orders table
                        if (billing.invoices && billing.invoices.length > 0) {
                            billing.invoices.forEach(function(invoice) {
                                addOrderToTable({
                                    id: invoice.invoice_id,
                                    type: invoice.invoice_type,
                                    source: invoice.invoice_source,
                                    order_number: invoice.invoice_number,
                                    date: invoice.date,
                                    total: invoice.total,
                                    discount: invoice.discount_price || 0,
                                    name: billing.ship_to.name,
                                    note: invoice.note || '',
                                });
                            });
                        }

                        // Set discount and calculate totals
                        $('#total-discount').val(billing.discount_price.toLocaleString('id-ID'));
                        calculateTotals();
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to load billing data.'
                    });
                }
            });
        }

        // Function to add order to selected orders table
        function addOrderToTable(orderData) {
            const tableBody = $('#selected-orders-table tbody');
            const rowId = 'order-' + orderData.id;
            const no = tableBody.children('tr').length + 1;

            // Check if row already exists
            if ($('#' + rowId).length > 0) return;

            const row = `
                <tr id="${rowId}" 
                    data-order-id="${orderData.id}" 
                    data-order-type="${orderData.type}" 
                    data-order-source="${orderData.source}" 
                    data-order-number="${orderData.order_number}">
                    <td></td>
                    <td>
                        ${no}
                    </td>
                    <td>${orderData.order_number}</td>
                    <td>
                        ${orderData.type === 'App\\Models\\Orders\\SalesOrder\\SalesOrderModel' ? 'Sales' : orderData.type === 'App\\Models\\Orders\\PurchaseOrder\\PurchaseOrderModel' ? 'Purchase' : (orderData.type || '')}
                        <input type="hidden" name="order_types[]" value="${orderData.type}">
                        <input type="hidden" name="order_sources[]" value="${orderData.source}">
                        <input type="hidden" name="order_numbers[]" value="${orderData.order_number}">
                    </td>
                    <td>${orderData.date ? new Date(orderData.date).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' }) : ''}</td>
                    <td>${orderData.name || ''}</td>
                    <td class="text-end">Rp ${orderData.total ? orderData.total.toLocaleString('id-ID') : '0'}</td>
                    <td>${orderData.shop_name || ''}</td>
                    <td>
                        <input type="number" class="form-control discount" data-id="${orderData.id}" value="${orderData.discount || 0}" min="0" step="0.01">
                    </td>
                    <td>
                        <input type="text" class="form-control" name="notes[]" placeholder="Enter note" value="${orderData.note || ''}">
                    </td>
                    <td>
                        <input type="text" class="form-control subtotal" data-id="${orderData.id}" data-original-total="${orderData.total || 0}" value="${orderData.total ? orderData.total.toLocaleString('id-ID') : '0'}" readonly>
                        <input type="hidden" name="invoice_ids[]" value="${orderData.id}">
                    </td>
                </tr>
            `;

            tableBody.append(row);
        }

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
