@extends('template.master')

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
        <div class="card shadow-lg">
            <div class="card-body">
                {{-- Title --}}
                <div class="card-title h5">
                    @if (isset($data['profile']))
                        Edit
                    @else
                        Add New
                    @endif
                    Sales Order Recycle
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
                                <label for="sales-order-number">Sales Order Number <span
                                        class="login-danger">*</span></label>
                                <input type="text" class="form-control" id="sales-order-number" name="salesordernumber"
                                    placeholder="Enter distributor name" required
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

                        <input type="hidden" name="Latitude" id="Latitude"
                            value="@if (isset($data['profile'])) {{ ltrim($data['profile']['latitude']) }} @endif"
                            required>
                        <input type="hidden" name="Longitude" id="Longitude"
                            value="@if (isset($data['profile'])) {{ ltrim($data['profile']['longitude']) }} @endif"
                            required>
                    </div>

                    {{-- Customer, Distributor Shop & Technician --}}
                    <div class="row">
                        <div class="col">
                            <div class="form-group local-forms">
                                <label for="vendor">Vendor <span class="login-danger">*</span></label>
                                <select class="form-control" id="vendor" name="vendor" required>
                                    <option></option>
                                    @foreach ($data['DistributorShop'] as $distributorShop)
                                        <option value="{{ $distributorShop['id'] }}"
                                            @if (isset($data['profile']) && $data['profile']['vendor'] == $distributorShop['id']) selected @endif>
                                            {{ $distributorShop['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Distributor Shop --}}
                        <div class="col">
                            <div class="form-group local-forms">
                                <label for="ship_to">Ship To <span class="login-danger">*</span></label>
                                <select class="form-control" id="ship_to" name="ship_to" required>
                                    <option></option>
                                    @foreach ($data['Distributor'] as $distributor)
                                        <option value="{{ $distributor['id'] }}"
                                            @if (isset($data['profile']) && $data['profile']['ship_to'] == $distributor['id']) selected @endif>
                                            {{ $distributor['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Details --}}
                    <table class="table mb-2" id="table-battery-detail">
                        {{-- Header --}}
                        <thead>
                            <tr>
                                <td colspan="7" class="h5 text-center">
                                    Item
                                    <button type="button" id="btn-add-row"
                                        class="btn btn-primary btn-sm rounded-circle mx-2">
                                        <i class="fas fa-plus"></i>
                                    </button>
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
                                            ];
                                            $encodedTargets = json_encode($targets);
                                        @endphp

                                        @component('components.autocomplete', [
                                            'id' => "battery-name-$counter",
                                            'class' => 'battery-name',
                                            'value' => isset($data['profile']['batteries']) ? $battery['battery_name'] : '',
                                            'name' => 'batteriesname[]',
                                            'nameHiddenId' => 'batteriesid[]',
                                            'valueHiddenId' => isset($data['profile']['batteries']) ? $battery['battery_id'] : '',
                                            'url' => '/battery-recycle/get/',
                                            'placeholder' => 'Enter item name',
                                            'targets' => $encodedTargets,
                                            'callback' => 'calculateTotal',
                                        ])
                                        @endcomponent
                                    </td>

                                    {{-- Retail Price --}}
                                    <td>
                                        <div class="input-group">
                                            <span class="input-group-text border-end">IDR</span>
                                            <input type="text" class="form-control text-end battery-priceretail"
                                                id="battery-priceretail-{{ $counter }}" name="batteriespriceretail[]"
                                                placeholder="Enter item retail price" required
                                                @isset($data['profile']['batteries']) value="{{ $battery['battery_price_retail'] }}" @endisset>
                                        </div>
                                    </td>

                                    {{-- Tax --}}
                                    <td>
                                        {{-- input type hidden battery type --}}
                                        <input type="hidden"
                                            class="form-control battery-type-{{ $counter }} battery-type"
                                            id="battery-type-{{ $counter }}" name="batteriestype[]" value="regular">

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
                                            <input type="text" class="form-control text-end battery-discountprice"
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
                                            <div class="col">
                                                <div class="input-group">
                                                    <span class="input-group-text border-end">IDR</span>
                                                    <input type="text" class="form-control text-end battery-price"
                                                        id="battery-price-{{ $counter }}" name="batteriesprice[]"
                                                        placeholder="Enter item price" required readonly
                                                        @isset($data['profile']['batteries']) value="{{ $battery['price_net'] }}" @endisset>
                                                </div>
                                            </div>

                                            <div class="col-sm-2">
                                                <button type="button"
                                                    class="btn btn-danger btn-sm disabled btn-delete-row"
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
                                <td colspan="5"></td>
                                <td class="text-end">Subtotal</td>
                                <td>
                                    <div class="input-group">
                                        <span class="input-group-text border-end">IDR</span>
                                        <input type="text" class="form-control text-end" id="subtotal"
                                            name="subtotal"
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
                                            {{-- Discount Percentage --}}
                                            <div class="input-group d-none" id="discount-percentage">
                                                <input type="text" pattern="[0-9.]+" class="form-control text-end"
                                                    id="discount" name="discount"
                                                    @isset($data['profile'])value="{{ $data['profile']['discount'] }}" @else value="0" @endisset
                                                    @isset($data['profile']['discount']) readonly @endisset required>
                                                <span class="input-group-text border-end">%</span>
                                            </div>

                                            {{-- Discount Price --}}
                                            <div class="input-group" id="discount-price">
                                                <span class="input-group-text border-end">IDR</span>
                                                <input type="text" class="form-control text-end"
                                                    id="discount-price-value" name="discountprice"
                                                    @isset($data['profile']['discount']) readonly @endisset
                                                    @isset($data['profile'])value="{{ $data['profile']['discount_price'] }}" @else value="0" @endisset
                                                    required>
                                            </div>
                                        </div>

                                        <div class="col-sm-2">
                                            <input type="checkbox" id="toggle-discount" data-toggle="toggle"
                                                data-size="sm" data-offlabel="%" data-onlabel="IDR" checked readonly>
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
                                        <input type="text" class="form-control text-end" id="total"
                                            name="total"
                                            @isset($data['profile'])value="{{ $data['profile']['total'] }}" @else value="0" @endisset
                                            required readonly>
                                    </div>
                                </td>
                            </tr>

                            {{-- Payment Method & Status --}}
                            <tr>
                                <td colspan="5"></td>
                                <td class="text-end">Payment method</td>
                                <td>
                                    <div class="row">
                                        <div class="col">
                                            <select class="form-control" id="payment-method" name="paymentmethod"
                                                required>
                                                <option></option>
                                                @foreach ($data['payment_methods'] as $method)
                                                    <option value="{{ $method['id'] }}"
                                                        @if (isset($data['profile']) && $data['profile']['payment_method_id'] == $method['id']) selected @endif>
                                                        {{ $method['name'] }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-5">
                                            <select name="status" id="status" class="form-control" required>
                                                <option value="paid" @if (isset($data['profile']) && $data['profile']['status'] == 'paid') selected @endif>
                                                    Paid
                                                </option>
                                                <option value="pending"
                                                    @if (isset($data['profile']) && $data['profile']['status'] == 'pending') selected @endif>
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

                    <div class="card" id="ExpenseSection">
                        <div class="card-body">

                            <button class="btn btn-primary mb-3" id="addExpense" type="button">Add Expense</button>
                            <div class="table-responsive" id="ExpenseTable">
                                <table class="table table-center mb-0">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Chart of Account</th>
                                            <th>Expense Name</th>
                                            <th>Amount</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="ExpenseTableBody">
                                        {{-- @dd($data['profile']['billing_invoice_expenses']); --}}
                                        @if (isset($data['profile']['billing_invoice_expenses']))
                                            @if (count($data['profile']['billing_invoice_expenses']) == 0)
                                                <tr id="NoExpenseRow">
                                                    <td colspan="5" class="text-center">No expenses added</td>
                                                </tr>
                                            @endif

                                            @foreach ($data['profile']['billing_invoice_expenses'] as $index => $expense)
                                                <tr data-expense-id="{{ $expense['id'] }}">
                                                    <td>{{ $index + 1 }}</td>
                                                    <td class="chart-of-account">{{ $expense['debit_account']['number'] }}
                                                        -
                                                        {{ $expense['debit_account']['name'] }}</td>
                                                    <td class="expense-name">{{ $expense['description'] }}
                                                        <input type="hidden" name="ExpenseIds[]"
                                                            value="{{ $expense['expense_id'] }}">
                                                    </td>
                                                    <td class="expense-amount">
                                                        Rp. {{ number_format($expense['amount'], 0, ',', '.') }}
                                                        <input type ="hidden" name="ExpenseAmounts[]"
                                                            value="{{ number_format($expense['amount'], 0, ',', '.') }}"
                                                            class="ExpenseAmount">
                                                    </td>
                                                    <td>
                                                        <button type="button"
                                                            class="btn btn-danger btn-sm btn-delete-expense"
                                                            title="Delete Expense"><i class="fas fa-trash"></i></button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="3" class="text-end">Total Expense</th>
                                            <th colspan="2" id="TotalExpense">
                                                @if (isset($data['profile']['billing_invoice_expenses']))
                                                    Rp.
                                                    {{ number_format(array_sum(array_column($data['profile']['billing_invoice_expenses'], 'amount')), 0, ',', '.') }}
                                                @else
                                                    Rp. 0
                                                @endif
                                            </th>
                                        </tr>
                                    </tfoot>
                                </table>

                            </div>
                        </div>
                    </div>


                    {{-- Modal Add Expense --}}
                    <div class="modal fade" id="AddExpenseModal" tabindex="-1" aria-labelledby="AddExpenseModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="AddExpenseModalLabel">Add Expense</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="ExpenseName" class="form-label">Expense Name</label>
                                        <select class="form-select" id="ExpenseName" name="ExpenseName">
                                            <option value="">Select Expense</option>
                                            @foreach ($data['expenses'] as $expense)
                                                <option value="{{ $expense['id'] }}"
                                                    data-chart-of-account="{{ $expense['chart_of_account']['number'] }} - {{ $expense['chart_of_account']['name'] }}">
                                                    {{ $expense['chart_of_account']['number'] }} -
                                                    {{ $expense['chart_of_account']['name'] }} -
                                                    {{ $expense['name'] }}</option>
                                            @endforeach
                                        </select>

                                    </div>
                                    <div class="mb-3">
                                        <label for="ExpenseAmount" class="form-label">Amount</label>
                                        <input type="number" class="form-control" id="ExpenseAmount"
                                            name="ExpenseAmount" placeholder="Enter Expense Amount">
                                    </div>
                                    <button class="btn btn-primary" id="btnAddExpense" type="button">Add
                                        Expense</button>
                                </div>
                            </div>
                        </div>
                    </div>

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
                            Sales Order Recycle </button>

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
            $('#customer').select2({
                placeholder: "Enter customer"
            });

            $("#customer").on("select2:select", function(e) {
                if (e.params.data.id === "new") {
                    $("#customer-new-row").removeClass('d-none');
                    $("#customer-name").attr("required", true);
                    $("#customer-contact").attr("required", true);

                    $('#customer-vehicle').select2({
                        placeholder: "Enter customer owned vehicle"
                    });
                } else {
                    $("#customer-new-row").addClass('d-none');
                    $("#customer-name").attr("required", false);
                    $("#customer-contact").attr("required", false);
                }
            });

            $('#vendor').select2({
                placeholder: "Enter vendor"
            });

            $('#ship_to').select2({
                placeholder: "Enter Ship To"
            });

            $('#payment-method').select2({
                placeholder: "Enter payment method"
            });

            $('#status').select2({});
        })
    </script>

    {{-- Form Handler --}}
    <script>
        let indexUrl = "/sales-order";

        $("#quotation-form").on("submit", function(event) {
            event.preventDefault();

            let mode = $("#btn-save").attr("value"); // update || create
            let url = (mode == "update") ? "/sales-order/recycle/update" : "/sales-order/recycle/store";

            let address = $("#AddressSearchColumnSalesOrderEditable").val();
            let lat = $("#Latitude").val();
            let lon = $("#Longitude").val();

            calculateTotal();

            // Obtain submitted form data.
            let formData = new FormData($(this)[0]);

            let expenses = [];
            $("#ExpenseTableBody tr").each(function() {
                let expenseId = $(this).data("expense-id");
                let chartOfAccount = $(this).find(".chart-of-account").text();
                let expenseName = $(this).find(".expense-name").text();
                let amountText = $(this).find(".expense-amount").text().replace(/[^0-9.-]+/g, "");
                let amount = parseFloat(amountText);

                if (!isNaN(amount)) {
                    expenses.push({
                        id: expenseId,
                        chart_of_account: chartOfAccount,
                        name: expenseName,
                        amount: amount,
                    });
                }
            });

            formData.append("expenses", JSON.stringify(expenses));

            // Show SweetAlert loading
            Swal.fire({
                title: 'Please wait',
                text: 'Processing...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Send submit POST request via AJAX.
            sendSubmitRequest(url, formData, function() {
                Swal.close();
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

                calculateTotal();
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

        $(document).on("change keyup", ".battery-discountprice, #tax, #discount, #discount-price-value, #extra-discount",
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
                    url: "/sales-order/technician/get/" + parentId,
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
                        subtotal -= value;
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

        $("#addExpense").on("click", function() {
            $("#AddExpenseModal").modal("show");
        });

        $("#ExpenseAmount").on("keypress", function(e) {
            if (e.which == 13) {
                e.preventDefault();
                $("#btnAddExpense").click();
            }
        });

        $("#btnAddExpense").on("click", function() {
            var ExpenseName = $("#ExpenseName").val();
            var ExpenseAmount = $("#ExpenseAmount").val();
            var ExpenseText = $("#ExpenseName option:selected").text();
            var ChartOfAccount = $("#ExpenseName option:selected").data("chart-of-account");
            var ExpenseId = $("#ExpenseName option:selected").val();

            if (ExpenseName == "" || ExpenseAmount == "") {
                swal.fire("Error!", "Please fill all fields", "error");
                return;
            }

            if ($("#NoExpenseRow").length) {
                $("#NoExpenseRow").remove();
            }

            var no = $("#ExpenseTableBody tr").length + 1;

            var newRow = `
            <tr>
                <td>${no}</td>
                <td>${ChartOfAccount}</td>
                <td>${ExpenseText}
                    <input type="hidden" name="ExpenseIds[]" value="${ExpenseId}" class="ExpenseId">
                </td>
                <td>Rp. ${parseFloat(ExpenseAmount).toLocaleString('id-ID')}
                    <input type="hidden" name="ExpenseAmounts[]" value="${ExpenseAmount}" class="ExpenseAmount">
                </td>
                <td><button class="btn btn-danger btn-sm btn-delete-expense"><i class="fas fa-trash"></i></button></td>
            </tr>
        `;

            var totalExpense = 0;

            $("#ExpenseTableBody tr").each(function() {
                var amount = parseFloat($(this).find("td:eq(3)").text().replace("Rp. ", "").replace(/\./g,
                    ""));
                totalExpense += amount;
            });
            totalExpense += parseFloat(ExpenseAmount);

            $("#ExpenseTableBody").append(newRow);
            $("#TotalExpense").text("Rp. " + totalExpense.toLocaleString('id-ID'));
            $("#AddExpenseModal").modal("hide");

            // clear modal input
            $("#ExpenseName").val("");
            $("#ExpenseAmount").val("");
        });

        $(document).on("click", ".btn-delete-expense", function() {
            var row = $(this).closest("tr");
            var amount = parseFloat(row.find("td:eq(3)").text().replace("Rp. ", "").replace(/\./g, ""));
            row.remove();

            var totalExpense = 0;
            $("#ExpenseTableBody tr").each(function() {
                var amount = parseFloat($(this).find("td:eq(3)").text().replace("Rp. ", "").replace(/\./g,
                    ""));
                totalExpense += amount;
            });

            $("#TotalExpense").text("Rp. " + totalExpense.toLocaleString('id-ID'));

            $("#ExpenseTableBody tr").each(function(index) {
                $(this).find("td:eq(0)").text(index + 1);
            });
        });
    </script>
@endsection
