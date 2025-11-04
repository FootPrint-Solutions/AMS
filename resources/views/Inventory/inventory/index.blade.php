@extends('template.master')

@section('content')
    {{-- Form --}}
    <div class="card">

        <div class="card-body">
            {{-- Title --}}
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">Inventory Spreadsheet</h3>
                    </div>
                </div>
            </div>
            <br>

            {{-- Table --}}
            <div class="table-responsive">
                <table class="table table-striped" id="table-battery">
                    <thead>
                        <tr>
                            <th scope="col" class="table-col-no">#</th>
                            <th scope="col">ID</th>
                            <th scope="col">Name</th>
                            <th scope="col">Stock</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($data['inventories'] as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item[0] }}</td>
                                <td>{{ $item[2] }}</td>
                                <td>{{ formatPrice($item[6]) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            {{-- Title --}}
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">Inventory</h3>
                    </div>
                </div>
            </div>
            <br>

            {{-- Table Inventory Details --}}
            <div class="table-responsive">
                <table class="table table-striped" id="table-inventory-details">
                    <thead>
                        <tr>
                            <th scope="col" class="table-col-no">#</th>
                            <th scope="col">ID</th>
                            <th scope="col">Inventory ID</th>
                            <th scope="col">Battery ID</th>
                            <th scope="col">Type</th>
                            <th scope="col">Reference</th>
                            <th scope="col">Quantity</th>
                            <th scope="col">Sold</th>
                            <th scope="col">Sold At</th>
                            <th scope="col">Note</th>
                            <th scope="col">Created At</th>
                            <th scope="col">Updated At</th>
                            <th scope="col">Reference ID</th>
                            <th scope="col">Reference Type</th>
                        </tr>
                    </thead>
                </table>
            </div>


        </div>
    </div>

    <script>
        var table;

        $(document).ready(function() {
            // DataTables configuration
            table = $("#table-battery").DataTable({
                lengthMenu: [
                    [5, 10, 25],
                    [5, 10, 25]
                ],
                pageLength: 10,
                responsive: true,
                processing: true,
                order: [],
                columnDefs: [{
                    targets: [0],
                    orderable: false
                }, {
                    targets: [-1],
                    className: 'dt-body-right'
                }],
                dom: "lBfrtip",
                buttons: getDatatablesButtonConfigurations(),
                language: getDatatablesLanguangeConfigurations("Battery"),
            });

            // DataTables configuration
            $("#table-inventory-details").DataTable({
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
                    url: "/inventory/details/show",
                    type: "POST",
                    data: function(d) {
                        d._token = "{{ csrf_token() }}";
                    }
                },
                columnDefs: [{
                        targets: [0],
                        orderable: false
                    },
                    {
                        targets: [-4, -5, -6],
                        className: 'dt-body-right'
                    }
                ],
                dom: "lBfrtip",
                buttons: getDatatablesButtonConfigurations(),
                language: getDatatablesLanguangeConfigurations("Inventory"),
            });
        });
    </script>
@endsection
