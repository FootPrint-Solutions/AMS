<div class="card invoice-info-card">
    <div class="card-body">
        <div class="invoice-item invoice-item-one">
            <div class="row">
                <div class="col-md-6">
                    <div class="invoice-head">
                        <h2>Invoice</h2>
                        <p>Invoice Number : {{ $InvoiceNumber }}</p>
                        <input type="hidden" name="invoiceNumber" id="invoiceNumber" value="{{ $InvoiceNumber }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="invoice-info">
                        {{-- Billed to --}}
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
                            {{ $AddressCustomer }}, {{ $alternativeAddress }}
                        </p>
                    </div>
                </div>
                <div class="col-md-6">
                    {{-- Payment Details --}}
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class=" table table-center mb-0">
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
                    </tr>
                </thead>
                <tbody>
                    @foreach ($dataProduct as $data)
                        <tr>
                            <td>
                                <input type="text" name="BatteryNamePaymentDetails[]"
                                    class="form-control BatteryNamePaymentDetails" value="{{ $data['name'] }}" readonly>
                            </td>
                            <td>
                                <input readonly type="number" name="QtyPaymentDetails[]"
                                    class="form-control QtyPaymentDetails" value="{{ $data['qty'] }}">
                            </td>
                            <td> <input readonly type="text" name="PricePaymentDetails2[]"
                                    class="form-control PricePaymentDetails2" value="{{ $data['price'] }}">
                                <input readonly type="hidden" name="PricePaymentDetails[]"
                                    class="form-control PricePaymentDetails" value="{{ $data['price'] }}">
                            </td>
                            <td>
                                <input readonly type="text" name="TaxPaymentDetails[]"
                                    class="form-control TaxPaymentDetails" value="{{ $data['TaxRow'] }}">
                            </td>
                            <td>
                                <input readonly type="text" name="PriceTaxPaymentDetails[]"
                                    class="form-control PriceTaxPaymentDetails" value="{{ $data['TaxPriceRow'] }}">
                            </td>
                            <td>
                                <input readonly type="number" name="DiscountPaymentDetails[]"
                                    class="form-control DiscountPaymentDetails" value="{{ $data['DiscountRow'] }}">
                            </td>
                            <td>
                                <input readonly type="text" name="NetPricePaymentDetails[]"
                                    class="form-control NetPricePaymentDetails" value="{{ $data['NetPrice'] }}">
                            </td>
                            <td>
                                <input readonly type="text" name="SubtotalPaymentDetails[]"
                                    class="form-control SubtotalPaymentDetails" value="{{ $data['SubtotalRow'] }}">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>


        <div class="row align-items-center justify-content-center">
            <div class="col-lg-6 col-md-6">
                <div class="invoice-total-card">
                    <div class="invoice-total-box">
                        {{-- SELECT OPTION PAYMENT METHOD --}}

                        <div class="invoice-total-inner">
                            <h5>Payment Method</h5>
                            <select class="form-select" name="PaymentMethod" id="PaymentMethod">
                                @foreach ($PaymentMethod as $pm)
                                    <option value="{{ $pm['id'] }}">{{ $pm['name'] }}</option>
                                @endforeach
                            </select>

                            <div id="MidtransPaymentLink" class="d-none mt-3">
                                @if (isset($DistributorShop) && !empty($DistributorShop))
                                    <label class="custom_check w-100">
                                        <input type="checkbox" class="CheckMidtrans" name="CheckMidtrans"
                                            id="CheckMidtrans" checked readonly>
                                        <span class="checkmark"></span> Use Payment Link Midtrans
                                    </label>
                                @elseif (isset($DistributorShop) && empty($DistributorShop))
                                    <label class="custom_check w-100">
                                        <input type="checkbox" class="CheckMidtrans" name="CheckMidtrans"
                                            id="CheckMidtrans" disabled checked readonly>
                                        <span class="checkmark"></span> Use Payment Link Midtrans
                                    </label>
                                @endif
                                <p>Payment Link : </p>
                                <p>{{ $snapToken }}</p>
                                <input class="linkMidtrans" id="LinkPaymentMidtrans" type="hidden"
                                    name="LinkPaymentMidtrans" value="{{ $snapToken }}">
                            </div>
                        </div>
                        {{-- Total Amount --}}
                    </div>
                </div>

            </div>
            <div class="col-lg-6 col-md-6">
                <div class="invoice-total-card">
                    <div class="invoice-total-box">
                        <div class="invoice-total-inner">
                            <p>Subtotal <span>Rp. {{ number_format($Subtotal, 0, ',', '.') }}</span></p>
                            <div class="d-none">
                                @if ($typeDiscount == 'rupiah')
                                    <p>Discount <span>Rp. {{ number_format($Discount, 0, ',', '.') }}</span></p>
                                @else
                                    <p>Discount (%) <span>{{ $Discount }}</span></p>
                                @endif
                            </div>
                            {{-- <p>Tax (%) <span>{{ $tax }}</span></p> --}}
                            {{-- <p>Extra Discount <span>{{ $ExtraDiscount }}</span></p> --}}
                        </div>
                        <div class="invoice-total-footer">
                            <h4>Total Amount <span>Rp. {{ number_format($TotalAmount, 0, ',', '.') }}</span></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $("#btnCopyPaymentDetails").on("click", function() {
        var FullName = $("#FullName").val();
        var ContactNumber = $("#ContactNumber").val();
        var VehicleCustomer = $('#VehicleCustomer').val();
        var Latitude = $("#Latitude").val();
        var Longitude = $("#Longitude").val();
        var AddressCustomer = $("#AddressCustomer").val();
        var Battery = [];
        var QtyTabel = []; // Menambahkan array untuk menyimpan kuantitas
        var PriceTabel = []; // Menambahkan array untuk menyimpan harga
        var links = [];
        var Battery = [];
        var InvoiceNumber = $("#invoiceNumber").val();
        // $(".add-table-items tbody tr").each(function() {
        //     var batteryName = $(this).find("input[name='BatteryNameCheckout[]']").val();
        //     var quantity = $(this).find("input[name='QtyCheckout[]']").val();
        //     var price = $(this).find("input[name='PriceCheckout[]']").val();
        //     Battery.push({
        //         batteryName: batteryName,
        //         quantity: quantity,
        //         price: price
        //     });
        // });
        var IsMidtrans = $("#CheckMidtrans").prop("checked");
        if (IsMidtrans) {
            var linkMidtrans = $("#LinkPaymentMidtrans").val();
            links.push(linkMidtrans);
            var IsMidtrans = "midtrans";
        } else {
            $(".LinkPayment").each(function() {
                var value = $(this).val();
                links.push(value);
            });
            var IsMidtrans = "not midtrans";
        }
        $(".add-table-items tbody tr").each(function() {
            var batteryName = $(this).find("input[name='BatteryNameCheckout[]']").val();
            var quantity = $(this).find("input[name='QtyCheckout[]']").val();
            var price = $(this).find("input[name='NetPrice[]']").val();
            Battery.push({
                batteryName: batteryName,
                quantity: quantity,
                price: price
            });
            QtyTabel.push(quantity); // Menambahkan kuantitas ke dalam array
            PriceTabel.push(price); // Menambahkan harga ke dalam array
        });

        var subtotal = $("#subtotal").val();
        var tax = $("#tax").val();
        var discount = $("#discount").val();
        var TotalAmountHidden = $("#TotalAmountHidden").val();
        var PaymentMethod = $("#PaymentMethod").val();
        var typeDiscount = $("#type-discount").val();

        var data = {
            FullName: FullName,
            ContactNumber: ContactNumber,
            Battery: Battery,
            InvoiceNumber: InvoiceNumber,
            IsMidtrans: IsMidtrans,
            links: links,
            Subtotal: subtotal,
            Tax: tax,
            Discount: discount,
            TotalAmount: TotalAmountHidden,
            VehicleCustomer: VehicleCustomer,
            Latitude: Latitude,
            Longitude: Longitude,
            AddressCustomer: AddressCustomer,
            PaymentMethod: PaymentMethod,
            typeDiscount: typeDiscount,
            _token: $('meta[name="csrf-token"]').attr('content')
        };

        $.ajax({
            url: "/quotation/payment-details/copy",
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

    // if PaymentMethod display midtrans
    $("#PaymentMethod").on("change", function() {
        checkpaymentmethod();
    });

    function checkpaymentmethod() {
        var PaymentMethod = $("#PaymentMethod").val();
        if (PaymentMethod == 1) {
            $("#MidtransPaymentLink").removeClass("d-none");
        } else {
            $("#MidtransPaymentLink").addClass("d-none");
        }
    }

    checkpaymentmethod();
</script>
