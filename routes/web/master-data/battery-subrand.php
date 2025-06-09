<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MasterData\Battery\BatterySubbrand;


Route::get('/battery/subbrand', [BatterySubbrand::class, 'index'])->name('battery.subbrand.index')->middleware('permission:view_battery');
Route::post('/battery/subbrand/show', [BatterySubbrand::class, 'show'])->name('battery.subbrand.show')->middleware('permission:view_battery');
Route::get('/battery/subbrand/create', [BatterySubbrand::class, 'create'])->middleware('permission:add_battery');
Route::get('/battery/subbrand/edit/{id}', [BatterySubbrand::class, 'edit'])->name('battery.subbrand.edit')->middleware('permission:edit_battery');
Route::post('/battery/subbrand/store', [BatterySubbrand::class, 'store'])->name('battery.subbrand.store')->middleware('permission:add_battery');
Route::post('/battery/subbrand/update', [BatterySubbrand::class, 'update'])->name('battery.subbrand.update')->middleware('permission:edit_battery');
Route::post('/battery/subbrand/destroy', [BatterySubbrand::class, 'destroy'])->name('battery.subbrand.destroy')->middleware('permission:delete_battery');
Route::post('/battery/subbrand/toggle-visibility', [BatterySubbrand::class, 'toggleVisibility'])->name('battery.subbrand.toggle-visibility')->middleware('permission:edit_battery');
