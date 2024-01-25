<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\MasterData\VehicleModel;

class Vehicle extends Controller
{

    public function index()
    {
        return view(
            'Masterdata/Vehicle/index',
            [
                'title' => 'Vehicle | ' . config('app.name'),
                'data' => VehicleModel::all(),
                'active' => 1,
            ]
        );
    }


    public function create()
    {
        return view('Masterdata/Vehicle/create');
    }
}
