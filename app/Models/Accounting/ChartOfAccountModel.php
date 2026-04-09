<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

// TRAITS
use App\Traits\DataTablesTrait;
use OwenIt\Auditing\Auditable as AuditableTrait;

class ChartOfAccountModel extends Model implements Auditable
{
    use HasFactory, SoftDeletes, DataTablesTrait, AuditableTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'chart_of_accounts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['number', 'name', 'chart_of_account_group_id', 'is_active'];

    /**
     * Relation to account group.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function group()
    {
        return $this->belongsTo(ChartOfAccountGroupModel::class, 'chart_of_account_group_id');
    }

    /**
     * Get all data for DataTables.
     * 
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public static function allForDataTables($request)
    {
        $selectColumns = ['id', 'number', 'name', 'chart_of_account_group_id', 'is_active'];
        $searchColumns = ['number', 'name'];

        $query = self::query()->with('group');
        $query->select($selectColumns);

        return self::getAllRows($request, $query, $selectColumns, $searchColumns);
    }
}
