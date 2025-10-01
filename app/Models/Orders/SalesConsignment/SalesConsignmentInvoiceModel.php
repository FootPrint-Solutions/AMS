<?php

namespace App\Models\Orders\SalesConsignment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Orders\SalesConsignment\SalesConsignmentModel;
use App\Models\Orders\SalesInvoice\SalesInvoiceModel;

class SalesConsignmentInvoiceModel extends Model
{
    use HasFactory;

    protected $table = 'sales_consignment_invoices';

    protected $fillable = [
        'sales_consignment_id',
        'sales_invoice_id',
    ];

    /**
     * Get the sales consignment that owns the pivot.
     */
    public function salesConsignment(): BelongsTo
    {
        return $this->belongsTo(SalesConsignmentModel::class, 'sales_consignment_id');
    }

    /**
     * Get the sales invoice that owns the pivot.
     */
    public function salesInvoice(): BelongsTo
    {
        return $this->belongsTo(SalesInvoiceModel::class, 'sales_invoice_id');
    }
}
