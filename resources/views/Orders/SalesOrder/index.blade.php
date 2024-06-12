@extends('template.master')

@section('content')
    {{-- Form --}}
    <div class="card">
        <div class="card-body">
            {{-- Title --}}
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">Sales Order</h3>
                    </div>

                    <div class="col-auto text-end float-end ms-auto download-grp">
                        <button id="btn-add" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add
                            New Sales Order</button>
                    </div>
                </div>
            </div>
            <br>

            {{-- Table --}}
            <table class="table table-striped" id="table-sales-order">
                <thead>
                    <tr>
                        <th scope="col" class="table-col-no">#</th>
                        <th scope="col">Sales Order Number</th>
                        <th scope="col">Date</th>
                        <th scope="col">Customer</th>
                        <th scope="col">Vehicle</th>
                        <th scope="col">Distributor/Shop</th>
                        <th scope="col">Technician</th>
                        <th scope="col">Total (IDR)</th>
                        <th scope="col">Payment Method</th>
                        <th scope="col">Status</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

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
                responsive: true,
                processing: true,
                serverSide: true,
                order: [],
                ajax: {
                    url: "/sales-order/show",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}"
                    }
                },
                columnDefs: [{
                    targets: [0],
                    orderable: false,
                }, {
                    targets: [7],
                    className: 'dt-body-right table-col-price'
                }, {
                    targets: [0, -1],
                    className: 'dt-body-center'
                }],
                dom: "lBfrtip",
                buttons: getDatatablesButtonConfigurations([{
                    text: "<i class='fas fa-file-text'></i> Post",
                    action: function(e, dt, node, config) {
                        // Get the selected row's id.
                        let selectedRows = table.rows({
                            selected: true
                        }).data().toArray();
                        if (selectedRows.length !== 1) {
                            Swal.fire({
                                title: "Error",
                                text: "Please select a single row for posting.",
                                icon: "error",
                            });
                            return;
                        }

                        // Post the selected sales order.
                        sendPostRequest(selectedRows[0][10], "/sales-order/post/",
                            function() {
                                // Reload the index table.
                                table.ajax.reload();
                            });
                    },
                    className: "btn btn-outline-secondary btn-sm",
                }, {
                    text: "<i class='fas fa-file-text'></i> Invoice",
                    action: function(e, dt, node, config) {
                        // Get the selected row's id.
                        let selectedRows = table.rows({
                            selected: true
                        }).data().toArray();
                        if (selectedRows.length !== 1) {
                            Swal.fire({
                                title: "Error",
                                text: "Please select a single row for downloading invoice.",
                                icon: "error",
                            });
                            return;
                        }

                        // Download invoice as pdf.
                        downloadPDF("/sales-order/invoice/" + selectedRows[0][10]);
                    },
                    className: "btn btn-outline-secondary btn-sm",
                }, {
                    text: "<i class='fas fa-screwdriver-wrench'></i> Create Work Order",
                    action: function(e, dt, node, config) {
                        // Get the selected row's id.
                        let selectedRows = table.rows({
                            selected: true
                        }).data().toArray();
                        if (selectedRows.length !== 1) {
                            Swal.fire({
                                title: "Error",
                                text: "Please select a single row for creating work order.",
                                icon: "error",
                            });
                            return;
                        }

                        // Redirect to create work order page.
                        createworkorder("/sales-order/work-order/" + selectedRows[0][10]);
                    },
                    className: "btn btn-outline-warning btn-sm",
                }]),
                language: getDatatablesLanguangeConfigurations("Sales Order"),
                select: true,
                rowCallback: function(row, data) {
                    if (data[11] == "posted")
                        $('td', row).addClass("text-success");
                    else if (data[11] == "completed")
                        $('td', row).addClass("text-info");
                }
            });

            // Load DataTables toolbar component.
            appendDatatablesToolbar(10, "/sales-order/edit/");
        });
    </script>

    {{-- Click Event Handler --}}
    <script>
        $('#btn-add').on('click', function() {
            goToPage("/sales-order/create");
        });
    </script>
@endsection
