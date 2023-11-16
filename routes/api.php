<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\C_Auth;
use App\Http\Controllers\C_blog;
use App\Http\Controllers\C_category;
use App\Http\Controllers\C_kelas;
use App\Http\Controllers\C_materi;
use App\Http\Controllers\C_Review;
use Illuminate\Support\Facades\Password;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

//auth
Route::post('login', [C_Auth::class, 'login']);
Route::post('register', [C_Auth::class, 'register']);
Route::post('/forget-password', [C_Auth::class, 'forgetPassword'])->name('forget.password');
Route::get('/reset-password/{token}', [C_Auth::class, 'resetPassword'])->name('password.reset');
Route::post('/reset-password', [C_Auth::class, 'resetPassword'])->name('password.update');
Route::post('/logout', [C_Auth::class, 'logout']);

//category
Route::post('createcat', [C_category::class, 'create']);
Route::put('/editcat/{id}', [C_category::class, 'update']);
Route::delete('/deletecat/{id}', [C_category::class, 'destroy']);

//blog
Route::post('createblog', [C_blog::class, 'post_blog']);
Route::get('paginateblog', [C_blog::class, 'index']);
Route::put('/editblog/{id}', [C_blog::class, 'update']);
Route::delete('/deleteblog/{id}', [C_blog::class, 'destroy']);

//kelas
Route::post('createkelas', [C_kelas::class, 'post_kelas']);
Route::get('paginatekelas', [C_kelas::class, 'index']);
Route::put('/editkelas/{id}', [C_kelas::class, 'update']);
Route::delete('/deletekelas/{id}', [C_kelas::class, 'destroy']);

//materi
Route::post('createmateri', [C_materi::class, 'post_materi']);
Route::put('/editmateri/{id}', [C_materi::class, 'update']);
Route::delete('/deletemateri/{id}', [C_materi::class, 'destroy']);

//review
Route::post('createreview', [C_Review::class, 'post_review']);
Route::delete('/deletereview/{id}', [C_Review::class, 'destroy']);
