<?php

namespace App\Http\Controllers\Orders;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

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
// Midtrans 
use App\Services\Midtrans\CreateSnapTokenService;

class QuickQuotation extends Controller
{
    public function index()
    {
        return view(
            'Orders.QuickQuotation.index',
            getIndexData(
                'Quick Quotation',
                3,
                5,
                array(
                    'Vehicle' => VehicleModel::all()->toArray()
                )
            )
        );
    }

    public function findCustomer(Request $request)
    {
        $query = $request->input('input');
        $results = CustomerModel::where('name', 'like', '%' . $query . '%')->orderBy('name', 'asc')->limit(10)->get();
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
*Your address* :
📍 $AddressCustomer 

*Your email* :
📧 $EmailCustomer

*Your contact number* :
📞 $ContactNumber

*Your vehicle* :
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
        $results = CustomerModel::find($id)->vehicles()->pluck("vehicle_id")->toArray();
        return response()->json($results);
    }

    public function findVehicleByIdVehicle(Request $request)
    {
        $ids = $request->input('id');
        if ($request->input('shop_id')) {
            $results = VehicleModel::getBatteryRecomendationWithDistributor($ids, $request->input('shop_id'));
        } else {
            $results = VehicleModel::whereIn('id', $ids)->with('batteries')->get()->pluck('batteries')->flatten();
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
        $results = BatteryModel::where('id', $ids)->get()->toArray();

        if ($results == null) {
            return getResponseData(false, "Battery not found");
        } else {
            $arrayBattery = "";
            foreach ($results as $key => $value) {
                $arrayBattery .= "*Name* : " . $value['name'] . "\r";
                $arrayBattery .= "*Capacity* : " . $value['capacity'] . "\r";
                $arrayBattery .= "*Price* : Rp. " . number_format($value['price_retail'], 0, "", ".") . "\r";
                $arrayBattery .= "*Warranty* : " . $value['warranty'] . " Bulan";


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
            $BatteryData = BatteryModel::whereIn('id', $request->input('Battery'))->get();
        }

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
        $InvoiceNumber = "INV" . date('YmdHis') . rand(1000, 9999);
        $BatteryNameTabel = $request->input('BatteryNameTabel');
        $QtyTabel = $request->input('QtyTabel');
        $PriceTabel = $request->input('PriceTabel');
        $Link = $request->input('LinkTokopedia');
        $tax = $request->input('tax') ?? 0;
        $Discount = $request->input('Discount') ?? 0;
        $ExtraDiscount = $request->input('ExtraDiscount') ?? 0;
        if ($request->input('DistributorShopId') != null) {
            $DistibutorShop = DistributorShopModel::find($request->input('DistributorShopId'));
            $BatteryData = BatteryModel::getBatteryDistributor($request->input('Battery'), $request->input('DistributorShopId'));
            $dataProduct = [];
            foreach ($BatteryData as $key => $value) {
                $dataProduct[] = [
                    'name' => $value->name,
                    'qty' => $QtyTabel[$key],
                    'price' => $value->price_retail,
                    'link' => $Link[$key],
                ];
            }
        } else {
            $DistibutorShop = "";
            $dataProduct = [];
            foreach ($BatteryNameTabel as $key => $value) {
                $dataProduct[] = [
                    'name' => $value,
                    'qty' => $QtyTabel[$key],
                    'price' => $PriceTabel[$key],
                    'link' => ''
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
        ];

        $midtrans = new CreateSnapTokenService($InvoiceNumber);
        $snapToken = $midtrans->getSnapTokenUrl($data);

        $data['snapToken'] = $snapToken;

        return view('Orders.QuickQuotation.step-4-paymentpreview', $data);
    }

    public function getBatteryCopyDetail(Request $request)
    {
        $batteryIds = $request->input('Battery');
        $Fullname = $request->input('FullName');
        $batteries = BatteryModel::whereIn('id', $batteryIds)->get();

        $arrayBattery = "";
        foreach ($batteries as $battery) {
            $arrayBattery .= "*Name* : " . $battery->name . "\r";
            $arrayBattery .= "*Capacity* : " . $battery->capacity . "\r";
            $arrayBattery .= "*Price* : Rp. " . number_format($battery->price_retail, 0, "", ".") . "\r";
            $arrayBattery .= "*Warranty* : " . $battery->warranty . " Bulan\r";
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

        $message  = $opening_message . "\n" . $content_message . "\n" . $TemplateMessagePersonalDetails['closing_message'];

        return getResponseData(true, $message);
    }


    public function shareInvoice(Request $request)
    {
        $url = "http://185.199.52.172:5001/send-message";
        $template = $request->input('TemplateMessage');
        $BatteryNameTabel = $request->input('BatteryNameTabel');
        $QtyTabel = $request->input('QtyTabel');
        $PriceTabel = $request->input('PriceTabel');
        $TotalAmount = $request->input('TotalAmount');
        $tax = $request->input('tax') ?? 0;
        $Discount = $request->input('Discount') ?? 0;
        $ExtraDiscount = $request->input('ExtraDiscount') ?? 0;
        $Fullname = $request->input('FullName');
        $ContactNumber = $request->input('ContactNumber');

        foreach ($BatteryNameTabel as $key => $value) {
            $BatteryNameTabel[$key] = $value . "";
            $QtyTabel[$key] = $QtyTabel[$key] . "";
            $PriceTabel[$key] = "Rp. " . number_format($PriceTabel[$key], 0, "", ".") . "";
        }

        $BatteryNames = implode(', ', $BatteryNameTabel);
        $Qtys = implode(', ', $QtyTabel);
        $Prices = implode(', ', $PriceTabel);

        $text = str_replace(
            ['<NAME>', '<BATTERYNAME>', '<QUANTITY>', '<BATTERYPRICE>', '<TOTALAMOUNT>', '<TAX>', '<DISCOUNT>', '<EXTRADISCOUNT>', 'and your technician is <NAMETECHNICIAN>  the number : <PHONETECHNICIAN>', '<PHONETECHNICIAN>'],
            [
                $Fullname, $BatteryNames, $Qtys, $Prices, "Rp. " . number_format($TotalAmount, 0, "", "."), $tax, $Discount, $ExtraDiscount, ''
            ],
            $template
        );

        $text = trim($text);
        $data = [
            'to' => "62" . $ContactNumber,
            'session' => auth()->user()->username,
            'text' => "$text",
        ];

        $response = Http::post($url, $data);
        $responseData = $response->json();

        if (isset($responseData['data']['status']) && $responseData['data']['status'] == true) {
            return getResponseData(true, "Message sent successfully");
        } else {
            return getResponseData(false, "Failed to send message => " . $responseData['data']['message']);
        }
    }

    public static function sharePaymentDetails(Request $request)
    {
        $url = "http://185.199.52.172:5001/send-message";
        $template = $request->input('TemplateMessage');
        $Fullname = $request->input('FullName');
        $ContactNumber = $request->input('ContactNumber');

        $text = str_replace(
            ['<NAME>', '<PAYMENTLINK>'],
            [$Fullname, 'https://www.google.com'],
            $template
        );
    }

    public static function saveData(Request $request)
    {
        $tax = $request->input('tax') ?? 0;
        $Discount = $request->input('Discount') ?? 0;
        $ExtraDiscount = $request->input('ExtraDiscount') ?? 0;
        $total = $request->input('TotalAmount');
        $status = "Pending";

        if ($request->input('CheckMidtrans') == 1) {
            $payment_methode = "midtrans";
            $midtransInvoice = $request->input('invoiceNumber');
            $midtransPaymentLink = $request->input('paymentLink');
        } else {
            $payment_methode = "tokopedia";
        }


        if ($request->input('IdCustomer') != null) {
            $Customer = CustomerModel::find($request->input('IdCustomer'));
            $Customer->vehicles()->sync($request->input('VehicleCustomer'));
        } else {
            $Customer = CustomerModel::firstOrCreate(
                ['email' => $request->input('EmailCustomer')],
                [
                    'name' => $request->input('FullName'),
                    'address' => $request->input('AddressCustomer'),
                    'contact' => $request->input('ContactNumber'),
                    'latitude' => $request->input('Latitude'),
                    'longitude' => $request->input('Longitude')
                ]
            );

            $Customer->vehicles()->sync($request->input('VehicleCustomer'));
        }

        if ($request->input('DistributorShopId') != null) {
            $DistributorShop = DistributorShopModel::find($request->input('DistributorShopId'));
            $distributorTechnician = DistributorShopModel::find($request->input('DistributorShopId'))->technicians()->get()->toArray();

            $link = $request->input('linkPayment');
            // update link to databse 
            foreach ($link as $key => $value) {
                $data = [
                    'url' => $value
                ];
                DistributorShopBatteryModel::where('distributor_shop_id', $request->input('DistributorShopId'))->where('battery_id', $request->input('Battery')[$key])->update($data);
            }
        } else {
            $DistributorShop = null;
        }

        $data = [
            'sales_order_number' => SalesOrderModel::newCode(),
            'customer_id' => $Customer->id,
            'distributor_shop_id' => $DistributorShop->id ?? null,
            'distributor_shop_technician_id' => $distributorTechnician[0]['id'] ?? null,
            'total' => $total,
            'tax' => $tax,
            'discount' => $Discount,
            'extra_discount' => $ExtraDiscount,
            'payment_methode' => $payment_methode,
            'midtrans_invoice' => $midtransInvoice ?? null,
            'midtrans_payment_link' => $midtransPaymentLink ?? null,
            'status' => $status,
            'address' => $request->input('AddressCustomer'),
            'latitude' => $request->input('Latitude'),
            'longitude' => $request->input('Longitude'),
            'date' => date('Y-m-d')
        ];

        $Quotation = SalesOrderModel::create($data);

        if ($request->input('DistributorShopId') != null) {
            $BatteryData = BatteryModel::getBatteryDistributor($request->input('Battery'), $request->input('DistributorShopId'));
            $dataProduct = [];
            foreach ($BatteryData as $key => $value) {
                for ($i = 0; $i <  $request->input('QtyTabel')[$key]; $i++) {
                    $dataProduct[] = [
                        'sales_order_id' => $Quotation->id,
                        'battery_id' => $value->id,
                        'battery_name' => $value->name,
                        // 'quantity' => 1,
                        'battery_price' => $value->price_retail,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ];
                }
            }
        } else {
            $dataProduct = [];
            foreach ($request->input('BatteryNameTabel') as $key => $value) {
                for ($i = 0; $i < $request->input('QtyTabel')[$key]; $i++) {
                    $dataProduct[] = [
                        'sales_order_id' => $Quotation->id,
                        'battery_id' => $request->input('Battery')[$key],
                        'battery_name' => $value,
                        // 'quantity' => 1,
                        'battery_price' => $request->input('PriceTabel')[$key],
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ];
                }
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
*Your address* :
📍 $AddressCustomer 

*Your email* :
📧 $EmailCustomer

*Your contact number* :
📞 $ContactNumber

*Your vehicle* :
$arrayVehicle
";

        $message  = $opening_message . "\n" . $content_message . "\n" . $TemplateMessagePersonalDetails['closing_message'];

        return getResponseData(true, $message);
    }
}
