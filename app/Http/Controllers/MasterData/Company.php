<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Company extends Controller
{
    public function index()
    {
        $data = array(
            'title' => 'Company | ' . config('app.name'),
            'active' => 1,
        );
        return view('Masterdata/Company/index', $data);
    }
}
