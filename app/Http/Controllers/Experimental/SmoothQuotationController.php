<?php

namespace App\Http\Controllers\Experimental;

use App\Http\Controllers\Controller;
use App\Http\Controllers\MasterData\Vehicle\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

// MODELS
use App\Models\MasterData\Customer\CustomerModel;
use App\Models\MasterData\Vehicle\VehicleModel;
use App\Models\MasterData\Vehicle\VehicleBrandModel;
use App\Models\MasterData\Vehicle\VehicleYearModel;
use App\Models\MasterData\Vehicle\VehicleFuelModel;
use App\Models\MasterData\Vehicle\VehicleTransmissionModel;
use App\Models\MasterData\Distributor\DistributorShopModel;
use App\Models\MasterData\Battery\BatteryModel;
use App\Models\MasterData\Battery\BatteryRecycleModel;
use App\Models\MasterData\Battery\BatteryImport;
use App\Models\MasterData\Battery\BatterySizeCategoryModel;
use App\Models\MasterData\Distributor\DistributorShopBatteryModel;
use App\Models\Orders\SalesOrder\SalesOrderModel;
use App\Models\Orders\SalesOrder\SalesOrderBatteryModel;
use App\Models\Orders\PurchaseOrder\PurchaseOrderModel;
use App\Models\Orders\PurchaseOrder\PurchaseOrderBatteryModel;
use App\Models\Accounting\BillingModel;
use App\Models\Accounting\BillingInvoiceModel;
use App\Models\Settings\MessageTemplateModel;
use App\Models\Settings\TaxModel;
use App\Models\MasterData\Battery\BatteryUrlModel;
use App\Models\Settings\PaymentMethodModel;
use App\Models\Accounting\ExpenseModel;
use App\Models\Orders\SalesOrder\SalesOrderExpenseModel;
use App\Models\Accounting\BillingInvoiceExpenseModel;
use App\Models\Servers\ServerPaymentGatewayModel;

// Midtrans 
use App\Services\Midtrans\CreateSnapTokenService;

class SmoothQuotationController extends Controller
{
    public function index(Request $request)
    {
        $request->session()->put('invoice', SalesOrderModel::newCode());
        $Distibutor = DistributorShopModel::where('latitude', '!=', null)->where('longitude', '!=', null)->where('status', 1)->get()->toArray();

        $datalatlong = [];
        foreach ($Distibutor as $key => $value) {
            $datalatlong[] = [
                'latitude' => $value['latitude'],
                'longitude' => $value['longitude'],
                'name' => $value['name'],
                'address' => $value['address'],
                'contact' => $value['contact'],
                'id' => $value['id']
            ];
        }

        return view(
            'experimental.quick-quotation.index',
            getIndexData(
                'Smooth Quick Quotation',
                array(
                    'Vehicle' => VehicleModel::with(['brand', 'year'])->where('status', 1)->get()->toArray(),
                    'BatteryCategory' => BatterySizeCategoryModel::orderBy('name', 'asc')->get()->toArray(),
                    'VehicleBrands' => VehicleBrandModel::where('status', 1)->orderBy('name', 'asc')->get()->toArray(),
                    'VehicleYears' => VehicleYearModel::orderBy('start_year', 'asc')->get()->toArray(),
                    'VehicleFuels' => VehicleFuelModel::where('status', 1)->orderBy('name', 'asc')->get()->toArray(),
                    'VehicleTransmissions' => VehicleTransmissionModel::where('status', 1)->orderBy('name', 'asc')->get()->toArray(),
                    'datalatlong ' => $datalatlong,
                    'distibutor' => $Distibutor
                )
            )
        );
    }

    public function findCustomer(Request $request)
    {
        $query = $request->input('input');
        $results = CustomerModel::where('name', 'like', '%' . $query . '%')->where('status', 1)->orderBy('name', 'asc')->limit(10)->get();
        return response()->json($results);
    }

    public function shareFormPersonalDetails(Request $request)
    {
        $url = "https://whatsapp.akikita.id/send-message";
        $Fullname = $request->input('FullName');
        $AddressCustomer = $request->input('AddressCustomer');
        $EmailCustomer = $request->input('EmailCustomer');
        $ContactNumber = $request->input('ContactNumber');
        $VehicleCustomer = VehicleModel::whereIn('id', $request->input('VehicleCustomer'))->pluck('name')->toArray();

        $arrayVehicle = "";
        foreach ($VehicleCustomer as $key => $value) {
            $arrayVehicle .= "- " . $value . "\r";
        }

        $TemplateMessagePersonalDetails = MessageTemplateModel::where('name', 'personal_details')->first()->toArray();

        $opening_message = str_replace(
            ["<FULLNAME>", "<B>", "<ENTER>"],
            [$Fullname, "*", "\n"],
            $TemplateMessagePersonalDetails['opening_message']
        );

        $content_message = "
*Alamat Anda* :
📍 $AddressCustomer 

*Email Anda* :
📧 $EmailCustomer

*Nomor kontak Anda* :
📞 +62 $ContactNumber

*Kendaraan Anda* :
$arrayVehicle";

        $message  = $opening_message . "\n" . $content_message . "\n" . $TemplateMessagePersonalDetails['closing_message'];


        $data = [
            'to' => "62" . $request->input('ContactNumber'),
            'session' => "admin_ams",
            'text' => $message,
        ];

        try {
            $response = Http::post($url, $data);
            if ($response->successful()) {
                $responseData = $response->json();
                if (isset($responseData['data']['status']) && $responseData['data']['status'] == 1) {
                    return getResponseData(true, "Message sent successfully");
                } else {
                    return getResponseData(false, "Failed to send message : " . $responseData['data']['message']);
                }
            } else {
                $responseData = $response->json();
                return getResponseData(false, "Failed to send message : " . $responseData['message']);
            }
        } catch (\Exception $e) {
            return getResponseData(false, "Failed to send message => " . $e->getMessage());
        }
    }

