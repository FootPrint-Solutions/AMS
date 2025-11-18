<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Accounting\Billing;


Route::get('/billing/', [Billing::class, 'index'])->name('billing.index')->middleware('permission:view_billing');
Route::post('/billing/show', [Billing::class, 'show'])->name('billing.show')->middleware('permission:view_billing');
Route::get('/billing/create', [Billing::class, 'create'])->middleware('permission:add_billing');
Route::get('/billing/edit/{id}', [Billing::class, 'edit'])->name('billing.edit')->middleware('permission:edit_billing');
Route::post('/billing/store', [Billing::class, 'store'])->name('billing.store')->middleware('permission:add_billing');
Route::post('/billing/update', [Billing::class, 'update'])->name('billing.update')->middleware('permission:edit_billing');
Route::post('/billing/destroy', [Billing::class, 'destroy'])->name('billing.destroy')->middleware('permission:delete_billing');
Route::post('/billing/toggle', [Billing::class, 'toggle'])->name('billing.toggle')->middleware('permission:edit_billing');

Route::get('/billing/shipto/get', [Billing::class, 'getShipTo'])->name('billing.shipto.get')->middleware('permission:view_billing');
Route::get('/billing/vendor/get', [Billing::class, 'getVendors'])->name('billing.vendor.get')->middleware('permission:view_billing');
Route::post('/billing/orders/get', [Billing::class, 'getOrdersData'])->name('billing.orders.get')->middleware('permission:view_billing');
Route::post('/billing/orders/add-temp', [Billing::class, 'addOrdersToTemp'])->name('billing.orders.add-temp')->middleware('permission:view_billing');
