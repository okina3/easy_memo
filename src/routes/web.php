<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\User\ImageController;
use App\Http\Controllers\User\MemoController;
use App\Http\Controllers\User\ShareSettingController;
use App\Http\Controllers\User\TagController;
use App\Http\Controllers\User\TrashedMemoController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('user.welcome');
// });

Route::get('/dashboard', function () {
    return view('user.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth:users')->group(function () {
    //メモ管理画面
    Route::controller(MemoController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::post('store', 'store')->name('store');
        Route::get('show/{memo}', 'show')->name('show');
        Route::get('edit/{memo}', 'edit')->name('edit');
        Route::patch('update', 'update')->name('update');
        Route::delete('destroy', 'destroy')->name('destroy');
    });
});

//タグ管理画面
Route::controller(TagController::class)->prefix('tag')->group(function () {
    Route::get('/', 'index')->name('tag.index');
    Route::post('/store', 'store')->name('tag.store');
    Route::delete('/destroy', 'destroy')->name('tag.destroy');
});

//画像管理画面
Route::controller(ImageController::class)->prefix('image')->group(function () {
    Route::get('/', 'index')->name('image.index');
    Route::get('/create', 'create')->name('image.create');
    Route::post('/store', 'store')->name('image.store');
    Route::get('/show/{image}', 'show')->name('image.show');
    Route::delete('/destroy', 'destroy')->name('image.destroy');
});

//共有メモ管理画面
Route::controller(ShareSettingController::class)->prefix('share-setting')->group(function () {
    Route::get('/', 'index')->name('share-setting.index');
    Route::post('/store', 'store')->name('share-setting.store');
    Route::get('/show/{share}', 'show')->name('share-setting.show');
    Route::get('/edit/{share}', 'edit')->name('share-setting.edit');
    Route::patch('/update', 'update')->name('share-setting.update');
    Route::delete('/destroy', 'destroy')->name('share-setting.destroy');
});

//ソフトデリートしたメモ画面
Route::controller(TrashedMemoController::class)->prefix('trashed-memo')->group(function () {
    Route::get('/', 'index')->name('trashed-memo.index');
    Route::patch('/undo', 'undo')->name('trashed-memo.undo');
    Route::delete('/destroy', 'destroy')->name('trashed-memo.destroy');
});

// Route::middleware('auth')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });

require __DIR__ . '/auth.php';
