<?php

use App\Http\Controllers\C_admin;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\C_Auth;
use App\Http\Controllers\C_blog;
use App\Http\Controllers\C_category;
use App\Http\Controllers\C_kelas;
use App\Http\Controllers\C_materi;
use App\Http\Controllers\C_mentor;
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

Route::controller(C_Auth::class)->group(function () {
    Route::post('login', 'login');
    Route::post('register', 'register');
    Route::post('/forget-password', 'forgetPassword');
    Route::get('/reset-password/{token}', 'resetpassword');
});

Route::middleware('auth:sanctum')->group(function () {

    //auth
    Route::controller(C_Auth::class)->group(function () {
        Route::post('/reset-password', 'resetpassword')->middleware('forStatus:aktif');
        Route::post('/logout', 'logout')->middleware('forStatus:aktif');
    });

    //category
    Route::controller(C_category::class)->group(function () {
        Route::post('createcat', 'create')->middleware('forStatus:aktif', 'forRole:superadmin,admin,mentor');
        Route::put('/editcat/{id}', 'update')->middleware('forStatus:aktif', 'forRole:superadmin,admin,mentor');
        Route::delete('/deletecat/{id}', 'destroy')->middleware('forStatus:aktif', 'forRole:superadmin,admin,mentor');
    });

    //blog
    Route::controller(C_blog::class)->group(function () {
        Route::post('createblog', 'post_blog')->middleware('forStatus:aktif', 'forRole:superadmin,admin,mentor');
        Route::get('paginateblog', 'index')->middleware('forStatus:aktif', 'forRole:superadmin,admin,mentor');
        Route::put('/editblog/{id}', 'update')->middleware('forStatus:aktif', 'forRole:superadmin,admin,mentor');
        Route::delete('/deleteblog/{id}', 'destroy')->middleware('forStatus:aktif', 'forRole:superadmin,admin,mentor');
    });

    //kelas
    Route::controller(C_kelas::class)->group(function () {
        Route::post('createkelas', 'post_kelas')->middleware('forStatus:aktif', 'forRole:mentor');
        Route::get('paginatekelas', 'index')->middleware('forStatus:aktif', 'forRole:mentor');
        Route::put('/editkelas/{id}', 'update')->middleware('forStatus:aktif', 'forRole:mentor');
        Route::delete('/deletekelas/{id}', 'destroy')->middleware('forStatus:aktif', 'forRole:mentor');
    });

    //materi
    Route::controller(C_materi::class)->group(function () {
        Route::post('createmateri', 'post_materi')->middleware('forStatus:aktif');
        Route::put('/editmateri/{id}', 'update')->middleware('forStatus:aktif');
        Route::delete('/deletemateri/{id}', 'destroy')->middleware('forStatus:aktif');
    });

    //admin action
    Route::controller(C_admin::class)->group(function () {
        Route::get('getallsiswa', 'index')->middleware('forStatus:aktif');
        Route::post('/activationsiswa/{id}', 'activationsiswa')->middleware('forStatus:aktif');
        Route::post('/nonactivationsiswa/{id}', 'nonactivationsiswa')->middleware('forStatus:aktif');
        Route::post('/activationkelas/{id}', 'activationkelas')->middleware('forStatus:aktif');
    });

    //crud admin
    Route::controller(C_admin::class)->group(function () {
        Route::post('loginadmin', 'loginadmin')->middleware('forStatus:aktif');
        Route::post('registeradmin', 'registeradmin')->middleware('forStatus:aktif');
        Route::put('/editadmin/{id}', 'updateadmin')->middleware('forStatus:aktif');
        Route::delete('/deleteadmin/{id}', 'destroyadmin')->middleware('forStatus:aktif');
    });

    //crud mentor
    Route::controller(C_admin::class)->group(function () {
        Route::post('registermentor', 'registermentor')->middleware('forStatus:aktif');
        Route::put('/editmentor/{id}', 'updatementor')->middleware('forStatus:aktif');
        Route::delete('/deletementor/{id}', 'destroymentor')->middleware('forStatus:aktif');
    });

    //review
    Route::controller(C_Review::class)->group(function () {
        Route::post('createreview', 'post_review')->middleware('forStatus:aktif');
        Route::delete('/deletereview/{id}', 'destroy')->middleware('forStatus:aktif');
    });
});

Route::any('/{any}', function () {
    return response()->json(['message' => 'Not Found'], 404);
})->where('any', '.*');