    public function findVehicleByIdCustomer(Request $request)
    {
        $id = $request->input('id');
        if (isset($id)) {
            $results = CustomerModel::find($id)->vehicles()->pluck("vehicle_id")->toArray();
            return response()->json($results);
        } else {
            return response()->json([]);
        }
    }

    public function findVehicleByIdVehicle(Request $request)
    {
        $ids = $request->input('id');
        $custom = $request->input('custom');
        $category = $request->input('category');
        $cca = $request->input('cca');
        $capacity = $request->input('capacity');
        $dimension = $request->input('dimension');
        $idcustom = explode(",", $request->input('name'));

        if (isset($custom) && $custom == 'true') {
            if ($idcustom) {
                if (isset($category) && $category != '') {
                    if ($idcustom[0] == 'ALL') {
                        if ($category != 'ALL') {
                            $results = VehicleModel::getBatteryRecomendationWithCategoryAll($category);
                        }

                        if ($cca  != 'ALL') {
                            $results = VehicleModel::getBatteryRecomendationWithCategoryAndCca($cca);
                        }

                        if ($capacity != 'ALL') {
                            $results = VehicleModel::getBatteryRecomendationWithCategoryAndCapacity($capacity);
                        }

                        if ($dimension != 'ALL') {
                            $results = VehicleModel::getBatteryRecomendationWithCategoryAndDimension($dimension);
                        }
                    } else {
                        $results = VehicleModel::getBatteryRecomendationWithCategoryFix($idcustom);
                    }
                } else {
                    $results = VehicleModel::getBatteryRecomendationWithCategoryFix($idcustom);
                }
            } else {
                $results = VehicleModel::getBatteryRecomendationWithOutDistributor($ids, $request->input('shop_id'));
            }
        } else {
            if ($request->input('shop_id')) {
                $results = VehicleModel::getBatteryRecomendationWithDistributor($ids, $request->input('shop_id'));
            } else {
                $results = VehicleModel::getBatteryRecomendationWithOutDistributor($ids, $request->input('shop_id'));
            }
        }
        $tax = TaxModel::where('status', '1')->first();
        if ($tax && $results) {
            foreach ($results as $result) {
                $result->tax = $tax->percentage;
            }
        }
        return response()->json($results);
    }

    public function getMapsNearAddressCustomer(Request $request)
    {
        $address = $request->input('address');
        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');
        $Distibutor = DistributorShopModel::where('latitude', '!=', null)->where('longitude', '!=', null)->get()->toArray();

        $datalatlong = [];
        foreach ($Distibutor as $key => $value) {
            $datalatlong[] = [
                'latitude' => $value['latitude'],
                'longitude' => $value['longitude'],
                'name' => $value['name'],
                'address' => $value['address'],
                'contact' => $value['contact'],
                'id' => $value['id']
            ];
        }

        $data = [
            'address' => $address,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'distributor' => $Distibutor,
            'datalatlong' => $datalatlong
        ];

        return view('experimental.quick-quotation.step-2-mapsaddressdistributor', $data);
    }

