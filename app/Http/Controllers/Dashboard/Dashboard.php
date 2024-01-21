<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Dashboard extends Controller
{
    //

    public function index()
    {
        $data = array(
            'title' => 'Dashboard | ' . config('app.name'),
        );
        return view('Dashboard.index', $data);
    }
}
