<?php

namespace App\Http\Controllers\MasterData\Distributor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Validation\ValidationException;

// MODELS
use App\Models\MasterData\Distributor\DistributorShopModel;
use App\Models\MasterData\Distributor\DistributorShopTechnicianModel;

class DistributorShopTechnician extends Controller
{
    private $title = "Shop Technician";

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view(
            'MasterData.Distributor.Technician.index',
            getIndexData(
                $this->title
            )
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
            'MasterData.Distributor.Technician.create',
            getIndexData(
                $this->title,
                array(
                    "shops" => DistributorShopModel::with("distributor")->where("status", 1)->get()->toArray()
                )
            )
        );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id = null)
    {
        if ($id == null) return redirect()->route("distributor.technician.index");
        $technician = DistributorShopTechnicianModel::find($id);
        if ($technician == null) return redirect()->route("distributor.technician.index");
        return view(
            'MasterData.Distributor.Technician.create',
            getIndexData(
                $this->title,
                array(
                    "profile" => $technician->toArray(),
                    "shops" => DistributorShopModel::with("distributor")->where("status", 1)->get()->toArray()
                )
            )
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        // Get all DataTables requests.
        $draw = $request->input("draw");
        $start = $request->input("start");

        // Get technician data (rows and count).
        $data = DistributorShopTechnicianModel::allForDataTables($request);

        // Set rows to be displayed in technician table.
        $rows = [];
        $no = $start + 1;
        foreach ($data["row"] as $key) {
            $row = [];
            $row[] = $no++;
            $row[] = $key->name;
            $row[] = $key->shop->name ?? "-";
            $row[] = "<span class='text-secondary'>+62</span> " . $key->contact;
            $row[] = $key->email ?? "-";
            $row[] = $key->id;
            $rows[] = $row;
        }

        return response()->json(array(
            "draw" => $draw,
            "recordsTotal" => DistributorShopTechnicianModel::count(),
            "recordsFiltered" => $data["count"],
            "data" => $rows
        ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate(
                [
                    "name" => "required",
                    "contact" => "required",
                    "email" => "email",
                    "note" => "nullable"
                ],
                [
                    "name.required" => "Please fill out the technician name!",
                    "contact.required" => "Please fill out the contact number!",
                    "email.email" => "Please fill out the correct email format!"
                ]
            );

            $technician = new DistributorShopTechnicianModel();
            $technician->name = $validatedData["name"];
            $technician->distributor_shop_id = $request->shop;
            $technician->contact = $validatedData["contact"];
            $technician->email = $validatedData["email"];
            $technician->note = $request->note;
            $status = $technician->save();

            // Set a new response data to be sent.
            return getResponseData(
                $status,
                $status ? "The new technician was successfully created!" : "Failed to create the new technician!"
            );
        } catch (ValidationException $e) {
            // Tangani pengecualian jika validasi gagal
            return getResponseData(false, $e->validator->errors()->first());
        } catch (Exception $e) {
            // Logging error message.
            Log::error($e->getMessage());

            // Set an error response data to be sent.
            return getResponseData(false);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        try {
            $validatedData = $request->validate(
                [
                    "name" => "required",
                    "contact" => "required",
                    "email" => "email",
                    "note" => "nullable"
                ],
                [
                    "name.required" => "Please fill out the technician name!",
                    "contact.required" => "Please fill out the contact number!",
                    "email.email" => "Please fill out the correct email format!"
                ]
            );

            $technician = DistributorShopTechnicianModel::find($request->id);
            $technician->name = $validatedData["name"];
            $technician->distributor_shop_id = $request->shop;
            $technician->contact = $validatedData["contact"];
            $technician->email = $validatedData["email"];
            $technician->note = $request->note;
            $status = $technician->save();

            // Set a new response data to be sent.
            return getResponseData(
                $status,
                $status ? "The new technician was successfully updated!" : "Failed to update the new technician!"
            );
        } catch (ValidationException $e) {
            // Tangani pengecualian jika validasi gagal
            return getResponseData(false, $e->validator->errors()->first());
        } catch (Exception $e) {
            // Logging error message.
            Log::error($e->getMessage());

            // Set an error response data to be sent.
            return getResponseData(false);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        try {
            $status = true;
            $ids = $request->id;

            foreach ($ids as $id) {
                $technician = DistributorShopTechnicianModel::find($id);
                $status = $technician->delete();
            }

            // Set a new response data to be sent.
            return getResponseData(
                $status,
                $status ? "The new technician was successfully deleted!" : "Failed to delete the new technician!"
            );
        } catch (Exception $e) {
            // Logging error message.
            Log::error($e->getMessage());

            // Set an error response data to be sent.
            return getResponseData(false);
        }
    }
}
