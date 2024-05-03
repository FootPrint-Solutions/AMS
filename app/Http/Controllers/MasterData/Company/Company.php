<?php

namespace App\Http\Controllers\MasterData\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

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
        try {
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
        } catch (Exception $e) {
            // Logging error message.
            Log::error($e->getMessage());

            // Set an error response data to be sent.
            return getResponseData(false);
        }
    }

    /**
     * Update the company resource in database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    public function update(Request $request)
    {
        try {
            // Validasi input dari formulir
            $validatedData = $request->validate(
                [
                    'name' => 'required|string',
                    'address' => 'required|string',
                    'contact' => 'required|string',
                    'email' => ['required', 'email'],
                ],
                [
                    'name.required' => 'Company name is required!',
                    'address.required' => 'Company address is required!',
                    'contact.required' => 'Company contact is required!',
                    'email.required' => 'Company email is required!',
                    'email.email' => 'Company email must be a valid email address!',
                ]
            );

            // Cek apakah data perusahaan sudah ada
            $company = CompanyModel::first();

            if ($company) {
                // Data perusahaan ditemukan, maka perbarui data yang ada.
                $company->name = $validatedData['name'];
                $company->address = $validatedData['address'];
                $company->contact = $validatedData['contact'];
                $company->email = $validatedData['email'];
                $status = $company->update();
            } else {
                // Data perusahaan tidak ditemukan, maka buat data perusahaan baru.
                $company = new CompanyModel();
                $company->name = $validatedData['name'];
                $company->address = $validatedData['address'];
                $company->contact = $validatedData['contact'];
                $company->email = $validatedData['email'];
                $status = $company->save();
            }

            // Set data respons yang baru.
            return getResponseData(
                $status,
                $status ? "Company profile was successfully updated!" : "Failed to update company profile!"
            );
        } catch (ValidationException $e) {
            // Tangani pengecualian jika validasi gagal
            return getResponseData(false, $e->validator->errors()->first());
        } catch (Exception $e) {
            // Tangani pengecualian lainnya
            Log::error($e->getMessage());
            return getResponseData(false);
        }
    }
}
