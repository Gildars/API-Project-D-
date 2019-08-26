<?php

namespace Tests\Unit;

use App\User;
use Illuminate\Support\Facades\App;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

class RegisterTest extends TestCase
{

    use WithFaker;

    public function test_registration_new_user()
    {
        $name = str_random(10);
        $this->post('/register', [
            'name'     => $name,
            'email'    => $this->faker->email,
            'password' => Str::random(10),
        ])->assertJsonStructure([
            "token",
            "message",
            "status_code",
        ])->assertJsonFragment(['status_code' => 201])
             ->assertStatus(200);

        User::all()->where('name', $name)->first()->delete();
    }

    public function test_registration_new_user_when_the_user_exists()
    {
        $name  = str_random(10);
        $email = $this->faker->email;

        $this->post('/register', [
            'name'     => $name,
            'email'    => $email,
            'password' => Str::random(10),
        ]);

        $this->post('/register', [
            'name'     => $name,
            'email'    => $email,
            'password' => Str::random(10),
        ])->assertJsonStructure([
            "message",
            "errors",
            "status_code",
        ])->assertJsonFragment([
            'message'     => 'Validation Error',
            'errors'      => [
                'name'  => ['The name has already been taken.'],
                'email' => ['The email has already been taken.'],
            ],
            'status_code' => 422,
        ])->assertStatus(422);

        User::all()->where('name', $name)->first()->delete();
    }

    public function test_registration_with_not_credentials()
    {
        $this->post('/register', [
            'name'     => '',
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
            'name'     => 'qwe',
            'email'    => 'qwert#gmail.com',
            'password' => 'qwer',
        ])->assertJsonStructure([
            "message",
            "errors",
            "status_code",
        ])->assertStatus(422);
    }


}
