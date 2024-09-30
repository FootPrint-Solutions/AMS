@extends('template.master')

@section('content')
    @php
        $profileLabel = isset($data['profile']) ? 'Edit' : 'Create';
    @endphp

    {{-- Form --}}
    <div class="card shadow">
        <div class="card-body">
            {{-- Title --}}
            <div class="card-title h5">
                @isset($data['profile'])
                    Update
                @else
                    Create New
                @endisset
                User
            </div>
            <br>

            {{-- Form --}}
            <form id="form-user">
                @csrf

                @isset($data['profile'])
                @else
                    <div class="alert alert-warning" role="alert">
                        <strong>Warning!</strong> New account password is username + 123.
                    </div>
                @endisset

                {{-- Name & Code --}}
                <div class="row">
                    {{-- Name --}}
                    <div class="col">
                        <div class="form-group local-forms">
                            <label for="name">Name <span class="login-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name"
                                placeholder="Enter user name" required
                                @isset($data['profile']) value="{{ $data['profile']['name'] }}" readonly @endisset>
                        </div>
                    </div>

                    {{-- Role --}}
                    <div class="col">
                        <div class="form-group local-forms">
                            <label for="role">Role <span class="login-danger">*</span></label>
                            <select id="role" name="role" class="form-control">
                                <option value="user" @if (isset($data['profile']) && $data['profile']['level'] == 'user') selected @endif>User</option>
                                <option value="developer" @if (isset($data['profile']) && $data['profile']['level'] == 'developer') selected @endif>Developer
                                </option>
                                <option value="technician" @if (isset($data['profile']) && $data['profile']['level'] == 'technician') selected @endif>Technician
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Username & E-mail --}}
                <div class="row">
                    {{-- Username --}}
                    <div class="col">
                        <div class="form-group local-forms">
                            <label for="username">Username <span class="login-danger">*</span></label>
                            <input type="text" class="form-control" id="username" name="username"
                                placeholder="Enter user username" required
                                @isset($data['profile']) value="{{ $data['profile']['username'] }}" readonly @endisset>
                        </div>
                    </div>

                    {{-- E-mail --}}
                    <div class="col">
                        <div class="form-group local-forms">
                            <label for="email">E-mail</label>
                            <input type="email" class="form-control" id="email" name="email"
                                placeholder="Enter user e-mail" required
                                @isset($data['profile']) value="{{ $data['profile']['email'] }}" readonly @endisset>
                        </div>
                    </div>
                </div>

                {{-- Id --}}
                @isset($data['profile'])
                    <input type="hidden" name="id" value="{{ $data['profile']['id'] }}">
                @endisset

                {{-- Buttons --}}
                <div class="d-flex flex-row-reverse">
                    <button type="submit" class="btn btn-success mx-1" id="btn-save"
                        @isset($data['profile'])
                    value="update">
                        Update
                    @else
                    value="create">
                        Create
                    @endisset
                        User</button>
                        <button type="reset" class="btn btn-danger mx-1">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $("#role").select2({});
        })
    </script>

    {{-- Form Handler --}}
    <script>
        $(document).ready(function() {
            $("#form-user").on("submit", function(event) {
                event.preventDefault();

                let mode = $("#btn-save").attr("value"); // update || create
                let url = (mode == "update") ? "/user-manager/update" :
                    "/user-manager/store";

                // Obtain submitted form data.
                let formData = new FormData($(this)[0]);

                // Send submit POST request via AJAX.
                sendSubmitRequest(url, formData, function() {
                    // Redirect to index page.
                    goToPage("/user-manager");
                });
            });

            $("#form-user").on("reset", function() {
                goToPage("/user-manager");
            });
        });
    </script>
@endsection
