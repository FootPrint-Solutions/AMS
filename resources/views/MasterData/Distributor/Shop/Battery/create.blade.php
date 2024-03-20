@extends('template.master')

@section('content')
    {{-- Form --}}
    <div class="card">
        <div class="card-body">
            {{-- Title --}}
            <div class="card-title h2">
                @isset($data['profile'])
                    Add New
                @else
                    Edit
                @endisset
                Battery Details for Shop
            </div>
            <br>

            {{-- Form --}}
            <form id="distributor-shop-battery-form">
                @csrf

                {{-- Name & Distributor --}}
                <div class="row">
                    {{-- Distributor --}}
                    <div class="col">
                        <div class="form-group local-forms">
                            <label for="distributor-name">Distributor</label>
                            <input type="text" class="form-control" id="distributor-name" name="distributorname"
                                value="{{ $data['distributor']['name'] }}" readonly>
                        </div>
                    </div>

                    {{-- Shop --}}
                    <div class="col">
                        <div class="form-group local-forms">
                            <label for="shop-name">Shop</label>
                            <input type="text" class="form-control" id="shop-name" name="shopname"
                                value="{{ $data['shop']['name'] }}" readonly>
                            <input type="hidden" name="shopid" value="{{ $data['shop']['id'] }}">
                        </div>
                    </div>
                </div>

                {{-- Battery --}}
                <div class="row">
                    {{-- Battery --}}
                    <div class="col">
                        <div class="form-group local-forms">
                            <label for="battery">Battery <span class="login-danger">*</span></label>
                            <select class="form-control" id="battery" name="battery">
                                <option></option>
                                @foreach ($data['batteries'] as $battery)
                                    <option value="{{ $battery['id'] }}" @if (isset($data['profile']) && $data['profile']['battery_id'] == $battery['id']) selected @endif>
                                        {{ $battery['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Price --}}
                    <div class="col">
                        <div class="form-group local-forms">
                            <label for="price">Price Retail <span class="login-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text border-end">IDR</span>
                                <input type="text" min="0" class="form-control" id="price" name="price"
                                    placeholder="Enter battery price retail" required
                                    @if (isset($data['profile'])) value="{{ $data['profile']['price'] }}" @endif>
                            </div>
                            <small id="price-warning-number" class="form-text text-danger" style="display: none;">Please
                                enter a valid numeric value for the price.</small>
                        </div>
                    </div>

                    {{-- URL --}}
                    <div class="col">
                        <div class="form-group local-forms">
                            <label for="url">Battery URL</label>
                            <input type="url" pattern="https?://.+" class="form-control" id="url" name="url"
                                placeholder="Enter battery product url"
                                @if (isset($data['profile'])) value="{{ $data['profile']['url'] }}" @endif>
                        </div>
                    </div>
                </div>

                {{-- Hidden Inputs --}}
                @isset($data['profile'])
                    <input type="hidden" name="id" value="{{ $data['profile']['id'] }}">
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
        </div>
    </div>

    {{-- Address Modal --}}
    @include('maps.addressmodal')

    <script>
        let indexUrl = "/distributor/shop";

        $(document).ready(function() {
            formatPrice($("#price"), $("#price-warning-number"));

            $('#battery').select2({
                placeholder: "Select battery product"
            });

            $("#distributor-shop-battery-form").on("submit", function(event) {
                event.preventDefault();

                let mode = $("#btn-save").attr("value"); // update || create
                let url = (mode == "update") ? "/distributor/shop/battery/update" :
                    "/distributor/shop/battery/store";

                // Obtain submitted form data.
                let formData = new FormData($(this)[0]);

                // Send submit POST request via AJAX.
                sendSubmitRequest(url, formData, function() {
                    // Redirect to index page.
                    goToPage(indexUrl);
                });
            });

            $("#distributor-shop-battery-form").on("reset", function() {
                goToPage(indexUrl);
            });

            $('#price').on("keyup", function() {
                formatPrice($("#price"), $("#price-warning-number"));
            });
        });
    </script>
@endsection
