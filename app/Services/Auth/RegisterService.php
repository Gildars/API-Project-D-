<?php

namespace App\Services\Auth;

use App\Http\Requests\Auth\StoreUserRequest;
use App\Modules\Character\Application\Services\CharacterService;
use App\Modules\Character\UI\Http\CommandMappers\CreateCharacterCommandMapper;
use App\Modules\User\Application\Services\UserService;
use App\Modules\User\UI\Http\CommandMappers\CreateUserCommandMapper;
use App\Repositories\UserRepository;
use Dingo\Api\Routing\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Class RegisterService
 *
 * @package App\Services\Auth
 */
class RegisterService
{
    use Helpers;

    /**
     * @var UserService
     */
    protected $userService;
    /**
     * @var CreateUserCommandMapper
     */
    protected $userCommandMapper;

    /**
     * @var CreateCharacterCommandMapper
     */
    protected $characterCommandMapper;
    /**
     * @var UserRepository
     */
    protected $userRepository;
    /**
     * @var LoginService
     */
    protected $loginService;
    /**
     * @var CharacterService
     */
    protected $characterService;

    /**
     * Create a new controller instance.
     *
     * @param UserService $userService
     * @param CreateUserCommandMapper $userCommandMapper
     * @param UserRepository $userRepository
     * @param CreateCharacterCommandMapper $characterCommandMapper
     * @param LoginService $loginService
     * @param CharacterService $characterService
     */
    public function __construct(
        UserService $userService,
        CreateUserCommandMapper $userCommandMapper,
        UserRepository $userRepository,
        CreateCharacterCommandMapper $characterCommandMapper,
        LoginService $loginService,
        CharacterService $characterService
    ) {
        $this->userService = $userService;
        $this->userCommandMapper = $userCommandMapper;
        $this->userRepository = $userRepository;
        $this->loginService = $loginService;
        $this->characterCommandMapper = $characterCommandMapper;
        $this->characterService = $characterService;
    }

    /**
     * @param array $request
     * @return bool|string
     */
    public function createUserAndAuthorize(StoreUserRequest $request)
    {
        DB::beginTransaction();
        try {
            // Create User
            $userCommandMapper = $this->userCommandMapper->map($request);
            if ($user = $this->userService->create($userCommandMapper)) {
                $token = $this->loginService->authorization($request, $this->userRepository);
            }
            //Create Character
            $createCharacterCommand = $this->characterCommandMapper->map($request, $user);
            $character = $this->characterService->create($createCharacterCommand);
            if ($user && $character && $token) {
                DB::commit();
                return $token;
            }
        } catch (\Exception $e) {
            DB::rollback();
        }
        return false;
    }
}
