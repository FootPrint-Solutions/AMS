@extends('template.master')

@section('content')
    <link rel="stylesheet" href="{{ asset('plugins/bootstrap5-toggle/css/bootstrap5-toggle.min.css') }}">
    <style>
        .select2-container--open {
            z-index: 1100;
        }
    </style>

    {{-- Form --}}
    <div class="card">
        <div class="card-body">
            {{-- Title --}}
            <div class="card-title h5">
                @isset($data['profile'])
                    Edit
                @else
                    Add New
                @endisset
                Promo
            </div>
            <br>

            {{-- Form --}}
            <form id="promo-form">
                @csrf

                {{-- Name & Period --}}
                <div class="row">
                    {{-- Name --}}
                    <div class="col">
                        <div class="form-group local-forms">
                            <label for="name">Name <span class="login-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name"
                                placeholder="Enter promo name" required
                                @if (isset($data['profile'])) value="{{ $data['profile']['name'] }}" @endif>
                        </div>
                    </div>

                    {{-- Period Start --}}
                    <div class="col">
                        <div class="form-group local-forms">
                            <label for="period-start">Period Start<span class="login-danger">*</span></label>
                            <input type="date" class="form-control" id="period-start" name="periodstart" required
                                value=@isset($data['profile']) {{ $data['profile']['period_start'] }} @else {{ date('Y-m-d') }} @endisset>
                        </div>
                    </div>

                    {{-- Period End --}}
                    <div class="col">
                        <div class="form-group local-forms">
                            <label for="period-end">Period End<span class="login-danger">*</span></label>
                            <input type="date" class="form-control" id="period-end" name="periodend" required
                                value=@isset($data['profile']) {{ $data['profile']['period_end'] }} @else {{ date('Y-m-d') }} @endisset>
                        </div>
                    </div>
                </div>

                {{-- Discount & Net Price Generator --}}
                <div class="row">
                    <div class="col">
                        <div class="row">
                            <div class="col">
                                <div class="input-group">
                                    <input type="text" class="form-control" id="battery-discount-all">
                                    <span class="input-group-text border-end">%</span>
                                </div>
                            </div>

                            <div class="col-sm-4">
                                <button type="button" class="btn btn-info mx-1" id="btn-apply-discount">Apply Discount to
                                    All</button>
                            </div>
                        </div>
                    </div>

                    <div class="col">
                        <div class="row">
                            <div class="col">
                                <div class="input-group">
                                    <span class="input-group-text border-end">IDR</span>
                                    <input type="text" class="form-control" id="battery-pricenet-all">
                                </div>
                            </div>

                            <div class="col-sm-4">
                                <button type="button" class="btn btn-info mx-1" id="btn-apply-price">Apply Net Price to
                                    All</button>
                            </div>
                        </div>
                    </div>
                </div>
                <br>

                {{-- Details --}}
                <table class="table mb-2" id="table-battery-detail">
                    {{-- Header --}}
                    <thead>
                        <tr>
                            <td colspan="4" class="h5 text-center">
                                Item
                                <button type="button" id="btn-add-battery"
                                    class="btn btn-primary btn-sm rounded-circle mx-2" data-bs-toggle="modal"
                                    data-bs-target="#battery-modal">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </td>
                        </tr>
                    </thead>

                    {{-- Body (Items) --}}
                    <tbody>
                        @php
                            $batteries = isset($data['profile']['batteries']) ? $data['profile']['batteries'] : [''];
                            $counter = 1;
                        @endphp

                        @foreach ($batteries as $battery)
                            <tr class="table-battery-detail-row d-none">
                                {{-- Name --}}
                                <td>
                                    <input type="text" class="form-control battery-name"
                                        id="battery-name-{{ $counter }}" name="batteriesname[]"
                                        placeholder="Enter battery name"
                                        @isset($data['profile']['batteries']) value="{{ $battery['discount'] }}" @endisset>
                                </td>

                                {{-- Retail Price --}}
                                <td>
                                    <div class="input-group">
                                        <span class="input-group-text border-end">IDR</span>
                                        <input type="text" class="form-control text-end battery-priceretail"
                                            id="battery-priceretail-{{ $counter }}" name="batteriespriceretail[]"
                                            placeholder="Enter item retail price" readonly>
                                    </div>
                                </td>

                                {{-- Net Price & Delete --}}
                                <td>
                                    {{-- Net Price --}}
                                    <div class="row">
                                        <div class="col">
                                            {{-- Discount Percentage --}}
                                            <div class="input-group" id="battery-discountpercentage-{{ $counter }}">
                                                <input type="text" class="form-control battery-discount"
                                                    id="battery-discount-{{ $counter }}" name="batteriesdisc[]"
                                                    placeholder="Enter battery discount"
                                                    @isset($data['profile']['batteries']) value="{{ $battery['discount'] }}" @endisset>
                                                <span class="input-group-text border-end">%</span>
                                            </div>

                                            {{-- Discount Price --}}
                                            <div class="input-group d-none" id="battery-discountprice-{{ $counter }}">
                                                <span class="input-group-text border-end">IDR</span>
                                                <input type="text" class="form-control text-end battery-pricenet"
                                                    id="battery-pricenet-{{ $counter }}" name="batteriespricenet[]"
                                                    placeholder="Enter item net price" required
                                                    @isset($data['profile']['batteries']) value="{{ $battery['net_price'] }}" @endisset>
                                            </div>
                                        </div>

                                        {{-- Toggle --}}
                                        <div class="col-sm-2">
                                            <input type="checkbox" class="toggle-discount"
                                                id="toggle-discount-{{ $counter }}" data-toggle="toggle"
                                                data-size="sm" data-offlabel="%" data-onlabel="IDR" data-width="70"
                                                data-height="25">
                                        </div>

                                        {{-- Delete --}}
                                        <div class="col-sm-1">
                                            <button type="button" class="btn btn-danger btn-sm btn-delete-row"
                                                title="Delete Item"><i class="fas fa-xmark"></i></button>
                                        </div>
                                    </div>
                                </td>

                                {{-- Hidden Inputs --}}
                                <input type="hidden" name="detailid[]" id="battery-id-{{ $counter }}"
                                    @isset($data['profile']['batteries']) value="{{ $battery['id'] }}" @endisset>
                            </tr>

                            @php
                                $counter++;
                            @endphp
                        @endforeach
                    </tbody>
                </table>
                <br>

                {{-- Hidden Inputs --}}
                <input type="hidden" id="id" name="id"
                    @if (isset($data['profile'])) value="{{ $data['profile']['id'] }}" @endif>

                {{-- Buttons --}}
                <div class="d-flex flex-row-reverse">
                    {{-- Create or Update Button --}}
                    <button type="submit" class="btn btn-success mx-1" id="btn-save"
                        @isset($data['profile']) value="update">
                    Update
                    @else
                    value="create">
                    Create @endisset
                        Promo </button>

                        {{-- Cancel Button --}}
                        <button type="reset" class="btn btn-danger mx-1" id="btn-cancel">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Battery --}}
    <div id="battery-modal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                {{-- Header --}}
                <div class="modal-header">
                    <h4 class="modal-title" id="standard-modalLabel">Add Batteries</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                {{-- Body --}}
                <div class="modal-body">
                    {{-- Size Category --}}
                    <div class="form-group local-forms">
                        <label for="size">Size Category</label>
                        <select class="form-control" id="size">
                            <option></option>
                            @foreach ($data['battery_categories'] as $size)
                                <option value="{{ $size['id'] }}">
                                    {{ $size['name'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Battery List --}}
                    <ul class="list-group" id="list-battery"></ul>
                </div>

                {{-- Footer --}}
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="btn-add-modal">Add batteries</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Select2 Configurations --}}
    <script>
        var selectedBatteries = [];

        $(document).ready(function() {
            $('#size').select2({
                placeholder: "Enter battery size category"
            });

            $("#size").on("select2:select", function(e) {
                // Empty all current items in list.
                $('#list-battery').empty();

                // Get selected size category.
                let sizeId = e.params.data.id;

                // Show the list of batteries of the selected size category.
                $.ajax({
                    url: "/battery/get/size/" + sizeId,
                    success: function(data) {
                        selectedBatteries = [];

                        data.forEach(battery => {
                            // Make the list item for battery.
                            let item = document.createElement('li');
                            item.className = 'list-group-item';
                            item.innerHTML = battery.name;

                            // Append the created list item into list.
                            $("#list-battery").append(item);

                            // Append battery to selected battery.
                            selectedBatteries.push(battery);
                        });
                    }
                });
            });
        });
    </script>

    {{-- Form Hanlder --}}
    <script>
        let indexUrl = "/promo";

        $(document).ready(function() {
            $("#promo-form").on("submit", function(event) {
                event.preventDefault();

                let mode = $("#btn-save").attr("value"); // update || create
                let url = (mode == "update") ? "/promo/update" : "/promo/store";

                // Obtain submitted form data.
                let formData = new FormData($(this)[0]);

                // Send submit POST request via AJAX.
                sendSubmitRequest(url, formData, function() {
                    // Redirect to index page.
                    goToPage(indexUrl);
                });
            });

            $("#promo-form").on("reset", function() {
                goToPage(indexUrl);
            });
        });
    </script>

    {{-- Modal Handler --}}
    <script>
        $('#battery-modal').on('shown.bs.modal', function() {
            $("#size").val(null).trigger('change');
            $('#list-battery').empty();
        });
    </script>

    {{-- Change Event Handler --}}
    <script src="{{ asset('plugins/bootstrap5-toggle/js/bootstrap5-toggle.ecmas.min.js') }}" defer></script>
    <script>
        $(document).on("change", ".toggle-discount", function() {
            let id = $(this).attr("id");
            let parts = id.split('-');
            let counter = parts[parts.length - 1];

            if ($(this).prop('checked')) {
                $("#battery-discountprice-" + counter).removeClass("d-none");
                $("#battery-discountpercentage-" + counter).addClass("d-none");
            } else {
                $("#battery-discountprice-" + counter).addClass("d-none");
                $("#battery-discountpercentage-" + counter).removeClass("d-none");
            }
        });

        $(document).ready(function() {
            $(".battery-discount, .battery-pricenet").on("change", function() {
                // Validate input value.
                let value = parseInt($(this).val(), 10);
                if (isNaN(value)) {
                    $(this).val("0");
                }

                let id = $(this).attr("id");
                let parts = id.split('-');
                let counter = parts[parts.length - 1];

                // Recalculate total value.
                calculatePriceDiscount(counter, $(this).hasClass("battery-discount"));
            });
        });
    </script>

    {{-- Click Event Handler --}}
    <script>
        $(document).ready(function() {
            $("#btn-apply-discount").on('click', function() {
                $(".battery-discount").val($("#battery-discount-all").val());

                // Calculate the price based on applied discount.
                $(".battery-discount").each(function() {
                    let element = $(this);
                    let id = element.attr("id");
                    let parts = id.split('-');
                    let counter = parts[parts.length - 1];
                    calculatePriceDiscount(counter, true);
                });
            });

            $("#btn-apply-price").on('click', function() {
                $(".battery-pricenet").val($("#battery-pricenet-all").val());

                // Calculate the discount based on applied price.
                $(".battery-discount").each(function() {
                    let element = $(this);
                    let id = element.attr("id");
                    let parts = id.split('-');
                    let counter = parts[parts.length - 1];
                    calculatePriceDiscount(counter, false);
                });
            });

            $("#btn-add-modal").on('click', function() {
                selectedBatteries.forEach(battery => {
                    // Get the count of rows.
                    let count = $(".table-battery-detail-row").length;

                    // Get the last row in list.
                    let newRow = $('.table-battery-detail-row').last();

                    // Check if the last row has been unhidden.
                    // If it's been unhidden, clone it.
                    if (count >= 1 && !newRow.hasClass("d-none"))
                        newRow = newRow.clone();
                    newRow.removeClass('d-none');
                    newRow.find('input').val('');

                    // Set new id to each elements inside.
                    let number;
                    newRow.find('*[id]').each(function() {
                        let id = $(this).attr("id");
                        let parts = id.split('-');
                        number = parseInt(parts[parts.length - 1]) + 1;
                        $(this).attr("id", parts[0] + '-' + parts[1] + '-' + number);
                    });

                    $('#table-battery-detail').append(newRow);

                    // Assign value to every columns.
                    $("#battery-name-" + number).val(battery.name);
                    $("#battery-priceretail-" + number).val(battery.price_retail);
                    $("#battery-id-" + number).val(battery.id);

                    // Format the retail price.
                    formatPrice($("#battery-priceretail-" + number));
                });

                $('#battery-modal').modal('hide');
            });
        });

        // Attach a click event handler to all delete row buttons.
        $(document).on("click", ".btn-delete-row", function() {
            // Get the count of rows.
            let count = $(".table-battery-detail-item").length;

            // Check if count of rows is one ore more.
            // If it's the only row, add d-non instaed of removing it.
            if (count > 1) {
                $(this).closest("tr").remove();
            } else {
                let row = $(this).closest("tr");
                row.addClass("d-none");
                row.find('input').val('');
            }
        });
    </script>

    {{-- JS Functions --}}
    <script>
        /**
         * Calculate the total price with tax, discount, and extra discount included.
         * 
         * @param {boolean} discountPrice - (Optional) Indicates if the discount price value has been changed..
         * @returns {number} The total price after applying tax, discount, and extra discount.
         */
        function calculatePriceDiscount(counter, discountPriceIsChanged = false) {
            let priceRetail = parseInt($('#battery-priceretail-' + counter).val().replace(/\D/g, ''));
            let discount = parseInt($('#battery-discount-' + counter).val().replace(/\D/g, ''));
            let priceNet = parseInt($('#battery-pricenet-' + counter).val().replace(/\D/g, ''));

            if (discountPriceIsChanged)
                $('#battery-pricenet-' + counter).val(priceRetail - (priceRetail * discount / 100));
            else
                $('#battery-discount-' + counter).val(Math.round((priceNet / priceRetail * 100)));

            formatPrice($(".battery-pricenet"));
        }
    </script>
@endsection
