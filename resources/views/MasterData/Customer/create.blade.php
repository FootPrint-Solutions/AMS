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
                    Edit Customer
                @else
                    Add New Customer
                @endif
            </div>
            <br>

            {{-- Form --}}
            <form id="customer-form">
                @csrf

                {{-- Name --}}
                <div class="form-group local-forms">
                    <label for="name">Name <span class="login-danger">*</span></label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="Enter customer name"
                        required @if (isset($data['profile'])) value="{{ $data['profile']['name'] }}" @endif>
                </div>

                {{-- Address --}}
                <div class="form-group local-forms">
                    <div class="row">
                        <div class="col">
                            <label for="address">Address <span class="login-danger">*</span></label>
                            <input readonly type="text" class="form-control" id="AddressSearchColumn" name="address"
                                placeholder="Enter customer address" required
                                @if (isset($data['profile'])) value="{{ $data['profile']['address'] }}" @endif>

                            <input type="hidden" id="Latitude" name="Latitude"
                                @if (isset($data['profile'])) value="{{ $data['profile']['latitude'] }}" @endif>
                            <input type="hidden" id="Longitude" name="Longitude"
                                @if (isset($data['profile'])) value="{{ $data['profile']['longitude'] }}" @endif>
                        </div>
                        <div class="col">
                            <button type="button" class="btn btn-primary" id="btnAddress">
                                <i class="fa fa-map-marker"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="row">
                    {{-- Contact --}}
                    <div class="col">
                        <div class="form-group local-forms">
                            <label for="contact">Contact <span class="login-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text border-end country-code">+62</span>
                                <input type="tel" pattern="[1-9][0-9]{7,}"
                                    title="At least 8 digits with no leading zero" class="form-control" id="contact"
                                    name="contact" placeholder="Enter customer contact" required
                                    @if (isset($data['profile'])) value="{{ $data['profile']['contact'] }}" @endif>
                            </div>
                        </div>
                    </div>

                    {{-- Email --}}
                    <div class="col">
                        <div class="form-group local-forms">
                            <label for="contact">E-mail</label>
                            <input type="email" class="form-control" id="email" name="email"
                                placeholder="Enter customer e-mail"
                                @if (isset($data['profile'])) value="{{ $data['profile']['email'] }}" @endif>
                        </div>
                    </div>
                </div>

                {{-- Vehicle --}}
                <div class="form-group local-forms">
                    <label for="vehicle">Customer Vehicle</label>
                    <select class="form-control" id="vehicle" name="vehicle[]" multiple="multiple">
                        @foreach ($data['vehicles'] as $vehicle)
                            <option value="{{ $vehicle['id'] }}" @if (isset($data['owned_vehicles']) && in_array($vehicle['id'], $data['owned_vehicles'])) selected @endif>
                                {{ $vehicle['name'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Hidden Inputs --}}
                <input type="hidden" id="id" name="id"
                    @if (isset($data['profile'])) value="{{ $data['profile']['id'] }}" @endif>

                {{-- Buttons --}}
                <div class="d-flex flex-row-reverse">
                    {{-- Create or Update Button --}}
                    <button type="submit" class="btn btn-success mx-1" id="btn-save"
                        @if (isset($data['profile'])) value="update">
                    Update Customer
                    @else
                    value="create">
                    Create Customer @endif
                        </button>

                        {{-- Cancel Button --}}
                        <button type="reset" class="btn btn-danger mx-1" id="btn-cancel">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Address Modal --}}
    @include('maps.addressmodal')

    <script>
        let indexUrl = "/customer";

        $(document).ready(function() {
            $('#vehicle').select2({
                placeholder: "Customer owned vehicles"
            });

            $("#customer-form").on("submit", function(event) {
                event.preventDefault();

                if ($("#AddressSearchColumn").val() == "") {
                    swal.fire("Error!", "Please Fill The Address Column", "error");
                    $("#AddressSearchColumn").focus();
                    return;
                }
                let mode = $("#btn-save").attr("value"); // update || create
                let url = (mode == "update") ? "/customer/update" : "/customer/store";

                // Obtain submitted form data.
                let formData = new FormData($(this)[0]);

                // Send submit POST request via AJAX.
                sendSubmitRequest(url, formData, function() {
                    // Redirect to index page.
                    goToPage(indexUrl);
                });
            });

            $("#customer-form").on("reset", function() {
                goToPage(indexUrl);
            });
        });
    </script>
@endsection
