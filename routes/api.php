<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PostController;



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
//Guest Controller
Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);

Route::get('/posts', [PostController::class, 'index']); 
Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');

Route::group(['middleware' => ['auth:api']], function(){
    //User Route
    Route::group(['prefix' => 'user'], function () {
        Route::post('/update-account', [UserController::class, 'updateAccount']);
        Route::post('/change-password', [UserController::class, 'changePassword']);
        Route::post('/logout', [UserController::class, 'logout']);  
    });

    //Post Route
    Route::post('/posts', [PostController::class, 'store']);
    Route::patch('/posts/{post}', [PostController::class, 'update']);
    Route::delete('/posts/{post}', [PostController::class, 'destroy']);


});









