<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// Models
use App\Models\Accounting\BillingModel;
use App\Models\MasterData\Distributor\DistributorShopModel;
use App\Models\MasterData\Customer\CustomerModel;

class Billing extends Controller
{
    private $title = "Billing";

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view(
            "Accounting.Billing.index",
            getIndexData($this->title)
        );
    }

    /** 
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view(
            "Accounting.Billing.create",
            getIndexData(
                $this->title,
                [
                    'billing_number' => BillingModel::generateBillingNumber(),
                    'distributorShops' => DistributorShopModel::all(),
                    'customers' => CustomerModel::all(),
                ]
            )
        );
    }
}
