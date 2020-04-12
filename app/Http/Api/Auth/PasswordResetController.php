<?php

namespace App\Http\Api\Auth;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Auth\StorePasswordResetRequest;
use App\Http\Requests\Auth\UpdatePasswordRequest;
use App\Repositories\PasswordResetRepository;
use App\Repositories\UserRepository;
use App\Services\Auth\LoginService;
use App\Services\Auth\PasswordResetService;
use Dingo\Api\Routing\Helpers;
use Carbon\Carbon;
use App\Notifications\PasswordResetRequest;
use App\Notifications\PasswordResetSuccess;
use Illuminate\Support\Facades\DB;

/**
 * Class PasswordResetController
 *
 * @package App\Http\Api\Auth
 */
class PasswordResetController extends BaseController
{
    use Helpers;

    /**
     * @var PasswordResetRepository
     */
    protected $passwordResetRepository;

    /**
     * @var PasswordResetService
     */
    protected $passwordResetService;

    /**
     * PasswordResetController constructor.
     *
     * @param PasswordResetRepository $passwordResetRepository
     * @param PasswordResetService    $passwordResetService
     */
    public function __construct(
        PasswordResetRepository $passwordResetRepository,
        PasswordResetService $passwordResetService
    ) {
        parent::__construct();
        $this->passwordResetRepository = $passwordResetRepository;
        $this->passwordResetService = $passwordResetService;
    }

    /**
     * @OA\Post(
     *     path="/password/create",
     *     description="Отправляет письмо на почту для вотановления аккаунта.",
     *     tags={"password"},
     * @OA\Parameter(
     *     name="email",
     *     in="query",
     *     required=true,
     * @OA\Schema(
     *             type="string",
     *         )
     * ),
     * @OA\Response(response="200", description="Письмо дял востановления аккаунта отправлено на почту."),
     * @OA\Response(response="422", description="Ошибка валидации."),
     * )
     * @param                       StorePasswordResetRequest $request
     * @param                       UserRepository            $userRepository
     * @return                      \Illuminate\Http\JsonResponse
     */
    public function create(StorePasswordResetRequest $request, UserRepository $userRepository)
    {
        $user = $userRepository->getByEmail($request->email);

        if ($user && ($passwordReset = $this->passwordResetService->createOrUpdateRequestToResetPassword($request))) {
            $user->notify(
                new PasswordResetRequest($passwordReset->token)
            );
        }

        return response()->json(
            [
                'message' => trans('passwords.sent'),
            ],
            200
        );
    }


    /**
     * @OA\Get(
     *     path="/password/find{token}",
     *     description="Ищет и токен востановления пароля.",
     *     tags={"password"},
     * @OA\Parameter(
     *     name="token",
     *     in="path",
     *     required=true,
     * @OA\Schema(
     *             type="string",
     *         )
     * ),
     * @OA\Response(response="200", description="Токен найден."),
     * @OA\Response(response="404", description="Токен не найден."),
     * @OA\Response(response="422", description="Не корректный токен."),
     * )
     */
    /**
     * Find token password reset
     *
     * @param  string $token
     * @return \Illuminate\Http\JsonResponse
     */
    public function find(string $token)
    {
        $passwordReset = $this->passwordResetRepository->getByToken($token);
        if (!$passwordReset) {
            return response()->json(
                [
                    'message' => trans('passwords.token'),
                ],
                404
            );
        }
        if (Carbon::parse($passwordReset->updated_at)->addMinutes(720)->isPast()) {
            $passwordReset->delete();

            return response()->json(
                [
                    'message' => trans('passwords.token'),
                ],
                404
            );
        }

        return response()->json($passwordReset, 200);
    }

    /**
     * @OA\Post(
     *     path="/password/reset",
     *     description="Сохраняет новый пароль.",
     *     tags={"password"},
     * @OA\Parameter(
     *     name="token",
     *     in="query",
     *     required=true,
     * @OA\Schema(
     *             type="string",
     *         ),
     *     ),
     * @OA\Parameter(
     *     name="password",
     *     in="query",
     *     required=true,
     * @OA\Schema(
     *             type="string",
     *         ),
     *     ),
     * @OA\Parameter(
     *     name="email",
     *     in="query",
     *     required=true,
     * @OA\Schema(
     *             type="string",
     *         )
     * ),
     * @OA\Response(response="200", description="Пароль изменен."),
     * @OA\Response(response="404", description="Токен или email не найден."),
     * @OA\Response(response="422", description="Некоррректный пароль или пароль совпадает со старым паролем."),
     * )
     * @param                       UpdatePasswordRequest $request
     * @param                       UserRepository         $userRepository
     * @param                       LoginService           $loginService
     * @return                      \Illuminate\Http\JsonResponse
     */
    public function reset(UpdatePasswordRequest $request, UserRepository $userRepository, LoginService $loginService)
    {
        $passwordReset = $this->passwordResetService->hasPasswordResetQueryFromUser($request->token, $request->email);
        if (!$passwordReset) {
            return response()->json(
                [
                    'message' => trans('passwords.token'),
                ],
                404
            );
        }
        if (!$user = $userRepository->getByEmail($request->email)) {
            return response()->json(
                [
                    'message' => trans('passwords.email_not_found'),
                ],
                404
            );
        }
        if ($loginService->comparePassword($user, $request->password)) {
            return response()->json(
                [
                    'message' => trans('passwords.not_match'),
                ],
                422
            );
        }
        if ($this->passwordResetRepository->resetPassword($user, $passwordReset, $request->password)) {
            $user->notify(new PasswordResetSuccess());
        }
        return response()->json($user);
    }

}
