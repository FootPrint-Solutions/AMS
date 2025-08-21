<div class="row justify-content-center mt-4">
    <div class="col-lg-6 col-md-8">
        <div class="card border-0 rounded-3 shadow-sm">
            <div class="card-body text-center p-4">
                <h5 class="mb-3"><i class="fas fa-credit-card text-primary me-2"></i>API Key Payment Gateway</h5>
                <div class="alert alert-info mb-4">
                    Please be careful when updating your API key. Make sure to update your API key in your payment gateway
                </div>
                <form id="api-key-form" class="text-start">
                    @csrf
                    <div class="mb-3">
                        <label for="id_merchant" class="form-label text-muted">ID Merchant</label>
                        <input type="text" class="form-control" id="id_merchant" name="id_merchant" placeholder="Enter your ID Merchant" required value="{{ $data['ServerPaymentGateway']->id_merchant ?? '' }}" autocomplete="off">
                    </div>
                    <div class="mb-3">
                        <label for="client_key" class="form-label text-muted">Client Key</label>
                        <input type="text" class="form-control" id="client_key" name="client_key" placeholder="Enter your Client Key" required value="{{ $data['ServerPaymentGateway']->client_key ?? '' }}" autocomplete="off">
                    </div>
                    <div class="mb-3">
                        <label for="server_key" class="form-label text-muted">Server Key</label>
                        <input type="text" class="form-control" id="server_key" name="server_key" placeholder="Enter your Server Key" required value="{{ $data['ServerPaymentGateway']->server_key ?? '' }}" autocomplete="off">
                    </div>
                    <div class="mb-3">
                        <label for="environment" class="form-label text-muted">Environment</label>
                        <select class="form-control" id="environment" name="environment" required>
                            <option value="0" {{ isset($data['ServerPaymentGateway']) && $data['ServerPaymentGateway']->is_active == '0' ? 'selected' : '' }}>Sandbox</option>
                            <option value="1" {{ isset($data['ServerPaymentGateway']) && $data['ServerPaymentGateway']->is_active == '1' ? 'selected' : '' }}>Production</option>
                        </select>
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
    $("#api-key-form").on("submit", function(event) {
        event.preventDefault();

        // Get form data.
        let formData = new FormData($(this)[0]);

        // Send form data to Vehicle controller using AJAX.
        $.ajax({
            url: "/profile/api-key/update",
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                // Show success alert. json response
                let responseData = JSON.parse(response);
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
                // Show error alert.
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: response.responseJSON.message,
                });
            },
        });
    });
</script>