@extends('template.master')

@section('content')
    <div class="card">

        <div class="card-body">
            {{-- Title --}}
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">Data Battery</h3>
                    </div>
                </div>
            </div>
            <br>

            {{-- Table --}}
            <div class="table-responsive">
                <table class="table table-striped table-sm" id="table-battery">
                    <thead>
                        <tr>
                            <th scope="col" class="table-col-no">#</th>
                            <th scope="col">Name</th>
                            <th scope="col">Slug</th>
                            <th scope="col">Permalink</th>
                            <th scope="col">Status</th>
                            <th scope="col">Image</th>
                            <th scope="col">Dimensions (mm)</th>
                            <th scope="col">Stock Qty</th>
                            <th scope="col">Regular Price (IDR)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (!empty($data['products']))
                            @foreach ($data['products'] as $battery)
                                <tr>
                                    <th scope="row">{{ $loop->iteration }}</th>
                                    <td>{{ $battery->name }}</td>
                                    <td>{{ $battery->slug }}</td>
                                    <td>{{ $battery->permalink }}</td>
                                    <td>{{ $battery->status }}</td>
                                    <td>


                                        @if (!empty($battery->images) && isset($battery->images[0]))
                                            {{-- button click to view image --}}
                                            <button type="button" class="btn btn-primary btn-view-image"
                                                data-url="{{ $battery->images[0]->src }}">
                                                View Image
                                            </button>
                                        @else
                                            No Image
                                        @endif
                                    </td>
                                    <td>{{ $battery->dimensions->length }} x {{ $battery->dimensions->width }} x
                                        {{ $battery->dimensions->height }}</td>
                                    <td>{{ $battery->stock_quantity }}</td>
                                    <td>{{ $battery->regular_price }}</td>
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
            $('#table-battery').DataTable({
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
                    // button sync category to wooCommerce
                    {
                        text: 'Sync Category',
                        className: 'btn btn-outline-primary btn-sm',
                        // swal confirmation
                        action: function(e, dt, node, config) {
                            Swal.fire({
                                title: 'Are you sure?',
                                text: "You want to sync category to WooCommerce?",
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#3085d6',
                                cancelButtonColor: '#d33',
                                confirmButtonText: 'Yes, sync it!'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    // ajax request
                                    $.ajax({
                                        url: '/data-battery/sync-category',
                                        method: 'POST',
                                        data: {
                                            _token: "{{ csrf_token() }}"
                                        },
                                        success: function(response) {
                                            Swal.fire(
                                                'Success!',
                                                'Category has been synced.',
                                                'success'
                                            )
                                        },
                                        error: function(xhr, status, error) {
                                            Swal.fire(
                                                'Error!',
                                                'Failed to sync category.',
                                                'error'
                                            )
                                        }
                                    });
                                }
                            })
                        }
                    },
                    // button sync product to wooCommerce
                    {
                        text: 'Sync Product',
                        className: 'btn btn-outline-primary btn-sm',
                        // swal confirmation
                        action: function(e, dt, node, config) {
                            Swal.fire({
                                title: 'Are you sure?',
                                text: "You want to sync product to WooCommerce?",
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#3085d6',
                                cancelButtonColor: '#d33',
                                confirmButtonText: 'Yes, sync it!'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    // ajax request
                                    $.ajax({
                                        url: '/data-battery/sync-product',
                                        method: 'POST',
                                        data: {
                                            _token: "{{ csrf_token() }}"
                                        },
                                        success: function(response) {
                                            Swal.fire(
                                                'Success!',
                                                'Product has been synced.',
                                                'success'
                                            )
                                        },
                                        error: function(xhr, status, error) {
                                            Swal.fire(
                                                'Error!',
                                                'Failed to sync product.',
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

        // btn-view-image
        $(document).on('click', '.btn-view-image', function() {
            var url = $(this).data('url');
            Swal.fire({
                imageUrl: url,
                imageWidth: 600,
                imageHeight: 400,
                imageAlt: 'Image',
            })
        });
    </script>
@endsection
