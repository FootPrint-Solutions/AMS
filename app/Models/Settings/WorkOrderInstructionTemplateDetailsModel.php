<?php

namespace App\Models\Settings;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

use OwenIt\Auditing\Auditable as AuditableTrait;

class WorkOrderInstructionTemplateDetailsModel extends Model implements Auditable
{
    use HasFactory, AuditableTrait;

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

    public function template()
    {
        return $this->belongsTo(WorkOrderInstructionTemplateModel::class, 'work_order_instruction_template_id');
    }
}
