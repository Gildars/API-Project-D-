<?php

namespace App\Services\Auth;

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
     * @var UserRepository
     */
    protected $userRepository;
    /**
     * @var LoginService
     */
    protected $loginService;

    /**
     * RegisterService constructor.
     *
     * @param $userRepository
     * @param $loginService
     */
    public function __construct(UserRepository $userRepository, LoginService $loginService)
    {
        $this->userRepository = $userRepository;
        $this->loginService = $loginService;
    }

    /**
     * @param  Request $request
     * @return bool|string
     */
    public function createUserAndAuthorize(CreateUserCommand $createUserCommand)
    {
        $user = null;
        $user = $this->userService->create($createUserCommand);
        if ($user && ($token = $this->loginService->authorization($createUserCommand, $this->userRepository))) {
            return $token;
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
