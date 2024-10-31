<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WorkOrderInstructionTemplate extends Controller
{
    private $title = "Work Order Instruction Template";

    public function index()
    {
        return view(
            'Settings.WorkOrderInstruction.index',
            getIndexData(
                $this->title
            )
        );
    }
}
