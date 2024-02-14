<?php

namespace App\Http\Controllers\MasterData\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// MODELS
use App\Models\MasterData\Company\CompanyModel;

class Company extends Controller
{
    private $title = "Company";
    private $menu = 2;
    private $submenu = 1;

    /**
     * Show the form for editing Company profile resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Check whether a company profile has been created or not.
        $company = CompanyModel::firstOrNew();
        if (!$company->exists) {
            $company->name = "";
            $company->address = "";
            $company->contact = "";
            $company->email = "";
            $company->save();
        }

        return view(
            "MasterData.Company.edit",
            getIndexData(
                $this->title,
                $this->menu,
                $this->submenu,
                array(
                    "profile" => CompanyModel::first()->toArray()
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
        return getResponseData(
            $status,
            $status ? "Company profile was successfully updated!" : "Failed to update company profile!"
        );
    }
}
