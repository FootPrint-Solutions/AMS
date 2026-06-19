@extends('template.master')

@section('content')
    <style>
        #table-table-commission_filter {
            margin-top: 10px !important;
        }

        .dataTables_wrapper {
            position: relative;
            clear: both;
            margin-top: 40px;
        }
    </style>

    {{-- Form --}}
    <div class="card">
        <div class="card-body">
            {{-- Title --}}
            <div class="card-title h5">
                @if (isset($data['profile']))
                    Edit Shop
                @else
                    Add New Shop
                @endif
            </div>
            <br>

            {{-- Form --}}
            <form id="distributor-shop-form">
                @csrf

                {{-- Name & Distributor --}}
                <div class="row">
                    {{-- Name --}}
                    <div class="col">
                        <div class="form-group local-forms">
                            <label for="name">Name <span class="login-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name"
                                placeholder="Enter shop name" required
                                @if (isset($data['profile'])) value="{{ $data['profile']['name'] }}" @endif>
                        </div>
                    </div>

                    {{-- Distributor --}}
                    <div class="col">
                        <div class="form-group local-forms">
                            <label for="distributor">Distributor <span class="login-danger">*</span></label>
                            <select class="form-control" id="distributor" name="distributor" required>
                                <option></option>
                                @foreach ($data['distributors'] as $distributor)
                                    <option value="{{ $distributor['id'] }}"
                                        @if (isset($data['profile']) && $data['profile']['distributor_id'] == $distributor['id']) selected @endif>{{ $distributor['name'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Address --}}
                <div class="row">
                    <div class="col">
                        <div class="form-group local-forms">
                            <label for="distributor-address">Address <span class="login-danger">*</span></label>
                            <input readonly type="text" class="form-control" id="AddressSearchColumn" name="address"
                                placeholder="Enter distributor address" required
                                @isset($data['profile']) value="{{ $data['profile']['address'] }}" @endisset>

                            <input type="hidden" id="Latitude" name="Latitude"
                                @if (isset($data['profile'])) value="{{ $data['profile']['latitude'] }}" @endif>
                            <input type="hidden" id="Longitude" name="Longitude"
                                @if (isset($data['profile'])) value="{{ $data['profile']['longitude'] }}" @endif>
                        </div>
                    </div>

                    {{-- Map Marker --}}
                    <div class="col-sm-auto">
                        <button type="button" class="btn btn-primary" id="btnAddress">
                            <i class="fa fa-map-marker"></i>
                        </button>
                    </div>
                </div>

                {{-- Contact Person, Contact and Email --}}
                <div class="row">
                    {{-- Contact Person --}}
                    <div class="col">
                        <div class="form-group local-forms">
                            <label for="contact-person">Contact Person <span class="login-danger">*</span></label>
                            <input type="text" class="form-control" id="contact-person" name="contactperson"
                                placeholder="Enter shop contact person name" required
                                @isset($data['profile']) value="{{ $data['profile']['contact_person'] }}" @endisset>
                        </div>
                    </div>

                    {{-- Contact --}}
                    <div class="col">
                        <div class="form-group local-forms">
                            <label for="contact">Contact <span class="login-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text border-end country-code">+62</span>
                                <input type="tel" pattern="[1-9][0-9]{7,}"
                                    title="At least 8 digits with no leading zero" class="form-control" id="contact"
                                    name="contact" placeholder="Enter shop contact" required
                                    @isset($data['profile']) value="{{ $data['profile'] ? $data['profile']['contact'] : '' }}" @endisset>
                            </div>
                        </div>
                    </div>

                    {{-- E-mail --}}
                    <div class="col">
                        <div class="form-group local-forms">
                            <label for="email">E-mail</label>
                            <input type="email" class="form-control" id="email" name="email"
                                placeholder="Enter shop e-mail"
                                @isset($data['profile']) value="{{ $data['profile'] ? $data['profile']['email'] : '' }}" @endisset>
                        </div>
                    </div>
                </div>


                <div class="row">

                    {{-- Technician Chart Of Account --}}
                    <div class="col">
                        <div class="form-group local-forms">
                            <label for="technicianAccount">Technician Account <span class="login-danger">*</span></label>
                            <select class="form-control" id="technicianAccount" name="technicianAccount" required>
                                <option>Select technician account</option>
                                @foreach ($data['chartOfAccounts'] as $account)
                                    <option value="{{ $account['id'] }}" @if (isset($data['profile']) &&
                                            $data['technicianAccount'] &&
                                            $data['technicianAccount']->chart_of_account_id == $account['id']
                                    ) selected @endif>
                                        {{ $account['number'] }} - {{ $account['name'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Technician Commission Amoutn --}}
                    <div class="col">
                        <div class="form-group local-forms">
                            <label for="technicianCommission">Technician Commission Amount</label>
                            <input type="number" min="0" class="form-control" id="technicianCommission"
                                name="technicianCommission" placeholder="Enter technician commission amount"
                                @isset($data['profile']) value="{{ $data['technicianAccount'] ? $data['technicianAccount']->commission : '' }}" @endisset>
                        </div>
                    </div>
                </div>

                <div class="row">
                    {{-- PIC Chart Of Account --}}
                    <div class="col">
                        <div class="form-group local-forms">
                            <label for="picAccount">PIC Account <span class="login-danger">*</span></label>
                            <select class="form-control" id="picAccount" name="picAccount" required>
                                <option>Select PIC account</option>
                                @foreach ($data['chartOfAccounts'] as $account)
                                    <option value="{{ $account['id'] }}"
                                        @if (isset($data['profile']) && $data['picAccount'] && $data['picAccount']->chart_of_account_id == $account['id']) selected @endif>
                                        {{ $account['number'] }} - {{ $account['name'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- PIC Commission Amount --}}
                    <div class="col">
                        <div class="form-group local-forms">
                            <label for="picCommission">PIC Commission Amount</label>
                            <input type="number" min="0" class="form-control" id="picCommission"
                                name="picCommission" placeholder="Enter PIC commission amount"
                                @isset($data['profile']) value="{{ $data['picAccount'] ? $data['picAccount']->commission : '' }}" @endisset>
                        </div>
                    </div>
                </div>

                <div class="row">
                    {{-- Pit Stop Chart Of Account --}}
                    <div class="col">
                        <div class="form-group local-forms">
                            <label for="pitStopAccount">Pit Stop Account <span class="login-danger">*</span></label>
                            <select class="form-control" id="pitStopAccount" name="pit_stopAccount" required>
                                <option>Select pit stop account</option>
                                @foreach ($data['chartOfAccounts'] as $account)
                                    <option value="{{ $account['id'] }}"
                                        @if (isset($data['profile']) &&
                                                $data['pitStopAccount'] &&
                                                $data['pitStopAccount']->chart_of_account_id == $account['id']
                                        ) selected @endif>
                                        {{ $account['number'] }} - {{ $account['name'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Pit Stop Commission Amount --}}
                    <div class="col">
                        <div class="form-group local-forms">
                            <label for="pit_stopCommission">Pit Stop Commission Amount</label>
                            <input type="number" min="0" class="form-control" id="pit_stopCommission"
                                name="pit_stopCommission" placeholder="Enter pit stop commission amount"
                                @isset($data['profile']) value="{{ $data['pitStopAccount'] ? $data['pitStopAccount']->commission : '' }}" @endisset>
                        </div>
                    </div>
                </div>

                {{-- Note --}}
                <div class="form-group local-forms">
                    <label for="note">Note</label>
                    <textarea type="text" class="form-control" id="note" name="note"
                        placeholder="Enter some notes regarding the shop">
@if (isset($data['profile']) && !empty($data['profile']['note']))
{{ $data['profile']['note'] }}
@endif
</textarea>
                </div>

                {{-- Hidden Inputs --}}
                @isset($data['profile'])
                    <input type="hidden" id="id" name="id" value="{{ $data['profile']['id'] }}">
                @endisset

                {{-- Buttons --}}
                <div class="d-flex flex-row-reverse">
                    {{-- Create Button --}}
                    <button type="submit" class="btn btn-success mx-1" id="btn-save"
                        @if (isset($data['profile'])) value="update">
                    Update
                    @else
                    value="create">
                    Create @endif
                        Shop </button>

                        {{-- Cancel Button --}}
                        <button type="reset" class="btn btn-danger mx-1" id="btn-cancel">Cancel</button>
                </div>
            </form>

            <br><br>

            {{-- Battery Import Section --}}
            @if (isset($data['profile']))
                <div class="row text-end mt-3">
                    <div class="col">
                        {{-- Download Template Battery Import --}}
                        <a href="/distributor/shop/{{ $data['profile']['id'] }}/battery-import-template"
                            class="btn btn-outline-success btn-sm me-1">
                            <i class="fa fa-download"></i> Download Battery Import Template
                        </a>
                    </div>
                    <div class="col">
                        {{-- Import Battery --}}
                        <form id="battery-import-form" class="d-inline" enctype="multipart/form-data">
                            @csrf
                            <input type="file" id="batteryImportFile" name="batteryImportFile" accept=".xlsx">
                            <button type="button" class="btn btn-outline-primary btn-sm" id="btnBatteryImport">
                                <i class="fa fa-upload"></i> Import Battery
                            </button>
                        </form>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col">
                        <div class="table-responsive">
                            <table class="table table-striped custom-table" id="table-commission">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Battery Name</th>
                                        <th>Commission Type</th>
                                        <th>Commission Amount</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Address Modal --}}
    @include('maps.AddressModal')

    <script>
        let indexUrl = "/distributor/shop";

        $(document).ready(function() {
            $('#distributor').select2({
                placeholder: "Enter distributor brand"
            });
            $('#technicianAccount').select2({
                placeholder: "Select technician account"
            });
            $('#picAccount').select2({
                placeholder: "Select PIC account"
            });
            $('#pitStopAccount').select2({
                placeholder: "Select pit stop account"
            });

            $("#distributor-shop-form").on("submit", function(event) {
                event.preventDefault();
                if ($("#AddressSearchColumn").val() == "") {
                    swal.fire("Error!", "Please Fill The Address Column", "error");
                    $("#AddressSearchColumn").focus();
                    return;
                }
                let mode = $("#btn-save").attr("value"); // update || create
                let url = (mode == "update") ? "/distributor/shop/update" : "/distributor/shop/store";

                // Obtain submitted form data.
                let formData = new FormData($(this)[0]);

                // Send submit POST request via AJAX.
                sendSubmitRequest(url, formData, function() {
                    // Redirect to index page.
                    goToPage(indexUrl);
                });
            });

            $("#distributor-shop-form").on("reset", function() {
                goToPage(indexUrl);
            });

            // btnBatteryImport ajax submit
            $("#btnBatteryImport").on("click", function(event) {
                event.preventDefault();
                let formData = new FormData($("#battery-import-form")[0]);
                let shopId = $("#id").val();
                let url = "/distributor/shop/" + shopId + "/battery-import";

                // Add CSRF token
                formData.append("_token", "{{ csrf_token() }}");
                formData.append("batteryImportFile", $("#batteryImportFile")[0].files[0]);
                formData.append("shopId", shopId);

                $.ajax({
                    url: url,
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            swal.fire("Success!", response.message, "success");

                            // Reload the commission DataTable
                            if (tableCommission) {
                                tableCommission.ajax.reload();
                            }
                        } else {
                            swal.fire("Error!", response.message, "error");
                        }
                    },
                    error: function(xhr, status, error) {
                        swal.fire("Error!",
                            "An error occurred while importing the battery data.", "error");
                    }
                });
            });

            // Initialize Commission DataTable if the table exists (only in Edit mode)
            let tableCommission;
            if ($("#table-commission").length > 0) {
                let shopId = $("#id").val();
                tableCommission = $("#table-commission").DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "/distributor/shop/commission/show",
                        type: "POST",
                        data: function(d) {
                            d._token = "{{ csrf_token() }}";
                            d.shop_id = shopId;
                        }
                    },
                    columns: [{
                            data: 0,
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 1
                        },
                        {
                            data: 2
                        },
                        {
                            data: 3,
                            className: 'text-end'
                        },
                        {
                            data: 4,
                            orderable: false,
                            searchable: false,
                            render: function(data, type, row) {
                                return `
                                    <button type="button" class="btn btn-sm btn-outline-primary btn-edit-commission" data-id="${data}" data-name="${row[1]}" data-type="${row[2]}" data-amount="${row[3]}">
                                        <i class="fa fa-edit"></i> Edit
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger btn-delete-commission" data-id="${data}">
                                        <i class="fa fa-trash"></i> Delete
                                    </button>
                                `;
                            }
                        }
                    ]
                });

                // Edit commission handler
                $("#table-commission").on("click", ".btn-edit-commission", function() {
                    let id = $(this).data("id");
                    let name = $(this).data("name");
                    let type = $(this).data("type");
                    let amount = $(this).data("amount").toString().replace(/\./g, ""); // strip dots

                    swal.fire({
                        title: 'Edit Commission',
                        text: `Enter new commission for ${name} (${type}):`,
                        input: 'number',
                        inputValue: amount,
                        showCancelButton: true,
                        confirmButtonText: 'Update',
                        preConfirm: (newAmount) => {
                            if (!newAmount || newAmount < 0) {
                                Swal.showValidationMessage('Please enter a valid amount');
                            }
                            return newAmount;
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: "/distributor/shop/commission/update",
                                type: "POST",
                                data: {
                                    _token: "{{ csrf_token() }}",
                                    id: id,
                                    commission: result.value
                                },
                                success: function(response) {
                                    if (response.success) {
                                        swal.fire("Success!", response.message,
                                            "success");
                                        tableCommission.ajax.reload();
                                    } else {
                                        swal.fire("Error!", response.message, "error");
                                    }
                                },
                                error: function() {
                                    swal.fire("Error!", "Failed to update commission.",
                                        "error");
                                }
                            });
                        }
                    });
                });

                // Delete commission handler
                $("#table-commission").on("click", ".btn-delete-commission", function() {
                    let id = $(this).data("id");

                    swal.fire({
                        title: 'Are you sure?',
                        text: "You won't be able to revert this!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: "/distributor/shop/commission/destroy",
                                type: "POST",
                                data: {
                                    _token: "{{ csrf_token() }}",
                                    id: id
                                },
                                success: function(response) {
                                    if (response.success) {
                                        swal.fire("Deleted!", response.message,
                                            "success");
                                        tableCommission.ajax.reload();
                                    } else {
                                        swal.fire("Error!", response.message, "error");
                                    }
                                },
                                error: function() {
                                    swal.fire("Error!", "Failed to delete commission.",
                                        "error");
                                }
                            });
                        }
                    });
                });
            }
        });
    </script>
@endsection
