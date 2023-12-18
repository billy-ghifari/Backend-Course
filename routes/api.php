<?php

use App\Http\Controllers\C_admin;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\C_Auth;
use App\Http\Controllers\C_blog;
use App\Http\Controllers\C_category;
use App\Http\Controllers\C_categoryblog;
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
    Route::post('loginadmin', 'loginadmin');
    Route::post('register', 'register');
    Route::post('/forget-password', 'forgetPassword');
    Route::post('/reset-password/{token}', 'resetPassword');
});

Route::middleware('auth:sanctum')->group(function () {

    //auth
    Route::controller(C_Auth::class)->group(function () {
        Route::post('/logout', 'logout')->middleware('forStatus:aktif');
    });

    //category
    Route::controller(C_category::class)->group(function () {
        Route::post('createcat', 'create')->middleware('forStatus:aktif', 'forRole:superadmin,admin,mentor');
        Route::put('/editcat/{id}', 'update')->middleware('forStatus:aktif', 'forRole:superadmin,admin,mentor');
        Route::delete('/deletecat/{id}', 'destroy')->middleware('forStatus:aktif', 'forRole:superadmin,admin,mentor');
    });

    //categoryblog
    Route::controller(C_categoryblog::class)->group(function () {
        Route::post('createcatblog', 'create')->middleware('forStatus:aktif', 'forRole:superadmin,admin,mentor');
        Route::get('paginatecatblog', 'index')->middleware('forStatus:aktif', 'forRole:superadmin,admin,mentor,siswa');
        Route::get('getallcatblog', 'getallcatblog')->middleware('forStatus:aktif', 'forRole:superadmin,admin,mentor,siswa');
        Route::delete('/deletecatblog/{id}', 'destroy')->middleware('forStatus:aktif', 'forRole:superadmin,admin,mentor');
    });

    //blog
    Route::controller(C_blog::class)->group(function () {
        Route::post('createblog', 'post_blog')->middleware('forStatus:aktif', 'forRole:superadmin,admin,mentor');
        Route::get('paginateblog', 'index')->middleware('forStatus:aktif', 'forRole:superadmin,admin,mentor,siswa');
        Route::get('paginatesomeblog', 'getall')->middleware('forStatus:aktif');
        Route::get('allblog', 'allblog')->middleware('forStatus:aktif');
        Route::get('getallblog', 'getallblog')->middleware('forStatus:aktif');
        Route::get('/getoneblog/{id}', 'getblog')->middleware('forStatus:aktif');
        Route::put('/editblog/{id}', 'update')->middleware('forStatus:aktif', 'forRole:superadmin,admin,mentor');
        Route::delete('/deleteblog/{id}', 'destroy')->middleware('forStatus:aktif', 'forRole:superadmin,admin,mentor');
    });

    //kelas
    Route::controller(C_kelas::class)->group(function () {
        Route::post('createkelas', 'post_kelas')->middleware('forStatus:aktif', 'forRole:mentor');
        Route::get('paginatesomekelas', 'getall_course')->middleware('forStatus:aktif',);
        Route::get('paginatekelas', 'index')->middleware('forStatus:aktif',);
        Route::get('getonekelas', 'getone_kelas')->middleware('forStatus:aktif',);
        Route::get('getkelas', 'get_kelas')->middleware('forStatus:aktif',);
        Route::get('getallkelas', 'getallkelas')->middleware('forStatus:aktif',);
        Route::get('/getkelasbyid/{id}', 'getClassById')->middleware('forStatus:aktif',);
        Route::put('/editkelas/{id}', 'update')->middleware('forStatus:aktif', 'forRole:mentor');
        Route::delete('/deletekelas/{id}', 'destroy')->middleware('forStatus:aktif', 'forRole:mentor');
    });

    //materi
    Route::controller(C_materi::class)->group(function () {
        Route::post('createmateri', 'post_materi')->middleware('forStatus:aktif');
        Route::get('getallmateri', 'getallmateri')->middleware('forStatus:aktif',);
        Route::get('/getmateri/{id}', 'getmateri')->middleware('forStatus:aktif',);
        Route::put('/editmateri/{id}', 'update')->middleware('forStatus:aktif');
        Route::delete('/deletemateri/{id}', 'destroy')->middleware('forStatus:aktif');
    });

    //siswa action
    Route::controller(C_admin::class)->group(function () {
        Route::get('getallsiswa', 'index')->middleware('forStatus:aktif');
        Route::get('getsiswa', 'get_siswa')->middleware('forStatus:aktif');
        Route::get('getallonsiswa', 'siswa_on')->middleware('forStatus:aktif');
        Route::post('/activationsiswa/{id}', 'activationsiswa')->middleware('forStatus:aktif');
        Route::post('/nonactivationsiswa/{id}', 'nonactivationsiswa')->middleware('forStatus:aktif');
        Route::post('/activationkelas/{id}', 'activationkelas')->middleware('forStatus:aktif');
    });

    //crud admin
    Route::controller(C_admin::class)->group(function () {
        Route::post('registeradmin', 'registeradmin')->middleware('forStatus:aktif');
        Route::get('getalladmin', 'alladmin')->middleware('forStatus:aktif');
        Route::get('/getprofile/{id}', 'get_profile')->middleware('forStatus:aktif');
        Route::get('/getuuid/{uuid}', 'getiduser')->middleware('forStatus:aktif');
        Route::put('/editadmin/{id}', 'updateadmin')->middleware('forStatus:aktif');
        Route::delete('/deleteadmin/{id}', 'destroyadmin')->middleware('forStatus:aktif');
    });

    //crud mentor
    Route::controller(C_admin::class)->group(function () {
        Route::get('getallmentor', 'allmentor')->middleware('forStatus:aktif');
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
