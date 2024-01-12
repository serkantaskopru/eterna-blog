<?php

use App\Http\Controllers\ProfileController;

use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [\App\Http\Controllers\HomeController::class, 'index']);
Route::get('/get-categories', [\App\Http\Controllers\CategoryController::class, 'categories'])->name('category.fetch');
Route::get('/category/{id}', [\App\Http\Controllers\CategoryController::class, 'view'])->name('category.view');
Route::get('/category-posts/{id}', [\App\Http\Controllers\CategoryController::class, 'posts'])->name('category.posts');
Route::get('/post/{id}', [\App\Http\Controllers\PostController::class, 'view'])->name('post.view');
Route::post('/newsletter-store', [\App\Http\Controllers\NewsletterController::class, 'store'])->name('newsletter.store');



Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'verified']],function(){
    Route::get('/', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
    Route::group(['as' => 'dashboard.post.', 'prefix' => 'post'],function(){
        Route::get('/view', [\App\Http\Controllers\Management\PostController::class, 'view'])->name('view');
        Route::get('/edit/{id}', [\App\Http\Controllers\Management\PostController::class, 'edit'])->name('edit');
        Route::post('/create', [\App\Http\Controllers\Management\PostController::class, 'create'])->name('create');
        Route::post('/update', [\App\Http\Controllers\Management\PostController::class, 'update'])->name('update');
        Route::post('/delete', [\App\Http\Controllers\Management\PostController::class, 'delete'])->name('delete');
    });
    Route::group(['as' => 'dashboard.category.', 'prefix' => 'category'],function(){
        Route::get('/view', [\App\Http\Controllers\Management\CategoryController::class, 'view'])->name('view');
        Route::get('/edit/{id}', [\App\Http\Controllers\Management\CategoryController::class, 'edit'])->name('edit');
        Route::post('/create', [\App\Http\Controllers\Management\CategoryController::class, 'create'])->name('create');
        Route::post('/update', [\App\Http\Controllers\Management\CategoryController::class, 'update'])->name('update');
        Route::post('/delete', [\App\Http\Controllers\Management\CategoryController::class, 'delete'])->name('delete');
    });
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
