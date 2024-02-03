@extends('template.master')

@section('content')
{{-- Form --}}
<div class="card">
    <div class="card-body">
        {{-- Title --}}
        <div class="card-title h2">
            Battery
            <button class="btn btn-primary" id="btn-add"><i class="fa fa-plus-circle" aria-hidden="true"></i> Add new battery</a>
        </div>
        <br>

        {{-- Table --}}
        <table class="table table-striped" id="table-battery">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Name</th>
                    <th scope="col">Brand</th>
                    <th scope="col">Subbrand Category</th>
                    <th scope="col">Usage Type</th>
                    <th scope="col">Size Category</th>
                    <th scope="col">Technology</th>
                    <th scope="col">Dimensions</th>
                    <th scope="col">Standard CCA</th>
                    <th scope="col">Capacity</th>
                    <th scope="col">Warranty</th>
                    <th scope="col">Retail Price (IDR)</th>
                    <th scope="col">Edit</th>
                    <th scope="col">Delete</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<script>
    var table;

    $(document).ready(function() {
        // DataTables configuration
        table = $('#table-battery').DataTable({
            "dom": "lBfrtp",
            "buttons": ['copy', 'csv', 'excel', 'pdf', 'print'],
            "searching": true,
            "stateSave": false,
            "processing": true,
            "serverSide": true,
            "paging": true,
            "pagingType": 'numbers',
            "ajax": {
                "url": "/battery/show",
                "type": "POST",
                "data": {
                    "_token": "{{ csrf_token() }}"
                }
            }
        });

        $("#btn-add").on("click", function() {
            goToPage("/battery/create");
        });
    });
</script>
@endsection
