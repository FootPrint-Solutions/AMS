@extends('template.master')

@section('content')
{{-- Title --}}
<div class="h1">
    Battery <a href="/battery/create" type="button" class="btn btn-primary"><i class="fa fa-plus-circle" aria-hidden="true"></i> Add new battery</a>
</div>

{{-- Form --}}
<div class="card">
    <div class="card-body">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Name</th>
                    <th scope="col">Brand</th>
                    <th scope="col">Dimensions</th>
                    <th scope="col">Standard CCA</th>
                    <th scope="col">Capacity</th>
                    <th scope="col">Warranty</th>
                    <th scope="col">Retail Price (IDR)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td scope="row">1</td>
                    <td scope="row">Amaron GO 95D31R</td>
                    <td scope="row">AMARON</td>
                    <td scope="row">300 x 172 x 233</td>
                    <td scope="row">720 A</td>
                    <td scope="row">75 AH</td>
                    <td scope="row">1 week</td>
                    <td scope="row" class="text-end">1,670,000</td>
                </tr>

                <tr>
                    <td scope="row">2</td>
                    <td scope="row">Amaron GO 9523SM</td>
                    <td scope="row">AMARON</td>
                    <td scope="row">200 x 123 x 200</td>
                    <td scope="row">600 A</td>
                    <td scope="row">75 AH</td>
                    <td scope="row">6 weeks</td>
                    <td scope="row" class="text-end">859,000</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
