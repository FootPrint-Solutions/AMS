<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class Profile extends Controller
{
    public function index()
    {
        try {
            $response = Http::get('http://172.104.32.164:5001/start-session-json', [
                'session' => auth()->user()->username,
                'scan' => 'true',
            ]);
            if (isset($response['data']['qr']) && $response['data']['qr'] != null) {
                $QrCode = $response['data']['qr'];
            } else {
                $QrCode = "";
            }
        } catch (\Throwable $th) {
            $QrCode = "";
        }

        return view(
            'Profile.index',
            getIndexData(
                'Profile',
                '',
                '',
                array(
                    'QrCode' => $QrCode
                )
            )
        );
    }

    public function deleteSessionWhatsapp()
    {
        try {
            $response = Http::get('http://172.104.32.164:5001/delete-session', [
                'session' => auth()->user()->username,
            ]);

            if (isset($response['message'])) {
                return response()->json([
                    'status' => true,
                    'message' => 'Session deleted successfully'
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed to delete session'
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to delete session'
            ]);
        }
    }
}
