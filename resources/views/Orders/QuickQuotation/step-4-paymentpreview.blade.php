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
                            {{ $AddressCustomer }}, <br>
                        </p>
                    </div>
                </div>
                <div class="col-md-6">
                    {{-- Payment Details --}}
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
                                    @if (isset($DistributorShop) && !empty($DistributorShop))
                                        <th style="width: 20%;">Platform</th>
                                        <th>Link E-Commerce</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($dataProduct as $data)
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
                                            <input type="text" name="BatteryNamePaymentDetails[]"
                                                class="form-control BatteryNamePaymentDetails"
                                                value="{{ $data['name'] }}" readonly>
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
                                        @if (isset($DistributorShop) && !empty($DistributorShop))
                                            <td>
                                                <input type="text" name="PlatformPayment[]"
                                                    class="form-control PlatformPayment"
                                                    value="{{ $data['platform'] }}" readonly>
                                            </td>
                                            <td>
                                                <input type="text" name="LinkPayment[]"
                                                    class="form-control LinkPayment" value="{{ $data['link'] }}"
                                                    readonly>
                                            </td>
                                        @endif
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
                <div class="invoice-total-card">
                    <div class="invoice-total-box">
                        <div class="invoice-total-inner">
                            @if (isset($DistributorShop) && !empty($DistributorShop))
                                <label class="custom_check w-100">
                                    <input type="checkbox" class="CheckMidtrans" name="CheckMidtrans"
                                        id="CheckMidtrans">
                                    <span class="checkmark"></span> Use Payment Link Midtrans
                                </label>
                            @elseif (isset($DistributorShop) && empty($DistributorShop))
                                <label class="custom_check w-100">
                                    <input type="checkbox" class="CheckMidtrans" name="CheckMidtrans" id="CheckMidtrans"
                                        disabled checked>
                                    <span class="checkmark"></span> Use Payment Link Midtrans
                                </label>
                            @endif
                            <p>Payment Link : </p>
                            <p>{{ $snapToken }}</p>
                            <input class="linkMidtrans" id="LinkPaymentMidtrans" type="hidden"
                                name="LinkPaymentMidtrans" value="{{ $snapToken }}">
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
                            <p>Discount (%) <span>{{ $Discount }}</span></p>
                            <p>Tax (%) <span>{{ $tax }}</span></p>
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
        // check if midtrans is checked
        var links = [];
        var Battery = [];
        var InvoiceNumber = $("#invoiceNumber").val();
        $(".add-table-items tbody tr").each(function() {
            var batteryName = $(this).find("input[name='BatteryNameCheckout[]']").val();
            var quantity = $(this).find("input[name='QtyCheckout[]']").val();
            var price = $(this).find("input[name='PriceCheckout[]']").val();
            Battery.push({
                batteryName: batteryName,
                quantity: quantity,
                price: price
            });
        });
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

        var data = {
            FullName: FullName,
            Links: links,
            IsMidtrans: IsMidtrans,
            Battery: Battery,
            InvoiceNumber: InvoiceNumber,
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
</script>
