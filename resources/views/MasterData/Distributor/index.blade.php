@extends('template.master')

@section('content')
    {{-- Form --}}
    <div class="card">
        <div class="card-body">
            {{-- Title --}}
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">Distributor</h3>
                    </div>
                    <div class="col-auto text-end float-end ms-auto download-grp">
                        <button id="btn-add" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add
                            New Distributor</button>
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
                                    class="fa-solid fa-file-import"></i> Import Vehicle</button>
                            <a href="{{ asset('template/excel/SampleImportVehicle.xlsx') }}"
                                class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-download"></i>
                                Download Sample Import Data</a>
                        </div>
                    </div>
                </div>
            </form>

            {{-- Table --}}
            {{-- <table class="table table-striped" id="table-vehicle">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Name</th>
                        <th scope="col">Brand</th>
                        <th scope="col">URL</th>
                    </tr>
                </thead>
            </table> --}}
        </div>
    </div>

    <script>
        var table;

        $(document).ready(function() {
            // Add New Vehicle button
            $("#btn-add").on("click", function() {
                goToPage("/vehicle/create");
            });

            // Add New Brand button
            $("#btn-add-brand").on("click", function() {
                goToPage("/vehicle/brand/create");
            });
        });

        function edit(id) {
            goToPage("/vehicle/edit/" + id);
        }

        function destroy(id) {
            $.ajax({
                url: "/vehicle/destroy",
                method: "POST",
                data: {
                    "_token": "{{ csrf_token() }}",
                    "id": id
                },
                success: function(response) {
                    // Get response data (in JSON).
                    let responseData = JSON.parse(response);

                    // Check response data status.
                    // Status indicates the success status of company profile update.
                    if (responseData.status) {
                        // Delete process was succeeded.
                        showSuccessToast(responseData.message);
                    } else {
                        // Delete process was failed.
                        showErrorToast(responseData.message);
                    }

                    // Reload table with updated rows.
                    table.ajax.reload();
                }
            });
        }

        $("#form-import").on("submit", function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.ajax({
                url: '/vehicle/import',
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
    </script>
@endsection
