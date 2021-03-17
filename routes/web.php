<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers;

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

Route::group(['prefix' => LaravelLocalization::setLocale(), 'middleware' => ['localeCookieRedirect']], function(){
    //Basic
    Route::get('/', [Controllers\MainController::class, 'index']);
    Route::get('terms', [Controllers\MainController::class, 'index']);
    Route::get('privacy', [Controllers\MainController::class, 'index']);
    Route::get('cookies', [Controllers\MainController::class, 'index']);
    Route::get('contact', [Controllers\MainController::class, 'index']);
    Route::get('suggestions', [Controllers\MainController::class, 'index']);
    Route::get('about', [Controllers\MainController::class, 'index']);
    Route::get('faqs', [Controllers\MainController::class, 'index']);
    Route::get('connect-to-call', [Controllers\MainController::class, 'index']);
});

Route::group(['middleware' => ['auth']], function() {
    Route::get('auth-logout', [Controllers\ProfileController::class, 'logout']);

    //Videocall
    Route::post('send_peer_offer', [Controllers\ClassesController::class, 'sendPeerOffer']);
    Route::post('send_peer_answer', [Controllers\ClassesController::class, 'sendPeerAnswer']);
    Route::post('send_ice_candidate', [Controllers\ClassesController::class, 'sendIceCandidate']);
});

Route::get('test', [Controllers\MainController::class, 'index']);
Route::get('test2', [Controllers\MainController::class, 'test']);

