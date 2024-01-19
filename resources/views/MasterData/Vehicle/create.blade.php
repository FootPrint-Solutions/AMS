@extends('template.master')

@section('content')
{{-- Title --}}
<div class="h1">
    Add New Vehicle
</div>

{{-- Form --}}
<div class="card">
    <div class="card-body">
        <form action="" method="POST">
            {{-- Name --}}
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" class="form-control" id="name" placeholder="Customer name" value="Toyota Avanza 2009">
            </div>
            
            {{-- Brand --}}
            <div class="form-group">
                <label for="brand">Brand</label>
                <select class="form-select" id="brand">
                    <option>Select car brand</option>
                    <option value="toyota" selected>Toyota</option>
                    <option value="mazda">Mazda</option>
                    <option value="-1">Add New...</option>
                </select>
            </div>

            {{-- Battery --}}
            <div class="form-group">
                <label for="battery" class="col-sm-2 col-form-label">Battery</label>
                <div class="col-sm-10">
                    <div class="border rounded p-2">
                        <span class="btn btn-primary">B20R</span>
                        <span class="btn btn-primary">Bw4R</span>
                        <span class="btn btn-success"><i class="fa fa-plus-circle" aria-hidden="true"></i></span>
                    </div>
                </div>
            </div>
            
            {{-- Buttons --}}
            <button type="submit" class="btn btn-primary">Save</button>
            <a href="/vehicle/" type="button" class="btn btn-danger">Cancel</a>
        </form>
    </div>
</div>
@endsection
