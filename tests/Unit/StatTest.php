<?php

namespace Tests\Unit;

use App\Models\Friend;
use Tests\TestCase;

class StatTest extends TestCase
{

    public function test_get_character_stats()
    {
        $login = $this->loginWithFakeUser();
        $this->get('/stats', [], $login['headers'])
            ->assertStatus(200);
    }

}