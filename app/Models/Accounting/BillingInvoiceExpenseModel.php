<?php

namespace App\Models\Accounting;

use App\Models\Orders\SalesInvoice\SalesInvoiceModel;
use App\Models\Accounting\ChartOfAccountModel;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BillingInvoiceExpenseModel extends Model
{
    protected $table = 'billing_invoice_expenses';

    protected $fillable = [
        'billing_invoice_id',
        'expense_id',
        'sales_order_id',
        'debit_account_id',
        'credit_account_id',
        'description',
        'amount',
    ];

    protected $casts = [
        'billing_invoice_id' => 'integer',
        'sales_order_id' => 'integer',
        'debit_account_id' => 'integer',
        'credit_account_id' => 'integer',
        'amount' => 'decimal:2',
    ];

    public function billingInvoice(): BelongsTo
    {
        return $this->belongsTo(BillingInvoiceModel::class, 'billing_invoice_id');
    }

    public function salesInvoice(): BelongsTo
    {
        return $this->belongsTo(SalesInvoiceModel::class, 'sales_invoice_id');
    }

    public function debitAccount(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccountModel::class, 'debit_account_id');
    }

    public function creditAccount(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccountModel::class, 'credit_account_id');
    }
}
