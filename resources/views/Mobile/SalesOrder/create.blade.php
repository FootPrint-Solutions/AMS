{{-- mobile version --}}
<style>
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

    #input-search {
        background-color: rgb(241, 241, 241);
    }

    .autocomplete-name {
        position: absolute;
        background-color: #f1f1f1;
        max-height: 150px;
        overflow-y: auto;
        border: 1px solid #ccc;
        z-index: 999;
    }

    .suggestion {
        padding: 10px;
        cursor: pointer;
    }

    .suggestion:hover {
        background-color: #ddd;
    }

    #title {
        font-weight: 700;
        font-size: 16px;
        line-height: 24px;
        color: #000000;
    }

    #btn-add-detail-mobile {
        background-color: rgb(95, 211, 169);
        color: rgb(256, 256, 256);
        width: 24px;
        height: 24px;
        text-align: center;
        justify-content: center;
    }


    .text-very-small {
        font-size: 0.7em;
    }

    .list-dash-border {
        border-bottom: 1px dashed #DCDCDC;
    }

    #card-total {
        width: 100%;
        background: #DCDCDC;
        border-radius: 0px 0px 7px 7px;
    }

    #card-grand-total {
        width: 100%;
        background: #BCEBEC;
        border-radius: 7px 7px 7px 7px;
    }

    #btn-add-mobile,
    #btn-save-detail-mobile {
        color: rgb(256, 256, 256);
        background-color: rgb(95, 211, 169);
        height: 50px;
        border-radius: 20px;
    }
</style>

