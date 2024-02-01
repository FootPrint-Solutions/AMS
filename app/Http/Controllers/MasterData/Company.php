<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// MODELS
use App\Models\MasterData\CompanyModel;

class Company extends Controller
{
    /**
     * Show the form for editing Company profile resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view(
            'MasterData/Company/edit',
            getIndexData(
                'Company',
                2,
                1,
                array(
                    'company_profile' => CompanyModel::first()->toArray()
                )
            )
        );
    }

    /**
     * Update the company resource in database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    public function update(Request $request)
    {
        $company = CompanyModel::first();

        if ($company) {
            // A company data is found therefore update the existing data.
            $company->name = $request->name;
            $company->address = $request->address;
            $company->contact = $request->contact;
            $company->email = $request->email;
            $status = $company->update();
        } else {
            // A company data is not found therefore insert a new company data.
            $company = new CompanyModel();
            $company->name = $request->name;
            $company->address = $request->address;
            $company->contact = $request->contact;
            $company->email = $request->email;
            $status = $company->save();
        }

        // Set a new response data to be sent.
        if ($status) {
            // The updating or inserting process is succeeded.
            $message = 'Company profile was successfully updated!';
        } else {
            // The updating or inserting process is failed.
            $message = 'Failed to update company profile!';
        }

        return json_encode([
            'status' => $status,
            'message' => $message
        ]);
    }
}
