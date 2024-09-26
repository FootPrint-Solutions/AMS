<?php

namespace App\Http\Controllers\Orders;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

// Models
use App\Models\Orders\WorkOrderInstruction\WorkOrderInstructionModel;
use App\Models\Orders\WorkOrderInstruction\WorkOrderInstructionPhotosModel;

class WorkOrderInstruction extends Controller
{
    private $title = 'Work Order Instruction';

    public function index()
    {
        return view(
            'Orders.WorkOrderInstruction.index',
            getIndexData(
                $this->title
            )
        );
    }

    /**
     * Display a listing of the work order instructions.
     *
     * This method handles the DataTables request and returns a JSON response
     * containing the work order instructions data.
     *
     * @param \Illuminate\Http\Request $request The incoming request instance.
     * 
     * @return \Illuminate\Http\JsonResponse The JSON response containing the work order instructions data.
     * 
     * The JSON response structure:
     * - draw: The draw counter for DataTables.
     * - recordsTotal: The total number of work order instructions.
     * - recordsFiltered: The number of filtered work order instructions.
     * - data: An array of work order instructions data, each containing:
     *   - Row number
     *   - Work order number
     *   - Sales order number
     *   - Formatted date
     *   - Customer name
     *   - Quantity
     *   - Formatted total price
     *   - Address
     *   - ID
     *   - Status
     */
    public function show(Request $request)
    {
        // Get all DataTables requests.
        $draw = $request->input("draw");
        $start = $request->input("start");

        // Get customer data (rows and count).
        $data = WorkOrderInstructionModel::allForDataTables($request);

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
            $row[] = $key->work_order_instruction_number;
            $row[] = formatDate($key->date);
            $row[] = $key->date_complete ? formatDateTime($key->date_complete) : '';
            $row[] = $key->name;
            $row[] = formatPrice($key->total);
            $row[] = $key->address;
            $row[] = $key->id;
            $row[] = $key->status;
            $rows[] = $row;
        }

