<?php

namespace App\Models\Orders\SalesConsignment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesConsignmentBatteriesModel extends Model
{
    use HasFactory;

    protected $table = 'sales_consignment_batteries';

    protected $fillable = [
        'sales_consignment_id',
        'sales_invoice_number',
        'invoice_number',
        'date',
        'customer_id',
        'vehicle_id',
        'distributor_shop_id',
        'distributor_shop_technician_id',
        'discount',
        'discount_price',
        'subtotal',
        'total',
        'payment_status',
        'status',
        'address',
        'alternative_address',
        'latitude',
        'longitude',
        'payment_method_id',
        'midtrans_invoice_number',
        'midtrans_payment_link',
        'source_platform',
        'source_id',
    ];

    protected $casts = [
        'date' => 'date',
        'discount' => 'decimal:2',
        'discount_price' => 'double',
        'subtotal' => 'double',
        'total' => 'double',
    ];
}
