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
                    <button id="btn-add" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add New Purchase
                        Order</button>
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
                    <label for="filter-supplier">Supplier</label>
                    <select class="form-control" id="filter-supplier" onchange="reloadTable()">
                        <option value="">All Suppliers</option>
                        @foreach ($data['suppliers'] as $supplier)
                            <option value="{{ $supplier['id'] }}">{{ $supplier['name'] }}</option>
                        @endforeach
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
            </div>

        </div>
    </div>

    <div class="card">
        <div class="card-body">
            {{-- Table --}}
            <table class="table table-striped" id="table-purchase-order">
                <thead>
                    <tr>
                        <th scope="col" class="table-col-no">#</th>
                        <th scope="col">PO Number</th>
                        <th scope="col">Invoice Number</th>
                        <th scope="col">Date</th>
                        <th scope="col">Supplier</th>
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

    {{-- DataTables Configuration --}}
    <script>
        let table;
        $(document).ready(function() {
            loadTable();
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
                order: [],
                pageLength: 10,
                ajax: {
                    url: "/purchase-order/show",
                    type: "POST",
                    data: function(d) {
                        d._token = "{{ csrf_token() }}";
                        d.status = $("#filter-status").val();
                        d.supplier_id = $("#filter-supplier").val();
                        d.dateStart = $("#filter-date-start").val();
                        d.dateEnd = $("#filter-date-end").val();
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
                        data: 6
                    },
                    {
                        data: 7
                    },
                    {
                        data: 8
                    },
                    {
                        data: 9
                    }
                ],
                dom: "lBfrtip",
                buttons: [{
                        text: "<i class='fas fa-file-excel'></i> Export All to Excel",
                        className: "btn btn-outline-secondary btn-sm",
                        action: function(e, dt, node, config) {
                            Swal.fire({
                                title: "Exporting Data",
                                text: "Please wait...",
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });

                            $.ajax({
                                url: '/purchase-order/export/details',
                                method: 'POST',
                                data: {
                                    _token: "{{ csrf_token() }}",
                                    status: $("#filter-status").val(),
                                    supplier_id: $("#filter-supplier").val(),
                                    dateStart: $("#filter-date-start").val(),
                                    dateEnd: $("#filter-date-end").val()
                                },
                                xhrFields: {
                                    responseType: 'blob'
                                },
                                success: function(data) {
                                    var url = window.URL.createObjectURL(data);
                                    var a = document.createElement('a');
                                    a.href = url;

                                    var dateStart = $("#filter-date-start").val();
                                    var dateEnd = $("#filter-date-end").val();
                                    var filename = 'purchase-orders-details';
                                    if (dateStart && dateEnd) {
                                        filename += ' ' + dateStart + ' to ' + dateEnd;
                                    } else if (dateStart) {
                                        filename += ' from ' + dateStart;
                                    } else if (dateEnd) {
                                        filename += ' until ' + dateEnd;
                                    } else {
                                        filename += ' ' + new Date().toISOString().slice(0,
                                            10);
                                    }
                                    filename += '.xlsx';
                                    a.download = filename;
                                    document.body.append(a);
                                    a.click();
                                    a.remove();
                                    window.URL.revokeObjectURL(url);

                                    Swal.close();
                                },
                                error: function() {
                                    alert('Error exporting data');
                                }
                            });
                        }
                    },
                    {
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
                        text: "<i class='fas fa-print'></i> Print",
                        className: "btn btn-outline-danger btn-sm",
                        action: function(e, dt, node, config) {
                            let selectedData = dt.row({
                                selected: true
                            }).data();
                            if (selectedData) {
                                printPurchaseOrder();
                            } else {
                                Swal.fire('No row selected', 'Please select a row to post.',
                                    'warning');
                            }
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
    </script>
@endsection
