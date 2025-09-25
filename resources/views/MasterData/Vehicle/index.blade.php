@extends('template.master')

@section('content')
    {{-- Form --}}
    <div class="card">
        <div class="card-body">
            {{-- Title --}}
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">Vehicle</h3>
                    </div>
                    <div class="col-auto text-end float-end ms-auto download-grp">
                        <button id="btn-add" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add
                            New Vehicle</button>
                    </div>
                </div>
            </div>
            <br>

            {{-- Import Form --}}
            <form id="form-import" method="POST" enctype="multipart/form-data" class="mb-3">
                @csrf
                <div class="row align-items-center">
                    <div class="col-8">
                        <div class="input-group">
                            <input type="file" name="file" class="form-control form-control-sm" required
                                accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">
                            <button type="submit" class="btn btn-outline-success btn-sm" id='btn-import'><i
                                    class="fa-solid fa-file-import"></i> Import Vehicle</button>
                            <a href="{{ asset('template/excel/SampleImportVehicle.xlsx') }}"
                                class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-download"></i>
                                Download Sample Import Data</a>
                        </div>
                    </div>
                </div>
            </form>

            {{-- Table --}}
            <table class="table table-striped" id="table-vehicle">
                <thead>
                    <tr>
                        <th scope="col" class="table-col-no">#</th>
                        <th scope="col">Name</th>
                        <th scope="col">Brand</th>
                        <th scope="col">URL</th>
                        <th scope="col">Battery Size</th>
                        <th scope="col">Year</th>
                        <th scope="col">Note</th>
                        <th scope="col" class="table-col-status">Status</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <script>
        var table;

        $(document).ready(function() {
            // DataTables configuration
            table = $("#table-vehicle").DataTable({
                lengthMenu: [
                    [10, 25, 50],
                    [10, 25, 50]
                ],
                pageLength: 10,
                responsive: true,
                processing: true,
                serverSide: true,
                order: [],
                ajax: {
                    url: "/vehicle/show",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}"
                    }
                },
                columnDefs: [{
                    targets: [0],
                    orderable: false
                }, {
                    targets: [0, -1],
                    className: 'text-center'
                }],
                dom: "lBfrtip",
                buttons: getDatatablesButtonConfigurations(),
                language: getDatatablesLanguangeConfigurations("Vehicle"),
                select: true,
                rowCallback: function(row, data) {
                    if (data[5] == 0) {
                        $('td', row).addClass("text-muted");
                    }
                }
            });

            // Load DataTables toolbar component.
            appendDatatablesToolbar(8, "/vehicle/edit/", null, "/vehicle/toggle");

            // Add New Vehicle button
            $("#btn-add").on("click", function() {
                goToPage("/vehicle/create");
            });
        });

        $("#form-import").on("submit", function(e) {
            e.preventDefault();
            $("#btn-import").attr("disabled", true);
            var button = $("#btn-import");
            button.html(
                '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...'
            );
            var formData = new FormData(this);
            $.ajax({
                url: '/vehicle/import',
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    // Company profile update was succeeded.
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
    </script>
@endsection
