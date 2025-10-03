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
                    <div class="row">
                        {{-- Sales Consignment Number --}}
                        <div class="col">
                            <div class="form-group local-forms">
                                <label for="sales-consignment-number">Sales Consignment Number</label>
                                <input type="text" class="form-control" id="sales-consignment-number"
                                    name="salesconsignmentnumber" placeholder="Enter consignment number" readonly
                                    @isset($data['profile'])
                       value="{{ $data['profile']['sales_consignment_number'] ?? $data['consignment_number'] }}"
                   @else
                       value="{{ $data['consignment_number'] ?? '' }}"
                   @endisset>
                            </div>
                        </div>

                        {{-- Sales Consignment Date --}}
                        <div class="col">
                            <div class="form-group local-forms">
                                <label for="sales-consignment-date">Sales Consignment Date</label>
                                <input type="date" class="form-control" id="sales-consignment-date"
                                    name="salesconsignmentdate"
                                    @isset($data['profile'])
                       value="{{ $data['profile']['sales_consignment_date'] ?? $data['consignment_date'] }}"
                   @else
                       value="{{ $data['consignment_date'] ?? date('Y-m-d') }}"
                   @endisset>
                            </div>
                        </div>

                        {{-- Distributor --}}
                        <div class="col">
                            <div class="form-group local-forms">
                                <label for="distributor">Distributor <span class="login-danger">*</span></label>
                                <select class="form-control select" id="distributor" name="distributor"
                                    data-placeholder="Select Distributor">
                                    <option value="">Select Distributor</option>
                                    @foreach ($data['distributors'] as $distributor)
                                        <option value="{{ $distributor['id'] }}"
                                            @isset($data['profile']) @if ($data['profile']['distributor_id'] == $distributor['id']) selected @endif
                                            @endisset>
                                            {{ $distributor['name'] }}</option>
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
                            <table class="table table-striped" id="selected-sales-invoices-table">
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
                                Total Savings: Rp <span id="total-savings">0</span>
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
                        </div>
                    </div>

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

                    <script>
                        function calculateTotals() {
                            let subtotal = 0;
                            let totalRowDiscounts = 0;
                            let subtotalAfterRowDiscounts = 0;

                            // Calculate subtotal and row-level discounts
                            $('#selected-sales-invoices-table tbody tr').each(function() {
                                const total = parseFloat($(this).find('.subtotal').attr('data-original-total')) || 0;
                                const discount = parseFloat($(this).find('.discount').val().replace(/[,.]/g, '')) || 0;
                                const rowSubtotal = Math.max(0, total - discount);

                                subtotal += total;
                                totalRowDiscounts += discount;
                                subtotalAfterRowDiscounts += rowSubtotal;

                                // Update subtotal field in the row
                                $(this).find('.subtotal').val(rowSubtotal.toLocaleString('id-ID'));
                            });

                            // Get additional total discount (overall discount)
                            const additionalDiscount = parseFloat($('#total-discount').val().replace(/[,.]/g, '')) || 0;

                            // Calculate grand total: subtotal - row discounts - additional discount
                            const grandTotal = Math.max(0, subtotalAfterRowDiscounts - additionalDiscount);

                            // Calculate total savings
                            const totalSavings = totalRowDiscounts + additionalDiscount;

                            // Update display fields
                            $('#subtotal').val(subtotal.toLocaleString('id-ID'));
                            $('#item-discounts-total').val(totalRowDiscounts.toLocaleString('id-ID'));
                            $('#grand-total').val(grandTotal.toLocaleString('id-ID'));

                            // Update savings badge
                            if (totalSavings > 0) {
                                $('#total-savings').text(totalSavings.toLocaleString('id-ID'));
                                $('#discount-savings-badge').show();
                            } else {
                                $('#discount-savings-badge').hide();
                            }
                        }

                        // Event listeners
                        $('#selected-sales-invoices-table').on('input', '.discount', calculateTotals);
                        $('#selected-sales-invoices-table').on('click', '.delete-invoice-row', calculateTotals);
                        $(document).on('ajaxComplete', function() {
                            calculateTotals();
                        });

                        // When total discount input changes, recalculate grand total
                        $('#total-discount').on('input', function() {
                            // Format the input value as user types
                            let value = $(this).val().replace(/[^0-9]/g, '');
                            if (value) {
                                $(this).val(parseInt(value).toLocaleString('id-ID'));
                            }
                            calculateTotals();
                        });

                        // Format total discount on blur and validate
                        $('#total-discount').on('blur', function() {
                            let value = $(this).val().replace(/[^0-9]/g, '');

                            if (value) {
                                // Get subtotal after item discounts for validation
                                let subtotalAfterItemDiscounts = 0;
                                $('#selected-sales-invoices-table tbody tr').each(function() {
                                    const total = parseFloat($(this).find('.subtotal').attr('data-original-total')) || 0;
                                    const discount = parseFloat($(this).find('.discount').val()) || 0;
                                    const rowSubtotal = Math.max(0, total - discount);
                                    subtotalAfterItemDiscounts += rowSubtotal;
                                });

                                let numericValue = parseInt(value);

                                // Validate: cannot be negative or exceed subtotal after item discounts
                                if (numericValue < 0) {
                                    numericValue = 0;
                                    alert('Additional discount cannot be negative.');
                                } else if (numericValue > subtotalAfterItemDiscounts) {
                                    numericValue = subtotalAfterItemDiscounts;
                                    alert('Additional discount cannot exceed the subtotal after item discounts.');
                                }

                                $(this).val(numericValue.toLocaleString('id-ID'));
                                calculateTotals();
                            }
                        });

                        // Reset total discount button
                        $('#reset-total-discount').on('click', function() {
                            $('#total-discount').val('0');
                            calculateTotals();
                        });

                        // Initialize tooltips
                        $(document).ready(function() {
                            $('[data-bs-toggle="tooltip"]').tooltip();
                        });

                        // Form submission
                        $('#quotation-form').on('submit', function(e) {
                            e.preventDefault();

                            // Validate that at least one invoice is selected
                            if ($('#selected-sales-invoices-table tbody tr').length === 0) {
                                alert('Please select at least one sales invoice.');
                                return;
                            }

                            // Prepare form data
                            const formData = new FormData(this);

                            // Convert formatted numbers back to numeric values for submission
                            const totalDiscount = $('#total-discount').val().replace(/[,.]/g, '') || '0';
                            formData.set('total_discount', totalDiscount);

                            const subtotal = $('#subtotal').val().replace(/[,.]/g, '') || '0';
                            formData.set('subtotal', subtotal);

                            const grandTotal = $('#grand-total').val().replace(/[,.]/g, '') || '0';
                            formData.set('grand_total', grandTotal);

                            // Collect selected invoices data
                            const selectedInvoices = [];
                            $('#selected-sales-invoices-table tbody tr').each(function() {
                                const row = $(this);
                                selectedInvoices.push({
                                    id: row.find('.discount').data('id'),
                                    discount: row.find('.discount').val().replace(/[,.]/g, '') || '0',
                                    subtotal: row.find('.subtotal').val().replace(/[,.]/g, '') || '0'
                                });
                            });

                            formData.append('selected_invoices', JSON.stringify(selectedInvoices));

                            // Show loading state
                            $('#btn-save-consignment').prop('disabled', true).html(
                                '<i class="fas fa-spinner fa-spin"></i> Saving...');

                            // Submit form (you'll need to implement the actual submission logic)
                            console.log('Form data ready for submission:', {
                                total_discount: totalDiscount,
                                subtotal: subtotal,
                                grand_total: grandTotal,
                                selected_invoices: selectedInvoices
                            });

                            // Restore button state (remove this when implementing actual submission)
                            setTimeout(() => {
                                $('#btn-save-consignment').prop('disabled', false).html(
                                    '<i class="fas fa-save"></i> Save Sales Consignment');
                                alert('Form submission prepared! Check console for data structure.');
                            }, 1000);
                        });
                    </script>
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
                    <table class="table table-striped" id="table-sales-invoice">
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
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Data will be populated via JavaScript --}}
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="btn-select-invoices">Select Invoices</button>
                </div>
            </div>
        </div>
    </div>


    <script>
        const distributorSelect = $('#distributor');
        const btnFindSalesInvoice = $('#btn-find-sales-invoice');
        const modalShowInvoice = new bootstrap.Modal(document.getElementById('modal-show-invoice'));
        const tableSalesInvoiceBody = $('#table-sales-invoice tbody');

        btnFindSalesInvoice.on('click', function() {
            const distributorId = distributorSelect.val();
            if (!distributorId) {
                alert('Please select a distributor first.');
                return;
            }

            // Fetch sales invoices via AJAX
            $.ajax({
                url: '/sales-invoices/by-distributor',
                method: 'GET',
                data: {
                    distributor_id: distributorId
                },
                success: function(response) {
                    tableSalesInvoiceBody.empty();

                    let number = 1;
                    response.data.forEach(invoice => {
                        const row = `
                            <tr>
                                <td>
                                    <input type="checkbox" class="form-check-input select-invoice form-input" data-id="${invoice.id}">
                                </td>
                                <td>${number}</td>
                                <td>${invoice.sales_invoice_number ?? ''}</td>
                                <td>${invoice.invoice_number ?? ''}</td>
                                <td>${invoice.date ?? ''}</td>
                                <td>${invoice.customer.name ?? ''}</td>
                                <td>Rp ${(invoice.total ?? 0).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")}</td>
                                <td>${invoice.address ?? ''}</td>
                            </tr>
                        `;
                        tableSalesInvoiceBody.append(row);
                        number++;
                    });

                    $('#table-sales-invoice').DataTable();

                    modalShowInvoice.show();
                },
                error: function() {
                    alert('Failed to fetch sales invoices. Please try again.');
                }
            });
        });

        tableSalesInvoiceBody.on('click', '.select-invoice', function() {
            const invoiceId = $(this).data('id');
            if ($(this).is(':checked')) {
                console.log('Selected Invoice ID:', invoiceId);
            }
        });

        $('#btn-select-invoices').on('click', function() {
            const selectedInvoices = [];
            $('.select-invoice:checked').each(function() {
                selectedInvoices.push($(this).data('id'));
            });

            if (selectedInvoices.length === 0) {
                alert('Please select at least one invoice.');
                return;
            }

            // Fetch selected invoices details via AJAX
            $.ajax({
                url: '/sales-invoices/add-consignment-temp',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    invoice_ids: selectedInvoices
                },
                success: function(response) {
                    const tableBody = $('#selected-sales-invoices-table tbody');
                    tableBody.empty();

                    let number = 1;
                    response.data.forEach(invoice => {
                        const row = `
                            <tr>
                                <td>
                                    <button type="button" class="btn btn-danger btn-sm delete-invoice-row">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <input type="hidden" name="invoice_ids[]" value="${invoice.id}">
                                </td>
                                <td>${number}</td>
                                <td>${invoice.sales_invoice_number ?? ''}</td>
                                <td>${invoice.invoice_number ?? ''}</td>
                                <td>${invoice.date ?? ''}</td>
                                <td>${invoice.customer.name ?? ''}</td>
                                <td>Rp ${(invoice.total ?? 0).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")}</td>
                                <td>${invoice.address ?? ''}</td>
                                <td><input type="number" class="form-control form-input discount" data-id="${invoice.id}" value="0"></td>
                                <td><input type="number" class="form-control form-input note" data-id="${invoice.id}" value=""></td>
                                <td><input type="text" class="form-control form-input subtotal" data-id="${invoice.id}" data-original-total="${invoice.total ?? 0}" value="${invoice.total ?? 0}" readonly></td>
                            </tr>
                        `;
                        tableBody.append(row);
                        number++;
                    });

                    modalShowInvoice.hide();
                },
                error: function() {
                    alert('Failed to fetch invoice details. Please try again.');
                }
            });
        });

        $('#selected-sales-invoices-table').on('click', '.delete-invoice-row', function() {
            $(this).closest('tr').remove();
        });

        $('#selected-sales-invoices-table').on('input', '.discount', function() {
            const invoiceId = $(this).data('id');
            let discount = parseFloat($(this).val()) || 0;
            const subtotalField = $(`.subtotal[data-id="${invoiceId}"]`);
            const originalTotal = parseFloat(subtotalField.attr('data-original-total')) || 0;

            // Validate discount: cannot be negative or exceed original total
            if (discount < 0) {
                discount = 0;
                $(this).val(discount);
                alert('Discount cannot be negative.');
            } else if (discount > originalTotal) {
                discount = originalTotal;
                $(this).val(discount);
                alert('Discount cannot exceed the original invoice total.');
            }

            const newSubtotal = Math.max(0, originalTotal - discount);
            // Format rupiah tanpa desimal, titik sebagai separator ribuan
            subtotalField.val(`${Math.floor(newSubtotal).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")}`);
        });

        // Format discount on blur for item discounts
        $('#selected-sales-invoices-table').on('blur', '.discount', function() {
            let value = parseFloat($(this).val()) || 0;
            $(this).val(value.toLocaleString('id-ID'));
        });
    </script>
@endsection
