<div>
    <div class="mb-4">
        <h5>Customer Details</h5>
    </div>
    <div class="card bg-grey mt-3">
        <div class="card-body">
            <div class="container">
                <h5 id="full_name_customer_checkout_mobile"></h5>
                <h5 id="address_customer_checkout_mobile"></h5>
                <h5><span id="email_customer_checkout_mobile"></span>, <span id="number_customer_checkout_mobile"></span>
                </h5>
            </div>
        </div>
    </div>
    <div>
        <div class="mb-4">
            <h5>Vehicle Customer</h5>
        </div>
        <div class="card bg-grey mt-3">
            <div class="card-body">
                <div class="container">
                    <h5 id="vehicle_customer_checkout_mobile"></h5>
                </div>
            </div>
        </div>
    </div>
    <div>
        <div class="mb-4">
            <h5>Item Details</h5>
        </div>
    </div>
    <div class="card bg-grey mt-3">
        <div class="card-body">
            <div class="battery_customer_checkout_mobile" id="battery_customer_checkout_mobile">
                <div class="btn-add-more mt-3">
                    <span>Tambah</span>
                </div>
            </div>
        </div>
    </div>
    <div class="card bg-stabilo mt-3">
        <div class="card-body">
            <div class="">
                <h4>Partner Detail</h4>
                <div class="row">
                    <div class="col">
                        <h6>
                            Distributor Shop Name
                        </h6>
                    </div>
                    <div class="col">
                        <h6 id="distributor_shop_name_checkout_mobile">

                        </h6>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <h6>
                            Address Distributor / Shop
                        </h6>
                    </div>
                    <div class="col">
                        <h6 id="address_distributor_checkout_mobile">
                        </h6>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <h6>
                            Technicians
                        </h6>
                    </div>
                    <div class="col">
                        <select name="technicians_checkout_mobile" id="technicians_checkout_mobile">
                            <option value="">Select Technicians</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <h6>
                            Mechanic Phone
                        </h6>
                    </div>
                    <div class="col">
                        <h6 id="mechanic_phone_checkout_mobile">

                        </h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card bg-stabilo mt-3">
        <div class="card-body">
            <div class="">
                <h4>Summary</h4>
                <div class="row">
                    <div class="col">
                        <h6>
                            Subtotal
                        </h6>
                    </div>
                    <div class="col">
                        <input type="hidden" name="subtotal_hidden_checkout_mobile"
                            id="subtotal_hidden_checkout_mobile">
                        <h6 id="subtotal_checkout_mobile">

                        </h6>
                    </div>
                </div>
                {{-- dot white hr --}}
                <div class="dot-white-hr"></div>
                <div class="row">
                    <div class="col">
                        <h5>Grand Total</h5>
                    </div>
                    <div class="col">
                        <h5 id="total_amount_checkout_mobile"></h5>
                        <input type="hidden" name="total_amount_hidden_checkout_mobile"
                            id="total_amount_hidden_checkout_mobile">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="bottom-buttons pager wizard twitter-bs-wizard-pager-link">
        {{-- copy button --}}
        <button class="btn btn-custom btn-whatsapp" id="btn-copy-text-step-3-mobile">
            <i class="fa fa-copy fa-md"></i>
            Copy
        </button>
        {{-- share button --}}
        <button class="btn btn-custom btn-whatsapp" id="btn-share-whatsapp-step-3-mobile">
            <i class="fa-brands fa-whatsapp"></i>
            Share
        </button>
        {{-- next button --}}
        <button id="checkout-mobile-next-button-lower" class="btn btn-custom btn-next next"
            href="javascript: void(0);">Next
            <i class="fa fa-chevron-right"></i>
        </button>
    </div>
</div>


