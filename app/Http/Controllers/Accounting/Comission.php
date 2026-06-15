<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    public function edit($id)
    {
        $commission = CommissionModel::with('items')->findOrFail($id);

        return view(
            "Accounting.Commission.create",
            getIndexData(
                $this->title,
                [
                    'commission' => $commission,
                    'distributor_shops' => DistributorShopModel::get()->toArray(),
                    'chart_of_accounts' => ChartOfAccountModel::get()->toArray(),
                    'type' => 'edit',
                ]
            )
        );
    }

    public function show(Request $request)
    {
        $draw = (int) $request->input('draw', 0);
        $start = (int) $request->input('start', 0);

        $data = CommissionModel::allForDataTables($request);

        $rows = [];
        $no = $start + 1;
        foreach ($data['row'] as $item) {
            $statusBadgeClass = $item->status === 'post' ? 'badge-success' : 'badge-secondary text-dark';

            $row = [];
            $row[] = $item->id;
            $row[] = $no++;
            $row[] = $item->commission_number ?? '-';
            $row[] = formatDate((string) $item->date, 'j M Y');
            $row[] = formatPrice($item->total ?? 0);
            $row[] = "<span class='badge $statusBadgeClass'>" . ($item->status ?? '-') . '</span>';
            $rows[] = $row;
        }

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => CommissionModel::count(),
            'recordsFiltered' => $data['count'],
            'data' => $rows,
        ]);
    }

    public function getItems($id)
    {
        $commissionItems = CommissionItemModel::with(['distributorShop', 'salesOrderBattery.salesOrder', 'battery', 'debitAccount', 'creditAccount'])->where('commission_id', $id)->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Commission items retrieved successfully.',
            'data' => $commissionItems
        ]);
    }

    public function getSalesOrders(Request $request)
    {
        $distributorShopId = $request->query('distributor_shop_id');
        $salesOrderId = $request->query('selected_order_ids');
        $filterStartDate = $request->query('filter_start_date');
        $filterEndDate = $request->query('filter_end_date');

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
            $salesOrderBatteries = SalesOrderBatteryModel::with('salesOrder')->whereHas('salesOrder', function ($query) use ($distributorShopId, $salesOrderId, $filterStartDate, $filterEndDate) {
                $query->where('distributor_shop_id', $distributorShopId)
                    ->where('status', 'posted');

                if ($filterStartDate) {
                    $query->where('date', '>=', $filterStartDate);
                }

                if ($filterEndDate) {
                    $query->where('date', '<=', $filterEndDate);
                }
            })->whereIn('id',  $salesOrderId)->orderBy('created_at', 'desc')->get()->toArray();

            return response()->json(
                [
                    'status' => 'success',
                    'message' => 'Selected sales orders retrieved successfully.',
                    'data' => $salesOrderBatteries
                ]
            );
        } else {

            $salesOrderBatteries = SalesOrderBatteryModel::with('salesOrder')->whereHas('salesOrder', function ($query) use ($distributorShopId, $filterStartDate, $filterEndDate) {
                $query->where('distributor_shop_id', $distributorShopId)
                    ->where('status', 'posted');

                if ($filterStartDate) {
                    $query->where('date', '>=', $filterStartDate);
                }

                if ($filterEndDate) {
                    $query->where('date', '<=', $filterEndDate);
                }
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

    public function store(Request $request)
    {
        $request->validate([
            'commission_number' => 'required|unique:commissions,commission_number',
            'date' => 'required|date',
            'total' => 'required|numeric|min:0',
            'sales_order_battery_id' => 'required|array|min:1',
            'sales_order_battery_id.*' => 'required|exists:sales_order_battery,id',
            'commission_type' => 'required|array|min:1',
            'commission_type.*' => 'required|string',
            'commission_value' => 'required|array|min:1',
            'commission_value.*' => 'required|numeric|min:0',
            'debit_account' => 'required|array|min:1',
            'debit_account.*' => 'required|exists:chart_of_accounts,id',
            'credit_account' => 'required|array|min:1',
            'credit_account.*' => 'required|exists:chart_of_accounts,id',
        ]);

        try {
            DB::beginTransaction();
            $salesOrder = SalesOrderBatteryModel::with('salesOrder')->findOrFail($request->input('sales_order_battery_id')[0]);

            // Create the commission record
            $commission = CommissionModel::create([
                'commission_number' => $request->input('commission_number'),
                'date' => $request->input('date'),
                'total' => str_replace('.', '', $request->input('total')),
                'created_by' => auth()->user()->id,
            ]);

            // Create the commission items
            foreach ($request->input('sales_order_battery_id') as $index => $salesOrderBatteryId) {
                CommissionItemModel::create([
                    'commission_id' => $commission->id,
                    'distributor_shop_id' => $salesOrder->salesOrder->distributor_shop_id,
                    'sales_order_id' => $salesOrder->sales_order_id,
                    'sales_order_battery_id' => $salesOrderBatteryId,
                    'battery_id' => $salesOrder->battery_id,
                    'commission_type' => $request->input('commission_type')[$index],
                    'commission_amount' => $request->input('commission_value')[$index],
                    'debit_account_id' => $request->input('debit_account')[$index],
                    'credit_account_id' => $request->input('credit_account')[$index],
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Commission created successfully.',
                'data' => $commission->load('items')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create commission: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:commissions,id',
            'date' => 'required|date',
            'total' => 'required|numeric|min:0',
            'sales_order_battery_id' => 'required|array|min:1',
            'sales_order_battery_id.*' => 'required|exists:sales_order_battery,id',
            'commission_type' => 'required|array|min:1',
            'commission_type.*' => 'required|string',
            'commission_value' => 'required|array|min:1',
            'commission_value.*' => 'required|numeric|min:0',
            'debit_account' => 'required|array|min:1',
            'debit_account.*' => 'required|exists:chart_of_accounts,id',
            'credit_account' => 'required|array|min:1',
            'credit_account.*' => 'required|exists:chart_of_accounts,id',
        ]);

        try {
            DB::beginTransaction();

            $commission = CommissionModel::findOrFail($request->input('id'));
            $salesOrder = SalesOrderBatteryModel::with('salesOrder')->findOrFail($request->input('sales_order_battery_id')[0]);

            // Update the commission record
            $commission->update([
                'date' => $request->input('date'),
                'total' => str_replace('.', '', $request->input('total')),
                'updated_by' => auth()->user()->id,
            ]);

            // Delete existing commission items
            CommissionItemModel::where('commission_id', $commission->id)->delete();

            // Create new commission items
            foreach ($request->input('sales_order_battery_id') as $index => $salesOrderBatteryId) {
                CommissionItemModel::create([
                    'commission_id' => $commission->id,
                    'distributor_shop_id' => $salesOrder->salesOrder->distributor_shop_id,
                    'sales_order_id' => $salesOrder->sales_order_id,
                    'sales_order_battery_id' => $salesOrderBatteryId,
                    'battery_id' => $salesOrder->battery_id,
                    'commission_type' => $request->input('commission_type')[$index],
                    'commission_amount' => $request->input('commission_value')[$index],
                    'debit_account_id' => $request->input('debit_account')[$index],
                    'credit_account_id' => $request->input('credit_account')[$index],
                ]);
            }

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Commission updated successfully.',
                'data' => $commission->load('items')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update commission: ' . $e->getMessage(),
            ], 500);
        }
    }
}
