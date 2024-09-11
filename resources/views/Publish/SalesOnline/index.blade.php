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
                            <th scope="col">Address</th>
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
                                    <td>{{ $sales->date_created }}</td>
                                    <td>{{ $sales->billing->first_name }} {{ $sales->billing->last_name }}</td>
                                    <td>{{ $sales->shipping->address_1 }} {{ $sales->shipping->address_2 }}
                                        {{ $sales->shipping->city }} {{ $sales->shipping->postcode }}</td>
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
                                text: "You want to sync Sales Online to AMS Database ?",
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#3085d6',
                                cancelButtonColor: '#d33',
                                confirmButtonText: 'Yes, sync it!'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    // ajax request
                                    $.ajax({
                                        url: '/sales-online/sync-sales-online',
                                        method: 'POST',
                                        data: {
                                            _token: "{{ csrf_token() }}"
                                        },
                                        success: function(response) {
                                            Swal.fire(
                                                'Success!',
                                                'Sales Online has been synced.',
                                                'success'
                                            )
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
                    }
                ],
            });
        });
    </script>
@endsection
