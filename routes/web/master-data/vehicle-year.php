<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MasterData\Vehicle\VehicleYear;

Route::get('/vehicle/year', [VehicleYear::class, 'index'])->name('vehicle.year.index')->middleware('permission:view_vehicle');
Route::post('/vehicle/year/show', [VehicleYear::class, 'show'])->name('vehicle.year.show')->middleware('permission:view_vehicle');
Route::get('/vehicle/year/create', [VehicleYear::class, 'create'])->middleware('permission:add_vehicle');
Route::get('/vehicle/year/edit/{id}', [VehicleYear::class, 'edit'])->name('vehicle.year.edit')->middleware('permission:edit_vehicle');
Route::post('/vehicle/year/store', [VehicleYear::class, 'store'])->name('vehicle.year.store')->middleware('permission:add_vehicle');
Route::post('/vehicle/year/update', [VehicleYear::class, 'update'])->name('vehicle.year.update')->middleware('permission:edit_vehicle');
Route::post('/vehicle/year/toggle', [VehicleYear::class, 'updateStatus'])->name('vehicle.year.toggle')->middleware('permission:edit_vehicle');
