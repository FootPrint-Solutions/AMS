@extends('template.master')

@section('content')
{{-- Form --}}
<div class="card">
    <div class="card-body">
        {{-- Title --}}
        <div class="card-title h5">
            @isset($data['template'])
            Edit
            @else
            Add New
            @endisset
            template
        </div>
        <br>

        {{-- Form --}}
        <form id="template-master-form">
            @csrf

            {{-- Name --}}
            <div class="row">
                {{-- Name --}}
                <div class="col">
                    <div class="form-group local-forms">
                        <label for="name">Name <span class="login-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Enter Template Name" required autocomplete="off" @if (isset($data['template'])) value="{{ $data['template']['name'] }}" @endif>
                    </div>
                </div>

            </div>

            {{-- Hidden Inputs --}}
            <input type="hidden" id="id" name="id" @if (isset($data['template'])) value="{{ $data['template']['id'] }}" @endif>

            {{-- Buttons --}}
            <div class="d-flex flex-row-reverse">
                {{-- Create or Update Button --}}
                <button type="submit" class="btn btn-success mx-1" id="btn-save" @isset($data['template']) value="update">
                    Update
                    @else
                    value="create">
                    Create @endisset
                    template </button>

                {{-- Cancel Button --}}
                <button type="reset" class="btn btn-danger mx-1" id="btn-cancel">Cancel</button>
            </div>
        </form>
    </div>
</div>

{{-- Form Hanlder --}}
<script>
    let indexUrl = "/template/print";

    $(document).ready(function() {
        $("#template-master-form").on("submit", function(event) {
            event.preventDefault();

            let mode = $("#btn-save").attr("value"); // update || create
            let url = (mode == "update") ? "/template/update" : "/template/store";

            // Obtain submitted form data.
            let message = "";
            let formData = new FormData($(this)[0]);

            sendSubmitRequest(url, formData, function() {
                // Redirect to index page.
                goToPage(indexUrl);
            });
        });

        $("#template-master-form").on("reset", function() {
            goToPage(indexUrl);
        });
    });
</script>
@endsection