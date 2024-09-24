<?php

namespace App\Http\Controllers\Orders;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// Models
use App\Models\Orders\WorkOrderInstruction\WorkOrderInstructionModel;

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
            $row[] = $key->date_complete ? formatDate($key->date_complete) : '';
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

    public function InstructionDetail($id)
    {
        if ($id == "login") {
            return redirect()->route('login');
        } else {
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

    public function destroy(Request $request)
    {
        $workOrderInstruction = WorkOrderInstructionModel::find($request->id);
        $workOrderInstruction->delete();
        return response()->json([
            'status' => 'Success',
            'message' => 'Work Order Instruction has been deleted'
        ]);
    }
}
