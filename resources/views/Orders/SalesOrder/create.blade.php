@extends('template.master')

@section('content')
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
                            <label for="quotation-number">Sales Order Number <span class="login-danger">*</span></label>
                            <input type="text" class="form-control" id="quotation-number" name="quotationnumber"
                                placeholder="Enter distributor name" required readonly
                                @isset($data['profile'])
                            value="{{ $data['profile']['quotation_number'] }}"
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
                @if (!isset($data['profile']['batteries']))
                    <table class="table mb-2" id="table-battery-detail">
                        {{-- Header --}}
                        <thead>
                            <tr>
                                <td colspan="4" class="h5 text-center">
                                    Item <button type="button" id="btn-add-row"
                                        class="btn btn-primary btn-sm rounded-circle mx-2"><i
                                            class="fas fa-plus"></i></button>
                                </td>
                            </tr>
                        </thead>

                        {{-- Body (Items) --}}
                        <tbody>
                            <tr class="table-battery-detail-row">
                                {{-- Name --}}
                                <td>
                                    @php
                                        $targets = ['battery-price-1', 'battery-qty-1'];
                                        $encodedTargets = json_encode($targets);
                                    @endphp

                                    @component('components.autocomplete', [
                                        'id' => 'battery-name-1',
                                        'class' => 'battery-name',
                                        'value' => isset($data['profile']['batteries']) ? $item['battery_name'] : '',
                                        'name' => 'batteriesname[]',
                                        'nameHiddenId' => 'batteriesid[]',
                                        'url' => '/battery/get/',
                                        'placeholder' => 'Enter item name',
                                        'targets' => $encodedTargets,
                                    ])
                                    @endcomponent
                                </td>

                                {{-- Quantity --}}
                                <td><input type="number" class="form-control battery-qty" id="battery-qty-1"
                                        name="batteriesqty[]" min="0" value=0 placeholder="Enter item quantity">
                                </td>

                                {{-- Price --}}
                                <td>
                                    <div class="input-group">
                                        <span class="input-group-text border-end">IDR</span>
                                        <input type="text"pattern="[0-9,]+" class="form-control text-end battery-price"
                                            id="battery-price-1" name="batteriesprice[]" placeholder="Enter item price">
                                    </div>
                                </td>

                                {{-- Total --}}
                                <td>
                                    <div class="row">
                                        <div class="col">
                                            <div class="col">
                                                <div class="input-group">
                                                    <span class="input-group-text border-end">IDR</span>
                                                    <input type="text"pattern="[0-9,]+"
                                                        class="form-control text-end battery-total" id="battery-total-1"
                                                        value="0" readonly>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-2">
                                            <button type="button" class="btn btn-danger btn-sm disabled btn-delete-row"><i
                                                    class="fas fa-xmark"></i></button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>

                        {{-- Footer (Tax, Discount, Total) --}}
                        <tfoot>
                            {{-- Tax --}}
                            <tr>
                                <td colspan="2"></td>
                                <td class="text-end">Tax</td>
                                <td>
                                    <div class="input-group">
                                        <input type="text" pattern="[0-9]+" class="form-control text-end" id="tax"
                                            name="tax" value="0" required>
                                        <span class="input-group-text border-end">%</span>
                                    </div>
                                </td>
                            </tr>

                            {{-- Discount --}}
                            <tr>
                                <td colspan="2"></td>
                                <td class="text-end">Discount</td>
                                <td>
                                    <div class="input-group">
                                        <input type="text" pattern="[0-9]+" class="form-control text-end"
                                            id="discount" name="discount" value="0" required>
                                        <span class="input-group-text border-end">%</span>
                                    </div>
                                </td>
                            </tr>

                            {{-- Extra Discount --}}
                            <tr>
                                <td colspan="2"></td>
                                <td class="text-end">Extra Discount</td>
                                <td>
                                    <div class="input-group">
                                        <input type="text" pattern="[0-9]+" class="form-control text-end"
                                            id="extra-discount" name="extradiscount" value="0" required>
                                        <span class="input-group-text border-end">%</span>
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
                                        <input type="text" pattern="[0-9,]+" class="form-control text-end"
                                            id="total" name="total" value="0" required readonly>
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
                                            <select name="paymentmethod" id="payment-method" class="form-control"
                                                required>
                                                <option value="cash" selected>Cash</option>
                                                <option value="tokopedia">Tokopedia</option>
                                                <option value="midtrans">Midtrans</option>
                                            </select>
                                        </div>

                                        <div class="col-5">
                                            <select name="status" id="status" class="form-control" required>
                                                <option value="paid" class="text-success">Paid</option>
                                                <option value="pending">Pending</option>
                                                <option value="failed">Failed</option>
                                            </select>
                                        </div>
                                    </div>

                                </td>
                            </tr>
                        </tfoot>
                    </table>
                @endif
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
                newRow.find('.battery-qty').val('0');
                newRow.find('.battery-total').val('0');
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
    <script>
        $(document).ready(function() {
            $("#tax, #discount, #extra-discount").on("change", function() {
                // Validate input value.
                let value = parseInt($(this).val(), 10);
                if (isNaN(value)) {
                    $(this).val("0");
                }

                // Recalculate total value.
                $("#total").val(calculateTotal());
            });
        });

        // Attach an input event handler for each battery quantity and price fields.
        $(document).on("change", ".battery-qty, .battery-price", function() {
            // Get a total price for an item.
            let quantity = $(this).hasClass("battery-qty") ? parseInt($(this).val()) : parseInt($(this).closest(
                "tr").find(".battery-qty").val());
            let price = $(this).hasClass("battery-price") ? parseInt($(this).val()) : parseInt($(this).closest("tr")
                .find(".battery-price").val());
            let total = 0;
            if (!isNaN(quantity) && !isNaN(price)) {
                total = quantity * price;
            }
            $(this).closest("tr").find(".battery-total").val(total);

            // Set total price value.
            $("#total").val(calculateTotal());
        });
    </script>

    {{--  --}}
    <script>
        /**
         * Calculate the total price with tax, discount, and extra discount included.
         * 
         * @returns {number} The total price after applying tax, discount, and extra discount.
         */
        function calculateTotal() {
            // Calculate subtotal based on each items' total price.
            let subtotal = 0;
            $(".battery-total").each(function() {
                let value = parseInt($(this).val());
                if (!isNaN(value)) {
                    subtotal += value;
                }
            });

            // Obtain tax, discount and extra discount value (in percentage).
            let tax = subtotal * parseFloat($("#tax").val()) / 100;
            let discount = subtotal * parseFloat($("#discount").val()) / 100;
            let extraDiscount = subtotal * parseFloat($("#extra-discount").val()) / 100;

            // Calculate total price and set value to total price.
            return (subtotal - discount - extraDiscount) + tax;
        }
    </script>
@endsection
