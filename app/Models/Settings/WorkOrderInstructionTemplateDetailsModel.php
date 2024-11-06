<?php

namespace App\Models\Settings;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkOrderInstructionTemplateDetailsModel extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'work_order_instruction_template_details';

    // The attributes that are mass assignable.
    protected $fillable = [
        'work_order_instruction_template_id',
        'instruction',
        'type',
        'group',
        'is_required',
        'created_by',
        'updated_by',
    ];
}
