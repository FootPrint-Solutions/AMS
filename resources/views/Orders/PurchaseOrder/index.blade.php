@extends('template.master')

@section('content')
    {{-- Form --}}
    <div class="card bg-white">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Purchase Order</h3>
                </div>


                <div class="col-auto text-end float-end ms-auto download-grp">
                    <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <i class="fas fa-plus"></i> Add New
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="javascript:void(0)" id="btn-add">
                                <i class="fas fa-plus me-2"></i> Add New Purchase Order
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="javascript:void(0)" id="btn-add-recycle">
                                <i class="fas fa-recycle me-2"></i> Add New Purchase Order Recycle
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="card-body">
            {{-- Filters --}}
            <div class="row mb-3">
                <div class="col-md-3">
                    <label for="filter-status">Status</label>
                    <select class="form-control" id="filter-status" onchange="reloadTable()">
                        <option value="">All Status</option>
                        <option value="draft">Draft</option>
                        <option value="posted">Posted</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filter-vendor">Vendor</label>
                    <select class="form-control" id="filter-vendor" onchange="reloadTable()">
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filter-ship-to">Ship To</label>
                    <select class="form-control" id="filter-ship-to" onchange="reloadTable()">
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filter-date-start">Date From</label>
                    <input type="date" class="form-control" id="filter-date-start" onchange="reloadTable()">
                </div>
                <div class="col-md-3">
                    <label for="filter-date-end">Date To</label>
                    <input type="date" class="form-control" id="filter-date-end" onchange="reloadTable()">
                </div>

                <div class="col-md-3">
                    <label for="filter-po-type">Purchase Order Type</label>
                    <select class="form-control" id="filter-po-type" onchange="reloadTable()">
                        <option value="">All Types</option>
                        <option value="regular">Regular</option>
                        <option value="recycle">Recycle</option>
                    </select>
                </div>
            </div>

        </div>
    </div>

    <div class="card">
        <div class="card-body">
            {{-- Table --}}
            <table class="table table-striped" id="table-purchase-order">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col" class="table-col-no">No</th>
                        <th scope="col">PO Number</th>
                        <th scope="col">Invoice Number</th>
                        <th scope="col">Date</th>
                        <th scope="col">Vendor</th>
                        <th scope="col">Ship To</th>
                        <th scope="col">Subtotal</th>
                        <th scope="col">Discount</th>
                        <th scope="col">Total</th>
                        <th scope="col">Payment Status</th>
                        <th scope="col">Status</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    {{-- Toastr JS --}}
    <script src="{{ asset('/plugins/toastr/toastr.min.js') }}"></script>

    {{-- DataTables Configuration --}}
    <script>
        let table;
        $(document).ready(function() {
            loadTable();

            $('#table-purchase-order tbody').on('click', 'td.dt-control', function() {
                var tr = $(this).closest('tr');
                var row = table.row(tr);

                if (row.child.isShown()) {

                    row.child.hide();
                    tr.removeClass('shown');
                } else {
                    // Open this row
                    var purchaseOrderId = table.row(tr).data()[0];

                    // Fetch items via AJAX
                    $.ajax({
                        url: '/purchase-order/items/' + purchaseOrderId,
                        method: 'GET',
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            if (response && (response.success === true || response.status ===
                                    'success')) {
                                var items = response.data || [];
                                if (items.length === 0) {
                                    row.child('<div class="p-2">No items found.</div>').show();
                                    tr.addClass('shown');
                                    return;
                                }

                                var itemTable =
                                    '<table class="table table-bordered"><thead><tr>' +
                                    '<th>Production Code</th><th>Item Name</th><th>Quantity</th><th>Price (IDR)</th><th>Type</th><th>Discount</th>' +
                                    '</tr></thead><tbody>';

                                items.forEach(function(item) {
                                    var name = item.battery_name || item.item_name ||
                                        '-';
                                    var qty = (item.quantity !== null && item
                                            .quantity !== undefined) ? item.quantity :
                                        '-';
                                    var price = formatCurrency(item.price_net ??
                                        item.price_net);
                                    var type = item.battery_type || '-';
                                    var prodCode = item.battery_production_code || '-';
                                    var discount = item.discount_price ? formatCurrency(
                                        item.discount_price) : '-';

                                    itemTable += '<tr>' +
                                        '<td>' + prodCode + '</td>' +
                                        '<td>' + name + '</td>' +
                                        '<td>' + qty + '</td>' +
                                        '<td>' + price + '</td>' +
                                        '<td>' + type + '</td>' +
                                        '<td>' + discount + '</td>' +
                                        '</tr>';
                                });

                                itemTable += '</tbody></table>';

                                row.child(itemTable).show();
                                tr.addClass('shown');
                            } else {
                                Swal.fire({
                                    title: "Error",
                                    text: "Failed to fetch items.",
                                    icon: "error",
                                });
                            }
                        }
                    });
                }
            });
        });

        function loadTable() {
            table = $('#table-purchase-order').DataTable({
                lengthMenu: [
                    [5, 10, 25],
                    [5, 10, 25]
                ],
                responsive: true,
                processing: true,
                serverSide: true,
                destroy: true,
                order: [
                    [1, 'desc']
                ],
                pageLength: 10,
                ajax: {
                    url: "/purchase-order/show",
                    type: "POST",
                    data: function(d) {
                        d._token = "{{ csrf_token() }}";
                        d.status = $("#filter-status").val();
                        d.vendor_id = $("#filter-vendor").val();
                        d.dateStart = $("#filter-date-start").val();
                        d.dateEnd = $("#filter-date-end").val();
                        d.ship_to_id = $("#filter-ship-to").val();
                        d.po_type = $("#filter-po-type").val();
                    }
                },
                columnDefs: [{
                        targets: [0],
                        orderable: false,
                        className: 'dt-body-center'
                    },
                    {
                        targets: [5, 6, 7],
                        className: 'dt-body-right'
                    },
                    {
                        targets: [8, 9],
                        className: 'dt-body-center'
                    }
                ],
                columns: [{
                        targets: 0,
                        className: 'dt-control',
                        orderable: false,
                        data: null,
                        defaultContent: '',
                    },
                    {
                        data: null,
                        render: function(data, type, row, meta) {
                            return meta.row + 1;
                        }
                    },
                    {
                        data: 1
                    },
                    {
                        data: 2
                    },
                    {
                        data: 3
                    },
                    {
                        data: 4
                    },
                    {
                        data: 5
                    },
                    {
                        data: 6,
                    },
                    {
                        data: 7,
                    },
                    {
                        data: 8,
                    },
                    {
                        data: 9,
                    },
                    {
                        data: 10,
                    }
                ],
                dom: "lBfrtip",
                buttons: [{
                        text: "<i class='fas fa-pencil'></i> Edit",
                        className: "btn btn-outline-primary btn-sm",
                        action: function(e, dt, node, config) {
                            var selectedRows = table.rows({
                                selected: true
                            }).data().toArray();
                            if (selectedRows.length !== 1) {
                                Swal.fire({
                                    title: "Error",
                                    text: "Please select a single row for editing.",
                                    icon: "error",
                                });
                                return;
                            }
                            let id = selectedRows[0][0];
                            goToPage("/purchase-order/edit/" + id);
                        }
                    },
                    {
                        text: "<i class='fas fa-trash'></i> Delete",
                        className: "btn btn-outline-danger btn-sm ml-1",
                        action: function(e, dt, node, config) {
                            var selectedRows = table.rows({
                                selected: true
                            }).data().toArray();
                            if (selectedRows.length === 0) {
                                Swal.fire({
                                    title: "Error",
                                    text: "Please select at least one row for deleting.",
                                    icon: "error",
                                });
                                return;
                            }
                            let ids = selectedRows.map(row => row[0]);
                            Swal.fire({
                                title: 'Are you sure?',
                                text: "You won't be able to revert this!",
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#3085d6',
                                cancelButtonColor: '#d33',
                                confirmButtonText: 'Yes, delete it!'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    $.ajax({
                                        url: "/purchase-order/destroy",
                                        type: "POST",
                                        data: {
                                            _token: "{{ csrf_token() }}",
                                            ids: ids
                                        },
                                        success: function(response) {
                                            if (response.status === "success") {
                                                Swal.fire('Deleted!', response.message,
                                                    'success');
                                                table.ajax.reload();
                                            } else {
                                                Swal.fire('Error!', response.message,
                                                    'error');
                                            }
                                        },
                                        error: function() {
                                            Swal.fire('Error!',
                                                'An error occurred while deleting.',
                                                'error');
                                        }
                                    });
                                }
                            });
                        }
                    },
                    {
                        text: "<i class='fas fa-paper-plane'></i> Post",
                        className: "btn btn-outline-success btn-sm",
                        action: function(e, dt, node, config) {
                            var selectedRows = table.rows({
                                selected: true
                            }).data().toArray();
                            if (selectedRows.length === 0) {
                                Swal.fire('No row selected', 'Please select at least one row to post.',
                                    'warning');
                                return;
                            }
                            let ids = selectedRows.map(row => row[0]);
                            Swal.fire({
                                title: 'Post Purchase Order',
                                text: 'Are you sure you want to post the selected Purchase Order(s)?',
                                icon: 'question',
                                showCancelButton: true,
                                confirmButtonText: 'Yes, Post',
                                cancelButtonText: 'Cancel',
                                confirmButtonColor: '#3085d6'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    $.ajax({
                                        url: '/purchase-order/post',
                                        type: 'POST',
                                        data: {
                                            _token: '{{ csrf_token() }}',
                                            ids: ids
                                        },
                                        success: function(response) {
                                            if (response.status === 'success') {
                                                Swal.fire('Posted!', response.message,
                                                    'success');
                                                table.ajax.reload();
                                            } else {
                                                Swal.fire('Error!', response.message,
                                                    'error');
                                            }
                                        },
                                        error: function(xhr) {
                                            let errorMessage = xhr.responseJSON
                                                ?.message || 'An error occurred';
                                            Swal.fire('Error!', errorMessage, 'error');
                                        }
                                    });
                                }
                            });
                        }
                    }
                ],
                language: getDatatablesLanguangeConfigurations("Purchase Order"),
                select: true,
                rowCallback: function(row, data) {
                    if (data[9] == "posted")
                        $('td', row).addClass("text-success");
                    else if (data[9] == "completed")
                        $('td', row).addClass("text-info");
                }
            });
        }

        function reloadTable() {
            $('#table-purchase-order').DataTable().ajax.reload();
        }

        function deletePurchaseOrder(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "/purchase-order/destroy/" + id,
                        type: "DELETE",
                        data: {
                            "_token": "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            if (response.status === 'success') {
                                Swal.fire('Deleted!', response.message, 'success');
                                reloadTable();
                            } else {
                                Swal.fire('Error!', response.message, 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Error!', 'An error occurred while deleting.', 'error');
                        }
                    });
                }
            });
        }

        $('#btn-add').on('click', function() {
            goToPage("/purchase-order/create");
        });

        $('#btn-add-recycle').on('click', function() {
            goToPage("/purchase-order/create-recycle");
        });

        function printPurchaseOrder() {
            var selectedRows = table.rows({
                selected: true
            }).data();
            if (selectedRows.length === 0) {
                Swal.fire('No row selected', 'Please select a row to print.', 'warning');
                return;
            }

            var salesConsignmentIds = selectedRows.map(row => row[0]);
            var salesConsignmentNumbers = selectedRows.map(row => row[1]);
            var salesConsignmentString = salesConsignmentIds.join(',');

            swal.fire({
                title: 'Print Purchase Order',
                html: `Are you sure you want to print the following Purchase Order(s)?<br><strong>${salesConsignmentNumbers.join(', ')}</strong>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, Print',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#3085d6'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/purchase-order/get-print',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            ids: salesConsignmentString
                        },
                        success: function(response) {
                            if (response.status == 'success') {

                                var ids = response.data.map(item => item.id);
                                downloadPDF("/purchase-order/print/" + ids
                                    .join(
                                        ","));

                            } else {
                                Swal.fire({
                                    title: "Error",
                                    text: response.message,
                                    icon: "error",
                                });
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


        @if (session('error'))
            toastr.error("{{ session('error') }}");
        @endif

        $(document).ready(function() {
            $('#filter-vendor').select2({
                placeholder: "Enter vendor",
                minimumInputLength: 1,
                ajax: {
                    url: "/purchase-order/vendor/get",
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

            $('#filter-ship-to').select2({
                placeholder: "Enter Ship To",
                minimumInputLength: 1,
                ajax: {
                    url: "/purchase-order/shipto/get",
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

            $('#status').select2({});
        })

        function formatCurrency(amount) {
            if (amount === null || amount === undefined) return '-';
            var num = parseFloat(amount) || 0;
            return 'Rp. ' + num.toLocaleString('id-ID', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            });
        }
    </script>
@endsection
