@extends('template.master')

@section('content')
    {{-- Form --}}
    <div class="card">
        <div class="card-body">
            {{-- Title --}}
            <div class="card-title h5">
                @if (isset($data['profile']))
                    Edit Shop
                @else
                    Add New Shop
                @endif
            </div>
            <br>

            {{-- Form --}}
            <form id="distributor-shop-form">
                @csrf

                {{-- Name & Distributor --}}
                <div class="row">
                    {{-- Name --}}
                    <div class="col">
                        <div class="form-group local-forms">
                            <label for="name">Name <span class="login-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name"
                                placeholder="Enter shop name" required
                                @if (isset($data['profile'])) value="{{ $data['profile']['name'] }}" @endif>
                        </div>
                    </div>

                    {{-- Distributor --}}
                    <div class="col">
                        <div class="form-group local-forms">
                            <label for="distributor">Distributor <span class="login-danger">*</span></label>
                            <select class="form-control" id="distributor" name="distributor" required>
                                <option></option>
                                @foreach ($data['distributors'] as $distributor)
                                    <option value="{{ $distributor['id'] }}"
                                        @if (isset($data['profile']) && $data['profile']['distributor_id'] == $distributor['id']) selected @endif>{{ $distributor['name'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Address --}}
                <div class="row">
                    <div class="col">
                        <div class="form-group local-forms">
                            <label for="distributor-address">Address <span class="login-danger">*</span></label>
                            <input readonly type="text" class="form-control" id="AddressSearchColumn" name="address"
                                placeholder="Enter distributor address" required
                                @isset($data['profile']) value="{{ $data['profile']['address'] }}" @endisset>

                            <input type="hidden" id="Latitude" name="Latitude"
                                @if (isset($data['profile'])) value="{{ $data['profile']['latitude'] }}" @endif>
                            <input type="hidden" id="Longitude" name="Longitude"
                                @if (isset($data['profile'])) value="{{ $data['profile']['longitude'] }}" @endif>
                        </div>
                    </div>

                    {{-- Map Marker --}}
                    <div class="col-sm-auto">
                        <button type="button" class="btn btn-primary" id="btnAddress">
                            <i class="fa fa-map-marker"></i>
                        </button>
                    </div>
                </div>

                {{-- Contact Person, Contact and Email --}}
                <div class="row">
                    {{-- Contact Person --}}
                    <div class="col">
                        <div class="form-group local-forms">
                            <label for="contact-person">Contact Person <span class="login-danger">*</span></label>
                            <input type="text" class="form-control" id="contact-person" name="contactperson"
                                placeholder="Enter shop contact person name" required
                                @isset($data['profile']) value="{{ $data['profile']['address'] }}" @endisset>
                        </div>
                    </div>

                    {{-- Contact --}}
                    <div class="col">
                        <div class="form-group local-forms">
                            <label for="contact">Contact <span class="login-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text border-end country-code">+62</span>
                                <input type="tel" pattern="[0-9]+" class="form-control" id="contact" name="contact"
                                    placeholder="Enter shop contact" required
                                    @isset($data['profile']) value="{{ $data['profile'] ? $data['profile']['contact'] : '' }}" @endisset>
                            </div>
                        </div>
                    </div>

                    {{-- E-mail --}}
                    <div class="col">
                        <div class="form-group local-forms">
                            <label for="email">E-mail</label>
                            <input type="email" class="form-control" id="email" name="email"
                                placeholder="Enter shop e-mail"
                                @isset($data['profile']) value="{{ $data['profile'] ? $data['profile']['email'] : '' }}" @endisset>
                        </div>
                    </div>
                </div>

                {{-- Note --}}
                <div class="form-group local-forms">
                    <label for="note">Note</label>
                    <textarea type="text" class="form-control" id="note" name="note"
                        placeholder="Enter some notes regarding the shop">
@if (isset($data['profile']) && !empty($data['profile']['note']))
{{ $data['profile']['note'] }}
@endif
</textarea>
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
                        Shop </button>

                        {{-- Cancel Button --}}
                        <button type="reset" class="btn btn-danger mx-1" id="btn-cancel">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Address Modal --}}
    @include('maps.addressmodal')

    <script>
        let indexUrl = "/distributor/shop";

        $(document).ready(function() {
            $('#distributor').select2({
                placeholder: "Enter distributor brand"
            });

            $("#distributor-shop-form").on("submit", function(event) {
                event.preventDefault();
                if ($("#AddressSearchColumn").val() == "") {
                    swal.fire("Error!", "Please Fill The Address Column", "error");
                    $("#AddressSearchColumn").focus();
                    return;
                }
                let mode = $("#btn-save").attr("value"); // update || create
                let url = (mode == "update") ? "/distributor/shop/update" : "/distributor/shop/store";

                // Obtain submitted form data.
                let formData = new FormData($(this)[0]);

                // Send submit POST request via AJAX.
                sendSubmitRequest(url, formData, function() {
                    // Redirect to index page.
                    goToPage(indexUrl);
                });
            });

            $("#distributor-shop-form").on("reset", function() {
                goToPage(indexUrl);
            });
        });
    </script>
@endsection
