<?php

namespace App\Http\Controllers\MasterData\Distributor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;
use App\Exports\DistributorShopBatteryCommissionTemplateExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\DistributorShopBatteryCommissionImport;
use App\Models\MasterData\Distributor\DistributorShopCommissionModel;
use App\Models\MasterData\Battery\BatteryModel;

// MODELS
use App\Models\MasterData\Distributor\DistributorShopModel;
use App\Models\MasterData\Distributor\DistributorModel;
use App\Models\Accounting\ChartOfAccountModel;
use App\Models\MasterData\Distributor\DistributorShopAccountModel;

class DistributorShop extends Controller
{
    private $title = "Distributor Shop";

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view(
            'MasterData.Distributor.Shop.index',
            getIndexData(
                $this->title
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
            'MasterData.Distributor.Shop.create',
            getIndexData(
                $this->title,
                array(
                    "distributors" => DistributorModel::where('status', 1)->get()->toArray(),
                    "chartOfAccounts" => ChartOfAccountModel::where('is_active', 1)->get()->toArray(),
                )
            )
        );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return view(
            'MasterData.Distributor.Shop.create',
            getIndexData(
                $this->title,
                array(
                    "profile" => DistributorShopModel::with('accounts')->find($id)->toArray(),
                    "distributors" => DistributorModel::where('status', 1)->get()->toArray(),
                    "chartOfAccounts" => ChartOfAccountModel::where('is_active', 1)->get()->toArray(),
                    "technicianAccount" => DistributorShopModel::with('accounts')->find($id)->accounts->where('type', 'technician')->first() ?? null,
                    "picAccount" => DistributorShopModel::with('accounts')->find($id)->accounts->where('type', 'pic')->first() ?? null,
                    "pitStopAccount" => DistributorShopModel::with('accounts')->find($id)->accounts->where('type', 'pit_stop')->first() ?? null,
                )
            )
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        // Get all DataTables requests.
        $draw = $request->input("draw");
        $start = $request->input("start");

        // Get shop data (rows and count).
        $data = DistributorShopModel::allForDataTables($request);

        // Set rows to be displayed in table.
        $rows = [];
        $no = $start + 1;
        foreach ($data["row"] as $key) {
            // Set status indicator color based on status.
            if ($key->status == 0) {
                $statusIndicatorColor = "text-danger";
            } else {
                $statusIndicatorColor = "text-success";
            }

            // Set an array for each row.
            $row = [];
            $row[] = $no++;
            $row[] = $key->name;
            $row[] = $key->distributor->name ?? '-';
            $row[] = $key->address;
            $row[] = $key->contact_person;
            $row[] = "<span class='text-secondary'>+62</span> " . $key->contact;
            $row[] = $key->email ?? "-";
            $row[] = "<i class='fa-solid fa-circle $statusIndicatorColor'></i>";
            $row[] = $key->id;
            $row[] = $key->distributor_id;
            $row[] = $key->status;
            $rows[] = $row;
        }

        return response()->json(array(
            "draw" => $draw,
            "recordsTotal" => DistributorShopModel::count(),
            "recordsFiltered" => $data["count"],
            "data" => $rows
        ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $shop = new DistributorShopModel();
            $shop->name = $request->name;
            $shop->distributor_id = $request->distributor;
            $shop->address = $request->address;
            $shop->contact_person = $request->contactperson;
            $shop->contact = $request->contact;
            $shop->email = $request->email;
            $shop->note = $request->note;
            $shop->latitude = $request->Latitude;
            $shop->longitude = $request->Longitude;
            $status = $shop->save();

            // Create shop accounts if they exist in the request
            if ($request->has('technicianAccount')) {
                $technicianAccount = new DistributorShopAccountModel();
                $technicianAccount->distributor_shop_id = $shop->id;
                $technicianAccount->chart_of_account_id = $request->technicianAccount;
                $technicianAccount->commission = $request->technicianCommission;
                $technicianAccount->type = 'technician';
                $technicianAccount->save();
            }

            if ($request->has('picAccount')) {
                $picAccount = new DistributorShopAccountModel();
                $picAccount->distributor_shop_id = $shop->id;
                $picAccount->chart_of_account_id = $request->picAccount;
                $picAccount->commission = $request->picCommission;
                $picAccount->type = 'pic';
                $picAccount->save();
            }

            if ($request->has('pit_stopAccount')) {
                $pitStopAccount = new DistributorShopAccountModel();
                $pitStopAccount->distributor_shop_id = $shop->id;
                $pitStopAccount->chart_of_account_id = $request->pit_stopAccount;
                $pitStopAccount->commission = $request->pit_stopCommission;
                $pitStopAccount->type = 'pit_stop';
                $pitStopAccount->save();
            }

            // Set a new response data to be sent.
            return getResponseData(
                $status,
                $status ? "The new shop was successfully created!" : "Failed to create the new shop!"
            );
        } catch (Exception $e) {
            // Logging error message.
            Log::error($e->getMessage());

            // Set an error response data to be sent.
            return getResponseData(false);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        try {
            $shop = DistributorShopModel::find($request->id);
            $shop->name = $request->name;
            $shop->distributor_id = $request->distributor;
            $shop->address = $request->address;
            $shop->contact_person = $request->contactperson;
            $shop->contact = $request->contact;
            $shop->email = $request->email;
            $shop->note = $request->note;
            $shop->latitude = $request->Latitude;
            $shop->longitude = $request->Longitude;
            $status = $shop->save();

            // Update or create shop accounts based on the request
            $accountTypes = ['technician', 'pic', 'pit_stop'];

            foreach ($accountTypes as $type) {
                $accountId = $request->get("{$type}Account");
                $commission = $request->get("{$type}Commission");

                if ($accountId !== null && $accountId !== '' && $commission !== null && $commission !== '') {
                    $account = DistributorShopAccountModel::updateOrCreate(
                        [
                            'distributor_shop_id' => $shop->id,
                            'type' => $type
                        ],
                        [
                            'chart_of_account_id' => $accountId,
                            'commission' => $commission
                        ]
                    );
                }
            }

            // Set a new response data to be sent.
            return getResponseData(
                $status,
                $status ? "The shop was successfully updated!" : "Failed to update the shop!"
            );
        } catch (Exception $e) {
            // Logging error message.
            Log::error($e->getMessage());

            // Set an error response data to be sent.
            return getResponseData(false);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateStatus(Request $request)
    {
        try {
            $shop = DistributorShopModel::find($request->id);

            if (!$shop) {
                return getResponseData(false, "Shop not found!");
            }

            // Check whether distributor is active or inactive.
            $distributor = DistributorModel::find($shop->distributor_id);
            if (!$distributor) {
                return getResponseData(false, "Distributor not found!");
            }

            if ($distributor->status == 0) {
                // If inactive, the shop status cannot be changed.
                return getResponseData(
                    false,
                    "Failed to update the selected shop status as the distributor is inactive!"
                );
            } else {
                $shop->status = $shop->status ? 0 : 1;
                $status = $shop->save();

                // Set a new response data to be sent.
                return getResponseData(
                    $status,
                    $status ? "The selected shop was successfully updated!" : "Failed to update the selected shop!"
                );
            }
        } catch (Exception $e) {
            // Logging error message.
            Log::error($e->getMessage());

            // Set an error response data to be sent.
            return getResponseData(false);
        }
    }

    public function account($shopId)
    {
        try {
            $accounts = DistributorShopAccountModel::with('chartOfAccount')
                ->where('distributor_shop_id', $shopId)
                ->get();

            return response()->json([
                'status' => true,
                'message' => "Accounts retrieved successfully!",
                'accounts' => $accounts
            ]);
        } catch (Exception $e) {
            Log::error($e->getMessage());

            return response()->json([
                'status' => false,
                'message' => "Failed to retrieve accounts!"
            ]);
        }
    }

    public function downloadTemplate($shopId)
    {
        $shop = DistributorShopModel::find($shopId);

        if (!$shop) {
            return redirect()->back()->with('error', 'Shop not found!');
        }

        $fileName = "Distributor_Shop_Battery_Commission_Template.xlsx";

        return Excel::download(new DistributorShopBatteryCommissionTemplateExport(), $fileName);
    }

    public function import(Request $request, $shopId)
    {
        $request->validate([
            'batteryImportFile' => 'required|mimes:xlsx,xls,csv',
        ]);
        $path1 = $request->file('batteryImportFile')->store('temp');
        $path = storage_path('app') . '/' . $path1;

        try {
            $import = new DistributorShopBatteryCommissionImport($shopId);
            Excel::import($import, $path);

            $imported = $import->getTotalImportedRows();
            $total = $import->getTotalRows();
            $unimported = $import->getUnimportedRows();

            $message = "Successfully imported {$imported} of {$total} rows.";
            if (count($unimported) > 0) {
                $message .= " Failed to import " . count($unimported) . " rows.";
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'unimported' => $unimported
            ]);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred during import: ' . $e->getMessage()
            ]);
        }
    }

    public function showCommissions(Request $request)
    {
        $draw = $request->input("draw");
        $start = $request->input("start");

        $data = DistributorShopCommissionModel::whereForDataTables($request);

        $rows = [];
        $no = $start + 1;
        foreach ($data["row"] as $key) {
            $row = [];
            $row[] = $no++;
            $row[] = $key->battery_name;
            $row[] = ucfirst($key->type);
            $row[] = number_format($key->commission, 0, ',', '.');
            $row[] = $key->id; // ID for Action buttons (edit / delete)
            $rows[] = $row;
        }

        return response()->json(array(
            "draw" => $draw,
            "recordsTotal" => DistributorShopCommissionModel::where('distributor_shop_id', $request->shop_id)->count(),
            "recordsFiltered" => $data["count"],
            "data" => $rows
        ));
    }

    public function updateCommission(Request $request)
    {
        try {
            $commission = DistributorShopCommissionModel::find($request->id);
            if (!$commission) {
                return response()->json([
                    'success' => false,
                    'message' => 'Commission not found!'
                ]);
            }

            $amount = $request->commission;
            if ($amount === null || $amount === '') {
                return response()->json([
                    'success' => false,
                    'message' => 'Commission amount is required.'
                ]);
            }
            $amount = (double) str_replace(['.', ','], ['', '.'], $amount);

            $commission->commission = $amount;
            $status = $commission->save();

            return response()->json([
                'success' => $status,
                'message' => $status ? 'Commission successfully updated!' : 'Failed to update commission!'
            ]);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ]);
        }
    }

    public function destroyCommission(Request $request)
    {
        try {
            $commission = DistributorShopCommissionModel::find($request->id);
            if (!$commission) {
                return response()->json([
                    'success' => false,
                    'message' => 'Commission not found!'
                ]);
            }

            $status = $commission->delete();

            return response()->json([
                'success' => $status,
                'message' => $status ? 'Commission successfully deleted!' : 'Failed to delete commission!'
            ]);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ]);
        }
    }
}
