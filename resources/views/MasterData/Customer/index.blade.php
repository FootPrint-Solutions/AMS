@extends('template.master')

@section('content')
{{-- Form --}}
<div class="card">
    <div class="card-body">
        {{-- Title --}}
        <div class="card-title h2">
            Customer
            <a type="button" class="btn btn-primary" id="btn-add"><i class="fa fa-plus-circle" aria-hidden="true"></i> Add new customer </a>
        </div>
        <br>

        {{-- Table --}}
        <table class="table table-striped" id="table-customer">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Name</th>
                    <th scope="col">Contact</th>
                    <th scope="col">E-mail</th>
                    <th scope="col">Address</th>
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
        table = $('#table-customer').DataTable({
            "dom": 'lBfrtp',
            "buttons": ['copy', 'csv', 'excel', 'pdf', 'print'],
            "searching": true,
            "stateSave": false,
            "processing": true,
            "serverSide": true,
            "paging": true,
            "pagingType": 'numbers',
            "ajax": {
                "url": "/customer/show",
                "type": "POST",
                "data": {
                    "_token": "{{ csrf_token() }}"
                }
            }
        });

        $('#btn-add').on('click', function() {
            goToPage("/customer/create");
        });
    });

    function edit(id) {
        goToPage("/customer/edit/" + id);
    }

    function destroy(id) {
        $.ajax({
            url: '/customer/destroy',
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
