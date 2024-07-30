{{-- MOBILE VERSION --}}
<div class="d-block d-md-none">
    <div id="basic-pills-wizard" class="twitter-bs-wizard">
        <ul class="twitter-bs-wizard-nav nav nav-pills nav-justified">
            <li class="nav-item active">
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
            <li class="nav-item">
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
            <li class="nav-item">
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
            <div class="tab-pane active" id="seller-details" style="display: block;">
                <div class="mb-4">
                    <h4>Enter Your Vehicle Details</h4>
                </div>
                <form>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="basicpill-firstname-input" class="form-label">Members Name</label>
                                <input type="text" class="form-control" id="members_name_mobile"
                                    name="members_name_mobile" required>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="basicpill-lastname-input" class="form-label">Vehicle Customer</label>
                                <input type="text" class="form-control" id="vehicle_customer"
                                    name="vehicle_customer">
                            </div>
                        </div>
                    </div>
                </form>
                <ul class="pager wizard twitter-bs-wizard-pager-link d-none">
                    <li class="next">
                        <a id="personal-details-mobile-next-button" href="javascript: void(0);"
                            class="btn btn-primary seller-next-btn">Next
                            <i class="bx bx-chevron-right ms-1"></i>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="tab-pane" id="company-document" style="display: none; opacity: 1;">
                <div>
                    <div class="mb-4">
                        <h5>Enter Your Address</h5>
                    </div>
                    <form>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="basicpill-pancard-input" class="form-label">Address 1</label>
                                    <input type="text" class="form-control" id="basicpill-pancard-input">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="basicpill-vatno-input" class="form-label">Address 2</label>
                                    <input type="text" class="form-control" id="basicpill-vatno-input">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="basicpill-cstno-input" class="form-label">Landmark</label>
                                    <input type="text" class="form-control" id="basicpill-cstno-input">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="basicpill-servicetax-input" class="form-label">Town</label>
                                    <input type="text" class="form-control" id="basicpill-servicetax-input">
                                </div>
                            </div>
                        </div>
                    </form>
                    <ul class="pager wizard twitter-bs-wizard-pager-link">
                        <li class="previous disabled"><a href="javascript: void(0);"
                                class="btn btn-primary seller-previous-btn"><i class="bx bx-chevron-left me-1"></i>
                                Previous</a></li>
                        <li class="next"><a href="javascript: void(0);"
                                class="btn btn-primary seller-next-btn">Next <i
                                    class="bx bx-chevron-right ms-1"></i></a></li>
                    </ul>
                </div>
            </div>

            <div class="tab-pane" id="bank-detail" style="display: none; opacity: 1;">
                <div>
                    <div class="mb-4">
                        <h5>Payment Details</h5>
                    </div>
                    <form>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="basicpill-namecard-input" class="form-label">Name on Card</label>
                                    <input type="text" class="form-control" id="basicpill-namecard-input">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label">Credit Card Type</label>
                                    <select class="form-select">
                                        <option selected="">Select Card Type</option>
                                        <option value="AE">American Express</option>
                                        <option value="VI">Visa</option>
                                        <option value="MC">MasterCard</option>
                                        <option value="DI">Discover</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="basicpill-cardno-input" class="form-label">Credit Card Number</label>
                                    <input type="text" class="form-control" id="basicpill-cardno-input">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="basicpill-card-verification-input" class="form-label">Card
                                        Verification Number</label>
                                    <input type="text" class="form-control"
                                        id="basicpill-card-verification-input">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="basicpill-expiration-input" class="form-label">Expiration Date</label>
                                    <input type="text" class="form-control" id="basicpill-expiration-input">
                                </div>
                            </div>
                        </div>
                    </form>
                    <ul class="pager wizard twitter-bs-wizard-pager-link">
                        <li class="previous disabled"><a href="javascript: void(0);"
                                class="btn btn-primary seller-previous-btn"><i class="bx bx-chevron-left me-1"></i>
                                Previous</a></li>
                        <li class="float-end"><a href="javascript: void(0);" class="btn btn-primary"
                                data-bs-toggle="modal" data-bs-target=".confirmModal">Save
                                Changes</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <h4 class="mb-3 mt-3">Product Recomendation Display
        <button class="btn btn-copy-text-mobile">
            <svg width="20" height="23" viewBox="0 0 20 23" fill="none"
                xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M2.54771 22.6854C1.91804 22.6854 1.38145 22.4637 0.937955 22.0202C0.494455 21.5769 0.272705 21.0403 0.272705 20.4104V6.48219H2.54771V20.4104H13.6017V22.6854H2.54771ZM6.82271 18.4104C6.19304 18.4104 5.65645 18.1887 5.21295 17.7452C4.76945 17.3019 4.54771 16.7653 4.54771 16.1354V2.58994C4.54771 1.96011 4.76945 1.42352 5.21295 0.980191C5.65645 0.536691 6.19304 0.314941 6.82271 0.314941H13.7392L19.8587 6.43444V16.1354C19.8587 16.7653 19.637 17.3019 19.1935 17.7452C18.75 18.1887 18.2134 18.4104 17.5837 18.4104H6.82271ZM12.5837 7.58994H17.5837L12.5837 2.58994V7.58994Z"
                    fill="white" />
            </svg>
        </button>
    </h4>

    {{-- sample owl carousel --}}
    <div class="owl-carousel owl-theme loop">
        <div class="item product-card">
            <img src="https://via.placeholder.com/150" alt="" class="image-carousel">
            <div class="row mt-3">
                <div class="col">
                    <div class="text-carousell">
                        AMARON GO 46B24R
                    </div>
                </div>
                <div class="col-4">
                    <button class="btn btn-dark btn-sm btn-circle">+</button>
                </div>
            </div>
        </div>
        <div class="item product-card">
            <img src="https://via.placeholder.com/150" alt="" class="image-carousel">
            <div class="row mt-3">
                <div class="col">
                    <div class="text-carousell">
                        AMARON GO 46B24R
                    </div>
                </div>
                <div class="col-4">
                    <button class="btn btn-dark btn-sm btn-circle">+</button>
                </div>
            </div>
        </div>
        <div class="item product-card">
            <img src="https://via.placeholder.com/150" alt="" class="image-carousel">
            <div class="row mt-3">
                <div class="col">
                    <div class="text-carousell">
                        AMARON GO 46B24R
                    </div>
                </div>
                <div class="col-4">
                    <button class="btn btn-dark btn-sm btn-circle">+</button>
                </div>
            </div>
        </div>
        <div class="item product-card">
            <img src="https://via.placeholder.com/150" alt="" class="image-carousel">
            <div class="row mt-3">
                <div class="col">
                    <div class="text-carousell">
                        AMARON GO 46B24R
                    </div>
                </div>
                <div class="col-4">
                    <button class="btn btn-dark btn-sm btn-circle">+</button>
                </div>
            </div>
        </div>
        <div class="item product-card">
            <img src="https://via.placeholder.com/150" alt="" class="image-carousel">
            <div class="row mt-3">
                <div class="col">
                    <div class="text-carousell">
                        AMARON GO 46B24R
                    </div>
                </div>
                <div class="col-4">
                    <button class="btn btn-dark btn-sm btn-circle">+</button>
                </div>
            </div>
        </div>
        <div class="item product-card">
            <img src="https://via.placeholder.com/150" alt="" class="image-carousel">
            <div class="row mt-3">
                <div class="col">
                    <div class="text-carousell">
                        AMARON GO 46B24R
                    </div>
                </div>
                <div class="col-4">
                    <button class="btn btn-dark btn-sm btn-circle">+</button>
                </div>
            </div>
        </div>
    </div>
    {{-- end sample owl carousel --}}

    <div class="bottom-buttons pager wizard twitter-bs-wizard-pager-link">
        {{-- share button --}}
        <button class="btn btn-custom btn-whatsapp">
            <i class="fa-brands fa-whatsapp"></i>
            Share
        </button>
        {{-- next button --}}
        <button id="personal-details-mobile-next-button-lower" class="btn btn-custom btn-next next"
            href="javascript: void(0);">Next
            <i class="fa fa-chevron-right"></i>
        </button>
    </div>
</div>
{{-- END MOBILE VERSION --}}

{{-- Owl Carousel JS --}}
<script src="{{ asset('/plugins/owl-carousel/owl.carousel.min.js') }}"></script>


{{-- FUNCTION STEP 1 --}}
<script>
    // personal-details-mobile-next-button click event
    $('#members_name_mobile').on('keyup', function() {

    });
</script>


{{-- FORM WIZARD CONTROL --}}
<script>
    $('.loop').owlCarousel({
        center: true,
        items: 2,
        loop: true,
        margin: 10,
        dots: true,
        responsive: {
            600: {
                items: 4
            }
        },
    });

    // add custom css to owl dots
    $('.owl-dots').css('text-align', 'justify');

    // personal-details-mobile-next-button-lower click event
    $('#personal-details-mobile-next-button-lower').click(function() {
        $("#personal-details-mobile-next-button").click();
    });
</script>
