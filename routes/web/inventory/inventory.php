<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Inventory\Inventory;

Route::get('/inventory', [Inventory::class, 'index'])->name('inventory.index')->middleware('permission:view_inventory');
Route::get('/inventory/get/{name}', [Inventory::class, 'getStock'])->name('inventory.get')->middleware('permission:view_inventory');
