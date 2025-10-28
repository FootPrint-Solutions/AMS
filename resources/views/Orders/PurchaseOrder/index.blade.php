@extends('template.master')

@section('content')
    {{-- Form --}}
    <div class="card bg-white">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Purchase Order</h3>
                </div>


                <div class="col-auto text-end float-end ms-auto download-grp">
                    <button id="btn-add" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add New Purchase
                        Order</button>
                </div>
            </div>
        </div>
        <div class="card-body">
            {{-- Filters --}}
            <div class="row mb-3">
                <div class="col-md-3">
                    <label for="filter-status">Status</label>
                    <select class="form-control" id="filter-status" onchange="reloadTable()">
                        <option value="">All Status</option>
                        <option value="draft">Draft</option>
                        <option value="posted">Posted</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filter-supplier">Supplier</label>
                    <select class="form-control" id="filter-supplier" onchange="reloadTable()">
                        <option value="">All Suppliers</option>
                        @foreach ($data['suppliers'] as $supplier)
                            <option value="{{ $supplier['id'] }}">{{ $supplier['name'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filter-date-start">Date From</label>
                    <input type="date" class="form-control" id="filter-date-start" onchange="reloadTable()">
                </div>
                <div class="col-md-3">
                    <label for="filter-date-end">Date To</label>
                    <input type="date" class="form-control" id="filter-date-end" onchange="reloadTable()">
                </div>
            </div>

        </div>
    </div>

    <div class="card">
        <div class="card-body">
            {{-- Table --}}
            <table class="table table-striped" id="table-purchase-order">
                <thead>
                    <tr>
                        <th scope="col" class="table-col-no">#</th>
                        <th scope="col">PO Number</th>
                        <th scope="col">Invoice Number</th>
                        <th scope="col">Date</th>
                        <th scope="col">Supplier</th>
                        <th scope="col">Subtotal</th>
                        <th scope="col">Discount</th>
                        <th scope="col">Total</th>
                        <th scope="col">Payment Status</th>
                        <th scope="col">Status</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    {{-- DataTables Configuration --}}
    <script>
        $(document).ready(function() {
            loadTable();
        });

        function loadTable() {
            $('#table-purchase-order').DataTable({
                processing: true,
                serverSide: true,
                destroy: true,
                ajax: {
                    url: "/purchase-order/show",
                    type: "POST",
                    data: function(d) {
                        d._token = "{{ csrf_token() }}";
                        d.status = $("#filter-status").val();
                        d.supplier_id = $("#filter-supplier").val();
                        d.dateStart = $("#filter-date-start").val();
                        d.dateEnd = $("#filter-date-end").val();
                    }
                },
                columns: [{
                        data: 0,
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 1
                    },
                    {
                        data: 2
                    },
                    {
                        data: 3
                    },
                    {
                        data: 4
                    },
                    {
                        data: 5,
                        className: 'text-end'
                    },
                    {
                        data: 6,
                        className: 'text-end'
                    },
                    {
                        data: 7,
                        className: 'text-end'
                    },
                    {
                        data: 8,
                        className: 'text-center'
                    },
                    {
                        data: 9,
                        className: 'text-center'
                    }
                ],
                order: [
                    [0, 'desc']
                ],
                language: {
                    processing: "Loading...",
                    search: "Search:",
                    lengthMenu: "Show _MENU_ entries",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                    infoEmpty: "Showing 0 to 0 of 0 entries",
                    infoFiltered: "(filtered from _MAX_ total entries)",
                    loadingRecords: "Loading...",
                    zeroRecords: "No matching records found",
                    emptyTable: "No data available in table",
                    paginate: {
                        first: "First",
                        previous: "Previous",
                        next: "Next",
                        last: "Last"
                    }
                }
            });
        }

        function reloadTable() {
            $('#table-purchase-order').DataTable().ajax.reload();
        }

        function deletePurchaseOrder(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "/purchase-order/destroy/" + id,
                        type: "DELETE",
                        data: {
                            "_token": "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            if (response.status === 'success') {
                                Swal.fire('Deleted!', response.message, 'success');
                                reloadTable();
                            } else {
                                Swal.fire('Error!', response.message, 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Error!', 'An error occurred while deleting.', 'error');
                        }
                    });
                }
            });
        }
    </script>
@endsection
