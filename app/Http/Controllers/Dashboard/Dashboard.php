<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

// Models
use App\Models\MasterData\Battery\BatteryModel;
use App\Models\MasterData\Customer\CustomerModel;
use App\Models\MasterData\Vehicle\VehicleModel;
use App\Models\Orders\SalesOrder\SalesOrderModel;

class Dashboard extends Controller
{
    /**
     * Display the dashboard view.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('Dashboard.index',   getIndexData(
            'Dashboard',
            1,
            '',
            array(
                'NumberOfCustomer' => CustomerModel::count(),
                'NumberOfVehicle' => VehicleModel::count(),
                'NumberOfBattery' => BatteryModel::count(),
                'TotalRevenue' => SalesOrderModel::sum('total'),
            )
        ));
    }
}
