@extends('template.master')

@section('content')
    <div class="card">
        <div class="card-body">
            {{-- Title --}}
            <div class="card-title h5">
                Edit Inventory
            </div>
            <br>

            {{-- Form --}}
            <form id="battery-form">
                @csrf

                <div class="row">
                    {{-- Battery Name --}}
                    <div class="col">
                        <div class="form-group local-forms">
                            <label for="battery-name">Name <span class="login-danger">*</span></label>
                            <input type="text" class="form-control" id="battery-name" name="name"
                                placeholder="Enter battery name" value="{{ $data['profile'][1] }}" readonly required>
                        </div>
                    </div>

                    {{-- Quantity --}}
                    <div class="col">
                        <div class="form-group local-forms">
                            <label for="battery-quantity">Quantity <span class="login-danger">*</span></label>
                            <input type="number" class="form-control" id="battery-quantity" name="quantity"
                                placeholder="Enter battery quantity" value="{{ $data['profile'][2] }}" required>
                        </div>
                    </div>
                </div>

                <input type="hidden" name="number" value="{{ $data['profile'][0] }}">

                {{-- Buttons --}}
                <div class="d-flex flex-row-reverse">
                    {{-- Save Button --}}
                    <button type="submit" class="btn btn-success mx-1" id="btn-save">Save Battery Quantity</button>

                    {{-- Reset Button --}}
                    <button type="reset" class="btn btn-danger mx-1" id="btn-cancel">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $("#battery-form").on("submit", function(event) {
                event.preventDefault();

                // Obtain submitted form data.
                let formData = new FormData($(this)[0]);

                // Send submit POST request via AJAX.
                sendSubmitRequest("/inventory/update", formData, function() {
                    // Redirect to index page.
                    goToPage("/inventory");
                });
            });

            $("#battery-form").on("reset", function() {
                goToPage("/inventory");
            });
        });
    </script>
@endsection
