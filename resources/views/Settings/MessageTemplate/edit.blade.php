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

            <form id="message-template-form">
                @csrf

                {{-- Personal Detail --}}
                <div class="form-group local-forms">
                    <label for="personal-detail">Personal Detail Template</label>
                    <textarea type="text" class="form-control" id="personal-detail" name="personaldetail"
                        placeholder="Enter message template for personal detail">
@isset($data['templates']['personal_details'])
{{ $data['templates']['personal_details'] }}
@endisset
</textarea>
                </div>

                {{-- Product Recommendation --}}
                <div class="form-group local-forms">
                    <label for="product-recommendation">Product Recommendation Display Template</label>
                    <textarea type="text" class="form-control" id="product-recommendation" name="productrecommendation"
                        placeholder="Enter message template for product recommendation">
@isset($data['templates']['product_recommendation'])
{{ $data['templates']['product_recommendation'] }}
@endisset
</textarea>
                </div>

                {{-- Checkout Page --}}
                <div class="form-group local-forms">
                    <label for="checkout-page">Checkout Page Template</label>
                    <textarea type="text" class="form-control" id="checkout-page" name="checkoutpage"
                        placeholder="Enter message template for checkout page">
@isset($data['templates']['checkout_page'])
{{ $data['templates']['checkout_page'] }}
@endisset
</textarea>
                </div>

                {{-- Payment Details --}}
                <div class="form-group local-forms">
                    <label for="payment-details">Payment Details Template</label>
                    <textarea type="text" class="form-control" id="payment-details" name="paymentdetails"
                        placeholder="Enter message template for payment details">
@isset($data['templates']['payment_details'])
{{ $data['templates']['payment_details'] }}
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

                // Obtain submitted form data.
                let formData = new FormData($(this)[0]);

                // Send submit POST request via AJAX.
                sendSubmitRequest("/template/message/update", formData);
            });

            $("#message-template-form").on("reset", function() {
                goToPage("/template/message");
            });
        });
    </script>
@endsection
