@extends('template.master')

@section('content')
    {{-- Form --}}
    <div class="card">
        <div class="card-body">
            {{-- Title --}}
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">Battery Subbrand Category</h3>
                    </div>
                    <div class="col-auto text-end float-end ms-auto download-grp">
                        <button id="btn-add" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add
                            New Battery Subbrand Category</button>
                    </div>
                </div>
            </div>
            <br>

            {{-- Table --}}
            <table class="table table-striped" id="table-battery-subbrand">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Name</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <script>
        var table;

        $(document).ready(function() {
            // DataTables configuration
            table = $("#table-battery-subbrand").DataTable({
                lengthMenu: [
                    [5, 10, 25],
                    [5, 10, 25]
                ],
                responsive: true,
                processing: true,
                serverSide: true,
                order: [],
                ajax: {
                    url: "/battery/subbrand/show",
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
                language: getDatatablesLanguangeConfigurations("Battery Subbrand Category"),
                select: true,
            });

            // Load DataTables toolbar component.
            appendDatatablesToolbar(2);

            // Add New Vehicle brand button
            $("#btn-add").on("click", function() {
                goToPage("/battery/subbrand/create");
            });
        });

        function edit(id) {
            goToPage("/battery/subbrand/edit/" + id);
        }

        function destroy(id) {
            sendDestroyRequest(id, "/battery/subbrand/destroy", function() {
                // Reload the index table.
                table.ajax.reload();
            });
        }
    </script>
@endsection
