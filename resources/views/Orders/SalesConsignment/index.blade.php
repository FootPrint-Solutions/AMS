@extends('template.master')

@section('content')
    {{-- Table --}}
    <div class="d-none d-lg-block">
        <div class="card bg-white">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">Sales Consignment</h3>
                    </div>

                    <div class="col-auto text-end float-end ms-auto download-grp">
                        <button id="btn-add" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add
                            New Sales Consignment</button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row align-items-center mb-3">
                    <div class="col-md-1 text-md-right text-left font-weight-bold">
                        Date
                    </div>
                    <div class="col-md-4">
                        <div class="row align-items-center">
                            <div class="col-5 pr-0">
                                <input type="date" class="form-control" id="input-sales-consignment-date-start"
                                    onchange="reloadTable()">
                            </div>
                            <div class="col-2 text-center px-0">
                                to
                            </div>
                            <div class="col-5 pl-0">
                                <input type="date" class="form-control" id="input-sales-consignment-date-end"
                                    onchange="reloadTable()">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-1 text-md-right text-left font-weight-bold">
                        Status
                    </div>
                    <div class="col-md-4">
                        <select class="form-control" id="sales-consignment-status-filter" onchange="onStatusFilterChange()">
                            <option value="all">All</option>
                            <option value="draft">Draft</option>
                            <option value="posted">Printed</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                {{-- DataTable --}}
                <table id="sales-consignment-table" class="table table-striped" style="width:100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>No</th>
                            <th>Consignment Number</th>
                            <th>Date</th>
                            <th>Vendor</th>
                            <th>Ship To</th>
                            <th>Subtotal</th>
                            <th>Discount Price</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    {{-- Scripts --}}
    <script>
        let table;

        $(document).ready(function() {
            // Initialize DataTable
            // Add subgrid toggle column to the table header
            $('#sales-consignment-table thead tr').prepend('<th></th>');

            table = $("#sales-consignment-table").DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "/sales-consignment/show",
                    type: "POST",
                    data: function(d) {
                        d._token = '{{ csrf_token() }}';
                        d.status = getActiveFilter();
                        d.date_start = $('#input-sales-consignment-date-start').val();
                        d.date_end = $('#input-sales-consignment-date-end').val();
                    }
                },
                columns: [{
                        className: 'dt-control',
                        orderable: false,
                        data: null,
                        defaultContent: '',
                        searchable: false
                    },
                    {
                        title: 'No',
                        data: 0,
                        orderable: false,
                        searchable: false
                    },
                    {
                        title: 'Consignment Number',
                        data: 1
                    },
                    {
                        title: 'Date',
                        data: 2
                    },
                    {
                        title: 'Vendor',
                        data: 3
                    },
                    {
                        title: 'Ship To',
                        data: 4
                    },
                    {
                        title: 'Subtotal',
                        data: 5,
                        render: function(data, type) {
                            return type === 'display' || type === 'filter' ? parseFloat(data)
                                .toLocaleString('id-ID') : data;
                        }
                    },
                    {
                        title: 'Discount Price',
                        data: 6,
                        render: function(data, type) {
                            return type === 'display' || type === 'filter' ? parseFloat(data)
                                .toLocaleString('id-ID') : data;
                        }
                    },
                    {
                        title: 'Total Amount',
                        data: 7,
                        render: function(data, type) {
                            return type === 'display' || type === 'filter' ? parseFloat(data)
                                .toLocaleString('id-ID') : data;
                        }
                    },
                    {
                        title: 'Status',
                        data: 8,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            // Render HTML badge for status
                            if (type === 'display' || type === 'filter') {
                                return data;
                            }
                            return $(data).text(); // fallback for other types
                        }
                    },
                    {
                        title: 'ID',
                        data: 9,
                        visible: false
                    }
                ],
                dom: "lBfrtip",
                buttons: getDatatablesButtonConfigurations([{
                        text: "<i class='fas fa-pencil'></i> Edit",
                        className: "btn btn-outline-primary btn-sm",
                        action: function(e, dt, node, config) {
                            let selectedData = dt.row({
                                selected: true
                            }).data();
                            if (selectedData) {
                                editConsignment(selectedData[9]);
                            } else {
                                Swal.fire('No row selected', 'Please select a row to edit.',
                                    'warning');
                            }
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
                                printConsignment();
                            } else {
                                Swal.fire('No row selected', 'Please select a row to post.',
                                    'warning');
                            }
                        }
                    },
                    {
                        text: "<i class='fas fa-trash'></i> Delete",
                        className: "btn btn-outline-danger btn-sm",
                        action: function(e, dt, node, config) {
                            let selectedData = dt.row({
                                selected: true
                            }).data();
                            if (selectedData) {
                                deleteConsignment(selectedData[9]);
                            } else {
                                Swal.fire('No row selected', 'Please select a row to delete.',
                                    'warning');
                            }
                        }
                    }
                ]),
                language: getDatatablesLanguangeConfigurations("Sales Consignment"),
                order: [
                    [3, 'desc']
                ],
                select: true,
            });

            // Subgrid: format function
            function formatSubgrid(rowData) {
                let consignmentId = rowData[9];
                let html = '<div class="subgrid-loading">Loading...</div>';
                $.ajax({
                    url: '/sales-consignment/items/' + consignmentId,
                    type: 'GET',
                    success: function(response) {
                        if (response.success && Array.isArray(response.data) && response.data.length >
                            0) {
                            let rows = response.data.map(function(item, idx) {
                                return `
                                    <tr>
                                        <td>${idx + 1}</td>
                                        <td>${item.sales_invoice_number || ''}</td>
                                        <td>${item.invoice_number || ''}</td>
                                        <td>${item.date ? new Date(item.date).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' }).replace(/ /g, ' ') : ''}</td>
                                        <td class="text-end">${parseFloat(item.subtotal).toLocaleString('id-ID')}</td>
                                        <td class="text-end">${parseFloat(item.discount_price).toLocaleString('id-ID')}</td>
                                        <td class="text-end">${parseFloat(item.total).toLocaleString('id-ID')}</td>
                                    </tr>
                                `;
                            }).join('');
                            html = `
                            <div class="mb-2 font-weight-bold">Sales Consignment Items</div>
                            <div class="container mb-2 border-1">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm mb-0">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Sales Invoice No</th>
                                                <th>Invoice Number</th>
                                                <th>Date</th>
                                                <th class="text-end">Subtotal</th>
                                                <th class="text-end">Discount Price</th>
                                                <th class="text-end">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            ${rows}
                                        </tbody>
                                    </table>
                                </div>
                                </div>
                            `;
                        } else {
                            html = '<div class="text-muted">No items found.</div>';
                        }
                        $('#subgrid-' + consignmentId).html(html);
                    },
                    error: function() {
                        $('#subgrid-' + consignmentId).html(
                            '<div class="text-danger">Failed to load details.</div>');
                    }
                });

                return '<div id="subgrid-' + consignmentId + '">' + html + '</div>';
            }

            // Add event listener for opening and closing subgrid
            $('#sales-consignment-table tbody').on('click', 'td.dt-control', function() {
                var tr = $(this).closest('tr');
                var row = table.row(tr);

                if (row.child.isShown()) {
                    // This row is already open - close it
                    row.child.hide();
                    tr.removeClass('shown');
                } else {
                    // Open this row
                    row.child(formatSubgrid(row.data())).show();
                    tr.addClass('shown');
                }
            });

            // Filter buttons
            $('.filter-btn').on('click', function() {
                $('.filter-btn').removeClass('active');
                $(this).addClass('active');
                table.ajax.reload();
            });
        });

        function getActiveFilter() {
            return $('.filter-btn.active').data('status');
        }

        function reloadTable() {
            table.ajax.reload();
        }

        // Action handlers
        function viewConsignment(id) {
            window.location.href = `/sales-consignment/${id}`;
        }

        function printConsignment() {
            var selectedRows = table.rows({
                selected: true
            }).data();
            if (selectedRows.length === 0) {
                Swal.fire('No row selected', 'Please select a row to print.', 'warning');
                return;
            }

            var salesConsignmentIds = selectedRows.map(row => row[9]);
            var salesConsignmentNumbers = selectedRows.map(row => row[1]);
            var salesConsignmentString = salesConsignmentIds.join(',');

            swal.fire({
                title: 'Print Sales Consignment',
                html: `Are you sure you want to print the following Sales Consignment(s)?<br><strong>${salesConsignmentNumbers.join(', ')}</strong>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, Print',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#3085d6'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/sales-consignment/get-print',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            ids: salesConsignmentString
                        },
                        success: function(response) {
                            if (response.status == 'success') {

                                var ids = response.data.map(item => item.id);
                                downloadPDF("/sales-consignment/print/" + ids
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

        function deleteConsignment(id) {
            Swal.fire({
                title: 'Delete Consignment',
                text: 'Are you sure you want to delete this consignment? This action cannot be undone!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Delete it!',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#d33'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/sales-consignment/destroy',
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}',
                            id: id
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire('Deleted!', response.message, 'success');
                                table.ajax.reload();
                            } else {
                                Swal.fire('Error!', response.message, 'error');
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

        function editConsignment(id) {
            window.location.href = `/sales-consignment/edit/${id}`;
        }

        $('#btn-add').on('click', function() {
            window.location.href = '/sales-consignment/createnoids';
        });

        function onStatusFilterChange() {
            table.ajax.reload();
        }

        function getActiveFilter() {
            return $('#sales-consignment-status-filter').val();
        }
    </script>
@endsection
