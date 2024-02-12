<?php

use Illuminate\Support\Facades\Route;

// DASHBOARD
use App\Http\Controllers\Dashboard\Dashboard;

// MASTER DATA
use App\Http\Controllers\MasterData\Company;
use App\Http\Controllers\MasterData\Customer;
use App\Http\Controllers\MasterData\Vehicle\Vehicle;
use App\Http\Controllers\MasterData\Vehicle\VehicleBrand;
use App\Http\Controllers\MasterData\Battery\Battery;
use App\Http\Controllers\MasterData\Battery\BatteryBrand;
use App\Http\Controllers\MasterData\Battery\BatterySubbrand;
use App\Http\Controllers\MasterData\Battery\BatteryTechnology;
use App\Http\Controllers\MasterData\Battery\BatteryUsage;

// ORDERS
use App\Http\Controllers\Orders\Quotation;

// AUTH
use App\Http\Controllers\Auth\Authentication;

// PROFILE
use App\Http\Controllers\Profile;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::middleware(['auth'])->group(function () {
    // DASHBOARD
    Route::get('/', [Dashboard::class, 'index']);

    // MASTER DATA
    // Company
    Route::get('/company', [Company::class, 'index']);
    Route::post('/company/update', [Company::class, 'update']);

    // Customer
    Route::get('/customer', [Customer::class, 'index']);
    Route::post('/customer/show', [Customer::class, 'show'])->name('customer.show');
    Route::get('/customer/create', [Customer::class, 'create']);
    Route::get('/customer/edit/{id}', [Customer::class, 'edit'])->name('customer.edit');
    Route::post('/customer/store', [Customer::class, 'store'])->name('customer.store');
    Route::post('/customer/update', [Customer::class, 'update'])->name('customer.update');
    Route::post('/customer/destroy', [Customer::class, 'destroy'])->name('customer.destroy');

    // Vehicle
    Route::get('/vehicle', [Vehicle::class, 'index']);
    Route::post('/vehicle/show', [Vehicle::class, 'show'])->name('vehicle.show');
    Route::get('/vehicle/create', [Vehicle::class, 'create']);
    Route::get('/vehicle/edit/{id}', [Vehicle::class, 'edit'])->name('vehicle.edit');
    Route::post('/vehicle/store', [Vehicle::class, 'store'])->name('vehicle.store');
    Route::post('/vehicle/update', [Vehicle::class, 'update'])->name('vehicle.update');
    Route::post('/vehicle/destroy', [Vehicle::class, 'destroy'])->name('vehicle.destroy');

    // Vehicle Brand
    Route::get('/vehicle/brand', [VehicleBrand::class, 'index']);
    Route::post('/vehicle/brand/show', [VehicleBrand::class, 'show'])->name('vehicle.brand.show');
    Route::get('/vehicle/brand/create', [VehicleBrand::class, 'create']);
    Route::get('/vehicle/brand/edit/{id}', [VehicleBrand::class, 'edit'])->name('vehicle.brand.edit');
    Route::post('/vehicle/brand/store', [VehicleBrand::class, 'store'])->name('vehicle.brand.store');
    Route::post('/vehicle/brand/update', [VehicleBrand::class, 'update'])->name('vehicle.brand.update');
    Route::post('/vehicle/brand/destroy', [VehicleBrand::class, 'destroy'])->name('vehicle.brand.destroy');

    // Battery
    Route::get('/battery', [Battery::class, 'index']);
    Route::post('/battery/show', [Battery::class, 'show'])->name('battery.show');
    Route::get('/battery/create', [Battery::class, 'create']);
    Route::get('/battery/edit/{id}', [Battery::class, 'edit'])->name('battery.edit');
    Route::post('/battery/store', [Battery::class, 'store'])->name('battery.store');
    Route::post('/battery/update', [Battery::class, 'update'])->name('battery.update');
    Route::post('/battery/destroy', [Battery::class, 'destroy'])->name('battery.destroy');

    // Battery Brand
    Route::get('/battery/brand', [BatteryBrand::class, 'index']);
    Route::post('/battery/brand/show', [BatteryBrand::class, 'show'])->name('battery.brand.show');
    Route::get('/battery/brand/create', [BatteryBrand::class, 'create']);
    Route::get('/battery/brand/edit/{id}', [BatteryBrand::class, 'edit'])->name('battery.brand.edit');
    Route::post('/battery/brand/store', [BatteryBrand::class, 'store'])->name('battery.brand.store');
    Route::post('/battery/brand/update', [BatteryBrand::class, 'update'])->name('battery.brand.update');
    Route::post('/battery/brand/destroy', [BatteryBrand::class, 'destroy'])->name('battery.brand.destroy');

    // Battery Subbrand Category
    Route::get('/battery/subbrand', [BatterySubbrand::class, 'index']);
    Route::post('/battery/subbrand/show', [BatterySubbrand::class, 'show'])->name('battery.subbrand.show');
    Route::get('/battery/subbrand/create', [BatterySubbrand::class, 'create']);
    Route::get('/battery/subbrand/edit/{id}', [BatterySubbrand::class, 'edit'])->name('battery.subbrand.edit');
    Route::post('/battery/subbrand/store', [BatterySubbrand::class, 'store'])->name('battery.subbrand.store');
    Route::post('/battery/subbrand/update', [BatterySubbrand::class, 'update'])->name('battery.subbrand.update');
    Route::post('/battery/subbrand/destroy', [BatterySubbrand::class, 'destroy'])->name('battery.subbrand.destroy');

    // Battery Usage Type
    Route::get('/battery/usage', [BatteryUsage::class, 'index']);
    Route::post('/battery/usage/show', [BatteryUsage::class, 'show'])->name('battery.usage.show');
    Route::get('/battery/usage/create', [BatteryUsage::class, 'create']);
    Route::get('/battery/usage/edit/{id}', [BatteryUsage::class, 'edit'])->name('battery.usage.edit');
    Route::post('/battery/usage/store', [BatteryUsage::class, 'store'])->name('battery.usage.store');
    Route::post('/battery/usage/update', [BatteryUsage::class, 'update'])->name('battery.usage.update');
    Route::post('/battery/usage/destroy', [BatteryUsage::class, 'destroy'])->name('battery.usage.destroy');

    // Technology
    Route::get('/battery/technology/create', [BatteryTechnology::class, 'create']);
    Route::post('/battery/technology/store', [BatteryTechnology::class, 'store']);

    // Orders
    // Quick Quotation
    Route::get('/quotation', [Quotation::class, 'index']);
    Route::get('/find-customer', [Quotation::class, 'findCustomer'])->name('quotation.findCustomer');
    Route::post('/share-form-personal-details', [Quotation::class, 'shareFormPersonalDetails'])->name('quotation.shareFormPersonalDetails');

    //profile
    Route::get('/profile',  [Profile::class, 'index']);
    Route::get('/delete-session-whatsapp', [Profile::class, 'deleteSessionWhatsapp']);

    // Logout
    Route::get('/logout', [Authentication::class, 'logout']);
});

// Auth
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [Authentication::class, 'index'])->name('login');
    Route::post('/auth', [Authentication::class, 'authenticate'])->name('auth.authenticate');
});
