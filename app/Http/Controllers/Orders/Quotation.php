<?php

namespace App\Http\Controllers\Orders;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Quotation extends Controller
{
    public function index()
    {
        return view(
            'Orders/Quotation/index',
            [
                'title' => 'Quick Quotation | ' . config('app.name'),
                'subtitle' => 'List',
                'active' => 2,
            ]
        );
    }
}
