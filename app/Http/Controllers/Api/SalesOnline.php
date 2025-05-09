<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SalesOnline extends Controller
{
    public function receiveData(Request $request)
    {
        // Validasi metode request
        if (!$request->isMethod('post')) {
            return response()->json(['status' => 'error', 'message' => 'Invalid request method'], 405);
        }

        // Ambil data dari request
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

        // Simpan atau proses data sesuai kebutuhan
        // Contoh: Simpan ke database atau kirim ke layanan lain

        return response()->json(['status' => 'success', 'message' => 'Data berhasil diterima.', 'data' => $data, 'cartDetails' => $cartDetails]);
    }
}
