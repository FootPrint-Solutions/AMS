<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MasterData\Distributor\DistributorShop;
use App\Http\Controllers\MasterData\Distributor\DistributorShopBattery;

Route::get('/distributor/shop', [DistributorShop::class, 'index'])->name('distributor.shop.index')->middleware('permission:view_distributor');
Route::post('/distributor/shop/show', [DistributorShop::class, 'show'])->name('distributor.shop.show')->middleware('permission:view_distributor');
Route::get('/distributor/shop/create', [DistributorShop::class, 'create'])->name('distributor.shop.create')->middleware('permission:add_distributor');
Route::get('/distributor/shop/edit/{id}', [DistributorShop::class, 'edit'])->name('distributor.shop.edit')->middleware('permission:edit_distributor');
Route::post('/distributor/shop/store', [DistributorShop::class, 'store'])->name('distributor.shop.store')->middleware('permission:add_distributor');
Route::post('/distributor/shop/update', [DistributorShop::class, 'update'])->name('distributor.shop.update')->middleware('permission:edit_distributor');
Route::post('/distributor/shop/toggle', [DistributorShop::class, 'updateStatus'])->name('distributor.shop.toggle')->middleware('permission:edit_distributor');
Route::post('/distributor/shop/battery/show', [DistributorShopBattery::class, 'show'])->name('distributor.shop.battery.show')->middleware('permission:view_distributor');
Route::get('/distributor/shop/battery/create/{shopId}/{distributorId}', [DistributorShopBattery::class, 'create'])->name('distributor.shop.battery.create')->middleware('permission:add_distributor');
Route::get('/distributor/shop/battery/edit/{id}', [DistributorShopBattery::class, 'edit'])->name('distributor.shop.battery.edit')->middleware('permission:edit_distributor');
Route::post('/distributor/shop/battery/store', [DistributorShopBattery::class, 'store'])->name('distributor.shop.battery.store')->middleware('permission:add_distributor');
Route::post('/distributor/shop/battery/store/batch/{shopId}', [DistributorShopBattery::class, 'storeBatch'])->name('distributor.shop.battery.store.batch')->middleware('permission:add_distributor');
Route::post('/distributor/shop/battery/update', [DistributorShopBattery::class, 'update'])->name('distributor.shop.battery.update')->middleware('permission:edit_distributor');
Route::post('/distributor/shop/battery/destroy', [DistributorShopBattery::class, 'destroy'])->name('distributor.shop.battery.destroy')->middleware('permission:delete_distributor');
Route::get('/distributor/shop/account/{shopId}', [DistributorShop::class, 'account'])->name('distributor.shop.account')->middleware('permission:view_distributor');
Route::get('/distributor/shop/{shopId}/battery-import-template', [DistributorShop::class, 'downloadTemplate'])->name('distributor.shop.battery.import.template')->middleware('permission:view_distributor');
Route::post('/distributor/shop/{shopId}/battery-import', [DistributorShop::class, 'import'])->name('distributor.shop.battery.import')->middleware('permission:edit_distributor');
Route::post('/distributor/shop/commission/show', [DistributorShop::class, 'showCommissions'])->name('distributor.shop.commission.show')->middleware('permission:view_distributor');
Route::post('/distributor/shop/commission/update', [DistributorShop::class, 'updateCommission'])->name('distributor.shop.commission.update')->middleware('permission:edit_distributor');
Route::post('/distributor/shop/commission/destroy', [DistributorShop::class, 'destroyCommission'])->name('distributor.shop.commission.destroy')->middleware('permission:edit_distributor');
Route::post('/distributor/shop/{id}/commission/delete-all', [DistributorShop::class, 'destroyAllCommission'])->name('distributor.shop.commission.destroy_all')->middleware('permission:edit_distributor');
