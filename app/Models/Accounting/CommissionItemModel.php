<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Accounting\CommissionModel;
use App\Models\MasterData\Distributor\DistributorShopModel;
use App\Models\Orders\SalesOrder\SalesOrderBatteryModel;
use App\Models\Accounting\ChartOfAccountModel;
use App\Models\MasterData\Battery\BatteryModel;

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

    public function commission()
    {
        return $this->belongsTo(CommissionModel::class, 'commission_id');
    }

    public function distributorShop()
    {
        return $this->belongsTo(DistributorShopModel::class, 'distributor_shop_id');
    }

    public function salesOrderBattery()
    {
        return $this->belongsTo(SalesOrderBatteryModel::class, 'sales_order_battery_id');
    }

    public function creditAccount()
    {
        return $this->belongsTo(ChartOfAccountModel::class, 'credit_account_id');
    }

    public function debitAccount()
    {
        return $this->belongsTo(ChartOfAccountModel::class, 'debit_account_id');
    }

    public function battery()
    {
        return $this->belongsTo(BatteryModel::class, 'battery_id');
    }
}
