@extends('template.master')

@section('content')
    {{-- Form --}}
    <div class="card">
        <div class="card-body">
            {{-- Title --}}
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">Work Order</h3>
                    </div>

                    {{-- <div class="col-auto text-end float-end ms-auto download-grp">
                        <button id="btn-add" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add
                            New Sales Order</button>
                    </div> --}}
                </div>
            </div>
            <br>

            {{-- Table --}}
            <table class="table table-striped" id="table-work-order">
                <thead>
                    <tr>
                        <th scope="col" class="table-col-no">#</th>
                        <th scope="col">Work Order Number</th>
                        <th scope="col">Sales Order Number</th>
                        <th scope="col">Date</th>
                        <th scope="col">Customer</th>
                        <th scope="col">Qty</th>
                        <th scope="col">Total (IDR)</th>
                        <th scope="col">Address</th>
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
            table = $("#table-work-order").DataTable({
                lengthMenu: [
                    [5, 10, 25],
                    [5, 10, 25]
                ],
                responsive: true,
                processing: true,
                serverSide: true,
                order: [],
                ajax: {
                    url: "/work-order/show",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}"
                    }
                },
                columnDefs: [{
                    targets: [0],
                    orderable: false,
                }, {
                    targets: [6],
                    className: 'dt-body-right table-col-price'
                }, {
                    targets: [0, 7],
                    className: 'dt-body-center'
                }],
                dom: "lBfrtip",
                buttons: getDatatablesButtonConfigurations([{
                    text: "<i class='fas fa-print'></i> Print Work Order",
                    action: function(e, dt, node, config) {
                        // Get the selected row's id.
                        let selectedRows = table.rows({
                            selected: true
                        }).data().toArray();
                        if (selectedRows.length !== 1) {
                            Swal.fire({
                                title: "Error",
                                text: "Please select a single row for printing work order.",
                                icon: "error",
                            });
                            return;
                        }

                        // Download invoice as pdf.
                        downloadPDF("/work-order/print/" + selectedRows[0][8]);
                    },
                    className: "btn btn-outline-danger btn-sm",
                }]),
                language: getDatatablesLanguangeConfigurations("Sales Order"),
                select: true,
            });

            // Load DataTables toolbar component.
            appendDatatablesToolbar(9);
        });
    </script>

    {{-- Click Event Handler --}}
    <script>
        $('#btn-add').on('click', function() {
            goToPage("/sales-order/create");
        });
    </script>
@endsection
