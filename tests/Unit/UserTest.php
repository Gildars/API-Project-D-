<?php

namespace Tests\Unit;

use App\MailConfirmation;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function test_create_a_request_to_confirm_mail()
    {
        $this->get('user/mailConfirmCreate', [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
        ])->assertJsonStructure([
            'message',
        ]);
    }

    public function test_confirm_mail_by_clicking_on_the_link_from_the_letter()
    {
        $mailConfirmation = MailConfirmation::where('email',
            'test@gmail.com')->first();
        $user             = $this->post('/login', [
            'email'    => 'test@gmail.com',
            'password' => 'Catharsiscur19',
        ])->assertJsonStructure([
            'token',
            'status_code',
            'message',
        ])->assertStatus(200);

        $user = json_decode($user->getContent());
        $this->get("user/mailConfirm/{$mailConfirmation->token}", [
            "Authorization" => 'Bearer ' . $user->token,
        ])
             ->assertJsonStructure([
                 'message',
             ])->assertStatus(200);
    }
}
