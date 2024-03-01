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
            Menu Parent
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
                        <input type="text" class="form-control" id="name" name="name" placeholder="Enter menu parent name" required
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
                        <input type="text" class="form-control" id="url" name="url" placeholder="Enter menu parent url" required
                        @isset($data['profile'])
                            value="{{ $data['profile']['url'] }}"
                        @endisset
                        >
                    </div>
                </div>
            </div>

            {{-- Menu Parent & Positioning --}}
            <div class="row">
                {{-- After --}}
                <div class="col">
                    <div class="form-group local-forms">
                        <label for="menu-parent">Position (before which menu parent)</label>
                        <select class="form-control" id="menu-parent" name="after">
                            <option></option>
                            @foreach ($data['menu_parents'] as $parent)
                                <option value="{{ $parent['id'] }}" @if (isset($data['profile']) && $data['profile']['order'] == $parent['order'] - 1) selected @endif>{{ $parent['name'] }}</option>
                            @endforeach
                            <option value="clear">Clear menu selection</option>
                        </select>
                    </div>
                </div>

                {{-- Icon & Preview --}}
                <div class="col">
                    <div class="row">
                        {{-- Icon --}}
                        <div class="col">
                            <div class="form-group local-forms">
                                <label for="url">Icon Class <span class="login-danger">*</span></label>
                                <input type="text" class="form-control" id="icon" name="icon" placeholder="Enter menu parent icon (eg. fa fa-times)" required
                                @isset($data['profile'])
                                    value="{{ $data['profile']['icon'] }}"
                                @endisset
                                >
                            </div>
                        </div>

                        {{-- Preview --}}
                        <div class="col-sm-1">
                            <div class="border rounded bg-dark text-white h-50 d-flex justify-content-center align-items-center">
                                <span class="align-middle"><i class="fa fa-times" id="icon-preview" aria-hidden="true"></i></span>
                            </div>
                        </div>                        
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
                    Menu Parent
                </button>

                {{-- Cancel Button --}}
                <button type="reset" class="btn btn-danger mx-1" id="btn-cancel">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#menu-parent').select2({
            placeholder: "Enter menu parent"
        });

        $("#menu-parent").on("select2:select", function (e) {
            // Check if user has selected 'Clear menu selection'.
            if (e.params.data.id === "clear") {
                // Clear current selection.
                $(this).val(null).trigger("change");
            }
        });

        $("#icon").on("keyup", function() {
            $("#icon-preview").removeClass();
            $("#icon-preview").addClass($(this).val());
        });

        $("#menu-form").on("submit", function(event) {
            event.preventDefault();

            // Get current display mode (Update or Create).
            let mode = $("#btn-save").attr("value");
            let url = "/menu/parent/store";
            if (mode == "update") {
                url = "/menu/parent/update";
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

