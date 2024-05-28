<?php

namespace App\Models\Orders\WorkOrder;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

// TRAITS
use App\Traits\DataTablesTrait;
use OwenIt\Auditing\Auditable as AuditableTrait;

class WorkOrderModel extends Model implements Auditable
{
    use HasFactory, SoftDeletes, DataTablesTrait, AuditableTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'work_orders';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'work_order_number',
        'date',
        'sales_order_id',
        'customer_id',
        'tax',
        'tax_price',
        'discount_price',
        'discount',
        'total',
        'status',
        'address',
        'latitude',
        'longitude'
    ];

    public static function newCode()
    {
        // Get the latest added code.
        $latestCode = self::withTrashed()
            ->orderByDesc('created_at')
            ->first()?->work_order_number ?? null;

        // Generate the new sales order code.
        $year = substr($latestCode, 2, 2);
        $month = substr($latestCode, 4, 2);
        $currentYear = date('y');
        $currentMonth = date('m');

        $newCode = "WO";
        if ($year == $currentYear) {
            if ($month == $currentMonth) {
                // Generate new code with new iteration only.
                $iteration = substr($latestCode, 6);
                $nextIteration = str_pad((int)$iteration + 1, strlen($iteration), '0', STR_PAD_LEFT);
                $newCode .= $year . $month . $nextIteration;
            } else {
                // Generate new code with new month.
                $newCode .= $year . $currentMonth . '00001';
            }
        } else {
            // Generate new code with new year and new month.
            $newCode .= $currentYear . $currentMonth . '00001';
        }
        return $newCode;
    }

    public function batteries()
    {
        return $this->hasMany(WorkOrderBatteryModel::class, 'work_order_id');
    }

    public function salesOrder()
    {
        return $this->belongsTo(SalesOrderModel::class, 'sales_order_id');
    }
}
