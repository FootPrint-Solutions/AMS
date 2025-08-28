<style>
    #AutoCompleteFullNameCustomerContact.autocomplete-dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        z-index: 1000;
        background: #fff;
        border: none;
        border-radius: 0 0 6px 6px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        max-height: 220px;
        overflow-y: auto;
        margin-top: 2px;
        display: none;
    }

    #AutoCompleteFullNameCustomerContact.autocomplete-dropdown.active {
        border: 1px solid #e0e0e0;
        display: block;
    }

    #AutoCompleteFullNameCustomerContact .suggestion {
        padding: 10px 16px;
        cursor: pointer;
        transition: background 0.2s;
        font-size: 15px;
    }

    #AutoCompleteFullNameCustomerContact .suggestion:hover,
    #AutoCompleteFullNameCustomerContact .suggestion.active {
        background: #f1f3f4;
        color: #007bff;
    }

    #AutoCompleteFullNameCustomer.autocomplete-dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        z-index: 1000;
        background: #fff;
        border: none;
        border-radius: 0 0 6px 6px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        max-height: 220px;
        overflow-y: auto;
        margin-top: 2px;
        display: none;
    }

    #AutoCompleteFullNameCustomer.autocomplete-dropdown.active {
        border: 1px solid #e0e0e0;
        display: block;
    }

    #AutoCompleteFullNameCustomer .suggestion {
        padding: 10px 16px;
        cursor: pointer;
        transition: background 0.2s;
        font-size: 15px;
    }

    #AutoCompleteFullNameCustomer .suggestion:hover,
    #AutoCompleteFullNameCustomer .suggestion.active {
        background: #f1f3f4;
        color: #007bff;
    }
