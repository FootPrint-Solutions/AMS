<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// MODELS
use App\Models\MasterData\Battery\BatterySubbrandCategoryModel;

class BatterySubbrand extends Controller
{
    /**
     * Show the form for creating Vehicle Subbrand Category profile resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view(
            'MasterData.Battery.BatterySubbrand.create',
            getIndexData(
                'Battery Subbrand Category',
                2,
                4
            )
        );
    }

    /**
     * Store a newly created Vehicle Subbrand Category resource in database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    public function store(Request $request)
    {
        $subbrand = new BatterySubbrandCategoryModel();
        $subbrand->name = $request->name;
        $status = $subbrand->save();

        // Set a new response data to be sent.
        if ($status) {
            // The inserting process is succeeded.
            $message = 'The new battery subbrand category was successfully created!';
        } else {
            // The inserting process is failed.
            $message = 'Failed to create the new battery subbrand category!';
        }

        return getResponseData($status, $message);
    }
}
