<?php

namespace App\Models\Orders\SalesOnline;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\MasterData\Battery\BatteryModel;

class SalesOnlineBatteriesModel extends Model
{
    use HasFactory;

    protected $table = 'sales_online_batteries';

    protected $fillable = [
        'sales_online_id',
        'battery_id',
        'name',
        'price',
        'image',
        'quantity',
        'total_price',
    ];

    public function salesOnline()
    {
        return $this->belongsTo(SalesOnlineModel::class, 'sales_online_id');
    }

    public function battery()
    {
        return $this->belongsTo(BatteryModel::class, 'battery_id');
    }
}
