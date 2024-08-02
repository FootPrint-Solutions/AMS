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
                                <input type="text" class="form-control" id="members_name_input_mobile"
                                    name="members_name_input_mobile" required>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="basicpill-lastname-input" class="form-label">Vehicle Customer</label>
                                <input type="text" class="form-control" id="vehicle_customer_input_mobile"
                                    name="vehicle_customer_input_mobile">
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

                <h4 class=" mt-3">Product Recomendation Display
                    <button class="btn btn-copy-text-mobile" style="border-radius: 10px;">
                        <svg width="20" height="23" viewBox="0 0 20 23" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M2.54771 22.6854C1.91804 22.6854 1.38145 22.4637 0.937955 22.0202C0.494455 21.5769 0.272705 21.0403 0.272705 20.4104V6.48219H2.54771V20.4104H13.6017V22.6854H2.54771ZM6.82271 18.4104C6.19304 18.4104 5.65645 18.1887 5.21295 17.7452C4.76945 17.3019 4.54771 16.7653 4.54771 16.1354V2.58994C4.54771 1.96011 4.76945 1.42352 5.21295 0.980191C5.65645 0.536691 6.19304 0.314941 6.82271 0.314941H13.7392L19.8587 6.43444V16.1354C19.8587 16.7653 19.637 17.3019 19.1935 17.7452C18.75 18.1887 18.2134 18.4104 17.5837 18.4104H6.82271ZM12.5837 7.58994H17.5837L12.5837 2.58994V7.58994Z"
                                fill="white" />
                        </svg>
                    </button>
                    <button class="btn btn-copy-text-mobile" style="border-radius: 10px; background-color: #D9D9D9">
                        <svg width="21" height="21" viewBox="0 0 21 21" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M7.63743 12.3624H17.6374L14.1874 7.86243L11.8874 10.8624L10.3374 8.86243L7.63743 12.3624ZM6.70918 16.5657C6.07951 16.5657 5.54293 16.3439 5.09943 15.9004C4.65593 15.4569 4.43418 14.9203 4.43418 14.2907V2.43418C4.43418 1.80451 4.65593 1.26793 5.09943 0.824429C5.54293 0.380929 6.07951 0.15918 6.70918 0.15918H18.5657C19.1953 0.15918 19.7319 0.380929 20.1754 0.824429C20.6189 1.26793 20.8407 1.80451 20.8407 2.43418V14.2907C20.8407 14.9203 20.6189 15.4569 20.1754 15.9004C19.7319 16.3439 19.1953 16.5657 18.5657 16.5657H6.70918ZM2.43418 20.8407C1.80451 20.8407 1.26793 20.6189 0.82443 20.1754C0.38093 19.7319 0.15918 19.1953 0.15918 18.5657V4.43418H2.43418V18.5657H16.5657V20.8407H2.43418Z"
                                fill="#5F6368" />
                        </svg>
                    </button>
                </h4>
                <div class="checkbox-all mb-3">
                    <input type="checkbox" class="checbox-centang" /> <span class="text-grey">Select All</span>
                </div>

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

            <div class="tab-pane" id="company-document" style="display: none; opacity: 1;">
                <div>
                    <div class="mb-4">
                        <h5>Enter Your Address</h5>
                    </div>
                    <form>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="basicpill-pancard-input" class="form-label">Full Name</label>
                                    <input type="text" class="form-control" id="full_name_input_mobile"
                                        name="full_name_input_mobile">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="basicpill-vatno-input" class="form-label">Contact Number</label>
                                    <input type="text" class="form-control" id="contact_input_mobile"
                                        name="contact_input_mobile">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="basicpill-cstno-input" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email_input_mobiles"
                                        name="email_input_mobile">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="basicpill-servicetax-input" class="form-label">Customer
                                        Address</label>
                                    <input type="text" class="form-control" id="customer    ">
                                </div>

                                <button type="button" class="btn btn-maps-select btn-sm">
                                    <svg width="21" height="21" viewBox="0 0 21 21" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M11.475 20.4498V18.4498C12.175 18.3498 12.8542 18.1581 13.5125 17.8748C14.1709 17.5915 14.7917 17.2331 15.375 16.7998L16.825 18.2498C16.0417 18.8665 15.2 19.3623 14.3 19.7373C13.4 20.1123 12.4584 20.3498 11.475 20.4498ZM18.225 16.7998L16.825 15.3998C17.2584 14.8498 17.6084 14.2456 17.875 13.5873C18.1417 12.929 18.325 12.2331 18.425 11.4998H20.475C20.3417 12.5331 20.0875 13.4956 19.7125 14.3873C19.3375 15.279 18.8417 16.0831 18.225 16.7998ZM18.425 9.4998C18.325 8.7498 18.1417 8.04564 17.875 7.3873C17.6084 6.72897 17.2584 6.13314 16.825 5.5998L18.225 4.1998C18.8584 4.93314 19.3709 5.7498 19.7625 6.6498C20.1542 7.5498 20.3917 8.4998 20.475 9.4998H18.425ZM9.47502 20.4498C6.92502 20.1498 4.79586 19.0581 3.08752 17.1748C1.37919 15.2915 0.525024 13.0665 0.525024 10.4998C0.525024 7.91647 1.37919 5.68314 3.08752 3.7998C4.79586 1.91647 6.92502 0.833138 9.47502 0.549805V2.5498C7.47502 2.83314 5.81669 3.7248 4.50002 5.2248C3.18336 6.7248 2.52502 8.48314 2.52502 10.4998C2.52502 12.5165 3.18336 14.2706 4.50002 15.7623C5.81669 17.254 7.47502 18.1498 9.47502 18.4498V20.4498ZM15.425 4.1998C14.825 3.7498 14.1917 3.38314 13.525 3.0998C12.8584 2.81647 12.175 2.63314 11.475 2.5498V0.549805C12.4584 0.633138 13.4 0.862305 14.3 1.2373C15.2 1.6123 16.0417 2.11647 16.825 2.7498L15.425 4.1998ZM10.5 15.4998C9.53336 14.6831 8.62502 13.8081 7.77502 12.8748C6.92502 11.9415 6.50002 10.8498 6.50002 9.5998C6.50002 8.46647 6.88752 7.4998 7.66252 6.6998C8.43752 5.8998 9.38336 5.4998 10.5 5.4998C11.6167 5.4998 12.5625 5.8998 13.3375 6.6998C14.1125 7.4998 14.5 8.46647 14.5 9.5998C14.5 10.8498 14.075 11.9415 13.225 12.8748C12.375 13.8081 11.4667 14.6831 10.5 15.4998ZM10.5 10.4998C10.8 10.4998 11.0542 10.3956 11.2625 10.1873C11.4709 9.97897 11.575 9.7248 11.575 9.4248C11.575 9.14147 11.4709 8.89147 11.2625 8.6748C11.0542 8.45814 10.8 8.3498 10.5 8.3498C10.2 8.3498 9.94586 8.45814 9.73752 8.6748C9.52919 8.89147 9.42502 9.14147 9.42502 9.4248C9.42502 9.7248 9.52919 9.97897 9.73752 10.1873C9.94586 10.3956 10.2 10.4998 10.5 10.4998Z"
                                            fill="#FDFFFE" />
                                    </svg>

                                    Choose from Maps
                                </button>
                            </div>

                            <div class="row mt-3">
                                <div class="col-10">
                                    <div class="mb-3">
                                        <label for="basicpill-cstno-input" class="form-label">Distributor Shop</label>
                                        <input type="email" class="form-control" id="email_input_mobiles"
                                            name="email_input_mobile">
                                    </div>
                                </div>
                                <div class="col-1" style="margin-top: 35px;">
                                    <button type="button" class="btn btn-maps-select btn-md">
                                        <svg width="18" height="26" viewBox="0 0 18 26" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M9 25.5C6.79167 25.5 4.98958 25.151 3.59375 24.4531C2.19792 23.7552 1.5 22.8542 1.5 21.75C1.5 21.25 1.65104 20.7865 1.95312 20.3594C2.25521 19.9323 2.67708 19.5625 3.21875 19.25L5.1875 21.0938C5 21.1771 4.79688 21.2708 4.57812 21.375C4.35938 21.4792 4.1875 21.6042 4.0625 21.75C4.33333 22.0833 4.95833 22.375 5.9375 22.625C6.91667 22.875 7.9375 23 9 23C10.0625 23 11.0885 22.875 12.0781 22.625C13.0677 22.375 13.6979 22.0833 13.9688 21.75C13.8229 21.5833 13.6354 21.4479 13.4062 21.3438C13.1771 21.2396 12.9583 21.1458 12.75 21.0625L14.6875 19.1875C15.2708 19.5208 15.7188 19.901 16.0312 20.3281C16.3438 20.7552 16.5 21.2292 16.5 21.75C16.5 22.8542 15.8021 23.7552 14.4062 24.4531C13.0104 25.151 11.2083 25.5 9 25.5ZM9.03125 18.625C11.0938 17.1042 12.6458 15.5781 13.6875 14.0469C14.7292 12.5156 15.25 10.9792 15.25 9.4375C15.25 7.3125 14.5729 5.70833 13.2188 4.625C11.8646 3.54167 10.4583 3 9 3C7.54167 3 6.13542 3.54167 4.78125 4.625C3.42708 5.70833 2.75 7.3125 2.75 9.4375C2.75 10.8333 3.26042 12.2865 4.28125 13.7969C5.30208 15.3073 6.88542 16.9167 9.03125 18.625ZM9 21.75C6.0625 19.5833 3.86979 17.4792 2.42188 15.4375C0.973958 13.3958 0.25 11.3958 0.25 9.4375C0.25 7.95833 0.515625 6.66146 1.04688 5.54688C1.57812 4.43229 2.26042 3.5 3.09375 2.75C3.92708 2 4.86458 1.4375 5.90625 1.0625C6.94792 0.6875 7.97917 0.5 9 0.5C10.0208 0.5 11.0521 0.6875 12.0938 1.0625C13.1354 1.4375 14.0729 2 14.9062 2.75C15.7396 3.5 16.4219 4.43229 16.9531 5.54688C17.4844 6.66146 17.75 7.95833 17.75 9.4375C17.75 11.3958 17.026 13.3958 15.5781 15.4375C14.1302 17.4792 11.9375 19.5833 9 21.75ZM9 11.75C9.6875 11.75 10.276 11.5052 10.7656 11.0156C11.2552 10.526 11.5 9.9375 11.5 9.25C11.5 8.5625 11.2552 7.97396 10.7656 7.48438C10.276 6.99479 9.6875 6.75 9 6.75C8.3125 6.75 7.72396 6.99479 7.23438 7.48438C6.74479 7.97396 6.5 8.5625 6.5 9.25C6.5 9.9375 6.74479 10.526 7.23438 11.0156C7.72396 11.5052 8.3125 11.75 9 11.75Z"
                                                fill="white" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <ul class="pager wizard twitter-bs-wizard-pager-link d-none">
                        <li class="previous disabled"><a href="javascript: void(0);"
                                class="btn btn-primary seller-previous-btn"><i class="bx bx-chevron-left me-1"></i>
                                Previous</a></li>
                        <li class="next"><a href="javascript: void(0);" class="btn btn-primary seller-next-btn"
                                id="recomendation-display-mobile-next-button">Next <i
                                    class="bx bx-chevron-right ms-1"></i></a></li>
                    </ul>

                    {{-- sample owl carousel --}}
                    <div class="owl-carousel owl-theme loop-2">
                        <div class="item product-card">
                            <img src="https://via.placeholder.com/150" alt="" class="image-carousel">
                            <div class="row mt-3">
                                <div class="col">
                                    <div class="text-carousell">
                                        AMARON GO 46B24R
                                    </div>
                                </div>
                                <div class="col-4">
                                    <button class="btn btn-green-stabilo btn-sm btn-circle">
                                        <i class="fa fa-check"></i>
                                    </button>
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
                                    <button class="btn btn-green-stabilo btn-sm btn-circle">
                                        <i class="fa fa-check"></i>
                                    </button>
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
                                    <button class="btn btn-green-stabilo btn-sm btn-circle">
                                        <i class="fa fa-check"></i>
                                    </button>
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
                                    <button class="btn btn-green-stabilo btn-sm btn-circle">
                                        <i class="fa fa-check"></i>
                                    </button>
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
                                    <button class="btn btn-green-stabilo btn-sm btn-circle">
                                        <i class="fa fa-check"></i>
                                    </button>
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
                                    <button class="btn btn-green-stabilo btn-sm btn-circle">
                                        <i class="fa fa-check"></i>
                                    </button>
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
                        <button id="recomendation-display-mobile-next-button-lower"
                            class="btn btn-custom btn-next next" href="javascript: void(0);">Next
                            <i class="fa fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="tab-pane" id="bank-detail" style="display: none; opacity: 1;">
                <div>
                    <div class="mb-4">
                        <h5>Customer Details</h5>
                    </div>
                    <div class="card bg-grey mt-3">
                        <div class="card-body">
                            <div class="container">
                                <h5>Gozal</h5>
                                <h5>Lagadar, Margaasih, Bandung Regency, West Java, Indonesia,</h5>
                                <h5>testapahayo@gmail.com, 6281947175795</h5>
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
                                    <h5>Toyota Harrier (2014-2020) Non Turbo 2014-2017</h5>
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
                            <div class="container mt-5">
                                <div class="item-detail d-flex align-items-center">
                                    <div class="ms-3 flex-grow-1">
                                        <h5 class="mb-1">AMARON Hi-Life Duro 105D26L</h5>
                                        <p class="mb-1">3.135.750</p>
                                        <button class="btn btn-sm btn-rounded btn-dark-blue">
                                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M8.08489 9.58487C8.47131 9.58487 8.79822 9.4577 9.06561 9.20336C9.3329 8.94902 9.46654 8.63771 9.46654 8.26943C9.46654 7.90106 9.33314 7.58947 9.06633 7.33466C8.79953 7.07977 8.47297 6.95232 8.08665 6.95232C7.70022 6.95232 7.37337 7.07949 7.10608 7.33383C6.83869 7.58817 6.705 7.89948 6.705 8.26776C6.705 8.63613 6.8384 8.94772 7.1052 9.20253C7.372 9.45742 7.69857 9.58487 8.08489 9.58487ZM11.0665 12L9.47443 10.493C9.26987 10.6093 9.04982 10.6983 8.81429 10.7599C8.57866 10.8216 8.33582 10.8524 8.08577 10.8524C7.33289 10.8524 6.69297 10.6012 6.16598 10.0988C5.63891 9.59639 5.37537 8.9863 5.37537 8.26859C5.37537 7.55089 5.63891 6.9408 6.16598 6.43834C6.69297 5.93597 7.33289 5.68479 8.08577 5.68479C8.83864 5.68479 9.47862 5.93597 10.0057 6.43834C10.5327 6.9408 10.7962 7.55089 10.7962 8.26859C10.7962 8.51541 10.762 8.75077 10.6936 8.97465C10.6252 9.19853 10.5301 9.40798 10.4081 9.60298L12 11.1101L11.0665 12ZM1.32963 11.3696C0.961621 11.3696 0.648013 11.246 0.388808 10.9989C0.129602 10.7518 0 10.4529 0 10.1021V1.26753C0 0.916705 0.129602 0.617745 0.388808 0.370647C0.648013 0.123549 0.961621 0 1.32963 0H6.01227L9.58884 3.40951V4.85909C9.35038 4.764 9.10559 4.69213 8.85447 4.64347C8.60325 4.59481 8.34702 4.57048 8.08577 4.57048C7.54622 4.57048 7.04281 4.66464 6.57554 4.85296C6.10827 5.04137 5.70071 5.30532 5.35286 5.64481H2.4566V6.80913H4.52309C4.44516 6.97665 4.38374 7.14936 4.33884 7.32728C4.29383 7.50529 4.25925 7.68734 4.2351 7.87343H2.4566V9.03775H4.29471C4.39494 9.51978 4.58747 9.96235 4.8723 10.3654C5.15712 10.7686 5.52216 11.1034 5.96742 11.3696H1.32963ZM5.33694 4.0533H8.2592L5.33694 1.26753V4.0533Z"
                                                    fill="white" />
                                            </svg>
                                            Lihat Detail</button>
                                    </div>
                                    <div class="item-qty">
                                        <img src="https://via.placeholder.com/50" alt="Item Image" class="item-img">
                                        <button class="btn btn-outline-secondary btn-control-rounded">-</button>
                                        <span class="mx-2">10</span>
                                        <button class="btn btn-outline-secondary btn-control-rounded">+</button>
                                    </div>
                                </div>

                                <div class="item-detail d-flex align-items-center">
                                    <div class="ms-3 flex-grow-1">
                                        <h5 class="mb-1">AMARON Hi-Life Duro 105D26L</h5>
                                        <p class="mb-1">3.135.750</p>
                                        <button class="btn btn-sm btn-rounded btn-dark-blue">
                                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M8.08489 9.58487C8.47131 9.58487 8.79822 9.4577 9.06561 9.20336C9.3329 8.94902 9.46654 8.63771 9.46654 8.26943C9.46654 7.90106 9.33314 7.58947 9.06633 7.33466C8.79953 7.07977 8.47297 6.95232 8.08665 6.95232C7.70022 6.95232 7.37337 7.07949 7.10608 7.33383C6.83869 7.58817 6.705 7.89948 6.705 8.26776C6.705 8.63613 6.8384 8.94772 7.1052 9.20253C7.372 9.45742 7.69857 9.58487 8.08489 9.58487ZM11.0665 12L9.47443 10.493C9.26987 10.6093 9.04982 10.6983 8.81429 10.7599C8.57866 10.8216 8.33582 10.8524 8.08577 10.8524C7.33289 10.8524 6.69297 10.6012 6.16598 10.0988C5.63891 9.59639 5.37537 8.9863 5.37537 8.26859C5.37537 7.55089 5.63891 6.9408 6.16598 6.43834C6.69297 5.93597 7.33289 5.68479 8.08577 5.68479C8.83864 5.68479 9.47862 5.93597 10.0057 6.43834C10.5327 6.9408 10.7962 7.55089 10.7962 8.26859C10.7962 8.51541 10.762 8.75077 10.6936 8.97465C10.6252 9.19853 10.5301 9.40798 10.4081 9.60298L12 11.1101L11.0665 12ZM1.32963 11.3696C0.961621 11.3696 0.648013 11.246 0.388808 10.9989C0.129602 10.7518 0 10.4529 0 10.1021V1.26753C0 0.916705 0.129602 0.617745 0.388808 0.370647C0.648013 0.123549 0.961621 0 1.32963 0H6.01227L9.58884 3.40951V4.85909C9.35038 4.764 9.10559 4.69213 8.85447 4.64347C8.60325 4.59481 8.34702 4.57048 8.08577 4.57048C7.54622 4.57048 7.04281 4.66464 6.57554 4.85296C6.10827 5.04137 5.70071 5.30532 5.35286 5.64481H2.4566V6.80913H4.52309C4.44516 6.97665 4.38374 7.14936 4.33884 7.32728C4.29383 7.50529 4.25925 7.68734 4.2351 7.87343H2.4566V9.03775H4.29471C4.39494 9.51978 4.58747 9.96235 4.8723 10.3654C5.15712 10.7686 5.52216 11.1034 5.96742 11.3696H1.32963ZM5.33694 4.0533H8.2592L5.33694 1.26753V4.0533Z"
                                                    fill="white" />
                                            </svg>
                                            Lihat Detail
                                        </button>
                                    </div>
                                    <div class="item-qty">
                                        <img src="https://via.placeholder.com/50" alt="Item Image" class="item-img">
                                        <button class="btn btn-outline-secondary btn-control-rounded">-</button>
                                        <span class="mx-2">10</span>
                                        <button class="btn btn-outline-secondary btn-control-rounded">+</button>
                                    </div>
                                </div>

                                <div class="item-detail d-flex align-items-center">
                                    <div class="ms-3 flex-grow-1">
                                        <h5 class="mb-1">AMARON Hi-Life Duro 105D26L</h5>
                                        <p class="mb-1">3.135.750</p>
                                        <button class="btn btn-sm btn-rounded btn-dark-blue">
                                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M8.08489 9.58487C8.47131 9.58487 8.79822 9.4577 9.06561 9.20336C9.3329 8.94902 9.46654 8.63771 9.46654 8.26943C9.46654 7.90106 9.33314 7.58947 9.06633 7.33466C8.79953 7.07977 8.47297 6.95232 8.08665 6.95232C7.70022 6.95232 7.37337 7.07949 7.10608 7.33383C6.83869 7.58817 6.705 7.89948 6.705 8.26776C6.705 8.63613 6.8384 8.94772 7.1052 9.20253C7.372 9.45742 7.69857 9.58487 8.08489 9.58487ZM11.0665 12L9.47443 10.493C9.26987 10.6093 9.04982 10.6983 8.81429 10.7599C8.57866 10.8216 8.33582 10.8524 8.08577 10.8524C7.33289 10.8524 6.69297 10.6012 6.16598 10.0988C5.63891 9.59639 5.37537 8.9863 5.37537 8.26859C5.37537 7.55089 5.63891 6.9408 6.16598 6.43834C6.69297 5.93597 7.33289 5.68479 8.08577 5.68479C8.83864 5.68479 9.47862 5.93597 10.0057 6.43834C10.5327 6.9408 10.7962 7.55089 10.7962 8.26859C10.7962 8.51541 10.762 8.75077 10.6936 8.97465C10.6252 9.19853 10.5301 9.40798 10.4081 9.60298L12 11.1101L11.0665 12ZM1.32963 11.3696C0.961621 11.3696 0.648013 11.246 0.388808 10.9989C0.129602 10.7518 0 10.4529 0 10.1021V1.26753C0 0.916705 0.129602 0.617745 0.388808 0.370647C0.648013 0.123549 0.961621 0 1.32963 0H6.01227L9.58884 3.40951V4.85909C9.35038 4.764 9.10559 4.69213 8.85447 4.64347C8.60325 4.59481 8.34702 4.57048 8.08577 4.57048C7.54622 4.57048 7.04281 4.66464 6.57554 4.85296C6.10827 5.04137 5.70071 5.30532 5.35286 5.64481H2.4566V6.80913H4.52309C4.44516 6.97665 4.38374 7.14936 4.33884 7.32728C4.29383 7.50529 4.25925 7.68734 4.2351 7.87343H2.4566V9.03775H4.29471C4.39494 9.51978 4.58747 9.96235 4.8723 10.3654C5.15712 10.7686 5.52216 11.1034 5.96742 11.3696H1.32963ZM5.33694 4.0533H8.2592L5.33694 1.26753V4.0533Z"
                                                    fill="white" />
                                            </svg>
                                            Lihat Detail</button>
                                    </div>
                                    <div class="item-qty">
                                        <img src="https://via.placeholder.com/50" alt="Item Image" class="item-img">
                                        <button class="btn btn-outline-secondary btn-control-rounded">-</button>
                                        <span class="mx-2">10</span>
                                        <button class="btn btn-outline-secondary btn-control-rounded">+</button>
                                    </div>
                                </div>

                                <div class="btn-add-more mt-3">
                                    <span>Tambah</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card bg-stabilo mt-3">
                        <div class="card-body">
                            <div class="container mt-2">
                                <h3>Partner Detail </h3>
                                <h4>Distributor</h4>
                            </div>
                        </div>
                    </div>
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

    $(".loop-2").owlCarousel({
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

    // recomendation-display-mobile-next-button-lower click event
    $('#recomendation-display-mobile-next-button-lower').click(function() {
        $("#recomendation-display-mobile-next-button").click();
    });
</script>
