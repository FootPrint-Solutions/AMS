@extends('template.master')

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title h5">
                @if (isset($data['profile']))
                    Edit Expense
                @else
                    Add New Expense
                @endif
            </div>
            <br>
            <form id="expense-form">
                @csrf

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group local-forms">
                            <label for="name">Expense Name <span class="login-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name"
                                placeholder="Enter expense name" required
                                @if (isset($data['profile'])) value="{{ $data['profile']['name'] }}" @endif>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group local-forms">
                            <label for="description">Description</label>
                            <input type="text" class="form-control" id="description" name="description"
                                placeholder="Enter description"
                                @if (isset($data['profile'])) value="{{ $data['profile']['description'] }}" @endif>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="form-group local-forms">
                            <label for="is_active">Status <span class="login-danger">*</span></label>
                            <select class="form-control" id="is_active" name="is_active" required>
                                <option value="1" @if (isset($data['profile']) && $data['profile']['is_active']) selected @endif>Active</option>
                                <option value="0" @if (isset($data['profile']) && !$data['profile']['is_active']) selected @endif>Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>

                @isset($data['profile'])
                    <input type="hidden" id="id" name="id" value="{{ $data['profile']['id'] }}">
                @endisset

                <div class="d-flex flex-row-reverse mt-4">
                    <button type="submit" class="btn btn-success mx-1" id="btn-save"
                        @if (isset($data['profile'])) value="update">Update Expense
                    @else value="create">Create Expense @endif
                        </button>
                        <button type="reset" type="button" class="btn btn-danger mx-1" id="btn-cancel">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let indexUrl = "/expense";

        $(document).ready(function() {
            $("#expense-form").on("submit", function(event) {
                event.preventDefault();

                let mode = $("#btn-save").attr("value");
                let url = (mode == "update") ? "/expense/update" : "/expense/store";

                let formData = new FormData($(this)[0]);

                Swal.fire({
                    title: 'Processing...',
                    text: 'Please wait while we save your data.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                sendSubmitRequest(url, formData, function() {
                    goToPage(indexUrl);
                });
            });

            $("#expense-form").on("reset", function() {
                goToPage(indexUrl);
            });
        });
    </script>
@endsection
