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
use App\Models\MasterData\Company\CompanyModel;
use App\Models\Orders\SalesOrder\SalesOrderBatteryModel;
use App\Models\Orders\SalesOrder\SalesOrderModel;
use App\Models\Orders\WorkOrder\TrackingModel;
use Illuminate\Support\Facades\DB;

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
        $invoice = $request->invoice_check;

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
        $qrCode = QrCode::size(60)->generate($mapsUrl);

        $view = $this->getViewByType($tipe);

        $company = CompanyModel::first();
        return view($view, compact('workOrder', 'qrCode', 'taskOne', 'taskTwo', 'company'));
    }

    public function printMobile(request $request)
    {
        $validationRules = [
            'work_order_id' => 'required|integer|exists:work_orders,id'
        ];

        $tipe = $request->print_option;
        $invoice = $request->invoice_check;

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
        $qrCode = QrCode::size(60)->generate($mapsUrl);

        $view = $this->getViewByType(
            $tipe,
            true
        );

        $company = CompanyModel::first();
        return view($view, compact('workOrder', 'qrCode', 'taskOne', 'taskTwo', 'company'));
    }

    public function uploadImage(Request $request)
    {
        DB::beginTransaction();

        try {
            // jika ada inputan image 
            if (!$request->hasFile('image')) {
            } else {
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
            }

            if ($request->battery_id) {
                // looping battery id and update production code
                foreach ($request->battery_id as $key => $value) {
                    $battery = SalesOrderBatteryModel::find($value);
                    $battery->battery_production_code = $request->production_code[$key];
                    if ($request->hasFile('battery_image') && isset($request->file('battery_image')[$key]))
                        $battery->image = basename($request->file("battery_image")[$key]->store("public/image/work-order/complete-image-file"));

                    $battery->save();
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data has been saved successfully.'
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
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

        // qrcode contain work order id
        $qrCode = QrCode::size(60)->generate($workOrder->id);
        return view('Orders.WorkOrder.Technician.print', compact('workOrder', 'qrCode'));
    }

    public function printTechnicianReportMobile(Request $request)
    {
        $workOrder = WorkOrderModel::getWorkOrderData($request->id);

        // qrcode contain work order id
        $qrCode = QrCode::size(60)->generate($workOrder->id);
        return view('Mobile.Orders.WorkOrder.print', compact('workOrder', 'qrCode'));
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

    private function getViewByType($tipe, $mobile = false)
    {
        $mobile = $mobile ? "Mobile." : "";

        switch ($tipe) {
            case "regular_dan_instalasi":
                return $mobile . 'Orders.WorkOrder.RegularInstalasi.print';
            case "tokopedia_dan_instalasi":
                return $mobile . 'Orders.WorkOrder.TokopediaInstalasi.print';
            case "tokopedia_tanpa_instalasi":
                return $mobile . 'Orders.WorkOrder.Tokopedia.print';
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

    public function getProductionCode(Request $request)
    {
        try {
            $workOrder = WorkOrderModel::getWorkOrderData($request->work_order_id);
            return response()->json([
                'status' => true,
                'production_code' => $workOrder
            ]);
        } catch (\Throwable $th) {
            Log::error($th);
            return response()->json([
                'status' => false,
                'message' => 'Failed to get production code.'
            ]);
        }
    }

    public function lazyLoadList(Request $request)
    {
        try {
            $workOrders = WorkOrderModel::lazyLoadList($request);
            return response()->json([
                'status' => true,
                'work_orders' => $workOrders
            ]);
        } catch (\Throwable $th) {
            Log::error($th);
            return response()->json([
                'status' => false,
                'message' => 'Failed to get work orders.'
            ]);
        }
    }

    public function getWorkOrderDetail(Request $request)
    {
        try {
            $workOrder = WorkOrderModel::getWorkOrderData($request->work_order_id);
            return response()->json([
                'status' => true,
                'work_order' => $workOrder
            ]);
        } catch (\Throwable $th) {
            Log::error($th);
            return response()->json([
                'status' => false,
                'message' => 'Failed to get work order detail.'
            ]);
        }
    }

    /**
     * 
     */
    public function startTracking(Request $request)
    {
        DB::beginTransaction();

        try {
            $workOrderId = $request->workOrderId;
            $currentLat = $request->currentLat;
            $currentLon = $request->currentLon;
            $order = WorkOrderModel::with('salesOrder')->find($workOrderId);

            if ($order->status == 'completed')
                throw new Exception("Unable to start tracking for completed work order.");

            if (TrackingModel::where('work_order_id', $order->id)->exists())
                throw new Exception("There is already a tracking process for the current work order.");

            // Save tracking.
            $tracking = new TrackingModel();
            $tracking->work_order_id = $order->id;
            $tracking->latitude_start = $currentLat;
            $tracking->longitude_start = $currentLon;
            $tracking->latitude_current = $currentLat;
            $tracking->longitude_current = $currentLon;
            $tracking->latitude_destination = $order->latitude;
            $tracking->longitude_destination = $order->longitude;
            $status = $tracking->save();

            if ($status)
                DB::commit();
            else
                DB::rollBack();

            return getResponseData(
                $status,
                $status ? "The tracking process was successfully started!" : "Failed to start the tracking process!"
            );
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return getResponseData(false);
        }
    }

    /**
     * 
     */
    public function updateTracking(Request $request)
    {
        DB::beginTransaction();

        try {
            $workOrderId = $request->workOrderId;
            $currentLat = $request->currentLat;
            $currentLon = $request->currentLon;

            // Save tracking.
            $tracking = TrackingModel::where('work_order_id', $workOrderId)->first();

            if ($tracking->latitude_current != $currentLat || $tracking->longitude_current != $currentLon) {
                $tracking->latitude_current = $currentLat;
                $tracking->longitude_current = $currentLon;
                $status = $tracking->save();

                if ($status)
                    DB::commit();
                else
                    DB::rollBack();

                return getResponseData(
                    $status,
                    $status ? "The tracking process was successfully updated!" : "Failed to update the tracking process!"
                );
            }
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return getResponseData(false);
        }
    }

    /**
     * 
     */
    public function endTracking(Request $request)
    {
        DB::beginTransaction();

        try {
            $workOrderId = $request->workOrderId;
            $currentLat = $request->currentLat;
            $currentLon = $request->currentLon;

            // Save tracking.
            $tracking = TrackingModel::where('work_order_id', $workOrderId)->first();

            if (!$tracking)
                throw new Exception("No tracking process for the current work order.");

            $tracking->latitude_end = $currentLat;
            $tracking->longitude_end = $currentLon;
            $status = $tracking->save();

            if ($status)
                DB::commit();
            else
                DB::rollBack();

            return getResponseData(
                $status,
                $status ? "The tracking process was successfully ended!" : "Failed to end the tracking process!"
            );
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return getResponseData(false);
        }
    }

    /**
     *  Get tracking data.
     */
    public function trackingOrder($id)
    {
        try {
            $workOrderId = $id;
            $tracking = TrackingModel::where('work_order_id', $workOrderId)->first();

            // return view
            return view('Orders.Tracking.index', compact('tracking'));
        } catch (\Throwable $th) {
            Log::error($th);
            return response()->json([
                'status' => false,
                'message' => 'Failed to get tracking data. ' . $th->getMessage()
            ]);
        }
    }

    public function trackingOrderLive($id)
    {
        try {
            $workOrderId = $id;
            $tracking = TrackingModel::where('work_order_id', $workOrderId)->first();

            // return json 
            return response()->json([
                'status' => true,
                'tracking' => $tracking
            ]);
        } catch (\Throwable $th) {
            Log::error($th);
            return response()->json([
                'status' => false,
                'message' => 'Failed to get tracking data. ' . $th->getMessage()
            ]);
        }
    }
}
