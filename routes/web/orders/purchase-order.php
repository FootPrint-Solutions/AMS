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
    Route::get('/create-recycle', [PurchaseOrder::class, 'createRecycle'])->name('purchase-order.create-recycle');
    Route::post('/store', [PurchaseOrder::class, 'store'])->name('purchase-order.store');
    Route::post('/show', [PurchaseOrder::class, 'show'])->name('purchase-order.show');
    Route::get('/edit/{id}', [PurchaseOrder::class, 'edit'])->name('purchase-order.edit');
    Route::post('/update', [PurchaseOrder::class, 'update'])->name('purchase-order.update');
    Route::post('/destroy', [PurchaseOrder::class, 'destroy'])->name('purchase-order.destroy');
    Route::post('/get-data', [PurchaseOrder::class, 'getData'])->name('purchase-order.get-data');
    Route::post('/get-print', [PurchaseOrder::class, 'getPrint'])->name('purchase-order.get-print');
    Route::get('/print/{ids}', [PurchaseOrder::class, 'print'])->name('purchase-order.print');
    Route::post('/post', [PurchaseOrder::class, 'post'])->name('purchase-order.post');

    Route::get('/vendor/get', [PurchaseOrder::class, 'getVendor'])->name('purchase-order.vendor.get');
    Route::get('/shipto/get', [PurchaseOrder::class, 'getShipTo'])->name('purchase-order.shipto.get');
    Route::post('/sales-order/find', [PurchaseOrder::class, 'findShop'])->name('purchase-order.shop.find');
    Route::post('/sales-order/list', [PurchaseOrder::class, 'getSalesOrderList'])->name('purchase-order.sales-order.list');
    Route::post('/sales-order/details', [PurchaseOrder::class, 'getSalesOrderDetails'])->name('purchase-order.sales-order.details');
});
