<?php

namespace App\Models\Accounting;

use App\Models\Accounting\ChartOfAccountModel;
use App\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// TRAITS
use App\Traits\DataTablesTrait;

class AccountingReceiveModel extends Model
{
    use HasFactory, SoftDeletes, DataTablesTrait;

    protected $table = 'accounting_receive';

    protected $fillable = [
        'voucher_number',
        'to',
        'bank_account_no',
        'address',
        'account_id',
        'account_name',
        'date',
        'total',
        'status',
        'type',
        'note',
        'created_by',
        'updated_by',
    ];

    protected $dates = [
        'date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'date' => 'date',
        'total' => 'double',
    ];

    private static $selectColumns = [
        'id',
        'voucher_number',
        'to',
        'bank_account_no',
        'address',
        'account_id',
        'account_name',
        'date',
        'total',
        'status',
        'type',
        'note',
        'created_by',
        'updated_by',
    ];

    public static function generateVoucherNumber()
    {
        $latestCodeModel = self::withTrashed()
            ->orderByDesc('created_at')
            ->first();
        $latestCode = $latestCodeModel ? $latestCodeModel->voucher_number : null;

        $currentYear = date('y');
        $currentMonth = date('m');

        if (empty($latestCode) || strlen((string) $latestCode) < 7) {
            return 'RE' . $currentYear . $currentMonth . '00001';
        }

        $year = substr($latestCode, 2, 2);
        $month = substr($latestCode, 4, 2);

        $newCode = "RE";
        if ($year == $currentYear) {
            if ($month == $currentMonth) {
                $iteration = substr($latestCode, 6);
                $nextIteration = str_pad((int)$iteration + 1, strlen($iteration), '0', STR_PAD_LEFT);
                $newCode .= $year . $currentMonth . $nextIteration;
            } else {
                $newCode .= $year . $currentMonth . '00001';
            }
        } else {
            $newCode .= $currentYear . $currentMonth . '00001';
        }
        return $newCode;
    }

    public static function allForDataTables($request): array
    {
        $selectColumns = ['id', 'voucher_number', 'to', 'date', 'total', 'status', 'type'];
        $searchColumns = ['voucher_number', 'to', 'account_name', 'status', 'type', 'note'];

        $query = self::query()->with(['createdBy', 'updatedBy']);
        $query->select(self::$selectColumns);

        if (!empty($request->status) && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if (!empty($request->type) && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        if (!empty($request->dateStart)) {
            $query->whereDate('date', '>=', $request->dateStart);
        }

        if (!empty($request->dateEnd)) {
            $query->whereDate('date', '<=', $request->dateEnd);
        }

        return self::getAllRows($request, $query, $selectColumns, $searchColumns);
    }

    public function account()
    {
        return $this->belongsTo(ChartOfAccountModel::class, 'account_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function details()
    {
        return $this->hasMany(AccountingReceiveDetailModel::class, 'cb_receive_id', 'id');
    }
}
