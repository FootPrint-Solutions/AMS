<div>
    <div class="mb-4">
        <h5>Enter Your Vehicle Details</h5>
    </div>
    <form id='FormPersonalDetails'>
        <div class="row">
            <div class="col-lg-8">
                <div class="form-group local-forms">
                    <label for="company-name">Vehicle Customer <span class="login-danger">*</span></label>
                    <select name="VehicleCustomer[]" multiple='multiple' id='VehicleCustomer' class="form-select" aria-label="Default select example">
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
                id: VehicleCustomer,
            },
            success: function(data) {
                var html = '';
                // jika data kosong
                if (data.length === 0) {
                    html =
                        '<div class="alert alert-danger alert-dismissible fade show" role="alert">No Battery Found</div>';
                    $('#ResultRecommendationBatteryVehicle').html(html);
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
                        html += '<p>Details & Specification :</p>';
                        html += '<ul class="list-group list-group-flush">';
                        html += '<li class="list-group-item">Warranty : ' +
                            vehicle.warranty + ' Months</li>';

                        html += '<li class="list-group-item">Price : Rp. ' +
                            Number(vehicle.price_retail).toLocaleString(
                                'id-ID') + '</li>';
                        html += '<li class="list-group-item">Size : ' +
                            vehicle.size_category + '</li>';
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
                            '<input type="checkbox" name="CheckBattery1[]" value=' +
                            vehicle.id + '> Add to cart';
                        html +=
                            '</label>';
                        html += '</div>';
                        html += '</div>';
                        html +=
                            '</div>';
                        html += '</div>';
                        html += '</div>';
                        html +=
                            '</div>';
                    });
                    $('#ResultRecommendationBatteryVehicle').html(html);
                    // getMapsNearAddressCustomer();
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
                        html += '<p>Details & Specification :</p>';
                        html += '<ul class="list-group list-group-flush">';
                        html += '<li class="list-group-item">Warranty : ' +
                            vehicle.warranty + ' Months</li>';

                        html += '<li class="list-group-item">Price : Rp. ' +
                            Number(vehicle.price_retail).toLocaleString(
                                'id-ID') + '</li>';
                        html += '<li class="list-group-item">Size : ' +
                            vehicle.size_category + '</li>';
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
                            vehicle.id + '> Add to cart';
                        html +=
                            '</label>';
                        html += '</div>';
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
</script>