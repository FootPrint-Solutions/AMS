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
use App\Traits\TracksUpdates;

// MODEL
use App\Models\Orders\WorkOrder\WorkOrderModel;
use App\Models\Orders\WorkOrderInstruction\WorkOrderInstructionPhotosModel;
use App\Models\User;

class WorkOrderInstructionModel extends Model implements Auditable
{
    use HasFactory, SoftDeletes, DataTablesTrait, AuditableTrait, TracksUpdates;

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
            'users.name as updated_by',
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
            'users.name',
        ];

        $query = self::query();
        $query->select($selectColumns)
            ->join('work_orders', 'work_order_instructions.work_order_id', '=', 'work_orders.id')
            ->join('sales_orders', 'work_orders.sales_order_id', '=', 'sales_orders.id')
            ->join('customers', 'sales_orders.customer_id', '=', 'customers.id')
            ->leftJoin('users', 'work_order_instructions.updated_by', '=', 'users.id');


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

    public static function lazyLoadList($request)
    {
        $search = $request->input("search");
        $order = $request->input("order.0.column");
        $dir = $request->input("order.0.dir");
        $start = $request->input("offset");
        $length = $request->input("limit");

        // get data from table work order for datatable
        $data = self::select('work_order_instructions.id', 'work_order_instructions.work_order_instruction_number', 'work_order_instructions.date', 'work_order_instructions.date_complete', 'work_order_instructions.work_order_id', 'work_order_instructions.created_at', 'work_order_instructions.updated_at', 'work_orders.work_order_number', 'sales_orders.sales_order_number', 'customers.name', 'sales_orders.total')
            ->join('work_orders', 'work_order_instructions.work_order_id', '=', 'work_orders.id')
            ->join('sales_orders', 'work_orders.sales_order_id', '=', 'sales_orders.id')
            ->join('customers', 'sales_orders.customer_id', '=', 'customers.id')
            ->where('work_order_instructions.work_order_instruction_number', 'like', '%' . $search . '%')
            ->orWhere('work_order_instructions.date', 'like', '%' . $search . '%')
            ->orWhere('work_order_instructions.date_complete', 'like', '%' . $search . '%')
            ->orWhere('work_orders.work_order_number', 'like', '%' . $search . '%')
            ->orWhere('sales_orders.sales_order_number', 'like', '%' . $search . '%')
            ->orWhere('customers.name', 'like', '%' . $search . '%')
            ->orderBy('work_order_instructions.id', "desc")
            ->skip($start)
            ->take($length)
            ->get();

        $count = self::select('work_order_instructions.id', 'work_order_instructions.work_order_instruction_number', 'work_order_instructions.date', 'work_order_instructions.date_complete', 'work_order_instructions.work_order_id', 'work_order_instructions.created_at', 'work_order_instructions.updated_at')
            ->join('work_orders', 'work_order_instructions.work_order_id', '=', 'work_orders.id')
            ->join('sales_orders', 'work_orders.sales_order_id', '=', 'sales_orders.id')
            ->join('customers', 'sales_orders.customer_id', '=', 'customers.id')
            ->where('work_order_instructions.work_order_instruction_number', 'like', '%' . $search . '%')
            ->orWhere('work_order_instructions.date', 'like', '%' . $search . '%')
            ->orWhere('work_order_instructions.date_complete', 'like', '%' . $search . '%')
            ->orWhere('work_orders.work_order_number', 'like', '%' . $search . '%')
            ->orWhere('sales_orders.sales_order_number', 'like', '%' . $search . '%')
            ->orWhere('customers.name', 'like', '%' . $search . '%')
            ->count();


        return [
            'row' => $data,
            'count' => $count
        ];
    }

    // user relationship
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
