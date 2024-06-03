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
                Tax
            </div>
            <br>

            {{-- Form --}}
            <form id="tax-form">
                @csrf

                {{-- Percentage & Valid Until --}}
                <div class="row">
                    {{-- Percentage --}}
                    <div class="col">
                        <div class="form-group local-forms">
                            <label for="percentage">Percentage <span class="login-danger">*</span></label>
                            <input type="number" class="form-control" id="percentage" name="percentage"
                                placeholder="Enter tax percentage" required
                                @if (isset($data['profile'])) value="{{ $data['profile']['percentage'] }}" @endif>
                        </div>
                    </div>

                    {{-- Valid From --}}
                    <div class="col">
                        <div class="form-group local-forms">
                            <label for="valid-from">Valid From<span class="login-danger">*</span></label>
                            <input type="date" class="form-control" id="valid-from" name="validfrom" required
                                value=@isset($data['profile'])) {{ $data['profile']['valid_from'] }} @else {{ date('Y-m-d') }} @endisset>
                        </div>
                    </div>
                </div>

                {{-- Hidden Inputs --}}
                <input type="hidden" id="id" name="id"
                    @if (isset($data['profile'])) value="{{ $data['profile']['id'] }}" @endif>

                {{-- Buttons --}}
                <div class="d-flex flex-row-reverse">
                    {{-- Create or Update Button --}}
                    <button type="submit" class="btn btn-success mx-1" id="btn-save"
                        @isset($data['profile']) value="update">
                    Update
                    @else
                    value="create">
                    Create @endisset
                        Tax </button>

                        {{-- Cancel Button --}}
                        <button type="reset" class="btn btn-danger mx-1" id="btn-cancel">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Address Modal --}}
    @include('maps.addressmodal')

    {{-- Form Hanlder --}}
    <script>
        let indexUrl = "/tax";

        $(document).ready(function() {
            $("#tax-form").on("submit", function(event) {
                event.preventDefault();

                let mode = $("#btn-save").attr("value"); // update || create
                let url = (mode == "update") ? "/tax/update" : "/tax/store";

                // Obtain submitted form data.
                let message = "";
                let formData = new FormData($(this)[0]);
                if (mode == "update") {
                    let status = $("#isactive").is(':checked') ? "active" :
                        "inactive";
                    formData.append("status", status);

                    if (status === "active") {
                        message =
                            "When updating the status of a tax to active, all other taxes will be automatically set inactive.";
                    }
                } else {
                    message =
                        "When creating a new tax, all other taxes will be automatically set inactive.";
                }

                if (message === "") {
                    // Send submit POST request via AJAX.
                    sendSubmitRequest(url, formData, function() {
                        // Redirect to index page.
                        goToPage(indexUrl);
                    });
                } else {
                    // Show an alert before storing an item.
                    Swal.fire({
                        title: "Are you sure?",
                        text: message,
                        icon: "question",
                        showCancelButton: true,
                        reverseButtons: true,
                        confirmButtonText: "Yes, " + mode + "!",
                        cancelButtonText: "No, cancel!"
                    }).then(function(e) {
                        // If user has confirmed, do the destroy process.
                        if (e.value === true) {
                            // Send submit POST request via AJAX.
                            sendSubmitRequest(url, formData, function() {
                                // Redirect to index page.
                                goToPage(indexUrl);
                            });
                        }
                    });
                }
            });

            $("#tax-form").on("reset", function() {
                goToPage(indexUrl);
            });
        });
    </script>
@endsection
