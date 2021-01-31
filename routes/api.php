<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\PostController;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login', PostController::class . '@apiLogin');

Route::post('/checkuser', PostController::class . '@apiCheckuser');

Route::post('/signup', PostController::class . '@apiSignup');

Route::get('/member', PostController::class . '@apiMember');

Route::get('/session', PostController::class . '@apiSession');

Route::delete('/session', PostController::class . '@apiClearSession');