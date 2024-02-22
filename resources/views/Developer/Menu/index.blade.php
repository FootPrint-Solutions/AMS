@extends('template.master')

@section('content')
    {{-- Form --}}
    <div class="card">
        <div class="card-body">
            {{-- Title --}}
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">Menu Manager</h3>
                    </div>
                    <div class="col-auto text-end float-end ms-auto download-grp">
                        <button id="btn-add" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add
                            New Menu</button>
                    </div>
                </div>
            </div>
            <br>

            {{-- Table --}}
            <table class="table table-striped" id="table-menu">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Menu Parent</th>
                        <th scope="col">Menu</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <script>
        var table;

        $(document).ready(function() {
            // DataTables configuration
            table = $("#table-menu").DataTable({
                lengthMenu: [
                    [5, 10, 25],
                    [5, 10, 25]
                ],
                responsive: true,
                processing: true,
                serverSide: true,
                order: [],
                ajax: {
                    url: "/menu/show",
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
                language: getDatatablesLanguangeConfigurations("Distributor"),
                select: true,
            });

            // Load DataTables toolbar component.
            appendDatatablesToolbar(6);

            // Add New distributor button
            $("#btn-add").on("click", function() {
                goToPage("/menu/create");
            });
        });

        function edit(id) {
            goToPage("/distributor/edit/" + id);
        }

        function destroy(id) {
            sendDestroyRequest(id, "/distributor/destroy", function() {
                // Reload the index table.
                table.ajax.reload();
            });
        }
    </script>
@endsection