@extends('template.master')

@section('content')
    <div class="card">

        <div class="card-body">
            {{-- Title --}}
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">Data Sales Online</h3>
                    </div>
                </div>
            </div>
            <br>

            {{-- Table --}}
            <div class="table-responsive">
                <table class="table table-striped table-sm" id="table-sales-online">
                    <thead>
                        <tr>
                            <th scope="col" class="table-col-no"></th>
                            <th scope="col" class="table-col-no">#</th>
                            <th scope="col">Sales Online ID</th>
                            <th scope="col">Delivery Date</th>
                            <th scope="col">Customer Name</th>
                            <th scope="col">Customer Phone Number</th>
                            <th scope="col">Customer Address</th>
                            <th scope="col">Qty</th>
                            <th scope="col">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Modal View Details --}}
    <div class="modal fade" id="modalViewDetails" tabindex="-1" role="dialog" aria-labelledby="modalViewDetailsLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content" id="modal-view-details">
            </div>
        </div>
    </div>

    {{--  modal form to choose vehicle, distributor, and technician --}}
    <div class="modal fade" id="modalSaveSalesOrder" tabindex="-1" role="dialog"
        aria-labelledby="modalSaveSalesOrderLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content" id="modal-save-sales-order">
                {{-- form --}}
                <form action="{{ route('sales-online.saveToSalesOrders') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalSaveSalesOrderLabel">Save Sales Online to Sales Orders</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h5>Choose Vehicle</h5>
                                <select class="form-select" name="vehicle_id" required>
                                    <option value="">Choose Vehicle</option>
                                    @foreach ($data['Vehicles'] as $vehicle)
                                        <option value="{{ $vehicle->id }}">{{ $vehicle->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <h5>Choose Distributor</h5>
                                <select class="form-select" name="distributor_id" id="distributor_id" required>
                                    <option value="">Choose Distributor</option>
                                    @foreach ($data['Distributors'] as $distributor)
                                        <option value="{{ $distributor->id }}">{{ $distributor->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <h5>Choose Technician</h5>
                                <select class="form-select" name="technician_id">
                                    <option value="">Choose Technician</option>
                                </select>
                            </div>
                        </div>
                        <input type="hidden" name="sales_online_number" id="sales_online_number">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="save-to-sales-orders">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <script>
        // set DataTable
        $(document).ready(function() {
            $('#table-sales-online').DataTable({
                "order": [
                    [0, "asc"]
                ],
                "columnDefs": [{
                    "targets": [5],
                    "orderable": false
                }],
                "lengthMenu": [
                    [5, 25, 50, -1],
                    [5, 25, 50, "All"]
                ],
                "pageLength": 5,
                // custom button 
                dom: 'Bfrtip',
                select: true,
                rowCallback: function(row, data) {
                    if (data[9] && data[9] !== "completed") {
                        $('td', row).addClass("text-success");
                    } else if (data[9] === "completed") {
                        $('td', row).addClass("text-info");
                    }
                },
                responsive: true,
                processing: true,
                serverSide: true,
                order: [],
                ajax: {
                    url: "/sales-online/show",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}"
                    }
                },
                buttons: [{
                    text: 'Save to Sales Orders',
                    className: 'btn btn-outline-warning btn-sm',
                    action: function(e, dt, node, config) {
                        var data = dt.rows({
                            selected: true
                        }).data();
                        if (data.length == 0) {
                            Swal.fire(
                                'Error!',
                                'Please select at least one row.',
                                'error'
                            )
                        } else {
                            var sales_online_number = data[0][2];
                            // show modal with form to choose vehicle, distributor, and technician
                            $('#modalSaveSalesOrder').modal('show');
                            $('#sales_online_number').val(sales_online_number);
                        }
                    }
                }],
            });

            $('#table-sales-online tbody').on('click', 'tr', function() {
                var tr = $(this);
                var row = table.row(tr);

                if (row.child.isShown()) {
                    row.child.hide();
                    tr.removeClass('shown');
                } else {
                    // Open
                    var rowData = row.data();
                    var salesOnlineId = rowData[0];

                    $.ajax({
                        url: "/sales-online/batteries/" + salesOnlineId,
                        type: "GET",
                        success: function(res) {
                            row.child(formatSubgrid(res)).show();
                            tr.addClass('shown');
                        }
                    });
                }
            });
        });


        function formatSubgrid(data) {
            let html = '<table class="table table-sm table-bordered mb-0">';
            html += '<thead><tr><th>Name</th><th>Quantity</th><th>Price</th><th>Total</th></tr></thead><tbody>';

            data.forEach(function(battery) {
                html += '<tr>' +
                    '<td>' + battery.name + '</td>' +
                    '<td>' + battery.quantity + '</td>' +
                    '<td>' + battery.price + '</td>' +
                    '<td>' + battery.total_price + '</td>' +
                    '</tr>';
            });

            html += '</tbody></table>';
            return html;
        }

        // get technician based on distributor
        $('#distributor_id').change(function() {
            var distributor_id = $(this).val();
            $.ajax({
                url: '/sales-online/get-technician',
                method: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    distributor_id: distributor_id
                },
                success: function(response) {
                    var html = '<option value="">Choose Technician</option>';
                    for (var i = 0; i < response.data.length; i++) {
                        var technician = response.data[i];
                        html += '<option value="' + technician.id + '">' + technician
                            .name + '</option>';
                    }
                    $('select[name="technician_id"]').html(html);
                },
                error: function(xhr, status, error) {
                    Swal.fire(
                        'Error!',
                        'Failed to get technician.',
                        'error'
                    )
                }
            });
        });

        // save to sales orders
        $('#save-to-sales-orders').click(function() {
            var vehicle_id = $('select[name="vehicle_id"]').val();
            var distributor_id = $('select[name="distributor_id"]').val();
            var technician_id = $('select[name="technician_id"]').val();
            var sales_online_number = $('#sales_online_number').val();
            if (vehicle_id == '' || distributor_id == '') {
                Swal.fire(
                    'Error!',
                    'Please choose vehicle, distributor.',
                    'error'
                )
            } else {
                // loading 
                Swal.fire({
                    title: 'Please Wait..',
                    html: 'Saving to Sales Orders..',
                    didOpen: () => {
                        Swal.showLoading()
                    },
                    allowOutsideClick: false
                });
                // ajax request
                $.ajax({
                    url: '/sales-online/save-to-sales-orders',
                    method: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        vehicle_id: vehicle_id,
                        distributor_id: distributor_id,
                        technician_id: technician_id,
                        sales_online_number: sales_online_number
                    },
                    success: function(response) {
                        if (response.status == 'success') {
                            Swal.fire(
                                'Success!',
                                response.message,
                                'success'
                            )

                            $('#table-sales-online').DataTable().ajax.reload();
                            $('#modalSaveSalesOrder').modal('hide');
                        } else {
                            Swal.fire(
                                'Error!',
                                response.message,
                                'error'
                            )
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire(
                            'Error!',
                            'Failed to save to sales orders.',
                            'error'
                        )
                    }
                });
            }
        });
    </script>
@endsection
