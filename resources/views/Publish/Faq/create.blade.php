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
                FAQ
            </div>
            <br>

            {{-- Form --}}
            <form id="faq-form">
                @csrf

                {{-- Question --}}
                <div class="form-group local-forms">
                    <label for="question">Question <span class="login-danger">*</span></label>
                    <input type="text" class="form-control" id="question" name="question" placeholder="Enter question"
                        required @isset($data['profile']) value="{{ $data['profile']['question'] }}" @endisset>
                </div>

                {{-- Answer --}}
                <div class="form-group local-forms">
                    <label for="answer">Answer <span class="login-danger">*</span></label>
                    <textarea class="form-control" id="answer" name="answer" rows="4" placeholder="Enter answer">
@isset($data['profile'])
{{ $data['profile']['answer'] }}
@endisset
</textarea>
                </div>

                {{-- Status --}}
                <div class="form-group local-forms d-none">
                    <label for="status">Status <span class="login-danger">*</span></label>
                    <select class="form-control" id="status" name="status" required>
                        <option value="1"
                            @isset($data['profile']) @if ($data['profile']['status'] == 1) selected @endif @endisset>
                            Active</option>
                        <option value="0"
                            @isset($data['profile']) @if ($data['profile']['status'] == 0) selected @endif @endisset>
                            Inactive</option>
                    </select>
                </div>

                {{-- Hidden Inputs --}}
                @isset($data['profile'])
                    <input type="hidden" name="id" id="id" value="{{ $data['profile']['id'] }}">
                @endisset

                {{-- Buttons --}}
                <div class="d-flex flex-row-reverse">
                    {{-- Save Button --}}
                    <button type="submit" class="btn btn-success mx-1" id="btn-save"
                        @isset($data['profile']) value="update">Update FAQ</button>
                @else
                value="create">Create FAQ</button>
                @endisset
                        {{-- Cancel Button --}} <button type="reset" type="button" class="btn btn-danger mx-1"
                        id="btn-cancel">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script src="{{ asset('plugins/tinymce/js/tinymce/tinymce.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $("#faq-form").on("submit", function(event) {
                event.preventDefault();

                // Validate TinyMCE content
                if (tinymce.get("answer").getContent().trim() === "") {
                    alert("Answer field is required.");
                    return;
                }

                // Disable button
                $("#btn-save").attr("disabled", true);
                $("#btn-save").html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...'
                );

                // Get current display mode (Update or Create).
                let mode = $("#btn-save").attr("value");
                let url = "/faq/store";
                if (mode == "update") {
                    url = "/faq/update";
                }

                // Get FAQ form data.
                let formData = new FormData($(this)[0]);
                formData.append("answer", tinymce.get("answer").getContent());
                formData.append("question", $("#question").val());
                formData.append("status", $("#status").val());
                formData.append("id", $("#id").val());

                // Send submit POST request via AJAX.
                sendSubmitRequest(url, formData, function() {
                    // Redirect to index page.
                    goToPage("/faq");
                });
            });

            $("#faq-form").on("reset", function() {
                goToPage("/faq");
            });

            initTinyMCE("#answer");
        });

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
    </script>
@endsection
