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
        $api->post('/refresh', 'App\Http\Api\Auth\LoginController@refresh');

        $api->group([
            'middleware' => [
                'api',
                'jwt.auth',
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
                'middleware' => [
                    'has.character'
                ],
            ], function ($api) {

                $api->group([
                    'prefix' => 'inventory'
                ], function ($api) {
                    $api->get('/', 'App\Http\Api\Controllers\InventoryController@getInventory');
                    $api->post(
                        '/item/{item}/equip',
                        'App\Http\Api\Controllers\InventoryController@equipItem'
                    );
                    $api->post(
                        '/item/{item}/un-equip',
                        'App\Http\Api\Controllers\InventoryController@unEquipItem'
                    );
                });

                $api->group([
                    'prefix' => 'friends'
                ], function ($api) {
                    $api->post('/{name}', 'App\Http\Api\Controllers\FriendController@add')
                        ->where(['name' => '[а-яА-ЯёЁa-zA-Z0-9]+']);
                    $api->get('/{skip?}', 'App\Http\Api\Controllers\FriendController@getFriends')
                        ->where(['skip' => '[0-9]{2}+']);
                    $api->delete(
                        '/{id}',
                        'App\Http\Api\Controllers\FriendController@deleteFriend'
                    )->where(['id' => '[0-9]+']);
                });

                $api->group([
                    'prefix' => 'character'
                ], function ($api) {
                    $api->get('/name/{name}', 'App\Http\Api\Controllers\CharacterController@getCharacterByName');
                    $api->get('/id/{id}', 'App\Http\Api\Controllers\CharacterController@getCharacterById');
                    $api->put('/', 'App\Http\Api\Controllers\CharacterController@update');
                    $api->get('/attack/{defenderId}', 'App\Http\Api\Controllers\CharacterController@attack');
                });

                $api->group([
                    'prefix' => 'messages'
                ], function ($api) {
                    $api->post('/{id}/{skip?}', 'App\Http\Api\Controllers\MessageController@sendMessage');
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

                $api->group([
                    'prefix' => 'threads'
                ], function ($api) {
                    $api->get(
                        '/{skip?}',
                        'App\Http\Api\Controllers\ThreadsController@getInbox'
                    )->where([
                        'skip' => '[0-9]+',
                    ]);
                    $api->delete(
                        '/{id}',
                        'App\Http\Api\Controllers\ThreadsController@deleteConversation'
                    )->where([
                        'id' => '[0-9]+',
                    ]);
                });
                $api->group([
                    'prefix' => 'stats'
                ], function ($api) {
                    $api->get('/', 'App\Http\Api\Controllers\StatController@getStats');
                    $api->post('/', 'App\Http\Api\Controllers\StatController@increaseStats');
                });
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
