<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\MasterData\Battery\BatteryModel;
use App\Models\MasterData\CustomerModel;
use App\Models\MasterData\VehicleModel;
use Illuminate\Http\Request;

use App\Models\Menu;
use App\Models\MenuParent;

class Dashboard extends Controller
{
    /**
     * Display the dashboard view.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $menu = MenuParent::with('menus')->get()->toArray();
        session(['menu' => $menu]);
        return view('Dashboard.index',   getIndexData(
            'Dashboard',
            1,
            '',
            array(
                'NumberOfCustomer' => CustomerModel::count(),
                'NumberOfVehicle' => VehicleModel::count(),
                'NumberOfBattery' => BatteryModel::count()
            )
        ));
    }
}
