<?php

namespace Tests;

use App\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function loginWithFakeUser()
    {
        $request = $this->post('/login', [
            'email' => 'test@gmail.com',
            'password' => 'Catharsiscur19',
        ])->assertJsonStructure(['message'])
            ->assertStatus(200);
        $token = ($request->baseResponse->original['token']);
        $headers = ['Authorization' => "Bearer $token"];
        $user = $this->get('/user', $headers)->assertStatus(200);
        return [
            'data' => [
                $request->original,
                'user' => $user->original,
            ],
            'token' => $token,
            'headers' => $headers
        ];
    }
}
