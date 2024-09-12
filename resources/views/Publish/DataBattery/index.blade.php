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
                            <th scope="col">Product ID</th>
                            <th scope="col">Name</th>
                            <th Scope="col">Vehicle</th>
                            <th scope="col">Status</th>
                            <th scope="col">Stock Qty</th>
                            <th scope="col">Regular Price (IDR)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (!empty($data['products']))
                            @foreach ($data['products'] as $battery)
                                <tr>
                                    <th scope="row">{{ $loop->iteration }}</th>
                                    <td>{{ $battery->id }}</td>
                                    <td>{{ $battery->name }}</td>
                                    <td>{{ $battery->status }}</td>
                                    <td>{{ $battery->status }}</td>
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


    {{-- Modal View Details --}}
    <div class="modal fade" id="modalViewDetails" tabindex="-1" role="dialog" aria-labelledby="modalViewDetailsLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content" id="modal-view-details">
            </div>
        </div>
    </div>

    {{-- send data product properties --}}
    <input type="hidden" name="count-category" id="count-category" value="0">
    <input type="hidden" name="limit-category" id="limit-category" value="2">
    <input type="hidden" name="offset-category" id="offset-category" value="0">
    <input type="hidden" name="count-category-now" id="count-category-now" value="0">

    {{-- send data product properties --}}
    <input type="hidden" name="count-product" id="count-product" value="0">
    <input type="hidden" name="limit-product" id="limit-product" value="2">
    <input type="hidden" name="offset-product" id="offset-product" value="0">
    <input type="hidden" name="count-product-now" id="count-product-now" value="0">

    <script src="{{ asset('plugins/tinymce/js/tinymce/tinymce.min.js') }}"></script>

    <script>
        // set DataTable
        $(document).ready(function() {
            $('#table-battery').DataTable({
                "order": [
                    [0, "asc"]
                ],
                "columnDefs": [{
                    "targets": [4],
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
                                    // loading 
                                    Swal.fire({
                                        title: 'Loading...',
                                        allowOutsideClick: false,
                                        showConfirmButton: false,
                                        willOpen: () => {
                                            Swal.showLoading()
                                        },
                                    });
                                    // ajax request
                                    $.ajax({
                                        url: '/data-battery/sync-product',
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
                                                'Failed to sync product.',
                                                'error'
                                            )
                                        }
                                    });
                                }
                            })
                        }
                    },
                    // button send product to woocommerce
                    {
                        text: 'Send Product',
                        className: 'btn btn-outline-primary btn-sm',
                        action: function(e, dt, node, config) {
                            // loading
                            Swal.fire({
                                title: 'Please Wait..',
                                html: 'Sending Product Data..',
                                didOpen: () => {
                                    Swal.showLoading();
                                },
                            });

                            var count_product = $('#count-product').val();
                            var limit_product = $('#limit-product').val();
                            var offset_product = $('#offset-product').val();
                            var count_product_now = $('#count-product-now').val();

                            // Function to handle sending product data with recursion
                            function sendproductData(offset) {
                                $.ajax({
                                    url: '/data-battery/send-product-partially',
                                    method: 'POST',
                                    data: {
                                        _token: "{{ csrf_token() }}",
                                        limit: limit_product,
                                        offset: offset
                                    },
                                    success: function(response) {
                                        if (response.status == 'success') {
                                            var message = '';
                                            response.data.forEach(function(data) {
                                                message +=
                                                    '<span class="badge bg-success">' +
                                                    data.message +
                                                    '</span><br>';
                                            });
                                            count_product_now = parseInt(
                                                count_product_now) + parseInt(
                                                limit_product);
                                            $('#count-product-now').val(
                                                count_product_now);

                                            var percent = (count_product_now /
                                                count_product) * 100;
                                            Swal.update({
                                                html: 'Sending Product Data.. <br> <div class="progress"> <div class="progress-bar" role="progressbar" style="width: ' +
                                                    percent +
                                                    '%" aria-valuenow="' +
                                                    percent +
                                                    '" aria-valuemin="0" aria-valuemax="100"></div> </div> <div class="show-percentage"></div> <div class="show-message-progress"></div>',
                                                allowOutsideClick: false,
                                                showConfirmButton: false,
                                                didOpen: () => {
                                                    Swal.showLoading();
                                                },
                                                showCloseButton: false,
                                            });

                                            // update offset
                                            offset_product = parseInt(
                                                    offset_product) +
                                                parseInt(limit_product);
                                            $('#offset-product').val(offset_product);

                                            // show percentage
                                            $('.show-percentage').html(
                                                percent.toFixed(2) + '%');

                                            // show message progress
                                            $('.show-message-progress').html(
                                                message);


                                            if (count_product_now < count_product) {
                                                // Recursively call until all product are sent
                                                sendproductData(count_product_now);
                                            } else {
                                                Swal.fire(
                                                    'Success!',
                                                    'Product data has been sent.',
                                                    'success'
                                                );
                                            }
                                        } else {
                                            Swal.fire(
                                                'Error!',
                                                'Failed to send Product data.',
                                                'error'
                                            );
                                        }
                                    },
                                    error: function(xhr, status, error) {
                                        Swal.fire(
                                            'Error!',
                                            'Failed to send Product data.',
                                            'error'
                                        );
                                    }
                                });
                            }

                            // Get the count of Product first before starting the send process
                            $.ajax({
                                url: '/data-battery/count-product',
                                method: 'POST',
                                data: {
                                    _token: "{{ csrf_token() }}"
                                },
                                success: function(response) {
                                    if (response.status == 'success') {


                                        $('#count-product').val(response.data);
                                        count_product = response.data;

                                        // Start sending category data
                                        sendproductData(offset_product);
                                    } else {
                                        Swal.fire(
                                            'Error!',
                                            response.message,
                                            'error'
                                        );
                                    }
                                },
                                error: function(xhr, status, error) {
                                    Swal.fire(
                                        'Error!',
                                        'Failed to count product data.',
                                        'error'
                                    );
                                }
                            });
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
                                    'Please select a product.',
                                    'error'
                                )
                            } else {
                                var id = data[0][1];
                                $('#modalViewDetails').modal('show');
                                // loading
                                $('#modal-view-details').html(
                                    '<br><br><br><div class="text-center"><div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div></div><br><br><br>'
                                );
                                // ajax request
                                $.ajax({
                                    url: '/data-battery/view-details',
                                    method: 'POST',
                                    data: {
                                        _token: "{{ csrf_token() }}",
                                        id: id
                                    },
                                    success: function(response) {

                                        // set up data to view
                                        var data = response.data;
                                        var html =
                                            '<div class="modal-header"><h5 class="modal-title" id="modalViewDetailsLabel">Product Details</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div><div class="modal-body"><div class="row"><div class="col-md-6"><div class="form-group"><label for="product-id">Product ID</label><input type="text" class="form-control" id="product-id" value="' +
                                            data.id +
                                            '" readonly></div></div><div class="col-md-6"><div class="form-group"><label for="product-name">Name</label><input type="text" class="form-control" id="product-name" value="' +
                                            data.name +
                                            '" readonly></div></div></div><div class="row"><div class="col-md-6"><div class="form-group"><label for="product-status">Status</label><input type="text" class="form-control" id="product-status" value="' +
                                            data.status +
                                            '" readonly></div></div><div class="col-md-6"><div class="form-group"><label for="product-stock-qty">Stock Qty</label><input type="text" class="form-control" id="product-stock-qty" value="' +
                                            data.stock_quantity +
                                            '" readonly></div></div></div><div class="row"><div class="col-md-6"><div class="form-group"><label for="product-regular-price">Regular Price (IDR)</label><input type="text" class="form-control" id="product-regular-price" value="' +
                                            data.regular_price +
                                            '" readonly></div></div></div><div class="row"><div class="col-md-12"><div class="form-group"><label for="product-description">Description</label><textarea class="form-control" id="product-description" rows="5" readonly>' +
                                            data.description +
                                            '</textarea></div></div></div><div class="row"><div class="col-md-12"><div class="form-group"><label for="product-short-description">Short Description</label><textarea class="form-control" id="product-short-description" rows="3" readonly>' +
                                            data.short_description +
                                            '</textarea></div></div></div><div class="row"><div class="col text-center"><h5>Images</h5></div></div><div class="row">';
                                        // loop images if exist
                                        if (data.images.length == 0) {
                                            html +=
                                                '<div class="col text-center"><h6>No images available.</h6></div>';
                                        } else {
                                            data.images.forEach(function(image) {
                                                html +=
                                                    '<div class="col-md-4"><img src="' +
                                                    image.src +
                                                    '" class="img-fluid img-thumbnail btn-view-image" data-url="' +
                                                    image.src + '"></div>';
                                            });
                                        }
                                        html +=
                                            '</div></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button></div>';
                                        // set html
                                        $('#modal-view-details').html(html);

                                        // set up tinymce
                                        var tinymceConfigProductDescription = {
                                            selector: 'textarea#product-description',
                                            height: 300,
                                            menubar: false,
                                            plugins: 'lists, link, image, media',
                                            toolbar: 'undo redo | formatselect | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist | link image media',
                                            content_css: [
                                                '//fonts.googleapis.com/css?family=Lato:300,300i,400,400i',
                                                '//www.tiny.cloud/css/codepen.min.css'
                                            ]
                                        };

                                        var tinymceConfigProductShortDescription = {
                                            selector: 'textarea#product-short-description',
                                            height: 200,
                                            menubar: false,
                                            plugins: 'lists, link, image, media',
                                            toolbar: 'undo redo | formatselect | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist | link image media',
                                            content_css: [
                                                '//fonts.googleapis.com/css?family=Lato:300,300i,400,400i',
                                                '//www.tiny.cloud/css/codepen.min.css'
                                            ]
                                        };

                                        tinymce.init(tinymceConfigProductDescription);
                                        tinymce.init(
                                            tinymceConfigProductShortDescription);

                                    },
                                    error: function(xhr, status, error) {
                                        $('#modal-view-details').html(
                                            '<br><br><br><div class="text-center"><h5>Failed to load data.</h5></div><br><br><br>'
                                        );
                                    }
                                });
                            }
                        }
                    }, // send category data
                    {
                        text: 'Send Category Data',
                        className: 'btn btn-outline-warning btn-sm',
                        action: function(e, dt, node, config) {
                            // loading
                            Swal.fire({
                                title: 'Please Wait..',
                                html: 'Sending Category Data..',
                                didOpen: () => {
                                    Swal.showLoading();
                                },
                            });

                            var count_category = $('#count-category').val();
                            var limit_category = $('#limit-category').val();
                            var offset_category = $('#offset-category').val();
                            var count_category_now = $('#count-category-now').val();

                            // Function to handle sending category data with recursion
                            function sendCategoryData(offset) {
                                $.ajax({
                                    url: '/data-battery/send-category-partially',
                                    method: 'POST',
                                    data: {
                                        _token: "{{ csrf_token() }}",
                                        limit: limit_category,
                                        offset: offset
                                    },
                                    success: function(response) {
                                        if (response.status == 'success') {
                                            var message = '';
                                            response.data.forEach(function(data) {
                                                message +=
                                                    '<span class="badge bg-success">' +
                                                    data.message +
                                                    '</span><br>';
                                            });
                                            count_category_now = parseInt(
                                                count_category_now) + parseInt(
                                                limit_category);
                                            $('#count-category-now').val(
                                                count_category_now);

                                            var percent = (count_category_now /
                                                count_category) * 100;
                                            Swal.update({
                                                html: 'Sending Category Data.. <br> <div class="progress"> <div class="progress-bar" role="progressbar" style="width: ' +
                                                    percent +
                                                    '%" aria-valuenow="' +
                                                    percent +
                                                    '" aria-valuemin="0" aria-valuemax="100"></div> </div> <div class="show-percentage"></div> <div class="show-message-progress"></div>',
                                                allowOutsideClick: false,
                                                showConfirmButton: false,
                                                didOpen: () => {
                                                    Swal.showLoading();
                                                },
                                                showCloseButton: false,
                                            });

                                            // update offset
                                            offset_category = parseInt(
                                                    offset_category) +
                                                parseInt(limit_category);
                                            $('#offset-category').val(offset_category);

                                            // show percentage
                                            $('.show-percentage').html(
                                                percent.toFixed(2) + '%');

                                            // show message progress
                                            $('.show-message-progress').html(
                                                message);


                                            if (count_category_now < count_category) {
                                                // Recursively call until all categories are sent
                                                sendCategoryData(count_category_now);
                                            } else {
                                                Swal.fire(
                                                    'Success!',
                                                    'Category data has been sent.',
                                                    'success'
                                                );
                                            }
                                        } else {
                                            Swal.fire(
                                                'Error!',
                                                'Failed to send category data.',
                                                'error'
                                            );
                                        }
                                    },
                                    error: function(xhr, status, error) {
                                        Swal.fire(
                                            'Error!',
                                            'Failed to send category data.',
                                            'error'
                                        );
                                    }
                                });
                            }

                            // Get the count of categories first before starting the send process
                            $.ajax({
                                url: '/data-battery/count-category',
                                method: 'POST',
                                data: {
                                    _token: "{{ csrf_token() }}"
                                },
                                success: function(response) {
                                    if (response.status == 'success') {


                                        $('#count-category').val(response.data);
                                        count_category = response.data;

                                        // Start sending category data
                                        sendCategoryData(offset_category);
                                    } else {
                                        Swal.fire(
                                            'Error!',
                                            response.message,
                                            'error'
                                        );
                                    }
                                },
                                error: function(xhr, status, error) {
                                    Swal.fire(
                                        'Error!',
                                        'Failed to count category data.',
                                        'error'
                                    );
                                }
                            });
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
