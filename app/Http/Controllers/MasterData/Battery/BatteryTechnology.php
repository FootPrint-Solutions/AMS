<?php

namespace App\Http\Controllers\MasterData\Battery;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// MODELS
use App\Models\MasterData\Battery\BatteryTechnologyModel;

class BatteryTechnology extends Controller
{
    /**
     * Show the form for creating Battery Technology profile resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view(
            'MasterData.Battery.BatteryTechnology.create',
            getIndexData(
                'Battery Usage Type',
                2,
                4
            )
        );
    }

    /**
     * Store a newly created Battery Technology resource in database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    public function store(Request $request)
    {
        $technology = new BatteryTechnologyModel();
        $technology->name = $request->name;
        $status = $technology->save();

        // Set a new response data to be sent.
        if ($status) {
            // The inserting process is succeeded.
            $message = 'The new battery technology was successfully created!';
        } else {
            // The inserting process is failed.
            $message = 'Failed to create the new battery technology!';
        }

        return getResponseData($status, $message);
    }
}
