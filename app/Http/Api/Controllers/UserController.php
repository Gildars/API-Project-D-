<?php

namespace App\Http\Api\Controllers;

use App\Http\Controllers\BaseController;
use App\Models\MailConfirmation;
use App\Notifications\MailConfirmationRequest;
use App\Repositories\MailConfirmationRepository;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use Dingo\Api\Routing\Helpers;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;

class UserController extends BaseController
{
    use Helpers;

    public function __construct()
    {
        parent::__construct();
        $this->middleware('api.auth');
    }

    public function index()
    {
        $user = $this->auth->user();

        return $user;
    }

    /**
     * @OA\Post(
     *     path="/users/mailConfirmCreate/",
     *     description="Отправляет письмо для подтверждения почты.",
     *     tags={"users"},
     * @OA\Response(response="200", description="Письмо отправленно."),
     * @OA\Response(response="404", description="Токен не найден."),
     * @OA\Response(response="422", description="Ваша почта уже подтверждена."),
     * )
     */
    public function mailConfirmCreate(MailConfirmationRepository $mailConfirmationRepository)
    {
        $user = $this->auth->user();

        if ($user->email_verified_at) {
            return response()->json(
                [
                'message' => trans('messages.user.mail_confirmation.request.already_confirmed'),
                ], 422
            );
        }
        $passwordReset = $mailConfirmationRepository->updateOrCreateToken(str_random(60), $user->email);
        if ($user && $passwordReset) {
            $user->notify(
                new MailConfirmationRequest($passwordReset->token)
            );
        }

        return response()->json(
            [
            'message' => trans('messages.user.mail_confirmation.request.send'),
            ], 200
        );
    }

    /**
     * @OA\Post(
     *     path="/users/mailConfirm/{token}",
     *     description="Подтверждает почту.",
     *     tags={"users"},
     * @OA\Parameter(
     *     name="token",
     *     in="path",
     *     required=true,
     * @OA\Schema(
     *             type="string"
     *         )
     * ),
     * @OA\Response(response="200", description="Почта подтверждена."),
     * @OA\Response(response="404", description="Токен не найден."),
     * @OA\Response(response="422", description="Почта подтверждена."),
     * )
     */
    public function mailConfirm(
        string $token,
        MailConfirmationRepository $mailConfirmationRepository,
        UserRepository $userRepositorys
    ) {
        $user = $this->auth->user();

        if ($user->email_verified_at) {
            return response()->json(
                [
                'message' => trans('messages.user.mail_confirmation.request.already_confirmed'),
                ], 422
            );
        }

        $mailConfirmation = $mailConfirmationRepository->getByToken($token);

        if (!$mailConfirmation) {
            return response()->json(
                [
                'message' => trans('messages.user.mail_confirmation.request.token'),
                ], 404
            );
        }

        if ($mailConfirmationRepository->deleteIfExpired($mailConfirmation)) {
            return response()->json(
                [
                'message' => trans('messages.user.mail_confirmation.request.token'),
                ], 404
            );
        }

        if ($mailConfirmation->token == $token) {
            $userRepositorys->verifyEmail($user);
            return response()->json(trans('messages.user.mail_confirmation.request.success'), 200);
        }

    }

}
