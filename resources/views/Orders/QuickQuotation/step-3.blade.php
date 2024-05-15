<link rel="stylesheet" href="{{ asset('plugins/bootstrap5-toggle/css/bootstrap5-toggle.min.css') }}">
<div>
    <div class="mb-4">
        <h5>Checkout Page</h5>
    </div>
    <div id="CheckoutPreview"></div>

    <div class="row">
        <div class="col">
            <a href="javascript: void(0);" class="btn btn-primary seller-previous-btn"><i
                    class="bx bx-chevron-left me-1"></i> Previous</a>
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
        var Battery = [];
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
        var subtotal = $("#subtotal").val();
        var tax = $("#tax").val();
        var discount = $("#discount").val();
        var TotalAmountHidden = $("#TotalAmountHidden").val();

        var data = {
            FullName: FullName,
            Battery: Battery,
            Subtotal: subtotal,
            Tax: tax,
            Discount: discount,
            TotalAmount: TotalAmountHidden,
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
</script>