<div class="d-block d-md-none mb-3">
    {{-- Title --}}
    <div class="mb-4" id="title">Add New Sales Order</div>

    <form id="sales-order-form-mobile">
        @csrf

        {{-- Date --}}
        <div class="form-group local-forms mb-4">
            <label for="date">Sales Order Number <span class="login-danger">*</span></label>
            <input type="text" name="salesordernumber" class="form-control" readonly
                @isset($data['profile'])
                            value="{{ $data['profile']['sales_order_number'] }}"
                        @else
                            value="{{ $data['number'] }}"
                        @endisset>
        </div>

        {{-- Date --}}
        <div class="form-group local-forms mb-4">
            <label for="date">Date <span class="login-danger">*</span></label>
            <input type="date" name="date" id="date" class="form-control"
                @isset($data['profile'])
                            value="{{ $data['profile']['date'] }}"
                        @else
                            value="{{ date('Y-m-d') }}"
                        @endisset>
        </div>

        {{-- Customer --}}
        <div class="form-group local-forms mb-4">
            <label for="customer">Customer <span class="login-danger">*</span></label>
            <select class="form-control" id="customer" name="customer" required>
                <option></option>
                @foreach ($data['customers'] as $customer)
                    <option value="{{ $customer['id'] }}" @if (isset($data['profile']) && $data['profile']['customer_id'] == $customer['id']) selected @endif>
                        {{ $customer['name'] }}</option>
                @endforeach
            </select>
        </div>

        {{-- Address --}}
        <div class="form-group local-forms mb-4">
            <label for="address">Address <span class="login-danger">*</span></label>
            <input type="text" name="Address" id="address" class="form-control"
                placeholder="Enter customer address"
                value="@if (isset($data['profile'])) {{ ltrim($data['profile']['address']) }} @endif">

            <input type="hidden" name="Latitude" id="Latitude" value="1" required>
            <input type="hidden" name="Longitude" id="Longitude" value="1" required>
        </div>

        {{-- Vehicle --}}
        <div class="form-group local-forms mb-4">
            <label for="vehicle">Vehicle <span class="login-danger">*</span></label>
            <select class="form-control" id="vehicle" name="vehicle" required>
                <option></option>
                @foreach ($data['vehicles'] as $vehicle)
                    <option value="{{ $vehicle['id'] }}" @if (isset($data['profile']) && $data['profile']['vehicle_id'] == $vehicle['id']) selected @endif>
                        {{ $vehicle['name'] }}</option>
                @endforeach
            </select>
        </div>

        {{-- Shop --}}
        <div class="form-group local-forms mb-4">
            <label for="shop">Shop <span class="login-danger">*</span></label>
            <select class="form-control" id="shop" name="shop" required>
                <option></option>
                @foreach ($data['shops'] as $shop)
                    <option value="{{ $shop['id'] }}" @if (isset($data['profile']) && $data['profile']['distributor_shop_id'] == $shop['id']) selected @endif>
                        {{ $shop['distributor']['name'] . ' - ' . $shop['name'] }}</option>
                @endforeach
            </select>
        </div>

        {{-- Technician --}}
        <div class="form-group local-forms mb-4">
            <label for="technician">Technician</label>
            <select class="form-control" id="technician" name="technician">
                <option></option>
                <option disabled>Select a distributor to select a technician</option>
            </select>
            @isset($data['profile'])
                <input type="hidden" id="technician_id" value="{{ $data['profile']['distributor_shop_technician_id'] }}">
            @endisset
        </div>

        <div class="form-group local-forms mb-4">
            <label for="payment-method">Payment Method <span class="login-danger">*</span></label>
            <select class="form-control" id="payment-method" name="paymentmethod" required>
                <option></option>
                @foreach ($data['payment_methods'] as $method)
                    <option value="{{ $method['id'] }}" @if (isset($data['profile']) && $data['profile']['payment_method_id'] == $method['id']) selected @endif>
                        {{ $method['name'] }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group local-forms mb-4">
            <label for="payment-method">Status <span class="login-danger">*</span></label>
            <select name="status" id="status" class="form-control" required>
                <option value="paid" @if (isset($data['profile']) && $data['profile']['status'] == 'paid') selected @endif>Paid
                </option>
                <option value="pending" @if (isset($data['profile']) && $data['profile']['status'] == 'pending') selected @endif>
                    Pending</option>
                <option value="failed" @if (isset($data['profile']) && $data['profile']['status'] == 'failed') selected @endif>
                    Failed</option>
            </select>
        </div>

        {{-- Add Item --}}
        <div class="mb-1" id="title">Add Item <button type="button" class="btn rounded-circle"
                id="btn-add-detail-mobile"><span class="material-icons text-very-small">add</span></button></div>

        {{-- List Details --}}
        <ul class="list-group list-group-flush" id="list-detail">
            @isset($data['profile'])
                @foreach ($batteries as $battery)
                    <li class="list-group-item list-dash-border">
                        <div class="row">
                            <div class="col-8">
                                <div class="row">
                                    <p class="fw-bold text-truncate">{{ $battery['battery_name'] }}</p>
                                    <p class="text-muted text-very-small">{{ $battery['battery_production_code'] }}</p>
                                </div>
                            </div>

                            <div class="col-4">
                                <div class="row">
                                    <div class="col">
                                        <div class="d-flex justify-content-between">
                                            <span class="badge bg-warning">Tax {{ $battery['tax'] }}%</span>
                                            <span class="badge bg-danger">Disc {{ $battery['discount'] }}%</span>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <p class="fw-bold">Rp{{ formatPrice($battery['price_net']) }}</p>
                                    </div>
                                </div>
                            </div>

                            <input type="hidden" name="batteriescode[]"
                                value="{{ $battery['battery_production_code'] }}">
                            <input type="hidden" name="batteriesname[]" value="{{ $battery['battery_name'] }}">
                            <input type="hidden" name="batteriesid[]" value="{{ $battery['battery_id'] }}">
                            <input type="hidden" name="batteriespriceretail[]"
                                value="{{ $battery['battery_price_retail'] }}">
                            <input type="hidden" name="batteriestax[]" value="{{ $battery['tax'] }}">
                            <input type="hidden" name="batteriestaxprice[]" value="{{ $battery['tax_price'] }}">
                            <input type="hidden" name="batteriesdiscount[]" value="{{ $battery['discount'] }}">
                            <input type="hidden" name="batteriesdiscountprice[]"
                                value="{{ $battery['discount_price'] }}">
                            <input type="hidden" name="batteriesprice[]" class="batteriespricemobile"
                                value="{{ $battery['price_net'] }}">
                            <input type="hidden" name="detailid[]" value="{{ $battery['id'] }}">
                        </div>
                    </li>
                @endforeach
            @endisset
        </ul>

        {{-- Total --}}
        <div class="card" id="card-total">
            <div class="card-body">
                <p class="card-text text-center"><span class="fw-bold">Total</span> : Rp<span id="span-subtotal">
                        @isset($data['profile'])
                            {{ formatPrice($data['profile']['subtotal']) }}
                        @else
                            0
                        @endisset
                    </span>
                </p>
                <input type="hidden" name="subtotal" id="subtotal"
                    @isset($data['profile'])
                            value="{{ $data['profile']['subtotal'] }}"
                        @else
                            value="0"
                        @endisset>
            </div>
        </div>

        {{-- Discount & Grand Total --}}
        <div class="card" id="card-grand-total">
            <div class="card-body">
                {{-- Discount --}}
                <div class="row">
                    <div class="col-4"><span class="fw-bold">Invoice Discount</span></div>
                    <div class="col">
                        <div class="input-group">
                            <span class="input-group-text border-end">Rp</span>
                            <input type="text" class="form-control" name="discount" id="discount-mobile"
                                @isset($data['profile'])
                            value="{{ $data['profile']['discount_price'] }}"
                        @else
                            value="0"
                        @endisset>
                        </div>
                    </div>
                    {{-- <div class="col-2">Toggle</div> --}}
                </div>

                <hr>

                {{-- Grand Total --}}
                <div class="row">
                    <div class="col-4"></div>
                    <div class="col-7"><span class="fw-bold">Grand Total</span> : Rp<span id="span-grand-total">
                            @isset($data['profile'])
                                {{ formatPrice($data['profile']['subtotal']) }}
                            @else
                                0
                            @endisset
                        </span>
                    </div>
                    <input type="hidden" name="total" id="grandtotal"
                        @isset($data['profile'])
                            value="{{ $data['profile']['total'] }}"
                        @else
                            value="0"
                        @endisset>
                </div>
            </div>
        </div>

        @isset($data['profile'])
            <input type="hidden" id="id" name="id" value="{{ $data['profile']['id'] }}">
        @endisset

        {{-- Button --}}
        <button class="btn btn-block" id="btn-add-mobile"
            @if (isset($data['profile'])) value="update">
                    Update
                @else
                    value="create">
                    Create @endif
            Sales Order </button>
    </form>
</div>

{{-- Modal Detail --}}
<div class="modal fade" id="modal-detail">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            {{-- Header --}}
            <h3 class="text-center mt-3">Add Item</h3>

            {{-- Body --}}
            <div class="modal-body">
                <div class="row mb-4">
                    <div class="input-with-icon">
                        <span class="material-icons">search</span>
                        <input type="text" class="form-control" id="input-search-detail-battery"
                            placeholder="Search Item">
                        <div class="autocomplete-name" id="autocomplete-name"></div>
                    </div>
                </div>

                <div class="form-group local-forms mb-4">
                    <label>Production Code</label>
                    <input type="text" id="productioncode" class="form-control">
                </div>

                <div class="form-group local-forms mb-4">
                    <label>Product Name</label>
                    <input type="text" id="productname" class="form-control">
                </div>

                <div class="form-group local-forms mb-4">
                    <div class="input-group">
                        <label>Price Retail</label>
                        <span class="input-group-text border-end">Rp</span>
                        <input type="text" id="priceretail" class="form-control">
                    </div>
                </div>

                <div class="row">
                    <div class="col-4">
                        <div class="form-group local-forms mb-4">
                            <div class="input-group">
                                <label>Tax</label>
                                <input type="text" id="tax" class="form-control">
                                <input type="hidden" id="taxinprice">
                                <span class="input-group-text border-end">%</span>
                            </div>
                        </div>
                    </div>

                    <div class="col">
                        <div class="form-group local-forms mb-4">
                            <div class="input-group">
                                <label>Price + Tax</label>
                                <span class="input-group-text border-end">Rp</span>
                                <input type="text" id="pricetax" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group local-forms mb-4">
                    <div class="input-group">
                        <label>Discount</label>
                        <input type="text" id="detdiscount" class="form-control">
                        <input type="hidden" id="detdiscountinprice">
                        <span class="input-group-text border-end">%</span>
                    </div>
                </div>

                <div class="form-group local-forms mb-4">
                    <div class="input-group">
                        <label>Price Net</label>
                        <span class="input-group-text border-end">Rp</span>
                        <input type="text" id="pricenet" class="form-control">
                    </div>
                </div>

                <input type="hidden" id="detid">

                <button class="btn btn-block" id="btn-save-detail-mobile">Add</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(function() {
        $("#btn-add-detail-mobile").on("click", function() {
            // Clear all inputs value.
            $("#modal-detail input").val('');

            // Show modal.
            $("#modal-detail").modal("show");
        })

        $("#btn-save-detail-mobile").on("click", function() {
            var productName = $("#productname").val();
            var productionCode = $("#productioncode").val();
            var priceRetail = $("#priceretail").val();
            var tax = $("#tax").val();
            var taxPrice = $("#taxinprice").val();
            var discount = $("#detdiscount").val();
            var discountPrice = $("#detdiscountinprice").val();
            var priceNet = $("#pricenet").val();
            var id = $("#detid").val();

            var list = `
            <li class="list-group-item list-dash-border">
                <div class="row">
                    <div class="col-8">
                        <div class="row">
                            <p class="fw-bold text-truncate">${productName}</p>
                            <p class="text-muted text-very-small">${productionCode}</p>
                        </div>
                    </div>

                    <div class="col-4">
                        <div class="row">
                            <div class="col">
                                <div class="d-flex justify-content-between">
                                    <span class="badge bg-warning">Tax ${tax}%</span>
                                    <span class="badge bg-danger">Disc ${discount}%</span>
                                </div>
                            </div>
                            <div class="col-12">
                                <p class="fw-bold">Rp${formatNumberWithSeparator(priceNet)}</p>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="batteriescode[]" value="${productionCode}">
                    <input type="hidden" name="batteriesname[]" value="${productName}">
                    <input type="hidden" name="batteriesid[]" value="${id}">
                    <input type="hidden" name="batteriespriceretail[]" value="${removeSeparators(priceRetail)}">
                    <input type="hidden" name="batteriestax[]" value="${tax}">
                    <input type="hidden" name="batteriestaxprice[]" value=${removeSeparators(taxPrice)}>
                    <input type="hidden" name="batteriesdiscount[]" value="${discount}">
                    <input type="hidden" name="batteriesdiscountprice[]" value="${removeSeparators(discountPrice)}">
                    <input type="hidden" name="batteriesprice[]" class="batteriespricemobile" value="${removeSeparators(priceNet)}">
                </div>
            </li>
            `;
            $("#list-detail").append(list);

            // Calculate total.
            var subtotal = 0;
            $(".batteriespricemobile").each(function() {
                subtotal += parseInt($(this).val());
            });
            $("#span-subtotal").html(formatNumberWithSeparator(subtotal));
            $("#subtotal").val(subtotal);
            var discount = parseInt($("#discount-mobile").val());
            var total = subtotal - discount;
            $("#span-grand-total").html(formatNumberWithSeparator(total));
            $("#grandtotal").val(subtotal);

            // Hide modal
            $("#modal-detail").modal("hide");
        })

        $("#discount-mobile").on("keyup", function() {
            var subtotal = 0;
            $(".batteriespricemobile").each(function() {
                subtotal += parseInt($(this).val());
            });
            $("#span-subtotal").html(formatNumberWithSeparator(subtotal));
            $("#subtotal").val(subtotal);
            var discount = parseInt($("#discount-mobile").val());
            var total = subtotal - discount;
            $("#span-grand-total").html(formatNumberWithSeparator(total));
            $("#grandtotal").val(subtotal);
        });

        $("#sales-order-form-mobile").on("submit", function(event) {
            event.preventDefault();

            let mode = $("#btn-add-mobile").attr("value"); // update || create
            let url = (mode == "update") ? "/sales-order/update" : "/sales-order/store";

            // let address = $("#AddressSearchColumn").val();
            // let lat = $("#Latitude").val();
            // let lon = $("#Longitude").val();
            // if (address == "" || lat == "" || lon == "") {
            //     Swal.fire({
            //         title: "Error",
            //         text: "Please select address",
            //         icon: "error",
            //     });
            //     return;
            // }
            // calculateTotal();

            // Obtain submitted form data.
            let formData = new FormData($(this)[0]);

            // Send submit POST request via AJAX.
            sendSubmitRequest(url, formData, function() {
                // Redirect to index page.
                goToPage(indexUrl);
            });
        });
    });

    $("#modal-detail").on('keyup', '#tax, #detdiscount', function() {
        calculateDetailTotal();
    });

    $("#modal-detail").on('keyup', '#input-search-detail-battery', function() {
        let keyword = $(this).val();

        if (keyword.length > 0) {
            let autocomplete = $(this).next('.autocomplete-name');
            $('.autocomplete-name').html('');

            $.ajax({
                url: "/battery/get/" + keyword,
                type: "GET",
                success: function(data) {
                    if (data.length > 0) {
                        autocomplete.empty();

                        data.forEach(function(item) {
                            let suggestion = $('<div class="suggestion">' + item.name +
                                '</div>');

                            suggestion.click(function() {
                                $("#productname").val(item.name);
                                $("#priceretail").val(formatNumberWithSeparator(item
                                    .price_retail));
                                $("#tax").val(0);
                                $("#pricetax").val(0);
                                $("#detdiscount").val(0);
                                $("#pricenet").val(0);
                                $("#detid").val(item.id);

                                // Calculate subtotal.
                                calculateDetailTotal();
                                autocomplete.html('');
                            });

                            autocomplete.append(suggestion);
                        });
                    } else {
                        autocomplete.html('');
                    }
                }
            });
        } else {
            $('.autocomplete-name').html('');
        }
    });
</script>

<script>
    function calculateDetailTotal() {
        var priceRetail = parseInt(removeSeparators($("#priceretail").val()));
        var tax = parseInt($("#tax").val());
        $("#taxinprice").val(tax * priceRetail / 100);

        // Calculate price + tax.
        var priceTax = (tax * priceRetail / 100) + priceRetail;
        $("#pricetax").val(formatNumberWithSeparator(priceTax));

        // Calculate total.
        var discount = parseInt($("#detdiscount").val());
        $("#detdiscountinprice").val(priceTax * discount / 100);

        var priceDiscount = priceTax - (priceTax * discount / 100);
        $("#pricenet").val(formatNumberWithSeparator(priceDiscount));
    }
</script>
