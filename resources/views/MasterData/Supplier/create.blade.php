@extends('template.master')

@section('content')
    <style>
        #MapsAddressFinder {
            height: 400px;
            width: 100%;
            margin-bottom: 20px;
        }
    </style>
    {{-- Form --}}
    <div class="card">
        <div class="card-body">
            {{-- Title --}}
            <div class="card-title h5">
                @if (isset($data['profile']))
                    Edit Supplier
                @else
                    Add New Supplier
                @endif
            </div>
            <br>

            {{-- Form --}}
            <form id="supplier-form">
                @csrf

                {{-- Name --}}
                <div class="form-group local-forms">
                    <label for="name">Name <span class="login-danger">*</span></label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="Enter supplier name"
                        required @if (isset($data['profile'])) value="{{ $data['profile']['name'] }}" @endif
                        autocomplete="off">
                </div>

                {{-- Address --}}
                <div class="form-group local-forms">
                    <div class="row">
                        <div class="col">
                            <label for="address">Address</label>
                            <input type="text" class="form-control" id="AddressSearchColumn" name="address"
                                placeholder="Enter supplier address"
                                @if (isset($data['profile'])) value="{{ $data['profile']['address'] }}" @endif
                                autocomplete="off">

                            <input type="hidden" id="Latitude" name="Latitude"
                                @if (isset($data['profile'])) value="{{ $data['profile']['latitude'] }}" @endif>
                            <input type="hidden" id="Longitude" name="Longitude"
                                @if (isset($data['profile'])) value="{{ $data['profile']['longitude'] }}" @endif>
                        </div>

                        {{-- Contact --}}
                        <div class="col">
                            <div class="form-group local-forms">
                                <label for="contact">Contact </label>
                                <div class="input-group">
                                    <span class="input-group-text border-end country-code">+62</span>
                                    <input type="tel" pattern="[1-9][0-9]{7,}"
                                        title="At least 8 digits with no leading zero" class="form-control" id="contact"
                                        name="contact" placeholder="Enter supplier contact"
                                        @if (isset($data['profile'])) value="{{ $data['profile']['contact'] }}" @endif
                                        autocomplete="off">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">


                    {{-- Email --}}
                    <div class="col">
                        <div class="form-group local-forms">
                            <label for="contact">E-mail</label>
                            <input type="email" class="form-control" id="email" name="email"
                                placeholder="Enter supplier e-mail"
                                @if (isset($data['profile'])) value="{{ $data['profile']['email'] }}" @endif
                                autocomplete="off">
                        </div>
                    </div>
                    <div class="col"></div>
                </div>


                {{-- Hidden Inputs --}}
                <input type="hidden" id="id" name="id"
                    @if (isset($data['profile'])) value="{{ $data['profile']['id'] }}" @endif>

                {{-- Buttons --}}
                <div class="d-flex flex-row-reverse">
                    {{-- Create or Update Button --}}
                    <button type="submit" class="btn btn-success mx-1" id="btn-save"
                        @if (isset($data['profile'])) value="update">
                    Update Supplier
                    @else
                    value="create">
                    Create Supplier @endif
                        </button>

                        {{-- Cancel Button --}}
                        <button type="reset" class="btn btn-danger mx-1" id="btn-cancel">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let indexUrl = "/supplier";

        $(document).ready(function() {
            $('#vehicle').select2({
                placeholder: "Supplier owned vehicles"
            });

            $("#supplier-form").on("submit", function(event) {
                event.preventDefault();

                swal.fire({
                    title: "Processing...",
                    text: "Please wait while we save your data.",
                    allowOutsideClick: false,
                    didOpen: () => {
                        swal.showLoading();
                    }
                });

                let mode = $("#btn-save").attr("value"); // update || create
                let url = (mode == "update") ? "/supplier/update" : "/supplier/store";

                // Obtain submitted form data.
                let formData = new FormData($(this)[0]);

                // Send submit POST request via AJAX.
                sendSubmitRequest(url, formData, function() {
                    // Redirect to index page.
                    goToPage(indexUrl);
                });
            });

            $("#supplier-form").on("reset", function() {
                goToPage(indexUrl);
            });
        });
    </script>
@endsection
