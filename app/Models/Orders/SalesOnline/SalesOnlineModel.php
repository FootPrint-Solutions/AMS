<?php

namespace App\Models\Orders\SalesOnline;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesOnlineModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sales_online';

    protected $fillable = [
        'customer_name',
        'province',
        'city',
        'district',
        'sub_district',
        'postal_code',
        'phone_number',
        'email',
        'vehicle_plate',
        'delivery_date',
        'additional_info',
        'address',
    ];

    protected $dates = [
        'delivery_date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}
