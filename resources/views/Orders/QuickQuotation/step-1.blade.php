<style>
    .group-box-panel-title {
        background-color: #fff;
        padding: 10px;
    }

    .group-box-panel {
        border: 1px solid #e0e0e0;
        border-radius: 5px;
        padding: 10px;
        margin-bottom: 10px;
        margin-top: 10px;
    }
</style>
<div>
    <div class="mb-4">
        <h5>Enter Your Vehicle Details</h5>
    </div>
    <form id='FormPersonalDetails'>
        <div class="row g-3">
            <!-- Member's Name -->
            <div class="col-lg-6 col-md-6 col-sm-12">
                <div class="form-group local-forms">
                    <label for="FullNameStep1">Members Name</label>
                    <input type="text" class="form-control" id="FullNameStep1" name="FullNameStep1" placeholder="Enter Full Name" value="" required autocomplete="off">
                    <div id="AutoCompleteFullNameCustomerStep1"></div>
                    <span class="badge bg-success mt-2" id="UserExistStep1" style="display: none;">User Exist</span>
                    <span class="badge bg-warning mt-2" id="UserNotExistStep1" style="display: none;">New User</span>
                </div>
            </div>

            <!-- Vehicle + Checkboxes in one row -->
            <div class="col-lg-12">
                <div class="row g-3 align-items-center">
                    <!-- Vehicle Customer Select -->
                    <div class="col-lg-6 col-md-6 col-sm-12">
                        <div class="form-group local-forms">
                            <label for="VehicleCustomer">Vehicle Customer <span class="login-danger">*</span></label>
                            <select name="VehicleCustomer[]" multiple="multiple" id="VehicleCustomer" class="form-select" aria-label="Select vehicles">
                                @foreach ($data['Vehicle'] as $vehicle)
                                <option value="{{ $vehicle['id'] }}">
                                    {{ $vehicle['name'] }}{{ $vehicle['note'] ? ' - ' . $vehicle['note'] : '' }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Custom Vehicle Checkbox -->
                    <div class="col-lg-3 col-md-3 col-sm-6">
                        <div class="form-check" style="margin-bottom: 2.125rem;">
                            <input type="checkbox" class="form-check-input" id="custom-vehicle" name="custom-vehicle">
                            <label class="form-check-label" for="custom-vehicle">Custom Vehicle</label>
                        </div>
                    </div>

                    <!-- Ignore Stock Checkbox -->
                    <div class="col-lg-3 col-md-3 col-sm-6">
                        <div class="form-check" style="margin-bottom: 2.125rem;">
                            <input type="checkbox" class="form-check-input" id="ignore-stock" name="ignore-stock">
                            <label class="form-check-label" for="ignore-stock">Ignore Stock</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="group-box-panel d-none" id="group-box">
                    <div class="row d-none mt-3" id="custom-vehicle-form">
                        <div class="col">
                            <div class="form-group local-forms">
                                <label>Battery Category</label>
                                <select name="BatteryCategory" id="BatteryCategory" class="form-select" aria-label="Default select example" style="width: 100%;">
                                    <option value="">Select Battery Category</option>
                                </select>
                                <script>
                                    $(document).ready(function() {
                                        $('#BatteryCategory').select2({
                                            width: 'resolve',
                                            ajax: {
                                                url: '/quotation/battery/autoCompleteCategory',
                                                dataType: 'json',
                                                delay: 250,
                                                data: function(params) {
                                                    return {
                                                        q: params.term // search term
                                                    };
                                                },
                                                processResults: function(data) {
                                                    return {
                                                        results: $.map(data, function(item) {
                                                            return {
                                                                text: item.name,
                                                                id: item.id
                                                            }
                                                        })
                                                    };
                                                },
                                                cache: true
                                            },
                                            minimumInputLength: 1,
                                            placeholder: 'Select Battery Category',
                                            allowClear: true
                                        });
                                    });
                                </script>
                            </div>
                        </div>

                        {{-- battery cca --}}
                        <div class="col">
                            <div class="form-group local-forms">
                                <label>Battery CCA</label>
                                <select name="BatteryCCA" id="BatteryCCA" class="form-select" aria-label="Default select example">
                                    <option value="all">Select All</option>
                                </select>
                                <script>
                                    $(document).ready(function() {
                                        $('#BatteryCCA').select2({
                                            width: 'resolve',
                                            ajax: {
                                                url: '/quotation/battery/autoCompleteCCA',
                                                dataType: 'json',
                                                delay: 250,
                                                data: function(params) {
                                                    return {
                                                        q: params.term // search term
                                                    };
                                                },
                                                processResults: function(data) {
                                                    return {
                                                        results: $.map(data, function(item) {
                                                            return {
                                                                text: item.standard_cca,
                                                                id: item.standard_cca
                                                            }
                                                        })
                                                    };
                                                },
                                                cache: true
                                            },
                                            minimumInputLength: 1,
                                            placeholder: 'Select Battery CCA',
                                            allowClear: true
                                        });
                                    });
                                </script>
                            </div>
                        </div>

                        {{-- battery capacity --}}
                        <div class="col">
                            <div class="form-group local-forms">
                                <label>Battery Capacity</label>
                                <select name="BatteryCapacity" id="BatteryCapacity" class="form-select" aria-label="Default select example">
                                    <option value="all">Select All</option>
                                </select>
                                <script>
                                    $(document).ready(function() {
                                        $('#BatteryCapacity').select2({
                                            width: 'resolve',
                                            ajax: {
                                                url: '/quotation/battery/autoCompleteCapacity',
                                                dataType: 'json',
                                                delay: 250,
                                                data: function(params) {
                                                    return {
                                                        q: params.term // search term
                                                    };
                                                },
                                                processResults: function(data) {
                                                    return {
                                                        results: $.map(data, function(item) {
                                                            return {
                                                                text: item.capacity,
                                                                id: item.capacity
                                                            }
                                                        })
                                                    };
                                                },
                                                cache: true
                                            },
                                            minimumInputLength: 1,
                                            placeholder: 'Select Battery Capacity',
                                            allowClear: true
                                        });
                                    });
                                </script>
                            </div>
                        </div>

                        {{-- battery dimension --}}
                        <div class="col">
                            <div class="form-group local-forms">
                                <label>Battery Dimension</label>
                                <select name="BatteryDimension" id="BatteryDimension" class="form-select" aria-label="Default select example">
                                    <option value="all">Select All</option>
                                </select>
                                <script>
                                    $(document).ready(function() {
                                        $('#BatteryDimension').select2({
                                            width: 'resolve',
                                            ajax: {
                                                url: '/quotation/battery/autoCompleteDimension',
                                                dataType: 'json',
                                                delay: 250,
                                                data: function(params) {
                                                    return {
                                                        q: params.term // search term
                                                    };
                                                },
                                                processResults: function(data) {
                                                    return {
                                                        results: $.map(data, function(item) {
                                                            return {
                                                                text: item.dimension_length + ' x ' +
                                                                    item.dimension_width + ' x ' +
                                                                    item.dimension_height + ' mm',
                                                                id: item.dimension_length + ',' +
                                                                    item.dimension_width + ',' +
                                                                    item.dimension_height
                                                            }
                                                        })
                                                    };
                                                },
                                                cache: true
                                            },
                                            minimumInputLength: 1,
                                            placeholder: 'Select Battery Dimension',
                                            allowClear: true
                                        });
                                    });
                                </script>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="group-box-panel d-none" id="group-box-2">
                    <div class="row d-none mt-3" id="custom-vehicle-form-2">
                        {{-- battery name --}}
                        <div class="col">
                            <div class="form-group local-forms">
                                <label>Battery Name</label>
                                <select name="BatteryName" id="BatteryName" class="form-select" aria-label="Default select example">
                                    <option value="all">Select All</option>
                                </select>
                                <script>
                                    $(document).ready(function() {
                                        $('#BatteryName').select2({
                                            width: 'resolve',
                                            ajax: {
                                                url: '/quotation/battery/autoCompleteName',
                                                dataType: 'json',
                                                delay: 250,
                                                data: function(params) {
                                                    return {
                                                        q: params.term,
                                                        category: $('#BatteryCategory').val(),
                                                        cca: $('#BatteryCCA').val(),
                                                        capacity: $('#BatteryCapacity').val(),
                                                        dimension: $('#BatteryDimension').val()
                                                    };
                                                },
                                                processResults: function(data) {
                                                    return {
                                                        results: $.map(data, function(item) {
                                                            return {
                                                                text: item.name,
                                                                id: item.id
                                                            }
                                                        })
                                                    };
                                                },
                                                cache: true
                                            },
                                            minimumInputLength: 1,
                                            placeholder: 'Select Battery Name',
                                            allowClear: true
                                        });
                                    });
                                </script>
                            </div>
                        </div>
                        {{-- button add battery --}}
                        <div class="col">
                            <button type="button" class="btn btn-primary" id="btnAddBattery">Show Battery</button>
                        </div>

                        <input type="hidden" name="IdBatteryArray" id="IdBatteryArray">
                    </div>
                </div>
            </div>
        </div>
    </form>

    <br>
    <h5> Product Recomendation Display</h5>
    <div class="row" id="ResultRecommendationBatteryVehicle"></div>
    <div class="row" id="ResultRecommendationStockBatteryVehicle"></div>
    <div class="row">
        <div class="col text-end">
            <button id="btnSelectAllBattery" class="btn btn-primary" onclick="selectAll()"><i class="fas fa-check"></i>
                Select All</button>
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
                    <button id="screenshoot-btn" class="btn btn-primary" style="display: none;">Convert to
                        Image</button>
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

        $('#custom-vehicle').on('change', function() {
            if ($(this).is(':checked')) {
                $('#custom-vehicle-form').removeClass('d-none');
                $('#custom-vehicle-form-2').removeClass('d-none');
                $("#group-box").removeClass('d-none');
                $("#group-box-2").removeClass('d-none');
            } else {
                $('#custom-vehicle-form').addClass('d-none');
                $('#custom-vehicle-form-2').addClass('d-none');
                $("#group-box").addClass('d-none');
                $("#group-box-2").addClass('d-none');
            }
        });

        let suppressChange = false;

        function resetOtherFields(except) {
            if (suppressChange) return;

            suppressChange = true;

            const fields = ['#BatteryCategory', '#BatteryCCA', '#BatteryCapacity', '#BatteryDimension'];

            fields.forEach(function(field) {
                if (field !== except) {
                    $(field).val('ALL').trigger('change.select2');
                }
            });

            $('#IdBatteryArray').val('');

            suppressChange = false;
        }

        $('#BatteryCategory').on('change', function() {
            resetOtherFields('#BatteryCategory');
            getBatteryByVehicle();
        });

        $('#BatteryCCA').on('change', function() {
            resetOtherFields('#BatteryCCA');
            getBatteryByVehicle();
        });

        $('#BatteryCapacity').on('change', function() {
            resetOtherFields('#BatteryCapacity');
            getBatteryByVehicle();
        });

        $('#BatteryDimension').on('change', function() {
            resetOtherFields('#BatteryDimension');
            getBatteryByVehicle();
        });

        // $('#BatteryCategory').on('change', function() {
        //     var BatteryCategory = $(this).val();
        //     if (BatteryCategory.length == 0) {
        //         return;
        //     }

        //     $.ajax({
        //         url: "/quotation/battery/filter/category",
        //         type: "GET",
        //         data: {
        //             category: BatteryCategory
        //         },
        //         success: function(data) {
        //             if (data.status == 'success') {
        //                 var html = '<option value="all">Select All</option>';
        //                 data.data.forEach(function(item) {
        //                     html += '<option value="' + item.standard_cca + '">' +
        //                         item.standard_cca + '</option>';
        //                 });
        //                 $('#BatteryCCA').html(html);

        //                 $('#BatteryCCA').trigger('change');
        //             } else {
        //                 swal.fire("Error!", data.message, "error");
        //             }
        //         }
        //     });
        // });

        // $('#BatteryCCA').on('change', function() {
        //     var BatteryCategory = $('#BatteryCategory').val();
        //     var BatteryCCA = $(this).val();
        //     if (BatteryCCA.length == 0) {
        //         return;
        //     }

        //     $.ajax({
        //         url: "/quotation/battery/filter/cca",
        //         type: "GET",
        //         data: {
        //             category: BatteryCategory,
        //             cca: BatteryCCA
        //         },
        //         success: function(data) {
        //             if (data.status == 'success') {
        //                 var html = '<option value="all">Select All</option>';
        //                 data.data.forEach(function(item) {
        //                     html += '<option value="' + item.capacity + '">' +
        //                         item.capacity + '</option>';
        //                 });
        //                 $('#BatteryCapacity').html(html);

        //                 $('#BatteryCapacity').trigger('change');
        //             } else {
        //                 swal.fire("Error!", data.message, "error");
        //             }
        //         }
        //     });
        // });

        // $('#BatteryCapacity').on('change', function() {
        //     var BatteryCategory = $('#BatteryCategory').val();
        //     var BatteryCCA = $('#BatteryCCA').val();
        //     var BatteryCapacity = $(this).val();
        //     if (BatteryCapacity.length == 0) {
        //         return;
        //     }

        //     $.ajax({
        //         url: "/quotation/battery/filter/capacity",
        //         type: "GET",
        //         data: {
        //             category: BatteryCategory,
        //             cca: BatteryCCA,
        //             capacity: BatteryCapacity
        //         },
        //         success: function(data) {
        //             if (data.status == 'success') {
        //                 var html = '<option value="all">Select All</option>';
        //                 data.data.forEach(function(item) {
        //                     html += '<option value="' + item.dimension_length +
        //                         ',' + item.dimension_width +
        //                         ',' + item.dimension_height + '">' + item
        //                         .dimension_length + ' x ' +
        //                         item.dimension_width + ' x ' + item
        //                         .dimension_height + ' mm</option>';
        //                 });
        //                 $('#BatteryDimension').html(html);

        //                 $('#BatteryDimension').trigger('change');
        //             } else {
        //                 swal.fire("Error!", data.message, "error");
        //             }
        //         }
        //     });
        // });

        // $('#BatteryDimension').on('change', function() {
        //     var BatteryCategory = $('#BatteryCategory').val();
        //     var BatteryCCA = $('#BatteryCCA').val();
        //     var BatteryCapacity = $('#BatteryCapacity').val();
        //     var BatteryDimension = $(this).val();
        //     if (BatteryDimension.length == 0) {
        //         return;
        //     }

        //     $.ajax({
        //         url: "/quotation/battery/filter/dimension",
        //         type: "GET",
        //         data: {
        //             category: BatteryCategory,
        //             cca: BatteryCCA,
        //             capacity: BatteryCapacity,
        //             dimension: BatteryDimension
        //         },
        //         success: function(data) {
        //             if (data.status == 'success') {
        //                 var html = '<option value="all">Select All</option>';
        //                 data.data.forEach(function(item) {
        //                     html += '<option value="' + item.id + '">' + item
        //                         .name + '</option>';
        //                 });
        //                 $('#BatteryName').html(html);
        //             } else {
        //                 swal.fire("Error!", data.message, "error");
        //             }
        //         }
        //     });
        // });

        $('#btnAddBattery').on('click', function() {
            var BatteryName = $('#BatteryName').val();
            var BatteryCategory = $('#BatteryCategory').val();
            var IdBatteryArray = $('#IdBatteryArray').val();

            if (BatteryName.length == 0) {
                swal.fire("Error!", "Please select battery name", "error");
                return;
            }

            if (IdBatteryArray.length == 0) {
                if (BatteryName == 'all') {
                    $('#IdBatteryArray').val('');
                    $('#BatteryName option').each(function() {
                        if ($(this).val() !== 'all') {
                            if (IdBatteryArray.length == 0) {
                                IdBatteryArray = $(this).val();
                            } else {
                                IdBatteryArray += ',' + $(this).val();
                            }
                        }
                    });
                    $('#IdBatteryArray').val(IdBatteryArray);
                } else {
                    $('#IdBatteryArray').val(BatteryName);
                }
            } else {
                if (BatteryName == 'all') {
                    $('#IdBatteryArray').val('');
                    $('#BatteryName option').each(function() {
                        if ($(this).val() !== 'all') {
                            if (IdBatteryArray.length == 0) {
                                IdBatteryArray = $(this).val();
                            } else {
                                IdBatteryArray += ',' + $(this).val();
                            }
                        }
                    });
                    $('#IdBatteryArray').val(IdBatteryArray);
                } else {
                    if (!IdBatteryArray.split(',').includes(BatteryName)) {
                        $('#IdBatteryArray').val(IdBatteryArray + ',' + BatteryName);
                    }
                }
            }

            getBatteryByVehicle();
        });
    });

    function getBatteryByVehicle() {
        var VehicleCustomer = $('#VehicleCustomer').val();
        var custom = $('#custom-vehicle').is(':checked');
        var ignoreStock = $('#ignore-stock').is(':checked');

        if (!custom) {
            if (VehicleCustomer.length == 0 || VehicleCustomer == null) {
                var html =
                    '<div class="alert alert-danger alert-dismissible fade show" role="alert">No Vehicle Selected</div>';
                $('#ResultRecommendationBatteryVehicle').html(html);
                return;
            }
        }

        var html = '<div class="spinner-border text-primary text-center" role="status">';
        html += '<span class="visually-hidden">Loading...</span>';
        html += '</div>';
        $('#ResultRecommendationBatteryVehicle').html(html);
        $("#ResultRecommendationStockBatteryVehicle").html("");

        // array data
        if (custom) {
            data = {
                id: VehicleCustomer,
                custom: $('#custom-vehicle').is(':checked'),
                category: $('#BatteryCategory').val() ? $('#BatteryCategory').val() : 'ALL',
                name: $('#IdBatteryArray').val() ? $('#IdBatteryArray').val() : 'ALL',
                cca: $('#BatteryCCA').val() ? $('#BatteryCCA').val() : 'ALL',
                capacity: $('#BatteryCapacity').val() ? $('#BatteryCapacity').val() : 'ALL',
                dimension: $('#BatteryDimension').val() ? $('#BatteryDimension').val() : 'ALL',
            }
        } else {
            data = {
                id: VehicleCustomer,
            }
        }

        $('#btnSelectAllBattery').prop('disabled', true);

        $.ajax({
            url: "/quotation/vehicle/find",
            type: "GET",
            data: data,
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
                            '<div class="col-md-2_4 col-sm-6 mb-4" id="' + vehicle.code +
                            '" style="flex: 0 0 calc(20% - 1rem); margin: 0.5rem; position: relative;">'; // Menambahkan position relative untuk badge
                        html += '<div class="blog grid-blog d-flex flex-column h-100">';
                        html += '<div class="blog-imagex">';
                        html += '<a href="#!">';
                        if (vehicle.image == null) {
                            vehicle.image = 'https://placehold.co/100x100';
                            html += '<img class="img-fluid" src="' + vehicle.image +
                                '" alt="Post Image">';
                        } else {
                            var baseUrl = "{{ asset('storage/image/battery/compressed/') }}";
                            vehicle.image = vehicle.image;
                            html += '<img class="img-fluid" src="' + baseUrl + '/' + vehicle.image +
                                '" alt="Post Image" onerror="this.onerror=null; this.src=\'https://placehold.co/100x100\';">';
                        }
                        html += '</a>';
                        html += '</div>';
                        html += '<div class="blog-content">';
                        // limit 25 karakter
                        var name = vehicle.name;
                        if (name.length > 25) {
                            name = name.substring(0, 25) + '...';
                        }

                        html += '<h3 class="blog-title mt-3 "><a href="#!">' + name + '</a></h3>';
                        html += '<p>Detail & Spesifikasi :</p>';
                        html += '<ul class="list-group list-group-flush">';
                        html +=
                            '<li class="list-group-item"><div><div>Dimensi :</div><div>' +
                            vehicle
                            .dimension_length + ' x ' + vehicle.dimension_width + ' x ' + vehicle
                            .dimension_height + ' mm</div></div></li>';
                        html +=
                            '<li class="list-group-item"><div ><div >Kapasitas :</div><div >' +
                            vehicle.capacity +
                            ' AH</div></div></li></li>';
                        html +=
                            '<li class="list-group-item"><div ><div >CCA :</div><div >' +
                            vehicle
                            .standard_cca + '</div></div></li>'
                        html +=
                            '<li class="list-group-item"><div ><div >Garansi :</div><div >' +
                            vehicle.warranty +
                            ' Bulan</div></div></li>';
                        if (vehicle.discount == 0) {
                            html +=
                                '<li class="list-group-item"><div ><div >Harga :</div><div >Rp. ' +
                                Number((vehicle.price_net))
                                .toLocaleString('id-ID') +
                                '</div></div></li>';
                            html +=
                                '<li class="list-group-item" style="font-size: 14px;"><div ><div >Harga + PPN :</div><div >Rp. ' +
                                Number((vehicle.price_net * (1 + vehicle.tax / 100)))
                                .toLocaleString('id-ID') +
                                '</div></div></li>';
                            html += '<li class="list-group-item"></li>';
                        } else {
                            var price_with_tax = vehicle.price_retail_original + (vehicle
                                .price_retail_original * (vehicle.tax / 100));
                            html +=
                                '<li class="list-group-item"><div ><div >Harga :</div><div ><span class="price-original position-relative">Rp. ' +
                                Number(price_with_tax).toLocaleString('id-ID') +
                                '<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="margin-left: 15px;">Disc Rp. ' +
                                Number(vehicle.discount_price).toLocaleString('id-ID') +
                                ' </span> </span></div></div></li>';
                            var price_with_tax = vehicle.price_retail_original + (vehicle
                                .price_retail_original * (vehicle.tax / 100));
                            var price_discount = vehicle.price_retail_original - (vehicle
                                .price_retail_original * (vehicle.discount / 100));
                            var price_tax = price_discount + (price_discount * (vehicle.tax / 100));
                            // var price_tax = vehicle.price_retail_original - vehicle.
                            html +=
                                '<li class="list-group-item" style="font-size: 14px;"><div ><div >Harga + PPN :</div><div ><span class="price-discount">Rp. ' +
                                Number((vehicle.price_net))
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
                        if (vehicle.code == null) {
                            html += '<input type="checkbox" name="CheckBattery1x[]" value=' +
                                vehicle
                                .id + ' id="checkBoxBattery' + vehicle
                                .id + '"> Select Battery';
                        } else {
                            html += '<input type="checkbox" name="CheckBattery1[]" value=' + vehicle
                                .id + ' id="checkBoxBattery' + vehicle
                                .id + '"> Select Battery ';
                        }
                        if (custom) {
                            html +=
                                '<button type="button" class="btn btn-danger btn-xs" onclick="removeBattery(' +
                                vehicle.id + ')"><i class="fas fa-trash"></i></button>';
                        }

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

                        var col =
                            '<div class="col alert alert-primary text-center mx-2" id="stock' +
                            vehicle.id +
                            '"></div>';
                        $("#ResultRecommendationStockBatteryVehicle").append(col);

                        if (ignoreStock) {
                            $("#checkBoxBattery" + vehicle.id).prop('disabled', false);

                            $("#stock" + vehicle.id).html("Stock : 0");
                            if (vehicle.code != null) {
                                $("#stock" + vehicle.id).append("<br>ID : " + vehicle.code);
                            } else {
                                $("#stock" + vehicle.id).append("<br>ID : -");
                            }
                        } else {
                            (function(vehicleId, vehicleCode) {
                                $.ajax({
                                    url: "/inventory/get/" + vehicleCode,
                                    type: "GET",
                                    success: function(data) {
                                        if (data == 0 || data == '-' || data == null) {
                                            $("#checkBoxBattery" + vehicleId).prop('disabled', true);
                                        } else {
                                            $("#checkBoxBattery" + vehicleId).prop('disabled', false);
                                        }

                                        $("#stock" + vehicleId).html("Stock : " + data);
                                        if (vehicleCode != null) {
                                            $("#stock" + vehicleId).append("<br>ID : " + vehicleCode);
                                        } else {
                                            $("#stock" + vehicleId).append("<br>ID : -");
                                        }
                                    }
                                });
                            })(vehicle.id, vehicle.code);
                        }
                    });
                    html += '</div>'; // Menutup row terakhir
                    $('#ResultRecommendationBatteryVehicle').html(html);
                    $('#btnSelectAllBattery').prop('disabled', false);
                }
            }
        });
    }

    function removeBattery(id) {
        var IdBatteryArray = $('#IdBatteryArray').val();
        var array = IdBatteryArray.split(',');
        var index = array.indexOf(id.toString());
        if (index > -1) {
            array.splice(index, 1);
        }
        $('#IdBatteryArray').val(array.join());
        getBatteryByVehicle();
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
                                'https://placehold.co/100x100';
                            html += '<img class="img-fluid" src="' + vehicle
                                .image + '" alt="Post Image">';
                        } else {
                            var baseUrl =
                                "{{ asset('storage/image/battery/compressed/') }}";
                            vehicle.image = vehicle.image;
                            html += '<img class="img-fluid" src="' +
                                baseUrl +
                                '/' + vehicle.image +
                                '" alt="Post Image" onerror="this.onerror=null; this.src=\'https://placehold.co/100x100\';">';
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
                            '<li class="list-group-item"><div class="row"><div >Dimensi :</div><div >' +
                            vehicle
                            .dimension_length + ' x ' + vehicle.dimension_width + ' x ' +
                            vehicle
                            .dimension_height + ' mm</div></div></li>';
                        html +=
                            '<li class="list-group-item"><div class="row"><div >Kapasitas :</div><div >' +
                            vehicle.capacity +
                            ' AH</div></div></li></li>';
                        html +=
                            '<li class="list-group-item"><div class="row"><div >CCA :</div><div >' +
                            vehicle
                            .standard_cca + '</div></div></li>'
                        html +=
                            '<li class="list-group-item"><div class="row"><div >Garansi :</div><div >' +
                            vehicle.warranty +
                            ' Bulan</div></div></li>';
                        if (vehicle.discount == 0) {
                            html +=
                                '<li class="list-group-item"><div class="row"><div >Harga :</div><div >Rp. ' +
                                Number((vehicle.price_net))
                                .toLocaleString('id-ID') +
                                '</div></div></li>';
                            html +=
                                '<li class="list-group-item" style="font-size: 14px;"><div class="row"><div >Harga + PPN :</div><div >Rp. ' +
                                Number((vehicle.price_net * (1 + vehicle.tax / 100)))
                                .toLocaleString('id-ID') +
                                '</div></div></li>';
                            html += '<li class="list-group-item"></li>';
                        } else {
                            var price_with_tax = vehicle.price_retail_original + (vehicle
                                .price_retail_original * (vehicle.tax / 100));
                            html +=
                                '<li class="list-group-item"><div class="row"><div >Harga :</div><div ><span class="price-original position-relative">Rp. ' +
                                Number(price_with_tax).toLocaleString(
                                    'id-ID') +
                                '<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="margin-left: 15px;">Disc Rp. ' +
                                Number(vehicle.discount_price).toLocaleString('id-ID') +
                                '</span> </span></div></div></li>';
                            var price_discount = vehicle.price_retail_original - (vehicle
                                .price_retail_original * (vehicle.discount / 100));
                            var price_tax = price_discount + (price_discount * (vehicle
                                .tax / 100));
                            html +=
                                '<li class="list-group-item" style="font-size: 14px;"><div class="row"><div >Harga + PPN :</div><div ><span class="price-discount">Rp. ' +
                                Number((vehicle.price_net))
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


        // init maps
        initMapDekstop();



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
            $('#alternative_address').val(suggestions[index].address);
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
                // check apakah didalam #body-screenshoot ada table atau tidak
                // jika ada maka trigger click event
                if ($('#body-screenshoot table').length > 0) {
                    $('#screenshoot-btn').trigger('click');
                }
            }
        });
    });


    function selectAll() {
        var enabledCheckboxes = $("input[name='CheckBattery1[]']:not(:disabled)");

        if (enabledCheckboxes.length == 0) {
            swal.fire("Error!", "No available battery to select", "error");
            return;
        }

        enabledCheckboxes.prop('checked', true);

        // change button text
        $('#btnSelectAllBattery').html('<i class="fas fa-times"></i> Unselect All');
        $('#btnSelectAllBattery').attr('onclick', 'unselectAll()');
    }

    function unselectAll() {
        var enabledCheckboxes = $("input[name='CheckBattery1[]']:not(:disabled)");

        if (enabledCheckboxes.length == 0) {
            swal.fire("Error!", "No available battery to unselect", "error");
            return;
        }

        enabledCheckboxes.prop('checked', false);

        // change button text
        $('#btnSelectAllBattery').html('<i class="fas fa-check"></i> Select All');
        $('#btnSelectAllBattery').attr('onclick', 'selectAll()');
    }

    $('#ignore-stock').on('change', function() {
        getBatteryByVehicle();
    });

    $(document).on('input keyup change', '#AddressCustomer', function() {
        var addressValue = $(this).val();
        $('#alternative_address').val(addressValue);
    });
</script>