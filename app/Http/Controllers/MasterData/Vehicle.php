<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Vehicle extends Controller
{

    public function index()
    {
        $data = array(
            'title' => 'Vehicle | ' . config('app.name'),
        );
        return view('Masterdata/Vehicle/index', $data);
    }


    public function create()
    {
        return view('Masterdata/Vehicle/create');
    }
}
