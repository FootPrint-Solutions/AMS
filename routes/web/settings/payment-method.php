<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Settings\PaymentMethod;

Route::get('/payment', [PaymentMethod::class, 'index'])->name('payment.index')->middleware('permission:view_payment_method');
Route::post('/payment/show', [PaymentMethod::class, 'show'])->name('payment.show')->middleware('permission:view_payment_method');
Route::get('/payment/create', [PaymentMethod::class, 'create'])->middleware('permission:add_payment_method');
Route::get('/payment/edit/{id}', [PaymentMethod::class, 'edit'])->name('payment.edit')->middleware('permission:edit_payment_method');
Route::post('/payment/store', [PaymentMethod::class, 'store'])->name('payment.store')->middleware('permission:add_payment_method');
Route::post('/payment/update', [PaymentMethod::class, 'update'])->name('payment.update')->middleware('permission:edit_payment_method');
Route::post('/payment/toggle', [PaymentMethod::class, 'updateStatus'])->name('payment.toggle')->middleware('permission:edit_payment_method');
Route::post('/payment/destroy', [PaymentMethod::class, 'destroy'])->name('payment.destroy')->middleware('permission:delete_payment_method');
