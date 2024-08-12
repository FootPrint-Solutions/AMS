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

    #btnAddressx {
        max-width: 2em !important;
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
            <label for="customerx">Customer <span class="login-danger">*</span></label>
            <select class="form-control" id="customerx" name="customer" required>
                <option></option>
                @foreach ($data['customers'] as $customer)
                    <option value="{{ $customer['id'] }}" @if (isset($data['profile']) && $data['profile']['customer_id'] == $customer['id']) selected @endif>
                        {{ $customer['name'] }}</option>
                @endforeach
            </select>
        </div>

        {{-- Address --}}
        <div class="row">
            <div class="col-10">
                <div class="form-group local-forms">
                    <label for="address">Address <span class="login-danger">*</span></label>
                    <input type="text" class="form-control" name="Address" id="AddressSearchColumnx"
                        value="@if (isset($data['profile'])) {{ ltrim($data['profile']['address']) }} @endif"
                        readonly required>
                </div>
            </div>

            <div class="col">
                <div class="col-sm-2">
                    <button type="button" class="btn btn-primary" id="btnAddressx"><i
                            class="fas fa-map-marker"></i></button>
                    <input type="hidden" name="Latitude" id="Latitudex"
                        value="@if (isset($data['profile'])) {{ ltrim($data['profile']['latitude']) }} @endif"
                        required>
                    <input type="hidden" name="Longitude" id="Longitudex"
                        value="@if (isset($data['profile'])) {{ ltrim($data['profile']['longitude']) }} @endif"
                        required>
                </div>
            </div>
        </div>

        {{-- Vehicle --}}
        <div class="form-group local-forms mb-4">
            <label for="vehiclex">Vehicle <span class="login-danger">*</span></label>
            <select class="form-control" id="vehiclex" name="vehicle" required>
                <option></option>
                @foreach ($data['vehicles'] as $vehicle)
                    <option value="{{ $vehicle['id'] }}" @if (isset($data['profile']) && $data['profile']['vehicle_id'] == $vehicle['id']) selected @endif>
                        {{ $vehicle['name'] }}</option>
                @endforeach
            </select>
        </div>

        {{-- Shop --}}
        <div class="form-group local-forms mb-4">
            <label for="shopx">Shop <span class="login-danger">*</span></label>
            <select class="form-control" id="shopx" name="shop" required>
                <option></option>
                @foreach ($data['shops'] as $shop)
                    <option value="{{ $shop['id'] }}" @if (isset($data['profile']) && $data['profile']['distributor_shop_id'] == $shop['id']) selected @endif>
                        {{ $shop['distributor']['name'] . ' - ' . $shop['name'] }}</option>
                @endforeach
            </select>
        </div>

        {{-- Technician --}}
        <div class="form-group local-forms mb-4">
            <label for="technicianx">Technician</label>
            <select class="form-control" id="technicianx" name="technician">
                <option></option>
                <option disabled>Select a distributor and shop to select a technician</option>
            </select>
            @isset($data['profile'])
                <input type="hidden" id="technician_id" value="{{ $data['profile']['distributor_shop_technician_id'] }}">
            @endisset
        </div>

        <div class="form-group local-forms mb-4">
            <label for="payment-methodx">Payment Method <span class="login-danger">*</span></label>
            <select class="form-control" id="payment-methodx" name="paymentmethod" required>
                <option></option>
                @foreach ($data['payment_methods'] as $method)
                    <option value="{{ $method['id'] }}" @if (isset($data['profile']) && $data['profile']['payment_method_id'] == $method['id']) selected @endif>
                        {{ $method['name'] }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group local-forms mb-4">
            <label for="statusx">Status <span class="login-danger">*</span></label>
            <select name="status" id="statusx" class="form-control" required>
                <option value="paid" @if (isset($data['profile']) && $data['profile']['status'] == 'paid') selected @endif>Paid
                </option>
                <option value="pending" @if (isset($data['profile']) && $data['profile']['status'] == 'pending') selected @endif>
                    Pending</option>
                <option value="failed" @if (isset($data['profile']) && $data['profile']['status'] == 'failed') selected @endif>
                    Failed</option>
            </select>
        </div>

        {{-- Add Item --}}
        @if (!isset($data['profile']))
            <div class="mb-1" id="title">Add Item <button type="button" class="btn rounded-circle"
                    id="btn-add-detail-mobile"><span class="material-icons text-very-small">add</span></button></div>
        @endif

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
                <input type="hidden" name="subtotal" id="subtotalx"
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
                            <input type="hidden" name="discount"
                                @isset($data['profile'])
                            value="{{ $data['profile']['discount'] }}"
                        @else
                            value="0"
                        @endisset>
                            <input type="text" class="form-control" name="discountprice" id="discount-mobile"
                                @isset($data['profile'])
                                readonly
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
                                {{ formatPrice($data['profile']['total']) }}
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
                                <input type="text" id="taxdet" class="form-control">
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
        $('#customerx').select2({
            placeholder: "Select customer"
        });

        $('#shopx').select2({
            placeholder: "Select distributor and shop"
        });

        $('#technicianx').select2({
            placeholder: "Select shop technician"
        });

        $('#vehiclex').select2({
            placeholder: "Select customer vehicle"
        });

        $('#payment-methodx').select2({
            placeholder: "Select payment method"
        });

        $('#statusx').select2({
            placeholder: "Select payment method"
        });
    })
