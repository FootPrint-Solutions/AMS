@extends('template.master')

@section('content')
    {{-- Custom CSS  MOBILE RESPONSIVE --}}
    <link rel="stylesheet" href="{{ asset('/css/quick-quotation.css') }}">
    {{-- Title --}}


    {{-- DESKTOP VERSION --}}
    <div class="d-none d-lg-block">
        <div class="row">

            <!-- Lightbox -->
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Quick Quotation</h4>
                    </div>
                    <div class="card-body">
                        <div id="basic-pills-wizard" class="twitter-bs-wizard">
                            <ul class="twitter-bs-wizard-nav">
                                <li class="nav-item active">
                                    <a href="#seller-details" class="nav-link" data-toggle="tab">
                                        <div class="step-icon" data-bs-toggle="tooltip" data-bs-placement="top"
                                            title="Personal Detail">
                                            <i class="far fa-user"></i>
                                        </div>
                                    </a>
                                </li>
                                <li class="nav-item" id="ProductDisplay">
                                    <a href="#product-display" class="nav-link" data-toggle="tab">
                                        <div class="step-icon" data-bs-toggle="tooltip" data-bs-placement="top"
                                            title="Product Recomendation Display">
                                            <i class="fa-solid fa-boxes-stacked"></i>
                                        </div>
                                    </a>
                                </li>
                                <li class="nav-item" id="CheckoutDisplay">
                                    <a href="#company-document" class="nav-link" data-toggle="tab">
                                        <div class="step-icon" data-bs-toggle="tooltip" data-bs-placement="top"
                                            title="Checkout Page">
                                            <i class="fas fa-file-invoice"></i>
                                        </div>
                                    </a>
                                </li>

                                <li class="nav-item" id="PaymentDisplay">
                                    <a href="#bank-detail" class="nav-link" data-toggle="tab">
                                        <div class="step-icon" data-bs-toggle="tooltip" data-bs-placement="top"
                                            title="Payment Details">
                                            <i class="fas fa-credit-card"></i>
                                        </div>
                                    </a>
                                </li>
                            </ul>
                            <!-- wizard-nav -->

                            <div class="tab-content twitter-bs-wizard-tab-content">
                                <div class="tab-pane active" id="seller-details">
                                    @include('Orders.QuickQuotation.step-1')
                                </div>
                                <!-- tab pane -->
                                <div class="tab-pane" id="product-display">
                                    @include('Orders.QuickQuotation.step-2')
                                </div>
                                <!-- tab pane -->
                                <div class="tab-pane" id="checkout">
                                    @include('Orders.QuickQuotation.step-3')
                                </div>
                                <!-- tab pane -->
                                <div class="tab-pane" id="bank-detail">
                                    @include('Orders.QuickQuotation.step-4')
                                </div>
                                <!-- tab pane -->
                            </div>
                            <!-- end tab content -->
                        </div>
                    </div>
                    <!-- end card body -->
                </div>
            </div>
            <!-- /Wizard -->
        </div>

        <div class="clipboard visually-hidden">
            <textarea cols="30" rows="10" id="CopyPersonalDetails" name="CopyPersonalDetails"></textarea>
        </div>


        <div class="modal fade" id="ModalCopyLinkBattery" tabindex="-1" role="dialog"
            aria-labelledby="ModalCopyLinkBattery" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary">
                        <h5 class="modal-title text-white">Copy Link Battery</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#VehicleCustomer').select2({
                maximumSelectionLength: 1
            });

            $('#BtnShareFormPersonalDetails').on('click', function() {
                var button = $(this);
                button.prop('disabled', true);
                button.html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...'
                );
                let FullName = $('#FullName').val();
                let ContactNumber = $('#ContactNumber').val();
                let AddressCustomer = $('#AddressCustomer').val();
                let VehicleCustomer = $('#VehicleCustomer').val();
                let EmailCustomer = $('#EmailCustomer').val();

                if (FullName == '') {
                    swal.fire("Error!", "Full Name is required", "error");
                    button.prop('disabled', false);
                    button.html(
                        "<i class='fa-brands fa-whatsapp'></i> Share "
                    );
                    return;
                }

                if (ContactNumber == '') {
                    swal.fire("Error!", "Contact Number is required", "error");
                    button.prop('disabled', false);
                    button.html(
                        "<i class='fa-brands fa-whatsapp'></i> Share "
                    );
                    return;
                }

                if (AddressCustomer == '') {
                    swal.fire("Error!", "Address Customer is required", "error");
                    button.prop('disabled', false);
                    button.html(
                        "<i class='fa-brands fa-whatsapp'></i> Share "
                    );
                    return;
                }

                if (VehicleCustomer == '') {
                    swal.fire("Error!", "Vehicle Customer is required", "error");
                    button.prop('disabled', false);
                    button.html(
                        "<i class='fa-brands fa-whatsapp'></i> Share "
                    );
                    return;
                }

                if (EmailCustomer == '') {
                    swal.fire("Error!", "Email Customer is required", "error");
                    button.prop('disabled', false);
                    button.html(
                        "<i class='fa-brands fa-whatsapp'></i> Share "
                    );
                    return;
                }

                if (!isValidEmail(EmailCustomer)) {
                    swal.fire("Error!", "Email is not valid", "error");
                    button.prop('disabled', false);
                    button.html(
                        "<i class='fa-brands fa-whatsapp'></i> Share "
                    );
                    return;
                }

                if (ContactNumber.substring(0, 1) != '8') {
                    swal.fire("Error!", "Contact Number must start with 8", "error");
                    button.prop('disabled', false);
                    button.html(
                        "<i class='fa-brands fa-whatsapp'></i> Share "
                    );
                    return;
                }

                let data = {
                    FullName: FullName,
                    ContactNumber: ContactNumber,
                    AddressCustomer: AddressCustomer,
                    VehicleCustomer: VehicleCustomer,
                    EmailCustomer: EmailCustomer,
                    _token: $('meta[name="csrf-token"]').attr('content')
                };


                swal.fire({
                    title: "Are you sure?",
                    text: "You will not be able to recover this imaginary file!",
                    icon: "warning",
                    showCancelButton: !0,
                    confirmButtonText: "Yes, share it!",
                    cancelButtonText: "No, cancel!",
                    reverseButtons: !0
                }).then(function(e) {
                    if (e.value === true) {
                        $.ajax({
                            url: "/quotation/customer/share",
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

                                    button.prop('disabled', false);
                                    button.html(
                                        "<i class='fa-brands fa-whatsapp'></i> Share"
                                    );
                                } else {
                                    Swal.fire({
                                        title: "Error",
                                        text: ResponseData.message ||
                                            "Something went wrong, please try again later",
                                        icon: "error",
                                    });

                                    button.prop('disabled', false);
                                    button.html(
                                        "<i class='fa-brands fa-whatsapp'></i> Share "
                                    );
                                };
                            }
                        });
                    } else {
                        button.prop('disabled', false);
                        button.html(
                            "<i class='fa-brands fa-whatsapp'></i> Share "
                        );
                    };
                });

            });

            function isValidEmail(email) {
                var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return emailRegex.test(email);
            }








            $(".product-next-btn").on('click', function() {
                $("#CheckoutPreview").html('');
                var FullName = $("#FullName").val();
                var EmailCustomer = $("#EmailCustomer").val();
                var ContactNumber = $("#ContactNumber").val();
                var AddressCustomer = $("#AddressCustomer").val();
                var VehicleCustomer = $("#VehicleCustomer").val();
                var TemplateMessage = $("#TemplateMessage").val();
                var Battery = $("input[name='CheckBattery[]']:checked").map(function() {
                    return $(this).val();
                }).get();

                if (Battery.length == 0) {
                    swal.fire("Error!", "Please select battery", "error");
                    return;
                }

                var distributorChecked = $("input[name='CheckDistributor[]']:checked").map(function() {
                    return $(this).val();
                }).get();

                if (distributorChecked.length > 1) {
                    swal.fire("Error!", "Please select only one distributor", "error");
                    return;
                }

                if (FullName == '') {
                    swal.fire("Error!", "Full Name is required", "error");
                    return;
                }

                if (ContactNumber == '') {
                    swal.fire("Error!", "Contact Number is required", "error");
                    return;
                }

                if (AddressCustomer == '') {
                    swal.fire("Error!", "Address Customer is required", "error");
                    return;
                }

                var DistributorShopId = $("#DistributorShopId").val();

                if (DistributorShopId == '') {
                    swal.fire("Error!", "Please select distributor", "error");
                    return false;
                }

                // jika contact number tidak diawali dengan 8
                if (ContactNumber.substring(0, 1) != '8') {
                    swal.fire("Error!", "Contact Number must start with 8", "error");
                    return false;
                }

                $('#btnNextStep3').trigger('click');

                if ($('#CheckoutDisplay').hasClass('active')) {
                    var Latitude = $("#Latitude").val();
                    var Longitude = $("#Longitude").val();
                    var IdCustomer = $("#IdCustomer").val();
                    var Battery = $("input[name='CheckBattery[]']:checked").map(function() {
                        return $(this).val();
                    }).get();
                    var DistributorShopId = $("#DistributorShopId").val();

                    var data = {
                        FullName: FullName,
                        EmailCustomer: EmailCustomer,
                        ContactNumber: ContactNumber,
                        AddressCustomer: AddressCustomer,
                        VehicleCustomer: VehicleCustomer,
                        TemplateMessage: TemplateMessage,
                        Latitude: Latitude,
                        Longitude: Longitude,
                        IdCustomer: IdCustomer,
                        Battery: Battery,
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        DistributorShopId: DistributorShopId
                    };

                    $.ajax({
                        url: "/quotation/checkout",
                        type: "GET",
                        data: data,
                        success: function(data) {
                            $("#CheckoutPreview").html(data);
                        }
                    });
                }
            });


            $("#btnNextStep4").on('click', function() {
                // $('#btnNextStep4').trigger('click');
                var BatteryIdCheckout = [];
                $(".BatteryIdCheckout").each(function() {
                    var value = $(this).val();
                    BatteryIdCheckout.push(value);
                });

                // jika BatteryIdCheckout kosong 
                if (BatteryIdCheckout.length == 0) {
                    swal.fire("Error!", "Please select battery", "error");
                    return false;
                }


                if ($('#PaymentDisplay').hasClass('active')) {
                    var FullName = $("#FullName").val();
                    var EmailCustomer = $("#EmailCustomer").val();
                    var ContactNumber = $("#ContactNumber").val();
                    var AddressCustomer = $("#AddressCustomer").val();
                    var VehicleCustomer = $("#VehicleCustomer").val();
                    var TemplateMessage = $("#TemplateMessage").val();
                    var Latitude = $("#Latitude").val();
                    var Longitude = $("#Longitude").val();
                    var IdCustomer = $("#IdCustomer").val();
                    var Battery = $("input[name='CheckBattery[]']:checked").map(function() {
                        return $(this).val();
                    }).get();
                    var TotalAmount = $("#TotalAmountHidden").val();
                    var tax = $("#tax").val();
                    var Discount = $("#discount").val();
                    var ExtraDiscount = $("#Extradiscount").val();
                    var QtyTabel = [];
                    var PriceTabel = [];
                    var BatteryNameTabel = [];
                    var Platform = [];
                    var LinkTokopedia = [];
                    var BatteryIdCheckout = [];
                    var GrossPrice = [];
                    var DiscountRow = [];
                    var NetPrice = [];
                    var SubtotalRow = [];
                    var TaxRow = [];
                    var TaxPriceRow = [];
                    $(".QtyCheckout").each(function() {
                        var value = $(this).val();
                        QtyTabel.push(value);
                    });

                    $(".PriceCheckout").each(function() {
                        var value = $(this).val();
                        PriceTabel.push(value);
                    });

                    $(".BatteryNameCheckout").each(function() {
                        var value = $(this).val();
                        BatteryNameTabel.push(value);
                    });

                    $(".LinkTokopedia").each(function() {
                        var value = $(this).val();
                        LinkTokopedia.push(value);
                    });
                    var DistributorShopId = $("#DistributorShopId").val();

                    $(".Platform").each(function() {
                        var value = $(this).val();
                        Platform.push(value);
                    });

                    $(".BatteryIdCheckout").each(function() {
                        var value = $(this).val();
                        BatteryIdCheckout.push(value);
                    });

                    $(".GrossPrice").each(function() {
                        var value = $(this).val();
                        GrossPrice.push(value);
                    });

                    $(".DiscountRow").each(function() {
                        var value = $(this).val();
                        DiscountRow.push(value);
                    });

                    $(".NetPrice").each(function() {
                        var value = $(this).val();
                        NetPrice.push(value);
                    });

                    $(".SubtotalRow").each(function() {
                        var value = $(this).val();
                        SubtotalRow.push(value);
                    });

                    $(".TaxRow").each(function() {
                        var value = $(this).val();
                        TaxRow.push(value);
                    });

                    $(".PriceTaxRow").each(function() {
                        var value = $(this).val();
                        TaxPriceRow.push(value);
                    });

                    var subtotal = $("#subtotal").val();
                    var DiscountRupiah = $("#discount-rupiah").val();
                    var DiscountPercentage = $("#discount-percent").val();
                    var typeDiscount = $("#type-discount").val();

                    var data = {
                        FullName: FullName,
                        EmailCustomer: EmailCustomer,
                        ContactNumber: ContactNumber,
                        AddressCustomer: AddressCustomer,
                        VehicleCustomer: VehicleCustomer,
                        TemplateMessage: TemplateMessage,
                        Latitude: Latitude,
                        Longitude: Longitude,
                        IdCustomer: IdCustomer,
                        Battery: BatteryIdCheckout,
                        TotalAmount: TotalAmount,
                        tax: tax,
                        Discount: Discount,
                        ExtraDiscount: ExtraDiscount,
                        BatteryNameTabel: BatteryNameTabel,
                        QtyTabel: QtyTabel,
                        PriceTabel: PriceTabel,
                        DistributorShopId: DistributorShopId,
                        LinkTokopedia: LinkTokopedia,
                        Platform: Platform,
                        subtotal: subtotal,
                        DiscountRupiah: DiscountRupiah,
                        DiscountPercentage: DiscountPercentage,
                        GrossPrice: GrossPrice,
                        DiscountRow: DiscountRow,
                        NetPrice: NetPrice,
                        SubtotalRow: SubtotalRow,
                        TaxRow: TaxRow,
                        TaxPriceRow: TaxPriceRow,
                        typeDiscount: typeDiscount,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    };

                    $.ajax({
                        url: "/quotation/payment",
                        type: "GET",
                        data: data,
                        success: function(data) {
                            $("#PaymentPreview").html(data);
                        }
                    });
                }
            });


            $("#BtnShareInvoice").on('click', function() {
                var button = $(this);
                button.prop('disabled', true);
                button.html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...'
                );

                var FullName = $("#FullName").val();
                var ContactNumber = $("#ContactNumber").val();
                var VehicleCustomer = $('#VehicleCustomer').val();
                var Latitude = $("#Latitude").val();
                var Longitude = $("#Longitude").val();
                var AddressCustomer = $("#AddressCustomer").val();
                var Battery = [];
                var QtyTabel = []; // Menambahkan array untuk menyimpan kuantitas
                var PriceTabel = []; // Menambahkan array untuk menyimpan harga

                $(".add-table-items tbody tr").each(function() {
                    var batteryName = $(this).find("input[name='BatteryNameCheckout[]']").val();
                    var quantity = $(this).find("input[name='QtyCheckout[]']").val();
                    var price = $(this).find("input[name='NetPrice[]']").val();
                    Battery.push({
                        batteryName: batteryName,
                        quantity: quantity,
                        price: price
                    });
                    QtyTabel.push(quantity); // Menambahkan kuantitas ke dalam array
                    PriceTabel.push(price); // Menambahkan harga ke dalam array
                });

                var subtotal = $("#subtotal").val();
                var tax = $("#tax").val();
                var discount = $("#discount").val();
                var TotalAmountHidden = $("#TotalAmountHidden").val();
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

                // Mengubah pengecekan QtyTabel dan PriceTabel
                if (QtyTabel.some(qty => qty === '' || qty <= 0)) {
                    swal.fire("Error!", "Please insert quantity", "error");
                    button.prop('disabled', false);
                    button.html("<i class='fa-brands fa-whatsapp'></i> Share");
                    return;
                }

                if (PriceTabel.some(price => price === '' || price <= 0)) {
                    swal.fire("Error!", "Please insert price", "error");
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

            $("#BtnSharePaymentDetails").on('click', function() {
                var button = $(this);
                button.prop('disabled', true);
                button.html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...'
                );

                var FullName = $("#FullName").val();
                var ContactNumber = $("#ContactNumber").val();
                var VehicleCustomer = $('#VehicleCustomer').val();
                var Latitude = $("#Latitude").val();
                var Longitude = $("#Longitude").val();
                var AddressCustomer = $("#AddressCustomer").val();
                var Battery = [];
                var QtyTabel = []; // Menambahkan array untuk menyimpan kuantitas
                var PriceTabel = []; // Menambahkan array untuk menyimpan harga
                var links = [];
                var Battery = [];
                var InvoiceNumber = $("#invoiceNumber").val();
                // $(".add-table-items tbody tr").each(function() {
                //     var batteryName = $(this).find("input[name='BatteryNameCheckout[]']").val();
                //     var quantity = $(this).find("input[name='QtyCheckout[]']").val();
                //     var price = $(this).find("input[name='PriceCheckout[]']").val();
                //     Battery.push({
                //         batteryName: batteryName,
                //         quantity: quantity,
                //         price: price
                //     });
                // });
                var IsMidtrans = $("#CheckMidtrans").prop("checked");
                if (IsMidtrans) {
                    var linkMidtrans = $("#LinkPaymentMidtrans").val();
                    links.push(linkMidtrans);
                    var IsMidtrans = "midtrans";
                } else {
                    $(".LinkPayment").each(function() {
                        var value = $(this).val();
                        links.push(value);
                    });
                    var IsMidtrans = "not midtrans";
                }
                $(".add-table-items tbody tr").each(function() {
                    var batteryName = $(this).find("input[name='BatteryNameCheckout[]']").val();
                    var quantity = $(this).find("input[name='QtyCheckout[]']").val();
                    var price = $(this).find("input[name='NetPrice[]']").val();
                    Battery.push({
                        batteryName: batteryName,
                        quantity: quantity,
                        price: price
                    });
                    QtyTabel.push(quantity); // Menambahkan kuantitas ke dalam array
                    PriceTabel.push(price); // Menambahkan harga ke dalam array
                });

                var subtotal = $("#subtotal").val();
                var tax = $("#tax").val();
                var discount = $("#discount").val();
                var TotalAmountHidden = $("#TotalAmountHidden").val();
                var PaymentMethod = $("#PaymentMethod").val();
                var typeDiscount = $("#type-discount").val();

                var data = {
                    FullName: FullName,
                    ContactNumber: ContactNumber,
                    Battery: Battery,
                    InvoiceNumber: InvoiceNumber,
                    IsMidtrans: IsMidtrans,
                    links: links,
                    Subtotal: subtotal,
                    Tax: tax,
                    Discount: discount,
                    TotalAmount: TotalAmountHidden,
                    VehicleCustomer: VehicleCustomer,
                    Latitude: Latitude,
                    Longitude: Longitude,
                    AddressCustomer: AddressCustomer,
                    PaymentMethod: PaymentMethod,
                    typeDiscount: typeDiscount,
                    _token: $('meta[name="csrf-token"]').attr('content')
                };

                $.ajax({
                    url: "/quotation/share-payment-details",
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
                        button.html(
                            "<i class='fa-brands fa-whatsapp'></i> Share"
                        );
                    }
                });
            });

            $("#ButtonSaveData").on('click', function() {
                var button = $(this);
                button.prop('disabled', true);
                button.html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...'
                );

                var FullName = $("#FullName").val();
                var EmailCustomer = $("#EmailCustomer").val();
                var ContactNumber = $("#ContactNumber").val();
                var AddressCustomer = $("#AddressCustomer").val();
                var VehicleCustomer = $("#VehicleCustomer").val();
                var Latitude = $("#Latitude").val();
                var Longitude = $("#Longitude").val();
                var IdCustomer = $("#IdCustomer").val();
                var Battery = $("input[name='CheckBattery[]']:checked").map(function() {
                    return $(this).val();
                }).get();
                var TotalAmount = $("#TotalAmountHidden").val();
                var tax = $("#tax").val();
                var Discount = $("#discount").val();
                var ExtraDiscount = $("#Extradiscount").val();
                var invoiceNumber = $("#invoiceNumber").val();
                var techniciansName = $("#techniciansName").val();
                var subtotal = $("#subtotal").val();
                if ($('.CheckMidtrans').is(':checked')) {
                    var CheckMidtrans = 1;
                    var linkPayment = $("#LinkPaymentMidtrans").val();
                } else {
                    var CheckMidtrans = 0;
                }

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

                $(".QtyCheckout").each(function() {
                    var value = $(this).val();
                    QtyTabel.push(value);
                });

                $(".PriceCheckout").each(function() {
                    var value = $(this).val();
                    PriceTabel.push(value);
                });

                $(".BatteryNameCheckout").each(function() {
                    var value = $(this).val();
                    BatteryNameTabel.push(value);
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

                var PaymentMethod = $("#PaymentMethod").val();
                var DistributorShopId = $("#DistributorShopId").val();
                var DiscountRupiah = $("#discount-rupiah").val();
                var DiscountPercentage = $("#discount-percent").val();
                var typeDiscount = $("#type-discount").val();

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
                    url: "/quotation/save-data",
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
                            setTimeout(function() {
                                window.location.href = "/sales-order";
                            }, 2000);
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
                        button.html(
                            "Save Changes"
                        );
                    }
                });
            });
        });

        function CopyLinkBattery(x) {
            var id = $(x).data('id');
            $('#ModalCopyLinkBattery').modal('show');
            var data = {
                id: id,
                _token: $('meta[name="csrf-token"]').attr('content')
            };
            $.ajax({
                url: "/quotation/get-link-battery",
                type: "GET",
                data: data,
                success: function(data) {
                    if (data.error) {
                        alert(data.error);
                        return;
                    }
                    var modalBody = $("#ModalCopyLinkBattery .modal-body");
                    modalBody.empty();

                    data.forEach(function(item) {
                        var inputGroup = `
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" value="${item.platform} - ${item.url}" readonly>
                                <button class="btn btn-outline-secondary" type="button" data-id="${item.url}" onclick="CopyToClipboard(this)">Copy</button>
                            </div>
                        `;
                        modalBody.append(inputGroup);
                    });
                },
                error: function(err) {
                    var modalBody = $("#ModalCopyLinkBattery .modal-body");
                    modalBody.empty();
                    var inputGroup = `
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" value="Data not found" readonly>
                    </div>
                `;
                    modalBody.append(inputGroup);
                }
            });
        }

        function CopyToClipboard(x) {
            var input = $(x).prev();
            input.select();
            document.execCommand("copy");
            Swal.fire({
                title: "Success",
                text: "Link copied to clipboard",
                icon: "success",
            });
        }
    </script>

    {{-- GOOGLE MAPS JANGAN DIOTAK ATIK YA GESSS YAA  --}}
    <script>
        var map;
        var marker;

        function initMap() {
            map = new google.maps.Map(document.getElementById('map'), {
                center: {
                    lat: -6.8837859188198784,
                    lng: 107.5403487263912
                },
                zoom: 17
            });

            var input = document.getElementById('AddressCustomer');
            var autocomplete = new google.maps.places.Autocomplete(input);
            autocomplete.bindTo('bounds', map);

            marker = new google.maps.marker.AdvancedMarkerElement({
                map: map,
                draggable: true
            });

            autocomplete.addListener('place_changed', function() {
                var place = autocomplete.getPlace();
                if (!place.geometry) {
                    console.error("Place details not found");
                    return;
                }

                var location = place.geometry.location;
                if (isNaN(location.lat()) || isNaN(location.lng())) {
                    console.error("Invalid coordinates");
                    return;
                }

                if (place.geometry.viewport && place.geometry.viewport instanceof google.maps.LatLngBounds) {
                    map.fitBounds(place.geometry.viewport);
                } else {
                    if (place.geometry.location) {
                        map.setCenter(location);
                        map.setZoom(17);
                    } else {
                        console.error("Viewport not available");
                    }
                }

                marker.setPosition(location);
                marker.setVisible(true);


                var address = place.formatted_address;
                var latitude = parseFloat(place.geometry.location.lat());
                var longitude = parseFloat(place.geometry.location.lng());


                document.getElementById('AddressCustomer').value = address;
                document.getElementById('Latitude').value = latitude;
                document.getElementById('Longitude').value = longitude;
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


                            document.getElementById('AddressCustomer').value = address;
                            document.getElementById('Latitude').value = latitude;
                            document.getElementById('Longitude').value = longitude;
                        }
                    } else {
                        console.error('Geocoder failed due to: ' + status);
                    }
                });

                // panggil auto complete
                var input = document.getElementById('AddressCustomer');
                var autocomplete = new google.maps.places.Autocomplete(input);
                autocomplete.bindTo('bounds', map);

                autocomplete.addListener('place_changed', function() {
                    var place = autocomplete.getPlace();
                    if (!place.geometry) {
                        console.error("Place details not found");
                        return;
                    }

                    var location = place.geometry.location;
                    if (isNaN(location.lat()) || isNaN(location.lng())) {
                        console.error("Invalid coordinates");
                        return;
                    }

                    if (place.geometry.viewport && place.geometry.viewport instanceof google.maps
                        .LatLngBounds) {
                        map.fitBounds(place.geometry.viewport);
                    } else {
                        if (place.geometry.location) {
                            map.setCenter(location);
                            map.setZoom(17);
                        } else {
                            console.error("Viewport not available");
                        }
                    }

                    marker.setPosition(location);
                    marker.setVisible(true);


                    var address = place.formatted_address;
                    var latitude = parseFloat(place.geometry.location.lat());
                    var longitude = parseFloat(place.geometry.location.lng());


                    document.getElementById('AddressCustomer').value = address;
                    document.getElementById('Latitude').value = latitude;
                    document.getElementById('Longitude').value = longitude;


                });
            });
        }
    </script>
    <script async
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCAlBnX9jmy3JurAGnyIAFNSyS7i5cgfzA&loading=async&libraries=places,marker&callback=initMap">
    </script>
    {{-- END DESKTOP VERSION --}}

    @include('Mobile.Orders.QuickQuotation.index')
@endsection
