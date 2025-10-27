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
                        <h3 class="page-title">Supplier</h3>
                    </div>
                    <div class="col-auto text-end float-end ms-auto download-grp">
                        <button id="btn-add" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add
                            New Supplier</button>
                    </div>
                </div>
            </div>
            <br>

            {{-- Table --}}
            <table class="table table-striped" id="table-supplier">
                <thead>
                    <tr>
                        <th scope="col" class="table-col-no">#</th>
                        <th scope="col">Name</th>
                        <th scope="col">Contact</th>
                        <th scope="col">E-mail</th>
                        <th scope="col">Address</th>
                        <th scope="col" class="table-col-status">Status</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <script>
        var table;
        $(document).ready(function() {
            // DataTables configuration
            table = $("#table-supplier").DataTable({
                lengthMenu: [
                    [5, 10, 25],
                    [5, 10, 25]
                ],
                responsive: true,
                processing: true,
                serverSide: true,
                order: [],
                ajax: {
                    url: "/supplier/show",
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
                buttons: getDatatablesButtonConfigurations(),
                language: getDatatablesLanguangeConfigurations("Supplier"),
                select: true,
                rowCallback: function(row, data) {
                    if (data[7] == 0) {
                        $('td', row).addClass("text-muted");
                    }
                }
            });

            // Load DataTables toolbar component.
            appendDatatablesToolbar(6, "/supplier/edit/", null, "/supplier/toggle");

            $('#btn-add').on('click', function() {
                goToPage("/supplier/create");
            });
        });
    </script>
@endsection
