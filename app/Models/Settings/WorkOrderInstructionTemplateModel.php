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

    // The attributes that are mass assignable.
    protected $fillable = [
        'id',
        'name',
        'description',
        'instruction',
        'created_by',
        'updated_by',
    ];

    // relationship with WorkOrderInstructionTemplateDetailsModel
    public function details()
    {
        return $this->hasMany(WorkOrderInstructionTemplateDetailsModel::class, 'work_order_instruction_template_id', 'id');
    }
}