{{-- battery detail modal --}}
<div class="modal fade" id="battery_detail_checkout_mobile" tabindex="-1"
    aria-labelledby="battery_detail_checkout_mobile" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="battery_detail_checkout_mobile">Battery Detail</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div class="row">
                    <div class="col">
                        <img src="" alt="" id="image_battery_checkout_mobile"
                            style="width: 100px; height: 100px;">
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        Battery
                    </div>
                    <div class="col">
                        :
                    </div>
                    <div class="col" id="name_battery_detail_mobile_checkout">

                    </div>
                </div>

                {{-- qty  --}}
                <div class="row">
                    <div class="col">
                        Qty
                    </div>
                    <div class="col">
                        :
                    </div>
                    <div class="col">
                        <input type="number" name="qty_battery_detail_input_mobile_checkout"
                            id="qty_battery_detail_input_mobile_checkout" value="1" class="form-control">
                    </div>
                </div>

                {{-- Gross Price --}}
                <div class="row">
                    <div class="col">
                        Gross Price
                    </div>
                    <div class="col">
                        :
                    </div>
                    <div class="col">
                        <span id="price_battery_detail_mobile_checkout"></span>
                        <input type="hidden" name="price_battery_detail_input_mobile_checkout"
                            id="price_battery_detail_input_mobile_checkout">
                    </div>
                </div>

                {{-- Tax --}}
                <div class="row">
                    <div class="col">
                        Tax
                    </div>
                    <div class="col">
                        :
                    </div>
                    <div class="col">
                        <input type="text" name="tax_battery_detail_input_mobile_checkout"
                            id="tax_battery_detail_input_mobile_checkout" value="" readonly
                            class="form-control">
                    </div>
                </div>

                {{-- Price + Tax  --}}
                <div class="row">
                    <div class="col">
                        Price + Tax
                    </div>
                    <div class="col">
                        :
                    </div>
                    <div class="col">
                        <input type="text" name="battery_price_tax_detail_input_mobile_checkout"
                            id="battery_price_tax_detail_input_mobile_checkout" value="0" readonly
                            class="form-control">
                    </div>
                </div>

                {{-- discount --}}
                <div class="row">
                    <div class="col">
                        Diskon (Rp)
                    </div>
                    <div class="col">
                        :
                    </div>
                    <div class="col">
                        <input type="number" name="discount_battery_detail_input_mobile_checkout"
                            id="discount_battery_detail_input_mobile_checkout" value="0" class="form-control">
                    </div>
                </div>

                {{-- Net Price --}}
                <div class="row">
                    <div class="col">
                        Net Price
                    </div>
                    <div class="col">
                        :
                    </div>
                    <div class="col">
                        <input type="text" name="net_price_battery_detail_input_mobile_checkout"
                            id="net_price_battery_detail_input_mobile_checkout" value="0" readonly
                            class="form-control">
                    </div>
                </div>

                {{-- subtotal  --}}
                <div class="row">
                    <div class="col">
                        Subtotal
                    </div>
                    <div class="col">
                        :
                    </div>
                    <div class="col">
                        <input type="text" name="subtotal_battery_detail_input_mobile_checkout"
                            id="subtotal_battery_detail_input_mobile_checkout" value="0" readonly
                            class="form-control">
                    </div>
                </div>

                {{-- battery id  --}}
                <input type="hidden" name="battery_id_detail_checkout_mobile"
                    id="battery_id_detail_checkout_mobile">

                {{-- unit price --}}
                <input type="hidden" name="unit_price_detail_checkout_mobile" id="unit_price_detail_checkout_mobile"
                    value="0">
            </div>
        </div>
    </div>
</div>

