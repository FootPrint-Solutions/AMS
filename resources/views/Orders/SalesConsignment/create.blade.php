@extends('template.master')

@section('content')
    <link rel="stylesheet" href="{{ asset('plugins/bootstrap5-toggle/css/bootstrap5-toggle.min.css') }}">
    <style>
        #MapsAddressFinder {
            height: 400px;
            width: 100%;
            margin-bottom: 20px;
        }

        /* Styling for readonly fields */
        .form-control[readonly] {
            background-color: #f8f9fa;
            border-color: #dee2e6;
        }

        /* Styling for editable discount fields */
        #total-discount {
            border-left: 4px solid #28a745;
            background-color: #f8fff9;
        }

        .discount {
            border-left: 3px solid #ffc107;
            background-color: #fffef7;
        }

        /* Summary section styling */
        .summary-section {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
        }

        .dataTables_filter {
            margin-top: 0px;
        }
    </style>

    <div class="d-none d-lg-block">
        <div class="card">
            <div class="card-body">
                {{-- Title --}}
                <div class="card-title h5">
                    @if (isset($data['type']) && $data['type'] == 'edit')
                        Edit
                    @else
                        Add New
                    @endif
                    Sales Consignment
                </div>
                <br>

                <form id="quotation-form">
                    @csrf

                    {{-- Quotation Number & Date --}}
                    <div class="row mb-5">
                        {{-- Sales Consignment Number --}}
                        <div class="col">
                            <div class="form-group local-forms">
                                <label for="sales-consignment-number">Sales Consignment Number</label>
                                <input type="text" class="form-control" id="sales-consignment-number"
                                    name="salesconsignmentnumber" placeholder="Enter consignment number" readonly
                                    value="{{ $data['consignment_number'] ?? '' }}">
                            </div>
                        </div>

                        {{-- Sales Consignment Date --}}
                        <div class="col">
                            <div class="form-group local-forms">
                                <label for="sales-consignment-date">Sales Consignment Date</label>
                                <input type="date" class="form-control" id="sales-consignment-date"
                                    name="salesconsignmentdate" value="{{ $data['consignment_date'] ?? date('Y-m-d') }}">
                            </div>
                        </div>

                        {{-- Vendor --}}
                        <div class="col">
                            <div class="form-group local-forms">
                                <label for="distributor">Vendor <span class="login-danger">*</span></label>
                                <select class="form-control select" id="vendor_id" name="vendor_id"
                                    data-placeholder="Select Vendor">
                                    <option value="">Select Vendor</option>
                                    @foreach ($data['distributors'] as $distributor)
                                        <option value="{{ $distributor['id'] }}">
                                            {{ $distributor['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- To --}}
                        <div class="col">
                            <div class="form-group local-forms">
                                <label for="to">Ship To <span class="login-danger">*</span></label>
                                <select class="form-control select" id="ship_to_id" name="ship_to_id"
                                    data-placeholder="Select Shop" required>
                                    <option value="">Select Shop</option>
                                    @foreach ($data['shops'] as $shop)
                                        <option value="{{ $shop['id'] }}">
                                            {{ $shop['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Button Find Sales Invoice --}}
                        <div class="col d-flex align-items-end">
                            <div class="form-group local-forms">
                                <button type="button" class="btn btn-primary" id="btn-find-sales-invoice">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <table class="table table-striped mt-5" id="selected-sales-invoices-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>No</th>
                                        <th>Sales Invoice Number</th>
                                        <th>Invoice Number</th>
                                        <th>Date</th>
                                        <th>Customer Name</th>
                                        <th>Total</th>
                                        <th>Address</th>
                                        <th>Discount</th>
                                        <th>Note</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- Rows will be dynamically added via JavaScript --}}
                                </tbody>
                            </table>
                        </div>
                    </div>


                    {{-- Summary Section --}}
                    <div class="summary-section">
                        <h6 class="mb-3">
                            Consignment Summary
                            <span class="badge bg-secondary ms-2" id="discount-savings-badge" style="display: none;">
                                Total Discount9: Rp <span id="total-savings">0</span>
                            </span>
                        </h6>
                        <div class="row">
                            <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                                <div class="form-group local-forms">
                                    <label for="subtotal">Subtotal (Before Discounts)</label>
                                    <input type="text" class="form-control" id="subtotal" name="subtotal" readonly>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                                <div class="form-group local-forms">
                                    <label for="item-discounts-total">Total Item Discounts</label>
                                    <input type="text" class="form-control" id="item-discounts-total"
                                        name="item_discounts_total" readonly>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                                <div class="form-group local-forms">
                                    <label for="total-discount">
                                        Discount (Overall)
                                    </label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="total-discount" name="total_discount"
                                            placeholder="Enter additional discount amount" value="0">
                                        <button class="btn btn-outline-secondary" type="button" id="reset-total-discount"
                                            title="Reset to 0">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                                <div class="form-group local-forms">
                                    <label for="grand-total">
                                        <strong>Grand Total</strong>
                                        <i class="fas fa-info-circle text-primary ms-1" data-bs-toggle="tooltip"
                                            data-bs-placement="top"
                                            title="Grand Total = Subtotal - Item Discounts - Additional Discount"></i>
                                    </label>
                                    <input type="text" class="form-control font-weight-bold" id="grand-total"
                                        name="grand_total" readonly
                                        style="font-weight: bold; font-size: 1.1em; background-color: #e8f5e8;">
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                                <div class="form-group local-forms">
                                    <label for="status">Status <span class="login-danger">*</span></label>
                                    <select class="form-control select" id="status" name="status" required>
                                        <option value="draft" selected>Draft</option>
                                        <option value="posted">Posted</option>
                                        <option value="completed">Completed</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Additional Form Fields --}}
                    <div class="row mt-3">

                    </div>

                    {{-- Hidden Fields --}}
                    <input type="hidden" name="discount" id="discount-percentage" value="0">
                    <input type="hidden" name="discountprice" id="discount-price-hidden" value="0">
                    <input type="hidden" name="totalexpenses" id="total-expenses-hidden" value="0">

                    {{-- Submit Button --}}
                    <div class="row mt-4">
                        <div class="col-12 text-end">
                            <button type="button" class="btn btn-danger me-2" onclick="window.history.back()">
                                <i class="fas fa-arrow-left"></i> Back
                            </button>
                            <button type="submit" class="btn btn-success" id="btn-save-consignment">
                                <i class="fas fa-save"></i> Save Sales Consignment
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>


    {{-- Modal Show Invoice --}}
    <div class="modal fade" id="modal-show-invoice" tabindex="-1" aria-labelledby="modal-show-invoice-label"
        aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <div class="container-fluid px-0">
                        <div class="row mb-3 align-items-end">
                            <div class="col-md-5">
                                <label for="filter-start-date" class="form-label mb-1">Start Date</label>
                                <input type="date" class="form-control" id="filter-start-date"
                                    name="filter_start_date" value="{{ date('Y-m-d', strtotime('-30 days')) }}">
                            </div>
                            <div class="col-md-5">
                                <label for="filter-end-date" class="form-label mb-1">End Date</label>
                                <input type="date" class="form-control" id="filter-end-date" name="filter_end_date"
                                    value="{{ date('Y-m-d') }}">
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle" id="table-sales-invoice">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width:40px;" class="text-center">
                                            <input type="checkbox" id="select-all-invoices" class="form-check-input">
                                        </th>
                                        <th style="width:40px;" class="text-center">No</th>
                                        <th>Sales Invoice Number</th>
                                        <th>Invoice Number</th>
                                        <th>Date</th>
                                        <th>Customer Name</th>
                                        <th class="text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- Data will be populated via AJAX DataTable --}}
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="btn-select-invoices">Select Invoices</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let salesInvoiceDataTable = null;
        const distributorSelect = $('#vendor_id');
        const btnFindSalesInvoice = $('#btn-find-sales-invoice');
        const modalShowInvoice = new bootstrap.Modal(document.getElementById('modal-show-invoice'));

        function initializeSalesInvoiceDataTable() {
            if (salesInvoiceDataTable) salesInvoiceDataTable.destroy();

            salesInvoiceDataTable = $('#table-sales-invoice').DataTable({
                processing: true,
                serverSide: true,
                searching: true,
                paging: true,
                lengthChange: true,
                pageLength: 10,
                autoWidth: false,
                lengthMenu: [
                    [10, 25, 50, 100],
                    [10, 25, 50, 100]
                ],
                ajax: {
                    url: '/sales-invoices/by-distributor-datatable',
                    type: 'POST',
                    data: function(d) {
                        d._token = '{{ csrf_token() }}';
                        d.distributor_id = distributorSelect.val();
                        d.start_date = $('#filter-start-date').val();
                        d.end_date = $('#filter-end-date').val();
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to load sales invoices.'
                        });
                    }
                },
                columns: [{
                        data: 'checkbox',
                        name: 'checkbox',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'number',
                        name: 'number',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'sales_invoice_number',
                        name: 'sales_invoice_number',
                        className: 'text-center'
                    },
                    {
                        data: 'invoice_number',
                        name: 'invoice_number',
                        className: 'text-center'
                    },
                    {
                        data: 'date',
                        name: 'date',
                        className: 'text-center'
                    },
                    {
                        data: 'customer_name',
                        name: 'customer_name'
                    },
                    {
                        data: 'total',
                        name: 'total',
                        className: 'text-end'
                    }
                ],
                order: [
                    [2, 'desc']
                ],
                language: {
                    processing: "Loading sales invoices...",
                    emptyTable: "No sales invoices found for selected distributor and date range",
                    zeroRecords: "No matching sales invoices found"
                },
                drawCallback: function() {
                    updateSelectAllCheckbox();
                }
            });
        }

        function updateSelectAllCheckbox() {
            const total = $('#table-sales-invoice tbody .select-invoice').length;
            const checked = $('#table-sales-invoice tbody .select-invoice:checked').length;
            const $master = $('#select-all-invoices');
            if (total === 0) {
                $master.prop({
                    indeterminate: false,
                    checked: false
                });
                return;
            }
            if (checked === total) $master.prop({
                indeterminate: false,
                checked: true
            });
            else if (checked > 0) $master.prop({
                indeterminate: true
            });
            else $master.prop({
                indeterminate: false,
                checked: false
            });
        }

        $('#select-all-invoices').on('change', function() {
            const v = $(this).is(':checked');
            $('#table-sales-invoice tbody .select-invoice').prop('checked', v);
        });

        $('#table-sales-invoice').on('change', '.select-invoice', updateSelectAllCheckbox);

        btnFindSalesInvoice.on('click', function() {
            const distributorId = distributorSelect.val();
            if (!distributorId) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Vendor Required',
                    text: 'Please select a vendor first.'
                });
                return;
            }
            modalShowInvoice.show();
            salesInvoiceDataTable ? salesInvoiceDataTable.ajax.reload() : initializeSalesInvoiceDataTable();
        });

        $('#filter-start-date, #filter-end-date').on('change', function() {
            if (salesInvoiceDataTable && distributorSelect.val()) salesInvoiceDataTable.ajax.reload();
        });

        distributorSelect.on('change', function() {
            if (salesInvoiceDataTable) salesInvoiceDataTable.ajax.reload();
        });

        $('#btn-select-invoices').on('click', function() {
            const ids = [];
            $('.select-invoice:checked').each(function() {
                ids.push($(this).data('id'));
            });
            if (ids.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'No Invoice Selected',
                    text: 'Please select at least one invoice.'
                });
                return;
            }

            const $btn = $(this).prop('disabled', true).html(
                '<i class="fas fa-spinner fa-spin"></i> Processing...');
            $.ajax({
                url: '/sales-invoices/add-consignment-temp',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    invoice_ids: ids
                },
                success: function(response) {
                    const $tbody = $('#selected-sales-invoices-table tbody').empty();
                    let no = 1;
                    response.data.forEach(inv => {
                        const total = inv.total ?? 0;
                        const row = `
                    <tr>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm delete-invoice-row">
                                <i class="fas fa-trash"></i>
                            </button>
                            <input type="hidden" name="invoice_ids[]" value="${inv.id}">
                        </td>
                        <td>${no++}</td>
                        <td>${inv.sales_invoice_number ?? ''}</td>
                        <td>${inv.invoice_number ?? ''}</td>
                        <td>${inv.date ?? ''}</td>
                        <td>${inv.customer ? inv.customer.name : ''}</td>
                        <td>Rp ${Math.floor(total).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")}</td>
                        <td>${inv.address ?? ''}</td>
                        <td><input type="number" class="form-control form-input discount" data-id="${inv.id}" value="0" min="0" max="${total}"></td>
                        <td><input type="text" class="form-control form-input note" data-id="${inv.id}" value="" placeholder="Optional note"></td>
                        <td><input type="text" class="form-control form-input subtotal" data-id="${inv.id}" data-original-total="${total}" value="${Math.floor(total).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")}" readonly></td>
                    </tr>`;
                        $tbody.append(row);
                    });
                    calculateTotals();
                    modalShowInvoice.hide();
                    $btn.prop('disabled', false).text('Select Invoices');
                    $('.select-invoice').prop('checked', false);
                    $('#select-all-invoices').prop('checked', false);
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Failed',
                        text: 'Failed to fetch invoice details.'
                    });
                    $btn.prop('disabled', false).text('Select Invoices');
                }
            });
        });

        $('#selected-sales-invoices-table').on('click', '.delete-invoice-row', function() {
            $(this).closest('tr').remove();
            calculateTotals();
        });

        $('#selected-sales-invoices-table').on('input', '.discount', function() {
            const id = $(this).data('id');
            let disc = parseFloat($(this).val()) || 0;
            const $sub = $(`.subtotal[data-id="${id}"]`);
            const original = parseFloat($sub.attr('data-original-total')) || 0;

            if (disc < 0) {
                disc = 0;
                $(this).val(0);
                Swal.fire({
                    icon: 'warning',
                    title: 'Invalid Discount',
                    text: 'Discount cannot be negative.'
                });
            }
            if (disc > original) {
                disc = original;
                $(this).val(original);
                Swal.fire({
                    icon: 'warning',
                    title: 'Invalid Discount',
                    text: 'Discount cannot exceed the original invoice total.'
                });
            }

            const newSub = Math.max(0, original - disc);
            $sub.val(newSub.toLocaleString('id-ID'));
            calculateTotals();
        }).on('blur', '.discount', function() {
            let v = parseFloat($(this).val()) || 0;
            $(this).val(v);
        });

        function calculateTotals() {
            let subtotal = 0,
                rowDisc = 0,
                afterRow = 0;
            $('#selected-sales-invoices-table tbody tr').each(function() {
                const total = parseFloat($(this).find('.subtotal').attr('data-original-total')) || 0;
                const disc = parseFloat(($(this).find('.discount').val() || '0').replace(/[,.]/g, '')) || 0;
                const sub = Math.max(0, total - disc);
                subtotal += total;
                rowDisc += disc;
                afterRow += sub;
                $(this).find('.subtotal').val(sub.toLocaleString('id-ID'));
            });

            const addDisc = parseFloat($('#total-discount').val().replace(/[^\d]/g, '')) || 0;
            const grand = Math.max(0, afterRow - addDisc);
            const savings = rowDisc + addDisc;

            $('#subtotal').val(subtotal.toLocaleString('id-ID'));
            $('#item-discounts-total').val(rowDisc.toLocaleString('id-ID'));
            $('#grand-total').val(grand.toLocaleString('id-ID'));

            if (savings > 0) {
                $('#total-savings').text(savings.toLocaleString('id-ID'));
                $('#discount-savings-badge').show();
            } else {
                $('#discount-savings-badge').hide();
            }
        }

        $('#total-discount').on('input', function() {
            let v = $(this).val().replace(/[^\d]/g, '');
            $(this).val(v ? parseInt(v).toLocaleString('id-ID') : '0');
            calculateTotals();
        });

        $('#total-discount').on('blur', function() {
            // validasi terhadap subtotal setelah item discount
            let afterRow = 0;
            $('#selected-sales-invoices-table tbody tr').each(function() {
                const total = parseFloat($(this).find('.subtotal').attr('data-original-total')) || 0;
                const disc = parseFloat($(this).find('.discount').val()) || 0;
                afterRow += Math.max(0, total - disc);
            });
            let v = parseInt($(this).val().replace(/[^\d]/g, '')) || 0;
            if (v > afterRow) {
                v = afterRow;
                Swal.fire({
                    icon: 'warning',
                    title: 'Invalid Discount',
                    text: 'Total discount cannot exceed the subtotal after item discounts.'
                });
            }
            $(this).val(v.toLocaleString('id-ID'));
            calculateTotals();
        });

        $('#reset-total-discount').on('click', function() {
            $('#total-discount').val('0');
            calculateTotals();
        });

        $(document).ready(function() {
            $('[data-bs-toggle="tooltip"]').tooltip();
        });

        // ======= S A T U - S A T U N Y A  H A N D L E R  S U B M I T =======
        $('#quotation-form').on('submit', function(e) {
            e.preventDefault();

            if ($('#selected-sales-invoices-table tbody tr').length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'No Invoice Selected',
                    text: 'Please select at least one sales invoice to create consignment.'
                });
                return;
            }

            const formData = new FormData(this);

            // angka diformat → balik ke numerik
            const totalDiscount = $('#total-discount').val().replace(/[,.]/g, '') || '0';
            formData.set('discountprice', totalDiscount);
            $('#discount-price-hidden').val(totalDiscount);

            const subtotal = $('#subtotal').val().replace(/[,.]/g, '') || '0';
            formData.set('subtotal', subtotal);

            const grandTotal = $('#grand-total').val().replace(/[,.]/g, '') || '0';
            // backend kamu sebelumnya membaca total grand di key 'total'
            formData.set('total', grandTotal);

            // totalexpenses diset 0 (sesuai hidden)
            formData.set('totalexpenses', '0');
            $('#total-expenses-hidden').val('0');

            // kirim array id invoice (pakai nama yg backend harapkan)
            const ids = [];
            $('#selected-sales-invoices-table tbody tr').each(function() {
                const id = $(this).find('input[name="invoice_ids[]"]').val();
                if (id) ids.push(id);
            });
            // pastikan hanya ada sales_invoice_ids[] (hapus varian lain bila ada)
            formData.delete('sales_invoice_ids');
            formData.delete('sales_invoice_ids[]');
            ids.forEach(id => formData.append('sales_invoice_ids[]', id));

            $.ajax({
                url: '/sales-consignment/store',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function() {
                    $('#btn-save-consignment').prop('disabled', true).html(
                        '<i class="fas fa-spinner fa-spin"></i> Saving...');
                },
                success: function(res) {
                    if (res.success) {
                        Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: 'Sales Consignment created successfully!'
                            })
                            .then(() => {
                                window.location.href = '{{ route('sales-consignment.index') }}';
                            });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: res.message || 'Failed to create Sales Consignment.'
                        });
                    }
                },
                error: function(xhr) {
                    const msg = xhr.responseJSON?.message ||
                        'Failed to create Sales Consignment. Please try again.';
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: msg
                    });
                },
                complete: function() {
                    $('#btn-save-consignment').prop('disabled', false).html(
                        '<i class="fas fa-save"></i> Save Sales Consignment');
                }
            });
        });
    </script>
@endsection
