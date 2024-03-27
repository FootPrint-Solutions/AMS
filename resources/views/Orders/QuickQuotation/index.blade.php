@extends('template.master')

@section('content')
    <style>
        #AutoCompleteFullNameCustomer {
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

        #map {
            height: 205px;
            width: 100%;
            margin-bottom: 20px;
        }

        .visually-hidden {
            position: absolute !important;
            height: 1px;
            width: 1px;
            overflow: hidden;
            clip: rect(1px 1px 1px 1px);
            /* IE6, IE7 */
            clip: rect(1px, 1px, 1px, 1px);
            white-space: nowrap;
            /* added line */
        }

        .blog-image img {
            width: 75%;
        }
    </style>
    {{-- Title --}}



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

    <script>
        $(document).ready(function() {
            $('#VehicleCustomer').select2();

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
                let templateMessage = $('#TemplateMessage').val();

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

                if (ContactNumber.substring(0, 1) != '8') {
                    swal.fire("Error!", "Contact Number must start with 8", "error");
                    button.prop('disabled', false);
                    button.html(
                        "<i class='fa-brands fa-whatsapp'></i> Share "
                    );
                    return;
                }

                if (templateMessage.includes('<NAME>') == false || templateMessage.includes('<ADDRESS>') ==
                    false || templateMessage.includes('<EMAIL>') == false || templateMessage.includes(
                        '<VEHICLE>') == false) {
                    swal.fire("Error!",
                        "Template Message must contain NAME, ADDRESS, EMAIL, VEHICLE", "error");
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
                    TemplateMessage: templateMessage,
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

            $('#FullName').on('keyup', function() {
                var input = $(this).val();
                if (input.length > 0) {
                    $.ajax({
                        url: "/quotation/customer/find",
                        type: "GET",
                        data: {
                            input: input
                        },
                        success: function(data) {
                            // let suggestions = data.map(item => item.name);
                            if (data.length > 0) {
                                displaySuggestions(data);
                            } else {
                                $('#AutoCompleteFullNameCustomer').html('');
                                $("#EmailCustomer").val('');
                                $("#ContactNumber").val('');
                                $("#AddressCustomer").val('');
                                $("#IdCustomer").val('');
                                $("#Latitude").val('');
                                $("#Longitude").val('');
                                $('#UserExist').hide();
                                $('#UserNotExist').show();
                            }
                        }
                    });
                } else {
                    $('#AutoCompleteFullNameCustomer').html('');
                    $("#EmailCustomer").val('');
                    $("#ContactNumber").val('');
                    $("#AddressCustomer").val('');
                    $("#Latitude").val('');
                    $("#Longitude").val('');
                    $("#IdCustomer").val('');
                    var IdCustomer = $("#IdCustomer").val();
                    if (IdCustomer != '') {
                        $('#UserExist').show();
                        $('#UserNotExist').hide();
                    } else {
                        $('#UserExist').hide();
                        $('#UserNotExist').show();
                    }
                }
            });

            function displaySuggestions(suggestions) {
                $('#AutoCompleteFullNameCustomer').empty();

                suggestions.forEach(function(item) {
                    $('#AutoCompleteFullNameCustomer').append('<div class="suggestion">' + item.name +
                        '</div>');
                    // $("#EmailCustomer").val(item.email);
                    // $("#ContactNumber").val(item.contact);
                    // $("#AddressCustomer").val(item.address);
                });

                $('.suggestion').click(function() {
                    var index = $(this).index();

                    $('#FullName').val(suggestions[index].name);
                    var cleanNumber = suggestions[index].contact.replace(/\D/g, '');
                    $('#ContactNumber').val(cleanNumber);
                    $('#EmailCustomer').val(suggestions[index].email);
                    $('#AddressCustomer').val(suggestions[index].address);
                    $('#IdCustomer').val(suggestions[index].id);
                    $("#Latitude").val(suggestions[index].latitude);
                    $("#Longitude").val(suggestions[index].longitude);
                    $('#AutoCompleteFullNameCustomer').empty();
                    // call google maps
                    initMap();

                    var IdCustomer = $("#IdCustomer").val();
                    if (IdCustomer != '') {
                        $('#UserExist').show();
                        $('#UserNotExist').hide();

                        // get vehcile by  id 
                        $.ajax({
                            url: "/quotation/customer/vehicle/find",
                            type: "GET",
                            data: {
                                id: IdCustomer,
                            },
                            success: function(data) {
                                var vehicles = data;
                                $('#VehicleCustomer').val(vehicles);
                                $('#VehicleCustomer').trigger('change');
                            }
                        });
                    } else {
                        $('#UserExist').hide();
                        $('#UserNotExist').show();
                    }
                });
            }

            function updateCopyPersonalDetails() {
                var FullName = $("#FullName").val();
                var AddressCustomer = $('#AddressCustomer').val();
                var EmailCustomer = $('#EmailCustomer').val();
                var vehicles = $('#VehicleCustomer').find('option:selected').map(function() {
                    return $(this).text().trim();
                }).get();
                var VehicleCustomer = vehicles.join(", ");
                var TemplateMessage = $('#TemplateMessage').val();

                var CopyPersonalDetails = TemplateMessage.replace('<NAME>', FullName).replace('<ADDRESS>',
                    AddressCustomer).replace('<EMAIL>', EmailCustomer).replace('<VEHICLE>',
                    VehicleCustomer);
                $('#CopyPersonalDetails').val(CopyPersonalDetails);
            }

            var ElementId =
                "#FullName, #EmailCustomer, #ContactNumber, #AddressCustomer, #VehicleCustomer, #TemplateMessage, #AutoCompleteFullNameCustomer, .select2-selection__choice, #CopyPersonalDetails, #btnCopyAddress";
            $(ElementId).on('click keyup', updateCopyPersonalDetails);


            $("#btnCopyAddress").on('click', function() {
                swal.fire("Copied!", "Personal Details Copied", "success");
            });


            // check 
            $('.seller-next-btn-check').on('click', function() {
                var FullName = $("#FullName").val();
                var EmailCustomer = $("#EmailCustomer").val();
                var ContactNumber = $("#ContactNumber").val();
                var AddressCustomer = $("#AddressCustomer").val();
                var VehicleCustomer = $("#VehicleCustomer").val();
                var TemplateMessage = $("#TemplateMessage").val();
                var Latitude = $("#Latitude").val();
                var Longitude = $("#Longitude").val();
                var IdCustomer = $("#IdCustomer").val();

                if (FullName == '') {
                    swal.fire("Error!", "Full Name is required", "error");
                    return;
                }

                if (EmailCustomer == '') {
                    swal.fire("Error!", "Email Customer is required", "error");
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

                if (VehicleCustomer == '') {
                    swal.fire("Error!", "Vehicle Customer is required", "error");
                    return;
                }

                if (Latitude == '' || Longitude == '') {
                    swal.fire("Error!", "Latitude and Longitude is required", "error");
                    return;
                }

                if (TemplateMessage.includes('<NAME>') == false || TemplateMessage.includes('<ADDRESS>') ==
                    false || TemplateMessage.includes('<EMAIL>') == false || TemplateMessage.includes(
                        '<VEHICLE>') == false) {
                    swal.fire("Error!",
                        "Template Message must contain NAME, ADDRESS, EMAIL, VEHICLE", "error");
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

                $('#btnNextStep2').trigger('click');

                // check jika button next step 2 berhasil di click
                if ($('#ProductDisplay').hasClass('active')) {
                    $.ajax({
                        url: "/quotation/vehicle/find",
                        type: "GET",
                        data: {
                            id: VehicleCustomer,
                        },
                        success: function(data) {
                            var html = '';
                            // jika data kosong
                            if (data.length === 0) {
                                html =
                                    '<div class="alert alert-danger alert-dismissible fade show" role="alert">No Battery Found</div>';
                                $('#ResultRecommendationBattery').html(html);
                                return;
                            } else {
                                data.forEach(function(vehicle) {
                                    html +=
                                        '<div class="col-md-6 col-xl-4 col-sm-12 d-flex">';
                                    html += '<div class="blog grid-blog flex-fill">';
                                    html += '<div class="blog-image">';
                                    html += '<a href="#!">';
                                    if (vehicle.image == null) {

                                        vehicle.image =
                                            'https://via.placeholder.com/210x210';
                                        html += '<img class="img-fluid" src="' + vehicle
                                            .image + '" alt="Post Image">';
                                    } else {
                                        var baseUrl =
                                            "{{ asset('storage/image/battery/') }}";
                                        vehicle.image = vehicle.image;
                                        html += '<img class="img-fluid" src="' +
                                            baseUrl +
                                            '/' + vehicle.image +
                                            '" alt="Post Image">';
                                    }
                                    html += '</a>';
                                    html += '</div>';
                                    html += '<div class="blog-content">';
                                    html +=
                                        '<h3 class="blog-title"><a href="#!">' +
                                        vehicle.name + '</a></h3>';
                                    html += '<p>Details & Specification :</p>';
                                    html += '<ul class="list-group list-group-flush">';
                                    html += '<li class="list-group-item">Warranty : ' +
                                        vehicle.warranty + ' Months</li>';

                                    html += '<li class="list-group-item">Price : Rp. ' +
                                        Number(vehicle.price_retail).toLocaleString(
                                            'id-ID') + '</li>';
                                    html += '</ul>';
                                    html +=
                                        '</div>';
                                    html += '<div class="row">';
                                    html +=
                                        '<div class="edit-options">';
                                    html +=
                                        '<div class="text-end inactive-style mt-3">';
                                    html +=
                                        '<div class="checkbox">';
                                    html += '<label>';
                                    html +=
                                        '<input type="checkbox" name="CheckBattery[]" value=' +
                                        vehicle.id + '> Share To Customer';
                                    html +=
                                        '</label>';
                                    html += '</div>';
                                    html += '</div>';
                                    html +=
                                        '</div>';
                                    html += '</div>';
                                    html += '</div>';
                                    html +=
                                        '</div>';
                                });
                                $('#ResultRecommendationBattery').html(html);
                                getMapsNearAddressCustomer();
                            }
                        }
                    });
                }

                function getMapsNearAddressCustomer() {
                    var address = $('#AddressCustomer').val();
                    var latitude = $('#Latitude').val();
                    var longitude = $('#Longitude').val();
                    var idCustomer = $('#IdCustomer').val();
                    var data = {
                        address: address,
                        latitude: latitude,
                        longitude: longitude,
                    };

                    $.ajax({
                        url: "/quotation/customer/maps/near",
                        type: "GET",
                        data: data,
                        success: function(data) {
                            $("#MapsDistributorRecomendation").html(data);
                        }
                    });
                }
            });

            $("#BtnShareBattery").click(function() {
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
                var TemplateMessage = $("#TemplateMessage").val();
                var Latitude = $("#Latitude").val();
                var Longitude = $("#Longitude").val();
                var IdCustomer = $("#IdCustomer").val();
                var Battery = $("input[name='CheckBattery[]']:checked").map(function() {
                    return $(this).val();
                }).get();
                var TemplateMessageStep2 = $("#TemplateMessageStep2").val();

                if (Battery.length == 0) {
                    swal.fire("Error!", "Please select battery", "error");
                    button.prop('disabled', false);
                    button.html(
                        "<i class='fa-brands fa-whatsapp'></i> Share"
                    );
                    return;
                }

                if (FullName == '') {
                    swal.fire("Error!", "Full Name is required", "error");
                    button.prop('disabled', false);
                    button.html(
                        "<i class='fa-brands fa-whatsapp'></i> Share"
                    );
                    return;
                }

                if (EmailCustomer == '') {
                    swal.fire("Error!", "Email Customer is required", "error");
                    button.prop('disabled', false);
                    button.html(
                        "<i class='fa-brands fa-whatsapp'></i> Share"
                    );
                    return;
                }

                if (ContactNumber == '') {
                    swal.fire("Error!", "Contact Number is required", "error");
                    button.prop('disabled', false);
                    button.html(
                        "<i class='fa-brands fa-whatsapp'></i> Share"
                    );
                    return;
                }

                if (AddressCustomer == '') {
                    swal.fire("Error!", "Address Customer is required", "error");
                    button.prop('disabled', false);
                    button.html(
                        "<i class='fa-brands fa-whatsapp'></i> Share"
                    );
                    return;
                }

                if (VehicleCustomer == '') {
                    swal.fire("Error!", "Vehicle Customer is required", "error");
                    button.prop('disabled', false);
                    button.html(
                        "<i class='fa-brands fa-whatsapp'></i> Share"
                    );
                    return;
                }

                if (Latitude == '' || Longitude == '') {
                    swal.fire("Error!", "Latitude and Longitude is required", "error");
                    button.prop('disabled', false);
                    button.html(
                        "<i class='fa-brands fa-whatsapp'></i> Share"
                    );
                    return;
                }

                if (TemplateMessage.includes('<NAME>') == false || TemplateMessage.includes('<ADDRESS>') ==
                    false || TemplateMessage.includes('<EMAIL>') == false || TemplateMessage.includes(
                        '<VEHICLE>') == false) {
                    swal.fire("Error!",
                        "Template Message must contain NAME, ADDRESS, EMAIL, VEHICLE", "error");
                    button.prop('disabled', false);
                    button.html(
                        "<i class='fa-brands fa-whatsapp'></i> Share"
                    );
                    return;
                }

                if (TemplateMessageStep2.includes('<BATTERYNAME>') == false || TemplateMessageStep2
                    .includes('<BATTERYCAPACITY>') == false || TemplateMessageStep2
                    .includes('<BATTERYPRICE>') == false || TemplateMessageStep2
                    .includes('<BATTERYWARRANTY>') == false) {
                    swal.fire("Error!",
                        "Template Message must contain BATTERYNAME, BATTERYPRICE, BATTERYWARRANTY",
                        "error");
                    button.prop('disabled', false);
                    button.html(
                        "<i class='fa-brands fa-whatsapp'></i> Share"
                    );
                    return;
                }

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
                    TemplateMessageStep2: TemplateMessageStep2,
                    _token: $('meta[name="csrf-token"]').attr('content')
                };

                var Battery = $("input[name='CheckBattery[]']:checked").map(function() {
                    return $(this).val();
                }).get();

                Battery.forEach(function(battery) {
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
                        Battery: battery,
                        TemplateMessageStep2: TemplateMessageStep2,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    };

                    $.ajax({
                        url: "/quotation/battery/share",
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
                                    "<i class='fa-brands fa-whatsapp'></i> Share"
                                );
                            };
                        }
                    });
                });
            });

            $(".product-next-btn").on('click', function() {
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

                $('#btnNextStep3').trigger('click');

                if ($('#CheckoutDisplay').hasClass('active')) {
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
                    var LinkTokopedia = [];
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
                        TotalAmount: TotalAmount,
                        tax: tax,
                        Discount: Discount,
                        ExtraDiscount: ExtraDiscount,
                        BatteryNameTabel: BatteryNameTabel,
                        QtyTabel: QtyTabel,
                        PriceTabel: PriceTabel,
                        DistributorShopId: DistributorShopId,
                        LinkTokopedia: LinkTokopedia,
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
                var Discount = $("#Discount").val();
                var ExtraDiscount = $("#ExtraDiscount").val();

                var QtyTabel = [];
                var PriceTabel = [];
                var BatteryNameTabel = [];
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

                var templateMessage = $("#TemplateMessageStep3").val();

                if (FullName == '' || EmailCustomer == '' || ContactNumber == '' || AddressCustomer == '' ||
                    VehicleCustomer == '' || Latitude == '' || Longitude == '' || templateMessage == '') {
                    swal.fire("Error!", "Please fill in all required fields", "error");
                    button.prop('disabled', false);
                    button.html(
                        "<i class='fa-brands fa-whatsapp'></i> Share"
                    );
                    return;
                }

                if (templateMessage.includes('<NAME>') == false || templateMessage.includes(
                        '<BATTERYNAME>') == false || templateMessage.includes('<QUANTITY>') == false ||
                    templateMessage.includes('<BATTERYPRICE>') == false || templateMessage.includes(
                        '<TAX>') == false || templateMessage.includes('<DISCOUNT>') == false ||
                    templateMessage
                    .includes('<EXTRADISCOUNT>') == false || templateMessage.includes('<TOTALAMOUNT>') ==
                    false) {
                    swal.fire("Error!",
                        "Template Message must contain NAME, BATTERYNAME, QUANTITY, BATTERYPRICE, TAX, DISCOUNT, EXTRADISCOUNT, TOTALAMOUNT",
                        "error");
                    button.prop('disabled', false);
                    button.html(
                        "<i class='fa-brands fa-whatsapp'></i> Share"
                    );
                    return;
                }

                if (BatteryNameTabel == '') {
                    swal.fire("Error!", "Please select battery", "error");
                    button.prop('disabled', false);
                    button.html(
                        "<i class='fa-brands fa-whatsapp'></i> Share"
                    );
                    return;
                }

                if (QtyTabel == '' || QtyTabel <= 0) {
                    swal.fire("Error!", "Please insert quantity", "error");
                    button.prop('disabled', false);
                    button.html(
                        "<i class='fa-brands fa-whatsapp'></i> Share"
                    );
                    return;
                }

                if (PriceTabel == '' || PriceTabel <= 0) {
                    swal.fire("Error!", "Please insert price", "error");
                    button.prop('disabled', false);
                    button.html(
                        "<i class='fa-brands fa-whatsapp'></i> Share"
                    );
                    return;
                }

                var data = {
                    FullName: FullName,
                    EmailCustomer: EmailCustomer,
                    ContactNumber: ContactNumber,
                    AddressCustomer: AddressCustomer,
                    VehicleCustomer: VehicleCustomer,
                    TemplateMessage: templateMessage,
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
                    _token: $('meta[name="csrf-token"]').attr('content')
                };

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
                        button.html(
                            "<i class='fa-brands fa-whatsapp'></i> Share"
                        );
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
                var templateMessage = $("#TemplateMessageStep4").val();

                if (FullName == '' || EmailCustomer == '' || ContactNumber == '' || AddressCustomer == '' ||
                    VehicleCustomer == '' || Latitude == '' || Longitude == '' || templateMessage == '') {
                    swal.fire("Error!", "Please fill in all required fields", "error");
                    button.prop('disabled', false);
                    button.html(
                        "<i class='fa-brands fa-whatsapp'></i> Share"
                    );
                    return;
                }

                if (templateMessage.includes('<NAME>') == false || templateMessage.includes(
                        '<PAYMENTLINK>') == false) {
                    swal.fire("Error!",
                        "Template Message must contain NAME, PAYMENTLINK ", "error");
                    button.prop('disabled', false);
                    button.html(
                        "<i class='fa-brands fa-whatsapp'></i> Share"
                    );
                    return;
                }

                var data = {
                    FullName: FullName,
                    ContactNumber: ContactNumber,
                    TemplateMessage: templateMessage,
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

            marker = new google.maps.Marker({
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
    <script async defer
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCAlBnX9jmy3JurAGnyIAFNSyS7i5cgfzA&libraries=places&callback=initMap">
    </script>



    <br><br><br><br><br>
@endsection
