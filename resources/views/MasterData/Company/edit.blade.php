@extends('template.master')

@section('content')
<div class="card">
    <div class="card-body">
        {{-- Title --}}
        <div class="card-title h2">
            Company Profile
        </div>

        @if(session('status') && session('message'))
            <div class="alert {{ session('status') ? 'alert-success' : 'alert-danger' }}">
                {{ session('message') }}
            </div>
        @else
            <br>
        @endif

        {{-- Form --}}
        <form action="/company/update" method="POST" id="company-form">
            @csrf

            {{-- Company Name --}}
            <div class="form-group row">
                <div class="col-1">
                    <label for="company-name">Name</label>
                </div>

                <div class="col-11">
                    <input type="text" class="form-control" id="company-name" name="name" placeholder="Company name" value="{{ $data ? $data->name : '' }}">

                </div>
            </div>
            
            {{-- Company Address --}}
            <div class="form-group row">
                <div class="col-1">
                    <label for="company-address">Address</label>
                </div>

                <div class="col-11">
                    <input type="text" class="form-control" id="company-address" name="address" placeholder="Company address" value="{{ $data ? $data->address : '' }}">
                </div>
            </div>

            {{-- Company Contact --}}
            <div class="form-group row">
                <div class="col-1">
                    <label for="company-address">Contact</label>
                </div>

                <div class="col-11">    
                    <input type="text" class="form-control" id="company-contact" name="contact" placeholder="Company contact" value="{{ $data ? $data->contact : '' }}">
                </div>
            </div>

            {{-- Company E-mail --}}
            <div class="form-group row">
                <div class="col-1">
                    <label for="company-email">E-mail</label>
                </div>

                <div class="col-11">
                    <input type="email" class="form-control" id="company-email" name="email" placeholder="Company e-mail" value="{{ $data ? $data->email : '' }}">
                </div>
            </div>
            
            {{-- Buttons --}}
            <a class="btn btn-primary" id="btn-save">Save</button>
            <a href="/company" class="btn btn-danger">Reset</a>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
    $(document).ready(function() {
        $("#btn-save").on('click', function() {
            $.ajax({
                url: '/company/update',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    let responseData = JSON.parse(response);
                    if (responseData.status) {
                        // Success
                        showSuccessToast(responseData.message);
                    } else {
                        // Failed
                        showErrorToast(responseData.message);
                    }
                }
            });
        });
    });
</script>
@endsection