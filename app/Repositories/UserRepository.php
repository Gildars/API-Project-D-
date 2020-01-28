<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

/**
 * Class UserRepository
 *
 * @package App\Repositories
 */
class UserRepository extends BaseRepository
{
    /**
     * @var User
     */
    protected $model;

    /**
     * UserRepository constructor.
     *
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->model = $user;
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
     * @param User $user
     */
    public function verifyEmail(User $user)
    {
        $user->email_verified_at = Carbon::now();
        $user->update();
    }

    /**
     * @param  string $email
     * @return mixed
     */
    public function getByEmail(string $email)
    {
        $user = $this->model->where('email', $email)->first();
        return $user;
    }


    public function create(Request $request)
    {
        $user = new $this->model;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->name = $request->name;
        $user->gender = $request->gender;
        $user->classId = $request->class;
        $user->save();
        return $user;
    }
}
