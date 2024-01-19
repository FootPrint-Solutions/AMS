<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Company extends Controller
{
    public function index()
    {
        return view('Masterdata/Company/index');
    }
}
