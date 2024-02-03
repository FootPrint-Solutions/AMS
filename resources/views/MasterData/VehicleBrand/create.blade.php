@extends('template.master')

@section('content')
{{-- Form --}}
<div class="card">
    <div class="card-body">
        {{-- Title --}}
        <div class="card-title h2">
            Add New Brand
        </div>
        <br>

        {{-- Form --}}
        <form id="vehicle-brand-form">
            @csrf

            {{-- Name --}}
            <div class="form-group local-forms">
                <label for="name">Name</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Enter vehicle brand name" required
                @if (isset($profile))
                    value="{{ $profile['name'] }}"
                @endif
                >
            </div>
            
            {{-- Buttons --}}
            <div class="d-flex flex-row-reverse">
                {{-- Create Button --}}
                <a class="btn btn-primary mx-1" id="btn-save" value="create">Create</button>

                {{-- Cancel Button --}}
                <a type="button" class="btn btn-danger mx-1" id="btn-cancel">Cancel</a>
            </div>
            
            
        </form>
    </div>
</div>

<script>
    $(document).ready(function() {
        $("#btn-save").on('click', function() {
            // Get vehicle brand form data.
            let formData = new FormData($('#vehicle-brand-form')[0]);
            
            // Send vehicle brand form data to VehicleBrand controller using AJAX.
            $.ajax({
                url: '/vehicle/brand/store',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    // Get response data (in JSON).
                    let responseData = JSON.parse(response);

                    // Check response data status.
                    // Status indicates the success status of vehicle creating porcess.
                    if (responseData.status) {
                        // Creating new vehicle was succeeded.
                        showSuccessToast(responseData.message);
                    } else {
                        // Creating new vehicle was failed.
                        showErrorToast(responseData.message);
                    }

                    // Redirect to Vehicle index page.
                    window.location.href = "/vehicle";
                }
            });
        });

        $("#btn-cancel").on('click', function() {
            $.ajax({
                url: '/vehicle',
                success: function(response) {
                    $('#main-wrapper').html(response);
                }
            });
        });
    });
</script>
@endsection

