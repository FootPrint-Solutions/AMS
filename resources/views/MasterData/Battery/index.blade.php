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
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        var table;

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
                var formData = new FormData();
                formData.append('file', $('input[type="file"]')[0].files[0]);
                formData.append('_token', "{{ csrf_token() }}");

                $.ajax({
                    url: '/battery/import/price/preview',
                    method: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        $("#import-result-content").html(response);
                        $("#modal-import-result").modal("show");
                    }
                });
            });
        });
    </script>
@endsection
