@extends('template.master')

@section('content')
    <style>
        .select2-container--open {
            z-index: 1100;
        }
    </style>

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
                Promo
            </div>
            <br>

            {{-- Form --}}
            <form id="promo-form">
                @csrf

                {{-- Name & Period --}}
                <div class="row">
                    {{-- Name --}}
                    <div class="col">
                        <div class="form-group local-forms">
                            <label for="name">Name <span class="login-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name"
                                placeholder="Enter promo name" required
                                @if (isset($data['profile'])) value="{{ $data['profile']['name'] }}" @endif>
                        </div>
                    </div>

                    {{-- Period Start --}}
                    <div class="col">
                        <div class="form-group local-forms">
                            <label for="period-start">Period Start<span class="login-danger">*</span></label>
                            <input type="date" class="form-control" id="period-start" name="periodstart" required
                                value=@isset($data['profile']) {{ $data['profile']['period_start'] }} @else {{ date('Y-m-d') }} @endisset>
                        </div>
                    </div>

                    {{-- Period End --}}
                    <div class="col">
                        <div class="form-group local-forms">
                            <label for="period-end">Period End<span class="login-danger">*</span></label>
                            <input type="date" class="form-control" id="period-end" name="periodend" required
                                value=@isset($data['profile']) {{ $data['profile']['period_end'] }} @else {{ date('Y-m-d') }} @endisset>
                        </div>
                    </div>
                </div>

                {{-- Details --}}
                <table class="table mb-2" id="table-battery-detail">
                    {{-- Header --}}
                    <thead>
                        <tr>
                            <td colspan="4" class="h5 text-center">
                                Item
                                <button type="button" id="btn-add-battery"
                                    class="btn btn-primary btn-sm rounded-circle mx-2" data-bs-toggle="modal"
                                    data-bs-target="#battery-modal">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </td>
                        </tr>
                    </thead>

                    {{-- Body (Items) --}}
                    <tbody>
                        @php
                            $batteries = isset($data['profile']['batteries']) ? $data['profile']['batteries'] : [''];
                            $counter = 1;
                        @endphp

                        @foreach ($batteries as $battery)
                            <tr class="table-battery-detail-row d-none">
                                {{-- Name --}}
                                <td>
                                    @php
                                        $targets = ["battery-price-$counter"];
                                        $encodedTargets = json_encode($targets);
                                    @endphp

                                    @isset($data['profile'])
                                        <input type="text" class="form-control" required
                                            @isset($data['profile']['batteries']) readonly @endisset
                                            @isset($data['profile']['batteries']) value="{{ $battery['battery_name'] }}" @endisset>
                                    @else
                                        @component('components.autocomplete', [
                                            'id' => "battery-name-$counter",
                                            'class' => 'battery-name',
                                            'value' => isset($data['profile']['batteries']) ? $battery['battery_name'] : '',
                                            'name' => 'batteriesname[]',
                                            'nameHiddenId' => 'batteriesid[]',
                                            'url' => '/battery/get/',
                                            'placeholder' => 'Enter item name',
                                            'targets' => $encodedTargets,
                                            'callback' => 'calculateTotal',
                                        ])
                                        @endcomponent
                                    @endisset
                                </td>

                                {{-- Discount --}}
                                <td>
                                    <input type="text" class="form-control battery-discount"
                                        id="battery-discount-{{ $counter }}" name="batteriesdisc[]"
                                        placeholder="Enter battery discount"
                                        @isset($data['profile']['batteries'])value="{{ $battery['discount'] }}" @endisset>
                                </td>

                                {{-- Price --}}
                                <td>
                                    <div class="row">
                                        <div class="col">
                                            <div class="input-group">
                                                <span class="input-group-text border-end">IDR</span>
                                                <input type="text" class="form-control text-end battery-price"
                                                    id="battery-price-{{ $counter }}" name="batteriesprice[]"
                                                    placeholder="Enter item price" required
                                                    @isset($data['profile']['batteries']) readonly @endisset
                                                    @isset($data['profile']['batteries']) value="{{ $battery['net_price'] }}" @endisset>
                                            </div>

                                            <div class="col-sm-2">
                                                <button type="button" class="btn btn-danger btn-sm disabled btn-delete-row"
                                                    title="Delete Item"><i class="fas fa-xmark"></i></button>
                                            </div>
                                        </div>
                                </td>

                                {{-- Hidden Inputs --}}
                                @isset($data['profile']['batteries'])
                                    <input type="hidden" name="detailid[]" value="{{ $battery['id'] }}">
                                @endisset
                            </tr>

                            @php
                                $counter++;
                            @endphp
                        @endforeach
                    </tbody>
                </table>
                <br>

                {{-- Hidden Inputs --}}
                <input type="hidden" id="id" name="id"
                    @if (isset($data['profile'])) value="{{ $data['profile']['id'] }}" @endif>

                {{-- Buttons --}}
                <div class="d-flex flex-row-reverse">
                    {{-- Create or Update Button --}}
                    <button type="submit" class="btn btn-success mx-1" id="btn-save"
                        @isset($data['profile']) value="update">
                    Update
                    @else
                    value="create">
                    Create @endisset
                        Promo </button>

                        {{-- Cancel Button --}}
                        <button type="reset" class="btn btn-danger mx-1" id="btn-cancel">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Battery --}}
    <div id="battery-modal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                {{-- Header --}}
                <div class="modal-header">
                    <h4 class="modal-title" id="standard-modalLabel">Add Batteries</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                {{-- Body --}}
                <div class="modal-body">
                    {{-- Size Category --}}
                    <div class="form-group local-forms">
                        <label for="size">Size Category</label>
                        <select class="form-control" id="size">
                            <option></option>
                            @foreach ($data['battery_categories'] as $size)
                                <option value="{{ $size['id'] }}">
                                    {{ $size['name'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Battery List --}}
                    <ul class="list-group" id="list-battery"></ul>
                </div>

                {{-- Footer --}}
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="btn-add-modal">Add batteries</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Select2 Configurations --}}
    <script>
        $(document).ready(function() {
            $('#size').select2({
                placeholder: "Enter battery size category"
            });

            $("#size").on("select2:select", function(e) {
                // Empty all current items in list.
                $('#list-battery').empty();

                // Get selected size category.
                let sizeId = e.params.data.id;

                // Show the list of batteries of the selected size category.
                $.ajax({
                    url: "/battery/get/size/" + sizeId,
                    success: function(data) {
                        data.forEach(battery => {
                            // Make the list item for battery.
                            let item = document.createElement('li');
                            item.className = 'list-group-item';
                            item.innerHTML = battery.name;

                            // Append the created list item into list.
                            $("#list-battery").append(item);
                        });
                    }
                });
            });
        });
    </script>

    {{-- Form Hanlder --}}
    <script>
        let indexUrl = "/promo";

        $(document).ready(function() {
            $("#promo-form").on("submit", function(event) {
                event.preventDefault();

                let mode = $("#btn-save").attr("value"); // update || create
                let url = (mode == "update") ? "/promo/update" : "/promo/store";

                // Obtain submitted form data.
                let formData = new FormData($(this)[0]);

                // Send submit POST request via AJAX.
                sendSubmitRequest(url, formData, function() {
                    // Redirect to index page.
                    goToPage(indexUrl);
                });
            });

            $("#promo-form").on("reset", function() {
                goToPage(indexUrl);
            });
        });
    </script>

    {{-- Modal Handler --}}
    <script>
        $('#battery-modal').on('shown.bs.modal', function() {
            $("#size").val(null).trigger('change');
            $('#list-battery').empty();
        });
    </script>

    {{-- Click Event Handler --}}
    <script>
        $(document).ready(function() {
            $("#btn-add-modal").on('click', function() {
                //
            });
        });
    </script>
@endsection