    public function shareBattery(Request $request)
    {
        $url = "https://whatsapp.akikita.id/send-image";
        $ids = $request->input('Battery');
        $Fullname = $request->input('FullName');
        $Tax = TaxModel::where('status', '1')->first()->percentage;

        $results = BatteryModel::join('battery_prices', 'batteries.id', '=', 'battery_prices.battery_id', 'left')
            ->where('batteries.id', $ids)
            ->select('batteries.*', 'battery_prices.discount', 'battery_prices.price_net', 'battery_prices.price_retail as price_retail_original')
            ->get()
            ->toArray();

        if ($results == null) {
            return getResponseData(false, "Battery not found");
        } else {
            $arrayBattery = "";
            foreach ($results as $key => $value) {
                $arrayBattery .= "*Nama* : " . $value['name'] . "\r\n";
                $arrayBattery .= "*Dimensi* : " . $value['dimension_length'] . " x " . $value['dimension_width'] . " x " . $value['dimension_height'] . " cm\r\n";
                $arrayBattery .= "*Kapasitas* : " . $value['capacity'] . " AH\r\n";
                $arrayBattery .= "*CCA* : " . $value['standard_cca'] . " A\r\n";
                $arrayBattery .= "*Garansi* : " . $value['warranty'] . " Bulan\r\n";
                if ($value['discount'] != 0) {
                    $grossPriceWithTax = $value['price_retail'] + ($value['price_retail'] * $Tax / 100);
                    $price_discount = $value['price_retail'] - ($value['price_retail'] * $value['discount'] / 100);
                    $price_tax =  $price_discount + ($price_discount * $Tax / 100);
                    $arrayBattery .= "*Harga* : ~Rp. " . number_format($grossPriceWithTax, 0, "", ".") . "~ \n*Discount* : " . number_format($value['discount']) . "%\r\n";
                    $arrayBattery .= "*Harga + PPN* : Rp. " . number_format($price_tax, 0, "", ".") . "\r";
                } else {
                    $grossPriceWithTax = $value['price_retail'] + ($value['price_retail'] * $Tax / 100);
                    $arrayBattery .= "*Harga* : Rp. " . number_format($grossPriceWithTax, 0, "", ".") . "\r\n";
                    $arrayBattery .= "*Discount* : " . number_format($value['discount']) . "%\r\n";
                    $arrayBattery .= "*Harga + PPN* : Rp. " . number_format($value['price_retail'] + ($value['price_retail'] * $Tax / 100), 0, "", ".") . "\r";
                }

                $TemplateMessagePersonalDetails = MessageTemplateModel::where('name', 'product_recommendation')->first()->toArray();

                $opening_message = str_replace(
                    ["<FULLNAME>", "<B>", "<ENTER>"],
                    [$Fullname, "*", "\n"],
                    $TemplateMessagePersonalDetails['opening_message']
                );

                $content_message = "\n" . $arrayBattery . "\n";
                $message  = $opening_message . "\n" . $content_message . "\n" . $TemplateMessagePersonalDetails['closing_message'];

                if ($value['image'] != null) {
                    $value['image'] = asset('storage/image/battery/' . $value['image']);
                } else {
                    $value['image'] = null;
                }

                if ($value['image'] != null) {
                    $head = @get_headers($value['image']);
                    if ($head && strpos($head[0], '200')) {
                        $value['image'] = $value['image'];
                    } else {
                        $value['image'] = "https://via.placeholder.com/210x210";
                    }
                } else {
                    $value['image'] = "https://via.placeholder.com/210x210";
                }

                $data = [
                    'to' => "62" . $request->input('ContactNumber'),
                    'session' => "admin_ams",
                    'url' => $value['image'] ?? "https://via.placeholder.com/210x210",
                    'caption' => $message,
                ];

                try {
                    $response = Http::post($url, $data);
                    if ($response->successful()) {
                        $responseData = $response->json();
                        if (isset($responseData['data']['status']) && $responseData['data']['status'] == 1) {
                            return getResponseData(true, "Message sent successfully");
                        } else {
                            return getResponseData(false, "Failed to send message");
                        }
                    } else {
                        $responseData = $response->json();
                        return getResponseData(false, "Failed to send message : " . $responseData['message']);
                    }
                } catch (\Exception $e) {
                    return getResponseData(false, "Failed to send message => " . $e->getMessage());
                }
            }
        }
    }

    public function getCheckoutPreview(Request $request)
    {
        $Fullname = $request->input('FullName');
        $AddressCustomer = $request->input('AddressCustomer');
        $EmailCustomer = $request->input('EmailCustomer');
        $ContactNumber = $request->input('ContactNumber');
        $VehicleCustomer = VehicleModel::whereIn('id', $request->input('VehicleCustomer'))->pluck('name')->toArray();
        if ($request->input('DistributorShopId') != null) {
            $BatteryData = BatteryModel::getBatteryDistributor($request->input('Battery'), $request->input('DistributorShopId'));
            $distributorChecked = DistributorShopModel::find($request->input('DistributorShopId'))->toArray();
            $distributorTechnician = DistributorShopModel::find($request->input('DistributorShopId'))->technicians()->get()->toArray();
        } else {
            $distributorChecked = "";
            $distributorTechnician = "";
            $BatteryData = BatteryModel::getBatteryDistributor($request->input('Battery'), $request->input('DistributorShopId'));
        }
        $tax = TaxModel::where('status', '1')->first()->percentage;
        $alternativeAddress = $request->input('alternative_address');

        $data = [
            'Fullname' => $Fullname,
            'AddressCustomer' => $AddressCustomer,
            'EmailCustomer' => $EmailCustomer,
            'ContactNumber' => $ContactNumber,
            'VehicleCustomer' => $VehicleCustomer,
            'VehicleCustomerString' => implode(', ', $VehicleCustomer),
            'Battery' => $BatteryData,
            'Latitude' => $request->input('Latitude'),
            'Longitude' => $request->input('Longitude'),
            'Distributor' => $distributorChecked,
            'DistributorTechnician' => $distributorTechnician,
            'tax' => $tax,
            'alternativeAddress' => $alternativeAddress
        ];

        return view('experimental.quick-quotation.step-3-checkoutpreview', $data);
    }

