{{-- Work Order Custom Css --}}
<link rel="stylesheet" href="{{ asset('css/work-order.css') }}">

<div class="d-block d-md-none">
    <div class="container">
        <h3>
            Work Order
        </h3>

        {{-- form search with side icon --}}
        <div class="row">
            <div class="col">
                <div class="top-nav-search-custom-mobile">
                    <form>
                        <input type="text" class="form-control" placeholder="Search here" id="search-work-order">
                        <button class="btn" type="submit"><i class="fas fa-search"></i></button>
                    </form>
                </div>
            </div>
            <div class="col text-justify">
                {{-- button rounded with dot icon --}}
                <button class="btn btn-secondary float-right btn-sm btn-rounded text-center mt-2" id="btn-pop-up">
                    <i class="material-icons">more_horiz</i>
                </button>
            </div>
        </div>


        {{-- Work Order List --}}
        <div class="card bg-grey mt-4 p-3">
            {{-- Lazy load configuration --}}
            <input type="hidden" id="lazy-load-limit">
            <input type="hidden" id="lazy-load-offset">
            <div class="scrollable-y" id="scrollable-container">
                <div id="lazy-load-list-data"></div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Work Order Mobile Menu --}}
<div class="modal fade" id="modal-work-order-mobile-menu" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body text-center">
                <input type="hidden" name="wo-id-mobile" id="wo-id-mobile">
                <div class="container mt-5">
                    <div class="row">
                        <div class="col-6">
                            <button class="custom-btn delete-btn">
                                <i class="fas fa-times-circle"></i>
                                Delete Work Order
                            </button>
                        </div>
                        <div class="col-6">
                            <button class="custom-btn print-work-btn">
                                <i class="fas fa-print"></i>
                                Print Work <br> Order
                            </button>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <button class="custom-btn print-tech-btn">
                                <i class="fas fa-file-alt"></i>
                                Print Technician Report
                            </button>
                        </div>
                        <div class="col-6">
                            <button class="custom-btn" id="go-btn">
                                <i class="fas fa-motorcycle"></i>
                                <span id="go-btn-text">Go Technician Go</span>
                            </button>
                        </div>
                    </div>

                    <div class="row">
                        <button class="custom-btn complete-btn">
                            <i class="fas fa-check-circle"></i>
                            Complete Work Order
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Work Order Detail --}}
<div class="modal fade" id="modal-work-order-detail" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailWOModalLabel">Detail WO</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <table>
                    <tr>
                        <td width="40%">WO Number</td>
                        <td width="20%">:</td>
                        <td width="100%" id="no-wo-detail">WO240700008</td>
                    </tr>
                    <tr>
                        <td>SO Number</td>
                        <td>:</td>
                        <td id="no-so-detail"></td>
                    </tr>
                    <tr>
                        <td>Date</td>
                        <td>:</td>
                        <td id="tanggal-wo-detail"></td>
                    </tr>
                    <tr>
                        <td>Customer</td>
                        <td>:</td>
                        <td id="nama-customer-detail"></td>
                    </tr>
                    <tr>
                        <td>Address</td>
                        <td>:</td>
                        <td id="alamat-customer-detail"></td>
                    </tr>
                    <tr>
                        <td>Discount</td>
                        <td>:</td>
                        <td id="discount-wo-detail"></td>
                    </tr>
                    <tr>
                        <td>Extra Discount</td>
                        <td>:</td>
                        <td id="extra-discount-wo-detail"></td>
                    </tr>
                    <tr>
                        <td>Total (IDR)</td>
                        <td>:</td>
                        <td id="total-wo-detail"></td>
                    </tr>
                    <tr>
                        <td>Status</td>
                        <td>:</td>
                        <td id="status-wo-detail"></td>
                    </tr>
                </table>
                <br>
                <div class="detail-section">
                    <div class="section-title">Batteries</div>
                    <div class="badge-custom">
                        <table id="list-battery-detail">
                        </table>
                    </div>
                </div>
                <table>
                    <tr>
                        <td width="50%">Payment Status</td>
                        <td width="50%">:</td>
                        <td width="50%" id="payment-status-detail"></td>
                    </tr>
                    <tr>
                        <td>Payment Method</td>
                        <td>:</td>
                        <td id="payment-method-detail"></td>
                    </tr>
                    <tr>
                        <td>Payment Link</td>
                        <td>:</td>
                        <td><a href="#" id="payment-link-detail"></a></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<input type="hidden" name="work_order_id_mobile" id="work_order_id_mobile">

