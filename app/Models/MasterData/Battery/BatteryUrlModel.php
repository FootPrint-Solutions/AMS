<?php

namespace App\Models\MasterData\Battery;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

// TRAITS
use OwenIt\Auditing\Auditable as AuditableTrait;

class BatteryUrlModel extends Model implements Auditable
{
    use HasFactory, AuditableTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'battery_urls';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'platform',
        'url'
    ];
}