    public function getPaymentPreview(Request $request)
    {
        $Fullname = $request->input('FullName');
        $AddressCustomer = $request->input('AddressCustomer');
        $EmailCustomer = $request->input('EmailCustomer');
        $ContactNumber = $request->input('ContactNumber');
        $VehicleCustomer = VehicleModel::whereIn('id', $request->input('VehicleCustomer'))->pluck('name')->toArray();
        $Battery = BatteryModel::whereIn('id', $request->input('Battery'))->pluck('name')->toArray();
        $BatteryData = BatteryModel::whereIn('id', $request->input('Battery'))->get()->toArray();
        $TotalAmount = $request->input('TotalAmount');
        $InvoiceNumber = $request->session()->get('invoice', SalesOrderModel::newCode());
        $BatteryNameTabel = $request->input('BatteryNameTabel') ?? [];
        $QtyTabel = $request->input('QtyTabel') ?? [];
        $PriceTabel = $request->input('PriceTabel') ?? [];
        $Link = $request->input('LinkTokopedia');
        $Platform = $request->input('Platform');
        $tax = $request->input('tax') ?? 0;
        $ExtraDiscount = $request->input('ExtraDiscount') ?? 0;

        $GrossPrice = $request->input('GrossPrice') ?? [];
        $NetPrice = $request->input('NetPrice') ?? [];
        $DiscountRow = $request->input('DiscountRow') ?? [];
        $SubtotalRow = $request->input('SubtotalRow') ?? [];
        $TaxRow = $request->input('TaxRow') ?? [];
        $TaxPriceRow = $request->input('TaxPriceRow') ?? [];
        $PaymentMethod = PaymentMethodModel::all()->toArray();
        $typeDiscount = $request->input('typeDiscount');
        $alternativeAddress = $request->input('alternative_address');
        $IsInstallationIncluded = $request->input('IsInstallationIncluded');

        if ($typeDiscount == 'rupiah') {
            $Discount = $request->input('DiscountRupiah') ?? 0;
        } else {
            $Discount = $request->input('DiscountPercentage') ?? 0;
        }
        if ($request->input('DistributorShopId') != null) {
            $DistibutorShop = DistributorShopModel::find($request->input('DistributorShopId'));
            $BatteryData = BatteryModel::getBatteryDistributor($request->input('Battery'), $request->input('DistributorShopId'));
            $dataProduct = [];
            foreach ($BatteryNameTabel as $key => $value) {
                $dataProduct[] = [
                    'name' => $value,
                    'qty' => $QtyTabel[$key],
                    'price' => $GrossPrice[$key],
                    'link' => '',
                    'DiscountRow' => $DiscountRow[$key],
                    'NetPrice' => $NetPrice[$key],
                    'SubtotalRow' => $SubtotalRow[$key],
                    'TaxRow' => $TaxRow[$key],
                    'TaxPriceRow' => $TaxPriceRow[$key],
                    'BatteryType' => $request->input('BatteryType')[$key] ?? 'regular',
                ];
            }
        } else {
            $DistibutorShop = "";
            $dataProduct = [];
            foreach ($BatteryNameTabel as $key => $value) {
                $dataProduct[] = [
                    'name' => $value,
                    'qty' => $QtyTabel[$key],
                    'price' => $GrossPrice[$key],
                    'link' => '',
                    'DiscountRow' => $DiscountRow[$key],
                    'NetPrice' => $NetPrice[$key],
                    'SubtotalRow' => $SubtotalRow[$key],
                    'TaxRow' => $TaxRow[$key],
                    'TaxPriceRow' => $TaxPriceRow[$key],
                    'BatteryType' => $request->input('BatteryType')[$key] ?? 'regular',
                ];
            }
        }

        $data = [
            'Fullname' => $Fullname,
            'AddressCustomer' => $AddressCustomer,
            'EmailCustomer' => $EmailCustomer,
            'ContactNumber' => $ContactNumber,
            'VehicleCustomer' => $VehicleCustomer,
            'VehicleCustomerString' => implode(', ', $VehicleCustomer),
            'Battery' => $BatteryData,
            'BatteryString' => implode(', ', $Battery),
            'Latitude' => $request->input('Latitude'),
            'Longitude' => $request->input('Longitude'),
            'InvoiceNumber' => $InvoiceNumber,
            'TotalAmount' => $TotalAmount,
            'dataProduct' => $dataProduct,
            'tax' => $tax,
            'Discount' => $Discount,
            'ExtraDiscount' => $ExtraDiscount,
            'DistributorShop' => $DistibutorShop,
            'Subtotal' => $request->input('subtotal') ?? 0,
            'PaymentMethod' => $PaymentMethod,
            'typeDiscount' => $typeDiscount,
            'alternativeAddress' => $alternativeAddress,
            'IsInstallationIncluded' => $IsInstallationIncluded,
            'expenses' => ExpenseModel::with('chartOfAccount')->where('is_active', 1)->orderBy('name', 'asc')->get()->toArray()
        ];

        $ServerPaymentGateway = ServerPaymentGatewayModel::where('name', 'MIDTRANS')->first();
        $invoiceNumberMidtrans = $InvoiceNumber . '-' . time();
        $request->session()->put('invoiceNumberMidtrans', $invoiceNumberMidtrans);
        $data['invoiceNumberMidtrans'] = $invoiceNumberMidtrans;
        if ($ServerPaymentGateway) {
            try {
                $midtrans = new CreateSnapTokenService($request->session()->get('invoiceNumberMidtrans'));
                $snapToken = $midtrans->getSnapTokenUrl($data);
                $data['snapToken'] = $snapToken;
            } catch (\Throwable $th) {
                $data['snapToken'] = $th->getMessage();
            }
        } else {
            $data['snapToken'] = null;
        }

        return view('experimental.quick-quotation.step-4-paymentpreview', $data);
    }

