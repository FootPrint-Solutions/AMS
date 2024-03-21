@extends('template.master')

@section('content')
    <div class="card">
        <div class="card-body">
            {{-- Title --}}
            <div class="card-title h5">
                Message Templates
            </div>
            <br>

            <form id="message-template-form">
                <div class="form-group local-forms">
                    <label for="personal-detail">Personal Detail Template</label>
                    <textarea type="text" class="form-control" id="personal-detail" name="personaldetail"
                        placeholder="Enter message template for personal detail"></textarea>
                </div>

                <div class="form-group local-forms">
                    <label for="product-recommendation">Product Recommendation Display Template</label>
                    <textarea type="text" class="form-control" id="product-recommendation" name="productrecommendation"
                        placeholder="Enter message template for product recommendation"></textarea>
                </div>

                <div class="form-group local-forms">
                    <label for="checkout-page">Checkout Page Template</label>
                    <textarea type="text" class="form-control" id="checkout-page" name="checkoutpage"
                        placeholder="Enter message template for checkout page"></textarea>
                </div>

                <div class="form-group local-forms">
                    <label for="payment-details">Payment Details Template</label>
                    <textarea type="text" class="form-control" id="payment-details" name="paymentdetails"
                        placeholder="Enter message template for payment details"></textarea>
                </div>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function() {});
    </script>
@endsection
