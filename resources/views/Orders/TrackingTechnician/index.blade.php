@extends('template.master')

@section('content')
    <style>
        .btn-excel {
            background-color: #4CAF50;
            /* Green */
            border: none;
            color: white;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
            border-radius: 5px;
        }
    </style>
    {{-- Form --}}
    <div class="card">
        <div class="card-body">
            {{-- Title --}}
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">Tracking Technician</h3>
                    </div>
                </div>
            </div>
            <br>

            {{-- Table --}}
            <table class="table table-striped" id="table-tracking-technician">
                <thead>
                    <tr>
                        <th scope="col" class="table-col-no">#</th>
                        <th scope="col">Work Order Number</th>
                        <th scope="col">Customer</th>
                        <th scope="col">Address Customer</th>
                        <th scope="col">Arrived</th>
                        <th scope="col">Link Tracking</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <script>
        var table;
        $(document).ready(function() {
            // DataTables configuration
            table = $("#table-tracking-technician").DataTable({
                lengthMenu: [
                    [5, 10, 25],
                    [5, 10, 25]
                ],
                responsive: true,
                processing: true,
                serverSide: true,
                order: [],
                ajax: {
                    url: "/tracking-technician/show",
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
                    className: 'text-center'
                }],
                dom: "lBfrtip",
                buttons: getDatatablesButtonConfigurations([
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
                            let ids = selectedRows.map(row => row[6]);
                            sendDestroyRequest(ids, "/tracking-technician/delete", function() {
                                // Reload the index table.
                                table.ajax.reload();
                            });
                        }
                    },

                    // share to whatsapp button 
                    {
                        text: "<i class='fab fa-whatsapp'></i> Share to WhatsApp",
                        className: "btn btn-outline-success btn-sm ml-1",
                        action: function(e, dt, node, config) {
                            var selectedRows = table.rows({
                                selected: true
                            }).data().toArray();

                            if (selectedRows.length === 0) {
                                Swal.fire({
                                    title: "Error",
                                    text: "Please select at least one row for sharing.",
                                    icon: "error",
                                });
                                return;
                            }
                            let ids = selectedRows.map(row => row[6]);
                            sendShareRequest(ids, "/tracking-technician/share", function() {
                                // Reload the index table.
                                table.ajax.reload();
                            });
                        }
                    },
                ]),
                language: getDatatablesLanguangeConfigurations("Tracking Technician"),
                select: true,
                rowCallback: function(row, data) {
                    if (data[7] == 0) {
                        $('td', row).addClass("text-muted");
                    }
                }
            });
        });

        function sendShareRequest(ids, url, callback) {
            $.ajax({
                url: url,
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    ids: ids
                },
                success: function(response) {
                    var response = JSON.parse(response);
                    if (response.status == true) {
                        Swal.fire({
                            title: "Success",
                            text: response.message,
                            icon: "success",
                        });
                        callback();
                    } else {
                        Swal.fire({
                            title: "Error",
                            text: response.message,
                            icon: "error",
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        title: "Error",
                        text: "An error occurred while processing your request.",
                        icon: "error",
                    });
                }
            });
        }
    </script>
@endsection
