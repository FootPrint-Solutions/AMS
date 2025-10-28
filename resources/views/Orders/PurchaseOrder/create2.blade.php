@extends('template.master')

@section('content')
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
            <form id="purchase-order-form">
                @csrf

                {{-- Purchase Order Number & Date --}}
                <div class="row">
                    {{-- Purchase Order Number --}}
                    <div class="col">
                        <div class="form-group local-forms">
                            <label for="purchase-order-number">Purchase Order Number <span
                                    class="login-danger">*</span></label>
                            <input type="text" class="form-control" id="purchase-order-number"
                                name="purchase_order_number" placeholder="Enter purchase order number"
                                value="{{ isset($data['profile']) ? $data['profile']['purchase_order_number'] : $purchaseOrderNumber }}"
                                {{ isset($data['profile']) ? 'readonly' : '' }} required>
                        </div>
                    </div>

                    {{-- Date --}}
                    <div class="col">
                        <div class="form-group local-forms">
                            <label for="date">Date <span class="login-danger">*</span></label>
                            <input type="date" class="form-control" id="date" name="date"
                                value="{{ isset($data['profile']) ? $data['profile']['date'] : date('Y-m-d') }}" required>
                        </div>
                    </div>

                    {{-- Supplier --}}
                    <div class="col">
                        <div class="form-group local-forms">
                            <label for="supplier">Supplier <span class="login-danger">*</span></label>
                            <select class="form-control" id="supplier" name="supplier_id" required>
                                <option value="">Select Supplier</option>
                                @foreach ($data['suppliers'] as $supplier)
                                <option value="{{ $supplier['id'] }}"
                                    data-address="{{ $supplier['address'] }}"
                                    data-contact="{{ $supplier['contact'] }}"
                                    data-email="{{ $supplier['email'] }}"
                                    {{ isset($data['profile']) && $data['profile']['supplier_id'] == $supplier['id'] ? 'selected' : '' }}>
                                    {{ $supplier['name'] }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Invoice Number --}}
                    <div class="col">
                        <div class="form-group local-forms">
                            <label for="invoice-number">Invoice Number</label>
                            <input type="text" class="form-control" id="invoice-number" name="invoice_number"
                                placeholder="Enter invoice number"
                                value="{{ isset($data['profile']) ? $data['profile']['invoice_number'] : '' }}">
                        </div>
                    </div>
                </div>

                {{-- Supplier Address --}}
                <div class="row">
                    <div class="col">
                        <div class="form-group local-forms">
                            <label for="supplier-address">Supplier Address</label>
                            <textarea class="form-control" id="supplier-address" name="address" rows="3" readonly
                                placeholder="Supplier address will appear here">{{ isset($data['profile']) ? $data['profile']['address'] : '' }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Payment Status --}}
                <div class="row">
                    <div class="col-3">
                        <div class="form-group local-forms">
                            <label for="payment-status">Payment Status <span class="login-danger">*</span></label>
                            <select class="form-control" id="payment-status" name="payment_status" required>
                                <option value="">Select Payment Status</option>
                                <option value="pending" {{ isset($data['profile']) && $data['profile']['payment_status'] == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="paid" {{ isset($data['profile']) && $data['profile']['payment_status'] == 'paid' ? 'selected' : '' }}>Paid</option>
                                <option value="partial" {{ isset($data['profile']) && $data['profile']['payment_status'] == 'partial' ? 'selected' : '' }}>Partial</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group local-forms">
                            <label for="status">Status <span class="login-danger">*</span></label>
                            <select class="form-control" id="status" name="status" required>
                                <option value="draft" {{ isset($data['profile']) && $data['profile']['status'] == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="posted" {{ isset($data['profile']) && $data['profile']['status'] == 'posted' ? 'selected' : '' }}>Posted</option>
                                <option value="completed" {{ isset($data['profile']) && $data['profile']['status'] == 'completed' ? 'selected' : '' }}>Completed</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Details --}}
                <table class="table mb-2" id="table-battery-detail">
                    {{-- Header --}}
                    <thead>
                        <tr>
                            <th colspan="7" class="text-center">
                                <button type="button" class="btn btn-sm btn-success" id="btn-add-row">
                                    <i class="fas fa-plus"></i> Add Battery
                                </button>
                            </th>
                        </tr>

                        <tr class="text-center">
                            <th style="width: 20%;">Battery</th>
                            <th style="width: 12%;">Retail Price</th>
                            <th style="width: 8%;">Tax (%)</th>
                            <th style="width: 12%;">Price After Tax</th>
                            <th style="width: 12%;">Discount Price</th>
                            <th style="width: 12%;">Net Price</th>
                            <th style="width: 8%;">Quantity</th>
                            <th style="width: 8%;">Production Code</th>
                            <th style="width: 8%;">Action</th>
                        </tr>
                    </thead>

                    {{-- Body (Items) --}}
                    <tbody>
                        @php
                        $batteries = isset($data['profile']['batteries']) ? $data['profile']['batteries'] : [[]];
                        $counter = 1;
                        @endphp

                        @foreach ($batteries as $battery)
                        <tr class="table-battery-detail-row">
                            <td>
                                <select class="form-control autocomplete battery-select"
                                    id="battery-select-{{ $counter }}"
                                    name="battery_id[]"
                                    data-targets='["battery-priceretail-{{ $counter }}", "battery-type-{{ $counter }}"]'
                                    required>
                                    <option value="">Select Battery</option>
                                    @foreach ($data['batteries'] as $batteryOption)
                                    <option value="{{ $batteryOption['id'] }}"
                                        data-price="{{ $batteryOption['price_retail'] }}"
                                        data-type="{{ $batteryOption['type'] }}"
                                        {{ isset($battery['battery_id']) && $battery['battery_id'] == $batteryOption['id'] ? 'selected' : '' }}>
                                        {{ $batteryOption['name'] }}
                                    </option>
                                    @endforeach
                                </select>
                            </td>

                            <td>
                                <input type="text" class="form-control text-end battery-priceretail"
                                    id="battery-priceretail-{{ $counter }}"
                                    name="battery_price_retail[]"
                                    value="{{ isset($battery['battery_price_retail']) ? number_format($battery['battery_price_retail'], 0, ',', '.') : '0' }}"
                                    required>
                            </td>

                            <td>
                                <input type="number" class="form-control text-center battery-tax"
                                    id="battery-tax-{{ $counter }}"
                                    name="battery_tax[]"
                                    value="{{ isset($battery['tax']) ? $battery['tax'] : '11' }}"
                                    min="0" max="100" step="0.01">
                            </td>

                            <td>
                                <input type="text" class="form-control text-end battery-priceaftertax"
                                    id="battery-priceaftertax-{{ $counter }}"
                                    value="{{ isset($battery['battery_price_retail']) ? number_format($battery['battery_price_retail'] + $battery['tax_price'], 0, ',', '.') : '0' }}"
                                    readonly>
                            </td>

                            <td>
                                <input type="text" class="form-control text-end battery-discountprice"
                                    id="battery-discountprice-{{ $counter }}"
                                    name="battery_discount_price[]"
                                    value="{{ isset($battery['discount_price']) ? number_format($battery['discount_price'], 0, ',', '.') : '0' }}">
                            </td>

                            <td>
                                <input type="text" class="form-control text-end battery-price"
                                    id="battery-price-{{ $counter }}"
                                    name="battery_net_price[]"
                                    value="{{ isset($battery['price_net']) ? number_format($battery['price_net'], 0, ',', '.') : '0' }}"
                                    readonly>
                            </td>

                            <td>
                                <input type="number" class="form-control text-center battery-quantity"
                                    id="battery-quantity-{{ $counter }}"
                                    name="battery_quantity[]"
                                    value="{{ isset($battery['quantity']) ? $battery['quantity'] : '1' }}"
                                    min="1" step="1" required>
                            </td>

                            <td>
                                <input type="text" class="form-control text-center"
                                    id="battery-production-code-{{ $counter }}"
                                    name="battery_production_code[]"
                                    value="{{ isset($battery['battery_production_code']) ? $battery['battery_production_code'] : '' }}"
                                    placeholder="Optional">
                                <input type="hidden" class="battery-type"
                                    id="battery-type-{{ $counter }}"
                                    value="{{ isset($battery['type']) ? $battery['type'] : 'regular' }}">
                            </td>

                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-danger btn-delete-row">
                                    <i class="fas fa-trash"></i>
                                </button>
                                {{-- Hidden Inputs --}}
                                @isset($data['profile']['batteries'])
                                <input type="hidden" name="detailid[]" value="{{ $battery['id'] }}">
                                @endisset
                            </td>
                        </tr>

                        @php
                        $counter++;
                        @endphp
                        @endforeach
                    </tbody>

                    {{-- Footer (Discount, Total) --}}
                    <tfoot>
                        {{-- Subtotal --}}
                        <tr>
                            <td colspan="7"></td>
                            <td class="text-end">Subtotal</td>
                            <td>
                                <div class="input-group">
                                    <span class="input-group-text border-end">IDR</span>
                                    <input type="text" class="form-control text-end" id="subtotal" name="subtotal"
                                        value="{{ isset($data['profile']) ? number_format($data['profile']['subtotal'], 0, ',', '.') : '0' }}"
                                        readonly>
                                </div>
                            </td>
                        </tr>

                        {{-- Discount --}}
                        <tr>
                            <td colspan="7"></td>
                            <td class="text-end">Discount</td>
                            <td>
                                <div class="input-group">
                                    <span class="input-group-text border-end">IDR</span>
                                    <input type="text" class="form-control text-end" id="discount-price-value"
                                        name="discount_price"
                                        value="{{ isset($data['profile']) ? number_format($data['profile']['discount_price'], 0, ',', '.') : '0' }}">
                                </div>
                            </td>
                        </tr>

                        {{-- Total --}}
                        <tr>
                            <td colspan="7"></td>
                            <td class="text-end">Total</td>
                            <td>
                                <div class="input-group">
                                    <span class="input-group-text border-end">IDR</span>
                                    <input type="text" class="form-control text-end" id="total" name="total"
                                        value="{{ isset($data['profile']) ? number_format($data['profile']['total'], 0, ',', '.') : '0' }}"
                                        readonly>
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
                        Purchase Order </button>

                    {{-- Cancel Button --}}
                    <button type="reset" class="btn btn-danger mx-1" id="btn-cancel">Cancel</button>
                </div>
            </form>
        </div>
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

{{-- Select2 Configurations --}}
<script>
    $(document).ready(function() {
        $('#supplier').select2({
            placeholder: "Select supplier"
        });

        $('#payment-status').select2({
            placeholder: "Select payment status"
        });

        $('#status').select2({});

        $("#supplier").on("select2:select", function(e) {
            const selectedOption = $(this).find('option:selected');
            const address = selectedOption.data('address');
            $('#supplier-address').val(address);
        });

        // Initialize battery selects
        $('.battery-select').each(function() {
            $(this).select2({
                placeholder: "Select battery"
            });
        });
    });
</script>

{{-- Form Handler --}}
<script>
    let indexUrl = "/purchase-order";

    $("#purchase-order-form").on("submit", function(event) {
        event.preventDefault();

        let mode = $("#btn-save").attr("value"); // update || create
        let url = (mode == "update") ? "/purchase-order/update" : "/purchase-order/store";

        calculateTotal();

        // Obtain submitted form data.
        let formData = new FormData($(this)[0]);

        // Send submit POST request via AJAX.
        sendSubmitRequest(url, formData, function() {
            // Redirect to index page.
            goToPage(indexUrl);
        });
    });

    $("#purchase-order-form").on("reset", function() {
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
            newRow.find('select').val('').trigger('change');
            newRow.find('.btn-delete-row').removeClass('disabled');

            // Set new id to each elements inside.
            let number;
            newRow.find('*[id]').each(function() {
                let id = $(this).attr("id");
                let parts = id.split('-');
                number = parseInt(parts[parts.length - 1]) + 1;
                let newId = parts.slice(0, -1).join('-') + '-' + number;
                $(this).attr("id", newId);
            });

            // Update name attributes
            newRow.find('*[name]').each(function() {
                let name = $(this).attr("name");
                if (name && name.includes('[]')) {
                    // Keep array notation as is
                }
            });

            var targets = JSON.stringify(["battery-priceretail-" + number,
                "battery-type-" + number,
            ]);
            newRow.find(".autocomplete").attr("data-targets", targets);

            $('#table-battery-detail tbody').append(newRow);

            // Initialize select2 for the new row
            newRow.find('.battery-select').select2({
                placeholder: "Select battery"
            });
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
            calculateTotal();
        }
    });
</script>

{{-- Change Event Handler --}}
<script>
    $(document).on("change", ".battery-select", function() {
        const selectedOption = $(this).find('option:selected');
        const price = selectedOption.data('price') || 0;
        const type = selectedOption.data('type') || 'regular';

        const row = $(this).closest('tr');
        row.find('.battery-priceretail').val(formatNumber(price));
        row.find('.battery-type').val(type);

        calculateRowTotal(row);
        calculateTotal();
    });

    $(document).on("change keyup", ".battery-discountprice, .battery-tax, #discount-price-value", function() {
        // Validate input value.
        let value = parseInt($(this).val(), 10);
        if (isNaN(value)) {
            $(this).val("0");
        }

        if ($(this).hasClass('battery-discountprice') || $(this).hasClass('battery-tax')) {
            calculateRowTotal($(this).closest('tr'));
        }

        // Recalculate total value.
        calculateTotal();
    });

    $(document).on("keyup", ".battery-priceretail", function() {
        formatPrice($(this));
        calculateRowTotal($(this).closest('tr'));
        calculateTotal();
    });

    function calculateRowTotal(row) {
        const priceRetail = parseInt(row.find(".battery-priceretail").val().replace(/\D/g, '')) || 0;
        const tax = parseFloat(row.find(".battery-tax").val()) || 0;
        const type = row.find(".battery-type").val();

        // Calculate tax price
        const taxPrice = priceRetail * tax / 100;
        row.find(".battery-taxprice").val(taxPrice);

        // Calculate price after tax
        const priceAfterTax = Math.round(priceRetail + taxPrice);
        row.find(".battery-priceaftertax").val(formatNumber(priceAfterTax));

        // Calculate discount
        const discountPrice = parseInt(row.find(".battery-discountprice").val().replace(/\D/g, '')) || 0;

        // Calculate net price
        const netPrice = priceAfterTax - discountPrice;
        row.find(".battery-price").val(formatNumber(netPrice));

        // Format displayed prices
        formatPrice(row.find(".battery-priceaftertax"));
        formatPrice(row.find(".battery-price"));

        // Apply styling based on type
        if (type == 'recycle') {
            row.addClass('bg-danger');
        } else {
            row.removeClass('bg-danger');
        }
    }
</script>

{{-- JS functions --}}
<script>
    /**
     * Calculate the total price with discount included.
     * 
     * @returns {number} The total price after applying discount.
     */
    function calculateTotal() {
        // Calculate subtotal based on each items' price.
        let subtotal = 0;
        $(".battery-price").each(function() {
            let row = $(this).closest('tr');
            let type = row.find(".battery-type").val();
            let value = parseInt($(this).val().replace(/\D/g, '')) || 0;
            let quantity = parseInt(row.find(".battery-quantity").val()) || 1;

            if (type != 'regular') {
                subtotal -= (value * quantity);
            } else {
                subtotal += (value * quantity);
            }
        });

        $("#subtotal").val(formatNumber(subtotal));

        // Calculate total value.
        let discount = parseInt($("#discount-price-value").val().replace(/\D/g, '')) || 0;
        let total = subtotal - discount;
        $("#total").val(formatNumber(total));

        // Format all price fields value.
        formatPrice($("#subtotal"));
        formatPrice($("#total"));
        formatPrice($("#discount-price-value"));

        return total;
    }

    function formatNumber(num) {
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }
</script>
@endsection