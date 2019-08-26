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

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', function ($api) {
    $api->post('login', 'App\Http\Api\Auth\LoginController@login')
        ->middleware('localization');
    $api->post('register', 'App\Http\Api\Auth\RegisterController@register')
        ->middleware('localization');
    $api->group(['middleware' => 'api.auth'], function ($api) {
        $api->get('user', 'App\Http\Api\Controllers\UsersController@index');
    });
    $api->group([
        'middleware' => 'api',
        'prefix'     => 'password',
    ], function ($api) {
        $api->post('create',
            'App\Http\Api\Auth\PasswordResetController@create');
        $api->get('find/{token}',
            'App\Http\Api\Auth\PasswordResetController@find');
        $api->post('reset', 'App\Http\Api\Auth\PasswordResetController@reset');
    });
});
