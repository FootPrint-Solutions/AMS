@extends('template.master')

@section('content')
    {{-- Form --}}
    <div class="card">
        <div class="card-body">
            {{-- Title --}}
            <div class="card-title h5">
                @isset($data['profile'])
                    Edit
                @else
                    Add New
                @endisset
                Vehicle Year
            </div>
            <br>

            {{-- Form --}}
            <form id="vehicle-Year-form">
                @csrf

                {{-- Start Year --}}
                <div class="form-group local-forms">
                    <label for="name">Start Year <span class="login-danger">*</span></label>
                    <input type="number" class="form-control" id="start_year" name="start_year"
                        placeholder="Enter vehicle Start Year" required
                        @if (isset($data['profile'])) value="{{ $data['profile']['start_year'] }}" @endif>
                </div>

                {{-- End Year --}}
                <div class="form-group local-forms">
                    <label for="name">End Year <span class="login-danger">*</span></label>
                    <input type="number" class="form-control" id="end_year" name="end_year"
                        placeholder="Enter vehicle End Year" required
                        @if (isset($data['profile'])) value="{{ $data['profile']['end_year'] }}" @endif>
                </div>

                {{-- Hidden Inputs --}}
                <input type="hidden" name="id"
                    @if (isset($data['profile'])) value="{{ $data['profile']['id'] }}" @endif>

                {{-- Buttons --}}
                <div class="d-flex flex-row-reverse">
                    {{-- Create Button --}}
                    <button type="submit" class="btn btn-success mx-1" id="btn-save"
                        @if (isset($data['profile'])) value="update">
                    Update Vehicle Year
                    @else
                    value="create">
                    Create Vehicle Year @endif
                        </button>

                        {{-- Cancel Button --}}
                        <button type="reset" type="button" class="btn btn-danger mx-1" id="btn-cancel">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let indexUrl = "/vehicle/year";

        $(document).ready(function() {
            $("#vehicle-Year-form").on("submit", function(event) {
                event.preventDefault();

                // disable
                $("#btn-save").attr("disabled", true);
                $("#btn-save").html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...'
                );

                let mode = $("#btn-save").attr("value"); // update || create
                let url = (mode == "update") ? "/vehicle/year/update" : "/vehicle/year/store";

                // Obtain submitted form data.
                let formData = new FormData($(this)[0]);

                // Send submit POST request via AJAX.
                sendSubmitRequest(url, formData, function() {
                    // Redirect to index page.
                    // enable 
                    $("#btn-save").attr("disabled", false);
                    $("#btn-save").html(
                        (mode == "update") ? "Update Vehicle Year" : "Create Vehicle Year"
                    );
                    goToPage(indexUrl);
                });
            });

            $("#vehicle-Year-form").on("reset", function() {
                goToPage(indexUrl);
            });
        });
    </script>
@endsection
