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
                pageLength: 10,
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
                    {
                        targets: [7],
                        className: "text-end table-col-price",
                    },
                    {
                        targets: [10],
                        visible: false,
                        searchable: false
                    },
                    {
                        targets: [11],
                        visible: false,
                        searchable: false
                    }
                ],
                dom: "lBfrtip",
                buttons: getDatatablesButtonConfigurations(),
                language: getDatatablesLanguangeConfigurations("Inventory Recycle"),
                select: true,
                multiselect: true,
                rowCallback: function(row, data) {
                    if (data[11] == 1) {
                        $('td', row).addClass("text-danger");
                    }

                }

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

        function soldOut(ids) {
            swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, sold out!",
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "/inventory/recycle/sold-out",
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            ids: ids
                        },
                        success: function(response) {
                            let data = JSON.parse(response);
                            if (data.status) {
                                table.ajax.reload(null, false);
                                swal.fire({
                                    icon: "success",
                                    title: "Success",
                                    text: "The selected brands were successfully marked as sold out!",
                                });

                                table.ajax.reload(null, false);
                            } else {
                                swal.fire({
                                    icon: "error",
                                    title: "Error",
                                    text: data.message,
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error(xhr.responseText);
                            swal.fire({
                                icon: "error",
                                title: "Error",
                                text: "An error occurred while processing your request.",
                            });
                        }
                    });
                }
            });
        }
    </script>
@endsection
