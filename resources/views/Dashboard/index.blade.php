@extends('template.master')

@section('content')
    <style>
        .breadcrumb-item+.breadcrumb-item::before {
            float: left;
            padding-right: var(--bs-breadcrumb-item-padding-x);
            color: var(--bs-breadcrumb-divider-color);
            content: var(--bs-breadcrumb-divider, "/");
        }

        #table-promo tbody tr:hover {
            cursor: pointer;
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

    {{-- Promos --}}
    <div class="card flex-fill student-space comman-shadow">
        <div class="card-header d-flex align-items-center">
            <h5 class="card-title">Currently Active Promo</h5>
            <ul class="chart-list-out student-ellips">
                <li class="star-menus" id="star-menus-promo">
                    <div class="container d-none" id="item-menus-promo">
                        <button class="btn btn-sm btn-primary mx-1" id="btn-promo-limited">Limited</button>
                        <button class="btn btn-sm btn-info mx-1" id="btn-promo-unlimited">Unlimited</button>
                    </div>
                    <input type="hidden" id="promo-filter" value="limited">

                    {{-- <a href="javascript:;"><i class="fas fa-ellipsis-v"></i></a> --}}
                </li>
            </ul>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped" id="table-promo">
                    <thead>
                        <tr>
                            <th scope="col" class="table-col-no">#</th>
                            <th scope="col">Name</th>
                            <th scope="col">Discounted Batteries</th>
                            <th scope="col">Valid Until</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    {{-- DataTables configuration --}}
    <script>
        var promoTable;

        $(document).ready(function() {
            // DataTables configuration
            promoTable = $("#table-promo").DataTable({
                lengthMenu: [
                    [3, 5]
                ],
                responsive: true,
                processing: true,
                serverSide: true,
                order: [],
                ajax: {
                    url: "/promo/show/dashboard",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        type: 'limited'
                    }
                },
                columnDefs: [{
                    targets: [0],
                    orderable: false
                }],
                dom: "tp",
                rowCallback: function(row, data) {
                    if (data[5])
                        $('td', row).addClass("table-warning");

                    $(row).click(function() {
                        document.location.href = '/promo/edit/' + data[4];
                    });
                }
            });
        });
    </script>

    {{-- Click Handler Function --}}
    <script>
        $(document).ready(function() {
            // $("#star-menus-promo").on('click', function() {
            //     if ($("#item-menus-promo").hasClass("d-none"))
            //         $("#item-menus-promo").css({
            //             right: '-100%',
            //             opacity: 0
            //         }).removeClass("d-none")
            //         .animate({
            //             right: '0',
            //             opacity: 1
            //         }, 'fast');
            //     else
            //         $("#item-menus-promo").animate({
            //             right: '-100%',
            //             opacity: 0
            //         }, 'fast', function() {
            //             $(this).addClass("d-none");
            //         });
            // });
        });
    </script>
@endsection
