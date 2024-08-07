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

    $("#personal-details-mobile-li").click(function() {
        $("#personal-details-mobile-li").addClass("active");
        $("#personal-details-mobile-tab").css("display", "block");

        $("#product-recommendation-mobile-li").removeClass("active");
        $("#product-recommendation-mobile-tab").css("display", "none");

        $("#checkout-page-mobile-li").removeClass("active");
        $("#checkout-page-mobile-tab").css("display", "none");

        $("#payment-detail-mobile-li").removeClass("active");
        $("#payment-detail-mobile-tab").css("display", "none");
    });

    $("#product-recommendation-mobile-li").click(function() {
        // trigger click personal-details-mobile-next-button-lower button 
        $("#personal-details-mobile-next-button-lower").trigger("click");
    });

    $("#checkout-page-mobile-li").click(function() {
        // trigger click recomendation-display-mobile-next-button-lower button
        $("#recomendation-display-mobile-next-button-lower").trigger("click");
    });

    $("#payment-detail-mobile-li").click(function() {
        // trigger checkout-mobile-next-button-lower button 
        $("#checkout-mobile-next-button-lower").trigger("click");
    });
</script>
