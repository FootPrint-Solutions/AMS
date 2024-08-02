{{-- mobile version --}}
<style>
    #title {
        font-weight: 700;
        font-size: 16px;
        line-height: 24px;
        color: #000000;
    }

    .input-with-icon {
        position: relative;
        display: inline-block;
    }

    .input-with-icon input {
        padding-left: 30px;
    }

    .input-with-icon .material-icons {
        position: absolute;
        top: 50%;
        left: 15px;
        transform: translateY(-50%);
        pointer-events: none;
        color: #aaa;
    }

    #btn-more {
        width: 44px;
        height: 44px;
        border-radius: 100%;
        flex: none;
        order: 1;
        flex-grow: 0;
        text-align: center;
        color: rgb(256, 256, 256);
        background-color: rgb(143, 143, 143);
    }

    #input-search {
        background-color: rgb(241, 241, 241);
    }

    .card {
        background-color: rgb(241, 241, 241);
    }

    #btn-add-mobile {
        color: rgb(256, 256, 256);
        background-color: rgb(95, 211, 169);
        height: 50px;
        border-radius: 20px;
    }

    .list-group-item {
        margin-bottom: 10px;
        border-radius: .25rem;
    }

    .text-very-small {
        font-size: 0.7em;
    }

    .scrollable-list {
        max-height: 50vh;
        overflow-y: auto;
    }

    .btn-number {
        background-color: rgb(42, 57, 80);
        color: rgb(256, 256, 256);
        width: 44px;
        height: 44px;
    }

    .btn-number-selected {
        background-color: rgb(95, 211, 169);
    }

    #btn-post {
        background-color: rgb(42, 57, 80);
        color: rgb(256, 256, 256);
        height: 55px;
    }

    #btn-invoice {
        background-color: rgb(38, 64, 105);
        color: rgb(256, 256, 256);
        height: 55px;
    }

    #btn-work-order {
        background-color: rgb(10, 117, 157);
        color: rgb(256, 256, 256);
        height: 55px;
    }

    #btn-recreate-payment {
        background-color: rgb(9, 197, 203);
        color: rgb(256, 256, 256);
        height: 55px;
    }

    #btn-copy-link {
        background-color: rgb(45, 194, 141);
        color: rgb(256, 256, 256);
        height: 55px;
    }

    .icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 44px;
        height: 44px;
        border-radius: 100%;
        background-color: rgba(255, 255, 255, 0.4);
    }

    .filter-status {
        cursor: pointer;
    }

    .active {
        font-weight: 700;
        text-decoration: underline;
    }
</style>

<div class="d-block d-md-none mb-3">
    {{-- Title --}}
    <div class="mb-3" id="title">Sales Order</div>

    {{-- Search --}}
    <div class="row mb-4">
        <div class="col-10 input-with-icon">
            <span class="material-icons">search</span>
            <input type="text" class="form-control" id="input-search"
                placeholder="Search by Customer, Sales Order Number">
        </div>

        <div class="col">
            <button id="btn-more" data-bs-toggle="modal" data-bs-target="#modal-action"><span
                    class="material-icons">more_horiz</span></button>
        </div>
    </div>

    {{-- Sales Orders List --}}
    <div class="card">
        <div class="card-body">
            {{-- Status --}}
            <h5 class="card-title">
                <div class="row text-center">
                    <div class="col filter-status active" data-status="all">All</div>
                    <div class="col filter-status" data-status="paid">Paid</div>
                    <div class="col filter-status" data-status="pending">Pending</div>
                    <div class="col filter-status" data-status="failed">Failed</div>
                </div>
            </h5>
            <hr>

            {{-- List --}}
            <div class="scrollable-list">
                <ul class="list-group"></ul>
            </div>
        </div>
    </div>

    {{-- Button --}}
    <button class="btn btn-block" id="btn-add-mobile">Add New Sales Order</button>
</div>

