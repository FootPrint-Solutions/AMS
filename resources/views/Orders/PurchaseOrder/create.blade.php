@extends('template.master')
{{-- @dd($data); --}}

@section('content')
    <link rel="stylesheet" href="{{ asset('plugins/bootstrap5-toggle/css/bootstrap5-toggle.min.css') }}">
    <style>
        #MapsAddressFinder {
            height: 400px;
            width: 100%;
            margin-bottom: 20px;
        }
    </style>

    {{-- Form --}}
    <div class="d-none d-lg-block">
        <div class="card">
            <div class="card-body">
                {{-- Title --}}
                <div class="card-title h5">
                    @if (isset($data['profile']))
                        Edit
                    @else
                        Add New
                    @endif
                    Purchase Order
                </div>
                <br>

                {{-- Form --}}
                <form id="quotation-form">
                    @csrf

                    <input type="hidden" id="type" name="type" value="regular">

                    {{-- Quotation Number & Date --}}
                    <div class="row">
                        {{-- Quotation Number --}}
                        <div class="col">
                            <div class="form-group local-forms">
                                <label for="purchase-order-number">PO Number <span class="login-danger">*</span></label>
                                <input type="text" class="form-control" id="purchase-order-number"
                                    name="purchaseordernumber" placeholder="Enter distributor name" required readonly
                                    @isset($data['profile'])
                                value="{{ $data['profile']['purchase_order_number'] }}"
                                @else
                                value="{{ $data['number'] }}"
                                @endisset>
                            </div>
                        </div>

                        {{-- Date --}}
                        <div class="col">
                            <div class="form-group local-forms">
                                <label for="quotation-date">Quotation Date <span class="login-danger">*</span></label>
                                <input type="date" class="form-control" id="quotation-date" name="date" required
                                    @isset($data['profile'])
                                value="{{ \Carbon\Carbon::parse($data['profile']['date'])->format('Y-m-d') }}"
                                @else
                                value="{{ date('Y-m-d') }}"
                                @endisset>
                            </div>
                        </div>

                        {{-- Supplier --}}
                        <div class="col">
                            <div class="row">
                                <div class="col">
                                    <div class="form-group local-forms">
                                        <label for="vendor">Vendor <span class="login-danger">*</span>
                                            <i class="fas fa-info-circle ms-1 text-muted" data-toggle="tooltip"
                                                data-placement="top"
                                                title="This vendor data contains customer, supplier, or distributor shop data."></i>
                                        </label>
                                        <select class="form-control" id="vendor" name="vendor" required>

                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Shops --}}
                        <div class="col">
                            <div class="form-group local-forms">
                                <label for="ship_to">Ship To <span class="login-danger">*</span>
                                    <i class="fas fa-info-circle ms-1 text-muted" data-toggle="tooltip" data-placement="top"
                                        title="This vendor data contains shop, distributor, or customer data."></i>
                                </label>
                                <select class="form-control" id="ship_to" name="ship_to" required>
                                </select>
                            </div>
                        </div>

                        <div class="col">
                            <div class="form-group local-forms">
                                <label for="address">Address <span class="login-danger">*</span></label>
                                <input type="text" class="form-control" name="Address"
                                    id="AddressSearchColumnSalesOrderEditable"
                                    value="@if (isset($data['profile'])) {{ ltrim($data['profile']['address']) }} @endif"
                                    required autocomplete="off">
                            </div>
                        </div>

                        {{-- Invoice Number --}}
                        <div class="col">
                            <div class="form-group local-forms">
                                <label for="InvoiceNumber">Invoice Number <span class="login-danger">*</span></label>
                                <input type="text" class="form-control" name="InvoiceNumber" id="InvoiceNumber"
                                    value="@if (isset($data['profile'])) {{ ltrim($data['profile']['invoice_number']) }} @endif"
                                    autocomplete="off">
                            </div>
                        </div>
                    </div>

                    {{-- Details --}}
                    <table class="table mb-2" id="table-battery-detail">
                        {{-- Header --}}
                        <thead>
                            <tr>
                                <td colspan="7" class="h5 text-center">
                                    Item @if (!isset($data['profile']))
                                        <button type="button" id="btn-add-row"
                                            class="btn btn-primary btn-sm rounded-circle mx-2"><i
                                                class="fas fa-plus"></i></button>
                                    @endif
                                </td>
                            </tr>

                            <tr class="text-center">
                                <td class="p-1 text-muted small">Production Code</td>
                                <td class="p-1 text-muted small">Name</td>
                                <td class="p-1 text-muted small">Price Retail</td>
                                <td class="p-1 text-muted small">Tax</td>
                                <td class="p-1 text-muted small">Price + Tax</td>
                                <td class="p-1 text-muted small">Discount</td>
                                <td class="p-1 text-muted small">Price Net</td>
                            </tr>
                        </thead>

                        {{-- Body (Items) --}}
                        <tbody>
                            @php
                                $batteries = isset($data['profile']['batteries'])
                                    ? $data['profile']['batteries']
                                    : [''];
                                $counter = 1;
                            @endphp

                            @foreach ($batteries as $battery)
                                @php
                                    if (
                                        isset($battery['battery']['type']) &&
                                        $battery['battery']['type'] != 'regular'
                                    ) {
                                        $class_tr = 'bg-danger';
                                    } else {
                                        $class_tr = '';
                                    }
                                @endphp
                                <tr class="table-battery-detail-row {{ $class_tr }}">
                                    {{-- Production Code --}}
                                    <td>
                                        <input type="text" class="form-control battery-code" id="battery-production-code"
                                            name="batteriescode[]" placeholder="Enter item production code"
                                            @isset($data['profile']['batteries'])value="{{ $battery['battery_production_code'] }}" @endisset>
                                    </td>

                                    {{-- Name --}}
                                    <td>
                                        @php
                                            $targets = [
                                                "battery-priceretail-$counter",
                                                "battery-discount-$counter",
                                                "battery-type-$counter",
                                                "battery-type-$counter",
                                            ];
                                            $encodedTargets = json_encode($targets);
                                        @endphp

                                        @isset($data['profile'])
                                            <input type="text" class="form-control" required
                                                @isset($data['profile']['batteries']) readonly @endisset
                                                @isset($data['profile']['batteries']) value="{{ $battery['battery_name'] }}" @endisset>
                                        @else
                                            @component('components.autocomplete', [
                                                'id' => "battery-name-$counter",
                                                'class' => 'battery-name',
                                                'value' => isset($data['profile']['batteries']) ? $battery['battery_name'] : '',
                                                'name' => 'batteriesname[]',
                                                'nameHiddenId' => 'batteriesid[]',
                                                'url' => '/battery/get/',
                                                'placeholder' => 'Enter item name',
                                                'targets' => $encodedTargets,
                                                'callback' => 'calculateTotal',
                                            ])
                                            @endcomponent
                                        @endisset
                                    </td>

                                    {{-- Retail Price --}}
                                    <td>
                                        <div class="input-group">
                                            <span class="input-group-text border-end">IDR</span>
                                            <input type="text" class="form-control text-end battery-priceretail"
                                                id="battery-priceretail-{{ $counter }}"
                                                name="batteriespriceretail[]" placeholder="Enter item retail price"
                                                required
                                                @isset($data['profile']['batteries']) value="{{ $battery['battery_price_retail'] }}" @endisset>
                                        </div>
                                    </td>

                                    {{-- Tax --}}
                                    <td>
                                        {{-- input type hidden battery type --}}
                                        <input type="hidden"
                                            class="form-control battery-type-{{ $counter }} battery-type"
                                            id="battery-type-{{ $counter }}" name="batteriestype[]"
                                            @isset($battery['type']) value="{{ $battery['type'] }}" @endisset>


                                        <div class="input-group">
                                            <input type="text" class="form-control text-end battery-tax"
                                                id="battery-tax-{{ $counter }}" name="batteriestax[]" required
                                                readonly
                                                @isset($data['profile']['batteries']) value="{{ $battery['tax'] }}" @else value="{{ $data['tax'] }}" @endisset>
                                            <span class="input-group-text border-end">%</span>
                                        </div>

                                        <input type="hidden" class="form-control text-end battery-taxprice"
                                            id="battery-taxprice-{{ $counter }}" name="batteriestaxprice[]">
                                    </td>

                                    {{-- Tax Price --}}
                                    <td>
                                        <div class="input-group">
                                            <span class="input-group-text border-end">IDR</span>
                                            <input type="text" class="form-control text-end battery-priceaftertax"
                                                id="battery-priceaftertax-{{ $counter }}" required readonly
                                                @isset($data['profile']['batteries']) value="{{ $battery['battery_price_retail'] + $battery['tax_price'] }}" @endisset>
                                        </div>
                                    </td>

                                    {{-- Discount --}}
                                    <td>
                                        <div class="input-group">
                                            <span class="input-group-text border-end">IDR</span>
                                            <input type="number" class="form-control text-end battery-discountprice"
                                                id="battery-discountprice-{{ $counter }}"
                                                name="batteriesdiscountprice[]" placeholder="Enter item discount price"
                                                required @isset($data['profile']['batteries']) readonly @endisset
                                                @isset($data['profile']['batteries']) value="{{ $battery['discount_price'] }}" @endisset>
                                        </div>

                                        <input type="hidden" class="form-control text-end battery-discount"
                                            id="battery-discount-{{ $counter }}" name="batteriesdiscount[]">
                                    </td>

                                    {{-- Net Price --}}
                                    <td>
                                        <div class="row">
                                            @if (!isset($data['profile']))
                                                <div class="col">
                                            @endif

                                            <div class="input-group">
                                                <span class="input-group-text border-end">IDR</span>
                                                <input type="text" class="form-control text-end battery-price"
                                                    id="battery-price-{{ $counter }}" name="batteriesprice[]"
                                                    placeholder="Enter item price" required readonly
                                                    @isset($data['profile']['batteries']) value="{{ $battery['price_net'] }}" @endisset>
                                            </div>

                                            @if (!isset($data['profile']))
                                        </div>

                                        <div class="col-sm-2">
                                            <button type="button" class="btn btn-danger btn-sm disabled btn-delete-row"
                                                title="Delete Item"><i class="fas fa-xmark"></i></button>
                                        </div>
                            @endif
            </div>
            </td>

            {{-- Hidden Inputs --}}
            @isset($data['profile']['batteries'])
                <input type="hidden" name="detailid[]" value="{{ $battery['id'] }}">
            @endisset
            </tr>

            @php
                $counter++;
            @endphp
            @endforeach
            </tbody>

            {{-- Footer (Tax, Discount, Total) --}}
            <tfoot>
                {{-- Subtotal --}}
                <tr>
                    <td colspan="5"></td>
                    <td class="text-end">Subtotal</td>
                    <td>
                        <div class="input-group">
                            <span class="input-group-text border-end">IDR</span>
                            <input type="text" class="form-control text-end" id="subtotal" name="subtotal"
                                @isset($data['profile'])value="{{ $data['profile']['subtotal'] }}" @else value="0" @endisset
                                readonly required>
                        </div>
                    </td>
                </tr>

                {{-- Discount --}}
                <tr>
                    <td colspan="5"></td>
                    <td class="text-end">Discount</td>
                    <td>
                        <div class="row">
                            <div class="col">
                                {{-- Discount Price --}}
                                <div class="input-group" id="discount-price">
                                    <span class="input-group-text border-end">IDR</span>
                                    <input type="text" class="form-control text-end" id="discount-price-value"
                                        name="discountprice" @isset($data['profile']['discount']) readonly @endisset
                                        @isset($data['profile'])value="{{ $data['profile']['discount_price'] }}" @else value="0" @endisset
                                        required>
                                </div>
                            </div>

                            <div class="col-sm-2">
                                <input type="checkbox" id="toggle-discount" data-toggle="toggle" data-size="sm"
                                    data-offlabel="%" data-onlabel="IDR" checked readonly>
                            </div>
                        </div>
                    </td>
                </tr>

                {{-- Total --}}
                <tr>
                    <td colspan="5"></td>
                    <td class="text-end">Total</td>
                    <td>
                        <div class="input-group">
                            <span class="input-group-text border-end">IDR</span>
                            <input type="text" class="form-control text-end" id="total" name="total"
                                @isset($data['profile'])value="{{ $data['profile']['total'] }}" @else value="0" @endisset
                                required readonly>
                        </div>
                    </td>
                </tr>

                {{-- Payment Method & Status --}}
                <tr>
                    <td colspan="5"></td>
                    <td class="text-end">Payment Status</td>
                    <td>
                        <div class="row">
                            <div class="col">
                                <select name="status" id="status" class="form-control" required>
                                    <option value="paid" @if (isset($data['profile']) && $data['profile']['status'] == 'paid') selected @endif>Paid
                                    </option>
                                    <option value="pending" @if (isset($data['profile']) && $data['profile']['status'] == 'pending') selected @endif>
                                        Pending</option>
                                    <option value="failed" @if (isset($data['profile']) && $data['profile']['status'] == 'failed') selected @endif>
                                        Failed</option>
                                </select>
                            </div>
                        </div>

                    </td>
                </tr>
            </tfoot>
            </table>
            <br>

            {{-- Hidden Inputs --}}
            @isset($data['profile'])
                <input type="hidden" id="id" name="id" value="{{ $data['profile']['id'] }}">
            @endisset

            {{-- Buttons --}}
            <div class="d-flex flex-row-reverse">
                {{-- Create Button --}}
                <button type="submit" class="btn btn-success mx-1" id="btn-save"
                    @if (isset($data['profile'])) value="update">
                        Update
                        @else
                        value="create">
                        Create @endif
                    Quotation </button>

                    {{-- Cancel Button --}}
                    <button type="reset" class="btn btn-danger mx-1" id="btn-cancel">Cancel</button>
            </div>
            </form>
        </div>
    </div>


    <script>
        $(document).ready(function() {
            formatPrice($(".battery-priceretail"));
            formatPrice($(".battery-priceaftertax"));
            formatPrice($(".battery-price"));
            formatPrice($("#discount-price-value"));
            formatPrice($("#subtotal"));
            formatPrice($("#total"));

            calculateTotal();
        });
    </script>

    @isset($data['profile'])
        <script>
            (function() {
                // Vendor
                var vendorId =
                    "{{ $data['profile']['vendor_id'] ?? '' }}";
                var vendorType =
                    {!! json_encode($data['profile']['vendor_type'] ?? '') !!};
                var vendorText =
                    "{{ $data['profile']['vendor']['name'] ?? '' }}";
                if (vendorId && vendorType) {
                    var option = new Option(vendorText, vendorId + '-' + vendorType, true,
                        true);
                    $('#vendor').append(option).trigger('change');
                }

                // Ship To
                var shipToId =
                    "{{ $data['profile']['ship_to_id'] ?? '' }}";
                var shipToType =
                    {!! json_encode($data['profile']['ship_to_type'] ?? '') !!};
                var shipToText =
                    "{{ $data['profile']['ship_to']['name'] ?? '' }}";
                if (shipToId && shipToType) {
                    var option = new Option(shipToText, shipToId + '-' + shipToType, true,
                        true);
                    $('#ship_to').append(option).trigger('change');
                }
            })();
        </script>
    @endisset

    {{-- Select2 Configurations --}}
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


        $(document).ready(function() {
            $('#vendor').select2({
                placeholder: "Enter vendor",
                minimumInputLength: 1,
                ajax: {
                    url: "/purchase-order/vendor/get",
                    dataType: "json",
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term
                        };
                    },
                    processResults: function(response) {
                        var items = (response && response.data) ? response.data : response;
                        return {
                            results: items.map(function(item) {
                                return {
                                    id: item.id + '-' + item.reference_type,
                                    text: item.text || item.name || '',
                                    raw_id: item.id,
                                    type: item.type,
                                    reference_type: item.reference_type || null,
                                };
                            })
                        };
                    },
                    cache: true
                },
                escapeMarkup: function(markup) {
                    return markup;
                },
                templateResult: function(repo) {
                    return repo.text;
                },
                templateSelection: function(repo) {
                    return repo.text;
                }
            });

            $('#ship_to').select2({
                placeholder: "Enter Ship To",
                minimumInputLength: 1,
                ajax: {
                    url: "/purchase-order/shipto/get",
                    dataType: "json",
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term
                        };
                    },
                    processResults: function(response) {
                        var items = (response && response.data) ? response.data : response;
                        return {
                            results: items.map(function(item) {
                                return {
                                    id: item.id + '-' + item.reference_type,
                                    text: item.text || item.name || '',
                                    raw_id: item.id,
                                    type: item.type,
                                    reference_type: item.reference_type || null,
                                };
                            })
                        };
                    },
                    cache: true
                },
                escapeMarkup: function(markup) {
                    return markup;
                },
                templateResult: function(repo) {
                    return repo.text;
                },
                templateSelection: function(repo) {
                    return repo.text;
                }
            });

            $('#status').select2({});
        })
    </script>

    {{-- Form Handler --}}
    <script>
        let indexUrl = "/purchase-order";

        $("#quotation-form").on("submit", function(event) {
            event.preventDefault();

            let mode = $("#btn-save").attr("value"); // update || create
            let url = (mode == "update") ? "/purchase-order/update" : "/purchase-order/store";

            let address = $("#AddressSearchColumnSalesOrderEditable").val();
            let lat = $("#Latitude").val();
            let lon = $("#Longitude").val();
            if (address == "") {
                Swal.fire({
                    title: "Error",
                    text: "Please select address",
                    icon: "error",
                });
                return;
            }
            calculateTotal();

            // Show loading Swal
            // Swal.fire({
            //     title: "Processing...",
            //     text: "Please wait while your data is being saved.",
            //     allowOutsideClick: false,
            //     didOpen: () => {
            //         Swal.showLoading();
            //     }
            // });

            // Obtain submitted form data.
            let formData = new FormData($(this)[0]);

            // Send submit POST request via AJAX.
            sendSubmitRequest(url, formData, function() {
                // Redirect to index page.
                goToPage(indexUrl);
            });
        });

        $("#quotation-form").on("reset", function() {
            goToPage(indexUrl);
        });
    </script>

    {{-- Click Event Handler --}}
    <script>
        $(document).ready(function() {
            $("#btn-add-row").on("click", function() {
                // Enable the delete row button as a new row is to be appended.
                $(".btn-delete-row").removeClass("disabled");
                calculateTotal();

                // Clone the last row.
                let newRow = $('.table-battery-detail-row').last().clone();
                newRow.find('input').not('.battery-tax').val('');
                newRow.find('.btn-delete-row').removeClass('disabled');

                // Set new id to each elements inside.
                let number;
                newRow.find('*[id]').each(function() {
                    let id = $(this).attr("id");
                    let parts = id.split('-');
                    number = parseInt(parts[parts.length - 1]) + 1;
                    $(this).attr("id", parts[0] + '-' + parts[1] + '-' + number);
                });

                var targets = JSON.stringify(["battery-priceretail-" + number,
                    "battery-discount-" + number, "battery-type-" +
                    number,
                ]);
                newRow.find(".autocomplete").attr("data-targets", targets);

                $('#table-battery-detail tbody').append(newRow);
            });
        });

        // Attach a click event handler to all delete row buttons.
        $(document).on("click", ".btn-delete-row", function() {
            let count = $(".table-battery-detail-row").length;
            if (count > 1) {
                $(this).closest("tr").remove();
                $(".btn-delete-row").removeClass("disabled");

                // Check whether the number of rows is exactly two.
                // If it is and one of them is about to be deleted, disable the delete row.
                if (count === 2) {
                    $(".btn-delete-row").addClass("disabled");
                }
            }
        });
    </script>

    {{-- Change Event Handler --}}
    <script src="{{ asset('plugins/bootstrap5-toggle/js/bootstrap5-toggle.ecmas.min.js') }}" defer></script>
    <script>
        $(document).ready(function() {
            $('#toggle-tax').on("change", function() {
                if ($(this).prop('checked')) {
                    $("#tax-price").removeClass("d-none");
                    $("#tax-percentage").addClass("d-none");
                } else {
                    $("#tax-price").addClass("d-none");
                    $("#tax-percentage").removeClass("d-none");
                }
            });

            $('#toggle-discount').on("change", function() {
                if ($(this).prop('checked')) {
                    $("#discount-price").removeClass("d-none");
                    $("#discount-percentage").addClass("d-none");
                } else {
                    $("#discount-price").addClass("d-none");
                    $("#discount-percentage").removeClass("d-none");
                }
            });
        });

        $(document).on("change keyup",
            ".battery-discountprice, #tax, #discount, #discount-price-value, #extra-discount, .battery-priceretail",
            function() {
                // Validate input value.
                let value = parseInt($(this).val(), 10);
                if (isNaN(value)) {
                    $(this).val("0");
                }

                // Recalculate total value.
                calculateTotal($(this).attr("id") === "discount-price-value");
            });
    </script>

    {{-- Keyup Event Handler --}}
    <script>
        // Attach a keyup event handler for each battery price fields.
        $(document).on("keyup", ".battery-price", function() {
            formatPrice($(this));
            calculateTotal();
        });
    </script>

    {{-- JS functions --}}
    <script>
        /**
         * Load technician select list data.
         */
        function loadTechnicianData() {
            let parentId = $("#shop").val();

            if (parentId)
                $.ajax({
                    url: "/purchase-order/technician/get/" + parentId,
                    method: "GET",
                    success: function(response) {
                        // Clear current options and value.
                        $("#technician").empty().val(null).trigger("change");

                        let emptyOption = new Option("", "", false, false);
                        $("#technician").append(emptyOption).trigger("change");

                        let mode = $("#btn-save").attr("value"); // update || create
                        response.forEach(function(menu) {
                            // Append new options.
                            let selected = false;
                            if (mode == 'update') {
                                // Get saved technician id.
                                let id = $("#technician_id").val();
                                if (menu.id == id) {
                                    selected = true;
                                }
                            }

                            let newOption = new Option(menu.name, menu.id, false,
                                selected);
                            $("#technician").append(newOption).trigger("change");
                        });
                    }
                });
        }

        /**
         * Calculate the total price with tax, discount, and extra discount included.
         * 
         * @param {boolean} discountPrice - (Optional) Indicates if the discount price value has been changed..
         * @returns {number} The total price after applying tax, discount, and extra discount.
         */
        function calculateTotal(discountPriceIsChanged = false) {
            // Calculate subtotal based on each items' price.
            let subtotal = 0;
            $(".battery-price").each(function() {
                let row = $(this).closest('tr');
                let priceRetail = parseInt(row.find(".battery-priceretail").val().replace(/\D/g, ''));
                let tax = parseInt(row.find(".battery-tax").val());
                let type = row.find(".battery-type").val();

                if (type == 'recycle') {
                    $(this).closest('tr').addClass('bg-danger');
                } else {
                    $(this).closest('tr').removeClass('bg-danger');
                }

                // Count price + tax.
                let priceTax = priceRetail * tax / 100;
                row.find(".battery-taxprice").val(priceTax);
                let priceAfterTax = Math.round(priceRetail + priceTax);
                row.find(".battery-priceaftertax").val(priceAfterTax);

                // Count price + tax - discount.
                let discountPrice = parseInt(row.find(".battery-discountprice").val().replace(/\D/g, ''));
                let discount = discountPrice / priceAfterTax * 100;
                row.find(".battery-discount").val(discount);
                $(this).val(priceAfterTax - discountPrice);
                let value = parseInt($(this).val().replace(/\D/g, ''));
                if (!isNaN(value)) {
                    if (type != 'regular') {
                        subtotal += value;
                        console.log("Subtotal: " + subtotal);
                        console.log("type: " + type);
                    } else {
                        subtotal += value;
                    }
                }

                // Format displayed price.
                formatPrice(row.find(".battery-priceaftertax"));
                formatPrice($(this));
            });
            $("#subtotal").val(subtotal);

            // Obtain and calculate discount and tax value.
            let discount = parseInt($("#discount-price-value").val().replace(/\D/g, '')) || 0;
            $("#discount").val(Math.round(discount / subtotal * 100));

            // Calculate total value.
            let total = (subtotal - discount);
            $("#total").val(total);

            // Format all price fields value.
            formatPrice($(".battery-priceretail"));
            formatPrice($("#subtotal"));
            formatPrice($("#total"));
            formatPrice($("#discount-price-value"));

            return total;
        }
    </script>
@endsection
