<style>
    .bg-grey {
        background-color: #6c757d !important;
        color: #fff;
    }
</style>
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
                        <th style="width: 20%;">Platform</th>
                        <th>Link E-Commerce</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach ($Battery as $battery)
                    <?php
                    if (isset($Distributor) && !empty($Distributor)) {
                        $batteryUrl = DB::table('battery_urls')
                            ->where('battery_id', $battery->id)
                            ->get()
                            ->toArray();
                    }
                    ?>
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
                                <input type="text" name="PriceCheckout[]" id="PriceCheckout"
                                    class="form-control PriceCheckout text-end"
                                    value="{{ number_format($battery->price_retail, '0', ',', '.') }}">
                            </div>
                        </td>
                        @if (isset($Distributor) && !empty($Distributor))
                            <td>
                                <select name="Platform[]" id="Platform" class="form-control Platform">
                                    <option data-urlecommerce="" value="">-- Choose Platform --</option>
                                    @foreach ($batteryUrl as $url)
                                        <option data-urlecommerce="{{ $url->url }}" value="{{ $url->platform }}">
                                            {{ $url->platform }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="text" name="LinkTokopedia[]" id="LinkTokopedia"
                                    class="form-control LinkTokopedia" value="{{ $battery->url }}" autocomplete="off">
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
                        <label for="order-customer" class="col-sm-5 col-form-label">Technicians</label>
                        <div class="col-sm-7">
                            <select name="techniciansName" id="techniciansName" class="form-control">
                                <option value="">-- No Technician --</option>
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
                            <input type="text" class="form-control" id="subtotal2" name="subtotal2"
                                value="0" readonly>
                            <input type="hidden" class="form-control" id="subtotal" name="subtotal"
                                value="0">
                        </div>
                    </div>

                    <div class="form-group row mb-3">
                        <label for="order-customer" class="col-sm-5 col-form-label">Discount</label>
                        <div class="col-sm-7">
                            <div class="input-group">
                                <input type="number" class="form-control" id="discount" name="discount"
                                    value="0">
                                {{-- <span class="input-group-text border-end bg-grey">%</span> --}}
                                <input type="hidden" name="discount-rupiah" id="discount-rupiah">
                                <input type="hidden" name="discount-percent" id="discount-percent">
                                <input type="hidden" name="type-discount" id="type-discount">
                                <button class="btn bg-success btn-sm" id="btn-percent">%</button>
                                <button class="btn bg-grey btn-sm" id="btn-rupiah">Rp.</button>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row mb-3">
                        <label for="order-customer" class="col-sm-5 col-form-label">Tax</label>
                        <div class="col-sm-7">
                            <div class="input-group">
                                <input type="number" class="form-control" id="tax" name="tax"
                                    value="{{ $tax }}">
                                <span class="input-group-text border-end">%</span>
                            </div>
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
        $('.Platform').on('change', function() {
            var selectedUrl = $(this).find('option:selected').data('urlecommerce');
            var linkInput = $(this).closest('tr').find('.LinkTokopedia');
            linkInput.val(selectedUrl);
        });

        function calculateTotalAmount() {
            var total = 0;


            $(".add-table-items tbody tr").each(function() {
                var quantity = $(this).find("input[name='QtyCheckout[]']").val();
                var price = parseFloat($(this).find("input[name='PriceCheckout[]']").val().replace(
                    /\./g, '').replace(',', '.'));


                var subtotal = quantity * price;
                total += subtotal;
            });

            var subtotal = total;
            $("#subtotal").val(subtotal);
            var formatedSubtotal = subtotal.toLocaleString('id-ID', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            });
            $("#subtotal2").val(formatedSubtotal);


            var tax = $("#tax").val();
            var discount = $("#discount").val();
            if (tax == "") {
                tax = 0;
            }
            if (discount == "") {
                discount = 0;
            }
            var typeDiscount = $("#type-discount").val();
            if (typeDiscount == "rupiah") {
                var discountvalue = parseInt(discount);
                var taxvalue = (subtotal - discountvalue) * (parseInt(tax) / 100);
                var GrandTotal = (subtotal - parseInt(discountvalue)) + parseInt(taxvalue);
                var discountPercent = (parseInt(discount) / subtotal) * 100;
                $("#discount-rupiah").val(discountvalue);
                $("#discount-percent").val(discountPercent);
            } else {
                var discountvalue = subtotal * (parseInt(discount) / 100);
                var taxvalue = (subtotal - discountvalue) * (parseInt(tax) / 100);
                var GrandTotal = (subtotal - parseInt(discountvalue)) + parseInt(taxvalue);
                $("#discount-rupiah").val(discountvalue);
                $("#discount-percent").val(discount);
            }

            $("#tax").val(tax);
            $("#discount").val(discount);
            $("#TotalAmount").text(GrandTotal
                .toLocaleString('id-ID', {
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

        // formatPrice($(".PriceCheckout"));

        $('.PriceCheckout').on("keyup", function() {
            formatPrice($(this));
        });


        // button rupiah ditekan maka btn percent menjadi bg-grey dan btn rupiah menjadi bg-success
        $('#btn-rupiah').on('click', function() {
            $('#btn-percent').removeClass('bg-success').addClass('bg-grey');
            $('#btn-rupiah').removeClass('bg-grey').addClass('bg-success');
            var discount = $('#discount').val();
            var subtotal = $('#subtotal').val();
            var discountvalue = $('#discount-rupiah').val();
            $('#discount-rupiah').val(discountvalue);
            $('#discount-percent').val(discount);
            $('#type-discount').val('rupiah');
            calculateTotalAmount();
        });

        // button percent ditekan maka btn rupiah menjadi bg-grey dan btn percent menjadi bg-success
        $('#btn-percent').on('click', function() {
            $('#btn-rupiah').removeClass('bg-success').addClass('bg-grey');
            $('#btn-percent').removeClass('bg-grey').addClass('bg-success');
            var discount = $('#discount').val();
            var subtotal = $('#subtotal').val();
            var discountvalue = subtotal * (parseInt(discount) / 100);
            $('#discount-rupiah').val(discountvalue);
            $('#discount-percent').val(discount);
            $('#type-discount').val('percent');
            calculateTotalAmount();
        });

    });
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/autonumeric/4.10.5/autoNumeric.min.js"
    integrity="sha512-EGJ6YGRXzV3b1ouNsqiw4bI8wxwd+/ZBN+cjxbm6q1vh3i3H19AJtHVaICXry109EVn4pLBGAwaVJLQhcazS2w=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
