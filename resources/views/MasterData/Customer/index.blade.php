@extends('template.master')

@section('content')
{{-- Title --}}
<div class="h1">
    Customer <a href="/customer/create" type="button" class="btn btn-primary"><i class="fa fa-plus-circle" aria-hidden="true"></i> Add new customer </a>
</div>

{{-- Form --}}
<div class="card">
    <div class="card-body">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Name</th>
                    <th scope="col">Contact</th>
                    <th scope="col">E-mail</th>
                    <th scope="col">Address</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td scope="row">1</td>
                    <td scope="row">Nakano Nino</td>
                    <td scope="row">+62 421 5142 1932</td>
                    <td scope="row">ninobestgirl@hotmail.com</td>
                    <td scope="row">Jalan Tokyo R no. 42 
                        <button type="button" class="btn btn-primary"><i class="fa fa-map-marker" aria-hidden="true"></i></button>
                    </td>
                </tr>

                <tr>
                    <td scope="row">2</td>
                    <td scope="row">Yor Forger</td>
                    <td scope="row">+62 232 2898 2093</td>
                    <td scope="row">yorforger1@gmail.com</td>
                    <td scope="row">Jalan Berlint City no. 238
                        <button type="button" class="btn btn-primary"><i class="fa fa-map-marker" aria-hidden="true"></i></button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
