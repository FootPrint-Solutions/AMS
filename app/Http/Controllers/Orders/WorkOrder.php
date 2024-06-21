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
use App\Models\Settings\PrintTemplateModel;

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
            $row[] = $key->status;
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
        $validationRules = [
            'work_order_id' => 'required|integer|exists:work_orders,id'
        ];

        $tipe = $request->print_option;

        // check if image is exist
        if ($request->hasFile('image')) {
            if ($tipe == "tokopedia_dan_instalasi" || $tipe == "tokopedia_tanpa_instalasi") {
                $validationRules['image'] = 'required|image|mimes:jpeg,png,jpg,gif|max:2048';
            }

            $request->validate($validationRules);

            if (isset($validationRules['image'])) {
                $image = $request->file('image');
                $workOrderId = $request->input('work_order_id');
                $imageExtension = $image->getClientOriginalExtension();
                $imageFileName = $workOrderId . '.' . $imageExtension;
                $imagePath = 'image/work-order/' . $imageFileName;
                $storedImagePath = $image->storeAs('public/' . dirname($imagePath), $imageFileName);
                WorkOrderModel::updateImagePath($workOrderId, $imagePath);
            }
        }

        $workOrder = WorkOrderModel::getWorkOrderData($request->work_order_id);
        $templateType = $this->getTemplateType($tipe);
        $template = PrintTemplateModel::where('name', $templateType)->first();
        $taskOne = $template->details()->where("type", "page-one")->get();
        $taskTwo = $template->details()->where("type", "page-two")->get();

        $baseUrl = "https://www.google.com/maps?q=";
        $mapsUrl = $baseUrl . $workOrder->latitude . "," . $workOrder->longitude;
        $qrCode = QrCode::size(72)->generate($mapsUrl);

        $view = $this->getViewByType($tipe);

        return view($view, compact('workOrder', 'qrCode', 'taskOne', 'taskTwo'));
    }

    public function uploadImage(Request $request)
    {
        try {
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                'work_order_id' => 'required|integer|exists:work_orders,id'
            ]);

            $image = $request->file('image');
            $workOrderId = $request->input('work_order_id');
            $imageExtension = $image->getClientOriginalExtension();
            $imageFileName = $workOrderId . '.' . $imageExtension;
            $imagePath = 'image/work-order/attachment-file/' . $imageFileName;
            $storedImagePath = $image->storeAs('public/' . dirname($imagePath), $imageFileName);
            WorkOrderModel::updateFileCompleteWorkOrderPath($workOrderId, $imagePath);
            WorkOrderModel::updateStatusCompletedWorkOrderSalesOrder($workOrderId);

            return response()->json([
                'success' => true,
                'message' => 'Image uploaded successfully.',
                'image_path' => Storage::url($imagePath)
            ]);
        } catch (\Throwable $th) {
            Log::error($th);
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload image ' . $th->getMessage()
            ]);
        }
    }

    public function printTechnicianReport(Request $request)
    {
        $workOrder = WorkOrderModel::getWorkOrderData($request->id);
        return view('Orders.WorkOrder.Technician.print', compact('workOrder'));
    }

    private function getTemplateType($tipe)
    {
        switch ($tipe) {
            case "regular_dan_instalasi":
                return 'template-print-regular';
            case "tokopedia_dan_instalasi":
                return 'template-print-tokopedia';
            case "tokopedia_tanpa_instalasi":
                return 'template-print-tokopedia-tanpa-instalasi';
            default:
                throw new \InvalidArgumentException('Tipe cetak tidak valid');
        }
    }

    private function getViewByType($tipe)
    {
        switch ($tipe) {
            case "regular_dan_instalasi":
                return 'Orders.WorkOrder.RegularInstalasi.print';
            case "tokopedia_dan_instalasi":
                return 'Orders.WorkOrder.TokopediaInstalasi.print';
            case "tokopedia_tanpa_instalasi":
                return 'Orders.WorkOrder.Tokopedia.print';
            default:
                throw new \InvalidArgumentException('Tipe cetak tidak valid');
        }
    }

    public function destroy(Request $request)
    {
        try {
            $workOrder = WorkOrderModel::find($request->work_order_id);
            $workOrder->delete();
            return response()->json([
                'success' => true,
                'message' => 'Work order deleted successfully.'
            ]);
        } catch (\Throwable $th) {
            Log::error($th);
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete work order.'
            ]);
        }
    }

    public function detail(Request $request)
    {
        $workOrder = WorkOrderModel::getWorkOrderData($request->work_order_id);
        return view('Orders.WorkOrder.detail', compact('workOrder'));
    }
}
