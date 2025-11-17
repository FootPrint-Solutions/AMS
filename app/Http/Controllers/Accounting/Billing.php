<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Billing extends Controller
{
    private $title = "Billing";

    public function index()
    {
        return view(
            "Accounting.Billing.index",
            getIndexData($this->title)
        );
    }
}
