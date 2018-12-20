<?php

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


Route::group(['prefix' => 'auth'], function () {

    Route::post('login', 'Api\AuthController@login');
    Route::post('signup', 'Api\AuthController@signUp');

    Route::group(['middleware' => 'auth:api'], function () {
        Route::get('logout', 'Api\AuthController@logout');
    });
});

Route::group(['middleware' => 'auth:api'], function () {

    Route::apiResource('users', 'Api\UsersController');
    Route::apiResource('cards', 'Api\CardsController');
    Route::apiResource('cases', 'Api\CasesController');
});

