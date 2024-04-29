@extends('template.master')

@section('content')
    {{-- Form --}}
    <div class="card">
        <div class="card-body">
            {{-- Title --}}
            <div class="card-title h5">
                @if (isset($data['profile']))
                    Edit Vehicle
                @else
                    Add New Vehicle
                @endif
            </div>
            <br>

            {{-- Form --}}
            <form id="vehicle-form">
                @csrf

                {{-- Name --}}
                <div class="form-group local-forms">
                    <label for="name">Name <span class="login-danger">*</span></label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="Enter vehicle name"
                        required @if (isset($data['profile'])) value="{{ $data['profile']['name'] }}" @endif>
                </div>

                {{-- URL --}}
                <div class="form-group local-forms">
                    <label for="url">URL</label>
                    <input type="url" pattern="https?://.+" class="form-control" id="url" name="url"
                        placeholder="Enter vehicle url link"
                        @if (isset($data['profile'])) value="{{ $data['profile']['url'] }}" @endif>
                </div>

                {{-- Brand --}}
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

                {{-- Quick Add New Brand --}}
                <div id="brand-new-group" class="form-group local-forms" style="display: none;">
                    <label for="brand-new">New Brand <span class="login-danger">*</span></label>
                    <input type="text" class="form-control" id="brand-new" name="newbrand">
                </div>

                {{-- Battery --}}
                <div class="row">
                    {{-- Primary Battery --}}
                    <div class="col">
                        <div class="form-group local-forms">
                            <label for="battery-primary">Battery Size Category (primary) <span
                                    class="login-danger">*</span></label>
                            <select class="form-control" id="battery-primary" name="batteryprimary" required>
                                <option></option>
                                @foreach ($data['battery_size_categories'] as $battery)
                                    <option value="{{ $battery['id'] }}" @if (isset($data['primary_battery']) && $data['primary_battery'] == $battery['id']) selected @endif>
                                        {{ $battery['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Secondary Battery --}}
                    <div class="col">
                        <div class="form-group local-forms">
                            <label for="battery-secondary">Battery Size Category (alternative)</label>
                            <select class="form-control" id="battery-secondary" name="batterysecondary[]"
                                multiple="multiple">
                                @foreach ($data['battery_size_categories'] as $battery)
                                    <option value="{{ $battery['id'] }}" @if (isset($data['secondary_batteries']) && in_array($battery['id'], $data['secondary_batteries'])) selected @endif>
                                        {{ $battery['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Hidden Inputs --}}
                <input type="hidden" id="id" name="id"
                    @if (isset($data['profile'])) value="{{ $data['profile']['id'] }}" @endif>

                {{-- Buttons --}}
                <div class="d-flex flex-row-reverse">
                    {{-- Create Button --}}
                    <button type="submit" class="btn btn-success mx-1" id="btn-save"
                        @if (isset($data['profile'])) value="update">
                        Update
                    @else
                        value="create">
                        Create @endif
                        Vehicle </button>

                        {{-- Cancel Button --}}
                        <button type="reset" class="btn btn-danger mx-1" id="btn-cancel">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let indexUrl = "/vehicle";

        $(document).ready(function() {
            $('#brand').select2({
                placeholder: "Enter vehicle brand"
            });

            $('#battery-primary').select2({
                placeholder: "Enter vehicle primary battery size category"
            });

            $('#battery-secondary').select2({
                placeholder: "Enter vehicle secondary battery size category"
            });

            $("#brand").on("select2:select", function(e) {
                if (e.params.data.id === "new") {
                    $("#brand-new-group").show();
                    $("#brand-new-group").attr("required", true);
                } else {
                    $("#brand-new-group").hide();
                    $("#brand-new-group").attr("required", false);
                }
            });

            $("#vehicle-form").on("submit", function(event) {
                event.preventDefault();

                let mode = $("#btn-save").attr("value"); // update || create
                let url = (mode == "update") ? "/vehicle/update" : "/vehicle/store";

                // Obtain submitted form data.
                let formData = new FormData($(this)[0]);

                // Send submit POST request via AJAX.
                sendSubmitRequest(url, formData, function() {
                    // Redirect to index page.
                    goToPage(indexUrl);
                });
            });

            $("#vehicle-form").on("reset", function() {
                goToPage(indexUrl);
            });
        });
    </script>
@endsection
