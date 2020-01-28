<?php

namespace Tests\Unit;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;


class RegisterTest extends TestCase
{

    use WithFaker;

    public function test_registration_new_user()
    {
        $email = $this->faker->email;

        $this->post('/register', [
            'email' => $email,
            'password' => Str::random(10),
            'class' => 1,
            'gender' => 'female',
            'name' => 'Tara'
        ])->assertStatus(201);

        User::all()->where('email', $email)->first()->delete();
    }

    public function test_registration_new_user_when_the_user_exists()
    {
        $email = $this->faker->email;

        $this->post('/register', [
            'email' => $email,
            'password' => Str::random(10),
            'class' => 1,
            'gender' => 'female',
            'name' => 'Tara'
        ]);

        $this->post('/register', [
            'email' => $email,
            'password' => Str::random(10),
            'class' => 1,
            'gender' => 'female',
            'name' => 'Tara'
        ])->assertStatus(422);

        User::all()->where('email', $email)->first()->delete();
    }

    public function test_registration_with_not_credentials()
    {
        $this->post('/register', [
            'email' => '',
            'password' => '',
            'class' => null,
            'gender' => '',
            'name' => ''
        ])->assertStatus(422);

    }

    public function test_registration_with_not_correct_credentials()
    {
        $this->post('/register', [
            'email' => 'qwert#gmail.com',
            'password' => 'qwer',
            'class' => 1,
            'gender' => 'female',
            'name' => 'Tara2154Tara2154Tara2154Tara2154Tara2154Tara2154Tara2154Tara2154Tara2154'
        ])->assertStatus(422);
    }

    /*public function test_create_character_with_correct_data()
    {
        $user = User::where('email', 'test1@gmail.com')->first();
        $user->name = null;
        $user->update();
        $token = JWTAuth::fromUser($user);
        $name = 'TestOne';
        $this->post('users/createCharacter', [
            'name' => $name,
            'class' => '1',
        ], [
            'Authorization' => 'Bearer ' . $token,
        ])->assertStatus(201);
    }

    public function test_create_character_with_not_correct_data()
    {
        $user = User::where('email', 'test1@gmail.com')->first();
        $user->name = null;
        $user->update();
        $token = JWTAuth::fromUser($user);
        $name = 'TsetName';

        $this->post('users/createCharacter', [
            'name' => $name,
            'class' => '15658',
        ], [
            'Authorization' => 'Bearer ' . $token,
        ])->assertStatus(422);
    }*/

}
