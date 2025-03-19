<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MasterData\Customer\Customer;

Route::get('/customer', [Customer::class, 'index'])->name('customer.index')->middleware('permission:view_customer');
Route::post('/customer/show', [Customer::class, 'show'])->name('customer.show')->middleware('permission:view_customer');
Route::get('/customer/create', [Customer::class, 'create'])->middleware('permission:add_customer');
Route::get('/customer/edit/{id}', [Customer::class, 'edit'])->name('customer.edit')->middleware('permission:edit_customer');
Route::post('/customer/store', [Customer::class, 'store'])->name('customer.store')->middleware('permission:add_customer');
Route::post('/customer/update', [Customer::class, 'update'])->name('customer.update')->middleware('permission:edit_customer');
Route::post('/customer/toggle', [Customer::class, 'updateStatus'])->name('customer.toggle')->middleware('permission:edit_customer');
