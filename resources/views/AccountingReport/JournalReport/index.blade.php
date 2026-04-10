@extends('template.master')

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title mb-0">Journal Report</h3>
                    </div>
                </div>
            </div>
            <br>

            <form id="form-journal-report" method="GET" target="_blank">
                <div class="row align-items-end mb-3">
                    <div class="col-md-3">
                        <label for="date-start" class="form-label">Start Month</label>
                        <input type="text" class="form-control" id="date-start" name="dateStart" placeholder="MM-YYYY"
                            required>
                    </div>
                    <div class="col-md-3">
                        <label for="date-end" class="form-label">End Month</label>
                        <input type="text" class="form-control" id="date-end" name="dateEnd" placeholder="MM-YYYY"
                            required>
                    </div>
                    <div class="col-md-3">
                        <label for="filter" class="form-label">Filter (Optional)</label>
                        <input type="text" class="form-control" id="filter" name="filter"
                            placeholder="Voucher Number or Account Name">
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary" onclick="return submitForm(event)">
                            <i class="fas fa-print"></i> Print Report
                        </button>
                    </div>
                </div>
            </form>

            <div id="report-preview" class="mt-3">
                <div class="alert alert-info text-center">
                    Please fill in the form and click "Print Report" to preview the journal report.
                </div>
            </div>
        </div>
    </div>

    <!-- Include jQuery UI for datepicker -->
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

    <script>
        $(document).ready(function() {
            const datePickerConfig = {
                dateFormat: 'mm-yy',
                changeMonth: true,
                changeYear: true,
                onClose: function(dateText, inst) {
                    $(this).val(dateText);
                }
            };

            $("#date-start").datepicker(datePickerConfig);
            $("#date-end").datepicker(datePickerConfig);

            const today = new Date();
            const currentMonth = String(today.getMonth() + 1).padStart(2, '0');
            const currentYear = today.getFullYear();
            const currentDate = `${currentMonth}-${currentYear}`;

            $("#date-start").val(currentDate);
            $("#date-end").val(currentDate);
        });

        function submitForm(event) {
            event.preventDefault();

            const dateStart = $('#date-start').val();
            const dateEnd = $('#date-end').val();
            const filter = $('#filter').val() || '';

            if (!dateStart) {
                Swal.fire('Error', 'Please select start month', 'error');
                return false;
            }

            if (!dateEnd) {
                Swal.fire('Error', 'Please select end month', 'error');
                return false;
            }

            const [startMonth, startYear] = dateStart.split('-');
            const [endMonth, endYear] = dateEnd.split('-');

            if (parseInt(startYear) > parseInt(endYear) ||
                (parseInt(startYear) === parseInt(endYear) && parseInt(startMonth) > parseInt(endMonth))) {
                Swal.fire('Error', 'Start month cannot be greater than end month', 'error');
                return false;
            }

            let url = `/journal-report/print/${dateStart}/${dateEnd}`;
            if (filter) {
                url += `/${encodeURIComponent(filter)}`;
            }

            window.open(url, '_blank');
            return false;
        }
    </script>
@endsection