<script>
    var isLoading = false; // Flag to check if lazy loading is in progress


    $(document).ready(function() {
        // Lazy load configuration
        $('#lazy-load-limit').val(10);
        $('#lazy-load-offset').val(0);

        // Load data
        loadWorkOrderList();

        // Load more data scrollabe y
        $('#scrollable-container').scroll(function() {
            if ($('#scrollable-container').scrollTop() + $('#scrollable-container').height() >= $(
                    '#lazy-load-list-data').height()) {
                if (!isLoading) { // Check if a load is not in progress
                    loadWorkOrderList();
                }
            }
        });

        // when btn-work-order-card clicked add class active
        $(document).on('click', '#btn-work-order-card', function() {
            $('.bg-dark-blue').removeClass('btn-wo-active');
            $(this).closest('#btn-work-order-card').addClass('btn-wo-active');
        });

        $('#lazy-load-list-data').on('click', '#work-order-list', function() {
            var workOrderId = $(this).data('id');

            // change button color 
            $('.bg-dark-blue').removeClass('btn-wo-active');
            $(this).find('.bg-dark-blue').addClass('btn-wo-active');
        });


        // when btn-pop-up clicked 
        $('#btn-pop-up').click(function() {
            // check if there is active class
            if ($('.btn-wo-active').length) {
                var workOrderId = $('.btn-wo-active').data('id');
                $('#work_order_id_mobile').val(workOrderId);
                // show modal
                $('#modal-work-order-mobile-menu').modal('show');
            } else {
                // show pop up
                swal.fire({
                    title: 'No Work Order Selected',
                    text: 'Please select work order first',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
            }
        });

        // on double click work order list
        $('#lazy-load-list-data').on('dblclick', '#work-order-list', function() {
            var workOrderId = $(this).data('id');
            $('#work_order_id_mobile').val(workOrderId);

            // ajax get work order detail
            $.ajax({
                url: '/work-order/mobile/detail',
                type: 'GET',
                data: {
                    work_order_id: workOrderId
                },
                success: function(response) {
                    if (response.status) {
                        var workOrder = response.work_order;
                        $('#detailWOModalLabel').text('Detail WO ' + workOrder
                            .work_order_number);
                        $('#no-wo-detail').text(workOrder.work_order_number);
                        $('#no-so-detail').text(workOrder.sales_order_number);
                        // format date work order
                        $('#tanggal-wo-detail').text(new Date(workOrder.date)
                            .toLocaleDateString(
                                'id-ID', {
                                    weekday: 'long',
                                    year: 'numeric',
                                    month: 'long',
                                    day: 'numeric'
                                }));
                        $('#nama-customer-detail').text(workOrder.customer.name);
                        $('#alamat-customer-detail').text(workOrder.customer.address);
                        $('#discount-wo-detail').text(workOrder.discount + '%');
                        $('#extra-discount-wo-detail').text(workOrder.extra_discount + '%');
                        // format total number to currency
                        $('#total-wo-detail').text(new Intl.NumberFormat('id-ID', {
                            style: 'currency',
                            currency: 'IDR',
                            minimumFractionDigits: 0
                        }).format(workOrder.total));
                        $('#status-wo-detail').text(workOrder.sales_order.status);
                        $('#payment-status-detail').text(workOrder.sales_order
                            .status);
                        $('#payment-method-detail').text(workOrder.sales_order
                            .payment_method.name);
                        $('#payment-link-detail').text(workOrder.sales_order
                            .midtrans_payment_link);

                        // battery list loop 
                        $('#list-battery-detail').empty();
                        workOrder.batteries.forEach(function(battery) {
                            $('#list-battery-detail').append(`
                                <tr>
                                    <td colspan="3">${battery.battery_name}</td>
                                </tr>
                                <tr>
                                    <td width="60%">${new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(battery.battery_price)}</td>
                                    <td width="50%">${battery.quantity}</td>
                                    <td width="50%">${new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(battery.battery_price)}</td>
                                </tr>
                            `);
                        });


                        $('#work_order_id_mobile').val(workOrder.id);
                    }
                }
            });

            // show modal work order detail
            $('#modal-work-order-detail').modal('show');
        });


        // when delete work order clicked
        $('.delete-btn').click(function() {
            var workOrderId = $('#work_order_id_mobile').val();
            if (workOrderId) {
                swal.fire({
                    title: 'Are you sure?',
                    text: 'You will not be able to recover this work order!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'No, keep it'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/work-order/mobile/delete',
                            type: 'POST',
                            data: {
                                work_order_id: workOrderId,
                                _token: "{{ csrf_token() }}",
                            },
                            success: function(response) {
                                console.log(response);

                                if (response.success) {
                                    swal.fire({
                                        title: 'Deleted!',
                                        text: 'Work order has been deleted.',
                                        icon: 'success',
                                        confirmButtonText: 'OK'
                                    });
                                } else {
                                    swal.fire({
                                        title: 'Unable to delete!',
                                        text: 'Work order cannot be deleted.',
                                        icon: 'error',
                                        confirmButtonText: 'OK'
                                    });
                                }

                                $('#modal-work-order-mobile-menu').modal(
                                    'hide');
                            }
                        });
                    }
                });
            } else {
                swal.fire({
                    title: 'No Work Order Selected',
                    text: 'Please select work order first',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
            }
        });

        // when print work order clicked
        $('.print-work-btn').click(function() {
            var workOrderId = $('#work_order_id_mobile').val();
            if (workOrderId) {
                $("#work_order_id").val(workOrderId);
                showModalPrint("/work-order/print/" + workOrderId, workOrderId);
            } else {
                swal.fire({
                    title: 'No Work Order Selected',
                    text: 'Please select work order first',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
            }
        });

        // when print technician report clicked
        $('.print-tech-btn').click(function() {
            var workOrderId = $('#work_order_id_mobile').val();
            if (workOrderId) {
                $("#work_order_id").val(workOrderId);
                goToPage("/work-order/mobile/print-technician-report/" + workOrderId, true);
                // window.location = "/work-order/print-technician-report/" + workOrderId;
            } else {
                swal.fire({
                    title: 'No Work Order Selected',
                    text: 'Please select work order first',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
            }
        });

        // when complete work order clicked
        $('.complete-btn').click(function() {
            $('#modal-work-order-mobile-menu').modal('hide');
            var workOrderId = $('#work_order_id_mobile').val();
            if (workOrderId) {
                $.ajax({
                    url: "/work-order/production-code",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        work_order_id: workOrderId
                    },
                    success: function(responseData) {
                        if (responseData.status == true) {
                            let batteries = responseData.production_code
                                .sales_order.batteries;
                            let batteriesHtml = ``;
                            batteriesHtml += `
                <table class="table">
                    <thead>
                        <tr>
                            <th>Production Code</th>
                            <th>Battery Name</th>
                            <th width="20%">Qty</th>
                            <th width="20%">Image</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

                            batteries.forEach(function(battery) {
                                // jika battery production code null, maka tampilkan input text ganti dengan kosong
                                if (battery
                                    .battery_production_code == null
                                ) {
                                    battery
                                        .battery_production_code =
                                        '';
                                }
                                batteriesHtml += `
                    <tr>
                        <input type="hidden" name="battery_id[]" value="${battery.id}">
                        <td><input type="text" name="production_code[]" value="${battery.battery_production_code}" class="form-control"></td>
                        <td><input type="text" name="battery_name[]" value="${battery.battery_name}" class="form-control" readonly></td>
                        <td><input type="number" name="battery_quantity[]" value="${battery.quantity}" class="form-control" readonly></td>
                        
                        <td>
                            <label for="image-${battery.id}" class="btn btn-primary btn-sm">
                                <i class="fa-solid fa-camera"></i>
                                <span class="file-name"></span>
                            </label>
                            <input type="file" name="battery_image[]" id="image-${battery.id}" class="d-none file-input" accept="image/*" capture="camera">
                        </td>
                    </tr>
                `;
                            });

                            batteriesHtml += `
                    </tbody>
                </table>
            `;


                            $('.production_code_data').html(`
                                            <div class="text-center">
                                            <p>Work Order: ${responseData.production_code.work_order_number}</p>
                                            </div>
                                            <div class="batteries-data">${batteriesHtml}</div>`);
                        } else {
                            $('.production_code_data').html(`
                                            <div class="text-center">
                                                <p class="text-danger">Failed to load production code.</p>
                                            </div>
                                        `);
                        }
                    },
                    error: function(xhr) {
                        $('.production_code_data').html(`
                                        <div class="text-center">
                                            <p class="text-danger">Failed to load production code.</p>
                                        </div>
                                    `);
                    }
                });

                // show modal for upload image
                $('#modal-upload-complete-work-order').modal('show');
                $('#work_order_id_image').val(workOrderId);

            } else {
                swal.fire({
                    title: 'No Work Order Selected',
                    text: 'Please select work order first',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
            }
        });

        // search-work-order keyup
        $('#search-work-order').keyup(function() {
            $('#lazy-load-list-data').empty();
            $('#lazy-load-limit').val(10);
            $('#lazy-load-offset').val(0);
            loadWorkOrderList();
        });
    });

    function loadWorkOrderList() {
        isLoading = true;

        var limit = $('#lazy-load-limit').val();
        var offset = $('#lazy-load-offset').val();
        var search = $('#search-work-order').val();

        // loading animation
        $('#lazy-load-list-data').append(`
            <div class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
        `);
        $.ajax({
            url: "/work-order/mobile/lazy-load/list",
            type: 'GET',
            data: {
                limit: limit,
                offset: offset,
                search: search
            },
            success: function(response) {
                if (response.status) {
                    var workOrders = response.work_orders.row;
                    var workOrderList = $('#lazy-load-list-data');

                    var no = parseInt(offset) + 1;
                    workOrders.forEach(function(workOrder) {

                        // format number to currency wihtout decimal
                        formatNumber = new Intl.NumberFormat('id-ID', {
                            style: 'currency',
                            currency: 'IDR',
                            minimumFractionDigits: 0
                        }).format(workOrder.total);

                        var textColor = '';
                        if (workOrder.status == 'completed')
                            textColor = 'text-info';

                        var workOrderCard = `
                        <div class="card bg-white mt-1" id="work-order-list" data-id="${workOrder.id}">
                        <div class="row mt-2 mb-2 left-2 ${textColor}">
                            <div class="col-2">
                                <button class="btn bg-dark-blue float-left btn-rounded text-center font-size-10 text-light" id="btn-work-order-card" data-id="${workOrder.id}">
                                    ${no++}
                                </button>
                            </div>
                            <div class="col-6">
                                <span class="work-order-name">
                                    ${workOrder.customer_name}
                                </span>
                                <br>
                                <span class="work-order-id">
                                    ${workOrder.work_order_number}
                                </span>
                            </div>
                            <div class="col">
                                <span class="work-order-price right-2">
                                    ${formatNumber}
                                </span>
                            </div>
                        </div>
                        </div>
                            `;
                        workOrderList.append(workOrderCard);
                    });

                    // remove loading animation
                    $('#lazy-load-list-data').find('.spinner-border').remove();

                    $('#lazy-load-offset').val(parseInt(offset) + parseInt(limit));
                    $('#lazy-load-limit').val(parseInt(limit) + 10);


                } else {
                    // remove loading animation
                    $('#lazy-load-list-data').find('.spinner-border').remove();
                    $('#lazy-load-list-data').append(`
                        <div class="text-center">
                            <p>No data found</p>
                        </div>
                    `);
                }
                isLoading = false;
            },
            error: function() {
                // Remove loading animation
                $('#lazy-load-list-data').find('.spinner-border').remove();
                isLoading = false; // Reset the flag if there's an error
            }
        });
    }

    function showModalPrint(url, id) {
        // hide modal work order detail
        $("#modal-work-order-mobile-menu").modal('hide');
        // Show the print modal.
        $('#modal-printx').modal('show');
        $("#work_order_idx").val(id);
        showUploadImage();
    }
</script>

<script>
    let trackStart = false;
    let intervalId;

    $(function() {
        $(document).on('change', '.file-input', function() {
            var fileName = $(this).val().split('\\').pop(); // Mendapatkan nama file
            $(this).siblings('label').find('.file-name').text(fileName); // Menampilkan nama file
        });

        $("#go-btn").on('click', function() {
            var workOrderId = $('#work_order_id_mobile').val();
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    const latitude = position.coords.latitude;
                    const longitude = position.coords.longitude;

                    if (!trackStart)
                        startTracking(workOrderId, latitude, longitude);
                    else
                        endTracking(workOrderId, latitude, longitude);
                });
            } else {
                Swal.fire({
                    title: 'Tracking Error',
                    text: 'Your browser does not support geolocation tracking.',
                    icon: 'error',
                    timer: 1000,
                    showConfirmButton: false
                });
            }
        });
    });

    function startTracking(workOrderId, latitude, longitude) {
        // Start the tracking.
        trackStart = true;

        $.ajax({
            url: '/work-order/mobile/track/start',
            method: 'POST',
            data: {
                workOrderId: workOrderId,
                currentLat: latitude,
                currentLon: longitude,
                _token: "{{ csrf_token() }}",
            },
            beforeSend: function() {
                $("#go-btn").addClass("disabled");
            },
            success: function(response) {
                var response = JSON.parse(response);
                if (!response.status) {
                    trackStart = false;
                    Swal.fire({
                        title: 'Tracking Error',
                        text: 'There was an error starting the tracking.',
                        icon: 'error',
                        timer: 1000,
                        showConfirmButton: false
                    });
                    return;
                }

                Swal.fire({
                    title: 'Tracking Started',
                    text: 'Do not close the browser while tracking is running!',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });
                $("#go-btn").removeClass("disabled");
                $("#go-btn-text").text("Stop Tracking Technician");

                intervalId = setInterval(function() {
                    navigator.geolocation.getCurrentPosition(function(position) {
                        let latitude = position.coords.latitude;
                        let longitude = position.coords.longitude;

                        updateTracking(workOrderId, latitude, longitude);
                    }, function(error) {
                        console.log("Error getting location:", error);
                    }, {
                        enableHighAccuracy: true,
                        timeout: 5000
                    });
                }, 5000);
            },
            error: function(error) {
                Swal.fire({
                    title: 'Tracking Error',
                    text: 'There was an error starting the trackingx.',
                    icon: 'error',
                    timer: 1000,
                    showConfirmButton: false
                });
                trackStart = false;
            }
        });
    }

    function updateTracking(workOrderId, latitude, longitude) {
        $.ajax({
            url: '/work-order/mobile/track/update',
            method: 'POST',
            data: {
                workOrderId: workOrderId,
                currentLat: latitude,
                currentLon: longitude,
                _token: "{{ csrf_token() }}",
            },
            success: function(response) {
                console.log("Tracking updated:", latitude, longitude);
            },
            error: function(error) {
                console.log("Tracking update error:", error);
            }
        });
    }

    function endTracking(workOrderId, latitude, longitude) {
        // Stop the tracking.
        trackStart = false;

        // Stop tracking.
        clearInterval(intervalId);

        $.ajax({
            url: '/work-order/mobile/track/end',
            method: 'POST',
            data: {
                workOrderId: workOrderId,
                currentLat: latitude,
                currentLon: longitude,
                _token: "{{ csrf_token() }}",
            },
            success: function(response) {
                var response = JSON.parse(response);

                if (!response.status) {
                    Swal.fire({
                        title: 'Tracking Error',
                        text: 'There was an error ending the tracking.',
                        icon: 'error',
                        timer: 1000,
                        showConfirmButton: false
                    });
                    return;
                }

                Swal.fire({
                    title: 'Tracking Ended',
                    icon: 'success',
                    timer: 3000,
                    showConfirmButton: false
                });
                $("#go-btn-text").text("Go Technician Go");
            },
            error: function(error) {
                Swal.fire({
                    title: 'Ending Tracking Error',
                    text: 'There was an error ending the tracking.',
                    icon: 'error',
                    timer: 1000,
                    showConfirmButton: false
                });
            }
        });
    }
</script>


<div class="modal fade" id="modal-printx" tabindex="-1" aria-labelledby="modal-print-label" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-light" id="modal-print-label"><i class="fas fa-print"></i> Print Work
                    Order
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{-- select option --}}
                <div class="form-group mb-3">
                    <form action="/work-order/mobile/print" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="work_order_id" id="work_order_idx">
                        <label for="print_type">Print Type</label>
                        <label for="print_option">Select Print Option:</label>
                        <select class="form-select" id="print_option" name="print_option">
                            <option value="regular_dan_instalasi">1. Regular dan Instalasi</option>
                            <option value="tokopedia_dan_instalasi">2. Tokopedia dan Instalasi</option>
                            <option value="tokopedia_tanpa_instalasi">3. Tokopedia tanpa Instalasi</option>
                        </select>

                        <div id="upload-column"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary" id="btn-print"><i class="fas fa-print"></i>
                    Print</button>
                </form>
            </div>
        </div>
    </div>
</div>
