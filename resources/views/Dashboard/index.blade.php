@extends('template.master')

@section('content')
    <style>
        .breadcrumb-item+.breadcrumb-item::before {
            float: left;
            padding-right: var(--bs-breadcrumb-item-padding-x);
            color: var(--bs-breadcrumb-divider-color);
            content: "{{ request()->path() }}";
        }

        .card {
            transition: transform .1s;
        }

        .card:hover {
            transform: scale(1.01);
        }

        #table-promo tbody tr:hover {
            cursor: pointer;
            background-color: rgba(192, 192, 192, 0.5)
        }

        .loading-spinner {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        @media (max-width: 991.98px) {
            .card:hover {
                transform: none;
            }

            .page-sub-header {
                margin-bottom: 1rem;
            }

            .page-sub-header .page-title {
                font-size: 1.25rem;
                line-height: 1.4;
            }

            .breadcrumb {
                margin-bottom: 0;
                font-size: 0.85rem;
                flex-wrap: wrap;
            }

            #chart-revenue {
                height: 240px !important;
            }

            #chart-revenue .apexcharts-xaxis-label {
                font-size: 10px;
            }

            .card .card-body {
                padding: 1rem;
            }

            .row .col-xl-3.col-sm-6.col-12.d-flex {
                margin-bottom: 0.75rem;
            }

            .db-widgets {
                gap: 0.75rem;
            }

            .db-info h6 {
                font-size: 0.85rem;
                margin-bottom: 0.25rem;
            }

            .db-info h3 {
                font-size: 1.15rem;
                word-break: break-word;
            }

            .card-header {
                flex-direction: column;
                align-items: flex-start !important;
                gap: 0.75rem;
            }

            .chart-list-out {
                width: 100%;
            }

            #item-menus-promo {
                display: flex;
                width: 100%;
                padding: 0;
            }

            #item-menus-promo .btn {
                flex: 1;
            }

            .table-responsive {
                font-size: 0.9rem;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            #table-promo {
                min-width: 560px;
            }

            #table-promo td,
            #table-promo th {
                white-space: nowrap;
            }
        }

        @media (max-width: 575.98px) {
            .page-sub-header .page-title {
                font-size: 1.05rem;
            }

            .form-label {
                font-size: 0.85rem;
                margin-bottom: 0.35rem;
            }

            #start-date,
            #end-date {
                min-height: 42px;
                font-size: 0.9rem;
            }
        }
    </style>

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @if (auth()->user()->level == 'technician')
    @else
        {{-- Header --}}
        <div class="page-header">
            <div class="row">
                <div class="col-sm-12">
                    <div>
                        <div class="page-sub-header">
                            <h3 class="page-title">Welcome, @auth{{ Auth::user()->name }}@endauth!</h3>

                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="">Home</a></li>
                                <li class="breadcrumb-item active">Dashboard</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Overview Row --}}
        <div>
            <div class="row">
                {{-- add chart revenue --}}
                <div class="col">
                    <div class="card flex-fill w-100 comman-shadow">
                        <div class="card-body">

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="start-date" class="form-label">Start Date</label>
                                    <input type="date" id="start-date" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label for="end-date" class="form-label">End Date</label>
                                    <input type="date" id="end-date" class="form-control">
                                </div>
                            </div>

                            <div class="loading-spinner" id="chart-loading">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                            <div id="chart-revenue" style="height: 300px;"></div>
                        </div>
                    </div>
                </div>
            </div>
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

                <div class="col-xl-3 col-sm-6 col-12 d-flex">
                    <div class="card bg-comman w-100">
                        <div class="card-body">
                            <div class="db-widgets d-flex justify-content-between align-items-center">
                                <div class="db-info">
                                    <h6>Sales Order</h6>
                                    <h3>{{ $data['NumberOfSalesOrder'] }}</h3>
                                </div>
                                <div class="db-icon">
                                    <i class="fa fa-file-invoice text-dark"></i>
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
                                    <h6>Purchase Order</h6>
                                    <h3>{{ $data['NumberOfPurchaseOrder'] }}</h3>
                                </div>
                                <div class="db-icon">
                                    <i class="fa fa-shopping-cart text-dark"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Today's Summary --}}
        <div class="row mb-3">
            <div class="col-12">
                <h5 class="fw-semibold mb-2">Today's Summary</h5>
            </div>
            @php
                $soPct =
                    $data['NumberOfSalesOrderYesterday'] > 0
                        ? round(
                            (($data['NumberOfSalesOrderToday'] - $data['NumberOfSalesOrderYesterday']) /
                                $data['NumberOfSalesOrderYesterday']) *
                                100,
                        )
                        : ($data['NumberOfSalesOrderToday'] > 0
                            ? 100
                            : 0);
                $revPct =
                    $data['YesterdayRevenue'] > 0
                        ? round((($data['TodayRevenue'] - $data['YesterdayRevenue']) / $data['YesterdayRevenue']) * 100)
                        : ($data['TodayRevenue'] > 0
                            ? 100
                            : 0);
                $paidPct =
                    $data['PaidSalesOrderYesterday'] > 0
                        ? round(
                            (($data['PaidSalesOrderToday'] - $data['PaidSalesOrderYesterday']) /
                                $data['PaidSalesOrderYesterday']) *
                                100,
                        )
                        : ($data['PaidSalesOrderToday'] > 0
                            ? 100
                            : 0);
                $unpaidPct =
                    $data['UnpaidSalesOrderYesterday'] > 0
                        ? round(
                            (($data['UnpaidSalesOrder'] - $data['UnpaidSalesOrderYesterday']) /
                                $data['UnpaidSalesOrderYesterday']) *
                                100,
                        )
                        : ($data['UnpaidSalesOrder'] > 0
                            ? 100
                            : 0);
            @endphp
            <div class="col-xl-3 col-sm-6 col-12 d-flex">
                <div class="card bg-comman w-100">
                    <div class="card-body">
                        <div class="db-widgets d-flex justify-content-between align-items-center">
                            <div class="db-info">
                                <h6>Sales Order Today</h6>
                                <h3 class="mb-1">{{ $data['NumberOfSalesOrderToday'] }}</h3>
                                <div class="small">
                                    @if ($soPct > 0)
                                        <span class="text-success"><i
                                                class="fa fa-arrow-up me-1"></i>{{ $soPct }}%</span>
                                    @elseif ($soPct < 0)
                                        <span class="text-danger"><i
                                                class="fa fa-arrow-down me-1"></i>{{ abs($soPct) }}%</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                    vs yesterday
                                </div>
                            </div>
                            <div class="db-icon">
                                <i class="fa fa-file-invoice text-dark"></i>
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
                                <h6>Revenue Today</h6>
                                <h3 class="mb-1">Rp. {{ number_format($data['TodayRevenue'], 0, ',', '.') }}</h3>
                                <div class="small">
                                    @if ($revPct > 0)
                                        <span class="text-success"><i
                                                class="fa fa-arrow-up me-1"></i>{{ $revPct }}%</span>
                                    @elseif ($revPct < 0)
                                        <span class="text-danger"><i
                                                class="fa fa-arrow-down me-1"></i>{{ abs($revPct) }}%</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                    vs yesterday
                                </div>
                            </div>
                            <div class="db-icon">
                                <i class="fa fa-dollar-sign text-dark"></i>
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
                                <h6>Paid Sales Order Today</h6>
                                <h3 class="mb-1">{{ $data['PaidSalesOrderToday'] }}</h3>
                                <div class="small">
                                    @if ($paidPct > 0)
                                        <span class="text-success"><i
                                                class="fa fa-arrow-up me-1"></i>{{ $paidPct }}%</span>
                                    @elseif ($paidPct < 0)
                                        <span class="text-danger"><i
                                                class="fa fa-arrow-down me-1"></i>{{ abs($paidPct) }}%</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                    vs yesterday
                                </div>
                            </div>
                            <div class="db-icon">
                                <i class="fa fa-check-circle text-dark"></i>
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
                                <h6>Unpaid Sales Order</h6>
                                <h3 class="mb-1">{{ $data['UnpaidSalesOrder'] }}</h3>
                                <div class="small">
                                    @if ($unpaidPct > 0)
                                        <span class="text-danger"><i
                                                class="fa fa-arrow-up me-1"></i>{{ $unpaidPct }}%</span>
                                    @elseif ($unpaidPct < 0)
                                        <span class="text-success"><i
                                                class="fa fa-arrow-down me-1"></i>{{ abs($unpaidPct) }}%</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                    vs yesterday
                                </div>
                            </div>
                            <div class="db-icon">
                                <i class="fa fa-exclamation-triangle text-dark"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Recent Activity --}}
        <div class="row">
            <div class="col-lg-6 mb-3">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Recent Sales Orders</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Number</th>
                                        <th>Date</th>
                                        <th>Customer</th>
                                        <th class="text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($data['RecentSalesOrders'] as $so)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <a href="/sales-order/edit/{{ $so['id'] }}" target="_blank"
                                                    class="text-primary">
                                                    {{ $so['number'] }}
                                                </a>
                                            </td>
                                            <td>{{ $so['date'] ? formatDate($so['date'], 'j M Y') : '-' }}</td>
                                            <td>{{ $so['customer'] }}</td>
                                            <td class="text-end">Rp. {{ number_format($so['total'], 0, ',', '.') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-3">No sales orders yet.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mb-3">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Recent Purchase Orders</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Number</th>
                                        <th>Date</th>
                                        <th>Vendor</th>
                                        <th class="text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($data['RecentPurchaseOrders'] as $po)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <a href="/purchase-order/edit/{{ $po['id'] }}" target="_blank"
                                                    class="text-primary">
                                                    {{ $po['number'] }}
                                                </a>
                                            </td>
                                            <td>{{ $po['date'] ? formatDate($po['date'], 'j M Y') : '-' }}</td>
                                            <td>{{ $po['vendor'] }}</td>
                                            <td class="text-end">Rp. {{ number_format($po['total'], 0, ',', '.') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-3">No purchase orders yet.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Unpaid SO --}}
        @if ($data['UnpaidSalesOrders']->isNotEmpty())
            <div class="row mb-3">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fa fa-exclamation-triangle text-warning me-1"></i>
                                Unpaid Sales Orders
                                <span
                                    class="badge bg-warning text-dark ms-1">{{ $data['UnpaidSalesOrders']->count() }}</span>
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Number</th>
                                            <th>Date</th>
                                            <th>Customer</th>
                                            <th class="text-end">Total</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($data['UnpaidSalesOrders'] as $so)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>
                                                    <a href="/sales-order/edit/{{ $so['id'] }}" target="_blank"
                                                        class="text-primary">
                                                        {{ $so['number'] }}
                                                    </a>
                                                </td>
                                                <td>{{ $so['date'] ? formatDate($so['date'], 'j M Y') : '-' }}</td>
                                                <td>{{ $so['customer'] }}</td>
                                                <td class="text-end">Rp. {{ number_format($so['total'], 0, ',', '.') }}
                                                </td>
                                                <td><span
                                                        class="badge bg-warning text-dark">{{ ucfirst($so['payment_status']) }}</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div>
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
        </div>
    @endif

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

            // ajax chart & configuration for revenue
        });

        var rupiahFormatter = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        });

        var chartRevenue; // Store chart instance

        function renderRevenueChart(data) {
            const dates = data.map(d => new Intl.DateTimeFormat('id-ID', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            }).format(new Date(d.date)));

            const totals = data.map(d => d.total);

            const options = {
                series: [{
                    name: "Revenue",
                    data: totals
                }],
                chart: {
                    height: 350,
                    type: 'line',
                    zoom: {
                        enabled: false
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth'
                },
                title: {
                    text: 'Revenue Chart',
                    align: 'left'
                },
                grid: {
                    row: {
                        colors: ['#f3f3f3', 'transparent'],
                        opacity: 0.5
                    }
                },
                xaxis: {
                    categories: dates
                },
                tooltip: {
                    y: {
                        formatter: value => rupiahFormatter.format(value)
                    }
                },
                yaxis: {
                    labels: {
                        formatter: value => rupiahFormatter.format(value)
                    }
                },
                responsive: [{
                    breakpoint: 768,
                    options: {
                        chart: {
                            height: 260
                        },
                        xaxis: {
                            labels: {
                                rotate: -30,
                                hideOverlappingLabels: true
                            }
                        },
                        stroke: {
                            width: 2
                        }
                    }
                }]
            };

            if (chartRevenue) {
                chartRevenue.updateOptions(options);
            } else {
                chartRevenue = new ApexCharts(document.querySelector("#chart-revenue"), options);
                chartRevenue.render();
            }

            $("#chart-loading").hide();
            $("#chart-revenue").fadeIn();
        }

        function fetchRevenueData() {
            $("#chart-loading").show();
            $("#chart-revenue").hide();
            $.ajax({
                url: "/dashboard/chart/revenue",
                type: "POST",
                data: {
                    start_date: $("#start-date").val() || "{{ date('Y-m-d', strtotime('-1 month')) }}",
                    end_date: $("#end-date").val() || "{{ date('Y-m-d') }}",
                    _token: "{{ csrf_token() }}"
                },
                success: function(data) {
                    renderRevenueChart(data);
                }
            });
        }

        $(document).ready(function() {
            fetchRevenueData(); // initial load
            $("#start-date, #end-date").on("change", fetchRevenueData); // update on change
        });
    </script>
@endsection
