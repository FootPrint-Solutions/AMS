<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

// MODELS
use App\Models\Settings\TaxModel;

class Tax extends Controller
{
    private $title = "Tax Manager";

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
                $this->title
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
                $this->title
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

        // Get tax data (rows and count).
        $data = TaxModel::allForDataTables($request);

        // Set rows to be displayed in tax table.
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
            $row[] = $key->percentage;
            $row[] = $key->valid_from;
            $row[] = "<i class='fa-solid fa-circle $statusIndicatorColor'></i>";
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
            $tax->valid_from = $request->validfrom;
            $status = $tax->save();

            // Set a new response data to be sent.
            return getResponseData(
                $status,
                $status ? "The new tax was successfully created!" : "Failed to create the new tax!"
            );
        } catch (Exception $e) {
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
            $tax->valid_from = $request->validfrom;
            $status = $tax->save();

            // Set a new response data to be sent.
            return getResponseData(
                $status,
                $status ? "The tax was successfully updated!" : "Failed to update the tax!"
            );
        } catch (Exception $e) {
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
            // Set tax's status to true.
            $tax = TaxModel::find($request->id);
            if (!$tax->status) {
                $tax->status = $tax->status ? 0 : 1;
                $status = $tax->save();

                // Set all other taxes' status to false.
                TaxModel::where('id', '!=', $request->id)->update(['status' => 0]);

                // Set a new response data to be sent.
                return getResponseData(
                    $status,
                    $status ? "The selected tax was successfully updated!" : "Failed to update the selected tax!"
                );
            } else {
                // Set a new response data to be sent.
                return getResponseData(
                    false,
                    "The selected tax is currently the only active tax. Please select an inactive tax as an active tax."
                );
            }
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
        } catch (Exception $e) {
            // Set an error response data to be sent.
            return getResponseData(false);
        }
    }
}
