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
                                        {{-- button delete --}}
                                        <button class="btn btn-danger btn-sm" id="delete-row-page-one">
                                            <i class="fas fa-trash"></i>
                                        </button>

                                        {{-- button add sub row --}}
                                        <button class="btn btn-primary btn-sm" id="add-sub-row-page-one"
                                            data-id="{{ $item->id }}">
                                            <i class="fas fa-plus"></i>
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
                                        {{-- button delete --}}
                                        <button class="btn btn-danger btn-sm" id="delete-row-page-two">
                                            <i class="fas fa-trash"></i>
                                        </button>

                                        {{-- button add sub row --}}
                                        <button class="btn btn-primary btn-sm" id="add-sub-row-page-two"
                                            data-id="{{ $item->id }}">
                                            <i class="fas fa-plus"></i>
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

    {{-- modal-sub-row --}}
    <div class="modal fade modal-lg" id="modal-sub-row" tabindex="-1" role="dialog" aria-labelledby="modal-sub-row"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form id="form-sub-row">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal-sub-row">Add Sub Task</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">

                        {{-- table-sub-task --}}
                        <div class="table-responsive mb-5" id="table-sub-task"></div>


                        <input type="hidden" name="id" id="id">
                        <div class="mb-3">
                            <label for="step_no" class="form-label">Step</label>
                            <input type="number" class="form-control" name="step_no" id="step_no" required>
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label">Template</label>
                            <textarea class="form-control" name="message" id="message" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="btn-save-sub-row">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // add-row-page-one
            $("#add-row-page-one").on("click", function() {
                let table = $("#table-page-one tbody");
                let rowCount = table.children().length;
                // jika sudah 15 row, maka tidak bisa menambah row lagi
                if (rowCount >= 15) {
                    swal.fire({
                        title: "Warning",
                        text: "Maximum row is 15",
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

                            <button class="btn btn-primary btn-sm" id="add-sub-row-page-one" data-id=${rowCount}>
                                <i class="fas fa-plus"></i>
                            </button>
                        </td>
                    </tr>
                `;
                table.append(newRow);
            });

            // add-sub-row-page-one show modal  
            $("#table-page-one").on("click", "#add-sub-row-page-one", function() {
                let id = $(this).data("id");
                let stepNo = $(`#step_no\\[${id}\\]`).val();
                let message = $(`#message\\[${id}\\]`).val();

                $("#modal-sub-row").modal("show");
                $("#modal-sub-row").find("#step_no").val(stepNo);
                $("#modal-sub-row").find("#message").val(message);
                $("#modal-sub-row").find("#id").val(id);

                // get sub task
                $.ajax({
                    url: "/template/print/get/sub-task",
                    type: "POST",
                    data: {
                        id: id,
                        _token: "{{ csrf_token() }}",
                    },
                    success: function(response) {
                        let table = $("#table-sub-task");
                        let tableContent = `
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Step</th>
                                        <th>Template</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                        `;

                        response.forEach((item, index) => {
                            tableContent += `
                                <tr>
                                    <td>${index + 1}</td>
                                    <td>${item.step_no}</td>
                                    <td>${item.message}</td>
                                    <td>
                                        <button class="btn btn-danger btn-sm" id="delete-sub-row-page-one" data-id="${item.id}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            `;
                        });

                        tableContent += `
                            </tbody>
                        </table>
                        `;

                        table.html(tableContent);
                    }
                });
            });

            // save btn-save-sub-row
            $("#btn-save-sub-row").on("click", function() {
                let $btn = $(this);
                $btn.prop("disabled", true);
                $btn.html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...'
                );

                let formData = new FormData($("#form-sub-row")[0]);
                let id = $("#id").val();
                // check if the row step_no and message is empty
                if ($("#step_no").val() == "" || $("#message").val() == "") {
                    swal.fire({
                        title: "Warning",
                        text: "Please fill all the fields",
                        icon: "warning",
                    });
                    $btn.prop("disabled", false);
                    $btn.html("Save");
                    return false;
                }

                // check if message not more than 85 characters
                if ($("#message").val().length > 75) {
                    swal.fire({
                        title: "Warning",
                        text: "Message must be less than 75 characters",
                        icon: "warning",
                    });
                    $btn.prop("disabled", false);
                    $btn.html("Save");
                    return false;
                }
                formData.append("tipe", "page-one");
                formData.append("id_master", id);
                formData.append("_token", "{{ csrf_token() }}");

                sendSubmitRequest("/template/print/update/sub-task", formData);

                $btn.prop("disabled", false);
                $btn.html("Save");

                // setTimeout(function() {
                //     window.location.reload();
                // }, 2000);
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

                    // check if message not more than 85 characters
                    if ($(tr).find("textarea[name^='message']").val().length > 85) {
                        swal.fire({
                            title: "Warning",
                            text: "Message must be less than 85 characters",
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

            // delete-sub-row-page-one 
            $("#table-sub-task").on("click", "#delete-sub-row-page-one", function() {
                let id = $(this).data("id");
                let formData = new FormData();
                formData.append("id", id);
                formData.append("_token", "{{ csrf_token() }}");

                sendSubmitRequest("/template/print/delete/sub-task", formData);
            });
        });

        $(document).ready(function() {
            // add-row-page-two
            $("#add-row-page-two").on("click", function() {
                let table = $("#table-page-two tbody");
                let rowCount = table.children().length;
                // jika sudah 15 row
                if (rowCount >= 15) {
                    swal.fire({
                        title: "Warning",
                        text: "Maximum row is 15",
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

                            <button class="btn btn-primary btn-sm" id="add-sub-row-page-two" data-id=${rowCount}>
                                <i class="fas fa-plus"></i>
                            </button>
                        </td>
                    </tr>
                `;
                table.append(newRow);
            });

            // add-sub-row-page-two
            $("#table-page-two").on("click", "#add-sub-row-page-two", function() {
                let id = $(this).data("id");
                let stepNo = $(`#step_no\\[${id}\\]`).val();
                let message = $(`#message\\[${id}\\]`).val();

                $("#modal-sub-row").modal("show");
                $("#modal-sub-row").find("#step_no").val(stepNo);
                $("#modal-sub-row").find("#message").val(message);
                $("#modal-sub-row").find("#id").val(id);

                // get sub task
                $.ajax({
                    url: "/template/print/get/sub-task",
                    type: "POST",
                    data: {
                        id: id,
                        _token: "{{ csrf_token() }}",
                    },
                    success: function(response) {
                        let table = $("#table-sub-task");
                        let tableContent = `
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Step</th>
                                        <th>Template</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                        `;

                        response.forEach((item, index) => {
                            tableContent += `
                                <tr>
                                    <td>${index + 1}</td>
                                    <td>${item.step_no}</td>
                                    <td>${item.message}</td>
                                    <td>
                                        <button class="btn btn-danger btn-sm" id="delete-sub-row-page-two" data-id="${item.id}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            `;
                        });

                        tableContent += `
                            </tbody>
                        </table>
                        `;

                        table.html(tableContent);
                    }
                });
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

                    // check if message not more than 85 characters
                    if ($(tr).find("textarea[name^='message']").val().length > 85) {
                        swal.fire({
                            title: "Warning",
                            text: "Message must be less than 85 characters",
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
