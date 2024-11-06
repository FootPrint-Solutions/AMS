<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// models
use App\Models\Settings\WorkOrderInstructionTemplateModel;
use App\Models\Settings\WorkOrderInstructionTemplateDetailsModel;

class WorkOrderInstructionTemplate extends Controller
{
    private $title = "Work Order Instruction Template";

    public function index()
    {
        $data = WorkOrderInstructionTemplateModel::with('details')->get();
        // dd($data);
        if ($data->isEmpty()) {
            return view(
                'Settings.WorkOrderInstruction.index',
                getIndexData(
                    $this->title
                )
            );
        } else {
            return view(
                'Settings.WorkOrderInstruction.edit',
                getIndexData(
                    $this->title,
                    $data
                )
            );
        }
    }

    public function store(Request $request)
    {
        $data = $request->all();

        try {
            DB::beginTransaction();

            // delete all data first 
            WorkOrderInstructionTemplateDetailsModel::truncate();
            WorkOrderInstructionTemplateModel::truncate();


            $master = new WorkOrderInstructionTemplateModel();
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
}
