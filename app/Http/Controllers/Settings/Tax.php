<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;

// MODELS
use App\Models\Settings\TaxModel;

class Tax extends Controller
{
    private $title = "Tax Manager";
    private $menu = 5;
    private $submenu = 2;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view(
            'Settings.TaxManager.index',
            getIndexData(
                $this->title,
                $this->menu,
                $this->submenu
            )
        );
    }

    /**
     * Show the form for creating resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view(
            "Settings.TaxManager.create",
            getIndexData(
                $this->title,
                $this->menu,
                $this->submenu,
            )
        );
    }

    /**
     * Show the form for editing resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return view(
            "Settings.TaxManager.create",
            getIndexData(
                $this->title,
                $this->menu,
                $this->submenu,
                array(
                    "profile" => TaxModel::find($id)->toArray()
                )
            )
        );
    }

    /**
     * Display all resources.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        // Get all DataTables requests.
        $draw = $request->input("draw");
        $start = $request->input("start");

        // Get customer data (rows and count).
        $data = TaxModel::allForDataTables($request);

        // Set rows to be displayed in customer table.
        $rows = [];
        $no = $start + 1;
        foreach ($data["row"] as $key) {
            // Set an array for each row.
            $row = [];
            $row[] = $no++;
            $row[] = $key->percentage;
            $row[] = $key->valid_until;
            $row[] = $key->status;
            $row[] = $key->id;
            $rows[] = $row;
        }

        return response()->json(array(
            "draw" => $draw,
            "recordsTotal" => TaxModel::count(),
            "recordsFiltered" => $data["count"],
            "data" => $rows
        ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    public function store(Request $request)
    {
        try {
            $tax = new TaxModel();
            $tax->percentage = $request->percentage;
            $tax->valid_until = $request->validuntil;
            $status = $tax->save();

            // Set a new response data to be sent.
            return getResponseData(
                $status,
                $status ? "The new tax was successfully created!" : "Failed to create the new tax!"
            );
        } catch (Exception) {
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
    public function update(Request $request)
    {
        try {
            $tax = TaxModel::find($request->id);
            $tax->percentage = $request->percentage;
            $tax->valid_until = $request->validuntil;
            $status = $tax->save();

            // Set a new response data to be sent.
            return getResponseData(
                $status,
                $status ? "The tax was successfully updated!" : "Failed to update the tax!"
            );
        } catch (Exception) {
            // Set an error response data to be sent.
            return getResponseData(false);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        try {
            $status = true;
            $ids = $request->id;

            foreach ($ids as $id) {
                $tax = TaxModel::find($id);
                $status &= $tax->delete();
            }

            // Set a new response data to be sent.
            return getResponseData(
                $status,
                $status ? "The selected tax was successfully deleted!" : "Failed to delete the selected tax!"
            );
        } catch (Exception) {
            // Set an error response data to be sent.
            return getResponseData(false);
        }
    }
}
