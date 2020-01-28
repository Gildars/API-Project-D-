<?php

namespace App\Repositories;

use App\Models\PasswordReset;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Class PasswordResetRepository
 *
 * @package App\Repositories
 */
class PasswordResetRepository extends BaseRepository
{
    /**
     * @var User
     */
    protected $model;

    /**
     * PasswordResetRepository constructor.
     *
     * @param PasswordReset $passwordReset
     */
    public function __construct(PasswordReset $passwordReset)
    {
        $this->model = $passwordReset;
    }

    /**
     * @param  int $id
     * @return \App\Models\Model
     */
    public function getById($id)
    {
        return $this->model
            ->find($id);
    }


    /**
     * @param  string $email
     * @param  string $token
     * @return mixed
     */
    public function createOrUpdate(string $email, string $token)
    {
        $passwordReset = $this->model::updateOrCreate(
            ['email' => $email],
            [
                'email' => $email,
                'token' => $token,
            ]
        );
        return $passwordReset;
    }

    /**
     * @param  string $token
     * @return mixed
     */
    public function getByToken(string $token)
    {
        $passwordReset = $this->model->where('token', $token)->first();
        return $passwordReset;
    }

    /**
     * @param  string $token
     * @param  string $email
     * @return mixed
     */
    public function getByTokenAndEmail(string $token, string $email)
    {
        $passwordReset = PasswordReset::where(
            [
                ['token', $token],
                ['email', $email],
            ]
        )->first();
        return $passwordReset;
    }

    /**
     * @param  User          $user
     * @param  PasswordReset $passwordReset
     * @param  string        $password
     * @return bool
     */
    public function resetPassword(User $user, PasswordReset $passwordReset, string $password)
    {
        try {
            DB::transaction(
                function () use ($user, $passwordReset, $password) {
                    $user->password = bcrypt($password);
                    $user->save();
                    $passwordReset->delete();
                }
            );
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }
}
