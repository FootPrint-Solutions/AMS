<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// MODELS
use App\Models\MasterData\Battery\BatteryUsageTypeModel;

class BatteryUsage extends Controller
{
    /**
     * Show the form for creating Vehicle Usage Type profile resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view(
            'MasterData.BatteryUsage.create',
            getIndexData(
                'Battery Usage Type',
                2,
                4
            )
        );
    }

    /**
     * Store a newly created Vehicle Usage Type resource in database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    public function store(Request $request)
    {
        $usage = new BatteryUsageTypeModel();
        $usage->name = $request->name;
        $status = $usage->save();

        // Set a new response data to be sent.
        if ($status) {
            // The inserting process is succeeded.
            $message = 'The new battery usage type was successfully created!';
        } else {
            // The inserting process is failed.
            $message = 'Failed to create the new battery usage type!';
        }

        return getResponseData($status, $message);
    }
}
