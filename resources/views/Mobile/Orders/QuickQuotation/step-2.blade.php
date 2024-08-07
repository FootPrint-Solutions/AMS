<div>
    <div class="mb-4">
        <h5>Enter Your Address</h5>
    </div>
    <form>
        <div class="row">
            <div class="col-lg-6">
                <div class="mb-3">
                    <label class="form-label">Full Name</label>
                    <input type="text" class="form-control" id="full_name_input_mobile" name="full_name_input_mobile">
                </div>
            </div>
            <div class="col-lg-6">
                <div class="mb-3">
                    <label class="form-label">Contact Number</label>
                    <input type="text" class="form-control" id="contact_input_mobile" name="contact_input_mobile">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6">
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" id="email_input_mobile" name="email_input_mobile">
                </div>
            </div>
            <div class="col-lg-6">
                <div class="mb-3">
                    <label class="form-label">Customer
                        Address</label>
                    <input type="text" class="form-control" id="address_input_mobile" name="address_input_mobile">
                </div>

                {{-- <button type="button" class="btn btn-maps-select btn-sm">
                    <svg width="21" height="21" viewBox="0 0 21 21" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M11.475 20.4498V18.4498C12.175 18.3498 12.8542 18.1581 13.5125 17.8748C14.1709 17.5915 14.7917 17.2331 15.375 16.7998L16.825 18.2498C16.0417 18.8665 15.2 19.3623 14.3 19.7373C13.4 20.1123 12.4584 20.3498 11.475 20.4498ZM18.225 16.7998L16.825 15.3998C17.2584 14.8498 17.6084 14.2456 17.875 13.5873C18.1417 12.929 18.325 12.2331 18.425 11.4998H20.475C20.3417 12.5331 20.0875 13.4956 19.7125 14.3873C19.3375 15.279 18.8417 16.0831 18.225 16.7998ZM18.425 9.4998C18.325 8.7498 18.1417 8.04564 17.875 7.3873C17.6084 6.72897 17.2584 6.13314 16.825 5.5998L18.225 4.1998C18.8584 4.93314 19.3709 5.7498 19.7625 6.6498C20.1542 7.5498 20.3917 8.4998 20.475 9.4998H18.425ZM9.47502 20.4498C6.92502 20.1498 4.79586 19.0581 3.08752 17.1748C1.37919 15.2915 0.525024 13.0665 0.525024 10.4998C0.525024 7.91647 1.37919 5.68314 3.08752 3.7998C4.79586 1.91647 6.92502 0.833138 9.47502 0.549805V2.5498C7.47502 2.83314 5.81669 3.7248 4.50002 5.2248C3.18336 6.7248 2.52502 8.48314 2.52502 10.4998C2.52502 12.5165 3.18336 14.2706 4.50002 15.7623C5.81669 17.254 7.47502 18.1498 9.47502 18.4498V20.4498ZM15.425 4.1998C14.825 3.7498 14.1917 3.38314 13.525 3.0998C12.8584 2.81647 12.175 2.63314 11.475 2.5498V0.549805C12.4584 0.633138 13.4 0.862305 14.3 1.2373C15.2 1.6123 16.0417 2.11647 16.825 2.7498L15.425 4.1998ZM10.5 15.4998C9.53336 14.6831 8.62502 13.8081 7.77502 12.8748C6.92502 11.9415 6.50002 10.8498 6.50002 9.5998C6.50002 8.46647 6.88752 7.4998 7.66252 6.6998C8.43752 5.8998 9.38336 5.4998 10.5 5.4998C11.6167 5.4998 12.5625 5.8998 13.3375 6.6998C14.1125 7.4998 14.5 8.46647 14.5 9.5998C14.5 10.8498 14.075 11.9415 13.225 12.8748C12.375 13.8081 11.4667 14.6831 10.5 15.4998ZM10.5 10.4998C10.8 10.4998 11.0542 10.3956 11.2625 10.1873C11.4709 9.97897 11.575 9.7248 11.575 9.4248C11.575 9.14147 11.4709 8.89147 11.2625 8.6748C11.0542 8.45814 10.8 8.3498 10.5 8.3498C10.2 8.3498 9.94586 8.45814 9.73752 8.6748C9.52919 8.89147 9.42502 9.14147 9.42502 9.4248C9.42502 9.7248 9.52919 9.97897 9.73752 10.1873C9.94586 10.3956 10.2 10.4998 10.5 10.4998Z"
                            fill="#FDFFFE" />
                    </svg>

                    Choose from Maps
                </button> --}}
                <input type="hidden" name="latitude_input_mobile" id="latitude_input_mobile">
                <input type="hidden" name="longitude_input_mobile" id="longitude_input_mobile">
            </div>

            <div class="row mt-3">
                <div class="col-10">
                    <div class="mb-3">
                        <label class="form-label">Distributor Shop</label>
                        <select class="form-select" id="distributor_input_mobile" name="distributor_input_mobile" required>
                            <option value="">-- Choose Distributor --</option>
                        </select>
                    </div>
                </div>
                <div class="col-1" style="margin-top: 29px;">
                    <button type="button" class="btn btn-maps-select btn-md" onclick="showMapsDistributorMobile()">
                        <svg width="18" height="26" viewBox="0 0 18 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9 25.5C6.79167 25.5 4.98958 25.151 3.59375 24.4531C2.19792 23.7552 1.5 22.8542 1.5 21.75C1.5 21.25 1.65104 20.7865 1.95312 20.3594C2.25521 19.9323 2.67708 19.5625 3.21875 19.25L5.1875 21.0938C5 21.1771 4.79688 21.2708 4.57812 21.375C4.35938 21.4792 4.1875 21.6042 4.0625 21.75C4.33333 22.0833 4.95833 22.375 5.9375 22.625C6.91667 22.875 7.9375 23 9 23C10.0625 23 11.0885 22.875 12.0781 22.625C13.0677 22.375 13.6979 22.0833 13.9688 21.75C13.8229 21.5833 13.6354 21.4479 13.4062 21.3438C13.1771 21.2396 12.9583 21.1458 12.75 21.0625L14.6875 19.1875C15.2708 19.5208 15.7188 19.901 16.0312 20.3281C16.3438 20.7552 16.5 21.2292 16.5 21.75C16.5 22.8542 15.8021 23.7552 14.4062 24.4531C13.0104 25.151 11.2083 25.5 9 25.5ZM9.03125 18.625C11.0938 17.1042 12.6458 15.5781 13.6875 14.0469C14.7292 12.5156 15.25 10.9792 15.25 9.4375C15.25 7.3125 14.5729 5.70833 13.2188 4.625C11.8646 3.54167 10.4583 3 9 3C7.54167 3 6.13542 3.54167 4.78125 4.625C3.42708 5.70833 2.75 7.3125 2.75 9.4375C2.75 10.8333 3.26042 12.2865 4.28125 13.7969C5.30208 15.3073 6.88542 16.9167 9.03125 18.625ZM9 21.75C6.0625 19.5833 3.86979 17.4792 2.42188 15.4375C0.973958 13.3958 0.25 11.3958 0.25 9.4375C0.25 7.95833 0.515625 6.66146 1.04688 5.54688C1.57812 4.43229 2.26042 3.5 3.09375 2.75C3.92708 2 4.86458 1.4375 5.90625 1.0625C6.94792 0.6875 7.97917 0.5 9 0.5C10.0208 0.5 11.0521 0.6875 12.0938 1.0625C13.1354 1.4375 14.0729 2 14.9062 2.75C15.7396 3.5 16.4219 4.43229 16.9531 5.54688C17.4844 6.66146 17.75 7.95833 17.75 9.4375C17.75 11.3958 17.026 13.3958 15.5781 15.4375C14.1302 17.4792 11.9375 19.5833 9 21.75ZM9 11.75C9.6875 11.75 10.276 11.5052 10.7656 11.0156C11.2552 10.526 11.5 9.9375 11.5 9.25C11.5 8.5625 11.2552 7.97396 10.7656 7.48438C10.276 6.99479 9.6875 6.75 9 6.75C8.3125 6.75 7.72396 6.99479 7.23438 7.48438C6.74479 7.97396 6.5 8.5625 6.5 9.25C6.5 9.9375 6.74479 10.526 7.23438 11.0156C7.72396 11.5052 8.3125 11.75 9 11.75Z" fill="white" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </form>
    <ul class="pager wizard twitter-bs-wizard-pager-link d-none">
        <li class="previous disabled"><a href="javascript: void(0);" class="btn btn-primary seller-previous-btn"><i class="bx bx-chevron-left me-1"></i>
                Previous</a></li>
        <li class="next"><a href="javascript: void(0);" class="btn btn-primary seller-next-btn" id="recomendation-display-mobile-next-button">Next <i class="bx bx-chevron-right ms-1"></i></a></li>
    </ul>

    {{-- sample owl carousel --}}
    <div id="ResultRecommendationBatteryVehicleMobile2"></div>
    <div class="owl-carousel owl-theme loop2" id="owl-carousel2">
    </div>
    {{-- end sample owl carousel --}}

    <div class="bottom-buttons pager wizard twitter-bs-wizard-pager-link">
        {{-- share button --}}
        <button class="btn btn-custom btn-whatsapp" id="btn-share-whatsapp-step-2-mobile">
            <i class="fa-brands fa-whatsapp"></i>
            Share
        </button>
        {{-- next button --}}
        <button id="recomendation-display-mobile-next-button-lower" class="btn btn-custom btn-next next" href="javascript: void(0);">Next
            <i class="fa fa-chevron-right"></i>
        </button>
    </div>
