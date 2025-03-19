<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MasterData\Battery\BatteryTechnology;

Route::get('/battery/technology', [BatteryTechnology::class, 'index'])->name('battery.Technology.index')->middleware('permission:view_battery');
Route::post('/battery/technology/show', [BatteryTechnology::class, 'show'])->name('battery.technology.show')->middleware('permission:view_battery');
Route::get('/battery/technology/create', [BatteryTechnology::class, 'create'])->middleware('permission:add_battery');
Route::get('/battery/technology/edit/{id}', [BatteryTechnology::class, 'edit'])->name('battery.technology.edit')->middleware('permission:edit_battery');
Route::post('/battery/technology/store', [BatteryTechnology::class, 'store'])->name('battery.technology.store')->middleware('permission:add_battery');
Route::post('/battery/technology/update', [BatteryTechnology::class, 'update'])->name('battery.technology.update')->middleware('permission:edit_battery');
Route::post('/battery/technology/destroy', [BatteryTechnology::class, 'destroy'])->name('battery.technology.destroy')->middleware('permission:delete_battery');
