@extends('template.master')

@section('content')
    {{-- Form --}}
    <div class="card">
        <div class="card-body">
            {{-- Title --}}
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">Tax</h3>
                    </div>
                    <div class="col-auto text-end float-end ms-auto download-grp">
                        <button id="btn-add" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add
                            New Tax</button>
                    </div>
                </div>
            </div>
            <br>

            {{-- Table --}}
            <table class="table table-striped" id="table-tax">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Tax Percentage</th>
                        <th scope="col">Valid Until</th>
                        <th scope="col">Status</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <script>
        var table;
        $(document).ready(function() {
            // DataTables configuration
            table = $("#table-tax").DataTable({
                lengthMenu: [
                    [5, 10, 25],
                    [5, 10, 25]
                ],
                responsive: true,
                processing: true,
                serverSide: true,
                order: [],
                ajax: {
                    url: "/tax/show",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}"
                    }
                },
                columnDefs: [{
                    targets: [0],
                    orderable: false
                }],
                dom: "lBfrtip",
                buttons: getDatatablesButtonConfigurations(),
                language: getDatatablesLanguangeConfigurations("Tax"),
                select: true,
            });

            // Load DataTables toolbar component.
            appendDatatablesToolbar(4, "/tax/edit/", "/tax/destroy");

            $('#btn-add').on('click', function() {
                goToPage("/tax/create");
            });
        });
    </script>
@endsection
