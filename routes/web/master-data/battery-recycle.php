<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MasterData\Battery\BatteryRecycle;

Route::get('/battery-recycle', [BatteryRecycle::class, 'index'])->name('battery.recycle.index')->middleware('permission:view_battery_recycle');
Route::post('/battery-recycle/show', [BatteryRecycle::class, 'show'])->name('battery.recycle.show')->middleware('permission:view_battery_recycle');
Route::get('/battery-recycle/create', [BatteryRecycle::class, 'create'])->middleware('permission:add_battery_recycle');
Route::get('/battery-recycle/edit/{id}', [BatteryRecycle::class, 'edit'])->name('battery.recycle.edit')->middleware('permission:edit_battery_recycle');
Route::post('/battery-recycle/store', [BatteryRecycle::class, 'store'])->name('battery.recycle.store')->middleware('permission:add_battery_recycle');
Route::post('/battery-recycle/update', [BatteryRecycle::class, 'update'])->name('battery.recycle.update')->middleware('permission:edit_battery_recycle');
Route::post('/battery-recycle/destroy', [BatteryRecycle::class, 'destroy'])->name('battery.recycle.destroy')->middleware('permission:delete_battery_recycle');

Route::get('/battery-recycle/get/{keyword}', [BatteryRecycle::class, 'getBatteryRecycle'])->name('battery.recycle.get');
