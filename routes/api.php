<?php

use Illuminate\Http\Request;

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

Route::group(['prefix' => 'authentication', 'namespace' => 'Authentication'], function() {
    Route::post('signIn', 'LoadController@loginUser');
    Route::post('signUp', 'LoadController@createUser');
    Route::post('forgotPassword', 'LoadController@sendPasswordRecoveryMail');
    Route::post('resetPassword', 'LoadController@resetPassword');

    /*
    | If the application requires an access code before sign up,
    | something like an OTP being sent to a provided email
    | you can remove the comment on the block below.
    */
    
    /* 
        Route::post('sendCode', 'LoadController@getSignUpCode');
        Route::post('confirmCode', 'LoadController@useSignUpCode');
    */
});

Route::group(['prefix' => 'user', 'namespace' => 'User', 'middleware' => 'jwtAuth'], function() {
    Route::get("/", 'LoadController@fetchOne');
    Route::post('update', 'LoadController@updateUser');
});