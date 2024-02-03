@extends('template.master')

@section('content')
<div class="card">
    <div class="card-body">
        {{-- Title --}}
        <div class="card-title h2">
            Company Profile
        </div>
        <br>

        {{-- Form --}}
        <form id="company-form">
            @csrf

            {{-- Company Name --}}
            <div class="form-group local-forms">
                <label for="company-name">Name <span class="login-danger">*</span></label>
                <input type="text" class="form-control" id="company-name" name="name" placeholder="Enter company name" value="{{ $data['company_profile'] ? $data['company_profile']['name'] : '' }}" required>
            </div>
            
            {{-- Company Address --}}
            <div class="form-group local-forms">
                <label for="company-address">Address <span class="login-danger">*</span></label>
                <input type="text" class="form-control" id="company-address" name="address" placeholder="Enter company address" value="{{ $data['company_profile'] ? $data['company_profile']['address'] : '' }}" required>
            </div>

            <div class="row">
                {{-- Company Contact --}}
                <div class="col">
                    <div class="form-group local-forms">
                        <label for="company-contact">Contact <span class="login-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text border-end country-code">+62</span>
                            <input type="text" class="form-control" id="company-contact" name="contact" placeholder="Enter company contact" value="{{ $data['company_profile'] ? $data['company_profile']['contact'] : '' }}" required>
                        </div>
                    </div>
                </div>

                {{-- Company E-mail --}}
                <div class="col">
                    <div class="form-group local-forms">
                        <label for="company-email">E-mail <span class="login-danger">*</span></label>
                        <input type="email" class="form-control" id="company-email" name="email" placeholder="Enter company e-mail" value="{{ $data['company_profile'] ? $data['company_profile']['email'] : '' }}" required>
                    </div>
                </div>
            </div>
            
            {{-- Buttons --}}
            <div class="d-flex flex-row-reverse">
                {{-- Save Button --}}
                <button type="submit" class="btn btn-success mx-1" id="btn-save">Save Company Profile</button>

                {{-- Reset Button --}}
                <button type="reset" class="btn btn-danger mx-1" id="btn-reset">Reset</button>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function() {
        $("#company-form").on("submit", function(event) {
            event.preventDefault();

            // Get company form data.
            let formData = new FormData($(this)[0]);
            
            // Send company form data to Company controller using AJAX.
            $.ajax({
                url: '/company/update',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    // Get response data (in JSON).
                    let responseData = JSON.parse(response);

                    // Check response data status.
                    // Status indicates the success status of company profile update.
                    if (responseData.status) {
                        // Company profile update was succeeded.
                        showSuccessToast(responseData.message);
                    } else {
                        // Company profile update was failed.
                        showErrorToast(responseData.message);
                    }
                }
            });
        });

        $("#company-form").on("reset", function() {
            $.ajax({
                url: '/company',
                success: function(response) {
                    $('#main-wrapper').html(response);
                }
            });
        });
    });
</script>
@endsection