@extends('template.master')

@section('content')
    {{-- Form --}}
    <div class="card">
        <div class="card-body">
            {{-- Title --}}
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">User Manager</h3>
                    </div>
                </div>
            </div>
            <br>

            <div class="row">
                {{-- Username --}}
                <div class="col">
                    <div class="form-group local-forms">
                        <label for="url">Name</label>
                        <input type="text" class="form-control" id="url" name="url" placeholder="Enter menu url" required
                        @isset($data['profile'])
                            value="{{ $data['profile']['username'] }} - {{ $data['profile']['name'] }}"
                        @endisset
                        >
                    </div>
                </div>
                
                {{-- Name --}}
                <div class="col">
                    <div class="form-group local-forms">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Enter menu name" required
                        @isset($data['profile'])
                            value="{{ $data['profile']['name'] }}"
                        @endisset
                        >
                    </div>
                </div>
            </div>

            {{-- Table --}}
            <table class="table table-striped w-25" id="table-user">
                <tbody>
                    @foreach ($data["menus"] as $menu)
                        <tr>
                            <td><input type="checkbox"></td>
                            <td>{{ $menu["menu_parent"]["name"] }} - {{ $menu["name"] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <script>
        $(document).ready(function() {
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