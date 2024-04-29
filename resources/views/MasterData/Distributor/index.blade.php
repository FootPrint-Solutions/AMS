@extends('template.master')

@section('content')
    {{-- Form --}}
    <div class="card">
        <div class="card-body">
            {{-- Title --}}
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">Distributor</h3>
                    </div>
                    <div class="col-auto text-end float-end ms-auto download-grp">
                        <button id="btn-add" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add
                            New Distributor</button>
                    </div>
                </div>
            </div>
            <br>

            {{-- Table --}}
            <table class="table table-striped" id="table-distributor">
                <thead>
                    <tr>
                        <th scope="col" class="table-col-no">#</th>
                        <th scope="col">Name</th>
                        <th scope="col">Address</th>
                        <th scope="col">Contact Person</th>
                        <th scope="col">Contact</th>
                        <th scope="col">E-mail</th>
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
            table = $("#table-distributor").DataTable({
                lengthMenu: [
                    [5, 10, 25],
                    [5, 10, 25]
                ],
                responsive: true,
                processing: true,
                serverSide: true,
                order: [],
                ajax: {
                    url: "/distributor/show",
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
                language: getDatatablesLanguangeConfigurations("Distributor"),
                select: true,
                rowCallback: function(row, data) {
                    if (data[8] == 0) {
                        $('td', row).addClass("text-muted");
                    }
                },
            });

            // Load DataTables toolbar component.
            appendDatatablesToolbar(7, "/distributor/edit/", null, "/distributor/toggle");

            // Add New distributor button
            $("#btn-add").on("click", function() {
                goToPage("/distributor/create");
            });

            // Add New Brand button
            $("#btn-add-brand").on("click", function() {
                goToPage("/distributor/brand/create");
            });
        });
    </script>
@endsection
