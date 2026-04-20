<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// Models
use App\Models\Accounting\AccountingExpenseModel;
use App\Models\Accounting\ChartOfAccountModel;

class AccountingExpenseDetailModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'accounting_expense_details';

    protected $fillable = [
        'cb_expense_id',
        'account_id',
        'account_name',
        'description',
        'total',
    ];

    protected $casts = [
        'total' => 'double',
    ];

    public function expense()
    {
        return $this->belongsTo(AccountingExpenseModel::class, 'cb_expense_id');
    }

    public function account()
    {
        return $this->belongsTo(ChartOfAccountModel::class, 'account_id');
    }
}
