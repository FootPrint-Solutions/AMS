<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

// TRAITS
use App\Traits\DataTablesTrait;
use OwenIt\Auditing\Auditable as AuditableTrait;

// MODELS
use App\Models\User;

class JournalTransactionModel extends Model implements Auditable
{
    use HasFactory, SoftDeletes, DataTablesTrait, AuditableTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'journal_entries';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'date',
        'voucher_number',
        'total',
        'status',
        'note',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'date' => 'date',
        'total' => 'double',
    ];

    /**
     * Select columns for DataTables queries.
     *
     * @var array<int, string>
     */
    private static $selectColumns = [
        'id',
        'date',
        'voucher_number',
        'total',
        'status',
        'note',
        'created_by',
        'updated_by',
    ];

    /**
     * Generate voucher number with format JRYYMM#####.
     */
    public static function generateVoucherNumber(): string
    {
        $latest = self::withTrashed()->orderByDesc('id')->first();
        $currentYear = date('y');
        $currentMonth = date('m');

        if (!$latest || empty($latest->voucher_number) || strlen($latest->voucher_number) < 6) {
            return 'JR' . $currentYear . $currentMonth . '00001';
        }

        $latestNumber = (string) $latest->voucher_number;
        $latestYear = substr($latestNumber, 2, 2);
        $latestMonth = substr($latestNumber, 4, 2);

        if ($latestYear !== $currentYear || $latestMonth !== $currentMonth) {
            return 'JR' . $currentYear . $currentMonth . '00001';
        }

        $runningNumber = (int) substr($latestNumber, 6);

        return 'JR' . $currentYear . $currentMonth . str_pad((string) ($runningNumber + 1), 5, '0', STR_PAD_LEFT);
    }

    /**
     * Get all journal entries for DataTables.
     */
    public static function allForDataTables($request): array
    {
        $selectColumns = ['id', 'date', 'voucher_number', 'total', 'status', 'note'];
        $searchColumns = ['voucher_number', 'status', 'note'];

        $query = self::query()->with(['createdBy', 'updatedBy']);
        $query->select(self::$selectColumns);

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

    /**
     * Get the user who created this entry.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this entry.
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get journal details associated with this entry.
     */
    public function details(): HasMany
    {
        return $this->hasMany(JournalTransactionDetailModel::class, 'journal_entry_id', 'id');
    }
}
