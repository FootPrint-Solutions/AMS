<?php

namespace App\Models\MasterData\Distributor;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DistributorShopModel extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'distributor_shop';

    /**
     * Get distributor shop.
     */
    public function distributor(): BelongsTo
    {
        return $this->belongsTo(DistributorModel::class, 'id_distributor');
    }
}
