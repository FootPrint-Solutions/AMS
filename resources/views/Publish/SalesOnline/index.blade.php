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
                            <th scope="col" class="table-col-no">#</th>
                            <th scope="col">Sales Online Number</th>
                            <th scope="col">Date</th>
                            <th scope="col">Customer Name</th>
                            <th scope="col">Total</th>
                            <th scope="col">Payment Status</th>
                            <th scope="col">Payment Method</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (!empty($data['Sales']))
                            @foreach ($data['Sales'] as $sales)
                                @php
                                    if ($sales->date_paid != null) {
                                        $status = '<span class="badge badge-success">Paid</span>';
                                    } else {
                                        $status = '<span class="badge badge-danger">Unpaid</span>';
                                    }

                                @endphp
                                <tr>
                                    <th scope="row">{{ $loop->iteration }}</th>
                                    <td>{{ $sales->number }}</td>
                                    <td>{{ formatDateWoo($sales->date_created) }}</td>
                                    <td>{{ $sales->billing->first_name }} {{ $sales->billing->last_name }}</td>
                                    <td>{!! formatPrice($sales->total) !!}</td>
                                    <td>{!! $status !!}</td>
                                    <td>{{ $sales->payment_method }}</td>
                                </tr>
                            @endforeach
                        @endif
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
                buttons: [{
                        extend: 'excel',
                        text: 'Export to Excel',
                        className: 'btn btn-outline-success btn-sm'
                    },
                    // button sync Sales Online to wooCommerce
                    {
                        text: 'Sync Sales Online',
                        className: 'btn btn-outline-primary btn-sm',
                        // swal confirmation
                        action: function(e, dt, node, config) {
                            Swal.fire({
                                title: 'Are you sure?',
                                text: "You want to sync Sales Online ?",
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#3085d6',
                                cancelButtonColor: '#d33',
                                confirmButtonText: 'Yes, sync it!'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    // loading 
                                    Swal.fire({
                                        title: 'Please Wait..',
                                        html: 'Syncing Sales Online..',
                                        didOpen: () => {
                                            Swal.showLoading()
                                        },
                                    });
                                    // ajax request
                                    $.ajax({
                                        url: '/sales-online/sync-sales-online',
                                        method: 'POST',
                                        data: {
                                            _token: "{{ csrf_token() }}"
                                        },
                                        success: function(response) {
                                            if (response.status == 'success') {
                                                Swal.fire(
                                                    'Success!',
                                                    response.message,
                                                    'success'
                                                )

                                                // refresh page 
                                                setTimeout(function() {
                                                    location.reload();
                                                }, 2000);
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
                                                'Failed to sync sales online.',
                                                'error'
                                            )
                                        }
                                    });
                                }
                            })
                        }
                    },
                    // button view details 
                    {
                        text: 'View Details',
                        className: 'btn btn-outline-info btn-sm',
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
                                var sales_online_number = data[0][1];
                                $('#modalViewDetails').modal('show');
                                // loading
                                $('#modal-view-details').html(
                                    '<br><br><br><div class="text-center"><div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div></div><br><br><br>'
                                );
                                // ajax request
                                $.ajax({
                                    url: '/sales-online/view-details',
                                    method: 'POST',
                                    data: {
                                        _token: "{{ csrf_token() }}",
                                        sales_online_number: sales_online_number
                                    },
                                    success: function(response) {

                                        // set up data to display
                                        var data = response.data;
                                        var html = '<div class="modal-header">';
                                        html +=
                                            '<h5 class="modal-title" id="modalViewDetailsLabel">Sales Online Details</h5>';
                                        html +=
                                            '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>';
                                        html += '</div>';
                                        html += '<div class="modal-body">';
                                        html += '<div class="row">';
                                        html += '<div class="col-md-6">';
                                        html += '<h5>Order Information</h5>';
                                        html +=
                                            '<table class="table table-borderless">';
                                        html += '<tr>';
                                        html += '<td>Order Number</td>';
                                        html += '<td>' + data.number + '</td>';
                                        html += '</tr>';
                                        html += '<tr>';
                                        html += '<td>Date Created</td>';
                                        html += '<td>' + data.date_created + '</td>';
                                        html += '</tr>';
                                        html += '<tr>';
                                        html += '<td>Date Paid</td>';
                                        html += '<td>' + data.date_paid + '</td>';
                                        html += '</tr>';
                                        html += '<tr>';
                                        html += '<td>Total</td>';
                                        formatPrice = new Intl.NumberFormat('id-ID', {
                                            style: 'currency',
                                            currency: 'IDR'
                                        }).format(data.total);
                                        html += '<td>' + formatPrice + '</td>';
                                        html += '</tr>';
                                        html += '<tr>';
                                        html += '<td>Payment Method</td>';
                                        html += '<td>' + data.payment_method + '</td>';
                                        html += '</tr>';
                                        html += '</table>';
                                        html += '</div>';
                                        html += '<div class="col-md-6">';
                                        html += '<h5>Billing Information</h5>';
                                        html +=
                                            '<table class="table table-borderless">';
                                        html += '<tr>';
                                        html += '<td>Name</td>';
                                        html += '<td colspan="2">' + data.billing
                                            .first_name + ' ' +
                                            data.billing.last_name + '</td>';
                                        html += '</tr>';
                                        html += '<tr>';
                                        html += '<td>Address</td>';
                                        html += '<td colspan="2">' + data.billing
                                            .address_1 +
                                            '</td>';
                                        html += '</tr>';
                                        html += '<tr>';
                                        html += '<td>City</td>';
                                        html += '<td>' + data.billing.city + '</td>';
                                        html += '<td>State</td>';
                                        html += '<td>' + data.billing.state + '</td>';
                                        html += '</tr>';
                                        html += '<tr>';
                                        html += '<td>Postcode</td>';
                                        html += '<td>' + data.billing.postcode +
                                            '</td>';
                                        html += '<td>Country</td>';
                                        html += '<td>' + data.billing.country + '</td>';
                                        html += '</tr>';
                                        html += '<tr>';
                                        html += '<td>Email</td>';
                                        html += '<td>' + data.billing.email + '</td>';
                                        html += '</tr>';
                                        html += '<tr>';
                                        html += '<td>Phone</td>';
                                        html += '<td>' + data.billing.phone + '</td>';
                                        html += '</tr>';
                                        html += '</table>';
                                        html += '</div>';
                                        html += '</div>';
                                        html += '</div>';

                                        // show detail items
                                        html += '<div class="container">';
                                        html += '<h5>Items</h5>';
                                        html +=
                                            '<table class="table table-bordered table-sm">';
                                        html += '<thead>';
                                        html += '<tr>';
                                        html += '<th scope="col">#</th>';
                                        html += '<th scope="col">Product</th>';
                                        html += '<th scope="col">Quantity</th>';
                                        html += '<th scope="col">Price</th>';
                                        html += '<th scope="col">Total</th>';
                                        html += '</tr>';
                                        html += '</thead>';
                                        html += '<tbody>';
                                        var total = 0;
                                        for (var i = 0; i < data.line_items
                                            .length; i++) {
                                            var item = data.line_items[i];
                                            html += '<tr>';
                                            html += '<td>' + (i + 1) + '</td>';
                                            html += '<td>' + item.name + '</td>';
                                            html += '<td>' + item.quantity + '</td>';
                                            formatPrice = new Intl.NumberFormat(
                                                'id-ID', {
                                                    style: 'currency',
                                                    currency: 'IDR'
                                                }).format(item.price);
                                            html += '<td>' + formatPrice + '</td>';
                                            formatPrice = new Intl.NumberFormat(
                                                'id-ID', {
                                                    style: 'currency',
                                                    currency: 'IDR'
                                                }).format(item.total);
                                            html += '<td>' + formatPrice + '</td>';
                                            html += '</tr>';
                                            total += item.total;
                                        }

                                        html += '</tbody>';
                                        html += '</table>';

                                        html += '<div class="modal-footer">';
                                        html +=
                                            '<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>';
                                        html += '</div>';

                                        $('#modal-view-details').html(html);
                                    },
                                    error: function(xhr, status, error) {
                                        Swal.fire(
                                            'Error!',
                                            'Failed to view details.',
                                            'error'
                                        )
                                    }
                                });
                            }
                        }
                    },
                    // button save to sales orders
                    {
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
                                var sales_online_number = data[0][1];
                                // show modal with form to choose vehicle, distributor, and technician
                                $('#modalSaveSalesOrder').modal('show');
                                $('#sales_online_number').val(sales_online_number);
                            }
                        }
                    }
                ],
            });
        });

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

                            // refresh page 
                            setTimeout(function() {
                                location.reload();
                            }, 2000);
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
