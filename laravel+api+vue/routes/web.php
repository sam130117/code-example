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

Route::get('/', 'HomeController@index');

Route::group(['prefix' => 'cards'], function () {
    Route::get('/{card_id}', 'CardsController@index');
    Route::get('/', 'CardsController@index');
});

Route::group(['prefix' => 'cases'], function () {
    Route::get('/', 'CasesController@index');
    Route::get('/{case_id}', 'CasesController@index');
});

Route::group(['prefix' => 'users'], function () {
    Route::get('/', 'UsersController@index');
});


/* Storage */

Route::group(['prefix' => 'storage'], function () {
    Route::group(['prefix' => 'cards'], function () {
        Route::get('/', 'CardsController@getCards');
        Route::get('/{card_id}', 'CardsController@getCard');
        Route::post('/', 'CardsController@store');
        Route::put('/{card_id}', 'CardsController@store');
        Route::delete('/{card_id}', 'CardsController@destroy');
    });
    Route::group(['prefix' => 'cases'], function () {
        Route::get('/', 'CasesController@getCases');
        Route::get('/{id}', 'CasesController@getCase');
        Route::post('/', 'CasesController@store');
        Route::put('/{case_id}', 'CasesController@store');
        Route::delete('/{case_id}', 'CasesController@destroy');
    });
});




