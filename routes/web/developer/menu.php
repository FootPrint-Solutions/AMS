<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Developer\Menu;


Route::get('/menu', [Menu::class, 'index'])->name('menu.index')->middleware('permission:view_menu_manager');
Route::post('/menu/show', [Menu::class, 'show'])->name('menu.show')->middleware('permission:view_menu_manager');
Route::get('/menu/create', [Menu::class, 'create'])->middleware('permission:add_menu_manager');
Route::get('/menu/edit/{id}', [Menu::class, 'edit'])->name('menu.edit')->middleware('permission:edit_menu_manager');
Route::post('/menu/store', [Menu::class, 'store'])->name('menu.store')->middleware('permission:add_menu_manager');
Route::post('/menu/update', [Menu::class, 'update'])->name('menu.update')->middleware('permission:edit_menu_manager');
Route::post('/menu/destroy', [Menu::class, 'destroy'])->name('menu.destroy')->middleware('permission:delete_menu_manager');
Route::get('/menu/refresh', [Menu::class, 'refresh'])->name('menu.refresh')->middleware('permission:view_menu_manager');
Route::get('/menu/get/parent/{id}', [Menu::class, 'getMenu'])->name('menu.getMenu')->middleware('permission:view_menu_manager');
