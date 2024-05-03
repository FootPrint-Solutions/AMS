@extends('template.master')

@section('content')
    <style>
        .breadcrumb-item+.breadcrumb-item::before {
            float: left;
            padding-right: var(--bs-breadcrumb-item-padding-x);
            color: var(--bs-breadcrumb-divider-color);
            content: var(--bs-breadcrumb-divider, "/");
        }
    </style>
    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <div class="page-sub-header">
                    <h3 class="page-title">Welcome @auth {{ Auth::user()->name }} @endauth !</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="">Home</a></li>
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <!-- Overview Section -->
    <div class="row">
        <div class="col-xl-3 col-sm-6 col-12 d-flex">
            <div class="card bg-comman w-100">
                <div class="card-body">
                    <div class="db-widgets d-flex justify-content-between align-items-center">
                        <div class="db-info">
                            <h6>Customer</h6>
                            <h3>{{ $data['NumberOfCustomer'] }}</h3>
                        </div>
                        <div class="db-icon">
                            <i class="fa fa-users text-dark"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6 col-12 d-flex">
            <div class="card bg-comman w-100">
                <div class="card-body">
                    <div class="db-widgets d-flex justify-content-between align-items-center">
                        <div class="db-info">
                            <h6>Vehicle</h6>
                            <h3>{{ $data['NumberOfVehicle'] }}</h3>
                        </div>
                        <div class="db-icon">
                            <i class="fa fa-car text-dark"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6 col-12 d-flex">
            <div class="card bg-comman w-100">
                <div class="card-body">
                    <div class="db-widgets d-flex justify-content-between align-items-center">
                        <div class="db-info">
                            <h6>Battery</h6>
                            <h3>{{ $data['NumberOfBattery'] }}</h3>
                        </div>
                        <div class="db-icon">
                            <i class="fa fa-car-battery text-dark"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6 col-12 d-flex">
            <div class="card bg-comman w-100">
                <div class="card-body">
                    <div class="db-widgets d-flex justify-content-between align-items-center">
                        <div class="db-info">
                            <h6>Revenue</h6>
                            <h3>Rp. {{ number_format($data['TotalRevenue'], 0, ',', '.') }}</h3>
                        </div>
                        <div class="db-icon">
                            <i class="fa fa-dollar-sign text-dark"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Overview Section -->
@endsection
