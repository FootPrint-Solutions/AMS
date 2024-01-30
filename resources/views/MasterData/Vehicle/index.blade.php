@extends('template.master')

@section('content')
{{-- Form --}}
<div class="card">
    <div class="card-body">
        {{-- Title --}}
        <div class="card-title h2">
            Vehicle
            <a href="/vehicle/create" type="button" class="btn btn-primary"><i class="fa fa-plus-circle" aria-hidden="true"></i> Add new vehicle</a>
        </div>
        <br>

        {{-- Table --}}
        <table class="table table-striped" id="table-vehicle">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Name</th>
                    <th scope="col">Brand</th>
                    <th scope="col">URL</th>
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
        table = $('#table-vehicle').DataTable({
            "dom": 'lBfrtp',
            "buttons": ['copy', 'csv', 'excel', 'pdf', 'print'],
            "searching": true,
            "stateSave": false,
            "processing": true,
            "serverSide": true,
            "paging": true,
            "pagingType": 'numbers',
            "ajax": {
                "url": "/vehicle/show",
                "type": "POST",
                "data": {
                    "_token": "{{ csrf_token() }}"
                }
            }
        });

        $('#btn-add').on('click', function() {
            $.ajax({
                url: '/vehicle/create',
                success: function(response) {
                    $('#main-wrapper').html(response);
                }
            });
        });
    });

    function edit(id) {
        $.ajax({
            url: '/vehicle/edit/' + id,
            success: function(response) {
                $('#main-wrapper').html(response);
            }
        });
    }

    function destroy(id) {
        $.ajax({
            url: '/vehicle/destroy',
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
