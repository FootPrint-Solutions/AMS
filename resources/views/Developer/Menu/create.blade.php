@extends('template.master')

@section('content')
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
                        <input type="text" class="form-control" id="name" name="name" placeholder="Enter menu name" required
                        @isset($data['profile'])
                            value="{{ $data['profile']['name'] }}"
                        @endisset
                        >
                    </div>
                </div>

                {{-- URL --}}
                <div class="col">
                    <div class="form-group local-forms">
                        <label for="url">URL <span class="login-danger">*</span></label>
                        <input type="text" class="form-control" id="url" name="url" placeholder="Enter menu url" required
                        @isset($data['profile'])
                            value="{{ $data['profile']['url'] }}"
                        @endisset
                        >
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
                                <option value="{{ $parent['id'] }}" @if (isset($data['profile']) && $data['profile']['id_parent'] == $parent['id']) selected @endif>{{ $parent['name'] }}</option>
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
                                <option value="{{ $menu['id'] }}" @if (isset($data['profile']) && $data['profile']['order'] == $menu['order'] - 1) selected @endif>{{ $menu['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Hidden Inputs --}}
            @isset($data['profile'])
                <input type="hidden" id="id" name="id" value="{{ $data['profile']['id'] }}">
            @endisset
            
            {{-- Buttons --}}
            <div class="d-flex flex-row-reverse">
                {{-- Create Button --}}
                <button type="submit" class="btn btn-success mx-1" id="btn-save"
                    @if (isset($data['profile']))
                        value="update">
                        Update
                    @else
                        value="create">
                        Create
                    @endif
                    Menu
                </button>

                {{-- Cancel Button --}}
                <button type="reset" class="btn btn-danger mx-1" id="btn-cancel">Cancel</button>
            </div>
        </form>
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

