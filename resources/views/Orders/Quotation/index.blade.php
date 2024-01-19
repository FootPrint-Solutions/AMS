@extends('template.master')

@section('content')
{{-- Title --}}
<div class="h1">
    Quick Quotation
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div id="progrss-wizard" class="twitter-bs-wizard">
                    <ul class="twitter-bs-wizard-nav nav nav-pills nav-justified">
                        <li class="nav-item">
                            <a href="#progress-seller-details" class="nav-link" data-toggle="tab">
                                <div class="step-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="Customer Details">
                                    <i class="far fa-user"></i>
                                </div>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#progress-company-document" class="nav-link" data-toggle="tab">
                                <div class="step-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="Order Details">
                                    <i class="fa-solid fa-receipt"></i>
                                </div>
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a href="#progress-bank-detail" class="nav-link" data-toggle="tab">
                                <div class="step-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="Payment Details">
                                    <i class="fas fa-credit-card"></i>
                                </div>
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content twitter-bs-wizard-tab-content">
                        {{-- Customer Details --}}
                        <div class="tab-pane active" id="progress-seller-details">
                            <div class="h3 mb-4">
                                Customer Details <button class="btn btn-success btn-sm mx-2"><i class="fa fa-share" aria-hidden="true"></i></button>
                            </div>

                            <form>
                                {{-- Full Name --}}
                                <div class="form-group row mb-3">
                                    <label for="customer-full-name" class="col-sm-2 col-form-label">Full name</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="customer-full-name" value="Azunyan">
                                    </div>
                                </div>

                                {{-- Contact --}}
                                <div class="form-group row mb-3">
                                    <label for="customer-contact" class="col-sm-2 col-form-label">Contact</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="customer-contact" value="azunyannyaa@hotmail.com">
                                    </div>
                                </div>

                                {{-- Address --}}
                                <div class="form-group row mb-3">
                                    <label for="customer-address" class="col-sm-2 col-form-label">Address</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="customer-address" value="Jalan Hokago Tea Time no. 54">
                                    </div>
                                </div>

                                {{-- Vehicle --}}
                                <div class="form-group row mb-3">
                                    <label for="customer-contact" class="col-sm-2 col-form-label">Vehicle</label>
                                    <div class="col-sm-10">
                                        <div class="border rounded p-2">
                                            <span class="btn btn-primary">Toyota Avanza</span>
                                            <span class="btn btn-primary">Azunyan #2</span>
                                            <span class="btn btn-primary">Hohoho</span>
                                            <span class="btn btn-success"><i class="fa fa-plus-circle" aria-hidden="true"></i></span>
                                        </div>
                                    </div>
                                </div>
                            </form>

                            <div class="row">
                                <div class="col text-end">
                                    <a href="javascript: void(0);" class="btn btn-primary seller-next-btn"> Next <i class="bx bx-chevron-right ms-1"></i></a>
                                </div>
                            </div>
                        </div>

                        {{-- Order Details --}}
                        <div class="tab-pane" id="progress-company-document">
                          <div>
                            <div class="h3 mb-4">
                                Order Details <button class="btn btn-success btn-sm mx-2"><i class="fa fa-share" aria-hidden="true"></i></button>
                            </div>

                            <form>
                                {{-- Customer --}}
                                <div class="form-group row mb-3">
                                    <label for="order-customer" class="col-sm-2 col-form-label">Customer</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="order-customer" value="Azunyan #3">
                                    </div>
                                </div>

                                {{-- Customer Vehicle --}}
                                <div class="form-group row mb-3">
                                    <label for="order-customer-vehicle" class="col-sm-2 col-form-label">Customer Vehicle</label>
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
                                        <input type="text" class="form-control" id="order-battery" value="Amaron GO 95D31R">
                                    </div>
                                    <div class="col-sm-1">
                                        <input type="number" class="form-control" id="order-battery" value="1" min="1">
                                    </div>
                                </div>

                                {{-- Battery Retail Price --}}
                                <div class="form-group row mb-3">
                                    <label for="order-battery-price" class="col-sm-2 col-form-label">Battery Retail Price</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="order-battery" value="1670000" readonly>
                                    </div>
                                </div>

                                {{-- Battery Discount --}}
                                <div class="form-group row mb-3">
                                    <label for="order-battery-price" class="col-sm-2 col-form-label">Battery Discounted Price</label>
                                    <div class="col-sm-5">
                                        <input type="number" class="form-control" id="order-battery" value="0">
                                    </div>

                                    <label for="order-battery-price" class="col-sm-2 col-form-label">Extra Discount</label>
                                    <div class="col-sm-3">
                                        <input type="number" class="form-control" id="order-battery" value="0">
                                    </div>
                                </div>

                                {{-- Trade In --}}
                                <div class="form-group row mb-3">
                                    <label for="order-tradein" class="col-sm-2 col-form-label">Trade In</label>
                                    <div class="col-sm-5">
                                        <input type="radio" id="order-tradein-yes" name="order-tradein" value="1">
                                        <label for="order-tradein-yes">Yes</label><br>
                                    </div>
                                    <div class="col-sm-5">
                                        <input type="radio" id="order-tradein-no" name="order-tradein" value="0">
                                        <label for="order-tradein-no">No</label><br>
                                    </div>
                                </div>

                                {{-- Trade In Type --}}
                                <div class="form-group row mb-3">
                                    <label for="order-tradein-type" class="col-sm-2 col-form-label">Trade In Type</label>
                                    <div class="col-sm-10">
                                        <input type="number" class="form-control" id="order-tradein-type" value="0">
                                    </div>
                                </div>

                                {{-- Trade In Value --}}
                                <div class="form-group row mb-3">
                                    <label for="order-tradein-value" class="col-sm-2 col-form-label">Trade In Value</label>
                                    <div class="col-sm-10">
                                        <input type="number" class="form-control" id="order-tradein-value" value="0">
                                    </div>
                                </div>

                                {{-- Delivery Cost --}}
                                <div class="form-group row mb-3">
                                    <label for="order-delivery-cost" class="col-sm-2 col-form-label">Delivery Cost</label>
                                    <div class="col-sm-10">
                                        <input type="number" class="form-control" id="order-delivery-cost" value="0">
                                    </div>
                                </div>

                                {{-- Installation --}}
                                <div class="form-group row mb-3">
                                    <label for="order-installation" class="col-sm-2 col-form-label">Installation</label>
                                    <div class="col-sm-5">
                                        <input type="radio" id="order-installation-yes" name="order-installation" value="1">
                                        <label for="order-installation-yes">Yes</label><br>
                                    </div>
                                    <div class="col-sm-5">
                                        <input type="radio" id="order-installation-no" name="order-installation" value="0">
                                        <label for="order-installation-no">No</label><br>
                                    </div>
                                </div>

                                {{-- Total --}}
                                <div class="form-group row mb-3">
                                    <label for="order-delivery-cost" class="col-sm-2 col-form-label">Total</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="order-delivery-cost" value="1670000" readonly>
                                    </div>
                                </div>
                            </form>

                            <div class="row">
                                <div class="col">
                                    <a href="javascript: void(0);" class="btn btn-primary seller-previous-btn"><i class="bx bx-chevron-left me-1"></i> Previous</a>
                                </div>

                                <div class="col text-end">
                                    <a href="javascript: void(0);" class="btn btn-primary seller-next-btn">Next <i class="bx bx-chevron-right ms-1"></i></a>
                                </div>
                            </div>
                          </div>
                        </div>

                        {{-- Payment Details --}}
                        <div class="tab-pane" id="progress-bank-detail">
                            <div>
                                <div class="mb-4">
                                    <h5>Payment Details</h5>
                                </div>

                                <div class="h4 mb-4">
                                    <div class="text-center">
                                        Is everything OK?
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col">
                                        <a href="javascript: void(0);" class="btn btn-primary seller-previous-btn"><i class="bx bx-chevron-left me-1"></i> Previous</a>
                                    </div>
                                    <div class="col text-end">
                                        <a href="javascript: void(0);" class="btn btn-primary" data-bs-toggle="modal" data-bs-target=".confirmModal">OK</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end card body -->
        </div>
        <!-- end card -->
    </div>
    <!-- end col -->
</div>
@endsection