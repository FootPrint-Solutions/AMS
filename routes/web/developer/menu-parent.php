<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Developer\MenuParent;

Route::get('/menu/parent/create', [MenuParent::class, 'create'])->middleware('permission:add_menu_manager');
Route::post('/menu/parent/store', [MenuParent::class, 'store'])->name('menu.parent.store')->middleware('permission:add_menu_manager');
Route::get('/logs', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index']);
