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
                Battery Subbrand Category
            </div>
            <br>

            {{-- Form --}}
            <form id="battery-subbrand-form">
                @csrf

                {{-- Name --}}
                <div class="form-group local-forms">
                    <label for="name">Name <span class="login-danger">*</span></label>
                    <input type="text" class="form-control" id="name" name="name"
                        placeholder="Enter battery subbrand name" required
                        @isset($data['profile'])
                    value="{{ $data['profile']['name'] }}"
                @endisset>
                </div>

                {{-- Hidden Inputs --}}
                @isset($data['profile'])
                    <input type="hidden" name="id" value="{{ $data['profile']['id'] }}">
                @endisset

                {{-- Buttons --}}
                <div class="d-flex flex-row-reverse">
                    {{-- Create Button --}}
                    <button type="submit" class="btn btn-success mx-1" id="btn-save"
                        @isset($data['profile'])
                    value="update">Update Battery Subbrand</button>
                @else
                    value="create">Create Battery Subbrand</button>
                @endisset
                        {{-- Cancel Button --}} <button type="reset" type="button" class="btn btn-danger mx-1"
                        id="btn-cancel">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $("#battery-subbrand-form").on("submit", function(event) {
                event.preventDefault();

                // Get current display mode (Update or Create).
                let mode = $("#btn-save").attr("value");
                let url = "/battery/subbrand/store";
                if (mode == "update") {
                    url = "/battery/subbrand/update";
                }

                // Get battery brand form data.
                let formData = new FormData($(this)[0]);

                // Send submit POST request via AJAX.
                sendSubmitRequest(url, formData, function() {
                    // Redirect to index page.
                    goToPage("/battery/subbrand");
                });
            });

            $("#battery-subbrand-form").on("reset", function() {
                goToPage("/battery/subbrand");
            });
        });
    </script>
@endsection
