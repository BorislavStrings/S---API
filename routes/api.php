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

Route::get('/', 'UserController@index');
Route::post('user/login', 'UserController@login');
Route::post('user/register', 'UserController@register');

Route::group(['middleware' => ['auth:api']], function () {

});

Route::group(['middleware' => ['jwt.auth']], function () {
    Route::get('user/get', 'UserController@get');

    Route::post('user/cv/set', 'UserController@setCV');
    Route::get('user/cv/get', 'UserController@getCV');

    Route::post('user/locations/set', 'UserController@setLocations');
    Route::get('user/locations/get', 'UserController@getLocations');

    Route::get('user/appliedOffers/get', 'UserController@getUserAppliedOffers');

    Route::get('skills/all', 'SkillsController@all');
    Route::post('user/skills/set', 'UserController@setSkills');
    Route::get('user/skills/get', 'UserController@getUserSkills');

    Route::get('offers/allSorted', 'JobOffersController@allSorted');
    Route::get('offers/all', 'JobOffersController@all');
    Route::get('offer/{offer_id}', 'JobOffersController@getOffer');
    Route::get('offers/apply/{offer_id}', 'JobOffersController@apply');
    Route::get('offers/apply/cancel/{offer_id}', 'JobOffersController@disapply');

    Route::get('statistics/set', 'StatisticController@setStatistic');
    Route::get('statistics/delete', 'StatisticController@deleteDevice');
});

Route::get('user/token_refresh', 'UserController@refreshToken');
