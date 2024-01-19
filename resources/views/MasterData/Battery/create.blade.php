@extends('template.master')

@section('content')
{{-- Title --}}
<div class="h1">
    Add New Battery
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

            {{-- Alternate Names --}}
            <div class="form-group">
                <label for="alternate-name" class="col-sm-2 col-form-label">Alternate Names</label>
                <div class="col-sm-10">
                    <div class="border rounded p-2">
                        <span class="btn btn-primary">Azunyan #2</span>
                        <span class="btn btn-primary">Azunyan #3</span>
                        <span class="btn btn-success"><i class="fa fa-plus-circle" aria-hidden="true"></i></span>
                    </div>
                </div>
            </div>
            
            {{-- Brand --}}
            <div class="form-group">
                <label for="brand">Brand</label>
                <select class="form-select" id="brand">
                    <option>Select battery brand</option>
                    <option value="toyota" selected>AMARON</option>
                    <option value="-1">Add New...</option>
                </select>
            </div>

            {{-- Usage Type --}}
            <div class="form-group">
                <label for="brand">Usage Type</label>
                <select class="form-select" id="brand">
                    <option>Select usage type</option>
                    <option value="toyota" selected>CAR MF</option>
                    <option value="-1">Add New...</option>
                </select>
            </div>

            {{-- Usage Type --}}
            <div class="form-group">
                <label for="technology">Battery Technology</label>
                <select class="form-select" id="brand">
                    <option>Select battery technology</option>
                    <option value="toyota" selected>Maintenance-Free</option>
                    <option value="-1">Add New...</option>
                </select>
            </div>

            {{-- Dimension --}}
            <div class="form-group">
                <label for="dimension">Dimension (length x width x height)</label>
                <div class="row">
                    <div class="col">
                        <input type="number" class="form-control" id="dimension-l" placeholder="Customer name" value="300">
                    </div>

                    <div class="col">
                        <input type="number" class="form-control" id="dimension-w" placeholder="Customer name" value="200">
                    </div>

                    <div class="col">
                        <input type="number" class="form-control" id="dimension-h" placeholder="Customer name" value="200">
                    </div>
                </div>
            </div>

            {{-- Standard CCA --}}
            <div class="form-group">
                <label for="cca">Standard CCA</label>
                <input type="number" class="form-control" id="cca" placeholder="Standard CCA" value="720">
            </div>

            {{-- Capacity --}}
            <div class="form-group">
                <label for="capacity">Capacity</label>
                <input type="number" class="form-control" id="capacity" placeholder="Battery Capacity" value="56">
            </div>
            
            {{-- Buttons --}}
            <button type="submit" class="btn btn-primary">Save</button>
            <a href="/battery/" type="button" class="btn btn-danger">Cancel</a>
        </form>
    </div>
</div>
@endsection