    public function storeVehicle(Request $request)
    {
        $id = $request->input('customer_id');
        $customer = CustomerModel::find($id);

        $validatedData = $request->validate([
            'brand' => 'required',
            'name' => 'required',
            'fuel' => 'required',
            'transmission' => 'required',
            'year' => 'required',
            'note' => 'nullable',
        ]);

        $vehicle = new VehicleModel();
        $vehicle->brand_id = $request->input('brand');
        $vehicle->name = $request->input('name');
        $vehicle->fuel_id = $request->input('fuel');
        $vehicle->transmission_id = $request->input('transmission');
        $vehicle->year_id = $request->input('year');
        $vehicle->note = $request->input('note');
        $vehicle->status = 1;
        $vehicle->save();

        if ($customer) {
            $customer->vehicles()->attach($vehicle->id);
        }

        return response()->json([
            'status' => true,
            'message' => 'Vehicle successfully created',
            'data' => $vehicle
        ]);
    }

    public function getVehicleList()
    {
        $results = VehicleModel::with(['brand', 'year'])->where('status', 1)->get()->toArray();
        return response()->json($results);
    }

    public function getBatteryCopyDetail(Request $request)
    {
        $ids = $request->input('Battery');
        $Fullname = $request->input('FullName');
        $Tax = TaxModel::where('status', '1')->first()->percentage;

        $results = BatteryModel::join('battery_prices', 'batteries.id', '=', 'battery_prices.battery_id', 'left')
            ->where('batteries.id', $ids)
            ->select('batteries.*', 'battery_prices.discount', 'battery_prices.price_net', 'battery_prices.price_retail as price_retail_original')
            ->get()
            ->toArray();

        if ($results == null) {
            return getResponseData(false, "Battery not found");
        } else {
            $arrayBattery = "";
            foreach ($results as $key => $value) {
                $arrayBattery .= "*Nama* : " . $value['name'] . "\r\n";
                $arrayBattery .= "*Dimensi* : " . $value['dimension_length'] . " x " . $value['dimension_width'] . " x " . $value['dimension_height'] . " cm\r\n";
                $arrayBattery .= "*Kapasitas* : " . $value['capacity'] . " AH\r\n";
                $arrayBattery .= "*CCA* : " . $value['standard_cca'] . " A\r\n";
                $arrayBattery .= "*Garansi* : " . $value['warranty'] . " Bulan\r\n";
                if ($value['discount'] != 0) {
                    $grossPriceWithTax = $value['price_retail'] + ($value['price_retail'] * $Tax / 100);
                    $price_discount = $value['price_retail'] - ($value['price_retail'] * $value['discount'] / 100);
                    $price_tax =  $price_discount + ($price_discount * $Tax / 100);
                    $arrayBattery .= "*Harga* : ~Rp. " . number_format($grossPriceWithTax, 0, "", ".") . "~ \n*Discount* : " . number_format($value['discount']) . "%\r\n";
                    $arrayBattery .= "*Harga + PPN* : Rp. " . number_format($price_tax, 0, "", ".") . "\r";
                } else {
                    $grossPriceWithTax = $value['price_retail'] + ($value['price_retail'] * $Tax / 100);
                    $arrayBattery .= "*Harga* : Rp. " . number_format($grossPriceWithTax, 0, "", ".") . "\r\n";
                    $arrayBattery .= "*Discount* : " . number_format($value['discount']) . "%\r\n";
                    $arrayBattery .= "*Harga + PPN* : Rp. " . number_format($value['price_retail'] + ($value['price_retail'] * $Tax / 100), 0, "", ".") . "\r";
                }

                $TemplateMessagePersonalDetails = MessageTemplateModel::where('name', 'product_recommendation')->first()->toArray();

                $opening_message = str_replace(
                    ["<FULLNAME>", "<B>", "<ENTER>"],
                    [$Fullname, "*", "\n"],
                    $TemplateMessagePersonalDetails['opening_message']
                );

                $content_message = "\n" . $arrayBattery . "\n";
                $message  = $opening_message . "\n" . $content_message . "\n" . $TemplateMessagePersonalDetails['closing_message'];

                return response()->json([
                    'status' => true,
                    'message' => 'Detail data copied successfully',
                    'data' => $message
                ]);
            }
        }
    }

