<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Inventory\Inventory;

Route::get('/inventory', [Inventory::class, 'index'])->name('inventory.index')->middleware('permission:view_inventory');
Route::get('/inventory/get/{name}', [Inventory::class, 'getStock'])->name('inventory.get')->middleware('permission:view_inventory');
Route::post('/inventory/details/show', [Inventory::class, 'showDetails'])->name('inventory.details.show')->middleware('permission:view_inventory');
Route::post('/inventory/details/total-qty', [Inventory::class, 'getTotalQty'])->name('inventory.details.total-qty')->middleware('permission:view_inventory');
Route::post('/inventory/show', [Inventory::class, 'show'])->name('inventory.show')->middleware('permission:view_inventory');
Route::post('/inventory/sync-stock', [Inventory::class, 'syncStock'])->name('inventory.sync-stock')->middleware('permission:edit_inventory');
Route::post('/inventory/delete', [Inventory::class, 'delete'])->name('inventory.delete')->middleware('permission:delete_inventory');
Route::get('/inventory/details', [Inventory::class, 'detailsIndex'])->name('inventory.details.index')->middleware('permission:view_inventory');
Route::get('/inventory/details/{id?}', [Inventory::class, 'detailsIndex'])->name('inventory.details')->middleware('permission:view_inventory');
