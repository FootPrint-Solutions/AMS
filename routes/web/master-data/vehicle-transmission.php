<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MasterData\Vehicle\VehicleTransmission;

Route::get('/vehicle/transmission', [VehicleTransmission::class, 'index'])->name('vehicle.transmission.index')->middleware('permission:view_vehicle');
Route::post('/vehicle/transmission/show', [VehicleTransmission::class, 'show'])->name('vehicle.transmission.show')->middleware('permission:view_vehicle');
Route::get('/vehicle/transmission/create', [VehicleTransmission::class, 'create'])->middleware('permission:add_vehicle');
Route::get('/vehicle/transmission/edit/{id}', [VehicleTransmission::class, 'edit'])->name('vehicle.transmission.edit')->middleware('permission:edit_vehicle');
Route::post('/vehicle/transmission/store', [VehicleTransmission::class, 'store'])->name('vehicle.transmission.store')->middleware('permission:add_vehicle');
Route::post('/vehicle/transmission/update', [VehicleTransmission::class, 'update'])->name('vehicle.transmission.update')->middleware('permission:edit_vehicle');
Route::post('/vehicle/transmission/toggle', [VehicleTransmission::class, 'updateStatus'])->name('vehicle.transmission.toggle')->middleware('permission:edit_vehicle');
