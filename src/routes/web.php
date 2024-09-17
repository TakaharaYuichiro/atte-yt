<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\AdminController;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegisterdUserController;
use App\Http\Controllers\AuthenticatedSessionController;

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

// 主要なページ
Route::get('/', [ContactController::class, 'index']);
Route::post('/confirm', [ContactController::class, 'confirm']);
Route::post('/confirm/store', [ContactController::class, 'store']);
Route::get('/thanks', [ContactController::class, 'thanks']);

// ログイン関連のページ
Route::post('/register',[RegisterdUserController::class,'store']);
Route::post('/login',[AuthenticatedSessionController::class,'store']);
Route::post('/logout',[AuthenticatedSessionController::class,'destroy']);

// 管理者ページ
Route::middleware('auth')->group(function () {
    Route::get('/admin', [AdminController::class, 'index']);
    Route::get('/admin/search', [AdminController::class, 'search']);
    Route::get('/admin/export', [AdminController::class, 'export']);
    Route::patch('/admin/show_detail', [AdminController::class, 'showDetail']);
    Route::delete('/admin/delete_data', [AdminController::class, 'deleteData']);
});

// ＊＊＊＊＊＊＊＊デバッグ中はログイン機能をオフにしておく　　リリース時注意！！！！！！！！！！！！！
// Route::get('/admin', [AdminController::class, 'index']);
// Route::get('/admin/search', [AdminController::class, 'search']);
// Route::get('/admin/export', [AdminController::class, 'export']);
// Route::patch('/admin/show_detail', [AdminController::class, 'showDetail']);
// Route::delete('/admin/delete_data', [AdminController::class, 'deleteData']);
