@extends('template.master')

@section('content')
    <div class="card">
        <div class="card-body">
            {{-- Title --}}
            <div class="card-title h2">
                Company Profile
            </div>
            <br>

            {{-- Form --}}
            <form id="company-form">
                @csrf

                {{-- Company Name --}}
                <div class="form-group local-forms">
                    <label for="company-name">Name <span class="login-danger">*</span></label>
                    <input type="text" class="form-control" id="company-name" name="name"
                        placeholder="Enter company name" value="{{ $data['profile'] ? $data['profile']['name'] : '' }}"
                        required>
                </div>

                {{-- Company Address --}}
                <div class="form-group local-forms">
                    <label for="company-address">Address <span class="login-danger">*</span></label>
                    <input type="text" class="form-control" id="company-address" name="address"
                        placeholder="Enter company address"
                        value="{{ $data['profile'] ? $data['profile']['address'] : '' }}" required>
                </div>

                <div class="row">
                    {{-- Company Contact --}}
                    <div class="col">
                        <div class="form-group local-forms">
                            <label for="company-contact">Contact <span class="login-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text border-end country-code">+62</span>
                                <input type="tel" pattern="[0-9]+" class="form-control" id="company-contact"
                                    name="contact" placeholder="Enter company contact"
                                    value="{{ $data['profile'] ? $data['profile']['contact'] : '' }}" required>
                            </div>
                        </div>
                    </div>

                    {{-- Company E-mail --}}
                    <div class="col">
                        <div class="form-group local-forms">
                            <label for="company-email">E-mail <span class="login-danger">*</span></label>
                            <input type="email" class="form-control" id="company-email" name="email"
                                placeholder="Enter company e-mail"
                                value="{{ $data['profile'] ? $data['profile']['email'] : '' }}" required>
                        </div>
                    </div>
                </div>

                {{-- Buttons --}}
                <div class="d-flex flex-row-reverse">
                    {{-- Save Button --}}
                    <button type="submit" class="btn btn-success mx-1" id="btn-save">Save Company Profile</button>

                    {{-- Reset Button --}}
                    <button type="reset" class="btn btn-danger mx-1" id="btn-reset">Reset</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $("#company-form").on("submit", function(event) {
                event.preventDefault();

                // Obtain submitted form data.
                let formData = new FormData($(this)[0]);

                // Send submit POST request via AJAX.
                sendSubmitRequest("/company/update", formData);
            });

            $("#company-form").on("reset", function() {
                goToPage("/company");
            });
        });
    </script>
@endsection
