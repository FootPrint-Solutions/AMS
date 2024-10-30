<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// CONTROLLER
use App\Http\Controllers\Api\Filter;

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
