<?php

namespace App\Http\Api\Controllers;

use App\MailConfirmation;
use App\Notifications\MailConfirmationRequest;
use Carbon\Carbon;
use Dingo\Api\Routing\Helpers;
use Illuminate\Routing\Controller;

class UsersController extends Controller
{
    use Helpers;

    public function __construct()
    {
        $this->middleware('api.auth');
    }

    public function index()
    {
        $user = $this->auth->user();

        return $user;
    }

    public function mailConfirmCreate()
    {
        $user = $this->auth->user();

        if ($user->email_verified_at) {
            return response()->json([
                'message' => trans('messages.user.mail_confirmation.request.already_confirmed'),
            ]);
        }
        $passwordReset = MailConfirmation::updateOrCreate(
            ['email' => $user->email],
            [
                'email' => $user->email,
                'token' => str_random(60),
            ]
        );
        if ($user && $passwordReset) {
            $user->notify(
                new MailConfirmationRequest($passwordReset->token)
            );
        }

        return response()->json([
            'message' => trans('messages.user.mail_confirmation.request.send'),
        ]);
    }

    public function mailConfirm($token)
    {
        $user = $this->auth->user();

        if ($user->email_verified_at) {
            return response()->json([
                'message' => trans('messages.user.mail_confirmation.request.already_confirmed'),
            ]);
        }

        $mailConfirmation = MailConfirmation::where('token', $token)->first();

        if ( ! $mailConfirmation) {
            return response()->json([
                'message' => trans('messages.user.mail_confirmation.request.token'),
            ], 404);
        }

        if (Carbon::parse($mailConfirmation->updated_at)->addMinutes(720)
                  ->isPast()) {
            $mailConfirmation->delete();

            return response()->json([
                'message' => trans('messages.user.mail_confirmation.request.token'),
            ], 404);
        }

        if ($mailConfirmation->token == $token) {
            $user                    = $this->auth->user();
            $user->email_verified_at = Carbon::now();
            $user->update();

            return response()->json(trans('messages.user.mail_confirmation.request.success'));
        }

    }
}