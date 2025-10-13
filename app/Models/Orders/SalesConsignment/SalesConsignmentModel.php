<?php

namespace App\Models\Orders\SalesConsignment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Orders\SalesInvoice\SalesInvoiceModel;
use App\Models\Orders\SalesConsignment\SalesConsignmentBatteriesModel;
use App\Models\MasterData\Distributor\DistributorModel;
use App\Models\MasterData\Distributor\DistributorShopModel;

class SalesConsignmentModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sales_consignments';

    protected $fillable = [
        'sales_consignment_number',
        'vendor_id',
        'vendor_name',
        'ship_to_id',
        'ship_to_name',
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
            'vendor_name',
            'ship_to_name',
            'payment_status',
            'status'
        ];

        $orderColumns = [
            'id',
            'sales_consignment_number',
            'vendor_name',
            'ship_to_name',
            'date',
            'discount',
            'discount_price',
            'subtotal',
            'total_expenses',
            'total',
            'payment_status',
            'status'
        ];

        $query = self::query();

        // Filter by status if provided
        if ($request->has('status') && $request->status !== null && $request->status !== '' && $request->status !== 'all') {
            $query->where('sales_consignments.status', $request->status);
        }

        // Filter by date range if provided
        if ($request->has('date_start') && $request->date_start) {
            $query->where('sales_consignments.date', '>=', $request->date_start);
        }
        if ($request->has('date_end') && $request->date_end) {
            $query->where('sales_consignments.date', '<=', $request->date_end);
        }

        // Filter by vendor if provided
        if ($request->has('vendor_id') && $request->vendor_id) {
            $query->where('sales_consignments.vendor_id', $request->vendor_id);
        }

        // Filter by ship_to if provided
        if ($request->has('ship_to_id') && $request->ship_to_id) {
            $query->where('sales_consignments.ship_to_id', $request->ship_to_id);
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
        $latestCodeModel = self::withTrashed()
            ->orderByDesc('created_at')
            ->first();
        $latestCode = $latestCodeModel ? $latestCodeModel->sales_consignment_number : null;

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
    public function consignmentBatteries()
    {
        return $this->hasMany(
            SalesConsignmentBatteriesModel::class,
            'sales_consignment_id',
            'id'
        )->with('salesInvoice');
    }

    /**
     * Get the distributor (vendor) associated with the sales consignment.
     */
    public function vendor()
    {
        return $this->belongsTo(DistributorModel::class, 'vendor_id', 'id');
    }

    /**
     * Get the distributor shop (ship_to) associated with the sales consignment.
     */
    public function shipTo()
    {
        return $this->belongsTo(DistributorShopModel::class, 'ship_to_id', 'id');
    }
}
