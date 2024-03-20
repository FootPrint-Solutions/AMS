<div class="card invoice-info-card">
    <div class="card-body">
        <div class="invoice-item invoice-item-one">
            <div class="row">
                <div class="col-md-6">
                    <!-- <div class="invoice-logo">
                        <img src="assets/img/logo.png" alt="logo">
                    </div> -->
                    <div class="invoice-head">
                        <h2>Invoice</h2>
                        <p>Invoice Number : {{ $InvoiceNumber }}</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="invoice-info">
                        {{-- <strong class="customer-text-one">Invoice From</strong>
                        <h6 class="invoice-name">Company Name</h6>
                        <p class="invoice-details">
                            9087484288 <br>
                            Address line 1, Address line 2<br>
                            Zip code ,City - Country
                        </p> --}}
                    </div>
                </div>
            </div>
        </div>

        <div class="invoice-item invoice-item-two">
            <div class="row">
                <div class="col-md-6">
                    <div class="invoice-info">
                        <strong class="customer-text-one">Billed to</strong>
                        <h6 class="invoice-name">{{ $Fullname }}</h6>
                        <p class="invoice-details invoice-details-two">
                            62{{ $ContactNumber }} <br>
                            {{ $EmailCustomer }} <br>
                            {{ $AddressCustomer }}, <br>
                        </p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="invoice-info invoice-info2">
                        {{-- <strong class="customer-text-one">Payment Details</strong>
                        <p class="invoice-details">
                            Debit Card <br>
                            XXXXXXXXXXXX-2541 <br>
                            HDFC Bank
                        </p>
                        <div class="invoice-item-box">
                            <p>Recurring : 15 Months</p>
                            <p class="mb-0">PO Number : 54515454</p>
                        </div> --}}
                    </div>
                </div>
            </div>
        </div>


        <div class="invoice-issues-box">
            <div class="row">
                <div class="col-lg-2 col-md-4">
                    <div class="invoice-issues-date">
                        <p>Payment Link : </p>
                    </div>
                </div>
                <div class="col-lg-10 col-md-4">
                    <div class="invoice-issues-date">
                        <p>{{ $snapToken }}</p>
                    </div>
                </div>
            </div>
        </div>


        <div class="invoice-item invoice-table-wrap">
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="invoice-table table table-center mb-0">
                            <thead>
                                <tr>
                                    <th>Battery</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($Battery as $battery)
                                    <tr>
                                        <td>
                                            <input type="text" name="BatteryNameCheckout[]" class="form-control"
                                                value="{{ $battery['name'] }}" readonly>
                                        </td>
                                        <td>
                                            <input readonly type="number" name="QtyCheckout[]" id="QtyCheckout"
                                                class="form-control" value="1">
                                        </td>
                                        <td>
                                            <input readonly type="text" name="PriceCheckout[]" id="PriceCheckout"
                                                class="form-control PriceCheckout"
                                                value="{{ $battery['price_retail'] }}">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="row align-items-center justify-content-center">
            <div class="col-lg-6 col-md-6">
                <div class="invoice-terms">
                    <h6>Mechanic Name :</h6>
                    <p class="mb-0">mechanic's name will appear here</p>
                </div>
                <div class="invoice-terms">
                    <h6>Mechanic Phone Number :</h6>
                    <p class="mb-0">mechanic's phone number will appear here</p>
                </div>
            </div>
            <div class="col-lg-6 col-md-6">
                <div class="invoice-total-card">
                    <div class="invoice-total-box">
                        <div class="invoice-total-inner">
                            <p>Tax <span>Rp. 0</span></p>
                            <p>Discount <span>Rp. 0</span></p>
                            <p>Extra Discount <span>Rp. 0</span></p>
                        </div>
                        <div class="invoice-total-footer">
                            <h4>Total Amount <span>Rp. {{ number_format($TotalAmount, 0, ',', '.') }}</span></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="invoice-sign text-end">
            {{-- <img class="img-fluid d-inline-block" src="assets/img/signature.png" alt="sign">
            <span class="d-block">Harristemp</span> --}}
        </div>
    </div>
</div>
