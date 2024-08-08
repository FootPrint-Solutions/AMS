<div>
    <div class="container my-5">
        <div class="row">
            <div class="col-12">
                <!-- Invoice Header -->
                <div class="d-flex justify-content-between align-items-center bg-dark-blue-only text-white p-3 rounded">
                    {{-- button rounded with icon invoice --}}
                    <button class="btn rounded-pill text-light" style="background-color:#60D3AA;">
                        <i class="fas fa-file-invoice"></i>
                    </button>
                    {{-- <h5 class="mb-0">INVOICE</h5> --}}
                    <span>Invoice Number : <span id="invoice_number_payment_details_mobile"></span>
                </div>

                <!-- Billed To Section -->
                <div class="card mt-3 bg-grey-custom">
                    <div class="card-body">
                        <h5 class="card-title">Billed to</h5>
                        <p class="mb-0" id="full_name_customer_payment_details_mobile"></p>
                        <p class="mb-0" id="number_customer_payment_details_mobile"></p>
                        <p class="mb-0" id="email_customer_payment_details_mobile"></p>
                        <p class="mb-0" id="address_customer_payment_details_mobile"></p>
                    </div>
                </div>

                <!-- Products Section -->
                <div class="mt-3" id="battery-payment-details-mobile">

                </div>

                <!-- Grandtotal Section -->
                <div class="d-flex justify-content-between align-items-center bg-light p-3 mt-3 rounded">
                    <h5 class="mb-0">Grandtotal :</h5>
                    <h5 class="mb-0" id="grand_total_payment_details_mobile"></h5>
                </div>
            </div>
        </div>

        <div class="card bg-stabilo mt-3" style="background-color: #BCEBEC;">
            <div class="card-body">
                <div class="row">
                    <div class="col-2">
                        <button class="btn rounded-pill text-light" style="background-color:#60D3AA;">
                            <i class="fas fa-dollar-sign"></i>
                        </button>
                    </div>
                    <div class="col-6" style="margin-top: 5px;">
                        Payment Method
                    </div>
                    <div class="col">
                        <button class="btn rounded-pill text-light" style="background-color:#1FBABF;"
                            id="btn-select-payment-method">
                            Select
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" id="payment_gateway_payment_details_mobile" name="payment_gateway_payment_details_mobile">

    <div class="bottom-buttons pager wizard twitter-bs-wizard-pager-link">
        {{-- share button --}}
        <button class="btn btn-custom btn-whatsapp" id="btn-share-whatsapp-step-3-mobile">
            <i class="fa-brands fa-whatsapp"></i>
            Share
        </button>
        {{-- next button --}}
        <button id="payment-details-mobile-save-button-lower" class="btn btn-custom btn-next next"
            href="javascript: void(0);" style="background-color:#0B759D;">Save
            <i class="fa fa-save"></i>
        </button>
    </div>
</div>


<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentModalLabel">Payment Method</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="payment-method-payment-details-mobile">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="btn-select-payment-method-mobile">Select</button>
            </div>
        </div>
    </div>
</div>


