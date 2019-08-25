<?php

namespace Tests\Unit;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginTest extends TestCase
{

    public function test_user_can_login_with_correct_credentials()
    {
        $this->post('/login', [
            'email' => 'pavlitto97@gmail.com',
            'password' => 'Catharsiscur1997'
        ])->assertJsonStructure([
            'token', 'status_code', 'message'
        ])->assertStatus(200);
    }

    public function test_user_can_login_with_not_correct_credentials()
    {
        $this->post('/login', [
            'email' => 'pavlitto97@gmail.com',
            'password' => 'Hfiksfjpow'
        ])->assertJsonStructure(['message'])
            ->assertStatus(401);
    }
    public function test_user_can_login_with_not_credentials()
    {
        $this->post('/login', [
            'email' => '',
            'password' => ''
        ])->assertStatus(401);
    }
}
