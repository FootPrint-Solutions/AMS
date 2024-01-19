<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Vehicle extends Controller
{

    public function index()
    {
        return view('Masterdata/Vehicle/index');
    }


    public function create()
    {
        return view('Masterdata/Vehicle/create');
    }
}
