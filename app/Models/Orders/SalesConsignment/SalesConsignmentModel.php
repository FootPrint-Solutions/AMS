<?php

namespace App\Models\Orders\SalesConsignment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Orders\SalesInvoice\SalesInvoiceModel;
use App\Models\Orders\SalesConsignment\SalesConsignmentBatteriesModel;

class SalesConsignmentModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sales_consignments';

    protected $fillable = [
        'sales_consignment_number',
        'date',
        'discount',
        'discount_price',
        'subtotal',
        'total_expenses',
        'total',
        'payment_status',
        'status',
    ];

    protected $dates = [
        'date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Get all data for DataTables.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public static function allForDataTables($request)
    {
        $selectColumns = [
            'sales_consignments.*'
        ];
        $searchColumns = [
            'sales_consignment_number',
        ];

        $orderColumns = [
            'id',
            'sales_consignment_number',
            'date',
            'customer_name',
            'vehicle_name',
            'shop_name',
            'distributor_name',
            'technician_name',
            'total',
            'payment_method_name',
            'status'
        ];

        $query = self::query();

        if ($request->dateStart && $request->dateEnd) {
            $query->whereBetween('sales_consignments.date', [$request->dateStart, $request->dateEnd]);
        }

        $query->select($selectColumns);

        // DataTables pagination and search
        $draw = $request->input("draw");
        $start = $request->input("start", 0);
        $length = $request->input("length", 10);
        $searchValue = $request->input("search.value");

        // Search
        if ($searchValue) {
            $query->where(function ($q) use ($searchColumns, $searchValue) {
                foreach ($searchColumns as $col) {
                    $q->orWhere($col, 'like', '%' . $searchValue . '%');
                }
            });
        }

        // Order
        $orderColIndex = $request->input("order.0.column");
        $orderDir = $request->input("order.0.dir", "asc");
        if (isset($orderColumns[$orderColIndex])) {
            $query->orderBy($orderColumns[$orderColIndex], $orderDir);
        } else {
            $query->orderBy('sales_consignments.id', 'desc');
        }

        $countQuery = clone $query;
        $count = $countQuery->count();

        $rows = $query->skip($start)->take($length)->get();

        return [
            "row" => $rows,
            "count" => $count
        ];
    }

    /**
     * Generate new sales invoice code.
     */
    public static function newCode()
    {
        $latestCode = self::withTrashed()
            ->orderByDesc('created_at')
            ->first()?->sales_consignment_number ?? null;

        $year = substr($latestCode, 2, 2);
        $month = substr($latestCode, 4, 2);
        $currentYear = date('y');
        $currentMonth = date('m');

        $newCode = "SC";
        if ($year == $currentYear) {
            if ($month == $currentMonth) {
                $iteration = substr($latestCode, 6);
                $nextIteration = str_pad((int)$iteration + 1, strlen($iteration), '0', STR_PAD_LEFT);
                $newCode .= $year . $month . $nextIteration;
            } else {
                $newCode .= $year . $currentMonth . '00001';
            }
        } else {
            $newCode .= $currentYear . $currentMonth . '00001';
        }
        return $newCode;
    }

    /**
     * Get the batteries associated with the sales consignment.
     */
    public function consignmentBatteries(): BelongsToMany
    {
        return $this->belongsToMany(
            SalesConsignmentBatteriesModel::class,
            'sales_consignment_batteries',
            'sales_consignment_id',
            'battery_id'
        )->withTimestamps();
    }
}
