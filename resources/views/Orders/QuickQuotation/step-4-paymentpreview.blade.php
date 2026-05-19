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
                        <tr @if ($data['BatteryType'] == 'recycle') class="bg-danger" @endif>
                            <td>
                                <input type="text" name="BatteryNamePaymentDetails[]"
                                    class="form-control BatteryNamePaymentDetails" value="{{ $data['name'] }}" readonly>
                                <input type="hidden" name="BatteryType[]" class="BatteryTypeCheckout"
                                    value="{{ $data['BatteryType'] }}">
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


        <div class="row align-items-center justify-content-center mt-3">
            <div class="col-lg-6 col-md-6">
                <div class="invoice-total-card">
                    <div class="invoice-total-box">
                        {{-- SELECT OPTION PAYMENT METHOD --}}

                        <div class="invoice-total-inner">
                            <h5>Payment Method</h5>
                            <select class="form-select" name="PaymentMethod" id="PaymentMethod">
                                @foreach ($PaymentMethod as $pm)
                                    <option value="{{ $pm['id'] }}" data-payment-type="{{ $pm['type'] }}">
                                        {{ $pm['name'] }}</option>
                                @endforeach
                            </select>


                            <div id="InvoicePaymentMarketplace" class="d-none mt-3">
                                <div class="mt-3">
                                    <label for="MarketplaceInvoice" class="form-label">Marketplace Invoice
                                        Number</label>
                                    <input type="text" class="form-control" id="MarketplaceInvoice"
                                        name="MarketplaceInvoice" placeholder="Enter Marketplace Invoice Number"
                                        autocomplete="off">
                                </div>
                            </div>

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

    <div class="card" id="ExpenseSection">
        <div class="card-body">
            {{-- add button add expense --}}
            <button class="btn btn-primary mb-3" id="addExpense">Add Expense</button>
            <div class="table-responsive" id="ExpenseTable">
                <table class="table table-center mb-0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Chart of Account</th>
                            <th>Expense Name</th>
                            <th>Amount</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="ExpenseTableBody">
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3" class="text-end">Total Expense</th>
                            <th colspan="2" id="TotalExpense">Rp. 0</th>
                        </tr>
                    </tfoot>
                </table>

            </div>
        </div>
    </div>
    {{-- @dd($expenses) --}}

    {{-- Modal Add Expense --}}
    <div class="modal fade" id="AddExpenseModal" tabindex="-1" aria-labelledby="AddExpenseModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="AddExpenseModalLabel">Add Expense</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="ExpenseName" class="form-label">Expense Name</label>
                        <select class="form-select" id="ExpenseName" name="ExpenseName">
                            <option value="">Select Expense</option>
                            @foreach ($expenses as $expense)
                                <option value="{{ $expense['id'] }}"
                                    data-chart-of-account="{{ $expense['chart_of_account']['number'] }} - {{ $expense['chart_of_account']['name'] }}">
                                    {{ $expense['chart_of_account']['number'] }} -
                                    {{ $expense['chart_of_account']['name'] }} -
                                    {{ $expense['name'] }}</option>
                            @endforeach
                        </select>

                    </div>
                    <div class="mb-3">
                        <label for="ExpenseAmount" class="form-label">Amount</label>
                        <input type="number" class="form-control" id="ExpenseAmount" name="ExpenseAmount"
                            placeholder="Enter Expense Amount">
                    </div>
                    <button type="submit" class="btn btn-primary" id="btnAddExpense">Add Expense</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $("#addExpense").on("click", function() {
        $("#AddExpenseModal").modal("show");
    });

    $("#ExpenseAmount").on("keypress", function(e) {
        if (e.which == 13) {
            e.preventDefault();
            $("#btnAddExpense").click();
        }
    });

    $("#btnAddExpense").on("click", function() {
        var ExpenseName = $("#ExpenseName").val();
        var ExpenseAmount = $("#ExpenseAmount").val();
        var ExpenseText = $("#ExpenseName option:selected").text();
        var ChartOfAccount = $("#ExpenseName option:selected").data("chart-of-account");
        var ExpenseId = $("#ExpenseName option:selected").val();
        var no = $("#ExpenseTableBody tr").length + 1;

        if (ExpenseName == "" || ExpenseAmount == "") {
            swal.fire("Error!", "Please fill all fields", "error");
            return;
        }

        var newRow = `
            <tr>
                <td>${no}</td>
                <td>${ChartOfAccount}</td>
                <td>${ExpenseText}
                    <input type="hidden" name="ExpenseIds[]" value="${ExpenseId}" class="ExpenseId">
                </td>
                <td>Rp. ${parseFloat(ExpenseAmount).toLocaleString('id-ID')}
                    <input type="hidden" name="ExpenseAmounts[]" value="${ExpenseAmount}" class="ExpenseAmount">
                </td>
                <td><button class="btn btn-danger btn-sm btn-delete-expense"><i class="fas fa-trash"></i></button></td>
            </tr>
        `;

        var totalExpense = 0;

        $("#ExpenseTableBody tr").each(function() {
            var amount = parseFloat($(this).find("td:eq(3)").text().replace("Rp. ", "").replace(/\./g,
                ""));
            totalExpense += amount;
        });
        totalExpense += parseFloat(ExpenseAmount);

        $("#ExpenseTableBody").append(newRow);
        $("#TotalExpense").text("Rp. " + totalExpense.toLocaleString('id-ID'));
        $("#AddExpenseModal").modal("hide");
    });

    $(document).on("click", ".btn-delete-expense", function() {
        var row = $(this).closest("tr");
        var amount = parseFloat(row.find("td:eq(3)").text().replace("Rp. ", "").replace(/\./g, ""));
        var totalExpense = parseFloat($("#TotalExpense").text().replace("Rp. ", "").replace(/\./g, ""));
        totalExpense -= amount;
        $("#TotalExpense").text("Rp. " + totalExpense.toLocaleString('id-ID'));
        row.remove();
    });

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
        var paymentType = $("#PaymentMethod option:selected").data("payment-type");

        if (paymentType == "marketplace") {
            $("#InvoicePaymentMarketplace").removeClass("d-none");
        } else {
            $("#InvoicePaymentMarketplace").addClass("d-none");
        }


        if (PaymentMethod == 1) {
            $("#MidtransPaymentLink").removeClass("d-none");
        } else {
            $("#MidtransPaymentLink").addClass("d-none");
        }
    }

    checkpaymentmethod();
</script>
