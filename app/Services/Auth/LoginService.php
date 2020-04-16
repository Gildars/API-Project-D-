<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;

/**
 * Class LoginService
 *
 * @package App\Services\Auth
 */
class LoginService
{

    /**
     * @var UserRepository
     */
    protected $userRepository;


    /**
     * LoginService constructor.
     *
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }


    /**
     * @param Request $request
     * @param UserRepository $userRepository
     * @return array|bool
     */
    public function authorization(Request $request, UserRepository $userRepository)
    {
        $user = $userRepository->getByEmail($request->email);
        if ($this->comparePassword($user, $request->password)) {
            $token = $this->getTokenFromUser($user);
            return [
                "token" => $token,
                "user" => $user
            ];
        }
        return false;
    }

    /**
     * @param  User $user
     * @param  string $password
     * @return bool
     */
    public function comparePassword(User $user, string $password): bool
    {
        if ($user && Hash::check($password, $user->password)) {
            return true;
        }
        return false;
    }

    /**
     * @param  User $user
     * @return string
     */
    private function getTokenFromUser(User $user): string
    {
        $token = JWTAuth::fromUser($user);
        return $token;
    }
}
