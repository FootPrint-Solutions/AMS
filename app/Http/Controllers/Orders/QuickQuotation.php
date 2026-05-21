<?php

namespace App\Http\Controllers\Orders;

use App\Http\Controllers\Controller;
use App\Http\Controllers\MasterData\Vehicle\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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
use Illuminate\Support\Facades\DB;

// Midtrans 
use App\Services\Midtrans\CreateSnapTokenService;
use Faker\Provider\ar_EG\Payment;

class QuickQuotation extends Controller
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
            'Orders.QuickQuotation.index',
            getIndexData(
                'Quick Quotation',
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

    function shareFormPersonalDetails(Request $request)
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
                // $results = VehicleModel::whereIn('id', $ids)->with('batteries')->get()->pluck('batteries')->flatten();
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

        return view('Orders.QuickQuotation.step-2-mapsaddressdistributor', $data);
    }

    public function shareBattery(Request $request)
    {
        $url = "https://whatsapp.akikita.id/send-image";
        $ids = $request->input('Battery');
        $Fullname = $request->input('FullName');
        // $results = BatteryModel::where('id', $ids)->get()->toArray();
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
                    // hitung price discount 
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

                $content_message = "
$arrayBattery
";

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

        return view('Orders.QuickQuotation.step-3-checkoutpreview', $data);
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
        $BatteryNameTabel = $request->input('BatteryNameTabel');
        $QtyTabel = $request->input('QtyTabel');
        $PriceTabel = $request->input('PriceTabel');
        $Link = $request->input('LinkTokopedia');
        $Platform = $request->input('Platform');
        $tax = $request->input('tax') ?? 0;
        $ExtraDiscount = $request->input('ExtraDiscount') ?? 0;
        $GrossPrice = $request->input('GrossPrice');
        $NetPrice = $request->input('NetPrice');
        $DiscountRow = $request->input('DiscountRow');
        $SubtotalRow = $request->input('SubtotalRow');
        $TaxRow = $request->input('TaxRow');
        $TaxPriceRow = $request->input('TaxPriceRow');
        $PaymentMethod = PaymentMethodModel::all()->toArray();
        $typeDiscount = $request->input('typeDiscount');
        $alternativeAddress = $request->input('alternative_address');
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
            'expenses' => ExpenseModel::with('chartOfAccount')->where('is_active', 1)->orderBy('name', 'asc')->get()->toArray()
        ];


        // Check API Key Allready Exist
        $ServerPaymentGateway = ServerPaymentGatewayModel::where('name', 'MIDTRANS')->first();
        $invoiceNumberMidtrans = $InvoiceNumber . '-' . time();
        $request->session()->put('invoiceNumberMidtrans', $invoiceNumberMidtrans);
        $data['invoiceNumberMidtrans'] = $invoiceNumberMidtrans;
        if ($ServerPaymentGateway) {

            // create snap token from session invoice number
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


        return view('Orders.QuickQuotation.step-4-paymentpreview', $data);
    }

    public function getBatteryCopyDetail(Request $request)
    {
        $batteryIds = $request->input('Battery');
        $Fullname = $request->input('FullName');
        $batteries = BatteryModel::join('battery_prices', 'batteries.id', '=', 'battery_prices.battery_id', 'left')
            ->whereIn('batteries.id', $batteryIds)
            ->select('batteries.*', 'battery_prices.discount', 'battery_prices.price_net', 'battery_prices.price_retail as price_retail_original', 'battery_prices.discount_price')
            ->get();
        $Tax = TaxModel::where('status', '1')->first()->percentage;


        $arrayBattery = "";
        foreach ($batteries as $battery) {
            if ($battery->price_net != 0) {
                $grossPriceWithTax = $battery->price_retail_original + ($battery->price_retail_original * $Tax / 100);
                $price_net = $battery->price_retail_original;
                $discount = $battery->discount;
                $price_discount = $price_net - ($price_net * $discount / 100);
                $price_tax = $price_discount + ($price_discount * $Tax / 100);
            } else {
                $grossPriceWithTax = $battery->price_retail_original + ($battery->price_retail_original * $Tax / 100);
                $price_net = $battery->price_retail_original;
                $discount = 0;
                $price_net = $price_net - ($price_net * $discount / 100);
                $price_tax = $price_net + ($price_net * $Tax / 100);
            }
            $arrayBattery .= "*Nama* : " . $battery->name . "\r";
            $arrayBattery .= "*Dimensi* : " . $battery->dimension_length . " x " . $battery->dimension_width . " x " . $battery->dimension_height . " cm\r";
            $arrayBattery .= "*Kapasitas* : " . $battery->capacity . " AH\r";
            $arrayBattery .= "*CCA* : " . $battery->standard_cca . " A\r";
            $arrayBattery .= "*Garansi* : " . $battery->warranty . " Bulan\r";
            $arrayBattery .= "*Harga* : Rp. " . number_format($grossPriceWithTax, 0, "", ".") . "\r";
            $arrayBattery .= "*Discount* : Rp. " . number_format($battery->discount_price, 0, "", ".") . "\r";
            $arrayBattery .= "*Harga + PPN* : Rp. " . number_format($battery->price_net, 0, "", ".") . "\r";

            $arrayBattery .= "\r";
        }

        $TemplateMessagePersonalDetails = MessageTemplateModel::where('name', 'product_recommendation')->first()->toArray();

        $opening_message = str_replace(
            ["<FULLNAME>", "<B>", "<ENTER>"],
            [$Fullname, "*", "\n"],
            $TemplateMessagePersonalDetails['opening_message']
        );

        $content_message = "
$arrayBattery
";

        $closing_message = str_replace(
            ["<ENTER>", "<B>"],
            ["\n", "*"],
            $TemplateMessagePersonalDetails['closing_message']
        );

        $message  = $opening_message . "\n" . $content_message . "\n" . $closing_message;

        return getResponseData(true, $message);
    }


    public function shareInvoice(Request $request)
    {
        $url = "https://whatsapp.akikita.id/send-message";
        $FullName = $request->input('FullName');
        $ContactNumber = $request->input('ContactNumber');
        $Battery = $request->input('Battery');
        $Subtotal = $request->input('Subtotal');
        $Tax = $request->input('Tax');
        $Discount = $request->input('Discount');
        $TotalAmount = $request->input('TotalAmount');
        $VehicleCustomer = VehicleModel::whereIn('id', $request->input('VehicleCustomer'))->pluck('name')->toArray();
        $latitude = $request->input('Latitude');
        $longitude = $request->input('Longitude');
        $typeDiscount = $request->input('typeDiscount');

        $arrayVehicle = "";
        foreach ($VehicleCustomer as $key => $value) {
            if ($key == count($VehicleCustomer) - 1) {
                $arrayVehicle .= "" . $value;
            } else {
                $arrayVehicle .= "" . $value . ",";
            }
        }

        $TemplateMessagePersonalDetails = MessageTemplateModel::where('name', 'checkout_page')->first()->toArray();

        $opening_message = str_replace(
            ["<FULLNAME>", "<ENTER>", "<B>", "<INVOICENUMBER>"],
            [$FullName, "\n", "*", $request->session()->get('invoice', SalesOrderModel::newCode())],
            $TemplateMessagePersonalDetails['opening_message']
        );

        $baseUrl = "https://www.google.com/maps?q=";
        $mapsUrl = $baseUrl .  $latitude . "," . $longitude;

        $content_message = "";
        $no = 1;

        $content_message .= "*DETAIL PEMBELI*";
        $content_message .= "\r\n";
        $content_message .= "```> Nama : " . $FullName . "\r\n```";
        $content_message .= "```> Telp : 62" . $ContactNumber . "\r\n```";
        $content_message .= "```> Almt : " . $request->input('AddressCustomer') . "\r\n```";
        $content_message .= "```> Mobl : " . $arrayVehicle . "\r\n```";
        $content_message .= "```> Maps : " . $mapsUrl  . "\r\n\n```";

        foreach ($Battery as $item) {
            $item['price'] = str_replace(".", "", $item['price']);
            $content_message .= "Item " . $no++ . "\r\n";
            $content_message .= "*Nama*       : " . $item['batteryName'] . "\r\n";
            $content_message .= "*Kuantitas* : " . $item['quantity'] . "\r\n";
            $content_message .= "*Harga*      : Rp. " . number_format($item['price'], 0, "", ".") . "\r\n\r\n";
        }

        $content_message .= "*PERHITUNGAN TOTAL* \r\n";
        $content_message .= "```> Subtotal : Rp. " . number_format($Subtotal, 0, "", ".") . "\r\n```";
        if ($typeDiscount == 'rupiah') {
            $content_message .= "```> Disc     : Rp. " . number_format($Discount, 0, "", ".") . "\r\n```";
        } else {
            $content_message .= "```> Disc     : " . number_format($Discount, 0, "", ".") . "%\r\n```";
        }
        // $content_message .= "```> Tax      : " . number_format($Tax, 0, "", ".") . "%\r\n```";
        $content_message .= "```> Total    : Rp. " . number_format($TotalAmount, 0, "", ".") . "\r\n```";
        $content_message .= "> _Biaya instalasi sudah termasuk dalam perhitungan total_\r\n\n";

        $content_message .= "*TOTAL : Rp. " . number_format($TotalAmount, 0, "", ".") . "*\r\n";

        $closing_message = str_replace(
            ["<ENTER>", "<B>", "<I>"],
            ["\n", "*", "_"],
            $TemplateMessagePersonalDetails['closing_message']
        );

        $message  = $opening_message . "\n" . $content_message . "\n" . $closing_message;

        $data = [
            'to' => "62" . $ContactNumber,
            'session' => "admin_ams",
            'text' => "$message",
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

    public static function sharePaymentDetails(Request $request)
    {
        $url = "https://whatsapp.akikita.id/send-message";
        $FullName = $request->input('FullName');
        $Battery = $request->input('Battery');
        $IsMidtrans = $request->input('IsMidtrans');
        $InvoiceNumber = $request->input('InvoiceNumber');
        $PaymentLinks = $request->input('links');
        $latitude = $request->input('Latitude');
        $longitude = $request->input('Longitude');
        $ContactNumber = $request->input('ContactNumber');
        $VehicleCustomer = VehicleModel::whereIn('id', $request->input('VehicleCustomer'))->pluck('name')->toArray();
        $Tax = $request->input('Tax') ?? 0;
        $PaymentMethod = $request->input('PaymentMethod');
        $PaymentMethodData = PaymentMethodModel::where('id', $PaymentMethod)->first()->toArray();
        $arrayVehicle = "";
        $type = $request->input('type');
        foreach ($VehicleCustomer as $key => $value) {
            if ($key == count($VehicleCustomer) - 1) {
                $arrayVehicle .= "" . $value;
            } else {
                $arrayVehicle .= "" . $value . ",";
            }
        }
        $Subtotal = $request->input('Subtotal');
        $Discount = $request->input('Discount');
        $TotalAmount = $request->input('TotalAmount');
        $typeDiscount = $request->input('typeDiscount');

        $TemplateMessagePersonalDetails = MessageTemplateModel::where('name', 'payment_details')->first()->toArray();

        $opening_message = str_replace(
            ["<FULLNAME>", "<ENTER>", "<B>"],
            [$FullName, "\n", "*"],
            $TemplateMessagePersonalDetails['opening_message']
        );

        $closing_message = str_replace(
            ["<ENTER>", "<B>"],
            ["\n", "*"],
            $TemplateMessagePersonalDetails['closing_message']
        );

        $baseUrl = "https://www.google.com/maps?q=";
        $mapsUrl = $baseUrl .  $latitude . "," . $longitude;
        $no = 1;
        $content_message = "";
        $content_message .= "*DETAIL PEMBELI*";
        $content_message .= "\r\n";
        $content_message .= "```> Nama : " . $FullName . "\r\n```";
        $content_message .= "```> Telp : 62" . $ContactNumber . "\r\n```";
        $content_message .= "```> Almt : " . $request->input('AddressCustomer') . "\r\n```";
        $content_message .= "```> Mobl : " . $arrayVehicle . "\r\n```";
        $content_message .= "```> Maps : " . $mapsUrl  . "\r\n\n```";

        foreach ($Battery as $item) {
            $item['price'] = str_replace(".", "", $item['price']);
            $content_message .= "Item " . $no++ . "\r\n";
            $content_message .= "*Nama*       : " . $item['batteryName'] . "\r\n";
            $content_message .= "*Kuantitas* : " . $item['quantity'] . "\r\n";
            $content_message .= "*Harga*      : Rp. " . number_format($item['price'], 0, "", ".") . "\r\n\r\n";
        }

        $content_message .= "*PERHITUNGAN TOTAL* \r\n";
        $content_message .= "```> Subtotal : Rp. " . number_format($Subtotal, 0, "", ".") . "\r\n```";
        if ($typeDiscount == 'rupiah') {
            $content_message .= "```> Disc     : Rp. " . number_format($Discount, 0, "", ".") . "\r\n```";
        } else {
            $content_message .= "```> Disc     : " . number_format($Discount, 0, "", ".") . "%\r\n```";
        }
        // $content_message .= "```> Tax      : " . number_format($Tax, 0, "", ".") . "%```\r\n";
        $content_message .= "```> Total    : Rp. " . number_format($TotalAmount, 0, "", ".") . "\r\n```";
        $content_message .= "> _Biaya instalasi sudah termasuk dalam perhitungan total_\r\n\n";

        $content_message .= "*TOTAL : Rp. " . number_format($TotalAmount, 0, "", ".") . "*\r\n";

        if ($PaymentMethodData['id'] == 1) {
            if ($type = "mobile") {
                $content_message .= "Invoice Number : *" . $InvoiceNumber . "*\r\n";
                $content_message .= "Metode Pembayaran : *" . $PaymentMethodData['name'] . "*\r\n";
            } else {
                $content_message .= "Invoice Number : *" . $InvoiceNumber . "*\r\n";
                $content_message .= "Metode Pembayaran : *Midtrans*\r\n";
                $content_message .= "Silakan klik link berikut untuk melakukan pembayaran:\r\n";
                foreach ($PaymentLinks as $link) {
                    $content_message .= "*$link*\r\n";
                }
            }
        } else {
            // $content_message = "";
            $content_message .= "Invoice Number : *" . $InvoiceNumber . "*\r\n";
            $content_message .= "Metode Pembayaran : *" . $PaymentMethodData['name'] . "*\r\n";
            // foreach ($Battery as $index => $item) {
            //     $content_message .= "🔋 Battery " . ($index + 1) . "\r\n";
            //     $content_message .= "*Nama* : " . $item['batteryName'] . "\r\n";
            //     $content_message .= "*Link Pembayaran* : " . $PaymentLinks[$index] . "\r\n\r\n";
            // }
        }

        $message  = $opening_message . "\n" . $content_message . "\n" . $closing_message;

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

    public static function saveData(Request $request)
    {
        try {
            DB::beginTransaction();
            $tax = $request->input('tax') ?? 0;
            $Discount = $request->input('Discount') ?? 0;
            $ExtraDiscount = $request->input('ExtraDiscount') ?? 0;
            $subtotal = $request->input('subtotal');
            $tax_price = ($tax * $subtotal) / 100;
            $discount_price = ($Discount * $subtotal) / 100;
            $total = $request->input('TotalAmount');
            $status = "Pending";
            $DiscountRupiah = $request->input('DiscountRupiah');
            $DiscountPercentage = $request->input('DiscountPercentage');
            $PaymentMethod = $request->input('PaymentMethod');
            $PaymentMethodData = PaymentMethodModel::where('id', $PaymentMethod)->first()->toArray();
            $VehicleCustomer = $request->input('VehicleCustomer');
            $MarketplaceInvoice = $request->input('MarketplaceInvoice') ?? null;
            $batteryType = $request->input('BatteryType');

            if ($PaymentMethodData['id'] == 1) {
                $payment_methode = "midtrans";
                $midtransInvoice = $request->session()->get('invoiceNumberMidtrans');
                $midtransPaymentLink = $request->input('linkMidtrans');
            } else {
                $payment_methode = $PaymentMethodData['name'];
            }


            if ($request->input('IdCustomer') != null or $request->input('IdCustomer') != '') {
                $Customer = CustomerModel::find($request->input('IdCustomer'));
                $Customer->vehicles()->sync($request->input('VehicleCustomer'));
                $tes = "YA";
            } else {
                $Customer = CustomerModel::firstOrCreate(
                    ['contact' => $request->input('contact')],
                    [
                        'name' => $request->input('FullName'),
                        'address' => $request->input('AddressCustomer') ?? 'unknown address',
                        'contact' => $request->input('ContactNumber'),
                        'latitude' => $request->input('Latitude') ?? 0,
                        'longitude' => $request->input('Longitude') ?? 0,
                    ]
                );
                $tes = "TIDAK";
                $Customer->vehicles()->sync($request->input('VehicleCustomer'));
            }

            if ($request->input('DistributorShopId') != null) {
                $DistributorShop = DistributorShopModel::find($request->input('DistributorShopId'));
                $distributorTechnician = DistributorShopModel::find($request->input('DistributorShopId'))->technicians()->get()->toArray();
            } else {
                $DistributorShop = null;
            }

            $data = [
                'sales_order_number' => $request->session()->get('invoice', SalesOrderModel::newCode()),
                'invoice_number' => $MarketplaceInvoice,
                'customer_id' => $Customer->id,
                'vehicle_id' => $VehicleCustomer[0],
                'distributor_shop_id' => $DistributorShop->id ?? null,
                'distributor_shop_technician_id' => $distributorTechnician[0]['id'] ?? null,
                'subtotal' => $subtotal,
                'total' => $total,
                'discount' => $DiscountPercentage ?? 0,
                'discount_price' => $DiscountRupiah ?? 0,
                'payment_method_id' => $PaymentMethodData['id'],
                'midtrans_invoice_number' => $midtransInvoice ?? null,
                'midtrans_payment_link' => $midtransPaymentLink ?? null,
                'payment_status' => "pending",
                'status' => "draft",
                'address' => $request->input('AddressCustomer') ?? 'unknown address',
                'alternative_address' => $request->input('alternative_address'),
                'latitude' => $request->input('Latitude') ?? 0,
                'longitude' => $request->input('Longitude') ?? 0,
                'date' => date('Y-m-d')
            ];

            $Quotation = SalesOrderModel::create($data);

            // check if battery recycle is exist in batteryType
            if (in_array('recycle', $batteryType)) {
                $subtotalRecycle = 0;
                $totalRecycle = 0;
                $discountRecycle = 0;
                foreach ($request->input('BatteryNameTabel') as $key => $value) {
                    if ($batteryType[$key] == 'recycle') {
                        $subtotalRecycle += str_replace(".", "", $request->input('SubtotalPayment')[$key]);
                        if ($request->input('DiscountPayment')[$key] != 0) {
                            $GrossPrice = str_replace(".", "", $request->input('GrossPricePayment')[$key]);
                            $DiscountPrice = str_replace(".", "", $request->input('DiscountPayment')[$key]);
                            $discountPercent = $GrossPrice != 0 ? ($DiscountPrice / $GrossPrice) * 100 : 0;
                            $discountRecycle += $discountPercent;
                        }
                    }

                    $totalRecycle = $subtotalRecycle - $discountRecycle;
                }

                $purchaseOrder = PurchaseOrderModel::create([
                    'purchase_order_number' => PurchaseOrderModel::generatePurchaseOrderNumber(),
                    'date' => date('Y-m-d'),
                    'vendor_id' => $Customer->id,
                    'vendor_type' => CustomerModel::class,
                    'ship_to_id' => $DistributorShop->id,
                    'ship_to_type' => DistributorShopModel::class,
                    'address' => $request->input('AddressCustomer') ?? 'unknown address',
                    'latitude' => $request->input('Latitude') ?? 0,
                    'longitude' => $request->input('Longitude') ?? 0,
                    'discount_price' => $discountRecycle,
                    'subtotal' => $subtotalRecycle,
                    'total' => $totalRecycle,
                    'payment_status' => $request->input('status') ?? 'pending',
                    'status' => 'draft',
                    'invoice_number' => $Quotation->sales_order_number,
                    'type' => 'recycle'
                ]);

                $billing = BillingModel::create([
                    'billing_number' => BillingModel::generateSalesBillingNumber(),
                    'vendor_id' => $DistributorShop->id,
                    'vendor_type' => DistributorShopModel::class,
                    'ship_to_id' => $Customer->id,
                    'ship_to_type' => CustomerModel::class,
                    'date' => date('Y-m-d'),
                    'discount' => $DiscountPercentage ?? 0,
                    'discount_price' => $DiscountRupiah ?? 0,
                    'subtotal' => $subtotal,
                    'total' => $total,
                    'status' => $request->input('status', 'draft'),
                ]);

                if ($billing ?? false) {
                    BillingInvoiceModel::create([
                        'billing_id' => $billing->id,
                        'invoice_id' => $Quotation->id,
                        'invoice_type' => SalesOrderModel::class,
                        'invoice_number' => $Quotation->sales_order_number,
                        'date' => date('Y-m-d'),
                        'discount' => $DiscountPercentage ?? 0,
                        'discount_price' => $DiscountRupiah ?? 0,
                        'subtotal' => $subtotal,
                        'total' => $total,
                        'note' => 'Battery Regular From Sales Order ' . $Quotation->sales_order_number,
                    ]);

                    BillingInvoiceModel::create([
                        'billing_id' => $billing->id,
                        'invoice_id' => $purchaseOrder->id,
                        'invoice_type' => PurchaseOrderModel::class,
                        'invoice_number' => $purchaseOrder->purchase_order_number,
                        'date' => date('Y-m-d'),
                        'discount' => 0,
                        'discount_price' => $discountRecycle,
                        'subtotal' => $subtotalRecycle,
                        'total' => $totalRecycle,
                        'note' => 'Battery Recycle From Purchase Order ' . $purchaseOrder->purchase_order_number,
                    ]);
                }
            } else {
                $billing = BillingModel::create([
                    'billing_number' => BillingModel::generateSalesBillingNumber(),
                    'vendor_id' => $DistributorShop->id,
                    'vendor_type' => DistributorShopModel::class,
                    'ship_to_id' => $Customer->id,
                    'ship_to_type' => CustomerModel::class,
                    'date' => date('Y-m-d'),
                    'discount' => $DiscountPercentage ?? 0,
                    'discount_price' => $DiscountRupiah ?? 0,
                    'subtotal' => $subtotal,
                    'total' => $total,
                    'status' => $request->input('status', 'draft'),
                ]);

                if ($billing ?? false) {
                    BillingInvoiceModel::create([
                        'billing_id' => $billing->id,
                        'invoice_id' => $Quotation->id,
                        'invoice_type' => SalesOrderModel::class,
                        'invoice_number' => $Quotation->sales_order_number,
                        'date' => date('Y-m-d'),
                        'discount' => $DiscountPercentage ?? 0,
                        'discount_price' => $DiscountRupiah ?? 0,
                        'subtotal' => $subtotal,
                        'total' => $total,
                        'note' => 'Battery Regular From Sales Order ' . $Quotation->sales_order_number,
                    ]);
                }
            }

            $dataProduct = [];
            $dataProductRecycle = [];
            $totalPriceNetAll = 0;
            $totalPriceNetRecycle = 0;
            $totalPriceNetRegular = 0;
            foreach ($request->input('BatteryNameTabel') as $key => $value) {
                for ($i = 0; $i < $request->input('QtyTabel')[$key]; $i++) {
                    if ($batteryType[$key] == 'regular') {
                        if ($request->input('DiscountPayment')[$key] != 0) {
                            $TaxPayment = $request->input('TaxPayment')[$key] ?? 0;
                            $GrossPrice = str_replace(".", "", $request->input('GrossPricePayment')[$key]);
                            $DiscountPrice = str_replace(".", "", $request->input('DiscountPayment')[$key]);
                            $PriceNet = str_replace(".", "", $request->input('NetPricePayment')[$key]);
                            $Subtotal = str_replace(".", "", $request->input('SubtotalPayment')[$key]);
                            $TaxPrice = $GrossPrice * $TaxPayment / 100;
                            $DiscountPercent = $GrossPrice != 0 ? ($DiscountPrice / $GrossPrice) * 100 : 0;
                        } else {
                            $TaxPayment = $request->input('TaxPayment')[$key] ?? 0;
                            $GrossPrice = str_replace(".", "", $request->input('GrossPricePayment')[$key]);
                            $DiscountPrice = str_replace(".", "", $request->input('DiscountPayment')[$key]);
                            $PriceNet = str_replace(".", "", $request->input('NetPricePayment')[$key]);
                            $Subtotal = str_replace(".", "", $request->input('SubtotalPayment')[$key]);
                            $TaxPrice = $GrossPrice * $TaxPayment / 100;
                            $DiscountPercent = $GrossPrice != 0 ? ($DiscountPrice / $GrossPrice) * 100 : 0;
                        }
                        $dataProduct[] = [
                            'sales_order_id' => $Quotation->id,
                            'battery_id' => $request->input('BatteryIdCheckout')[$key],
                            'battery_name' => $value,
                            'battery_price_retail' => $GrossPrice,
                            'discount' => $DiscountPercent,
                            'discount_price' => $DiscountPrice,
                            'tax' => $request->input('TaxPayment')[$key],
                            'tax_price' =>  $TaxPrice,
                            'price_net' =>  str_replace(".", "", $PriceNet),
                            'quantity' => 1,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ];

                        $totalPriceNetRegular += str_replace(".", "", $request->input('NetPricePayment')[$key]);
                    } else {
                        if ($request->input('DiscountPayment')[$key] != 0) {
                            $TaxPayment = $request->input('TaxPayment')[$key] ?? 0;
                            $GrossPrice = str_replace(".", "", $request->input('GrossPricePayment')[$key]);
                            $DiscountPrice = str_replace(".", "", $request->input('DiscountPayment')[$key]);
                            $PriceNet = str_replace(".", "", $request->input('NetPricePayment')[$key]);
                            $Subtotal = str_replace(".", "", $request->input('SubtotalPayment')[$key]);
                            $TaxPrice = $GrossPrice * $TaxPayment / 100;
                            $DiscountPercent = $GrossPrice != 0 ? ($DiscountPrice / $GrossPrice) * 100 : 0;
                        } else {
                            $TaxPayment = $request->input('TaxPayment')[$key] ?? 0;
                            $GrossPrice = str_replace(".", "", $request->input('GrossPricePayment')[$key]);
                            $DiscountPrice = str_replace(".", "", $request->input('DiscountPayment')[$key]);
                            $PriceNet = str_replace(".", "", $request->input('NetPricePayment')[$key]);
                            $Subtotal = str_replace(".", "", $request->input('SubtotalPayment')[$key]);
                            $TaxPrice = $GrossPrice * $TaxPayment / 100;
                            $DiscountPercent = $GrossPrice != 0 ? ($DiscountPrice / $GrossPrice) * 100 : 0;
                        }
                        $dataProductRecycle[] = [
                            'sales_order_id' => $Quotation->id,
                            'battery_id' => $request->input('BatteryIdCheckout')[$key],
                            'battery_name' => $value,
                            'battery_price_retail' => $GrossPrice,
                            'discount' => $DiscountPercent,
                            'discount_price' => $DiscountPrice,
                            'tax' => $request->input('TaxPayment')[$key],
                            'tax_price' =>  $TaxPrice,
                            'price_net' =>  str_replace(".", "", $PriceNet),
                            'quantity' => 1,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ];

                        PurchaseOrderBatteryModel::create([
                            'purchase_order_id' => $purchaseOrder->id,
                            'sales_order_battery_id' => NULL,
                            'source' => 'recycle',
                            'battery_id' => $request->input('BatteryIdCheckout')[$key],
                            'battery_name' => $value,
                            'battery_price_retail' => $GrossPrice,
                            'tax' => $request->input('TaxPayment')[$key],
                            'tax_price' =>  $TaxPrice,
                            'discount' => $request->input('DiscountPayment')[$key],
                            'discount_price' => $DiscountPrice,
                            'price_net' =>  str_replace(".", "", $PriceNet),
                            'quantity' => 1,
                            'battery_production_code' => NULL
                        ]);

                        $totalPriceNetRecycle += str_replace(".", "", $request->input('NetPricePayment')[$key]);
                    }
                }
                $totalPriceNetAll += str_replace(".", "", $request->input('NetPricePayment')[$key]) * $request->input('QtyTabel')[$key];
            }

            // update total price net in sales order
            $Quotation->total = $totalPriceNetRegular;
            $Quotation->save();

            // update total price net in billing invoice
            $billing = BillingInvoiceModel::where('invoice_id', $Quotation->id)
                ->where('invoice_type', SalesOrderModel::class)
                ->first();
            if ($billing) {
                $billing->subtotal = $totalPriceNetRegular;
                $billing->total = $totalPriceNetRegular;
                $billing->save();
            }


            // Save Sales Order Expense
            // ExpenseIds, ExpenseAmounts
            if ($request->input('ExpenseIds') && $request->input('ExpenseAmounts')) {
                $ExpenseIds = $request->input('ExpenseIds');
                $ExpenseAmounts = $request->input('ExpenseAmounts');
                foreach ($ExpenseIds as $index => $ExpenseId) {
                    SalesOrderExpenseModel::create([
                        'sales_order_id' => $Quotation->id,
                        'expense_id' => $ExpenseId,
                        'amount' => str_replace(".", "", $ExpenseAmounts[$index]),
                    ]);

                    BillingInvoiceExpenseModel::create([
                        'billing_invoice_id' => $billing->id,
                        'sales_order_id' => $Quotation->id,
                        'debit_account_id' => ExpenseModel::find($ExpenseId)->chart_of_account_id,
                        'credit_account_id' => NULL,
                        'description' => ExpenseModel::find($ExpenseId)->name,
                        'amount' => str_replace(".", "", $ExpenseAmounts[$index]),
                    ]);
                }
            }


            // sum price_net and sum discount_price
            $QuuotationBattery = SalesOrderBatteryModel::insert($dataProduct);
            if (!$QuuotationBattery) {
                DB::rollBack();
                return getResponseData(false, "Failed to save data");
            } else {
                DB::commit();
                return getResponseData(true, "Data saved successfully");
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th);
            return getResponseData(false, "Failed to save data => " . $th->getMessage());
        }
    }

    public function getCustomerCopyDetail(Request $request)
    {
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
$arrayVehicle
";

        $closing_message = str_replace(
            ["<ENTER>", "<B>"],
            ["\n", "*"],
            $TemplateMessagePersonalDetails['closing_message']
        );

        $message  = $opening_message . "\n" . $content_message . "\n" . $closing_message;

        return getResponseData(true, $message);
    }

    public function getCheckoutCopyDetail(Request $request)
    {
        $FullName = $request->input('FullName');
        $ContactNumber = $request->input('ContactNumber');
        $Battery = $request->input('Battery');
        $Subtotal = $request->input('Subtotal');
        $Tax = $request->input('Tax');
        $Discount = $request->input('Discount');
        $TotalAmount = $request->input('TotalAmount');
        $VehicleCustomer = VehicleModel::whereIn('id', $request->input('VehicleCustomer'))->pluck('name')->toArray();
        $latitude = $request->input('Latitude');
        $longitude = $request->input('Longitude');
        $typeDiscount = $request->input('typeDiscount');

        $arrayVehicle = "";
        foreach ($VehicleCustomer as $key => $value) {
            if ($key == count($VehicleCustomer) - 1) {
                $arrayVehicle .= "" . $value;
            } else {
                $arrayVehicle .= "" . $value . ",";
            }
        }

        $TemplateMessagePersonalDetails = MessageTemplateModel::where('name', 'checkout_page')->first()->toArray();

        $opening_message = str_replace(
            ["<FULLNAME>", "<ENTER>", "<B>", "<INVOICENUMBER>"],
            [$FullName, "\n", "*", $request->session()->get('invoice', SalesOrderModel::newCode())],
            $TemplateMessagePersonalDetails['opening_message']
        );

        $baseUrl = "https://www.google.com/maps?q=";
        $mapsUrl = $baseUrl .  $latitude . "," . $longitude;

        $content_message = "";
        $no = 1;

        $content_message .= "*DETAIL PEMBELI*";
        $content_message .= "\r";
        $content_message .= "```> Nama : " . $FullName . "\r```";
        $content_message .= "```> Telp : 62" . $ContactNumber . "\r```";
        $content_message .= "```> Almt : " . $request->input('AddressCustomer') . "\r```";
        $content_message .= "```> Mobl : " . $arrayVehicle . "\r```";
        $content_message .= "```> Maps : " . $mapsUrl  . "\r\n\n```";

        foreach ($Battery as $item) {
            $item['price'] = str_replace(".", "", $item['price']);
            $content_message .= "Item " . $no++ . "\r\n";
            $content_message .= "*Nama* : " . $item['batteryName'] . "\r\n";
            $content_message .= "*Kuantitas* : " . $item['quantity'] . "\r\n";
            $content_message .= "*Harga* : Rp. " . number_format($item['price'], 0, "", ".") . "\r\n\r\n";
        }

        $content_message .= "*PERHITUNGAN TOTAL* \r";
        $content_message .= "```> Subtotal : Rp. " . number_format($Subtotal, 0, "", ".") . "\r\n```";
        if ($typeDiscount == 'rupiah') {
            $content_message .= "```> Disc     : Rp. " . number_format($Discount, 0, "", ".") . "\r\n```";
        } else {
            $content_message .= "```> Disc     : " . number_format($Discount, 0, "", ".") . "%\r\n```";
        }
        // $content_message .= "```> Tax      : " . number_format($Tax, 0, "", ".") . "%\r\n";
        $content_message .= "```> Total    : Rp. " . number_format($TotalAmount, 0, "", ".") . "\r\n```";
        $content_message .= "> _Biaya instalasi sudah termasuk dalam perhitungan total_\r\n\n";

        $content_message .= "*TOTAL : Rp. " . number_format($TotalAmount, 0, "", ".") . "*\r\n";

        $closing_message = str_replace(
            ["<ENTER>", "<B>", "<I>"],
            ["\n", "*", "_"],
            $TemplateMessagePersonalDetails['closing_message']
        );

        $message  = $opening_message . "\n" . $content_message . "\n" . $closing_message;

        return getResponseData(true, $message);
    }

    public function getPaymentDetailsCopyDetail(Request $request)
    {
        $FullName = $request->input('FullName');
        $Battery = $request->input('Battery');
        $IsMidtrans = $request->input('IsMidtrans');
        $InvoiceNumber = $request->input('InvoiceNumber');
        $PaymentLinks = $request->input('links');
        $latitude = $request->input('Latitude');
        $longitude = $request->input('Longitude');
        $ContactNumber = $request->input('ContactNumber');
        $VehicleCustomer = VehicleModel::whereIn('id', $request->input('VehicleCustomer'))->pluck('name')->toArray();
        $Tax = $request->input('Tax') ?? 0;
        $PaymentMethod = $request->input('PaymentMethod');
        $PaymentMethodData = PaymentMethodModel::where('id', $PaymentMethod)->first()->toArray();
        $arrayVehicle = "";
        $tipeDevice = $request->input('type');
        foreach ($VehicleCustomer as $key => $value) {
            if ($key == count($VehicleCustomer) - 1) {
                $arrayVehicle .= "" . $value;
            } else {
                $arrayVehicle .= "" . $value . ",";
            }
        }
        $Subtotal = $request->input('Subtotal');
        $Discount = $request->input('Discount');
        $TotalAmount = $request->input('TotalAmount');
        $typeDiscount = $request->input('typeDiscount');

        $TemplateMessagePersonalDetails = MessageTemplateModel::where('name', 'payment_details')->first()->toArray();

        $opening_message = str_replace(
            ["<FULLNAME>", "<ENTER>", "<B>"],
            [$FullName, "\n", "*"],
            $TemplateMessagePersonalDetails['opening_message']
        );

        $closing_message = str_replace(
            ["<ENTER>", "<B>"],
            ["\n", "*"],
            $TemplateMessagePersonalDetails['closing_message']
        );

        $baseUrl = "https://www.google.com/maps?q=";
        $mapsUrl = $baseUrl .  $latitude . "," . $longitude;
        $no = 1;
        $content_message = "";
        $content_message .= "*DETAIL PEMBELI*";
        $content_message .= "\r\n";
        $content_message .= "```> Nama : " . $FullName . "\r\n```";
        $content_message .= "```> Telp : 62" . $ContactNumber . "\r\n```";
        $content_message .= "```> Almt : " . $request->input('AddressCustomer') . "\r\n```";
        $content_message .= "```> Mobl : " . $arrayVehicle . "\r\n```";
        $content_message .= "```> Maps : " . $mapsUrl  . "\r\n\n```";

        foreach ($Battery as $item) {
            $item['price'] = str_replace(".", "", $item['price']);
            $content_message .= "Item " . $no++ . "\r\n";
            $content_message .= "*Nama*       : " . $item['batteryName'] . "\r\n";
            $content_message .= "*Kuantitas* : " . $item['quantity'] . "\r\n";
            $content_message .= "*Harga*      : Rp. " . number_format($item['price'], 0, "", ".") . "\r\n\r\n";
        }

        $content_message .= "*PERHITUNGAN TOTAL* \r\n";
        $content_message .= "```> Subtotal : Rp. " . number_format($Subtotal, 0, "", ".") . "\r\n```";
        if ($typeDiscount == 'rupiah') {
            $content_message .= "```> Disc     : Rp. " . number_format($Discount, 0, "", ".") . "\r\n```";
        } else {
            $content_message .= "```> Disc     : " . number_format($Discount, 0, "", ".") . "%\r\n```";
        }
        // $content_message .= "```> Tax      : " . number_format($Tax, 0, "", ".") . "%```\r\n";
        $content_message .= "```> Total    : Rp. " . number_format($TotalAmount, 0, "", ".") . "\r\n```";
        $content_message .= "> _Biaya instalasi sudah termasuk dalam perhitungan total_\r\n\n";

        $content_message .= "*TOTAL : Rp. " . number_format($TotalAmount, 0, "", ".") . "*\r\n";

        if ($PaymentMethodData['id'] == 1) {
            if ($tipeDevice = "mobile") {
                $content_message .= "Invoice Number : *" . $InvoiceNumber . "*\r\n";
                $content_message .= "Metode Pembayaran : *" . $PaymentMethodData['name'] . "*\r\n";
            } else {
                $content_message .= "Invoice Number : *" . $InvoiceNumber . "*\r\n";
                $content_message .= "Metode Pembayaran : *Midtrans*\r\n";
                $content_message .= "Silakan klik link berikut untuk melakukan pembayaran:\r\n";
                foreach ($PaymentLinks as $link) {
                    $content_message .= "*$link*\r\n";
                }
            }
        } else {
            // $content_message = "";
            $content_message .= "Invoice Number : *" . $InvoiceNumber . "*\r\n";
            $content_message .= "Metode Pembayaran : *" . $PaymentMethodData['name'] . "*\r\n";
            // foreach ($Battery as $index => $item) {
            //     $content_message .= "🔋 Battery " . ($index + 1) . "\r\n";
            //     $content_message .= "*Nama* : " . $item['batteryName'] . "\r\n";
            //     $content_message .= "*Link Pembayaran* : " . $PaymentLinks[$index] . "\r\n\r\n";
            // }
        }

        $message  = $opening_message . "\n" . $content_message . "\n" . $closing_message;

        return getResponseData(true, $message);
    }

    public function findCustomerByContact(Request $request)
    {
        $query = $request->input('input');
        $results = CustomerModel::where('contact', 'like', '%' . $query . '%')->orderBy('name', 'asc')->limit(10)->get();
        return response()->json($results);
    }

    public function findBattery(Request $request)
    {
        $query = $request->input('input');
        $results = BatteryModel::whereIn('batteries.id', $request->input('Battery'))
            ->leftJoin('battery_size_categories', 'batteries.size_category_id', '=', 'battery_size_categories.id')
            ->join('battery_prices', 'battery_prices.battery_id', '=', 'batteries.id', 'left')
            ->orderBy('batteries.name', 'asc')
            ->select('batteries.*', 'battery_size_categories.name as size_category', 'battery_prices.discount', 'battery_prices.price_net', 'battery_prices.price_retail as price_retail_original', 'battery_prices.discount', 'battery_prices.discount_price')
            ->limit(10)
            ->get();
        $tax = TaxModel::where('status', '1')->first();
        if ($tax && $results) {
            foreach ($results as $result) {
                $result->tax = $tax->percentage;
            }
        }
        return response()->json($results);
    }

    public function getLinkBattery(Request $request)
    {
        $id = $request->input('id');
        if (!$id) {
            return response()->json(['error' => 'ID is required'], 400);
        }
        $results = BatteryUrlModel::where('battery_id', $id)->get();
        if ($results->isEmpty()) {
            return response()->json(['error' => 'No data found'], 404);
        }
        return response()->json($results);
    }

    public function findDistributor(Request $request)
    {
        $query = $request->input('input');
        $results = DistributorShopModel::where('status', 1)->get();
        return response()->json($results);
    }

    public function autoCompleteBattery(Request $request)
    {
        $query = $request->input('query');
        $type = $request->input('typeBattery');
        if ($type && $type == 'regular') {
            $results = BatteryModel::where('batteries.name', 'like', '%' . $query . '%')
                ->where('batteries.type', 'regular')
                ->leftJoin('battery_prices', 'battery_prices.battery_id', '=', 'batteries.id')
                ->orderBy('batteries.name', 'asc')
                ->select('batteries.*', 'battery_prices.discount', 'battery_prices.price_net', 'battery_prices.price_retail as price_retail_original')
                ->limit(10)
                ->get();
            $tax = TaxModel::where('status', '1')->first();
            if ($tax && $results) {
                foreach ($results as $result) {
                    $result->tax = $tax->percentage;
                }
            }
        } else {
            // Get battery data for autocomplete (non-regular type)
            $results = BatteryRecycleModel::where('battery_recycles.name', 'like', '%' . $query . '%')
                ->orderBy('battery_recycles.name', 'asc')
                ->select('battery_recycles.*', DB::raw('0 as discount'), 'battery_recycles.price as price_net', 'battery_recycles.price as price_retail_original', DB::raw('"recycle" as type'))
                ->limit(10)
                ->get();

            $tax = TaxModel::where('status', '1')->first();
            foreach ($results as $result) {
                $result->tax = $tax ? $tax->percentage : 0;
                $result->price_retail = $result->price_retail_original ?? 0;
                $result->discount = 0;
                $result->price_net = $result->price_retail_original ?? 0;
                $result->price_tax = $result->price_net + ($result->price_net * $result->tax / 100);
                $result->net_price = $result->price_tax - ($result->price_tax * $result->discount / 100);
                $result->editable_price = $result->editable_price ?? 0;
            }
        }

        return response()->json($results);
    }

    public function screenshotBattery(Request $request)
    {
        $Battery = BatteryModel::getBatteryData($request->input('Battery'))->toArray();
        $Tax = TaxModel::where('status', '1')->first()->percentage;

        $batteries = [
            'categories' => [],
            'units' => ['NAMA UNIT'],
            'images' => ['GAMBAR UNIT'],
            'technologies' => ['TEKNOLOGI AKI'],
            'dimensions' => ['DIMENSI (mm)'],
            'capacities' => ['KAPASITAS'],
            'cca' => ['STANDAR CCA'],
            'warranties' => ['GARANSI'],
            'prices' => ['HARGA'],
        ];

        foreach ($Battery as $key) {
            $batteryPrices = $key['battery_prices'];
            $batteries['categories'][] = $key['brand']['name'] . ' ' . $key['subbrand_category']['name'];
            if (preg_match('/[0-9A-Z]+[0-9A-Z]$/', $key['name'], $matches)) {
                $batteries['units'][] = $matches[0];
            } else {
                $batteries['units'][] = $key['name'];
            }
            if ($key['image'] != null) {
                $value['image'] = asset('storage/image/battery/compressed/' . $key['image']);
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
            $batteries['images'][] = $value['image'];
            $batteries['technologies'][] = $key['technology']['name'];
            $batteries['dimensions'][] = $key['dimension_length'] . ' x ' . $key['dimension_width'] . ' x ' . $key['dimension_height'];
            $batteries['capacities'][] = $key['capacity'] . " AH";
            $batteries['cca'][] = $key['standard_cca'] . " A";
            $batteries['warranties'][] = $key['warranty'] . ' bulan';
            $pricePPn = $batteryPrices[0]['price_retail'] + ($batteryPrices[0]['price_retail'] * $Tax / 100);
            $priceDiscount = $batteryPrices[0]['discount_price'];
            $priceNetto = $batteryPrices[0]['price_net'];
            $batteries['prices'][] = [
                'original' =>  "Rp. " . number_format($pricePPn, 0, "", "."),
                'discount' => number_format($batteryPrices[0]['discount'], 0, "", ".") . "",
                'netto' => "Rp. " . number_format($priceNetto, 0, "", "."),
                'price_discount' => "Rp. " . number_format($priceDiscount, 0, "", "."),
            ];
        }

        // load view
        return view('Orders.QuickQuotation.Screenshoot.battery', compact('batteries'));
    }

    public function saveScreenshoot(Request $request)
    {
        $image = $request->file('image');
        $random_id = $request->input('random_id');

        // check if image is exist
        try {
            if ($request->hasFile('image')) {
                $request->validate([
                    'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                    'random_id' => 'required'
                ]);

                $imageExtension = $image->getClientOriginalExtension();
                $imageFileName = $random_id . '.' . $imageExtension;
                $imagePath = 'image/quick-quotation/screenshot/' . $imageFileName;
                $storedImagePath = $image->storeAs('public/' . dirname($imagePath), $imageFileName);
                return response()->json([
                    'success' => true,
                    'message' => 'Image uploaded successfully.',
                    'image_path' => Storage::url($imagePath)
                ]);
            } else {
                // change  data:image/png;base64 to data:image/png;base64,
                $image = str_replace('data:image/png;base64,', '', $image);
                $image = str_replace(' ', '+', $image);
                $imageName = $random_id . '.png';
                $imagePath = 'image/quick-quotation/screenshot/' . $imageName;
                $imageFullPath = storage_path('app/public/' . $imagePath);
                $imageData = base64_decode($image);
                file_put_contents($imageFullPath, $imageData);
                return response()->json([
                    'success' => true,
                    'message' => 'Image uploaded successfully.',
                    'image_path' => Storage::url($imagePath)
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload image. ' . $e->getMessage()
            ]);
        }
    }

    public function mobileCheckout(Request $request)
    {
        try {
            $data = array(
                'Fullname' => $request->input('FullName'),
                'ContactNumber' => $request->input('ContactNumber'),
                'AddressCustomer' => $request->input('AddressCustomer'),
                'AlternativeAddress' => $request->input('AddressCustomerAlternative'),
                'EmailCustomer' => $request->input('EmailCustomer'),
                'VehicleCustomer' => VehicleModel::whereIn('id', $request->input('VehicleCustomer'))->pluck('name')->toArray(),
                'Battery' => BatteryModel::getBatteryData($request->input('Battery'))->toArray(),
                'Ditributor' => DistributorShopModel::find($request->input('DistributorShopId'))->toArray(),
                'DistributorTechnician' => DistributorShopModel::find($request->input('DistributorShopId'))->technicians()->get()->toArray(),
                'Tax' => TaxModel::where('status', '1')->first()->percentage ?? 0,
            );

            // return as json
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get data. ' . $th->getMessage()
            ]);
        }
    }

    public function getBatteryDetail(Request $request)
    {
        try {
            $data = array($request->input('id'));
            $Battery = BatteryModel::getBatteryData($data)->toArray();
            $Tax = TaxModel::where('status', '1')->first()->percentage;

            return response()->json([
                'success' => true,
                'data' => $Battery,
                'tax' => $Tax
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get data. ' . $th->getMessage()
            ]);
        }
    }

    public function mobilePayment(Request $request)
    {
        try {
            $data = array(
                'FullName' => $request->input('FullName'),
                'Battery' => BatteryModel::getBatteryData($request->input('Battery'))->toArray(),
                'IsMidtrans' => $request->input('IsMidtrans'),
                'InvoiceNumber' => $request->input('InvoiceNumber'),
                'links' => $request->input('links'),
                'Latitude' => $request->input('Latitude'),
                'Longitude' => $request->input('Longitude'),
                'ContactNumber' => $request->input('ContactNumber'),
                'VehicleCustomer' => VehicleModel::whereIn('id', $request->input('VehicleCustomer'))->pluck('name')->toArray(),
                'Tax' => $request->input('Tax') ?? 0,
                'Subtotal' => $request->input('Subtotal'),
                'Discount' => $request->input('Discount'),
                'TotalAmount' => $request->input('TotalAmount'),
                'typeDiscount' => $request->input('typeDiscount'),
                'Qty' => $request->input('Qty'),
                'Price' => $request->input('Price'),
                'EmailCustomer' => $request->input('EmailCustomer'),
                'AddressCustomer' => $request->input('AddressCustomer'),
                'AddressCustomerAlternative' => $request->input('AddressCustomerAlternative'),
                'InvoiceNumber' => $request->session()->get('invoice', SalesOrderModel::newCode()),
                'PaymentMethod' => PaymentMethodModel::all()->toArray()
            );

            // return as json
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get data. ' . $th->getMessage()
            ]);
        }
    }

    public function saveDataMobile(Request $request)
    {
        try {
            DB::beginTransaction();

            $FullName = $request->input('FullName');
            $ContactNumber = $request->input('ContactNumber');
            $AddressCustomer = $request->input('AddressCustomer');
            $AddressCustomerAlternative = $request->input('AddressCustomerAlternative');
            $VehicleCustomer = $request->input('VehicleCustomer');
            $Latitude = $request->input('Latitude');
            $Longitude = $request->input('Longitude');
            $IdCustomer = $request->input('IdCustomer');
            $Battery = BatteryModel::getBatteryData($request->input('Battery'))->toArray();
            $TotalAmount = $request->input('TotalAmount');
            $Qty = $request->input('QtyTabel');
            $Price = $request->input('PriceTabel');
            $DistributorShop = DistributorShopModel::find($request->input('DistributorShopId'));
            $invoiceNumber = $request->input('invoiceNumber');
            $techniciansName = DistributorShopModel::find($request->input('DistributorShopId'))->technicians()->get()->toArray();
            $SUbtotal = $request->input('subtotal');
            $PaymentMethod = PaymentMethodModel::where('id', $request->input('PaymentMethod'))->first()->toArray();

            // check if customer is exist or not 
            if ($IdCustomer != null or $IdCustomer != '') {
                $Customer = CustomerModel::find($IdCustomer);
                $Customer->vehicles()->sync($request->input('VehicleCustomer'));
            } else {
                $Customer = CustomerModel::firstOrCreate(
                    ['contact' => $ContactNumber],
                    [
                        'name' => $FullName,
                        'address' => $AddressCustomer ?? 'unknown address',
                        'contact' => $ContactNumber,
                        'latitude' => $Latitude ?? 0,
                        'longitude' => $Longitude ?? 0,
                    ]
                );
                $Customer->vehicles()->sync($request->input('VehicleCustomer'));
            }

            // SAVE DATA TO SALES ORDER 
            $data = [
                'sales_order_number' => $invoiceNumber,
                'customer_id' => $Customer->id,
                'vehicle_id' => $VehicleCustomer[0],
                'distributor_shop_id' => $DistributorShop->id,
                'distributor_shop_technician_id' => $techniciansName[0]['id'] ?? null,
                'subtotal' => $SUbtotal,
                'total' => $TotalAmount,
                'discount' => $request->input('Discount') ?? 0,
                'discount_price' => $request->input('DiscountRupiah') ?? 0,
                'payment_method_id' => $PaymentMethod['id'],
                'midtrans_invoice_number' => null,
                'midtrans_payment_link' => null,
                'payment_status' => "pending",
                'status' => "draft",
                'address' => $AddressCustomer ?? 'unknown address',
                'alternative_address' => $AddressCustomerAlternative,
                'latitude' => $Latitude ?? 0,
                'longitude' => $Longitude ?? 0,
                'date' => date('Y-m-d')
            ];

            $Quotation = SalesOrderModel::create($data);

            // SAVE DATA TO SALES ORDER BATTERY
            $dataProduct = [];
            foreach ($Battery as $key => $value) {
                for ($i = 0; $i < $Qty[$key]; $i++) {
                    $dataProduct[] = [
                        'sales_order_id' => $Quotation->id,
                        'battery_id' => $value['id'],
                        'battery_name' => $value['name'],
                        'battery_price_retail' => $Price[$key],
                        'discount' => $value['discount'] ?? 0,
                        'discount_price' => $value['discount_price'] ?? 0,
                        'tax' => $value['tax'] ?? 0,
                        'tax_price' => $value['tax_price'] ?? 0,
                        'price_net' => $Price[$key],
                        'quantity' => 1,
                    ];
                }
            }

            $QuuotationBattery = SalesOrderBatteryModel::insert($dataProduct);
            if (!$QuuotationBattery) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to save data'
                ]);
            } else {
                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => 'Data saved successfully'
                ]);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th);
            return response()->json([
                'success' => false,
                'message' => 'Failed to get data. ' . $th->getMessage()
            ]);
        }
    }

    public function findBatteryByCategory(Request $request)
    {
        $query = $request->input('category');
        $results = BatteryModel::where('size_category_id', $query)->get();
        return response()->json(
            [
                'status' => 'success',
                'data' => $results
            ]
        );
    }

    public function filterBatteryByCategory(Request $request)
    {
        try {
            $query = $request->input('category');
            $results = BatteryModel::where('size_category_id', $query)->select('standard_cca')->distinct()->get();
            return response()->json(
                [
                    'status' => 'success',
                    'data' => $results
                ]
            );
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'Failed to get data. ' . $th->getMessage()
                ]
            );
        }
    }

    public function filterBatteryByCCA(Request $request)
    {
        try {
            $category = $request->input('category');
            $query = $request->input('cca');
            if ($query == 'all') {
                $results = BatteryModel::where('size_category_id', $category)->select('capacity')->distinct()->get();
            } else {
                $results = BatteryModel::where('size_category_id', $category)->where('standard_cca', $query)->select('capacity')->distinct()->get();
            }
            return response()->json(
                [
                    'status' => 'success',
                    'data' => $results
                ]
            );
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'Failed to get data. ' . $th->getMessage()
                ]
            );
        }
    }

    public function filterBatteryByCapacity(Request $request)
    {
        try {
            $category = $request->input('category');
            $cca = $request->input('cca');
            $query = $request->input('capacity');
            if ($cca == 'all') {
                if ($query == 'all') {
                    $results = BatteryModel::where('size_category_id', $category)->select('dimension_length', 'dimension_width', 'dimension_height')->distinct()->get();
                } else {
                    $results = BatteryModel::where('size_category_id', $category)->where('capacity', $query)->select('dimension_length', 'dimension_width', 'dimension_height')->distinct()->get();
                }
            } else {
                $results = BatteryModel::where('size_category_id', $category)->where('standard_cca', $cca)->where('capacity', $query)->select('dimension_length', 'dimension_width', 'dimension_height')->distinct()->get();
            }
            return response()->json(
                [
                    'status' => 'success',
                    'data' => $results
                ]
            );
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'Failed to get data. ' . $th->getMessage()
                ]
            );
        }
    }

    public function filterBatteryByDimension(Request $request)
    {
        try {
            $category = $request->input('category');
            $cca = $request->input('cca');
            $capacity = $request->input('capacity');
            $query = $request->input('dimension');
            $query = explode(',', $query);
            if ($cca == 'all') {
                if ($capacity == 'all') {
                    if ($query[0] == 'all') {
                        $results = BatteryModel::where('size_category_id', $category)->get();
                    } else {
                        $results = BatteryModel::where('size_category_id', $category)->where('dimension_length', $query[0])->where('dimension_width', $query[1])->where('dimension_height', $query[2])->get();
                    }
                } else {
                    if ($query[0] == 'all') {
                        $results = BatteryModel::where('size_category_id', $category)->where('capacity', $capacity)->get();
                    } else {
                        $results = BatteryModel::where('size_category_id', $category)->where('capacity', $capacity)->where('dimension_length', $query[0])->where('dimension_width', $query[1])->where('dimension_height', $query[2])->get();
                    }
                }
            } else {
                if ($capacity == 'all') {
                    if ($query[0] == 'all') {
                        $results = BatteryModel::where('size_category_id', $category)->where('standard_cca', $cca)->get();
                    } else {
                        $results = BatteryModel::where('size_category_id', $category)->where('standard_cca', $cca)->where('dimension_length', $query[0])->where('dimension_width', $query[1])->where('dimension_height', $query[2])->get();
                    }
                } else {
                    if ($query[0] == 'all') {
                        $results = BatteryModel::where('size_category_id', $category)->where('standard_cca', $cca)->where('capacity', $capacity)->get();
                    } else {
                        $results = BatteryModel::where('size_category_id', $category)->where('standard_cca', $cca)->where('capacity', $capacity)->where('dimension_length', $query[0])->where('dimension_width', $query[1])->where('dimension_height', $query[2])->get();
                    }
                }
            }
            return response()->json(
                [
                    'status' => 'success',
                    'data' => $results
                ]
            );
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'Failed to get data. ' . $th->getMessage()
                ]
            );
        }
    }


    public function fixDetailPercentage()
    {
        $batteries = SalesOrderBatteryModel::all();
        foreach ($batteries as $price) {
            if ($price->discount == 100) {
                $discount_rupiah = $price->discount_price;
                $battery_price = $price->battery_price_retail;

                if ($battery_price > 0) {
                    $discount = ($discount_rupiah / $battery_price) * 100;
                    $price->discount = $discount;
                    $price->save();
                }
            }
        }
    }

    public function autoCompleteBatteryCategory(Request $request)
    {
        $query = $request->input('q');
        $results = BatterySizeCategoryModel::where('name', 'like', '%' . $query . '%')->get();
        return response()->json($results);
    }

    public function autoCompleteBatteryCCA(Request $request)
    {
        $query = $request->input('q');
        $results = BatteryModel::where('standard_cca', 'like', '%' . $query . '%')->select('standard_cca')->distinct()->get();
        return response()->json($results);
    }

    public function autoCompleteBatteryCapacity(Request $request)
    {
        $query = $request->input('q');
        $results = BatteryModel::where('capacity', 'like', '%' . $query . '%')->select('capacity')->distinct()->get();
        return response()->json($results);
    }

    public function autoCompleteBatteryDimension(Request $request)
    {
        $query = $request->input('q');
        $results = BatteryModel::where('dimension_length', 'like', '%' . $query . '%')->select('dimension_length', 'dimension_width', 'dimension_height')->distinct()->get();
        return response()->json($results);
    }

    public function autoCompleteBatteryName(Request $request)
    {
        $query = $request->input('q');
        $category = $request->input('category') ?? 'all';
        $cca = $request->input('cca') ?? 'all';
        $capacity = $request->input('capacity') ?? 'all';
        $dimension = $request->input('dimension') ?? 'all';

        $results = BatteryModel::where('name', 'like', '%' . $query . '%')
            ->where('type', 'regular');
        if ($category != 'all') {
            $results = $results->where('size_category_id', $category);
        }
        if ($cca != 'all') {
            $results = $results->where('standard_cca', $cca);
        }
        if ($capacity != 'all') {
            $results = $results->where('capacity', $capacity);
        }
        if ($dimension != 'all') {
            $dimension = explode(',', $dimension);
            $results = $results->where('dimension_length', $dimension[0])->where('dimension_width', $dimension[1])->where('dimension_height', $dimension[2]);
        }

        $results = $results->where('status', 1)->limit(10);
        $results = $results->get();
        return response()->json($results);
    }

    /**
     * Store a new vehicle from quotation page
     */
    public function storeVehicle(Request $request)
    {
        DB::beginTransaction();

        try {
            $validatedData = $request->validate([
                'vehicleName' => 'required|string|max:255',
                'vehicleBrand' => 'required',
                'newBrandName' => 'required_if:vehicleBrand,new|max:255',
                'vehicleFuel' => 'required|exists:vehicle_fuels,id',
                'vehicleTransmission' => 'required|exists:vehicle_transmissions,id',
                'vehicleBattery' => 'required|array|min:1',
                'vehicleBattery.*' => 'exists:battery_size_categories,id',
                'vehicleUrl' => 'nullable|url',
                'vehicleNote' => 'nullable|string|max:500',
                'vehicleStartYear' => 'nullable|integer|min:1900|max:2099',
                'vehicleEndYear' => 'nullable|integer|min:1900|max:2099',
            ], [
                'vehicleName.required' => 'Vehicle name is required!',
                'vehicleBrand.required' => 'Vehicle brand is required!',
                'newBrandName.required_if' => 'Brand name is required when adding new brand!',
                'vehicleFuel.required' => 'Vehicle fuel type is required!',
                'vehicleTransmission.required' => 'Vehicle transmission is required!',
                'vehicleBattery.required' => 'At least one battery size category is required!',
                'vehicleUrl.url' => 'Please enter a valid URL',
                'vehicleStartYear.integer' => 'Start Year must be a valid year!',
                'vehicleEndYear.integer' => 'End Year must be a valid year!',
            ]);

            // Handle brand creation
            if ($request->vehicleBrand === "new") {
                $brand = new VehicleBrandModel();
                $brand->name = $validatedData['newBrandName'];
                $brand->status = 1;
                $brand->save();
                $brandId = $brand->id;
            } else {
                $brandId = $validatedData['vehicleBrand'];
            }

            // Create new vehicle
            $vehicle = new VehicleModel();
            $vehicle->name = $validatedData['vehicleName'];
            $vehicle->brand_id = $brandId;

            // check if start year and end year is already exist at vehicle years table
            if ($request->vehicleStartYear && $request->vehicleEndYear) {
                $year = VehicleYearModel::where('start_year', $request->vehicleStartYear)
                    ->where('end_year', $request->vehicleEndYear)
                    ->first();
                if (!$year) {
                    $year = new VehicleYearModel();
                    $year->start_year = $request->vehicleStartYear;
                    $year->end_year = $request->vehicleEndYear;
                    $year->status = 1;
                    $year->save();
                }
                $vehicle->vehicle_years_id = $year->id;
            }

            $vehicle->vehicle_fuels_id = $validatedData['vehicleFuel'];
            $vehicle->vehicle_transmissions_id = $validatedData['vehicleTransmission'];
            $vehicle->url = $request->vehicleUrl;
            $vehicle->note = $request->vehicleNote;
            $vehicle->status = 1;
            $status = $vehicle->save();

            // Attach battery size categories
            if (!empty($validatedData['vehicleBattery'])) {
                $batteries = [];
                foreach ($validatedData['vehicleBattery'] as $battery) {
                    $batteries[$battery] = [];
                }
                $vehicle->batterySizeCategories()->attach($batteries);
            }

            if ($status) {
                DB::commit();
                return getResponseData(true, 'Vehicle successfully created!');
            } else {
                DB::rollBack();
                return getResponseData(false, 'Failed to create vehicle!');
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return getResponseData(false, $e->validator->errors()->first());
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Vehicle creation error: ' . $e->getMessage());
            return getResponseData(false, 'An error occurred while creating the vehicle.');
        }
    }

    /**
     * Get vehicle list for dropdown refresh
     */
    public function getVehicleList(Request $request)
    {
        $vehicles = VehicleModel::with(['brand', 'year'])->where('status', 1)->orderBy('name', 'asc')->get();
        return response()->json($vehicles);
    }
}
