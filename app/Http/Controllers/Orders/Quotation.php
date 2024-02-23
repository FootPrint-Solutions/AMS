<?php

namespace App\Http\Controllers\Orders;

use App\Http\Controllers\Controller;;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

// MODELS
use App\Models\MasterData\Customer\CustomerModel;
use App\Models\MasterData\Vehicle\VehicleBrandModel;
use App\Models\MasterData\Vehicle\VehicleModel;
use App\Models\MasterData\Customer\CustomerVehicleModel;
use App\Models\MasterData\Distributor\DistributorShopModel;
use App\Models\MasterData\Battery\BatteryModel;

class Quotation extends Controller
{
    public function index()
    {
        return view(
            'Orders/Quotation/index',
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
        $url = "http://139.162.35.251:5001/send-message";
        $vehicleCustomer = $request->input('VehicleCustomer');
        $vehicleCustomerString = is_array($vehicleCustomer) ? implode(', ', $vehicleCustomer) : $vehicleCustomer;

        $template = $request->input('TemplateMessage');
        $text = str_replace(
            ['<NAME>', '<ADDRESS>', '<EMAIL>', '<VEHICLE>'],
            [$request->input('FullName'), $request->input('AddressCustomer'), $request->input('EmailCustomer'), $vehicleCustomerString],
            $template
        );


        $data = [
            'to' => "62" . $request->input('ContactNumber'),
            'session' => auth()->user()->username,
            'text' => $text,
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
                return getResponseData(false, "Failed to send message");
            }
        } catch (\Exception $e) {
            return getResponseData(false, "Failed to send message => " . $e->getMessage());
        }
    }

    public function findVehicleByIdCustomer(Request $request)
    {
        $id = $request->input('id');
        $results = CustomerModel::find($id)->vehicles()->pluck("id_vehicle")->toArray();
        return response()->json($results);
    }

    public function findVehicleByIdVehicle(Request $request)
    {
        $ids = $request->input('id');
        $results = VehicleModel::whereIn('id', $ids)->with('batteries')->get()->pluck('batteries')->flatten();
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
            ];
        }

        $data = [
            'address' => $address,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'distributor' => $Distibutor,
            'datalatlong' => $datalatlong
        ];

        return view('Orders.Quotation.mapsaddressdistributor', $data);
    }

    public function shareBattery(Request $request)
    {
        $url = "http://139.162.35.251:5001/send-image";
        $ids = $request->input('Battery');
        $results = BatteryModel::where('id', $ids)->get()->toArray();


        foreach ($results as $key => $value) {
            if ($value['image'] != null) {
                $value['image'] = asset('storage/image/battery/' . $value['image']);
            } else {
                $value['image'] = null;
            }
            $data = [
                'to' => "62" . $request->input('ContactNumber'),
                'session' => auth()->user()->username,
                'url' => $value['image'] ?? "https://via.placeholder.com/210x210",
                'caption' => "Battery Name : " . $value['name'] . "\n" . "Battery Capacity : " . $value['capacity'] . "\n" . "Battery Price : Rp. " . number_format($value['price_retail'], 0, "", ".") . "\n" . "Battery Warranty : " . $value['warranty'] . "\n" . "\n",
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
                    return getResponseData(false, "Failed to send message");
                }
            } catch (\Exception $e) {
                return getResponseData(false, "Failed to send message => " . $e->getMessage());
            }
        }
    }
}
