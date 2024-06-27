@extends('template.master')

@section('content')
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
                        <div class="row">
                            <div class="col">
                                <div class="form-group local-forms">
                                    <label for="period-end">Period End</label>
                                    <input type="date" class="form-control" id="period-end" name="periodend"
                                        value=@isset($data['profile']) {{ $data['profile']['period_end'] }} @else {{ date('Y-m-d') }} @endisset>
                                </div>
                            </div>

                            {{-- Unlimited Period --}}
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <input type="checkbox" class="form-check-input" id="unlimited-period">
                                    <label class="form-check-label" for="unlimited-period"> Unlimited</label><br>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Discount & Net Price Generator --}}
                <div class="row">
                    <div class="row">
                        <div class="col">
                            <div class="input-group">
                                <input type="text" class="form-control" id="battery-discount-all">
                                <span class="input-group-text border-end">%</span>
                            </div>
                        </div>

                        <div class="col-sm-3">
                            <button type="button" class="btn btn-info btn-block" id="btn-apply-discount">Apply
                                Discount to
                                All</button>
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

                        <tr class="text-center">
                            <td class="p-1 text-muted small">Name</td>
                            <td class="p-1 text-muted small">Price Retail</td>
                            <td class="p-1 text-muted small">Discount</td>
                            <td class="p-1 text-muted small">Price Net</td>
                        </tr>
                    </thead>

                    {{-- Body (Items) --}}
                    <tbody>
                        @php
                            $batteries = isset($data['profile']['batteries']) ? $data['profile']['batteries'] : [''];
                            $counter = 1;
                        @endphp

                        @foreach ($batteries as $battery)
                            <tr class="table-battery-detail-row @if (!isset($data['profile']['batteries'])) d-none @endif">
                                {{-- Name --}}
                                <td>
                                    <input type="text" class="form-control battery-name"
                                        id="battery-name-{{ $counter }}" name="batteriesname[]"
                                        placeholder="Enter battery name"
                                        @isset($data['profile']['batteries']) value="{{ $battery['name'] }}" @endisset>
                                </td>

                                {{-- Retail Price --}}
                                <td>
                                    <div class="input-group">
                                        <span class="input-group-text border-end">IDR</span>
                                        <input type="text" class="form-control text-end battery-priceretail"
                                            id="battery-priceretail-{{ $counter }}" name="batteriespriceretail[]"
                                            placeholder="Enter item retail price" readonly
                                            @isset($data['profile']['batteries']) value="{{ $battery['price_retail'] }}" @endisset>
                                    </div>
                                </td>

                                {{-- Discount --}}
                                <td>
                                    {{-- Discount Percentage --}}
                                    <div class="input-group">
                                        <input type="text" class="form-control battery-discount"
                                            id="battery-discount-{{ $counter }}" name="batteriesdisc[]"
                                            placeholder="Enter battery discount"
                                            @isset($data['profile']['batteries']) value="{{ $battery['discount'] }}" @endisset>
                                        <span class="input-group-text border-end">%</span>
                                    </div>

                                    <input type="hidden" class="form-control text-end battery-discountprice"
                                        id="battery-discountprice-{{ $counter }}" name="batteriesdiscprice[]">
                                </td>

                                {{-- Net Price & Delete --}}
                                <td>
                                    {{-- Net Price --}}
                                    <div class="row">
                                        <div class="col">
                                            {{-- Discount Price --}}
                                            <div class="input-group">
                                                <span class="input-group-text border-end">IDR</span>
                                                <input type="text" class="form-control text-end battery-pricenet"
                                                    id="battery-pricenet-{{ $counter }}" name="batteriespricenet[]"
                                                    required readonly
                                                    @isset($data['profile']['batteries']) value="{{ $battery['price_net'] }}" @endisset>
                                            </div>
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
                                    @isset($data['profile']['batteries']) value="{{ $battery['battery_id'] }}" @endisset>
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
                    {{-- Filter --}}
                    <div class="row">
                        {{-- Size Category --}}
                        <div class="col">
                            <div class="form-group local-forms">
                                <label for="battery-size">Size Category</label>
                                <select class="form-control" style="width: 100%" id="battery-size">
                                    <option></option>
                                    @foreach ($data['battery_categories'] as $size)
                                        <option value="{{ $size['id'] }}">
                                            {{ $size['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Name --}}
                        <div class="col">
                            <div class="form-group local-forms">
                                <label for="battery-name">Name</label>
                                <input type="text" class="form-control" id="battery-name" name="battery-name"
                                    placeholder="Enter battery name">
                            </div>
                        </div>

                        {{-- Select All --}}
                        <div class="col-sm-2">
                            <button type="button" class="btn btn-info" id="btn-all-modal">Select all</button>
                        </div>
                    </div>

                    {{-- Battery List --}}
                    <div id="list-battery-none">No batteries found</div>
                    <ul class="list-group list-group-flush d-none" id="list-battery"></ul>
                </div>

                {{-- Footer --}}
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="btn-add-modal">Add batteries</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        var selectedBatteries = []; /* List of selected batteries in modal */
        var shownBatteries = []; /* List of shown batteries (not yet selected) in modal */

        $(document).ready(function() {
            formatPrice($(".battery-priceretail"));
            formatPrice($(".battery-pricenet"));
        });
    </script>

    {{-- Select2 Configurations --}}
    <script>
        $(document).ready(function() {
            $('#battery-size').select2({
                placeholder: "Enter battery size category"
            });

            $("#battery-size").on("select2:select", function(e) {
                // Get selected size category.
                let sizeId = e.params.data.id;
                let name = $("#battery-name").val();

                // Show the list of batteries of the selected size category.
                searchAndShowBattery(sizeId, name);
            });
        });
    </script>

    {{-- Form Hanlder --}}
    <script>
        let indexUrl = "/promo";

        $(document).ready(function() {
            $("#promo-form").on("submit", function(event) {
                event.preventDefault();

                let batteryLength = $('.battery-name:visible').length;
                if (batteryLength < 1) {
                    Swal.fire({
                        title: "Cannot Save Promo",
                        text: "A promo must have at least one item.",
                        icon: "warning"
                    });
                    return;
                }

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
            $("#battery-size").val(null).trigger('change');
            $("#battery-name").val('');
            $('#list-battery').empty();
        });
    </script>

    {{-- Change Event Handler --}}
    <script>
        $(document).ready(function() {
            $("#unlimited-period").on("change", function() {
                if ($(this).is(':checked')) {
                    $("#period-end").val('');
                } else {
                    let date = new Date();
                    let day = ("0" + date.getDate()).slice(-2);
                    let month = ("0" + (date.getMonth() + 1)).slice(-2);
                    $("#period-end").val(date.getFullYear() + "-" + (month) + "-" + (day));
                }
            });

            $("#period-end").on("change", function() {
                $("#unlimited-period").prop('checked', false);
            });
        });

        $(document).on("change keyup", ".battery-discount", function() {
            // Validate input value.
            let value = parseInt($(this).val(), 10);
            if (isNaN(value)) {
                $(this).val("0");
            }

            let id = $(this).attr("id");
            let parts = id.split('-');
            let counter = parts[parts.length - 1];

            // Recalculate total value.
            calculatePriceDiscount(counter);
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
                    calculatePriceDiscount(counter);
                });
            });

            $("#btn-all-modal").on('click', function() {
                $(".list-group-item").each(function() {
                    $(this).addClass("active");
                    selectedBatteries = shownBatteries;
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
            let count = $(".table-battery-detail-row").length;

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

    {{-- Keyup Event Handler --}}
    <script>
        $(document).ready(function() {
            $("#battery-name").on("change", function() {
                // 
                let name = $(this).val();
                let sizeId = $("#battery-size").val();

                // Show the list of batteries of the selected size category.
                searchAndShowBattery(sizeId, name);
            });
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
        function calculatePriceDiscount(counter) {
            let priceRetail = parseInt($('#battery-priceretail-' + counter).val().replace(/\D/g, ''));
            let discount = parseInt($('#battery-discount-' + counter).val());
            let discountPrice = priceRetail * discount / 100;

            $("#battery-discountprice-" + counter).val(discountPrice);
            $('#battery-pricenet-' + counter).val(priceRetail - discountPrice);

            formatPrice($(".battery-pricenet"));
        }

        /**
         * Search and show the list of batteries based on size category and name.
         * 
         * @param {number} sizeCategoryId - The id of selected size category.
         * @param {string} name - The keyword of battery name.
         */
        function searchAndShowBattery(sizeCategoryId, name) {
            // Empty all current items in list.
            $('#list-battery').empty();

            $.ajax({
                url: "/battery/get/size",
                method: "post",
                data: {
                    "_token": "{{ csrf_token() }}",
                    sizeId: sizeCategoryId,
                    name: name
                },
                success: function(data) {
                    selectedBatteries = [];
                    shownBatteries = [];

                    // If there is at least one battery to display, show the list group.
                    // Otherwise, show the 'No batteries found' container.
                    if (data.length > 0) {
                        $("#list-battery").removeClass("d-none");
                        $("#list-battery-none").addClass("d-none");

                        data.forEach(battery => {
                            // Make the list item for battery.
                            let item = document.createElement('li');
                            item.className = 'list-group-item list-group-item-action';
                            item.innerHTML = battery.name;

                            item.onclick = function() {
                                if ($(this).hasClass("active")) {
                                    $(this).removeClass("active");

                                    // Remove battery from selected battery.
                                    const index = selectedBatteries.indexOf(
                                        battery);
                                    if (index > -1)
                                        selectedBatteries.splice(index, 1);
                                } else {
                                    $(this).addClass("active");

                                    // Append battery to selected battery.
                                    selectedBatteries.push(battery);
                                }
                            };

                            // Append the created list item into list.
                            $("#list-battery").append(item);

                            // Push battery info to shownBatteries.
                            shownBatteries.push(battery);
                        });
                    } else {
                        $("#list-battery").addClass("d-none");
                        $("#list-battery-none").removeClass("d-none");
                    }
                }
            });
        }
    </script>
@endsection
