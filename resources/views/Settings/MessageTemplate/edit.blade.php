@extends('template.master')
{{-- @dd($data) --}}

@section('content')
    <div class="card">
        <div class="card-body">
            {{-- Title --}}
            <div class="card-title h5">
                Message Templates
            </div>
            <br>

            {{-- Message --}}
            <form id="message-template-form">
                @csrf

                {{-- Opening Personal Detail --}}
                <div class="form-group local-forms">
                    <label for="personal-detail">Opening Personal Detail Template</label>
                    <textarea type="text" class="form-control" id="opening-personal-detail" name="openingpersonaldetail"
                        placeholder="Enter message template for personal detail">
@isset($data['templates'][0])
{{ $data['templates'][0]['opening_message'] }}
@endisset
</textarea>
                </div>

                {{-- Closing Personal Detail --}}
                <div class="form-group local-forms">
                    <label for="personal-detail">Closing Personal Detail Template</label>
                    <textarea type="text" class="form-control" id="closing-personal-detail" name="closingpersonaldetail"
                        placeholder="Enter message template for personal detail">
@isset($data['templates'][0])
{{ $data['templates'][0]['closing_message'] }}
@endisset
</textarea>
                </div>

                {{-- Opening Product Recommendation --}}
                <div class="form-group local-forms">
                    <label for="product-recommendation">Opening Product Recommendation Display Template</label>
                    <textarea type="text" class="form-control" id="opening-product-recommendation" name="openingproductrecommendation"
                        placeholder="Enter message template for product recommendation">
@isset($data['templates'][1]['opening_message'])
{{ $data['templates'][1]['opening_message'] }}
@endisset
</textarea>
                </div>

                {{-- Closing Product Recommendation --}}
                <div class="form-group local-forms">
                    <label for="product-recommendation">Closing Product Recommendation Display Template</label>
                    <textarea type="text" class="form-control" id="closing-product-recommendation" name="closingproductrecommendation"
                        placeholder="Enter message template for product recommendation">
@isset($data['templates'][1]['closing_message'])
{{ $data['templates'][1]['closing_message'] }}
@endisset
</textarea>
                </div>

                {{-- Opening Checkout Page --}}
                <div class="form-group local-forms">
                    <label for="checkout-page">Opening Checkout Page Template</label>
                    <textarea type="text" class="form-control" id="opening-checkout-page" name="openingcheckoutpage"
                        placeholder="Enter message template for checkout page">
@isset($data['templates'][2]['opening_message'])
{{ $data['templates'][2]['opening_message'] }}
@endisset
</textarea>
                </div>

                {{-- Closing Checkout Page --}}
                <div class="form-group local-forms">
                    <label for="checkout-page">Closing Checkout Page Template</label>
                    <textarea type="text" class="form-control" id="closing-checkout-page" name="closingcheckoutpage"
                        placeholder="Enter message template for checkout page">
@isset($data['templates'][2]['closing_message'])
{{ $data['templates'][2]['closing_message'] }}
@endisset
</textarea>
                </div>

                {{-- Opening Payment Details --}}
                <div class="form-group local-forms">
                    <label for="payment-details">Opening Payment Details Template</label>
                    <textarea type="text" class="form-control" id="opening-payment-details" name="openingpaymentdetails"
                        placeholder="Enter message template for payment details">
@isset($data['templates'][3]['opening_message'])
{{ $data['templates'][3]['opening_message'] }}
@endisset
</textarea>
                </div>

                {{-- Closing Payment Details --}}
                <div class="form-group local-forms">
                    <label for="payment-details">Closing Payment Details Template</label>
                    <textarea type="text" class="form-control" id="closing-payment-details" name="closingpaymentdetails"
                        placeholder="Enter message template for payment details">
@isset($data['templates'][3]['closing_message'])
{{ $data['templates'][3]['closing_message'] }}
@endisset
</textarea>
                </div>

                {{-- Buttons --}}
                <div class="d-flex flex-row-reverse">
                    {{-- Save Button --}}
                    <button type="submit" class="btn btn-success mx-1" id="btn-save">Save Message Templates</button>

                    {{-- Reset Button --}}
                    <button type="reset" class="btn btn-danger mx-1" id="btn-reset">Reset</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $("#message-template-form").on("submit", function(event) {
                event.preventDefault();
                $("#btn-save").prop("disabled", true);
                $("#btn-save").html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...'
                );

                // Obtain submitted form data.
                let formData = new FormData($(this)[0]);

                // Send submit POST request via AJAX.
                sendSubmitRequest("/template/message/update", formData);

                $("#btn-save").prop("disabled", false);
                $("#btn-save").html("Save Message Templates");
            });

            $("#message-template-form").on("reset", function() {
                goToPage("/template/message");
            });
        });
    </script>
@endsection
