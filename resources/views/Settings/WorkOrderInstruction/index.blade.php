@extends('template.master')

@section('content')
    <style>
        /* sticky card for Work Order Instruction Template Detail */
        .card-sticky {
            position: -webkit-sticky;
            position: sticky;
            top: 65px;
            z-index: 1000;
        }
    </style>
    <div class="card shadow">
        <div class="card-header">
            {{-- Title --}}
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title text-center">Work Order Instruction Template Configuration</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- bagi menjadi dua colom --}}
    <div class="row">
        <div class="col-md-9">
            <div class="card shadow" id="step-1">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="page-title
                                text-center">Step 1
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mt-2">
                        <div class="col-md-2 d-flex align-items-center">
                            Deskripsi
                        </div>
                        <div class="col-md">
                            <textarea class="form-control" id="input-work-order-instruction-description-step-1"
                                name="input-work-order-instruction-description[]"></textarea>
                        </div>

                    </div>
                    <div class="input-div-step-1"></div>
                    <div class="row mt-2">
                        <div class="col-md-2 d-flex align-items-center">
                            <button class="btn btn-primary btn-sm btn-add-input" id="btn-add-input" data-step="1"
                                onclick="addInputModal()"><i class="fas fa-plus"></i>
                                Add Input</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="step-div"></div>
        </div>
        <div class="col-md-3">
            <div class="card shadow card-sticky">
                <div class="card-header">
                    {{-- Title --}}
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="page-title text-center">Work Order Instruction Template Detail</h3>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    {{-- button add new step --}}
                    <button class="btn btn-primary btn-sm" data-step="1" onclick="addStep()">Add New Step</button>

                </div>
            </div>
        </div>
    </div>

    {{-- modalInputOption --}}
    <div class="modal fade" id="modalInputOption" tabindex="-1" aria-labelledby="modalInputOptionLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalInputOptionLabel">Add Input Option</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-2 d-flex align-items-center">
                            Input Type
                        </div>
                        <div class="col-md">
                            <select class="form-select" id="input-work-order-instruction-input-input-type"
                                name="input-work-order-instruction-input-input-type" onchange="changeInputType()">
                                <option value="text">Text</option>
                                <option value="number">Number</option>
                                <option value="date">Date</option>
                                <option value="checkbox">Checkbox</option>
                                <option value="radio">Radio</option>
                                <option value="select">Select</option>
                                <option value="image">Image</option>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-center d-none"
                            id="input-work-order-instruction-input-group-label">
                            Group
                        </div>
                        <div class="col-md d-none" id="input-work-order-instruction-input-group">
                            <select class="form-select" id="input-work-order-instruction-input-group-select"
                                name="input-work-order-instruction-input-group-select">
                                @for ($i = 1; $i <= 10; $i++)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-center">
                            <button class="btn btn-primary btn-sm" onclick="addOption()">Add Option</button>
                        </div>
                    </div>

                    <div class="form-input-option mt-3"></div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="addInput()">Add</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let step = 1;
        let input = 1;
        let stepDiv = $('.step-div');

        function addStep() {
            step++;
            stepDiv.append(`
                <div class="card shadow" id="step-${step}">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col">
                                <h3 class="page-title text-center">Step ${step}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mt-2">
                            <div class="col-md-2 d-flex align-items-center">
                                Deskripsi
                            </div>
                            <div class="col-md">
                                <textarea class="form-control" id="input-work-order-instruction-description-step-${step}"
                                    name="input-work-order-instruction-description[]"></textarea>
                            </div>
                        </div>
                        <div class="input-div-step-${step}"></div>
                        <div class="row mt-2">
                            <div class="col-md-2 d-flex align-items-center">
                                <button class="btn btn-primary btn-sm btn-add-input" id="btn-add-input" onclick="addInputModal()" data-step="${step}"><i
                                        class="fas fa-plus"></i>
                                    Add Input</button>
                            </div>
                        </div>
                    </div>
                </div>
            `);
        }

        function addInputModal() {
            $("#modalInputOption").modal('show');
        }

        function addOption() {
            const inputType = $('#input-work-order-instruction-input-input-type').val();
            const inputOption = $('.form-input-option');
            const inputCount = inputOption.find('table tbody tr').length + 1;
            const inputGroup = $('#input-work-order-instruction-input-group-select').val();

            // Check if table exists, if not, create one
            if (inputOption.find('table').length === 0) {
                inputOption.append(`
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Question</th>
                        <th>Input Type</th>
                        <th>Group</th>
                        <th>Required</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        `);
            }

            // Add a new option row
            const optionRow = `
        <tr>
            <td>${inputCount}</td>
            <td><input type="text" class="form-control" name="input-work-order-instruction-input-question[]"></td>
            <td>${inputType}</td>
            <td>${inputGroup}</td>
            <td><input type="checkbox" name="input-work-order-instruction-input-required[]"></td>
            <td><button class="btn btn-danger btn-sm" onclick="deleteOption(this)">Delete</button></td>
        </tr>
    `;

            inputOption.find('table tbody').append(optionRow);
        }

        function deleteOption(button) {
            $(button).closest('tr').remove();
            updateRowNumbers();
        }

        function updateRowNumbers() {
            $('.form-input-option table tbody tr').each(function(index) {
                $(this).find('td:first').text(index + 1);
            });
        }

        function changeInputType() {
            const inputType = $('#input-work-order-instruction-input-input-type').val();
            const inputGroupLabel = $('#input-work-order-instruction-input-group-label');
            const inputGroup = $('#input-work-order-instruction-input-group');

            if (inputType === 'radio' || inputType === 'select') {
                inputGroupLabel.removeClass('d-none');
                inputGroup.removeClass('d-none');
            } else {
                inputGroupLabel.addClass('d-none');
                inputGroup.addClass('d-none');
            }
        }
    </script>
@endsection
