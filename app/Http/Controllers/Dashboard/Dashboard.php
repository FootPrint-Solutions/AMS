<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

// Models
use App\Models\MasterData\Battery\BatteryModel;
use App\Models\MasterData\Customer\CustomerModel;
use App\Models\MasterData\Vehicle\VehicleModel;
use App\Models\Orders\PurchaseOrder\PurchaseOrderModel;
use App\Models\Orders\SalesOrder\SalesOrderModel;
use App\Models\Settings\PromoModel;
use App\Models\Orders\WorkOrder\WorkOrderModel;
use Illuminate\Support\Carbon;

class Dashboard extends Controller
{
    /**
     * Display the dashboard view.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $today = Carbon::today()->format('Y-m-d');
        $yesterday = Carbon::yesterday()->format('Y-m-d');

        $numberOfSalesOrderToday = SalesOrderModel::whereDate('date', $today)->count();
        $numberOfSalesOrderYesterday = SalesOrderModel::whereDate('date', $yesterday)->count();
        $todayRevenue = SalesOrderModel::whereDate('date', $today)->sum('total');
        $yesterdayRevenue = SalesOrderModel::whereDate('date', $yesterday)->sum('total');

        $paidSalesOrderToday = SalesOrderModel::where('payment_status', 'paid')
            ->whereDate('date', $today)
            ->count();
        $paidSalesOrderYesterday = SalesOrderModel::where('payment_status', 'paid')
            ->whereDate('date', $yesterday)
            ->count();

        $unpaidSalesOrderToday = SalesOrderModel::whereIn('payment_status', ['unpaid', 'pending'])
            ->whereDate('date', $today)
            ->count();
        $unpaidSalesOrderYesterday = SalesOrderModel::whereIn('payment_status', ['unpaid', 'pending'])
            ->whereDate('date', $yesterday)
            ->count();

        $recentSalesOrders = SalesOrderModel::with('customer')
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->take(5)
            ->get()
            ->map(function ($so) {
                return [
                    'id' => $so->id,
                    'number' => $so->sales_order_number,
                    'date' => $so->date,
                    'customer' => $so->customer->name ?? '-',
                    'total' => $so->total,
                    'payment_status' => $so->payment_status,
                ];
            });

        $recentPurchaseOrders = PurchaseOrderModel::with('vendor')
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->take(5)
            ->get()
            ->map(function ($po) {
                return [
                    'id' => $po->id,
                    'number' => $po->purchase_order_number,
                    'date' => $po->date,
                    'vendor' => $po->vendor->name ?? '-',
                    'total' => $po->total,
                ];
            });

        $unpaidSalesOrders = SalesOrderModel::with('customer')
            ->whereIn('payment_status', ['unpaid', 'pending'])
            ->whereDate('date', $today)
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->take(10)
            ->get()
            ->map(function ($so) {
                return [
                    'id' => $so->id,
                    'number' => $so->sales_order_number,
                    'date' => $so->date,
                    'customer' => $so->customer->name ?? '-',
                    'total' => $so->total,
                    'payment_status' => $so->payment_status,
                ];
            });

        return view(
            'Dashboard.index',
            getIndexData('Dashboard', [
                'NumberOfCustomer' => CustomerModel::count(),
                'NumberOfVehicle' => VehicleModel::count(),
                'NumberOfBattery' => BatteryModel::count(),
                'TotalRevenue' => SalesOrderModel::sum('total'),
                'NumberOfWorkOrder' => WorkOrderModel::count(),
                'NumberOfPurchaseOrder' => PurchaseOrderModel::count(),
                'NumberOfSalesOrder' => SalesOrderModel::count(),
                'NumberOfSalesOrderToday' => $numberOfSalesOrderToday,
                'NumberOfSalesOrderYesterday' => $numberOfSalesOrderYesterday,
                'TodayRevenue' => $todayRevenue,
                'YesterdayRevenue' => $yesterdayRevenue,
                'PaidSalesOrderToday' => $paidSalesOrderToday,
                'PaidSalesOrderYesterday' => $paidSalesOrderYesterday,
                'UnpaidSalesOrder' => $unpaidSalesOrderToday,
                'UnpaidSalesOrderYesterday' => $unpaidSalesOrderYesterday,
                'RecentSalesOrders' => $recentSalesOrders,
                'RecentPurchaseOrders' => $recentPurchaseOrders,
                'UnpaidSalesOrders' => $unpaidSalesOrders,
                'Promo' => PromoModel::where('period_start', '<=', $today)->where('period_end', '>=', $today)->get(),
            ]),
        );
    }

    /**
     * Get the data for the dashboard.
     *
     * @return array
     */
    public function getRevenueChart(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        $revenue = SalesOrderModel::selectRaw('DATE(date) as date, SUM(total) as total')
            ->whereBetween('date', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        $data = [];
        foreach ($revenue as $r) {
            $data[] = [
                'date' => $r->date,
                'total' => $r->total,
            ];
        }

        return response()->json($data);
    }
}