{{-- Modal Action --}}
<div class="modal fade" id="modal-action">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            {{-- Header --}}
            <h3 class="text-center mt-3">Action</h3>

            {{-- Body --}}
            <div class="modal-body">
                <button class="btn btn-block mb-2 fw-bold text-start" id="btn-post">
                    <div class="icon"><span class="material-icons">task</span></div>
                    Post
                </button>

                <button class="btn btn-block mb-2 fw-bold text-start" id="btn-invoice">
                    <div class="icon"><span class="material-icons">description</span></div>
                    Invoice
                </button>

                <button class="btn btn-block mb-2 fw-bold text-start" id="btn-work-order">
                    <div class="icon"><span class="material-icons">construction</span></div>
                    Create Work Order
                </button>

                <button class="btn btn-block mb-2 fw-bold text-start" id="btn-recreate-payment">
                    <div class="icon"><span class="material-icons">add_link</span></div>
                    Re-create Payment
                </button>

                <button class="btn btn-block mb-2 fw-bold text-start" id="btn-copy-link">
                    <div class="icon"><span class="material-icons">link</span></div>
                    Copy Payment Link
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Detail --}}
<div class="modal fade" id="modal-detail">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            {{-- Header --}}
            <h3 class="text-center mt-3">SO Details</h3>

            {{-- Body --}}
            <div class="modal-body">
                <table>
                    <tr>
                        <td>SO Number</td>
                        <td>:</td>
                        <td><span id="detail-so-number"></span></td>
                    </tr>

                    <tr>
                        <td>Date</td>
                        <td>:</td>
                        <td><span id="detail-date"></span></td>
                    </tr>

                    <tr>
                        <td>Vehicle</td>
                        <td>:</td>
                        <td><span id="detail-vehicle"></span></td>
                    </tr>

                    <tr>
                        <td>Distributor/Shop</td>
                        <td>:</td>
                        <td><span id="detail-distributor"></span>/<span id="detail-shop"></span></td>
                    </tr>

                    <tr>
                        <td>Technician</td>
                        <td>:</td>
                        <td><span id="detail-technician"></span></td>
                    </tr>

                    <tr>
                        <td>Total</td>
                        <td>:</td>
                        <td><span id="detail-total"></span></td>
                    </tr>

                    <tr>
                        <td>Payment Status</td>
                        <td>:</td>
                        <td><span id="detail-payment-status"></span></td>
                    </tr>

                    <tr>
                        <td>Status</td>
                        <td>:</td>
                        <td><span id="detail-status"></span></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>


<script>
    $(function() {
        refreshList();

        $(".filter-status").on("click", function() {
            let status = $(this).data("status");
            let filter = $("#input-search").val();

            $(".filter-status").each(function() {
                $(this).removeClass("active");
            });
            $(this).addClass("active");

            refreshList(status, filter);
        })

        $("#input-search").on("keyup", function() {
            let status = "all";
            $(".filter-status").each(function() {
                if ($(this).hasClass("active"))
                    status = $(this).data("status");
            });

            let filter = $(this).val();
            refreshList(status, filter);
        })

        // Post
        $("#btn-post").on("click", function() {
            let formData = new FormData();
            formData.set("ids", getSelected());
            formData.set("_token", "{{ csrf_token() }}");
            sendSubmitRequest("/sales-order/post", formData, function() {
                $("#modal-action").modal("hide");
                refreshList();
            });
        })

        // Invoice
        $("#btn-invoice").on("click", function() {
            let selected = getSelected();
            if (selected.length > 1) {
                alert("Cannot download invoice for more than one sales order.");
            } else {
                downloadPDF("/sales-order/invoice/" + selected[0]);
                $("#modal-action").modal("hide");
                refreshList();
            }
        })
    });

    $(document).on("click", ".btn-number", function() {
        if ($(this).hasClass("btn-number-selected")) {
            $(this).html($(this).data('number'));
            $(this).removeClass("btn-number-selected");
        } else {
            $(this).html("<span class='material-icons'>check</span>");
            $(this).addClass("btn-number-selected");
        }
    });
</script>

<script>
    function getSelected() {
        let selected = [];
        $(".btn-number-selected").each(function() {
            selected.push($(this).data("id"));
        });
        return selected;
    }

    function viewDetail(orderId) {
        console.log("Order ID: " + orderId);
        $.ajax({
            url: "/sales-order/show/detail/mobile/" + orderId,
            type: "GET",
            success: function(data) {
                console.log(data);
                $("#detail-so-number").text(data.sales_order_number);
                $("#detail-date").text(data.date);
                $("#detail-vehicle").text(data.vehicle.name);
                $("#detail-distributor").text(data.shop.distributor.name);
                $("#detail-shop").text(data.shop.name);
                $("#detail-technician").text(data.technician.name);
                $("#detail-total").text("Rp" + data.total);
                $("#detail-payment-status").text(data.payment_status);
                $("#detail-status").text(data.status);

                $("#modal-detail").modal("show");
            }
        });
    }

    function refreshList(status = 'all', filter = '') {
        $.ajax({
            url: "/sales-order/show/mobile/" + status + "/" + filter,
            type: "GET",
            success: function(data) {
                var list = $('.list-group');
                list.empty();

                for (i = 0; i < data.length; i++) {
                    var order = data[i];
                    var textColor = '';
                    if (order.status == 'posted')
                        textColor = 'text-success';
                    else if (order.status == 'completed')
                        textColor = 'text-info';

                    var listItem = `
                        <li class="list-group-item ${textColor}">
                            <div class="row">
                                <div class="col-2">
                                    <button class="btn rounded-circle btn-number" data-id="${order.id}" data-number="${i + 1}">${i + 1}</button>
                                </div>

                                <div class="col-6" onclick="viewDetail('${order.id}')">
                                    <div class="row">
                                        <p class="fw-bold text-truncate">${order.customer.name}</p>
                                        <p class="text-muted text-very-small">${order.sales_order_number}</p>
                                    </div>
                                </div>

                                <div class="col-4" onclick="viewDetail('${order.id}')">
                                    <p>Rp${order.total}</p>
                                </div>
                            </div>
                        </li>
                    `;

                    list.append(listItem);
                }
            }
        });
    }
</script>
