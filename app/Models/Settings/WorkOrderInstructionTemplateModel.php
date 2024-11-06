<?php

namespace App\Models\Settings;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkOrderInstructionTemplateModel extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'work_order_instruction_templates';

    // relationship with WorkOrderInstructionTemplateDetailsModel
    public function details()
    {
        return $this->hasMany(WorkOrderInstructionTemplateDetailsModel::class, 'work_order_instruction_template_id', 'id');
    }
}
