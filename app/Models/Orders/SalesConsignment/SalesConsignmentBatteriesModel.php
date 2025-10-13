<?php

namespace App\Models\Orders\SalesConsignment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Orders\SalesInvoice\SalesInvoiceModel;

class SalesConsignmentBatteriesModel extends Model
{
    use HasFactory;

    protected $table = 'sales_consignment_batteries';

    protected $fillable = [
        'sales_consignment_id',
        'sales_invoice_id',
        'sales_invoice_number',
        'invoice_number',
        'date',
        'discount',
        'discount_price',
        'subtotal',
        'total',
    ];

    protected $casts = [
        'date' => 'date',
        'discount' => 'decimal:2',
        'discount_price' => 'double',
        'subtotal' => 'double',
        'total' => 'double',
    ];

    public function salesInvoice()
    {
        return $this->belongsTo(SalesInvoiceModel::class, 'sales_invoice_id');
    }
}
