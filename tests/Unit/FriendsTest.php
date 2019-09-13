<?php

namespace Tests\Unit;

use App\Friend;
use Tests\TestCase;

class FriendsTest extends TestCase
{

    public function test_add_a_player_to_friend_with_the_correct_request()
    {
        $login = $this->loginWithFakeUser();
        $friends = Friend::query()
            ->where(function ($query) use ($login) {
                $query->where('id_friend_one', '=', $login['data']['user']['id'])
                    ->orWhere('id_friend_one', '=', 5);
            })
            ->where(function ($query) use ($login) {
                $query->orWhere('id_friend_two', '=', $login['data']['user']['id'])
                    ->orWhere('id_friend_two', '=', 5);
            })->first();
        if ($friends) {
            $friends->delete();
        }
        $this->post('/friends/5', [], $login['headers'])
            ->assertJsonStructure(['message'])
            ->assertStatus(201);
    }

    public function test_deletion_of_a_character_from_friends()
    {
        $login = $this->loginWithFakeUser();
        $this->delete('/friends/5', [], $login['headers'])
            ->assertStatus(200);
    }

    public function test_to_get_a_list_of_all_friends()
    {
        $login = $this->loginWithFakeUser();
        $this->get('/friends/', $login['headers'])
            ->assertStatus(200);
    }
}