</style>
<div>
    <div class="mb-4">
        <h5>Enter Personal Detail</h5>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="form-group local-forms">
                <div class="input-group">
                    <span class="input-group-text border-end country-code">+62</span>
                    <label>Contact Number <span class="login-danger">*</span></label>
                    <input type="number" class="form-control" id="ContactNumber" name="ContactNumber"
                        placeholder="Enter Contract Number" value="" required autocomplete="off">
                </div>
                <div id="AutoCompleteFullNameCustomerContact" class="autocomplete-dropdown"></div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="form-group local-forms">
                <label>Email </label>
                <input type="email" class="form-control" id="EmailCustomer" name="EmailCustomer"
                    placeholder="Enter Email" value="" autocomplete="off">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6">
            <div class="form-group local-forms">
                <label>Full Name <span class="login-danger">*</span></label>
                <input type="text" class="form-control" id="FullName" name="FullName" placeholder="Enter Full Name"
                    value="" required autocomplete="off">
                <div id="AutoCompleteFullNameCustomer" class="autocomplete-dropdown"></div>
                <span class="badge bg-success" id="UserExist" style='display:none;'>User
                    Exist</span>
                <span class="badge bg-warning" id="UserNotExist" style='display:none;'>New
                    User</span>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="form-group local-forms">
                <label>Address Customer</label>

                <input type="text" class="form-control" id="AddressCustomer" name="AddressCustomer">
            </div>
        </div>

        <div class="col-lg-6 d-none">
            <div id="map-customer-address"></div>
        </div>

        <div class="col-lg-6">
            <div class="form-group local-forms">
                <label>Distributor Shop
                    <span class="login-danger">*</span>
                </label>
                <select class="form-select" id="DistributorShopId" name="DistributorShopId" required>
                    <option value="">Select Distributor Shop</option>
                </select>

                {{-- <button type="button" class="btn btn-primary" id="btnShowMaps" onclick="showMapsDistributor()">Show
                    Maps Distributor Shop</button> --}}
            </div>
        </div>

        <div class="col-lg-6">
            <div class="form-group local-forms">
                <label>Coordinates latitude and longitude</label>
                <input type="text" class="form-control" id="cordinates" name="cordinates"
                    placeholder="Enter coordinates (lat, lon)" value="" pattern="^-?\d+(\.\d+)?,\s*-?\d+(\.\d+)?$"
                    title="Please enter valid coordinates in the format: latitude, longitude (e.g., -6.8806598931788505, 107.53427163803686)"
                    onkeyup="sanitizeCoordinates(this)" onfocus="sanitizeCoordinates(this)" required autocomplete="off">
            </div>
        </div>

        <div class="col-lg-6">
            {{-- alternative address --}}
            <div class="form-group local-forms">
                <label>Alternative Address ( Pin Location )</label>
                <textarea name="alternative_address" id="alternative_address" class="form-control" cols="30" rows="10"></textarea>

                <input type="hidden" name="IdCustomer" id="IdCustomer" value="">
                <input type="hidden" name="Latitude" id="Latitude" value="">
                <input type="hidden" name="Longitude" id="Longitude" value="">
            </div>
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
    function sanitizeCoordinates(input) {
        let value = input.value.replace(/[^0-9.,-]/g, '');
        let parts = value.split(',');
        if (parts.length > 2) {
            value = parts[0] + ',' + parts[1];
        }

        let coordinates = value.split(',');
        if (coordinates.length == 2) {
            let latitude = coordinates[0].trim();
            let longitude = coordinates[1].trim();
            $("#Latitude").val(latitude);
            $("#Longitude").val(longitude);
        } else {
            $("#Latitude").val('');
            $("#Longitude").val('');
        }

        input.value = value;
    }


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
        var $autocomplete = $('#AutoCompleteFullNameCustomerContact');
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
                        $autocomplete.addClass('active');
                    } else {
                        $autocomplete.removeClass('active').html('');
                        $("#EmailCustomer").val('');
                        // $("#FullName").val('');
                        $("#alternative_address").val('');
                        $("#IdCustomer").val('');
                        $("#Latitude").val('');
                        $("#Longitude").val('');
                        $('#UserExist').hide();
                        $('#UserNotExist').show();
                    }
                }
            });
        } else {
            $autocomplete.removeClass('active').html('');
            $("#EmailCustomer").val('');
            // $("#FullName").val('');
            $("#alternative_address").val('');
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

    $(document).on('mousedown', function(e) {
        ['#AutoCompleteFullNameCustomerContact', '#AutoCompleteFullNameCustomer'].forEach(function(selector) {
            var $autocomplete = $(selector);
            var $input = selector === '#AutoCompleteFullNameCustomerContact' ? $('#ContactNumber') : $('#FullName');
            if (
                !$autocomplete.is(e.target) &&
                $autocomplete.has(e.target).length === 0 &&
                !$input.is(e.target)
            ) {
                $autocomplete.removeClass('active').empty();
            }
        });
    });

    $('#FullName').on('keyup', function() {
        var input = $(this).val();
        var $autocomplete = $('#AutoCompleteFullNameCustomer');
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
                        $autocomplete.addClass('active');
                    } else {
                        $autocomplete.removeClass('active').html('');
                        $("#EmailCustomer").val('');
                        // $("#ContactNumber").val('');
                        $("#alternative_address").val('');
                        $("#IdCustomer").val('');
                        $("#Latitude").val('');
                        $("#Longitude").val('');
                        $('#UserExist').hide();
                        $('#UserNotExist').show();
                    }
                }
            });
        } else {
            $autocomplete.removeClass('active').html('');
            $("#EmailCustomer").val('');
            // $("#ContactNumber").val('');
            $("#alternative_address").val('');
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

    $('#AddressCustomer').on('input keyup change', function() {
        var addressValue = $(this).val();
        $('#alternative_address').val(addressValue);
    });

    function displaySuggestions(suggestions) {
        var $autocomplete = $('#AutoCompleteFullNameCustomer');
        $autocomplete.empty();
        suggestions.forEach(function(item) {
            $autocomplete.append('<div class="suggestion">' + item.name + '</div>');
        });
        $autocomplete.addClass('active');
        $('.suggestion').click(function() {
            var index = $(this).index();

            $('#FullName').val(suggestions[index].name);
            var cleanNumber = suggestions[index].contact.replace(/\D/g, '');
            $('#ContactNumber').val(cleanNumber);
            $('#EmailCustomer').val(suggestions[index].email);
            $('#AddressCustomer').val(suggestions[index].address);
            $('#alternative_address').val(suggestions[index].address);
            $('#IdCustomer').val(suggestions[index].id);
            $("#Latitude").val(suggestions[index].latitude);
            $("#Longitude").val(suggestions[index].longitude);
            $('#AutoCompleteFullNameCustomer').empty();
            $autocomplete.removeClass('active').empty();
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
            $('#alternative_address').val(suggestions[index].address);
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

        // jika nomer bukan diawali dengan angka 8
        if (contactNumber.charAt(0) != '8') {
            swal.fire("Error!", "Contact Number must start with 8", "error");
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
        var address = $('#alternative_address').val();
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