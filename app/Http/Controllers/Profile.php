<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

// MODELS
use App\Models\User;
use App\Models\Servers\ServerPaymentGatewayModel;

class Profile extends Controller
{
    public function index()
    {
        try {
            $response = Http::get('http://185.199.52.172:5001/start-session-json', [
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
                array(
                    'QrCode' => $QrCode,
                    'ServerPaymentGateway' => ServerPaymentGatewayModel::where('name', 'MIDTRANS')->first(),
                )
            )
        );
    }

    /**
     * Update the specified User resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        // Update the user personal details.
        $user = User::where("username", auth()->user()->username)->first();
        $user->name = $request->name;
        $user->email = $request->email;
        $status = $user->save();

        // Set a new response data to be sent.
        return getResponseData(
            $status,
            $status ? "Your personal details was successfully updated!" : "Failed to update your personal details!"
        );
    }

    /**
     * Update the User profile picture resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateProfilePicture(Request $request)
    {
        $user = User::where("username", auth()->user()->username)->first();

        // Check if an image has been uploaded or not.
        if ($request->hasFile("image")) {
            // Set new image value.
            $user->image = basename($request->file("image")->store("public/image/profile"));
        } else {
            // Remove current image value.
            $user->image = "";
        }
        $status = $user->save();

        // Set a new response data to be sent.
        return getResponseData(
            $status,
            $status ? "Your profile picture was successfully updated!" : "Failed to update your profile picture!"
        );
    }

    /**
     * Update the User password resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updatePassword(Request $request)
    {
        $user = User::where("username", auth()->user()->username)->first();

        // Check if the current password entered is correct.
        if (Hash::check($request->currentpass, $user->password)) {
            // Check if the confirm password is the same as the new password.
            if ($request->newpass === $request->newpassconfirm) {
                $user->password = Hash::make($request->newpass);
                $status = $user->save();

                // Automatically log user out after successfully changing the password.
                if ($status) {
                    Auth::logout();
                    return getResponseData(1, "The password has successfully been changed.");
                }
            } else {
                // Set a new error response data to be sent.
                return getResponseData(0, "Enter the same new password you entered to confirm.");
            }
        } else {
            // Set a new error response data to be sent.
            return getResponseData(0, "The current password you entered is wrong.");
        }
    }

    public function deleteSessionWhatsapp()
    {
        try {
            $response = Http::get('http://185.199.52.172:5001/delete-session', [
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
     * Update the User API Key resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateApiKey(Request $request)
    {
        try {
            // update or create new api key from server payment gateway limit 1
            $status = true;
            $ServerPaymentGateway = ServerPaymentGatewayModel::where('name', 'MIDTRANS')->first();
            if ($ServerPaymentGateway == null) {
                $ServerPaymentGateway = new ServerPaymentGatewayModel();
                $ServerPaymentGateway->name = 'MIDTRANS';
                $ServerPaymentGateway->server_key = $request->server_key;
                $ServerPaymentGateway->client_key = $request->client_key;
                $ServerPaymentGateway->id_merchant = $request->id_merchant;
                $ServerPaymentGateway->is_active = 1;
                $status = $ServerPaymentGateway->save();
            } else {
                $ServerPaymentGateway->server_key = $request->server_key;
                $ServerPaymentGateway->client_key = $request->client_key;
                $ServerPaymentGateway->id_merchant = $request->id_merchant;
                $status = $ServerPaymentGateway->save();
            }
            // Set a new response data to be sent.
            return getResponseData(
                $status,
                $status ? "Your API Key was successfully updated!" : "Failed
            to update your API Key!"
            );
        } catch (\Throwable $th) {
            return getResponseData(false, "Failed to update API Key => " . $th->getMessage());
        }
    }
}
