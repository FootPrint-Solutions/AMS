@extends('template.master')

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title mb-0">Journal Transaction List</h3>
                    </div>
                    <div class="col-auto text-end float-end ms-auto download-grp">
                        <button id="btn-export" class="btn btn-success btn-sm me-1">
                            <i class="fas fa-file-excel"></i> Export Excel
                        </button>
                        <button id="btn-import-template" class="btn btn-outline-success btn-sm me-1" type="button">
                            <i class="fas fa-download"></i> Download Template
                        </button>
                        <button id="btn-import" class="btn btn-outline-secondary btn-sm me-1" type="button">
                            <i class="fas fa-file-import"></i> Import Excel
                        </button>
                        <button id="btn-add" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Add New Journal Transaction
                        </button>
                    </div>
                </div>
            </div>
            <br>

            <div class="row align-items-end mb-3">
                <div class="col-md-3">
                    <label for="filter-status" class="form-label">Status</label>
                    <select class="form-control" id="filter-status">
                        <option value="all">All</option>
                        <option value="draft">Draft</option>
                        <option value="post">Post</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filter-date-start" class="form-label">Date From</label>
                    <input type="date" class="form-control" id="filter-date-start">
                </div>
                <div class="col-md-3">
                    <label for="filter-date-end" class="form-label">Date To</label>
                    <input type="date" class="form-control" id="filter-date-end">
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            {{-- Flash Messages --}}
            @if (Session::has('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ Session::get('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            <div class="table-responsive">
                <table class="table table-striped" id="table-journal-transaction">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>No</th>
                            <th>Voucher Number</th>
                            <th>Date</th>
                            <th>Note</th>
                            <th class="text-end">Total Debit</th>
                            <th class="text-end">Total Credit</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="journalImportModal" tabindex="-1" aria-labelledby="journalImportModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="journalImportModalLabel">Import Journal Transactions</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="journal-import-form" action="/journal-transaction/import" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-info mb-3">
                            Use one row for each journal detail. Put the voucher number, date, and note on the first row
                            of each transaction, then leave them blank on the next detail rows.
                        </div>

                        <div class="mb-3">
                            <label for="journal-import-file" class="form-label">Excel File</label>
                            <input type="file" class="form-control" id="journal-import-file" name="file"
                                accept=".xlsx,.xls,.csv" required>
                        </div>

                        <a href="/journal-transaction/template" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-download"></i> Download Template
                        </a>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success" id="btn-start-import">
                            <span class="spinner-border spinner-border-sm me-2 d-none" id="import-spinner" role="status"
                                aria-hidden="true"></span>
                            Import Now
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="import-loading-overlay" class="import-loading-overlay d-none">
        <div class="import-loading-card">
            <div class="spinner-border text-primary mb-3" role="status" aria-hidden="true"></div>
            <div class="h5 mb-1">Importing journal transactions</div>
            <div class="text-muted">Please wait while the file is processed.</div>
        </div>
    </div>

    <style>
        .import-loading-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.45);
            backdrop-filter: blur(3px);
            z-index: 3000;
            align-items: center;
            justify-content: center;
        }

        .import-loading-card {
            min-width: 280px;
            max-width: 92vw;
            padding: 1.5rem 1.75rem;
            border-radius: 1rem;
            background: #fff;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.18);
            text-align: center;
            animation: importPop 0.18s ease-out;
        }

        @keyframes importPop {
            from {
                transform: scale(0.96);
                opacity: 0;
            }

            to {
                transform: scale(1);
                opacity: 1;
            }
        }
    </style>

    <script>
        let table;

        $(document).ready(function() {
            const toCurrency = function(amount) {
                if (typeof formatCurrency === 'function') {
                    return formatCurrency(amount);
                }

                const number = Number(amount || 0);
                return new Intl.NumberFormat('id-ID').format(number);
            };

            table = $('#table-journal-transaction').DataTable({
                lengthMenu: [
                    [5, 10, 25],
                    [5, 10, 25]
                ],
                pageLength: 10,
                responsive: true,
                processing: true,
                serverSide: true,
                order: [
                    [1, 'desc']
                ],
                ajax: {
                    url: '/journal-transaction/show',
                    type: 'POST',
                    data: function(d) {
                        d._token = '{{ csrf_token() }}';
                        d.status = $('#filter-status').val();
                        d.dateStart = $('#filter-date-start').val();
                        d.dateEnd = $('#filter-date-end').val();
                    }
                },
                columns: [{
                        targets: 0,
                        className: 'dt-control',
                        orderable: false,
                        data: null,
                        defaultContent: '',
                    },
                    {
                        data: 1,
                        orderable: false,
                        className: 'dt-body-center'
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
                        className: 'dt-body-center'
                    }
                ],
                dom: 'lBfrtip',
                buttons: [{
                        text: "<i class='fas fa-pencil'></i> Edit",
                        className: 'btn btn-outline-primary btn-sm',
                        action: function() {
                            const selectedRows = table.rows({
                                selected: true
                            }).data().toArray();
                            if (selectedRows.length !== 1) {
                                Swal.fire('Error', 'Please select one journal transaction to edit.',
                                    'error');
                                return;
                            }

                            const id = selectedRows[0][0];
                            goToPage('/journal-transaction/edit/' + id);
                        }
                    },
                    {
                        text: "<i class='fas fa-trash'></i> Delete",
                        className: 'btn btn-outline-danger btn-sm',
                        action: function() {
                            const selectedRows = table.rows({
                                selected: true
                            }).data().toArray();
                            if (selectedRows.length === 0) {
                                Swal.fire('Error',
                                    'Please select at least one journal transaction.', 'error');
                                return;
                            }

                            const ids = selectedRows.map(row => row[0]);

                            Swal.fire({
                                title: 'Delete Journal Transaction',
                                text: 'Are you sure you want to delete selected journal transaction(s)?',
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonText: 'Yes, Delete',
                                cancelButtonText: 'Cancel',
                                confirmButtonColor: '#d33'
                            }).then((result) => {
                                if (!result.isConfirmed) {
                                    return;
                                }

                                $.ajax({
                                    url: '/journal-transaction/destroy',
                                    type: 'POST',
                                    data: {
                                        _token: '{{ csrf_token() }}',
                                        ids: ids
                                    },
                                    success: function(response) {
                                        if (typeof response === 'string') {
                                            try {
                                                response = JSON.parse(response);
                                            } catch (e) {
                                                Swal.fire('Error',
                                                    'Invalid response from server.',
                                                    'error');
                                                return;
                                            }
                                        }
                                        if (response.status) {
                                            Swal.fire('Deleted', response
                                                .message, 'success');
                                            table.ajax.reload();
                                            return;
                                        }
                                        Swal.fire('Error', response.message,
                                            'error');
                                    },
                                    error: function() {
                                        Swal.fire('Error',
                                            'Failed to delete journal transaction.',
                                            'error');
                                    }
                                });
                            });
                        }
                    },
                    {
                        text: "<i class='fas fa-paper-plane'></i> Post",
                        className: 'btn btn-outline-success btn-sm',
                        action: function() {
                            const selectedRows = table.rows({
                                selected: true
                            }).data().toArray();
                            if (selectedRows.length === 0) {
                                Swal.fire('Error',
                                    'Please select at least one journal transaction.', 'error');
                                return;
                            }

                            const ids = selectedRows.map(row => row[0]);

                            Swal.fire({
                                title: 'Post Journal Transaction',
                                text: 'Are you sure you want to post selected journal transaction(s)?',
                                icon: 'question',
                                showCancelButton: true,
                                confirmButtonText: 'Yes, Post',
                                cancelButtonText: 'Cancel',
                                confirmButtonColor: '#3085d6'
                            }).then((result) => {
                                if (!result.isConfirmed) {
                                    return;
                                }

                                $.ajax({
                                    url: '/journal-transaction/post',
                                    type: 'POST',
                                    data: {
                                        _token: '{{ csrf_token() }}',
                                        ids: ids
                                    },
                                    success: function(response) {
                                        if (typeof response === 'string') {
                                            try {
                                                response = JSON.parse(response);
                                            } catch (e) {
                                                Swal.fire('Error',
                                                    'Invalid response from server.',
                                                    'error');
                                                return;
                                            }
                                        }
                                        if (response.status) {
                                            Swal.fire('Success', response
                                                .message, 'success');
                                            table.ajax.reload();
                                            return;
                                        }
                                        Swal.fire('Error', response.message,
                                            'error');
                                    },
                                    error: function() {
                                        Swal.fire('Error',
                                            'Failed to post journal transaction.',
                                            'error');
                                    }
                                });
                            });
                        }
                    }
                ],
                language: getDatatablesLanguangeConfigurations('Journal Transaction'),
                select: true,
            });

            $('#table-journal-transaction tbody').on('click', 'td.dt-control', function() {
                var tr = $(this).closest('tr');
                var row = table.row(tr);

                if (row.child.isShown()) {

                    row.child.hide();
                    tr.removeClass('shown');
                } else {
                    // Open this row
                    var journalTransactionId = table.row(tr).data()[0];

                    // Fetch items via AJAX
                    $.ajax({
                        url: '/journal-transaction/items/' + journalTransactionId,
                        method: 'GET',
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            if (response && (response.success === true || response.status ===
                                    'success' || response.status === true)) {
                                var items = response.data || [];
                                if (items.length === 0) {
                                    row.child('<div class="p-2">No items found.</div>').show();
                                    tr.addClass('shown');
                                    return;
                                }

                                var itemTable =
                                    '<table class="table table-bordered"><thead><tr>' +
                                    '<th>Account Number</th><th>Account Name</th><th>Description</th><th>Ref</th><th class="text-end">Debit (IDR)</th><th class="text-end">Credit (IDR)</th>' +
                                    '</tr></thead><tbody>';

                                items.forEach(function(item) {
                                    var accountNumber = item.account_number || '-';
                                    var accountName = item.account_name || '-';
                                    var description = item.description || '-';
                                    var ref = item.ref_display || item.ref || '-';
                                    var debit = item.debit ? toCurrency(item
                                        .debit) : '-';
                                    var credit = item.credit ? toCurrency(item
                                        .credit) : '-';

                                    itemTable += '<tr>' +
                                        '<td>' + accountNumber + '</td>' +
                                        '<td>' + accountName + '</td>' +
                                        '<td>' + description + '</td>' +
                                        '<td>' + ref + '</td>' +
                                        '<td class="text-end">' + debit + '</td>' +
                                        '<td class="text-end">' + credit + '</td>' +
                                        '</tr>';
                                });

                                itemTable += '</tbody></table>';

                                row.child(itemTable).show();
                                tr.addClass('shown');
                            } else {
                                Swal.fire({
                                    title: "Error",
                                    text: response.message || "Failed to fetch items.",
                                    icon: "error",
                                });
                            }
                        },
                        error: function(xhr) {
                            Swal.fire({
                                title: "Error",
                                text: xhr.responseJSON?.message ||
                                    "Failed to fetch items.",
                                icon: "error",
                            });
                        }
                    });
                }
            });

            $('#btn-add').on('click', function() {
                goToPage('/journal-transaction/create');
            });

            $('#btn-import-template').on('click', function() {
                window.location.href = '/journal-transaction/template';
            });

            $('#btn-import').on('click', function() {
                const modal = new bootstrap.Modal(document.getElementById('journalImportModal'));
                modal.show();
            });

            $('#journal-import-form').on('submit', async function(event) {
                event.preventDefault();

                const fileInput = $('#journal-import-file')[0];
                if (!fileInput.files.length) {
                    Swal.fire('Warning', 'Please choose an Excel file first.', 'warning');
                    return;
                }

                const formData = new FormData(this);
                const submitButton = $('#btn-start-import');
                const spinner = $('#import-spinner');

                submitButton.prop('disabled', true);
                spinner.removeClass('d-none');
                $('#import-loading-overlay').removeClass('d-none').addClass('d-flex');

                Swal.fire({
                    title: 'Importing Journal Transactions',
                    text: 'Please wait while the file is being processed.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                try {
                    const response = await fetch('/journal-transaction/import', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    const html = await response.text();
                    document.open();
                    document.write(html);
                    document.close();
                } catch (error) {
                    submitButton.prop('disabled', false);
                    spinner.addClass('d-none');
                    $('#import-loading-overlay').addClass('d-none').removeClass('d-flex');
                    Swal.close();
                    Swal.fire('Error', 'Failed to import the file.', 'error');
                }
            });

            $('#btn-export').on('click', function() {
                const status = $('#filter-status').val();
                const dateStart = $('#filter-date-start').val();
                const dateEnd = $('#filter-date-end').val();

                const params = new URLSearchParams();
                if (status) {
                    params.append('status', status);
                }
                if (dateStart) {
                    params.append('dateStart', dateStart);
                }
                if (dateEnd) {
                    params.append('dateEnd', dateEnd);
                }

                window.location.href = '/journal-transaction/export' + (params.toString() ? '?' + params
                    .toString() : '');
            });

            $('#filter-status, #filter-date-start, #filter-date-end').on('change', function() {
                table.ajax.reload();
            });
        });
    </script>
@endsection
