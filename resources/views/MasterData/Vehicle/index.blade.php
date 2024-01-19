@extends('template.master')

@section('content')
{{-- Title --}}
<div class="h1">
    Vehicle <a href="/vehicle/create" type="button" class="btn btn-primary"><i class="fa fa-plus-circle" aria-hidden="true"></i> Add new vehicle</a>
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
                    <th scope="col">Battery</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td scope="row">1</td>
                    <td scope="row">Toyota Avanza</td>
                    <td scope="row">Toyota</td>
                    <td scope="row">
                        <span class="badge badge-primary">B20R</span>
                        <span class="badge badge-primary">B24R</span>
                    </td>
                </tr>

                <tr>
                    <td scope="row">2</td>
                    <td scope="row">Mazda Nyaa</td>
                    <td scope="row">Mazda</td>
                    <td scope="row">
                        <span class="badge badge-primary">B20R</span>
                        <span class="badge badge-primary">B24R</span>
                        <span class="badge badge-primary">B29R</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

{{-- Detail Modal --}}
<div class="modal fade" id="vehicle-detail-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                ...
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</div>
@endsection
