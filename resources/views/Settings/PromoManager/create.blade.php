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
                                    class="btn btn-primary btn-sm rounded-circle mx-2"><i class="fas fa-plus"></i></button>
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
                            <tr class="table-battery-detail-row">
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
                                    <input type="text" class="form-control battery-code" id="battery-production-code"
                                        name="batteriesdisc[]" placeholder="Enter item production code"
                                        @isset($data['profile']['batteries'])value="{{ $battery['battery_production_code'] }}" @endisset>
                                </td>

                                {{-- Price --}}
                                <td>
                                    <div class="row">
                                        @if (!isset($data['profile']))
                                            <div class="col">
                                        @endif

                                        <div class="input-group">
                                            <span class="input-group-text border-end">IDR</span>
                                            <input type="text" class="form-control text-end battery-price"
                                                id="battery-price-{{ $counter }}" name="batteriesprice[]"
                                                placeholder="Enter item price" required
                                                @isset($data['profile']['batteries']) readonly @endisset
                                                @isset($data['profile']['batteries']) value="{{ $battery['battery_price'] }}" @endisset>
                                        </div>

                                        @if (!isset($data['profile']))
                                    </div>

                                    <div class="col-sm-2">
                                        <button type="button" class="btn btn-danger btn-sm disabled btn-delete-row"
                                            title="Delete Item"><i class="fas fa-xmark"></i></button>
                                    </div>
                        @endif
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
@endsection
