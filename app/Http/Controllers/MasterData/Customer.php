<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Customer extends Controller
{
    public function index()
    {
        return view(
            'masterdata.Customer.index',
            [
                'title' => 'Customer',
                'subtitle' => 'List',
                'active' => 'customer',
            ]
        );
    }

    public function create()
    {
        return view('masterdata.Customer.create');
    }
}
