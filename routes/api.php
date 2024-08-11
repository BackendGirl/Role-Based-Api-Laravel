<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::post('register',[UserController::class,'register']);
Route::post('login',[UserController::class,'login'])->name('login');
Route::post('forget-password',[UserController::class,'forgetPassword']);
Route::post('verify-otp',[UserController::class,'verifyOtp']);


Route::group(['middleware'=>'auth:sanctum'],function(){
    Route::post('update-profile',[UserController::class,'updateProfile']);
    Route::post('update-password',[UserController::class,'updatePassword']);
    Route::get('get-profile',[UserController::class,'getProfile']);

    Route::group(['middleware'=>'IsAdmin'],function(){
        Route::get('get-all-users',[AdminController::class,'getAllUsers']);
        Route::get('get-user/{id}',[AdminController::class,'getUser']);
    });
});