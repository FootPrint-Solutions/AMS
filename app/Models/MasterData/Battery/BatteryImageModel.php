<?php

namespace App\Models\MasterData\Battery;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable as AuditableTrait;
use Illuminate\Support\Facades\DB;
use OwenIt\Auditing\Contracts\Auditable;

use App\Models\MasterData\Battery\BatteryModel;

class BatteryImageModel extends Model implements Auditable
{
    use HasFactory, AuditableTrait;

    protected $table = 'battery_images';

    protected $fillable = [
        'battery_id',
        'image_path',
        'image_type',
    ];

    public function battery()
    {
        return $this->belongsTo(BatteryModel::class, 'battery_id');
    }
}
