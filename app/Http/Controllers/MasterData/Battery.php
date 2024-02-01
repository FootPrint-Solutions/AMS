<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Battery extends Controller
{

    public function index()
    {
        return view(
            'MasterData/Battery/index',
            getIndexData(
                'Battery',
                2,
                4
            )
        );
    }

    public function create()
    {
        return view('Masterdata/Battery/create');
    }
}
