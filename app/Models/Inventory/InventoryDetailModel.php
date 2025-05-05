<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryDetailModel extends Model
{
    use HasFactory;

    protected $table = 'inventory_details';

    protected $fillable = [
        'inventory_id',
        'battery_id',
        'type',
        'reference',
        'quantity',
        'note',
    ];

    // Define the relationship with the Inventory model
    public function inventory()
    {
        return $this->belongsTo(InventoryModel::class, 'inventory_id');
    }

    public function reference()
    {
        return $this->morphTo();
    }
}
