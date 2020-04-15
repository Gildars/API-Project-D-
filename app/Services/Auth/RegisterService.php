<?php

namespace App\Services\Auth;

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
    public function createUserAndAuthorize(Request $request)
    {
        DB::beginTransaction();
        try {
            // Create User
            $userCommandMapper = $this->userCommandMapper->map($request);
            $user = $this->userService->create($userCommandMapper);

            //Create Character
            $createCharacterCommand = $this->characterCommandMapper->map($request);
            $character = $this->characterService->create($createCharacterCommand);
            if ($user && $character && ($token = $this->loginService->authorization($request, $this->userRepository))) {
                DB::commit();
                return $token;
            }
        } catch (\Exception $e) {
            DB::rollback();
        }
        return false;
    }


    /**
     * @param  Request $request
     * @return \Illuminate\Auth\GenericUser|\Illuminate\Database\Eloquent\Model
     */
    /* public function createCharacter(Request $request)
     {
         $user = $this->auth()->user();
         if (!$user->name) {
             $user->name = $request->name;
             $user->gender = $request->gender;
             $user->update();
         }
         return $user;
     }*/
}
