<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MasterData\Distributor\DistributorShopTechnician;

Route::get('/distributor/technician', [DistributorShopTechnician::class, 'index'])->name('distributor.technician.index')->middleware('permission:view_distributor');
Route::post('/distributor/technician/show', [DistributorShopTechnician::class, 'show'])->name('distributor.technician.show')->middleware('permission:view_distributor');
Route::get('/distributor/technician/create', [DistributorShopTechnician::class, 'create'])->name('distributor.technician.create')->middleware('permission:add_distributor');
Route::get('/distributor/technician/edit/{id}', [DistributorShopTechnician::class, 'edit'])->name('distributor.technician.edit')->middleware('permission:edit_distributor');
Route::post('/distributor/technician/store', [DistributorShopTechnician::class, 'store'])->name('distributor.technician.store')->middleware('permission:add_distributor');
Route::post('/distributor/technician/update', [DistributorShopTechnician::class, 'update'])->name('distributor.technician.update')->middleware('permission:edit_distributor');
Route::post('/distributor/technician/destroy', [DistributorShopTechnician::class, 'destroy'])->name('distributor.technician.destroy')->middleware('permission:delete_distributor');
