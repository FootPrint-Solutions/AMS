<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// TRAITS
use App\Traits\DataTablesTrait;
use App\Models\Accounting\ChartOfAccountModel;
use App\Models\Accounting\JournalTransactionModel;
use App\Models\Orders\PurchaseOrder\PurchaseOrderModel;
use App\Models\Orders\SalesOrder\SalesOrderModel;

class JournalTransactionDetailModel extends Model
{
    use HasFactory, DataTablesTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'journal_entry_details';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'journal_entry_id',
        'chart_of_account_id',
        'account_number',
        'account_name',
        'description',
        'ref',
        'debit',
        'credit',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'debit' => 'double',
        'credit' => 'double',
    ];

    /**
     * The list of columns in the associated table.
     */
    private static $selectColumns = [
        'id',
        'journal_entry_id',
        'chart_of_account_id',
        'account_number',
        'account_name',
        'description',
        'debit',
        'credit',
    ];

    /**
     * Get all data for DataTables.
     * 
     * @param \Illuminate\Http\Request $request The POST request obtained (for DataTables configuration).
     * @return array Associative array containing data for DataTables display.
     */
    public static function whereForDataTables($request)
    {
        $selectColumns = ['id', 'journal_entry_id', 'account_number', 'account_name', 'debit', 'credit'];

        $query = self::query()
            ->where('journal_entry_id', $request->id);
        $query->select(self::$selectColumns);

        return self::getAllRows($request, $query, $selectColumns, $selectColumns);
    }

    /**
     * Get the chart of account associated with the journal entry detail.
     */
    public function chartOfAccount()
    {
        return $this->belongsTo(ChartOfAccountModel::class, 'chart_of_account_id', 'id');
    }

    /**
     * Get the journal entry associated with the detail.
     */
    public function journalEntry()
    {
        return $this->belongsTo(JournalTransactionModel::class, 'journal_entry_id', 'id');
    }

    public function ref()
    {
        $salesOrder = SalesOrderModel::where('sales_order_number', $this->ref)->first();
        if ($salesOrder) {
            return $salesOrder;
        }

        $purchaseOrder = PurchaseOrderModel::where('purchase_order_number', $this->ref)->first();
        if ($purchaseOrder) {
            return $purchaseOrder;
        }

        return null;
    }
}
