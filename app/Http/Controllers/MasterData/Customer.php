<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// MODELS
use App\Models\MasterData\CustomerModel;

class Customer extends Controller
{
    /**
     * Show the Customer index page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view(
            'MasterData/Customer/index',
            array(
                'title' => 'Customer | ' . config('app.name'),
                'subtitle' => 'List',
                'active' => 2,
            )
        );
    }

    /**
     * Show the form for editing Company profile resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view(
            'MasterData/Customer/create',
            array(
                'title' => 'Customer | ' . config('app.name'),
                'subtitle' => 'List',
                'active' => 2,
            )
        );
    }

    /**
     * Store a newly created Customer resource in database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $customer = new CustomerModel();
        $customer->name = $request->name;
        $customer->address = $request->address;
        $customer->contact = $request->contact;
        $customer->email = $request->email;
        $status = $customer->save();

        // Set a new response data to be sent.
        if ($status) {
            // The inserting process is succeeded.
            $message = 'The new customer was successfully created!';
        } else {
            // The inserting process is failed.
            $message = 'Failed to create the new customer!';
        }

        return json_encode([
            'status' => $status,
            'message' => $message
        ]);
    }
}
