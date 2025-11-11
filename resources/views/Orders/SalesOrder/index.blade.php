@extends('template.master')

@section('content')
    {{-- Form --}}
    <div class="d-none d-lg-block">
        <div class="card bg-white">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">Sales Order</h3>
                    </div>

                    <div class="col-auto text-end float-end ms-auto download-grp">
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <i class="fas fa-plus"></i> Add New
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="javascript:void(0)" id="btn-add">
                                        <i class="fas fa-plus me-2"></i> Add New Sales Order
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="javascript:void(0)" id="btn-add-recycle">
                                        <i class="fas fa-recycle me-2"></i> Add New Sales Order Recycle
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                {{-- filter tanggal --}}
                <div class="row mt-2">
                    <div class="col-md-1 d-flex align-items-center">
                        Date
                    </div>

                    <div class="col-md-4">
                        <div class="row align-items-center">
                            <div class="col-5">
                                <input type="date" class="form-control" id="input-sales-order-date-start"
                                    onchange="reloadTable()">
                            </div>
                            <div class="col-2 text-center">
                                to
                            </div>
                            <div class="col-5">
                                <input type="date" class="form-control" id="input-sales-order-date-end"
                                    onchange="reloadTable()">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-1"></div>

                </div>
            </div>
        </div>


        <div class="card bg-white">
            <div class="card-body">

                {{-- Table --}}
                <div class="table-responsive">
                    <table class="table table-striped" id="table-sales-order">
                        <thead>
                            <tr>
                                <th scope="col" class="table-col-no">#</th>
                                <th scope="col">Sales Order Number</th>
                                <th scope="col">Marketplace Inv No.</th>
                                <th scope="col">Date</th>
                                <th scope="col">Customer</th>
                                <th scope="col">Vehicle</th>
                                <th scope="col">Distributor/Shop</th>
                                <th scope="col">Technician</th>
                                <th scope="col">Total (IDR)</th>
                                <th scope="col">Payment Status</th>
                                <th scope="col">Status</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @include('Mobile.SalesOrder.index')

    {{-- DataTables Configurations --}}
    <script>
        var table;

        $(document).ready(function() {
            // DataTables configuration
            table = $("#table-sales-order").DataTable({
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
                    url: "/sales-order/show",
                    type: "POST",
                    data: function(d) {
                        d._token = "{{ csrf_token() }}";
                        d.dateStart = document.getElementById('input-sales-order-date-start').value;
                        d.dateEnd = document.getElementById('input-sales-order-date-end').value;
                    }
                },
                columnDefs: [{
                    targets: [0],
                    orderable: false,
                }, {
                    targets: [8],
                    className: 'dt-body-right table-col-price'
                }, {
                    targets: [0, -1, -2],
                    className: 'dt-body-center'
                }],
                dom: "lBfrtip",
                buttons: getDatatablesButtonConfigurations([
                    // Export to Excel
                    {
                        text: "<i class='fas fa-file-excel'></i> Export All to Excel",
                        className: "btn btn-outline-secondary btn-sm",
                        action: function(e, dt, node, config) {
                            var formData = new FormData();
                            formData.append('_token', "{{ csrf_token() }}");

                            Swal.fire({
                                title: "Exporting Data",
                                text: "Please wait...",
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });

                            $.ajax({
                                url: '/sales-order/export/details',
                                method: 'POST',
                                data: {
                                    _token: "{{ csrf_token() }}",
                                    dateStart: document.getElementById(
                                        'input-sales-order-date-start').value,
                                    dateEnd: document.getElementById(
                                        'input-sales-order-date-end').value
                                },
                                xhrFields: {
                                    responseType: 'blob'
                                },
                                success: function(data) {
                                    var url = window.URL.createObjectURL(data);
                                    var a = document.createElement('a');
                                    a.href = url;

                                    var dateStart = document.getElementById(
                                        'input-sales-order-date-start').value;
                                    var dateEnd = document.getElementById(
                                        'input-sales-order-date-end').value;
                                    var filename = 'sales-orders-details';
                                    if (dateStart && dateEnd) {
                                        filename += ' ' + dateStart + ' to ' +
                                            dateEnd;
                                    } else if (dateStart) {
                                        filename += ' from ' + dateStart;
                                    } else if (dateEnd) {
                                        filename += ' until ' + dateEnd;
                                    } else {
                                        filename += ' ' + new Date().toISOString()
                                            .slice(0, 10);
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
                    // Edit    
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
                            let id = selectedRows[0][11];
                            goToPage("/sales-order/edit/" + id);
                        }
                    },
                    // Delete   
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
                            let ids = selectedRows.map(row => row[11]);
                            sendDestroyRequest(ids, "/sales-order/delete", function() {
                                // Reload the index table.
                                table.ajax.reload();
                            });
                        }
                    },
                    // button show modal More Action
                    {
                        text: "<i class='fas fa-ellipsis-v'></i> More Action",
                        className: "btn btn-outline-secondary btn-sm",
                        action: function(e, dt, node, config) {
                            // Get the selected row's id.
                            let selectedRows = table.rows({
                                selected: true
                            }).data().toArray();

                            // Show modal more action
                            showModalMoreAction(selectedRows[0][11], selectedRows[0][11]);
                        }
                    },
                ]),
                language: getDatatablesLanguangeConfigurations("Sales Order"),
                select: true,
                rowCallback: function(row, data) {
                    if (data[12] == "posted")
                        $('td', row).addClass("text-success");
                    else if (data[12] == "completed")
                        $('td', row).addClass("text-info");
                }
            });
        });

        function reloadTable() {
            var dateStart = document.getElementById('input-sales-order-date-start').value;
            var dateEnd = document.getElementById('input-sales-order-date-end').value;

            // Reload the table.
            table.ajax.reload(null, false);
        }
    </script>

    {{-- Modal More Action --}}
    <div class="modal fade" id="modal-more-action" tabindex="-1" aria-labelledby="modal-more-action-label"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-more-action-label">More Action</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <input type="hidden" id="modal-more-action-id">
                    <div class="row">
                        <div class="col-6 col-md-3 mb-3">
                            <!-- Button Post -->
                            <button class="btn btn-primary btn-sm w-100" id="btn-post" onclick="postSalesOrder()">
                                <i class="fas fa-file-text me-2"></i> <span id="btn-post-text">Post</span>
                            </button>
                        </div>
                        <div class="col-6 col-md-3 mb-3">
                            <!-- Button Invoice -->
                            <button class="btn btn-primary w-100 btn-sm" id="btn-invoice" onclick="downloadInvoice()">
                                <i class="fas fa-file-text me-2"></i> Invoice
                            </button>
                        </div>
                        <div class="col-6 col-md-3 mb-3">
                            {{-- button print po --}}
                            <button class="btn btn-primary w-100 btn-sm" id="btn-invoice" onclick="downloadPurchaseOrder()">
                                <i class="fas fa-file-text me-2"></i> Purchase Order
                            </button>
                        </div>
                        <div class="col-6 col-md-3 mb-3">
                            <!-- Button Create Work Order -->
                            <button class="btn btn-primary w-100 btn-sm text-truncate-custom" id="btn-work-order"
                                onclick="createWorkOrder()">
                                <i class="fas fa-screwdriver-wrench me-2"></i> Create Work Order
                            </button>
                        </div>
                        <div class="col-6 col-md-3 mb-3">
                            <!-- Button Re-Create Payment Link -->
                            <button class="btn btn-primary w-100 btn-sm text-truncate-custom" id="btn-recreate-payment-link"
                                onclick="recreatePaymentLink()">
                                <i class="fas fa-link me-2"></i> Re-Create Payment Link
                            </button>
                        </div>

                        {{-- button copy link payment  --}}
                        <div class="col-6 col-md-3 mb-3">
                            <button class="btn btn-primary w-100 btn-sm text-truncate-custom"
                                id="btn-copy-link-payment-midtrans">
                                <i class="fas fa-link me-2"></i> Copy Payment Link
                            </button>
                        </div>

                        {{-- Button Multiple Print Purchase Order --}}
                        <div class="col-6 col-md-3 mb-3">
                            <button class="btn btn-primary w-100 btn-sm" id="btn-multiple-print-purchase-order"
                                onclick="multiplePrintPurchaseOrder()">
                                <i class="fas fa-file-text me-2"></i> Multiple Purchase Order
                            </button>
                        </div>

                        {{-- Button Create Invoice --}}
                        <div class="col-6 col-md-3 mb-3">
                            <button class="btn btn-primary w-100 btn-sm h-100" id="btn-create-invoice"
                                onclick="createSalesInvoice('/sales-invoice/create', )">
                                <i class="fas fa-file-invoice-dollar me-2"></i> Create Sales Invoice
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- Click Event Handler --}}
    <script>
        $('#btn-add').on('click', function() {
            goToPage("/sales-order/create");
        });

        function showModalMoreAction(id, posted) {
            // Show the modal.
            $('#modal-more-action').modal('show');

            // Set button post text to Unpost if it's already posted.
            if (posted == 'posted')
                $("#btn-post-text").html("Unpost");

            // Set the id.
            $('#modal-more-action-id').val(id);
        }

        function createSalesInvoice(url) {
            var selectedRows = table.rows({
                selected: true
            }).data().toArray();

            if (selectedRows.length > 1) {
                Swal.fire({
                    title: "Error",
                    text: "Please select a single row for creating invoice.",
                    icon: "error",
                });
                return;
            }

            // ajax check posting status
            $.ajax({
                url: "/sales-order/post/check",
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    id: selectedRows[0][11]
                },
                success: function(response) {
                    if (response.status == 'success') {
                        // Redirect to create sales invoice page.
                        goToPage(url + "/" + selectedRows[0][11]);
                    } else {
                        Swal.fire({
                            title: "Error",
                            text: response.message,
                            icon: "error",
                        });
                    }
                }
            });

            // Hide the modal.
            $('#modal-more-action').modal('hide');
        }

        function postSalesOrder() {

            var selectedRows = table.rows({
                selected: true
            }).data().toArray();
            if (selectedRows.length > 1) {
                Swal.fire({
                    title: "Error",
                    text: "Please select a single row for posting.",
                    icon: "error",
                });
                return;
            }

            // Post the selected sales order.
            sendPostRequest($('#modal-more-action-id').val(), "/sales-order/post",
                function() {
                    // Reload the index table.
                    table.ajax.reload();
                });

            // Hide the modal.
            $('#modal-more-action').modal('hide');
        }

        function downloadInvoice() {
            var selectedRows = table.rows({
                selected: true
            }).data().toArray();
            if (selectedRows.length > 1) {
                Swal.fire({
                    title: "Error",
                    text: "Please select a single row for posting.",
                    icon: "error",
                });
                return;
            }

            // Download invoice as pdf.
            downloadPDF("/sales-order/invoice/" + $('#modal-more-action-id').val());

            // Hide the modal.
            $('#modal-more-action').modal('hide');
        }

        function downloadPurchaseOrder() {

            var selectedRows = table.rows({
                selected: true
            }).data().toArray();
            if (selectedRows.length > 1) {
                Swal.fire({
                    title: "Error",
                    text: "Please select a single row for posting.",
                    icon: "error",
                });
                return;
            }

            var salesOrderId = $('#modal-more-action-id').val();
            $.ajax({
                url: "/sales-order/get-purchase-order-number/" + salesOrderId,
                method: "GET",
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    if (response.status == 'success') {
                        //   change the title of the pdf
                        var title = response.data;
                        var pdfTitle = "Purchase Order " + title;

                        // change the  <title>Dashboard | AMS</title>
                        document.title = pdfTitle;

                        // Download purchase order as pdf.
                        downloadPDF("/sales-order/purchase-order/" + $('#modal-more-action-id').val());

                    } else {
                        Swal.fire({
                            title: "Error",
                            text: response.message,
                            icon: "error",
                        });
                    }
                }
            });

            // Hide the modal.
            $('#modal-more-action').modal('hide');
        }

        function createWorkOrder() {

            var selectedRows = table.rows({
                selected: true
            }).data().toArray();
            if (selectedRows.length > 1) {
                Swal.fire({
                    title: "Error",
                    text: "Please select a single row for posting.",
                    icon: "error",
                });
                return;
            }

            // Redirect to create work order page.
            createworkorder("/sales-order/work-order/" + $('#modal-more-action-id').val());

            // Hide the modal.
            $('#modal-more-action').modal('hide');
        }

        function recreatePaymentLink() {

            var selectedRows = table.rows({
                selected: true
            }).data().toArray();
            if (selectedRows.length > 1) {
                Swal.fire({
                    title: "Error",
                    text: "Please select a single row for posting.",
                    icon: "error",
                });
                return;
            }

            Swal.fire({
                title: "Re-Create Payment Link",
                text: "Are you sure you want to re-create the payment link?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes",
                cancelButtonText: "No",
            }).then((result) => {
                if (result.isConfirmed) {
                    // Re-create the payment link.
                    $.ajax({
                        url: "/sales-order/recreate-payment-link/" + $('#modal-more-action-id').val(),
                        method: "GET",
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            let responseData = JSON.parse(response);
                            if (responseData.status == true) {
                                Swal.fire({
                                    title: "Success",
                                    text: responseData.message,
                                    icon: "success",
                                });
                            } else {
                                Swal.fire({
                                    title: "Error",
                                    text: responseData.message,
                                    icon: "error",
                                });
                            }
                        }
                    });
                }
            });
        }

        $("#btn-copy-link-payment-midtrans").on("click", function() {
            var selectedRows = table.rows({
                selected: true
            }).data().toArray();
            if (selectedRows.length > 1) {
                Swal.fire({
                    title: "Error",
                    text: "Please select a single row for posting.",
                    icon: "error",
                });
                return;
            }

            $("#modal-more-action").modal("hide");
            // send ajax 
            $.ajax({
                url: "/sales-order/copy-link-payment/" + $('#modal-more-action-id').val(),
                method: "GET",
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    let responseData = JSON.parse(response);
                    if (responseData.status == true) {
                        var copyText = responseData.message

                        // create sweet alert with input text   
                        Swal.fire({
                            title: "Payment Link",
                            html: `<input type="text" id="paymentLink" class="form-control" value="${copyText}">`,
                            showCancelButton: true,
                            confirmButtonText: "Copy",
                            cancelButtonText: "Close",
                        }).then((result) => {
                            if (result.isConfirmed) {
                                var copyText = document.getElementById("paymentLink");
                                copyText.select();
                                copyText.setSelectionRange(0, 99999);
                                document.execCommand("copy");
                                Swal.fire({
                                    title: "Success",
                                    text: "Payment link copied",
                                    icon: "success",
                                });
                            }
                        });
                    } else {
                        Swal.fire({
                            title: "Error",
                            text: responseData.message,
                            icon: "error",
                        });
                    }
                }
            });
        });

        function multiplePrintPurchaseOrder() {
            var selectedRows = table.rows({
                selected: true
            }).data().toArray();
            if (selectedRows.length === 0) {
                Swal.fire({
                    title: "Error",
                    text: "Please select at least one row for printing.",
                    icon: "error",
                });
                return;
            }

            var salesOrderIds = selectedRows.map(row => row[11]);
            var salesOrderNumbers = selectedRows.map(row => row[1]);
            var salesOrderNumbersString = salesOrderNumbers.join(", ");

            const recycleSpanRegex = /<span[^>]*>\s*Recycle\s*<\/span>/i;
            if (recycleSpanRegex.test(salesOrderNumbersString)) {
                Swal.fire({
                    title: "Error",
                    text: "Purchase Order creation is not available for recycle type Sales Orders.",
                    icon: "error",
                });
                return;
            }

            Swal.fire({
                title: "Print Purchase Order",
                text: "Are you sure you want to print the purchase order for the following sales orders? \n" +
                    salesOrderNumbersString,
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes",
                cancelButtonText: "No",
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "/sales-order/get-multiple-print-purchase-order",
                        method: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            salesOrderIds: salesOrderIds
                        },
                        success: function(response) {
                            if (response.status == 'success') {

                                var ids = response.data.map(item => item.id);
                                downloadPDF("/sales-order/multiple-print-purchase-order/" + ids.join(
                                    ","));

                            } else {
                                Swal.fire({
                                    title: "Error",
                                    text: response.message,
                                    icon: "error",
                                });
                            }
                        }
                    });
                }
            });
        }

        $('#btn-add-recycle').on('click', function() {
            goToPage("/sales-order/create-recycle");
        });
    </script>
@endsection
