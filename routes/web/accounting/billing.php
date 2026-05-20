<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Accounting\Billing;


Route::get('/billing/', [Billing::class, 'index'])->name('billing.index')->middleware('permission:view_billing');
Route::post('/billing/show', [Billing::class, 'show'])->name('billing.show')->middleware('permission:view_billing');
Route::get('/billing/items/{id}', [Billing::class, 'getBillingItems'])->name('billing.items')->middleware('permission:view_billing');
Route::get('/billing/create', [Billing::class, 'create'])->name('billing.create')->middleware('permission:add_billing');
Route::get('/billing/create-purchase', [Billing::class, 'createPurchase'])->name('billing.create-purchase')->middleware('permission:add_billing');
Route::get('/billing/edit/{id}', [Billing::class, 'edit'])->name('billing.edit')->middleware('permission:edit_billing');
Route::get('/billing/data/{id}', [Billing::class, 'getBillingData'])->name('billing.data')->middleware('permission:view_billing');
Route::post('/billing/store', [Billing::class, 'store'])->name('billing.store')->middleware('permission:add_billing');
Route::post('/billing/update', [Billing::class, 'update'])->name('billing.update')->middleware('permission:edit_billing');
Route::post('/billing/destroy', [Billing::class, 'destroy'])->name('billing.destroy')->middleware('permission:delete_billing');
Route::post('/billing/toggle', [Billing::class, 'toggle'])->name('billing.toggle')->middleware('permission:edit_billing');
Route::post('/billing/post', [Billing::class, 'post'])->name('billing.post')->middleware('permission:edit_billing');
Route::post('/billing/check-missing-accounts', [Billing::class, 'checkMissingAccounts'])->name('billing.check-missing-accounts')->middleware('permission:edit_billing');
Route::post('/billing/update-accounts-and-post', [Billing::class, 'updateAccountsAndPost'])->name('billing.update-accounts-and-post')->middleware('permission:edit_billing');
Route::get('/billing/print/{id}', [Billing::class, 'print'])->name('billing.print')->middleware('permission:view_billing');
Route::get('/billing/print-receipt/{id}', [Billing::class, 'printReceipt'])->name('billing.print-receipt')->middleware('permission:view_billing');

Route::get('/billing/shipto/get', [Billing::class, 'getShipTo'])->name('billing.shipto.get')->middleware('permission:view_billing');
Route::get('/billing/vendor/get', [Billing::class, 'getVendors'])->name('billing.vendor.get')->middleware('permission:view_billing');
Route::post('/billing/orders/get', [Billing::class, 'getOrdersData'])->name('billing.orders.get')->middleware('permission:view_billing');
Route::post('/billing/purchase-orders/get', [Billing::class, 'getPurchaseOrdersData'])->name('billing.purchase-orders.get')->middleware('permission:view_billing');
Route::post('/billing/purchase-orders/sales/get', [Billing::class, 'getSalesPurchaseOrdersData'])->name('billing.sales-purchase-orders.get')->middleware('permission:view_billing');
Route::post('/billing/orders/add-temp', [Billing::class, 'addOrdersToTemp'])->name('billing.orders.add-temp')->middleware('permission:view_billing');
Route::get('/billing/order/expense/{id}', [Billing::class, 'getOrderExpense'])->name('billing.order.expense')->middleware('permission:view_billing');
Route::post('/billing/expenses/save', [Billing::class, 'saveExpenses'])->name('billing.expenses.save')->middleware('permission:add_billing|edit_billing');
