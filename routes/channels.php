<?php

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.User.{id}', function ($user, $id) {
    return (int)$user->id === (int)$id;
});
Broadcast::channel('chat.{conversationId}', function ($user, $conversationId) {
    $conversation = Nahid\Talk\Conversations\Conversation::findOrNew($conversationId);
    if ($conversation->user_one == $user->id || $conversation->user_two == $user->id) {
        return true;
    }
});