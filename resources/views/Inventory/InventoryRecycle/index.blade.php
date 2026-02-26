@extends('template.master')

@section('content')
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    <div class="card">
        <div class="card-body">
            {{-- Title --}}
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">Inventory Recycle</h3>
                    </div>
                </div>
            </div>
            <br>

            <div class="table-responsive">
                <table class="table table-striped" id="table-inventory-recycle">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">No</th>
                            <th scope="col">Distributor / Shop</th>
                            <th scope="col">Stock</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <script>
        $(function() {
            // DataTables config for table-inventory-recycle
            $("#table-inventory-recycle").DataTable({
                lengthMenu: [
                    [5, 10, 25],
                    [5, 10, 25]
                ],
                pageLength: 10,
                responsive: true,
                processing: true,
                serverSide: true,
                order: [],
                ajax: {
                    url: "/inventory/recycle/show",
                    type: "POST",
                    data: function(d) {
                        d._token = "{{ csrf_token() }}";
                    }
                },
                columns: [{
                        className: 'dt-control',
                        orderable: false,
                        data: null,
                        defaultContent: ''
                    },
                    {
                        data: 1,
                        render: function(data, type, row, meta) {
                            return meta.row + 1;
                        },
                        orderable: false
                    },
                    {
                        data: 2,
                        orderable: false
                    },
                    {
                        data: 3,
                        className: "text-end",
                        orderable: false
                    }
                ],
                columnDefs: [{
                        targets: 0,
                        orderable: false
                    },
                    {
                        targets: 3,
                        className: "text-end"
                    },
                ],
                dom: "lBfrtip",
                buttons: [{
                    text: '<i class="fa-solid fa-sync"></i> Sync Stock',
                    className: "btn btn-outline-primary btn-sm ml-1",
                    action: function(e, dt) {
                        swal.fire({
                            title: 'Are you sure?',
                            text: "This will synchronize stock from details to master inventory.",
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Yes, sync it!'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                swal.fire({
                                    title: 'Syncing...',
                                    text: 'Please wait while we synchronize the stock.',
                                    allowOutsideClick: false,
                                    allowEscapeKey: false,
                                    didOpen: () => {
                                        swal.showLoading();
                                    }
                                });
                                $.ajax({
                                    url: '/inventory/recycle/sync-stock',
                                    type: 'POST',
                                    data: {
                                        _token: "{{ csrf_token() }}"
                                    },
                                    success: function(response) {
                                        swal.fire({
                                            icon: 'success',
                                            title: 'Success',
                                            text: response
                                                .message ||
                                                'Stock synchronized successfully!'
                                        });
                                        dt.ajax.reload();
                                    },
                                    error: function(xhr) {
                                        swal.fire({
                                            icon: 'error',
                                            title: 'Error',
                                            text: xhr
                                                .responseJSON
                                                ?.message ||
                                                'Failed to sync stock.'
                                        });
                                    }
                                });
                            }
                        });
                    }
                }],
                language: getDatatablesLanguangeConfigurations("Inventory Recycle"),
                select: true,
                multiselect: true,
                rowCallback: function(row, data) {
                    if (data[3] < 0) $(row).find('td').addClass("text-danger");
                }
            });

            // Add event listener for opening and closing details
            $('#table-inventory-recycle tbody').on('click', 'td.dt-control', function() {
                var tr = $(this).closest('tr');
                var row = $('#table-inventory-recycle').DataTable().row(tr);

                if (row.child.isShown()) {
                    row.child.hide();
                    tr.removeClass('shown');
                } else {
                    var InventoryId = row.data()[1];
                    row.child(formatSubGrid(row.data())).show();
                    tr.addClass('shown');

                    // Load details via AJAX
                    $.ajax({
                        url: '/inventory/recycle/details/show',
                        type: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}",
                            distributorShopId: InventoryId
                        },
                        success: function(response) {
                            row.child(response).show();
                            // Initialize DataTable on the child row
                            var childTable = $(row.child()).find(
                                '.table-inventory-recycle-details');
                            if (childTable.length === 0) {
                                var tableHtml = '<div class="table-responsive">' +
                                    '<table class="table table-striped table-sm table-inventory-recycle-details">' +
                                    '<thead>' +
                                    '<tr>' +
                                    '<th scope="col">#</th>' +
                                    '<th scope="col">SO/PO Date</th>' +
                                    '<th scope="col">SO/PO Number</th>' +
                                    '<th scope="col">Customer/Supplier</th>' +
                                    '<th scope="col">Distributor Shop</th>' +
                                    '<th scope="col">Battery</th>' +
                                    '<th scope="col">Production Code</th>' +
                                    '<th scope="col">Type</th>' +
                                    '<th scope="col">Qty</th>' +
                                    '<th scope="col">Price</th>' +
                                    '<th scope="col">id</th>' +
                                    '<th scope="col">sold</th>' +
                                    '</tr>' +
                                    '</thead>' +
                                    '</table>' +
                                    '</div>';

                                row.child(tableHtml).show();

                                $(row.child()).find('.table-inventory-recycle-details')
                                    .DataTable({
                                        data: response.data,
                                        paging: false,
                                        searching: false,
                                        info: false,
                                        ordering: false,
                                        columns: [{
                                                data: 0
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
                                                data: 6
                                            },
                                            {
                                                data: 7,
                                                className: "text-center"
                                            },
                                            {
                                                data: 8,
                                                className: "text-end"
                                            },
                                            {
                                                data: 9,
                                                className: "text-end"
                                            },
                                            {
                                                data: 10
                                            },
                                            {
                                                data: 11
                                            }
                                        ]
                                    });
                            }
                        },
                        error: function() {
                            row.child(
                                '<div class="p-3 text-danger">Failed to load details.</div>'
                            ).show();
                        }
                    });
                }
            });

            function formatSubGrid(data) {
                return '<div class="p-3">Loading details for ' + data[2] + '...</div>';
            }
        });
    </script>
@endsection
