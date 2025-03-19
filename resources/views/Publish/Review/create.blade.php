@extends('template.master')

@section('content')
    {{-- Form --}}
    <div class="card">
        <div class="card-body">
            {{-- Title --}}
            <div class="card-title h5">
                @isset($data['profile'])
                    Edit
                @else
                    Add New
                @endisset
                Review
            </div>
            <br>

            {{-- Form --}}
            <form id="review-form">
                @csrf

                {{-- Name --}}
                <div class="form-group local-forms">
                    <label for="name">Name <span class="login-danger">*</span></label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="Enter reviewer name"
                        required @isset($data['profile']) value="{{ $data['profile']['name'] }}" @endisset>
                </div>

                {{-- Vehicle --}}
                <div class="form-group local-forms">
                    <label for="vehicle_id">Vehicle <span class="login-danger">*</span></label>
                    <select class="form-control" id="vehicle_id" name="vehicle_id" required>
                        <option value="">Select Vehicle</option>
                        @foreach ($data['vehicles'] as $vehicle)
                            <option value="{{ $vehicle->id }}"
                                @isset($data['profile']) @if ($data['profile']['vehicle_id'] == $vehicle->id) selected @endif @endisset>
                                {{ $vehicle->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Testimonial --}}
                <div class="form-group local-forms">
                    <label for="testimonial">Testimonial <span class="login-danger">*</span></label>
                    <textarea class="form-control" id="testimonial" name="testimonial" rows="4" placeholder="Enter testimonial"
                        required>
@isset($data['profile'])
{{ $data['profile']['testimonial'] }}
@endisset
</textarea>
                </div>

                {{-- Stars --}}
                <div class="form-group local-forms">
                    <label for="stars">Stars <span class="login-danger">*</span></label>
                    <input type="number" class="form-control" id="stars" name="stars" step="0.1" min="0"
                        max="5" placeholder="Enter rating (0-5)" required
                        @isset($data['profile']) value="{{ $data['profile']['stars'] }}" @endisset>
                </div>

                {{-- User Photo --}}
                <div class="form-group local-forms">
                    <label for="user_photo">User Photo <span class="login-danger">*</span></label>
                    <input type="file" class="form-control" id="user_photo" name="user_photo"
                        onchange="previewImage(this, '#user_photo_preview')" required>
                    @isset($data['profile']['user_photo'])
                        <div class="mt-2">
                            <img id="user_photo_preview"
                                src="{{ asset('storage/reviews/user_photos/' . $data['profile']['user_photo']) }}"
                                alt="User Photo" class="img-thumbnail" style="max-width: 150px;"
                                onerror="this.onerror=null;this.src='https://placehold.co/50x50';">
                        </div>
                    @else
                        <div class="mt-2">
                            <img id="user_photo_preview" src="#" alt="User Photo Preview" class="img-thumbnail"
                                style="max-width: 150px; display: none;"
                                onerror="this.onerror=null;this.src='https://placehold.co/50x50';">
                        </div>
                    @endisset
                </div>

                {{-- Testimonial Photo --}}
                <div class="form-group local-forms">
                    <label for="testimonial_photo">Testimonial Photo <span class="login-danger">*</span></label>
                    <input type="file" class="form-control" id="testimonial_photo" name="testimonial_photo"
                        onchange="previewImage(this, '#testimonial_photo_preview')" required>
                    @isset($data['profile']['testimonial_photo'])
                        <div class="mt-2">
                            <img id="testimonial_photo_preview"
                                src="{{ asset('storage/reviews/testimonial_photos/' . $data['profile']['testimonial_photo']) }}"
                                alt="Testimonial Photo" class="img-thumbnail" style="max-width: 150px;"
                                onerror="this.onerror=null;this.src='https://placehold.co/50x50';">
                        </div>
                    @else
                        <div class="mt-2">
                            <img id="testimonial_photo_preview" src="#" alt="Testimonial Photo Preview"
                                class="img-thumbnail" style="max-width: 150px; display: none;"
                                onerror="this.onerror=null;this.src='https://placehold.co/50x50';">
                        </div>
                    @endisset
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
                @isset($data['profile'])
                    <input type="hidden" name="id" value="{{ $data['profile']['id'] }}">
                @endisset

                {{-- Buttons --}}
                <div class="d-flex flex-row-reverse">
                    {{-- Save Button --}}
                    <button type="submit" class="btn btn-success mx-1" id="btn-save"
                        @isset($data['profile']) value="update">Update Review</button>
                @else
                value="create">Create Review</button>
                @endisset
                        {{-- Cancel Button --}} <button type="reset" type="button" class="btn btn-danger mx-1"
                        id="btn-cancel">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Initialize Select2 for vehicle selection.
            $('#vehicle_id').select2({
                placeholder: "Select Vehicle"
            });

            $('#stars').on('input', function() {
                let value = parseFloat($(this).val());
                if (value < 0 || value > 5) {
                    $(this).val('');
                }
            });

            $("#review-form").on("submit", function(event) {
                event.preventDefault();

                // Disable button
                $("#btn-save").attr("disabled", true);
                $("#btn-save").html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...'
                );

                // Get current display mode (Update or Create).
                let mode = $("#btn-save").attr("value");
                let url = "/review/store";
                if (mode == "update") {
                    url = "/review/update";
                }

                // Get review form data.
                let formData = new FormData($(this)[0]);

                // Send submit POST request via AJAX.
                sendSubmitRequest(url, formData, function() {
                    // Redirect to index page.
                    goToPage("/review");
                });
            });

            $("#review-form").on("reset", function() {
                goToPage("/review");
            });
        });
    </script>
@endsection
