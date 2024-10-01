@extends('template.master')

@section('content')
    <style>
        .local-forms {
            margin-bottom: 1rem;
        }

        .login-danger {
            color: red;
        }
    </style>
    {{-- Page Title --}}

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
                        <strong>Warning!</strong> New account password is same like username.
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


                <center>
                    <table border="1" cellpadding="10" cellspacing="0" class="table table-bordered bg-light">
                        <thead>
                            <tr>
                                <th>Menu</th>
                                <th>View</th>
                                <th>Add</th>
                                <th>Edit</th>
                                <th>Delete</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Loop menu parent and menus --}}
                            @foreach ($data['menu_parent'] as $menu)
                                <tr>
                                    <td colspan="5"><strong>{{ $menu->name }}</strong></td>
                                </tr>

                                @foreach ($menu->menus as $child)
                                    <tr>
                                        <td>{{ $child->name }}</td>
                                        @php
                                            $actions = ['view', 'add', 'edit', 'delete'];
                                        @endphp

                                        {{-- Loop through actions and create checkboxes --}}
                                        @foreach ($actions as $action)
                                            @php
                                                $slugname = str_replace(' ', '_', strtolower($child->name));
                                                $permissions = isset($data['profile']['permission'])
                                                    ? explode('|', $data['profile']['permission'])
                                                    : [];
                                            @endphp
                                            <td>
                                                <input type="checkbox" name="permission[]"
                                                    value="{{ $action }}_{{ $slugname }}"
                                                    id="{{ $child->id }}_{{ $action }}"
                                                    @if (in_array($action . '_' . $slugname, $permissions)) checked @endif>
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </center>



                {{-- Id --}}
                @isset($data['profile'])
                    <input type="hidden" name="id" value="{{ $data['profile']['id'] }}">
                @endisset

                {{-- Buttons --}}
                <div class="d-flex flex-row-reverse mt-5">
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


            // role on change event
            $("#role").on("change", function() {
                let role = $(this).val();
                let checkboxes = $("input[type='checkbox']");
                let developerPermissions = ["view_menu_manager", "add_menu_manager", "edit_menu_manager",
                    "delete_menu_manager"
                ];
                let technicianPermissions = ["view_work_order_instruction", "add_work_order_instruction",
                    "edit_work_order_instruction", "delete_work_order_instruction"
                ];

                checkboxes.prop("checked", false).prop("disabled", false);

                if (role == "user") {
                    checkboxes.prop("checked", true);
                    developerPermissions.forEach(function(permission) {
                        $(`input[type='checkbox'][value='${permission}']`).prop("checked", false)
                            .prop("disabled", true);
                    });
                } else if (role == "developer") {
                    checkboxes.prop("checked", true);
                } else if (role == "technician") {
                    technicianPermissions.forEach(function(permission) {
                        $(`input[type='checkbox'][value='${permission}']`).prop("checked", true);
                    });
                }
            });
        });
    </script>
@endsection
