<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Settings\UserManager;

Route::get('/user-manager', [UserManager::class, 'index'])->name('user-manager.index')->middleware('permission:view_user_manager');
Route::post('/user-manager/show', [UserManager::class, 'show'])->name('user-manager.show')->middleware('permission:view_user_manager');
Route::get('/user-manager/edit/{id}', [UserManager::class, 'edit'])->name('user-manager.edit')->middleware('permission:edit_user_manager');
Route::post('/user-manager/destroy', [UserManager::class, 'destroy'])->name('user-manager.destroy')->middleware('permission:delete_user_manager');
Route::post('/user-manager/update', [UserManager::class, 'update'])->name('user-manager.update')->middleware('permission:edit_user_manager');
Route::get('/user-manager/create', [UserManager::class, 'create'])->middleware('permission:add_user_manager');
Route::post('/user-manager/store', [UserManager::class, 'store'])->name('user-manager.store')->middleware('permission:add_user_manager');
