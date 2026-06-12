<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\MasterData\Distributor\DistributorShopModel;
use App\Models\Accounting\CommissionModel;
use App\Models\Accounting\CommissionItemModel;
use App\Models\Orders\SalesOrder\SalesOrderBatteryModel;
use App\Models\Accounting\ChartOfAccountModel;

class Comission extends Controller
{
    private $title = "Comission";

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view(
            "Accounting.Commission.index",
            getIndexData(
                $this->title,
                [
                    "DistributorShops" => DistributorShopModel::get()->toArray()
                ]
            )
        );
    }

    /** 
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view(
            "Accounting.Commission.create",
            getIndexData(
                $this->title,
                [
                    'commission_number' => CommissionModel::generateCommissionNumber(),
                    'distributor_shops' => DistributorShopModel::get()->toArray(),
                    'chart_of_accounts' => ChartOfAccountModel::get()->toArray(),
                ]
            )
        );
    }

    public function getSalesOrders(Request $request)
    {
        $distributorShopId = $request->query('distributor_shop_id');
        $salesOrderId = $request->query('selected_order_ids');
        // dd($distributorShopId, $salesOrderId);

        if (!$distributorShopId) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'Distributor shop ID is required.',
                ],
                400
            );
        }

        if ($salesOrderId) {
            $salesOrderBatteries = SalesOrderBatteryModel::with('salesOrder')->whereHas('salesOrder', function ($query) use ($distributorShopId, $salesOrderId) {
                $query->where('distributor_shop_id', $distributorShopId)
                    ->where('status', 'posted');
            })->whereIn('id',  $salesOrderId)->orderBy('created_at', 'desc')->get()->toArray();

            return response()->json(
                [
                    'status' => 'success',
                    'message' => 'Selected sales orders retrieved successfully.',
                    'data' => $salesOrderBatteries
                ]
            );
        } else {

            $salesOrderBatteries = SalesOrderBatteryModel::with('salesOrder')->whereHas('salesOrder', function ($query) use ($distributorShopId) {
                $query->where('distributor_shop_id', $distributorShopId)
                    ->where('status', 'posted');
            })->orderBy('created_at', 'desc')->get()->toArray();

            return response()->json(
                [
                    'status' => 'success',
                    'message' => 'Sales orders retrieved successfully.',
                    'data' => $salesOrderBatteries
                ]
            );
        }
    }
}
