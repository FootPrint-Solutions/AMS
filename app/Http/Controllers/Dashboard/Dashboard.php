<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
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
        session([
            'menu' => $menu
        ]);

        $data = array(
            'title' => 'Dashboard | ' . config('app.name'),
        );
        return view('Dashboard.index', $data);
    }
}
