<?php

namespace App\Http\Controllers\Orders;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

// MODELS
use App\Models\MasterData\Customer\CustomerModel;
use App\Models\MasterData\Vehicle\VehicleModel;
use App\Models\MasterData\Distributor\DistributorShopModel;
use App\Models\MasterData\Battery\BatteryModel;
use App\Models\MasterData\Battery\BatteryImport;
use App\Models\MasterData\Distributor\DistributorShopBatteryModel;
use App\Models\Orders\SalesOrder\SalesOrderModel;
use App\Models\Orders\SalesOrder\SalesOrderBatteryModel;
use App\Models\Settings\MessageTemplateModel;
use App\Models\Settings\TaxModel;
use App\Models\MasterData\Battery\BatteryUrlModel;
use App\Models\Settings\PaymentMethodModel;
use App\Models\Servers\ServerPaymentGatewayModel;

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
                    'Vehicle' => VehicleModel::all()->where('status', 1)->toArray(),
                    'datalatlong ' => $datalatlong,
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
        $url = "http://185.199.52.172:5001/send-message";
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
            'session' => auth()->user()->username,
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
        if ($request->input('shop_id')) {
            $results = VehicleModel::getBatteryRecomendationWithDistributor($ids, $request->input('shop_id'));
        } else {
            // $results = VehicleModel::whereIn('id', $ids)->with('batteries')->get()->pluck('batteries')->flatten();
            $results = VehicleModel::getBatteryRecomendationWithOutDistributor($ids, $request->input('shop_id'));
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
        $url = "http://185.199.52.172:5001/send-image";
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
                    'session' => auth()->user()->username,
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
        ];


        // Check API Key Allready Exist
        $ServerPaymentGateway = ServerPaymentGatewayModel::where('name', 'MIDTRANS')->first();
        if ($ServerPaymentGateway) {
            $midtrans = new CreateSnapTokenService($InvoiceNumber);
            $snapToken = $midtrans->getSnapTokenUrl($data);

            $data['snapToken'] = $snapToken;
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
            ->select('batteries.*', 'battery_prices.discount', 'battery_prices.price_net', 'battery_prices.price_retail as price_retail_original')
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
            $arrayBattery .= "*Discount* : " . number_format($discount, 0, "", ".") . "%\r";
            $arrayBattery .= "*Harga + PPN* : Rp. " . number_format($price_tax, 0, "", ".") . "\r";

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
        $url = "http://185.199.52.172:5001/send-message";
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
            'session' => auth()->user()->username,
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
        $url = "http://185.199.52.172:5001/send-message";
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
            $content_message .= "Invoice Number : *" . $InvoiceNumber . "*\r\n";
            $content_message .= "Metode Pembayaran : *Midtrans*\r\n";
            $content_message .= "Silakan klik link berikut untuk melakukan pembayaran:\r\n";
            foreach ($PaymentLinks as $link) {
                $content_message .= "*$link*\r\n";
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
            'session' => auth()->user()->username,
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

        if ($PaymentMethodData['id'] == 1) {
            $payment_methode = "midtrans";
            $midtransInvoice = $request->input('invoiceNumber');
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
                    'address' => $request->input('AddressCustomer'),
                    'contact' => $request->input('ContactNumber'),
                    'latitude' => $request->input('Latitude'),
                    'longitude' => $request->input('Longitude')
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
            'customer_id' => $Customer->id,
            'vehicle_id' => $VehicleCustomer[0],
            'distributor_shop_id' => $DistributorShop->id ?? null,
            'distributor_shop_technician_id' => $distributorTechnician[0]['id'] ?? null,
            'subtotal' => $subtotal,
            'total' => $total,
            'discount' => $DiscountPercentage,
            'discount_price' => $DiscountRupiah,
            'payment_method_id' => $PaymentMethodData['id'],
            'midtrans_invoice' => $midtransInvoice ?? null,
            'midtrans_payment_link' => $midtransPaymentLink ?? null,
            'payment_status' => "pending",
            'status' => "draft",
            'address' => $request->input('AddressCustomer'),
            'latitude' => $request->input('Latitude'),
            'longitude' => $request->input('Longitude'),
            'date' => date('Y-m-d')
        ];

        $Quotation = SalesOrderModel::create($data);


        $dataProduct = [];
        foreach ($request->input('BatteryNameTabel') as $key => $value) {
            for ($i = 0; $i < $request->input('QtyTabel')[$key]; $i++) {
                if ($request->input('DiscountPayment')[$key] != 0) {
                    $TaxPayment = $request->input('TaxPayment')[$key];
                    $GrossPrice = str_replace(".", "", $request->input('GrossPricePayment')[$key]);
                    $NetPrice = str_replace(".", "", $request->input('NetPricePayment')[$key]);
                    $Discount = $request->input('DiscountPayment')[$key];
                    $Subtotal = $request->input('SubtotalPayment')[$key];
                    $TaxPrice = $GrossPrice * $TaxPayment / 100;
                } else {
                    $TaxPayment = $request->input('TaxPayment')[$key];
                    $GrossPrice = str_replace(".", "", $request->input('GrossPricePayment')[$key]);
                    $NetPrice = str_replace(".", "", $request->input('NetPricePayment')[$key]);
                    $Discount = $request->input('DiscountPayment')[$key];
                    $Subtotal = $request->input('SubtotalPayment')[$key];
                    $TaxPrice = $GrossPrice * $TaxPayment / 100;
                }
                $dataProduct[] = [
                    'sales_order_id' => $Quotation->id,
                    'battery_id' => $request->input('BatteryIdCheckout')[$key],
                    'battery_name' => $value,
                    'battery_price_retail' => $GrossPrice,
                    'discount' => $Discount,
                    'discount_price' => "0",
                    'tax' => $request->input('TaxPayment')[$key],
                    'tax_price' =>  $TaxPrice,
                    'price_net' =>  str_replace(".", "", $Subtotal),
                    'quantity' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
            }
        }

        $QuuotationBattery = SalesOrderBatteryModel::insert($dataProduct);
        if (!$QuuotationBattery) {
            return getResponseData(false, "Failed to save data");
        } else {
            return getResponseData(true, "Data saved successfully");
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
            $content_message .= "Invoice Number : *" . $InvoiceNumber . "*\r\n";
            $content_message .= "Metode Pembayaran : *Midtrans*\r\n";
            $content_message .= "Silakan klik link berikut untuk melakukan pembayaran:\r\n";
            foreach ($PaymentLinks as $link) {
                $content_message .= "*$link*\r\n";
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
            ->select('batteries.*', 'battery_size_categories.name as size_category', 'battery_prices.discount', 'battery_prices.price_net', 'battery_prices.price_retail as price_retail_original', 'battery_prices.discount')
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
        // $results = BatteryModel::where('name', 'like', '%' . $query . '%')->limit(10)->get();
        $results = BatteryModel::where('batteries.name', 'like', '%' . $query . '%')
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
            }
            if ($key['image'] != null) {
                $value['image'] = asset('storage/image/battery/' . $key['image']);
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
            $priceDiscount = $pricePPn * $batteryPrices[0]['discount'] / 100;
            $priceNetto = $pricePPn - $priceDiscount;
            $batteries['prices'][] = [
                'original' =>  "Rp. " . number_format($pricePPn, 0, "", "."),
                'discount' => number_format($batteryPrices[0]['discount'], 0, "", ".") . "",
                'netto' => "Rp. " . number_format($priceNetto, 0, "", "."),
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
}
