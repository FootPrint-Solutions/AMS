<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Models
use App\Models\MenuParent;
use App\Models\Menu;

class Authentication extends Controller
{
    public function index()
    {
        // Set menu session.
        session([
            'menu' => MenuParent::with(['menus' => function ($query) {
                $query->orderBy('order');
            }])->orderBy('order')->get()->toArray(),
            'submenu' => Menu::with(['menuSubs' => function ($query) {
                $query->orderBy('order');
            }])->get()->mapWithKeys(function ($menu) {
                return [$menu->id => $menu->menuSubs->toArray()];
            })->toArray()
        ]);

        return view(
            'Auth.login',
            getIndexData(
                'Login',
            )
        );
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->intended('dashboard');
        }

        return back()->with('loginError', 'Login failed, please check your credentials');
    }

    public function logout(Request $request)
    {
        $profileController = new Profile();
        $profileController->deleteSessionWhatsapp();

        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function profile()
    {
        return view(
            'Auth.profile',
            getIndexData(
                'Profile',
            )
        );
    }
}
