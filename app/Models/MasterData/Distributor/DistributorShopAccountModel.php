<?php

namespace App\Models\MasterData\Distributor;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\MasterData\Distributor\DistributorShopModel;
use App\Models\Accounting\ChartOfAccountModel;
use App\Models\MasterData\Distributor\DistributorShopCommissionModel;

class DistributorShopAccountModel extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'distributor_shop_accounts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'distributor_shop_id',
        'type',
        'chart_of_account_id',
        'commission',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'distributor_shop_id' => 'integer',
        'chart_of_account_id' => 'integer',
        'commission' => 'double',
    ];

    /**
     * Get the distributor shop that owns the account.
     */
    public function distributorShop()
    {
        return $this->belongsTo(DistributorShopModel::class, 'distributor_shop_id');
    }

    /**
     * Get the chart of account associated with this shop account.
     */
    public function chartOfAccount()
    {
        return $this->belongsTo(ChartOfAccountModel::class, 'chart_of_account_id');
    }
}
