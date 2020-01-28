<?php

namespace Tests\Unit;

use App\Models\Friend;
use Tests\TestCase;

class FriendTest extends TestCase
{

    public function test_add_a_player_to_friend_with_the_correct_request()
    {
        $login = $this->loginWithFakeUser();
        $friendRepository = app( 'App\Repositories\FriendRepository');
        $friends = $friendRepository->getFriend($login['data']['user']['id'], 2);

        if ($friends) {
            $friendRepository->deleteFriend($friends);
        }

        $this->post('/friends/2', [], $login['headers'])
            ->assertJsonStructure(['message'])
            ->assertStatus(201);
    }

    public function test_to_get_a_list_of_all_friends()
    {
        $login = $this->loginWithFakeUser();

        $this->get('/friends', $login['headers'])
            ->assertStatus(200);
    }

    public function test_deletion_of_a_character_from_friends()
    {
        $login = $this->loginWithFakeUser();

        $this->delete('/friends/2', [], $login['headers'])
            ->assertStatus(200);
    }

}