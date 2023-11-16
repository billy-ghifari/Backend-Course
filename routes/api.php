<?php

use App\Http\Controllers\C_admin;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\C_Auth;
use App\Http\Controllers\C_blog;
use App\Http\Controllers\C_category;
use App\Http\Controllers\C_kelas;
use App\Http\Controllers\C_materi;

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

//category
Route::post('createcat', [C_category::class, 'create']);
Route::put('/editcat/{id}', [C_category::class, 'update']);
Route::delete('/deletecat/{id}', [C_category::class, 'destroy']);

//blog
Route::post('createblog', [C_blog::class, 'post_blog']);
Route::put('/editblog/{id}', [C_blog::class, 'update']);
Route::delete('/deleteblog/{id}', [C_blog::class, 'destroy']);

//kelas
Route::post('createkelas', [C_kelas::class, 'post_kelas']);
Route::put('/editkelas/{id}', [C_kelas::class, 'update']);
Route::delete('/deletekelas/{id}', [C_kelas::class, 'destroy']);

//materi
Route::post('createmateri', [C_materi::class, 'post_materi']);
Route::put('/editmateri/{id}', [C_materi::class, 'update']);
Route::delete('/deletemateri/{id}', [C_materi::class, 'destroy']);

//admin action
Route::get('getallsiswa', [C_admin::class, 'index']);
Route::post('/activationsiswa/{id}', [C_admin::class, 'activationsiswa']);
Route::post('/nonactivationsiswa/{id}', [C_admin::class, 'nonactivationsiswa']);

Route::post('/activationkelas/{id}', [C_admin::class, 'activationkelas']);
