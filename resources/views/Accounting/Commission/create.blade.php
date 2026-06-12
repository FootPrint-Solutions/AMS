@extends('template.master')

@section('content')
    <style>
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
                    Commission
                </div>
                <br>

                {{-- Form --}}
                <form id="commission-form">
                    @csrf

                    {{-- Commission Number & Date --}}
                    <div class="row">
                        {{-- Billing Number --}}
                        <div class="col-3">
                            <div class="form-group local-forms">
                                <label for="billing-number">Commission Number <span class="login-danger">*</span></label>
                                <input type="text" class="form-control" id="billing-number" name="billingnumber"
                                    placeholder="Enter commission number" required readonly
                                    value="{{ isset($data['billing']) ? $data['billing']->billing_number : $data['commission_number'] ?? '' }}">
                            </div>
                        </div>

                        {{-- Date --}}
                        <div class="col-3">
                            <div class="form-group local-forms">
                                <label for="quotation-date">Commission Date <span class="login-danger">*</span></label>
                                <input type="date" class="form-control" id="quotation-date" name="date" required
                                    value="{{ isset($data['billing']) ? \Carbon\Carbon::parse($data['billing']->date)->format('Y-m-d') : date('Y-m-d') }}">
                            </div>
                        </div>

                        {{-- Shops --}}
                        <div class="col">
                            <div class="form-group local-forms">
                                <label for="distributor_shop">Distributor Shop <span class="login-danger">*</span>
                                    <i class="fas fa-info-circle ms-1 text-muted" data-toggle="tooltip" data-placement="top"
                                        title="This vendor data contains customer data."></i>
                                </label>
                                <select class="form-control" id="distributor_shop" name="distributor_shop" required>
                                    <option value="">Select Distributor Shop</option>
                                    @foreach ($data['distributor_shops'] as $shop)
                                        <option value="{{ $shop['id'] }}"
                                            {{ isset($data['billing']) && $data['billing']->distributor_shop_id == $shop['id'] ? 'selected' : '' }}>
                                            {{ $shop['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Find Orders Button --}}
                        @if (!(isset($data['type']) && $data['type'] == 'edit'))
                            <div class="col">
                                <div class="form-group local-forms">
                                    <button type="button" class="btn btn-primary btn-lg" id="btn-find-sales-orders">
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
                                <td colspan="8" class="h5 text-center">
                                    Selected Orders
                                </td>
                            </tr>

                            <tr class="text-center">
                                <td class="p-1 text-muted small">#</td>
                                <td class="p-1 text-muted small">No</td>
                                <td class="p-1 text-muted small">Sales Order Number</td>
                                <td class="p-1 text-muted small">Battery Name</td>
                                <td class="p-1 text-muted small">Commission Type</td>
                                <td class="p-1 text-muted small">Commission Value</td>
                                <td class="p-1 text-muted small">Debit Account</td>
                                <td class="p-1 text-muted small">Credit Account</td>
                            </tr>
                        </thead>

                        {{-- Body (Items) --}}
                        <tbody>
                        </tbody>
                        <tfoot>
                            {{-- Total --}}
                            <tr>
                                <td colspan="6"></td>
                                <td class="text-end">Total</td>
                                <td>
                                    <div class="input-group">
                                        <span class="input-group-text border-end">IDR</span>
                                        <input type="text" class="form-control text-end" id="total" name="total"
                                            value="0" required readonly>
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
                            Commission </button>

                            {{-- Cancel Button --}}
                            <button type="reset" class="btn btn-danger mx-1" id="btn-cancel">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    {{-- Modal Show Sales Orders Details --}}
    <div class="modal fade" id="modal-sales-orders" tabindex="-1" aria-labelledby="modal-sales-orders-label"
        aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-sales-orders-label">Select Sales Orders</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-striped" id="table-sales-orders" style="width:100%">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>#</th>
                                    <th>Sales Order Number</th>
                                    <th>Battery Name</th>
                                    <th>Include Installation</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="btn-add-selected-orders">Add Selected
                        Orders</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        var chartOfAccounts = @json($data['chart_of_accounts']);

        // btn-find-sales-orders
        $('#btn-find-sales-orders').on('click', function() {

            if (!$('#distributor_shop').val()) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Please select a distributor shop first',
                });
                return;
            }

            $('#modal-sales-orders').modal('show');

            if (!$.fn.DataTable.isDataTable('#table-sales-orders')) {
                $('#table-sales-orders').DataTable({
                    processing: true,
                    serverSide: false,
                    ajax: {
                        url: '{{ route('commission.get_sales_orders') }}',
                        data: function(d) {
                            d.distributor_shop_id = $('#distributor_shop').val();
                        },
                        dataSrc: 'data'
                    },
                    columns: [{
                            data: 'id',
                            render: function(data, type, row) {
                                return `<input type="checkbox" class="select-order" value="${data}">`;
                            },
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: null,
                            render: function(data, type, row, meta) {
                                return meta.row + 1;
                            },
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'sales_order.sales_order_number'
                        },
                        {
                            data: 'battery_name'
                        },
                        {
                            data: 'is_installation_included',
                            render: function(data) {
                                return Number(data) === 1 ? 'Yes' : 'No';
                            }
                        }
                    ]
                });
            } else {
                $('#table-sales-orders').DataTable().ajax.reload();
            }
        });

        // btn-add-selected-orders
        $('#btn-add-selected-orders').on('click', function() {
            var selectedOrders = [];
            $('#table-sales-orders tbody input.select-order:checked').each(function() {
                selectedOrders.push($(this).val());
            });

            if (selectedOrders.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Please select at least one sales order',
                });
                return;
            }

            // Fetch details of selected sales orders
            $.ajax({
                url: '{{ route('commission.get_sales_orders') }}',
                method: 'GET',
                data: {
                    distributor_shop_id: $('#distributor_shop').val(),
                    selected_order_ids: selectedOrders
                },
                success: function(response) {
                    var orders = response.data;
                    var tbody = $('#selected-orders-table tbody');
                    orders.forEach(function(order, index) {
                        var commissionTypeOptions = [
                            'Technical commission',
                            'Install Commission',
                            'PIC Commission',
                            'Pitstop Commission'
                        ];

                        var chartOfAccountOptions = chartOfAccounts.map(function(account) {
                            return `<option value="${account.id}">${account.name}</option>`;
                        }).join('');

                        // loop through commission types tr and create row for each commission type
                        commissionTypeOptions.forEach(function(commissionType) {

                            if (commissionType === 'Install Commission' && Number(order
                                    .is_installation_included) === 0) {
                                return;
                            }

                            var row = `
                                <tr>
                                    <td><button type="button" class="btn btn-sm btn-danger btn-remove-order"><i class="fas fa-trash"></i></button></td>
                                    <td>${index + 1}</td>
                                    <td>${order.sales_order.sales_order_number}</td>
                                    <td>${order.battery_name}</td>
                                    <td>${commissionType}
                                        <input type="hidden" name="sales_order_battery_id[]" value="${order.id}">
                                        <input type="hidden" name="commission_type[]" value="${commissionType}">
                                        </td>
                                    <td><input type="number" class="form-control form-control-sm commission-value" name="commission_value[]" value="0"></td>
                                    <td>
                                        <select class="form-control form-control-sm debit-account" name="debit_account[]">
                                            <option value="">Select Debit Account</option>
                                            ${chartOfAccountOptions}
                                        </select>
                                    </td>
                                    <td>
                                        <select class="form-control form-control-sm credit-account" name="credit_account[]">
                                            <option value="">Select Credit Account</option>
                                            ${chartOfAccountOptions}
                                        </select>
                                    </td>
                                </tr>
                            `;
                            tbody.append(row);
                        });
                    });
                    $('#modal-sales-orders').modal('hide');
                    updateTotal();
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Failed to fetch sales order details',
                    });
                }
            });
        });

        $(document).on('input', '.commission-value', function() {
            updateTotal();
        });

        function updateTotal() {
            var total = 0;
            $('.commission-value').each(function() {
                total += Number($(this).val());
            });
            $('#total').val(total);
        }
    </script>
@endsection
