@extends('template.master')

@section('content')
{{-- Form --}}
<div class="card">
    <div class="card-body">
        {{-- Title --}}
        <div class="card-title h2">
            @if (isset($data['profile']))
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
            <div class="form-group local-forms">
                <label for="name">Name <span class="login-danger">*</span></label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Enter vehicle name" required
                @if (isset($data['profile']))
                    value="{{ $data['profile']['name'] }}"
                @endif
                >
            </div>

            {{-- URL --}}
            <div class="form-group local-forms">
                <label for="url">URL</label>
                <input type="url" pattern="https?://.+" class="form-control" id="url" name="url" placeholder="Enter vehicle url link"
                @if (isset($data['profile']))
                    value="{{ $data['profile']['url'] }}"
                @endif
                >
            </div>

            {{-- Brand --}}
            <div class="form-group local-forms">
                <label for="brand">Brand <span class="login-danger">*</span></label>
                <select class="form-control" id="brand" name="brand">
                    @foreach ($data['brands'] as $brand)
                        <option value="{{ $brand['id'] }}" @if (isset($data['profile']) && $data['profile']['id_brand'] == $brand['id']) selected @endif>{{ $brand['name'] }}</option>
                    @endforeach
                </select>
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
                    Vehicle
                </button>

                {{-- Cancel Button --}}
                <button type="reset" class="btn btn-danger mx-1" id="btn-cancel">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#vehicle').select2({
            placeholder: "Enter vehicle brand"
        });

        $("#vehicle-form").on("submit", function(event) {
            event.preventDefault();

            // Get current display mode (Update or Create).
            let mode = $("#btn-save").attr("value");
            let url = "/vehicle/store";
            if (mode == "update") {
                url = "/vehicle/update";
            }

            // Get form data.
            let formData = new FormData($(this)[0]);
            
            // Send form data to Vehicle controller using AJAX.
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
                        // Creating or updating process was succeeded.
                        showSuccessToast(responseData.message);
                    } else {
                        // Creating or updating process was failed.
                        showErrorToast(responseData.message);
                    }

                    // Redirect to index page.
                    goToPage("/vehicle");
                }
            });
        });

        $("#vehicle-form").on("reset", function() {
            goToPage("/vehicle");
        });
    });
</script>
@endsection

