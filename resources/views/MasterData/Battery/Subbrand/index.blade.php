@extends('template.master')

@section('content')
{{-- Form --}}
<div class="card">
    <div class="card-body">
        {{-- Title --}}
        <div class="card-title h2">
            Battery Subbrand Category
            <button class="btn btn-secondary" id="btn-add"><i class="fa fa-plus-circle" aria-hidden="true"></i> Add new subbrand category</button>
        </div>
        <br>

        {{-- Table --}}
        <table class="table table-striped" id="table-battery-subbrand">
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
        table = $('#table-battery-subbrand').DataTable({
            "dom": "lBfrtp",
            "buttons": ["copy", "csv", "excel", "pdf", "print"],
            "searching": true,
            "stateSave": false,
            "processing": true,
            "serverSide": true,
            "paging": true,
            "pagingType": "numbers",
            "ajax": {
                "url": "/battery/subbrand/show",
                "type": "POST",
                "data": {
                    "_token": "{{ csrf_token() }}"
                }
            }
        });

        // Add New Vehicle brand button
        $("#btn-add").on("click", function() {
            goToPage("/battery/subbrand/create");
        });
    });

    function edit(id) {
        goToPage("/battery/subbrand/edit/" + id);
    }

    function destroy(id) {
        $.ajax({
            url: "/battery/subbrand/destroy",
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
