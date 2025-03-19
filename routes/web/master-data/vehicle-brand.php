<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MasterData\Vehicle\VehicleBrand;

Route::get('/vehicle/brand', [VehicleBrand::class, 'index'])->name('vehicle.brand.index')->middleware('permission:view_vehicle');
Route::post('/vehicle/brand/show', [VehicleBrand::class, 'show'])->name('vehicle.brand.show')->middleware('permission:view_vehicle');
Route::get('/vehicle/brand/create', [VehicleBrand::class, 'create'])->middleware('permission:add_vehicle');
Route::get('/vehicle/brand/edit/{id}', [VehicleBrand::class, 'edit'])->name('vehicle.brand.edit')->middleware('permission:edit_vehicle');
Route::post('/vehicle/brand/store', [VehicleBrand::class, 'store'])->name('vehicle.brand.store')->middleware('permission:add_vehicle');
Route::post('/vehicle/brand/update', [VehicleBrand::class, 'update'])->name('vehicle.brand.update')->middleware('permission:edit_vehicle');
Route::post('/vehicle/brand/toggle', [VehicleBrand::class, 'updateStatus'])->name('vehicle.brand.toggle')->middleware('permission:edit_vehicle');
