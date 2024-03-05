<div class="row">
    <div class="col-xl-4 col-lg-6 col-md-6">
        <div class="invoice-info">
            <strong class="customer-text">Customer Detail
            </strong>
            <p class="invoice-details invoice-details-two mt-3">
                {{ $Fullname }} <br>
                {{ $AddressCustomer }},<br>
                {{ $EmailCustomer }}, 62{{ $ContactNumber }} <br>
            </p>
        </div>
    </div>
    <div class="col-xl-4 col-lg-6 col-md-6">
        <div class="invoice-info">
            <strong class="customer-text">Vehicle Customer</strong>
            <p class="invoice-details invoice-details-two mt-3"">
                {{ $VehicleCustomerString }} <br>

            </p>
        </div>
    </div>
</div>


<div class=" invoice-add-table">
    <h4>Item Details</h4>
    <div class="table-responsive">
        <table class="table table-center add-table-items">
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
                            <input type="number" name="QtyCheckout[]" id="QtyCheckout" class="form-control"
                                value="1">
                        </td>
                        <td>
                            <input type="text" name="PriceCheckout[]" id="PriceCheckout"
                                class="form-control PriceCheckout" value="{{ $battery['price_retail'] }}">
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="row mb-5">
    <div class="col-lg-7 col-md-6">
        <div class="invoice-fields">

            <div class="field-box">
                <h4 class="field-title">Partner Details</h4>
                <div class="form-group mb-0">
                    <label class="custom_check w-100">
                        <input type="checkbox" id="partnerCheck" name="invoice">
                        <span class="checkmark"></span> Enable Partner
                    </label>
                </div>
                <div class="form-group row mb-3">
                    <label for="order-customer" class="col-sm-5 col-form-label">Distributor / Shop Name</label>
                    <div class="col-sm-7">
                        <input readonly type="text" class="form-control" id="distributorshopcheckout"
                            name="distributorshopcheckout" value="" placeholder="Type Distributor Here...">
                        <div id="AutoCompleteDistibutorCheckout"></div>
                    </div>
                </div>

                <div class="form-group row mb-3">
                    <label for="order-customer" class="col-sm-5 col-form-label">Address Distributor / Shop</label>
                    <div class="col-sm-7">
                        <input readonly type="text" class="form-control" id="distributorshopcheckout"
                            name="distributorshopcheckout" value="" placeholder="">
                        <div id="AutoCompleteDistibutorCheckout"></div>
                    </div>
                </div>

                <div class="form-group row mb-3">
                    <label for="order-customer" class="col-sm-5 col-form-label">Mechanic Name</label>
                    <div class="col-sm-7">
                        <input readonly type="text" class="form-control" id="distributorshopcheckout"
                            name="distributorshopcheckout" value="" placeholder="Type Mechanic Name Here...">
                        <div id="AutoCompleteDistibutorCheckout"></div>
                    </div>
                </div>

                <div class="form-group row mb-3">
                    <label for="order-customer" class="col-sm-5 col-form-label">Mechanic Phone</label>
                    <div class="col-sm-7">
                        <input readonly type="text" class="form-control" id="distributorshopcheckout"
                            name="distributorshopcheckout" value="" placeholder="">
                        <div id="AutoCompleteDistibutorCheckout"></div>
                    </div>
                </div>

            </div>
        </div>

    </div>
    <div class="col-lg-5 col-md-6">
        <div class="invoice-total-card">
            <h4 class="invoice-total-title">Summary</h4>
            <div class="invoice-total-box">
                <div class="invoice-total-inner">
                    <div class="form-group row mb-3">
                        <label for="order-customer" class="col-sm-5 col-form-label">Tax</label>
                        <div class="col-sm-7">
                            <input readonly type="text" class="form-control" id="tax" name="tax"
                                value="">
                        </div>
                    </div>

                    <div class="form-group row mb-3">
                        <label for="order-customer" class="col-sm-5 col-form-label">Discount</label>
                        <div class="col-sm-7">
                            <input readonly type="text" class="form-control" id="discount" name="discount"
                                value="">
                        </div>
                    </div>

                    <div class="form-group row mb-3">
                        <label for="order-customer" class="col-sm-5 col-form-label">Extra Discount</label>
                        <div class="col-sm-7">
                            <input readonly type="text" class="form-control" id="Extradiscount"
                                name="Extradiscount" value="">
                        </div>
                    </div>


                </div>
                <div class="invoice-total-footer">
                    <h4>Total Amount <span id="TotalAmount"></span></h4>
                    <input type="hidden" name="TotalAmountHidden" id="TotalAmountHidden">
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    $(document).ready(function() {
        calculateTotalAmount();

        // new AutoNumeric('#tax', {
        //     currencySymbol: "Rp. ",
        //     digitGroupSeparator: ".",
        //     decimalCharacter: ",",
        //     minimumValue: '0'
        // });

        // new AutoNumeric('#discount', {
        //     currencySymbol: "Rp. ",
        //     digitGroupSeparator: ".",
        //     decimalCharacter: ",",
        //     minimumValue: '0'
        // });

        // new AutoNumeric('#Extradiscount', {
        //     currencySymbol: "Rp. ",
        //     digitGroupSeparator: ".",
        //     decimalCharacter: ",",
        //     minimumValue: '0'
        // });

        // new AutoNumeric('#PriceCheckout', {
        //     currencySymbol: "Rp. ",
        //     digitGroupSeparator: ".",
        //     decimalCharacter: ",",
        //     minimumValue: '0',
        // });
    });

    function calculateTotalAmount() {
        var subtotal = 0;
        var tax = getRawValue(document.getElementById('tax').value) || 0;
        var discount = getRawValue(document.getElementById('discount').value) || 0;
        var extraDiscount = getRawValue(document.getElementById('Extradiscount').value) || 0;

        var tax = parseFloat(tax);
        var discount = parseFloat(discount);
        var extraDiscount = parseFloat(extraDiscount);

        var rows = document.querySelectorAll('.add-table-items tbody tr');
        rows.forEach(function(row) {
            var price = row.querySelector('input[name="PriceCheckout[]"]').value || 0;
            var qty = row.querySelector('input[name="QtyCheckout[]"]').value || 0;
            price = getRawValue(price);
            subtotal += price * qty;
        });

        var totalAmount = (subtotal + tax) - (discount + extraDiscount);

        document.getElementById('TotalAmount')
            .innerText = totalAmount.toLocaleString('id-ID', {
                style: 'currency',
                currency: 'IDR'
            });
        document.getElementById('TotalAmountHidden').value = totalAmount;
    }

    document.addEventListener('DOMContentLoaded', function() {
        calculateTotalAmount();

        var inputs = document.querySelectorAll('.add-table-items tbody input');
        inputs.forEach(function(input) {
            input.addEventListener('change', calculateTotalAmount);
        });

        var discountInputs = document.querySelectorAll('.invoice-total-box input');
        discountInputs.forEach(function(input) {
            input.addEventListener('input', calculateTotalAmount);
        });
    });

    $("#QtyCheckout").on('keyup', function() {
        calculateTotalAmount();
    });

    $("#tax").on('keyup', function() {
        calculateTotalAmount();
    });

    $("#discount").on('keyup', function() {
        calculateTotalAmount();
    });

    $("#Extradiscount").on('keyup', function() {
        calculateTotalAmount();
    });

    $("#PriceCheckout").on('keyup', function() {
        calculateTotalAmount();
    });

    $(".PriceCheckout").on('keyup', function() {
        calculateTotalAmount();
    });

    $(".form-control").on('keyup', function() {
        calculateTotalAmount();
    });

    function getRawValue(val) {
        var result = val.replace(/\./g, "").replace(/\,/g, ".").replace("Rp. ", "").trim();
        var result = result.replace("Rp", "").trim();
        return result;
    }

    function formatCurrency(amount) {
        return "Rp. " + amount.toFixed(2).replace(/\./g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.') + ',00';
    }
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/autonumeric/4.10.5/autoNumeric.min.js"
    integrity="sha512-EGJ6YGRXzV3b1ouNsqiw4bI8wxwd+/ZBN+cjxbm6q1vh3i3H19AJtHVaICXry109EVn4pLBGAwaVJLQhcazS2w=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
