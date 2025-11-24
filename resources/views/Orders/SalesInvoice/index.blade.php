@extends('template.master')

@section('content')
    {{-- Form --}}
    <div class="d-none d-lg-block">
        <div class="card bg-white">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">Sales Invoice</h3>
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
                                <input type="date" class="form-control" id="input-sales-invoice-date-start"
                                    onchange="reloadTable()">
                            </div>
                            <div class="col-2 text-center">
                                to
                            </div>
                            <div class="col-5">
                                <input type="date" class="form-control" id="input-sales-invoice-date-end"
                                    onchange="reloadTable()">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-1 d-flex align-items-center">
                        Distributor/Shop
                    </div>
                    <div class="col-md-4">
                        <select class="form-control" id="input-sales-invoice-distributor-shop" onchange="reloadTable()">
                            <option value="">All</option>
                            @foreach ($data['distributors'] as $shop)
                                <option value="{{ $shop['id'] }}">
                                    {{ $shop['name'] }}
                                    @if (isset($shop['distributor']))
                                        - {{ $shop['distributor']['name'] }}
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>


        <div class="card bg-white">
            <div class="card-body">

                <div class="table-responsive">
                    {{-- Table --}}
                    <table class="table table-striped" id="table-sales-invoice">
                        <thead>
                            <tr>
                                <th scope="col" class="table-col-no">#</th>
                                <th scope="col">Sales Invoice Number</th>
                                <th scope="col">Sales Order Number</th>
                                <th scope="col">Marketplace Inv No.</th>
                                <th scope="col">Date</th>
                                <th scope="col">Customer</th>
                                <th scope="col">Vehicle</th>
                                <th scope="col">Distributor/Shop</th>
                                <th scope="col">Technician</th>
                                <th scope="col">Total (IDR)</th>
                                <th scope="col">Payment Status</th>
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
            table = $("#table-sales-invoice").DataTable({
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
                    url: "/sales-invoice/show",
                    type: "POST",
                    data: function(d) {
                        d._token = "{{ csrf_token() }}";
                        d.dateStart = document.getElementById('input-sales-invoice-date-start').value;
                        d.dateEnd = document.getElementById('input-sales-invoice-date-end').value;
                        d.distributorShopId = document.getElementById(
                            'input-sales-invoice-distributor-shop').value;
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
                                url: '/sales-invoice/export/details',
                                method: 'POST',
                                data: {
                                    _token: "{{ csrf_token() }}",
                                    dateStart: document.getElementById(
                                        'input-sales-invoice-date-start').value,
                                    dateEnd: document.getElementById(
                                        'input-sales-invoice-date-end').value
                                },
                                xhrFields: {
                                    responseType: 'blob'
                                },
                                success: function(data) {
                                    var url = window.URL.createObjectURL(data);
                                    var a = document.createElement('a');
                                    a.href = url;

                                    var dateStart = document.getElementById(
                                        'input-sales-invoice-date-start').value;
                                    var dateEnd = document.getElementById(
                                        'input-sales-invoice-date-end').value;
                                    var filename = 'sales-invoice-details';
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
                            let id = selectedRows[0][12];
                            goToPage("/sales-invoice/edit/" + id);
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
                            let ids = selectedRows.map(row => row[12]);
                            sendDestroyRequest(ids, "/sales-invoice/delete", function() {
                                // Reload the index table.
                                table.ajax.reload();
                            });
                        }
                    },
                ]),
                language: getDatatablesLanguangeConfigurations("Sales Invoice"),
                select: true,
                rowCallback: function(row, data) {
                    if (data[10] == "posted")
                        $('td', row).addClass("text-success");
                    else if (data[10] == "completed")
                        $('td', row).addClass("text-info");
                }
            });
        });

        function reloadTable() {
            var dateStart = document.getElementById('input-sales-invoice-date-start').value;
            var dateEnd = document.getElementById('input-sales-invoice-date-end').value;

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
                            <button class="btn btn-outline-success btn-sm w-100" id="btn-post" onclick="postSalesOrder()">
                                <i class="fas fa-file-text me-2"></i> <span id="btn-post-text">Post</span>
                            </button>
                        </div>
                        <div class="col-6 col-md-3 mb-3">
                            <!-- Button Invoice -->
                            <button class="btn btn-outline-secondary w-100 btn-sm" id="btn-invoice"
                                onclick="downloadInvoice()">
                                <i class="fas fa-file-text me-2"></i> Invoice
                            </button>
                        </div>
                        <div class="col-6 col-md-3 mb-3">
                            {{-- button print po --}}
                            <button class="btn btn-outline-secondary w-100 btn-sm" id="btn-invoice"
                                onclick="downloadPurchaseOrder()">
                                <i class="fas fa-file-text me-2"></i> Purchase Order
                            </button>
                        </div>
                        <div class="col-6 col-md-3 mb-3">
                            <!-- Button Create Work Order -->
                            <button class="btn btn-outline-warning w-100 btn-sm text-truncate-custom" id="btn-work-order"
                                onclick="createWorkOrder()">
                                <i class="fas fa-screwdriver-wrench me-2"></i> Create Work Order
                            </button>
                        </div>
                        <div class="col-6 col-md-3 mb-3">
                            <!-- Button Re-Create Payment Link -->
                            <button class="btn btn-outline-info w-100 btn-sm text-truncate-custom"
                                id="btn-recreate-payment-link" onclick="recreatePaymentLink()">
                                <i class="fas fa-link me-2"></i> Re-Create Payment Link
                            </button>
                        </div>

                        {{-- button copy link payment  --}}
                        <div class="col-6 col-md-3 mb-3">
                            <button class="btn btn-outline-info w-100 btn-sm text-truncate-custom"
                                id="btn-copy-link-payment-midtrans">
                                <i class="fas fa-link me-2"></i> Copy Payment Link
                            </button>
                        </div>

                        {{-- Button Multiple Print Purchase Order --}}
                        <div class="col-6 col-md-3 mb-3">
                            <button class="btn btn-outline-secondary w-100 btn-sm" id="btn-multiple-print-purchase-order"
                                onclick="multiplePrintPurchaseOrder()">
                                <i class="fas fa-file-text me-2"></i> Multiple Purchase Order
                            </button>
                        </div>

                        {{-- Button Multiple Print Consignment by Distributor --}}
                        <div class="col-6 col-md-3 mb-3">
                            <button class="btn btn-outline-primary w-100 btn-sm" id="btn-multiple-print-consignment"
                                onclick="multiplePrintConsignment()">
                                <i class="fas fa-building me-2"></i> Multiple Consignment by Distributor
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
            goToPage("/sales-invoice/create");
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
            sendPostRequest($('#modal-more-action-id').val(), "/sales-invoice/post",
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
            downloadPDF("/sales-invoice/invoice/" + $('#modal-more-action-id').val());

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
                url: "/sales-invoice/get-purchase-order-number/" + salesOrderId,
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
                        downloadPDF("/sales-invoice/purchase-order/" + $('#modal-more-action-id').val());

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
            createworkorder("/sales-invoice/work-order/" + $('#modal-more-action-id').val());

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
                        url: "/sales-invoice/recreate-payment-link/" + $('#modal-more-action-id').val(),
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
                url: "/sales-invoice/copy-link-payment/" + $('#modal-more-action-id').val(),
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

            var salesOrderIds = selectedRows.map(row => row[10]);
            var salesOrderNumbers = selectedRows.map(row => row[1]);
            var salesOrderNumbersString = salesOrderNumbers.join(", ");

            Swal.fire({
                title: "Print Purchase Order",
                text: "Are you sure you want to print the purchase order for the following sales invoice? \n" +
                    salesOrderNumbersString,
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes",
                cancelButtonText: "No",
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "/sales-invoice/get-multiple-print-purchase-order",
                        method: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            salesOrderIds: salesOrderIds
                        },
                        success: function(response) {
                            if (response.status == 'success') {

                                var ids = response.data.map(item => item.id);
                                downloadPDF("/sales-invoice/multiple-print-purchase-order/" + ids.join(
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

        function multiplePrintConsignment() {
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

            var salesInvoiceIds = selectedRows.map(row => row[12]); // Changed from row[10] to row[12]
            var salesInvoiceNumbers = selectedRows.map(row => row[1]);
            var salesInvoiceNumbersString = salesInvoiceNumbers.join(", ");

            console.log('Selected Sales Invoice IDs:', salesInvoiceIds); // Debug log

            Swal.fire({
                title: "Print Multiple Consignment by Distributor",
                text: "Are you sure you want to print the consignment grouped by distributor for the following sales invoices? \n" +
                    salesInvoiceNumbersString,
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes",
                cancelButtonText: "No",
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "/sales-invoice/get-multiple-print-consignment",
                        method: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            salesInvoiceIds: salesInvoiceIds
                        },
                        success: function(response) {
                            console.log('Response:', response); // Debug log
                            if (response.success) {
                                // Open PDF in new tab using the IDs
                                downloadPDF("/sales-invoice/multiple-print-consignment/" +
                                    salesInvoiceIds.join(","));
                            } else {
                                Swal.fire({
                                    title: "Error",
                                    text: response.message || "Failed to generate consignment.",
                                    icon: "error",
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            console.log('AJAX Error:', xhr.responseText); // Debug log
                            Swal.fire({
                                title: "Error",
                                text: "Failed to process request: " + error,
                                icon: "error",
                            });
                        }
                    });
                }
            });
        }
    </script>
@endsection
