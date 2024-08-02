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
                <button class="btn btn-secondary float-right btn-sm btn-rounded text-center mt-2">
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

        // when click work order list
        $('#lazy-load-list-data').on('click', '#work-order-list', function() {
            var workOrderId = $(this).data('id');
            // add class active to selected work order
            $('#lazy-load-list-data').find('.card').removeClass('card-active-work-order');
            $(this).addClass('card-active-work-order');
        });
    });

    function loadWorkOrderList() {
        isLoading = true; // Set the flag to true indicating loading in progress

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
                                <button class="btn bg-dark-blue float-left btn-rounded text-center font-size-10 text-light">
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
                isLoading = false; // Reset the flag after the load is complete
                // $('#lazy-load-list-data').append(response);
                // $('#lazy-load-offset').val(parseInt(offset) + parseInt(limit));
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
