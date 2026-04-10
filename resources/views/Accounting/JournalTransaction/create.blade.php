@extends('template.master')

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title h5">
                @if (isset($data['profile']))
                    Edit Journal Transaction
                @else
                    Add New Journal Transaction
                @endif
            </div>
            <br>

            <form id="journal-transaction-form">
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
                    <div class="col-md-12">
                        <div class="form-group local-forms">
                            <label for="note">Note</label>
                            <textarea class="form-control" id="note" name="note" rows="2" placeholder="Enter note (optional)">{{ $data['profile']['note'] ?? '' }}</textarea>
                        </div>
                    </div>
                </div>

                <hr>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0">Journal Details</h6>
                    <button type="button" class="btn btn-primary btn-sm" id="btn-add-row">
                        <i class="fas fa-plus"></i> Add Row
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered" id="table-journal-detail">
                        <thead>
                            <tr>
                                <th style="width: 30%">Account</th>
                                <th>Description</th>
                                <th style="width: 15%">Debit</th>
                                <th style="width: 15%">Credit</th>
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
                                                'chart_of_account_id' => '',
                                                'description' => '',
                                                'debit' => 0,
                                                'credit' => 0,
                                            ],
                                        ];
                            @endphp

                            @foreach ($details as $detail)
                                <tr class="journal-detail-row">
                                    <td>
                                        <select class="form-control detail-coa" name="detail_chart_of_account_id[]"
                                            required>
                                            <option value="">Select Account</option>
                                            @foreach ($data['chartOfAccounts'] as $coa)
                                                <option value="{{ $coa['id'] }}"
                                                    @if (($detail['chart_of_account_id'] ?? '') == $coa['id']) selected @endif>
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
                                        <input type="number" class="form-control text-end detail-debit"
                                            name="detail_debit[]" value="{{ $detail['debit'] ?? 0 }}" min="0"
                                            step="0.01">
                                    </td>
                                    <td>
                                        <input type="number" class="form-control text-end detail-credit"
                                            name="detail_credit[]" value="{{ $detail['credit'] ?? 0 }}" min="0"
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
                                <th colspan="2" class="text-end">Total</th>
                                <th><input type="text" class="form-control text-end" id="total-debit" readonly
                                        value="0"></th>
                                <th><input type="text" class="form-control text-end" id="total-credit" readonly
                                        value="0"></th>
                                <th></th>
                            </tr>
                            <tr>
                                <th colspan="5" class="text-end">
                                    <span id="balance-indicator" class="badge bg-secondary">Not Balanced</span>
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                @isset($data['profile'])
                    <input type="hidden" id="id" name="id" value="{{ $data['profile']['id'] }}">
                @endisset

                <div class="d-flex flex-row-reverse mt-4">
                    <button type="submit" class="btn btn-success mx-1" id="btn-save"
                        @if (isset($data['profile'])) value="update">Update Journal Transaction
                    @else value="create">Create Journal Transaction @endif
                        </button>
                        <button type="reset" class="btn btn-danger mx-1" id="btn-cancel">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const indexUrl = '/journal-transaction';
        const coaOptionsHtml = `
            <option value="">Select Account</option>
            @foreach ($data['chartOfAccounts'] as $coa)
                <option value="{{ $coa['id'] }}">{{ $coa['number'] }} - {{ $coa['name'] }}</option>
            @endforeach
        `;

        function recalculateTotals() {
            let totalDebit = 0;
            let totalCredit = 0;

            $('.detail-debit').each(function() {
                totalDebit += parseFloat($(this).val() || 0);
            });

            $('.detail-credit').each(function() {
                totalCredit += parseFloat($(this).val() || 0);
            });

            $('#total-debit').val(totalDebit.toFixed(2));
            $('#total-credit').val(totalCredit.toFixed(2));

            if (Math.abs(totalDebit - totalCredit) < 0.01 && totalDebit > 0) {
                $('#balance-indicator').removeClass('bg-secondary bg-danger').addClass('bg-success').text('Balanced');
            } else {
                $('#balance-indicator').removeClass('bg-success').addClass('bg-danger').text('Not Balanced');
            }
        }

        function buildDetailRow() {
            return `
                <tr class="journal-detail-row">
                    <td>
                        <select class="form-control detail-coa" name="detail_chart_of_account_id[]" required>
                            ${coaOptionsHtml}
                        </select>
                    </td>
                    <td>
                        <input type="text" class="form-control" name="detail_description[]" placeholder="Description">
                    </td>
                    <td>
                        <input type="number" class="form-control text-end detail-debit" name="detail_debit[]" value="0" min="0" step="0.01">
                    </td>
                    <td>
                        <input type="number" class="form-control text-end detail-credit" name="detail_credit[]" value="0" min="0" step="0.01">
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
            recalculateTotals();

            $('#btn-add-row').on('click', function() {
                $('#table-journal-detail tbody').append(buildDetailRow());
            });

            $(document).on('click', '.btn-delete-row', function() {
                if ($('#table-journal-detail tbody tr').length <= 1) {
                    Swal.fire('Warning', 'At least one detail row is required.', 'warning');
                    return;
                }

                $(this).closest('tr').remove();
                recalculateTotals();
            });

            $(document).on('change keyup', '.detail-debit, .detail-credit', function() {
                recalculateTotals();
            });

            $('#journal-transaction-form').on('submit', function(event) {
                event.preventDefault();

                recalculateTotals();

                const totalDebit = parseFloat($('#total-debit').val() || 0);
                const totalCredit = parseFloat($('#total-credit').val() || 0);
                if (Math.abs(totalDebit - totalCredit) >= 0.01 || totalDebit <= 0) {
                    Swal.fire('Error', 'Total debit and credit must be balanced and greater than zero.',
                        'error');
                    return;
                }

                const mode = $('#btn-save').attr('value');
                const url = (mode === 'update') ? '/journal-transaction/update' :
                    '/journal-transaction/store';
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

            $('#journal-transaction-form').on('reset', function() {
                goToPage(indexUrl);
            });
        });
    </script>
@endsection
