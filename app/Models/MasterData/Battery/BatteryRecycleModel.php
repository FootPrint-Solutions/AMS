<?php

namespace App\Models\MasterData\Battery;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BatteryRecycleModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'battery_recycles';

    protected $fillable = [
        'name',
        'status',
        'note',
    ];
}
