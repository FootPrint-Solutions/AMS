<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Publish\DataBattery;

Route::get('/data-battery', [DataBattery::class, 'index'])->name('data-battery.index')->middleware('permission:view_battery_(_online_)');
Route::post('/data-battery/sync-woocommerce', [DataBattery::class, 'syncWooCommerce'])->name('data-battery.syncWooCommerce')->middleware('permission:view_battery_(_online_)');
Route::post('/data-battery/sync-category', [DataBattery::class, 'syncCategory'])->name('data-battery.syncCategory')->middleware('permission:view_battery_(_online_)');
Route::post('/data-battery/sync-product', [DataBattery::class, 'syncProduct'])->name('data-battery.syncProduct')->middleware('permission:view_battery_(_online_)');
Route::post('/data-battery/view-details', [DataBattery::class, 'viewDetails'])->name('data-battery.viewDetails')->middleware('permission:view_battery_(_online_)');
Route::post('/data-battery/send-product', [DataBattery::class, 'sendProduct'])->name('data-battery.sendProduct')->middleware('permission:view_battery_(_online_)');
Route::post('/data-battery/count-category', [DataBattery::class, 'countCategory'])->name('data-battery.countCategory')->middleware('permission:view_battery_(_online_)');
Route::post('/data-battery/send-category-partially', [DataBattery::class, 'sendCategoryPartially'])->name('data-battery.sendCategoryPartially')->middleware('permission:view_battery_(_online_)');
Route::post('/data-battery/count-product', [DataBattery::class, 'countProduct'])->name('data-battery.countProduct')->middleware('permission:view_battery_(_online_)');
Route::post('/data-battery/send-product-partially', [DataBattery::class, 'sendProductPartially'])->name('data-battery.sendProductPartially')->middleware('permission:view_battery_(_online_)');
Route::get('/data-battery/export/csv', [DataBattery::class, 'exportCsv'])->name('data-battery.exportCsv')->middleware('permission:view_battery_(_online_)');
