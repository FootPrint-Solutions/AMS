@extends('template.master')

@section('content')
    <div class="card shadow-lg mb-4">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title mb-0">Commission List</h3>
                </div>
                <div class="col-auto ms-auto text-end">
                    <a href="{{ route('commission.create') }}" id="btn-add" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Add New Commission
                    </a>
                </div>
            </div>
        </div>

        <div class="card-body">
            <form id="filter-form" class="row align-items-center mb-3">
                <label class="col-md-1 col-form-label fw-bold">Date</label>
                <div class="col-md-4">
                    <div class="row">
                        <div class="col-5 pe-0">
                            <input type="date" class="form-control" id="input-billing-date-start">
                        </div>
                        <div class="col-2 text-center px-0 align-self-center">to</div>
                        <div class="col-5 ps-0">
                            <input type="date" class="form-control" id="input-billing-date-end">
                        </div>
                    </div>
                </div>

                <label class="col-md-1 col-form-label fw-bold">Status</label>
                <div class="col-md-2">
                    <select class="form-select" id="billing-status-filter">
                        <option value="all">All</option>
                        <option value="draft">Draft</option>
                        <option value="posted">Posted</option>
                    </select>
                </div>

                <label class="col-md-1 col-form-label fw-bold">Distributor Shop</label>
                <div class="col-md-2">
                    <select class="form-select" id="distributor-shop-filter">
                        <option value="all">All</option>
                        @foreach ($data['DistributorShops'] as $shop)
                            <option value="{{ $shop['id'] }}">{{ $shop['name'] }}</option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-lg mb-4">
        <div class="card-body">
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-striped" id="table-billing" style="width:100%">
                    <thead>
                        <tr>
                            <th></th>
                            <th>#</th>
                            <th>Commission Number</th>
                            <th>Date</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection
