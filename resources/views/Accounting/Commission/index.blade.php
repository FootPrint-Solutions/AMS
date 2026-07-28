@extends('template.master')

@section('content')
    <div class="card shadow-lg mb-4">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title mb-0">Commission List</h3>
                </div>
                <div class="col-auto ms-auto text-end">
                    <button type="button" id="btn-sync-battery" class="btn btn-outline-warning btn-sm me-1">
                        <i class="fas fa-sync"></i> Sync Battery
                    </button>
                    <a href="{{ route('commission.create') }}" id="btn-add" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Add New Commission
                    </a>
                </div>
            </div>
        </div>

        <div class="card-body">
            <form id="filter-form" class="row align-items-center mb-3">
                <label class="col-md-1 col-form-label fw-bold">Date</label>
                <div class="col-md-4">
                    <div class="row">
                        <div class="col-5 pe-0">
                            <input type="date" class="form-control" id="input-commission-date-start">
                        </div>
                        <div class="col-2 text-center px-0 align-self-center">to</div>
                        <div class="col-5 ps-0">
                            <input type="date" class="form-control" id="input-commission-date-end">
                        </div>
                    </div>
                </div>

                <label class="col-md-1 col-form-label fw-bold">Status</label>
                <div class="col-md-2">
                    <select class="form-select" id="commission-status-filter">
                        <option value="all">All</option>
                        <option value="draft">Draft</option>
                        <option value="post">Posted</option>
                    </select>
                </div>

                <label class="col-md-2 col-form-label fw-bold">Distributor Shop</label>
                <div class="col-md-2">
                    <select class="form-select" id="distributor-shop-filter">
                        <option value="all">All</option>
                        @foreach ($data['DistributorShops'] as $shop)
                            <option value="{{ $shop['id'] }}">{{ $shop['name'] }}</option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-lg mb-4">
        <div class="card-body">
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-striped" id="table-commission" style="width:100%">
                    <thead>
                        <tr>
                            <th></th>
                            <th>#</th>
                            <th>Commission Number</th>
                            <th>Distributor Shop</th>
                            <th>Date</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <script>
        let table;

        $(document).ready(function() {
            const toCurrency = function(amount) {
                if (typeof formatCurrency === 'function') {
                    return formatCurrency(amount);
                }

                const number = Number(amount || 0);
                return new Intl.NumberFormat('id-ID').format(number);
            };

            table = $('#table-commission').DataTable({
                lengthMenu: [
                    [5, 10, 25],
                    [5, 10, 25]
                ],
                pageLength: 10,
                responsive: true,
                processing: true,
                serverSide: true,
                order: [
                    [1, 'desc']
                ],
                ajax: {
                    url: '/commission/show',
                    type: 'POST',
                    data: function(d) {
                        d._token = '{{ csrf_token() }}';
                        d.status = $('#commission-status-filter').val();
                        d.distributorShop = $('#distributor-shop-filter').val();
                        d.dateStart = $('#input-commission-date-start').val();
                        d.dateEnd = $('#input-commission-date-end').val();
                    }
                },
                columns: [{
                        targets: 0,
                        className: 'dt-control',
                        orderable: false,
                        data: null,
                        defaultContent: '',
                    },
                    {
                        data: 1,
                        orderable: false,
                        className: 'dt-body-center'
                    },
                    {
                        data: 2
                    },
                    {
                        data: 3,
                        orderable: false,
                    },
                    {
                        data: 4,
                        render: function(data) {
                            if (!data) return '-';
                            const date = new Date(data);
                            return date.toLocaleDateString('id-ID', {
                                day: '2-digit',
                                month: 'short',
                                year: 'numeric'
                            });
                        }
                    },
                    {
                        data: 5,
                        className: 'text-end',
                    },
                    {
                        data: 6,
                        className: 'dt-body-center'
                    }
                ],
                dom: 'lBfrtip',
                buttons: [{
                        text: "<i class='fas fa-pencil'></i> Edit",
                        className: 'btn btn-outline-primary btn-sm',
                        action: function() {
                            const selectedRows = table.rows({
                                selected: true
                            }).data().toArray();
                            if (selectedRows.length !== 1) {
                                Swal.fire('Error', 'Please select one commission to edit.',
                                    'error');
                                return;
                            }

                            const id = selectedRows[0][0];
                            goToPage('/commission/edit/' + id);
                        }
                    },
                    {
                        text: "<i class='fas fa-trash'></i> Delete",
                        className: 'btn btn-outline-danger btn-sm',
                        action: function() {
                            const selectedRows = table.rows({
                                selected: true
                            }).data().toArray();
                            if (selectedRows.length === 0) {
                                Swal.fire('Error', 'Please select at least one commission.',
                                    'error');
                                return;
                            }

                            const ids = selectedRows.map(row => row[0]);

                            Swal.fire({
                                title: 'Delete Commission',
                                text: 'Are you sure you want to delete selected commission(s)?',
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonText: 'Yes, Delete',
                                cancelButtonText: 'Cancel',
                                confirmButtonColor: '#d33'
                            }).then((result) => {
                                if (!result.isConfirmed) {
                                    return;
                                }

                                $.ajax({
                                    url: '/commission/destroy',
                                    type: 'POST',
                                    data: {
                                        _token: '{{ csrf_token() }}',
                                        ids: ids
                                    },
                                    success: function(response) {
                                        if (typeof response === 'string') {
                                            try {
                                                response = JSON.parse(response);
                                            } catch (e) {
                                                Swal.fire('Error',
                                                    'Invalid response from server.',
                                                    'error');
                                                return;
                                            }
                                        }
                                        if (response.status === 'success' ||
                                            response.status === true) {
                                            Swal.fire('Deleted', response
                                                .message,
                                                'success');
                                            table.ajax.reload();
                                            return;
                                        } else if (response.status ===
                                            'error' ||
                                            response.status === false) {
                                            Swal.fire('Error', response
                                                .message ||
                                                'Failed to delete commission.',
                                                'error');
                                            return;
                                        }
                                    },
                                    error: function() {
                                        Swal.fire('Error',
                                            'Failed to delete commission.',
                                            'error');
                                    }
                                });
                            });
                        }
                    },
                    {
                        text: "<i class='fas fa-paper-plane'></i> Post",
                        className: 'btn btn-outline-success btn-sm',
                        action: function() {
                            const selectedRows = table.rows({
                                selected: true
                            }).data().toArray();
                            if (selectedRows.length === 0) {
                                Swal.fire('Error', 'Please select at least one commission.',
                                    'error');
                                return;
                            }

                            const ids = selectedRows.map(row => row[0]);

                            Swal.fire({
                                title: 'Post Commission',
                                text: 'Are you sure you want to post selected commission(s)?',
                                icon: 'question',
                                showCancelButton: true,
                                confirmButtonText: 'Yes, Post',
                                cancelButtonText: 'Cancel',
                                confirmButtonColor: '#3085d6'
                            }).then((result) => {
                                if (!result.isConfirmed) {
                                    return;
                                }

                                $.ajax({
                                    url: '/commission/post',
                                    type: 'POST',
                                    data: {
                                        _token: '{{ csrf_token() }}',
                                        ids: ids
                                    },
                                    success: function(response) {
                                        if (typeof response === 'string') {
                                            try {
                                                response = JSON.parse(response);
                                            } catch (e) {
                                                Swal.fire('Error',
                                                    'Invalid response from server.',
                                                    'error');
                                                return;
                                            }
                                        }
                                        if (response.status === 'success' ||
                                            response.status === true) {
                                            Swal.fire('Success', response
                                                .message, 'success');
                                            table.ajax.reload();
                                            return;
                                        }
                                        Swal.fire('Error', response.message,
                                            'error');
                                    },
                                    error: function() {
                                        Swal.fire('Error',
                                            'Failed to post commission.',
                                            'error');
                                    }
                                });
                            });
                        }
                    },
                    {
                        text: "<i class='fas fa-print'></i> Print Recap Pitstop",
                        className: 'btn btn-outline-info btn-sm',
                        action: function() {
                            const selectedRows = table.rows({ selected: true }).data().toArray();
                            
                            if (selectedRows.length > 0) {
                                let firstShop = selectedRows[0][3];
                                let firstDate = selectedRows[0][4];
                                let firstMonthYear = firstDate.split(' ').slice(1).join(' ');

                                for (let i = 1; i < selectedRows.length; i++) {
                                    let shop = selectedRows[i][3];
                                    let date = selectedRows[i][4];
                                    let monthYear = date.split(' ').slice(1).join(' ');

                                    if (shop !== firstShop) {
                                        Swal.fire('Error', 'Tidak bisa mencetak dari Distributor Shop yang berbeda secara bersamaan.', 'error');
                                        return;
                                    }
                                    if (monthYear !== firstMonthYear) {
                                        Swal.fire('Error', 'Tidak bisa mencetak dari bulan yang berbeda secara bersamaan.', 'error');
                                        return;
                                    }
                                }
                            }

                            const status = $('#commission-status-filter').val();
                            const distributorShop = $('#distributor-shop-filter').val();
                            const dateStart = $('#input-commission-date-start').val();
                            const dateEnd = $('#input-commission-date-end').val();

                            const url = new URL(window.location.origin + '/commission/print-pitstop');
                            url.searchParams.append('status', status);
                            url.searchParams.append('distributorShop', distributorShop);
                            url.searchParams.append('dateStart', dateStart);
                            url.searchParams.append('dateEnd', dateEnd);

                            const selectedRows = table.rows({ selected: true }).data().toArray();
                            if (selectedRows.length > 0) {
                                const ids = selectedRows.map(row => row[0]).join(',');
                                url.searchParams.append('ids', ids);
                            }

                            window.open(url.toString(), '_blank');
                        }
                    },
                    {
                        text: "<i class='fas fa-print'></i> Print Recap PIC & Technician",
                        className: 'btn btn-outline-info btn-sm',
                        action: function() {
                            const selectedRows = table.rows({ selected: true }).data().toArray();
                            
                            if (selectedRows.length > 0) {
                                let firstShop = selectedRows[0][3];
                                let firstDate = selectedRows[0][4];
                                let firstMonthYear = firstDate.split(' ').slice(1).join(' ');

                                for (let i = 1; i < selectedRows.length; i++) {
                                    let shop = selectedRows[i][3];
                                    let date = selectedRows[i][4];
                                    let monthYear = date.split(' ').slice(1).join(' ');

                                    if (shop !== firstShop) {
                                        Swal.fire('Error', 'Tidak bisa mencetak dari Distributor Shop yang berbeda secara bersamaan.', 'error');
                                        return;
                                    }
                                    if (monthYear !== firstMonthYear) {
                                        Swal.fire('Error', 'Tidak bisa mencetak dari bulan yang berbeda secara bersamaan.', 'error');
                                        return;
                                    }
                                }
                            }

                            const status = $('#commission-status-filter').val();
                            const distributorShop = $('#distributor-shop-filter').val();
                            const dateStart = $('#input-commission-date-start').val();
                            const dateEnd = $('#input-commission-date-end').val();

                            const url = new URL(window.location.origin + '/commission/print-pic-technician');
                            url.searchParams.append('status', status);
                            url.searchParams.append('distributorShop', distributorShop);
                            url.searchParams.append('dateStart', dateStart);
                            url.searchParams.append('dateEnd', dateEnd);

                            const selectedRows = table.rows({ selected: true }).data().toArray();
                            if (selectedRows.length > 0) {
                                const ids = selectedRows.map(row => row[0]).join(',');
                                url.searchParams.append('ids', ids);
                            }

                            window.open(url.toString(), '_blank');
                        }
                    }
                ],
                language: getDatatablesLanguangeConfigurations('Commission'),
                select: true,
            });

            $('#table-commission tbody').on('click', 'td.dt-control', function() {
                var tr = $(this).closest('tr');
                var row = table.row(tr);

                if (row.child.isShown()) {
                    row.child.hide();
                    tr.removeClass('shown');
                } else {
                    var commissionId = table.row(tr).data()[0];

                    $.ajax({
                        url: '/commission/items/' + commissionId,
                        method: 'GET',
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            if (response && (response.success === true || response.status ===
                                    'success' ||
                                    response.status === true)) {
                                var items = response.data || [];
                                if (items.length === 0) {
                                    row.child('<div class="p-2">No items found.</div>').show();
                                    tr.addClass('shown');
                                    return;
                                }

                                var itemTable =
                                    '<table class="table table-bordered"><thead><tr>' +
                                    '<th>Distributor Shop</th><th>Sales Order Number</th><th>Battery</th><th>Commission Type</th><th>Debit Account</th><th>Credit Account</th><th class="text-end">Commission Amount (IDR)</th>' +
                                    '</tr></thead><tbody>';

                                items.forEach(function(item) {
                                    var distributorShopName = item.distributor_shop ?
                                        item.distributor_shop.name : '-';
                                    var salesOrderNumber = item.sales_order_battery &&
                                        item.sales_order_battery.sales_order ? item
                                        .sales_order_battery.sales_order
                                        .sales_order_number : '-';
                                    var commissionType = item.commission_type || '-';
                                    var debitAccountName = item.debit_account ? item
                                        .debit_account.name : '-';
                                    var creditAccountName = item.credit_account ? item
                                        .credit_account.name : '-';
                                    var commissionAmount = item.commission_amount ?
                                        toCurrency(item.commission_amount) :
                                        '-';
                                    var batteryName = item.battery.name || '-';

                                    itemTable += '<tr>' +
                                        '<td>' + distributorShopName + '</td>' +
                                        '<td>' + salesOrderNumber + '</td>' +
                                        '<td>' + batteryName + '</td>' +
                                        '<td>' + commissionType + '</td>' +
                                        '<td>' + debitAccountName + '</td>' +
                                        '<td>' + creditAccountName + '</td>' +
                                        '<td class="text-end">' + commissionAmount +
                                        '</td>' +
                                        '</tr>';
                                });

                                itemTable += '</tbody></table>';

                                row.child(itemTable).show();
                                tr.addClass('shown');
                            } else {
                                Swal.fire({
                                    title: 'Error',
                                    text: response.message || 'Failed to fetch items.',
                                    icon: 'error',
                                });
                            }
                        },
                        error: function(xhr) {
                            Swal.fire({
                                title: 'Error',
                                text: xhr.responseJSON?.message ||
                                    'Failed to fetch items.',
                                icon: 'error',
                            });
                        }
                    });
                }
            });

            $('#btn-add').on('click', function() {
                goToPage('/commission/create');
            });

            $('#commission-status-filter, #distributor-shop-filter, #input-commission-date-start, #input-commission-date-end')
                .on('change', function() {
                    table.ajax.reload();
                });

            $('#btn-sync-battery').on('click', function() {
                Swal.fire({
                    title: 'Sync Battery IDs?',
                    text: 'This will check all commission items and fix any mismatched battery_id by looking up the correct value from Sales Order Battery.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Sync Now',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#3085d6'
                }).then((result) => {
                    if (!result.isConfirmed) return;

                    Swal.fire({
                        title: 'Syncing...',
                        text: 'Please wait',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });

                    $.ajax({
                        url: '/commission/sync-battery',
                        type: 'POST',
                        data: { _token: '{{ csrf_token() }}' },
                        success: function(resp) {
                            Swal.fire('Done', resp.message, 'success');
                            table.ajax.reload();
                        },
                        error: function(xhr) {
                            let msg = 'Failed to sync.';
                            try { msg = JSON.parse(xhr.responseText).message || msg; } catch(e) {}
                            Swal.fire('Error', msg, 'error');
                        }
                    });
                });
            });
        });
    </script>
@endsection
