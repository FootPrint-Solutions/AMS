@extends('template.master')

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title h5">
                @if (isset($data['profile']))
                    Edit Accouting Receive
                @else
                    Add New Accouting Receive
                @endif
            </div>
            <br>

            <form id="accounting-receive-form">
                @csrf

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group local-forms">
                            <label for="voucher_number">Voucher Number <span class="login-danger">*</span></label>
                            <input type="text" class="form-control" id="voucher_number" name="voucher_number" required
                                readonly
                                @if (isset($data['profile'])) value="{{ $data['profile']['voucher_number'] }}" @else value="{{ $data['number'] }}" @endif>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group local-forms">
                            <label for="date">Date <span class="login-danger">*</span></label>
                            <input type="date" class="form-control" id="date" name="date" required
                                @if (isset($data['profile'])) value="{{ date('Y-m-d', strtotime($data['profile']['date'])) }}" @else value="{{ date('Y-m-d') }}" @endif>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group local-forms">
                            <label for="status_display">Status</label>
                            <input type="text" class="form-control" id="status_display" readonly
                                value="{{ isset($data['profile']) ? strtoupper($data['profile']['status']) : 'DRAFT' }}">
                        </div>
                    </div>
                </div>

                <div class="row mt-2">
                    <div class="col-md-4">
                        <div class="form-group local-forms">
                            <label for="type">Type <span class="login-danger">*</span></label>
                            @php
                                $selectedType = $data['profile']['type'] ?? 'cash';
                            @endphp
                            <select class="form-control" id="type" name="type" required>
                                <option value="cash" @if ($selectedType === 'cash') selected @endif>Cash</option>
                                <option value="bank" @if ($selectedType === 'bank') selected @endif>Bank</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group local-forms">
                            <label for="account_id">Debet Account <span class="login-danger">*</span></label>
                            <select class="form-control" id="account_id" name="account_id" required>
                                <option value="">Select Account</option>
                                @foreach ($data['chartOfAccounts'] as $coa)
                                    <option value="{{ $coa['id'] }}" @if (($data['profile']['account_id'] ?? '') == $coa['id']) selected @endif>
                                        {{ $coa['number'] }} - {{ $coa['name'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group local-forms">
                            <label for="to">Paid To</label>
                            <input type="text" class="form-control" id="to" name="to"
                                value="{{ $data['profile']['to'] ?? '' }}" placeholder="Enter recipient">
                        </div>
                    </div>
                </div>

                <div class="row mt-2">
                    <div class="col-md-6">
                        <div class="form-group local-forms">
                            <label for="bank_account_no">Bank Account No</label>
                            <input type="text" class="form-control" id="bank_account_no" name="bank_account_no"
                                value="{{ $data['profile']['bank_account_no'] ?? '' }}"
                                placeholder="Enter bank account number">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group local-forms">
                            <label for="address">Address</label>
                            <input type="text" class="form-control" id="address" name="address"
                                value="{{ $data['profile']['address'] ?? '' }}" placeholder="Enter address">
                        </div>
                    </div>
                </div>

                <div class="row mt-2">
                    <div class="col-md-12">
                        <div class="form-group local-forms">
                            <label for="note">Note</label>
                            <textarea class="form-control" id="note" name="note" rows="2" placeholder="Enter note (optional)">{{ $data['profile']['note'] ?? '' }}</textarea>
                        </div>
                    </div>
                </div>

                <hr>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0">Expense Details</h6>
                    <button type="button" class="btn btn-primary btn-sm" id="btn-add-row">
                        <i class="fas fa-plus"></i> Add Row
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered" id="table-expense-detail">
                        <thead>
                            <tr>
                                <th style="width: 35%">Credit Account</th>
                                <th>Description</th>
                                <th style="width: 20%">Total</th>
                                <th style="width: 5%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $details =
                                    isset($data['profile']['details']) && count($data['profile']['details'])
                                        ? $data['profile']['details']
                                        : [
                                            [
                                                'account_id' => '',
                                                'description' => '',
                                                'total' => 0,
                                            ],
                                        ];
                            @endphp

                            @foreach ($details as $detail)
                                <tr class="receive-detail-row">
                                    <td>
                                        <select class="form-control detail-account" name="detail_account_id[]" required>
                                            <option value="">Select Account</option>
                                            @foreach ($data['chartOfAccounts'] as $coa)
                                                <option value="{{ $coa['id'] }}"
                                                    @if (($detail['account_id'] ?? '') == $coa['id']) selected @endif>
                                                    {{ $coa['number'] }} - {{ $coa['name'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="detail_description[]"
                                            value="{{ $detail['description'] ?? '' }}" placeholder="Description">
                                    </td>
                                    <td>
                                        <input type="number" class="form-control text-end detail-total"
                                            name="detail_total[]" value="{{ $detail['total'] ?? 0 }}" min="0"
                                            step="0.01">
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-danger btn-sm btn-delete-row">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="2" class="text-end">Grand Total</th>
                                <th><input type="text" class="form-control text-end" id="grand-total" readonly
                                        value="0"></th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                @isset($data['profile'])
                    <input type="hidden" id="id" name="id" value="{{ $data['profile']['id'] }}">
                @endisset

                <div class="d-flex flex-row-reverse mt-4">
                    <button type="submit" class="btn btn-success mx-1" id="btn-save"
                        @if (isset($data['profile'])) value="update">Update Accouting Receive
                    @else value="create">Create Accouting Receive @endif
                        </button>
                        <button type="reset" class="btn btn-danger mx-1" id="btn-cancel">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const indexUrl = '/accounting-receive';
        const coaOptionsHtml = `
            <option value="">Select Account</option>
            @foreach ($data['chartOfAccounts'] as $coa)
                <option value="{{ $coa['id'] }}">{{ $coa['number'] }} - {{ $coa['name'] }}</option>
            @endforeach
        `;

        function recalculateGrandTotal() {
            let grandTotal = 0;

            $('.detail-total').each(function() {
                grandTotal += parseFloat($(this).val() || 0);
            });

            $('#grand-total').val(grandTotal.toFixed(2));
        }

        function buildDetailRow() {
            return `
                <tr class="receive-detail-row">
                    <td>
                        <select class="form-control detail-account" name="detail_account_id[]" required>
                            ${coaOptionsHtml}
                        </select>
                    </td>
                    <td>
                        <input type="text" class="form-control" name="detail_description[]" placeholder="Description">
                    </td>
                    <td>
                        <input type="number" class="form-control text-end detail-total" name="detail_total[]" value="0" min="0" step="0.01">
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-danger btn-sm btn-delete-row">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
        }

        $(document).ready(function() {
            recalculateGrandTotal();

            $('#btn-add-row').on('click', function() {
                $('#table-expense-detail tbody').append(buildDetailRow());
            });

            $(document).on('click', '.btn-delete-row', function() {
                if ($('#table-expense-detail tbody tr').length <= 1) {
                    Swal.fire('Warning', 'At least one detail row is required.', 'warning');
                    return;
                }

                $(this).closest('tr').remove();
                recalculateGrandTotal();
            });

            $(document).on('change keyup', '.detail-total', function() {
                recalculateGrandTotal();
            });

            $('#accounting-receive-form').on('submit', function(event) {
                event.preventDefault();

                recalculateGrandTotal();

                const grandTotal = parseFloat($('#grand-total').val() || 0);
                if (grandTotal <= 0) {
                    Swal.fire('Error', 'Grand total must be greater than zero.', 'error');
                    return;
                }

                const mode = $('#btn-save').attr('value');
                const url = (mode === 'update') ? '/accounting-receive/update' :
                    '/accounting-receive/store';
                const formData = new FormData($(this)[0]);

                Swal.fire({
                    title: 'Processing...',
                    text: 'Please wait while we save your data.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                sendSubmitRequest(url, formData, function() {
                    goToPage(indexUrl);
                });
            });

            $('#accounting-receive-form').on('reset', function() {
                goToPage(indexUrl);
            });
        });
    </script>
@endsection
