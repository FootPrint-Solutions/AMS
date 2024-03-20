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
                        <button id="btn-add-parent" class="btn btn-secondary btn-sm"><i class="fas fa-plus"></i> Add New Menu
                            Parent</button>
                        <button id="btn-add" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add New
                            Menu</button>
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
                dom: "Brtp",
                buttons: [{
                    text: 'Refresh Menu',
                    className: "btn btn-outline-primary btn-sm",
                    action: function() {
                        $.ajax({
                            url: '/menu/refresh',
                            type: 'GET',
                            success: function(response) {
                                location.reload();
                            }
                        });
                    }
                }],
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
                select: true,
                rowCallback: function(row, data) {
                    console.log(data, data[4]);
                    if (data[4] == "1") {
                        $(row).addClass("bg-secondary");
                    }
                }
            });

            // Load DataTables toolbar component.
            appendDatatablesToolbar(3, "/menu/edit/", "/menu/destroy");

            // Add New Menu button
            $("#btn-add").on("click", function() {
                goToPage("/menu/create");
            });

            // Add New Menu Parent button
            $("#btn-add-parent").on("click", function() {
                goToPage("/menu/parent/create");
            });
        });
    </script>
@endsection
