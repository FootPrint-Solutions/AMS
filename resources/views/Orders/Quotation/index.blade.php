@extends('template.master')

@section('content')
    {{-- Form --}}
    <div class="card">
        <div class="card-body">
            {{-- Title --}}
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">Quotation</h3>
                    </div>
                </div>
            </div>
            <br>

            {{-- Table --}}
            <table class="table table-striped" id="table-quotation">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Quotation Number</th>
                        <th scope="col">Customer</th>
                        <th scope="col">Distributor/Shop</th>
                        <th scope="col">Technician</th>
                        <th scope="col">Total (IDR)</th>
                        <th scope="col">Status</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    {{-- Detail Modal --}}
    <div class="modal modal-lg fade" id="quotation-detail-modal">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="quotation-detail-modal-title"></h5>

                    <div class="btn-group">
                        <select class="form-select" id="quotation-status-select" aria-label="Default select example"
                            style="width: auto !important; border-radius: 0 !important;">
                            <option value="paid">Paid</option>
                            <option value="pending">Pending</option>
                            <option value="failed">Failed</option>
                        </select>
                        <button id="btn-update" class="btn btn-primary btn-sm"><i class="fas fa-refresh"></i> Update
                            status</button>
                    </div>
                </div>

                <div class="modal-body">
                    <table class="table table-striped w-100" id="table-quotation-detail">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Battery Name</th>
                                <th scope="col">Quantity</th>
                                <th scope="col">Price (IDR)</th>
                                <th scope="col">Total Price (IDR)</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        var table;
        var tableTmp;

        $(document).ready(function() {
            // DataTables configuration
            table = $("#table-quotation").DataTable({
                lengthMenu: [
                    [5, 10, 25],
                    [5, 10, 25]
                ],
                responsive: true,
                processing: true,
                serverSide: true,
                order: [],
                ajax: {
                    url: "/quotation/show",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}"
                    }
                },
                columnDefs: [{
                    targets: [0],
                    orderable: false
                }, {
                    targets: [5],
                    className: 'dt-body-right'
                }, {
                    targets: [6],
                    className: 'dt-body-center'
                }],
                dom: "lBfrtip",
                buttons: getDatatablesButtonConfigurations([{
                        text: "<i class='fas fa-file-text'></i> View Invoice",
                        action: function(e, dt, node, config) {
                            // Get the selected row's id.
                            let selectedRows = table.rows({
                                selected: true
                            }).data().toArray();
                            if (selectedRows.length !== 1) {
                                Swal.fire({
                                    title: "Error",
                                    text: "Please select a single row for viewing invoice.",
                                    icon: "error",
                                });
                                return;
                            }

                            // Go to page invoice.
                            goToPage("/quotation/invoice/" + selectedRows[0][7])
                        },
                        className: "btn btn-outline-secondary btn-sm",
                    },
                    {
                        text: "<i class='fas fa-eye'></i> View Detail",
                        action: function(e, dt, node, config) {
                            // Get the selected row's id.
                            let selectedRows = table.rows({
                                selected: true
                            }).data().toArray();
                            if (selectedRows.length !== 1) {
                                Swal.fire({
                                    title: "Error",
                                    text: "Please select a single row for viewing details.",
                                    icon: "error",
                                });
                                return;
                            }

                            // Show popup modal.
                            $('#quotation-detail-modal').modal("show");

                            // Set modal title.
                            $("#quotation-detail-modal-title").text("Item Details (" +
                                selectedRows[0][1] + ")");

                            // Destroy previous set DataTables.
                            if ($.fn.DataTable.isDataTable("#table-quotation-detail")) {
                                $("#table-quotation-detail").DataTable().destroy();
                            }

                            // Set new DataTables.
                            tableTmp = table;
                            table = $("#table-quotation-detail").DataTable({
                                dom: "tp",
                                processing: true,
                                serverSide: true,
                                ajax: {
                                    url: "/quotation/battery/show",
                                    type: "POST",
                                    data: {
                                        _token: "{{ csrf_token() }}",
                                        id: selectedRows[0][7]
                                    }
                                },
                                columnDefs: [{
                                        targets: [0],
                                        orderable: false
                                    },
                                    {
                                        targets: [2, 3, 4],
                                        className: 'dt-body-right'
                                    }
                                ],
                                select: true,
                            });
                        },
                        className: "btn btn-outline-info btn-sm",
                    },
                ]),
                language: getDatatablesLanguangeConfigurations("Quotation"),
                select: true,
            });

            // Load DataTables toolbar component.
            appendDatatablesToolbar(7, "/quotation/edit/", "/quotation/destroy");

            $('#quotation-detail-modal').on('hidden.bs.modal', function(e) {
                table = tableTmp;
            });

            $("#btn-update").on("click", function() {
                // Get selected row's id.
                let selectedRows = tableTmp.rows({
                    selected: true
                }).data().toArray();

                // Send POST request to add all batteries.
                $.ajax({
                    url: "/quotation/update/status",
                    method: "POST",
                    data: {
                        "_token": "{{ csrf_token() }}",
                        "id": selectedRows[0][7],
                        "status": $("#quotation-status-select").val(),
                    },
                    success: function(response) {
                        // Get response data from url (in JSON).
                        let responseData = JSON.parse(response);

                        // Show Toast message based on responseData.
                        showResponseToast(responseData.status, responseData
                            .message);
                    }
                });

                // Reload the table.
                tableTmp.ajax.reload();
            });
        });
    </script>
@endsection
