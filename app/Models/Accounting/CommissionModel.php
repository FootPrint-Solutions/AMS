<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\DataTablesTrait;

use App\Models\Accounting\CommissionItemModel;

class CommissionModel extends Model
{
    use HasFactory, SoftDeletes, DataTablesTrait;

    protected $table = 'commissions';

    protected $fillable = [
        'commission_number',
        'date',
        'total',
        'status',
    ];

    protected $casts = [
        'date' => 'date',
        'total' => 'float',
    ];

    private static $selectColumns = [
        'id',
        'commission_number',
        'date',
        'total',
        'status',
    ];

    public static function allForDataTables($request): array
    {
        $selectColumns = ['id', 'commission_number', 'date', 'total', 'status'];
        $searchColumns = ['commission_number', 'date', 'total', 'status'];

        $query = self::query();
        $query->select(self::$selectColumns);

        //     d._token = '{{ csrf_token() }}';
        // d.status = $('#commission-status-filter').val();
        // d.dateStart = $('#input-commission-date-start').val();
        // d.dateEnd = $('#input-commission-date-end').val();

        if (!empty($request->status) && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if (!empty($request->dateStart)) {
            $query->whereDate('date', '>=', $request->dateStart);
        }

        if (!empty($request->dateEnd)) {
            $query->whereDate('date', '<=', $request->dateEnd);
        }

        return self::getAllRows($request, $query, $selectColumns, $searchColumns);
    }

    public static function generateCommissionNumber()
    {
        // Get the latest added code.
        $latestCode = self::withTrashed()
            ->where('commission_number', 'like', 'CM%')
            ->orderByDesc('commission_number')
            ->value('commission_number');

        // Generate the new commission number.
        $year = substr($latestCode, 2, 2);
        $month = substr($latestCode, 4, 2);
        $currentYear = date('y');
        $currentMonth = date('m');

        $newCode = "CM";
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

    public function items()
    {
        return $this->hasMany(CommissionItemModel::class, 'commission_id')->with(['distributorShop', 'salesOrderBattery.salesOrder', 'battery', 'debitAccount', 'creditAccount'])->orderBy('id');
    }
}
