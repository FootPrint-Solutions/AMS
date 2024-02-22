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
                        <h3 class="page-title">Customer</h3>
                    </div>
                    <div class="col-auto text-end float-end ms-auto download-grp">
                        <button id="btn-add" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add
                            New Customer</button>
                    </div>
                </div>
            </div>
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
        var table;
        $(document).ready(function() {
            // DataTables configuration

            table = $("#table-customer").DataTable({
                lengthMenu: [
                    [5, 10, 25],
                    [5, 10, 25]
                ],
                responsive: true,
                processing: true,
                serverSide: true,
                order: [],
                ajax: {
                    url: "/customer/show",
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
                ],
                language: {
                    searchPlaceholder: "Search Customer",
                    search: "",
                    lengthMenu: "_MENU_ entries | ",
                },
                select: true,
            });

            // Load DataTables toolbar component.
            appendDatatablesToolbar(5);

            $('#btn-add').on('click', function() {
                goToPage("/customer/create");
            });
        });

        function edit(id) {
            goToPage("/customer/edit/" + id);
        }

        function destroy(id) {
            sendDestroyRequest(id, "/customer/destroy");

            // Reload the index table.
            table.ajax.reload();
        }
    </script>
@endsection
