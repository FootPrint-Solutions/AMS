<div>
    <div class="mb-4">
        <h5>Enter Your Personal Details</h5>
    </div>
    <form id='FormPersonalDetails'>
        <div class="row">
            <div class="col-lg-6">
                <div class="form-group local-forms">
                    <label for="company-name">Full Name <span class="login-danger">*</span></label>
                    <input type="text" class="form-control" id="FullName" name="FullName" placeholder="Enter Full Name"
                        value="" required autocomplete="off">
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
                    <input type="text" class="form-control" id="EmailCustomer" name="EmailCustomer"
                        placeholder="Enter Email" value="" required autocomplete="off">
                </div>
            </div>

        </div>

        <div class="row">
            <div class="col-lg-6">
                <div class="form-group local-forms">
                    <div class="input-group">
                        <span class="input-group-text border-end country-code">+62</span>
                        <label for="company-name">Contact Number <span class="login-danger">*</span></label>
                        <input type="number" class="form-control" id="ContactNumber" name="ContactNumber"
                            placeholder="Enter Contract Number" value="" required autocomplete="off">
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="form-group local-forms">
                    <label for="company-name">Vehicle Customer <span class="login-danger">*</span></label>
                    <select name="VehicleCustomer[]" multiple='multiple' id='VehicleCustomer' class="form-select"
                        aria-label="Default select example">
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
                    <label for="company-contact">Address Customer <span class="login-danger">*</span></label>


                    {{-- <textarea class="form-control" id="AddressCustomer" name="AddressCustomer" placeholder="Enter Addres Customer"
                                                    value="" required autocomplete="off"></textarea> --}}

                    <input type="text" class="form-control" id="AddressCustomer" name="AddressCustomer">
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
            <button id="btnCopyAddress" class="btn clip-btn btn-primary"><i class="far fa-copy"></i>
                Copy from Input</button>
            <button id='BtnShareFormPersonalDetails' class="btn btn-success">
                <i class="fa-brands fa-whatsapp"></i>
                Share
            </button>
            <a href="javascript: void(0);" class="btn btn-primary seller-next-btn-check">
                Next
                <i class="bx bx-chevron-right ms-1"></i></a>
            <a id="btnNextStep2" href="javascript: void(0);" class="btn btn-primary seller-next-btn d-none">
                Next
                <i class="bx bx-chevron-right ms-1"></i></a>
        </div>
    </div>
</div>
