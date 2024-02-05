@extends('template.master')

@section('content')
{{-- Form --}}
<div class="card">
    <div class="card-body">
        {{-- Title --}}
        <div class="card-title h2">
            Battery
            <button class="btn btn-primary mx-1" id="btn-add"><i class="fa fa-plus-circle" aria-hidden="true"></i> Add new battery</a>
            <button class="btn btn-secondary mx-1" id="btn-add-brand"><i class="fa fa-plus-circle" aria-hidden="true"></i> Add new brand</a>
            <button class="btn btn-secondary mx-1" id="btn-add-subbrand"><i class="fa fa-plus-circle" aria-hidden="true"></i> Add new subbrand category</a>
            <button class="btn btn-secondary mx-1" id="btn-add-usage"><i class="fa fa-plus-circle" aria-hidden="true"></i> Add new usage type</a>
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

        $("#btn-add-brand").on("click", function() {
            goToPage("/battery/brand/create");
        });

        $("#btn-add-subbrand").on("click", function() {
            goToPage("/battery/subbrand/create");
        });

        $("#btn-add-usage").on("click", function() {
            goToPage("/battery/usage/create");
        });
    });

    function edit(id) {
        goToPage("/battery/edit/" + id);
    }

    function destroy(id) {
        $.ajax({
            url: '/battery/destroy',
            method: 'POST',
            data: {
                "_token": "{{ csrf_token() }}",
                "id": id
            },
            success: function(response) {
                // Get response data (in JSON).
                let responseData = JSON.parse(response);

                // Check response data status.
                // Status indicates the success status of company profile update.
                if (responseData.status) {
                    // Company profile update was succeeded.
                    showSuccessToast(responseData.message);
                } else {
                    // Company profile update was failed.
                    showErrorToast(responseData.message);
                }

                // Reload table with updated rows.
                table.ajax.reload();
            }
        });
    }
</script>
@endsection
