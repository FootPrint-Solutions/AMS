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
        ];

        $searchColumns = [
            'work_order_instructions.id',
            'work_orders.work_order_number',
            'sales_orders.sales_order_number',
            'work_order_instructions.date',
            'customers.name',
            'work_orders.total',
            'sales_orders.address',
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

        return self::getAllRows($request, $query, $selectColumns, $searchColumns, ['column' => 'work_order_instructions.updated_at', 'direction' => 'desc']);
    }
}
