<?php

namespace App\Http\Controllers\Orders;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Support\Facades\Storage;

// MODELS

// QR CODE
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\Orders\WorkOrder\WorkOrderModel;

class WorkOrder extends Controller
{
    private $title = 'Work Order';

    public function index()
    {
        return view(
            'Orders.WorkOrder.index',
            getIndexData(
                $this->title
            )
        );
    }

    public function show(Request $request)
    {
        // Get all DataTables requests.
        $draw = $request->input("draw");
        $start = $request->input("start");

        // Get customer data (rows and count).
        $data = WorkOrderModel::allForDataTables($request);

        // Set rows to be displayed in customer table.
        $rows = [];
        $no = $start + 1;
        foreach ($data["row"] as $key) {
            // Set the status badge class name depending on the status.
            if ($key->status == "paid") {
                $statusBadgeClass = "badge-success";
            } else if ($key->status == "pending") {
                $statusBadgeClass = "badge-warning";
            } else {
                $statusBadgeClass = "badge-danger";
            }

            // Set an array for each row.
            $row = [];
            $row[] = $no++;
            $row[] = $key->work_order_number;
            $row[] = $key->sales_order_number;
            $row[] = formatDate($key->date);
            $row[] = $key->customer_name;
            $row[] = $key->qty;
            $row[] = formatPrice($key->total);
            $row[] = $key->address;
            $row[] = $key->id;
            $rows[] = $row;
        }

        return response()->json(array(
            "draw" => $draw,
            "recordsTotal" => WorkOrderModel::count(),
            "recordsFiltered" => $data["count"],
            "data" => $rows
        ));
    }

    public function print(request $request)
    {
        // get work order data and work order battery detail data
        $workOrder = WorkOrderModel::getWorkOrderData($request->id);
        $baseUrl = "https://www.google.com/maps?q=";
        $mapsUrl = $baseUrl .  $workOrder->latitude . "," . $workOrder->longitude;
        $qrCode = QrCode::size(90)->generate($mapsUrl);

        // return view with work order data
        return view('Orders.WorkOrder.print', compact('workOrder', 'qrCode'));
    }

    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'work_order_id' => 'required|integer|exists:work_orders,id'
        ]);

        $image = $request->file('image');
        $workOrderId = $request->input('work_order_id');
        $imageExtension = $image->getClientOriginalExtension();
        $imageFileName = $workOrderId . '.' . $imageExtension;
        $imagePath = 'image/work-order/' . $imageFileName;
        $storedImagePath = $image->storeAs('public/' . dirname($imagePath), $imageFileName);
        WorkOrderModel::updateImagePath($workOrderId, $imagePath);
        return response()->json([
            'success' => true,
            'message' => 'Image uploaded successfully.',
            'image_path' => Storage::url($imagePath)
        ]);
    }
}
