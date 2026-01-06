@extends('template.master')

@section('content')
    <div class="card shadow-lg mb-4">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title mb-0">Billing</h3>
                </div>
                <div class="col-auto ms-auto text-end">
                    <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <i class="fas fa-plus"></i> Add New
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="javascript:void(0)" id="btn-add">
                                <i class="fas fa-plus me-2"></i> Add New Sales Billing
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="javascript:void(0)" id="btn-add-purchase-billing">
                                <i class="fas fa-recycle me-2"></i> Add New Purchase Billing
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form id="filter-form" class="row align-items-center mb-3">
                <label class="col-md-1 col-form-label fw-bold">Date</label>
                <div class="col-md-4">
                    <div class="row">
                        <div class="col-5 pe-0">
                            <input type="date" class="form-control" id="input-billing-date-start">
                        </div>
                        <div class="col-2 text-center px-0 align-self-center">to</div>
                        <div class="col-5 ps-0">
                            <input type="date" class="form-control" id="input-billing-date-end">
                        </div>
                    </div>
                </div>
                <label class="col-md-1 col-form-label fw-bold">Status</label>
                <div class="col-md-2">
                    <select class="form-select" id="billing-status-filter">
                        <option value="all">All</option>
                        <option value="draft">Draft</option>
                        <option value="posted">Posted</option>
                    </select>
                </div>

                {{-- Filter Billing Type ( Sales Billing / Purchase Billing ) --}}
                <label class="col-md-2 col-form-label fw-bold">Billing Type</label>
                <div class="col-md-2">
                    <select class="form-select" id="billing-type-filter">
                        <option value="all" selected>All</option>
                        <option value="sales_billing">Sales Billing</option>
                        <option value="purchase_billing">Purchase Billing</option>
                    </select>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-lg">
        <div class="card-body">
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-striped" id="table-billing" style="width:100%">
                    <thead>
                        <tr>
                            <th></th>
                            <th>#</th>
                            <th>Billing Number</th>
                            <th>Vendor</th>
                            <th>Ship To</th>
                            <th>Date</th>
                            <th class="text-end">Subtotal</th>
                            <th class="text-end">Discount Price</th>
                            <th class="text-end">Total Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <script>
        $(function() {
            let table = $("#table-billing").DataTable({
                lengthMenu: [
                    [5, 10, 25],
                    [5, 10, 25]
                ],
                pageLength: 10,
                responsive: true,
                processing: true,
                serverSide: true,
                order: [
                    [10, "desc"]
                ],
                ajax: {
                    url: "{{ url('/billing/show') }}",
                    type: "POST",
                    data: function(d) {
                        d._token = "{{ csrf_token() }}";
                        d.status = $("#billing-status-filter").val();
                        d.date_start = $("#input-billing-date-start").val();
                        d.date_end = $("#input-billing-date-end").val();
                        d.billing_type = $("#billing-type-filter").val();
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
                        className: "text-end"
                    },
                    {
                        data: 7,
                        className: "text-end"
                    },
                    {
                        data: 8,
                        className: "text-end"
                    },
                    {
                        data: 9
                    },
                    {
                        data: 10,
                        visible: false
                    }
                ],
                dom: "lBfrtip",
                buttons: [{
                        text: "<i class='fas fa-pencil'></i> Edit",
                        className: "btn btn-outline-primary btn-sm",
                        action: function() {
                            let selected = table.rows({
                                selected: true
                            }).data().toArray();
                            if (selected.length !== 1) {
                                Swal.fire("Error", "Please select a single row for editing.",
                                    "error");
                                return;
                            }
                            window.location.href = "/billing/edit/" + selected[0][10];
                        }
                    },
                    {
                        text: "<i class='fas fa-trash'></i> Delete",
                        className: "btn btn-outline-danger btn-sm ms-1",
                        action: function() {
                            let selected = table.rows({
                                selected: true
                            }).data().toArray();
                            if (!selected.length) {
                                Swal.fire("Error", "Please select at least one row for deleting.",
                                    "error");
                                return;
                            }
                            let ids = selected.map(row => row[10]);
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
                                    $.post("/billing/destroy", {
                                        _token: "{{ csrf_token() }}",
                                        ids: ids
                                    }, function(response) {
                                        if (response.status === "success") {
                                            Swal.fire('Deleted!', response.message,
                                                'success');
                                            table.ajax.reload();
                                        } else {
                                            Swal.fire('Error!', response.message,
                                                'error');
                                        }
                                    }).fail(function() {
                                        Swal.fire('Error!',
                                            'An error occurred while deleting.',
                                            'error');
                                    });
                                }
                            });
                        }
                    },
                    {
                        text: "<i class='fas fa-paper-plane'></i> Post",
                        className: "btn btn-outline-success btn-sm",
                        action: function() {
                            let selected = table.rows({
                                selected: true
                            }).data().toArray();
                            if (!selected.length) {
                                Swal.fire('No row selected',
                                    'Please select at least one row to post.', 'warning');
                                return;
                            }
                            let ids = selected.map(row => row[10]);
                            Swal.fire({
                                title: 'Post Billing',
                                text: 'Are you sure you want to post the selected Billing(s)?',
                                icon: 'question',
                                showCancelButton: true,
                                confirmButtonText: 'Yes, Post',
                                cancelButtonText: 'Cancel',
                                confirmButtonColor: '#3085d6'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    $.post('/billing/post', {
                                        _token: '{{ csrf_token() }}',
                                        ids: ids
                                    }, function(response) {
                                        if (response.status === 'success') {
                                            Swal.fire('Posted!', response.message,
                                                'success');
                                            table.ajax.reload();
                                        } else {
                                            Swal.fire('Error!', response.message,
                                                'error');
                                        }
                                    }).fail(function(xhr) {
                                        let errorMessage = xhr.responseJSON
                                            ?.message || 'An error occurred';
                                        Swal.fire('Error!', errorMessage, 'error');
                                    });
                                }
                            });
                        }
                    },
                    {
                        text: "<i class='fas fa-print'></i> Print",
                        className: "btn btn-outline-secondary btn-sm ms-1",
                        action: function() {
                            let selected = table.rows({
                                selected: true
                            }).data().toArray();
                            if (selected.length !== 1) {
                                Swal.fire("Error", "Please select a single row for printing.",
                                    "error");
                                return;
                            }
                            let id = selected[0][10];
                            // window.open("/billing/print/" + id, "_blank");

                            var billingNumber = selected[0][2];
                            var prefix = billingNumber.substring(0, 2);
                            if (prefix === 'SB') {
                                Swal.fire("Error", "Use Print Kwitansi for Sales Billing.",
                                    "error");
                                return;
                            }

                            downloadPDF("/billing/print/" + id, "_blank");
                        }
                    },
                    // // Print Kwitansi Button
                    {
                        text: "<i class='fas fa-receipt'></i> Print Kwitansi",
                        className: "btn btn-outline-secondary btn-sm ms-1",
                        action: function() {
                            let selected = table.rows({
                                selected: true
                            }).data().toArray();
                            if (selected.length !== 1) {
                                Swal.fire("Error",
                                    "Please select a single row for printing Kwitansi.",
                                    "error");
                                return;
                            }
                            let id = selected[0][10];
                            downloadPDF("/billing/print-receipt/" + id, "_blank");
                        }
                    }
                ],
                select: true,
                rowCallback: function(row, data) {
                    if (data[9] === "posted") {
                        $(row).find('td').addClass("text-success");
                    } else if (data[9] === "completed") {
                        $(row).find('td').addClass("text-info");
                    }
                }
            });

            $("#btn-add").on("click", function() {
                window.location.href = "/billing/create";
            });

            $("#btn-add-purchase-billing").on("click", function() {
                window.location.href = "/billing/create-purchase";
            });

            $("#billing-status-filter, #input-billing-date-start, #input-billing-date-end, #billing-type-filter")
                .on("change",
                    function() {
                        table.ajax.reload();
                    });

            $('#table-billing tbody').on('click', 'td.dt-control', function() {
                let tr = $(this).closest('tr');
                let row = table.row(tr);

                if (row.child.isShown()) {
                    row.child.hide();
                    tr.removeClass('shown');
                } else {
                    let billingId = row.data()[10];
                    $.get('/billing/items/' + billingId, {
                        _token: "{{ csrf_token() }}"
                    }, function(response) {
                        if (response && (response.status === 'success' || response.success ===
                                true)) {
                            let items = response.data || [];
                            if (!items.length) {
                                row.child('<div class="p-2">No items found.</div>').show();
                                tr.addClass('shown');
                                return;
                            }

                            items.forEach(item => {
                                if (item.invoice_source ===
                                    "App\\Models\\Orders\\PurchaseOrder\\PurchaseOrderModel"
                                ) {
                                    item.invoice_source = "Purchase order";
                                } else if (item.invoice_source ===
                                    "App\\Models\\Orders\\SalesOrder\\SalesOrderModel") {
                                    item.invoice_source = "Sales order";
                                }

                                if (item.invoice_type === "recycle") {
                                    item.subtotal = (parseFloat(item.subtotal) * -1)
                                        .toFixed(3);
                                    item.discount_price = (parseFloat(item.discount_price) *
                                        -1).toFixed(3);
                                    item.total = (parseFloat(item.total) * -1).toFixed(3);
                                }
                            });

                            let itemTable = `
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Invoice Number</th>
                                        <th>Invoice Type</th>
                                        <th>Date</th>
                                        <th>Subtotal</th>
                                        <th>Discount Price</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                        `;
                            items.forEach(item => {
                                itemTable += `
                                <tr>
                                    <td>${item.invoice_number ?? '-'}</td>
                                    <td>${item.invoice_source ?? '-'} ${item.invoice_type ?? '-'}</td>
                                    <td>${item.date ?? '-'}</td>
                                    <td>${item.subtotal ?? '-'}</td>
                                    <td>${item.discount_price ?? '-'}</td>
                                    <td>${item.total ?? '-'}</td>
                                </tr>
                            `;
                            });
                            itemTable += '</tbody></table>';
                            row.child(itemTable).show();
                            tr.addClass('shown');
                        } else {
                            Swal.fire("Error", "Failed to fetch items.", "error");
                        }
                    });
                }
            });
        });
    </script>
@endsection
