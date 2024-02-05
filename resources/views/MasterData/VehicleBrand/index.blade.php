@extends('template.master')

@section('content')
{{-- Form --}}
<div class="card">
    <div class="card-body">
        {{-- Title --}}
        <div class="card-title h2">
            Vehicle Brand
            <button class="btn btn-secondary" id="btn-add"><i class="fa fa-plus-circle" aria-hidden="true"></i> Add new brand</button>
        </div>
        <br>

        {{-- Table --}}
        <table class="table table-striped" id="table-vehicle-brand">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Name</th>
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
        table = $('#table-vehicle-brand').DataTable({
            "dom": "lBfrtp",
            "buttons": ["copy", "csv", "excel", "pdf", "print"],
            "searching": true,
            "stateSave": false,
            "processing": true,
            "serverSide": true,
            "paging": true,
            "pagingType": "numbers",
            "ajax": {
                "url": "/vehicle/brand/show",
                "type": "POST",
                "data": {
                    "_token": "{{ csrf_token() }}"
                }
            }
        });

        // Add New Vehicle brand button
        $("#btn-add").on("click", function() {
            goToPage("/vehicle/brand/create");
        });
    });

    function edit(id) {
        goToPage("/vehicle/brand/edit/" + id);
    }

    function destroy(id) {
        $.ajax({
            url: "/vehicle/brand/destroy",
            method: "POST",
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
                    // Delete process was succeeded.
                    showSuccessToast(responseData.message);
                } else {
                    // Delete process was failed.
                    showErrorToast(responseData.message);
                }

                // Reload table with updated rows.
                table.ajax.reload();
            }
        });
    }
</script>
@endsection
