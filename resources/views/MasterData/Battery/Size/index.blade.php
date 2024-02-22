@extends('template.master')

@section('content')
{{-- Form --}}
<div class="card">
    <div class="card-body">
        {{-- Title --}}
        <div class="card-title h2">
            Battery Size Category
            <button class="btn btn-secondary" id="btn-add"><i class="fa fa-plus-circle" aria-hidden="true"></i> Add new size category</button>
        </div>
        <br>

        {{-- Table --}}
        <table class="table table-striped" id="table-battery-size">
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
        table = $('#table-battery-size').DataTable({
            "dom": "lBfrtp",
            "buttons": ["copy", "csv", "excel", "pdf", "print"],
            "searching": true,
            "stateSave": false,
            "processing": true,
            "serverSide": true,
            "paging": true,
            "pagingType": "numbers",
            "ajax": {
                "url": "/battery/size/show",
                "type": "POST",
                "data": {
                    "_token": "{{ csrf_token() }}"
                }
            }
        });

        // Add New Battery Size Category button
        $("#btn-add").on("click", function() {
            goToPage("/battery/size/create");
        });
    });

    function edit(id) {
        goToPage("/battery/size/edit/" + id);
    }

    function destroy(id) {
        sendDestroyRequest(id, "/battery/size/destroy", function() {
            // Reload the index table.
            table.ajax.reload();
        });
    }
</script>
@endsection
