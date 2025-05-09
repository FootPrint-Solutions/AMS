<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Orders\SalesOnline\SalesOnlineModel;
use App\Models\Orders\SalesOnline\SalesOnlineBatteriesModel;
use App\Models\MasterData\Battery\BatteryModel;

class SalesOnline extends Controller
{
    public function receiveData(Request $request)
    {
        try {
            if (!$request->isMethod('post')) {
                return response()->json(['status' => 'error', 'message' => 'Invalid request method'], 405);
            }

            $data = $request->only([
                'customerName', 'province', 'city', 'district', 'subDistrict',
                'postalCode', 'phoneNumber', 'email', 'vehiclePlate', 'deliveryDate', 'additionalInfo'
            ]);

            $cartDetails = $request->input('cartDetails', []);

            // Validasi data
            if (
                empty($data['customerName']) || empty($data['province']) || empty($data['city']) ||
                empty($data['district']) || empty($data['subDistrict']) || empty($data['phoneNumber']) ||
                empty($data['deliveryDate'])
            ) {
                return response()->json(['status' => 'error', 'message' => 'Semua kolom yang wajib diisi harus diisi.']);
            }

            if (!preg_match('/^[0-9]{10,15}$/', $data['phoneNumber'])) {
                return response()->json(['status' => 'error', 'message' => 'Nomor telepon tidak valid. Harap gunakan format 628xxxxxxxxx.']);
            }

            $salesOnline = SalesOnlineModel::create([
                'customer_name' => $data['customerName'],
                'province' => $data['province'],
                'city' => $data['city'],
                'district' => $data['district'],
                'sub_district' => $data['subDistrict'],
                'postal_code' => $data['postalCode'],
                'phone_number' => $data['phoneNumber'],
                'email' => $data['email'],
                'vehicle_plate' => $data['vehiclePlate'],
                'delivery_date' => $data['deliveryDate'],
                'additional_info' => $data['additionalInfo'],
            ]);

            foreach ($cartDetails as $cart) {
                SalesOnlineBatteriesModel::create([
                    'sales_online_id' => $salesOnline->id,
                    'battery_id' => $cart['id'],
                    'name' => $cart['name'],
                    'price' => $cart['price'],
                    'image' => $cart['image'],
                    'quantity' => $cart['quantity'],
                    'total_price' => $cart['price'] * $cart['quantity'],
                ]);
            }

            return response()->json(['status' => 'success', 'message' => 'Data berhasil disimpan.', 'data' => $salesOnline]);
        } catch (\Throwable $th) {
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan saat menyimpan data: ' . $th->getMessage()], 500);
        }
    }
}
