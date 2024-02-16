<div class="row">
    {{-- Personal Details --}}
    <div class="col-lg-9">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title d-flex justify-content-between">
                    <span>Personal Details</span>
                    <a class="edit-link" data-bs-toggle="modal" href="#edit_personal_details"><i
                            class="far fa-edit me-1"></i>Edit</a>
                </h5>

                {{-- Name --}}
                <div class="row">
                    <p class="col-sm-3 text-muted text-sm-end mb-0 mb-sm-3">Name</p>
                    @auth
                        <p class="col-sm-9">{{ auth()->user()->name }}</p>
                    @endauth
                </div>

                {{-- Email --}}
                <div class="row">
                    <p class="col-sm-3 text-muted text-sm-end mb-0 mb-sm-3">Email ID</p>
                    @auth
                        <p class="col-sm-9">{{ auth()->user()->email }}</p>
                    @endauth
                </div>
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
    $('#btn-delete-wa-session').click(function() {
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
</script>