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

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', function ($api) {
    $api->group([
        'middleware' => [
            'localization',
        ]
    ], function ($api) {

        $api->post('/login', 'App\Http\Api\Auth\LoginController@login');
        $api->post('/register', 'App\Http\Api\Auth\RegisterController@register');

        $api->group([
            'middleware' => [
                'api',
                'api.auth',
                'LastActivityUser'
            ],
        ], function ($api) {
            $api->group([
                'prefix' => 'users'
            ], function ($api) {
                $api->get('/', 'App\Http\Api\Controllers\UserController@index');
                $api->post(
                    '/createCharacter',
                    'App\Http\Api\Auth\RegisterController@createCharacter'
                );
                $api->post(
                    '/mailConfirmCreate',
                    'App\Http\Api\Controllers\UserController@mailConfirmCreate'
                );
                $api->post(
                    '/mailConfirm/{token}',
                    'App\Http\Api\Controllers\UserController@mailConfirm'
                );
            });

            $api->group([
                'prefix' => 'friends'
            ], function ($api) {
                $api->post('/{id}', 'App\Http\Api\Controllers\FriendController@add')->where(['id' => '[0-9]+']);
                $api->get('/', 'App\Http\Api\Controllers\FriendController@getFriends');
                $api->delete(
                    '/{id}',
                    'App\Http\Api\Controllers\FriendController@deleteFriend'
                )->where(['id' => '[0-9]+']);
            });

            $api->group([
                'prefix' => 'messages'
            ], function ($api) {
                $api->post('/{id}', 'App\Http\Api\Controllers\MessageController@sendMessage');
                $api->delete(
                    '/{id}',
                    'App\Http\Api\Controllers\MessageController@deleteMessage'
                )->where(['id' => '[0-9]+']);
                $api->get(
                    '/{receiverId}/{offset?}',
                    'App\Http\Api\Controllers\MessageController@chatHistory'
                )->where([
                    'receiverId' => '[0-9]+',
                    'offset' => '[0-9]+'
                ]);
            });
        });
    });

    $api->group([
        'middleware' => 'api',
        'prefix' => 'password',
    ], function ($api) {
        $api->post(
            'create',
            'App\Http\Api\Auth\PasswordResetController@create'
        );
        $api->get(
            'find/{token}',
            'App\Http\Api\Auth\PasswordResetController@find'
        );
        $api->post('reset', 'App\Http\Api\Auth\PasswordResetController@reset');
    });
});
