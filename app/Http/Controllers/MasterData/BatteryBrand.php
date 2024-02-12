<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// MODELS
use App\Models\MasterData\Battery\BatteryBrandModel;

class BatteryBrand extends Controller
{
    /**
     * Show the form for creating Vehicle Brand profile resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view(
            'MasterData.Battery.BatteryBrand.create',
            getIndexData(
                'Battery Brand',
                2,
                4
            )
        );
    }

    /**
     * Store a newly created Vehicle Brand resource in database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    public function store(Request $request)
    {
        $brand = new BatteryBrandModel();
        $brand->name = $request->name;
        $status = $brand->save();

        // Set a new response data to be sent.
        if ($status) {
            // The inserting process is succeeded.
            $message = 'The new battery brand was successfully created!';
        } else {
            // The inserting process is failed.
            $message = 'Failed to create the new battery brand!';
        }

        return getResponseData($status, $message);
    }
}
