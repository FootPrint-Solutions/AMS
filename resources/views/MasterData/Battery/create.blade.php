@extends('template.master')

@section('content')
{{-- Form --}}
<div class="card">
    <div class="card-body">
        {{-- Title --}}
        <div class="card-title h2">
            Add New Battery
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
                        <input type="text" class="form-control" id="name" name="name" placeholder="Enter battery name" required>
                    </div>
                </div>
                
                {{-- Alternate Names --}}
                <div class="col">
                    <div class="form-group local-forms">
                        <label for="altname">Alternate Names</label>
                        <input type="text" class="form-control" id="altname" name="altname" placeholder="Enter battery alternate names">
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
                            @foreach ($data['brands'] as $brand)
                                <option value="{{ $brand['id'] }}" @if (isset($data['profile']) && $data['profile']['id_brand'] == $brand['id']) selected @endif>{{ $brand['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Subbrand Category --}}
                <div class="col">
                    <div class="form-group local-forms">
                        <label for="subbrand-category">Subbrand Category <span class="login-danger">*</span></label>
                        <select class="form-control" id="subbrand-category" name="subbrandcategory">
                            @foreach ($data['subbrand_categories'] as $category)
                                <option value="{{ $category['id'] }}" @if (isset($data['profile']) && $data['profile']['id_brand'] == $category['id']) selected @endif>{{ $category['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Usage Type & Technology --}}
            <div class="row">
                {{-- Usage Type --}}
                <div class="col">
                    <div class="form-group local-forms">
                        <label for="usagetype">Usage Type <span class="login-danger">*</span></label>
                        <select class="form-control" id="usagetype" name="usagetype">
                            @foreach ($data['usage_types'] as $usage)
                                <option value="{{ $usage['id'] }}" @if (isset($data['profile']) && $data['profile']['id_usage_type'] == $usage['id']) selected @endif>{{ $usage['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Battery Technology --}}
                <div class="col">
                    <div class="form-group local-forms">
                        <label for="technology">Technology <span class="login-danger">*</span></label>
                        <select class="form-control" id="technology" name="technology">
                            @foreach ($data['technologies'] as $tech)
                                <option value="{{ $tech['id'] }}" @if (isset($data['profile']) && $data['profile']['id_technology'] == $tech['id']) selected @endif>{{ $tech['name'] }}</option>
                            @endforeach
                        </select>
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
                                value="{{ $data['profile']['contact'] }}"
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
                                value="{{ $data['profile']['contact'] }}"
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
                                value="{{ $data['profile']['contact'] }}"
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
                                value="{{ $data['profile']['contact'] }}"
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
                                value="{{ $data['profile']['contact'] }}"
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
                        <input type="text" class="form-control" id="warranty" name="warranty" placeholder="Enter battery warranty duration" required
                        @if (isset($data['profile']))
                            value="{{ $data['profile']['contact'] }}"
                        @endif
                        >
                    </div>
                </div>

                {{-- Price Retail --}}
                <div class="col">
                    <div class="form-group local-forms">
                        <label for="price">Price Retail <span class="login-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text border-end">IDR</span>
                            <input type="number" min="0" class="form-control" id="price" name="price" placeholder="Enter battery price retail" required
                            @if (isset($data['profile']))
                                value="{{ $data['profile']['contact'] }}"
                            @endif
                            >
                        </div>
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

            {{-- Buttons --}}
            <div class="d-flex flex-row-reverse">
                {{-- Create Button --}}
                <button type="submit" class="btn btn-success mx-1" id="btn-save" value="create">Create Battery</button>

                {{-- Cancel Button --}}
                <button type="reset" type="button" class="btn btn-danger mx-1" id="btn-cancel">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#brand').select2({
            placeholder: "Enter battery brand"
        });

        $('#subbrand-category').select2({
            placeholder: "Enter battery subbrand category"
        });

        $('#usagetype').select2({
            placeholder: "Enter battery usage type"
        });

        $('#technology').select2({
            placeholder: "Enter battery technolgy"
        });

        $("#battery-form").on("submit", function(event) {
            event.preventDefault();

            // Get customer form data.
            let formData = new FormData($(this)[0]);
            
            // Send customer form data to Customer controller using AJAX.
            $.ajax({
                url: "/battery/store",
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

