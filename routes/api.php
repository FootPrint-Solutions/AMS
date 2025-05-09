<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// CONTROLLER
use App\Http\Controllers\Api\Filter;
use App\Http\Controllers\Api\Battery;
use App\Http\Controllers\Api\Gallery;
use App\Http\Controllers\Api\Faq;
use App\Http\Controllers\Api\Review;
use App\Http\Controllers\Api\SalesOnline;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// FILTER API
Route::get('filter/brand', [Filter::class, 'brand']);
Route::get('filter/brand/{brand}', [Filter::class, 'brandFind']);
Route::get('filter/model/{model}', [Filter::class, 'modelFind']);
Route::get('filter/battery', [Filter::class, 'battery']);
Route::get('filter/battery/{battery}', [Filter::class, 'batteryFind']);

// SEARCH API
Route::get('search/battery/{batteryName}', [Filter::class, 'searchBattery']);

// BATTERY API
Route::get('battery/random', [Battery::class, 'getRandomBattery']);
Route::get('battery/category', [Battery::class, 'getBatteryCategory']);
Route::get('battery/category/{category}', [Battery::class, 'findBatteriesByCategory']);

// Gallery API
Route::get('gallery', [Gallery::class, 'getAllGallery']);

// FAQ API
Route::get('faq', [Faq::class, 'getAllFaq']);

// REVIEW API
Route::get('review', [Review::class, 'getAllReview']);

// SALES ONLINE API
Route::post('sales-online/receive-data', [SalesOnline::class, 'receiveData']);
