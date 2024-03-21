<div>
    <div class="mb-4">
        <h5>Product Recomendation Display</h5>
    </div>

    <div id="MapsDistributorRecomendation">
    </div>

    <h6 class="mt-3">Our Battery Recommendation</h6>
    <div class="row" id="ResultRecommendationBattery">
    </div>
    <div class="form-group local-forms">
        <label for="company-contact">Template Message <span class="login-danger">*</span></label>
        <textarea class="form-control" id="TemplateMessageStep2" name="TemplateMessageStep2" placeholder="Enter Addres Customer"
            required autocomplete="off">Hello, <NAME> Hello, this is our recommended battery according to your vehicle type : Battery Name : <BATTERYNAME> <ENTER> Battery Capacity : <BATTERYCAPACITY> <ENTER> Battery Price : <BATTERYPRICE> <ENTER> Battery Warranty : <BATTERYWARRANTY>
        </textarea>

    </div>
    <div class="row">
        <div class="col">
            <a href="javascript: void(0);" class="btn btn-primary seller-previous-btn"><i
                    class="bx bx-chevron-left me-1"></i> Previous</a>
        </div>

        <div class="col text-end">
            <a id="btnCopyDetailProduct" class="btn clip-btn btn-primary" href="javascript:;"
                data-clipboard-action="copy" data-clipboard-target="#CopyDetailProduct">
                <i class="far fa-copy"></i>
                Copy from Input
            </a>
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

<div class="clipboard visually-hidden">
    <textarea cols="30" rows="10" id="CopyDetailProduct" name="CopyDetailProduct"></textarea>
</div>


<script>
    function getItemCheck() {
        var Battery = $("input[name='CheckBattery[]']:checked").map(function() {
            return $(this).val();
        }).get();

        var TemplateMessageStep2 = $('#TemplateMessageStep2').val();

        if (Battery.length == 0) {
            swal.fire("Error!", "Please select battery", "error");
            return;
        }

        if (TemplateMessageStep2.includes('<BATTERYNAME>') == false || TemplateMessageStep2
            .includes('<BATTERYCAPACITY>') == false || TemplateMessageStep2
            .includes('<BATTERYPRICE>') == false || TemplateMessageStep2
            .includes('<BATTERYWARRANTY>') == false) {
            swal.fire("Error!",
                "Template Message must contain BATTERYNAME, BATTERYPRICE, BATTERYWARRANTY",
                "error");
            return;
        }

        var data = {
            'Battery': Battery,
            'TemplateMessageStep2': TemplateMessageStep2,
            _token: $('meta[name="csrf-token"]').attr('content')
        };

        $.ajax({
            url: "/get-battery-copy-detail",
            type: "POST",
            data: data,
            success: function(response) {
                let ResponseData = JSON.parse(response);
                if (ResponseData.status == true) {
                    $('#CopyDetailProduct').val(ResponseData.message);
                } else {
                    swal.fire("Error!", response.message, "error");
                }
            }
        });
    }

    $("#btnCopyDetailProduct").click(function() {
        getItemCheck();
        swal.fire("Copied!", "Personal Details Copied", "success");
    });
</script>
