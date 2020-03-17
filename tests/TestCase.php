<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function loginWithFakeUser()
    {
        $request = $this->post('/login', [
            'email' => 'test1@gmail.com',
            'password' => 'Catharsis',
        ])->assertJsonStructure(['message'])
            ->assertStatus(200);
        $token = ($request->baseResponse->original['access_token']);
        $headers = [
            'Authorization' => "Bearer $token",
            'Content-Type' => 'application/json'
        ];
        $user = $this->get('/users', $headers)->assertStatus(200);
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
