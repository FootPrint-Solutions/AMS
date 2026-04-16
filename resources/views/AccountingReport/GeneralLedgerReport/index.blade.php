@extends('template.master')

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title mb-0">General Ledger</h3>
                    </div>
                </div>
            </div>

            <br>

            <form id="form-general-ledger" method="GET" target="_blank">
                <div class="row align-items-end mb-3">
                    <div class="col-md-3 mb-3">
                        <label for="date" class="form-label">Period</label>
                        <input type="text" class="form-control" id="date" name="date" placeholder="MM-YYYY"
                            required>
                    </div>

                    <div class="col-md-6">
                        <label for="account_ids" class="form-label">Chart of Account (Optional)</label>
                        <select id="account_ids" class="form-control" multiple>
                            @foreach ($accounts as $account)
                                <option value="{{ $account->id }}">{{ $account->number }} - {{ $account->name }}</option>
                            @endforeach
                        </select>
                        <div class="d-flex justify-content-between mt-1">
                            <small class="text-muted">Select 1 account for specific filter, or 2 accounts for range.</small>
                            <a href="#" id="clear-account-filter" class="small">Clear</a>
                        </div>
                    </div>

                    <div class="col-md-3 mb-3">
                        <button type="submit" class="btn btn-primary" onclick="return submitGeneralLedger(event)">
                            <i class="fas fa-print"></i> Print Report
                        </button>
                    </div>
                </div>
            </form>

            <div id="report-preview" class="mt-3">
                <div class="alert alert-info text-center mb-0">
                    Please fill in the form and click "Print Report" to preview the general ledger report.
                </div>
            </div>
        </div>
    </div>

    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

    <script>
        $(document).ready(function() {
            $("#date").datepicker({
                dateFormat: 'mm-yy',
                changeMonth: true,
                changeYear: true,
                onClose: function(dateText) {
                    $(this).val(dateText);
                }
            });

            const today = new Date();
            const currentMonth = String(today.getMonth() + 1).padStart(2, '0');
            const currentYear = today.getFullYear();
            $("#date").val(`${currentMonth}-${currentYear}`);

            $('#account_ids').select2({
                placeholder: 'Search account number or name',
                width: '100%'
            });

            $('#clear-account-filter').on('click', function(e) {
                e.preventDefault();
                $('#account_ids').val(null).trigger('change');
            });
        });

        function submitGeneralLedger(event) {
            event.preventDefault();

            const date = $('#date').val();
            if (!date) {
                Swal.fire('Error', 'Please select period first', 'error');
                return false;
            }

            const selectedAccounts = $('#account_ids').val() || [];
            let url = `{{ url('/general-ledger-report/print') }}/${encodeURIComponent(date)}`;

            if (selectedAccounts.length > 0) {
                url += `?account_ids=${selectedAccounts.join(',')}`;
            }

            window.open(url, '_blank');
            return false;
        }
    </script>
@endsection
