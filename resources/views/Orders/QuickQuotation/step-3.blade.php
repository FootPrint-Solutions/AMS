<div>
    <div class="mb-4">
        <h5>Chechout Page</h5>
    </div>
    <div id="CheckoutPreview"></div>

    <div class="row">
        <div class="col">
            <a href="javascript: void(0);" class="btn btn-primary seller-previous-btn"><i
                    class="bx bx-chevron-left me-1"></i> Previous</a>
        </div>

        <div class="col text-end">
            <a id="btnCopyOrderDetail" class="btn clip-btn btn-primary" href="javascript:;" data-clipboard-action="copy"
                data-clipboard-target="#CopyOrderDetail">
                <i class="far fa-copy"></i>
                Copy from Input
            </a>
            <button id='BtnShareInvoice' class="btn btn-success"> Share <i class="fa-brands fa-whatsapp"></i></button>
            <a id="btnNextStep4" href="javascript: void(0);" class="btn btn-primary seller-next-btn ">
                Next
                <i class="bx bx-chevron-right ms-1"></i>
            </a>
        </div>
    </div>
</div>

<script>
    function updateCopyOrderDetail() {
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

        var FullName = $("#FullName").val();
        var tax = $("#tax").val();
        var discount = $("#discount").val();
        var extraDiscount = $("#Extradiscount").val();
        var totalAmount = $("#TotalAmount").text();
        var techniciansName = $("#techniciansName option:selected").text();
        var techniciansPhone = $("#techniciansPhone").val();

        var TemplateMessage = $("#TemplateMessageStep3").val();
        var copyOrderDetail = TemplateMessage.replace("<NAME>", FullName);
        Battery.forEach(function(battery) {
            copyOrderDetail = copyOrderDetail.replace("<BATTERYNAME>", battery.batteryName);
            copyOrderDetail = copyOrderDetail.replace("<QUANTITY>", battery.quantity);
            copyOrderDetail = copyOrderDetail.replace("<BATTERYPRICE>", battery.price);
        });
        copyOrderDetail = copyOrderDetail.replace("<TAX>", tax);
        copyOrderDetail = copyOrderDetail.replace("<DISCOUNT>", discount);
        copyOrderDetail = copyOrderDetail.replace("<EXTRADISCOUNT>", extraDiscount);
        copyOrderDetail = copyOrderDetail.replace("<TOTALAMOUNT>", totalAmount);
        copyOrderDetail = copyOrderDetail.replace("<NAMETECHNICIAN>", techniciansName);
        copyOrderDetail = copyOrderDetail.replace("<PHONETECHNICIAN>", techniciansPhone);

        $("#CopyOrderDetail").val(copyOrderDetail);
    }

    $("#btnCopyOrderDetail").on("click", function() {
        updateCopyOrderDetail();
        var copyText = document.getElementById("CopyOrderDetail");
        swal.fire("Success", "Order Detail Copied", "success");
    });
</script>
