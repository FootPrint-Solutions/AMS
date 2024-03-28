@extends('template.master')

@section('content')
    <link rel="stylesheet" href="{{ asset('plugins/bootstrap5-toggle/css/bootstrap5-toggle.min.css') }}">
    <style>
        #table-battery-detail th:nth-child(1),
        #table-battery-detail td:nth-child(1),
        #table-battery-detail th:nth-child(3),
        #table-battery-detail td:nth-child(3),
        #table-battery-detail th:nth-child(4),
        #table-battery-detail td:nth-child(4) {
            width: 30%;
        }

        #table-battery-detail th:nth-child(2),
        #table-battery-detail td:nth-child(2) {
            width: 10%;
        }
    </style>

    {{-- Form --}}
    <div class="card">
        <div class="card-body">
            {{-- Title --}}
            <div class="card-title h5">
                @if (isset($data['profile']))
                    Edit
                @else
                    Add New
                @endif
                Sales Order
            </div>
            <br>

            {{-- Form --}}
            <form id="quotation-form">
                @csrf

                {{-- Quotation Number & Date --}}
                <div class="row">
                    {{-- Quotation Number --}}
                    <div class="col">
                        <div class="form-group local-forms">
                            <label for="sales-order-number">Sales Order Number <span class="login-danger">*</span></label>
                            <input type="text" class="form-control" id="sales-order-number" name="salesordernumber"
                                placeholder="Enter distributor name" required readonly
                                @isset($data['profile'])
                            value="{{ $data['profile']['sales_order_number'] }}"
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
                            value="{{ $data['profile']['date'] }}"
                        @else
                            value="{{ date('Y-m-d') }}"
                        @endisset>
                        </div>
                    </div>
                </div>

                {{-- Customer, Distributor Shop & Technician --}}
                <div class="row">
                    {{-- Customer --}}
                    <div class="col">
                        <div class="row">
                            <div class="col">
                                <div class="form-group local-forms">
                                    <label for="customer">Customer <span class="login-danger">*</span></label>
                                    <select class="form-control" id="customer" name="customer" required>
                                        <option></option>
                                        @foreach ($data['customers'] as $customer)
                                            <option value="{{ $customer['id'] }}"
                                                @if (isset($data['profile']) && $data['profile']['customer_id'] == $customer['id']) selected @endif>
                                                {{ $customer['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-2">
                                <button type="button" class="btn btn-primary"><i class="fas fa-location-dot"></i></button>
                            </div>
                        </div>
                    </div>

                    {{-- Distributor Shop --}}
                    <div class="col">
                        <div class="form-group local-forms">
                            <label for="shop">Shop</label>
                            <select class="form-control" id="shop" name="shop">
                                <option></option>
                                @foreach ($data['shops'] as $shop)
                                    <option value="{{ $shop['id'] }}" @if (isset($data['profile']) && $data['profile']['distributor_shop_id'] == $shop['id']) selected @endif>
                                        {{ $shop['distributor']['name'] . ' - ' . $shop['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Technician --}}
                    <div class="col">
                        <div class="form-group local-forms">
                            <label for="technician">Technician</label>
                            <select class="form-control" id="technician" name="technician">
                                <option></option>
                                <option disabled>Select a distributor to select a technician</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Details --}}
                <table class="table mb-2" id="table-battery-detail">
                    {{-- Header --}}
                    <thead>
                        <tr>
                            <td colspan="4" class="h5 text-center">
                                Item @if (!isset($data['profile']))
                                    <button type="button" id="btn-add-row"
                                        class="btn btn-primary btn-sm rounded-circle mx-2"><i
                                            class="fas fa-plus"></i></button>
                                @endif
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
                            <tr class="table-battery-detail-row">
                                {{-- Production Code --}}
                                <td>
                                    <input type="text" class="form-control battery-code" id="battery-production-code"
                                        name="batteriescode[]" placeholder="Enter item production code"
                                        @isset($data['profile']['batteries'])value="{{ $battery['battery_production_code'] }}" @endisset>
                                </td>

                                {{-- Name --}}
                                <td colspan="2">
                                    @php
                                        $targets = ["battery-price-$counter"];
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
                                        ])
                                        @endcomponent
                                    @endisset
                                </td>

                                {{-- Price --}}
                                <td>
                                    <div class="row">
                                        <div class="col">
                                            <div class="input-group">
                                                <span class="input-group-text border-end">IDR</span>
                                                <input type="text"pattern="[0-9,]+"
                                                    class="form-control text-end battery-price"
                                                    id="battery-price-{{ $counter }}" name="batteriesprice[]"
                                                    placeholder="Enter item price" required
                                                    @isset($data['profile']['batteries']) readonly @endisset
                                                    @isset($data['profile']['batteries']) value="{{ $battery['battery_price'] }}" @endisset>
                                            </div>
                                        </div>

                                        <div class="col-sm-2">
                                            <button type="button" class="btn btn-danger btn-sm disabled btn-delete-row"
                                                title="Delete Item"><i class="fas fa-xmark"></i></button>
                                        </div>
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
                            <td colspan="2"></td>
                            <td class="text-end">Subtotal</td>
                            <td>
                                <div class="input-group">
                                    <span class="input-group-text border-end">IDR</span>
                                    <input type="text" pattern="[0-9.]+" class="form-control text-end" id="subtotal"
                                        name="subtotal"
                                        @isset($data['profile'])value="{{ $data['profile']['subtotal'] }}" @else value="0" @endisset
                                        readonly required>
                                </div>
                            </td>
                        </tr>

                        {{-- Tax --}}
                        <tr>
                            <td colspan="2"></td>
                            <td class="text-end">Tax</td>
                            <td>
                                <div class="row">
                                    <div class="col">
                                        {{-- Tax Percentage --}}
                                        <div class="input-group" id="tax-percentage">
                                            <input type="text" pattern="[0-9.]+" class="form-control text-end"
                                                id="tax" name="tax"
                                                @isset($data['profile'])value="{{ $data['profile']['tax'] }}" @else value="0" @endisset
                                                @isset($data['profile']['tax']) readonly @endisset required>
                                            <span class="input-group-text border-end">%</span>
                                        </div>

                                        {{-- Tax Price --}}
                                        <div class="input-group d-none" id="tax-price">
                                            <span class="input-group-text border-end">IDR</span>
                                            <input type="text" class="form-control text-end" id="tax-price-value"
                                                name="taxprice"
                                                @isset($data['profile'])value="{{ $data['profile']['tax_price'] }}" @else value="0" @endisset
                                                readonly required>
                                        </div>
                                    </div>

                                    <div class="col-sm-2">
                                        <input type="checkbox" id="toggle-tax" data-toggle="toggle" data-size="sm"
                                            data-offlabel="%" data-onlabel="IDR">
                                    </div>
                                </div>
                            </td>
                        </tr>

                        {{-- Discount --}}
                        <tr>
                            <td colspan="2"></td>
                            <td class="text-end">Discount</td>
                            <td>
                                <div class="row">
                                    <div class="col">
                                        {{-- Discount Percentage --}}
                                        <div class="input-group" id="discount-percentage">
                                            <input type="text" pattern="[0-9.]+" class="form-control text-end"
                                                id="discount" name="discount"
                                                @isset($data['profile'])value="{{ $data['profile']['discount'] }}" @else value="0" @endisset
                                                @isset($data['profile']['discount']) readonly @endisset required>
                                            <span class="input-group-text border-end">%</span>
                                        </div>

                                        {{-- Discount Price --}}
                                        <div class="input-group d-none" id="discount-price">
                                            <span class="input-group-text border-end">IDR</span>
                                            <input type="text" class="form-control text-end" id="discount-price-value"
                                                name="discountprice"
                                                @isset($data['profile'])value="{{ $data['profile']['discount_price'] }}" @else value="0" @endisset
                                                readonly required>
                                        </div>
                                    </div>

                                    <div class="col-sm-2">
                                        <input type="checkbox" id="toggle-discount" data-toggle="toggle" data-size="sm"
                                            data-offlabel="%" data-onlabel="IDR">
                                    </div>
                                </div>
                            </td>
                        </tr>

                        {{-- Extra Discount --}}
                        <tr>
                            <td colspan="2"></td>
                            <td class="text-end">Extra Discount</td>
                            <td>
                                <div class="row">
                                    <div class="col">
                                        {{-- Extra Discount Percentage --}}
                                        <div class="input-group" id="extra-discount-percentage">
                                            <input type="text" pattern="[0-9.]+" class="form-control text-end"
                                                id="extra-discount" name="extradiscount"
                                                @isset($data['profile'])value="{{ $data['profile']['extra_discount'] }}" @else value="0" @endisset
                                                @isset($data['profile']['extra_discount']) readonly @endisset required>
                                            <span class="input-group-text border-end">%</span>
                                        </div>

                                        {{-- Extra Discount Price --}}
                                        <div class="input-group d-none" id="extra-discount-price">
                                            <span class="input-group-text border-end">IDR</span>
                                            <input type="text" class="form-control text-end"
                                                id="extra-discount-price-value" name="extradiscountprice"
                                                @isset($data['profile'])value="{{ $data['profile']['extra_discount_price'] }}" @else value="0" @endisset
                                                readonly required>
                                        </div>
                                    </div>

                                    <div class="col-sm-2">
                                        <input type="checkbox" id="toggle-extra-discount" data-toggle="toggle"
                                            data-size="sm" data-offlabel="%" data-onlabel="IDR">
                                    </div>
                                </div>
                            </td>
                        </tr>

                        {{-- Total --}}
                        <tr>
                            <td colspan="2"></td>
                            <td class="text-end">Total</td>
                            <td>
                                <div class="input-group">
                                    <span class="input-group-text border-end">IDR</span>
                                    <input type="text" pattern="[0-9,]+" class="form-control text-end" id="total"
                                        name="total"
                                        @isset($data['profile'])value="{{ $data['profile']['total'] }}" @else value="0" @endisset
                                        required readonly>
                                </div>
                            </td>
                        </tr>

                        {{-- Payment Method & Status --}}
                        <tr>
                            <td colspan="2"></td>
                            <td class="text-end">Payment method</td>
                            <td>
                                <div class="row">
                                    <div class="col">
                                        <select name="paymentmethod" id="payment-method" class="form-control" required>
                                            <option value="cash" @if (isset($data['profile']) && $data['profile']['payment_method'] == 'cash') selected @endif>Cash
                                            </option>
                                            <option value="tokopedia" @if (isset($data['profile']) && $data['profile']['payment_method'] == 'tokopedia') selected @endif>
                                                Tokopedia</option>
                                            <option value="midtrans" @if (isset($data['profile']) && $data['profile']['payment_method'] == 'midtrans') selected @endif>
                                                Midtrans</option>
                                        </select>
                                    </div>

                                    <div class="col-5">
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

    {{-- Select2 Configurations --}}
    <script>
        $(document).ready(function() {
            $('#customer').select2({
                placeholder: "Enter customer"
            });

            $('#shop').select2({
                placeholder: "Enter distributor shop"
            });

            $('#payment-method').select2({});

            $('#status').select2({});

            $("#shop").on("select2:select", function(e) {
                // Obtain selected parent id.
                let parentId = e.params.data.id;

                // Get the list of menus inside the selected parent.
                $.ajax({
                    url: "/sales-order/technician/get/" + parentId,
                    method: "GET",
                    success: function(response) {
                        console.log(response);
                        // Clear current options and value.
                        $("#technician").empty().val(null).trigger("change");

                        let emptyOption = new Option("", "", false, false);
                        $("#technician").append(emptyOption).trigger("change");

                        response.forEach(function(menu) {
                            // Append new options.
                            let newOption = new Option(menu.name, menu.id, false,
                                false);
                            $("#technician").append(newOption).trigger("change");
                        });
                    }
                });
            });

            $('#technician').select2({
                placeholder: "Enter technician"
            });
        })
    </script>

    {{-- Form Handler --}}
    <script>
        let indexUrl = "/sales-order";

        $("#quotation-form").on("submit", function(event) {
            event.preventDefault();

            let mode = $("#btn-save").attr("value"); // update || create
            let url = (mode == "update") ? "/sales-order/update" : "/sales-order/store";

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

                // Clone the last row.
                let newRow = $('.table-battery-detail-row').last().clone();
                newRow.find('input').val('');
                newRow.find('.btn-delete-row').removeClass('disabled');

                // Set new id to each elements inside.
                let number;
                newRow.find('*[id]').each(function() {
                    let id = $(this).attr("id");
                    let parts = id.split('-');
                    number = parseInt(parts[parts.length - 1]) + 1;
                    $(this).attr("id", parts[0] + '-' + parts[1] + '-' + number);
                });

                var targets = JSON.stringify(["battery-price-" + number]);
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

            $('#toggle-extra-discount').on("change", function() {
                if ($(this).prop('checked')) {
                    $("#extra-discount-price").removeClass("d-none");
                    $("#extra-discount-percentage").addClass("d-none");
                } else {
                    $("#extra-discount-price").addClass("d-none");
                    $("#extra-discount-percentage").removeClass("d-none");
                }
            });

            $("#tax, #discount, #extra-discount").on("change", function() {
                // Validate input value.
                let value = parseInt($(this).val(), 10);
                if (isNaN(value)) {
                    $(this).val("0");
                }

                // Recalculate total value.
                calculateTotal();
            });
        });

        // Attach an input event handler for each battery quantity and price fields.
        $(document).on("change", ".battery-price", function() {
            alert("Heh");
            // Set total price value.
            calculateTotal();
        });
    </script>

    {{-- JS functions --}}
    <script>
        /**
         * Calculate the total price with tax, discount, and extra discount included.
         * 
         * @returns {number} The total price after applying tax, discount, and extra discount.
         */
        function calculateTotal() {
            // Calculate subtotal based on each items' total price.
            let subtotal = 0;
            $(".battery-price").each(function() {
                let value = parseInt($(this).val());
                if (!isNaN(value)) {
                    subtotal += value;
                }
            });
            $("#subtotal").val(subtotal);

            // Obtain tax, discount and extra discount value (in percentage).
            let tax = subtotal * parseFloat($("#tax").val()) / 100;
            $("#tax-price-value").val(tax);
            let discount = subtotal * parseFloat($("#discount").val()) / 100;
            $("#discount-price-value").val(discount);
            let extraDiscount = subtotal * parseFloat($("#extra-discount").val()) / 100;
            console.log(extraDiscount);
            $("#extra-discount-price-value").val(extraDiscount);
            let total = (subtotal - discount - extraDiscount) + tax;
            $("#total").val(total);
            return total;
        }
    </script>
@endsection
