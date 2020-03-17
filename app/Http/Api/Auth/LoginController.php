<?php

namespace App\Http\Api\Auth;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Auth\UserRequests;
use App\Repositories\UserRepository;
use App\Services\Auth\LoginService;
use Dingo\Api\Routing\Helpers;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

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
            return $this->respondWithToken($token);
        }
        return response()->json(
            [
                'message' => trans('validation.custom.login.bad_credentials')
            ],
            422
        );
    }

    /**
     * @OA\Post(
     *     path="/refresh",
     *     description="Обновление токена доступа.",
     *     tags={"auth"},
     *
     * @OA\Response(response="200", description="Токен обновлен."),
     *
     * )
     * @param                       UserRequests $request
     * @return                      \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'message' => 'User Authenticated',
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Carbon::now('UTC')->addMinutes(auth()->factory()->getTTL())->timestamp
        ],200);
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
