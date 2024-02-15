<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;

// MODELS
use App\Models\User;

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
                return getResponseData(true, "Session deleted successfully");
            } else {
                return getResponseData(false, "Failed to delete session");
            }
        } catch (\Throwable $th) {
            return getResponseData(false, "Failed to delete session => " . $th->getMessage());
        }
    }

    /**
     * Update the specified Vehicle resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $user = User::where("username", auth()->user()->username)->first();

        if (Hash::check($request->oldpass, $user->password)) {
            $user->password = Hash::make($request->newpass);
            $status = $user->save();

            // Automatically log user out after successfully changing the password.
            if ($status) {
                return redirect("/logout");
            }
        } else {
            return redirect('/profile');
        }
    }
}
