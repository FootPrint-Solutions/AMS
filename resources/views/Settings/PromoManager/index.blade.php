@extends('template.master')

@section('content')
    {{-- Form --}}
    <div class="card">
        <div class="card-body">
            {{-- Title --}}
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">Battery Price Promo Manager</h3>
                    </div>
                    <div class="col-auto text-end float-end ms-auto download-grp">
                        <button id="btn-add" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add
                            New Promo</button>
                    </div>
                </div>
            </div>
            <br>

            {{-- Table --}}
            <table class="table table-striped" id="table-promo">
                <thead>
                    <tr>
                        <th scope="col" class="table-col-no">#</th>
                        <th scope="col">Name</th>
                        <th scope="col">Period Start</th>
                        <th scope="col">Period End</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    {{-- DataTables Configurations --}}
    <script>
        var table;

        $(document).ready(function() {
            // DataTables configuration
            table = $("#table-promo").DataTable({
                lengthMenu: [
                    [5, 10, 25],
                    [5, 10, 25]
                ],
                responsive: true,
                processing: true,
                serverSide: true,
                order: [],
                ajax: {
                    url: "/promo/show",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}"
                    }
                },
                columnDefs: [{
                    targets: [0],
                    orderable: false
                }, {
                    targets: [0],
                    className: 'dt-body-center'
                }],
                dom: "lBfrtip",
                buttons: getDatatablesButtonConfigurations(),
                language: getDatatablesLanguangeConfigurations("Promo"),
                select: true,
            });

            // Load DataTables toolbar component.
            appendDatatablesToolbar(4, "/promo/edit/", "/promo/destroy");
        });
    </script>

    {{-- Click Handler --}}
    <script>
        $('#btn-add').on('click', function() {
            goToPage("/promo/create");
        });
    </script>
@endsection
