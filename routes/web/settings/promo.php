<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Settings\Promo;

Route::get('/promo', [Promo::class, 'index'])->name('promo.index')->middleware('permission:view_promo');
Route::post('/promo/show', [Promo::class, 'show'])->name('promo.show')->middleware('permission:view_promo');
Route::post('/promo/show/dashboard', [Promo::class, 'showDashboard'])->name('promo.showDashboard')->middleware('permission:view_promo');
Route::get('/promo/create', [Promo::class, 'create'])->middleware('permission:add_promo');
Route::get('/promo/edit/{id}', [Promo::class, 'edit'])->name('promo.edit')->middleware('permission:edit_promo');
Route::post('/promo/store', [Promo::class, 'store'])->name('promo.store')->middleware('permission:add_promo');
Route::post('/promo/update', [Promo::class, 'update'])->name('promo.update')->middleware('permission:edit_promo');
Route::post('/promo/toggle', [Promo::class, 'updateStatus'])->name('promo.toggle')->middleware('permission:edit_promo');
Route::post('/promo/update-price-retail/{id}', [Promo::class, 'updatePriceRetail'])->name('promo.updatePriceRetail')->middleware('permission:edit_promo');
