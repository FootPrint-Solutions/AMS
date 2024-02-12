@extends('template.master')

@section('content')
{{-- Form --}}
<div class="card">
    <div class="card-body">
        {{-- Title --}}
        <div class="card-title h2">
            Battery Technology
            <button class="btn btn-secondary" id="btn-add"><i class="fa fa-plus-circle" aria-hidden="true"></i> Add new technology</button>
        </div>
        <br>

        {{-- Table --}}
        <table class="table table-striped" id="table-battery-tech">
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
        table = $('#table-battery-tech').DataTable({
            "dom": "lBfrtp",
            "buttons": ["copy", "csv", "excel", "pdf", "print"],
            "searching": true,
            "stateSave": false,
            "processing": true,
            "serverSide": true,
            "paging": true,
            "pagingType": "numbers",
            "ajax": {
                "url": "/battery/technology/show",
                "type": "POST",
                "data": {
                    "_token": "{{ csrf_token() }}"
                }
            }
        });

        // Add New Battery Technology button
        $("#btn-add").on("click", function() {
            goToPage("/battery/technology/create");
        });
    });

    function edit(id) {
        goToPage("/battery/technology/edit/" + id);
    }

    function destroy(id) {
        $.ajax({
            url: "/battery/technology/destroy",
            method: "POST",
            data: {
                "_token": "{{ csrf_token() }}",
                "id": id
            },
            success: function(response) {
                // Get response data (in JSON).
                let responseData = JSON.parse(response);

                // Check response data status.
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
