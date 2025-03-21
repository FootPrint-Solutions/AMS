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
                FAQ
            </div>
            <br>

            {{-- Form --}}
            <form id="faq-form">
                @csrf

                {{-- Question --}}
                <div class="form-group local-forms">
                    <label for="question">Question <span class="login-danger">*</span></label>
                    <input type="text" class="form-control" id="question" name="question" placeholder="Enter question"
                        required @isset($data['profile']) value="{{ $data['profile']['question'] }}" @endisset>
                </div>

                {{-- Answer --}}
                <div class="form-group local-forms">
                    <label for="answer">Answer <span class="login-danger">*</span></label>
                    <textarea class="form-control" id="answer" name="answer" rows="4" placeholder="Enter answer" required>
@isset($data['profile'])
{{ $data['profile']['answer'] }}
@endisset
</textarea>
                </div>

                {{-- Status --}}
                <div class="form-group local-forms d-none">
                    <label for="status">Status <span class="login-danger">*</span></label>
                    <select class="form-control" id="status" name="status" required>
                        <option value="1"
                            @isset($data['profile']) @if ($data['profile']['status'] == 1) selected @endif @endisset>
                            Active</option>
                        <option value="0"
                            @isset($data['profile']) @if ($data['profile']['status'] == 0) selected @endif @endisset>
                            Inactive</option>
                    </select>
                </div>

                {{-- Hidden Inputs --}}
                @isset($data['profile'])
                    <input type="hidden" name="id" value="{{ $data['profile']['id'] }}">
                @endisset

                {{-- Buttons --}}
                <div class="d-flex flex-row-reverse">
                    {{-- Save Button --}}
                    <button type="submit" class="btn btn-success mx-1" id="btn-save"
                        @isset($data['profile']) value="update">Update FAQ</button>
                @else
                value="create">Create FAQ</button>
                @endisset
                        {{-- Cancel Button --}} <button type="reset" type="button" class="btn btn-danger mx-1"
                        id="btn-cancel">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $("#faq-form").on("submit", function(event) {
                event.preventDefault();

                // Disable button
                $("#btn-save").attr("disabled", true);
                $("#btn-save").html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...'
                );

                // Get current display mode (Update or Create).
                let mode = $("#btn-save").attr("value");
                let url = "/faq/store";
                if (mode == "update") {
                    url = "/faq/update";
                }

                // Get FAQ form data.
                let formData = new FormData($(this)[0]);

                // Send submit POST request via AJAX.
                sendSubmitRequest(url, formData, function() {
                    // Redirect to index page.
                    goToPage("/faq");
                });
            });

            $("#faq-form").on("reset", function() {
                goToPage("/faq");
            });
        });
    </script>
@endsection