    public function shareInvoice(Request $request)
    {
        $url = "https://whatsapp.akikita.id/send-message";
        $Fullname = $request->input('FullName');
        $AddressCustomer = $request->input('AddressCustomer');
        $EmailCustomer = $request->input('EmailCustomer');
        $ContactNumber = $request->input('ContactNumber');
        $VehicleCustomer = VehicleModel::whereIn('id', $request->input('VehicleCustomer'))->pluck('name')->toArray();
        $InvoiceNumber = $request->session()->get('invoice', SalesOrderModel::newCode());
        $TotalAmount = $request->input('TotalAmount');
        $DistributorShopId = $request->input('DistributorShopId');
        $BatteryNameTabel = $request->input('BatteryNameTabel') ?? [];
        $QtyTabel = $request->input('QtyTabel') ?? [];
        $GrossPrice = $request->input('GrossPrice') ?? [];
        $SubtotalRow = $request->input('SubtotalRow') ?? [];
        $DiscountRow = $request->input('DiscountRow') ?? [];
        $TaxPriceRow = $request->input('TaxPriceRow') ?? [];

        $arrayVehicle = implode(', ', $VehicleCustomer);

        $batteryDetails = "";
        foreach ($BatteryNameTabel as $key => $value) {
            $batteryDetails .= "- " . $value . " (x" . $QtyTabel[$key] . ") - Rp. " . number_format($SubtotalRow[$key], 0, "", ".") . "\r\n";
        }

        $TemplateMessagePersonalDetails = MessageTemplateModel::where('name', 'invoice')->first()->toArray();

        $opening_message = str_replace(
            ["<FULLNAME>", "<B>", "<ENTER>"],
            [$Fullname, "*", "\n"],
            $TemplateMessagePersonalDetails['opening_message']
        );

        $content_message = "
*Nomor Invoice* : 
📄 $InvoiceNumber

*Kendaraan Anda* :
🚗 $arrayVehicle

*Detail Pesanan* :
$batteryDetails
*Total Pembayaran* :
💰 Rp. " . number_format($TotalAmount, 0, "", ".") . "

*Alamat Pengiriman* :
📍 $AddressCustomer";

        $message  = $opening_message . "\n" . $content_message . "\n" . $TemplateMessagePersonalDetails['closing_message'];

        $data = [
            'to' => "62" . $request->input('ContactNumber'),
            'session' => "admin_ams",
            'text' => $message,
        ];

        try {
            $response = Http::post($url, $data);
            if ($response->successful()) {
                $responseData = $response->json();
                if (isset($responseData['data']['status']) && $responseData['data']['status'] == 1) {
                    return getResponseData(true, "Invoice shared successfully");
                } else {
                    return getResponseData(false, "Failed to share invoice : " . $responseData['data']['message']);
                }
            } else {
                $responseData = $response->json();
                return getResponseData(false, "Failed to share invoice : " . $responseData['message']);
            }
        } catch (\Exception $e) {
            return getResponseData(false, "Failed to share invoice => " . $e->getMessage());
        }
    }

    public function sharePaymentDetails(Request $request)
    {
        $url = "https://whatsapp.akikita.id/send-message";
        $Fullname = $request->input('FullName');
        $ContactNumber = $request->input('ContactNumber');
        $InvoiceNumber = $request->session()->get('invoice', SalesOrderModel::newCode());
        $TotalAmount = $request->input('TotalAmount');

        $TemplateMessagePersonalDetails = MessageTemplateModel::where('name', 'payment_details')->first()->toArray();

        $opening_message = str_replace(
            ["<FULLNAME>", "<B>", "<ENTER>"],
            [$Fullname, "*", "\n"],
            $TemplateMessagePersonalDetails['opening_message']
        );

        $content_message = "
*Nomor Invoice* : 
📄 $InvoiceNumber

*Total Pembayaran* :
💰 Rp. " . number_format($TotalAmount, 0, "", ".") . "

Silakan lakukan pembayaran melalui link berikut:
" . $request->input('PaymentLink');

        $message  = $opening_message . "\n" . $content_message . "\n" . $TemplateMessagePersonalDetails['closing_message'];

        $data = [
            'to' => "62" . $request->input('ContactNumber'),
            'session' => "admin_ams",
            'text' => $message,
        ];

        try {
            $response = Http::post($url, $data);
            if ($response->successful()) {
                $responseData = $response->json();
                if (isset($responseData['data']['status']) && $responseData['data']['status'] == 1) {
                    return getResponseData(true, "Payment details shared successfully");
                } else {
                    return getResponseData(false, "Failed to share payment details : " . $responseData['data']['message']);
                }
            } else {
                $responseData = $response->json();
                return getResponseData(false, "Failed to share payment details : " . $responseData['message']);
            }
        } catch (\Exception $e) {
            return getResponseData(false, "Failed to share payment details => " . $e->getMessage());
        }
    }

