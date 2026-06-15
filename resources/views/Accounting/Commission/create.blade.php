@extends('template.master')

@section('content')
    <style>
        .dataTables_filter {
            margin-top: 0px;
        }

        .checkbox {
            width: 20px;
            height: 20px;
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
                <form id="commission-form" method="POST"
                    @if (isset($data['type']) && $data['type'] == 'edit') action="{{ route('commission.update') }}"
                @else action="{{ route('commission.store') }}" @endif>
                    @csrf

                    {{-- Commission Number & Date --}}
                    <div class="row">
                        {{-- Billing Number --}}
                        <div class="col-3">
                            <div class="form-group local-forms">
                                <label for="commission-number">Commission Number <span class="login-danger">*</span></label>
                                <input type="text" class="form-control" id="commission-number" name="commission_number"
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
                                <td colspan="7" class="h5 text-center">
                                    Selected Orders
                                </td>
                            </tr>
                            <tr>
                                <td colspan="6"></td>
                                <td>
                                    <select class="form-control form-control-sm" id="mass-credit-account">
                                        <option value="">Change All Credit Account</option>
                                        @foreach ($data['chart_of_accounts'] as $account)
                                            <option value="{{ $account['id'] }}">{{ $account['number'] }} -
                                                {{ $account['name'] }}</option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                            <tr class="text-center">
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
                                <td colspan="5"></td>
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
                            <a type="reset" class="btn btn-danger mx-1" id="btn-cancel"
                                href="{{ route('commission.index') }}">Cancel</a>
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
                    {{-- Filter Date --}}
                    <div class="row">
                        <div class="col-3">
                            <div class="form-group local-forms">
                                <label for="filter-start-date">Start Date</label>
                                <input type="date" class="form-control" id="filter-start-date"
                                    name="filter_start_date" value="{{ date('Y-m-d', strtotime('-1 month')) }}">
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="form-group local-forms">
                                <label for="filter-end-date">End Date</label>
                                <input type="date" class="form-control" id="filter-end-date" name="filter_end_date"
                                    value="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                    </div>



                    <div class="table-responsive">
                        <table class="table table-striped" id="table-sales-orders" style="width:100%">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>#</th>
                                    <th>Sales Order Number</th>
                                    <th>Sales Order Date</th>
                                    <th>Customer Name</th>
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
                            d.filter_start_date = $('#filter-start-date').val();
                            d.filter_end_date = $('#filter-end-date').val();
                        },
                        dataSrc: 'data'
                    },
                    columns: [{
                            data: 'id',
                            render: function(data, type, row) {
                                return `<input type="checkbox" class="select-order checkbox" value="${data}">`;
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
                            data: 'sales_order.date',
                            render: function(data) {
                                return new Date(data).toLocaleDateString('en-GB', {
                                    day: '2-digit',
                                    month: 'short',
                                    year: 'numeric'
                                });
                            }
                        },
                        {
                            data: 'sales_order.customer.name'
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
                    ],

                    // when row is clicked, toggle the checkbox
                    'createdRow': function(row, data, dataIndex) {
                        $(row).on('click', function(e) {
                            if (e.target.type !== 'checkbox') {
                                var checkbox = $(this).find('input.select-order');
                                checkbox.prop('checked', !checkbox.prop('checked'));
                            }
                        });
                    }
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
                        var shopAccounts = (order.sales_order && order.sales_order.shop && Array
                                .isArray(order.sales_order.shop.accounts)) ? order.sales_order
                            .shop.accounts : [];
                        var technicianAccount = shopAccounts.find(function(account) {
                            return account.type === 'technician';
                        });
                        var picAccount = shopAccounts.find(function(account) {
                            return account.type === 'pic';
                        });
                        var pitStopAccount = shopAccounts.find(function(account) {
                            return account.type === 'pit_stop';
                        });

                        var commissionTypeOptions = [
                            'Technical commission',
                            'Install Commission',
                            'PIC Commission',
                            'Pitstop Commission'
                        ];

                        var chartOfAccountOptions = chartOfAccounts.map(function(account) {
                            return `<option value="${account.id}">${account.number} - ${account.name}</option>`;
                        }).join('');

                        commissionTypeOptions.forEach(function(commissionType) {
                            var no = tbody.find('tr').length + 1;

                            if (commissionType === 'Install Commission' && Number(order
                                    .is_installation_included) === 0) {
                                return;
                            }

                            var technicalCommissionValue = technicianAccount ?
                                technicianAccount.commission : 0;
                            var installCommissionValue = 10000;
                            var picCommissionValue = picAccount ? picAccount
                                .commission : 0;
                            var pitstopCommissionValue = pitStopAccount ? pitStopAccount
                                .commission : 0;
                            var selectedAccountId = commissionType ===
                                'Technical commission' ? (technicianAccount ?
                                    technicianAccount.chart_of_account_id : '') :
                                commissionType === 'PIC Commission' ? (picAccount ?
                                    picAccount.chart_of_account_id : '') :
                                commissionType === 'Pitstop Commission' ? (
                                    pitStopAccount ? pitStopAccount
                                    .chart_of_account_id : '') : '';

                            var debitAccountOptions = chartOfAccountOptions.replace(
                                new RegExp(`value="${selectedAccountId}"`),
                                '$& selected');
                            var creditAccountOptions = chartOfAccountOptions.replace(
                                new RegExp(`value="${selectedAccountId}"`),
                                '$& selected');

                            var row = `
                                <tr>
                                    <td>${no}</td>
                                    <td>${order.sales_order.sales_order_number}</td>
                                    <td>${order.battery_name}</td>
                                    <td>${commissionType}
                                        <input type="hidden" name="sales_order_battery_id[]" value="${order.id}">
                                        <input type="hidden" name="commission_type[]" value="${commissionType}">
                                        </td>
                                    <td>
                                        <div class="input-group">
                                            <span class="input-group-text border-end">IDR <span class="login-danger">*</span></span>
                                        <input type="number" class="form-control  commission-value" name="commission_value[]" value="${commissionType === 'Technical commission' ? technicalCommissionValue : commissionType === 'Install Commission' ? installCommissionValue : commissionType === 'PIC Commission' ? picCommissionValue : commissionType === 'Pitstop Commission' ? pitstopCommissionValue : 0}" required>
                                        </div>
                                    </td>
                                    <td>
                                        <select class="form-control  debit-account" name="debit_account[]" required >
                                            <option value="">Select Debit Account</option>
                                            ${debitAccountOptions}
                                        </select>
                                    </td>
                                    <td>
                                        <div class="row">
                                        <div class="col">
                                        <select class="form-control  credit-account" name="credit_account[]" required>
                                            <option value="">Select Credit Account</option>
                                            ${chartOfAccountOptions}
                                        </select>
                                        </div>
                                        <div class="col-sm-2 d-flex align-items-center">
                                             <button type="button" class="btn btn-sm btn-danger btn-remove-order"><i class="fas fa-xmark"></i></button>
                                        </div>
                                        </div>
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

        $('#mass-credit-account').on('change', function() {
            var selectedAccountId = $(this).val();
            if (selectedAccountId) {
                $('.credit-account').val(selectedAccountId);
            }
        });

        $(document).on('click', '.btn-remove-order', function() {
            $(this).closest('tr').remove();
            updateTotal();
        });

        $(document).on('input', '.commission-value', function() {
            updateTotal();
        });

        $('#filter-start-date, #filter-end-date').on('change', function() {
            if ($('#modal-sales-orders').hasClass('show')) {
                $('#table-sales-orders').DataTable().ajax.reload();
            }
        });

        function updateTotal() {
            var total = 0;
            $('.commission-value').each(function() {
                total += Number($(this).val());
            });
            total = total.toLocaleString('id-ID');
            $('#total').val(total);
        }

        $('#commission-form').on('submit', function(e) {
            e.preventDefault();
            $('#btn-save').prop('disabled', true);


            if ($('#selected-orders-table tbody tr').length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Please add at least one sales order',
                });
                $('#btn-save').prop('disabled', false);
                return;
            }

            var formData = $(this).serialize();

            Swal.fire({
                title: 'Saving commission...',
                allowOutsideClick: false,
                didOpen: function() {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: formData,
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: response.message,
                    }).then(function() {
                        window.location.href = '{{ route('commission.index') }}';
                    });
                },
                error: function(xhr) {
                    var errorMessage = 'An error occurred while saving the commission';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: errorMessage,
                    });

                    $('#btn-save').prop('disabled', false);
                }
            });
        });
    </script>
@endsection
