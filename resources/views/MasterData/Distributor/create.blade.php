@extends('template.master')

@section('content')
    {{-- Form --}}
    <div class="card">
        <div class="card-body">
            {{-- Title --}}
            <div class="card-title h2">
                @if (isset($data['profile']))
                    Edit Distributor
                @else
                    Add New Distributor
                @endif
            </div>
            <br>

            {{-- Form --}}
            <form id="distributor-form">
                @csrf

                {{-- Name --}}
                <div class="form-group local-forms">
                    <label for="distributor-name">Name <span class="login-danger">*</span></label>
                    <input type="text" class="form-control" id="distributor-name" name="name"
                        placeholder="Enter distributor name" required
                        @isset($data['profile'])
                    value="{{ $data['profile']['name'] }}"
                @endisset>
                </div>

                {{-- Address and Is Shop --}}
                <div class="row">
                    <div class="col">
                        {{-- Address --}}
                        <div class="row">
                            <div class="col">
                                <div class="form-group local-forms">
                                    <label for="distributor-address">Address <span class="login-danger">*</span></label>
                                    <input readonly type="text" class="form-control" id="AddressSearchColumn"
                                        name="address" placeholder="Enter distributor address" required
                                        @isset($data['profile'])
                                    value="{{ $data['profile']['address'] }}"
                                @endisset>

                                    <input type="hidden" id="Latitude" name="Latitude">
                                    <input type="hidden" id="Longitude" name="Longitude">
                                </div>
                            </div>

                            <div class="col-sm-1">
                                <button type="button" class="btn btn-primary" id="btnAddress">
                                    <i class="fa fa-map-marker"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Is Shop --}}
                    <div class="col-sm-1">
                        <input class="form-check-input" type="checkbox" value="" id="isshop"
                            @if (isset($data['profile']) && $data['profile']['is_shop'] == 1) checked
                    @endisset
                    >
                    <label class="form-check-label" for="isshop">
                        Is shop
                    </label>
                </div>
            </div>

            {{-- Contact Person, Contact and Email --}}
            <div class="row">
                {{-- Contact Person --}}
                <div class="col">
                    <div class="form-group local-forms">
                        <label for="distributor-contact-person">Contact Person <span class="login-danger">*</span></label>
                        <input type="text" class="form-control" id="distributor-contact-person" name="contactperson" placeholder="Enter distributor contact person name" required
                        @isset($data['profile'])
                            value="{{ $data['profile']['address'] }}"
                        @endisset
                        >
                    </div>
                </div>
                
                {{-- Contact --}}
                <div class="col">
                    <div class="form-group local-forms">
                        <label for="distributor-contact">Contact <span class="login-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text border-end country-code">+62</span>
                            <input type="tel" pattern="[0-9]+" class="form-control" id="distributor-contact" name="contact" placeholder="Enter distributor contact" required
                            @isset($data['profile'])
                                value="{{ $data['profile'] ? $data['profile']['contact'] : '' }}"
                            @endisset
                            >
                        </div>
                    </div>
                </div>

                {{-- E-mail --}}
                <div class="col">
                    <div class="form-group local-forms">
                        <label for="distributor-email">E-mail</label>
                        <input type="email" class="form-control" id="distributor-email" name="email" placeholder="Enter distributor e-mail"
                        @isset($data['profile'])
                            value="{{ $data['profile'] ? $data['profile']['email'] : '' }}"
                        @endisset
                        >
                    </div>
                </div>
            </div>

            {{-- Note --}}
            <div class="form-group local-forms">
                <label for="note">Note</label>
                <textarea type="text" class="form-control" id="note" name="note" placeholder="Enter some notes regarding the distributor">@if (isset($data['profile']) && !empty($data['profile']['note'])) {{ $data['profile']['note'] }} @endif</textarea>
                    </div>

                    {{-- Hidden Inputs --}}
                    @isset($data['profile'])
                        <input type="hidden" id="id" name="id" value="{{ $data['profile']['id'] }}">
                    @endisset

                    {{-- Buttons --}}
                    <div class="d-flex flex-row-reverse">
                        {{-- Create Button --}}
                        <button type="submit" class="btn btn-success mx-1" id="btn-save"
                            @if (isset($data['profile'])) value="update">
                        Update
                    @else
                        value="create">
                        Create @endif
                            Distributor </button>

                            {{-- Cancel Button --}}
                            <button type="reset" class="btn btn-danger mx-1" id="btn-cancel">Cancel</button>
                    </div>
            </form>
        </div>
    </div>

    {{-- Address Modal --}}
    @include('maps.addressmodal')

    <script>
        $(document).ready(function() {
            $("#distributor-form").on("submit", function(event) {
                event.preventDefault();

                // Get current display mode (Update or Create).
                let mode = $("#btn-save").attr("value");
                let url = "/distributor/store";
                if (mode == "update") {
                    url = "/distributor/update";
                }

                // Get form data.
                let formData = new FormData($(this)[0]);
                formData.append("isshop", $("#isshop").is(":checked") ? 1 : 0);

                // Send form data to controller using AJAX.
                $.ajax({
                    url: url,
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        // Get response data (in JSON).
                        let responseData = JSON.parse(response);

                        // Check response data status (0 || 1).
                        if (responseData.status) {
                            // Creating or updating process was succeeded.
                            showSuccessToast(responseData.message);
                        } else {
                            // Creating or updating process was failed.
                            showErrorToast(responseData.message);
                        }

                        // Redirect to index page.
                        goToPage("/distributor");
                    }
                });
            });

            $("#distributor-form").on("reset", function() {
                goToPage("/distributor");
            });
        });
    </script>
@endsection
