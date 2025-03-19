<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Publish\Review;


Route::group(['prefix' => 'review'], function () {
    Route::get('/', [Review::class, 'index'])->name('publish.review.index');
    Route::post('/show', [Review::class, 'show'])->name('publish.review.show.ajax');
    Route::get('/create', [Review::class, 'create'])->name('publish.review.create');
    Route::post('/store', [Review::class, 'store'])->name('publish.review.store');
    Route::get('/{id}', [Review::class, 'show'])->name('publish.review.show');
    Route::get('/edit/{id}', [Review::class, 'edit'])->name('publish.review.edit');
    Route::post('/update', [Review::class, 'update'])->name('publish.review.update');
    Route::post('/destroy', [Review::class, 'destroy'])->name('publish.review.destroy');
});
