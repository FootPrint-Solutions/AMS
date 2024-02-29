<?php

namespace App\Models\MasterData\Distributor;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DistributorShopTechnicianModel extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'distributor_shop_technicians';

    /**
     * Get technicians' shop.
     */
    public function shop(): BelongsTo
    {
        return $this->belongsTo(DistributorShopModel::class, 'distributor_shop_id');
    }
}
