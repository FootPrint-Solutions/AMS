<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Orders\SalesConsignment;

/*
|--------------------------------------------------------------------------
| Sales Consignment Routes
|--------------------------------------------------------------------------
|
| Here is where you can register sales consignment routes for your application.
|
*/

Route::get('/sales-consignment', [SalesConsignment::class, 'index'])->name('sales-consignment.index')->middleware('permission:view_sales_consignment');
Route::get('/sales-consignment/show', [SalesConsignment::class, 'show'])->name('sales-consignment.show-datatable')->middleware('permission:view_sales_consignment');
Route::get('/sales-consignment/create/{ids}', [SalesConsignment::class, 'create'])->name('sales-consignment.create')->middleware('permission:add_sales_consignment');
Route::post('/sales-consignment/store', [SalesConsignment::class, 'store'])->name('sales-consignment.store')->middleware('permission:add_sales_consignment');
Route::get('/sales-consignment/createnoids', [SalesConsignment::class, 'createNoIds'])->name('sales-consignment.create-no-ids')->middleware('permission:add_sales_consignment');

Route::get('/sales-consignment/{id}', [SalesConsignment::class, 'detail'])->name('sales-consignment.detail')->middleware('permission:view_sales_consignment');
Route::put('/sales-consignment/post', [SalesConsignment::class, 'post'])->name('sales-consignment.post')->middleware('permission:edit_sales_consignment');
Route::delete('/sales-consignment/destroy', [SalesConsignment::class, 'destroy'])->name('sales-consignment.destroy')->middleware('permission:delete_sales_consignment');
