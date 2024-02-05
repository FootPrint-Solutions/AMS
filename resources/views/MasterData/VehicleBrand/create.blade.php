@extends('template.master')

@section('content')
{{-- Form --}}
<div class="card">
    <div class="card-body">
        {{-- Title --}}
        <div class="card-title h2">
            Add New Vehicle Brand
        </div>
        <br>

        {{-- Form --}}
        <form id="vehicle-brand-form">
            @csrf

            {{-- Name --}}
            <div class="form-group local-forms">
                <label for="name">Name <span class="login-danger">*</span></label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Enter vehicle brand name" required>
            </div>
            
            {{-- Buttons --}}
            <div class="d-flex flex-row-reverse">
                {{-- Create Button --}}
                <button type="submit" class="btn btn-success mx-1" id="btn-save" value="create">Create Vehicle Brand</button>

                {{-- Cancel Button --}}
                <button type="reset" type="button" class="btn btn-danger mx-1" id="btn-cancel">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function() {
        $("#vehicle-brand-form").on("submit", function(event) {
            event.preventDefault();

            // Get vehicle brand form data.
            let formData = new FormData($(this)[0]);
            
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
                    goToPage("/vehicle");
                }
            });
        });

        $("#vehicle-brand-form").on("reset", function() {
            goToPage("/vehicle");
        });
    });
</script>
@endsection

