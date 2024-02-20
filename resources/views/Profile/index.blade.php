@extends('template.master')

@section('content')
    <div class="row">
        <div class="col-md-12">
            {{-- Header --}}
            <div class="profile-header">
                <div class="row align-items-center">
                    {{-- Profile Picture --}}
                    <div class="col-auto profile-image">
                        @if (is_null(auth()->user()->image) || empty(auth()->user()->image))
                            <img class="rounded-circle" alt="User Image" src="{{ asset("/img/profiles/default_profile.png") }}">
                        @else
                            <img class="rounded-circle" alt="User Image" src="{{ asset("storage/image/profile/" . auth()->user()->image) }}">
                        @endif
                    </div>

                    {{-- Name and Email --}}
                    <div class="col ms-md-n2 profile-user-info">
                        @auth
                            <h4 class="user-name mb-0">{{ auth()->user()->name }}</h4>
                            <h6 class="text-muted">{{ auth()->user()->email }}</h6>
                        @endauth
                    </div>

                    {{-- Upload Profile Picture --}}
                    <div class="col-auto profile-btn">
                        <form id="profile-picture-form">
                            @csrf

                            <input type="file" id="image-input" name="image" style="display: none;">
                        </form>

                        <button class="btn btn-primary" id="btn-upload">Upload Profile Picture</button>
                        <button class="btn btn-danger" id="btn-remove">Remove Profile Picture</button>
                    </div>
                </div>
            </div>

            {{-- Content Title --}}
            <div class="profile-menu">
                <ul class="nav nav-tabs nav-tabs-solid">
                    {{-- About --}}
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#about-tab">About</a>
                    </li>

                    {{-- Password --}}
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#password-tab">Password</a>
                    </li>
                </ul>
            </div>

            {{-- Content Tab --}}
            <div class="tab-content profile-tab-cont">
                {{-- About Tab --}}
                <div class="tab-pane fade show active" id="about-tab">
                    @include('Profile.about')
                </div>

                {{-- Password Tab --}}
                <div id="password-tab" class="tab-pane fade">
                    @include('Profile.password')
                </div>

            </div>
        </div>
    </div>

    <script>
        $("#btn-upload").on("click", function() {
            // Show warning message before uploading a new profile picture.
            Swal.fire({
                title: "Are you sure you want to upload a new profile picture?",
                text: "Your current profile picture will be replaced!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes",
                cancelButtonText: "Cancel"
            }).then(function(e) {
                if (e.value === true) {
                    $("#image-input").trigger("click");
                }
            });
        });

        $("#btn-remove").on("click", function() {
            // Show warning message before uploading a new profile picture.
            Swal.fire({
                title: "Are you sure you want to remover your current profile picture?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, remove it",
                cancelButtonText: "Cancel"
            }).then(function(e) {
                if (e.value === true) {
                    $("#profile-picture-form").submit();
                }
            });
        })
        
        $("#image-input").on("change", function() {
            $("#profile-picture-form").submit();
        });

        $("#profile-picture-form").on("submit", function(event) {
            event.preventDefault();

            // Get form data.
            let formData = new FormData($(this)[0]);

            // Send form data to Vehicle controller using AJAX.
            $.ajax({
                url: "/profile/picture/update",
                method: "POST",
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
                    location.reload();
                }
            });
        });
    </script>
@endsection
