@extends('template.master')

@section('content')
    {{-- Form --}}
    <div class="card">
        <div class="card-body">
            {{-- Title --}}
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">Shop</h3>
                    </div>
                    <div class="col-auto text-end float-end ms-auto download-grp">
                        <button id="btn-add" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add
                            New Shop</button>
                    </div>
                </div>
            </div>
            <br>

            {{-- Table --}}
            <table class="table table-striped" id="table-distributor-shop">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Name</th>
                        <th scope="col">Distributor</th>
                        <th scope="col">Address</th>
                        <th scope="col">Contact Person</th>
                        <th scope="col">Contact</th>
                        <th scope="col">E-mail</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    {{-- Detail Modal --}}
    <div class="modal fade" id="shop-detail-modal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Items Detail</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <table class="table table-striped" id="table-distributor-shop-detail">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Price</th>
                                <th scope="col">URL</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        var table;
        var tableDetail;

        $(document).ready(function() {
            // DataTables configuration
            table = $("#table-distributor-shop").DataTable({
                lengthMenu: [
                    [5, 10, 25],
                    [5, 10, 25]
                ],
                responsive: true,
                processing: true,
                serverSide: true,
                order: [],
                ajax: {
                    url: "/distributor/shop/show",
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
                buttons: getDatatablesButtonConfigurations([{
                    text: "<i class='fas fa-eye'></i> View Detail",
                    action: function(e, dt, node, config) {
                        // Show popup modal.
                        $('#shop-detail-modal').modal("show");

                        // Set detail table inside modal.
                        tableDetail = $("#table-distributor-shop-detail").DataTable({
                            ajax: {
                                url: "/distributor/shop/show",
                                type: "POST",
                                data: {
                                    _token: "{{ csrf_token() }}",
                                }
                            },
                        });
                    },
                    className: "btn btn-outline-info btn-sm",
                }]),
                language: getDatatablesLanguangeConfigurations("Distributor Shop"),
                select: true,
            });

            // Load DataTables toolbar component.
            appendDatatablesToolbar(7, "/distributor/shop/edit/", "/distributor/shop/destroy");

            // Add New Store button
            $("#btn-add").on("click", function() {
                goToPage("/distributor/shop/create");
            });
        });
    </script>
@endsection
