@extends('template.master')

@section('content')
{{-- Form --}}
<div class="card">
    <div class="card-body">
        {{-- Title --}}
        <div class="card-title h2">
            @if (isset($profile))
                Edit Customer
            @else
                Add New Customer
            @endif
        </div>
        <br>

        {{-- Form --}}
        <form id="customer-form">
            @csrf

            {{-- Name --}}
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Customer name" required
                @if (isset($profile))
                    value="{{ $profile['name'] }}"
                @endif
                >
            </div>
            
            {{-- Address --}}
            <div class="form-group">
                <label for="address">Address</label>
                <input type="text" class="form-control" id="address" name="address" placeholder="Customer address" required
                @if (isset($profile))
                    value="{{ $profile['address'] }}"
                @endif
                >
            </div>

            {{-- Contact --}}
            <div class="form-group">
                <label for="contact">Contact</label>
                <input type="text" class="form-control" id="contact" name="contact" placeholder="Customer contact" required
                @if (isset($profile))
                    value="{{ $profile['contact'] }}"
                @endif
                >
            </div>

            {{-- Email --}}
            <div class="form-group">
                <label for="contact">E-mail</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Customer e-mail" required
                @if (isset($profile))
                    value="{{ $profile['email'] }}"
                @endif
                >
            </div>

            {{-- Vehicle --}}
            <div class="form-group">
                <label for="vehicle" class="col-sm-2 col-form-label">Customer Vehicle</label>
                <select class="form-control" id="vehicle" name="vehicle[]" multiple="multiple">
                    @foreach ($data['vehicles'] as $vehicle)
                        <option value="{{ $vehicle['id'] }}">{{ $vehicle['name'] }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Hidden Inputs --}}
            <input type="hidden" id="id" name="id"
            @if (isset($profile))
                value="{{ $profile['id'] }}"
            @endif
            >
            
            {{-- Buttons --}}
            <a class="btn btn-primary mx-1" id="btn-save"
                @if (isset($profile))
                    value="update">
                    Update
                @else
                    value="create">
                    Create
                @endif
            </button>
            <a type="button" class="btn btn-danger mx-1" id="btn-cancel">Cancel</a>
        </form>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#vehicle').select2({
            placeholder: "Customer owned vehicles"
        });

        $("#btn-save").on('click', function() {
            let mode = $(this).attr("value"); // Update or Create
            let url = "/customer/store";
            if (mode == "update") {
                url = "/customer/update";
            }

            // Get customer form data.
            let formData = new FormData($('#customer-form')[0]);
            
            // Send customer form data to Customer controller using AJAX.
            $.ajax({
                url: url,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    // Get response data (in JSON).
                    let responseData = JSON.parse(response);

                    // Check response data status.
                    // Status indicates the success status of customer creating porcess.
                    if (responseData.status) {
                        // Creating new customer was succeeded.
                        showSuccessToast(responseData.message);
                    } else {
                        // Creating new customer was failed.
                        showErrorToast(responseData.message);
                    }

                    // Redirect to Customer index page.
                    window.location.href = "/customer";
                }
            });
        });

        $("#btn-cancel").on('click', function() {
            $.ajax({
                url: '/customer',
                success: function(response) {
                    $('#main-wrapper').html(response);
                }
            });
        });
    });
</script>
@endsection
