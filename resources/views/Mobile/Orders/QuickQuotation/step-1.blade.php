 {{-- Select2 CSS --}}
 <link rel="stylesheet" href="{{ asset('/plugins/select2/css/select2.min.css') }}">

 {{-- OWL CAROUSEL CSS --}}
 <link rel="stylesheet" href="{{ asset('/plugins/owl-carousel/assets/owl.carousel.min.css') }}">
 <link rel="stylesheet" href="{{ asset('/plugins/owl-carousel/assets/owl.theme.default.min.css') }}">

 <div class="mb-4">
     <h4>Enter Your Vehicle Details</h4>
 </div>
 <form>
     <div class="row">
         <div class="col-lg-6">
             <div class="mb-3 form-group local-forms">
                 <label class="form-label">Members Name
                 </label>
                 <input type="text" class="form-control" id="members_name_input_mobile"
                     name="members_name_input_mobile" required>
                 <div id="AutoCompleteFullNameCustomerStep1Mobile"></div>
                 <span class="badge bg-success" id="UserExistStep1Mobile" style='display:none;'>User
                     Exist</span>
                 <span class="badge bg-warning" id="UserNotExistStep1Mobile" style='display:none;'>New
                     User</span>
             </div>
         </div>
         <div class="col-lg-6 mt-3">
             <div class="mb-3 form-group local-forms">
                 <label class="form-label">Vehicle Customer
                     <span class="login-danger">*</span>
                 </label>
                 <select class="form-control" id="vehicle_customer_input_mobile" multiple='multiple'
                     name="vehicle_customer_input_mobile[]">
                     @foreach ($data['Vehicle'] as $vehicle)
                         <option value="{{ $vehicle['id'] }}">
                             {{ $vehicle['name'] }}{{ $vehicle['note'] ? ' - ' . $vehicle['note'] : '' }}
                         </option>
                     @endforeach
                 </select>
             </div>
         </div>
         <div class="col-lg-6 mt-3">
             <input type="checkbox" class="checbox-centang" id="select-custom-vehicle" /> <span class="text-grey">Select
                 Custom Vehicle</span>
             <input type="checkbox" class="checbox-centang" id="select-ignore-stock" /> <span class="text-grey">Ignore
                 Stock</span>
         </div>
     </div>

     <div id="group-box-mobile" style="display: none;">
         <div class="row mt-3">

             {{-- Battery Category --}}
             <div class="col">
                 <div class="form-group local-forms">
                     <label>Battery Category</label>
                     <select name="BatteryCategoryMobile" id="BatteryCategoryMobile" class="form-select"
                         aria-label="Default select example" style="width: 100%;">
                         <option value="ALL">Select Battery Category</option>
                     </select>
                     <script>
                         $(document).ready(function() {
                             $('#BatteryCategoryMobile').select2({
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
                     <select name="BatteryCCAMobile" id="BatteryCCAMobile" class="form-select"
                         aria-label="Default select example">
                         <option value="ALL">Select All</option>
                     </select>
                     <script>
                         $(document).ready(function() {
                             $('#BatteryCCAMobile').select2({
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

         </div>

         <div class="row mt-2">
             {{-- battery capacity --}}
             <div class="col">
                 <div class="form-group local-forms">
                     <label>Battery Capacity</label>
                     <select name="BatteryCapacityMobile" id="BatteryCapacityMobile" class="form-select"
                         aria-label="Default select example">
                         <option value="ALL">Select All</option>
                     </select>
                     <script>
                         $(document).ready(function() {
                             $('#BatteryCapacityMobile').select2({
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
                     <select name="BatteryDimensionMobile" id="BatteryDimensionMobile" class="form-select"
                         aria-label="Default select example">
                         <option value="ALL">Select All</option>
                     </select>
                     <script>
                         $(document).ready(function() {
                             $('#BatteryDimensionMobile').select2({
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

         <div class="row mt-2">
             {{-- battery name --}}
             <div class="col">
                 <div class="form-group local-forms">
                     <label>Battery Name</label>
                     <select name="BatteryNameMobile" id="BatteryNameMobile" class="form-select"
                         aria-label="Default select example">
                         <option value="ALL">Select All</option>
                     </select>
                     <script>
                         $(document).ready(function() {
                             $('#BatteryNameMobile').select2({
                                 width: 'resolve',
                                 ajax: {
                                     url: '/quotation/battery/autoCompleteName',
                                     dataType: 'json',
                                     delay: 250,
                                     data: function(params) {
                                         return {
                                             q: params.term,
                                             category: $('#BatteryCategoryMobile').val(),
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
                 <button type="button" class="btn btn-primary" id="btnAddBatteryMobile">Show Battery</button>
             </div>

             <input type="hidden" name="IdBatteryArrayMobile" id="IdBatteryArrayMobile">
         </div>
     </div>
 </form>

 <h4 class=" mt-3">Product Recomendation Display</h4>
 <div class="checkbox-all mb-3">
     <input type="checkbox" class="checbox-centang" id="select-all-product" /> <span class="text-grey">Select All</span>
 </div>

 {{-- sample owl carousel --}}
 <div id="ResultRecommendationBatteryVehicleMobile"></div>
 <div class="owl-carousel owl-theme loop" id="owl-carousel">
 </div>
 {{-- end sample owl carousel --}}

 <div class="bottom-buttons ">
     {{-- screenshoot button --}}
     <button class="btn btn-custom btn-whatsapp btn-screenshoot" id="btn-screenshoot-mobile">
         <i class="fa fa-camera fa-md"></i>
         Capture
     </button>
     {{-- copy button --}}
     <button class="btn btn-custom btn-whatsapp" id="btn-copy-text-step-1-mobile">
         <i class="fa fa-copy fa-md"></i>
         Copy
     </button>
     {{-- next button --}}
     <button id="personal-details-mobile-next-button-lower" class="btn btn-custom btn-next next">Next
         <i class="fa fa-chevron-right"></i>
     </button>
 </div>


 <div class="modal fade" id="ModalScreenshotMobile" tabindex="-1" aria-labelledby="ModalScreenshotLabel"
     aria-hidden="true">
     <div class="modal-dialog modal-dialog-centered modal-xl">
         <div class="modal-content">
             <div class="modal-header">
                 <h5 class="modal-title" id="ModalScreenshotLabelMobile">
                     <button id="screenshoot-btn" class="btn btn-primary" style="display: none;">Convert to
                         Image</button>
                 </h5>
                 <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
             </div>
             <div class="modal-body text-center" id="ModalScreenshotBodyMobile" style="overflow-y: auto;">
             </div>
         </div>
     </div>
 </div>

 {{-- Owl Carousel JS --}}
 <script src="{{ asset('/plugins/owl-carousel/owl.carousel.min.js') }}"></script>

 {{-- Select2 JS --}}
 <script src="{{ asset('/plugins/select2/js/select2.min.js') }}"></script>


 <script>
     $(document).ready(function() {
         $('#select-custom-vehicle').on('change', function() {
             if ($(this).is(':checked')) {
                 $('#group-box-mobile').show();
             } else {
                 $('#group-box-mobile').hide();
             }
         });
     });

     let suppressChangeMobile = false;

     function resetOtherFieldsMobile(except) {
         if (suppressChangeMobile) return;

         suppressChangeMobile = true;
         const fieldsMobile = [
             '#BatteryCategoryMobile',
             '#BatteryCCAMobile',
             '#BatteryCapacityMobile',
             '#BatteryDimensionMobile',
         ];

         fieldsMobile.forEach(function(field) {
             if (field !== except) {
                 $(field).val('ALL').trigger('change.select2');
             }
         });

         $('#IdBatteryArrayMobile').val('');

         suppressChangeMobile = false;
     }

     $('#BatteryCategoryMobile').on('change', function() {
         resetOtherFieldsMobile('#BatteryCategoryMobile');
         getBatteryByVehicleMobile();
     });

     $('#BatteryCCAMobile').on('change', function() {
         resetOtherFieldsMobile('#BatteryCCAMobile');
         getBatteryByVehicleMobile();
     });

     $('#BatteryCapacityMobile').on('change', function() {
         resetOtherFieldsMobile('#BatteryCapacityMobile');
         getBatteryByVehicleMobile();
     });

     $('#BatteryDimensionMobile').on('change', function() {
         resetOtherFieldsMobile('#BatteryDimensionMobile');
         getBatteryByVehicleMobile();
     });

     $('#btnAddBatteryMobile').on('click', function() {
         var BatteryName = $('#BatteryNameMobile').val();
         var BatteryCategory = $('#BatteryCategoryMobile').val();
         var IdBatteryArray = $('#IdBatteryArrayMobile').val();

         if (BatteryName.length == 0) {
             swal.fire("Error!", "Please select battery name", "error");
             return;
         }

         if (IdBatteryArray.length == 0) {
             if (BatteryName == 'all') {
                 $('#IdBatteryArrayMobile').val('');
                 $('#BatteryNameMobile option').each(function() {
                     if ($(this).val() !== 'all') {
                         if (IdBatteryArray.length == 0) {
                             IdBatteryArray = $(this).val();
                         } else {
                             IdBatteryArray += ',' + $(this).val();
                         }
                     }
                 });
                 $('#IdBatteryArrayMobile').val(IdBatteryArray);
             } else {
                 $('#IdBatteryArrayMobile').val(BatteryName);
             }
         } else {
             if (BatteryName == 'all') {
                 $('#IdBatteryArrayMobile').val('');
                 $('#BatteryNameMobile option').each(function() {
                     if ($(this).val() !== 'all') {
                         if (IdBatteryArray.length == 0) {
                             IdBatteryArray = $(this).val();
                         } else {
                             IdBatteryArray += ',' + $(this).val();
                         }
                     }
                 });
                 $('#IdBatteryArrayMobile').val(IdBatteryArray);
             } else {
                 if (!IdBatteryArray.split(',').includes(BatteryName)) {
                     $('#IdBatteryArrayMobile').val(IdBatteryArray + ',' + BatteryName);
                 }
             }
         }

         getBatteryByVehicleMobile();
     });

     $("#btn-copy-text-step-1-mobile").on('click', function() {
         var FullName = $("#members_name_input_mobile").val();
         var Battery = [];
         $('.btn-owl-carousel').each(function() {
             if ($(this).data('check') == 1) {
                 Battery.push($(this).data('id'));
             }
         });

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

     // auto complete member
     $('#members_name_input_mobile').on('keyup', function() {
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
                         displaySuggestionsStep1Mobile(data);
                     } else {
                         $('#AutoCompleteFullNameCustomerStep1Mobile').html('');
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

     function displaySuggestionsStep1Mobile(suggestions) {
         $('#AutoCompleteFullNameCustomerStep1Mobile').empty();

         suggestions.forEach(function(item) {
             $('#AutoCompleteFullNameCustomerStep1Mobile').append('<div class="suggestionmobile">' + item.name +
                 '</div>');
         });

         $('.suggestionmobile').click(function() {
             var index = $(this).index();

             $('#members_name_input_mobile').val(suggestions[index].name);
             $('#full_name_input_mobile').val(suggestions[index].name);
             var cleanNumber = suggestions[index].contact.replace(/\D/g, '');
             $('#contact_input_mobile').val(cleanNumber);
             $('#email_input_mobile').val(suggestions[index].email);
             $('#address_input_mobile').val(suggestions[index].address);
             $('#IdCustomer').val(suggestions[index].id);
             $("#latitude_input_mobile").val(suggestions[index].latitude);
             $("#longitude_input_mobile").val(suggestions[index].longitude);
             $('#AutoCompleteFullNameCustomerStep1Mobile').empty();
             // call google maps
             // initMap();

             var IdCustomer = $("#IdCustomer").val();
             if (IdCustomer != '') {
                 $('#UserExistStep1Mobile').show();
                 $('#UserNotExistStep1Mobile').hide();

                 // get vehcile by id
                 $.ajax({
                     url: "/quotation/customer/vehicle/find",
                     type: "GET",
                     data: {
                         id: IdCustomer,
                     },
                     success: function(data) {
                         var vehicles = data;
                         $('#vehicle_customer_input_mobile').val(vehicles);
                         $('#vehicle_customer_input_mobile').trigger('change');
                     }
                 });
             } else {
                 $('#UserExistStep1Mobile').hide();
                 $('#UserNotExistStep1Mobile').show();
             }
         });
     }


     // vehicle customer configuration

     $('#vehicle_customer_input_mobile').select2({
         maximumSelectionLength: 1
     });

     $(document).ready(function() {
         $('#vehicle_customer_input_mobile').on('change', function() {
             getBatteryByVehicleMobile();
         });
     });

     function getBatteryByVehicleMobile() {
         // owl-carousel destroy
         $(".loop").owlCarousel('destroy');
         $(".loop").html('');
         var VehicleCustomer = $('#vehicle_customer_input_mobile').val();
         var custom = $('#select-custom-vehicle').is(':checked');
         var ignoreStock = $('#select-ignore-stock').is(':checked');

         if (!custom) {
             if (VehicleCustomer.length == 0 || VehicleCustomer == null) {
                 var html =
                     '<div class="alert alert-danger alert-dismissible fade show" role="alert">No Vehicle Selected</div>';
                 $('#ResultRecommendationBatteryVehicleMobile').html(html);
                 return;
             }
         }

         var loadingHtml = '<div class="spinner-border text-primary text-center" role="status">';
         loadingHtml += '<span class="visually-hidden">Loading...</span>';
         loadingHtml += '</div>';
         $('#ResultRecommendationBatteryVehicleMobile').html(loadingHtml);

         $.ajax({
             url: "/quotation/vehicle/find",
             type: "GET",
             data: {
                 id: VehicleCustomer,
                 custom: $('#select-custom-vehicle').is(':checked'),
                 category: $('#BatteryCategoryMobile').val() || 'ALL',
                 name: $('#IdBatteryArrayMobile').val() || 'ALL',
                 cca: $('#BatteryCCAMobile').val() || 'ALL',
                 capacity: $('#BatteryCapacityMobile').val() || 'ALL',
                 dimension: $('#BatteryDimensionMobile').val() || 'ALL',
             },
             success: function(data) {
                 var html = '';
                 if (data.length === 0) {
                     html =
                         '<div class="alert alert-danger alert-dismissible fade show" role="alert">No Battery Found</div>';
                 } else {

                     data.forEach(function(vehicle, index) {
                         html += '<div class="item product-card">';
                         if (vehicle.image == null) {
                             html +=
                                 '<img src="https://placehold.co/150" alt="" class="image-carousel">';
                         } else {
                             var baseUrl = "{{ asset('storage/image/battery/') }}";
                             vehicle.image = vehicle.image;
                             html += '<img src="' + baseUrl + '/' + vehicle.image +
                                 '" alt="" class="image-carousel" width="150px" onerror="this.onerror=null; this.src=\'https://placehold.co/150x150\';">';
                         }
                         html += '<div class="row mt-3">';
                         html += '<div class="col">';
                         html += '<div class="text-carousell">' + vehicle.name + '</div>';
                         html += '</div>';
                         html += '<div class="col-4">';
                         html +=
                             '<button class="btn btn-dark btn-sm btn-circle btn-owl-carousel btn-owl-carousel-check-' +
                             vehicle.id +
                             '" id="btn-owl-carousel" data-id="' +
                             vehicle.id + '" data-check="0" disabled>+</button>';
                         html += '</div>';
                         html += '</div>';
                         html += '<div class="text-carousell" id="stock' + vehicle.id +
                             '"></div>';
                         html += '</div>';
                     });

                 }
                 $('.loop').html(html);

                 var inventoryRequests = data.map(function(vehicle) {
                     return $.ajax({
                         url: "/inventory/get/" + vehicle.code,
                         type: "GET",
                         success: function(data) {
                             // jika data 0 atau - atau null maka disable checkbox 
                             if (data == 0 || data == '-' || data == null) {
                                 if (ignoreStock) {
                                     $(".btn-owl-carousel-check-" + vehicle.id).prop(
                                         'disabled',
                                         false);
                                 } else {
                                     $(".btn-owl-carousel-check-" + vehicle.id).prop(
                                         'disabled',
                                         true);
                                 }
                             } else {
                                 $(".btn-owl-carousel-check-" + vehicle.id).prop(
                                     'disabled',
                                     false);
                             }

                             // set stock
                             $("#stock" + vehicle.id).html("Stock : " + data);
                             // show code battery
                             if (vehicle.code != null) {
                                 $("#stock" + vehicle.id).append("<br>ID : " +
                                     vehicle.code);
                             } else {
                                 $("#stock" + vehicle.id).append("<br>ID : -");
                             }

                             // insert data to vehicle
                             vehicle.stock = data;
                         }
                     });
                 });

                 $.when.apply($, inventoryRequests).done(function() {
                     var owl = $('.loop');
                     owl.owlCarousel({
                         items: 2,
                         loop: false,
                         margin: 10,
                         dots: true,
                         responsive: {
                             600: {
                                 items: 4
                             }
                         },
                     });

                     $("#ResultRecommendationBatteryVehicleMobile").html('');
                 });


             },
             error: function() {
                 var html =
                     '<div class="alert alert-danger alert-dismissible fade show" role="alert">Error fetching data</div>';
                 $('#ResultRecommendationBatteryVehicleMobile').html(html);
             }
         });
     }

     // owl carousel button click event
     $(document).on('click', '#btn-owl-carousel', function() {
         var id = $(this).data('id');
         var check = $(this).data('check');
         var stok = $("#stock" + id).text();

         // check jika stok kosong
         if (stok == 'Stock : 0' || stok == 'Stock : -' || stok == 'Stock : null') {
             swal.fire("Error!", "Stock is empty", "error");
             return;
         }

         if (check == 0) {
             $(this).data('check', 1);
             $(this).html('<i class="fa fa-check"></i>');
             $(this).css('background-color', '#60D3AA');
         } else {
             $(this).data('check', 0);
             $(this).html('+');
             $(this).css('background-color', '#343A40');
         }
     });

     //checbox-centang on checkbox event
     $('#select-all-product').on('change', function() {
         if ($(this).is(':checked')) {
             $('.btn-owl-carousel').each(function() {
                 $(this).data('check', 1);
                 $(this).html('<i class="fa fa-check"></i>');
                 $(this).css('background-color', '#60D3AA');
             });
         } else {
             $('.btn-owl-carousel').each(function() {
                 $(this).data('check', 0);
                 $(this).html('+');
                 $(this).css('background-color', '#343A40');
             });
         }
     });

     $("#btn-screenshoot-mobile").click(function() {
         let $btn = $(this);
         $btn.prop("disabled", true);
         $btn.html(
             '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>'
         );

         // get all battery id selected in owl carousel
         var batteryId = [];
         $('.btn-owl-carousel').each(function() {
             if ($(this).data('check') == 1) {
                 batteryId.push($(this).data('id'));
             }
         });

         if (batteryId.length == 0) {
             swal.fire("Error!", "Please select battery", "error").then(() => {
                 $btn.prop("disabled", false);
                 $btn.html(
                     '<i class="fa fa-camera fa-md"></i> Capture'
                 );
             });
             return false;
         }

         $.ajax({
             url: "/quotation/battery/screenshot",
             type: "POST",
             data: {
                 Battery: batteryId,
                 _token: $('meta[name="csrf-token"]').attr('content')
             },
             success: function(data) {
                 $btn.prop("disabled", false);
                 $btn.html(
                     '<i class="fa fa-camera fa-md"></i> Capture'
                 );
                 $('#ModalScreenshotBodyMobile').html(data);
                 // check apakah didalam #body-screenshoot ada table atau tidak
                 // jika ada maka trigger click event
                 if ($('#body-screenshoot table').length > 0) {
                     $('#screenshoot-btn').trigger('click');
                     $('#ModalScreenshotMobile').modal('show');
                 }
             }
         });
     });

     $("#btn-copy-text-mobile").click(function() {
         var FullName = $("#members_name_input_mobile").val();
         var Battery = [];
         $('.btn-owl-carousel').each(function() {
             if ($(this).data('check') == 1) {
                 Battery.push($(this).data('id'));
             }
         });

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


     //if personal-details-mobile-next-button-lower click event
     $('#personal-details-mobile-next-button-lower').on('click', function() {
         // init maps
         initMapsMobile();
         $(".loop2").owlCarousel('destroy');
         $(".loop2").html('');
         var members_name_input_mobile = $('#members_name_input_mobile').val();
         var vehicle_customer_input_mobile = $('#vehicle_customer_input_mobile').val();

         //  if (members_name_input_mobile == '') {
         //      swal.fire("Error!", "Members Name is required", "error");
         //      return;
         //  }

         if (vehicle_customer_input_mobile == null || vehicle_customer_input_mobile.length == 0) {
             swal.fire("Error!", "Vehicle Customer is required", "error");
             return;
         }

         // get all battery id selected in owl carousel
         var batteryId = [];
         $('.btn-owl-carousel').each(function() {
             if ($(this).data('check') == 1) {
                 batteryId.push($(this).data('id'));
             }
         });

         if (batteryId.length == 0) {
             swal.fire("Error!", "Please select battery", "error");
             return;
         }

         $.ajax({
             url: "/quotation/battery/find",
             type: "GET",
             data: {
                 id: vehicle_customer_input_mobile,
                 Battery: batteryId,
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
                     // move to next tab step 2
                     $("#personal-details-mobile-li").removeClass("active");
                     $("#personal-details-mobile-tab").css("display", "none");
                     $("#product-recommendation-mobile-li").addClass("active");
                     $("#product-recommendation-mobile-tab").css("display", "block");

                     data.forEach(function(vehicle, index) {
                         html += '<div class="item product-card">';
                         if (vehicle.image == null) {
                             html +=
                                 '<img src="https://via.placeholder.com/150" alt="" class="image-carousel">';
                         } else {
                             var baseUrl = "{{ asset('storage/image/battery/') }}";
                             vehicle.image = vehicle.image;
                             html += '<img src="' + baseUrl + '/' + vehicle.image +
                                 '" alt="" class="image-carousel" width="150px">';
                         }
                         html += '<div class="row mt-3">';
                         html += '<div class="col">';
                         html += '<div class="text-carousell">' + vehicle.name + '</div>';
                         html += '</div>';
                         html += '<div class="col-4">';
                         html +=
                             '<button class="btn btn-dark btn-sm btn-circle btn-owl-carousel-step-2" id="btn-owl-carousel-step-2" data-id="' +
                             vehicle.id +
                             '" data-check="1" style="background-color:#60D3AA;" disabled><i class="fa fa-check"></i></button>';
                         html += '</div>';
                         html += '</div>';
                         html += '</div>';
                     });


                     $('.loop2').html(html);

                     var owl = $('.loop2');
                     owl.owlCarousel({
                         items: 2,
                         loop: false,
                         margin: 10,
                         dots: true,
                         responsive: {
                             600: {
                                 items: 4
                             }
                         },
                     });
                 }
             }
         });
     });

     $('#select-ignore-stock').on('change', function() {
         getBatteryByVehicleMobile();
     });
 </script>
