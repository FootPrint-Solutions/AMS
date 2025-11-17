@extends('template.master')

@section('content')
    <div class="card shadow-lg">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Billing</h3>
                </div>

                <div class="col-auto text-end float-end ms-auto download-grp">
                    <button id="btn-add" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add
                        New Billing</button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row align-items-center mb-3">
                <div class="col-md-1 text-md-right text-left font-weight-bold">
                    Date
                </div>
                <div class="col-md-4">
                    <div class="row align-items-center">
                        <div class="col-5 pr-0">
                            <input type="date" class="form-control" id="input-billing-date-start"
                                onchange="reloadTable()">
                        </div>
                        <div class="col-2 text-center px-0">
                            to
                        </div>
                        <div class="col-5 pl-0">
                            <input type="date" class="form-control" id="input-billing-date-end" onchange="reloadTable()">
                        </div>
                    </div>
                </div>
                <div class="col-md-1 text-md-right text-left font-weight-bold">
                    Status
                </div>
                <div class="col-md-2">
                    <select class="form-control" id="billing-status-filter" onchange="onStatusFilterChange()">
                        <option value="all">All</option>
                        <option value="draft">Draft</option>
                        <option value="posted">Printed</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-lg">
        <div class="card-body">
            {{-- Table --}}
            <div class="table-responsive">
                <table class="table table-striped" id="table-billing">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
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
            table = $("#table-billing").DataTable({
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
                    url: "/billing/show",
                    type: "POST",
                    data: function(d) {
                        d._token = "{{ csrf_token() }}";
                        d.status = $("#billing-status-filter").val();
                    }
                },
                columns: [{
                        data: 0,
                        orderable: false
                    },
                    {
                        data: 1
                    },
                    {
                        data: 2
                    },
                    {
                        data: 3,
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
            appendDatatablesToolbar(4, "/billing/edit/", null, "/billing/toggle");

            $("#btn-add").on("click", function() {
                goToPage("/billing/create");
            });

            $("#billing-status-filter").on("change", function() {
                table.ajax.reload();
            });
        });
    </script>
@endsection
