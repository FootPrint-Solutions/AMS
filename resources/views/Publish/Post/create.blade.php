@extends('template.master')

@section('content')
    <div class="row">
        {{-- Main Content --}}
        <div class="col-md-8">
            <div class="card mb-3">
                <div class="card-body">
                    <div class="card-title h5">
                        @isset($data['post'])
                            Edit
                        @else
                            Add New
                        @endisset
                        Post
                    </div>
                    <br>

                    <form id="post-form" enctype="multipart/form-data">
                        @csrf

                        {{-- Title --}}
                        <div class="form-group local-forms">
                            <input type="text" class="form-control form-control-lg" id="title" name="title"
                                placeholder="Add Title ( required )" value="{{ $data['post']->title ?? '' }}" required>
                        </div>

                        {{-- Slug --}}
                        <div class="form-group local-forms">
                            <input type="text" class="form-control" id="slug" name="slug"
                                placeholder="Slug ( required )" value="{{ $data['post']->slug ?? '' }}" required readonly>
                        </div>

                        {{-- Excerpt --}}
                        <div class="form-group local-forms">
                            <textarea class="form-control" id="excerpt" name="excerpt" placeholder="Excerpt">{{ $data['post']->excerpt ?? '' }}</textarea>
                        </div>

                        {{-- Content --}}
                        <div class="form-group local-forms">
                            <textarea class="form-control" id="content" name="content" rows="10"
                                placeholder="Write your post... ( required )">{{ $data['post']->content ?? '' }}</textarea>
                        </div>

                        {{-- Hidden Inputs --}}
                        @isset($data['post'])
                            <input type="hidden" name="id" value="{{ $data['post']->id }}">
                        @endisset

                        <div class="d-flex flex-row-reverse">
                            <button type="submit" class="btn btn-success mx-1" id="btn-save"
                                @isset($data['post']) value="update">Update Post</button>
                            @else
                            value="create">Create Post</button>
                            @endisset
                                <button type="reset" type="button" class="btn btn-danger mx-1"
                                id="btn-cancel">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="col-md-4">
            {{-- Publish --}}
            <div class="card mb-3">
                <div class="card-header">Publish</div>
                <div class="card-body">
                    {{-- Status --}}
                    <div class="form-group local-forms">
                        <label for="status">Status <span class="login-danger">*</span></label>
                        <select class="form-control" id="status" name="status" form="post-form" required>
                            <option value="1"
                                @isset($data['post']) @if ($data['post']->status == 'published') selected @endif @endisset>
                                Published</option>
                            <option value="0"
                                @isset($data['post']) @if ($data['post']->status == 'draft') selected @endif @endisset>
                                Draft</option>
                        </select>
                    </div>
                    {{-- Published At --}}
                    <div class="form-group local-forms">
                        <label for="published_at">Published At</label>
                        <input type="datetime-local" class="form-control" id="published_at" name="published_at"
                            form="post-form"
                            value="{{ isset($data['post']->published_at) ? \Carbon\Carbon::parse($data['post']->published_at)->format('Y-m-d\TH:i') : '' }}">
                    </div>
                </div>
            </div>

            {{-- Categories --}}
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Categories <span class="login-danger">*</span></span>
                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                        data-bs-target="#addCategoryModal">+ Add</button>
                </div>
                <div class="card-body">
                    <select class="form-control" id="category_id" name="category_id" form="post-form" required>
                        <option value="">Select Category</option>
                        @foreach ($data['categories'] as $category)
                            <option value="{{ $category->id }}"
                                @isset($data['post']) @if ($data['post']->category_id == $category->id) selected @endif @endisset>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Tags --}}
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Tags <span class="login-danger">*</span></span>
                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                        data-bs-target="#addTagModal">+ Add</button>
                </div>
                <div class="card-body">
                    <select class="form-control" id="tags" name="tags[]" form="post-form" multiple>
                        @foreach ($data['tags'] as $tag)
                            <option value="{{ $tag->id }}"
                                @isset($data['post'])
                                    @if ($data['post']->tags->contains($tag->id)) selected @endif
                                @endisset>
                                {{ $tag->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Featured Image --}}
            <div class="card mb-3">
                <div class="card-header">Featured Image <span class="login-danger">*</span></div>
                <div class="card-body">
                    <input type="file" class="form-control" id="featured_image" name="featured_image" form="post-form"
                        onchange="previewImage(this, '#featured_image_preview')" required
                        @isset($data['post']) accept="image/*" @endisset>
                    @isset($data['post']->featured_image)
                        <div class="mt-2">
                            <img id="featured_image_preview"
                                src="{{ asset('storage/posts/' . $data['post']->featured_image) }}" alt="Featured Image"
                                class="img-thumbnail" style="max-width: 150px;"
                                onerror="this.onerror=null;this.src='https://placehold.co/50x50';">
                        </div>
                    @else
                        <div class="mt-2">
                            <img id="featured_image_preview" src="#" alt="Image Preview" class="img-thumbnail"
                                style="max-width: 150px; display: none;"
                                onerror="this.onerror=null;this.src='https://placehold.co/50x50';">
                        </div>
                    @endisset
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Add Category --}}
    <div class="modal fade" id="addCategoryModal" tabindex="-1" role="dialog" aria-labelledby="addCategoryModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form id="add-category-form">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addCategoryModalLabel">Add New Category</h5>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="new_category_name">Category Name</label>
                            <input type="text" class="form-control" id="new_category_name" name="name" required>
                            <input type="hidden" name="status" value="1" id="new_category_status">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Add Category</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Add Tag --}}
    <div class="modal fade" id="addTagModal" tabindex="-1" role="dialog" aria-labelledby="addTagModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form id="add-tag-form">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addTagModalLabel">Add New Tag</h5>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="new_tag_name">Tag Name</label>
                            <input type="text" class="form-control" id="new_tag_name" name="name" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Add Tag</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="{{ asset('plugins/tinymce/js/tinymce/tinymce.min.js') }}"></script>

    <script>
        function previewImage(input, previewSelector) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.querySelector(previewSelector);
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        $(document).ready(function() {
            $('#category_id, #tags').select2({
                placeholder: "Select an option"
            });

            $("#post-form").on("submit", function(event) {
                event.preventDefault();

                $("#btn-save").attr("disabled", true);
                $("#btn-save").html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...'
                );

                let mode = $("#btn-save").attr("value");
                let url = "/post/store";
                if (mode == "update") {
                    url = "/post/update";
                }

                // Sync TinyMCE content to textarea
                if (typeof tinymce !== "undefined") {
                    tinymce.triggerSave();
                }

                let formData = new FormData($(this)[0]);

                sendSubmitRequest(url, formData, function() {
                    goToPage("/post");
                });
            });

            $("#post-form").on("reset", function() {
                goToPage("/post");
            });

            // Add Category
            $('#add-category-form').on('submit', function(e) {
                e.preventDefault();
                let name = $('#new_category_name').val();

                $.post('/post/category/store', {
                    name: name,
                    _token: '{{ csrf_token() }}',
                    status: $('#new_category_status').val()
                }, function(result) {
                    let resultData = JSON.parse(result);
                    if (resultData && resultData.status === true) {
                        let newCategory = resultData.data.find(item => item.name === name);
                        if (newCategory) {
                            $('#category_id').append(
                                `<option value="${newCategory.id}" selected>${newCategory.name}</option>`
                            ).trigger('change');

                            const modalEl = document.getElementById('addCategoryModal');
                            const modalInstance = bootstrap.Modal.getInstance(modalEl);
                            if (modalInstance) modalInstance.hide();

                            $('#new_category_name').val('');

                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: 'Category added successfully!'
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'New category not found in response.'
                            });
                        }
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to add category. Please try again.'
                        });
                    }
                });
            });

            // Add Tag
            $('#add-tag-form').on('submit', function(e) {
                e.preventDefault();
                let name = $('#new_tag_name').val();

                $.post('/post/tag/store', {
                    name: name,
                    _token: '{{ csrf_token() }}'
                }, function(result) {
                    let resultData = JSON.parse(result);
                    if (resultData && resultData.status === true) {
                        let newTag = resultData.data.find(item => item.name === name);
                        if (newTag) {
                            $('#tags').append(
                                `<option value="${newTag.id}" selected>${newTag.name}</option>`
                            ).trigger('change');

                            const modalEl = document.getElementById('addTagModal');
                            const modalInstance = bootstrap.Modal.getInstance(modalEl);
                            if (modalInstance) modalInstance.hide();

                            $('#new_tag_name').val('');

                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: 'Tag added successfully!'
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'New tag not found in response.'
                            });
                        }
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to add tag. Please try again.'
                        });
                    }
                });
            });

            // Auto-generate slug
            $('#title').on('input', function() {
                let title = $(this).val();
                let slug = title.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
                let url = 'https://akikita.web.id/p/' + slug;
                $('#slug').val(url);
            });

            tinymce.init({
                selector: '#content',
                height: 300,
                plugins: 'lists link image code',
                toolbar: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright | bullist numlist outdent indent | link image | code',
                images_upload_url: '/upload/image',
                automatic_uploads: true,
                file_picker_types: 'image',
                file_picker_callback: function(callback, value, meta) {
                    if (meta.filetype === 'image') {
                        let input = document.createElement('input');
                        input.setAttribute('type', 'file');
                        input.setAttribute('accept', 'image/*');
                        input.onchange = function() {
                            let file = this.files[0];
                            let reader = new FileReader();
                            reader.onload = function() {
                                callback(reader.result, {
                                    alt: file.name
                                });
                            };
                            reader.readAsDataURL(file);
                        };
                        input.click();
                    }
                }
            });

            $('#excerpt').on('input', function() {
                let text = $(this).val();
                let words = text.split(/\s+/);
                if (words.length > 50) {
                    $(this).val(words.slice(0, 50).join(' ') + '...');
                }
            });

        });
    </script>
@endsection
