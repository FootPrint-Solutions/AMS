@extends('template.master')

@section('content')
    {{-- Form --}}
    <div class="card">
        <div class="card-body">
            {{-- Title --}}
            <div class="card-title h2">
                @if (isset($data['profile']))
                    Edit
                @else
                    Add New
                @endif
                Technician
            </div>
            <br>

            {{-- Form --}}
            <form id="technician-form">
                @csrf

                {{-- Name & Shop --}}
                <div class="row">
                    {{-- Name --}}
                    <div class="col">
                        <div class="form-group local-forms">
                            <label for="name">Name <span class="login-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name"
                                placeholder="Enter vehicle name" required
                                @if (isset($data['profile'])) value="{{ $data['profile']['name'] }}" @endif>
                        </div>
                    </div>

                    {{-- Shop --}}
                    <div class="col">
                        <div class="form-group local-forms">
                            <label for="shop">Shop <span class="login-danger">*</span></label>
                            <select class="form-control" id="shop" name="shop" required>
                                <option></option>
                                @foreach ($data['shops'] as $shop)
                                    <option value="{{ $shop['id'] }}" @if (isset($data['profile']) && $data['profile']['distributor_shop_id'] == $shop['id']) selected @endif>
                                        {{ $shop['distributor']['name'] . ' - ' . $shop['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Contact and Email --}}
                <div class="row">
                    {{-- Contact --}}
                    <div class="col">
                        <div class="form-group local-forms">
                            <label for="contact">Contact <span class="login-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text border-end country-code">+62</span>
                                <input type="tel" pattern="[0-9]+" class="form-control" id="contact" name="contact"
                                    placeholder="Enter technician contact" required
                                    @isset($data['profile'])
                                value="{{ $data['profile'] ? $data['profile']['contact'] : '' }}"
                            @endisset>
                            </div>
                        </div>
                    </div>

                    {{-- E-mail --}}
                    <div class="col">
                        <div class="form-group local-forms">
                            <label for="email">E-mail</label>
                            <input type="email" class="form-control" id="email" name="email"
                                placeholder="Enter technician e-mail"
                                @isset($data['profile'])
                            value="{{ $data['profile'] ? $data['profile']['email'] : '' }}"
                        @endisset>
                        </div>
                    </div>
                </div>

                {{-- Note --}}
                <div class="form-group local-forms">
                    <label for="note">Note</label>
                    <textarea type="text" class="form-control" id="note" name="note" placeholder="Enter some notes regarding the technician">@if (isset($data['profile']) && !empty($data['profile']['note'])){{ $data['profile']['note'] }}@endif</textarea>
                </div>

                {{-- Hidden Inputs --}}
                <input type="hidden" id="id" name="id"
                    @if (isset($data['profile'])) value="{{ $data['profile']['id'] }}" @endif>

                {{-- Buttons --}}
                <div class="d-flex flex-row-reverse">
                    {{-- Create Button --}}
                    <button type="submit" class="btn btn-success mx-1" id="btn-save"
                        @if (isset($data['profile'])) value="update">
                        Update
                    @else
                        value="create">
                        Create @endif
                        Technician </button>

                        {{-- Cancel Button --}}
                        <button type="reset" class="btn btn-danger mx-1" id="btn-cancel">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let indexUrl = "/distributor/technician";

        $(document).ready(function() {
            $('#shop').select2({
                placeholder: "Enter technician shop"
            });

            $("#technician-form").on("submit", function(event) {
                event.preventDefault();

                let mode = $("#btn-save").attr("value"); // update || create
                let url = (mode == "update") ? "/distributor/technician/update" : "/distributor/technician/store";

                // Obtain submitted form data.
                let formData = new FormData($(this)[0]);

                // Send submit POST request via AJAX.
                sendSubmitRequest(url, formData, function() {
                    // Redirect to index page.
                    goToPage(indexUrl);
                });
            });

            $("#technician-form").on("reset", function() {
                goToPage(indexUrl);
            });
        });
    </script>
@endsection
