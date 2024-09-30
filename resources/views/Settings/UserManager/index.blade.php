@extends('template.master')

@section('content')
    <div class="card shadow">
        <div class="card-header">
            {{-- Title --}}
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">User Manager</h3>
                </div>


                <div class="col-auto text-end float-end ms-auto download-grp">

                    {{-- Button add new user --}}
                    <button id="btn-add" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add
                        New User</button>
                </div>

            </div>
        </div>

        <div class="card-body">
            {{-- Filter --}}
            <div class="row mt-2">
                <div class="col-md-1 d-flex align-items-center">
                    Filter
                </div>

                <div class="col-md-4">
                    <input type="text" placeholder="Username, name, email" id="input-user-manager-name"
                        class="form-control" onkeyup="reloadTable()">
                </div>

                <div class="col-md-1"></div>
            </div>
        </div>
    </div>


    <div class="card shadow">
        <div class="card-body">
            {{-- Table --}}
            <table class="table table-striped" id="table-user-manager">
                <thead>
                    <tr>
                        <th scope="col" class="table-col-no">#</th>
                        <th scope="col">Name</th>
                        <th scope="col">Username</th>
                        <th scope="col">Email</th>
                        <th scope="col">Role</th>
                        <th scope="col" class="table-col-action">Edit</th>
                        <th scope="col" class="table-col-action">Delete</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <script>
        let table;
        $(document).ready(function() {
            table = $("#table-user-manager").DataTable({
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
                    url: "/user-manager/show",
                    type: "POST",
                    data: function(d) {
                        return $.extend({}, d, {
                            _token: "{{ csrf_token() }}",
                            ...getAjaxData()
                        });
                    }
                }
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
                goToPage("/user-manager/create");
            });

            $("#input-user-manager-excel").on("change", function() {
                sendImportRequest("/user-manager/import", this.files[0]);
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
    </script>
@endsection
