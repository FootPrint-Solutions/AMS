@extends('template.master')

@section('content')
{{-- Form --}}
<div class="card">
    <div class="card-body">
        {{-- Title --}}
        <div class="card-title h2">
            @if (isset($data['profile']))
                Edit
            @else
                Add New
            @endif
            Technician
        </div>
        <br>

        {{-- Form --}}
        <form id="technician-form">
            @csrf

            {{-- Name & Shop --}}
            <div class="row">
                {{-- Name --}}
                <div class="col">
                    <div class="form-group local-forms">
                        <label for="name">Name <span class="login-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Enter vehicle name" required
                        @if (isset($data['profile']))
                            value="{{ $data['profile']['name'] }}"
                        @endif
                        >
                    </div>
                </div>

                {{-- Shop --}}
                <div class="col">
                    <div class="form-group local-forms">
                        <label for="shop">Shop <span class="login-danger">*</span></label>
                        <select class="form-control" id="shop" name="shop" required>
                            <option></option>
                            @foreach ($data['shops'] as $shop)
                                <option value="{{ $shop['id'] }}" @if (isset($data['profile']) && $data['profile']['id_shop'] == $shop['id']) selected @endif>{{ $shop['distributor']['name'] . " - " . $shop['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Contact and Email --}}
            <div class="row">
                {{-- Contact --}}
                <div class="col">
                    <div class="form-group local-forms">
                        <label for="contact">Contact <span class="login-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text border-end country-code">+62</span>
                            <input type="tel" pattern="[0-9]+" class="form-control" id="contact" name="contact" placeholder="Enter technician contact" required
                            @isset($data['profile'])
                                value="{{ $data['profile'] ? $data['profile']['contact'] : '' }}"
                            @endisset
                            >
                        </div>
                    </div>
                </div>

                {{-- E-mail --}}
                <div class="col">
                    <div class="form-group local-forms">
                        <label for="email">E-mail</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Enter technician e-mail"
                        @isset($data['profile'])
                            value="{{ $data['profile'] ? $data['profile']['email'] : '' }}"
                        @endisset
                        >
                    </div>
                </div>
            </div>

            {{-- Note --}}
            <div class="form-group local-forms">
                <label for="note">Note</label>
                <textarea type="text" class="form-control" id="note" name="note" placeholder="Enter some notes regarding the technician">@if (isset($data['profile']) && !empty($data['profile']['note'])) {{ $data['profile']['note'] }} @endif</textarea>
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
                        Update
                    @else
                        value="create">
                        Create
                    @endif
                    Technician
                </button>

                {{-- Cancel Button --}}
                <button type="reset" class="btn btn-danger mx-1" id="btn-cancel">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#shop').select2({
            placeholder: "Enter technician shop"
        });

        $("#technician-form").on("submit", function(event) {
            event.preventDefault();

            // Get current display mode (Update or Create).
            let mode = $("#btn-save").attr("value");
            let url = "/distributor/technician/store";
            if (mode == "update") {
                url = "/distributor/technician/update";
            }

            // Get form data.
            let formData = new FormData($(this)[0]);
            
            // Send form data to Vehicle controller using AJAX.
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
                    // Status indicates the success status of vehicle creating porcess.
                    if (responseData.status) {
                        // Creating or updating process was succeeded.
                        showSuccessToast(responseData.message);
                    } else {
                        // Creating or updating process was failed.
                        showErrorToast(responseData.message);
                    }

                    // Redirect to index page.
                    goToPage("/distributor/technician");
                }
            });
        });

        $("#technician-form").on("reset", function() {
            goToPage("/distributor/technician");
        });
    });
</script>
@endsection

