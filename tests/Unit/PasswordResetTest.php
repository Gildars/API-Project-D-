<?php

namespace Tests\Unit;

use App\Models\PasswordReset;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;

class PasswordResetTest extends TestCase
{
    use WithFaker;

    public function test_create_a_request_to_reset_the_password_using_email()
    {
        $this->post('password/create', [
            'email' => 'test1@gmail.com',
        ], [
            'headers' => [
                'Content-Type' => 'application/json',
            ]
        ])->assertStatus(200);
    }

    public function test_confirm_password_reset_by_clicking_on_the_link_from_the_letter()
    {
        $user = User::query()->where('email', 'test1@gmail.com')->first();
        $this->assertTrue((bool)$user, 'Not found the user');
        $passwordReset = PasswordReset::updateOrCreate(
            ['email' => $user->email],
            [
                'email' => $user->email,
                'token' => str_random(60),
            ]
        );
        $this->token = $passwordReset->token;
        if ($user && $passwordReset) {
            $this->get("password/find/{$passwordReset->token}")
                ->assertJsonStructure([
                    'email',
                    'token',
                    'created_at',
                    'updated_at',
                    'id',
                ])->assertStatus(200);
        }
    }

    public function test_set_the_new_password()
    {
        $user = User::query()->where('email', 'test1@gmail.com')->first();
        $this->assertTrue((bool)$user, 'Not found the user');
        $passwordReset = PasswordReset::updateOrCreate(
            ['email' => $user->email],
            [
                'email' => $user->email,
                'token' => str_random(60),
            ]
        );
        if ($user && $passwordReset) {
            $this->get("password/find/{$passwordReset->token}")
                ->assertStatus(200);
        }
        $this->assertTrue((bool)$passwordReset->token, 'Token not found!');
        $this->post('password/reset', [
            "email" => "test1@gmail.com",
            "password" => "Catharsis",
            "password_confirmation" => "Catharsis",
            "token" => $passwordReset->token,
        ])->assertStatus(422);
    }

    public function test_create_a_request_to_reset_the_password_using_email_with_not_credentials()
    {
        $this->post('password/create', [
            'email' => '',
        ], [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
        ])->assertStatus(422);
    }

    public function test_create_a_request_to_reset_the_password_using_email_when_the_user_not_exists()
    {
        $this->post('password/create', [
            'email' => $this->faker->email,
        ], [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
        ])->assertStatus(422);
    }

    public function test_confirm_password_reset_by_clicking_on_the_link_from_the_letter_with_not_token()
    {
        $this->get("password/find/")->assertJsonStructure([
            'message',
        ])->assertStatus(404);
    }

    public function test_confirm_password_reset_by_clicking_on_the_link_from_the_letter_with_not_correct_token()
    {
        $token = str_random(5);
        $this->get("password/find/{$token}")->assertJsonStructure([
            'message',
        ])->assertStatus(404);
    }

    public function test_set_the_new_password_with_not_token()
    {
        $this->post('password/reset', [
            "email" => "test1@gmail.com",
            "password" => "Catharsis",
            "password_confirmation" => "Catharsis",
            "token" => "",
        ])->assertStatus(422);
    }

    public function test_set_the_new_password_with_not_data()
    {
        $this->post('password/reset', [
        ])->assertStatus(422);
    }

    public function test_set_the_new_password_when_the_user_not_exists()
    {
        $email = 'test1@gmail.com';
        $user = User::query()->where('email', $email)->first();
        $passwordReset = PasswordReset::updateOrCreate(
            ['email' => $user->email],
            [
                'email' => $user->email,
                'token' => str_random(60),
            ]
        );
        if ($user && $passwordReset) {
            $this->get("password/find/{$passwordReset->token}")
                ->assertStatus(200);
        }
        $this->post('password/reset', [
            "email" => $this->faker->email,
            "password" => "Catharsis",
            "password_confirmation" => "Catharsis",
            "token" => $passwordReset->token,
        ])->assertStatus(404);
    }
}
