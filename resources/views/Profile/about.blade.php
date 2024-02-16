<div class="row">
    {{-- Personal Details --}}
    <div class="col-lg-9">
        <div class="card">
            <div class="card-body">
                {{-- Title --}}
                <h5 class="card-title d-flex justify-content-between align-middle">
                    <span>Personal Details</span>

                    <button class="btn btn-primary" id="btn-edit">
                        <i class="far fa-edit me-1"></i> Edit Profile
                    </button>
                </h5>

                <form id="personal-form">
                    @csrf

                    {{-- Name --}}
                    <div class="row">
                        <label for="name" class="col-sm-3 text-muted text-sm-end">Name</label>
                        <input type="text" class="col-sm-9 mb-3 border border-secondary rounded inputs" id="name" name="name" value="{{ auth()->user()->name }}" style="display: none;" required>

                        {{-- Current Name --}}
                        @auth
                            <p class="col-sm-9 currents">{{ auth()->user()->name }}</p>
                        @endauth
                    </div>

                    {{-- Email --}}
                    <div class="row">
                        <label for="email" class="col-sm-3 text-muted text-sm-end">Email</label>
                        <input type="email" class="col-sm-9 mb-3 border border-secondary rounded inputs" id="email" name="email" value="{{ auth()->user()->email }}" style="display: none;" required>

                        {{-- Current Email --}}
                        @auth
                            <p class="col-sm-9 currents">{{ auth()->user()->email }}</p>
                        @endauth
                    </div>

                    {{-- Username --}}
                    <div class="row">
                        <label class="col-sm-3 text-muted text-sm-end">Username</label>
                        @auth
                            <p class="col-sm-9" id="currusername">{{ auth()->user()->username }}</p>
                        @endauth
                    </div>

                    {{-- Buttons --}}
                    <div class="d-flex flex-row-reverse">
                        <button class="btn btn-success inputs" id="btn-save" type="submit" style="display: none;">Update Profile</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- WhatsApp Status --}}
    <div class="col-lg-3">
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
                    <button class="btn btn-success btn-lg mt-1" id='btn-delete-wa-session'
                        type="button"><i class="fe fe-check-verified"></i>
                        <i class="fa-solid fa-qrcode"></i></button>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    /**
     * Status indicating whether the page is in editing process or not.
     * 0 : view mode, 1 : editing mode
     */
    var editStatus = 0;

    $("#btn-edit").on("click", function() {
        if (editStatus == 0) {
            // In view mode, to edit mode.
            // Show all inputs form and hide current elements.
            $(".inputs").show();
            $(".currents").hide();

            // Change the value of the button.
            $(this).html("<i class='fa fa-chevron-left me-1'></i> Back");

            // Toggle editStatus to 1.
            editStatus = 1;
        } else {
            // In edit mode, to view mode.
            // Hide all inputs form and show current elements..
            $(".inputs").hide();
            $(".currents").show();

            // Change the value of the button.
            $(this).html("<i class='far fa-edit me-1'></i> Edit Profile");

            // Toggle editStatus to 0.
            editStatus = 0;
        }
    });

    $("#btn-delete-wa-session").on("click", function() {
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

    $("#personal-form").on("submit", function(event) {
        event.preventDefault();

        // Get form data.
        let formData = new FormData($(this)[0]);
        
        // Send form data to Vehicle controller using AJAX.
        $.ajax({
            url: "/profile/update",
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                // Get response data (in JSON).
                let responseData = JSON.parse(response);

                // Check response data status.
                if (responseData.status) {
                    // Updating proccess was succeeded.
                    showSuccessToast(responseData.message);
                    
                    goToPage("/profile");
                } else {
                    // Updating proccess was failed.
                    showErrorToast(responseData.message);
                }
            }
        });
    });
</script>