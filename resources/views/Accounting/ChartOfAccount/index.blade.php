@extends('template.master')

@section('content')
    {{-- Form --}}
    <div class="card">
        <div class="card-body">
            {{-- Title --}}
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">Chart of Account</h3>
                    </div>
                    <div class="col-auto text-end float-end ms-auto download-grp">
                        <button id="btn-add" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add
                            New Chart of Account</button>
                    </div>
                </div>
            </div>
            <br>

            {{-- Table --}}
            <table class="table table-striped" id="table-chart-of-account">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Number</th>
                        <th scope="col">Name</th>
                        <th scope="col">Group</th>
                        <th scope="col">Active</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <script>
        var table;

        $(document).ready(function() {
            // DataTables configuration
            table = $("#table-chart-of-account").DataTable({
                lengthMenu: [
                    [5, 10, 25],
                    [5, 10, 25]
                ],
                responsive: true,
                processing: true,
                serverSide: true,
                order: [],
                ajax: {
                    url: "/chart-of-account/show",
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
                buttons: getDatatablesButtonConfigurations(),
                language: getDatatablesLanguangeConfigurations("Chart of Account"),
                select: true,
            });

            // Load DataTables toolbar component.
            appendDatatablesToolbar(2, "/chart-of-account/edit/", "/chart-of-account/destroy");

            // Add New Chart of Account button
            $("#btn-add").on("click", function() {
                goToPage("/chart-of-account/create");
            });
        });
    </script>
@endsection
