<div>
    <div class="container my-5">
        <div class="row">
            <div class="col-12">
                <!-- Invoice Header -->
                <div class="d-flex justify-content-between align-items-center bg-dark-blue-only text-white p-3 rounded">
                    {{-- button rounded with icon invoice --}}
                    <button class="btn rounded-pill text-light" style="background-color:#60D3AA;">
                        <i class="fas fa-file-invoice"></i>
                    </button>
                    {{-- <h5 class="mb-0">INVOICE</h5> --}}
                    <span>Invoice Number : <strong id="invoice_number_payment_details_mobile">AK240700005</strong></span>
                </div>

                <!-- Billed To Section -->
                <div class="card mt-3 bg-grey-custom">
                    <div class="card-body">
                        <h5 class="card-title">Billed to</h5>
                        <p class="mb-0" id="full_name_customer_payment_details_mobile"></p>
                        <p class="mb-0" id="number_customer_payment_details_mobile"></p>
                        <p class="mb-0" id="email_customer_payment_details_mobile"></p>
                        <p class="mb-0" id="address_customer_payment_details_mobile"></p>
                    </div>
                </div>

                <!-- Products Section -->
                <div class="mt-3" id="battery-payment-details-mobile">

                </div>

                <!-- Grandtotal Section -->
                <div class="d-flex justify-content-between align-items-center bg-light p-3 mt-3 rounded">
                    <h5 class="mb-0">Grandtotal :</h5>
                    <h5 class="mb-0" id="grand_total_payment_details_mobile"></h5>
                </div>
            </div>
        </div>
    </div>
</div>
