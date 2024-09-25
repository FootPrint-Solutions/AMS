{{-- Work Order Custom Css --}}
<link rel="stylesheet" href="{{ asset('css/work-order-instruction.css') }}">

<style>
    .zoom {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 1000;
        transition: transform 0.2s ease-in, top 0.2s ease, left 0.2s ease;
        max-width: 90%;
        border: 3px solid black;
    }
</style>

<div class="d-block d-md-none">
    <div class="container">
        <h3>
            Work Order Instruction
        </h3>

        {{-- form search with side icon --}}
        <div class="row">
            <div class="col">
                <div class="top-nav-search-custom-mobile">
                    <form>
                        <input type="text" class="form-control" placeholder="Search here"
                            id="search-work-order-instruction">
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
                <div id="lazy-load-list-data-info"></div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Work Order Mobile Menu --}}
<div class="modal fade" id="modal-work-order-instruction-mobile-menu" tabindex="-1" role="dialog"
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
                                Delete Work Order <br>
                            </button>
                        </div>
                        <div class="col-6">
                            <button class="custom-btn copy-work-btn bg-primary">
                                <i class="fas fa-copy"></i>
                                Copy WO <br> Instruction
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Work Order Detail --}}
<div class="modal
                                    fade" id="modal-work-order-instruction-detail" tabindex="-1"
    role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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

        // when btn-work-order-instruction-card clicked add class active
        $(document).on('click', '#btn-work-order-instruction-card', function() {
            $('.bg-dark-blue').removeClass('btn-wo-active');
            $(this).closest('#btn-work-order-instruction-card').addClass('btn-wo-active');
        });

        $('#lazy-load-list-data').on('click', '#work-order-instruction-list', function() {
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
                $('#modal-work-order-instruction-mobile-menu').modal('show');
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
        $('#lazy-load-list-data').on('dblclick', '#work-order-instruction-list', function() {
            var workOrderId = $(this).data('id');
            $('#work_order_id_mobile').val(workOrderId);
            showModalDetail(workOrderId);

        });

        $(document).on("click", ".img-complete-battery", function() {
            if ($(this).hasClass("zoom")) {
                $(this).removeClass("zoom");
            } else {
                $(".img-complete-battery").each(function() {
                    $(this).removeClass("zoom");
                });
                $(this).addClass("zoom");
            }
        })


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
                            url: '/work-order-instruction/mobile/delete',
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

                                $('#modal-work-order-instruction-mobile-menu')
                                    .modal(
                                        'hide');
                                loadWorkOrderList(true);
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




        // search-work-order-instruction keyup
        $('#search-work-order-instruction').keyup(function() {
            $('#lazy-load-list-data').empty();
            $('#lazy-load-limit').val(10);
            $('#lazy-load-offset').val(0);
            loadWorkOrderList(true);
        });
    });

    function loadWorkOrderList(refreshDefault = false) {
        isLoading = true;

        var limit = $('#lazy-load-limit').val();
        var offset = $('#lazy-load-offset').val();
        var search = $('#search-work-order-instruction').val();

        if (refreshDefault) {
            limit = 10;
            offset = 0;
        }

        // loading animation
        $('#lazy-load-list-data-info').empty();
        $('#lazy-load-list-data-info').append(`
            <div class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
        `);


        $.ajax({
            url: "/work-order-instruction/mobile/lazy-load/list",
            type: 'GET',
            data: {
                limit: limit,
                offset: offset,
                search: search
            },
            success: function(response) {
                // remove loading animation
                $('#lazy-load-list-data-info').find('.spinner-border').remove();

                if (response.status) {
                    var workOrders = response.work_orders.row;


                    if (workOrders.length === 0) {
                        var workOrderList = $('#lazy-load-list-data-info');
                        workOrderList.append(`
                            <div class="text-center">
                                <p>No data found</p>
                            </div>
                        `).appendTo(workOrderList);
                    } else {
                        var workOrderList = $('#lazy-load-list-data');
                        var no = parseInt(offset) + 1;
                        workOrders.forEach(function(workOrder) {
                            // format number to currency without decimal
                            formatNumber = new Intl.NumberFormat('id-ID', {
                                style: 'currency',
                                currency: 'IDR',
                                minimumFractionDigits: 0
                            }).format(workOrder.total);

                            var textColor = '';
                            if (workOrder.date_complete != null && workOrder.date_complete != '') {
                                textColor = 'text-info';
                            } else {
                                textColor = '';
                            }

                            var formatDate = new Date(workOrder.date).toLocaleDateString('id-ID', {
                                weekday: 'long',
                                year: 'numeric',
                                month: 'long',
                                day: 'numeric'
                            });

                            var workOrderCard = `
                            <div class="card bg-white mt-1" id="work-order-instruction-list" data-id="${workOrder.id}">
                            <div class="row mt-2 mb-2 left-2 ${textColor}">
                                <div class="col-2">
                                    <button class="btn bg-dark-blue float-left btn-rounded text-center font-size-10 text-light" id="btn-work-order-instruction-card" data-id="${workOrder.id}" data-work-order-number="${workOrder.work_order_transaction_number}">
                                        ${no++}
                                    </button>
                                </div>
                                <div class="col-6">
                                    <span class="work-order-instruction-name">
                                        ${workOrder.name}
                                    </span>
                                    <br>
                                    <span class="work-order-instruction-id">
                                        ${workOrder.work_order_number}
                                    </span>
                                </div>
                                <div class="col">
                                    <span class="work-order-instruction-price right-2">
                                        ${formatNumber}
                                          ${workOrder.date}
                                    </span>
                                </div>
                               
                            </div>
                            </div>
                            `;
                            workOrderList.append(workOrderCard);
                        });

                        $('#lazy-load-offset').val(parseInt(offset) + parseInt(limit));
                        $('#lazy-load-limit').val(parseInt(limit) + 10);
                    }
                } else {
                    $('#lazy-load-list-data-info').append(`
                        <div class="text-center">
                            <p>No data data</p>
                        </div>
                    `);
                }
                isLoading = false;
            },
            error: function() {
                // Remove loading animation
                $('#lazy-load-list-data-info').find('.spinner-border').remove();
                $('#lazy-load-list-data-info').append(`
                    <div class="text-center">
                        <p>Error loading data</p>
                    </div>
                `);
                isLoading = false; // Reset the flag if there's an error
            }
        });
    }
</script>

<script></script>
