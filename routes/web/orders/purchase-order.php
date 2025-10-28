<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Orders\PurchaseOrder;

/*
|--------------------------------------------------------------------------
| Purchase Order Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for purchase order functionality.
|
*/

Route::prefix('purchase-order')->group(function () {
    Route::get('/', [PurchaseOrder::class, 'index'])->name('purchase-order.index');
    Route::get('/create', [PurchaseOrder::class, 'create'])->name('purchase-order.create');
    Route::post('/store', [PurchaseOrder::class, 'store'])->name('purchase-order.store');
    Route::post('/show', [PurchaseOrder::class, 'show'])->name('purchase-order.show');
    Route::get('/edit/{id}', [PurchaseOrder::class, 'edit'])->name('purchase-order.edit');
    Route::post('/update', [PurchaseOrder::class, 'update'])->name('purchase-order.update');
    Route::post('/destroy', [PurchaseOrder::class, 'destroy'])->name('purchase-order.destroy');
    Route::post('/get-data', [PurchaseOrder::class, 'getData'])->name('purchase-order.get-data');
});
