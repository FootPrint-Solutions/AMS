<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Battery extends Controller
{

    public function index()
    {
        $data = array(
            'title' => 'Battery | ' . config('app.name'),
            'active' => 2,
        );
        return view('Masterdata/Battery/index', $data);
    }

    public function create()
    {
        return view('Masterdata/Battery/create');
    }
}
