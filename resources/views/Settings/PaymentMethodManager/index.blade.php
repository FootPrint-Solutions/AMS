@extends('template.master')

@section('content')
    {{-- Form --}}
    <div class="card">
        <div class="card-body">
            {{-- Title --}}
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">Payment Method</h3>
                    </div>
                    <div class="col-auto text-end float-end ms-auto download-grp">
                        <button id="btn-add" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add
                            New Payment Method</button>
                    </div>
                </div>
            </div>
            <br>

            {{-- Table --}}
            <table class="table table-striped" id="table-payment-method">
                <thead>
                    <tr>
                        <th scope="col" class="table-col-no">#</th>
                        <th scope="col">Name</th>
                        <th scope="col" class="table-col-status">Status</th>
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
            table = $("#table-payment-method").DataTable({
                lengthMenu: [
                    [5, 10, 25],
                    [5, 10, 25]
                ],
                responsive: true,
                processing: true,
                serverSide: true,
                order: [],
                ajax: {
                    url: "/payment/show",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}"
                    }
                },
                columnDefs: [{
                    targets: [0],
                    orderable: false
                }, {
                    targets: [0, -1],
                    className: 'dt-body-center'
                }],
                dom: "lBfrtip",
                buttons: getDatatablesButtonConfigurations(),
                language: getDatatablesLanguangeConfigurations("Payment Method"),
                select: true,
                rowCallback: function(row, data) {
                    if (data[4] == 0) {
                        $('td', row).addClass("text-muted");
                    }
                }
            });

            // Load DataTables toolbar component.
            appendDatatablesToolbar(3, "/payment/edit/", null, "/payment/toggle");
        });
    </script>

    {{-- Click Handler --}}
    <script>
        $('#btn-add').on('click', function() {
            goToPage("/payment/create");
        });
    </script>
@endsection
