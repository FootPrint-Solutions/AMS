<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommissionItemModel extends Model
{
    use HasFactory;

    protected $table = 'commission_items';

    public $timestamps = false;

    protected $fillable = [
        'commission_id',
        'distributor_shop_id',
        'sales_order_id',
        'sales_order_battery_id',
        'battery_id',
        'commission_type',
        'commission_amount',
        'credit_account_id',
        'debit_account_id',
    ];

    protected $casts = [
        'commission_id' => 'integer',
        'distributor_shop_id' => 'integer',
        'sales_order_id' => 'integer',
        'sales_order_battery_id' => 'integer',
        'battery_id' => 'integer',
        'commission_type' => 'string',
        'commission_amount' => 'float',
        'credit_account_id' => 'integer',
        'debit_account_id' => 'integer',
    ];
}
