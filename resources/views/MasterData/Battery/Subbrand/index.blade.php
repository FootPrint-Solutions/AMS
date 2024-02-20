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
                    searchPlaceholder: "Search Battery Brand",
                    search: "",
                    lengthMenu: "_MENU_ entries | ",
                },
                select: true,
            });

            $(".dt-buttons").append(
                '<div class="btn-group"><button class="btn btn-outline-primary btn-sm edit-selected"><i class="fas fa-pencil"></i> Edit</button><button class="btn btn-outline-danger btn-sm delete-selected ml-1" > <i class="fas fa-trash"></i> Delete</button></div>'
            );


            $('.edit-selected').on('click', function() {
                var selectedRows = table.rows({
                    selected: true
                }).data().toArray();
                if (selectedRows.length === 0) {
                    Swal.fire({
                        title: "Error",
                        text: "Please select at least one row to edit.",
                        icon: "error",
                    });
                    return;
                }
                var selectedRow = selectedRows[0];
                var id = selectedRow[2];
                edit(id);
            });

            $('.delete-selected').on('click', function() {
                var selectedRows = table.rows({
                    selected: true
                }).data().toArray();
                if (selectedRows.length === 0) {
                    Swal.fire({
                        title: "Error",
                        text: "Please select at least one row to delete.",
                        icon: "error",
                    });
                    return;
                }
                var selectedRow = selectedRows[0];
                var id = selectedRow[2];
                destroy(id);
            });

            // Add New Vehicle brand button
            $("#btn-add").on("click", function() {
                goToPage("/battery/subbrand/create");
            });
        });

        function edit(id) {
            goToPage("/battery/subbrand/edit/" + id);
        }

        function destroy(id) {
            $.ajax({
                url: "/battery/subbrand/destroy",
                method: "POST",
                data: {
                    "_token": "{{ csrf_token() }}",
                    "id": id
                },
                success: function(response) {
                    // Get response data (in JSON).
                    let responseData = JSON.parse(response);

                    // Check response data status.
                    if (responseData.status) {
                        // Delete process was succeeded.
                        showSuccessToast(responseData.message);
                    } else {
                        // Delete process was failed.
                        showErrorToast(responseData.message);
                    }

                    // Reload table with updated rows.
                    table.ajax.reload();
                }
            });
        }
    </script>
@endsection
