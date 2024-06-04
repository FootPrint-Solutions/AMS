<div>
    <div class="mb-4">
        <h5>Enter Personal Detail</h5>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="form-group local-forms">
                <div class="input-group">
                    <span class="input-group-text border-end country-code">+62</span>
                    <label for="company-name">Contact Number <span class="login-danger">*</span></label>
                    <input type="number" class="form-control" id="ContactNumber" name="ContactNumber"
                        placeholder="Enter Contract Number" value="" required autocomplete="off">
                </div>
                <div id="AutoCompleteFullNameCustomerContact"></div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="form-group local-forms">
                <label for="company-name">Email </label>
                <input type="email" class="form-control" id="EmailCustomer" name="EmailCustomer"
                    placeholder="Enter Email" value="" autocomplete="off">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6">
            <div class="form-group local-forms">
                <label for="company-name">Full Name <span class="login-danger">*</span></label>
                <input type="text" class="form-control" id="FullName" name="FullName" placeholder="Enter Full Name"
                    value="" required autocomplete="off">
                <div id="AutoCompleteFullNameCustomer"></div>
                <span class="badge bg-success" id="UserExist" style='display:none;'>User
                    Exist</span>
                <span class="badge bg-warning" id="UserNotExist" style='display:none;'>New
                    User</span>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="form-group local-forms">
                <label for="company-contact">Address Customer <span class="login-danger">*</span></label>


                {{-- <textarea class="form-control" id="AddressCustomer" name="AddressCustomer" placeholder="Enter Addres Customer"
                                                    value="" required autocomplete="off"></textarea> --}}

                <input type="text" class="form-control" id="AddressCustomer" name="AddressCustomer">
            </div>

            <input type="hidden" name="IdCustomer" id="IdCustomer" value="">
            <input type="hidden" name="Latitude" id="Latitude" value="">
            <input type="hidden" name="Longitude" id="Longitude" value="">
        </div>

        <div class="col-lg-6 d-none">
            <div id="map"></div>
        </div>

        <div class="col-lg-6">
            <div class="form-group local-forms">
                <label for="company-name">Distributor Shop </label>
                <select class="form-select" id="DistributorShopId" name="DistributorShopId" required>
                    <option value="">Select Distributor Shop</option>
                </select>
            </div>
        </div>

        <div class="col-lg-6">
            <button type="button" class="btn btn-primary" id="btnShowMaps" onclick="showMapsDistributor()">Show
                Maps</button>
        </div>
    </div>

    <div class="modal fade" id="modalMapsDistributor" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Maps Distributor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div id="MapsDistributorRecomendation"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
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
            <button id='BtnShareBattery' class="btn btn-success"> Share <i
                    class="fa-brands fa-whatsapp"></i></button>
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
                    swal.fire("Copied!", "Product Details Copied", "success");
                } else {
                    swal.fire("Error!", response.message, "error");
                }
            }
        });
    });

    $("#ContactNumber").on('keyup', function() {
        var input = $(this).val();
        if (input.length > 0) {
            $.ajax({
                url: "/quotation/customer/findbycontact",
                type: "GET",
                data: {
                    input: input
                },
                success: function(data) {
                    // let suggestions = data.map(item => item.name);
                    if (data.length > 0) {
                        displaySuggestionsContact(data);
                    } else {
                        $('#AutoCompleteFullNameCustomerContact').html('');
                        $("#EmailCustomer").val('');
                        // $("#FullName").val('');
                        $("#AddressCustomer").val('');
                        $("#IdCustomer").val('');
                        $("#Latitude").val('');
                        $("#Longitude").val('');
                        $('#UserExist').hide();
                        $('#UserNotExist').show();
                    }
                }
            });
        } else {
            $('#AutoCompleteFullNameCustomer').html('');
            $("#EmailCustomer").val('');
            // $("#FullName").val('');
            $("#AddressCustomer").val('');
            $("#Latitude").val('');
            $("#Longitude").val('');
            $("#IdCustomer").val('');
            var IdCustomer = $("#IdCustomer").val();
            if (IdCustomer != '') {
                $('#UserExist').show();
                $('#UserNotExist').hide();
            } else {
                $('#UserExist').hide();
                $('#UserNotExist').show();
            }
        }
    });

    $('#FullName').on('keyup', function() {
        var input = $(this).val();
        if (input.length > 0) {
            $.ajax({
                url: "/quotation/customer/find",
                type: "GET",
                data: {
                    input: input
                },
                success: function(data) {
                    // let suggestions = data.map(item => item.name);
                    if (data.length > 0) {
                        displaySuggestions(data);
                    } else {
                        $('#AutoCompleteFullNameCustomer').html('');
                        $("#EmailCustomer").val('');
                        // $("#ContactNumber").val('');
                        $("#AddressCustomer").val('');
                        $("#IdCustomer").val('');
                        $("#Latitude").val('');
                        $("#Longitude").val('');
                        $('#UserExist').hide();
                        $('#UserNotExist').show();
                    }
                }
            });
        } else {
            $('#AutoCompleteFullNameCustomer').html('');
            $("#EmailCustomer").val('');
            // $("#ContactNumber").val('');
            $("#AddressCustomer").val('');
            $("#Latitude").val('');
            $("#Longitude").val('');
            $("#IdCustomer").val('');
            var IdCustomer = $("#IdCustomer").val();
            if (IdCustomer != '') {
                $('#UserExist').show();
                $('#UserNotExist').hide();
            } else {
                $('#UserExist').hide();
                $('#UserNotExist').show();
            }
        }
    });

    function displaySuggestions(suggestions) {
        $('#AutoCompleteFullNameCustomer').empty();

        suggestions.forEach(function(item) {
            $('#AutoCompleteFullNameCustomer').append('<div class="suggestion">' + item.name +
                '</div>');
        });

        $('.suggestion').click(function() {
            var index = $(this).index();

            $('#FullName').val(suggestions[index].name);
            var cleanNumber = suggestions[index].contact.replace(/\D/g, '');
            $('#ContactNumber').val(cleanNumber);
            $('#EmailCustomer').val(suggestions[index].email);
            $('#AddressCustomer').val(suggestions[index].address);
            $('#IdCustomer').val(suggestions[index].id);
            $("#Latitude").val(suggestions[index].latitude);
            $("#Longitude").val(suggestions[index].longitude);
            $('#AutoCompleteFullNameCustomer').empty();
            // call google maps
            // initMap();

            var IdCustomer = $("#IdCustomer").val();
            if (IdCustomer != '') {
                $('#UserExist').show();
                $('#UserNotExist').hide();

                // get vehcile by  id 
                // $.ajax({
                //     url: "/quotation/customer/vehicle/find",
                //     type: "GET",
                //     data: {
                //         id: IdCustomer,
                //     },
                //     success: function(data) {
                //         var vehicles = data;
                //         $('#VehicleCustomer').val(vehicles);
                //         $('#VehicleCustomer').trigger('change');
                //     }
                // });
            } else {
                $('#UserExist').hide();
                $('#UserNotExist').show();
            }
        });
    }

    function displaySuggestionsContact(suggestions) {
        $('#AutoCompleteFullNameCustomerContact').empty();

        suggestions.forEach(function(item) {
            $('#AutoCompleteFullNameCustomerContact').append('<div class="suggestion">' + item.name +
                ' - 62' + item.contact + '</div>');
        });

        $('.suggestion').click(function() {
            var index = $(this).index();

            $('#FullName').val(suggestions[index].name);
            var cleanNumber = suggestions[index].contact.replace(/\D/g, '');
            $('#ContactNumber').val(cleanNumber);
            $('#EmailCustomer').val(suggestions[index].email);
            $('#AddressCustomer').val(suggestions[index].address);
            $('#IdCustomer').val(suggestions[index].id);
            $("#Latitude").val(suggestions[index].latitude);
            $("#Longitude").val(suggestions[index].longitude);
            $('#AutoCompleteFullNameCustomerContact').empty();
            // call google maps
            initMap();

            var IdCustomer = $("#IdCustomer").val();
            if (IdCustomer != '') {
                $('#UserExist').show();
                $('#UserNotExist').hide();

                // get vehcile by  id 
                // $.ajax({
                //     url: "/quotation/customer/vehicle/find",
                //     type: "GET",
                //     data: {
                //         id: IdCustomer,
                //     },
                //     success: function(data) {
                //         var vehicles = data;
                //         $('#VehicleCustomer').val(vehicles);
                //         $('#VehicleCustomer').trigger('change');
                //     }
                // });
            } else {
                $('#UserExist').hide();
                $('#UserNotExist').show();
            }
        });
    }


    $("#BtnShareBattery").click(function() {
        var button = $(this);
        button.prop('disabled', true);
        button.html(
            '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...'
        );
        var FullName = $("#FullName").val();
        var EmailCustomer = $("#EmailCustomer").val();
        var VehicleCustomer = $("#VehicleCustomer").val();
        var Battery = $("input[name='CheckBattery[]']:checked").map(function() {
            return $(this).val();
        }).get();
        var contactNumber = $("#ContactNumber").val();

        if (Battery.length == 0) {
            swal.fire("Error!", "Please select battery", "error");
            button.prop('disabled', false);
            button.html(
                "<i class='fa-brands fa-whatsapp'></i> Share"
            );
            return;
        }

        if (FullName == '') {
            swal.fire("Error!", "Full Name is required", "error");
            button.prop('disabled', false);
            button.html(
                "<i class='fa-brands fa-whatsapp'></i> Share"
            );
            return;
        }

        Battery.forEach(function(battery) {
            var data = {
                FullName: FullName,
                Battery: battery,
                ContactNumber: contactNumber,
                _token: $('meta[name="csrf-token"]').attr('content')
            };

            $.ajax({
                url: "/quotation/battery/share",
                type: "POST",
                data: data,
                success: function(data) {
                    let ResponseData = JSON.parse(data);
                    if (ResponseData.status) {
                        Swal.fire({
                            title: "Success",
                            text: ResponseData.message,
                            icon: "success",
                        });
                        button.prop('disabled', false);
                        button.html(
                            "<i class='fa-brands fa-whatsapp'></i> Share"
                        );
                    } else {
                        Swal.fire({
                            title: "Error",
                            text: ResponseData.message ||
                                "Something went wrong, please try again later",
                            icon: "error",
                        });
                        button.prop('disabled', false);
                        button.html(
                            "<i class='fa-brands fa-whatsapp'></i> Share"
                        );
                    };
                }
            });
        });
    });


    function showMapsDistributor() {
        var address = $('#AddressCustomer').val();
        var latitude = $('#Latitude').val();
        var longitude = $('#Longitude').val();
        var idCustomer = $('#IdCustomer').val();
        var data = {
            address: address,
            latitude: latitude,
            longitude: longitude,
        };

        $.ajax({
            url: "/quotation/customer/maps/near",
            type: "GET",
            data: data,
            success: function(data) {
                $("#MapsDistributorRecomendation").html(data);
                $("#modalMapsDistributor").modal('show');
            }
        });

    }
</script>
