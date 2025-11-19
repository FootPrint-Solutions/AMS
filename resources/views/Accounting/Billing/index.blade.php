@extends('template.master')

@section('content')
    <div class="card shadow-lg">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Billing</h3>
                </div>

                <div class="col-auto text-end float-end ms-auto download-grp">
                    <button id="btn-add" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add
                        New Billing</button>
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
                            <input type="date" class="form-control" id="input-billing-date-start"
                                onchange="reloadTable()">
                        </div>
                        <div class="col-2 text-center px-0">
                            to
                        </div>
                        <div class="col-5 pl-0">
                            <input type="date" class="form-control" id="input-billing-date-end" onchange="reloadTable()">
                        </div>
                    </div>
                </div>
                <div class="col-md-1 text-md-right text-left font-weight-bold">
                    Status
                </div>
                <div class="col-md-2">
                    <select class="form-control" id="billing-status-filter" onchange="onStatusFilterChange()">
                        <option value="all">All</option>
                        <option value="draft">Draft</option>
                        <option value="posted">Printed</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-lg">
        <div class="card-body">

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Table --}}
            <div class="table-responsive">
                <table class="table table-striped" id="table-billing">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">No</th>
                            <th scope="col">Billing Number</th>
                            <th scope="col">Vendor</th>
                            <th scope="col">Ship To</th>
                            <th scope="col">Date</th>
                            <th scope="col">Discount Price</th>
                            <th scope="col">Subtotal</th>
                            <th scope="col">Total Amount</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <script>
        var table;

        $(document).ready(function() {
            table = $("#table-billing").DataTable({
                lengthMenu: [
                    [5, 10, 25],
                    [5, 10, 25]
                ],
                pageLength: 10,
                responsive: true,
                processing: true,
                serverSide: true,
                order: [
                    [9, "desc"]
                ], // default order by date desc
                ajax: {
                    url: "/billing/show",
                    type: "POST",
                    data: function(d) {
                        d._token = "{{ csrf_token() }}";
                        d.status = $("#billing-status-filter").val();
                        d.date_start = $("#input-billing-date-start").val();
                        d.date_end = $("#input-billing-date-end").val();
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
                        data: 1,
                        searchable: false
                    }, // rownum
                    {
                        data: 2
                    }, // billing_number
                    {
                        data: 3
                    }, // vendor_name
                    {
                        data: 4
                    }, // ship_to
                    {
                        data: 5
                    }, // billing_date
                    {
                        data: 6,
                        className: "text-end"
                    }, // discount_price
                    {
                        data: 7,
                        className: "text-end"
                    }, // subtotal
                    {
                        data: 8,
                        className: "text-end"
                    }, // total_amount
                    {
                        data: 9,
                        visible: false,
                        searchable: false
                    } // hidden id
                ],
                columnDefs: [{
                        targets: 0,
                        orderable: false,
                    },
                    {
                        targets: 6,
                        className: 'dt-body-center'
                    }
                ],
                dom: "lBfrtip",
                buttons: getDatatablesButtonConfigurations(),
                select: true,
                rowCallback: function(row, data) {
                    if (data[13]) {
                        $('td', row).addClass("text-muted");
                    }
                }
            });

            // Load DataTables toolbar component.
            appendDatatablesToolbar(9, "/billing/edit/", null, "/billing/toggle");

            $("#btn-add").on("click", function() {
                goToPage("/billing/create");
            });

            $("#billing-status-filter").on("change", function() {
                table.ajax.reload();
            });

            $("#input-billing-date-start, #input-billing-date-end").on("change", function() {
                table.ajax.reload();
            });

            $('#table-billing tbody').on('click', 'td.dt-control', function() {
                var tr = $(this).closest('tr');
                var row = table.row(tr);

                if (row.child.isShown()) {
                    // This row is already open - close it
                    row.child.hide();
                    tr.removeClass('shown');
                } else {
                    // Open this row
                    var billingId = row.data()[9];

                    // Fetch items via AJAX
                    $.ajax({
                        url: '/billing/items/' + billingId,
                        method: 'GET',
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            if (response && (response.status === 'success' || response
                                    .success === true)) {
                                var items = response.data || [];
                                if (items.length === 0) {
                                    row.child('<div class="p-2">No items found.</div>').show();
                                    tr.addClass('shown');
                                    return;
                                }

                                var itemTable =
                                    '<table class="table table-bordered"><thead><tr>' +
                                    '<th>Invoice Number</th><th>Date</th><th>Discount Price</th><th>Subtotal</th><th>Total</th>' +
                                    '</tr></thead><tbody>';

                                items.forEach(function(item) {
                                    var billingId = item.billing_id ?? '-';
                                    var invoiceNumber = item.invoice_number ?? '-';
                                    var date = item.date ?? '-';
                                    var discount = item.discount ?? '-';
                                    var discountPrice = item.discount_price ?? '-';
                                    var subtotal = item.subtotal ?? '-';
                                    var total = item.total ?? '-';

                                    itemTable += '<tr>' +
                                        '<td>' + invoiceNumber + '</td>' +
                                        '<td>' + date + '</td>' +
                                        '<td>' + discountPrice + '</td>' +
                                        '<td>' + subtotal + '</td>' +
                                        '<td>' + total + '</td>' +
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
    </script>
@endsection
