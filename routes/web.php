<?php

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