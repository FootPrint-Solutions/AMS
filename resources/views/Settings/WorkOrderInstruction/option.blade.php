@extends('template.master')

@section('content')
    <div class="card shadow">
        <div class="card-header">
            {{-- Title --}}
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">WO Instruction Template</h3>
                </div>


                <div class="col-auto text-end float-end ms-auto download-grp">

                    {{-- Button add new template --}}
                    <button id="btn-add" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add
                        New Template</button>
                </div>

            </div>
        </div>

        <div class="card-body d-none">
        </div>
    </div>


    <div class="card shadow">
        <div class="card-body">
            {{-- Table --}}
            <table class="table table-striped" id="table-wo-template">
                <thead>
                    <tr>
                        <th scope="col" class="table-col-no">#</th>
                        <th scope="col">Name</th>
                        <th scope="col">Step Total</th>
                        <th scope="col">Status</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    {{-- modal add and edit --}}
    <div class="modal fade" id="modal-add-edit" tabindex="-1" aria-labelledby="modal-add-edit" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id=></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="form-add-edit" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="input-template-name" class="form-label">Template Name</label>
                            <input type="text" class="form-control" id="input-template-name" name="name" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="btn-save">Save changes</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let table;
        $(document).ready(function() {
            table = $("#table-wo-template").DataTable({
                dom: "lrtip",
                lengthMenu: [
                    [5, 10, 25],
                    [5, 10, 25]
                ],
                processing: true,
                serverSide: true,
                scrollX: true,
                columnDefs: [{
                    targets: [0],
                    className: 'text-center'
                }],
                ajax: {
                    url: "/wo-instruction-template/show",
                    type: "POST",
                    data: function(d) {
                        return $.extend({}, d, {
                            _token: "{{ csrf_token() }}",
                            ...getAjaxData()
                        });
                    }
                },
                dom: "lBfrtip",
                select: true,
                buttons: [
                    // add delete button
                    {
                        text: "<i class='fas fa-trash'></i> Delete Template",
                        action: function(e, dt, node, config) {
                            // Get the selected row's id.
                            let selectedRows = table.rows({
                                selected: true
                            }).data().toArray();
                            if (selectedRows.length !== 1) {
                                Swal.fire({
                                    title: "Error",
                                    text: "Please select a single row for deleting Template.",
                                    icon: "error",
                                });
                                return;
                            }

                            deleteData(selectedRows[0][4]);
                        },
                        className: "btn btn-outline-danger btn-sm",
                    },
                    {
                        text: "<i class='fas fa-toggle-on'></i> Toggle Status",
                        action: function(e, dt, node, config) {
                            // Get the selected row's id.
                            let selectedRows = table.rows({
                                selected: true
                            }).data().toArray();
                            if (selectedRows.length !== 1) {
                                Swal.fire({
                                    title: "Error",
                                    text: "Please select a single row for toggling status.",
                                    icon: "error",
                                });
                                return;
                            }

                            toggleStatus(selectedRows[0][4]);
                        },
                        className: "btn btn-outline-primary btn-sm",
                    },
                    // edit
                    {
                        text: "<i class='fas fa-edit'></i> Edit Template",
                        action: function(e, dt, node, config) {
                            // Get the selected row's id.
                            let selectedRows = table.rows({
                                selected: true
                            }).data().toArray();
                            if (selectedRows.length !== 1) {
                                Swal.fire({
                                    title: "Error",
                                    text: "Please select a single row for editing Template.",
                                    icon: "error",
                                });
                                return;
                            }

                            window.location.href =
                                `/wo-instruction-template/option/${selectedRows[0][4]}`;
                        },
                        className: "btn btn-outline-primary btn-sm",
                    },
                ]
            });
        });

        function reloadTable() {
            table.ajax.reload();
        }
    </script>

    {{-- OnClick Handler --}}
    <script>
        $(document).ready(function() {
            $('#btn-add').on('click', function() {
                $('#modal-add-edit').modal('show');
                $('#modal-add-edit').find('.modal-title').text('Add New Template');
            });

            $('#btn-save').on('click', function() {
                let data = {
                    name: $('#input-template-name').val(),
                };

                if (data.name == '') {
                    Swal.fire({
                        title: "Error",
                        text: "Please fill in the template name.",
                        icon: "error",
                    });
                    return;
                }

                $.ajax({
                    url: '/wo-instruction-template/store/option',
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        ...data
                    },
                    success: function(response) {
                        if (response.status == 'success') {
                            $('#modal-add-edit').modal('hide');
                            reloadTable();
                        } else {
                            Swal.fire({
                                title: "Error",
                                text: response.message,
                                icon: "error",
                            });
                        }
                    },
                    error: function(response) {
                        Swal.fire({
                            title: "Error",
                            text: response.responseJSON.message,
                            icon: "error",
                        });
                    }
                });
            });
        });
    </script>

    <script>
        function getAjaxData() {
            return {
                filter: $("#input-user-manager-name").val(),
                status: $("#input-user-manager-status option:selected").val(),
            };
        }

        function deleteData(id) {
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!",
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/wo-instruction-template/destroy/option`,
                        type: "POST",
                        data: {
                            id: id,
                            _token: "{{ csrf_token() }}",
                        },
                        success: function(response) {
                            if (response.status == "success") {
                                Swal.fire({
                                    title: "Success",
                                    text: response.message,
                                    icon: "success",
                                });
                                reloadTable();
                            } else {
                                Swal.fire({
                                    title: "Error",
                                    text: response.message,
                                    icon: "error",
                                });
                            }
                        },
                        error: function(response) {
                            Swal.fire({
                                title: "Error",
                                text: response.responseJSON.message,
                                icon: "error",
                            });
                        },
                    });
                }
            });
        }

        function toggleStatus(id) {
            $.ajax({
                url: `/wo-instruction-template/toggle-status/option`,
                type: "POST",
                data: {
                    id: id,
                    _token: "{{ csrf_token() }}",
                },
                success: function(response) {
                    if (response.status == "success") {
                        Swal.fire({
                            title: "Success",
                            text: response.message,
                            icon: "success",
                        });
                        reloadTable();
                    } else {
                        Swal.fire({
                            title: "Error",
                            text: response.message,
                            icon: "error",
                        });
                    }
                },
                error: function(response) {
                    Swal.fire({
                        title: "Error",
                        text: response.responseJSON.message,
                        icon: "error",
                    });
                },
            });
        }
    </script>
@endsection
