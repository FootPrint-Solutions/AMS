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
                Vehicle Brand
            </div>
            <br>

            {{-- Form --}}
            <form id="vehicle-brand-form">
                @csrf

                {{-- Name --}}
                <div class="form-group local-forms">
                    <label for="name">Name <span class="login-danger">*</span></label>
                    <input type="text" class="form-control" id="name" name="name"
                        placeholder="Enter vehicle brand name" required
                        @if (isset($data['profile'])) value="{{ $data['profile']['name'] }}" @endif>
                </div>

                {{-- Hidden Inputs --}}
                <input type="hidden" name="id"
                    @if (isset($data['profile'])) value="{{ $data['profile']['id'] }}" @endif>

                {{-- Buttons --}}
                <div class="d-flex flex-row-reverse">
                    {{-- Create Button --}}
                    <button type="submit" class="btn btn-success mx-1" id="btn-save"
                        @if (isset($data['profile'])) value="update">
                    Update Vehicle Brand
                    @else
                    value="create">
                    Create Vehicle Brand @endif
                        </button>

                        {{-- Cancel Button --}}
                        <button type="reset" type="button" class="btn btn-danger mx-1" id="btn-cancel">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let indexUrl = "/vehicle/brand";

        $(document).ready(function() {
            $("#vehicle-brand-form").on("submit", function(event) {
                event.preventDefault();

                let mode = $("#btn-save").attr("value"); // update || create
                let url = (mode == "update") ? "/vehicle/brand/update" : "/vehicle/brand/store";

                // Obtain submitted form data.
                let formData = new FormData($(this)[0]);

                // Send submit POST request via AJAX.
                sendSubmitRequest(url, formData, function() {
                    // Redirect to index page.
                    goToPage(indexUrl);
                });
            });

            $("#vehicle-brand-form").on("reset", function() {
                goToPage(indexUrl);
            });
        });
    </script>
@endsection
