<?php

namespace App\Models\Accounting;

use App\Models\Accounting\ChartOfAccountModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use OwenIt\Auditing\Contracts\Auditable;

// TRAITS
use App\Traits\DataTablesTrait;
use OwenIt\Auditing\Auditable as AuditableTrait;

class ExpenseModel extends Model implements Auditable
{
    use HasFactory, SoftDeletes, DataTablesTrait, AuditableTrait;

    protected $table = 'expenses';

    protected $fillable = [
        'chart_of_account_id',
        'name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get all data for DataTables.
     * 
     * @param \Illuminate\Http\Request $request The POST request obtained (for DataTables configuration).
     * @return array Associative array containing data for DataTables display.
     */
    public static function allForDataTables($request)
    {
        // Set the list of select and search columns.
        $selectColumns = ['id', 'name', 'description', 'is_active', 'chart_of_account_id'];
        $searchColumns = ['name', 'description', 'chart_of_account_id'];

        // Build the query to obtain all rows.
        $query = self::query();
        $query->select($selectColumns);
        $query->with('chartOfAccount:id,name,number');

        // Filter by status if set.
        if ($request->status !== null && $request->status !== 'all') {
            $query->where('is_active', $request->status);
        }

        return self::getAllRows($request, $query, $selectColumns, $searchColumns);
    }

    /**
     * Get the chart of account associated with the expense.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo The relationship instance.
     */
    public function chartOfAccount()
    {
        return $this->belongsTo(ChartOfAccountModel::class, 'chart_of_account_id');
    }
}
