<?php

namespace App\Models\Orders\SalesInvoice;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

// TRAITS
use App\Traits\DataTablesTrait;
use OwenIt\Auditing\Auditable as AuditableTrait;

// MODELS
use App\Models\Accounting\ExpenseModel;
use App\Models\Orders\SalesInvoice\SalesInvoiceModel;

class SalesInvoiceExpenseModel extends Model implements Auditable
{
    use HasFactory, SoftDeletes, DataTablesTrait, AuditableTrait;

    protected $table = 'sales_invoice_expenses';

    protected $fillable = [
        'sales_invoice_id',
        'expense_id',
        'amount',
        'description'
    ];

    protected $casts = [
        'amount' => 'double',
    ];

    /**
     * Get the sales invoice that owns the expense.
     */
    public function salesInvoice(): BelongsTo
    {
        return $this->belongsTo(SalesInvoiceModel::class, 'sales_invoice_id');
    }

    /**
     * Get the expense details.
     */
    public function expense(): BelongsTo
    {
        return $this->belongsTo(ExpenseModel::class, 'expense_id');
    }
}
