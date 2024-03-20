@extends('template.master')

@section('content')
    {{-- Form --}}
    <div class="card">
        <div class="card-body">
            {{-- Title --}}
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">Shop</h3>
                    </div>
                    <div class="col-auto text-end float-end ms-auto download-grp">
                        <button id="btn-add" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add New Shop</button>
                    </div>
                </div>
            </div>
            <br>

            {{-- Table --}}
            <table class="table table-striped" id="table-distributor-shop">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Name</th>
                        <th scope="col">Distributor</th>
                        <th scope="col">Address</th>
                        <th scope="col">Contact Person</th>
                        <th scope="col">Contact</th>
                        <th scope="col">E-mail</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    {{-- Detail Modal --}}
    <div class="modal modal-lg fade" id="shop-detail-modal">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="shop-detail-modal-title"></h5>

                    <div class="btn-group">
                        <button id="btn-add-detail-all" class="btn btn-info btn-sm"><i class="fas fa-search-plus"></i> Add
                            All
                            Available Batteries</button>
                        <button id="btn-add-detail" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add New
                            Detail</button>
                    </div>
                </div>

                <div class="modal-body">
                    <table class="table table-striped w-100" id="table-distributor-shop-detail">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Battery Name</th>
                                <th scope="col">Price</th>
                                <th scope="col">URL</th>
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
            table = $("#table-distributor-shop").DataTable({
                lengthMenu: [
                    [5, 10, 25],
                    [5, 10, 25]
                ],
                responsive: true,
                processing: true,
                serverSide: true,
                order: [],
                ajax: {
                    url: "/distributor/shop/show",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}"
                    }
                },
                columnDefs: [{
                    targets: [0],
                    orderable: false
                }],
                dom: "lBfrtip",
                buttons: getDatatablesButtonConfigurations([{
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
                        $('#shop-detail-modal').modal("show");

                        // Set modal title.
                        $("#shop-detail-modal-title").text("Item Details (" + selectedRows[
                            0][2] + "/" + selectedRows[0][1] + ")");

                        // Destroy previous set DataTables.
                        if ($.fn.DataTable.isDataTable("#table-distributor-shop-detail")) {
                            $("#table-distributor-shop-detail").DataTable().destroy();
                        }

                        // Set new DataTables.
                        tableTmp = table;
                        table = $("#table-distributor-shop-detail").DataTable({
                            dom: "Btp",
                            processing: true,
                            serverSide: true,
                            buttons: [],
                            ajax: {
                                url: "/distributor/shop/battery/show",
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
                                    targets: [2],
                                    className: 'dt-body-right'
                                }
                            ],
                            select: true,
                        });
                        appendDatatablesToolbar(4, "/distributor/shop/battery/edit/",
                            "/distributor/shop/battery/destroy",
                            "#table-distributor-shop-detail_wrapper");
                    },
                    className: "btn btn-outline-info btn-sm",
                }]),
                language: getDatatablesLanguangeConfigurations("Distributor Shop"),
                select: true,
            });

            // Load DataTables toolbar component.
            appendDatatablesToolbar(7, "/distributor/shop/edit/", "/distributor/shop/destroy");

            $('#shop-detail-modal').on('hidden.bs.modal', function(e) {
                table = tableTmp;
            });

            // Add New Store button
            $("#btn-add").on("click", function() {
                goToPage("/distributor/shop/create");
            });

            // Add New Detail (modal) button
            $("#btn-add-detail").on("click", function() {
                // Get selected row's id.
                let selectedRows = tableTmp.rows({
                    selected: true
                }).data().toArray();

                goToPage("/distributor/shop/battery/create/" + selectedRows[0][7] + "/" + selectedRows[0][
                    8
                ]);
            });

            // Add All Available Batteries (modal) button
            $("#btn-add-detail-all").on("click", function() {
                // Get selected row's id.
                let selectedRows = tableTmp.rows({
                    selected: true
                }).data().toArray();

                // Show an alert asking whether to replace all batteries or not.
                Swal.fire({
                    title: "Do you want to replace all previously added batteries?",
                    icon: "question",
                    showDenyButton: true,
                    showCancelButton: true,
                    confirmButtonText: "Add only new batteries",
                    denyButtonText: "Replace all"
                }).then((result) => {
                    var replaceStatus;
                    if (result.isConfirmed) {
                        // Add only new batteries
                        replaceStatus = 0;
                    } else if (result.isDenied) {
                        // Replace all
                        replaceStatus = 1;
                    }

                    // Send POST request to add all batteries.
                    $.ajax({
                        url: "/distributor/shop/battery/store/batch/" + selectedRows[0][7],
                        method: "POST",
                        data: {
                            "_token": "{{ csrf_token() }}",
                            "replace": replaceStatus
                        },
                        success: function(response) {
                            // Get response data from url (in JSON).
                            let responseData = JSON.parse(response);

                            // Show Toast message based on responseData.
                            showResponseToast(responseData.status, responseData
                                .message);

                            // Reload the detail table.
                            table.ajax.reload();
                        }
                    });
                });
            });
        });
    </script>
@endsection
