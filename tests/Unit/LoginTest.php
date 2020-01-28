<?php

namespace Tests\Unit;

use Tests\TestCase;

class LoginTest extends TestCase
{

    public function test_user_can_login_with_correct_credentials()
    {
       $this->loginWithFakeUser();
    }

    public function test_user_can_login_with_not_correct_credentials()
    {
        $this->post('/login', [
            'email'    => 'pavlitto97@gmail.com',
            'password' => 'Hfiksfjpow',
        ])->assertJsonStructure(['message'])
             ->assertStatus(422);
    }

    public function test_user_can_login_with_not_credentials()
    {
        $this->post('/login', [
            'email'    => '',
            'password' => '',
        ])->assertStatus(422);
    }
}
