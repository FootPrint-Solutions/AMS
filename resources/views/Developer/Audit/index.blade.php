@extends('template.master')

@section('content')
    <div class="card">
        <div class="card-body">
            {{-- Title --}}
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">Audit</h3>
                    </div>
                </div>
            </div>
            <br>

            {{-- Table --}}
            <div class="table-responsive">
                <table class="table table-striped" id="table-menu">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">User Model</th>
                            <th scope="col">User </th>
                            <th scope="col">Event</th>
                            <th scope="col">Auditable Type</th>
                            <th scope="col">Auditable ID</th>
                            <th scope="col">Old Values</th>
                            <th scope="col">New Values</th>
                            <th scope="col">URL</th>
                            <th scope="col">IP Address</th>
                            <th scope="col">User Agent</th>
                            <th scope="col">Created At</th>
                            <th scope="col">Updated At</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <script>
        var table;

        $(document).ready(function() {
            // DataTables configuration
            table = $("#table-menu").DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                order: [],
                ajax: {
                    url: "/audit/show",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}"
                    }
                },
                columnDefs: [{
                    targets: [0],
                    orderable: false
                }],
                select: true,
                rowCallback: function(row, data) {
                    console.log(data, data[4]);
                    if (data[4] == "1") {
                        $(row).addClass("bg-secondary");
                    }
                },
            });
        });
    </script>
@endsection
