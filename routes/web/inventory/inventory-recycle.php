<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Inventory\InventoryRecycle;

Route::get('/inventory/recycle', [InventoryRecycle::class, 'index'])->name('inventory.recycle.index')->middleware('permission:view_inventory');
Route::post('/inventory/recycle/show', [InventoryRecycle::class, 'show'])->name('inventory.recycle.show')->middleware('permission:view_inventory');
Route::get('/inventory/recycle/create', [InventoryRecycle::class, 'create'])->middleware('permission:add_inventory');
Route::get('/inventory/recycle/edit/{id}', [InventoryRecycle::class, 'edit'])->name('inventory.recycle.edit')->middleware('permission:edit_inventory');
Route::post('/inventory/recycle/store', [InventoryRecycle::class, 'store'])->name('inventory.recycle.store')->middleware('permission:add_inventory');
Route::post('/inventory/recycle/update', [InventoryRecycle::class, 'update'])->name('inventory.recycle.update')->middleware('permission:edit_inventory');
Route::post('/inventory/recycle/destroy', [InventoryRecycle::class, 'destroy'])->name('inventory.recycle.destroy')->middleware('permission:delete_inventory');
Route::post('/inventory/recycle/sold-out', [InventoryRecycle::class, 'soldOut'])->name('inventory.recycle.sold-out')->middleware('permission:delete_inventory');
