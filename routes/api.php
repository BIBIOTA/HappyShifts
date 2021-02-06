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

Route::post('/googlelogin', PostController::class . '@apiGoogleLogin');

Route::post('/checkuser', PostController::class . '@apiCheckuser');

Route::post('/signup', PostController::class . '@apiSignup');

Route::get('/member', PostController::class . '@apiMember');

Route::get('/session', PostController::class . '@apiSession');

Route::delete('/session', PostController::class . '@apiClearSession');

Route::get('/events', PostController::class . '@apiUploadEvent');

Route::get('/getevents', PostController::class . '@apiGetEvent');

Route::get('/deleteevents', PostController::class . '@apiDeleteEvent');

Route::get('/uploadshiftlist', PostController::class . '@apiUploadShiftlist');

Route::get('/getshiftlist', PostController::class . '@apiGetShiftlist');

Route::get('/deleteshiftlist', PostController::class . '@apiDeletesShiftlist');

Route::get('/updateshiftlist', PostController::class . '@apiUpdateShiftlist');