</script>

<script>
    $(function() {
        function loadTechnicianDataMobile() {
            let parentId = $("#shopx").val();

            if (parentId)
                $.ajax({
                    url: "/sales-order/technician/get/" + parentId,
                    method: "GET",
                    success: function(response) {
                        // Clear current options and value.
                        $("#technicianx").empty().val(null).trigger("change");

                        let emptyOption = new Option("", "", false, false);
                        $("#technicianx").append(emptyOption).trigger("change");

                        let mode = $("#btn-save").attr("value"); // update || create
                        response.forEach(function(menu) {
                            // Append new options.
                            let selected = false;
                            if (mode == 'update') {
                                // Get saved technician id.
                                let id = $("#technician_id").val();
                                if (menu.id == id) {
                                    selected = true;
                                }
                            }

                            let newOption = new Option(menu.name, menu.id, false,
                                selected);
                            $("#technicianx").append(newOption).trigger("change");
                        });
                    }
                });
        }

        $("#btn-add-detail-mobile").on("click", function() {
            // Clear all inputs value.
            $("#modal-detail input").val('');

            // Show modal.
            $("#modal-detail").modal("show");
        })

        $("#shopx").on("change", function() {
            // Get the list of menus inside the selected parent.
            loadTechnicianDataMobile();
        });

        $("#btn-save-detail-mobile").on("click", function() {
            var productName = $("#productname").val();
            var productionCode = $("#productioncode").val();
            var priceRetail = $("#priceretail").val();
            var tax = $("#taxdet").val();
            var taxPrice = $("#taxinprice").val();
            var discount = $("#detdiscount").val();
            var discountPrice = $("#detdiscountinprice").val();
            var priceNet = $("#pricenet").val();
            var id = $("#detid").val();

            var list = `
            <li class="list-group-item list-dash-border">
                <div class="row">
                    <div class="col-6">
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

                    <div class="col-2">
                        <button type="button" class="btn btn-danger btn-sm btn-delete-row"
                                                title="Delete Item"><i class="fas fa-xmark"></i></button>
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
            $("#subtotalx").val(subtotal);
            var discount = parseInt($("#discount-mobile").val());
            var total = subtotal - discount;
            $("#span-grand-total").html(formatNumberWithSeparator(total));
            $("#grandtotal").val(total);

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
            $("#grandtotal").val(total);
        });

        $("#sales-order-form-mobile").on("submit", function(event) {
            event.preventDefault();

            let mode = $("#btn-add-mobile").attr("value"); // update || create
            let url = (mode == "update") ? "/sales-order/update" : "/sales-order/store";

            let address = $("#AddressSearchColumnx").val();
            let lat = $("#Latitudex").val();
            let lon = $("#Longitudex").val();
            console.log(address, lat, lon);

            if (address == "" || lat == "" || lon == "") {
                Swal.fire({
                    title: "Error",
                    text: "Please select address",
                    icon: "error",
                });
                return;
            }
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

    $("#modal-detail").on('keyup', '#taxdet, #detdiscount', function() {
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
                                $("#taxdet").val(0);
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

    $(document).on('click', '.btn-delete-row', function() {
        $(this).closest('.list-group-item').remove();
        var subtotal = 0;
        $(".batteriespricemobile").each(function() {
            subtotal += parseInt($(this).val());
        });
        $("#span-subtotal").html(formatNumberWithSeparator(subtotal));
        $("#subtotal").val(subtotal);
        var discount = parseInt($("#discount-mobile").val());
        var total = subtotal - discount;
        $("#span-grand-total").html(formatNumberWithSeparator(total));
        $("#grandtotal").val(total);
    });
</script>

<script>
    function calculateDetailTotal() {
        var priceRetail = parseInt(removeSeparators($("#priceretail").val()));
        var tax = parseInt($("#taxdet").val());
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










{{-- INI MAP KABRUT (KACAU BRUTAL) --}}
{{-- NANTI DIUBAH KOK, I PROMISE... ;p --}}
<style>
    #MapsAddressFinderModalx {
        height: 400px;
        width: 100%;
        margin-bottom: 20px;
    }

    .pac-container {
        z-index: 10000 !important;
    }
</style>
<div class="modal fade" id="modalAddressFinderx" tabindex="-1" aria-labelledby="myLargeModalLabel"
    style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myLargeModalLabel">Address Finder</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <input type="text" class="form-control mb-1" placeholder="Search your address here..."
                    name="AddressSearchColumnModal" id="AddressSearchColumnModalx">
                <div id="MapsAddressFinderModalx"></div>
            </div>

            <div class="modal-footer">
                <button type="button" id="btnCloseModalAddresFinderx" class="btn btn-success"
                    data-bs-dismiss="modal">Save</button>
            </div>
        </div>
    </div>
</div>
<script>
    var map;
    var marker;

    function initMapx() {
        var existingLat = parseFloat(document.getElementById('Latitudex').value);
        var existingLng = parseFloat(document.getElementById('Longitudex').value);

        if (isNaN(existingLat) || isNaN(existingLng)) {
            existingLat = -6.8837859188198784;
            existingLng = 107.5403487263912;
        }

        map = new google.maps.Map(document.getElementById('MapsAddressFinderModalx'), {
            center: {
                lat: existingLat,
                lng: existingLng
            },
            zoom: 15
        });

        var input = document.getElementById('AddressSearchColumnModalx');
        var autocomplete = new google.maps.places.Autocomplete(input);
        autocomplete.bindTo('bounds', map);

        marker = new google.maps.Marker({
            map: map,
            draggable: true,
            position: {
                lat: existingLat,
                lng: existingLng
            },
            visible: true
        });

        var geocoder = new google.maps.Geocoder();
        geocoder.geocode({
            'location': {
                lat: existingLat,
                lng: existingLng
            }
        }, function(results, status) {
            if (status === 'OK' && results[0]) {
                var address = results[0].formatted_address;
                document.getElementById('AddressSearchColumnModalx').value = address;
                document.getElementById('AddressSearchColumnx').value = address;
            } else {
                console.error('Geocoder failed due to: ' + status);
            }
        });

        autocomplete.addListener('place_changed', function() {
            var place = autocomplete.getPlace();
            if (!place.geometry) {
                console.error("Place details not found");
                return;
            }

            if (place.geometry.viewport) {
                map.fitBounds(place.geometry.viewport);
            } else {
                map.setCenter(place.geometry.location);
                map.setZoom(17);
            }

            marker.setPosition(place.geometry.location);
            marker.setVisible(true);


            var address = place.formatted_address;
            var latitude = place.geometry.location.lat();
            var longitude = place.geometry.location.lng();

            document.getElementById('AddressSearchColumnModalx').value = address;
            document.getElementById('AddressSearchColumnx').value = address;
            document.getElementById('Latitudex').value = latitude;
            document.getElementById('Longitudex').value = longitude;
        });


        google.maps.event.addListener(marker, 'dragend', function() {
            var position = marker.getPosition();
            map.panTo(position);

            var geocoder = new google.maps.Geocoder();
            geocoder.geocode({
                'location': position
            }, function(results, status) {
                if (status === 'OK') {
                    if (results[0]) {
                        var address = results[0].formatted_address;
                        var latitude = position.lat();
                        var longitude = position.lng();

                        document.getElementById('AddressSearchColumnModalx').value = address;
                        document.getElementById('AddressSearchColumnx').value = address;
                        document.getElementById('Latitudex').value = latitude;
                        document.getElementById('Longitudex').value = longitude;
                    }
                } else {
                    console.error('Geocoder failed due to: ' + status);
                }
            });
        });
    }

    function openAddressModalx() {
        $('#modalAddressFinderx').modal('show');
        setTimeout(function() {
            $("#AddressSearchColumnModalx").focus();
        }, 3000);
    }

    $("#AddressSearchColumnx").on("click", function() {
        openAddressModalx();
    });

    $("#btnAddressx").on("click", function() {
        openAddressModalx();
    });

    $(function() {
        initMapx();
    });
</script>
