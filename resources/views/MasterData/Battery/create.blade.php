@extends('template.master')

@section('content')
{{-- Form --}}
<div class="card">
    <div class="card-body">
        {{-- Title --}}
        <div class="card-title h2">
            @if (isset($data['profile']))
                Edit Battery
            @else
                Add New Battery
            @endif
        </div>
        <br>

        {{-- Form --}}
        <form id="battery-form">
            @csrf
            
            {{-- Name & Aliases --}}
            <div class="row">
                {{-- Name --}}
                <div class="col">
                    <div class="form-group local-forms">
                        <label for="name">Name <span class="login-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Enter battery name" required
                        @if (isset($data['profile']))
                            value="{{ $data['profile']['name'] }}"
                        @endif
                        >
                    </div>
                </div>
                
                {{-- Alternate Names --}}
                <div class="col">
                    <div class="form-group local-forms">
                        <label for="brand">Alternate Names</label>
                        <select class="form-control" id="altname" name="altname[]" multiple="multiple">
                            @if (isset($data["aliases"]) && !empty($data["aliases"]))
                                @foreach ($data["aliases"] as $alias)
                                    <option value="{{ $alias["name"] }}" selected>{{ $alias["name"] }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
            </div>

            {{-- Brand & Subbrand Category --}}
            <div class="row">
                {{-- Brand --}}
                <div class="col">
                    <div class="form-group local-forms">
                        <label for="brand">Brand <span class="login-danger">*</span></label>
                        <select class="form-control" id="brand" name="brand">
                            <option></option>
                            @foreach ($data['brands'] as $brand)
                                <option value="{{ $brand['id'] }}" @if (isset($data['profile']) && $data['profile']['id_brand'] == $brand['id']) selected @endif>{{ $brand['name'] }}</option>
                            @endforeach
                            <option value="new">Quick add new brand&hellip;</option>
                        </select>
                    </div>
                </div>

                {{-- Subbrand Category --}}
                <div class="col">
                    <div class="form-group local-forms">
                        <label for="subbrand-category">Subbrand Category <span class="login-danger">*</span></label>
                        <select class="form-control" id="subbrand-category" name="subbrandcategory">
                            <option></option>
                            @foreach ($data['subbrand_categories'] as $category)
                                <option value="{{ $category['id'] }}" @if (isset($data['profile']) && $data['profile']['id_brand'] == $category['id']) selected @endif>{{ $category['name'] }}</option>
                            @endforeach
                            <option value="new">Quick add new subbrand&hellip;</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Quick Add New Brand & Subbrand Category --}}
            <div class="row">
                {{-- New Brand --}}
                <div class="col">
                    <div id="brand-new-group" class="form-group local-forms" style="display: none;">
                        <label for="brand-new">New Brand <span class="login-danger">*</span></label>
                        <input type="text" class="form-control" id="brand-new" name="newbrand" placeholder="Enter new battery brand">
                    </div>
                </div>

                {{-- New Subbrand Category --}}
                <div class="col">
                    <div id="subbrand-category-new-group" class="form-group local-forms" style="display: none;">
                        <label for="subbrand-category-new">New Subbrand Category <span class="login-danger">*</span></label>
                        <input type="text" class="form-control" id="subbrand-category-new" name="newsubbrandcategory" placeholder="Enter new battery subbrand category">
                    </div>
                </div>
            </div>

            {{-- Usage Type, Technology & Size Category --}}
            <div class="row">
                {{-- Usage Type --}}
                <div class="col">
                    <div class="form-group local-forms">
                        <label for="usagetype">Usage Type <span class="login-danger">*</span></label>
                        <select class="form-control" id="usagetype" name="usagetype">
                            <option></option>
                            @foreach ($data['usage_types'] as $usage)
                                <option value="{{ $usage['id'] }}" @if (isset($data['profile']) && $data['profile']['id_usage_type'] == $usage['id']) selected @endif>{{ $usage['name'] }}</option>
                            @endforeach
                            <option value="new">Quick add new usage type&hellip;</option>
                        </select>
                    </div>
                </div>

                {{-- Battery Technology --}}
                <div class="col">
                    <div class="form-group local-forms">
                        <label for="technology">Technology <span class="login-danger">*</span></label>
                        <select class="form-control" id="technology" name="technology">
                            <option></option>
                            @foreach ($data['technologies'] as $tech)
                                <option value="{{ $tech['id'] }}" @if (isset($data['profile']) && $data['profile']['id_technology'] == $tech['id']) selected @endif>{{ $tech['name'] }}</option>
                            @endforeach
                            <option value="new">Quick add new technology&hellip;</option>
                        </select>
                    </div>
                </div>

                {{-- Size Category --}}
                <div class="col">
                    <div class="form-group local-forms">
                        <label for="size">Size Category <span class="login-danger">*</span></label>
                        <select class="form-control" id="size" name="size">
                            <option></option>
                            @foreach ($data['sizes'] as $size)
                                <option value="{{ $size['id'] }}" @if (isset($data['profile']) && $data['profile']['id_size_category'] == $size['id']) selected @endif>{{ $size['name'] }}</option>
                            @endforeach
                            <option value="new">Quick add new size category&hellip;</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Quick Add New Brand, Subbrand Category & Size Category --}}
            <div class="row">
                {{-- New Usage Type --}}
                <div class="col">
                    <div id="usagetype-new-group" class="form-group local-forms" style="display: none;">
                        <label for="usagetype-new">New Usage Type <span class="login-danger">*</span></label>
                        <input type="text" class="form-control" id="usagetype-new" name="newusagetype" placeholder="Enter new battery usage type">
                    </div>
                </div>

                {{-- New Technology --}}
                <div class="col">
                    <div id="technology-new-group" class="form-group local-forms" style="display: none;">
                        <label for="technology-new">New Technology <span class="login-danger">*</span></label>
                        <input type="text" class="form-control" id="technology-new" name="newtechnology" placeholder="Enter new battery technology">
                    </div>
                </div>

                {{-- Size Category --}}
                <div class="col">
                    <div id="size-new-group" class="form-group local-forms" style="display: none;">
                        <label for="size-new">New Size Category <span class="login-danger">*</span></label>
                        <input type="text" class="form-control" id="size-new" name="newsize" placeholder="Enter new size category">
                    </div>
                </div>
            </div>

            {{-- Dimension --}}
            <div class="row">
                {{-- Length --}}
                <div class="col">
                    <div class="form-group local-forms">
                        <label for="dimension-length">Length <span class="login-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" min="0" class="form-control" id="dimension-length" name="dimension[]" placeholder="Enter battery length" required
                            @if (isset($data['profile']))
                                value="{{ $data['profile']['dimension_length'] }}"
                            @endif
                            >
                            <span class="input-group-text border-end">mm</span>
                        </div>
                    </div>
                </div>

                {{-- Width --}}
                <div class="col">
                    <div class="form-group local-forms">
                        <label for="dimension-width">Width <span class="login-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" min="0" class="form-control" id="dimension-width" name="dimension[]" placeholder="Enter battery width" required
                            @if (isset($data['profile']))
                                value="{{ $data['profile']['dimension_width'] }}"
                            @endif
                            >
                            <span class="input-group-text border-end">mm</span>
                        </div>
                    </div>
                </div>

                {{-- Height --}}
                <div class="col">
                    <div class="form-group local-forms">
                        <label for="dimension-height">Height <span class="login-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" min="0" class="form-control" id="dimension-height" name="dimension[]" placeholder="Enter battery height" required
                            @if (isset($data['profile']))
                                value="{{ $data['profile']['dimension_height'] }}"
                            @endif
                            >
                            <span class="input-group-text border-end">mm</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Standard CCA & Capacity --}}
            <div class="row">
                {{-- Standard CCA --}}
                <div class="col">
                    <div class="form-group local-forms">
                        <label for="standard-cca">Standard CCA <span class="login-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" min="0" class="form-control" id="standard-cca" name="standardcca" placeholder="Enter battery standard CCA" required
                            @if (isset($data['profile']))
                                value="{{ $data['profile']['standard_cca'] }}"
                            @endif
                            >
                            <span class="input-group-text border-end">A</span>
                        </div>
                    </div>
                </div>

                {{-- Capacity --}}
                <div class="col">
                    <div class="form-group local-forms">
                        <label for="dimension-width">Capacity <span class="login-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" min="0" class="form-control" id="capacity" name="capacity" placeholder="Enter battery capacity" required
                            @if (isset($data['profile']))
                                value="{{ $data['profile']['capacity'] }}"
                            @endif
                            >
                            <span class="input-group-text border-end">AH</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Warranty & Price Retail --}}
            <div class="row">
                {{-- Warranty --}}
                <div class="col">
                    <div class="form-group local-forms">
                        <label for="warranty">Warranty <span class="login-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="warranty" name="warranty" placeholder="Enter battery warranty duration" required
                            @if (isset($data['profile']))
                            value="{{ $data['profile']['warranty'] }}"
                            @endif
                            >
                            <span class="input-group-text border-end">month</span>
                        </div>
                    </div>
                </div>

                {{-- Price Retail --}}
                <div class="col">
                    <div class="form-group local-forms">
                        <label for="price">Price Retail <span class="login-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text border-end">IDR</span>
                            <input type="text" min="0" class="form-control" id="price" name="price" placeholder="Enter battery price retail" required
                            @if (isset($data['profile']))
                                value="{{ $data['profile']['price_retail'] }}"
                            @endif
                            >
                        </div>
                        <small id="price-warning-number" class="form-text text-danger" style="display: none;">Please enter a valid numeric value for the price.</small>
                    </div>
                </div>
            </div>
            
            {{-- Image --}}
            <div class="form-group students-up-files">
                <label for="image">Upload Image</label>
                <div class="uplod">
                    <label class="file-upload image-upbtn mb-0">
                        Choose File <input type="file" id="image" name="image">
                    </label>
                </div>
            </div>

            {{-- Hidden Inputs --}}
            <input type="hidden" id="id" name="id"
            @if (isset($data['profile']))
                value="{{ $data['profile']['id'] }}"
            @endif
            >

            {{-- Buttons --}}
            <div class="d-flex flex-row-reverse">
                {{-- Create Button --}}
                <button type="submit" class="btn btn-success mx-1" id="btn-save"
                    @if (isset($data['profile']))
                        value="update">
                        Update Battery
                    @else
                        value="create">
                        Create Battery
                    @endif
                </button>

                {{-- Cancel Button --}}
                <button type="reset" type="button" class="btn btn-danger mx-1" id="btn-cancel">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#altname').select2({
            placeholder: "Enter battery alternative names (separated by comma)",
            tags: true,
            tokenSeparators: [',', ' '],
            minimumResultsForSearch: Infinity
        });

        $('#brand').select2({
            placeholder: "Enter battery brand"
        });

        $("#brand").on("select2:select", function (e) {
            if (e.params.data.id === "new") {
                $("#brand-new-group").show();
                $("#brand-new-group").attr("required", true);
            } else {
                $("#brand-new-group").hide();
                $("#brand-new-group").attr("required", false);
            }
        });

        $('#subbrand-category').select2({
            placeholder: "Enter battery subbrand category"
        });

        $("#subbrand-category").on("select2:select", function (e) {
            if (e.params.data.id === "new") {
                $("#subbrand-category-new-group").show();
                $("#subbrand-category-new-group").attr("required", true);
            } else {
                $("#subbrand-category-new-group").hide();
                $("#subbrand-category-new-group").attr("required", false);
            }
        });

        $('#usagetype').select2({
            placeholder: "Enter battery usage type"
        });

        $("#usagetype").on("select2:select", function (e) {
            if (e.params.data.id === "new") {
                $("#usagetype-new-group").show();
                $("#usagetype-new-group").attr("required", true);
            } else {
                $("#usagetype-new-group").hide();
                $("#usagetype-new-group").attr("required", false);
            }
        });


        $('#technology').select2({
            placeholder: "Enter battery technolgy"
        });

        $("#technology").on("select2:select", function (e) {
            if (e.params.data.id === "new") {
                $("#technology-new-group").show();
                $("#technology-new-group").attr("required", true);
            } else {
                $("#technology-new-group").hide();
                $("#technology-new-group").attr("required", false);
            }
        });

        $('#size').select2({
            placeholder: "Enter battery size category"
        });

        $("#size").on("select2:select", function (e) {
            if (e.params.data.id === "new") {
                $("#size-new-group").show();
                $("#size-new-group").attr("required", true);
            } else {
                $("#size-new-group").hide();
                $("#size-new-group").attr("required", false);
            }
        });

        $('#price').on("keyup", function() {
            let n = parseInt($(this).val().replace(/\D/g,''),10);
            
            if (!isNaN(n)) {
                $("#price-warning-number").hide();
                $(this).val(n.toLocaleString());
            } else {
                $("#price-warning-number").show();
                $(this).val("");
            }
        });

        $("#battery-form").on("submit", function(event) {
            event.preventDefault();

            let mode = $("#btn-save").attr("value"); // Update or Create
            let url = "/battery/store";
            if (mode == "update") {
                url = "/battery/update";
            }

            // Get customer form data.
            let formData = new FormData($(this)[0]);
            
            // Send customer form data to Customer controller using AJAX.
            $.ajax({
                url: url,
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    // Get response data (in JSON).
                    let responseData = JSON.parse(response);

                    // Check response data status.
                    // Status indicates the success status of customer creating porcess.
                    if (responseData.status) {
                        // Creating process was succeeded.
                        showSuccessToast(responseData.message);
                    } else {
                        // Creating process was failed.
                        showErrorToast(responseData.message);
                    }

                    // Redirect to Battery index page.
                    goToPage("/battery");
                }
            });
        });

        $("#battery-form").on("reset", function() {
            goToPage("/battery");
        });
    });
</script>
@endsection

