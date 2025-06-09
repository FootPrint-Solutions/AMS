@extends('template.master')

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Battery Subbrand Category
                    </h3>
                </div>
                <div class="col-auto text-end float-end ms-auto download-grp">
                    <button id="btn-add" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add
                        New Battery Subbrand Category</button>
                </div>
            </div>
        </div>
        <div class="card-body">
            {{-- filter status --}}
            <div class="row mt-2">
                <div class="col-md-2 d-flex align-items-center">
                    Is Visible Online
                </div>

                <div class="col-md-4">
                    <select class="form-select form-select-sm" id="filter-visible">
                        <option value="all">All</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
            </div>
        </div>
    </div>



    {{-- Form --}}
    <div class="card">
        <div class="card-body">
            {{-- Table --}}
            <table class="table table-striped" id="table-battery-subbrand">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Name</th>
                        <th scope="col">Is Visible Online</th>
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
                    data: function(d) {
                        d._token = "{{ csrf_token() }}";
                        d.filter_visible = $("#filter-visible").val();
                    }
                },
                columnDefs: [{
                    targets: [0],
                    orderable: false
                }],
                dom: "lBfrtip",
                buttons: getDatatablesButtonConfigurations([
                    // is visible online
                    {
                        text: '<i class="fas fa-eye"></i> Toggle Visibility',
                        className: "btn btn-outline-secondary btn-sm",
                        action: function(e, dt, node, config) {
                            var selectedRows = dt.rows({
                                selected: true
                            }).data();

                            if (selectedRows.length > 0) {
                                var ids = $.map(selectedRows, function(row) {
                                    return row[3];
                                });

                                $.ajax({
                                    url: "/battery/subbrand/toggle-visibility",
                                    type: "POST",
                                    data: {
                                        _token: "{{ csrf_token() }}",
                                        ids: ids
                                    },
                                    success: function(response) {
                                        dt.ajax.reload();
                                        showAlert(
                                            "Visibility toggled successfully.");
                                    },
                                    error: function(xhr) {
                                        showAlert("Error toggling visibility.",
                                            "error");
                                    }
                                });
                            } else {
                                showAlert("Please select at least one subbrand category.",
                                    "warning");
                            }
                        }
                    }
                ]),
                language: getDatatablesLanguangeConfigurations("Battery Subbrand Category"),
                select: true,
            });

            // Load DataTables toolbar component.
            appendDatatablesToolbar(3, "/battery/subbrand/edit/", "/battery/subbrand/destroy");

            // Add New Vehicle brand button
            $("#btn-add").on("click", function() {
                goToPage("/battery/subbrand/create");
            });

            // Filter by visibility
            $("#filter-visible").on("change", function() {
                table.ajax.reload();
            });
        });
    </script>
@endsection
