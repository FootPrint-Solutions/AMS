@extends('template.master')

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Battery
                    </h3>
                </div>
                <div class="col-auto text-end float-end ms-auto download-grp">
                    <button id="btn-add" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add
                        New Battery</button>
                </div>
            </div>
        </div>
        <div class="card-body">
            {{-- filter status --}}
            <div class="row mt-2">
                <div class="col-md-1 d-flex align-items-center">
                    Status
                </div>

                <div class="col-md-4">
                    <select class="form-select form-select-sm" id="filter-status">
                        <option value="all">All</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>

                <div class="col-md-1 d-flex align-items-center">
                    Type
                </div>

                <div class="col-md-4">
                    <select class="form-select form-select-sm" id="filter-type">
                        <option value="all">All</option>
                        <option value="regular">Regular</option>
                        <option value="recycle">Recycle</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- Form --}}
    <div class="card">

        <div class="card-body">
            {{-- Import Form --}}
            <form id="form-import" method="POST" enctype="multipart/form-data" class="mb-3">
                @csrf
                <div class="row align-items-center">
                    <div class="col-8">
                        <div class="input-group">
                            <input type="file" name="file" class="form-control form-control-sm" required
                                accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">
                            {{-- <button type="submit" class="btn btn-outline-success btn-sm" id='btn-import'>
                                <i class="fa-solid fa-file-import"></i> Import Battery Data</button> --}}
                            <button type="button" class="btn btn-outline-danger btn-sm" id='btn-import-price-ajax'>
                                <i class="fa-solid fa-dollar"></i> Import Battery Data Price</button>
                            {{-- <a href="{{ asset('template/excel/SampleImportBatteryBrand.xlsx') }}"
                                class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-download"></i>
                                Download Sample Import Data</a> --}}
                            {{-- Show Backup Data --}}
                            <button type="button" class="btn btn-outline-secondary btn-sm" id='btn-show-backup'>
                                <i class="fa-solid fa-clock-rotate-left"></i> Show Backup Data</button>
                        </div>
                    </div>
                </div>
            </form>


            {{-- Table --}}
            <div class="table-responsive">
                <table class="table table-striped" id="table-battery">
                    <thead>
                        <tr>
                            <th scope="col" class="table-col-no">#</th>
                            <th scope="col" class="table-col-status">Status</th>
                            <th scope="col">Code</th>
                            <th scope="col">Name</th>
                            <th scope="col">Brand</th>
                            <th scope="col">Subbrand Category</th>
                            <th scope="col">Usage Type</th>
                            <th scope="col">Size Category</th>
                            <th scope="col">Technology</th>
                            <th scope="col">Dimensions (mm)</th>
                            <th scope="col">Standard CCA (A)</th>
                            <th scope="col">Capacity (AH)</th>
                            <th scope="col">Warranty (month)</th>
                            <th scope="col">Retail Price (IDR)</th>
                            <th scope="col">Buy Price (IDR)</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-import-result" tabindex="-1" aria-labelledby="modal-import-result-label"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-import-result-label">Import Battery Result</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="import-result-content"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary d-none" id="btn-confirm-import-price">Confirm
                        Update</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal for Backup Data --}}
    <div class="modal fade" id="modal-backup-data" tabindex="-1" aria-labelledby="modal-backup-data-label"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-backup-data-label">Backup Data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="backup-data-content"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        var table;

        function escapeHtml(value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function formatPreviewValue(value) {
            if (value === null || value === undefined || value === '') {
                return '-';
            }

            if (typeof value === 'number') {
                return new Intl.NumberFormat('id-ID').format(value);
            }

            return escapeHtml(value);
        }

        function renderPreviewModal(response) {
            var previewRows = response.previewRows || [];
            var plannedUpdatedRows = response.plannedUpdatedRows || 0;
            var unimportedRows = response.unimportedRows || [];

            var summaryHtml = '<div class="row g-3 mb-3">' +
                '<div class="col-12 col-md-3"><div class="card border-0 shadow-sm"><div class="card-body"><div class="text-muted small text-uppercase fw-semibold">Total Rows</div><div class="h4 mb-0 text-primary">' +
                escapeHtml(response.totalRows || 0) + '</div></div></div></div>' +
                '<div class="col-12 col-md-3"><div class="card border-0 shadow-sm"><div class="card-body"><div class="text-muted small text-uppercase fw-semibold">Will Update</div><div class="h4 mb-0 text-warning">' +
                escapeHtml(plannedUpdatedRows) + '</div></div></div></div>' +
                '<div class="col-12 col-md-3"><div class="card border-0 shadow-sm"><div class="card-body"><div class="text-muted small text-uppercase fw-semibold">Skipped / Failed</div><div class="h4 mb-0 text-danger">' +
                escapeHtml(unimportedRows.length) + '</div></div></div></div>' +
                '<div class="col-12 col-md-3"><div class="card border-0 shadow-sm"><div class="card-body"><div class="text-muted small text-uppercase fw-semibold">Preview Rows</div><div class="h4 mb-0 text-info">' +
                escapeHtml(previewRows.length) + '</div></div></div></div>' +
                '</div>';

            var tableHtml = '<div class="table-responsive">' +
                '<table class="table table-sm table-bordered align-middle">' +
                '<thead class="table-light"><tr>' +
                '<th style="width:70px;">Row</th>' +
                '<th style="width:120px;">Battery ID</th>' +
                '<th style="width:120px;">Code</th>' +
                '<th>Name</th>' +
                '<th style="width:150px;">Current Retail</th>' +
                '<th style="width:150px;">New Retail</th>' +
                '<th style="width:150px;">Current Buy</th>' +
                '<th style="width:150px;">New Buy</th>' +
                '<th style="width:120px;">Action</th>' +
                '<th>Changes / Reason</th>' +
                '</tr></thead><tbody>';

            if (previewRows.length === 0) {
                tableHtml += '<tr><td colspan="9" class="text-center text-muted">No preview data available.</td></tr>';
            } else {
                previewRows.forEach(function(row) {
                    var badgeClass = 'bg-secondary';
                    var badgeText = row.action || 'skipped';

                    if (row.action === 'preview') {
                        badgeClass = 'bg-warning text-dark';
                        badgeText = 'preview';
                    } else if (row.action === 'updated') {
                        badgeClass = 'bg-success';
                        badgeText = 'updated';
                    } else if (row.action === 'failed') {
                        badgeClass = 'bg-danger';
                        badgeText = 'failed';
                    }

                    var changes = row.changes || {};
                    var changeList = Object.keys(changes).length ? Object.keys(changes).map(function(key) {
                        return '<div><strong>' + escapeHtml(key) + ':</strong> ' + formatPreviewValue(
                                changes[key][0]) + ' &rarr; ' + formatPreviewValue(changes[key][1]) +
                            '</div>';
                    }).join('') : '<div>-</div>';

                    tableHtml += '<tr>' +
                        '<td>' + escapeHtml(row.row_number || '-') + '</td>' +
                        '<td>' + escapeHtml(row.id || '-') + '</td>' +
                        '<td>' + escapeHtml(row.code || '-') + '</td>' +
                        '<td>' + escapeHtml(row.name || '-') + '</td>' +
                        '<td>' + formatPreviewValue(row.current_price_retail) + '</td>' +
                        '<td>' + formatPreviewValue(row.new_price_retail) + '</td>' +
                        '<td>' + formatPreviewValue(row.current_price_buy) + '</td>' +
                        '<td>' + formatPreviewValue(row.new_price_buy) + '</td>' +
                        '<td><span class="badge ' + badgeClass + '">' + escapeHtml(badgeText) + '</span></td>' +
                        '<td><div class="fw-semibold">' + escapeHtml(row.reason || '-') + '</div>' + changeList +
                        '</td>' +
                        '</tr>';
                });
            }

            tableHtml += '</tbody></table></div>';

            var noticeHtml = '<div class="alert alert-warning mb-3">' +
                'This preview has not saved any changes yet. Click <strong>Confirm Update</strong> to apply the changes, or close the modal to cancel everything.' +
                '</div>';

            return summaryHtml + noticeHtml + tableHtml;
        }

        $(document).ready(function() {
            // DataTables configuration
            table = $("#table-battery").DataTable({
                lengthMenu: [
                    [5, 10, 25],
                    [5, 10, 25]
                ],
                pageLength: 10,
                scrollX: true,
                responsive: true,
                processing: true,
                serverSide: true,
                order: [],
                ajax: {
                    url: "/battery/show",
                    type: "POST",
                    data: function(d) {
                        d._token = "{{ csrf_token() }}";
                        d.status = $("#filter-status").val();
                        d.type = $("#filter-type").val();
                    }
                },
                columnDefs: [{
                    targets: [0],
                    orderable: false
                }, {
                    targets: [8, 9, 10, 11],
                    className: 'dt-body-right'
                }, {
                    targets: [13, 14],
                    className: 'table-col-price'
                }, {
                    targets: [0, 1, -1],
                    className: 'dt-body-center'
                }],
                dom: "lBfrtip",
                buttons: getDatatablesButtonConfigurations([
                    // Export to Excel
                    {
                        text: "<i class='fas fa-file-excel'></i> Export All to Excel",
                        className: "btn btn-outline-secondary btn-sm",
                        action: function(e, dt, node, config) {
                            var formData = new FormData();
                            formData.append('_token', "{{ csrf_token() }}");

                            $.ajax({
                                url: '/battery/export',
                                method: 'POST',
                                data: {
                                    _token: "{{ csrf_token() }}"
                                },
                                xhrFields: {
                                    responseType: 'blob'
                                },
                                success: function(data) {
                                    var url = window.URL.createObjectURL(data);
                                    var a = document.createElement('a');
                                    a.href = url;
                                    a.download = 'batteries_' + new Date()
                                        .toISOString().slice(0, 19).replace(/[:T]/g,
                                            '-') + '.xlsx';
                                    document.body.append(a);
                                    a.click();
                                    a.remove();
                                    window.URL.revokeObjectURL(url);
                                },
                                error: function() {
                                    alert('Error exporting data');
                                }
                            });
                        }
                    },
                    // Compress Image Button
                    {
                        text: "<i class='fas fa-file-archive'></i> Compress Images",
                        className: "btn btn-outline-secondary btn-sm",
                        action: function(e, dt, node, config) {
                            // disable button
                            node.attr("disabled", true);
                            // swal loading
                            Swal.fire({
                                title: 'Compressing Images',
                                text: 'Please wait...',
                                allowOutsideClick: false,
                                onBeforeOpen: () => {
                                    Swal.showLoading();
                                },
                                showConfirmButton: false,
                            });
                            $.ajax({
                                url: '/battery/compress',
                                method: 'POST',
                                data: {
                                    _token: "{{ csrf_token() }}"
                                },
                                success: function(response) {
                                    // enable button
                                    node.attr("disabled", false);
                                    if (response.status == "success") {
                                        // swal
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Success',
                                            text: response.message,
                                        });
                                    } else {
                                        // swal
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Error',
                                            text: response.message,
                                        });
                                    }
                                }
                            });
                        }
                    }
                ]),
                language: getDatatablesLanguangeConfigurations("Battery"),
                select: true,
                rowCallback: function(row, data) {
                    if (data[16] == 0) {
                        $('td', row).addClass("text-muted");
                    }
                }
            });

            // Load DataTables toolbar component.
            appendDatatablesToolbar(15, "/battery/edit/", null, "/battery/toggle");

            $("#btn-add").on("click", function() {
                goToPage("/battery/create");
            });

            $("#form-import").on("submit", function(e) {
                $("#btn-import").attr("disabled", true);
                var button = $("#btn-import");
                button.html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...'
                );
                e.preventDefault();
                var formData = new FormData(this);
                $.ajax({
                    url: '/battery/import',
                    method: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        // Reset form.
                        $("#form-import")[0].reset();
                        $("#btn-import").attr("disabled", false);
                        button.html(
                            '<i class="fa-solid fa-file-import"></i> Import Battery Data'
                        );

                        // Reload table.
                        table.ajax.reload();

                        // Open import status page.
                        var newTab = window.open();
                        newTab.document.open();
                        newTab.document.write(response);
                        newTab.document.close();
                    }
                });
            });

            $("#btn-import-price").on("click", function() {
                $("#btn-import").attr("disabled", true);
                var button = $("#btn-import-price");
                button.html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...'
                );

                var formData = new FormData();
                formData.append('file', $('input[type="file"]')[0].files[0]);
                formData.append('_token', "{{ csrf_token() }}");

                $.ajax({
                    url: '/battery/import/price',
                    method: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        // Reset form.
                        $("#form-import")[0].reset();
                        $("#btn-import").attr("disabled", false);
                        button.html(
                            '<i class="fa-solid fa-file-import"></i> Import Battery Price'
                        );

                        // Reload table.
                        table.ajax.reload();

                        // Open import status page.
                        var newTab = window.open();
                        newTab.document.open();
                        newTab.document.write(response);
                        newTab.document.close();
                    }
                });
            })

            $("#filter-status").on("change", function() {
                table.ajax.reload();
            });

            $("#filter-type").on("change", function() {
                table.ajax.reload();
            });

            $("#btn-import-price-ajax").on("click", function() {
                var button = $(this);
                var fileInput = $('input[type="file"]')[0];

                if (!fileInput.files.length) {
                    alert('Please choose a file first.');
                    return;
                }

                button.attr('disabled', true);
                button.html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...'
                );

                var formData = new FormData();
                formData.append('file', fileInput.files[0]);
                formData.append('_token', "{{ csrf_token() }}");

                $.ajax({
                    url: '/battery/import/price/preview',
                    method: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        if (!response.status) {
                            $("#import-result-content").html(
                                '<div class="alert alert-danger mb-0">' + escapeHtml(
                                    response.error || 'Failed to load preview.') + '</div>');
                            $("#btn-confirm-import-price").addClass('d-none').data(
                                'preview-ready', false);
                            $("#modal-import-result").modal("show");
                            return;
                        }

                        $("#import-result-content").html(renderPreviewModal(response));
                        $("#btn-confirm-import-price").removeClass('d-none').data(
                            'preview-ready', true);
                        $("#modal-import-result").modal("show");

                        // remove loading state
                        button.attr('disabled', false).html(
                            '<i class="fa-solid fa-dollar"></i> Import Battery Data Price'
                        );
                    },
                    complete: function() {
                        button.attr('disabled', false);
                    }
                });
            });

            $("#btn-confirm-import-price").on("click", function() {
                if (!$(this).data('preview-ready')) {
                    return;
                }

                var button = $(this);
                var fileInput = $('input[type="file"]')[0];

                if (!fileInput.files.length) {
                    alert('Please choose a file first.');
                    return;
                }

                button.attr('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Updating...'
                );

                var formData = new FormData();
                formData.append('file', fileInput.files[0]);
                formData.append('_token', "{{ csrf_token() }}");

                $.ajax({
                    url: '/battery/import/price',
                    method: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        $("#form-import")[0].reset();
                        table.ajax.reload();

                        var newTab = window.open();
                        newTab.document.open();
                        newTab.document.write(response);
                        newTab.document.close();

                        $("#modal-import-result").modal('hide');
                    },
                    error: function() {
                        alert('Failed to apply import.');
                    },
                    complete: function() {
                        button.attr('disabled', false).html('Confirm Update');
                    }
                });
            });

            // btn-show-backup
            $("#btn-show-backup").on("click", function() {
                var button = $(this);
                button.attr('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...'
                );

                $.ajax({
                    url: '/battery/backup',
                    method: 'GET',
                    success: function(response) {
                        $("#backup-data-content").html(response);
                        $("#modal-backup-data").modal("show");
                    },
                    error: function() {
                        alert('Failed to load backup data.');
                    },
                    complete: function() {
                        button.attr('disabled', false).html(
                            '<i class="fa-solid fa-clock-rotate-left"></i> Show Backup Data'
                        );
                    }
                });
            });

            $(document).on('click', '.btn-restore-backup', function() {
                var backupNumber = $(this).data('backup-number');

                Swal.fire({
                    title: 'Restore Backup?',
                    text: 'This will restore all rows in backup number ' + backupNumber + '.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Restore',
                    cancelButtonText: 'Cancel'
                }).then(function(result) {
                    if (!result.isConfirmed) {
                        return;
                    }


                    // add swal loading
                    Swal.fire({
                        title: 'Restoring Backup',
                        text: 'Please wait...',
                        allowOutsideClick: false,
                        onBeforeOpen: () => {
                            Swal.showLoading();
                        },
                        showConfirmButton: false,
                    });

                    $.ajax({
                        url: '/battery/backup/restore',
                        method: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}",
                            backup_number: backupNumber
                        },
                        success: function(response) {
                            table.ajax.reload();

                            Swal.fire({
                                icon: 'success',
                                title: 'Restored',
                                text: (response.message ||
                                        'Backup restored successfully.') +
                                    (response.restored_count !== undefined ?
                                        ' Restored rows: ' + response
                                        .restored_count + '.' : '')
                            });
                        },
                        error: function(xhr) {
                            var message = (xhr.responseJSON && xhr.responseJSON
                                    .message) ? xhr
                                .responseJSON.message : 'Failed to restore backup.';

                            Swal.fire({
                                icon: 'error',
                                title: 'Restore Failed',
                                text: message
                            });
                        }
                    });
                });
            });

            $(document).on('click', '.btn-delete-backup', function() {
                var backupNumber = $(this).data('backup-number');

                Swal.fire({
                    title: 'Delete Backup?',
                    text: 'Backup number ' + backupNumber + ' will be deleted permanently.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Delete',
                    cancelButtonText: 'Cancel'
                }).then(function(result) {
                    if (!result.isConfirmed) {
                        return;
                    }

                    $.ajax({
                        url: '/battery/backup/delete',
                        method: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}",
                            _method: 'DELETE',
                            backup_number: backupNumber
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted',
                                text: response.message ||
                                    'Backup deleted successfully.'
                            });

                            // Reload grouped backup modal content and main battery table.
                            $.ajax({
                                url: '/battery/backup',
                                method: 'GET',
                                success: function(html) {
                                    $('#backup-data-content').html(html);
                                }
                            });

                            table.ajax.reload();
                        },
                        error: function(xhr) {
                            var message = (xhr.responseJSON && xhr.responseJSON
                                    .message) ? xhr
                                .responseJSON.message : 'Failed to delete backup.';

                            Swal.fire({
                                icon: 'error',
                                title: 'Delete Failed',
                                text: message
                            });
                        }
                    });
                });
            });
        });
    </script>
@endsection
