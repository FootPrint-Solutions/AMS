<?php

namespace App\Models\Orders\WorkOrder;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

use App\Traits\DataTablesTrait;
use OwenIt\Auditing\Auditable as AuditableTrait;

class WorkOrderBatteryModel extends Model implements Auditable
{
    use HasFactory, SoftDeletes, DataTablesTrait, AuditableTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'work_order_battery';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'work_order_id',
        'battery_id',
        'battery_name',
        'battery_price',
        'quantity'
    ];

    public function workOrder()
    {
        return $this->belongsTo(WorkOrderModel::class, 'work_order_id');
    }
}
