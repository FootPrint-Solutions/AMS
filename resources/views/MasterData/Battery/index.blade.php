@extends('template.master')

@section('content')
    {{-- Form --}}
    <div class="card">

        <div class="card-body">
            {{-- Title --}}
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">Battery</h3>
                    </div>
                    <div class="col-auto text-end float-end ms-auto download-grp">
                        <button id="btn-add" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add
                            New Battery</button>
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
                            <input type="file" name="file" class="form-control form-control-sm">
                            <button type="submit" class="btn btn-outline-success btn-sm"><i
                                    class="fa-solid fa-file-import"></i> Import Battery Data</button>
                            <a href="{{ asset('template/excel/SampleImportBatteryBrand.xlsx') }}"
                                class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-download"></i>
                                Download Sample Import Data</a>
                        </div>
                    </div>
                </div>
            </form>


            {{-- Table --}}
            <div class="table-responsive">
                <table class="table table-striped" id="table-battery">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
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
                        </tr>
                    </thead>
                </table>
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
                responsive: true,
                processing: true,
                serverSide: true,
                order: [],
                ajax: {
                    url: "/battery/show",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}"
                    }
                },
                columnDefs: [{
                    targets: [0],
                    orderable: false
                }],
                dom: "lBfrtip",
                buttons: getDatatablesButtonConfigurations(),
                language: {
                    searchPlaceholder: "Search Battery",
                    search: "",
                    lengthMenu: "_MENU_ entries | ",
                },
                select: true,
            });

            // Load DataTables toolbar component.
            appendDatatablesToolbar(12);
            
            $("#btn-add").on("click", function() {
                goToPage("/battery/create");
            });

            $("#btn-add-brand").on("click", function() {
                goToPage("/battery/brand/create");
            });

            $("#btn-add-subbrand").on("click", function() {
                goToPage("/battery/subbrand/create");
            });

            $("#btn-add-usage").on("click", function() {
                goToPage("/battery/usage/create");
            });

            $("#btn-add-tech").on("click", function() {
                goToPage("/battery/technology/create");
            });

            $("#form-import").on("submit", function(e) {
                e.preventDefault();
                var formData = new FormData(this);
                $.ajax({
                    url: '/battery/import',
                    method: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        // Get response data (in JSON).
                        let responseData = JSON.parse(response);

                        // Check response data status.
                        // Status indicates the success status of company profile update.
                        if (responseData.status) {
                            // Company profile update was succeeded.
                            showSuccessToast(responseData.message);
                            $("#form-import")[0].reset();
                        } else {
                            // Company profile update was failed.
                            showErrorToast(responseData.message);
                            $("#form-import")[0].reset();
                        }

                        // Reload table with updated rows.
                        table.ajax.reload();
                    }
                });
            });


        });

        function edit(id) {
            goToPage("/battery/edit/" + id);
        }

        function destroy(id) {
            sendDestroyRequest(id, "/battery/destroy", function() {
                // Reload the index table.
                table.ajax.reload();
            });
        }
    </script>
@endsection
