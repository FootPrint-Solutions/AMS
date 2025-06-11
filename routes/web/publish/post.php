<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Publish\Post;

Route::group(['prefix' => 'post'], function () {
    Route::get('/', [Post::class, 'index'])->name('publish.post.index');
    Route::post('/show', [Post::class, 'show'])->name('publish.post.show.ajax');
    Route::get('/create', [Post::class, 'create'])->name('publish.post.create');
    Route::post('/store', [Post::class, 'store'])->name('publish.post.store');
    Route::get('/{id}', [Post::class, 'show'])->name('publish.post.show');
    Route::get('/edit/{id}', [Post::class, 'edit'])->name('publish.post.edit');
    Route::post('/update', [Post::class, 'update'])->name('publish.post.update');
    Route::post('/destroy', [Post::class, 'destroy'])->name('publish.post.destroy');

    Route::post('/category/store', [Post::class, 'storeCategory'])->name('publish.post.category.store');
    Route::post('/tag/store', [Post::class, 'storeTag'])->name('publish.post.tag.store');
});
