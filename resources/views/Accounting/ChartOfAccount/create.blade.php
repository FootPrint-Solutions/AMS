@extends('template.master')

@section('content')
    {{-- Form --}}
    <div class="card">
        <div class="card-body">
            {{-- Title --}}
            <div class="card-title h5">
                @isset($data['profile'])
                    Edit
                @else
                    Add New
                @endisset
                Chart of Account
            </div>
            <br>

            {{-- Form --}}
            <form id="chart-of-account-form">
                @csrf

                {{-- Account Number --}}
                <div class="form-group local-forms">
                    <label for="number">Account Number <span class="login-danger">*</span></label>
                    <input type="text" class="form-control" id="number" name="number" placeholder="Enter account number"
                        required
                        @isset($data['profile'])
                            value="{{ $data['profile']['number'] }}"
                        @endisset>
                </div>

                {{-- Account Name --}}
                <div class="form-group local-forms">
                    <label for="name">Account Name <span class="login-danger">*</span></label>
                    <input type="text" class="form-control" id="name" name="name"
                        placeholder="Enter account name" required
                        @isset($data['profile'])
                            value="{{ $data['profile']['name'] }}"
                        @endisset>
                </div>

                {{-- Account Group --}}
                <div class="form-group local-forms">
                    <label for="chart_of_account_group_id">Account Group <span class="login-danger">*</span></label>
                    <select class="form-control" id="chart_of_account_group_id" name="chart_of_account_group_id" required>
                        <option value="">Select Account Group</option>
                        <option value="1"
                            @isset($data['profile']) @if ($data['profile']['chart_of_account_group_id'] == '1') selected @endif @endisset>
                            Aktiva</option>
                        <option value="2"
                            @isset($data['profile']) @if ($data['profile']['chart_of_account_group_id'] == '2') selected @endif @endisset>
                            Pasiva</option>
                        <option value="3"
                            @isset($data['profile']) @if ($data['profile']['chart_of_account_group_id'] == '3') selected @endif @endisset>
                            Laba Rugi</option>
                    </select>
                </div>

                {{-- Active Status --}}
                <div class="form-group local-forms">
                    <label for="is_active">Active Status <span class="login-danger">*</span></label>
                    <select class="form-control" id="is_active" name="is_active" required>
                        <option value="">Select Status</option>
                        <option value="1"
                            @isset($data['profile']) @if ($data['profile']['is_active'] == 1) selected @endif @endisset>
                            Active</option>
                        <option value="0"
                            @isset($data['profile']) @if ($data['profile']['is_active'] == 0) selected @endif @endisset>
                            Inactive</option>
                    </select>
                </div>

                {{-- Hidden Inputs --}}
                @isset($data['profile'])
                    <input type="hidden" name="id" value="{{ $data['profile']['id'] }}">
                @endisset

                {{-- Buttons --}}
                <div class="d-flex flex-row-reverse">
                    {{-- Save Button --}}
                    <button type="submit" class="btn btn-success mx-1" id="btn-save"
                        @isset($data['profile'])
                            value="update">Update Chart of Account
                        @else
                            value="create">Create Chart of Account
                        @endisset
                        </button>
                        {{-- Cancel Button --}}
                        <button type="reset" class="btn btn-danger mx-1" id="btn-cancel">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $("#chart-of-account-form").on("submit", function(event) {
                event.preventDefault();

                // Disable button and show loading
                $("#btn-save").attr("disabled", true);
                $("#btn-save").html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...'
                );

                // Get current mode (Update or Create)
                let mode = $("#btn-save").attr("value");
                let url = "/chart-of-account/store";
                if (mode == "update") {
                    url = "/chart-of-account/update";
                }

                // Get form data
                let formData = new FormData($(this)[0]);

                // Send AJAX request
                sendSubmitRequest(url, formData, function() {
                    goToPage("/chart-of-account");
                });
            });

            $("#chart-of-account-form").on("reset", function() {
                goToPage("/chart-of-account");
            });
        });
    </script>
@endsection
