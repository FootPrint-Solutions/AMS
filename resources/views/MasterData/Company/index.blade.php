@extends('template.master')

@section('content')
{{-- Title --}}
<div class="h1">
    Company Profile
</div>

{{-- Form --}}
<div class="card">
    <div class="card-body">
        <form action="" method="POST">
            {{-- Company Name --}}
            <div class="form-group">
                <label for="company-name">Name</label>
                <input type="text" class="form-control" id="company-name" placeholder="Company name" value="">
            </div>
            
            {{-- Company Address --}}
            <div class="form-group">
                <label for="company-address">Address</label>
                <input type="text" class="form-control" id="company-address" placeholder="Company address">
            </div>

            {{-- Company Contact --}}
            <div class="form-group">
                <label for="company-address">Contact</label>
                <input type="text" class="form-control" id="company-contact" placeholder="Company contact">
            </div>

            {{-- Company E-mail --}}
            <div class="form-group">
                <label for="company-email">E-mail</label>
                <input type="email" class="form-control" id="company-email" placeholder="Company e-mail">
            </div>
            
            {{-- Buttons --}}
            <button type="submit" class="btn btn-primary">Save</button>
            <button type="button" class="btn btn-danger">Reset</button>
        </form>
    </div>
</div>
@endsection
