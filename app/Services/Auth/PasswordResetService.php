<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Repositories\PasswordResetRepository;
use Dingo\Api\Routing\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Class RegisterService
 *
 * @package App\Services\Auth
 */
class PasswordResetService
{
    use Helpers;

    /**
     * @var PasswordResetRepository
     */
    protected $passwordResetRepository;

    /**
     * PasswordResetService constructor.
     *
     * @param PasswordResetRepository $passwordResetRepository
     */
    public function __construct(PasswordResetRepository $passwordResetRepository)
    {
        $this->passwordResetRepository = $passwordResetRepository;
    }

    /**
     * Создает персонажа.
     *
     * @param  Request $request
     * @return \Illuminate\Auth\GenericUser|\Illuminate\Database\Eloquent\Model
     */
    public function createOrUpdateRequestToResetPassword(Request $request)
    {
        $token = str_random(60);
        $passwordReset = $this->passwordResetRepository->createOrUpdate($request->email, $token);
        return $passwordReset;
    }


    /**
     * @param  string $token
     * @param  string $email
     * @return mixed
     */
    public function hasPasswordResetQueryFromUser(string $token, string $email)
    {
        $passwordReset = $this->passwordResetRepository->getByTokenAndEmail($token, $email);
        return $passwordReset;
    }
}