    public function saveData(Request $request)
    {
        DB::beginTransaction();

        try {
            $invoice = SalesOrderModel::newCode();
            $request->session()->put('invoice', $invoice);

            $customer = CustomerModel::where('contact', $request->input('ContactNumber'))->first();
            if (!$customer) {
                $customer = new CustomerModel();
                $customer->name = $request->input('FullName');
                $customer->contact = $request->input('ContactNumber');
                $customer->email = $request->input('EmailCustomer');
                $customer->address = $request->input('AddressCustomer');
                $customer->status = 1;
                $customer->save();

                $vehicles = $request->input('VehicleCustomer');
                if (is_array($vehicles)) {
                    foreach ($vehicles as $vehicle_id) {
                        $customer->vehicles()->attach($vehicle_id);
                    }
                }
            }

            $sales_order = new SalesOrderModel();
            $sales_order->code = $invoice;
            $sales_order->customer_id = $customer->id;
            $sales_order->distributor_shop_id = $request->input('DistributorShopId');
            $sales_order->status = 1; // Pending
            $sales_order->tax_percentage = $request->input('tax') ?? 0;
            $sales_order->extra_discount = $request->input('ExtraDiscount') ?? 0;
            $sales_order->discount_type = $request->input('typeDiscount');
            if ($sales_order->discount_type == 'rupiah') {
                $sales_order->discount_value = $request->input('DiscountRupiah') ?? 0;
            } else {
                $sales_order->discount_value = $request->input('DiscountPercentage') ?? 0;
            }
            $sales_order->subtotal = $request->input('subtotal') ?? 0;
            $sales_order->total_amount = $request->input('TotalAmount');
            $sales_order->payment_method_id = $request->input('PaymentMethodId');
            $sales_order->alternative_address = $request->input('alternative_address');
            $sales_order->is_installation_included = $request->input('IsInstallationIncluded') == 'true' ? 1 : 0;
            $sales_order->save();

            // Batteries
            $batteryIds = $request->input('Battery') ?? [];
            $batteryNames = $request->input('BatteryNameTabel') ?? [];
            $qtys = $request->input('QtyTabel') ?? [];
            $grossPrices = $request->input('GrossPrice') ?? [];
            $discounts = $request->input('DiscountRow') ?? [];
            $netPrices = $request->input('NetPrice') ?? [];
            $subtotals = $request->input('SubtotalRow') ?? [];
            $taxPercentages = $request->input('TaxRow') ?? [];
            $taxValues = $request->input('TaxPriceRow') ?? [];
            $batteryTypes = $request->input('BatteryType') ?? [];

            foreach ($batteryIds as $key => $batteryId) {
                $so_battery = new SalesOrderBatteryModel();
                $so_battery->sales_order_id = $sales_order->id;
                $so_battery->battery_id = $batteryId;
                $so_battery->qty = $qtys[$key] ?? 1;
                $so_battery->gross_price = $grossPrices[$key] ?? 0;
                $so_battery->discount = $discounts[$key] ?? 0;
                $so_battery->net_price = $netPrices[$key] ?? 0;
                $so_battery->subtotal = $subtotals[$key] ?? 0;
                $so_battery->tax_percentage = $taxPercentages[$key] ?? 0;
                $so_battery->tax_value = $taxValues[$key] ?? 0;
                $so_battery->battery_type = $batteryTypes[$key] ?? 'regular';
                $so_battery->save();
            }

            // Expenses
            $expenseIds = $request->input('expense_id') ?? [];
            $expenseValues = $request->input('expense_value') ?? [];
            foreach ($expenseIds as $key => $expenseId) {
                if (($expenseValues[$key] ?? 0) > 0) {
                    $so_expense = new SalesOrderExpenseModel();
                    $so_expense->sales_order_id = $sales_order->id;
                    $so_expense->expense_id = $expenseId;
                    $so_expense->value = $expenseValues[$key];
                    $so_expense->save();
                }
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Quotation successfully saved',
                'data' => [
                    'invoice' => $invoice,
                    'sales_order_id' => $sales_order->id
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to save quotation => ' . $e->getMessage()
            ]);
        }
    }

    public function getCustomerCopyDetail(Request $request)
    {
        $Fullname = $request->input('FullName');
        $AddressCustomer = $request->input('AddressCustomer');
        $EmailCustomer = $request->input('EmailCustomer');
        $ContactNumber = $request->input('ContactNumber');
        $VehicleCustomer = VehicleModel::whereIn('id', $request->input('VehicleCustomer'))->pluck('name')->toArray();

        $arrayVehicle = implode(', ', $VehicleCustomer);

        $message = "
*Detail Pelanggan* :
Nama: $Fullname
No. Kontak: +62 $ContactNumber
Email: $EmailCustomer
Alamat: $AddressCustomer
Kendaraan: $arrayVehicle";

        return response()->json([
            'status' => true,
            'message' => 'Customer details copied successfully',
            'data' => $message
        ]);
    }

    public function getCheckoutCopyDetail(Request $request)
    {
        $Fullname = $request->input('FullName');
        $AddressCustomer = $request->input('AddressCustomer');
        $ContactNumber = $request->input('ContactNumber');
        $BatteryNameTabel = $request->input('BatteryNameTabel') ?? [];
        $QtyTabel = $request->input('QtyTabel') ?? [];
        $SubtotalRow = $request->input('SubtotalRow') ?? [];

        $batteryDetails = "";
        foreach ($BatteryNameTabel as $key => $value) {
            $batteryDetails .= "- $value (x" . $QtyTabel[$key] . ") - Rp. " . number_format($SubtotalRow[$key], 0, "", ".") . "\r\n";
        }

        $message = "
*Ringkasan Checkout* :
Pelanggan: $Fullname (+62 $ContactNumber)
Alamat Pengiriman: $AddressCustomer

Produk yang Dipesan:
$batteryDetails";

        return response()->json([
            'status' => true,
            'message' => 'Checkout details copied successfully',
            'data' => $message
        ]);
    }

    public function getPaymentDetailsCopyDetail(Request $request)
    {
        $InvoiceNumber = $request->session()->get('invoice', SalesOrderModel::newCode());
        $TotalAmount = $request->input('TotalAmount');
        $PaymentLink = $request->input('PaymentLink');

        $message = "
*Detail Pembayaran* :
No. Invoice: $InvoiceNumber
Total Tagihan: Rp. " . number_format($TotalAmount, 0, "", ".") . "

Silakan lakukan pembayaran melalui tautan berikut:
$PaymentLink";

        return response()->json([
            'status' => true,
            'message' => 'Payment details copied successfully',
            'data' => $message
        ]);
    }