<script>
    // when technicians_checkout_mobile is changed get data-phone from the selected option set it to mechanic_phone_checkout_mobile
    $("#technicians_checkout_mobile").change(function() {
        var phone = $(this).find(':selected').data('phone');
        $("#mechanic_phone_checkout_mobile").text(phone);
    });

    $("#btn-copy-text-step-3-mobile").on("click", function() {
        var FullName = $("#full_name_input_mobile").val();
        var ContactNumber = $("#contact_input_mobile").val();
        var Battery = [];
        $(".item-detail.d-flex.align-items-center").each(function() {
            var batteryName = $(this).find("input[name='battery_name_checkout_mobile[]']").val();
            var quantity = $(this).find("input[name='qty_checkout_mobile[]']").val();
            var price = $(this).find("input[name='subtotal_checkout_mobile[]']").val();
            Battery.push({
                batteryName: batteryName,
                quantity: quantity,
                price: price
            });
        });
        var subtotal = $("#subtotal_hidden_checkout_mobile").val();
        var tax = $("#tax").val();
        var discount = $("#discount").val();
        var TotalAmountHidden = $("#total_amount_hidden_checkout_mobile").val();
        var VehicleCustomer = $("#vehicle_customer_input_mobile").val();
        var Latitude = $('#latitude_input_mobile').val();
        var Longitude = $('#longitude_input_mobile').val();
        var AddressCustomer = $("#address_input_mobile").val();
        var typeDiscount = $("#type-discount").val();

        var data = {
            FullName: FullName,
            ContactNumber: ContactNumber,
            Battery: Battery,
            Subtotal: subtotal,
            Tax: tax,
            Discount: discount,
            TotalAmount: TotalAmountHidden,
            VehicleCustomer: VehicleCustomer,
            Latitude: Latitude,
            Longitude: Longitude,
            AddressCustomer: AddressCustomer,
            typeDiscount: typeDiscount,
            _token: $('meta[name="csrf-token"]').attr('content')
        };

        $.ajax({
            url: "/quotation/checkout/copy",
            type: "POST",
            data: data,
            success: function(response) {
                let ResponseData = JSON.parse(response);
                if (ResponseData.status == true) {
                    var copyText = ResponseData.message;
                    var textArea = document.createElement("textarea");
                    textArea.value = copyText;
                    document.body.appendChild(textArea);
                    textArea.select();
                    document.execCommand('copy');
                    document.body.removeChild(textArea);
                    swal.fire("Copied!", "Personal Details Copied", "success");
                } else {
                    swal.fire("Error!", ResponseData.message, "error");
                }
            },
            error: function(xhr, status, error) {
                swal.fire("Error!", error, "error");
            }
        });
    });

    function min_qty_checkout(x) {
        var id = $(x).data('id');
        var qty = $("#qty_checkout_mobile_" + id).val();
        if (qty > 1) {
            qty--;

            var SubtotalBattery = $("#price_net_checkout_mobile_" + id).val() * qty;
            $("#subtotal_checkout_mobile_" + id).val(SubtotalBattery);
            $("#qty_" + id).text(qty);
            $("#qty_checkout_mobile_" + id).val(qty);

            calculateSubtotal();
        } else {
            swal.fire({
                title: 'Error',
                text: 'Quantity cannot be less than 1',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        }
    }

    function add_qty_checkout(x) {
        var id = $(x).data('id');
        var qty = $("#qty_checkout_mobile_" + id).val();
        qty++;

        var SubtotalBattery = $("#price_net_checkout_mobile_" + id).val() * qty;
        $("#subtotal_checkout_mobile_" + id).val(SubtotalBattery);
        $("#qty_" + id).text(qty);
        $("#qty_checkout_mobile_" + id).val(qty);

        calculateSubtotal();
    }

    function calculateSubtotal() {
        var subtotal = 0;
        $(".subtotal_checkout_mobile").each(function() {
            subtotal += parseInt($(this).val());
        });
        $("#subtotal_hidden_checkout_mobile").val(subtotal);
        formatSubtotal = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR'
        }).format(subtotal);
        $("#subtotal_checkout_mobile").text(formatSubtotal);

        $("#total_amount_hidden_checkout_mobile").val(subtotal);
        formatTotal = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR'
        }).format(subtotal);
        $("#total_amount_checkout_mobile").text(formatTotal);
    }

    function btn_detail_battery_checkout(x) {
        var id = $(x).data('id');

        // get qty and set to qty detail
        var qty = $("#qty_checkout_mobile_" + id).val();
        $("#qty_battery_detail_input_mobile_checkout").val(qty);

        // get discount_checkout_mobile and set to discount detail
        var discount = $("#discount_checkout_mobile_" + id).val();
        $("#discount_battery_detail_input_mobile_checkout").val(discount);

        // get unit_price_checkout_mobile and set to unit_price detail
        var unitPrice = $("#unit_price_checkout_mobile_" + id).val();
        $("#unit_price_detail_checkout_mobile").val(unitPrice);


        // send ajax to get detail battery 
        $.ajax({
            url: "/quotation/mobile/detail/battery",
            type: "POST",
            data: {
                id: id,
                _token: "{{ csrf_token() }}"
            },
            success: function(response) {
                // show modal 
                $("#battery_detail_checkout_mobile").modal('show');
                res = response.data[0];
                var baseUrl = "{{ asset('storage/image/battery/') }}";
                res.image = baseUrl + '/' + res.image;


                $("#image_battery_checkout_mobile").attr('src', res.image);
                $("#name_battery_detail_mobile_checkout").text(res.name);
                formatPriceRetail = new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR'
                }).format(res.price_retail);
                $("#price_battery_detail_mobile_checkout").text(formatPriceRetail);
                $("#price_battery_detail_input_mobile_checkout").val(res.price_retail);
                $("#tax_battery_detail_input_mobile_checkout").val(response.tax);


                var pricetax = res.price_retail + (res.price_retail * response.tax / 100);
                formatPriceTax = new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR'
                }).format(pricetax);
                $("#battery_price_tax_detail_input_mobile_checkout").val(formatPriceTax);
                $("#discount_battery_detail_input_mobile_checkout").val(res.battery_prices[0]
                    .discount_price);


                var netPrice = res.price_retail - res.battery_prices[0].discount_price;
                var formatNetPrice = new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR'
                }).format(netPrice);
                $("#net_price_battery_detail_input_mobile_checkout").val(formatNetPrice);


                $("#battery_id_detail_checkout_mobile").val(res
                    .id);
                calculateSubtotalBatteryDetail();
            }
        });
    }

    function calculateSubtotalBatteryDetail() {
        var price = $("#price_battery_detail_input_mobile_checkout").val();
        var qty = $("#qty_battery_detail_input_mobile_checkout").val();
        var tax = $("#tax_battery_detail_input_mobile_checkout").val();
        var discount = $("#discount_battery_detail_input_mobile_checkout").val();
        var unitPrice = $("#unit_price_detail_checkout_mobile").val();

        var unitPriceNet = unitPrice - discount;

        var subtotal = (price * qty) + (price * qty * tax / 100);
        subtotal = subtotal - discount;
        $("#subtotal_battery_detail_input_mobile_checkout").val(subtotal);

        formatSubtotal = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR'
        }).format(subtotal);
        $("#subtotal_battery_detail_input_mobile_checkout").val(formatSubtotal);
        $("#net_price_battery_detail_input_mobile_checkout").val(formatSubtotal);

        // set subtotal_checkout_mobile_
        $("#subtotal_checkout_mobile_" + $("#battery_id_detail_checkout_mobile").val()).val(subtotal);
        // set price_net_checkout_mobile_
        $("#price_net_checkout_mobile_" + $("#battery_id_detail_checkout_mobile").val()).val(unitPriceNet);
        // set discount_checkout_mobile_
        $("#discount_checkout_mobile_" + $("#battery_id_detail_checkout_mobile").val()).val(discount);
        // set qty_checkout_mobile_
        $("#qty_checkout_mobile_" + $("#battery_id_detail_checkout_mobile").val()).val(qty);
        $("#qty_" + $("#battery_id_detail_checkout_mobile").val()).text(qty);
        // set unit_price_checkout_mobile_
        $("#unit_price_checkout_mobile_" + $("#battery_id_detail_checkout_mobile").val()).val(unitPriceNet);
        calculateSubtotal();
    }

    // when qty_battery_detail_input_mobile_checkout, tax_battery_detail_input_mobile_checkout, discount_battery_detail_input_mobile_checkout is changed calculateSubtotalBatteryDetail
    $("#qty_battery_detail_input_mobile_checkout, #tax_battery_detail_input_mobile_checkout, #discount_battery_detail_input_mobile_checkout")
        .on("keyup", function() {
            calculateSubtotalBatteryDetail();
        });

    $("#btn-share-whatsapp-step-3-mobile").on('click', function() {
        var button = $(this);
        button.prop('disabled', true);
        button.html(
            '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...'
        );

        var FullName = $("#full_name_input_mobile").val();
        var ContactNumber = $("#contact_input_mobile").val();
        var VehicleCustomer = $("#vehicle_customer_input_mobile").val();
        var Latitude = $("#latitude_input_mobile").val();
        var Longitude = $("#longitude_input_mobile").val();
        var AddressCustomer = $('#address_input_mobile').val();

        var Battery = [];
        $(".item-detail.d-flex.align-items-center").each(function() {
            var batteryName = $(this).find("input[name='battery_name_checkout_mobile[]']").val();
            var quantity = $(this).find("input[name='qty_checkout_mobile[]']").val();
            var price = $(this).find("input[name='subtotal_checkout_mobile[]']").val();
            Battery.push({
                batteryName: batteryName,
                quantity: quantity,
                price: price
            });
        });

        var subtotal = $("#subtotal_hidden_checkout_mobile").val();
        var tax = $("#tax").val();
        var discount = $("#discount").val();
        var TotalAmountHidden = $("#total_amount_hidden_checkout_mobile").val();
        var typeDiscount = $("#type-discount").val();

        var data = {
            FullName: FullName,
            Battery: Battery,
            Subtotal: subtotal,
            Tax: tax,
            Discount: discount,
            TotalAmount: TotalAmountHidden,
            ContactNumber: ContactNumber,
            VehicleCustomer: VehicleCustomer,
            Latitude: Latitude,
            Longitude: Longitude,
            AddressCustomer: AddressCustomer,
            typeDiscount: typeDiscount,
            _token: $('meta[name="csrf-token"]').attr('content')
        };

        if (Battery.length === 0) { // Mengubah pengecekan Battery menjadi Battery.length
            swal.fire("Error!", "Please select battery", "error");
            button.prop('disabled', false);
            button.html("<i class='fa-brands fa-whatsapp'></i> Share");
            return;
        }


        $.ajax({
            url: "/quotation/share-invoice",
            type: "POST",
            data: data,
            success: function(data) {
                let ResponseData = JSON.parse(data);
                if (ResponseData.status) {
                    Swal.fire({
                        title: "Success",
                        text: ResponseData.message,
                        icon: "success",
                    });
                } else {
                    Swal.fire({
                        title: "Error",
                        text: ResponseData.message ||
                            "Something went wrong, please try again later",
                        icon: "error",
                    });
                }
            },
            complete: function() {
                button.prop('disabled', false);
                button.html("<i class='fa-brands fa-whatsapp'></i> Share");
            }
        });
    });

    $("#checkout-mobile-next-button-lower").click(function() {
        var FullName = $("#full_name_input_mobile").val();
        var ContactNumber = $("#contact_input_mobile").val();
        var EmailCustomer = $("#email_input_mobile").val();
        var VehicleCustomer = $("#vehicle_customer_input_mobile").val();
        var Latitude = $("#latitude_input_mobile").val();
        var Longitude = $("#longitude_input_mobile").val();
        var AddressCustomer = $('#address_input_mobile').val();
        var Battery = [];
        var QtyTabel = []; // Menambahkan array untuk menyimpan kuantitas
        var PriceTabel = []; // Menambahkan array untuk menyimpan harga

        // get all battery_id_checkout_mobile[] value
        $("input[name='battery_id_checkout_mobile[]']").each(function() {
            Battery.push($(this).val());
        });

        // get all qty_checkout_mobile[] value
        $("input[name='qty_checkout_mobile[]']").each(function() {
            QtyTabel.push($(this).val()); // Menambahkan kuantitas ke dalam array
        });

        // get all subtotal_checkout_mobile[] value
        $("input[name='subtotal_checkout_mobile[]']").each(function() {
            PriceTabel.push($(this).val()); // Menambahkan harga ke dalam array
        });

        var subtotal = $("#subtotal_hidden_checkout_mobile").val();
        var tax = $("#tax").val();
        var discount = $("#discount").val();
        var TotalAmountHidden = $("#total_amount_hidden_checkout_mobile").val();
        var typeDiscount = $("#type-discount").val();

        var data = {
            FullName: FullName,
            Battery: Battery,
            Qty: QtyTabel, // Menambahkan QtyTabel ke dalam data
            Price: PriceTabel, // Menambahkan PriceTabel ke dalam data
            Subtotal: subtotal,
            Tax: tax,
            Discount: discount,
            TotalAmount: TotalAmountHidden,
            ContactNumber: ContactNumber,
            VehicleCustomer: VehicleCustomer,
            Latitude: Latitude,
            Longitude: Longitude,
            AddressCustomer: AddressCustomer,
            typeDiscount: typeDiscount,
            EmailCustomer: EmailCustomer,
            _token: $('meta[name="csrf-token"]').attr('content')
        };

        if (Battery.length === 0) { // Mengubah pengecekan Battery menjadi Battery.length
            swal.fire("Error!", "Please select battery", "error");
            return;
        }

        $.ajax({
            url: "/quotation/mobile/payment",
            type: "POST",
            data: data,
            success: function(response) {
                if (response.success == true) {
                    $("#personal-details-mobile-li").removeClass("active");
                    $("#personal-details-mobile-tab").css("display", "none");
                    $("#product-recommendation-mobile-li").removeClass("active");
                    $("#product-recommendation-mobile-tab").css("display", "none");
                    $("#checkout-page-mobile-li").removeClass("active");
                    $("#checkout-page-mobile-tab").css("display", "none");
                    $("#payment-detail-mobile-li").addClass("active");
                    $("#payment-detail-mobile-tab").css("display", "block");

                    // set data to payment details 
                    $("#full_name_customer_payment_details_mobile").text(FullName);
                    $("#number_customer_payment_details_mobile").text(ContactNumber);
                    $("#email_customer_payment_details_mobile").text(EmailCustomer);
                    $("#address_customer_payment_details_mobile").text(AddressCustomer);
                    formatTotalAmount = new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR'
                    }).format(TotalAmountHidden);
                    $("#grand_total_payment_details_mobile").text(formatTotalAmount);
                    $("#invoice_number_payment_details_mobile").text(response.data
                        .InvoiceNumber);

                    // loop battery and set to battery-payment-details-mobile
                    var batteryHtml = "";
                    response.data.Battery.forEach(function(battery) {
                        var qty = response.data.Qty.shift();
                        var price = response.data.Price.shift();

                        baseUrl = "{{ asset('storage/image/battery/') }}";
                        battery.image = baseUrl + '/' + battery.image;

                        batteryHtml += `
                            <div class="d-flex justify-content-between align-items-center p-2 border-bottom">
                                <div class="d-flex align-items-center">
                                    <img src="${battery.image}" alt="Product Image" class="img-fluid" style="width: 70px; margin-left: 10px;">
                                    <div class="ml-3">
                                        <h6 class="mb-1">${battery.name}</h6>
                                        <p class="mb-0">${new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(price)}</p>
                                    </div>
                                </div>
                                <span>X${qty}</span>
                            </div>
                        `;
                    });
                    $("#battery-payment-details-mobile").html(batteryHtml);

                    // loop payment method and set to payment-method-payment-details-mobile
                    var paymentMethodHtml = "";
                    response.data.PaymentMethod.forEach(function(paymentMethod) {
                        var uniqueId = 'payment_gateway_payment_details_mobile_' +
                            paymentMethod.id;
                        paymentMethodHtml +=
                            '<div class="payment-method" data-id="' + uniqueId + '">';
                        paymentMethodHtml +=
                            '<input type="radio" name="paymentMethod" id="' + uniqueId +
                            '" value="' + paymentMethod.id + '" data-id="' +
                            paymentMethod
                            .id + '" onclick="selectPaymentMethodMobile(this)">';
                        paymentMethodHtml +=
                            '<label for="' + uniqueId + '">' + paymentMethod.name +
                            '</label>';
                        paymentMethodHtml += '</div>';
                    });
                    $("#payment-method-payment-details-mobile").html(paymentMethodHtml);

                } else {
                    Swal.fire({
                        title: "Error",
                        text: response.message ||
                            "Something went wrong, please try again later",
                        icon: "error",
                    });
                }
            }
        });
    });
</script>
