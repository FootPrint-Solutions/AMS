<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MasterData\Battery\Battery;

Route::get('/battery', [Battery::class, 'index'])->middleware('permission:view_battery')->name('battery.index');
Route::post('/battery/show', [Battery::class, 'show'])->name('battery.show')->middleware('permission:view_battery');
Route::get('/battery/create', [Battery::class, 'create'])->middleware('permission:add_battery');
Route::get('/battery/edit/{id}', [Battery::class, 'edit'])->name('battery.edit')->middleware('permission:edit_battery');
Route::post('/battery/store', [Battery::class, 'store'])->name('battery.store')->middleware('permission:add_battery');
Route::post('/battery/update', [Battery::class, 'update'])->name('battery.update')->middleware('permission:edit_battery');
Route::post('/battery/toggle', [Battery::class, 'updateStatus'])->name('battery.toggle')->middleware('permission:edit_battery');
Route::post('/battery/import', [Battery::class, 'import'])->name('battery.import')->middleware('permission:add_battery');
Route::post('/battery/import/price', [Battery::class, 'importPrice'])->name('battery.import.price')->middleware('permission:add_battery');
Route::post('/battery/export', [Battery::class, 'export'])->name('battery.export')->middleware('permission:view_battery');
Route::post('/battery/get/size', [Battery::class, 'getBatteriesBySizeCategory'])->name('battery.size')->middleware('permission:view_battery');
Route::get('/battery/get/{keyword}', [Battery::class, 'getBatteriesByKeyword'])->name('battery.get')->middleware('permission:view_battery');
Route::post('/battery/compress', [Battery::class, 'compress'])->name('battery.compress')->middleware('permission:view_battery');
