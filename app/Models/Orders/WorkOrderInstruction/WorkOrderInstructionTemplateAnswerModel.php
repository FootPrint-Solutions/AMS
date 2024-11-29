<?php

namespace App\Models\Orders\WorkOrderInstruction;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkOrderInstructionTemplateAnswerModel extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'work_order_instruction_template_answers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'work_order_id',
        'work_order_instruction_id',
        'work_order_instruction_template_detail_id',
        'name',
        'description',
        'instruction',
        'instruction_step',
        'type',
        'group',
        'is_required',
        'answer',
        'created_by',
        'updated_by',
    ];
}
