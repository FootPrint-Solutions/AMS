<div class="card">
    <div class="card-body">
        {{-- Title --}}
        <div class="card-title h5">
            Change Password
        </div>

        {{-- Warning Alert --}}
        <div class="alert alert-info mt-2">
            You will be automatically logged out upon changing your password.
        </div>

        {{-- Password Form --}}
        <div class="row mt-4">
            <form id="password-form">
                @csrf

                {{-- Current Password --}}
                <div class="form-group local-forms">
                    <label for="currentpass">Current Password <span class="login-danger">*</span></label>
                    <input type="password" class="form-control" id="currentpass" name="currentpass" placeholder="Enter your current password" required>
                </div>

                {{-- New Password --}}
                <div class="form-group local-forms">
                    <label for="newpass">New Password <span class="login-danger">*</span></label>
                    <input type="password" class="form-control" id="newpass" name="newpass" placeholder="Enter your new password" required>
                </div>

                {{-- New Password --}}
                <div class="form-group local-forms">
                    <label for="newpassconfirm">Confirm New Password <span class="login-danger">*</span></label>
                    <input type="password" class="form-control" id="newpassconfirm" name="newpassconfirm" placeholder="Reenter your new password" required>
                </div>

                <div class="d-flex flex-row-reverse">
                    <button class="btn btn-primary" type="submit">Update Password</button>
                </div>
            </form>
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
            url: "/profile/password/change",
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