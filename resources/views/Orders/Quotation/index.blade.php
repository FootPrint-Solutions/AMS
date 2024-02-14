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
            height: 400px;
            width: 100%;
            margin-bottom: 20px;
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
                            <li class="nav-item">
                                <a href="#product-display" class="nav-link" data-toggle="tab">
                                    <div class="step-icon" data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="Company Document">
                                        <i class="fa-solid fa-boxes-stacked"></i>
                                    </div>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#company-document" class="nav-link" data-toggle="tab">
                                    <div class="step-icon" data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="Company Document">
                                        <i class="fas fa-file-invoice"></i>
                                    </div>
                                </a>
                            </li>

                            <li class="nav-item">
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
                                                        <option value="{{ $vehicle['id'] }}">{{ $vehicle['name'] }}
                                                        </option>
                                                    @endforeach

                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group local-forms">
                                                <label for="company-contact">Address Customer <span
                                                        class="login-danger">*</span></label>


                                                <textarea class="form-control" id="AddressCustomer" name="AddressCustomer" placeholder="Enter Addres Customer"
                                                    value="" required autocomplete="off"></textarea>

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
                                        <button id='BtnShareFormPersonalDetails' class="btn btn-success"> Share <i
                                                class="fa-brands fa-whatsapp"></i></button>
                                        <a href="javascript: void(0);" class="btn btn-primary seller-next-btn"> Next <i
                                                class="bx bx-chevron-right ms-1"></i></a>
                                    </div>
                                </div>
                            </div>
                            <!-- tab pane -->
                            <div class="tab-pane" id="product-display">
                                <div>
                                    <div class="mb-4">
                                        <h5>Enter Your Order Detail</h5>
                                    </div>

                                    <div class="row">

                                        <div class="col-md-6 col-xl-4 col-sm-12 d-flex">
                                            <div class="blog grid-blog flex-fill">
                                                <div class="blog-image">
                                                    <a href="blog-details.html">
                                                        <img class="img-fluid" src="https://i.ibb.co/GdS8BTf/image.png"
                                                            alt="Post Image">
                                                    </a>
                                                    {{-- <div class="blog-views">
                                                        <i class="feather-eye me-1"></i> 225
                                                    </div> --}}
                                                </div>
                                                <div class="blog-content">
                                                    <h3 class="blog-title"><a href="blog-details.html">AMARON Quanta 9</a>
                                                    </h3>
                                                    <p>Details & Specification :</p>
                                                    <ul class="list-group list-group-flush">
                                                        <li class="list-group-item">Alt Name : UPS</li>
                                                        <li class="list-group-item">Brand : AMARON</li>
                                                        <li class="list-group-item">Sub Brand : QUANTA</li>
                                                        <li class="list-group-item">Battery Technology : VRLA Deep Cycle
                                                            Battery </li>
                                                        <li class="list-group-item">Warranty : 12 Months</li>
                                                        <li class="list-group-item">Capacity : 12V 7.2Ah</li>
                                                        <li class="list-group-item">Dimension : 151 x 65 x 94 mm</li>
                                                        <li class="list-group-item">Price : Rp. 475.000</li>
                                                    </ul>
                                                </div>
                                                <div class="row">
                                                    <div class="edit-options">
                                                        <div class="edit-delete-btn">
                                                            <a href="edit-blog.html" class="text-success"><i
                                                                    class="feather-edit-3 me-1"></i> Edit</a>
                                                            <a href="#" class="text-danger" data-bs-toggle="modal"
                                                                data-bs-target="#deleteModal"><i
                                                                    class="feather-trash-2 me-1"></i> Delete</a>
                                                        </div>
                                                        <div class="text-end inactive-style mt-3">
                                                            <div class="checkbox">
                                                                <label>
                                                                    <input type="checkbox" name="checkbox"> Send To
                                                                    Customer
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-xl-4 col-sm-12 d-flex">
                                            <div class="blog grid-blog flex-fill">
                                                <div class="blog-image">
                                                    <a href="blog-details.html">
                                                        <img class="img-fluid" src="https://i.ibb.co/GdS8BTf/image.png"
                                                            alt="Post Image">
                                                    </a>
                                                    {{-- <div class="blog-views">
                                                        <i class="feather-eye me-1"></i> 225
                                                    </div> --}}
                                                </div>
                                                <div class="blog-content">
                                                    <h3 class="blog-title"><a href="blog-details.html">AMARON Quanta 9</a>
                                                    </h3>
                                                    <p>Details & Specification :</p>
                                                    <ul class="list-group list-group-flush">
                                                        <li class="list-group-item">Alt Name : UPS</li>
                                                        <li class="list-group-item">Brand : AMARON</li>
                                                        <li class="list-group-item">Sub Brand : QUANTA</li>
                                                        <li class="list-group-item">Battery Technology : VRLA Deep Cycle
                                                            Battery </li>
                                                        <li class="list-group-item">Warranty : 12 Months</li>
                                                        <li class="list-group-item">Capacity : 12V 7.2Ah</li>
                                                        <li class="list-group-item">Dimension : 151 x 65 x 94 mm</li>
                                                        <li class="list-group-item">Price : Rp. 475.000</li>
                                                    </ul>
                                                </div>
                                                <div class="row">
                                                    <div class="edit-options">
                                                        <div class="edit-delete-btn">
                                                            <a href="edit-blog.html" class="text-success"><i
                                                                    class="feather-edit-3 me-1"></i> Edit</a>
                                                            <a href="#" class="text-danger" data-bs-toggle="modal"
                                                                data-bs-target="#deleteModal"><i
                                                                    class="feather-trash-2 me-1"></i> Delete</a>
                                                        </div>
                                                        <div class="text-end inactive-style mt-3">
                                                            <div class="checkbox">
                                                                <label>
                                                                    <input type="checkbox" name="checkbox"> Send To
                                                                    Customer
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-xl-4 col-sm-12 d-flex">
                                            <div class="blog grid-blog flex-fill">
                                                <div class="blog-image">
                                                    <a href="blog-details.html">
                                                        <img class="img-fluid" src="https://i.ibb.co/GdS8BTf/image.png"
                                                            alt="Post Image">
                                                    </a>
                                                    {{-- <div class="blog-views">
                                                        <i class="feather-eye me-1"></i> 225
                                                    </div> --}}
                                                </div>
                                                <div class="blog-content">
                                                    <h3 class="blog-title"><a href="blog-details.html">AMARON Quanta 9</a>
                                                    </h3>
                                                    <p>Details & Specification :</p>
                                                    <ul class="list-group list-group-flush list-group-sm">
                                                        <li class="list-group-item">Alt Name : UPS</li>
                                                        <li class="list-group-item">Brand : AMARON</li>
                                                        <li class="list-group-item">Sub Brand : QUANTA</li>
                                                        <li class="list-group-item">Battery Technology : VRLA Deep Cycle
                                                            Battery </li>
                                                        <li class="list-group-item">Warranty : 12 Months</li>
                                                        <li class="list-group-item">Capacity : 12V 7.2Ah</li>
                                                        <li class="list-group-item">Dimension : 151 x 65 x 94 mm</li>
                                                        <li class="list-group-item">Price : Rp. 475.000</li>
                                                    </ul>
                                                </div>
                                                <div class="row">
                                                    <div class="edit-options">
                                                        <div class="edit-delete-btn">
                                                            <a href="edit-blog.html" class="text-success"><i
                                                                    class="feather-edit-3 me-1"></i> Edit</a>
                                                            <a href="#" class="text-danger" data-bs-toggle="modal"
                                                                data-bs-target="#deleteModal"><i
                                                                    class="feather-trash-2 me-1"></i> Delete</a>
                                                        </div>
                                                        <div class="text-end inactive-style mt-3">
                                                            <div class="checkbox">
                                                                <label>
                                                                    <input type="checkbox" name="checkbox"> Send To
                                                                    Customer
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <a href="javascript: void(0);" class="btn btn-primary seller-previous-btn"><i
                                                    class="bx bx-chevron-left me-1"></i> Previous</a>
                                        </div>

                                        <div class="col text-end">
                                            <a href="javascript: void(0);" class="btn btn-success"> Share <i
                                                    class="fa-brands fa-whatsapp"></i></a>
                                            <a href="javascript: void(0);" class="btn btn-primary seller-next-btn">Next <i
                                                    class="bx bx-chevron-right ms-1"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- tab pane -->
                            <div class="tab-pane" id="company-document">
                                <div>
                                    <div class="mb-4">
                                        <h5>Enter Your Order Detail</h5>
                                    </div>

                                    <form>
                                        {{-- Customer --}}
                                        <div class="form-group row mb-3">
                                            <label for="order-customer" class="col-sm-2 col-form-label">Customer</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" id="order-customer"
                                                    value="Azunyan #3">
                                            </div>
                                        </div>

                                        {{-- Customer Vehicle --}}
                                        <div class="form-group row mb-3">
                                            <label for="order-customer-vehicle" class="col-sm-2 col-form-label">Customer
                                                Vehicle</label>
                                            <div class="col-sm-10">
                                                <div class="border rounded p-2">
                                                    <span class="btn btn-primary">Toyota Avanza</span>
                                                    <span class="btn btn-primary">Azunyan #2</span>
                                                    <span class="btn btn-primary">Hohoho</span>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Battery --}}
                                        <div class="form-group row mb-3">
                                            <label for="order-battery" class="col-sm-2 col-form-label">Battery</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" id="order-battery"
                                                    value="Amaron GO 95D31R">
                                            </div>
                                            <div class="col-sm-1">
                                                <input type="number" class="form-control" id="order-battery"
                                                    value="1" min="1">
                                            </div>
                                        </div>

                                        {{-- Battery Retail Price --}}
                                        <div class="form-group row mb-3">
                                            <label for="order-battery-price" class="col-sm-2 col-form-label">Battery
                                                Retail Price</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" id="order-battery"
                                                    value="1670000" readonly>
                                            </div>
                                        </div>

                                        {{-- Battery Discount --}}
                                        <div class="form-group row mb-3">
                                            <label for="order-battery-price" class="col-sm-2 col-form-label">Battery
                                                Discounted Price</label>
                                            <div class="col-sm-5">
                                                <input type="number" class="form-control" id="order-battery"
                                                    value="0">
                                            </div>

                                            <label for="order-battery-price" class="col-sm-2 col-form-label">Extra
                                                Discount</label>
                                            <div class="col-sm-3">
                                                <input type="number" class="form-control" id="order-battery"
                                                    value="0">
                                            </div>
                                        </div>

                                        {{-- Trade In --}}
                                        <div class="form-group row mb-3">
                                            <label for="order-tradein" class="col-sm-2 col-form-label">Trade In</label>
                                            <div class="col-sm-5">
                                                <input type="radio" id="order-tradein-yes" name="order-tradein"
                                                    value="1">
                                                <label for="order-tradein-yes">Yes</label><br>
                                            </div>
                                            <div class="col-sm-5">
                                                <input type="radio" id="order-tradein-no" name="order-tradein"
                                                    value="0">
                                                <label for="order-tradein-no">No</label><br>
                                            </div>
                                        </div>

                                        {{-- Trade In Type --}}
                                        <div class="form-group row mb-3">
                                            <label for="order-tradein-type" class="col-sm-2 col-form-label">Trade In
                                                Type</label>
                                            <div class="col-sm-10">
                                                <input type="number" class="form-control" id="order-tradein-type"
                                                    value="0">
                                            </div>
                                        </div>

                                        {{-- Trade In Value --}}
                                        <div class="form-group row mb-3">
                                            <label for="order-tradein-value" class="col-sm-2 col-form-label">Trade In
                                                Value</label>
                                            <div class="col-sm-10">
                                                <input type="number" class="form-control" id="order-tradein-value"
                                                    value="0">
                                            </div>
                                        </div>

                                        {{-- Delivery Cost --}}
                                        <div class="form-group row mb-3">
                                            <label for="order-delivery-cost" class="col-sm-2 col-form-label">Delivery
                                                Cost</label>
                                            <div class="col-sm-10">
                                                <input type="number" class="form-control" id="order-delivery-cost"
                                                    value="0">
                                            </div>
                                        </div>

                                        {{-- Installation --}}
                                        <div class="form-group row mb-3">
                                            <label for="order-installation"
                                                class="col-sm-2 col-form-label">Installation</label>
                                            <div class="col-sm-5">
                                                <input type="radio" id="order-installation-yes"
                                                    name="order-installation" value="1">
                                                <label for="order-installation-yes">Yes</label><br>
                                            </div>
                                            <div class="col-sm-5">
                                                <input type="radio" id="order-installation-no"
                                                    name="order-installation" value="0">
                                                <label for="order-installation-no">No</label><br>
                                            </div>
                                        </div>

                                        {{-- Total --}}
                                        <div class="form-group row mb-3">
                                            <label for="order-delivery-cost" class="col-sm-2 col-form-label">Total</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" id="order-delivery-cost"
                                                    value="1670000" readonly>
                                            </div>
                                        </div>
                                    </form>
                                    <div class="row">
                                        <div class="col">
                                            <a href="javascript: void(0);" class="btn btn-primary seller-previous-btn"><i
                                                    class="bx bx-chevron-left me-1"></i> Previous</a>
                                        </div>

                                        <div class="col text-end">
                                            <a href="javascript: void(0);" class="btn btn-success"> Share <i
                                                    class="fa-brands fa-whatsapp"></i></a>
                                            <a href="javascript: void(0);" class="btn btn-primary seller-next-btn">Next <i
                                                    class="bx bx-chevron-right ms-1"></i></a>
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
                                    <form>
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="basicpill-namecard-input" class="form-label">Name on
                                                        Card</label>
                                                    <input type="text" class="form-control"
                                                        id="basicpill-namecard-input">
                                                </div>
                                            </div>

                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Credit Card Type</label>
                                                    <select class="form-select">
                                                        <option selected>Select Card Type</option>
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
                                                    <label for="basicpill-cardno-input" class="form-label">Credit Card
                                                        Number</label>
                                                    <input type="text" class="form-control"
                                                        id="basicpill-cardno-input">
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
                                                    <label for="basicpill-expiration-input" class="form-label">Expiration
                                                        Date</label>
                                                    <input type="text" class="form-control"
                                                        id="basicpill-expiration-input">
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                    {{-- <ul class="pager wizard twitter-bs-wizard-pager-link">
                                        <li class="previous"><a href="javascript: void(0);"
                                                class="btn btn-primary seller-previous-btn"><i
                                                    class="bx bx-chevron-left me-1"></i> Previous</a></li>

                                        <li class="float-end">
                                            <a href="javascript: void(0);" class="btn btn-success"> Share <i
                                                    class="fa-brands fa-whatsapp"></i></a>
                                            <a href="javascript: void(0);" class="btn btn-primary" data-bs-toggle="modal"
                                                data-bs-target=".confirmModal">Save
                                                Changes</a>
                                        </li>
                                        
                                    </ul> --}}

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

                    var IdCustomer = $("#IdCustomer").val();
                    if (IdCustomer != '') {
                        $('#UserExist').show();
                        $('#UserNotExist').hide();
                    } else {
                        $('#UserExist').hide();
                        $('#UserNotExist').show();
                    }
                });
            }

        });
    </script>

    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCAlBnX9jmy3JurAGnyIAFNSyS7i5cgfzA&libraries=places">
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
