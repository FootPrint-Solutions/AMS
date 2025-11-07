@extends('template.master')

@section('content')
    {{-- Form --}}
    <div class="card">
        <div class="card-body">
            {{-- Title --}}
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">Battery Recycle</h3>
                    </div>
                    <div class="col-auto text-end float-end ms-auto download-grp">
                        <button id="btn-add" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add
                            New Battery Recycle</button>
                    </div>
                </div>
            </div>
            <br>

            {{-- Table --}}
            <table class="table table-striped" id="table-battery-recycle">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Name</th>
                        <th scope="col">Note</th>
                        <th scope="col">id</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <script>
        var table;

        $(document).ready(function() {
            // DataTables configuration
            table = $("#table-battery-recycle").DataTable({
                lengthMenu: [
                    [5, 10, 25],
                    [5, 10, 25]
                ],
                responsive: true,
                processing: true,
                serverSide: true,
                order: [],
                ajax: {
                    url: "{{ route('battery.recycle.show') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}"
                    }
                },
                columns: [{
                        data: 0,
                        name: '#',
                        orderable: false
                    },
                    {
                        data: 1,
                        name: 'name'
                    },
                    {
                        data: 2,
                        name: 'note'
                    },
                    // hide kolom id
                    {
                        data: 3,
                        name: 'id',
                        visible: false
                    }
                ],
                dom: "lBfrtip",
                buttons: getDatatablesButtonConfigurations(),
                language: getDatatablesLanguangeConfigurations("Battery Recycle"),
                select: true,
            });

            // Load DataTables toolbar component.
            appendDatatablesToolbar(3, "/battery-recycle/edit/", "/battery-recycle/destroy");

            // Add New Battery Recycle button
            $("#btn-add").on("click", function() {
                goToPage("/battery-recycle/create");
            });
        });
    </script>
@endsection
