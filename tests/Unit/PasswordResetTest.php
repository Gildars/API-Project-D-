<?php

namespace Tests\Unit;

use App\PasswordReset;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;

class PasswordResetTest extends TestCase
{
    use WithFaker;

    public function test_create_a_request_to_reset_the_password_using_email()
    {
        $this->post('password/create', [
            'email' => 'pavlitto97@gmail.com',
        ], [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
        ])->assertJsonStructure([
            'message',
        ])->assertJsonFragment([
            'message' => 'We have e-mailed your password reset link!',
        ]);
    }

    public function test_confirm_password_reset_by_clicking_on_the_link_from_the_letter(
    )
    {
        $user = User::query()->where('email', 'pavlitto97@gmail.com')->first();
        $this->assertTrue((bool)$user, 'Not found the user');
        $passwordReset = PasswordReset::updateOrCreate(
            ['email' => $user->email],
            [
                'email' => $user->email,
                'token' => str_random(60),
            ]
        );
        $this->token   = $passwordReset->token;
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
        $user = User::query()->where('email', 'pavlitto97@gmail.com')->first();
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
                 ->assertJsonStructure([
                     'email',
                     'token',
                     'created_at',
                     'updated_at',
                     'id',
                 ])->assertStatus(200);
        }
        $this->assertTrue((bool)$passwordReset->token, 'Token not found!');
        $this->post('password/reset', [
            "email"                 => "pavlitto97@gmail.com",
            "password"              => "Catharsiscur1997",
            "password_confirmation" => "Catharsiscur1997",
            "token"                 => $passwordReset->token,
        ])->assertStatus(403);
    }

    public function test_create_a_request_to_reset_the_password_using_email_with_not_credentials(
    )
    {
        $this->post('password/create', [
            'email' => '',
        ], [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
        ])->assertJsonStructure([
            'message',
        ])->assertStatus(422);
    }

    public function test_create_a_request_to_reset_the_password_using_email_when_the_user_not_exists(
    )
    {
        $this->post('password/create', [
            'email' => $this->faker->email,
        ], [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
        ])->assertJsonStructure([
            'message',
        ])->assertStatus(404);
    }

    public function test_confirm_password_reset_by_clicking_on_the_link_from_the_letter_with_not_token(
    )
    {
        $this->get("password/find/")->assertJsonStructure([
            'message',
        ])->assertStatus(404);
    }

    public function test_confirm_password_reset_by_clicking_on_the_link_from_the_letter_with_not_correct_token(
    )
    {
        $token = str_random(5);
        $this->get("password/find/{$token}")->assertJsonStructure([
            'message',
        ])->assertStatus(404);
    }

    public function test_set_the_new_password_with_not_token()
    {
        $this->post('password/reset', [
            "email"                 => "pavlitto97@gmail.com",
            "password"              => "Catharsiscur19",
            "password_confirmation" => "Catharsiscur19",
            "token"                 => "",
        ])->assertJsonStructure([
            'message',
        ])->assertStatus(422);
    }

    public function test_set_the_new_password_with_not_data()
    {
        $this->post('password/reset', [
        ])->assertJsonStructure([
            'message',
        ])->assertStatus(422);
    }

    public function test_set_the_new_password_when_the_user_not_exists()
    {
        $email         = 'pavlitto97@gmail.com';
        $user          = User::query()->where('email', $email)->first();
        $passwordReset = PasswordReset::updateOrCreate(
            ['email' => $user->email],
            [
                'email' => $user->email,
                'token' => str_random(60),
            ]
        );
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
        $this->post('password/reset', [
            "email"                 => $this->faker->email,
            "password"              => "Catharsiscur19",
            "password_confirmation" => "Catharsiscur19",
            "token"                 => $passwordReset->token,
        ])->assertJsonStructure([
            'message',
        ])->assertStatus(404);
    }
}
