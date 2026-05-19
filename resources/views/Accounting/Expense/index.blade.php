@extends('template.master')

@section('content')
    <div class="card">
        <div class="card-body">
            {{-- Title & Add Button --}}
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title mb-0">Expense List</h3>
                    </div>
                    <div class="col-auto text-end float-end ms-auto download-grp">
                        <button id="btn-add" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Add New Expense
                        </button>
                    </div>
                </div>
            </div>
            <br>
            {{-- Filter --}}
            <div class="row align-items-center mb-3">
                <div class="col-md-2 fw-bold">
                    Filter Status
                </div>
                <div class="col-md-4">
                    <select class="form-select form-select-sm" id="filter-status">
                        <option value="all">All</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
            </div>
            {{-- Table --}}
            <div class="table-responsive">
                <table class="table table-striped" id="table-expense">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Chart of Account</th>
                            <th scope="col">Name</th>
                            <th scope="col">Description</th>
                            <th scope="col">Status</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <script>
        var table;

        $(document).ready(function() {
            table = $("#table-expense").DataTable({
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
                    url: "/expense/show",
                    type: "POST",
                    data: function(d) {
                        d._token = "{{ csrf_token() }}";
                        d.status = $("#filter-status").val();
                    }
                },
                columns: [{
                        data: 0,
                        orderable: false
                    },
                    {
                        data: 1,
                        render: function(data, type, row) {
                            return data ? data : '-';
                        }
                    },
                    {
                        data: 2
                    },
                    {
                        data: 3
                    },
                    {
                        data: 4,
                        render: function(data, type, row) {
                            if (data == 1 || data == 'Active') {
                                return '<i class="fa-solid fa-circle text-success"></i>';
                            } else {
                                return '<i class="fa-solid fa-circle text-danger"></i>';
                            }
                        },
                        className: 'dt-body-center'
                    }
                ],
                columnDefs: [{
                    targets: [0],
                    orderable: false,
                }],
                dom: "lBfrtip",
                buttons: getDatatablesButtonConfigurations(),
                select: true,
                rowCallback: function(row, data) {
                    if (data[3] == 'Inactive') {
                        $('td', row).addClass("text-muted");
                    }
                }
            });

            // Load DataTables toolbar component.
            appendDatatablesToolbar(5, "/expense/edit/", null, "/expense/toggle");

            $("#btn-add").on("click", function() {
                goToPage("/expense/create");
            });

            $("#filter-status").on("change", function() {
                table.ajax.reload();
            });
        });
    </script>
@endsection
