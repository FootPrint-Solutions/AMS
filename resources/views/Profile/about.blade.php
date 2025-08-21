<div class="row justify-content-center mt-4">
    <div class="col-lg-6 col-md-8">
        <div class="card border-0 rounded-3 shadow-sm mb-3">
            <div class="card-body text-center p-4">
                <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=0D8ABC&color=fff&size=80"
                    class="rounded-circle mb-2" alt="Avatar" style="width:80px;height:80px;">
                <h5 class="mb-1">{{ auth()->user()->name }}</h5>
                <div class="text-muted mb-2">@ {{ auth()->user()->username }}</div>
                <form id="personal-form" class="mt-3">
                    @csrf
                    <div class="mb-2 text-start">
                        <label for="name" class="form-label text-muted">Name</label>
                        <input type="text" class="form-control inputs" id="name" name="name"
                            value="{{ auth()->user()->name }}" style="display:none;">
                        <p class="currents mb-0">{{ auth()->user()->name }}</p>
                    </div>
                    <div class="mb-2 text-start">
                        <label for="email" class="form-label text-muted">Email</label>
                        <input type="email" class="form-control inputs" id="email" name="email"
                            value="{{ auth()->user()->email }}" style="display:none;">
                        <p class="currents mb-0">{{ auth()->user()->email }}</p>
                    </div>
                    <div class="mb-2 text-start">
                        <label class="form-label text-muted">Username</label>
                        <p class="mb-0" id="currusername">{{ auth()->user()->username }}</p>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button class="btn btn-success inputs" id="btn-save" type="submit"
                            style="display:none;">Update</button>
                        <button class="btn btn-outline-primary ms-2" id="btn-edit" type="button"><i
                                class="far fa-edit me-1"></i> Edit</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="card border-0 rounded-3 shadow-sm">
            <div class="card-body text-center p-4">
                <h6 class="mb-3"><i class="fab fa-whatsapp text-success me-2"></i>Whatsapp Status</h6>
                @if ($data['QrCode'] != '')
                    <span class="badge bg-danger mb-2">Not Active, Please Scan here</span>
                    <img src="{{ $data['QrCode'] }}" alt="QrCode" class="img-fluid rounded-3 border-2 mb-2">
                @else
                    <span class="badge bg-success mb-2">Active</span>
                    <button class="btn btn-success w-100 mt-2" id='btn-delete-wa-session' type="button">
                        <i class="fe fe-check-verified"></i> <i class="fa-solid fa-qrcode"></i> Delete Session
                    </button>
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
