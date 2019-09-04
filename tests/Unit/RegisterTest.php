<?php

namespace Tests\Unit;

use App\User;
use Illuminate\Support\Facades\App;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;


class RegisterTest extends TestCase
{

    use WithFaker;

    public function test_registration_new_user()
    {
        $email = $this->faker->email;
        $this->post('/register', [
            'email'    => $email,
            'password' => Str::random(10),
        ])->assertJsonStructure([
            "token",
            "message",
            "status_code",
        ])->assertStatus(200);

        User::all()->where('email', $email)->first()->delete();
    }

    public function test_registration_new_user_when_the_user_exists()
    {
        $email = $this->faker->email;

        $this->post('/register', [
            'email'    => $email,
            'password' => Str::random(10),
        ]);

        $this->post('/register', [
            'email'    => $email,
            'password' => Str::random(10),
        ])->assertJsonStructure([
            "message",
            "errors",
            "status_code",
        ])->assertStatus(422);

        User::all()->where('email', $email)->first()->delete();
    }

    public function test_registration_with_not_credentials()
    {
        $this->post('/register', [
            'email'    => '',
            'password' => '',
        ])->assertJsonStructure([
            "message",
            "errors",
            "status_code",
        ])->assertStatus(422);

    }

    public function test_registration_with_not_correct_credentials()
    {
        $this->post('/register', [
            'email'    => 'qwert#gmail.com',
            'password' => 'qwer',
        ])->assertJsonStructure([
            "message",
            "errors",
            "status_code",
        ])->assertStatus(422);
    }

    public function test_create_character_with_correct_data()
    {
        $user       = User::where('email', 'test@gmail.com')->first();
        $user->name = null;
        $user->update();
        $token = JWTAuth::fromUser($user);
        $name  = 'TsetName';

        $this->post('/createCharacter', [
            'name'  => $name,
            'class' => '1',
        ], [
            'Authorization' => 'Bearer ' . $token,
        ])->assertStatus(200);
    }

    public function test_create_character_with_not_correct_data()
    {
        $user       = User::where('email', 'test@gmail.com')->first();
        $user->name = null;
        $user->update();
        $token = JWTAuth::fromUser($user);
        $name  = 'TsetName';

        $this->post('/createCharacter', [
            'name'  => $name,
            'class' => '15658',
        ], [
            'Authorization' => 'Bearer ' . $token,
        ])->assertStatus(422);
    }

}
