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

                {{-- Account Group --}}
                <div class="form-group local-forms">
                    <div class="d-flex justify-content-between align-items-center">
                        <label for="chart_of_account_group_id">Account Group <span class="login-danger">*</span></label>
                        <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal"
                            data-bs-target="#account-group-modal">
                            Manage Group
                        </button>
                    </div>
                    <select class="form-control" id="chart_of_account_group_id" name="chart_of_account_group_id" required>
                        <option value="">Select Account Group</option>
                        @if (isset($data['groups']) && count($data['groups']) > 0)
                            @foreach ($data['groups'] as $group)
                                <option value="{{ $group['id'] }}" data-number="{{ $group['number'] ?? '-' }}"
                                    @isset($data['profile'])
                                        @if ($data['profile']['chart_of_account_group_id'] == $group['id']) selected @endif
                                    @endisset>
                                    ({{ $group['number'] ?? '-' }})
                                    {{ $group['name'] }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>


                {{-- Account Number --}}
                <div class="form-group local-forms">
                    <label for="number">Account Number <span class="login-danger">*</span></label>
                    <input type="text" class="form-control" id="number" name="number"
                        placeholder="Enter account number" required
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

    {{-- Account Group Modal --}}
    <div id="account-group-modal" class="modal fade" tabindex="-1" role="dialog"
        aria-labelledby="account-group-modal-label" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="account-group-modal-label">Manage Account Group</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="account-group-form" class="mb-3">
                        @csrf
                        <input type="hidden" id="group_id" name="id">

                        <div class="row g-2 align-items-end">
                            {{-- Group Number --}}
                            <div class="col-md-4">
                                <label for="group_number" class="form-label">
                                    Group Number <span class="login-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="group_number" name="number"
                                    placeholder="Enter account group number" autocomplete="off" required>
                            </div>

                            {{-- Group Name --}}
                            <div class="col-md-8">
                                <label for="group_name" class="form-label">
                                    Group Name <span class="login-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="group_name" name="name"
                                    placeholder="Enter account group name" autocomplete="off" required>
                            </div>

                            <div class="col-12 d-flex gap-2 mt-2">
                                <button type="submit" class="btn btn-success" id="btn-group-save">
                                    Add Group
                                </button>
                                <button type="button" class="btn btn-secondary" id="btn-group-cancel">
                                    Cancel
                                </button>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th style="width: 8%">#</th>
                                    <th>Group Number</th>
                                    <th>Group Name</th>
                                    <th style="width: 25%">Action</th>
                                </tr>
                            </thead>
                            <tbody id="account-group-table-body">
                                <tr>
                                    <td colspan="4" class="text-center text-muted">Loading account groups...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            const csrfToken = "{{ csrf_token() }}";

            function parseJsonResponse(response) {
                if (typeof response === "string") {
                    try {
                        return JSON.parse(response);
                    } catch (error) {
                        return {
                            status: false,
                            data: []
                        };
                    }
                }

                return response;
            }

            function resetGroupForm() {
                $("#group_id").val("");
                $("#group_name").val("");
                $("#btn-group-save").text("Add Group");
            }

            function escapeHtml(value) {
                return $("<div>").text(value || "").html();
            }

            function setGroupOptions(groups, selectedId = null) {
                const previousSelected = $("#chart_of_account_group_id").val();
                const targetSelected = selectedId !== null ? String(selectedId) : String(previousSelected || "");

                let optionsHtml = '<option value="">Select Account Group</option>';
                groups.forEach(function(group) {
                    optionsHtml += `<option value="${group.id}">${escapeHtml(group.name)}</option>`;
                });

                $("#chart_of_account_group_id").html(optionsHtml);

                if (targetSelected !== "") {
                    $("#chart_of_account_group_id").val(targetSelected);
                }
            }

            function renderGroupTable(groups) {
                if (!groups || groups.length === 0) {
                    $("#account-group-table-body").html(
                        '<tr><td colspan="4" class="text-center text-muted">No account group data.</td></tr>'
                    );
                    return;
                }

                let rowsHtml = "";
                groups.forEach(function(group, index) {
                    const safeName = escapeHtml(group.name);

                    rowsHtml += `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${group.number ? escapeHtml(group.number) : '-'}</td>
                            <td>${safeName}</td>
                            <td>
                                <button type="button" class="btn btn-warning btn-sm btn-group-edit" data-id="${group.id}"
                                    data-name="${safeName}" data-number="${group.number ? escapeHtml(group.number) : ''}">Edit</button>
                                <button type="button" class="btn btn-danger btn-sm btn-group-delete" data-id="${group.id}">
                                    Delete
                                </button>
                            </td>
                        </tr>
                    `;
                });

                $("#account-group-table-body").html(rowsHtml);
            }

            function loadGroups(selectedId = null) {
                $.ajax({
                    url: "/chart-of-account/group/list",
                    method: "POST",
                    data: {
                        _token: csrfToken
                    },
                    success: function(response) {
                        const responseData = parseJsonResponse(response);
                        const groups = responseData.data || [];

                        renderGroupTable(groups);
                        setGroupOptions(groups, selectedId);
                    }
                });
            }

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

            $("#account-group-form").on("submit", function(event) {
                event.preventDefault();

                const selectedGroupId = $("#chart_of_account_group_id").val();
                const groupId = $("#group_id").val();
                const url = groupId ? "/chart-of-account/group/update" : "/chart-of-account/group/store";
                const formData = new FormData(this);

                sendSubmitRequest(url, formData, function() {
                    loadGroups(selectedGroupId);
                    resetGroupForm();
                });
            });

            $("#btn-group-cancel").on("click", function() {
                resetGroupForm();
            });

            $("#account-group-modal").on("shown.bs.modal", function() {
                loadGroups();
            });

            $("#account-group-table-body").on("click", ".btn-group-edit", function() {
                $("#group_id").val($(this).attr("data-id"));
                $("#group_name").val($(this).attr("data-name"));
                $("#btn-group-save").text("Update Group");
            });

            $("#account-group-table-body").on("click", ".btn-group-delete", function() {
                const groupId = $(this).attr("data-id");
                const selectedGroupId = $("#chart_of_account_group_id").val();

                Swal.fire({
                    title: "Are you sure?",
                    text: "Deleted account group cannot be restored.",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Yes, delete it!",
                    cancelButtonText: "Cancel",
                }).then((result) => {
                    if (!result.isConfirmed) {
                        return;
                    }

                    const formData = new FormData();
                    formData.append("_token", csrfToken);
                    formData.append("id", groupId);

                    sendSubmitRequest("/chart-of-account/group/destroy", formData, function() {
                        const nextSelected = selectedGroupId == groupId ? null :
                            selectedGroupId;
                        loadGroups(nextSelected);
                        resetGroupForm();
                    });
                });
            });

            $("#chart_of_account_group_id").on("change", function() {
                const groupId = $(this).val();
                if (!groupId) {
                    $("#number").val("");
                    return;
                }

                $.ajax({
                    url: "/chart-of-account/group/next-number",
                    method: "POST",
                    data: {
                        _token: csrfToken,
                        group_id: groupId
                    },
                    success: function(response) {
                        const responseData = parseJsonResponse(response);
                        if (responseData.status == "success") {
                            $("#number").val(responseData.data);
                        } else {
                            $("#number").val("");
                        }
                    }
                });
            });
        });
    </script>
@endsection
