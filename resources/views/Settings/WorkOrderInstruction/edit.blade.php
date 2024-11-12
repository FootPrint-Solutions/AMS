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
                    <h3 class="page-title text-center">Edit Work Order Instruction Template</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- bagi menjadi dua colom --}}
    <form action="{{ route('wo-instruction-template.edit') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-9">
                @foreach ($data as $step)
                    <input type="hidden" name="input-id[]" value="{{ $step->id }}" id="input-id-{{ $loop->index + 1 }}">
                    <div class="card shadow" id="step-{{ $loop->index + 1 }}">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h3 class="page-title text-center">Step {{ $loop->index + 1 }}</h3>
                                </div>
                                <div class="col-auto">
                                    <button type="button" class="btn btn-danger btn-sm"
                                        onclick="deleteStep({{ $loop->index + 1 }})"><i class="fas fa-trash"></i></button>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-2 d-flex align-items-center">
                                    Title
                                </div>
                                <div class="col-md">
                                    <input type="text" class="form-control"
                                        id="input-work-order-instruction-title-step-{{ $loop->index + 1 }}"
                                        name="input-work-order-instruction-title[]" value="{{ $step->name }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row mt-2">
                                <div class="col-md-2 d-flex align-items-center">
                                    Description
                                </div>
                                <div class="col-md">
                                    <textarea class="form-control" id="input-work-order-instruction-description-step-{{ $loop->index + 1 }}"
                                        name="input-work-order-instruction-description[]" required>{{ $step->description }}</textarea>
                                </div>
                            </div>
                            <div class="input-div-step-{{ $loop->index + 1 }} mt-3">
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
                                    <tbody>
                                        @foreach ($step->details as $input)
                                            <tr>
                                                <td>{{ $loop->index + 1 }}
                                                    <input type="hidden" name="input-step[]"
                                                        value="{{ $loop->index + 1 }}">
                                                    <input type="hidden" name="input-count[]"
                                                        value="{{ $loop->index + 1 }}">
                                                </td>
                                                <td>{{ $input->instruction }}
                                                    <input type="hidden" name="input-question[]"
                                                        value="{{ $input->instruction }}">
                                                </td>
                                                <td>{{ $input->type }}
                                                    <input type="hidden" name="input-type[]" value="{{ $input->type }}">
                                                </td>
                                                <td>{{ $input->group }}
                                                    <input type="hidden" name="input-group[]" value="{{ $input->group }}">
                                                </td>
                                                <td>{{ $input->is_required ? 'Yes' : 'No' }}
                                                    <input type="hidden" name="input-required[]"
                                                        value="{{ $input->is_required }}">
                                                </td>
                                                <td><button class="btn btn-danger btn-sm"
                                                        onclick="deleteOption(this)">Delete</button></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-2 d-flex align-items-center">
                                    <button type="button" class="btn btn-primary btn-sm btn-add-input" id="btn-add-input"
                                        data-step="{{ $loop->index + 1 }}" onclick="addInputModal(this)"><i
                                            class="fas fa-plus"></i>
                                        Add Input</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                <div class="step-div mt-3"></div>
            </div>
            <div class="col-md-3">
                <div class="card shadow card-sticky">
                    <div class="card-header bg-primary text-light">
                        {{-- Title --}}
                        <div class="row align-items-center">
                            <div class="col">
                                <h3 class="page-title text-center text-light">Work Order Instruction Template Detail</h3>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        {{-- button add new step --}}
                        <button type="button" class="btn btn-primary btn-lg w-100 mb-2" data-step="{{ $data->count() }}"
                            onclick="addStep()">
                            <i class="fas fa-plus"></i> Add New Step
                        </button>
                        {{-- button save all data --}}
                        <button id="save-button" class="btn btn-success btn-lg w-100 mt-3" type="button">
                            <i class="fas fa-save"></i> Save
                        </button>

                        <div class="mt-3">
                            <h5 class="text-primary">Variable Data</h5>
                            <p class="text-muted">Variable data is only available for the description field</p>
                            <div class="input-group mb-2">
                                <input type="text" value="<ADDRESSCUSTOMER>" readonly class="form-control">
                                <button class="btn btn-outline-secondary" type="button"
                                    onclick="copyToClipboard('<ADDRESSCUSTOMER>')">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                            <div class="input-group mb-2">
                                <input type="text" value="<NAMECUSTOMER>" readonly class="form-control">
                                <button class="btn btn-outline-secondary" type="button"
                                    onclick="copyToClipboard('<NAMECUSTOMER>')">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                            <div class="input-group mb-2">
                                <input type="text" value="<PHONECUSTOMER>" readonly class="form-control">
                                <button class="btn btn-outline-secondary" type="button"
                                    onclick="copyToClipboard('<PHONECUSTOMER>')">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                            <div class="input-group mb-2">
                                <input type="text" value="<EMAILCUSTOMER>" readonly class="form-control">
                                <button class="btn btn-outline-secondary" type="button"
                                    onclick="copyToClipboard('<EMAILCUSTOMER>')">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                            <div class="input-group mb-2">
                                <input type="text" value="<VEHICLECUSTOMER>" readonly class="form-control">
                                <button class="btn btn-outline-secondary" type="button"
                                    onclick="copyToClipboard('<VEHICLECUSTOMER>')">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                            <div class="input-group mb-2">
                                <input type="text" value="<BATTERYCUSTOMER>" readonly class="form-control">
                                <button class="btn btn-outline-secondary" type="button"
                                    onclick="copyToClipboard('<BATTERYCUSTOMER>')">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <input type="hidden" id="last-step" value="{{ $data->count() }}">
    </form>

    {{-- modalInputOption --}}
    <div class="modal fade" id="modalInputOption" tabindex="-1" aria-labelledby="modalInputOptionLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalInputOptionLabel">Add Input Option</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="input-work-order-instruction-input-step-modal-hidden"
                        name="input-work-order-instruction-input-step-modal-hidden" value="1">
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
                                {{-- <option value="radio">Radio</option>
                            <option value="select">Select</option> --}}
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
                            <button type="button" class="btn btn-primary btn-sm" onclick="addOption()">Add
                                Option</button>
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

    <script src="{{ asset('plugins/tinymce/js/tinymce/tinymce.min.js') }}"></script>
    <script>
        let step = @json($data->count());
        let stepDiv = $('.step-div');

        function addStep() {
            let lastStep = parseInt($('#last-step').val()) + 1;
            $('#last-step').val(lastStep);
            step = lastStep;

            // remove tinyMCE Last step and reinit tinyMCE
            const editor = tinymce.get(`input-work-order-instruction-description-step-${step}`);
            if (editor) {
                editor.remove();
            }

            const newStepHtml = `
                <div class="card shadow" id="step-${step}">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col">
                                <h3 class="page-title text-center">Step ${step}</h3>
                            </div>
                            <div class="col-auto">
                                <button class="btn btn-danger btn-sm" onclick="deleteStep(${step})"><i class="fas fa-trash"></i></button>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-2 d-flex align-items-center">
                                Title
                            </div>
                            <div class="col-md">
                                <input type="text" class="form-control" id="input-work-order-instruction-title-step-${step}"
                                    name="input-work-order-instruction-title[]" required>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mt-2">
                            <div class="col-md-2 d-flex align-items-center">
                                Description
                            </div>
                            <div class="col-md">
                                <textarea class="form-control" id="input-work-order-instruction-description-step-${step}"
                                    name="input-work-order-instruction-description[]" required></textarea>
                            </div>
                        </div>
                        <div class="input-div-step-${step} mt-3"></div>
                        <div class="row mt-2">
                            <div class="col-md-2 d-flex align-items-center">
                                <button type="button" class="btn btn-primary btn-sm btn-add-input" id="btn-add-input" onclick="addInputModal(this)" data-step="${step}"><i
                                        class="fas fa-plus"></i>
                                    Add Input</button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            stepDiv.append(newStepHtml);
            initTinyMCE(`#input-work-order-instruction-description-step-${step}`);
        }

        function initTinyMCE(selector) {
            tinymce.init({
                selector: selector,
                height: 500,
                plugins: [
                    'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                    'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                    'insertdatetime', 'media', 'table', 'help', 'wordcount'
                ],
                toolbar: 'undo redo | blocks | ' +
                    'bold italic backcolor | alignleft aligncenter ' +
                    'alignright alignjustify | bullist numlist outdent indent | ' +
                    'removeformat | help | undo redo',
                file_picker_types: 'image',
                automatic_uploads: true,
                file_picker_callback: (cb, value, meta) => {
                    const input = document.createElement('input');
                    input.setAttribute('type', 'file');
                    input.setAttribute('accept', 'image/*');
                    input.addEventListener('change', (e) => {
                        const file = e.target.files[0];
                        const reader = new FileReader();
                        reader.addEventListener('load', () => {
                            const id = 'blobid' + (new Date()).getTime();
                            const blobCache = tinymce.activeEditor.editorUpload.blobCache;
                            const base64 = reader.result.split(',')[1];
                            const blobInfo = blobCache.create(id, file, base64);
                            blobCache.add(blobInfo);
                            cb(blobInfo.blobUri(), {
                                title: file.name
                            });
                        });
                        reader.readAsDataURL(file);
                    });
                    input.click();
                },
                content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:16px }',
                init_instance_callback: function(editor) {
                    editor.on('init', function() {
                        // Add your loading effect code 
                    });
                }
            });
        }

        function addInputModal(button) {
            const step = $(button).data('step');
            $('#input-work-order-instruction-input-step-modal-hidden').val(step);
            $('#modalInputOption').modal('show');
            $('.form-input-option').html('');
        }

        function addOption() {
            const inputType = $('#input-work-order-instruction-input-input-type').val();
            const inputOption = $('.form-input-option');
            const inputCount = inputOption.find('table tbody tr').length + 1;
            const inputGroup = $('#input-work-order-instruction-input-group-select').val();

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

        function deleteStep(step) {
            if (step === 1) {
                swal.fire({
                    title: 'Error',
                    text: 'Step 1 cannot be deleted',
                    icon: 'error'
                });
                return;
            }

            if (step !== parseInt($('#last-step').val())) {
                swal.fire({
                    title: 'Error',
                    text: 'Only last step can be deleted',
                    icon: 'error'
                });
                return;
            }

            // check id exist
            const id = $('#input-id-' + step).val();
            if (id) {
                // confirm delete because it will delete all data
                swal.fire({
                    title: 'Are you sure?',
                    text: 'You will not be able to recover this step and its data!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'No, keep it'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // send ajax request
                        $.ajax({
                            url: "{{ route('wo-instruction-template.delete') }}",
                            type: "POST",
                            data: {
                                "_token": "{{ csrf_token() }}",
                                "id": id
                            },
                            success: function(response) {
                                // remove tinyMCE Last step and reinit tinyMCE
                                const editor = tinymce.get(
                                    `input-work-order-instruction-description-step-${step}`);
                                if (editor) {
                                    editor.remove();
                                }

                                $(`#step-${step}`).remove();
                                $('#last-step').val(parseInt($('#last-step').val()) - 1);

                                // respon toastr
                                toastr.success('Data deleted successfully');
                                location.reload();
                            }
                        });
                    }
                });
                return;
            }

            $(`#step-${step}`).remove();
            $('#last-step').val(parseInt($('#last-step').val()) - 1);
        }

        function addInput() {
            const inputType = $('#input-work-order-instruction-input-input-type').val();
            const inputGroup = $('#input-work-order-instruction-input-group-select').val();
            const inputQuestion = $('input[name="input-work-order-instruction-input-question[]"]');
            const inputRequired = $('input[name="input-work-order-instruction-input-required[]"]');
            const step = $('#input-work-order-instruction-input-step-modal-hidden').val();
            const inputDiv = $(`.input-div-step-${step}`);

            for (let i = 0; i < inputQuestion.length; i++) {
                if (inputQuestion.eq(i).val() === '') {
                    swal.fire({
                        title: 'Error',
                        text: 'Question cannot be empty',
                        icon: 'error'
                    });
                    return;
                }
            }

            if ((inputType === 'radio' || inputType === 'select') && inputQuestion.length === 0) {
                swal.fire({
                    title: 'Error',
                    text: 'At least one option is required for radio or select input type',
                    icon: 'error'
                });
                return;
            }

            if (inputQuestion.length === 0) {
                swal.fire({
                    title: 'Error',
                    text: 'At least one question is required',
                    icon: 'error'
                });
                return;
            }

            if (inputDiv.find('table').length === 0) {
                inputDiv.append(`
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

            for (let i = 0; i < inputQuestion.length; i++) {
                const inputCount = inputDiv.find('table tbody tr').length + 1;
                const optionRow = `
                    <tr>
                        <td>${inputCount}
                            <input type="hidden" name="input-step[]" value="${step}">
                            <input type="hidden" name="input-count[]" value="${inputCount}">
                        </td>
                        <td>${inputQuestion.eq(i).val()}
                            <input type="hidden" name="input-question[]" value="${inputQuestion.eq(i).val()}">
                        </td>
                        <td>${inputType}
                            <input type="hidden" name="input-type[]" value="${inputType}">
                        </td>
                        <td>${inputGroup}
                            <input type="hidden" name="input-group[]" value="${inputGroup}">
                        </td>
                        <td>${inputRequired.eq(i).is(':checked') ? 'Yes' : 'No'}
                            <input type="hidden" name="input-required[]" value="${inputRequired.eq(i).is(':checked')}">
                        </td>
                        <td><button class="btn btn-danger btn-sm" onclick="deleteOption(this)">Delete</button></td>
                    </tr>
                `;

                inputDiv.find('table tbody').append(optionRow);
            }

            $("#modalInputOption").modal('hide');
        }

        $(document).ready(function() {
            initTinyMCE('textarea');
            $('#save-button').on('click', function(event) {
                var total_steps = $('#last-step').val();
                // disable button
                $('#save-button').prop('disabled', true);

                // loop through each step
                for (var i = 1; i <= total_steps; i++) {
                    var id = $('#input-id-' + i).val();
                    var title = $('#input-work-order-instruction-title-step-' + i).val();
                    var description = tinymce.get('input-work-order-instruction-description-step-' + i)
                        .getContent();
                    var step = i;
                    var input_question = [];
                    var input_type = [];
                    var input_group = [];
                    var input_required = [];
                    var input_count = [];
                    // loop through each input
                    $('.input-div-step-' + i + ' table tbody tr').each(function() {
                        input_question.push($(this).find('td:nth-child(2) input').val());
                        input_type.push($(this).find('td:nth-child(3) input').val());
                        input_group.push($(this).find('td:nth-child(4) input').val());
                        input_required.push($(this).find('td:nth-child(5) input').val());
                        input_count.push($(this).find('td:nth-child(1) input').val());
                    });
                    // send ajax request
                    $.ajax({
                        url: "{{ route('wo-instruction-template.edit') }}",
                        type: "POST",
                        data: {
                            "_token": "{{ csrf_token() }}",
                            "id": id,
                            "title": title,
                            "description": description,
                            "step": step,
                            "input_question": input_question,
                            "input_type": input_type,
                            "input_group": input_group,
                            "input_required": input_required,
                            "input_count": input_count
                        },
                        success: function(response) {
                            // respon toastr
                            toastr.success('Data updated successfully');
                        }
                    });
                }
                // enable button
                $('#save-button').prop('disabled', false);
            });
        });

        function copyToClipboard(text) {
            var dummy = document.createElement("textarea");
            document.body.appendChild(dummy);
            dummy.value = text;
            dummy.select();
            document.execCommand("copy");
            document.body.removeChild(dummy);
            toastr.success('Copied to clipboard');
        }


        $(document).ready(function() {
            for (var i = 1; i <= step; i++) {
                if (i !== step) {
                    $('#step-' + i + ' button').remove();
                }
            }
        });
    </script>
@endsection
