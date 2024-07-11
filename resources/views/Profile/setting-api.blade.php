<div class="card">
    <div class="card-body">
        {{-- Title --}}
        <div class="card-title h5">
            Setting Api Key Payment Gateway
        </div>

        {{-- Warning Alert --}}
        <div class="alert alert-info mt-2">
            Please be careful when updating your API key. Make sure to update your API key in your payment gateway
        </div>

        {{-- Api Key Form --}}
        <div class="row mt-4">
            <form id="api-key-form">
                @csrf

                {{-- Id Merchant --}}
                <div class="form-group local-forms">
                    <label for="id_merchant">ID Merchant <span class="login-danger">*</span></label>
                    <input type="text" class="form-control" id="id_merchant" name="id_merchant"
                        placeholder="Enter your ID Merchant" required
                        value="{{ $data['ServerPaymentGateway']->id_merchant ?? '' }}" autocomplete="off">
                </div>

                {{-- Client Key --}}
                <div class="form-group local-forms">
                    <label for="client_key">Client Key <span class="login-danger">*</span></label>
                    <input type="text" class="form-control" id="client_key" name="client_key"
                        placeholder="Enter your Client Key" required
                        value="{{ $data['ServerPaymentGateway']->client_key ?? '' }}" autocomplete="off">
                </div>

                {{-- Server Key --}}
                <div class="form-group local-forms">
                    <label for="server_key">Server Key <span class="login-danger">*</span></label>
                    <input type="text" class="form-control" id="server_key" name="server_key"
                        placeholder="Enter your Server Key" required
                        value="{{ $data['ServerPaymentGateway']->server_key ?? '' }}" autocomplete="off">
                </div>

                {{-- Environment --}}
                <div class="form-group local-forms">
                    <label for="environment">Environment <span class="login-danger">*</span></label>
                    <select class="form-control" id="environment" name="environment" required>
                        <option value="0" {{ $data['ServerPaymentGateway']->is_active == '0' ? 'selected' : '' }}>
                            Sandbox</option>
                        <option value="1" {{ $data['ServerPaymentGateway']->is_active == '1' ? 'selected' : '' }}>
                            Production</option>
                    </select>
                </div>



                <div class="d-flex flex-row-reverse">
                    <button class="btn btn-success" type="submit">Update API Key</button>
                </div>
            </form>
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
