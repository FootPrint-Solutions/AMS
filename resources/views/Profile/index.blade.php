@extends('template.master')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="profile-header">
                <div class="row align-items-center">
                    <div class="col-auto profile-image">
                        <a href="#">
                            <img class="rounded-circle" alt="User Image" src="{{ asset('/img/profiles/avatar-02.jpg') }}">
                        </a>
                    </div>
                    <div class="col ms-md-n2 profile-user-info">
                        @auth
                            <h4 class="user-name mb-0">{{ auth()->user()->name }}</h4>
                            <h6 class="text-muted">{{ auth()->user()->email }}</h6>
                        @endauth
                    </div>
                    <div class="col-auto profile-btn">
                        <a href="" class="btn btn-primary">
                            Edit Profile Picture
                        </a>
                    </div>
                </div>
            </div>
            <div class="profile-menu">
                <ul class="nav nav-tabs nav-tabs-solid">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#per_details_tab">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#password_tab">Password</a>
                    </li>
                </ul>
            </div>
            <div class="tab-content profile-tab-cont">

                <!-- Personal Details Tab -->
                <div class="tab-pane fade show active" id="per_details_tab">

                    <!-- Personal Details -->
                    <div class="row">
                        <div class="col-lg-9">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title d-flex justify-content-between">
                                        <span>Personal Details</span>
                                        <a class="edit-link" data-bs-toggle="modal" href="#edit_personal_details"><i
                                                class="far fa-edit me-1"></i>Edit</a>
                                    </h5>
                                    <div class="row">
                                        <p class="col-sm-3 text-muted text-sm-end mb-0 mb-sm-3">Name</p>
                                        @auth
                                            <p class="col-sm-9">{{ auth()->user()->name }}</p>
                                        @endauth

                                    </div>

                                    <div class="row">
                                        <p class="col-sm-3 text-muted text-sm-end mb-0 mb-sm-3">Email ID</p>
                                        @auth
                                            <p class="col-sm-9">{{ auth()->user()->email }}</p>
                                        @endauth

                                    </div>

                                </div>
                            </div>

                        </div>

                        <div class="col-lg-3">

                            <!-- Account Status -->
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title d-flex justify-content-between">
                                        <span>Whatsapp Status</span>
                                        <a class="edit-link" href=""><i class="fa-solid fa-arrows-rotate"></i></a>
                                    </h5>

                                    @if ($data['QrCode'] != '')
                                        <badge class="badge bg-danger">Not Active, Please Scan here</badge>
                                        <img src="{{ $data['QrCode'] }}" alt="QrCode" class="img-fluid">
                                    @else
                                        <badge class="badge bg-success">Active</badge>
                                        <br>
                                        <button class="btn btn-success btn-lg mt-1" id='DeleteSessionWhatsApp'
                                            type="button"><i class="fe fe-check-verified"></i>
                                            <i class="fa-solid fa-qrcode"></i></button>
                                    @endif


                                </div>
                            </div>
                            <!-- /Account Status -->



                        </div>
                    </div>
                    <!-- /Personal Details -->

                </div>
                <!-- /Personal Details Tab -->

                <!-- Change Password Tab -->
                <div id="password_tab" class="tab-pane fade">

                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Change Password</h5>
                            <div class="row">
                                <div class="col-md-10 col-lg-6">
                                    <form id="form-password">
                                        @csrf

                                        <div class="form-group">
                                            <label>Old Password</label>
                                            <input type="password" name="oldpass" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label>New Password</label>
                                            <input type="password" name="newpass" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label>Confirm Password</label>
                                            <input type="password" name="newpassrecheck" class="form-control">
                                        </div>
                                        <button class="btn btn-primary" type="submit">Save Changes</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Change Password Tab -->

            </div>
        </div>
    </div>

    <script>
        $('#DeleteSessionWhatsApp').click(function() {
            swal.fire({
                title: "Are you sure?",
                text: "Once deleted, you will need to scan the QR code again to activate the session",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!",
                confirmButtonClass: "btn btn-primary",
                cancelButtonClass: "btn btn-danger ml-1",
                buttonsStyling: !1
            }).then((willDelete) => {
                if (willDelete.isConfirmed) {
                    $.ajax({
                        url: "/delete-session-whatsapp",
                        type: 'GET',
                        success: function(data) {
                            let ResponseData = JSON.parse(data);
                            if (ResponseData.status) {
                                swal.fire({
                                    title: "Success",
                                    text: ResponseData.message,
                                    icon: "success",
                                });
                                location.reload();
                            } else {
                                swal.fire({
                                    title: "Error",
                                    text: ResponseData.message ||
                                        "Something went wrong, please try again later",
                                    icon: "error",
                                });
                            }
                        },
                        error: function() {
                            swal.fire({
                                title: "Error",
                                text: "Something went wrong",
                                icon: "error",
                            });
                        }
                    });
                } else {
                    swal.fire({
                        title: "Cancelled",
                        text: "Your session is safe",
                        icon: "error",
                    });
                }
            });
        });

        $("#form-password").on("submit", function(event) {
            event.preventDefault();

            // Get form data.
            let formData = new FormData($(this)[0]);
            
            // Send form data to Vehicle controller using AJAX.
            $.ajax({
                url: '/profile/password/change',
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
                }
            });
        });
    </script>
@endsection
