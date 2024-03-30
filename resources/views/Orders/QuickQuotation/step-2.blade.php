<div>
    <div class="mb-4">
        <h5>Product Recomendation Display</h5>
    </div>

    <div id="MapsDistributorRecomendation">
    </div>

    <h6 class="mt-3">Our Battery Recommendation</h6>
    <div class="row" id="ResultRecommendationBattery">
    </div>
    <div class="row">
        <div class="col">
            <a href="javascript: void(0);" class="btn btn-primary seller-previous-btn"><i
                    class="bx bx-chevron-left me-1"></i> Previous
            </a>
        </div>
        <div class="col text-end">
            <button id="btnCopyDetailProduct" class="btn clip-btn btn-primary">
                <i class="far fa-copy"></i>
                Copy from Input
            </button>
            <button id='BtnShareBattery' class="btn btn-success"> Share <i class="fa-brands fa-whatsapp"></i></button>
            <a href="javascript: void(0);" class="btn btn-primary product-next-btn">Next
                <i class="bx bx-chevron-right ms-1"></i>
            </a>
            <a id="btnNextStep3" href="javascript: void(0);" class="btn btn-primary seller-next-btn d-none">
                Next
                <i class="bx bx-chevron-right ms-1"></i>
            </a>
        </div>
    </div>
</div>



<script>
    $("#btnCopyDetailProduct").click(function() {
        var FullName = $("#FullName").val();
        var Battery = $("input[name='CheckBattery[]']:checked").map(function() {
            return $(this).val();
        }).get();

        if (Battery.length == 0) {
            swal.fire("Error!", "Please select battery", "error");
            return;
        }

        var data = {
            'Battery': Battery,
            'FullName': FullName,
            _token: $('meta[name="csrf-token"]').attr('content')
        };

        $.ajax({
            url: "/quotation/battery/copy",
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
                    swal.fire("Error!", response.message, "error");
                }
            }
        });
    });
</script>
