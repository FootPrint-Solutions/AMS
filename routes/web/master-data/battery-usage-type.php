<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MasterData\Battery\BatteryUsage;

Route::get('/battery/usage', [BatteryUsage::class, 'index'])->name('battery.usage.index')->middleware('permission:view_battery');
Route::post('/battery/usage/show', [BatteryUsage::class, 'show'])->name('battery.usage.show')->middleware('permission:view_battery');
Route::get('/battery/usage/create', [BatteryUsage::class, 'create'])->middleware('permission:add_battery');
Route::get('/battery/usage/edit/{id}', [BatteryUsage::class, 'edit'])->name('battery.usage.edit')->middleware('permission:edit_battery');
Route::post('/battery/usage/store', [BatteryUsage::class, 'store'])->name('battery.usage.store')->middleware('permission:add_battery');
Route::post('/battery/usage/update', [BatteryUsage::class, 'update'])->name('battery.usage.update')->middleware('permission:edit_battery');
Route::post('/battery/usage/destroy', [BatteryUsage::class, 'destroy'])->name('battery.usage.destroy')->middleware('permission:delete_battery');
