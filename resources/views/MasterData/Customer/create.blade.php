@extends('template.master')

@section('content')
{{-- Title --}}
<div class="h1">
    Add New Customer
</div>

{{-- Form --}}
<div class="card">
    <div class="card-body">
        <form action="" method="POST">
            {{-- Name --}}
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" class="form-control" id="name" placeholder="Customer name" value="Azunyan">
            </div>
            
            {{-- Address --}}
            <div class="form-group">
                <label for="address">Address</label>
                <input type="text" class="form-control" id="address" placeholder="Customer address" value="Jalan hokago tea time no 54">
            </div>

            {{-- Contact --}}
            <div class="form-group">
                <label for="contact">Contact</label>
                <input type="text" class="form-control" id="contact" placeholder="Customer contact" value="08932321232">
            </div>

            {{-- Vehicle --}}
            <div class="form-group">
                <label for="vehicle" class="col-sm-2 col-form-label">Customer Vehicle</label>
                <div class="col-sm-10">
                    <div class="border rounded p-2">
                        <span class="btn btn-primary">Toyota Avanza</span>
                        <span class="btn btn-primary">Azunyan #2</span>
                        <span class="btn btn-primary">Hohoho</span>
                        <span class="btn btn-success"><i class="fa fa-plus-circle" aria-hidden="true"></i></span>
                    </div>
                </div>
            </div>
            
            {{-- Buttons --}}
            <button type="submit" class="btn btn-primary">Save</button>
            <a href="/customer/" type="button" class="btn btn-danger">Cancel</a>
        </form>
    </div>
</div>
@endsection
