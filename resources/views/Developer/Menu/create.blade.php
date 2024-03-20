@extends('template.master')

@section('content')
    <style>
        .row-double {
            padding-right: 15px;
        }
    </style>

    {{-- Form --}}
    <div class="card">
        <div class="card-body">
            {{-- Title --}}
            <div class="card-title h2">
                @isset($data['profile'])
                    Edit
                @else
                    Add New
                @endisset
                Menu
            </div>
            <br>

            {{-- Form --}}
            <form id="menu-form">
                @csrf

                <div class="row">
                    {{-- Name --}}
                    <div class="col">
                        <div class="form-group local-forms">
                            <label for="name">Name <span class="login-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name"
                                placeholder="Enter menu name" required
                                @isset($data['profile'])
                            value="{{ $data['profile']['name'] }}"
                        @endisset>
                        </div>
                    </div>

                    {{-- URL & Hidden --}}
                    <div class="col">
                        <div class="row row-double">
                            {{-- URL --}}
                            <div class="col">
                                <div class="form-group local-forms">
                                    <label for="url">URL <span class="login-danger">*</span></label>
                                    <input type="text" class="form-control" id="url" name="url"
                                        placeholder="Enter menu url" required
                                        @isset($data['profile'])
                                    value="{{ $data['profile']['url'] }}"
                                @endisset>
                                </div>
                            </div>

                            {{-- Hidden --}}
                            <div class="col-sm-1">
                                @if (isset($data['profile']) && $data['profile']['hide'] == 0)
                                    {{-- Shown --}}
                                    <button class="btn btn-light" id="btn-hide" data-hide="0" data-toggle="tooltip"
                                        data-placement="top" title="Menu is shown">
                                        <i class="fa fa-eye" id="btn-hide-icon" aria-hidden="true"></i>
                                    </button>
                                @else
                                    {{-- Hidden --}}
                                    <button class="btn btn-dark" id="btn-hide" data-hide="1" data-toggle="tooltip"
                                        data-placement="top" title="Menu is hidden">
                                        <i class="fa fa-eye-slash" id="btn-hide-icon" aria-hidden="true"></i>
                                    </button>
                                @endif
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Menu Parent & Positioning --}}
                <div class="row">
                    {{-- Menu Parent --}}
                    <div class="col">
                        <div class="form-group local-forms">
                            <label for="menu-parent">Parent <span class="login-danger">*</span></label>
                            <select class="form-control" id="menu-parent" name="menuparent" required>
                                <option></option>
                                @foreach ($data['menu_parents'] as $parent)
                                    <option value="{{ $parent['id'] }}" @if (isset($data['profile']) && $data['profile']['parent_id'] == $parent['id']) selected @endif>
                                        {{ $parent['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- After --}}
                    <div class="col">
                        <div class="form-group local-forms">
                            <label for="menu">Position (before which menu)</label>
                            <select class="form-control" id="menu" name="after">
                                <option></option>
                                @foreach ($data['menus'] as $menu)
                                    <option value="{{ $menu['id'] }}" @if (isset($data['profile']) && $data['profile']['order'] == $menu['order'] - 1) selected @endif>
                                        {{ $menu['name'] }}</option>
                                @endforeach
                                <option value="clear">Clear menu selection</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- SubMenu --}}
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal-submenu">
                    Manage submenus
                </button>

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
                        Menu </button>

                        {{-- Cancel Button --}}
                        <button type="reset" class="btn btn-danger mx-1" id="btn-cancel">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    {{-- SubMenu Modal --}}
    <div id="modal-submenu" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                {{-- Header --}}
                <div class="modal-header">
                    <h4 class="modal-title" id="standard-modalLabel">Manage Submenus</h4>
                    <button class="btn btn-primary mx-2" id="btn-add-submenu">Add</button>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                {{-- Body --}}
                <div class="modal-body">
                    <ul class="list-group list-group-flush" id="list-submenu">
                        @isset($data['profile'])
                            @foreach ($data['profile']['menu_subs'] as $submenu)
                                <li class="list-group-item">
                                    <div class="row">
                                        {{-- Submenu Name --}}
                                        <div class="col">
                                            <input type="text" class="form-control" name="submenuname[]"
                                                placeholder="Enter submenu name" value="{{ $submenu['name'] }}">
                                        </div>

                                        {{-- Submenu Url --}}
                                        <div class="col">
                                            <input type="text" class="form-control" name="submenuurl[]"
                                                placeholder="Enter submenu url" value="{{ $submenu['url'] }}">
                                        </div>

                                        {{-- Remove button --}}
                                        <div class="col-sm-1">
                                            <button class="btn btn-danger"><i class="fa fa-x"></i></button>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        @endisset
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#menu').select2({
                placeholder: "Enter menu"
            });

            $('#menu-parent').select2({
                placeholder: "Enter menu parent"
            });

            $('#btn-hide').on("click", function(e) {
                e.preventDefault();

                if ($(this).data("hide") == 0) {
                    $('#btn-hide-icon').removeClass();
                    $('#btn-hide-icon').addClass("fa fa-eye-slash");

                    $(this).removeClass();
                    $(this).addClass("btn btn-dark");
                    $(this).data("hide", 1);
                    $(this).attr("title", "Menu is hidden");
                } else {
                    $('#btn-hide-icon').removeClass();
                    $('#btn-hide-icon').addClass("fa fa-eye");

                    $(this).removeClass();
                    $(this).addClass("btn btn-light");
                    $(this).data("hide", 0);
                    $(this).attr("title", "Menu is shown");
                }
            });

            $("#menu").on("select2:select", function(e) {
                // Check if user has selected 'Clear menu selection'.
                if (e.params.data.id === "clear") {
                    // Clear current selection.
                    $(this).val(null).trigger("change");
                }
            });

            $("#menu-parent").on("select2:select", function(e) {
                // Obtain selected parent id.
                let parentId = e.params.data.id;

                // Get the list of menus inside the selected parent.
                $.ajax({
                    url: "/menu/get/parent/" + parentId,
                    method: "GET",
                    success: function(response) {
                        // Clear current options and value.
                        $("#menu").empty().val(null).trigger("change");

                        let emptyOption = new Option("", "", false, false);
                        $("#menu").append(emptyOption).trigger("change");

                        response.forEach(function(menu) {
                            // Append new options.
                            let newOption = new Option(menu.name, menu.id, false,
                            false);
                            $("#menu").append(newOption).trigger("change");
                        });

                        let clearOption = new Option("Clear menu selection", "clear", false,
                            false);
                        $("#menu").append(clearOption).trigger("change");
                    }
                });
            });

            $("#btn-add-submenu").on("click", function() {
                let newSubmenuList = "<li class='list-group-item'>" +
                    "<div class='row'>" +
                    // Submenu name
                    "<div class='col'>" +
                    "<input type='text' class='form-control' name='submenuname[]' placeholder='Enter submenu name' value=''>" +
                    "</div>" +

                    // Submenu url
                    "<div class='col'>" +
                    "<input type='text' class='form-control' name='submenuurl[]' placeholder='Enter submenu url' value=''>" +
                    "</div>" +

                    // Remove button
                    "<div class='col-sm-1'>" +
                    "<button class='btn btn-danger'><i class='fa-solid fa-x'></i></button>" +
                    "</div>" +
                    "</div>" +
                    "</li>";
                $("#list-submenu").append(newSubmenuList);
            });

            $("#menu-form").on("submit", function(event) {
                event.preventDefault();

                // Get current display mode (Update or Create).
                let mode = $("#btn-save").attr("value");
                let url = "/menu/store";
                if (mode == "update") {
                    url = "/menu/update";
                }

                // Get form data.
                let formData = new FormData($(this)[0]);
                formData.append('hide', $("#btn-hide").data("hide"));

                // Send form data to Vehicle controller using AJAX.
                $.ajax({
                    url: url,
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        // Get response data (in JSON).
                        let responseData = JSON.parse(response);

                        // Check response data status.
                        // Status indicates the success status of vehicle creating porcess.
                        if (responseData.status) {
                            // Creating or updating process was succeeded.
                            showSuccessToast(responseData.message);
                        } else {
                            // Creating or updating process was failed.
                            showErrorToast(responseData.message);
                        }

                        // Redirect to index page.
                        goToPage("/menu");
                    }
                });
            });

            $("#menu-form").on("reset", function() {
                goToPage("/menu");
            });
        });
    </script>
@endsection
