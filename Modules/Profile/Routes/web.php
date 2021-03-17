<?php
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



Route::prefix('profile')->group(function() {
    Route::get('/', [Controllers\MainController::class, 'index']);
});

Route::get('logout', [Controllers\MainController::class, 'logout']);
Route::group(['prefix' => LaravelLocalization::setLocale(), 'middleware' => ['localeCookieRedirect']], function(){
    Route::get('login', [Controllers\MainController::class, 'index'])->name('login');
    Route::get('register', [Controllers\MainController::class, 'index']);
    Route::get('password/email', [Controllers\MainController::class, 'index']);
    Route::get('password/reset', [Controllers\MainController::class, 'index']);
});

Route::group(['middleware' => ['auth']], function() {
    Route::post('update_user_info', [Controllers\ProfileController::class, 'updateUserInfo']);
});

//Auth
Route::post('login', [Controllers\ProfileController::class, 'login']);
Route::post('register', [Controllers\ProfileController::class, 'register']);
Route::post('password/email', [Controllers\ProfileController::class, 'sendResetPasswordLink']);
Route::post('password/reset', [Controllers\ProfileController::class, 'reset']);
Route::post('get_notifications', [Controllers\ProfileController::class, 'getNotifications']);
Route::post('mark_all_notifications', [Controllers\ProfileController::class, 'markAllNotifications']);
Route::post('get_invitation_code', [Controllers\ProfileController::class, 'getInvitationCode']);