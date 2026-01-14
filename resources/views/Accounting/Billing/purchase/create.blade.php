@extends('template.master')

@section('content')
    {{-- @dd($data) --}}
    <link rel="stylesheet" href="{{ asset('plugins/bootstrap5-toggle/css/bootstrap5-toggle.min.css') }}">
    <style>
        #table-orders_filter {
            margin-top: 10px !important;
        }

        #table-purchase-orders_filter {
            margin-top: 10px !important;
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
                    Purchase Billing
                </div>
                <br>

                {{-- Form --}}
                <form id="quotation-form">
                    @csrf

                    {{-- Quotation Number & Date --}}
                    <div class="row">
                        {{-- Billing Number --}}
                        <div class="col">
                            <div class="form-group local-forms">
                                <label for="billing-number">Billing Number <span class="login-danger">*</span></label>
                                <input type="text" class="form-control" id="billing-number" name="billingnumber"
                                    placeholder="Enter billing number" required readonly
                                    value="{{ isset($data['billing']) ? $data['billing']->billing_number : $data['billing_number'] ?? '' }}">
                            </div>
                        </div>

                        {{-- Date --}}
                        <div class="col">
                            <div class="form-group local-forms">
                                <label for="quotation-date">Billing Date <span class="login-danger">*</span></label>
                                <input type="date" class="form-control" id="quotation-date" name="date" required
                                    value="{{ isset($data['billing']) ? \Carbon\Carbon::parse($data['billing']->date)->format('Y-m-d') : date('Y-m-d') }}">
                            </div>
                        </div>

                        {{-- Vendor --}}
                        <div class="col">
                            <div class="row">
                                <div class="col">
                                    <div class="form-group local-forms">
                                        <label for="vendor">Vendor <span class="login-danger">*</span>
                                            <i class="fas fa-info-circle ms-1 text-muted" data-toggle="tooltip"
                                                data-placement="top"
                                                title="This vendor data contains distributor or customer data."></i>
                                        </label>
                                        <select class="form-control" id="vendor" name="vendor" required>

                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Shops --}}
                        <div class="col">
                            <div class="form-group local-forms">
                                <label for="ship_to">Ship To <span class="login-danger">*</span>
                                    <i class="fas fa-info-circle ms-1 text-muted" data-toggle="tooltip" data-placement="top"
                                        title="This vendor data contains distributor shop data."></i>
                                </label>
                                <select class="form-control" id="ship_to" name="ship_to" required>
                                </select>
                            </div>
                        </div>

                        {{-- Find Orders Button --}}
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

                    {{-- Details --}}
                    <table class="table mb-2" id="selected-orders-table">
                        {{-- Header --}}
                        <thead>
                            <tr>
                                <td colspan="9" class="h5 text-center">
                                    Selected Orders
                                </td>
                            </tr>

                            <tr class="text-center">
                                <td class="p-1 text-muted small">#</td>
                                <td class="p-1 text-muted small">No</td>
                                <td class="p-1 text-muted small">Order Number</td>
                                <td class="p-1 text-muted small">Type</td>
                                <td class="p-1 text-muted small">Date</td>
                                <td class="p-1 text-muted small">Name</td>
                                <td class="p-1 text-muted small">Subtotal</td>
                                <td class="p-1 text-muted small">Discount</td>
                                <td class="p-1 text-muted small">Total</td>
                            </tr>
                        </thead>

                        {{-- Body (Items) --}}
                        <tbody>
                        </tbody>

                        {{-- Footer (Tax, Discount, Total) --}}
                        <tfoot>
                            {{-- Subtotal --}}
                            <tr>
                                <td colspan="7"></td>
                                <td class="text-end">Subtotal</td>
                                <td>
                                    <div class="input-group">
                                        <span class="input-group-text border-end">IDR</span>
                                        <input type="text" class="form-control text-end" id="subtotal" name="subtotal"
                                            value="0" readonly required>
                                    </div>
                                </td>
                            </tr>

                            {{-- Discount --}}
                            <tr>
                                <td colspan="7"></td>
                                <td class="text-end">Discount</td>
                                <td>
                                    <div class="row">
                                        <div class="col">
                                            {{-- Discount Price --}}
                                            <div class="input-group" id="discount-price">
                                                <span class="input-group-text border-end">IDR</span>
                                                <input type="text" class="form-control text-end"
                                                    id="discount-price-value" name="discountprice" value="0">
                                            </div>
                                        </div>

                                        <div class="col-sm-2">
                                            <input type="checkbox" id="toggle-discount" data-toggle="toggle" data-size="sm"
                                                data-offlabel="%" data-onlabel="IDR" checked readonly>
                                        </div>
                                    </div>
                                </td>
                            </tr>

                            {{-- Total --}}
                            <tr>
                                <td colspan="7"></td>
                                <td class="text-end">Total</td>
                                <td>
                                    <div class="input-group">
                                        <span class="input-group-text border-end">IDR</span>
                                        <input type="text" class="form-control text-end" id="total"
                                            name="total" value="0" required readonly>
                                    </div>
                                </td>
                            </tr>

                            {{-- Payment Method & Status --}}
                            <tr>
                                <td colspan="7"></td>
                                <td class="text-end">Payment Status
                                </td>
                                <td>
                                    <div class="row">
                                        <div class="col">
                                            <select name="status" id="status" class="form-control" required>
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
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                    <br>


                    {{-- Hidden Inputs --}}
                    @if (isset($data['type']) && $data['type'] == 'edit')
                        <input type="hidden" id="id" name="id" value="{{ $data['billing']->id }}">
                    @endif

                    {{-- Buttons --}}
                    <div class="d-flex flex-row-reverse">
                        {{-- Create Button --}}
                        <button type="submit" class="btn btn-success mx-1" id="btn-save"
                            @if (isset($data['type']) && $data['type'] == 'edit') value="update">
                                Update
                            @else
                                value="create">
                                Create @endif
                            Billing </button>

                            {{-- Cancel Button --}}
                            <button type="reset" class="btn btn-danger mx-1" id="btn-cancel">Cancel</button>
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
                        {{-- Filter Section --}}
                        <div class="row mb-3 align-items-end">
                            <div class="col-md-5">
                                <label for="filter-start-date" class="form-label mb-1">Start Date</label>
                                <input type="date" class="form-control" id="filter-start-date"
                                    name="filter_start_date">
                            </div>
                            <div class="col-md-5">
                                <label for="filter-end-date" class="form-label mb-1">End Date</label>
                                <input type="date" class="form-control" id="filter-end-date" name="filter_end_date">
                            </div>
                        </div>
                        <hr>

                        {{-- Purchase Orders Table --}}
                        <div>
                            <h5 class="mb-2">Purchase Orders</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover align-middle" id="table-purchase-orders">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width:40px;" class="text-center">
                                                <input type="checkbox" id="select-all-purchase-orders"
                                                    class="form-check-input">
                                            </th>
                                            <th style="width:40px;" class="text-center">No</th>
                                            <th id="purchase-order-number-header">Order Number</th>
                                            <th>Date</th>
                                            <th id="supplier-header">Name</th>
                                            <th>Ship To</th>
                                            <th class="text-end">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- Data populated via AJAX --}}
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Sales Orders Table --}}
                        <div class="mb-4">
                            <h5 class="mb-2">Sales Orders Recycle</h5>
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
                                            <th id="customer-supplier-header">Name</th>
                                            <th>Ship To</th>
                                            <th class="text-end">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- Data populated via AJAX --}}
                                    </tbody>
                                </table>
                            </div>
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
        let purchaseOrdersDataTable = null;
        const vendorSelect = $("#vendor");
        const shipToSelect = $("#ship_to");
        const btnFind = $('#btn-find-billing');
        const modalShowOrders = new bootstrap.Modal(document.getElementById('modal-show-invoice'));

        function getShipToData() {
            const selected = shipToSelect.select2('data');
            if (selected && selected.length > 0) {
                const idType = selected[0].id.split('-');
                return {
                    id: idType[0],
                    type: idType[1] || selected[0].type
                };
            }
            return {
                id: null,
                type: null
            };
        }

        function initializeOrdersDataTable() {
            if (ordersDataTable) ordersDataTable.destroy();

            const shipToData = getShipToData();
            const shipToType = shipToData.type;
            const shipToId = shipToData.id;

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
                        const shipToData = getShipToData();
                        d._token = '{{ csrf_token() }}';
                        d.ship_to_id = shipToData.id;
                        d.ship_to_type = shipToData.type;
                        d.start_date = $('#filter-start-date').val();
                        d.end_date = $('#filter-end-date').val();
                        d.type = 'recycle';
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

        function initializePurchaseOrdersDataTable() {
            const shipToData = getShipToData();
            const shipToType = shipToData.type;
            const shipToId = shipToData.id;

            purchaseOrdersDataTable = $('#table-purchase-orders').DataTable({
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
                    url: '/billing/purchase-orders/get',
                    type: 'POST',
                    data: function(d) {
                        const shipToData = getShipToData();
                        d._token = '{{ csrf_token() }}';
                        d.ship_to_id = shipToData.id;
                        d.ship_to_type = shipToData.type;
                        d.start_date = $('#filter-start-date').val();
                        d.end_date = $('#filter-end-date').val();
                        d.type = 'regular';
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to load purchase orders.'
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
                        name: 'supplier_name'
                    },
                    {
                        data: 'shop_name',
                        name: 'ship_to_name'
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
                    processing: "Loading purchase orders...",
                    emptyTable: "No purchase orders found for selected vendor and date range",
                    zeroRecords: "No matching purchase orders found"
                },
                drawCallback: function() {
                    updateSelectAllPurchaseOrdersCheckbox();

                    $('#table-purchase-orders tbody tr').off('click.rowCheckbox').on('click.rowCheckbox',
                        function(e) {
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

        function updateSelectAllPurchaseOrdersCheckbox() {
            const total = $('#table-purchase-orders tbody .select-order').length;
            const checked = $('#table-purchase-orders tbody .select-order:checked').length;
            const $master = $('#select-all-purchase-orders');
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

        $('#select-all-purchase-orders').on('change', function() {
            const v = $(this).is(':checked');
            $('#table-purchase-orders tbody .select-order').prop('checked', v);
        });

        $('#table-orders').on('change', '.select-order', updateSelectAllCheckbox);
        $('#table-purchase-orders').on('change', '.select-order', updateSelectAllPurchaseOrdersCheckbox);

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
            purchaseOrdersDataTable ? purchaseOrdersDataTable.ajax.reload() : initializePurchaseOrdersDataTable();
        });

        $('#filter-start-date, #filter-end-date').on('change', function() {
            if (ordersDataTable && shipToSelect.val()) ordersDataTable.ajax.reload();
            if (purchaseOrdersDataTable && shipToSelect.val()) purchaseOrdersDataTable.ajax.reload();
        });

        shipToSelect.on('change', function() {
            if (ordersDataTable) ordersDataTable.ajax.reload();
            if (purchaseOrdersDataTable) purchaseOrdersDataTable.ajax.reload();
        });

        $('#btn-select-orders').on('click', function() {
            const ids = [];

            $('#table-orders .select-order:checked, #table-purchase-orders .select-order:checked').each(function() {
                const id = $(this).data('id');
                const type = $(this).data('type');
                console.log('Selected order:', {
                    id,
                    type
                }); // Debug log
                ids.push({
                    id: id,
                    type: type
                });
            });

            console.log('All selected orders:', ids); // Debug log

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

            const requestData = {
                _token: '{{ csrf_token() }}',
                order_ids: ids.map(item => item.id),
                order_types: ids.map(item => item.type)
            };

            console.log('Request data:', requestData); // Debug log

            $.ajax({
                url: '/billing/orders/add-temp',
                method: 'POST',
                data: requestData,
                success: function(response) {
                    console.log('Response from server:', response); // Debug log
                    if (response.status === 'success') {
                        let no = $('#selected-orders-table tbody tr').length + 1;
                        response.data.forEach(function(order) {
                            console.log('Processing order:', order); // Debug log
                            // Check jika order sudah ada
                            if ($(
                                    `#selected-orders-table tbody tr .subtotal[data-id="${order.id}"]`
                                )
                                .length > 0) {
                                return; // Skip jika sudah ada
                            }

                            let orderType = '';
                            let orderTotal = order.total;
                            let formattedTotal = order.formatted_total;

                            if (order.type === 'sales_order') {
                                orderType = 'Sales';
                                // Make total negative for purchase
                                orderTotal = -Math.abs(order.total);
                                formattedTotal = 'Rp -' + Math.abs(order.total).toLocaleString(
                                    'id-ID');
                            } else if (order.type === 'purchase_order') {
                                orderType = 'Purchase';
                            } else {
                                orderType = order.type || '';
                            }

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
                                        ${orderType}
                                        <input type="hidden" name="order_types[]" value="${order.type}">
                                        <input type="hidden" name="order_sources[]" value="${order.source}">
                                        <input type="hidden" name="order_numbers[]" value="${order.order_number}">
                                    </td>
                                    <td>${order.date}</td>
                                    <td>${order.customer_supplier_name}</td>
                                    <td class="text-end">
                                        ${formattedTotal}
                                        <input type="hidden" class="subtotal" data-id="${order.id}" data-original-subtotal="${orderTotal}">
                                    </td>
                                    <td>
                                        <input type="number" class="form-control discount" data-id="${order.id}" value="0" min="0" step="0.01">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control total" data-id="${order.id}" value="${orderTotal.toLocaleString('id-ID')}" readonly>
                                        <input type="hidden" name="invoice_ids[]" value="${order.id}">
                                    </td>
                                </tr>
                            `;
                            $('#selected-orders-table tbody').append(newRow);
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
                    $('#select-all-purchase-orders').prop('checked', false);
                }
            });
        });

        // Delete order row
        $('#selected-orders-table').on('click', '.delete-order-row', function() {
            $(this).closest('tr').remove();

            // Update nomor urut
            $('#selected-orders-table tbody tr').each(function(index) {
                $(this).find('td:eq(1)').text(index + 1);
            });

            calculateTotals();
        });

        $('#selected-orders-table').on('input', '.discount', function() {
            const $row = $(this).closest('tr');
            const id = $(this).data('id');

            let discount = parseFloat($(this).val()) || 0;

            const $subtotal = $row.find('.subtotal[data-id="' + id + '"]');
            const originalSubtotal = parseFloat($subtotal.attr('data-original-subtotal')) || 0;

            // Validasi discount
            if (discount < 0) {
                discount = 0;
                $(this).val(0);
                Swal.fire({
                    icon: 'warning',
                    title: 'Invalid Discount',
                    text: 'Discount cannot be negative.'
                });
            }

            let newTotal;
            if (originalSubtotal < 0) {
                newTotal = originalSubtotal + discount;
            } else {
                newTotal = originalSubtotal - discount;
            }

            $row.find('.total[data-id="' + id + '"]')
                .val(newTotal.toLocaleString('id-ID'));

            calculateTotals();
        });

        function calculateTotals() {
            let subtotal = 0;

            $('#selected-orders-table tbody tr').each(function() {
                const total = parseFloat($(this).find('.total').val().replace(/[^-\d]/g, '')) || 0;
                console.log('Row Total:', total);
                subtotal += total;
            });

            $('#subtotal').val(subtotal.toLocaleString('id-ID'));

            const overallDiscount = parseFloat($('#discount-price-value').val().replace(/[^\d]/g, '')) || 0;
            const grandTotal = Math.max(0, subtotal - overallDiscount);
            $('#total').val(grandTotal.toLocaleString('id-ID'));

            console.log({
                subtotal,
                overallDiscount,
                grandTotal
            });
        }


        $('#discount-price-value').on('input', function() {
            const val = parseInt($(this).val().replace(/[^\d]/g, '')) || 0;
            $(this).val(val.toLocaleString('id-ID'));
            calculateTotals();
        });

        // Form Submit Handler
        $('#quotation-form').on('submit', function(e) {
            e.preventDefault();

            // Validasi minimal 1 order
            if ($('#selected-orders-table tbody tr').length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'No Order Selected',
                    text: 'Please select at least one order to create billing.'
                });
                return;
            }

            const formData = new FormData(this);
            const isEdit = $('#btn-save').val() === 'update';

            // Set nilai subtotal dan total
            const subtotal = parseFloat($('#subtotal').val().replace(/[^\d]/g, '')) || 0;
            const overallDiscount = parseFloat($('#discount-price-value').val().replace(/[^\d]/g, '')) || 0;
            const grandTotal = parseFloat($('#total').val().replace(/[^\d]/g, '')) || 0;

            formData.set('subtotal', subtotal);
            formData.set('discountprice', overallDiscount);
            formData.set('total', grandTotal);

            // Kumpulkan data orders
            const ordersData = [];
            $('#selected-orders-table tbody tr').each(function() {
                const $row = $(this);
                const invoiceId = $row.find('input[name="invoice_ids[]"]').val();

                if (invoiceId) {
                    ordersData.push({
                        invoice_id: invoiceId,
                        order_type: $row.find('input[name="order_types[]"]').val(),
                        order_source: $row.find('input[name="order_sources[]"]').val(),
                        order_number: $row.find('input[name="order_numbers[]"]').val(),
                        discount: parseFloat($row.find('.discount').val()) || 0,
                        subtotal: parseFloat($row.find('.subtotal').attr(
                            'data-original-subtotal')) || 0,
                        total: parseFloat($row.find('.total').val().replace(/[^-\d]/g, '')) ||
                            0
                    });
                }
            });

            // Append orders data ke FormData
            formData.delete('order_types[]');
            formData.delete('order_sources[]');
            formData.delete('invoice_ids[]');
            formData.delete('order_numbers[]');
            formData.delete('discounts[]');
            formData.delete('subtotals[]');
            formData.delete('totals[]');

            ordersData.forEach((order) => {
                formData.append('invoice_ids[]', order.invoice_id);
                formData.append('order_types[]', order.order_type);
                formData.append('order_sources[]', order.order_source);
                formData.append('order_numbers[]', order.order_number);
                formData.append('discounts[]', order.discount);
                formData.append('subtotals[]', order.subtotal);
                formData.append('totals[]', order.total);
            });

            const url = isEdit ? '/billing/update' : '/billing/store';
            const actionText = isEdit ? 'update' : 'create';
            const actioningText = isEdit ? 'Updating' : 'Creating';

            $.ajax({
                url: url,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function() {
                    $('#btn-save').prop('disabled', true).html(
                        `<i class="fas fa-spinner fa-spin"></i> ${actioningText}...`);
                },
                success: function(res) {
                    if (res.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: res.message || `Billing ${actionText}d successfully!`
                        }).then(() => {
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
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: xhr.responseJSON?.message ||
                            `Failed to ${actionText} Billing. Please try again.`
                    });
                },
                complete: function() {
                    const buttonText = isEdit ? 'Update' : 'Create';
                    $('#btn-save').prop('disabled', false).html(buttonText + ' Billing');
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
                                    order_number: invoice.invoice_number,
                                    type: invoice.invoice_type,
                                    source: invoice.invoice_type,
                                    date: invoice.date,
                                    name: invoice.invoice_name,
                                    subtotal: invoice.subtotal,
                                    discount_price: invoice.discount_price,
                                    total: invoice.subtotal - invoice.discount_price
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
                    <td class="text-end">
                        Rp ${orderData.subtotal.toLocaleString('id-ID')}
                        <input type="hidden" class="subtotal" data-id="${orderData.id}" data-original-subtotal="${orderData.subtotal}">
                    </td>
                    <td>
                        <input type="number" class="form-control discount" data-id="${orderData.id}" value="${orderData.discount_price}" min="0" step="0.01">
                    </td>
                    <td>
                        <input type="text" class="form-control total" data-id="${orderData.id}" value="${(orderData.subtotal - orderData.discount_price).toLocaleString('id-ID')}" readonly>
                        <input type="hidden" name="invoice_ids[]" value="${orderData.id}">
                    </td>
                </tr>
            `;

            tableBody.append(row);
        }

        $(document).ready(function() {
            // Initialize Select2
            $('#vendor').select2({
                placeholder: "Select Vendor",
                minimumInputLength: 1,
                ajax: {
                    url: "/billing/vendor/get",
                    dataType: "json",
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term,
                            type: 'distributorshop'
                        };
                    },
                    processResults: function(response) {
                        const items = (response && response.data) ? response.data : response;
                        return {
                            results: items.map(function(item) {
                                const typeBadge = item.type ?
                                    `<span class="badge bg-info ms-2">${item.type.charAt(0).toUpperCase() + item.type.slice(1)}</span>` :
                                    '';
                                return {
                                    id: item.id,
                                    text: (item.text || item.name || '') + ' ' + typeBadge,
                                    type: item.type
                                };
                            })
                        };
                    },
                    cache: true
                },
                escapeMarkup: function(markup) {
                    return markup;
                }
            });

            $('#ship_to').select2({
                placeholder: "Enter Ship To",
                minimumInputLength: 1,
                ajax: {
                    url: "/billing/shipto/get",
                    dataType: "json",
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term,
                            type: 'distributorshop'
                        };
                    },
                    processResults: function(response) {
                        const items = (response && response.data) ? response.data : response;
                        return {
                            results: items.map(function(item) {
                                const typeBadge = item.type ?
                                    `<span class="badge bg-info ms-2">${item.type.charAt(0).toUpperCase() + item.type.slice(1)}</span>` :
                                    '';
                                return {
                                    id: item.id + '-' + item.reference_type,
                                    text: (item.text || item.name || '') + ' ' + typeBadge,
                                    type: item.type,
                                    reference_type: item.reference_type
                                };
                            })
                        };
                    },
                    cache: true
                },
                escapeMarkup: function(markup) {
                    return markup;
                }
            });

            // Initialize values for edit mode
            if (isEditMode) {
                $('#discount-price-value').val(editDiscountPrice.toLocaleString('id-ID'));
                loadBillingData();
            }
        });

        // Cancel button handler
        $("#btn-cancel").on("click", function() {
            window.location.href = '/billing';
        });

        document.title = "Purchase Billing - AMS";
    </script>
@endsection