        return response()->json(array(
            "draw" => $draw,
            "recordsTotal" => WorkOrderInstructionModel::count(),
            "recordsFiltered" => $data["count"],
            "data" => $rows
        ));
    }

    /**
     * Display the details of a specific work order instruction.
     *
     * This method retrieves the details of a work order instruction based on the provided ID.
     * If the ID is "login", it redirects the user to the login route.
     * Otherwise, it fetches the work order instruction details from the database and returns
     * the view with the retrieved data.
     *
     * @param string $id The ID of the work order instruction or the string "login".
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View The redirect response to the login route or the view with the work order instruction details.
     */
    public function InstructionDetail($id)
    {
        if ($id == "login") {
            return redirect()->route('login');
        } else {
            // uniq id to work order instruction number
            $temp = $id . '' . rand(1000, 9999);
            session(['temp_wo_instruction' => $temp]);

            $workOrderInstruction = WorkOrderInstructionModel::with('workOrder')->where('work_order_instruction_number', $id)->first();
            // dd($workOrderInstruction);
            return view(
                'Orders.WorkOrderInstruction.details',
                getIndexData(
                    $this->title,
                    $workOrderInstruction
                )
            );
        }
    }

    /**
     * Deletes a Work Order Instruction based on the provided request ID.
     *
     * @param \Illuminate\Http\Request $request The request object containing the ID of the Work Order Instruction to be deleted.
     * @return \Illuminate\Http\JsonResponse A JSON response indicating the status and message of the deletion operation.
     */
    public function destroy(Request $request)
    {
        $workOrderInstruction = WorkOrderInstructionModel::find($request->id);
        $workOrderInstruction->delete();
        return response()->json([
            'status' => 'Success',
            'message' => 'Work Order Instruction has been deleted'
        ]);
    }

    /**
     * Update the Work Order Instruction.
     *
     * This method handles the update of a Work Order Instruction, including the upload of images
     * and updating the related database records.
     *
     * @param \Illuminate\Http\Request $request The incoming request instance containing the data to update.
     * 
     * @return \Illuminate\Http\JsonResponse A JSON response indicating the status of the update operation.
     * 
     * @throws \Throwable If an error occurs during the update process.
     * 
     * The request is expected to contain the following:
     * - 'step8-FotoStiker': The image file for step 8.
     * - 'step9-FotoNomorProduksi': The image file for step 9.
     * - 'step9-FotoAkiDalamKapMesin': The image file for step 9.
     * - 'work_order_instruction_id': The ID of the work order instruction to update.
     * - 'date_complete' (optional): The completion date of the work order instruction.
     * 
     * The method performs the following actions:
     * 1. Retrieves the images from the request.
     * 2. Moves the images to the public storage path.
     * 3. Finds the WorkOrderInstructionModel by ID and updates the completion date.
     * 4. Updates or creates records in the WorkOrderInstructionPhotosModel for each image.
     * 5. Returns a JSON response indicating success or failure.
     */
    public function update(Request $request)
    {
        try {

            // db transaction
            DB::beginTransaction();

            $workOrderInstruction = WorkOrderInstructionModel::find($request->work_order_instruction_id);
            $workOrderInstruction->date_complete = $request->date_complete ?? date('Y-m-d H:i:s');
            $workOrderInstruction->save();


            WorkOrderInstructionPhotosModel::updateOrCreate(
                [
                    'work_order_instruction_id' => $request->work_order_instruction_id,
                    'step' => 'step8'
                ],
                [
                    'image' => session('step-8')
                ]
            );

            WorkOrderInstructionPhotosModel::updateOrCreate(
                [
                    'work_order_instruction_id' => $request->work_order_instruction_id,
                    'step' => 'step9',
                    'image' => session('step-9-1')
                ]
            );

            WorkOrderInstructionPhotosModel::updateOrCreate(
                [
                    'work_order_instruction_id' => $request->work_order_instruction_id,
                    'step' => 'step9',
                    'image' => session('step-9-2')
                ]
            );

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Work Order Instruction has been updated',
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            // Log the error
            Log::error($th);

            return response()->json([
                'status' => 'Error',
                'message' => 'Failed to update Work Order Instruction',
            ]);
        }
    }

    public function detail(Request $request)
    {
        $workOrderInstruction = WorkOrderInstructionModel::with('workOrder', 'photos')->find($request->work_order_id);
        return response()->json([
            'status' => 'success',
            'message' => 'Work Order Instruction data',
            'data' => $workOrderInstruction
        ]);
    }

    public function lazyLoadList(Request $request)
    {
        try {
            $workOrders = WorkOrderInstructionModel::lazyLoadList($request);
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

    public function uploadImage(Request $request)
    {
        try {
            $image1 = $request->file('step8-FotoStiker');
            $image2 = $request->file('step9-FotoNomorProduksi');
            $image3 = $request->file('step9-FotoAkiDalamKapMesin');
            $ket = $request->input('ket');

            if ($ket == 'step-8-image') {
                if ($image1) {
                    $image1->move(public_path('storage/image/work-order/instruction'), $image1->getClientOriginalName());
                }

                session(['step-8' => $image1->getClientOriginalName()]);
            } else if ($ket == 'step-9-1-image') {
                if ($image2) {
                    $image2->move(public_path('storage/image/work-order/instruction'), $image2->getClientOriginalName());
                }

                session(['step-9-1' => $image2->getClientOriginalName()]);
            } else if ($ket == 'step-9-2-image') {
                if ($image3) {
                    $image3->move(public_path('storage/image/work-order/instruction'), $image3->getClientOriginalName());
                }

                session(['step-9-2' => $image3->getClientOriginalName()]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Image uploaded successfully',
                'data' =>  $request->all()
            ]);
        } catch (\Throwable $th) {
            Log::error($th);
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to upload image' . $th->getMessage()
            ]);
        }
    }
}
