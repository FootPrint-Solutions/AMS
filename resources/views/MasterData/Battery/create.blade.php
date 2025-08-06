@extends('template.master')

@section('content')
    {{-- Form --}}
    <div class="card">
        <div class="card-body">
            {{-- Title --}}
            <div class="card-title h5">
                @if (isset($data['profile']))
                    Edit Battery
                @else
                    Add New Battery
                @endif
            </div>
            <br>

            {{-- Form --}}
            <form id="battery-form">
                @csrf

                {{-- Name & Aliases --}}
                <div class="row">
                    {{-- Name --}}
                    <div class="col">
                        <div class="form-group local-forms">
                            <label for="name">Name <span class="login-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name"
                                placeholder="Enter battery name" required
                                @if (isset($data['profile'])) value="{{ $data['profile']['name'] }}" @endif>
                        </div>
                    </div>

                    {{-- Alternate Names --}}
                    <div class="col">
                        <div class="form-group local-forms">
                            <label for="altname">Alternate Names <span class="login-danger">*</span></label>
                            <input type="text" class="form-control" id="altname" name="altname"
                                placeholder="Enter battery alternate name" required
                                @if (isset($data['profile'])) value="{{ $data['profile']['name_alternate'] }}" @endif>
                        </div>
                    </div>

                    {{-- Code --}}
                    <div class="col">
                        <div class="form-group local-forms">
                            <label for="code">Code <span class="login-danger">*</span></label>
                            <input type="text" class="form-control" id="code" name="code"
                                placeholder="Enter battery code"
                                @if (isset($data['profile']) && $data['profile']['code']) value="{{ $data['profile']['code']['code'] }}" required @else readonly @endif>
                        </div>
                    </div>
                </div>

                {{-- Brand & Subbrand Category --}}
                <div class="row">
                    {{-- Brand --}}
                    <div class="col">
                        <div class="form-group local-forms">
                            <label for="brand">Brand <span class="login-danger">*</span></label>
                            <select class="form-control" id="brand" name="brand" required>
                                <option></option>
                                @foreach ($data['brands'] as $brand)
                                    <option value="{{ $brand['id'] }}" @if (isset($data['profile']) && $data['profile']['brand_id'] == $brand['id']) selected @endif>
                                        {{ $brand['name'] }}</option>
                                @endforeach
                                <option value="new">Quick add new brand&hellip;</option>
                            </select>
                        </div>
                    </div>

                    {{-- Subbrand Category --}}
                    <div class="col">
                        <div class="form-group local-forms">
                            <label for="subbrand-category">Subbrand Category</label>
                            <select class="form-control" id="subbrand-category" name="subbrandcategory">
                                <option></option>
                                @foreach ($data['subbrand_categories'] as $category)
                                    <option value="{{ $category['id'] }}" @if (isset($data['profile']) && $data['profile']['subbrand_category_id'] == $category['id']) selected @endif>
                                        {{ $category['name'] }}</option>
                                @endforeach
                                <option value="new">Quick add new subbrand&hellip;</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Quick Add New Brand & Subbrand Category --}}
                <div class="row">
                    {{-- New Brand --}}
                    <div class="col">
                        <div id="brand-new-group" class="form-group local-forms" style="display: none;">
                            <label for="brand-new">New Brand <span class="login-danger">*</span></label>
                            <input type="text" class="form-control" id="brand-new" name="newbrand"
                                placeholder="Enter new battery brand">
                        </div>
                    </div>

                    {{-- New Subbrand Category --}}
                    <div class="col">
                        <div id="subbrand-category-new-group" class="form-group local-forms" style="display: none;">
                            <label for="subbrand-category-new">New Subbrand Category</label>
                            <input type="text" class="form-control" id="subbrand-category-new" name="newsubbrandcategory"
                                placeholder="Enter new battery subbrand category">
                        </div>
                    </div>
                </div>

                {{-- Usage Type, Technology & Size Category --}}
                <div class="row">
                    {{-- Usage Type --}}
                    <div class="col">
                        <div class="form-group local-forms">
                            <label for="usagetype">Usage Type</label>
                            <select class="form-control" id="usagetype" name="usagetype">
                                <option></option>
                                @foreach ($data['usage_types'] as $usage)
                                    <option value="{{ $usage['id'] }}" @if (isset($data['profile']) && $data['profile']['usage_type_id'] == $usage['id']) selected @endif>
                                        {{ $usage['name'] }}</option>
                                @endforeach
                                <option value="new">Quick add new usage type&hellip;</option>
                            </select>
                        </div>
                    </div>

                    {{-- Battery Technology --}}
                    <div class="col">
                        <div class="form-group local-forms">
                            <label for="technology">Technology</label>
                            <select class="form-control" id="technology" name="technology">
                                <option></option>
                                @foreach ($data['technologies'] as $tech)
                                    <option value="{{ $tech['id'] }}" @if (isset($data['profile']) && $data['profile']['technology_id'] == $tech['id']) selected @endif>
                                        {{ $tech['name'] }}</option>
                                @endforeach
                                <option value="new">Quick add new technology&hellip;</option>
                            </select>
                        </div>
                    </div>

                    {{-- Size Category --}}
                    <div class="col">
                        <div class="form-group local-forms">
                            <label for="size">Size Category</label>
                            <select class="form-control" id="size" name="size">
                                <option></option>
                                @foreach ($data['sizes'] as $size)
                                    <option value="{{ $size['id'] }}" @if (isset($data['profile']) && $data['profile']['size_category_id'] == $size['id']) selected @endif>
                                        {{ $size['name'] }}</option>
                                @endforeach
                                <option value="new">Quick add new size category&hellip;</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Quick Add New Brand, Subbrand Category & Size Category --}}
                <div class="row">
                    {{-- New Usage Type --}}
                    <div class="col">
                        <div id="usagetype-new-group" class="form-group local-forms" style="display: none;">
                            <label for="usagetype-new">New Usage Type</label>
                            <input type="text" class="form-control" id="usagetype-new" name="newusagetype"
                                placeholder="Enter new battery usage type">
                        </div>
                    </div>

                    {{-- New Technology --}}
                    <div class="col">
                        <div id="technology-new-group" class="form-group local-forms" style="display: none;">
                            <label for="technology-new">New Technology</label>
                            <input type="text" class="form-control" id="technology-new" name="newtechnology"
                                placeholder="Enter new battery technology">
                        </div>
                    </div>

                    {{-- Size Category --}}
                    <div class="col">
                        <div id="size-new-group" class="form-group local-forms" style="display: none;">
                            <label for="size-new">New Size Category</label>
                            <input type="text" class="form-control" id="size-new" name="newsize"
                                placeholder="Enter new size category">
                        </div>
                    </div>
                </div>

                {{-- Dimension --}}
                <div class="row">
                    {{-- Length --}}
                    <div class="col">
                        <div class="form-group local-forms">
                            <label for="dimension-length">Length <span class="login-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" min="0" class="form-control" id="dimension-length"
                                    name="dimension[]" placeholder="Enter battery length" required
                                    @if (isset($data['profile'])) value="{{ $data['profile']['dimension_length'] }}" @endif>
                                <span class="input-group-text border-end">mm</span>
                            </div>
                        </div>
                    </div>

                    {{-- Width --}}
                    <div class="col">
                        <div class="form-group local-forms">
                            <label for="dimension-width">Width <span class="login-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" min="0" class="form-control" id="dimension-width"
                                    name="dimension[]" placeholder="Enter battery width" required
                                    @if (isset($data['profile'])) value="{{ $data['profile']['dimension_width'] }}" @endif>
                                <span class="input-group-text border-end">mm</span>
                            </div>
                        </div>
                    </div>

                    {{-- Height --}}
                    <div class="col">
                        <div class="form-group local-forms">
                            <label for="dimension-height">Height <span class="login-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" min="0" class="form-control" id="dimension-height"
                                    name="dimension[]" placeholder="Enter battery height" required
                                    @if (isset($data['profile'])) value="{{ $data['profile']['dimension_height'] }}" @endif>
                                <span class="input-group-text border-end">mm</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Standard CCA & Capacity --}}
                <div class="row">
                    {{-- Standard CCA --}}
                    <div class="col">
                        <div class="form-group local-forms">
                            <label for="standard-cca">Standard CCA</label>
                            <div class="input-group">
                                <input type="number" min="0" class="form-control" id="standard-cca"
                                    name="standardcca" placeholder="Enter battery standard CCA"
                                    @if (isset($data['profile'])) value="{{ $data['profile']['standard_cca'] }}" @endif>
                                <span class="input-group-text border-end">A</span>
                            </div>
                        </div>
                    </div>

                    {{-- Capacity --}}
                    <div class="col">
                        <div class="form-group local-forms">
                            <label for="dimension-width">Capacity <span class="login-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" min="0" class="form-control" id="capacity"
                                    name="capacity" placeholder="Enter battery capacity" required
                                    @if (isset($data['profile'])) value="{{ $data['profile']['capacity'] }}" @endif>
                                <span class="input-group-text border-end">AH</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Warranty & Price Retail --}}
                <div class="row">
                    {{-- Warranty --}}
                    <div class="col">
                        <div class="form-group local-forms">
                            <label for="warranty">Warranty</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="warranty" name="warranty"
                                    placeholder="Enter battery warranty duration"
                                    @if (isset($data['profile'])) value="{{ $data['profile']['warranty'] }}" @endif>
                                <span class="input-group-text border-end">month</span>
                            </div>
                        </div>
                    </div>

                    {{-- Price Retail --}}
                    <div class="col">
                        <div class="form-group local-forms">
                            <label for="price">Price Retail <span class="login-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text border-end">IDR</span>
                                <input type="text" min="0" class="form-control" id="price" name="price"
                                    placeholder="Enter battery price retail" required
                                    @if (isset($data['profile'])) value="{{ $data['profile']['price_retail'] }}" @endif>
                            </div>
                            <small id="price-warning-number" class="form-text text-danger" style="display: none;">Please
                                enter a valid numeric value for the price.</small>
                        </div>
                    </div>

                    {{-- Editable Price Checkbox --}}
                    <div class="col">
                        <div class="form-group local-forms">
                            <label for="editable-price">Editable Price</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="editable-price"
                                    name="editable_price" @if (isset($data['profile']) && $data['profile']['editable_price']) checked @endif
                                    value="1">
                                <label class="form-check-label" for="editable-price">
                                    Allow editing of price
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- Battery Type --}}
                    <div class="col">
                        <div class="form-group local-forms">
                            <label for="battery-type">Battery Type</label>
                            <select class="form-control" id="type" name="type">
                                <option value="regular" @if (isset($data['profile']) && $data['profile']['type'] == 'regular') selected @endif>Regular
                                </option>
                                <option value="recycle" @if (isset($data['profile']) && $data['profile']['type'] == 'recycle') selected @endif>Recycle
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- URLs --}}
                @php
                    $urls =
                        isset($data['profile']['urls']) && count($data['profile']['urls']) > 0
                            ? $data['profile']['urls']
                            : [''];
                    $counter = 1;
                @endphp

                <label>URLs <button type="button" id="btn-add-row" class="btn btn-primary btn-sm rounded-circle mx-2"><i
                            class="fas fa-plus"></i></button></label>
                <ul class="list-group list-group-flush" id="battery-url-list">
                    @foreach ($urls as $url)
                        <li
                            class="list-group-item battery-url-list-item @if ($urls == ['']) d-none @endif">
                            <div class="row mt-1">
                                {{-- Platform --}}
                                <div class="col-sm-2">
                                    <div class="form-group local-forms">
                                        <label for="platform-{{ $counter }}">Platform</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control platform"
                                                id="platform-{{ $counter }}" name="platform[]"
                                                placeholder="Enter battery url platform"
                                                @if (isset($data['profile']) && count($data['profile']['urls']) > 0) value="{{ $url['platform'] }}" @endif>
                                        </div>
                                    </div>
                                </div>

                                {{-- URL --}}
                                <div class="col">
                                    <div class="form-group local-forms">
                                        <label for="url-{{ $counter }}">URL</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control url" id="url-{{ $counter }}"
                                                name="url[]" placeholder="Enter battery url"
                                                @if (isset($data['profile']) && count($data['profile']['urls']) > 0) value="{{ $url['url'] }}" @endif>
                                        </div>
                                    </div>
                                </div>

                                {{-- Delete --}}
                                <div class="col-sm-1">
                                    <button type="button" class="btn btn-danger btn-sm btn-delete-row"
                                        title="Delete Item"><i class="fas fa-xmark"></i></button>
                                </div>

                                {{-- Hidden Input --}}
                                @if (isset($data['profile']) && count($data['profile']['urls']) > 0)
                                    <input type="hidden" name="url_id[]" value="{{ $url['id'] }}">
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>

                {{-- Image --}}
                <div class="form-group mb-3">
                    <label for="image" class="form-label">Main Image</label>
                    <div class="d-flex align-items-center gap-2">
                        <input type="file" id="image" name="image" class="form-control"
                            style="max-width:250px;">
                        @isset($data['profile'])
                            @empty($data['profile']['image'])
                                <span class="text-muted ms-2">No image uploaded.</span>
                            @else
                                <button type="button" class="btn btn-outline-primary btn-sm ms-2" data-bs-toggle="modal"
                                    data-bs-target="#image-modal">
                                    <i class="fas fa-eye"></i> Preview
                                </button>
                            @endempty
                        @endisset
                    </div>
                </div>

                {{-- Multiple Detail Images --}}
                <div class="form-group mb-3">
                    <label for="detail-images" class="form-label">Detail Images</label>
                    <input type="file" id="detail-images" name="detail_images[]" multiple accept="image/*"
                        class="form-control" style="max-width:350px;">
                    <small class="form-text text-muted">Upload multiple images for battery details.</small>
                    @isset($data['profile']['batteryImages'])
                        <div class="mt-2">
                            <label class="form-label">Existing Detail Images:</label>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach ($data['profile']['batteryImages'] as $img)
                                    <div class="border rounded p-1 bg-light"
                                        style="width:100px; height:90px; display:flex; align-items:center; justify-content:center;">
                                        <img src="{{ asset('storage/' . $img['image_path']) }}" alt="Detail Image"
                                            class="img-fluid" style="max-width:90px;max-height:80px;object-fit:contain;"
                                            onerror="this.onerror=null;this.src='https://placehold.co/100';">
                                        @if (!empty($img['image_type']))
                                            <span class="badge bg-secondary mt-1">{{ $img['image_type'] }}</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endisset
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
                        Update Battery
                    @else
                        value="create">
                        Create Battery @endif
                        </button>

                        {{-- Cancel Button --}}
                        <button type="reset" type="button" class="btn btn-danger mx-1"
                            id="btn-cancel">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Image Preview Modal --}}
    @isset($data['profile'])
        <style>
            .image-thumb {
                transition: transform 0.3s ease, box-shadow 0.3s ease;
            }

            .image-thumb:hover {
                transform: scale(1.05);
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            }

            .carousel-item img {
                max-height: 200px;
                object-fit: contain;
            }

            .carousel-control-prev-icon,
            .carousel-control-next-icon {
                background-color: rgba(0, 0, 0, 0.4);
                border-radius: 50%;
                padding: 10px;
            }
        </style>

        <div id="image-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content rounded-3 shadow">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold border-bottom pb-2 w-100" id="imageModalLabel">Image Preview</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body d-flex flex-column align-items-center" style="min-height:250px;">
                        {{-- Main Image --}}
                        @if (!empty($data['profile']['image']))
                            <img src="{{ asset('storage/image/battery/' . $data['profile']['image']) }}" alt="Battery Image"
                                class="img-fluid rounded border mb-4 image-thumb"
                                style="max-height:350px; max-width:100%; object-fit:contain;" loading="lazy"
                                onerror="this.onerror=null;this.src='https://placehold.co/400';">
                        @else
                            <img src="https://placehold.co/400" alt="No Image" class="img-fluid rounded border mb-4"
                                style="max-height:350px; max-width:100%; object-fit:contain;">
                        @endif

                        {{-- Detail Images Carousel --}}
                        @if (!empty($data['profile']['battery_images']))
                            <div class="w-100 mt-3">
                                <label class="form-label fw-semibold mb-2">Detail Images:</label>

                                <div id="detailImagesCarousel" class="carousel slide position-relative"
                                    data-bs-ride="carousel">
                                    <div class="carousel-inner text-center">
                                        @foreach ($data['profile']['battery_images'] as $index => $img)
                                            <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                                <div class="position-relative d-inline-block">
                                                    <img src="{{ asset('storage/' . $img['image_path']) }}"
                                                        alt="Detail Image" class="img-fluid image-thumb"
                                                        style="max-height:200px;" loading="lazy"
                                                        onerror="this.onerror=null;this.src='https://placehold.co/300';">
                                                    <button type="button"
                                                        class="btn btn-sm btn-danger btn-delete-image position-absolute top-0 end-0 m-2"
                                                        style="z-index:2;" title="Delete Image"
                                                        data-image-id="{{ $img['id'] }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                    <script>
                                                        $(document).on('click', '.btn-delete-image', function(e) {
                                                            e.preventDefault();
                                                            let btn = $(this);
                                                            let imageId = btn.data('image-id');
                                                            Swal.fire({
                                                                title: 'Are you sure?',
                                                                text: 'Do you want to delete this image?',
                                                                icon: 'warning',
                                                                showCancelButton: true,
                                                                confirmButtonColor: '#d33',
                                                                cancelButtonColor: '#3085d6',
                                                                confirmButtonText: 'Yes, delete it!',
                                                                cancelButtonText: 'Cancel'
                                                            }).then((result) => {
                                                                if (result.isConfirmed) {
                                                                    $.ajax({
                                                                        url: "{{ url('battery/image/delete') }}",
                                                                        type: "POST",
                                                                        data: {
                                                                            _token: "{{ csrf_token() }}",
                                                                            id: imageId
                                                                        },
                                                                        success: function(response) {
                                                                            btn.closest('.carousel-item').remove();
                                                                            Swal.fire('Deleted!', 'Image has been deleted.', 'success');
                                                                        },
                                                                        error: function(xhr) {
                                                                            Swal.fire('Error!', 'Failed to delete image.', 'error');
                                                                        }
                                                                    });
                                                                }
                                                            });
                                                        });
                                                    </script>
                                                </div>
                                                @if (!empty($img['image_type']))
                                                    <div class="mt-2">
                                                        <span class="badge bg-secondary">{{ $img['image_type'] }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>

                                    <button class="carousel-control-prev" type="button"
                                        data-bs-target="#detailImagesCarousel" data-bs-slide="prev">
                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">Previous</span>
                                    </button>
                                    <button class="carousel-control-next" type="button"
                                        data-bs-target="#detailImagesCarousel" data-bs-slide="next">
                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">Next</span>
                                    </button>

                                    <div class="carousel-indicators mt-2">
                                        @foreach ($data['profile']['battery_images'] as $index => $img)
                                            <button type="button" data-bs-target="#detailImagesCarousel"
                                                data-bs-slide-to="{{ $index }}"
                                                class="{{ $index === 0 ? 'active' : '' }}"
                                                aria-current="{{ $index === 0 ? 'true' : 'false' }}"
                                                aria-label="Slide {{ $index + 1 }}">
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endisset

    {{-- Select2 Configurations --}}
    <script>
        $(document).ready(function() {
            $('#brand').select2({
                placeholder: "Enter battery brand"
            });

            $("#brand").on("select2:select", function(e) {
                if (e.params.data.id === "new") {
                    $("#brand-new-group").show();
                    $("#brand-new").attr("required", true);
                } else {
                    $("#brand-new-group").hide();
                    $("#brand-new").attr("required", false);
                }
            });

            $('#subbrand-category').select2({
                placeholder: "Enter battery subbrand category"
            });

            $("#subbrand-category").on("select2:select", function(e) {
                if (e.params.data.id === "new") {
                    $("#subbrand-category-new-group").show();
                } else {
                    $("#subbrand-category-new-group").hide();
                }
            });

            $('#usagetype').select2({
                placeholder: "Enter battery usage type"
            });

            $("#usagetype").on("select2:select", function(e) {
                if (e.params.data.id === "new") {
                    $("#usagetype-new-group").show();
                } else {
                    $("#usagetype-new-group").hide();
                }
            });


            $('#technology').select2({
                placeholder: "Enter battery technolgy"
            });

            $("#technology").on("select2:select", function(e) {
                if (e.params.data.id === "new") {
                    $("#technology-new-group").show();
                } else {
                    $("#technology-new-group").hide();
                }
            });

            $('#size').select2({
                placeholder: "Enter battery size category"
            });

            $("#size").on("select2:select", function(e) {
                if (e.params.data.id === "new") {
                    $("#size-new-group").show();
                } else {
                    $("#size-new-group").hide();
                }
            });

            $('#battery-type').select2({
                placeholder: "Select battery type"
            });
        });
    </script>

    {{-- Form Handler --}}
    <script>
        let indexUrl = "/battery";

        $(document).ready(function() {
            $("#battery-form").on("submit", function(event) {
                event.preventDefault();

                let mode = $("#btn-save").attr("value"); // update || create
                let url = (mode == "update") ? "/battery/update" : "/battery/store";

                // Obtain submitted form data.
                let formData = new FormData($(this)[0]);

                Swal.fire({
                    title: 'Processing...',
                    text: 'Please wait while we save your data.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Send submit POST request via AJAX.
                sendSubmitRequest(url, formData, function() {
                    // Redirect to index page.
                    goToPage(indexUrl);
                });
            });

            $("#battery-form").on("reset", function() {
                goToPage(indexUrl);
            });
        });
    </script>

    {{-- Click Event Handler --}}
    <script>
        $(document).ready(function() {
            $("#btn-add-row").on("click", function() {
                // Get the count of rows.
                let count = $(".battery-url-list-item").length;

                // Get the last row in list.
                let newRow = $('.battery-url-list-item').last();

                // Check if the last row has been unhidden.
                // If it's been unhidden, clone it.
                if (count >= 1 && !newRow.hasClass("d-none"))
                    newRow = newRow.clone();
                newRow.removeClass('d-none');
                newRow.find('input').val('');

                // Set new id to each elements inside.
                let number;
                newRow.find('*[id]').each(function() {
                    let id = $(this).attr("id");
                    let parts = id.split('-');
                    number = parseInt(parts[parts.length - 1]) + 1;
                    $(this).attr("id", parts[0] + '-' + parts[1] + '-' + number);
                });

                $('#battery-url-list').append(newRow);
            });
        });

        // Attach a click event handler to all delete row buttons.
        $(document).on("click", ".btn-delete-row", function() {
            // Get the count of rows.
            let count = $(".battery-url-list-item").length;

            // Check if count of rows is one ore more.
            // If it's the only row, add d-non instaed of removing it.
            if (count > 1) {
                $(this).closest("li").remove();
            } else {
                let row = $(this).closest("li");
                row.addClass("d-none");
                row.find('input').val('');
            }
        });
    </script>

    {{-- Keyup Event Handler --}}
    <script>
        $(document).ready(function() {
            formatPrice($("#price"), $("#price-warning-number"));

            $('#price').on("keyup", function() {
                formatPrice($("#price"), $("#price-warning-number"));
            });
        });
    </script>
@endsection
