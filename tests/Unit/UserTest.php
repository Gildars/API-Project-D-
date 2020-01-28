<?php

namespace Tests\Unit;

use App\Models\MailConfirmation;
use App\Models\User;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function test_condition_preparation()
    {
        $login = $this->loginWithFakeUser();

        $mailConfirmation = MailConfirmation::query()->where('email',
            'test1@gmail.com')->first();
        if ($mailConfirmation) {
            $mailConfirmation->delete();
        }

        $user = User::query()->where('id', '=', $login['data']['user']['id'])->first();
        $user->email_verified_at = null;
        $user->update();

    }

    public function test_create_a_request_to_confirm_mail()
    {
        $login = $this->loginWithFakeUser();

        $this->post('users/mailConfirmCreate',
            [
                'email' => 'test1@gmail.com',
            ],
            [
                'headers' => $login['headers']
                ,
            ])->assertJsonStructure([
            'message',
        ])->assertStatus(200);
    }

    public function test_confirm_mail_by_clicking_on_the_link_from_the_letter()
    {

        $mailConfirmation = MailConfirmation::where('email',
            'test1@gmail.com')->first();
        $user = $this->loginWithFakeUser();
        $this->post("users/mailConfirm/{$mailConfirmation->token}", [
            "Authorization" => 'Bearer ' . $user['token'],
        ])->assertStatus(200);
    }
}
