@extends('template.master')

@section('content')
    {{-- Form --}}
    <div class="card">
        <div class="card-body">
            {{-- Title --}}
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">Vehicle Brand</h3>
                    </div>
                    <div class="col-auto text-end float-end ms-auto download-grp">
                        <button id="btn-add" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add
                            Vehicle Brand</button>
                    </div>
                </div>
            </div>
            <br>

            {{-- Table --}}
            <table class="table table-striped" id="table-vehicle-brand">
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
            table = $("#table-vehicle-brand").DataTable({
                lengthMenu: [
                    [5, 10, 25],
                    [5, 10, 25]
                ],
                responsive: true,
                processing: true,
                serverSide: true,
                order: [],
                ajax: {
                    url: "/vehicle/brand/show",
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
                    searchPlaceholder: "Search Vehicle Brand",
                    search: "",
                    lengthMenu: "_MENU_ entries | ",
                },
                select: true,
            });

            // Load DataTables toolbar component.
            appendDatatablesToolbar(2);

            // Add New Vehicle brand button
            $("#btn-add").on("click", function() {
                goToPage("/vehicle/brand/create");
            });
        });

        function edit(id) {
            goToPage("/vehicle/brand/edit/" + id);
        }

        function destroy(id) {
            sendDestroyRequest(id, "/vehicle/brand/destroy");

            // Reload the index table.
            table.ajax.reload();
        }
    </script>
@endsection
