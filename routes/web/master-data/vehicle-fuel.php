<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MasterData\Vehicle\VehicleFuel;

Route::get('/vehicle/fuel', [VehicleFuel::class, 'index'])->name('vehicle.fuel.index')->middleware('permission:view_vehicle');
Route::post('/vehicle/fuel/show', [VehicleFuel::class, 'show'])->name('vehicle.fuel.show')->middleware('permission:view_vehicle');
Route::get('/vehicle/fuel/create', [VehicleFuel::class, 'create'])->middleware('permission:add_vehicle');
Route::get('/vehicle/fuel/edit/{id}', [VehicleFuel::class, 'edit'])->name('vehicle.fuel.edit')->middleware('permission:edit_vehicle');
Route::post('/vehicle/fuel/store', [VehicleFuel::class, 'store'])->name('vehicle.fuel.store')->middleware('permission:add_vehicle');
Route::post('/vehicle/fuel/update', [VehicleFuel::class, 'update'])->name('vehicle.fuel.update')->middleware('permission:edit_vehicle');
Route::post('/vehicle/fuel/toggle', [VehicleFuel::class, 'updateStatus'])->name('vehicle.fuel.toggle')->middleware('permission:edit_vehicle');