<script>
    $("#btn-select-payment-method").click(function() {
        // show modal payment method
        $("#paymentModal").modal("show");
    });

    $("#btn-select-payment-method-mobile").click(function() {
        // get selected payment method
        var paymentMethod = $("input[name='paymentMethod']:checked").val();

        // hide modal payment method
        $("#paymentModal").modal("hide");
    });

    // payment-method click check the radio button
    $(".payment-method").click(function() {
        // this data id
        var id = $(this).data("id");
        // set radio button checked
        $("#payment_gateway_payment_details_mobile_" + id).prop("checked", true);
    });

    function selectPaymentMethodMobile(x) {
        // get data-id 
        var id = $(x).data("id");

        // set radio button checked
        $("#payment_gateway_payment_details_mobile_" + id).prop("checked", true);
        $("#payment_gateway_payment_details_mobile").val(id);
    }

    $("#payment-details-mobile-save-button-lower").click(function() {
        var button = $(this);
        button.prop('disabled', true);
        button.html(
            '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...'
        );

        var FullName = $("#full_name_input_mobile").val();
        var EmailCustomer = $("#email_customer_input_mobile").val();
        var ContactNumber = $("#contact_input_mobile").val();
        var AddressCustomer = $('#address_input_mobile').val();
        var VehicleCustomer = $("#vehicle_customer_input_mobile").val();
        var Latitude = $("#latitude_input_mobile").val();
        var Longitude = $("#longitude_input_mobile").val();
        var IdCustomer = $("#IdCustomer").val();
        var Battery = [];
        $("input[name='battery_id_checkout_mobile[]']").each(function() {
            Battery.push($(this).val());
        });
        var TotalAmount = $("#total_amount_hidden_checkout_mobile").val();
        var tax = $("#tax").val();
        var Discount = $("#discount").val();
        var ExtraDiscount = $("#Extradiscount").val();
        var invoiceNumber = $("#invoice_number_payment_details_mobile").text();
        var techniciansName = $("#technicians_checkout_mobile").val();
        var subtotal = $("#subtotal_hidden_checkout_mobile").val();
        if ($('.CheckMidtrans').is(':checked')) {
            var CheckMidtrans = 1;
            var linkPayment = $("#LinkPaymentMidtrans").val();
        } else {
            var CheckMidtrans = 0;
        }
        var PaymentMethod = $("#payment_gateway_payment_details_mobile").val();
        var DistributorShopId = $('#distributor_input_mobile').val();
        var DiscountRupiah = $("#discount-rupiah").val();
        var DiscountPercentage = $("#discount-percent").val();
        var typeDiscount = $("#type-discount").val();

        var QtyTabel = [];
        var PriceTabel = [];
        var BatteryNameTabel = [];
        var LinkTokopedia = [];
        var QtyPayment = [];
        var GrossPricePayment = [];
        var DiscountPayment = [];
        var NetPricePayment = [];
        var SubtotalPayment = [];
        var TaxPayment = [];
        var TaxPricePayment = [];
        var BatteryIdCheckout = [];

        $("input[name='qty_checkout_mobile[]']").each(function() {
            QtyTabel.push($(this).val()); // Menambahkan kuantitas ke dalam array
        });

        $("input[name='subtotal_checkout_mobile[]']").each(function() {
            PriceTabel.push($(this).val()); // Menambahkan harga ke dalam array
        });

        $("input[name='battery_name_checkout_mobile[]']").each(function() {
            BatteryNameTabel.push($(this).val()); // Menambahkan nama ke dalam array
        });

        $(".LinkTokopedia").each(function() {
            var value = $(this).val();
            LinkTokopedia.push(value);
        });

        $(".QtyPaymentDetails").each(function() {
            var value = $(this).val();
            QtyPayment.push(value);
        });

        $(".PricePaymentDetails").each(function() {
            var value = $(this).val();
            GrossPricePayment.push(value);
        });

        $(".DiscountPaymentDetails").each(function() {
            var value = $(this).val();
            DiscountPayment.push(value);
        });

        $(".NetPricePaymentDetails").each(function() {
            var value = $(this).val();
            NetPricePayment.push(value);
        });

        $(".SubtotalPaymentDetails").each(function() {
            var value = $(this).val();
            SubtotalPayment.push(value);
        });

        $(".TaxPaymentDetails").each(function() {
            var value = $(this).val();
            TaxPayment.push(value);
        });

        $(".PriceTaxPaymentDetails").each(function() {
            var value = $(this).val();
            TaxPricePayment.push(value);
        });

        $(".BatteryIdCheckout").each(function() {
            var value = $(this).val();
            BatteryIdCheckout.push(value);
        });

        // if payment method is null then show alert
        if (PaymentMethod == null) {
            Swal.fire({
                title: "Error",
                text: "Please select payment method",
                icon: "error",
            });
            button.prop('disabled', false);
            button.html(
                "Save Changes"
            );
            return;
        }



        var data = {
            FullName: FullName,
            EmailCustomer: EmailCustomer,
            ContactNumber: ContactNumber,
            AddressCustomer: AddressCustomer,
            VehicleCustomer: VehicleCustomer,
            Latitude: Latitude,
            Longitude: Longitude,
            IdCustomer: IdCustomer,
            Battery: Battery,
            TotalAmount: TotalAmount,
            tax: tax,
            Discount: Discount,
            ExtraDiscount: ExtraDiscount,
            BatteryNameTabel: BatteryNameTabel,
            QtyTabel: QtyTabel,
            PriceTabel: PriceTabel,
            Discount: Discount,
            ExtraDiscount: ExtraDiscount,
            _token: $('meta[name="csrf-token"]').attr('content'),
            DistributorShopId: DistributorShopId,
            invoiceNumber: invoiceNumber,
            techniciansName: techniciansName,
            CheckMidtrans: CheckMidtrans,
            linkPayment: LinkTokopedia,
            linkMidtrans: linkPayment,
            subtotal: subtotal,
            DiscountRupiah: DiscountRupiah,
            DiscountPercentage: DiscountPercentage,
            QtyPayment: QtyPayment,
            GrossPricePayment: GrossPricePayment,
            DiscountPayment: DiscountPayment,
            NetPricePayment: NetPricePayment,
            SubtotalPayment: SubtotalPayment,
            PaymentMethod: PaymentMethod,
            TaxPayment: TaxPayment,
            TaxPricePayment: TaxPricePayment,
            BatteryIdCheckout: BatteryIdCheckout,
            typeDiscount: typeDiscount
        };

        $.ajax({
            url: "/quotation/mobile/save-data",
            type: "POST",
            data: data,
            success: function(data) {
                if (data.success == true) {
                    Swal.fire({
                        title: "Success",
                        text: data.message,
                        icon: "success",
                    });
                    setTimeout(function() {
                        window.location.href = "/sales-order";
                    }, 2000);
                } else {
                    Swal.fire({
                        title: "Error",
                        text: data.message ||
                            "Something went wrong, please try again later",
                        icon: "error",
                    });
                }
            },
            complete: function() {
                button.prop('disabled', false);
                button.html(
                    "Save Changes"
                );
            }
        });

    });
</script>
