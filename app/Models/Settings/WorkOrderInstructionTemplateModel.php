<?php

namespace App\Models\Settings;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

use OwenIt\Auditing\Auditable as AuditableTrait;

class WorkOrderInstructionTemplateModel extends Model implements Auditable
{
    use HasFactory, AuditableTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'work_order_instruction_templates';

    // The attributes that are mass assignable.
    protected $fillable = [
        'id',
        'work_order_instruction_template_option_id',
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

    // relationship with WorkOrderInstructionTemplateOptionModel
    public function option()
    {
        return $this->belongsTo(WorkOrderInstructionTemplateOptionModel::class, 'work_order_instruction_template_option_id', 'id');
    }
}
