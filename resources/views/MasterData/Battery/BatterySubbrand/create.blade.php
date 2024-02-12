@extends('template.master')

@section('content')
{{-- Form --}}
<div class="card">
    <div class="card-body">
        {{-- Title --}}
        <div class="card-title h2">
            Add New Battery Subbrand Category
        </div>
        <br>

        {{-- Form --}}
        <form id="battery-subbrand-form">
            @csrf

            {{-- Name --}}
            <div class="form-group local-forms">
                <label for="name">Name <span class="login-danger">*</span></label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Enter battery subbrand name" required>
            </div>
            
            {{-- Buttons --}}
            <div class="d-flex flex-row-reverse">
                {{-- Create Button --}}
                <button type="submit" class="btn btn-success mx-1" id="btn-save" value="create">Create Battery Subbrand</button>

                {{-- Cancel Button --}}
                <button type="reset" type="button" class="btn btn-danger mx-1" id="btn-cancel">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function() {
        $("#battery-subbrand-form").on("submit", function(event) {
            event.preventDefault();

            // Get battery brand form data.
            let formData = new FormData($(this)[0]);
            
            // Send battery brand form data to BatterySubbrand controller using AJAX.
            $.ajax({
                url: '/battery/subbrand/store',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    // Get response data (in JSON).
                    let responseData = JSON.parse(response);

                    // Check response data status.
                    // Status indicates the success status of battery creating porcess.
                    if (responseData.status) {
                        // Creating new battery was succeeded.
                        showSuccessToast(responseData.message);
                    } else {
                        // Creating new battery was failed.
                        showErrorToast(responseData.message);
                    }

                    // Redirect to battery index page.
                    goToPage("/battery");
                }
            });
        });

        $("#battery-subbrand-form").on("reset", function() {
            goToPage("/battery");
        });
    });
</script>
@endsection

