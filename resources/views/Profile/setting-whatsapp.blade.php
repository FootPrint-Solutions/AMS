<div class="card">
    <div class="card-body">
        {{-- Title --}}
        <div class="card-title h5">
            Setting WhatsApp Server
        </div>

        {{-- Warning Alert --}}
        <div class="alert alert-info mt-2">
            Please be careful when updating your WhatsApp server settings.
        </div>

        {{-- WhatsApp Server Form --}}
        <div class="row mt-4">
            <form id="whatsapp-server-form">
                @csrf

                {{-- Name --}}
                <div class="form-group local-forms">
                    <label for="name">Name <span class="login-danger">*</span></label>
                    <input type="text" class="form-control" id="name" name="name"
                        placeholder="Enter server name" required value="{{ $data['ServerWhatsapp']->name ?? '' }}"
                        autocomplete="off" readonly>
                </div>

                {{-- Number --}}
                <div class="form-group local-forms">
                    <label for="number">Number <span class="login-danger">*</span></label>
                    <input type="text" class="form-control" id="number" name="number"
                        placeholder="Enter WhatsApp number" required value="{{ $data['ServerWhatsapp']->number ?? '' }}"
                        autocomplete="off">
                </div>

                {{-- URL --}}
                <div class="form-group local-forms">
                    <label for="url">URL</label>
                    <input type="text" class="form-control" id="url" name="url"
                        placeholder="Enter server URL (optional)" value="{{ $data['ServerWhatsapp']->url ?? '' }}"
                        autocomplete="off" readonly>
                </div>

                <div class="d-flex flex-row-reverse">
                    <button class="btn btn-success" type="submit">Update WhatsApp Server</button>
                </div>
            </form>
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
