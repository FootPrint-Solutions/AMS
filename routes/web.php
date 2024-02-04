<?php

use Illuminate\Support\Facades\Route;

// MASTER DATA
use App\Http\Controllers\Orders\Quotation;
use App\Http\Controllers\MasterData\Battery;
use App\Http\Controllers\MasterData\Company;
use App\Http\Controllers\MasterData\Vehicle;
use App\Http\Controllers\Auth\Authentication;
use App\Http\Controllers\Dashboard\Dashboard;

// ORDERS
use App\Http\Controllers\MasterData\Customer;
use App\Http\Controllers\MasterData\VehicleBrand;

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


// DASHBOARD
Route::get('/', [Dashboard::class, 'index'])->middleware('auth');

// MASTER DATA
// Company
Route::get('/company', [Company::class, 'index'])->middleware('auth');
Route::post('/company/update', [Company::class, 'update'])->middleware('auth');

// Customer
Route::get('/customer', [Customer::class, 'index'])->middleware('auth');
Route::post('/customer/show', [Customer::class, 'show'])->middleware('auth')->name('customer.show');
Route::get('/customer/create', [Customer::class, 'create'])->middleware('auth');
Route::get('/customer/edit/{id}', [Customer::class, 'edit'])->middleware('auth')->name('customer.edit');
Route::post('/customer/store', [Customer::class, 'store'])->middleware('auth')->name('customer.store');
Route::post('/customer/update', [Customer::class, 'update'])->middleware('auth')->name('customer.update');
Route::post('/customer/destroy', [Customer::class, 'destroy'])->middleware('auth')->name('customer.destroy');

// Vehicle
Route::get('/vehicle', [Vehicle::class, 'index'])->middleware('auth');
Route::post('/vehicle/show', [Vehicle::class, 'show'])->middleware('auth')->name('vehicle.show');
Route::get('/vehicle/create', [Vehicle::class, 'create'])->middleware('auth');
Route::get('/vehicle/edit/{id}', [Vehicle::class, 'edit'])->middleware('auth')->name('vehicle.edit');
Route::post('/vehicle/store', [Vehicle::class, 'store'])->middleware('auth')->name('vehicle.store');
Route::post('/vehicle/update', [Vehicle::class, 'update'])->middleware('auth')->name('vehicle.update');
Route::post('/vehicle/destroy', [Vehicle::class, 'destroy'])->middleware('auth')->name('vehicle.destroy');

// Brand
Route::get('/vehicle/brand/create', [VehicleBrand::class, 'create'])->middleware('auth');
Route::post('/vehicle/brand/store', [VehicleBrand::class, 'store'])->middleware('auth')->name('vehicle.brand.store');

// Battery
Route::get('/battery', [Battery::class, 'index'])->middleware('auth');
Route::post('/battery/show', [Battery::class, 'show'])->middleware('auth')->name('battery.show');
Route::get('/battery/create', [Battery::class, 'create'])->middleware('auth');
Route::get('/battery/edit/{id}', [Battery::class, 'edit'])->middleware('auth')->name('battery.edit');
Route::post('/battery/store', [Battery::class, 'store'])->middleware('auth')->name('battery.store');
Route::post('/battery/update', [Battery::class, 'update'])->middleware('auth')->name('battery.update');
Route::post('/battery/destroy', [Battery::class, 'destroy'])->middleware('auth')->name('battery.destroy');

// Orders
// Quick Quotation
Route::get('/quotation', [Quotation::class, 'index'])->middleware('auth');
Route::get('/find-customer', [Quotation::class, 'findCustomer'])->middleware('auth')->name('quotation.findCustomer');
Route::post('/share-form-personal-details', [Quotation::class, 'shareFormPersonalDetails'])->middleware('auth')->name('quotation.shareFormPersonalDetails');

// Auth
Route::get('/login', [Authentication::class, 'index'])->middleware('guest')->name('login');
Route::post('/auth', [Authentication::class, 'authenticate'])->name('auth.authenticate');
Route::get('/logout', [Authentication::class, 'logout'])->middleware('auth');
