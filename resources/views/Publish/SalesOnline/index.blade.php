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
                                        html += '<td>' + data.billing.first_name + ' ' +
                                            data.billing.last_name + '</td>';
                                        html += '</tr>';
                                        html += '<tr>';
                                        html += '<td>Address</td>';
                                        html += '<td>' + data.billing.address_1 +
                                            '</td>';
                                        html += '</tr>';
                                        html += '<tr>';
                                        html += '<td>City</td>';
                                        html += '<td>' + data.billing.city + '</td>';
                                        html += '</tr>';
                                        html += '<tr>';
                                        html += '<td>State</td>';
                                        html += '<td>' + data.billing.state + '</td>';
                                        html += '</tr>';
                                        html += '<tr>';
                                        html += '<td>Postcode</td>';
                                        html += '<td>' + data.billing.postcode +
                                            '</td>';
                                        html += '</tr>';
                                        html += '<tr>';
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
                    }
                ],
            });
        });
    </script>
@endsection
