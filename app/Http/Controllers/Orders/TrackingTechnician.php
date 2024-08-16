<?php

namespace App\Http\Controllers\Orders;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use App\Models\Orders\WorkOrder\TrackingModel;

class TrackingTechnician extends Controller
{
    private $title = "Tracking Technician";

    /**
     * Show the Customer index page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view(
            "Orders.TrackingTechnician.index",
            getIndexData(
                $this->title
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
        $data = TrackingModel::allForDataTables($request);

        // Set rows to be displayed in customer table.
        $rows = [];
        $no = $start + 1;
        foreach ($data["row"] as $key) {
            if ($key->latitude_end == null && $key->longitude_end == null) {
                $is_arrived = "No";
            } else {
                $is_arrived = "Yes";
            }
            $row = [];
            $row[] = $no++;
            $row[] = $key->workOrder->work_order_number;
            $row[] = $key->workOrder->salesOrder->customer->name;
            $row[] = $key->workOrder->salesOrder->address;
            $row[] = $is_arrived;
            $row[] = "<a href='" . url("/tracking/" . $key->work_order_id) . "' class='btn btn-primary btn-sm' target='_blank'>View</a>";
            $row[] = $key->id;
            $rows[] = $row;
        }

        return response()->json(array(
            "draw" => $draw,
            "recordsTotal" => TrackingModel::count(),
            "recordsFiltered" => $data["count"],
            "data" => $rows
        ));
    }

    public function share(Request $request)
    {
        $tracking = TrackingModel::with('workOrder')->whereIn('id', $request->ids)->first();
        // dd($tracking);

        $url = "http://185.199.52.172:5001/send-message";
        $tracking_url = url("/tracking/" . $tracking->work_order_id);

        $content_message = "";
        $content_message .= "Work Order Number: " . $tracking->workOrder->work_order_number . "\n";
        $content_message .= "Customer Name: " . $tracking->workOrder->salesOrder->customer->name . "\n";
        $content_message .= "Address: " . $tracking->workOrder->salesOrder->address . "\n";
        $content_message .= "Your tracking link: " . $tracking_url . "\n";

        $data = [
            'to' => "62" . $tracking->workOrder->salesOrder->customer->contact,
            'session' => auth()->user()->username,
            'text' => $content_message
        ];

        try {
            $response = Http::post($url, $data);
            if ($response->successful()) {
                $responseData = $response->json();
                if (isset($responseData['data']['status']) && $responseData['data']['status'] == 1) {
                    return getResponseData(true, "Message sent successfully");
                } else {
                    return getResponseData(false, "Failed to send message : " . $responseData['data']['message']);
                }
            } else {
                $responseData = $response->json();
                return getResponseData(false, "Failed to send message : " . $responseData['message']);
            }
        } catch (\Exception $e) {
            return getResponseData(false, "Failed to send message => " . $e->getMessage());
        }
    }

    public function delete(Request $request)
    {
        $tracking = TrackingModel::whereIn('id', $request->id)->delete();
        return getResponseData(true, "Data deleted successfully");
    }
}
