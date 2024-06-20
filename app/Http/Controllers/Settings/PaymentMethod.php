<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

// MODELS
use App\Models\Settings\PaymentMethodModel;

class PaymentMethod extends Controller
{
    private $title = "Payment Method Manager";
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
            'Settings.PaymentMethodManager.index',
            getIndexData(
                $this->title,
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
            "Settings.PaymentMethodManager.create",
            getIndexData(
                $this->title,
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
            "Settings.PaymentMethodManager.create",
            getIndexData(
                $this->title,
                array(
                    "profile" => PaymentMethodModel::find($id)->toArray()
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
        $data = PaymentMethodModel::allForDataTables($request);

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
            $row[] = $key->name;
            $row[] = "<i class='fa-solid fa-circle $statusIndicatorColor'></i>";
            $row[] = $key->id;
            $row[] = $key->status;
            $rows[] = $row;
        }

        return response()->json(array(
            "draw" => $draw,
            "recordsTotal" => PaymentMethodModel::count(),
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
            $payment = new PaymentMethodModel();
            $payment->name = $request->name;
            $payment->note = $request->note;
            $status = $payment->save();

            // Set a new response data to be sent.
            return getResponseData(
                $status,
                $status ? "The new payment method was successfully created!" : "Failed to create the new payment method!"
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
            $payment = PaymentMethodModel::find($request->id);
            $payment->name = $request->name;
            $payment->note = $request->note;
            $status = $payment->save();

            // Set a new response data to be sent.
            return getResponseData(
                $status,
                $status ? "The selected payment method was successfully updated!" : "Failed to update the selected payment method!"
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
            $tax = PaymentMethodModel::find($request->id);
            $tax->status = $tax->status ? 0 : 1;
            $status = $tax->save();

            // Set a new response data to be sent.
            return getResponseData(
                $status,
                $status ? "The selected tax was successfully updated!" : "Failed to update the selected tax!"
            );
        } catch (Exception $e) {
            // Logging error message.
            Log::error($e->getMessage());

            // Set an error response data to be sent.
            return getResponseData(false);
        }
    }
}
