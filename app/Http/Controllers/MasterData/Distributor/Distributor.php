<?php

namespace App\Http\Controllers\MasterData\Distributor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

// MODELS
use App\Models\MasterData\Distributor\DistributorModel;
use App\Models\MasterData\Distributor\DistributorShopModel;

class Distributor extends Controller
{
    private $title = "Distributor";

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view(
            'MasterData.Distributor.index',
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
            'MasterData.Distributor.create',
            getIndexData(
                $this->title,
                array(
                    "shops" => DistributorShopModel::where("type", 1)->get()->toArray()
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
        if ($id == null) return redirect()->route('distributor.index');
        $distributor = DistributorModel::find($id);
        if (!$distributor) return redirect()->route('distributor.index');
        return view(
            'MasterData.Distributor.create',
            getIndexData(
                $this->title,
                array(
                    "profile" => $distributor->toArray(),
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

        // Get distributor data (rows and count).
        $data = DistributorModel::allForDataTables($request);

        // Set rows to be displayed in table.
        $rows = [];
        $no = $start + 1;
        foreach ($data["row"] as $key) {
            // Set status indicator color based on status.
            if ($key->status == 0) {
                $statusIndicatorColor = "text-danger";
            } else {
                $statusIndicatorColor = "text-success";
            }

            // Set an array for each row.
            $row = [];
            $row[] = $no++;
            $row[] = $key->name;
            $row[] = $key->address;
            $row[] = $key->contact_person;
            $row[] = "<span class='text-secondary'>+62</span> " . $key->contact;
            $row[] = $key->email ?? "-";
            $row[] = "<i class='fa-solid fa-circle $statusIndicatorColor'></i>";
            $row[] = $key->id;
            $row[] = $key->status;
            $rows[] = $row;
        }

        return response()->json(array(
            "draw" => $draw,
            "recordsTotal" => DistributorModel::count(),
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
                    'name' => 'required',
                    'address' => 'required',
                    'contactperson' => 'required',
                    'contact' => 'required',
                    'email' => 'email',
                    'note' => 'nullable',
                    'Latitude' => 'nullable',
                    'Longitude' => 'nullable',
                ],
                [
                    'name.required' => 'Please fill out the distributor name!',
                    'address.required' => 'Please fill out the distributor address!',
                    'contactperson.required' => 'Please fill out the contact person!',
                    'contact.required' => 'Please fill out the contact number!',
                    'email.email' => 'Please fill out a valid email address!',
                ]
            );

            $distributor = new DistributorModel();
            $distributor->name = $validatedData['name'];
            $distributor->is_shop = $request->isshop;
            $distributor->address = $validatedData['address'];
            $distributor->latitude = $request->Latitude;
            $distributor->longitude = $request->Longitude;
            $distributor->contact_person = $validatedData['contactperson'];
            $distributor->contact = $validatedData['contact'];
            $distributor->email = $validatedData['email'];
            $distributor->note = "";
            $status = $distributor->save();

            // Check if is shop is checked or not.
            if ($request->isshop == 1) {
                // Add a new shop for the distributor.
                $shop = new DistributorShopModel();
                $shop->name = "Distributor Main Shop";
                $shop->distributor_id = $distributor->id;
                $shop->type = 1;
                $shop->address = $validatedData['address'];
                $shop->contact_person = $validatedData['contactperson'];
                $shop->contact = $validatedData['contact'];
                $shop->email = $validatedData['email'];
                $shop->note = $validatedData['note'];
                $shop->latitude = $request->Latitude;
                $shop->longitude = $request->Longitude;
                $status &= $shop->save();
            } else {
                // Delete saved distributor shop.
                $shop = DistributorShopModel::where('distributor_id', $distributor->id)->where("type", 1)->first();
                if ($shop) {
                    $shop->delete();
                }
            }

            // Set a new response data to be sent.
            return getResponseData(
                $status,
                $status ? "The new distributor was successfully created!" : "Failed to create the new distributor!"
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
                    'name' => 'required',
                    'address' => 'required',
                    'contactperson' => 'required',
                    'contact' => 'required',
                    'email' => 'email',
                    'note' => 'nullable',
                    'Latitude' => 'nullable',
                    'Longitude' => 'nullable',
                ],
                [
                    'name.required' => 'Please fill out the distributor name!',
                    'address.required' => 'Please fill out the distributor address!',
                    'contactperson.required' => 'Please fill out the contact person!',
                    'contact.required' => 'Please fill out the contact number!',
                    'email.email' => 'Please fill out a valid email address!',
                ]
            );

            $distributor = DistributorModel::find($request->id);
            $distributor->name = $validatedData['name'];
            $distributor->is_shop = $request->isshop;
            $distributor->address = $validatedData['address'];
            $distributor->latitude = $request->Latitude;
            $distributor->longitude = $request->Longitude;
            $distributor->contact_person = $validatedData['contactperson'];
            $distributor->contact = $validatedData['contact'];
            $distributor->email = $validatedData['email'];
            $distributor->note = $request->note;
            $status = $distributor->save();

            // Check if is shop is checked or not.
            if ($request->isshop == 1) {
                $shop = DistributorShopModel::where('distributor_id', $distributor->id)->where("type", 1)->first();
                if (!$shop) {
                    // Add a new shop for the distributor.
                    $shop = new DistributorShopModel();
                    $shop->name = "Distributor Main Shop";
                    $shop->distributor_id = $distributor->id;
                    $shop->type = 1;
                    $shop->address = $validatedData['address'];
                    $shop->contact_person = $validatedData['contactperson'];
                    $shop->contact = $validatedData['contact'];
                    $shop->email = $validatedData['email'];
                    $shop->note = "";
                    $shop->latitude = $request->Latitude;
                    $shop->longitude = $request->Longitude;
                    $status &= $shop->save();
                }
            } else {
                // Delete saved distributor shop.
                $shop = DistributorShopModel::where('distributor_id', $distributor->id)->where("type", 1)->first();
                if ($shop) {
                    $shop->delete();
                }
            }

            // Set a new response data to be sent.
            return getResponseData(
                $status,
                $status ? "The selected distributor was successfully updated!" : "Failed to update the selected distributor!"
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
     * @return \Illuminate\Http\Response
     */
    public function updateStatus(Request $request)
    {
        try {
            $distributor = DistributorModel::find($request->id);
            $distributor->status = $distributor->status ? 0 : 1;
            $status = $distributor->save();

            // Update all distributor shops.
            $shops = DistributorShopModel::where('distributor_id', $distributor->id)->get();
            if ($shops->count() > 0) {
                foreach ($shops as $shop) {
                    $shop->status = $distributor->status;
                    $status &= $shop->save();
                }
            }

            // Set a new response data to be sent.
            return getResponseData(
                $status,
                $status ? "The selected distributor was successfully updated!" : "Failed to update the selected distributor!"
            );
        } catch (Exception $e) {
            // Logging error message.
            Log::error($e->getMessage());

            // Set an error response data to be sent.
            return getResponseData(false);
        }
    }
}
