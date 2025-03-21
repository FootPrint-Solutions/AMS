<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Publish\Gallery;

Route::group(['prefix' => 'gallery'], function () {
    Route::get('/', [Gallery::class, 'index'])->name('publish.gallery.index');
    Route::post('/show', [Gallery::class, 'show'])->name('publish.gallery.show.ajax');
    Route::get('/create', [Gallery::class, 'create'])->name('publish.gallery.create');
    Route::post('/store', [Gallery::class, 'store'])->name('publish.gallery.store');
    Route::get('/{id}', [Gallery::class, 'show'])->name('publish.gallery.show');
    Route::get('/edit/{id}', [Gallery::class, 'edit'])->name('publish.gallery.edit');
    Route::post('/update', [Gallery::class, 'update'])->name('publish.gallery.update');
    Route::post('/destroy', [Gallery::class, 'destroy'])->name('publish.gallery.destroy');
});
