@extends('template.master')

@section('content')
    {{-- Form --}}
    <div class="card d-none">

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
                            <th scope="col">#</th>
                            <th scope="col">SO/PO Date</th>
                            <th scope="col">SO/PO Number</th>
                            <th scope="col">Customer/Supplier</th>
                            <th scope="col">Distributor Shop</th>
                            <th scope="col">Battery</th>
                            <th scope="col">Production Code</th>
                            <th scope="col">Type</th>
                            <th scope="col">Qty</th>
                            <th scope="col">Price</th>
                            <th scope="col">id</th>
                            <th scope="col">sold</th>
                        </tr>
                    </thead>
                </table>
            </div>


        </div>
    </div>

    <script>
        $(function() {
            // DataTables config for table-battery
            $("#table-battery").DataTable({
                lengthMenu: [
                    [5, 10, 25],
                    [5, 10, 25]
                ],
                pageLength: 10,
                responsive: true,
                processing: true,
                order: [],
                columnDefs: [{
                        targets: 0,
                        orderable: false
                    },
                    {
                        targets: -1,
                        className: 'dt-body-right'
                    }
                ],
                dom: "lBfrtip",
                buttons: getDatatablesButtonConfigurations(),
                language: getDatatablesLanguangeConfigurations("Battery"),
            });

            // DataTables config for table-inventory-details
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
                    url: "/inventory/show",
                    type: "POST",
                    data: function(d) {
                        d._token = "{{ csrf_token() }}";
                        d.dateStart = $('#input-inventory-recycle-date-start').val();
                        d.dateEnd = $('#input-inventory-recycle-date-end').val();
                    }
                },
                columnDefs: [{
                        targets: 0,
                        orderable: false
                    },
                    {
                        targets: 5,
                        className: "text-end"
                    },
                    {
                        targets: [6, 7],
                        className: "text-end table-col-price"
                    },
                    {
                        targets: [10, 11],
                        visible: false,
                        searchable: false
                    }
                ],
                dom: "lBfrtip",
                buttons: getDatatablesButtonConfigurations([{
                    text: '<i class="fa-solid fa-cart-arrow-down"></i> Sold Out',
                    className: "btn btn-outline-danger btn-sm ml-1",
                    action: function(e, dt) {
                        var data = dt.rows({
                            selected: true
                        }).data().toArray();
                        if (data.length) {
                            var ids = data.map(item => item[10]);
                            soldOut(ids);
                        } else {
                            swal.fire({
                                icon: "warning",
                                title: "Warning",
                                text: "Please select at least one item.",
                            });
                        }
                    }
                }]),
                language: getDatatablesLanguangeConfigurations("Inventory Recycle"),
                select: true,
                multiselect: true,
                rowCallback: function(row, data) {
                    if (data[11] == 1) $(row).find('td').addClass("text-danger");
                }
            });
        });
    </script>
@endsection
