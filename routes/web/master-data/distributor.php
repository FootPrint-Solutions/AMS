<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MasterData\Distributor\Distributor;

Route::get('/distributor', [Distributor::class, 'index'])->name('distributor.index')->middleware('permission:view_distributor');
Route::post('/distributor/show', [Distributor::class, 'show'])->name('distributor.show')->middleware('permission:view_distributor');
Route::get('/distributor/create', [Distributor::class, 'create'])->middleware('permission:add_distributor');
Route::get('/distributor/edit/{id}', [Distributor::class, 'edit'])->name('distributor.edit')->middleware('permission:edit_distributor');
Route::post('/distributor/store', [Distributor::class, 'store'])->name('distributor.store')->middleware('permission:add_distributor');
Route::post('/distributor/update', [Distributor::class, 'update'])->name('distributor.update')->middleware('permission:edit_distributor');
Route::post('/distributor/toggle', [Distributor::class, 'updateStatus'])->name('distributor.toggle')->middleware('permission:edit_distributor');
