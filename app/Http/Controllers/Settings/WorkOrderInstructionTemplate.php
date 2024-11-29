<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// models
use App\Models\Settings\WorkOrderInstructionTemplateModel;
use App\Models\Settings\WorkOrderInstructionTemplateDetailsModel;
use App\Models\Settings\WorkOrderInstructionTemplateOptionModel;

class WorkOrderInstructionTemplate extends Controller
{
    private $title = "Work Order Instruction Template";

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view(
            'Settings.WorkOrderInstruction.option',
            getIndexData(
                $this->title
            )
        );
    }

    public function option($id)
    {
        if (!empty($id)) {
            $dataOption = WorkOrderInstructionTemplateOptionModel::find($id);
            if (is_null($dataOption)) {
                return redirect()->route('dashboard')->with('error', 'Invalid URL.');
            }
            $data = WorkOrderInstructionTemplateModel::with('details', 'option')->orderBy('instruction', 'asc')->where('work_order_instruction_template_option_id', $id)->get();
            $view = $data->isEmpty() ? 'Settings.WorkOrderInstruction.index' : 'Settings.WorkOrderInstruction.edit';
            $data = [
                'data' => $data,
                'dataOption' => $dataOption,
            ];
            return view(
                $view,
                getIndexData(
                    $this->title,
                    $data
                )
            );
        } else {
            return redirect()->route('dashboard')->with('error', 'Invalid URL.');
        }
    }

    public function store(Request $request)
    {
        $data = $request->all();

        try {
            DB::beginTransaction();

            $option = WorkOrderInstructionTemplateOptionModel::find($data['id_template']);
            $option->name = $data['title_template'];
            $option->save();

            $master = new WorkOrderInstructionTemplateModel();
            $master->work_order_instruction_template_option_id = $data['id_template'];
            $master->name = $data['title'];
            $master->description = $data['description'];
            $master->instruction = $data['step'];
            $master->created_by = auth()->user()->id;
            $master->updated_by = auth()->user()->id;
            $master->save();

            // check if data['input_question'] is not empty
            if (!empty($data['input_question'])) {
                foreach ($data['input_question'] as $index => $question) {
                    // Save detail for each instruction
                    WorkOrderInstructionTemplateDetailsModel::create([
                        'work_order_instruction_template_id' => $master->id,
                        'instruction' => $question,
                        'type' => $data['input_type'][$index],
                        'group' => $data['input_group'][$index],
                        'is_required' => filter_var($data['input_required'][$index], FILTER_VALIDATE_BOOLEAN),
                        'created_by' => auth()->user()->id,
                        'updated_by' => auth()->user()->id,
                    ]);
                }
            }

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Data saved successfully.',
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
            ]);
        }
    }

    public function update(Request $request)
    {
        $data = $request->all();

        try {
            DB::beginTransaction();

            $option = WorkOrderInstructionTemplateOptionModel::find($data['id_template']);
            $option->name = $data['title_template'];
            $option->save();

            $master = WorkOrderInstructionTemplateModel::updateOrCreate(
                ['id' => $data['id'] ?? null],
                [
                    'work_order_instruction_template_option_id' => $data['id_template'],
                    'name' => $data['title'],
                    'description' => $data['description'],
                    'instruction' => $data['step'],
                    'created_by' => auth()->user()->id,
                    'updated_by' => auth()->user()->id,
                ]
            );

            // check if data['input_question'] is not empty
            if (!empty($data['input_question'])) {
                // delete all details first
                WorkOrderInstructionTemplateDetailsModel::where('work_order_instruction_template_id', $master->id)->delete();

                foreach ($data['input_question'] as $index => $question) {
                    // Save detail for each instruction
                    WorkOrderInstructionTemplateDetailsModel::create([
                        'work_order_instruction_template_id' => $master->id,
                        'instruction' => $question,
                        'type' => $data['input_type'][$index],
                        'group' => $data['input_group'][$index],
                        'is_required' => filter_var($data['input_required'][$index], FILTER_VALIDATE_BOOLEAN),
                        'created_by' => auth()->user()->id,
                        'updated_by' => auth()->user()->id,
                    ]);
                }
            } else {
                // delete all details first
                WorkOrderInstructionTemplateDetailsModel::where('work_order_instruction_template_id', $master->id)->delete();
            }

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Data updated successfully.',
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
            ]);
        }
    }

    public function destroy(Request $request)
    {
        $data = $request->all();

        try {
            DB::beginTransaction();

            WorkOrderInstructionTemplateDetailsModel::where('work_order_instruction_template_id', $data['id'])->delete();
            WorkOrderInstructionTemplateModel::where('id', $data['id'])->delete();

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Data deleted successfully.',
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
            ]);
        }
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

        // Get User data (rows and count).
        $data = WorkOrderInstructionTemplateOptionModel::allForDataTables($request);

        // Set rows to be displayed in User table.
        $rows = [];
        $no = $start + 1;
        foreach ($data["row"] as $key) {
            $key->step_total = WorkOrderInstructionTemplateModel::where('work_order_instruction_template_option_id', $key->id)->count();
            if ($key->status == 1) {
                $key->status = '<span class="badge badge-success">Active</span>';
            } else {
                $key->status = '<span class="badge badge-danger">Inactive</span>';
            }

            $row = [];
            $row[] = $no++;
            $row[] = $key->name;
            $row[] = $key->step_total;
            $row[] = $key->status;
            $row[] = $key->id;
            $rows[] = $row;
        }

        return response()->json(array(
            "draw" => $draw,
            "recordsTotal" => WorkOrderInstructionTemplateOptionModel::count(),
            "recordsFiltered" => $data["count"],
            "data" => $rows
        ));
    }

    /**
     * Store a newly created Work Order Instruction Template Option in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     *
     * This method handles the creation of a new Work Order Instruction Template Option.
     * It begins a database transaction, creates a new instance of WorkOrderInstructionTemplateOptionModel,
     * sets its attributes, and saves it to the database. If the operation is successful, it commits the transaction
     * and returns a success response. If an error occurs, it rolls back the transaction and returns an error response.
     *
     * @throws \Throwable
     */
    public function storeOption(Request $request)
    {
        $data = $request->all();

        try {
            DB::beginTransaction();

            $master = new WorkOrderInstructionTemplateOptionModel();
            $master->name = $data['name'];
            $master->status = 0;
            $master->created_by = auth()->user()->id;
            $master->updated_by = auth()->user()->id;
            $master->save();

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Data saved successfully.',
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
            ]);
        }
    }

    /**
     * Destroy the specified option from storage.
     *
     * This method handles the deletion of a work order instruction template option.
     * It performs the deletion within a database transaction to ensure data integrity.
     * If any exception occurs during the process, the transaction is rolled back.
     *
     * @param \Illuminate\Http\Request $request The request instance containing the data to be deleted.
     * @return \Illuminate\Http\JsonResponse A JSON response indicating the success or failure of the operation.
     */
    public function destroyOption(Request $request)
    {
        $data = $request->all();

        try {
            DB::beginTransaction();
            $option = WorkOrderInstructionTemplateOptionModel::find($data['id']);
            if ($option->status == 1) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cannot delete an active option.',
                ]);
            }

            WorkOrderInstructionTemplateModel::where('work_order_instruction_template_option_id', $data['id'])->delete();
            WorkOrderInstructionTemplateOptionModel::where('id', $data['id'])->delete();

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Data deleted successfully.',
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
            ]);
        }
    }

    public function toggleStatusOption(Request $request)
    {
        $data = $request->all();

        try {
            DB::beginTransaction();

            $status = WorkOrderInstructionTemplateOptionModel::find($data['id']);
            $status->status = !$status->status;
            $status->save();

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Data updated successfully.',
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
            ]);
        }
    }
}
