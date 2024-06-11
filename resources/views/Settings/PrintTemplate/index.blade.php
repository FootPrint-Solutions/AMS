@extends('template.master')
{{-- @dd($data) --}}

@section('content')
    <div class="card">
        <div class="card-body">
            {{-- Title --}}
            <div class="card-title h5">
                Print Templates
            </div>
            <br>


            <table class="table table-bordered" id="table-work-order">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Step</th>
                        <th>Template</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data['templates'] as $key => $item)
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
                                <button class="btn btn-danger btn-sm" id="delete-row-work-order">
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
                    <button class="btn btn-primary btn-sm" id="add-row-work-order">Add Row</button>
                </div>
                <div class="col text-end">
                    <button class="btn btn-success btn-sm" id="btn-save-work-order">Save Print Templates</button>
                </div>
            </div>

        </div>
    </div>

    <script>
        $(document).ready(function() {
            // add-row-work-order
            $("#add-row-work-order").on("click", function() {
                let table = $("#table-work-order tbody");
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
                            <button class="btn btn-danger btn-sm" id="delete-row-work-order">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
                table.append(newRow);
            });

            // delete-row-work-order
            $("#table-work-order").on("click", "#delete-row-work-order", function() {
                $(this).closest("tr").remove();
            });

            // btn-save-work-order
            $("#btn-save-work-order").on("click", function() {
                let $btn = $(this);
                $btn.prop("disabled", true);
                $btn.html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...'
                );

                // Obtain submitted form data.
                let formData = new FormData();
                $("#table-work-order tbody tr").each(function(index, tr) {
                    let stepNo = $(tr).find("input[name^='step_no']").val();
                    let message = $(tr).find("textarea[name^='message']").val();

                    formData.append(`step_no[${index}]`, stepNo);
                    formData.append(`message[${index}]`, message);
                });
                formData.append("_token", "{{ csrf_token() }}");

                sendSubmitRequest("/template/print/update", formData);

                $btn.prop("disabled", false);
                $btn.html("Save Print Templates");
            });


            $("#message-template-form").on("submit", function(event) {
                event.preventDefault();
                $("#btn-save").prop("disabled", true);
                $("#btn-save").html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...'
                );

                // Obtain submitted form data.
                let formData = new FormData($(this)[0]);

                // Send submit POST request via AJAX.
                sendSubmitRequest("/template/message/update", formData);


                $("#btn-save").prop("disabled", false);
                $("#btn-save").html("Save Message Templates");
            });

            $("#message-template-form").on("reset", function() {
                goToPage("/template/message");
            });
        });
    </script>
@endsection
