<style>
    .bg-grey {
        background-color: #6c757d !important;
        color: #fff;
    }

    .autocomplete-suggestions {
        position: absolute;
        background-color: #f1f1f1;
        max-height: 150px;
        overflow-y: auto;
        border: 1px solid #ccc;
        z-index: 999;
    }

    .suggestion-item {
        padding: 10px;
        cursor: pointer;
    }

    .suggestion-item:hover {
        background-color: #ddd;
    }
</style>
<div class="row">
    <div class="col-xl-4 col-lg-6 col-md-6">
        <div class="invoice-info">
            <strong class="customer-text">Customer Detail
            </strong>
            <p class="invoice-details invoice-details-two mt-3">
                {{ $Fullname }} <br>
                {{ $AddressCustomer }}, {{ $alternativeAddress }}<br>
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



<div class="">
    <h4>Item Details</h4>
    <div class=" table-responsive">
        <table class="table table-center add-table-items">
            <thead>
                <tr>
                    <th style="width: 25%;">Battery</th>
                    <th style="width: 5%;">Quantity</th>
                    <th>Gross Price</th>
                    <th>Tax</th>
                    <th>Price + Tax</th>
                    <th style="width: 5%;">Discount ( Rp )</th>
                    <th>Net Price</th>
                    <th>Subtotal</th>
                    <th style="width: 10%;">Installation Included</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($Battery as $battery)
                    <?php
                    if ($battery->discount != 0) {
                        $discount = $battery->discount;
                        $price_retail = $battery->price_retail_original;
                        $price_net = $battery->price_net;
                        $price_tax = $price_retail + ($price_retail * $tax) / 100;
                        $discount_price = $battery->discount_price;
                    } else {
                        $discount = 0;
                        $price_retail = $battery->price_retail;
                        $price_net = $battery->price_retail;
                        $price_tax = $price_retail + ($price_retail * $tax) / 100;
                        $discount_price = 0;
                    }
                    ?>
                    <tr>
                        <td>
                            <input type="hidden" name="BatteryIdCheckout[]" id="BatteryIdCheckout"
                                class="BatteryIdCheckout" value="{{ $battery->id }}">
                            <input type="hidden" name="BatteryType[]" id="BatteryType" class="BatteryType"
                                value="{{ $battery->type }}">
                            <input type="text" name="BatteryNameCheckout[]" id="BatteryNameCheckout"
                                class="form-control BatteryNameCheckout" value="{{ $battery->name }}" readonly>
                        </td>
                        <td>
                            <input type="number" name="QtyCheckout[]" id="QtyCheckout" class="form-control QtyCheckout"
                                value="1">
                        </td>
                        {{-- gross --}}
                        <td>
                            <div class="input-group">
                                <input type="text" name="GrossPrice[]" id="GrossPrice"
                                    class="form-control GrossPrice text-end" value="{{ $price_retail }}"
                                    @if (!$battery->editable_price) disabled @endif>
                            </div>
                        </td>
                        {{-- tax --}}
                        <td>
                            <div class="input-group">
                                <input type="text" name="TaxRow[]" id="TaxRow"
                                    class="form-control TaxRow text-end" value="{{ $tax }}">
                            </div>
                        </td>
                        {{-- price + tax --}}
                        <td>
                            <div class="input-group">
                                <input type="text" name="PriceTaxRow[]" id="PriceTaxRow"
                                    class="form-control PriceTaxRow text-end" value="{{ $price_tax }}" disabled>
                            </div>
                        </td>
                        {{-- discount --}}
                        <td>
                            <div class="input-group">
                                <input type="number" name="DiscountRow[]" id="DiscountRow"
                                    class="form-control DiscountRow text-end" value="{{ $discount_price }}">
                            </div>
                        </td>
                        {{-- net --}}
                        <td>
                            <div class="input-group">
                                <input type="text" name="NetPrice[]" id="NetPrice"
                                    class="form-control NetPrice text-end" value="{{ $price_net }}" disabled>
                            </div>
                        </td>
                        {{-- subtotal --}}
                        <td>
                            <div class="input-group">
                                <input type="text" name="SubtotalRow[]" id="SubtotalRow"
                                    class="form-control SubtotalRow text-end" value="" disabled>
                            </div>
                        </td>
                        {{-- Installation Included --}}
                        <td id="InstallationIncluded">
                            <div class="form-check">
                                <input type="checkbox" name="IsInstallationIncluded[]" id="IsInstallationIncluded"
                                    class="form-check-input IsInstallationIncluded" value="0">
                                <label class="form-check-label" for="IsInstallationIncluded">Yes</label>
                            </div>
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm remove-row">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <button type="button" class="btn btn-primary btn-sm mt-3 mb-3 add-row">Add Row</button>
        {{-- Button Add Recycle --}}
        <button type="button" class="btn btn-danger btn-sm mt-3 mb-3 add-recycle-row">Add Recycle
            Battery</button>
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
                                    <option data-phone="62{{ $technician['contact'] }}"
                                        value="{{ $technician['id'] }}">
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

                    <div class="form-group row mb-3 d-none">
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

                    {{-- <div class="form-group row mb-3">
                        <label for="order-customer" class="col-sm-5 col-form-label">Tax</label>
                        <div class="col-sm-7">
                            <div class="input-group">
                                <input type="number" class="form-control" id="tax" name="tax"
                                    value="{{ $tax }}">
                        <span class="input-group-text border-end">%</span>
                    </div>
                </div>
            </div> --}}


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
        function formatNumber(num) {
            return num.toLocaleString('id-ID', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            });
        }

        // discount row format number on keyup
        $('.DiscountRow').on('keyup', function() {
            var discount = $(this).val();
            $(this).val(formatNumber(discount));
        });

        // Function to parse formatted number to float
        function parseFormattedNumber(num) {
            return parseFloat(num.replace(/\./g, '').replace(',', '.'));
        }

        function calculateRow(row) {
            var qty = parseFloat(row.find('.QtyCheckout').val()) || 0;
            var grossPriceReal = parseFormattedNumber(row.find('.GrossPrice').val().replace(/,/g, '')) || 0;
            var tax = parseFloat(row.find('.TaxRow').val()) || 0;
            var discount = parseFloat(row.find('.DiscountRow').val()) || 0;

            var pricetax = grossPriceReal + ((grossPriceReal * tax) / 100);
            row.find('.PriceTaxRow').val(formatNumber(pricetax));

            var netPrice = pricetax - discount;
            row.find('.NetPrice').val(formatNumber(netPrice));

            var subtotal = netPrice * qty;
            row.find('.SubtotalRow').val(formatNumber(subtotal));

            if (subtotal <= 0 || isNaN(subtotal)) {
                swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    title: 'Subtotal is less than 0',
                    showConfirmButton: false,
                    timer: 2000
                });
                row.find('.QtyCheckout').val(1);
                row.find('.NetPrice').val(formatNumber(pricetax));
                row.find('.SubtotalRow').val(formatNumber(pricetax));
            }

            console.log('pricetax', pricetax);
            console.log('grossPriceReal', grossPriceReal);
            console.log('tax', tax);
            console.log('discount', discount);
            console.log('netPrice', netPrice);
        }

        $(document).on('keyup keydown', '.QtyCheckout, .DiscountRow, .GrossPrice, .TaxRow', function() {
            var row = $(this).closest('tr');
            calculateRow(row);
            calculateTotalAmount();
        });

        $(document).on('click', '.QtyCheckout, .DiscountRow, .GrossPrice, .TaxRow', function() {
            var row = $(this).closest('tr');
            calculateRow(row);
            calculateTotalAmount();
        });

        // Calculate initial rows
        $('.add-table-items tbody tr').each(function() {
            calculateRow($(this));
            calculateTotalAmount();
        });

        function calculateTotalAmount() {
            var subtotal = 0;
            var subtotal_recycle = 0;
            $('.add-table-items tbody tr').each(function() {
                var row = $(this);
                var qty = parseFloat(row.find('.QtyCheckout').val()) || 0;
                var type = row.find('.BatteryType').val();
                var subtotalRow = parseFormattedNumber(row.find('.SubtotalRow').val().replace(/,/g,
                        '')) ||
                    0;

                if (type != 'regular') {
                    subtotal -= subtotalRow;
                    subtotal_recycle += subtotalRow;
                } else {
                    subtotal += subtotalRow;
                }
            });
            var discount = parseFloat($('#discount').val()) || 0;
            var tax = parseFloat($('#tax').val()) || 0;

            $("#subtotal").val(subtotal);
            var formatedSubtotal = subtotal.toLocaleString('id-ID', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            });
            $("#subtotal2").val(formatedSubtotal);

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

        $("#tax, #discount, #subtotal").on("input", function() {
            calculateTotalAmount();
        });

        $(document).on('click', '.remove-row', function() {
            // cek jika row yang dihapus adalah row terakhir, maka akan menampilkan alert
            if ($('.add-table-items tbody tr').length == 1) {
                swal.fire("Error!", "You can't delete the last row", "error");
                return false;
            }
            $(this).closest('tr').remove();
            calculateTotalAmount();
        });

        $(document).on('click', '.add-row', function() {
            calculateTotalAmount();
        });

        $(document).on('click', '.add-recycle-row', function() {
            calculateTotalAmount();
        });

        calculateTotalAmount();

        // IsInstallationIncluded checkbox change event
        $(document).on('change', '.IsInstallationIncluded', function() {
            var isChecked = $(this).is(':checked');
            $(this).val(isChecked ? '1' : '0');
        });
    });
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/autonumeric/4.10.5/autoNumeric.min.js"
    integrity="sha512-EGJ6YGRXzV3b1ouNsqiw4bI8wxwd+/ZBN+cjxbm6q1vh3i3H19AJtHVaICXry109EVn4pLBGAwaVJLQhcazS2w=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
