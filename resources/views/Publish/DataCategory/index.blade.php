@extends('template.master')

@section('content')
<div class="card">

    <div class="card-body">
        {{-- Title --}}
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Data Category</h3>
                </div>
            </div>
        </div>
        <br>

        {{-- Table --}}
        <div class="table-responsive">
            <table class="table table-striped table-sm" id="table-category">
                <thead>
                    <tr>
                        <th scope="col" class="table-col-no">#</th>
                        <th scope="col">Category ID</th>
                        <th scope="col">Name</th>
                    </tr>
                </thead>
                <tbody>
                    @if (!empty($data['category']))
                    @foreach ($data['category'] as $category)
                    <tr>
                        <th scope="row">{{ $loop->iteration }}</th>
                        <td>{{ $category->id }}</td>
                        <td>{{ $category->name }}</td>
                    </tr>
                    @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>


{{-- send data category properties --}}
<input type="hidden" name="count-category" id="count-category" value="0">
<input type="hidden" name="limit-category" id="limit-category" value="2">
<input type="hidden" name="offset-category" id="offset-category" value="0">
<input type="hidden" name="count-category-now" id="count-category-now" value="0">


<script src="{{ asset('plugins/tinymce/js/tinymce/tinymce.min.js') }}"></script>

<script>
    // set DataTable
    $(document).ready(function() {
        $('#table-category').DataTable({
            "order": [
                [0, "asc"]
            ],
            "columnDefs": [{
                "targets": 0,
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
                // {
                //     text: 'Sync Category',
                //     className: 'btn btn-outline-primary btn-sm',
                //     // swal confirmation
                //     action: function(e, dt, node, config) {
                //         Swal.fire({
                //             title: 'Are you sure?',
                //             text: "You want to sync Category to WooCommerce?",
                //             icon: 'warning',
                //             showCancelButton: true,
                //             confirmButtonColor: '#3085d6',
                //             cancelButtonColor: '#d33',
                //             confirmButtonText: 'Yes, sync it!'
                //         }).then((result) => {
                //             if (result.isConfirmed) {
                //                 // loading 
                //                 Swal.fire({
                //                     title: 'Loading...',
                //                     allowOutsideClick: false,
                //                     showConfirmButton: false,
                //                     willOpen: () => {
                //                         Swal.showLoading()
                //                     },
                //                 });
                //                 // ajax request
                //                 $.ajax({
                //                     url: '/data-category/sync-category',
                //                     method: 'POST',
                //                     data: {
                //                         _token: "{{ csrf_token() }}"
                //                     },
                //                     success: function(response) {
                //                         if (response.status == 'success') {
                //                             Swal.fire(
                //                                 'Success!',
                //                                 response.message,
                //                                 'success'
                //                             )
                //                         } else {
                //                             Swal.fire(
                //                                 'Error!',
                //                                 response.message,
                //                                 'error'
                //                             )
                //                         }
                //                     },
                //                     error: function(xhr, status, error) {
                //                         Swal.fire(
                //                             'Error!',
                //                             'Failed to sync product.',
                //                             'error'
                //                         )
                //                     }
                //                 });
                //             }
                //         })
                //     }
                // },
                // send parent category data
                // {
                //     text: 'Send Parent Category Data ( Vehicle Brand )',
                //     className: 'btn btn-outline-warning btn-sm',
                //     action: function(e, dt, node, config) {
                //         // loading
                //         Swal.fire({
                //             title: 'Please Wait..',
                //             html: 'Sending Parent Category Data..',
                //             didOpen: () => {
                //                 Swal.showLoading();
                //             },
                //         });

                //         var count_category = $('#count-category').val();
                //         var limit_category = $('#limit-category').val();
                //         var offset_category = $('#offset-category').val();
                //         var count_category_now = $('#count-category-now').val();

                //         // Function to handle sending parent category data with recursion
                //         function sendParentCategoryData(offset) {
                //             $.ajax({
                //                 url: '/data-category/send-parent-category-partially',
                //                 method: 'POST',
                //                 data: {
                //                     _token: "{{ csrf_token() }}",
                //                     limit: limit_category,
                //                     offset: offset
                //                 },
                //                 success: function(response) {
                //                     if (response.status == 'success') {
                //                         var message = '';
                //                         response.data.forEach(function(data) {
                //                             message +=
                //                                 '<span class="badge bg-success">' +
                //                                 data.message +
                //                                 '</span><br>';
                //                         });
                //                         count_category_now = parseInt(
                //                             count_category_now) + parseInt(
                //                             limit_category);
                //                         $('#count-category-now').val(
                //                             count_category_now);

                //                         var percent = (count_category_now /
                //                             count_category) * 100;
                //                         Swal.update({
                //                             html: 'Sending Parent Category Data.. <br> <div class="progress"> <div class="progress-bar" role="progressbar" style="width: ' +
                //                                 percent +
                //                                 '%" aria-valuenow="' +
                //                                 percent +
                //                                 '" aria-valuemin="0" aria-valuemax="100"></div> </div> <div class="show-percentage"></div> <div class="show-message-progress"></div>',
                //                             allowOutsideClick: false,
                //                             showConfirmButton: false,
                //                             didOpen: () => {
                //                                 Swal.showLoading();
                //                             },
                //                             showCloseButton: false,
                //                         });

                //                         // update offset
                //                         offset_category = parseInt(
                //                                 offset_category) +
                //                             parseInt(limit_category);
                //                         $('#offset-category').val(offset_category);

                //                         // show percentage
                //                         $('.show-percentage').html(
                //                             percent.toFixed(2) + '%');

                //                         // show message progress
                //                         $('.show-message-progress').html(
                //                             message);


                //                         if (count_category_now < count_category) {
                //                             // Recursively call until all parent categories are sent
                //                             sendParentCategoryData(
                //                                 count_category_now);
                //                         } else {
                //                             Swal.fire(
                //                                 'Success!',
                //                                 'Parent Category data has been sent.',
                //                                 'success'
                //                             );
                //                         }

                //                     } else {
                //                         Swal.fire(
                //                             'Error!',
                //                             'Failed to send parent category data.',
                //                             'error'
                //                         );
                //                     }
                //                 },
                //                 error: function(xhr, status, error) {
                //                     Swal.fire(
                //                         'Error!',
                //                         'Failed to send parent category data.',
                //                         'error'
                //                     );
                //                 }
                //             });
                //         }

                //         // Get the count of parent categories first before starting the send process
                //         $.ajax({
                //             url: '/data-category/count-parent-category',
                //             method: 'POST',
                //             data: {
                //                 _token: "{{ csrf_token() }}"
                //             },
                //             success: function(response) {
                //                 if (response.status == 'success') {
                //                     $('#count-category').val(response.data);
                //                     count_category = response.data;

                //                     // Start sending parent category data
                //                     sendParentCategoryData(offset_category);
                //                 } else {
                //                     Swal.fire(
                //                         'Error!',
                //                         response.message,
                //                         'error'
                //                     );
                //                 }
                //             },
                //             error: function(xhr, status, error) {
                //                 Swal.fire(
                //                     'Error!',
                //                     'Failed to count parent category data.',
                //                     'error'
                //                 );
                //             }
                //         });
                //     }

                // },
                // send category data
                // {
                //     text: 'Send Category Data ( Vehicle )',
                //     className: 'btn btn-outline-warning btn-sm',
                //     action: function(e, dt, node, config) {
                //         // loading
                //         Swal.fire({
                //             title: 'Please Wait..',
                //             html: 'Sending Category Data..',
                //             didOpen: () => {
                //                 Swal.showLoading();
                //             },
                //         });

                //         var count_category = $('#count-category').val();
                //         var limit_category = $('#limit-category').val();
                //         var offset_category = $('#offset-category').val();
                //         var count_category_now = $('#count-category-now').val();

                //         // Function to handle sending category data with recursion
                //         function sendCategoryData(offset) {
                //             $.ajax({
                //                 url: '/data-category/send-category-partially',
                //                 method: 'POST',
                //                 data: {
                //                     _token: "{{ csrf_token() }}",
                //                     limit: limit_category,
                //                     offset: offset
                //                 },
                //                 success: function(response) {
                //                     if (response.status == 'success') {
                //                         var message = '';
                //                         response.data.forEach(function(data) {
                //                             message +=
                //                                 '<span class="badge bg-success">' +
                //                                 data.message +
                //                                 '</span><br>';
                //                         });
                //                         count_category_now = parseInt(
                //                             count_category_now) + parseInt(
                //                             limit_category);
                //                         $('#count-category-now').val(
                //                             count_category_now);

                //                         var percent = (count_category_now /
                //                             count_category) * 100;
                //                         Swal.update({
                //                             html: 'Sending Category Data.. <br> <div class="progress"> <div class="progress-bar" role="progressbar" style="width: ' +
                //                                 percent +
                //                                 '%" aria-valuenow="' +
                //                                 percent +
                //                                 '" aria-valuemin="0" aria-valuemax="100"></div> </div> <div class="show-percentage"></div> <div class="show-message-progress"></div>',
                //                             allowOutsideClick: false,
                //                             showConfirmButton: false,
                //                             didOpen: () => {
                //                                 Swal.showLoading();
                //                             },
                //                             showCloseButton: false,
                //                         });

                //                         // update offset
                //                         offset_category = parseInt(
                //                                 offset_category) +
                //                             parseInt(limit_category);
                //                         $('#offset-category').val(offset_category);

                //                         // show percentage
                //                         $('.show-percentage').html(
                //                             percent.toFixed(2) + '%');

                //                         // show message progress
                //                         $('.show-message-progress').html(
                //                             message);


                //                         if (count_category_now < count_category) {
                //                             // Recursively call until all categories are sent
                //                             sendCategoryData(count_category_now);
                //                         } else {
                //                             Swal.fire(
                //                                 'Success!',
                //                                 'Category data has been sent.',
                //                                 'success'
                //                             );
                //                         }
                //                     } else {
                //                         Swal.fire(
                //                             'Error!',
                //                             'Failed to send category data.',
                //                             'error'
                //                         );
                //                     }
                //                 },
                //                 error: function(xhr, status, error) {
                //                     Swal.fire(
                //                         'Error!',
                //                         'Failed to send category data.',
                //                         'error'
                //                     );
                //                 }
                //             });
                //         }

                //         // Get the count of categories first before starting the send process
                //         $.ajax({
                //             url: '/data-category/count-category',
                //             method: 'POST',
                //             data: {
                //                 _token: "{{ csrf_token() }}"
                //             },
                //             success: function(response) {
                //                 if (response.status == 'success') {


                //                     $('#count-category').val(response.data);
                //                     count_category = response.data;

                //                     // Start sending category data
                //                     sendCategoryData(offset_category);
                //                 } else {
                //                     Swal.fire(
                //                         'Error!',
                //                         response.message,
                //                         'error'
                //                     );
                //                 }
                //             },
                //             error: function(xhr, status, error) {
                //                 Swal.fire(
                //                     'Error!',
                //                     'Failed to count category data.',
                //                     'error'
                //                 );
                //             }
                //         });
                //     }
                // }
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