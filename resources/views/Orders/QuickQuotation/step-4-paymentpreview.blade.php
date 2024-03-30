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
                                        <th>Link Tokopedia</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($dataProduct as $data)
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
                                        <td>
                                            <input readonly type="text" name="PricePaymentDetails[]"
                                                class="form-control PricePaymentDetails" value="{{ $data['price'] }}">
                                        </td>
                                        @if (isset($DistributorShop) && !empty($DistributorShop))
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
                                    <input type="checkbox" class="CheckMidtrans" name="CheckMidtrans">
                                    <span class="checkmark"></span> Use Payment Link Midtrans
                                </label>
                            @elseif (isset($DistributorShop) && empty($DistributorShop))
                                <label class="custom_check w-100">
                                    <input type="checkbox" class="CheckMidtrans" name="CheckMidtrans" disabled checked>
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
                            <p>Tax <span>{{ $tax }}</span></p>
                            <p>Discount <span>{{ $Discount }}</span></p>
                            <p>Extra Discount <span>{{ $ExtraDiscount }}</span></p>
                        </div>
                        <div class="invoice-total-footer">
                            <h4>Total Amount <span>Rp. {{ number_format($TotalAmount, 0, ',', '.') }}</span></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="invoice-sign text-end">
            <div class="form-group local-forms">
                <label for="company-contact">Template Message <span class="login-danger">*</span></label>
                <textarea class="form-control TemplateMessageStep4" name="TemplateMessageStep4" placeholder="Enter Addres Customer"
                    required autocomplete="off">Hello, <NAME> this is your Payment Link : <PAYMENTLINK> Please make a payment and confirm the payment to us. Thank you.
                </textarea>
            </div>
        </div>
    </div>
</div>

<div class="clipboard visually-hidden">
    <textarea cols="30" rows="10" class="CopyPaymentDetails" name="CopyPaymentDetails"></textarea>
</div>

<script>
    function updateCopyPaymentDetails() {
        var FullName = $("#FullName").val();
        var links = []; // Mendefinisikan variabel links di luar event handler

        $("#CheckMidtrans").change(function() {
            links = []; // Mengosongkan links setiap kali status checkbox berubah
            if ($(this).prop("checked")) {
                var linkMidtrans = $("#LinkPaymentMidtrans").val();
                links.push(linkMidtrans);
            } else {
                $(".LinkPayment").each(function() {
                    var value = $(this).val();
                    links.push(value);
                });
            }

            // Pemrosesan link harus dilakukan di dalam event handler change
            // untuk memastikan bahwa nilai links telah diperbarui
            var TemplateMessage = $(".TemplateMessageStep4").val();
            var copyPaymentDetails = TemplateMessage.replace("<NAME>", FullName);
            var linksString = links.join(", "); // implode link menggunakan koma
            copyPaymentDetails = copyPaymentDetails.replace("<PAYMENTLINK>", linksString);

            // Memperbarui HTML elemen dengan class CopyPaymentDetails
            $(".CopyPaymentDetails").html(copyPaymentDetails);
        });
    }

    // Panggil fungsi updateCopyPaymentDetails saat dokumen siap
    $(document).ready(function() {
        updateCopyPaymentDetails();
    });

    $("#btnCopyPaymentDetails").on("click", function() {
        updateCopyPaymentDetails();
        swal.fire("Success", "Payment Detail Copied", "success");
    });
</script>
