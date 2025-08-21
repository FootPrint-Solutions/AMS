<div class="row justify-content-center mt-4">
    <div class="col-lg-6 col-md-8">
        <div class="card border-0 rounded-3 shadow-sm">
            <div class="card-body text-center p-4">
                <h5 class="mb-3"><i class="fas fa-key text-primary me-2"></i>Change Password</h5>
                <div class="alert alert-info mb-4">
                    You will be automatically logged out upon changing your password.
                </div>
                <form id="password-form" class="text-start">
                    @csrf
                    <div class="mb-3">
                        <label for="currentpass" class="form-label text-muted">Current Password</label>
                        <input type="password" class="form-control" id="currentpass" name="currentpass"
                            placeholder="Enter your current password" required>
                    </div>
                    <div class="mb-3">
                        <label for="newpass" class="form-label text-muted">New Password</label>
                        <input type="password" class="form-control" id="newpass" name="newpass"
                            placeholder="Enter your new password" required>
                    </div>
                    <div class="mb-3">
                        <label for="newpassconfirm" class="form-label text-muted">Confirm New Password</label>
                        <input type="password" class="form-control" id="newpassconfirm" name="newpassconfirm"
                            placeholder="Reenter your new password" required>
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
    $("#password-form").on("submit", function(event) {
        event.preventDefault();

        // Get form data.
        let formData = new FormData($(this)[0]);

        // Send form data to Vehicle controller using AJAX.
        $.ajax({
            url: "/profile/password/update",
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                // Get response data (in JSON).
                let responseData = JSON.parse(response);

                // Check response data status.
                if (responseData.status) {
                    // Updating password proccess was succeeded.
                    showSuccessToast(responseData.message);
                    location.reload();
                } else {
                    // Updating password proccess was failed.
                    showErrorToast(responseData.message);
                }
            }
        });
    });
</script>
