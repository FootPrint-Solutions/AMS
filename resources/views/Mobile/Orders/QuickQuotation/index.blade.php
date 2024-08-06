{{-- MOBILE VERSION --}}
<div class="d-block d-md-none">
    <div id="basic-pills-wizard" class="twitter-bs-wizard">
        <ul class="twitter-bs-wizard-nav nav nav-pills nav-justified">
            <li class="nav-item active" id="personal-details-mobile-li">
                <a href="#personal-details-mobile" class="nav-link" data-toggle="tab">
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
                <a href="#product-recommendation-mobile" class="nav-link" data-toggle="tab">
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
                <a href="#checkout-page-mobile" class="nav-link" data-toggle="tab">
                    <div class="step-icon" data-bs-toggle="tooltip" data-bs-placement="top"
                        aria-label="checkout-page-mobile" data-bs-original-title="checkout-page-mobile">
                        <i class="fas fa-file-invoice"></i>
                    </div>
                </a>
                {{-- label --}}
                <span class="nav-label">STEP 3</span>
                <span class="nav-label">Checkout Page</span>
            </li>
            <li class="nav-item">
                <a href="#payment-detail-mobile" class="nav-link" data-toggle="tab">
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
        </div>
    </div>
</div>
{{-- END MOBILE VERSION --}}




{{-- FORM WIZARD CONTROL --}}
<script>
    // add custom css to owl dots
    $('.owl-dots').css('text-align', 'justify');

    // recomendation-display-mobile-next-button-lower click event
</script>
