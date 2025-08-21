<div class="row justify-content-center mt-4">
    <div class="col-lg-6 col-md-8">
        <div class="card border-0 rounded-3 shadow-sm">
            <div class="card-body text-center p-4">
                <h5 class="mb-3"><i class="fab fa-whatsapp text-success me-2"></i>WhatsApp Server Setting</h5>
                <div class="alert alert-info mb-4">
                    Please be careful when updating your WhatsApp server settings.
                </div>
                <form id="whatsapp-server-form" class="text-start">
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label text-muted">Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ $data['ServerWhatsapp']->name ?? '' }}" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="number" class="form-label text-muted">Number</label>
                        <input type="text" class="form-control" id="number" name="number" value="{{ $data['ServerWhatsapp']->number ?? '' }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="url" class="form-label text-muted">URL</label>
                        <input type="text" class="form-control" id="url" name="url" value="{{ $data['ServerWhatsapp']->url ?? '' }}" readonly>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button class="btn btn-success px-4" type="submit">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $("#whatsapp-server-form").on("submit", function(event) {
        event.preventDefault();

        let formData = new FormData($(this)[0]);

        $.ajax({
            url: "/profile/whatsapp-server/update",
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                let responseData = typeof response === "string" ? JSON.parse(response) : response;
                if (responseData.status == true) {
                    Swal.fire({
                        icon: "success",
                        title: "Success",
                        text: responseData.message,
                    });
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: responseData.message,
                    });
                }
            },
            error: function(response) {
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: response.responseJSON?.message || "An error occurred.",
                });
            },
        });
    });
</script>