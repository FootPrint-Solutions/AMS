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
            <div class="form-group row">
                <div class="col-1">
                    <label for="company-name">Name</label>
                </div>

                <div class="col-11">
                    <input type="text" class="form-control" id="company-name" name="name" placeholder="Company name" value="{{ $data ? $data->name : '' }}">

                </div>
            </div>
            
            {{-- Company Address --}}
            <div class="form-group row">
                <div class="col-1">
                    <label for="company-address">Address</label>
                </div>

                <div class="col-11">
                    <input type="text" class="form-control" id="company-address" name="address" placeholder="Company address" value="{{ $data ? $data->address : '' }}">
                </div>
            </div>

            {{-- Company Contact --}}
            <div class="form-group row">
                <div class="col-1">
                    <label for="company-address">Contact</label>
                </div>

                <div class="col-11">    
                    <input type="text" class="form-control" id="company-contact" name="contact" placeholder="Company contact" value="{{ $data ? $data->contact : '' }}">
                </div>
            </div>

            {{-- Company E-mail --}}
            <div class="form-group row">
                <div class="col-1">
                    <label for="company-email">E-mail</label>
                </div>

                <div class="col-11">
                    <input type="email" class="form-control" id="company-email" name="email" placeholder="Company e-mail" value="{{ $data ? $data->email : '' }}">
                </div>
            </div>
            
            {{-- Buttons --}}
            <a class="btn btn-primary mx-1" id="btn-save">Save</button>
            <a class="btn btn-danger mx-1" id="btn-reset">Reset</a>
        </form>
    </div>
</div>

<script>
    $(document).ready(function() {
        $("#btn-save").on('click', function() {
            // Get company form data.
            let formData = new FormData($('#company-form')[0]);
            
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

        $("#btn-reset").on('click', function() {
            $.ajax({
                url: '/company',
                success: function(response) {
                    window.location.replace('/company');
                }
            });
        });
    });
</script>
@endsection