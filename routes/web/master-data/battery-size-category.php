<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MasterData\Battery\BatterySize;

Route::get('/battery/size', [BatterySize::class, 'index'])->name('battery.size.index')->middleware('permission:view_battery');
Route::post('/battery/size/show', [BatterySize::class, 'show'])->name('battery.size.show')->middleware('permission:view_battery');
Route::get('/battery/size/create', [BatterySize::class, 'create'])->middleware('permission:add_battery');
Route::get('/battery/size/edit/{id}', [BatterySize::class, 'edit'])->name('battery.size.edit')->middleware('permission:edit_battery');
Route::post('/battery/size/store', [BatterySize::class, 'store'])->name('battery.size.store')->middleware('permission:add_battery');
Route::post('/battery/size/update', [BatterySize::class, 'update'])->name('battery.size.update')->middleware('permission:edit_battery');
Route::post('/battery/size/destroy', [BatterySize::class, 'destroy'])->name('battery.size.destroy')->middleware('permission:delete_battery');