    public function findCustomerByContact(Request $request)
    {
        $contact = $request->input('contact');
        $customer = CustomerModel::where('contact', $contact)->first();
        if ($customer) {
            $vehicles = $customer->vehicles()->pluck("vehicle_id")->toArray();
            return response()->json([
                'status' => true,
                'customer' => $customer,
                'vehicles' => $vehicles
            ]);
        }
        return response()->json([
            'status' => false
        ]);
    }

    public function findBattery(Request $request)
    {
        $query = $request->input('query');
        $shopId = $request->input('shop_id');
        if ($shopId) {
            $results = BatteryModel::getBatteryRecommendationWithDistributorAutocomplete($query, $shopId);
        } else {
            $results = BatteryModel::where('name', 'like', '%' . $query . '%')->limit(10)->get();
        }
        return response()->json($results);
    }

    public function getLinkBattery(Request $request)
    {
        $id = $request->input('id');
        $urls = BatteryUrlModel::where('battery_id', $id)->get()->toArray();
        return response()->json($urls);
    }

    public function findDistributor(Request $request)
    {
        $query = $request->input('query');
        $results = DistributorShopModel::where('name', 'like', '%' . $query . '%')->where('status', 1)->limit(10)->get();
        return response()->json($results);
    }

    public function autoCompleteBattery(Request $request)
    {
        $query = $request->input('query');
        $results = BatteryModel::where('name', 'like', '%' . $query . '%')->limit(10)->get();
        return response()->json($results);
    }

    public function screenshotBattery(Request $request)
    {
        $batteryId = $request->input('battery_id');
        $battery = BatteryModel::find($batteryId);
        if (!$battery) {
            return response()->json(['status' => false, 'message' => 'Battery not found']);
        }
        return view('experimental.quick-quotation.Screenshoot.battery', ['battery' => $battery]);
    }

    public function saveScreenshoot(Request $request)
    {
        $image = $request->input('image');
        $batteryId = $request->input('battery_id');
        $image = str_replace('data:image/png;base64,', '', $image);
        $image = str_replace(' ', '+', $image);
        $imageName = 'battery_' . $batteryId . '_' . time() . '.png';
        Storage::disk('public')->put('image/battery/screenshot/' . $imageName, base64_decode($image));

        return response()->json(['status' => true, 'path' => asset('storage/image/battery/screenshot/' . $imageName)]);
    }

    public function filterBatteryByCategory(Request $request)
    {
        $category = $request->input('category');
        $results = BatteryModel::where('size_category_id', $category)->get();
        return response()->json($results);
    }

    public function filterBatteryByCCA(Request $request)
    {
        $cca = $request->input('cca');
        $results = BatteryModel::where('standard_cca', $cca)->get();
        return response()->json($results);
    }

    public function filterBatteryByCapacity(Request $request)
    {
        $capacity = $request->input('capacity');
        $results = BatteryModel::where('capacity', $capacity)->get();
        return response()->json($results);
    }

    public function filterBatteryByDimension(Request $request)
    {
        $dimension = $request->input('dimension');
        $results = BatteryModel::where(DB::raw("CONCAT(dimension_length, 'x', dimension_width, 'x', dimension_height)"), $dimension)->get();
        return response()->json($results);
    }

    public function fixDetailPercentage(Request $request)
    {
        $tax = TaxModel::where('status', '1')->first();
        return response()->json([
            'status' => true,
            'tax' => $tax ? $tax->percentage : 0
        ]);
    }

    public function autoCompleteBatteryCategory(Request $request)
    {
        $query = $request->input('query');
        $results = BatterySizeCategoryModel::where('name', 'like', '%' . $query . '%')->limit(10)->get();
        return response()->json($results);
    }

    public function autoCompleteBatteryCCA(Request $request)
    {
        $query = $request->input('query');
        $results = BatteryModel::where('standard_cca', 'like', '%' . $query . '%')
            ->select('standard_cca')
            ->groupBy('standard_cca')
            ->limit(10)
            ->get();
        return response()->json($results);
    }

    public function autoCompleteBatteryCapacity(Request $request)
    {
        $query = $request->input('query');
        $results = BatteryModel::where('capacity', 'like', '%' . $query . '%')
            ->select('capacity')
            ->groupBy('capacity')
            ->limit(10)
            ->get();
        return response()->json($results);
    }

    public function autoCompleteBatteryDimension(Request $request)
    {
        $query = $request->input('query');
        $results = BatteryModel::where(DB::raw("CONCAT(dimension_length, 'x', dimension_width, 'x', dimension_height)"), 'like', '%' . $query . '%')
            ->select(DB::raw("CONCAT(dimension_length, 'x', dimension_width, 'x', dimension_height) as dimension"))
            ->groupBy('dimension')
            ->limit(10)
            ->get();
        return response()->json($results);
    }

    public function autoCompleteBatteryName(Request $request)
    {
        $query = $request->input('query');
        $results = BatteryModel::where('name', 'like', '%' . $query . '%')->limit(10)->get();
        return response()->json($results);
    }
}
