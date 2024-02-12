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

        .dataTables_filter {
            margin-top: -30px
        }
    </style>
    {{-- Form --}}
    <div class="card">
        <div class="card-body">
            {{-- Title --}}
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">Customer</h3>
                    </div>
                    <div class="col-auto text-end float-end ms-auto download-grp">

                        <a href="#" class="btn btn-outline-primary me-2"><i class="fas fa-download"></i> Download</a>
                        <button id="btn-add" class="btn btn-primary"><i class="fas fa-plus"></i> Add
                            New Customer</button>
                    </div>
                </div>
            </div>
            {{-- <div class="card-title h2">
                Customer
                <a type="button" class="btn btn-primary" id="btn-add"><i class="fa fa-plus-circle"
                        aria-hidden="true"></i> Add
                    new customer </a>
            </div> --}}
            <br>

            {{-- Table --}}
            <table class="table table-striped" id="table-customer">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Name</th>
                        <th scope="col">Contact</th>
                        <th scope="col">E-mail</th>
                        <th scope="col">Address</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // DataTables configuration

            var table = $("#table-customer").DataTable({
                lengthMenu: [
                    [5, 10, 25, -1],
                    [5, 10, 25, "All"]
                ],
                responsive: true,
                processing: true,
                serverSide: true,
                order: [],
                ajax: {
                    url: "/customer/json",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}"
                    }
                },
                columnDefs: [{
                    targets: [0],
                    orderable: false
                }],
                dom: "lBfrtp",
                buttons: [{
                        text: '<i class="fas fa-file-alt"></i> Export to PDF',
                        extend: 'pdf',
                        className: 'btn btn-outline-danger btn-sm',
                    }, {
                        text: '<i class="fas fa-file-excel"></i> Export to Excel',
                        extend: 'excel',
                        className: 'btn btn-outline-success btn-sm', // kelas CSS kustom
                    },
                    {
                        text: '<i class="fas fa-sync-alt"></i> Refresh',
                        action: function(e, dt, node, config) {
                            dt.ajax.reload();
                        },
                        className: 'btn btn-outline-primary btn-sm', // kelas CSS kustom
                    },

                ]
            });




            // table = $('#table-customer').DataTable({
            //     "dom": 'lBfrtp',
            //     "buttons": ['copy', 'csv', 'excel', 'pdf', 'print'],
            //     "searching": true,
            //     "stateSave": false,
            //     "processing": true,
            //     "serverSide": true,
            //     "paging": true,
            //     "pagingType": 'numbers',
            //     "ajax": {
            //         "url": "/customer/show",
            //         "type": "POST",
            //         "data": {
            //             "_token": "{{ csrf_token() }}"
            //         }
            //     }
            // });

            $('#btn-add').on('click', function() {
                goToPage("/customer/create");
            });
        });

        function edit(id) {
            goToPage("/customer/edit/" + id);
        }

        function destroy(id) {
            $.ajax({
                url: '/customer/destroy',
                method: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}",
                    "id": id
                },
                success: function(response) {
                    // Get response data (in JSON).
                    let responseData = JSON.parse(response);

                    // Check response data status.
                    // Status indicates the success status of company profile update.
                    if (responseData.status) {
                        // Company profile update was succeeded.
                        showSuccessToast(responseData.message);
                    } else {
                        // Company profile update was failed.
                        showErrorToast(responseData.message);
                    }

                    // Reload table with updated rows.
                    table.ajax.reload();
                }
            });
        }
    </script>
@endsection
