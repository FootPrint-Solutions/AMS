@extends('template.master')


@section('content')
    <div class="card">
        <div class="card-body">
            <h4 class="header-title mb-4">Print Templates</h4>
            <ul class="nav nav-pills navtab-bg nav-justified" role="tablist">
                <li class="nav-item" role="presentation">
                    <a href="#page-one" data-bs-toggle="tab" aria-expanded="true" class="nav-link active" aria-selected="true"
                        role="tab" tabindex="-1">
                        Page One
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="#page-two" data-bs-toggle="tab" aria-expanded="false" class="nav-link " aria-selected="false"
                        role="tab">
                        Page Two
                    </a>
                </li>
            </ul>

            <input type="hidden" name="id" id="id" value="{{ $data['template']['id'] }}">
            <div class="tab-content">
                <div class="tab-pane active show" id="page-one" role="tabpanel">
                    <table class="table table-bordered" id="table-page-one">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Step</th>
                                <th>Template</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data['templateDetailsPageOne'] as $key => $item)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>
                                        <input type="number" class="form-control" name="step_no[{{ $item->id }}]"
                                            id="step_no[{{ $item->id }}]" value="{{ $item->step_no }}" required>
                                    </td>
                                    <td>
                                        <textarea class="form-control" name="message[{{ $item->id }}]" id="message[{{ $item->id }}]" rows="3"
                                            required>{{ $item->message }}</textarea>
                                    </td>
                                    <td>
                                        <button class="btn btn-danger btn-sm" id="delete-row-page-one">
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
                            <button class="btn btn-primary btn-sm" id="add-row-page-one">Add Row</button>
                        </div>
                        <div class="col text-end">
                            <button class="btn btn-success btn-sm" id="btn-save-page-one">Save Print
                                Templates</button>
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="page-two" role="tabpanel">
                    <table class="table table-bordered" id="table-page-two">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Step</th>
                                <th>Template</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data['templateDetailsPageTwo'] as $key => $item)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>
                                        <input type="number" class="form-control" name="step_no[{{ $item->id }}]"
                                            id="step_no[{{ $item->id }}]" value="{{ $item->step_no }}" required>
                                    </td>
                                    <td>
                                        <textarea class="form-control" name="message[{{ $item->id }}]" id="message[{{ $item->id }}]" rows="3"
                                            required>{{ $item->message }}</textarea>
                                    </td>
                                    <td>
                                        <button class="btn btn-danger btn-sm" id="delete-row-page-two">
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
                            <button class="btn btn-primary btn-sm" id="add-row-page-two">Add Row</button>
                        </div>
                        <div class="col text-end">
                            <button class="btn btn-success btn-sm" id="btn-save-page-two">Save Print
                                Templates</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // add-row-page-one
            $("#add-row-page-one").on("click", function() {
                let table = $("#table-page-one tbody");
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
                            <input type="number" class="form-control" name="step_no[${rowCount}]" id="step_no[${rowCount}]" value="${rowCount + 1}" required>
                        </td>
                        <td>
                            <textarea class="form-control" name="message[${rowCount}]" id="message[${rowCount}]" rows="3" required></textarea>
                        </td>
                        <td>
                            <button class="btn btn-danger btn-sm" id="delete-row-page-one">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
                table.append(newRow);
            });

            // delete-row-page-one
            $("#table-page-one").on("click", "#delete-row-page-one", function() {
                $(this).closest("tr").remove();
            });

            // btn-save-page-one
            $("#btn-save-page-one").on("click", function() {
                let $btn = $(this);
                $btn.prop("disabled", true);
                $btn.html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...'
                );

                // Obtain submitted form data.
                let formData = new FormData();
                var hasError = false;
                $("#table-page-one tbody tr").each(function(index, tr) {

                    // check if the row is empty
                    if ($(tr).find("input[name^='step_no']").val() == "" || $(tr).find(
                            "textarea[name^='message']").val() == "") {
                        swal.fire({
                            title: "Warning",
                            text: "Please fill all the fields",
                            icon: "warning",
                        });
                        hasError = true;
                        return;
                    }

                    let stepNo = $(tr).find("input[name^='step_no']").val();
                    let message = $(tr).find("textarea[name^='message']").val();

                    formData.append(`step_no[${index}]`, stepNo);
                    formData.append(`message[${index}]`, message);
                });

                // check if the row is empty
                if ($("#table-page-one tbody tr").length == 0) {
                    swal.fire({
                        title: "Warning",
                        text: "Please add at least one row",
                        icon: "warning",
                    });
                    hasError = true;
                    return false;
                }

                // check if the row step_no and message is empty
                if ($("#table-page-one tbody tr").find("input[name^='step_no']").val() == "" || $(
                        "#table-page-one tbody tr").find(
                        "textarea[name^='message']").val() == "") {
                    swal.fire({
                        title: "Warning",
                        text: "Please fill all the fields",
                        icon: "warning",
                    });
                    hasError = true;
                    return false;
                }

                // check if the row step_no unique
                let stepNoArray = [];
                $("#table-page-one tbody tr").each(function(index, tr) {
                    let stepNo = $(tr).find("input[name^='step_no']").val();
                    if (stepNoArray.includes(stepNo)) {
                        swal.fire({
                            title: "Warning",
                            text: "Step No must be unique",
                            icon: "warning",
                        });
                        hasError = true;
                        return;
                    }
                    stepNoArray.push(stepNo);
                });

                if (hasError) {
                    $btn.prop("disabled", false);
                    $btn.html("Save Print Templates");
                    return false;
                }

                formData.append("tipe", "page-one");
                formData.append("id", $("#id").val());
                formData.append("_token", "{{ csrf_token() }}");

                sendSubmitRequest("/template/print/update/details", formData);

                $btn.prop("disabled", false);
                $btn.html("Save Print Templates");

                // redirect to the previous page after 3 seconds
                setTimeout(function() {
                    window.history.back();
                }, 2000);
            });
        });

        $(document).ready(function() {
            // add-row-page-two
            $("#add-row-page-two").on("click", function() {
                let table = $("#table-page-two tbody");
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
                            <input type="number" class="form-control" name="step_no[${rowCount}]" id="step_no[${rowCount}]" value="${rowCount + 1}" required>
                        </td>
                        <td>
                            <textarea class="form-control" name="message[${rowCount}]" id="message[${rowCount}]" rows="3" required></textarea>
                        </td>
                        <td>
                            <button class="btn btn-danger btn-sm" id="delete-row-page-two">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
                table.append(newRow);
            });

            // delete-row-page-two
            $("#table-page-two").on("click", "#delete-row-page-two", function() {
                $(this).closest("tr").remove();
            });

            // btn-save-page-two
            $("#btn-save-page-two").on("click", function() {
                let $btn = $(this);
                $btn.prop("disabled", true);
                $btn.html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...'
                );

                // Obtain submitted form data.
                let formData = new FormData();
                var hasError = false;
                $("#table-page-two tbody tr").each(function(index, tr) {

                    // check if the row is empty
                    if ($(tr).find("input[name^='step_no']").val() == "" || $(tr).find(
                            "textarea[name^='message']").val() == "") {
                        swal.fire({
                            title: "Warning",
                            text: "Please fill all the fields",
                            icon: "warning",
                        });
                        hasError = true;
                        return;
                    }

                    let stepNo = $(tr).find("input[name^='step_no']").val();
                    let message = $(tr).find("textarea[name^='message']").val();

                    formData.append(`step_no[${index}]`, stepNo);
                    formData.append(`message[${index}]`, message);
                });

                // check if the row is empty
                if ($("#table-page-two tbody tr").length == 0) {
                    swal.fire({
                        title: "Warning",
                        text: "Please add at least one row",
                        icon: "warning",
                    });
                    hasError = true;
                    return false;
                }

                // check if the row step_no and message is empty
                if ($("#table-page-two tbody tr").find("input[name^='step_no']").val() == "" || $(
                        "#table-page-two tbody tr").find(
                        "textarea[name^='message']").val() == "") {
                    swal.fire({
                        title: "Warning",
                        text: "Please fill all the fields",
                        icon: "warning",
                    });
                    hasError = true;
                    return false;
                }

                // check if the row step_no unique
                let stepNoArray = [];
                $("#table-page-two tbody tr").each(function(index, tr) {
                    let stepNo = $(tr).find("input[name^='step_no']").val();
                    if (stepNoArray.includes(stepNo)) {
                        swal.fire({
                            title: "Warning",
                            text: "Step No must be unique",
                            icon: "warning",
                        });
                        hasError = true;
                        return;
                    }
                    stepNoArray.push(stepNo);
                });

                if (hasError) {
                    $btn.prop("disabled", false);
                    $btn.html("Save Print Templates");
                    return false;
                }

                formData.append("tipe", "page-two");
                formData.append("id", $("#id").val());
                formData.append("_token", "{{ csrf_token() }}");

                sendSubmitRequest("/template/print/update/details", formData);

                $btn.prop("disabled", false);
                $btn.html("Save Print Templates");

                setTimeout(function() {
                    window.history.back();
                }, 2000);
            });
        });
    </script>
@endsection
