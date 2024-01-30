<?php

use App\Http\Controllers\Dashboard\Dashboard;
use App\Http\Controllers\MasterData\Battery;
use App\Http\Controllers\MasterData\Company;
use App\Http\Controllers\MasterData\Customer;
use App\Http\Controllers\MasterData\Vehicle;
use App\Http\Controllers\Orders\Quotation;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
});

// DASHBOARD
Route::get('/', [Dashboard::class, 'index']);


// MASTER DATA
// Company
Route::get('/company', [Company::class, 'index']);
Route::post('/company/update', [Company::class, 'update']);

// Customer
Route::get('/customer', [Customer::class, 'index']);
Route::post('/customer/show', [Customer::class, 'show']);
Route::get('/customer/create', [Customer::class, 'create']);
Route::get('/customer/edit/{id}', [Customer::class, 'edit']);
Route::post('/customer/store', [Customer::class, 'store']);
Route::post('/customer/update', [Customer::class, 'update']);
Route::post('/customer/destroy', [Customer::class, 'destroy']);

// Vehicle
Route::get('/vehicle', [Vehicle::class, 'index']);
Route::get('/vehicle/create', [Vehicle::class, 'create']);

// Battery
Route::get('/battery', [Battery::class, 'index']);
Route::get('/battery/create', [Battery::class, 'create']);

// Orders
// Quick Quotation
Route::get('/quotation', [Quotation::class, 'index']);
