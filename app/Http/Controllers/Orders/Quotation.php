<?php

namespace App\Http\Controllers\Orders;

use App\Http\Controllers\Controller;;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

// MODELS
use App\Models\MasterData\CustomerModel;
use App\Models\MasterData\VehicleBrandModel;

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
                    'VehicleBrandModel' => VehicleBrandModel::all()->toArray()
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
        $url = "http://172.104.32.122:5001/send-message";
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

            // Mengecek status respons dari API
            if ($response->successful()) {
                $responseData = $response->json();


                if (isset($responseData['data']['status']) && $responseData['data']['status'] == 1) {
                    // return response()->json($responseData['data']);
                    return response()->json(['status' => 'success', 'message' => 'Message sent successfully']);
                } else {


                    // return response()->json(['error' => 'Unexpected response structure'], 500);
                    return response()->json(['status' => 'error', 'message' => 'Failed to send message']);
                }
            } else {

                // return response()->json(['error' => 'Failed to send message'], $response->status());
                return response()->json(['status' => 'error', 'message' => 'Failed to send message']);
            }
        } catch (\Exception $e) {

            // return response()->json(['error' => $e->getMessage()], 500);
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
