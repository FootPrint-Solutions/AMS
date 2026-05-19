<?php

namespace App\Models\Settings;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// TRAITS
use App\Traits\DataTablesTrait;


use App\Models\Accounting\ChartOfAccountModel;

class PaymentMethodModel extends Model
{
    use HasFactory, SoftDeletes, DataTablesTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'payment_methods';

    /**
     * Get all data for DataTables.
     * 
     * @param \Illuminate\Http\Request $request The POST request obtained (for DataTables configuration).
     * @return array Associative array containing data for DataTables display.
     */
    public static function allForDataTables($request)
    {
        // Set the list of select and search columns.
        $selectColumns = ['id', 'type', 'name', 'status', 'chart_of_account_id'];
        $searchColumns = ['name', 'chart_of_account_id'];

        // Build the query to obtain all rows.
        $query = self::query();
        $query->select($selectColumns);
        $query->with('chartOfAccount:id,name,number');

        return self::getAllRows($request, $query, $selectColumns, $searchColumns);
    }

    /**
     * Get the chart of account associated with the payment method.
     */
    public function chartOfAccount()
    {
        return $this->belongsTo(ChartOfAccountModel::class, 'chart_of_account_id');
    }
}
