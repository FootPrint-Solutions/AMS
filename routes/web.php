<?php

use Illuminate\Support\Facades\Route;

// DASHBOARD
use App\Http\Controllers\Dashboard\Dashboard;

// MASTER DATA
use App\Http\Controllers\MasterData\Company\Company;
use App\Http\Controllers\MasterData\Customer\Customer;
use App\Http\Controllers\MasterData\Vehicle\Vehicle;
use App\Http\Controllers\MasterData\Vehicle\VehicleBrand;
use App\Http\Controllers\MasterData\Battery\Battery;
use App\Http\Controllers\MasterData\Battery\BatteryBrand;
use App\Http\Controllers\MasterData\Battery\BatterySubbrand;
use App\Http\Controllers\MasterData\Battery\BatteryTechnology;
use App\Http\Controllers\MasterData\Battery\BatteryUsage;
use App\Http\Controllers\MasterData\Battery\BatterySize;
use App\Http\Controllers\MasterData\Distributor\Distributor;
use App\Http\Controllers\MasterData\Distributor\DistributorShop;
use App\Http\Controllers\MasterData\Distributor\ShopTechnician;

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
    Route::get('/company', [Company::class, 'index'])->name('company.index');
    Route::post('/company/update', [Company::class, 'update'])->name('company.update');

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
    Route::post('/vehicle/import', [Vehicle::class, 'import'])->name('vehicle.import');

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
    Route::post('/battery/import', [Battery::class, 'import'])->name('battery.import');

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

    // Battery Technology
    Route::get('/battery/technology', [BatteryTechnology::class, 'index']);
    Route::post('/battery/technology/show', [BatteryTechnology::class, 'show'])->name('battery.technology.show');
    Route::get('/battery/technology/create', [BatteryTechnology::class, 'create']);
    Route::get('/battery/technology/edit/{id}', [BatteryTechnology::class, 'edit'])->name('battery.technology.edit');
    Route::post('/battery/technology/store', [BatteryTechnology::class, 'store'])->name('battery.technology.store');
    Route::post('/battery/technology/update', [BatteryTechnology::class, 'update'])->name('battery.technology.update');
    Route::post('/battery/technology/destroy', [BatteryTechnology::class, 'destroy'])->name('battery.technology.destroy');

    // Battery Size Category
    Route::get('/battery/size', [BatterySize::class, 'index']);
    Route::post('/battery/size/show', [BatterySize::class, 'show'])->name('battery.size.show');
    Route::get('/battery/size/create', [BatterySize::class, 'create']);
    Route::get('/battery/size/edit/{id}', [BatterySize::class, 'edit'])->name('battery.size.edit');
    Route::post('/battery/size/store', [BatterySize::class, 'store'])->name('battery.size.store');
    Route::post('/battery/size/update', [BatterySize::class, 'update'])->name('battery.size.update');
    Route::post('/battery/size/destroy', [BatterySize::class, 'destroy'])->name('battery.size.destroy');

    // Distributor
    Route::get('/distributor', [Distributor::class, 'index']);
    Route::post('/distributor/show', [Distributor::class, 'show']);
    Route::get('/distributor/create', [Distributor::class, 'create']);
    Route::get('/distributor/edit/{id}', [Distributor::class, 'edit']);
    Route::post('/distributor/store', [Distributor::class, 'store']);
    Route::post('/distributor/update', [Distributor::class, 'update']);
    Route::post('/distributor/destroy', [Distributor::class, 'destroy']);

    // Distributor Shop
    Route::get('/distributor/shop', [DistributorShop::class, 'index']);
    Route::get('/distributor/shop/create', [DistributorShop::class, 'create']);

    // Shop Technician
    Route::get('/distributor/technician', [ShopTechnician::class, 'index']);
    Route::get('/distributor/technician/create', [ShopTechnician::class, 'create']);

    // Orders
    // Quick Quotation
    Route::get('/quotation', [Quotation::class, 'index']);
    Route::get('/find-customer', [Quotation::class, 'findCustomer'])->name('quotation.findCustomer');
    Route::post('/share-form-personal-details', [Quotation::class, 'shareFormPersonalDetails'])->name('quotation.shareFormPersonalDetails');

    //profile
    Route::get('/profile',  [Profile::class, 'index']);
    Route::post('/profile/update', [Profile::class, 'update']);
    Route::post('/profile/picture/update', [Profile::class, 'updateProfilePicture']);
    Route::post('/profile/password/update', [Profile::class, 'updatePassword']);
    Route::get('/delete-session-whatsapp', [Profile::class, 'deleteSessionWhatsapp']);

    // Logout
    Route::get('/logout', [Authentication::class, 'logout']);
});

// Auth
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [Authentication::class, 'index'])->name('login');
    Route::post('/auth', [Authentication::class, 'authenticate'])->name('auth.authenticate');
});
