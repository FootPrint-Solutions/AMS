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
                    @if (isset($Distributor) && !empty($Distributor))
                        <th>Link Tokopedia</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach ($Battery as $battery)
                    <tr>
                        <td>
                            <input type="text" name="BatteryNameCheckout[]" id="BatteryNameCheckout"
                                class="form-control BatteryNameCheckout" value="{{ $battery->name }}" readonly>
                        </td>
                        <td>
                            <input type="number" name="QtyCheckout[]" id="QtyCheckout" class="form-control QtyCheckout"
                                value="1">
                        </td>
                        <td>
                            <div class="input-group">
                                <span class="input-group-text border-end">IDR</span>
                                <input type="number" name="PriceCheckout[]" id="PriceCheckout"
                                    class="form-control PriceCheckout text-end" value="{{ $battery->price_retail }}">
                            </div>
                        </td>
                        @if (isset($Distributor) && !empty($Distributor))
                            <td>
                                <input type="text" name="LinkTokopedia[]" id="LinkTokopedia"
                                    class="form-control LinkTokopedia" value="{{ $battery->url }}">
                            </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="row mb-5">
    <div class="col-lg-7 col-md-6">
        <div class="invoice-fields">
            @if (isset($Distributor) && !empty($Distributor))
                <div class="field-box">
                    <h4 class="field-title">Partner Details</h4>
                    <div class="form-group row mb-3">
                        <label for="order-customer" class="col-sm-5 col-form-label">Distributor Shop Name</label>
                        <div class="col-sm-7">
                            <input readonly type="text" class="form-control" id="distributorshopcheckout"
                                name="distributorshopcheckout" value="{{ $Distributor['name'] ?? '' }}"
                                placeholder="Type Distributor Here...">
                            <div id="AutoCompleteDistibutorCheckout"></div>
                        </div>
                    </div>

                    <div class="form-group row mb-3">
                        <label for="order-customer" class="col-sm-5 col-form-label">Address Distributor / Shop</label>
                        <div class="col-sm-7">
                            <input readonly type="text" class="form-control" id="distributorshopcheckout"
                                name="distributorshopcheckout" value="{{ $Distributor['address'] ?? '' }}"
                                placeholder="">
                            <div id="AutoCompleteDistibutorCheckout"></div>
                        </div>
                    </div>

                    <div class="form-group row mb-3">
                        <label for="order-customer" class="col-sm-5 col-form-label">Technicians Name</label>
                        <div class="col-sm-7">
                            <select name="techniciansName" id="techniciansName" class="form-control">
                                <option value="">-- Select Technicians --</option>
                                @foreach ($DistributorTechnician as $technician)
                                    <option data-phone="{{ $technician['contact'] }}" value="{{ $technician['id'] }}">
                                        {{ $technician['name'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row mb-3">
                        <label for="order-customer" class="col-sm-5 col-form-label">Mechanic Phone</label>
                        <div class="col-sm-7">
                            <input readonly type="text" class="form-control" id="techniciansPhone"
                                name="techniciansPhone" value="" placeholder="">
                        </div>
                    </div>

                </div>
            @else
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    Distributor Not Found
                </div>
            @endif
        </div>

    </div>
    <div class="col-lg-5 col-md-6">
        <div class="invoice-total-card">

            <div class="invoice-total-box">
                <div class="invoice-total-inner">
                    <h4 class="invoice-total-title">Summary</h4>
                    <div class="form-group row mb-3">
                        <label for="order-customer" class="col-sm-5 col-form-label">Subtotal</label>
                        <div class="col-sm-7">
                            <input type="number" class="form-control" id="subtotal" name="subtotal" value="0">
                        </div>
                    </div>

                    <div class="form-group row mb-3">
                        <label for="order-customer" class="col-sm-5 col-form-label">Discount</label>
                        <div class="col-sm-7">
                            <input type="number" class="form-control" id="discount" name="discount"
                                value="0">
                        </div>
                    </div>

                    <div class="form-group row mb-3">
                        <label for="order-customer" class="col-sm-5 col-form-label">Tax</label>
                        <div class="col-sm-7">
                            <input type="number" class="form-control" id="tax" name="tax"
                                value="{{ $tax }}">
                        </div>
                    </div>


                </div>
                <div class="invoice-total-footer">
                    <h4>Grand Total <span id="TotalAmount"></span></h4>
                    <input type="hidden" name="TotalAmountHidden" id="TotalAmountHidden">
                </div>
            </div>
        </div>
    </div>
</div>


@if (isset($Distributor) && !empty($Distributor))
    <script>
        $(document).ready(function() {
            document.getElementById('techniciansName').addEventListener('change', function() {
                var selectedOption = this.options[this.selectedIndex];
                var techniciansPhone = selectedOption.getAttribute('data-phone');
                document.getElementById('techniciansPhone').value = techniciansPhone;
            });
        });
    </script>
@endif

<script>
    $(document).ready(function() {
        function calculateTotalAmount() {
            var total = 0;


            $(".add-table-items tbody tr").each(function() {
                var quantity = $(this).find("input[name='QtyCheckout[]']").val();
                var price = $(this).find("input[name='PriceCheckout[]']").val();


                var subtotal = quantity * price;
                total += subtotal;
            });

            var subtotal = total;
            $("#subtotal").val(subtotal);

            var tax = $("#tax").val();
            var discount = $("#discount").val();
            if (tax == "") {
                tax = 0;
            }
            if (discount == "") {
                discount = 0;
            }
            var discountvalue = subtotal * (parseInt(discount) / 100);
            var taxvalue = (subtotal - discountvalue) * (parseInt(tax) / 100);

            var GrandTotal = (subtotal - parseInt(discountvalue)) + parseInt(taxvalue);

            $("#tax").val(tax);
            $("#discount").val(discount);
            $("#TotalAmount").text(GrandTotal.toLocaleString('id-ID', {
                style: 'currency',
                currency: 'IDR'
            }));
            $("#TotalAmountHidden").val(GrandTotal);
        }

        // Panggil fungsi calculateTotalAmount() setiap kali ada perubahan dalam input kuantitas atau harga
        $(".add-table-items tbody").on("input",
            "input[name='QtyCheckout[]'], input[name='PriceCheckout[]']",
            function() {
                calculateTotalAmount();
            });

        $("#tax, #discount, #subtotal").on("input", function() {
            calculateTotalAmount();
        });

        calculateTotalAmount();
    });
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/autonumeric/4.10.5/autoNumeric.min.js"
    integrity="sha512-EGJ6YGRXzV3b1ouNsqiw4bI8wxwd+/ZBN+cjxbm6q1vh3i3H19AJtHVaICXry109EVn4pLBGAwaVJLQhcazS2w=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
