{{-- MOBILE VERSION --}}
<div class="d-block d-md-none">
    <div id="basic-pills-wizard" class="twitter-bs-wizard">
        <ul class="twitter-bs-wizard-nav nav nav-pills nav-justified">
            <li class="nav-item active" id="personal-details-mobile-li">
                <a href="#!" class="nav-link" data-toggle="tab">
                    <div class="step-icon" data-bs-toggle="tooltip" data-bs-placement="top"
                        aria-label="personal-details-mobile" data-bs-original-title="personal-details-mobile">
                        <i class="far fa-user"></i>
                    </div>
                </a>
                {{-- label --}}
                <span class="nav-label">STEP 1</span>
                <span class="nav-label">Personal Details</span>
            </li>
            <li class="nav-item" id="product-recommendation-mobile-li">
                <a href="#!" class="nav-link" data-toggle="tab">
                    <div class="step-icon" data-bs-toggle="tooltip" data-bs-placement="top"
                        aria-label="product-recommendation-mobile"
                        data-bs-original-title="product-recommendation-mobile">
                        <i class="fas fa-boxes-stacked"></i>
                    </div>
                </a>
                {{-- label --}}
                <span class="nav-label">STEP 2</span>
                <span class="nav-label">Product Recomendation Display</span>
            </li>
            <li class="nav-item" id="checkout-page-mobile-li">
                <a href="#!" class="nav-link" data-toggle="tab">
                    <div class="step-icon" data-bs-toggle="tooltip" data-bs-placement="top"
                        aria-label="checkout-page-mobile" data-bs-original-title="checkout-page-mobile">
                        <i class="fas fa-file-invoice"></i>
                    </div>
                </a>
                {{-- label --}}
                <span class="nav-label">STEP 3</span>
                <span class="nav-label">Checkout Page</span>
            </li>
            <li class="nav-item" id="payment-detail-mobile-li">
                <a href="#!" class="nav-link" data-toggle="tab">
                    <div class="step-icon" data-bs-toggle="tooltip" data-bs-placement="top"
                        aria-label="payment-detail-mobile" data-bs-original-title="payment-detail-mobile">
                        <i class="fas fa-money-bills"></i>
                    </div>
                </a>
                {{-- label --}}
                <span class="nav-label">STEP 4</span>
                <span class="nav-label">Payment Details</span>
            </li>
        </ul>

        <div class="tab-content twitter-bs-wizard-tab-content">
            <div class="tab-pane active" id="personal-details-mobile-tab" style="display: block; opacity: 1;">
                @include('Mobile.Orders.QuickQuotation.step-1')
            </div>

            <div class="tab-pane" id="product-recommendation-mobile-tab" style="display: none; opacity: 1;">
                @include('Mobile.Orders.QuickQuotation.step-2')
            </div>

            <div class="tab-pane" id="checkout-page-mobile-tab" style="display: none; opacity: 1;">
                @include('Mobile.Orders.QuickQuotation.step-3')
            </div>

            <div class="tab-pane" id="payment-detail-mobile-tab" style="display: none; opacity: 1;">
                @include('Mobile.Orders.QuickQuotation.step-4')
            </div>
        </div>
    </div>
</div>
{{-- END MOBILE VERSION --}}




{{-- FORM WIZARD CONTROL --}}
<script>
    // add custom css to owl dots
    $('.owl-dots').css('text-align', 'justify');

    function validateMoveTab() {
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
            return false;
        }

        var distributorChecked = $('#distributor_input_mobile').val();

        if (distributorChecked == '') {
            swal.fire("Error!", "Please select distributor", "error");
            return false;
        }

        if (FullName == '') {
            swal.fire("Error!", "Full Name is required", "error");
            return false;
        }

        if (ContactNumber == '') {
            swal.fire("Error!", "Contact Number is required", "error");
            return false;
        }

        if (AddressCustomer == '') {
            swal.fire("Error!", "Address Customer is required", "error");
            return false;
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

        return true;
    }

    $("#personal-details-mobile-li").click(function() {
        toggleTab("#personal-details-mobile-li", "#personal-details-mobile-tab");
    });

    $("#product-recommendation-mobile-li").click(function() {
        toggleTab("#product-recommendation-mobile-li", "#product-recommendation-mobile-tab");
    });

    $("#checkout-page-mobile-li").click(function() {
        toggleTab("#checkout-page-mobile-li", "#checkout-page-mobile-tab");
    });

    $("#payment-detail-mobile-li").click(function() {
        toggleTab("#payment-detail-mobile-li", "#payment-detail-mobile-tab");
    });

    function toggleTab(activeTab, activeContent) {
        if (validateMoveTab()) {
            $(".nav-item").removeClass("active");
            $(".tab-pane").css("display", "none");

            $(activeTab).addClass("active");
            $(activeContent).css("display", "block");
        }
    }
</script>
