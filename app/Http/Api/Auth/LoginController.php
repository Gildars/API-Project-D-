<?php

namespace App\Http\Api\Auth;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Auth\UserRequests;
use App\Repositories\UserRepository;
use App\Services\Auth\LoginService;
use Dingo\Api\Routing\Helpers;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

/**
 * Class LoginController
 *
 * @package App\Http\Api\Auth
 */
class LoginController extends BaseController
{
    use AuthenticatesUsers;
    use Helpers;

    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @var LoginService
     */
    protected $loginService;

    /**
     * LoginController constructor.
     *
     * @param UserRepository $userRepository
     * @param LoginService $loginService
     */
    public function __construct(UserRepository $userRepository, LoginService $loginService)
    {
        parent::__construct();
        $this->userRepository = $userRepository;
        $this->loginService = $loginService;
    }

    /**
     * @OA\Post(
     *     path="/login",
     *     description="Авторизация.",
     *     tags={"auth"},
     *     @OA\Parameter(
     *     name="email",
     *     in="path",
     *     required=true,
     * @OA\Schema(
     *             type="string"
     *         )
     * ),
     *     @OA\Parameter(
     *     name="password",
     *     in="path",
     *     required=true,
     * @OA\Schema(
     *             type="string"
     *         )
     * ),
     * @OA\Response(response="200", description="Пользователь авторизован."),
     * @OA\Response(response="422", description="Не удалось авторизоватсья."),
     *
     * )
     * @param                       UserRequests $request
     * @return                      \Illuminate\Http\JsonResponse
     */
    public function login(UserRequests $request)
    {
        if ($token = $this->loginService->authorization($request, $this->userRepository)) {
            $this->clearLoginAttempts($request);
            return response()->json(
                [
                    'token' => $token,
                    'message' => 'User Authenticated',
                ],
                200
            );
        }
        /*throw new UnauthorizedHttpException(
            "Bad Credentials",
            trans('validation.custom.login.bad_credentials')
        );*/
    }

    /**
     * @OA\Post(
     *     path="/logout",
     *     description="Выход из игры.",
     *     tags={"auth"},
     * @OA\Response(response="200", description="Пользователь вышел из игры."),
     * )
     */
    public function logout()
    {
        $this->guard()->logout();
    }
}
