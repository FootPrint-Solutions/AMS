<div>
    <div class="mb-4">
        <h5>Enter Your Vehicle Details</h5>
    </div>
    <form id='FormPersonalDetails'>
        <div class="row">

            <div class="col-lg-6">
                <div class="form-group local-forms">
                    <label for="company-name">Members Name </label>
                    <input type="text" class="form-control" id="FullNameStep1" name="FullNameStep1"
                        placeholder="Enter Full Name" value="" required autocomplete="off">
                    <div id="AutoCompleteFullNameCustomerStep1"></div>
                    <span class="badge bg-success" id="UserExistStep1" style='display:none;'>User
                        Exist</span>
                    <span class="badge bg-warning" id="UserNotExistStep1" style='display:none;'>New
                        User</span>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="form-group local-forms">
                    <label for="company-name">Vehicle Customer <span class="login-danger">*</span></label>
                    <select name="VehicleCustomer[]" multiple='multiple' id='VehicleCustomer' class="form-select"
                        aria-label="Default select example">
                        @foreach ($data['Vehicle'] as $vehicle)
                            <option value="{{ $vehicle['id'] }}">
                                {{ trim($vehicle['name']) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-lg-6">

            </div>
        </div>
    </form>

    <br>
    <h5> Product Recomendation Display</h5>
    <div class="row" id="ResultRecommendationBatteryVehicle"></div>
    <div class="row">
        <div class="col text-end">
            <!-- screenshoot button -->
            <button id="screenshot" class="btn btn-primary"><i class="fas fa-camera"></i> Screenshot</button>
            <button id="btnCopyAddress" class="btn clip-btn btn-primary"><i class="far fa-copy"></i>
                Copy from Input</button>
            <a href="javascript: void(0);" class="btn btn-primary seller-next-btn-check">
                Next
                <i class="bx bx-chevron-right ms-1"></i></a>
            <a id="btnNextStep2" href="javascript: void(0);" class="btn btn-primary seller-next-btn d-none">
                Next
                <i class="bx bx-chevron-right ms-1"></i></a>
        </div>
    </div>
</div>

<!-- Modal screenshoot -->
<div class="modal fade" id="ModalScreenshot" tabindex="-1" aria-labelledby="ModalScreenshotLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalScreenshotLabel">
                    <button id="screenshoot-btn" class="btn btn-primary">Save to Image</button>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center" id="ModalScreenshotBody">
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#VehicleCustomer').on('change', function() {
            getBatteryByVehicle();
        });
    });

    function getBatteryByVehicle() {
        var VehicleCustomer = $('#VehicleCustomer').val();

        if (VehicleCustomer.length == 0 || VehicleCustomer == null) {
            var html =
                '<div class="alert alert-danger alert-dismissible fade show" role="alert">No Vehicle Selected</div>';
            $('#ResultRecommendationBatteryVehicle').html(html);
            return;
        }

        var html = '<div class="spinner-border text-primary text-center" role="status">';
        html += '<span class="visually-hidden">Loading...</span>';
        html += '</div>';
        $('#ResultRecommendationBatteryVehicle').html(html);

        $.ajax({
            url: "/quotation/vehicle/find",
            type: "GET",
            data: {
                id: VehicleCustomer
            },
            success: function(data) {
                var html = '';
                if (data.length === 0) {
                    html =
                        '<div class="alert alert-danger alert-dismissible fade show" role="alert">No Battery Found</div>';
                    $('#ResultRecommendationBatteryVehicle').html(html);
                    return;
                } else {
                    html = '<div class="row">'; // Memulai row baru untuk Bootstrap grid
                    data.forEach(function(vehicle, index) {
                        html +=
                            '<div class="col-md-2_4 col-sm-6 mb-4 d-flex" style="flex: 0 0 calc(20% - 1rem); margin: 0.5rem; position: relative;">'; // Menambahkan position relative untuk badge
                        html += '<div class="blog grid-blog flex-fill">';
                        html += '<div class="blog-imagex">';
                        html += '<a href="#!">';
                        if (vehicle.image == null) {
                            vehicle.image = 'https://via.placeholder.com/210x210';
                            html += '<img class="img-fluid" src="' + vehicle.image +
                                '" alt="Post Image">';
                        } else {
                            var baseUrl = "{{ asset('storage/image/battery/') }}";
                            vehicle.image = vehicle.image;
                            html += '<img class="img-fluid" src="' + baseUrl + '/' + vehicle.image +
                                '" alt="Post Image" onerror="this.onerror=null; this.src=\'https://via.placeholder.com/210x210\';">';
                        }
                        html += '</a>';
                        html += '</div>';
                        html += '<div class="blog-content">';
                        html += '<h3 class="blog-title mt-3 "><a href="#!">' + vehicle.name +
                            '</a></h3>';
                        html += '<p>Detail & Spesifikasi :</p>';
                        html += '<ul class="list-group list-group-flush">';
                        html +=
                            '<li class="list-group-item"><div class="row"><div class="col-xl-6">Dimensi  </div><div class="col-xl-1"> :</div><div class="col">' +
                            vehicle
                            .dimension_height + ' x ' + vehicle.dimension_width + ' x ' + vehicle
                            .dimension_length + ' mm</div></div></li>';
                        html +=
                            '<li class="list-group-item"><div class="row"><div class="col-xl-6">Kapasitas </div><div class="col-xl-1"> : </div><div class="col">' +
                            vehicle.capacity +
                            ' AH</div></div></li></li>';
                        html +=
                            '<li class="list-group-item"><div class="row"><div class="col-xl-6">CCA </div><div class="col-xl-1"> : </div><div class="col">' +
                            vehicle
                            .standard_cca + '</div></div></li>'
                        html +=
                            '<li class="list-group-item"><div class="row"><div class="col-xl-6">Garansi </div><div class="col-xl-1"> : </div><div class="col">' +
                            vehicle.warranty +
                            ' Bulan</div></div></li>';
                        if (vehicle.discount == 0) {
                            html +=
                                '<li class="list-group-item"><div class="row"><div class="col-xl-6">Harga</div><div class="col-xl-1"> : </div><div class="col">Rp. ' +
                                Number((vehicle.price_retail))
                                .toLocaleString('id-ID') +
                                '</div></div></li>';
                            html +=
                                '<li class="list-group-item"><div class="row"><div class="col-xl-6">Harga + PPN </div><div class="col-xl-1"> : </div><div class="col">Rp. ' +
                                Number((vehicle.price_retail * (1 + vehicle.tax / 100)))
                                .toLocaleString('id-ID') +
                                '</div></div></li>';
                            html += '<li class="list-group-item"></li>';
                        } else {
                            var price_with_tax = vehicle.price_retail_original + (vehicle
                                .price_retail_original * (vehicle.tax / 100));
                            html +=
                                '<li class="list-group-item"><div class="row"><div class="col-xl-6">Harga </div><div class="col-xl-1"> : </div><div class="col"><span class="price-original position-relative">Rp. ' +
                                Number(price_with_tax).toLocaleString('id-ID') +
                                '<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="margin-left: 15px;">Disc ' +
                                Number(vehicle.discount) + ' %</span> </span></div></div></li>';
                            var price_with_tax = vehicle.price_retail_original + (vehicle
                                .price_retail_original * (vehicle.tax / 100));
                            var price_discount = vehicle.price_retail_original - (vehicle
                                .price_retail_original * (vehicle.discount / 100));
                            var price_tax = price_discount + (price_discount * (vehicle.tax / 100));
                            html +=
                                '<li class="list-group-item"><div class="row"><div class="col-xl-6">Harga + PPN</div><div class="col-xl-1"> : </div><div class="col"><span class="price-discount">Rp. ' +
                                Number((price_tax))
                                .toLocaleString(
                                    'id-ID') +
                                '</span></div></div></li>';
                            html += '<li class="list-group-item"></li>';
                        }
                        html += '</ul>';
                        html += '</div>';
                        html += '<div class="row">';
                        html += '<div class="edit-options">';
                        html += '<div class="text-end inactive-style mt-3">';
                        html += '<div class="checkbox">';
                        html += '<label>';
                        html += '<input type="checkbox" name="CheckBattery1[]" value=' + vehicle
                            .id + '> Select Battery';
                        html += '</label>';
                        html += '</div>';
                        html += '</div>';
                        html += '</div>';
                        html += '</div>';
                        html += '</div>';
                        html += '</div>';

                        // Tutup row setelah 5 elemen
                        if ((index + 1) % 5 == 0) {
                            html += '</div><div class="row">';
                        }
                    });
                    html += '</div>'; // Menutup row terakhir
                    $('#ResultRecommendationBatteryVehicle').html(html);
                }
            }
        });
    }

    $("#btnCopyAddress").on('click', function() {
        var FullName = $("#FullName").val();
        var Battery = $("input[name='CheckBattery1[]']:checked").map(function() {
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

    // check 
    $('.seller-next-btn-check').on('click', function() {
        var VehicleCustomer = $('#VehicleCustomer').val();
        var AddressCustomer = $("#AddressCustomer").val();
        var Battery = $("input[name='CheckBattery1[]']:checked").map(function() {
            return $(this).val();
        }).get();


        if (VehicleCustomer == '') {
            swal.fire("Error!", "Vehicle Customer is required", "error");
            return;
        }

        if (Battery.length == 0) {
            swal.fire("Error!", "Please select battery", "error");
            return;
        }


        $('#btnNextStep2').trigger('click');

        // check jika button next step 2 berhasil di click
        // if ($('#ProductDisplay').hasClass('active')) {
        $.ajax({
            url: "/quotation/battery/find",
            type: "GET",
            data: {
                id: VehicleCustomer,
                Battery: Battery,
            },
            success: function(data) {
                var html = '';
                // jika data kosong
                if (data.length === 0) {
                    html =
                        '<div class="alert alert-danger alert-dismissible fade show" role="alert">No Battery Found</div>';
                    $('#ResultRecommendationBattery').html(html);
                    return;
                } else {
                    data.forEach(function(vehicle) {
                        html +=
                            '<div class="col-md-6 col-xl-4 col-sm-12 d-flex">';
                        html += '<div class="blog grid-blog flex-fill">';
                        html += '<div class="blog-imagex">';
                        html += '<a href="#!">';
                        if (vehicle.image == null) {

                            vehicle.image =
                                'https://via.placeholder.com/210x210';
                            html += '<img class="img-fluid" src="' + vehicle
                                .image + '" alt="Post Image">';
                        } else {
                            var baseUrl =
                                "{{ asset('storage/image/battery/') }}";
                            vehicle.image = vehicle.image;
                            html += '<img class="img-fluid" src="' +
                                baseUrl +
                                '/' + vehicle.image +
                                '" alt="Post Image" onerror="this.onerror=null; this.src=\'https://via.placeholder.com/210x210\';">';
                        }
                        html += '</a>';
                        html += '</div>';
                        html += '<div class="blog-content">';
                        html +=
                            '<h3 class="blog-title mt-3"><a href="#!">' +
                            vehicle.name + '</a></h3>';
                        html += '<p>Detail & Spesifikasi :</p>';
                        html += '<ul class="list-group list-group-flush">';
                        html +=
                            '<li class="list-group-item"><div class="row"><div class="col-xl-6">Dimensi  </div><div class="col-xl-1"> :</div><div class="col">' +
                            vehicle
                            .dimension_height + ' x ' + vehicle.dimension_width + ' x ' +
                            vehicle
                            .dimension_length + ' mm</div></div></li>';
                        html +=
                            '<li class="list-group-item"><div class="row"><div class="col-xl-6">Kapasitas </div><div class="col-xl-1"> : </div><div class="col">' +
                            vehicle.capacity +
                            ' AH</div></div></li></li>';
                        html +=
                            '<li class="list-group-item"><div class="row"><div class="col-xl-6">CCA </div><div class="col-xl-1"> : </div><div class="col">' +
                            vehicle
                            .standard_cca + '</div></div></li>'
                        html +=
                            '<li class="list-group-item"><div class="row"><div class="col-xl-6">Garansi </div><div class="col-xl-1"> : </div><div class="col">' +
                            vehicle.warranty +
                            ' Bulan</div></div></li>';
                        if (vehicle.discount == 0) {
                            html +=
                                '<li class="list-group-item"><div class="row"><div class="col-xl-6">Harga</div><div class="col-xl-1"> : </div><div class="col">Rp. ' +
                                Number((vehicle.price_retail))
                                .toLocaleString('id-ID') +
                                '</div></div></li>';
                            html +=
                                '<li class="list-group-item"><div class="row"><div class="col-xl-6">Harga + PPN </div><div class="col-xl-1"> : </div><div class="col">Rp. ' +
                                Number((vehicle.price_retail * (1 + vehicle.tax / 100)))
                                .toLocaleString('id-ID') +
                                '</div></div></li>';
                            html += '<li class="list-group-item"></li>';
                        } else {
                            var price_with_tax = vehicle.price_retail_original + (vehicle
                                .price_retail_original * (vehicle.tax / 100));
                            html +=
                                '<li class="list-group-item"><div class="row"><div class="col-xl-6">Harga </div><div class="col-xl-1"> : </div><div class="col"><span class="price-original position-relative">Rp. ' +
                                Number(price_with_tax).toLocaleString(
                                    'id-ID') +
                                '<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="margin-left: 15px;">Disc ' +
                                Number(vehicle.discount) +
                                ' %</span> </span></div></div></li>';
                            var price_discount = vehicle.price_retail_original - (vehicle
                                .price_retail_original * (vehicle.discount / 100));
                            var price_tax = price_discount + (price_discount * (vehicle
                                .tax / 100));
                            html +=
                                '<li class="list-group-item"><div class="row"><div class="col-xl-6">Harga + PPN</div><div class="col-xl-1"> : </div><div class="col"><span class="price-discount">Rp. ' +
                                Number((price_tax))
                                .toLocaleString(
                                    'id-ID') +
                                '</span></div></div></li>';
                            html += '<li class="list-group-item"></li>';
                        }
                        html += '</ul>';
                        html +=
                            '</div>';
                        html += '<div class="row">';
                        html +=
                            '<div class="edit-options">';
                        html +=
                            '<div class="text-end inactive-style mt-3">';
                        html +=
                            '<div class="checkbox">';
                        html += '<label>';
                        html +=
                            '<input type="checkbox" name="CheckBattery[]" value=' +
                            vehicle.id + ' checked> Select Battery';
                        html +=
                            '</label>';
                        html += '</div>';
                        html +=
                            '<button data-id="' + vehicle.id +
                            '" id="btnCopyLink" class="btn clip-btn btn-primary btn-xs" onclick="CopyLinkBattery(this)"><i class="far fa-copy"></i> Links</button>';
                        html += '</div>';
                        html +=
                            '</div>';
                        html += '</div>';
                        html += '</div>';
                        html +=
                            '</div>';
                    });
                    $('#ResultRecommendationBattery').html(html);
                }
            }
        });

        // ajax get data distributor & insert data to combo box #distibutorid
        $.ajax({
            url: "/quotation/distributor/find",
            type: "GET",
            data: {
                id: VehicleCustomer,
                Battery: Battery,
            },
            success: function(data) {
                var html = '<option value="">Select Distributor</option>';
                data.forEach(function(distributor) {
                    html += '<option value="' + distributor.id + '">' +
                        distributor.name + '</option>';
                });
                $('#DistributorShopId').html(html);
            }
        });



        // function getMapsNearAddressCustomer() {
        //     var address = $('#AddressCustomer').val();
        //     var latitude = $('#Latitude').val();
        //     var longitude = $('#Longitude').val();
        //     var idCustomer = $('#IdCustomer').val();
        //     var data = {
        //         address: address,
        //         latitude: latitude,
        //         longitude: longitude,
        //     };

        //     $.ajax({
        //         url: "/quotation/customer/maps/near",
        //         type: "GET",
        //         data: data,
        //         success: function(data) {
        //             $("#MapsDistributorRecomendation").html(data);
        //         }
        //     });
        // }
    });

    $('#FullNameStep1').on('keyup', function() {
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
                        displaySuggestionsStep1(data);
                    } else {
                        $('#AutoCompleteFullNameCustomerStep1').html('');
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
            $('#AutoCompleteFullNameCustomerStep1').html('');
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


    function displaySuggestionsStep1(suggestions) {
        $('#AutoCompleteFullNameCustomerStep1').empty();

        suggestions.forEach(function(item) {
            $('#AutoCompleteFullNameCustomerStep1').append('<div class="suggestion">' + item.name +
                '</div>');
        });

        $('.suggestion').click(function() {
            var index = $(this).index();

            $('#FullName').val(suggestions[index].name);
            $('#FullNameStep1').val(suggestions[index].name);
            var cleanNumber = suggestions[index].contact.replace(/\D/g, '');
            $('#ContactNumber').val(cleanNumber);
            $('#EmailCustomer').val(suggestions[index].email);
            $('#AddressCustomer').val(suggestions[index].address);
            $('#IdCustomer').val(suggestions[index].id);
            $("#Latitude").val(suggestions[index].latitude);
            $("#Longitude").val(suggestions[index].longitude);
            $('#AutoCompleteFullNameCustomerStep1').empty();
            // call google maps
            // initMap();

            var IdCustomer = $("#IdCustomer").val();
            if (IdCustomer != '') {
                $('#UserExist').show();
                $('#UserNotExist').hide();

                // get vehcile by id
                $.ajax({
                    url: "/quotation/customer/vehicle/find",
                    type: "GET",
                    data: {
                        id: IdCustomer,
                    },
                    success: function(data) {
                        var vehicles = data;
                        $('#VehicleCustomer').val(vehicles);
                        $('#VehicleCustomer').trigger('change');
                    }
                });
            } else {
                $('#UserExist').hide();
                $('#UserNotExist').show();
            }
        });
    }


    // screenshot
    $('#screenshot').on('click', function() {
        let $btn = $(this);
        $btn.prop("disabled", true);
        $btn.html(
            '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...'
        );

        var Battery = $("input[name='CheckBattery1[]']:checked").map(function() {
            return $(this).val();
        }).get();

        if (Battery.length == 0) {
            swal.fire("Error!", "Please select battery", "error").then(() => {
                $btn.prop("disabled", false);
                $btn.html('<i class="fas fa-camera"></i> Screenshot');
            });
            return false;
        }

        // ajax to screenshoot
        $.ajax({
            url: "/quotation/battery/screenshot",
            type: "POST",
            data: {
                Battery: Battery,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(data) {
                $btn.prop("disabled", false);
                $btn.html('<i class="fas fa-camera"></i> Screenshot');
                $('#ModalScreenshotBody').html(data);
                $('#ModalScreenshot').modal('show');
            }
        });
    });
</script>