</div>

{{-- Modal Maps Distributor Mobile --}}
<div class="modal fade" id="modalMapsDistributorMobile" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Maps Distributor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div id="MapsDistributorRecomendationMobile"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div id="map-mobile"></div>


<script>
    function showMapsDistributorMobile() {
        var address = $('#address_input_mobile').val();
        var latitude = $('#latitude_input_mobile').val();
        var longitude = $('#longitude_input_mobile').val();
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
                $("#MapsDistributorRecomendationMobile").html(data);
                $("#modalMapsDistributorMobile").modal('show');
            }
        });
    }

    // get distributor and insert to select option
    $(document).ready(function() {
        $.ajax({
            url: "/quotation/distributor/find",
            type: "GET",
            success: function(data) {
                var html = '<option value="">Select Distributor</option>';
                data.forEach(function(distributor) {
                    html += '<option value="' + distributor.id + '">' +
                        distributor.name + '</option>';
                });
                $('#distributor_input_mobile').html(html);
            }
        });
    });

    $("#btn-share-whatsapp-step-2-mobile").click(function() {
        var button = $(this);
        button.prop('disabled', true);
        button.html(
            '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...'
        );
        var FullName = $("#full_name_input_mobile").val();
        var EmailCustomer = $("#email_input_mobile").val();
        var VehicleCustomer = $("#vehicle_customer_input_mobile").val();
        var Battery = [];
        $('.btn-owl-carousel-step-2').each(function() {
            if ($(this).data('check') == 1) {
                Battery.push($(this).data('id'));
            }
        });
        var contactNumber = $("#contact_input_mobile").val();

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

        // jika nomer bukan diawali dengan angka 8
        if (contactNumber.charAt(0) != '8') {
            swal.fire("Error!", "Contact Number must start with 8", "error");
            button.prop('disabled', false);
            button.html(
                "<i class='fa-brands fa-whatsapp'></i> Share"
            );
            return;
        }

        Battery.forEach(function(battery) {
            var data = {
                FullName: FullName,
                Battery: battery,
                ContactNumber: contactNumber,
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

    $('#recomendation-display-mobile-next-button-lower').click(function() {

        var FullName = $("#full_name_input_mobile").val();
        var EmailCustomer = $("#email_input_mobile").val();
        var ContactNumber = $("#contact_input_mobile").val();
        var AddressCustomer = $("#address_input_mobile").val();
        var VehicleCustomer = $("#vehicle_customer_input_mobile").val();
        var Battery = [];
        $('.btn-owl-carousel-step-2').each(function() {
            if ($(this).data('check') == 1) {
                Battery.push($(this).data('id'));
            }
        });

        if (Battery.length == 0) {
            swal.fire("Error!", "Please select battery", "error");
            return;
        }

        var distributorChecked = $('#distributor_input_mobile').val();

        if (distributorChecked == '') {
            swal.fire("Error!", "Please select distributor", "error");
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

        var DistributorShopId = $('#distributor_input_mobile').val();

        if (DistributorShopId == '') {
            swal.fire("Error!", "Please select distributor", "error");
            return false;
        }

        // jika contact number tidak diawali dengan 8
        if (ContactNumber.substring(0, 1) != '8') {
            swal.fire("Error!", "Contact Number must start with 8", "error");
            return false;
        }

        var data = {
            FullName: FullName,
            EmailCustomer: EmailCustomer,
            ContactNumber: ContactNumber,
            AddressCustomer: AddressCustomer,
            VehicleCustomer: VehicleCustomer,
            Latitude: $('#latitude_input_mobile').val(),
            Longitude: $('#longitude_input_mobile').val(),
            IdCustomer: $('#IdCustomer').val(),
            Battery: Battery,
            _token: $('meta[name="csrf-token"]').attr('content'),
            DistributorShopId: DistributorShopId
        };

        $.ajax({
            url: "/quotation/mobile/checkout",
            type: "post",
            data: data,
            success: function(response) {
                if (response.success == true) {
                    // move to next tab step 3
                    $("#personal-details-mobile-li").removeClass("active");
                    $("#personal-details-mobile-tab").css("display", "none");
                    $("#product-recommendation-mobile-li").removeClass("active");
                    $("#product-recommendation-mobile-tab").css("display", "none");
                    $("#checkout-page-mobile-li").addClass("active");
                    $("#checkout-page-mobile-tab").css("display", "block");

                    // set value to checkout page
                    $("#full_name_customer_checkout_mobile").text(response.data.Fullname);
                    $("#email_customer_checkout_mobile").text(response.data.EmailCustomer);
                    $("#number_customer_checkout_mobile").text(response.data.ContactNumber);
                    $("#address_customer_checkout_mobile").text(response.data.AddressCustomer);
                    $("#vehicle_customer_checkout_mobile").text(response.data.VehicleCustomer.join(
                        ", "));
                    $("#distributor_shop_name_checkout_mobile").text(response.data
                        .Ditributor.name);
                    $("#address_distributor_checkout_mobile").text(response.data.Ditributor
                        .address);
                    // set technician to select option technicians_checkout_mobile
                    var htmltech = '<option value="" data-phone="">Select Technicians</option>';
                    response.data.DistributorTechnician.forEach(function(technician) {
                        htmltech += '<option value="' + technician.id + '" data-phone="62' +
                            technician.contact + '" >' + technician
                            .name + '</option>';
                    });
                    $('#technicians_checkout_mobile').html(htmltech);

                    var html = '';
                    response.data.Battery.forEach(function(battery) {
                        // price net battery prices 
                        var batteryPrices = battery.battery_prices[0];
                        var FormatPriceNet = new Intl.NumberFormat('id-ID', {
                            style: 'currency',
                            currency: 'IDR'
                        }).format(batteryPrices.price_net);

                        html += '<div class="item-detail d-flex align-items-center">';
                        html += '<div class="ms-3 flex-grow-1">';
                        html += '<h5 class="mb-1">' + battery.name + '</h5>';
                        html += '<p class="mb-1">' + FormatPriceNet +
                            '</p>';
                        // input type hidden battery name
                        html +=
                            '<input type="hidden" name="battery_name_checkout_mobile[]" id="battery_name_checkout_mobile_' +
                            battery.id + '" value="' + battery.name + '">';
                        // input type hidden price net
                        html +=
                            '<input type="hidden" name="price_net_checkout_mobile[]" id="price_net_checkout_mobile_' +
                            battery.id + '" value="' +
                            batteryPrices.price_net + '">';
                        // input type hidden battery id 
                        html +=
                            '<input type="hidden" name="battery_id_checkout_mobile[]" id="battery_id_checkout_mobile_' +
                            battery.id + '" value="' +
                            battery.id + '">';
                        // input type hidden qty battery
                        html +=
                            '<input type="hidden" name="qty_checkout_mobile[]" id="qty_checkout_mobile_' +
                            battery.id + '" value="1">';
                        // input type subtotal battery
                        html +=
                            '<input type="hidden" name="subtotal_checkout_mobile[]" id="subtotal_checkout_mobile_' +
                            battery.id + '" value="' + batteryPrices.price_net +
                            '" class="subtotal_checkout_mobile">';
                        html +=
                            '<button class="btn btn-sm btn-rounded btn-dark-blue" id="btn-detail-battery-checkout-mobile" data-id="' +
                            battery.id + '" onclick="btn_detail_battery_checkout(this)">';
                        html +=
                            '<svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">';
                        html +=
                            '<path d="M8.08489 9.58487C8.47131 9.58487 8.79822 9.4577 9.06561 9.20336C9.3329 8.94902 9.46654 8.63771 9.46654 8.26943C9.46654 7.90106 9.33314 7.58947 9.06633 7.33466C8.79953 7.07977 8.47297 6.95232 8.08665 6.95232C7.70022 6.95232 7.37337 7.07949 7.10608 7.33383C6.83869 7.58817 6.705 7.89948 6.705 8.26776C6.705 8.63613 6.8384 8.94772 7.1052 9.20253C7.372 9.45742 7.69857 9.58487 8.08489 9.58487ZM11.0665 12L9.47443 10.493C9.26987 10.6093 9.04982 10.6983 8.81429 10.7599C8.57866 10.8216 8.33582 10.8524 8.08577 10.8524C7.33289 10.8524 6.69297 10.6012 6.16598 10.0988C5.63891 9.59639 5.37537 8.9863 5.37537 8.26859C5.37537 7.55089 5.63891 6.9408 6.16598 6.43834C6.69297 5.93597 7.33289 5.68479 8.08577 5.68479C8.83864 5.68479 9.47862 5.93597 10.0057 6.43834C10.5327 6.9408 10.7962 7.55089 10.7962 8.26859C10.7962 8.51541 10.762 8.75077 10.6936 8.97465C10.6252 9.19853 10.5301 9.40798 10.4081 9.60298L12 11.1101L11.0665 12ZM1.32963 11.3696C0.961621 11.3696 0.648013 11.246 0.388808 10.9989C0.129602 10.7518 0 10.4529 0 10.1021V1.26753C0 0.916705 0.129602 0.617745 0.388808 0.370647C0.648013 0.123549 0.961621 0 1.32963 0H6.01227L9.58884 3.40951V4.85909C9.35038 4.764 9.10559 4.69213 8.85447 4.64347C8.60325 4.59481 8.34702 4.57048 8.08577 4.57048C7.54622 4.57048 7.04281 4.66464 6.57554 4.85296C6.10827 5.04137 5.70071 5.30532 5.35286 5.64481H2.4566V6.80913H4.52309C4.44516 6.97665 4.38374 7.14936 4.33884 7.32728C4.29383 7.50529 4.25925 7.68734 4.2351 7.87343H2.4566V9.03775H4.29471C4.39494 9.51978 4.58747 9.96235 4.8723 10.3654C5.15712 10.7686 5.52216 11.1034 5.96742 11.3696H1.32963ZM5.33694 4.0533H8.2592L5.33694 1.26753V4.0533Z" fill="white" />';
                        html += '</svg>';
                        html += 'Lihat Detail</button>';
                        html += '</div>';
                        html += '<div class="item-qty">';
                        html +=
                            '<button class="btn btn-outline-secondary btn-control-rounded btn-min-qty-checkout-mobile" data-id="' +
                            battery.id +
                            '" id="btn-min-qty-checkout-mobile" onclick="min_qty_checkout(this)">-</button>';
                        html += '<span class="mx-2" id="qty_' + battery.id + '">1</span>';
                        html +=
                            '<button class="btn btn-outline-secondary btn-control-rounded btn-add-qty-checkout-mobile" data-id="' +
                            battery.id +
                            '" id="btn-add-qty-checkout-mobile" onclick="add_qty_checkout(this)">+</button>';
                        html += '</div>';
                        html += '</div>';
                    });

                    $("#battery_customer_checkout_mobile").html(html);


                    // sum all price_net_checkout_mobile  and set to subtotal_checkout_mobile
                    var sum = 0;
                    $('input[name^="price_net_checkout_mobile"]').each(function() {
                        sum += Number($(this).val());
                    });
                    var FormatSumPriceNet = new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR'
                    }).format(sum);

                    $("#subtotal_checkout_mobile").text(FormatSumPriceNet);
                    $("#subtotal_hidden_checkout_mobile").val(sum);

                    $("#total_amount_hidden_checkout_mobile").val(sum);
                    $("#total_amount_checkout_mobile").text(FormatSumPriceNet);
                } else {
                    swal.fire("Error!", data.message, "error");
                }
            }
        });


    });
</script>


<script>
    var map;
    var marker;

    function initMap() {
        map = new google.maps.Map(document.getElementById('map-mobile'), {
            center: {
                lat: -6.8837859188198784,
                lng: 107.5403487263912
            },
            zoom: 17
        });

        var input = document.getElementById('address_input_mobile');
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


            document.getElementById('address_input_mobile').value = address;
            document.getElementById('latitude_input_mobile').value = latitude;
            document.getElementById('longitude_input_mobile').value = longitude;
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


                        document.getElementById('address_input_mobile').value = address;
                        document.getElementById('latitude_input_mobile').value = latitude;
                        document.getElementById('longitude_input_mobile').value = longitude;
                    }
                } else {
                    console.error('Geocoder failed due to: ' + status);
                }
            });

            // panggil auto complete
            var input = document.getElementById('address_input_mobile');
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


                document.getElementById('address_input_mobile').value = address;
                document.getElementById('latitude_input_mobile').value = latitude;
                document.getElementById('longitude_input_mobile').value = longitude;
            });
        });
    }
</script>