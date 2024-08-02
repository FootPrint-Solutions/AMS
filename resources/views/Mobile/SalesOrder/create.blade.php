{{-- mobile version --}}
<style>
    #title {
        font-weight: 700;
        font-size: 16px;
        line-height: 24px;
        color: #000000;
    }

    #btn-add-detail-mobile {
        background-color: rgb(95, 211, 169);
        color: rgb(256, 256, 256);
        width: 24px;
        height: 24px;
        text-align: center;
        justify-content: center;
    }


    .text-very-small {
        font-size: 0.7em;
    }

    .list-dash-border {
        border-bottom: 1px dashed #DCDCDC;
    }

    #card-total {
        width: 100%;
        background: #DCDCDC;
        border-radius: 0px 0px 7px 7px;
    }

    #card-grand-total {
        width: 100%;
        background: #BCEBEC;
        border-radius: 7px 7px 7px 7px;
    }

    #btn-add-mobile {
        color: rgb(256, 256, 256);
        background-color: rgb(95, 211, 169);
        height: 50px;
        border-radius: 20px;
    }
</style>

<div class="d-block d-md-none mb-3">
    {{-- Title --}}
    <div class="mb-4" id="title">Add New Sales Order</div>

    <form action="">
        {{-- Date --}}
        <div class="form-group local-forms mb-4">
            <label for="date">Date <span class="login-danger">*</span></label>
            <input type="date" name="date" id="date" class="form-control">
        </div>

        {{-- Customer --}}
        <div class="form-group local-forms mb-4">
            <label for="customer">Customer <span class="login-danger">*</span></label>
            <select class="form-control" id="customer" name="customer" required>
                <option></option>
                @foreach ($data['customers'] as $customer)
                    <option value="{{ $customer['id'] }}" @if (isset($data['profile']) && $data['profile']['customer_id'] == $customer['id']) selected @endif>
                        {{ $customer['name'] }}</option>
                @endforeach
            </select>
        </div>

        {{-- Address --}}
        <div class="form-group local-forms mb-4">
            <label for="address">Address <span class="login-danger">*</span></label>
            <input type="text" name="address" id="address" class="form-control"
                placeholder="Enter customer address">
        </div>

        {{-- Vehicle --}}
        <div class="form-group local-forms mb-4">
            <label for="vehicle">Vehicle <span class="login-danger">*</span></label>
            <select class="form-control" id="vehicle" name="vehicle" required>
                <option></option>
                @foreach ($data['vehicles'] as $vehicle)
                    <option value="{{ $vehicle['id'] }}" @if (isset($data['profile']) && $data['profile']['vehicle_id'] == $vehicle['id']) selected @endif>
                        {{ $vehicle['name'] }}</option>
                @endforeach
            </select>
        </div>

        {{-- Shop --}}
        <div class="form-group local-forms mb-4">
            <label for="shop">Shop <span class="login-danger">*</span></label>
            <select class="form-control" id="shop" name="shop" required>
                <option></option>
                @foreach ($data['shops'] as $shop)
                    <option value="{{ $shop['id'] }}" @if (isset($data['profile']) && $data['profile']['distributor_shop_id'] == $shop['id']) selected @endif>
                        {{ $shop['distributor']['name'] . ' - ' . $shop['name'] }}</option>
                @endforeach
            </select>
        </div>

        {{-- Technician --}}
        <div class="form-group local-forms mb-4">
            <label for="technician">Technician</label>
            <select class="form-control" id="technician" name="technician">
                <option></option>
                <option disabled>Select a distributor to select a technician</option>
            </select>
            @isset($data['profile'])
                <input type="hidden" id="technician_id" value="{{ $data['profile']['distributor_shop_technician_id'] }}">
            @endisset
        </div>

        {{-- Add Item --}}
        <div class="mb-1" id="title">Add Item <button class="btn rounded-circle" id="btn-add-detail-mobile"><span
                    class="material-icons text-very-small">add</span></button></div>

        {{-- List Details --}}
        <ul class="list-group list-group-flush">
            <li class="list-group-item list-dash-border">
                <div class="row">
                    <div class="col-8">
                        <div class="row">
                            <p class="fw-bold text-truncate">AMARON Quanta 9</p>
                            <p class="text-muted text-very-small">code123123</p>
                        </div>
                    </div>

                    <div class="col-4">
                        <div class="row">
                            <div class="col">
                                <div class="d-flex justify-content-between">
                                    <span class="badge bg-warning">Tax 11%</span>
                                    <span class="badge bg-danger">Disc 10%</span>
                                </div>
                            </div>
                            <div class="col-12">
                                <p class="fw-bold">Rp100000</p>
                            </div>
                        </div>
                    </div>
                </div>
            </li>

            <li class="list-group-item list-dash-border">
                <div class="row">
                    <div class="col-8">
                        <div class="row">
                            <p class="fw-bold text-truncate">AMARON Quanta 9</p>
                            <p class="text-muted text-very-small">code123123</p>
                        </div>
                    </div>

                    <div class="col-4">
                        <div class="row">
                            <div class="col">
                                <div class="d-flex justify-content-between">
                                    <span class="badge bg-warning">Tax 11%</span>
                                    <span class="badge bg-danger">Disc 10%</span>
                                </div>
                            </div>
                            <div class="col-12">
                                <p class="fw-bold">Rp100000</p>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
        </ul>

        {{-- Total --}}
        <div class="card" id="card-total">
            <div class="card-body">
                <p class="card-text text-center"><span class="fw-bold">Total</span> : Rp10101010</p>
            </div>
        </div>

        {{-- Discount & Grand Total --}}
        <div class="card" id="card-grand-total">
            <div class="card-body">
                {{--  --}}
                <div class="row">
                    <div class="col-4"><span class="fw-bold">Invoice Discount</span></div>
                    <div class="col-5"><input type="text" class="form-control" name="discount"></div>
                    <div class="col-2">Toggle</div>
                </div>

                <hr>

                {{--  --}}
                <div class="row">
                    <div class="col-4"></div>
                    <div class="col-7"><span class="fw-bold">Grand Total</span> : Rp10101010</div>
                </div>
            </div>
        </div>

        {{-- Button --}}
        <button class="btn btn-block" id="btn-add-mobile">Create New Sales Order</button>
    </form>
</div>

<script>
    $(function() {});
</script>
