<?php

namespace App\Models\Orders\SalesOrder;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Accounting\ExpenseModel;
use App\Models\Orders\SalesOrder\SalesOrderModel;

class SalesOrderExpenseModel extends Model
{
    use SoftDeletes;

    protected $table = 'sales_order_expenses';

    protected $fillable = [
        'sales_order_id',
        'expense_id',
        'amount',
    ];

    protected $casts = [
        'amount' => 'double',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function salesOrder()
    {
        return $this->belongsTo(SalesOrderModel::class, 'sales_order_id');
    }

    public function expense()
    {
        return $this->belongsTo(ExpenseModel::class, 'expense_id');
    }
}
