@extends('template.master')


@section('content')
    <div class="card">
        <div class="card-body">
            <h4 class="header-title mb-4">Print Templates</h4>
            <ul class="nav nav-pills navtab-bg nav-justified" role="tablist">
                <li class="nav-item" role="presentation">
                    <a href="#regular-instalasi" data-bs-toggle="tab" aria-expanded="true" class="nav-link active"
                        aria-selected="true" role="tab" tabindex="-1">
                        Regular dengan Instalasi
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="#tokopedia-instalasi" data-bs-toggle="tab" aria-expanded="false" class="nav-link "
                        aria-selected="false" role="tab">
                        Tokopedia dengan Instalasi
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="#tokopedia-tanpa-instalasi" data-bs-toggle="tab" aria-expanded="false" class="nav-link"
                        aria-selected="false" role="tab" tabindex="-1">
                        Tokopedia tanpa Instalasi
                    </a>
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active show" id="regular-instalasi" role="tabpanel">
                    <table class="table table-bordered" id="table-regular-instalasi">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Step</th>
                                <th>Template</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data['templatesRegularInstalasi'] as $key => $item)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>
                                        <input type="number" class="form-control" name="step_no[{{ $item->id }}]"
                                            id="step_no[{{ $item->id }}]" value="{{ $item->step_no }}">
                                    </td>
                                    <td>
                                        <textarea class="form-control" name="message[{{ $item->id }}]" id="message[{{ $item->id }}]" rows="3">{{ $item->message }}</textarea>
                                    </td>
                                    <td>
                                        <button class="btn btn-danger btn-sm" id="delete-row-regular-instalasi">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    {{-- add row --}}
                    <div class="row mt-3 mb-3">
                        <div class="col">
                            <button class="btn btn-primary btn-sm" id="add-row-regular-instalasi">Add Row</button>
                        </div>
                        <div class="col text-end">
                            <button class="btn btn-success btn-sm" id="btn-save-regular-instalasi">Save Print
                                Templates</button>
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="tokopedia-instalasi" role="tabpanel">
                    <table class="table table-bordered" id="table-tokopedia-instalasi">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Step</th>
                                <th>Template</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data['templatesTokopediaInstalasi'] as $key => $item)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>
                                        <input type="number" class="form-control" name="step_no[{{ $item->id }}]"
                                            id="step_no[{{ $item->id }}]" value="{{ $item->step_no }}">
                                    </td>
                                    <td>
                                        <textarea class="form-control" name="message[{{ $item->id }}]" id="message[{{ $item->id }}]" rows="3">{{ $item->message }}</textarea>
                                    </td>
                                    <td>
                                        <button class="btn btn-danger btn-sm" id="delete-row-tokopedia-instalasi">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    {{-- add row --}}
                    <div class="row mt-3 mb-3">
                        <div class="col">
                            <button class="btn btn-primary btn-sm" id="add-row-tokopedia-instalasi">Add Row</button>
                        </div>
                        <div class="col text-end">
                            <button class="btn btn-success btn-sm" id="btn-save-tokopedia-instalasi">Save Print
                                Templates</button>
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="tokopedia-tanpa-instalasi" role="tabpanel">
                    <table class="table table-bordered" id="table-tokopedia-tanpa-instalasi">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Step</th>
                                <th>Template</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data['templatesTokopediaTanpaInstalasi'] as $key => $item)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>
                                        <input type="number" class="form-control" name="step_no[{{ $item->id }}]"
                                            id="step_no[{{ $item->id }}]" value="{{ $item->step_no }}">
                                    </td>
                                    <td>
                                        <textarea class="form-control" name="message[{{ $item->id }}]" id="message[{{ $item->id }}]" rows="3">{{ $item->message }}</textarea>
                                    </td>
                                    <td>
                                        <button class="btn btn-danger btn-sm" id="delete-row-tokopedia-tanpa-instalasi">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    {{-- add row --}}
                    <div class="row mt-3 mb-3">
                        <div class="col">
                            <button class="btn btn-primary btn-sm" id="add-row-tokopedia-tanpa-instalasi">Add Row</button>
                        </div>
                        <div class="col text-end">
                            <button class="btn btn-success btn-sm" id="btn-save-tokopedia-tanpa-instalasi">Save Print
                                Templates</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // add-row-regular-instalasi
            $("#add-row-regular-instalasi").on("click", function() {
                let table = $("#table-regular-instalasi tbody");
                let rowCount = table.children().length;
                // jika sudah 11 row, maka tidak bisa menambah row lagi
                if (rowCount >= 11) {
                    swal.fire({
                        title: "Warning",
                        text: "Maximum row is 11",
                        icon: "warning",
                    });
                    return;
                }
                let newRow = `
                    <tr>
                        <td>${rowCount + 1}</td>
                        <td>
                            <input type="number" class="form-control" name="step_no[${rowCount}]" id="step_no[${rowCount}]" value="${rowCount + 1}">
                        </td>
                        <td>
                            <textarea class="form-control" name="message[${rowCount}]" id="message[${rowCount}]" rows="3"></textarea>
                        </td>
                        <td>
                            <button class="btn btn-danger btn-sm" id="delete-row-regular-instalasi">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
                table.append(newRow);
            });

            // delete-row-regular-instalasi
            $("#table-regular-instalasi").on("click", "#delete-row-regular-instalasi", function() {
                $(this).closest("tr").remove();
            });

            // btn-save-regular-instalasi
            $("#btn-save-regular-instalasi").on("click", function() {
                let $btn = $(this);
                $btn.prop("disabled", true);
                $btn.html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...'
                );

                // Obtain submitted form data.
                let formData = new FormData();
                $("#table-regular-instalasi tbody tr").each(function(index, tr) {
                    let stepNo = $(tr).find("input[name^='step_no']").val();
                    let message = $(tr).find("textarea[name^='message']").val();

                    formData.append(`step_no[${index}]`, stepNo);
                    formData.append(`message[${index}]`, message);
                });
                formData.append("tipe", "regular-instalasi");
                formData.append("_token", "{{ csrf_token() }}");

                sendSubmitRequest("/template/print/update", formData);

                $btn.prop("disabled", false);
                $btn.html("Save Print Templates");
            });
        });

        $(document).ready(function() {
            // add-row-tokopedia-instalasi
            $("#add-row-tokopedia-instalasi").on("click", function() {
                let table = $("#table-tokopedia-instalasi tbody");
                let rowCount = table.children().length;
                // jika sudah 11 row
                if (rowCount >= 11) {
                    swal.fire({
                        title: "Warning",
                        text: "Maximum row is 11",
                        icon: "warning",
                    });
                    return;
                }
                let newRow = `
                    <tr>
                        <td>${rowCount + 1}</td>
                        <td>
                            <input type="number" class="form-control" name="step_no[${rowCount}]" id="step_no[${rowCount}]" value="${rowCount + 1}">
                        </td>
                        <td>
                            <textarea class="form-control" name="message[${rowCount}]" id="message[${rowCount}]" rows="3"></textarea>
                        </td>
                        <td>
                            <button class="btn btn-danger btn-sm" id="delete-row-tokopedia-instalasi">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
                table.append(newRow);
            });

            // delete-row-tokopedia-instalasi
            $("#table-tokopedia-instalasi").on("click", "#delete-row-tokopedia-instalasi", function() {
                $(this).closest("tr").remove();
            });

            // btn-save-tokopedia-instalasi
            $("#btn-save-tokopedia-instalasi").on("click", function() {
                let $btn = $(this);
                $btn.prop("disabled", true);
                $btn.html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...'
                );

                // Obtain submitted form data.
                let formData = new FormData();
                $("#table-tokopedia-instalasi tbody tr").each(function(index, tr) {
                    let stepNo = $(tr).find("input[name^='step_no']").val();
                    let message = $(tr).find("textarea[name^='message']").val();

                    formData.append(`step_no[${index}]`, stepNo);
                    formData.append(`message[${index}]`, message);
                });
                formData.append("tipe", "tokopedia-instalasi");
                formData.append("_token", "{{ csrf_token() }}");

                sendSubmitRequest("/template/print/update", formData);

                $btn.prop("disabled", false);
                $btn.html("Save Print Templates");
            });

            // add-row-tokopedia-tanpa-instalasi
            $("#add-row-tokopedia-tanpa-instalasi").on("click", function() {
                let table = $("#table-tokopedia-tanpa-instalasi tbody");
                let rowCount = table.children().length;
                // jika sudah 11 row
                if (rowCount >= 11) {
                    swal.fire({
                        title: "Warning",
                        text: "Maximum row is 11",
                        icon: "warning",
                    });
                    return;
                }
                let newRow = `
                    <tr>
                        <td>${rowCount + 1}</td>
                        <td>
                            <input type="number" class="form-control" name="step_no[${rowCount}]" id="step_no[${rowCount}]" value="${rowCount + 1}">
                        </td>
                        <td>
                            <textarea class="form-control" name="message[${rowCount}]" id="message[${rowCount}]" rows="3"></textarea>
                        </td>
                        <td>
                            <button class="btn btn-danger btn-sm" id="delete-row-tokopedia-tanpa-instalasi">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
                table.append(newRow);
            });

            // delete-row-tokopedia-tanpa-instalasi
            $("#table-tokopedia-tanpa-instalasi").on("click", "#delete-row-tokopedia-tanpa-instalasi", function() {
                $(this).closest("tr").remove();
            });

            // btn-save-tokopedia-tanpa-instalasi
            $("#btn-save-tokopedia-tanpa-instalasi").on("click", function() {
                let $btn = $(this);
                $btn.prop("disabled", true);
                $btn.html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...'
                );

                // Obtain submitted form data.
                let formData = new FormData();
                $("#table-tokopedia-tanpa-instalasi tbody tr").each(function(index, tr) {
                    let stepNo = $(tr).find("input[name^='step_no']").val();
                    let message = $(tr).find("textarea[name^='message']").val();

                    formData.append(`step_no[${index}]`, stepNo);
                    formData.append(`message[${index}]`, message);
                });
                formData.append("tipe", "tokopedia-tanpa-instalasi");
                formData.append("_token", "{{ csrf_token() }}");

                sendSubmitRequest("/template/print/update", formData);

                $btn.prop("disabled", false);
                $btn.html("Save Print Templates");
            });
        });
    </script>
@endsection
