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
                Payment Method
            </div>
            <br>

            {{-- Form --}}
            <form id="payment-method-form">
                @csrf

                {{-- Name --}}
                <div class="row">
                    <div class="form-group local-forms">
                        <label for="name">Name <span class="login-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name"
                            placeholder="Enter payment method name" required
                            @if (isset($data['profile'])) value="{{ $data['profile']['name'] }}" @endif>
                    </div>
                </div>

                {{-- Type --}}
                <div class="row">
                    <div class="form-group local-forms">
                        <label for="type">Type <span class="login-danger">*</span></label>
                        <select class="form-control" id="type" name="type" required>
                            <option value="">Select payment method type</option>
                            <option value="regularpayment" @if (isset($data['profile']) && $data['profile']['type'] == 'regularpayment') selected @endif>Regular Payment
                            </option>
                            <option value="marketplace" @if (isset($data['profile']) && $data['profile']['type'] == 'marketplace') selected @endif>Market Place
                            </option>
                            <option value="paymentgateway" @if (isset($data['profile']) && $data['profile']['type'] == 'paymentgateway') selected @endif>Payment Gateway
                            </option>
                        </select>
                    </div>
                </div>

                {{-- Note --}}
                <div class="row">
                    <div class="form-group local-forms">
                        <label for="note">Note</label>
                        <textarea type="text" class="form-control" id="note" name="note" placeholder="Enter payment method note">
@isset($data['profile'][0])
{{ $data['profile']['note'] }}
@endisset
</textarea>
                    </div>
                </div>

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
                        Payment Method </button>

                        {{-- Cancel Button --}}
                        <button type="reset" class="btn btn-danger mx-1" id="btn-cancel">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Form Hanlder --}}
    <script>
        let indexUrl = "/payment";

        $(document).ready(function() {
            $("#payment-method-form").on("submit", function(event) {
                event.preventDefault();

                let mode = $("#btn-save").attr("value"); // update || create
                let url = (mode == "update") ? "/payment/update" : "/payment/store";

                // Obtain submitted form data.
                let formData = new FormData($(this)[0]);

                // Send submit POST request via AJAX.
                sendSubmitRequest(url, formData, function() {
                    // Redirect to index page.
                    goToPage(indexUrl);
                });
            });

            $("#payment-method-form").on("reset", function() {
                goToPage(indexUrl);
            });
        });
    </script>
@endsection
