<link rel="stylesheet" href="{{ asset('plugins/bootstrap5-toggle/css/bootstrap5-toggle.min.css') }}">
<div>
    <div class="mb-4">
        <h5>Checkout Page</h5>
    </div>
    <div id="CheckoutPreview"></div>

    <div class="row">
        <div class="col">
            <a href="javascript: void(0);" class="btn btn-primary seller-previous-btn"><i class="bx bx-chevron-left me-1"></i> Previous</a>
        </div>

        <div class="col text-end">
            <button id="btnCopyOrderDetail" class="btn clip-btn btn-primary">
                <i class="far fa-copy"></i>
                Copy from Input
            </button>
            <button id='BtnShareInvoice' class="btn btn-success"> Share <i class="fa-brands fa-whatsapp"></i></button>
            <a id="btnNextStep4" href="javascript: void(0);" class="btn btn-primary seller-next-btn ">
                Next
                <i class="bx bx-chevron-right ms-1"></i>
            </a>
        </div>
    </div>
</div>
<script src="{{ asset('/js/jquery-3.7.1.min.js') }}"></script>
<script src="{{ asset('plugins/bootstrap5-toggle/js/bootstrap5-toggle.ecmas.min.js') }}" defer></script>
<script>
    $("#btnCopyOrderDetail").on("click", function() {
        var FullName = $("#FullName").val();
        var ContactNumber = $("#ContactNumber").val();
        var Battery = [];
        $(".add-table-items tbody tr").each(function() {
            var batteryName = $(this).find("input[name='BatteryNameCheckout[]']").val();
            var quantity = $(this).find("input[name='QtyCheckout[]']").val();
            var price = $(this).find("input[name='SubtotalRow[]']").val();
            Battery.push({
                batteryName: batteryName,
                quantity: quantity,
                price: price
            });
        });
        var subtotal = $("#subtotal").val();
        var tax = $("#tax").val();
        var discount = $("#discount").val();
        var TotalAmountHidden = $("#TotalAmountHidden").val();
        var VehicleCustomer = $('#VehicleCustomer').val();
        var Latitude = $("#Latitude").val();
        var Longitude = $("#Longitude").val();
        var AddressCustomer = $("#AddressCustomer").val();
        var typeDiscount = $("#type-discount").val();

        var data = {
            FullName: FullName,
            ContactNumber: ContactNumber,
            Battery: Battery,
            Subtotal: subtotal,
            Tax: tax,
            Discount: discount,
            TotalAmount: TotalAmountHidden,
            VehicleCustomer: VehicleCustomer,
            Latitude: Latitude,
            Longitude: Longitude,
            AddressCustomer: AddressCustomer,
            typeDiscount: typeDiscount,
            _token: $('meta[name="csrf-token"]').attr('content')
        };

        $.ajax({
            url: "/quotation/checkout/copy",
            type: "POST",
            data: data,
            success: function(response) {
                let ResponseData = JSON.parse(response);
                if (ResponseData.status == true) {
                    var copyText = ResponseData.message;
                    var textArea = document.createElement("textarea");
                    textArea.value = copyText;
                    document.body.appendChild(textArea);
                    textArea.select();
                    document.execCommand('copy');
                    document.body.removeChild(textArea);
                    swal.fire("Copied!", "Personal Details Copied", "success");
                } else {
                    swal.fire("Error!", ResponseData.message, "error");
                }
            },
            error: function(xhr, status, error) {
                swal.fire("Error!", error, "error");
            }
        });
    });


    // button add row 
    $(document).on('click', '.add-row', function() {
        var html = '';
        html += '<tr>';
        html +=
            '<td><input type="hidden" name="BatteryIdCheckout[]" id="BatteryIdCheckout" class="BatteryIdCheckout"><input type="text" name="BatteryNameCheckout[]" id="BatteryNameCheckout" class="form-control BatteryNameCheckout" value="" autocomplete="off"><div id="showAutoCompleteBattery" class="autocomplete-suggestions"></div></td>';
        html +=
            '<td><input type="number" name="QtyCheckout[]" id="QtyCheckout" class="form-control QtyCheckout" value="1"></td>';
        html +=
            '<td><div class="input-group"><input type="text" name="GrossPrice[]" id="GrossPrice" class="form-control GrossPrice text-end" value="" disabled></div></td>';
        html +=
            '<td><div class="input-group"><input type="text" name="TaxRow[]" id="TaxRow" class="form-control TaxRow text-end" value="" disabled></div></td>';
        html +=
            '<td><div class="input-group"><input type="text" name="PriceTaxRow[]" id="PriceTaxRow" class="form-control PriceTaxRow text-end" value="" disabled></div></td>';
        html +=
            '<td><div class="input-group"><input type="text" name="DiscountRow[]" id="DiscountRow" class="form-control DiscountRow text-end" value=""></div></td>';
        html +=
            '<td><div class="input-group"><input type="text" name="NetPrice[]" id="NetPrice" class="form-control NetPrice text-end" value="" disabled></div></td>';
        html +=
            '<td><div class="input-group"><input type="text" name="SubtotalRow[]" id="SubtotalRow" class="form-control SubtotalRow text-end" value="" disabled></div></td>';
        html +=
            '<td><button type="button" class="btn btn-danger btn-sm remove-row"><i class="fas fa-trash"></i></button></td>';
        html += '</tr>';
        $('.add-table-items tbody').append(html);
    });

    function formatNumber(num) {
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    // autocomplete battery
    $(document).on('keyup', '.BatteryNameCheckout', function() {
        var query = $(this).val();
        var row = $(this).closest('tr'); // Mendapatkan baris yang sedang diubah
        if (query != '') {
            $.ajax({
                url: "/quotation/battery/autoComplete",
                method: "GET",
                data: {
                    query: query
                },
                success: function(data) {
                    var suggestions = '';
                    data.forEach(function(battery) {
                        if (battery.discount == 0) {
                            battery.discount = 0;
                            battery.price_net = battery.price_retail_original;
                            battery.tax = battery.tax;
                            battery.price_tax = battery.price_net + (battery.price_net *
                                battery.tax / 100);
                            battery.net_price = battery.price_tax - (battery.price_tax *
                                battery.discount / 100);
                        } else {
                            battery.price_net = battery.price_retail_original;
                            battery.tax = battery.tax;
                            battery.price_tax = battery.price_net + (battery.price_net *
                                battery.tax / 100);
                            battery.net_price = battery.price_tax - (battery.price_tax *
                                battery.discount / 100);
                            battery.discount = 0;
                        }
                        suggestions += '<div class="suggestion-item" data-id="' +
                            battery
                            .id + '" data-name="' + battery.name +
                            '" data-price-retail="' +
                            formatNumber(battery.price_retail) + '" data-discount="' +
                            battery.discount +
                            '" data-price-net="' + formatNumber(battery.price_net) +
                            '" data-id="' + formatNumber(battery.id) +
                            '" data-battery-tax="' +
                            formatNumber(battery.tax) + '" data-price-tax="' +
                            formatNumber(battery.price_tax) + '" data-net_price-tax="' +
                            formatNumber(battery.net_price) + '">' +
                            battery.name +
                            '</div>';
                    });
                    row.find('#showAutoCompleteBattery').html(suggestions).show();
                }
            });
        } else {
            row.find('#showAutoCompleteBattery').hide();
        }
    });

    $(document).on('click', '.suggestion-item', function() {
        var row = $(this).closest('tr');
        var batteryName = $(this).data('name');
        var priceRetail = $(this).data('price-retail');
        var discount = $(this).data('discount');
        var priceNet = $(this).data('net_price-tax');
        var subtotal = $(this).data('net_price-tax');
        var batteryId = $(this).data('id');
        var batteryTax = $(this).data('battery-tax');
        var priceTax = $(this).data('price-tax');

        row.find('.BatteryNameCheckout').val(batteryName);
        row.find('.GrossPrice').val(priceRetail);
        row.find('.DiscountRow').val(discount);
        row.find('.NetPrice').val(priceNet);
        row.find('.SubtotalRow').val(subtotal);
        row.find('.BatteryIdCheckout').val(batteryId);
        row.find('.TaxRow').val(batteryTax);
        row.find('.PriceTaxRow').val(priceTax);

        row.find('#showAutoCompleteBattery').hide();

        calculateTotalAmount();
    });

    function parseFormattedNumber(num) {
        return parseFloat(num.replace(/\./g, '').replace(',', '.'));
    }


    function calculateTotalAmount() {
        var subtotal = 0;
        $('.add-table-items tbody tr').each(function() {
            var row = $(this);
            var qty = parseFloat(row.find('.QtyCheckout').val()) || 0;
            var subtotalRow = parseFormattedNumber(row.find('.SubtotalRow').val().replace(/,/g,
                    '')) ||
                0;
            subtotal += subtotalRow;
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
</script>