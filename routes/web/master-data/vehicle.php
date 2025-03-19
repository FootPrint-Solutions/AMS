<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MasterData\Vehicle\Vehicle;

Route::get('/vehicle', [Vehicle::class, 'index'])->name('vehicle.index')->middleware('permission:view_vehicle');
Route::post('/vehicle/show', [Vehicle::class, 'show'])->name('vehicle.show')->middleware('permission:view_vehicle');
Route::get('/vehicle/create', [Vehicle::class, 'create'])->middleware('permission:add_vehicle');
Route::get('/vehicle/edit/{id}', [Vehicle::class, 'edit'])->name('vehicle.edit')->middleware('permission:edit_vehicle');
Route::post('/vehicle/store', [Vehicle::class, 'store'])->name('vehicle.store')->middleware('permission:add_vehicle');
Route::post('/vehicle/update', [Vehicle::class, 'update'])->name('vehicle.update')->middleware('permission:edit_vehicle');
Route::post('/vehicle/toggle', [Vehicle::class, 'updateStatus'])->name('vehicle.toggle')->middleware('permission:edit_vehicle');
Route::post('/vehicle/import', [Vehicle::class, 'import'])->name('vehicle.import')->middleware('permission:add_vehicle');
