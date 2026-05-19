@extends('template.master')

@section('content')
    {{-- Form --}}
    <div class="card">
        <div class="card-body">
            {{-- Title --}}
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">Battery Price Promo Manager</h3>
                    </div>
                    <div class="col-auto text-end float-end ms-auto download-grp">
                        <button id="btn-add" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add
                            New Promo</button>
                    </div>
                </div>
            </div>
            <br>

            {{-- Table --}}
            <table class="table table-striped" id="table-promo">
                <thead>
                    <tr>
                        <th scope="col" class="table-col-no">#</th>
                        <th scope="col">Name</th>
                        <th scope="col">Period Start</th>
                        <th scope="col">Period End</th>
                        <th scope="col" class="table-col-status">Status</th>
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
            table = $("#table-promo").DataTable({
                lengthMenu: [
                    [5, 10, 25],
                    [5, 10, 25]
                ],
                responsive: true,
                processing: true,
                serverSide: true,
                order: [],
                ajax: {
                    url: "/promo/show",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}"
                    }
                },
                columnDefs: [{
                    targets: [0],
                    orderable: false
                }, {
                    targets: [0, -1],
                    className: 'dt-body-center'
                }],
                dom: "lBfrtip",
                buttons: getDatatablesButtonConfigurations(
                    [{
                        // Update Price Retail
                        text: '<i class="fas fa-dollar-sign"></i> Update Price Retail',
                        className: "btn btn-outline-secondary btn-sm",
                        action: function(e, dt, node, config) {
                            var selectedData = table.rows({
                                selected: true
                            }).data();

                            if (selectedData.length == 0) {
                                swal.fire(
                                    "No promo selected",
                                    "Please select a promo to update its price retail.",
                                    "warning"
                                );
                            } else if (selectedData.length > 1) {
                                swal.fire(
                                    "Multiple promos selected",
                                    "Please select only one promo to update its price retail.",
                                    "warning"
                                );
                            } else {
                                var promoId = selectedData[0][5];

                                swal.fire({
                                    title: "Are you sure?",
                                    text: "You are about to update the price retail for this promo.",
                                    icon: "warning",
                                    showCancelButton: true,
                                    confirmButtonText: "Yes, update it!",
                                    cancelButtonText: "No, cancel!"
                                }).then((result) => {
                                    if (result.isConfirmed) {

                                        // loading
                                        swal.fire({
                                            title: "Updating...",
                                            text: "Please wait while the price retail is being updated.",
                                            allowOutsideClick: false,
                                            didOpen: () => {
                                                swal.showLoading();
                                            }
                                        });

                                        $.ajax({
                                            url: "/promo/update-price-retail/" +
                                                promoId,
                                            type: "POST",
                                            data: {
                                                _token: "{{ csrf_token() }}"
                                            },
                                            success: function(response) {
                                                var res = (
                                                        typeof response ===
                                                        "string") ? JSON
                                                    .parse(
                                                        response) :
                                                    response;
                                                var message = res && res
                                                    .message ? res.message :
                                                    "An unexpected response was received.";

                                                if (res && res.status ===
                                                    "success") {
                                                    swal.fire(
                                                        "Updated!",
                                                        message,
                                                        "success"
                                                    );
                                                } else {
                                                    swal.fire(
                                                        "Error!",
                                                        message,
                                                        "error"
                                                    );
                                                }

                                                table.ajax.reload(null,
                                                    false);
                                            },
                                            error: function(xhr, status,
                                                error) {
                                                swal.fire(
                                                    "Error!",
                                                    "An error occurred while updating the price retail.",
                                                    "error"
                                                );
                                                table.ajax.reload(null,
                                                    false);
                                            }
                                        });
                                    } else {
                                        swal.fire(
                                            "Cancelled",
                                            "The price retail update has been cancelled.",
                                            "info"
                                        );
                                    }
                                });
                            }
                        }
                    }]
                ),
                language: getDatatablesLanguangeConfigurations("Promo"),
                select: true,
                rowCallback: function(row, data) {
                    if (data[6] == 0) {
                        $('td', row).addClass("text-muted");
                    }
                }
            });

            // Load DataTables toolbar component.
            appendDatatablesToolbar(5, "/promo/edit/", null, "/promo/toggle");
        });
    </script>

    {{-- Click Handler --}}
    <script>
        $('#btn-add').on('click', function() {
            goToPage("/promo/create");
        });
    </script>
@endsection
