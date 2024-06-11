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
                Battery Brand
            </div>
            <br>

            {{-- Form --}}
            <form id="battery-brand-form">
                @csrf

                {{-- Name --}}
                <div class="form-group local-forms">
                    <label for="name">Name <span class="login-danger">*</span></label>
                    <input type="text" class="form-control" id="name" name="name"
                        placeholder="Enter battery brand name" required
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
                    value="update">Update Battery Brand</button>
                @else
                    value="create">Create Battery Brand</button>
                @endisset
                        {{-- Cancel Button --}} <button type="reset" type="button" class="btn btn-danger mx-1"
                        id="btn-cancel">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $("#battery-brand-form").on("submit", function(event) {
                event.preventDefault();

                // disable 
                $("#btn-save").attr("disabled", true);
                $("#btn-save").html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...'
                );

                // Get current display mode (Update or Create).
                let mode = $("#btn-save").attr("value");
                let url = "/battery/brand/store";
                if (mode == "update") {
                    url = "/battery/brand/update";
                }

                // Get battery brand form data.
                let formData = new FormData($(this)[0]);

                // Send submit POST request via AJAX.
                sendSubmitRequest(url, formData, function() {
                    // Redirect to index page.
                    goToPage("/battery/brand");
                });
            });

            $("#battery-brand-form").on("reset", function() {
                goToPage("/battery/brand");
            });
        });
    </script>
@endsection
