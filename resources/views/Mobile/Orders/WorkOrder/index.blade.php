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
                        <td width="50%%">WO Number</td>
                        <td width="50%">:</td>
                        <td width="50%">WO240700008</td>
                    </tr>
                    <tr>
                        <td>SO Number</td>
                        <td>:</td>
                        <td>AK240700008</td>
                    </tr>
                    <tr>
                        <td>Date</td>
                        <td>:</td>
                        <td>3 Juli 2024</td>
                    </tr>
                    <tr>
                        <td>Customer</td>
                        <td>:</td>
                        <td>Ginanjar</td>
                    </tr>
                    <tr>
                        <td>Address</td>
                        <td>:</td>
                        <td>Padasuka</td>
                    </tr>
                    <tr>
                        <td>Discount</td>
                        <td>:</td>
                        <td>8.05%</td>
                    </tr>
                    <tr>
                        <td>Extra Discount</td>
                        <td>:</td>
                        <td>0.00%</td>
                    </tr>
                    <tr>
                        <td>Total (IDR)</td>
                        <td>:</td>
                        <td>800.000</td>
                    </tr>
                    <tr>
                        <td>Status</td>
                        <td>:</td>
                        <td>Draft</td>
                    </tr>
                </table>
                <br>
                <div class="detail-section">
                    <div class="section-title">Batteries</div>
                    <div class="badge-custom">
                        <span class="text-battery-detail">AMARON Hi-Life 42B20L</span>
                        <br>
                        <table>
                            <tr>
                                <td width="60%">800.000</td>
                                <td width="50%">1</td>
                                <td width="50%">800.000</td>
                            </tr>
                        </table>
                    </div>
                </div>
                <table>
                    <tr>
                        <td width="50%">Payment Status</td>
                        <td width="50%">:</td>
                        <td width="50%">paid</td>
                    </tr>
                    <tr>
                        <td>Payment Method</td>
                        <td>:</td>
                        <td>Midtrans</td>
                    </tr>
                    <tr>
                        <td>Payment Link</td>
                        <td>:</td>
                        <td><a href="#">Payment Link</a></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<input type="hidden" name="work_order_id" id="work_order_id">

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
            $('#work_order_id').val(workOrderId);

            // show modal work order detail
            $('#modal-work-order-detail').modal('show');
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
            url: "/work-order/lazy-load/list",
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
                        var workOrderCard = `
                        <div class="card bg-white mt-1" id="work-order-list" data-id="${workOrder.id}">
                        <div class="row mt-2 mb-2 left-2">
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

    function clickWorkOrderList() {

    }
</script>
