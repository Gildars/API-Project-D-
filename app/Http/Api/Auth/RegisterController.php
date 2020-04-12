<?php

namespace App\Http\Api\Auth;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Auth\StoreUserRequest;
use App\Http\Requests\User\UpdateCharacterRequest;
use App\Repositories\UserRepository;
use App\Services\Auth\LoginService;
use App\Services\Auth\RegisterService;
use Dingo\Api\Routing\Helpers;
use Illuminate\Foundation\Auth\RegistersUsers;

/**
 * Class RegisterController
 *
 * @package App\Http\Api\Auth
 */
class RegisterController extends BaseController
{
    use RegistersUsers;
    use Helpers;

    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @var RegisterService
     */
    protected $registerService;

    /**
     * RegisterController constructor.
     *
     * @param UserRepository $userRepository
     * @param RegisterService $registerService
     */
    public function __construct(UserRepository $userRepository, RegisterService $registerService)
    {
        parent::__construct();
        $this->userRepository = $userRepository;
        $this->registerService = $registerService;
    }

    /**
     * @OA\Post(
     *     path="/auth/register",
     *     description="Регистрирует новый аккаунт.",
     *     tags={"auth"},
     * @OA\Parameter(
     *     name="email",
     *     in="query",
     *     required=true,
     * @OA\Schema(
     *             type="string",
     *         )
     * ),
     * @OA\Parameter(
     *     name="password",
     *     in="query",
     *     required=true,
     * @OA\Schema(
     *             type="string",
     *         )
     * ),
     * @OA\Response(response="201", description="Игрок успешно зарегистрирован."),
     * @OA\Response(response="404", description="Игрок не найден."),
     * )
     * @param                       StoreUserRequest $request
     * @return                      \Illuminate\Http\JsonResponse
     */
    public function register(StoreUserRequest $request)
    {
        if ($token = $this->registerService->createUserAndAuthorize($request)) {
            return response()->json(
                [
                    "token" => $token,
                    "message" => "User created",
                ],
                201
            );

        } else {
            return response()->json("User Not Found...", 404);
        }
    }

    /**
     * @OA\Post(
     *     path="/users/createCharacter",
     *     description="Регистрирует новый аккаунт.",
     *     tags={"users"},
     * @OA\Parameter(
     *     name="name",
     *     in="query",
     *     required=true,
     * @OA\Schema(
     *             type="string",
     *         )
     * ),
     * @OA\Parameter(
     *     name="class",
     *     in="query",
     *     required=true,
     * @OA\Schema(
     *             type="string",
     *         )
     * ),
     * @OA\Response(response="201", description="Персонаж успешно создан."),
     * @OA\Response(response="422", description="Нельзя создать персонажа."),
     * )
     * @param                       UpdateCharacterRequest $request
     * @return                      \Illuminate\Http\JsonResponse
     */
    protected function createCharacter(UpdateCharacterRequest $request)
    {
        $this->middleware('api.auth');
        if ($user = $this->registerService->createCharacter($request)) {
            return response()->json(
                [
                    [
                        $user,
                        trans('messages.users.character.created', ['name' => $user->name])
                    ],
                ],
                201
            );
        }
        return response()->json(
            [
                trans('messages.users.character.cannot_be_created', ['name' => $user->name])
            ],
            422
        );
    }

}
