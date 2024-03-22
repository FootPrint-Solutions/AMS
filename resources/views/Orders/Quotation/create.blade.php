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
                Quotation
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
                            <label for="quotation-number">Quotation Number <span class="login-danger">*</span></label>
                            <input type="text" class="form-control" id="quotation-number" name="quotationnumber"
                                placeholder="Enter distributor name" required readonly
                                @isset($data['profile'])
                            value="{{ $data['profile']['quotation_number'] }}"
                        @else
                            value="{{ 'QUO' . date('YmdHis') . rand(99, 999) }}"
                        @endisset>
                        </div>
                    </div>

                    {{-- Date --}}
                    <div class="col">
                        <div class="form-group local-forms">
                            <label for="quotation-date">Quotation Date <span class="login-danger">*</span></label>
                            <input type="date" class="form-control" id="quotation-date" name="date" required
                                @isset($data['profile'])
                            value="{{ $data['profile']['created_at'] }}"
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
                    <thead>
                        <tr>
                            <td colspan="4" class="h5 text-center">
                                Item <button type="button" id="btn-add-row"
                                    class="btn btn-primary btn-sm rounded-circle mx-2"><i class="fas fa-plus"></i></button>
                            </td>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td>
                                @component('components.autocomplete', ['url' => '/battery/get/', 'placeholder' => 'Enter item name'])
                                @endcomponent
                            </td>
                            <td><input type="number" class="form-control" name="batteriesqty[]" min="0"
                                    placeholder="Enter item quantity"></td>
                            <td><input type="text" class="form-control" name="batteriesqty[]"
                                    placeholder="Enter item price"></td>
                            <td>
                                <div class="row">
                                    <div class="col">
                                        <input type="text" class="form-control" value="0" readonly>
                                    </div>

                                    <div class="col-sm-2">
                                        <button type="button" class="btn btn-danger btn-sm"><i
                                                class="fas fa-xmark"></i></button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>

                    <tfoot>
                        <tr>
                            <td colspan="2"></td>
                            <td>Tax</td>
                            <td>
                                <div class="row">
                                    <div class="col">
                                        <input type="text" class="form-control" name="tax" value="0" required>
                                    </div>

                                    <div class="col-sm-2 status-toggle">
                                        <input type="checkbox" id="tax-check" class="check">
                                        <label for="tax-check" class="checktoggle">checkbox</label>
                                    </div>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td colspan="2"></td>
                            <td>Discount</td>
                            <td>
                                <div class="row">
                                    <div class="col">
                                        <input type="text" class="form-control" name="discount" value="0" required>
                                    </div>

                                    <div class="col-sm-2 status-toggle">
                                        <input type="checkbox" id="discount-check" class="check">
                                        <label for="discount-check" class="checktoggle">checkbox</label>
                                    </div>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td colspan="2"></td>
                            <td>Extra Discount</td>
                            <td>
                                <div class="row">
                                    <div class="col">
                                        <input type="text" class="form-control" name="extradiscount" value="0"
                                            required>
                                    </div>

                                    <div class="col-sm-2 status-toggle">
                                        <input type="checkbox" id="extra-discount-check" class="check">
                                        <label for="extra-discount-check" class="checktoggle">checkbox</label>
                                    </div>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td colspan="2"></td>
                            <td>Total</td>
                            <td><input type="text" class="form-control" name="discount" value="0" required
                                    readonly>
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
        let indexUrl = "/quotation";

        $(document).ready(function() {
            $('#customer').select2({
                placeholder: "Enter customer"
            });

            $('#shop').select2({
                placeholder: "Enter distributor shop"
            });

            $("#shop").on("select2:select", function(e) {
                // Obtain selected parent id.
                let parentId = e.params.data.id;

                // Get the list of menus inside the selected parent.
                $.ajax({
                    url: "/quotation/get/technician/" + parentId,
                    method: "GET",
                    success: function(response) {
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

            $("#btn-add-row").on("click", function() {
                var newRow = `
                    <tr>
                        <td><input type="text" class="form-control" name="batteriesname[]" placeholder="Enter item name"></td>
                        <td><input type="number" class="form-control" name="batteriesqty[]" placeholder="Enter item quantity"></td>
                        <td><input type="text" class="form-control" name="batteriesqty[]" placeholder="Enter item price"></td>
                        <td>
                            <div class="row">
                                <div class="col">
                                    <input type="text" class="form-control" value="0" readonly>
                                </div>
                                <div class="col-sm-2">
                                    <button type="button" class="btn btn-danger btn-sm"><i class="fas fa-times"></i></button>
                                </div>
                            </div>
                        </td>
                    </tr>
                `;
                $("#table-battery-detail tbody").append(newRow);
            });

            $("#distributor-form").on("submit", function(event) {
                event.preventDefault();
                if ($("#AddressSearchColumn").val() == "") {
                    swal.fire("Error!", "Please Fill The Address Column", "error");
                    $("#AddressSearchColumn").focus();
                    return;
                }
                let mode = $("#btn-save").attr("value"); // update || create
                let url = (mode == "update") ? "/distributor/update" : "/distributor/store";

                // Obtain submitted form data.
                let formData = new FormData($(this)[0]);
                formData.append('isshop', $("#isshop").is(':checked') ? 1 : 0);

                // Send submit POST request via AJAX.
                sendSubmitRequest(url, formData, function() {
                    // Redirect to index page.
                    goToPage(indexUrl);
                });
            });

            $("#quotation-form").on("reset", function() {
                goToPage(indexUrl);
            });
        });
    </script>
@endsection
