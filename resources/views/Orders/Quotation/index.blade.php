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
    <link rel="stylesheet" href="{{ asset('/plugins/twitter-bootstrap-wizard/form-wizard.css') }}">



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
                                        title="Seller Details">
                                        <i class="far fa-user"></i>
                                    </div>
                                </a>
                            </li>
                            <li class="nav-item" id="ProductDisplay">
                                <a href="#product-display" class="nav-link" data-toggle="tab">
                                    <div class="step-icon" data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="Company Document">
                                        <i class="fa-solid fa-boxes-stacked"></i>
                                    </div>
                                </a>
                            </li>
                            <li class="nav-item" id="CheckoutDisplay">
                                <a href="#company-document" class="nav-link" data-toggle="tab">
                                    <div class="step-icon" data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="Company Document">
                                        <i class="fas fa-file-invoice"></i>
                                    </div>
                                </a>
                            </li>

                            <li class="nav-item" id="PaymentDisplay">
                                <a href="#bank-detail" class="nav-link" data-toggle="tab">
                                    <div class="step-icon" data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="Bank Details">
                                        <i class="fas fa-credit-card"></i>
                                    </div>
                                </a>
                            </li>
                        </ul>
                        <!-- wizard-nav -->

                        <div class="tab-content twitter-bs-wizard-tab-content">
                            <div class="tab-pane active" id="seller-details">
                                <div class="mb-4">
                                    <h5>Enter Your Personal Details</h5>
                                </div>
                                <form id='FormPersonalDetails'>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group local-forms">
                                                <label for="company-name">Full Name <span
                                                        class="login-danger">*</span></label>
                                                <input type="text" class="form-control" id="FullName" name="FullName"
                                                    placeholder="Enter Full Name" value="" required
                                                    autocomplete="off">
                                                <div id="AutoCompleteFullNameCustomer"></div>
                                                <span class="badge bg-success" id="UserExist" style='display:none;'>User
                                                    Exist</span>
                                                <span class="badge bg-warning" id="UserNotExist" style='display:none;'>New
                                                    User</span>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group local-forms">
                                                <label for="company-name">Email <span class="login-danger">*</span></label>
                                                <input type="text" class="form-control" id="EmailCustomer"
                                                    name="EmailCustomer" placeholder="Enter Email" value="" required
                                                    autocomplete="off">
                                            </div>
                                        </div>

                                    </div>

                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group local-forms">
                                                <div class="input-group">
                                                    <span class="input-group-text border-end country-code">+62</span>
                                                    <label for="company-name">Contact Number <span
                                                            class="login-danger">*</span></label>
                                                    <input type="number" class="form-control" id="ContactNumber"
                                                        name="ContactNumber" placeholder="Enter Contract Number"
                                                        value="" required autocomplete="off">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group local-forms">
                                                <label for="company-name">Vehicle Customer <span
                                                        class="login-danger">*</span></label>
                                                <select name="VehicleCustomer[]" multiple='multiple' id='VehicleCustomer'
                                                    class="form-select" aria-label="Default select example">
                                                    @foreach ($data['Vehicle'] as $vehicle)
                                                        <option value="{{ $vehicle['id'] }}">
                                                            {{ trim($vehicle['name']) }}

                                                        </option>
                                                    @endforeach

                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group local-forms">
                                                <label for="company-contact">Address Customer <span
                                                        class="login-danger">*</span></label>


                                                {{-- <textarea class="form-control" id="AddressCustomer" name="AddressCustomer" placeholder="Enter Addres Customer"
                                                    value="" required autocomplete="off"></textarea> --}}

                                                <input type="text" class="form-control" id="AddressCustomer"
                                                    name="AddressCustomer">
                                            </div>

                                            <div class="form-group local-forms">
                                                <label for="company-contact">Template Message <span
                                                        class="login-danger">*</span></label>


                                                <textarea class="form-control" id="TemplateMessage" name="TemplateMessage" placeholder="Enter Addres Customer"
                                                    required autocomplete="off">Hello, <NAME> here is your address : <ADDRESS> and your email : <EMAIL> and your vehicle is <VEHICLE>          
                                                </textarea>

                                            </div>

                                            <input type="hidden" name="IdCustomer" id="IdCustomer" value="">
                                            <input type="hidden" name="Latitude" id="Latitude" value="">
                                            <input type="hidden" name="Longitude" id="Longitude" value="">
                                        </div>
                                        <div class="col-lg-6">
                                            <div id="map"></div>
                                        </div>
                                        <div class="col-lg-6">

                                        </div>
                                    </div>
                                </form>
                                <div class="row">
                                    <div class="col text-end">
                                        <a id="btnCopyAddress" class="btn clip-btn btn-primary" href="javascript:;"
                                            data-clipboard-action="copy" data-clipboard-target="#CopyPersonalDetails"><i
                                                class="far fa-copy"></i>
                                            Copy from Input</a>
                                        <button id='BtnShareFormPersonalDetails' class="btn btn-success"> Share <i
                                                class="fa-brands fa-whatsapp"></i></button>
                                        <a href="javascript: void(0);" class="btn btn-primary seller-next-btn-check">
                                            Next
                                            <i class="bx bx-chevron-right ms-1"></i></a>
                                        <a id="btnNextStep2" href="javascript: void(0);"
                                            class="btn btn-primary seller-next-btn d-none">
                                            Next
                                            <i class="bx bx-chevron-right ms-1"></i></a>
                                    </div>
                                </div>
                            </div>
                            <!-- tab pane -->
                            <div class="tab-pane" id="product-display">
                                <div>
                                    <div class="mb-4">
                                        <h5>Enter Your Order Detail</h5>
                                    </div>

                                    <div id="MapsDistributorRecomendation">
                                    </div>

                                    <h6 class="mt-3">Our Battery Recommendation</h6>
                                    <div class="row" id="ResultRecommendationBattery">
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <a href="javascript: void(0);" class="btn btn-primary seller-previous-btn"><i
                                                    class="bx bx-chevron-left me-1"></i> Previous</a>
                                        </div>

                                        <div class="col text-end">
                                            <button id='BtnShareBattery' class="btn btn-success"> Share <i
                                                    class="fa-brands fa-whatsapp"></i></button>
                                            <a href="javascript: void(0);" class="btn btn-primary product-next-btn">Next
                                                <i class="bx bx-chevron-right ms-1"></i>
                                            </a>
                                            <a id="btnNextStep3" href="javascript: void(0);"
                                                class="btn btn-primary seller-next-btn d-none">
                                                Next
                                                <i class="bx bx-chevron-right ms-1"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- tab pane -->
                            <div class="tab-pane" id="checkout">

                                <div>
                                    <div class="mb-4">
                                        <h5>Enter Your Order Detail</h5>
                                    </div>
                                    <div id="CheckoutPreview"></div>

                                    <div class="row">
                                        <div class="col">
                                            <a href="javascript: void(0);" class="btn btn-primary seller-previous-btn"><i
                                                    class="bx bx-chevron-left me-1"></i> Previous</a>
                                        </div>

                                        <div class="col text-end">
                                            <!-- <a href="javascript: void(0);" class="btn btn-success"> Share
                                                                                                            <i class="fa-brands fa-whatsapp"></i></a> -->
                                            <a id="btnNextStep4" href="javascript: void(0);"
                                                class="btn btn-primary seller-next-btn ">
                                                Next
                                                <i class="bx bx-chevron-right ms-1"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- tab pane -->
                            <div class="tab-pane" id="bank-detail">
                                <div>
                                    <div class="mb-4">
                                        <h5>Payment Details</h5>
                                    </div>
                                    <div id="PaymentPreview"></div>

                                    <div class="row">
                                        <div class="col">
                                            <a href="javascript: void(0);" class="btn btn-primary seller-previous-btn"><i
                                                    class="bx bx-chevron-left me-1"></i> Previous</a>
                                        </div>

                                        <div class="col text-end">
                                            <a href="javascript: void(0);" class="btn btn-success"> Share <i
                                                    class="fa-brands fa-whatsapp"></i></a>
                                            <a href="javascript: void(0);" class="btn btn-primary" data-bs-toggle="modal"
                                                data-bs-target=".confirmModal">Save
                                                Changes</a>
                                        </div>
                                    </div>
                                </div>
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

                let FullName = $('#FullName').val();
                let ContactNumber = $('#ContactNumber').val();
                let AddressCustomer = $('#AddressCustomer').val();
                let VehicleCustomer = $('#VehicleCustomer').val();
                let EmailCustomer = $('#EmailCustomer').val();
                let templateMessage = $('#TemplateMessage').val();

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

                if (VehicleCustomer == '') {
                    swal.fire("Error!", "Vehicle Customer is required", "error");
                    return;
                }

                if (EmailCustomer == '') {
                    swal.fire("Error!", "Email Customer is required", "error");
                    return;
                }

                if (ContactNumber.substring(0, 1) != '8') {
                    swal.fire("Error!", "Contact Number must start with 8", "error");
                    return;
                }

                if (templateMessage.includes('<NAME>') == false || templateMessage.includes('<ADDRESS>') ==
                    false || templateMessage.includes('<EMAIL>') == false || templateMessage.includes(
                        '<VEHICLE>') == false) {
                    swal.fire("Error!",
                        "Template Message must contain NAME, ADDRESS, EMAIL, VEHICLE", "error");
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
                            url: "/share-form-personal-details",
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
                                };
                            }
                        });
                    };
                });

            });

            $('#FullName').on('keyup', function() {
                var input = $(this).val();
                if (input.length > 0) {
                    $.ajax({
                        url: "/find-customer",
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
                            url: "/find-vehicle-by-id",
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

                $('#btnNextStep2').trigger('click');

                // check jika button next step 2 berhasil di click
                if ($('#ProductDisplay').hasClass('active')) {
                    $.ajax({
                        url: "/find-vehicle-by-id-vehicle",
                        type: "GET",
                        data: {
                            id: VehicleCustomer,
                        },
                        success: function(data) {
                            var html = '';
                            data.forEach(function(vehicle) {
                                html +=
                                    '<div class="col-md-6 col-xl-4 col-sm-12 d-flex">';
                                html += '<div class="blog grid-blog flex-fill">';
                                html += '<div class="blog-image">';
                                html += '<a href="blog-details.html">';
                                if (vehicle.image == null) {

                                    vehicle.image =
                                        'https://via.placeholder.com/210x210';
                                    html += '<img class="img-fluid" src="' + vehicle
                                        .image + '" alt="Post Image">';
                                } else {
                                    var baseUrl =
                                        "{{ asset('storage/image/battery/') }}";
                                    vehicle.image = vehicle.image;
                                    html += '<img class="img-fluid" src="' + baseUrl +
                                        '/' + vehicle.image +
                                        '" alt="Post Image">';
                                }
                                html += '</a>';
                                html += '</div>';
                                html += '<div class="blog-content">';
                                html +=
                                    '<h3 class="blog-title"><a href="blog-details.html">' +
                                    vehicle.name + '</a></h3>';
                                html += '<p>Details & Specification :</p>';
                                html += '<ul class="list-group list-group-flush">';
                                html += '<li class="list-group-item">Warranty : ' +
                                    vehicle.warranty + ' Months</li>';

                                html += '<li class="list-group-item">Price : Rp. ' +
                                    vehicle.price_retail + '</li>';
                                html += '</ul>';
                                html += '</div>';
                                html += '<div class="row">';
                                html += '<div class="edit-options">';
                                html += '<div class="text-end inactive-style mt-3">';
                                html += '<div class="checkbox">';
                                html += '<label>';
                                html +=
                                    '<input type="checkbox" name="CheckBattery[]" value=' +
                                    vehicle.id + '> Share To Customer';
                                html += '</label>';
                                html += '</div>';
                                html += '</div>';
                                html += '</div>';
                                html += '</div>';
                                html += '</div>';
                                html += '</div>';
                            });
                            $('#ResultRecommendationBattery').html(html);
                            getMapsNearAddressCustomer();
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
                        url: "/get-maps-near-address-customer",
                        type: "GET",
                        data: data,
                        success: function(data) {
                            $("#MapsDistributorRecomendation").html(data);
                        }
                    });
                }
            });

            $("#BtnShareBattery").click(function() {

                $("#BtnShareBattery").prop('disabled', true);
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

                if (Battery.length == 0) {
                    swal.fire("Error!", "Please select battery", "error");
                    return;
                }

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
                        _token: $('meta[name="csrf-token"]').attr('content')
                    };

                    $.ajax({
                        url: "/share-battery",
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

                                $("#BtnShareBattery").prop('disabled', false);
                            } else {
                                Swal.fire({
                                    title: "Error",
                                    text: ResponseData.message ||
                                        "Something went wrong, please try again later",
                                    icon: "error",
                                });

                                $("#BtnShareBattery").prop('disabled', false);
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
                        _token: $('meta[name="csrf-token"]').attr('content')
                    };

                    $.ajax({
                        url: "/get-checkout-preview",
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
                    var Discount = $("#Discount").val();
                    var ExtraDiscount = $("#ExtraDiscount").val();

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
                        _token: $('meta[name="csrf-token"]').attr('content')
                    };

                    $.ajax({
                        url: "/get-payment-preview",
                        type: "GET",
                        data: data,
                        success: function(data) {
                            $("#PaymentPreview").html(data);
                        }
                    });
                }
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
                zoom: 15
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
            });
        }
    </script>
    <script async defer
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCAlBnX9jmy3JurAGnyIAFNSyS7i5cgfzA&libraries=places&callback=initMap">
    </script>


    <script src="{{ asset('/plugins/twitter-bootstrap-wizard/jquery.bootstrap.wizard.min.js') }}"></script>
    <script src="{{ asset('/plugins/twitter-bootstrap-wizard/prettify.js') }}"></script>
    <script src="{{ asset('/plugins/twitter-bootstrap-wizard/form-wizard.js') }}"></script>
    <br><br><br><br><br>
@endsection
