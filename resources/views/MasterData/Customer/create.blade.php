@extends('template.master')

@section('content')
{{-- Form --}}
<div class="card">
    <div class="card-body">
        {{-- Title --}}
        <div class="card-title h2">
            Add New Customer
        </div>
        <br>

        {{-- Form --}}
        <form id="customer-form">
            @csrf

            {{-- Name --}}
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Customer name" required>
            </div>
            
            {{-- Address --}}
            <div class="form-group">
                <label for="address">Address</label>
                <input type="text" class="form-control" id="address" name="address" placeholder="Customer address" required>
            </div>

            {{-- Contact --}}
            <div class="form-group">
                <label for="contact">Contact</label>
                <input type="text" class="form-control" id="contact" name="contact" placeholder="Customer contact" required>
            </div>

            {{-- Email --}}
            <div class="form-group">
                <label for="contact">E-mail</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Customer e-mail" required>
            </div>

            {{-- Vehicle --}}
            {{-- <div class="form-group">
                <label for="vehicle" class="col-sm-2 col-form-label">Customer Vehicle</label>
                <div class="col-sm-10">
                    <div class="border rounded p-2">
                        <span class="btn btn-primary">Toyota Avanza</span>
                        <span class="btn btn-primary">Azunyan #2</span>
                        <span class="btn btn-primary">Hohoho</span>
                        <span class="btn btn-success"><i class="fa fa-plus-circle" aria-hidden="true"></i></span>
                    </div>
                </div>
            </div> --}}
            
            {{-- Buttons --}}
            <a class="btn btn-primary" id="btn-save">Save</button>
            <a href="/customer/" type="button" class="btn btn-danger">Cancel</a>
        </form>
    </div>
</div>

<script>
    $(document).ready(function() {
        $("#btn-save").on('click', function() {
            // Get customer form data.
            let formData = new FormData($('#customer-form')[0]);
            
            // Send customer form data to Customer controller using AJAX.
            $.ajax({
                url: '/customer/store',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    // Get response data (in JSON).
                    let responseData = JSON.parse(response);

                    // Check response data status.
                    // Status indicates the success status of customer creating porcess.
                    if (responseData.status) {
                        // Creating new customer was succeeded.
                        showSuccessToast(responseData.message);
                    } else {
                        // Creating new customer was failed.
                        showErrorToast(responseData.message);
                    }

                    // Redirect to Customer index page.
                    window.location.href = "/customer";
                }
            });
        });

        $("#btn-reset").on('click', function() {
            $.ajax({
                url: '/company',
                success: function(response) {
                    window.location.replace('/company');
                }
            });
        });
    });
</script>
@endsection
