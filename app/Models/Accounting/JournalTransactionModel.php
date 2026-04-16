<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
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

    /**
     * Get all journal entries with details for printing.
     *
     * @param string $dateStart Start date (Y-m-d format)
     * @param string $dateEnd End date (Y-m-d format)
     * @param string|null $filter Optional filter parameter
     * @return array Array of journal entry details
     */
    public static function allForPrint(string $dateStart, string $dateEnd, ?string $filter = null): array
    {
        $query = JournalTransactionDetailModel::query()
            ->join('journal_entries', 'journal_entry_details.journal_entry_id', '=', 'journal_entries.id')
            ->select(
                'journal_entries.voucher_number as number',
                'journal_entries.date',
                'journal_entry_details.account_number',
                'journal_entry_details.account_name',
                'journal_entry_details.description',
                'journal_entry_details.ref',
                'journal_entry_details.debit as total_debit',
                'journal_entry_details.credit as total_credit'
            )
            ->whereBetween('journal_entries.date', [$dateStart, $dateEnd])
            ->orderBy('journal_entries.voucher_number')
            ->orderBy('journal_entry_details.id');

        if (!empty($filter)) {
            $query->where(function ($q) use ($filter) {
                $q->where('journal_entries.voucher_number', 'like', '%' . $filter . '%')
                    ->orWhere('journal_entry_details.account_name', 'like', '%' . $filter . '%')
                    ->orWhere('journal_entry_details.account_number', 'like', '%' . $filter . '%')
                    ->orWhere('journal_entry_details.ref', 'like', '%' . $filter . '%');
            });
        }

        return $query->get()->toArray();
    }

    /**
     * Get detail rows for General Ledger report.
     *
     * @param string $dateStart Start date in Y-m-d format.
     * @param string $dateEnd End date in Y-m-d format.
     * @param array<int|string>|null $accountFilter Optional account id filter.
     * @return array<int, array<string, mixed>>
     */
    public static function allForGeneralLedger(string $dateStart, string $dateEnd, ?array $accountFilter = null): array
    {
        $query = JournalTransactionDetailModel::query()
            ->join('journal_entries', 'journal_entry_details.journal_entry_id', '=', 'journal_entries.id')
            ->leftJoin('chart_of_accounts as coa', 'journal_entry_details.chart_of_account_id', '=', 'coa.id')
            ->select([
                'journal_entry_details.chart_of_account_id as account_id',
                DB::raw('NULL as account_master_id'),
                DB::raw('NULL as account_master_number'),
                DB::raw('NULL as account_master_name'),
                'journal_entry_details.account_number as account_detail_number',
                'journal_entry_details.account_name as account_detail_name',
                'journal_entries.date',
                'journal_entries.voucher_number as number',
                'journal_entry_details.description',
                DB::raw('NULL as contact_person'),
                'journal_entry_details.debit as total_debit',
                'journal_entry_details.credit as total_credit',
                DB::raw('coa.number as coa_number'),
            ])
            ->whereBetween('journal_entries.date', [$dateStart, $dateEnd]);

        if (!empty($accountFilter)) {
            $accountFilter = array_values(array_filter($accountFilter));

            if (count($accountFilter) > 1) {
                $numbers = ChartOfAccountModel::query()
                    ->whereIn('id', [$accountFilter[0], $accountFilter[1]])
                    ->pluck('number')
                    ->sort()
                    ->values();

                if ($numbers->count() === 2) {
                    $query->whereBetween('coa.number', [$numbers[0], $numbers[1]]);
                } else {
                    $query->whereIn('journal_entry_details.chart_of_account_id', $accountFilter);
                }
            } else {
                $query->where('journal_entry_details.chart_of_account_id', $accountFilter[0]);
            }
        }

        return $query
            ->orderBy('coa.number')
            ->orderBy('journal_entries.date')
            ->orderBy('journal_entries.voucher_number')
            ->orderBy('journal_entry_details.id')
            ->get()
            ->map(function ($row) {
                $item = $row->toArray();
                unset($item['coa_number']);
                return $item;
            })
            ->values()
            ->toArray();
    }

    /**
     * Get opening balance (sum before start date) for an account.
     *
     * @return array{totalDebit: float, totalCredit: float}
     */
    public static function initialBalance(string $dateStart, int $accountId): array
    {
        $balance = JournalTransactionDetailModel::query()
            ->join('journal_entries', 'journal_entry_details.journal_entry_id', '=', 'journal_entries.id')
            ->where('journal_entry_details.chart_of_account_id', $accountId)
            ->whereDate('journal_entries.date', '<', $dateStart)
            ->selectRaw('COALESCE(SUM(journal_entry_details.debit), 0) as totalDebit, COALESCE(SUM(journal_entry_details.credit), 0) as totalCredit')
            ->first();

        return [
            'totalDebit' => (float) ($balance->totalDebit ?? 0),
            'totalCredit' => (float) ($balance->totalCredit ?? 0),
        ];
    }

    /**
     * Backward-compatible alias for initial balance in empty period.
     *
     * @return array{totalDebit: float, totalCredit: float}
     */
    public static function initialBalanceEmpty(string $dateStart, int $accountId): array
    {
        return self::initialBalance($dateStart, $accountId);
    }
}
