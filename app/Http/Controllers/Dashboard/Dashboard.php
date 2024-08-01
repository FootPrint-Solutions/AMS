<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

// Models
use App\Models\MasterData\Battery\BatteryModel;
use App\Models\MasterData\Customer\CustomerModel;
use App\Models\MasterData\Vehicle\VehicleModel;
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
        $today = Carbon::today();
        return view(
            'Dashboard.index',
            getIndexData('Dashboard', [
                'NumberOfCustomer' => CustomerModel::count(),
                'NumberOfVehicle' => VehicleModel::count(),
                'NumberOfBattery' => BatteryModel::count(),
                'TotalRevenue' => SalesOrderModel::sum('total'),
                'NumberOfWorkOrder' => WorkOrderModel::count(),
                'NumberOfSalesOrder' => SalesOrderModel::count(),
                'Promo' => PromoModel::where('period_start', '<=', $today)->where('period_end', '>=', $today)->get(),
            ]),
        );
    }

    /**
     * Get the data for the dashboard.
     *
     * @return array
     */
    public function getRevenueChart()
    {
        $revenue = SalesOrderModel::selectRaw('DATE(date) as date, SUM(total) as total')->groupBy('date')->get();

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
