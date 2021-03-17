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

Route::group(['prefix' => LaravelLocalization::setLocale(), 'middleware' => ['localeCookieRedirect']], function(){
    Route::group(['middleware' => ['auth']], function() {
        Route::get('/class/{id}', [Controllers\MainController::class, 'index']);
        Route::get('/class/{id}/call', [Controllers\MainController::class, 'index']);
    });
});

Route::group(['middleware' => ['auth']], function() {
    //Course
    Route::post('/upload_course_file', [Controllers\AcademyController::class, 'uploadCourseFile']);

    //Subject
    Route::post('/save_subject', [Controllers\AcademyController::class, 'saveSubject']);
    Route::post('/get_calendar_events/{timezone}', [Controllers\AcademyController::class, 'getCalendarEvents']);
    Route::post('/upload_subject_file', [Controllers\AcademyController::class, 'uploadSubjectFile']);
    

    //Class
    Route::post('/get_class_event', [Controllers\ClassesController::class, 'getClassEvent']);
    Route::post('/save_new_class', [Controllers\ClassesController::class, 'saveNewClass']);
    Route::post('/upload_class_file', [Controllers\ClassesController::class, 'uploadClassFile']);

    //Call
    Route::post('/get_peer_offer', [Controllers\ClassesController::class, 'getPeerOffer']);
    Route::post('/save_peer_offer', [Controllers\ClassesController::class, 'savePeerOffer']);
    Route::post('/start_ot_session', [Controllers\ClassesController::class, 'startOTSession']);
});
