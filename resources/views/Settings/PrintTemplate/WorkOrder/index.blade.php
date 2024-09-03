@extends('template.master')


@section('content')
    <div class="card">
        <div class="card-body">
            {{-- Title --}}
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">Work Order Print Template</h3>
                    </div>
                    <div class="col-auto text-end float-end ms-auto download-grp">
                        <button id="btn-add" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add
                            New Template</button>
                    </div>
                </div>
            </div>
            <br>

            {{-- Table --}}
            <table class="table table-striped" id="table-work-order-template">
                <thead>
                    <tr>
                        <th scope="col" class="table-col-no">#</th>
                        <th scope="col">Name</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>



    <script src="{{ asset('plugins/tinymce/js/tinymce/tinymce.min.js') }}"></script>
    {{-- Import Template --}}
    @foreach ($data['importTemplate'] as $template)
        <div class="card">
            <div class="card-body">
                <h4 class="header-title mb-4">{{ $template['name'] }} Import Custom
                    {{-- badge new --}}
                    <span class="badge bg-success rounded-pill">New</span>
                </h4>
                <p class="text-muted font-13 mb-4">
                    Import your {{ $template['name'] }} from Microsoft Word. Please note that the imported template will be
                    in HTML format.
                    @php
                        if ($template['name'] == 'Print Techician Report') {
                            // available parameter for Print Techician Report
                            echo '<br>Available parameters: ';
                            echo '<ul>';
                            echo '<li>{WORKORDERID}</li>';
                            echo '<li>{DATE}</li>';
                            echo '<li>{ADDRESS}</li>';
                            echo '<li>{BATTERY}</li>';
                            echo '</ul>';
                        }
                    @endphp
                </p>
                <textarea id="template-{{ $template['id'] }}" class="form-control" rows="10">
                    {{ $template['template'] }}
                </textarea>
            </div>

            {{-- save button template --}}
            <div class="card-footer text-end">
                <button class="btn btn-danger btn-template-clear" id="btn-template-clear"
                    data-id="{{ $template['id'] }}">Clear</button>
                <button class="btn btn-success" id="btn-template-save" data-id="{{ $template['id'] }}">Save</button>
            </div>
        </div>

        <script>
            var editor = tinymce.init({
                selector: 'textarea#template-{{ $template['id'] }}',
                height: 500,
                plugins: [
                    'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                    'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                    'insertdatetime', 'media', 'table', 'help', 'wordcount', 'importword', 'image code'
                ],
                toolbar: 'undo redo | blocks | ' +
                    'bold italic backcolor | alignleft aligncenter ' +
                    'alignright alignjustify | bullist numlist outdent indent | ' +
                    'removeformat | help | undo redo | image code',
                file_picker_types: 'image',
                automatic_uploads: true,
                file_picker_types: 'image',
                file_picker_callback: (cb, value, meta) => {
                    const input = document.createElement('input');
                    input.setAttribute('type', 'file');
                    input.setAttribute('accept', 'image/*');

                    input.addEventListener('change', (e) => {
                        const file = e.target.files[0];

                        const reader = new FileReader();
                        reader.addEventListener('load', () => {
                            /*
                              Note: Now we need to register the blob in TinyMCEs image blob
                              registry. In the next release this part hopefully won't be
                              necessary, as we are looking to handle it internally.
                            */
                            const id = 'blobid' + (new Date()).getTime();
                            const blobCache = tinymce.activeEditor.editorUpload.blobCache;
                            const base64 = reader.result.split(',')[1];
                            const blobInfo = blobCache.create(id, file, base64);
                            blobCache.add(blobInfo);

                            /* call the callback and populate the Title field with the file name */
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

            document.querySelectorAll('.btn-template-clear').forEach(function(button) {
                button.addEventListener('click', function() {
                    var templateId = button.getAttribute('data-id');
                    tinymce.get('template-' + templateId).setContent('');
                });
            });
        </script>
    @endforeach

    {{-- DataTables Configurations --}}
    <script>
        var table;

        $(document).ready(function() {
            // DataTables configuration
            table = $("#table-work-order-template").DataTable({
                lengthMenu: [
                    [5, 10, 25],
                    [5, 10, 25]
                ],
                responsive: true,
                processing: true,
                serverSide: true,
                order: [],
                ajax: {
                    url: "/template/show",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}"
                    }
                },
                columnDefs: [{
                    targets: [0],
                    orderable: false
                }, {
                    targets: [0, 1],
                    className: 'dt-body-center'
                }],
                dom: "lBfrtip",
                buttons: getDatatablesButtonConfigurations({
                    text: "<i class='fas fa-add'></i> Add Details Template",
                    action: function(e, dt, node, config) {
                        // Get the selected row's id.
                        let selectedRows = table.rows({
                            selected: true
                        }).data().toArray();
                        if (selectedRows.length !== 1) {
                            Swal.fire({
                                title: "Error",
                                text: "Please select a single row for this action.",
                                icon: "error",
                            });
                            return;
                        }

                        // Redirect to the details page.
                        goToPage("/template/details/" + selectedRows[0][2]);
                    },
                    className: "btn btn-outline-secondary btn-sm",
                }),
                language: getDatatablesLanguangeConfigurations("Work Order Print Templates"),
                select: true,
            });

            // Load DataTables toolbar component.
            appendDatatablesToolbar(2, "/template/edit/", "/template/destroy");
        });
    </script>

    {{-- Click Handler --}}
    <script>
        $('#btn-add').on('click', function() {
            goToPage("/template/create");
        });

        // btn-template-save
        $(document).on('click', '#btn-template-save', function() {
            let id = $(this).data('id');
            let template = tinymce.get('template-' + id).getContent();

            $.ajax({
                url: '/template/import/update',
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    id: id,
                    template: template
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            title: "Success",
                            text: "Template has been saved.",
                            icon: "success",
                        });
                    } else {
                        Swal.fire({
                            title: "Error",
                            text: "Failed to save template.",
                            icon: "error",
                        });
                    }
                }
            });
        });

        // btn-template-delete
        $(document).on('click', '#btn-template-delete', function() {
            let id = $(this).data('id');

            $.ajax({
                url: '/template/import/delete',
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    id: id
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            title: "Success",
                            text: "Template has been deleted.",
                            icon: "success",
                        });
                    } else {
                        Swal.fire({
                            title: "Error",
                            text: "Failed to delete template.",
                            icon: "error",
                        });
                    }
                }
            });
        });
    </script>
@endsection
