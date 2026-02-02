@extends('template.master')

@section('content')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

    {{-- Highlight Card --}}
    <div class="row">
        <div class="col-md-12">
            <div class="card bg-white shadow-lg">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <h5 class="card-title mb-0"><i class="fa-solid fa-boxes-stacked"></i> Total Quantity</h5>
                            <h2 class="mb-0" id="totalQty">0</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            {{-- Title --}}
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">Inventory Recycle Details</h3>
                    </div>
                    <div class="col-auto">
                        <a href="/inventory" class="btn btn-outline-secondary btn-sm">
                            <i class="fa-solid fa-arrow-left"></i> Back to Inventory Recycle
                        </a>
                    </div>
                </div>
            </div>
            <br>

            {{-- Filter Section --}}
            <div class="card">
                <div class="card-body">
                    <form id="filterForm">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Date Range</label>
                                    <input type="text" class="form-control" id="dateRange"
                                        placeholder="Select date range">
                                    <input type="hidden" id="dateStart" name="dateStart">
                                    <input type="hidden" id="dateEnd" name="dateEnd">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>SO/PO Number</label>
                                    <input type="text" class="form-control" id="orderNumber" name="orderNumber"
                                        placeholder="Enter SO/PO number">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Customer/Supplier</label>
                                    <input type="text" class="form-control" id="customerSupplier" name="customerSupplier"
                                        placeholder="Enter customer/supplier name">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Distributor Shop</label>
                                    <select class="form-control" id="distributorShop" name="distributorShop">
                                        <option value="">All Distributor Shops</option>
                                        @foreach ($distributorShops as $shop)
                                            <option value="{{ $shop->id }}">{{ $shop->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Battery</label>
                                    <select class="form-control" id="battery" name="battery">
                                        <option value="">All Batteries</option>
                                        @foreach ($batteries as $battery)
                                            <option value="{{ $battery->id }}">{{ $battery->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <div>
                                        <button type="button" class="btn btn-primary" id="btnFilter">
                                            <i class="fa-solid fa-filter"></i> Apply Filter
                                        </button>
                                        <button type="button" class="btn btn-danger" id="btnReset">
                                            <i class="fa-solid fa-rotate-left"></i> Reset
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped" id="table-inventory-recycle-details">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">SO/PO Date</th>
                            <th scope="col">SO/PO Number</th>
                            <th scope="col">Customer/Supplier</th>
                            <th scope="col">Distributor Shop</th>
                            <th scope="col">Battery</th>
                            <th scope="col">Production Code</th>
                            <th scope="col">Type</th>
                            <th scope="col">Qty</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <script>
        $(function() {
            // Initialize daterangepicker
            $('#dateRange').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Clear',
                    format: 'DD/MM/YYYY'
                }
            });

            $('#dateRange').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format(
                    'DD/MM/YYYY'));
                $('#dateStart').val(picker.startDate.format('YYYY-MM-DD'));
                $('#dateEnd').val(picker.endDate.format('YYYY-MM-DD'));
            });

            $('#dateRange').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
                $('#dateStart').val('');
                $('#dateEnd').val('');
            });

            // Initialize Select2 for dropdowns
            $('#distributorShop').select2({
                placeholder: 'All Distributor Shops',
                allowClear: true
            });

            $('#battery').select2({
                placeholder: 'All Batteries',
                allowClear: true
            });

            // Function to load total qty
            function loadTotalQty() {
                $.ajax({
                    url: '/inventory/recycle/details/total-qty',
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        dateStart: $('#dateStart').val(),
                        dateEnd: $('#dateEnd').val(),
                        orderNumber: $('#orderNumber').val(),
                        customerSupplier: $('#customerSupplier').val(),
                        distributorShop: $('#distributorShop').val(),
                        battery: $('#battery').val()
                    },
                    success: function(response) {
                        $('#totalQty').text(response.totalQty.toLocaleString());
                    }
                });
            }

            // Load initial total qty
            loadTotalQty();

            // DataTables config for table-inventory-recycle-details
            var table = $("#table-inventory-recycle-details").DataTable({
                lengthMenu: [
                    [10, 25, 50, 100],
                    [10, 25, 50, 100]
                ],
                pageLength: 25,
                responsive: true,
                processing: true,
                serverSide: true,
                order: [
                    [1, 'desc']
                ],
                ajax: {
                    url: "/inventory/recycle/details/show",
                    type: "POST",
                    data: function(d) {
                        d._token = "{{ csrf_token() }}";
                        d.dateStart = $('#dateStart').val();
                        d.dateEnd = $('#dateEnd').val();
                        d.orderNumber = $('#orderNumber').val();
                        d.customerSupplier = $('#customerSupplier').val();
                        d.distributorShop = $('#distributorShop').val();
                        d.battery = $('#battery').val();
                    }
                },
                columns: [{
                        data: 0,
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        },
                        orderable: false
                    },
                    {
                        data: 1,
                        orderable: true
                    },
                    {
                        data: 2,
                        orderable: true
                    },
                    {
                        data: 3,
                        orderable: true
                    },
                    {
                        data: 4,
                        orderable: true
                    },
                    {
                        data: 5,
                        orderable: true
                    },
                    {
                        data: 6,
                        orderable: true
                    },
                    {
                        data: 7,
                        className: "text-center",
                        orderable: true
                    },
                    {
                        data: 8,
                        className: "text-end",
                        orderable: true
                    }
                ],
                dom: "lBfrtip",
                buttons: [{
                    extend: 'excel',
                    text: '<i class="fa-solid fa-file-excel"></i> Export',
                    className: "btn btn-outline-success btn-sm ml-1",
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6, 7, 8]
                    }
                }],
                language: getDatatablesLanguangeConfigurations("Inventory Recycle Details")
            });

            // Apply filter button
            $('#btnFilter').on('click', function() {
                table.ajax.reload();
                loadTotalQty();
            });

            // Reset filter button
            $('#btnReset').on('click', function() {
                $('#filterForm')[0].reset();
                $('#dateRange').val('');
                $('#dateStart').val('');
                $('#dateEnd').val('');
                $('#distributorShop').val('').trigger('change');
                $('#battery').val('').trigger('change');
                table.ajax.reload();
                loadTotalQty();
            });
        });
    </script>
@endsection
