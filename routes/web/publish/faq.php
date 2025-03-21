<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Publish\Faq;

Route::group(['prefix' => 'faq'], function () {
    Route::get('/', [Faq::class, 'index'])->name('publish.faq.index');
    Route::post('/show', [Faq::class, 'show'])->name('publish.faq.show.ajax');
    Route::get('/create', [Faq::class, 'create'])->name('publish.faq.create');
    Route::post('/store', [Faq::class, 'store'])->name('publish.faq.store');
    Route::get('/{id}', [Faq::class, 'show'])->name('publish.faq.show');
    Route::get('/edit/{id}', [Faq::class, 'edit'])->name('publish.faq.edit');
    Route::post('/update', [Faq::class, 'update'])->name('publish.faq.update');
    Route::post('/destroy', [Faq::class, 'destroy'])->name('publish.faq.destroy');
});
