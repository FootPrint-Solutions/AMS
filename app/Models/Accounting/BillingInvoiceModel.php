<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Models
use App\Models\Accounting\BillingModel;

class BillingInvoiceModel extends Model
{
    use HasFactory;

    protected $table = 'billing_invoices';

    protected $fillable = [
        'billing_id',
        'invoice_id',
        'invoice_type',
        'invoice_number',
        'date',
        'discount',
        'discount_price',
        'subtotal',
        'total',
        'note',
    ];

    protected $casts = [
        'date' => 'date',
        'discount' => 'decimal:2',
        'discount_price' => 'float',
        'subtotal' => 'float',
        'total' => 'float',
    ];

    public function billing()
    {
        return $this->belongsTo(BillingModel::class, 'billing_id');
    }

    public function invoice()
    {
        return $this->morphTo();
    }
}
