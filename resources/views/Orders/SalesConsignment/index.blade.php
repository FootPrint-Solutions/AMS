@extends('template.master')

@section('content')
    {{-- Table --}}
    <div class="d-none d-lg-block">
        <div class="card bg-white">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">Sales Consignment</h3>
                    </div>

                    <div class="col-auto text-end float-end ms-auto download-grp">
                        <button id="btn-add" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add
                            New Sales Consignment</button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row align-items-center mb-3">
                    <div class="col-md-1 text-md-right text-left font-weight-bold">
                        Date
                    </div>
                    <div class="col-md-4">
                        <div class="row align-items-center">
                            <div class="col-5 pr-0">
                                <input type="date" class="form-control" id="input-sales-consignment-date-start"
                                    onchange="reloadTable()">
                            </div>
                            <div class="col-2 text-center px-0">
                                to
                            </div>
                            <div class="col-5 pl-0">
                                <input type="date" class="form-control" id="input-sales-consignment-date-end"
                                    onchange="reloadTable()">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-1 text-md-right text-left font-weight-bold">
                        Status
                    </div>
                    <div class="col-md-4">
                        <select class="form-control" id="sales-consignment-status-filter" onchange="onStatusFilterChange()">
                            <option value="all">All</option>
                            <option value="draft">Draft</option>
                            <option value="posted">Posted</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                    <script>
                        function onStatusFilterChange() {
                            table.ajax.reload();
                        }

                        function getActiveFilter() {
                            return $('#sales-consignment-status-filter').val();
                        }
                    </script>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">


                {{-- DataTable --}}
                <table id="sales-consignment-table" class="table table-striped" style="width:100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Consignment Number</th>
                            <th>Date</th>
                            <th>Total Amount</th>
                            <th>Payment Status</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    {{-- Scripts --}}
    <script>
        let table;

        $(document).ready(function() {
            // Initialize DataTable
            table = $("#sales-consignment-table").DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "/sales-consignment/show",
                    data: function(d) {
                        d.status = getActiveFilter();
                    }
                },
                columns: [{
                        title: 'No',
                        data: 0,
                        orderable: false,
                        searchable: false
                    },
                    {
                        title: 'Consignment Number',
                        data: 1
                    },
                    {
                        title: 'Date',
                        data: 2
                    },
                    {
                        title: 'Total Amount',
                        data: 3,
                        render: function(data, type, row) {
                            if (type === 'display' || type === 'filter') {
                                return parseInt(data).toLocaleString('id-ID');
                            }
                            return data;
                        }
                    },
                    {
                        title: 'Payment Status',
                        data: 4,
                        orderable: false,
                        searchable: false
                    },
                    {
                        title: 'Status',
                        data: 5,
                        orderable: false,
                        searchable: false
                    }
                ],
                dom: "lBfrtip",
                buttons: getDatatablesButtonConfigurations(),
                language: getDatatablesLanguangeConfigurations("Sales Consignment"),
                order: [
                    [2, 'desc']
                ] // Order by date descending
            });

            // Filter buttons
            $('.filter-btn').on('click', function() {
                $('.filter-btn').removeClass('active');
                $(this).addClass('active');
                table.ajax.reload();
            });
        });

        function getActiveFilter() {
            return $('.filter-btn.active').data('status');
        }

        function reloadTable() {
            table.ajax.reload();
        }

        // Action handlers
        function viewConsignment(id) {
            window.location.href = `/sales-consignment/${id}`;
        }

        function postConsignment(id) {
            Swal.fire({
                title: 'Post Consignment',
                text: 'Are you sure you want to post this consignment?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Post it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/sales-consignment/post',
                        type: 'PUT',
                        data: {
                            _token: '{{ csrf_token() }}',
                            id: id
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire('Success!', response.message, 'success');
                                table.ajax.reload();
                            } else {
                                Swal.fire('Error!', response.message, 'error');
                            }
                        },
                        error: function(xhr) {
                            let errorMessage = xhr.responseJSON?.message || 'An error occurred';
                            Swal.fire('Error!', errorMessage, 'error');
                        }
                    });
                }
            });
        }

        function deleteConsignment(id) {
            Swal.fire({
                title: 'Delete Consignment',
                text: 'Are you sure you want to delete this consignment? This action cannot be undone!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Delete it!',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#d33'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/sales-consignment/destroy',
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}',
                            id: id
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire('Deleted!', response.message, 'success');
                                table.ajax.reload();
                            } else {
                                Swal.fire('Error!', response.message, 'error');
                            }
                        },
                        error: function(xhr) {
                            let errorMessage = xhr.responseJSON?.message || 'An error occurred';
                            Swal.fire('Error!', errorMessage, 'error');
                        }
                    });
                }
            });
        }

        $('#btn-add').on('click', function() {
            window.location.href = '/sales-consignment/createnoids';
        });
    </script>
@endsection
