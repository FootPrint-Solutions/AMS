@extends('template.master')

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Vehicle Brand</h3>
                </div>
                <div class="col-auto text-end float-end ms-auto download-grp">
                    <button id="btn-add" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add Vehicle
                        Brand</button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row mt-2">
                <div class="col-md-1 d-flex align-items-center">
                    Status
                </div>
                <div class="col-md-4">
                    <select class="form-select form-select-sm" id="filter-status">
                        <option value="all">All</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
                <div class="col-md-1 d-flex align-items-center">
                    Visible
                </div>
                <div class="col-md-4">
                    <select class="form-select form-select-sm" id="filter-visible">
                        <option value="all">All</option>
                        <option value="1">Visible</option>
                        <option value="0">Hidden</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
    {{-- Form --}}
    <div class="card">
        <div class="card-body">
            {{-- Title --}}
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">Vehicle Brand</h3>
                    </div>
                </div>
            </div>
            <br>

            {{-- Table --}}
            <table class="table table-striped" id="table-vehicle-brand">
                <thead>
                    <tr>
                        <th scope="col" class="table-col-no">#</th>
                        <th scope="col">Name</th>
                        <th scope="col" class="table-col-status">Status</th>
                        <th scope="col" class="table-col-visible">Visible</th>
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
                pageLength: 10,
                responsive: true,
                processing: true,
                serverSide: true,
                order: [],
                ajax: {
                    url: "/vehicle/brand/show",
                    type: "POST",
                    data: function(d) {
                        d._token = "{{ csrf_token() }}";
                        d.status = $("#filter-status").val();
                        d.visible = $("#filter-visible").val();
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
                buttons: getDatatablesButtonConfigurations([{
                    extend: 'collection',
                    text: '<i class="fas fa-eye"></i> Visible',
                    className: 'btn btn-outline-secondary btn-sm',
                    action: function(e, dt, node, config) {
                        // Custom action for "Visible" button
                        var selectedRows = dt.rows({
                            selected: true
                        }).data();
                        if (selectedRows.length === 0) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'No rows selected',
                                text: 'Please select at least one row to toggle visibility.'
                            });
                            return;
                        }
                        // Example: send AJAX request to toggle visibility for selected rows
                        var ids = [];
                        for (var i = 0; i < selectedRows.length; i++) {
                            ids.push(selectedRows[i][0]); // Assuming first column is ID
                        }
                        $.ajax({
                            url: '/vehicle/brand/toggle-visible',
                            type: 'POST',
                            data: {
                                _token: "{{ csrf_token() }}",
                                ids: ids
                            },
                            success: function(response) {
                                dt.ajax.reload();
                            }
                        });
                    }
                }]),
                language: getDatatablesLanguangeConfigurations("Vehicle Brand"),
                select: true,
                rowCallback: function(row, data) {
                    if (data[4] == 0) {
                        $('td', row).addClass("text-muted");
                    }
                }
            });

            // Load DataTables toolbar component.
            appendDatatablesToolbar(4, "/vehicle/brand/edit/", null, "/vehicle/brand/toggle");

            // Add New Vehicle brand button
            $("#btn-add").on("click", function() {
                goToPage("/vehicle/brand/create");
            });

            // Filter by status
            $("#filter-status").on("change", function() {
                table.ajax.reload();
            });

            // Filter by visible
            $("#filter-visible").on("change", function() {
                table.ajax.reload();
            });
        });
    </script>
@endsection
