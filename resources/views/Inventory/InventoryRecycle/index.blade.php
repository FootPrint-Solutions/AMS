@extends('template.master')

@section('content')
    <div class="card bg-white">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Inventory Recycle</h3>
                </div>
            </div>
        </div>
        <div class="card-body">
            {{-- filter tanggal --}}
            <div class="row mt-2">
                <div class="col-md-1 d-flex align-items-center">
                    Date
                </div>

                <div class="col-md-4">
                    <div class="row align-items-center">
                        <div class="col-5">
                            <input type="date" class="form-control" id="input-inventory-recycle-date-start"
                                onchange="reloadTable()">
                        </div>
                        <div class="col-2 text-center">
                            to
                        </div>
                        <div class="col-5">
                            <input type="date" class="form-control" id="input-inventory-recycle-date-end"
                                onchange="reloadTable()">
                        </div>
                    </div>
                </div>

                <div class="col-md-1"></div>

            </div>
        </div>
    </div>

    {{-- Form --}}
    <div class="card">
        <div class="card-body">

            {{-- Table --}}
            <table class="table table-striped" id="table-inventory-recycle">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">SO Date</th>
                        <th scope="col">SO Number</th>
                        <th scope="col">Customer Name</th>
                        <th scope="col">Battery Name</th>
                        <th scope="col">Battery Qty</th>
                        <th scope="col">Battery Price</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <script>
        var table;

        $(document).ready(function() {
            // DataTables configuration
            table = $("#table-inventory-recycle").DataTable({
                lengthMenu: [
                    [5, 10, 25],
                    [5, 10, 25]
                ],
                responsive: true,
                processing: true,
                serverSide: true,
                order: [],
                ajax: {
                    url: "/inventory/recycle/show",
                    type: "POST",
                    data: function(d) {
                        d._token = "{{ csrf_token() }}";
                        d.dateStart = document.getElementById('input-inventory-recycle-date-start')
                            .value;
                        d.dateEnd = document.getElementById('input-inventory-recycle-date-end').value;
                    }
                },
                columnDefs: [{
                        targets: [0],
                        orderable: false
                    },
                    {
                        targets: [5],
                        className: "text-end",
                    },
                    {
                        targets: [6],
                        className: "text-end table-col-price",
                    },
                ],
                dom: "lBfrtip",
                buttons: getDatatablesButtonConfigurations(),
                language: getDatatablesLanguangeConfigurations("Inventory Recycle"),
                select: true,
            });


            // Add New Vehicle recycle button
            $("#btn-add").on("click", function() {
                goToPage("/inventory/recycle/create");
            });
        });

        function reloadTable() {
            var dateStart = document.getElementById('input-inventory-recycle-date-start').value;
            var dateEnd = document.getElementById('input-inventory-recycle-date-end').value;

            // Reload the table.
            table.ajax.reload(null, false);
        }
    </script>
@endsection
