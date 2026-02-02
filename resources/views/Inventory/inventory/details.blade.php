@extends('template.master')

@section('content')
    <div class="card">
        <div class="card-body">
            {{-- Title --}}
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">Inventory Details</h3>
                    </div>
                    <div class="col-auto">
                        <a href="/inventory" class="btn btn-outline-secondary btn-sm">
                            <i class="fa-solid fa-arrow-left"></i> Back to Inventory
                        </a>
                    </div>
                </div>
            </div>
            <br>

            <div class="table-responsive">
                <table class="table table-striped" id="table-inventory-details">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">SO/PO Date</th>
                            <th scope="col">SO/PO Number</th>
                            <th scope="col">Customer/Supplier</th>
                            <th scope="col">Distributor Shop</th>
                            <th scope="col">Battery</th>
                            <th scope="col">Production Code</th>
                            <th scope="col">Type</th>
                            <th scope="col">Qty</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <script>
        $(function() {
            // DataTables config for table-inventory-details
            $("#table-inventory-details").DataTable({
                lengthMenu: [
                    [10, 25, 50, 100],
                    [10, 25, 50, 100]
                ],
                pageLength: 25,
                responsive: true,
                processing: true,
                serverSide: true,
                order: [
                    [1, 'desc']
                ],
                ajax: {
                    url: "/inventory/details/show",
                    type: "POST",
                    data: function(d) {
                        d._token = "{{ csrf_token() }}";
                    }
                },
                columns: [{
                        data: 0,
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        },
                        orderable: false
                    },
                    {
                        data: 1,
                        orderable: true
                    },
                    {
                        data: 2,
                        orderable: true
                    },
                    {
                        data: 3,
                        orderable: true
                    },
                    {
                        data: 4,
                        orderable: true
                    },
                    {
                        data: 5,
                        orderable: true
                    },
                    {
                        data: 6,
                        orderable: true
                    },
                    {
                        data: 7,
                        className: "text-center",
                        orderable: true
                    },
                    {
                        data: 8,
                        className: "text-end",
                        orderable: true
                    }
                ],
                dom: "lBfrtip",
                buttons: [{
                    extend: 'excel',
                    text: '<i class="fa-solid fa-file-excel"></i> Export',
                    className: "btn btn-outline-success btn-sm ml-1",
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9]
                    }
                }],
                language: getDatatablesLanguangeConfigurations("Inventory Details")
            });
        });
    </script>
@endsection
