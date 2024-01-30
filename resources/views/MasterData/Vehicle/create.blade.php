@extends('template.master')

@section('content')
{{-- Form --}}
<div class="card">
    <div class="card-body">
        {{-- Title --}}
        <div class="card-title h2">
            @if (isset($profile))
                Edit Vehicle
            @else
                Add New Vehicle
            @endif
        </div>
        <br>

        {{-- Form --}}
        <form id="vehicle-form">
            @csrf

            {{-- Name --}}
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Vehicle name" required
                @if (isset($profile))
                    value="{{ $profile['name'] }}"
                @endif
                >
            </div>

            {{-- Brand --}}
            {{-- <div class="form-group">
                <label for="brand">Brand</label>
                <select class="form-select" id="brand">
                    <option>Select car brand</option>
                    <option value="toyota" selected>Toyota</option>
                    <option value="mazda">Mazda</option>
                    <option value="-1">Add New...</option>
                </select>
            </div> --}}

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
        $("#btn-save").on('click', function() {
            let mode = $(this).attr("value"); // Update or Create
            let url = "/vehicle/store";
            if (mode == "update") {
                url = "/vehicle/update";
            }

            // Get vehicle form data.
            let formData = new FormData($('#vehicle-form')[0]);
            
            // Send vehicle form data to Vehicle controller using AJAX.
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

