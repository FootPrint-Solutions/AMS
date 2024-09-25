<?php

namespace App\Models\Orders\WorkOrderInstruction;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

// TRAITS
use App\Traits\DataTablesTrait;
use OwenIt\Auditing\Auditable as AuditableTrait;

// MODEL
use App\Models\Orders\WorkOrder\WorkOrderModel;
use App\Models\Orders\WorkOrderInstruction\WorkOrderInstructionPhotosModel;

class WorkOrderInstructionModel extends Model implements Auditable
{
    use HasFactory, SoftDeletes, DataTablesTrait, AuditableTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'work_order_instructions';

    public static function allForDataTables($request)
    {
        $selectColumns = [
            'work_order_instructions.id',
            'work_orders.work_order_number',
            'sales_orders.sales_order_number',
            'work_order_instructions.date',
            'customers.name',
            'work_orders.total',
            'sales_orders.address',
            'work_order_instructions.work_order_instruction_number',
            'work_order_instructions.date_complete',
        ];

        $searchColumns = [
            'work_order_instructions.id',
            'work_orders.work_order_number',
            'sales_orders.sales_order_number',
            'work_order_instructions.date',
            'customers.name',
            'work_orders.total',
            'sales_orders.address',
            'work_order_instructions.date_complete',
        ];

        $query = self::query();
        $query->select($selectColumns)
            ->join('work_orders', 'work_order_instructions.work_order_id', '=', 'work_orders.id')
            ->join('sales_orders', 'work_orders.sales_order_id', '=', 'sales_orders.id')
            ->join('customers', 'sales_orders.customer_id', '=', 'customers.id');

        if (!empty($request->search['value'])) {
            $searchValue = $request->search['value'];
            $query->where(function ($q) use ($searchColumns, $searchValue) {
                foreach ($searchColumns as $column) {
                    $q->orWhere($column, 'LIKE', '%' . $searchValue . '%');
                }
            });
        }

        // if status is set, filter by status
        if (!empty($request->status)) {
            if ($request->status == 'complete') {
                $query->whereNotNull('work_order_instructions.date_complete');
            } else if ($request->status == 'uncomplete') {
                $query->whereNull('work_order_instructions.date_complete');
            } else {
                $query->where('work_order_instructions.date_complete', $request->status);
            }
        }

        return self::getAllRows($request, $query, $selectColumns, $searchColumns, ['column' => 'work_order_instructions.updated_at', 'direction' => 'desc']);
    }

    public static function newCode()
    {
        // Get the latest added code.
        $latestCode = self::withTrashed()
            ->orderByDesc('created_at')
            ->first()?->work_order_instruction_number ?? null;

        // Generate the new sales order code.
        $year = substr($latestCode, 2, 2);
        $month = substr($latestCode, 4, 2);
        $currentYear = date('y');
        $currentMonth = date('m');

        $newCode = "WI";
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

    // RELATIONSHIPS WITH WORK ORDER
    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrderModel::class, 'work_order_id')
            ->with('salesOrder', 'customer');
    }


    public function photos(): HasMany
    {
        return $this->hasMany(WorkOrderInstructionPhotosModel::class, 'work_order_instruction_id');
    }
}
