@extends('template.master')

@section('content')
    {{-- Form --}}
    <div class="card">
        <div class="card-body">
            {{-- Title --}}
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">Sales Order</h3>
                    </div>

                    <div class="col-auto text-end float-end ms-auto download-grp">
                        <button id="btn-add" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add
                            New Sales Order</button>
                    </div>
                </div>
            </div>
            <br>

            {{-- Table --}}
            <table class="table table-striped" id="table-sales-order">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Sales Order Number</th>
                        <th scope="col">Customer</th>
                        <th scope="col">Distributor/Shop</th>
                        <th scope="col">Technician</th>
                        <th scope="col">Total (IDR)</th>
                        <th scope="col">Payment Method</th>
                        <th scope="col">Status</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    {{-- DataTables Configurations --}}
    <script>
        var table;

        $(document).ready(function() {
            // DataTables configuration
            table = $("#table-sales-order").DataTable({
                lengthMenu: [
                    [5, 10, 25],
                    [5, 10, 25]
                ],
                responsive: true,
                processing: true,
                serverSide: true,
                order: [],
                ajax: {
                    url: "/sales-order/show",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}"
                    }
                },
                columnDefs: [{
                    targets: [0],
                    orderable: false
                }, {
                    targets: [5],
                    className: 'dt-body-right'
                }, {
                    targets: [7],
                    className: 'dt-body-center'
                }],
                dom: "lBfrtip",
                buttons: getDatatablesButtonConfigurations([{
                    text: "<i class='fas fa-file-text'></i> View Invoice",
                    action: function(e, dt, node, config) {
                        // Get the selected row's id.
                        let selectedRows = table.rows({
                            selected: true
                        }).data().toArray();
                        if (selectedRows.length !== 1) {
                            Swal.fire({
                                title: "Error",
                                text: "Please select a single row for viewing invoice.",
                                icon: "error",
                            });
                            return;
                        }

                        // Go to page invoice.
                        goToPage("/sales-order/invoice/" + selectedRows[0][8], true)
                    },
                    className: "btn btn-outline-secondary btn-sm",
                }, ]),
                language: getDatatablesLanguangeConfigurations("Sales Order"),
                select: true,
            });

            // Load DataTables toolbar component.
            appendDatatablesToolbar(8, "/sales-order/edit/", "/sales-order/destroy");
        });
    </script>

    {{-- Click Event Handler --}}
    <script>
        $('#btn-add').on('click', function() {
            goToPage("/sales-order/create");
        });
    </script>
@endsection
