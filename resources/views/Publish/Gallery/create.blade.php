@extends('template.master')

@section('content')
    {{-- Form --}}
    <div class="card">
        <div class="card-body">
            {{-- Title --}}
            <div class="card-title h5">
                @isset($data['gallery'])
                    Edit
                @else
                    Add New
                @endisset
                Gallery
            </div>
            <br>

            {{-- Form --}}
            <form id="gallery-form">
                @csrf

                {{-- Battery --}}
                <div class="form-group local-forms">
                    <label for="battery_id">Battery <span class="login-danger">*</span></label>
                    <select class="form-control" id="battery_id" name="battery_id" required>
                        <option value="">Select Battery</option>
                        @foreach ($data['batteries'] as $battery)
                            <option value="{{ $battery->id }}"
                                @isset($data['gallery']) @if ($data['gallery']['battery_id'] == $battery->id) selected @endif @endisset>
                                {{ $battery->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Vehicle --}}
                <div class="form-group local-forms">
                    <label for="vehicle_id">Vehicle <span class="login-danger">*</span></label>
                    <select class="form-control" id="vehicle_id" name="vehicle_id" required>
                        <option value="">Select Vehicle</option>
                        @foreach ($data['vehicles'] as $vehicle)
                            <option value="{{ $vehicle->id }}"
                                @isset($data['gallery']) @if ($data['gallery']['vehicle_id'] == $vehicle->id) selected @endif @endisset>
                                {{ $vehicle->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Photo --}}
                <div class="form-group local-forms">
                    <label for="photo">Photo <span class="login-danger">*</span></label>
                    <input type="file" class="form-control" id="photo" name="photo"
                        onchange="previewImage(this, '#photo_preview')" required>
                    @isset($data['gallery']['photo'])
                        <div class="mt-2">
                            <img id="photo_preview" src="{{ asset('storage/gallery/' . $data['gallery']['photo']) }}"
                                alt="Gallery Photo" class="img-thumbnail" style="max-width: 150px;"
                                onerror="this.onerror=null;this.src='https://placehold.co/50x50';">
                        </div>
                    @else
                        <div class="mt-2">
                            <img id="photo_preview" src="#" alt="Photo Preview" class="img-thumbnail"
                                style="max-width: 150px; display: none;"
                                onerror="this.onerror=null;this.src='https://placehold.co/50x50';">
                        </div>
                    @endisset
                </div>

                {{-- Status --}}
                <div class="form-group local-forms d-none">
                    <label for="status">Status <span class="login-danger">*</span></label>
                    <select class="form-control" id="status" name="status" required>
                        <option value="1"
                            @isset($data['gallery']) @if ($data['gallery']['status'] == 1) selected @endif @endisset>
                            Active</option>
                        <option value="0"
                            @isset($data['gallery']) @if ($data['gallery']['status'] == 0) selected @endif @endisset>
                            Inactive</option>
                    </select>
                </div>

                <script>
                    function previewImage(input, previewSelector) {
                        if (input.files && input.files[0]) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                const preview = document.querySelector(previewSelector);
                                preview.src = e.target.result;
                                preview.style.display = 'block';
                            };
                            reader.readAsDataURL(input.files[0]);
                        }
                    }
                </script>

                {{-- Hidden Inputs --}}
                @isset($data['gallery'])
                    <input type="hidden" name="id" value="{{ $data['gallery']['id'] }}">
                @endisset

                {{-- Buttons --}}
                <div class="d-flex flex-row-reverse">
                    {{-- Save Button --}}
                    <button type="submit" class="btn btn-success mx-1" id="btn-save"
                        @isset($data['gallery']) value="update">Update Gallery</button>
                @else
                value="create">Create Gallery</button>
                @endisset
                        {{-- Cancel Button --}} <button type="reset" type="button" class="btn btn-danger mx-1"
                        id="btn-cancel">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Initialize Select2 for dropdowns.
            $('#battery_id, #vehicle_id').select2({
                placeholder: "Select an option"
            });

            $("#gallery-form").on("submit", function(event) {
                event.preventDefault();

                // Disable button
                $("#btn-save").attr("disabled", true);
                $("#btn-save").html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...'
                );

                // Get current display mode (Update or Create).
                let mode = $("#btn-save").attr("value");
                let url = "/gallery/store";
                if (mode == "update") {
                    url = "/gallery/update";
                }

                // Get gallery form data.
                let formData = new FormData($(this)[0]);

                // Send submit POST request via AJAX.
                sendSubmitRequest(url, formData, function() {
                    // Redirect to index page.
                    goToPage("/gallery");
                });
            });

            $("#gallery-form").on("reset", function() {
                goToPage("/gallery");
            });
        });
    </script>
@endsection
