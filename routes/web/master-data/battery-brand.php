<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MasterData\Battery\BatteryBrand;

Route::get('/battery/brand', [BatteryBrand::class, 'index'])->name('battery.brand.index')->middleware('permission:view_battery');
Route::post('/battery/brand/show', [BatteryBrand::class, 'show'])->name('battery.brand.show')->middleware('permission:view_battery');
Route::get('/battery/brand/create', [BatteryBrand::class, 'create'])->middleware('permission:add_battery');
Route::get('/battery/brand/edit/{id}', [BatteryBrand::class, 'edit'])->name('battery.brand.edit')->middleware('permission:edit_battery');
Route::post('/battery/brand/store', [BatteryBrand::class, 'store'])->name('battery.brand.store')->middleware('permission:add_battery');
Route::post('/battery/brand/update', [BatteryBrand::class, 'update'])->name('battery.brand.update')->middleware('permission:edit_battery');
Route::post('/battery/brand/destroy', [BatteryBrand::class, 'destroy'])->name('battery.brand.destroy')->middleware('permission:delete_battery');
