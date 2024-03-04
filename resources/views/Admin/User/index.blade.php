@extends('template.master')

@section('content')
    {{-- Form --}}
    <div class="card">
        <div class="card-body">
            {{-- Title --}}
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">User Manager</h3>
                    </div>
                </div>
            </div>
            <br>

            {{-- Table --}}
            <table class="table table-striped" id="table-user">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">User</th>
                        <th scope="col">Allowed Menu</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <script>
        var table;

        $(document).ready(function() {
            // DataTables configuration
            table = $("#table-user").DataTable({
                lengthMenu: [
                    [5, 10, 25],
                    [5, 10, 25]
                ],
                responsive: true,
                processing: true,
                serverSide: true,
                order: [],
                ajax: {
                    url: "/user/show",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}"
                    }
                },
                columnDefs: [{
                    targets: [0],
                    orderable: false
                }],
                buttons: [],
                dom: "lBrtp",
                select: true,
            });

            // Load DataTables toolbar component.
            appendDatatablesToolbar(3);

            // Add New Menu button
            $("#btn-add").on("click", function() {
                goToPage("/menu/create");
            });

            // Add New Menu Parent button
            $("#btn-add-parent").on("click", function() {
                goToPage("/menu/parent/create");
            });
        });

        function edit(id) {
            goToPage("/user/edit/" + id);
        }
    </script>
@endsection