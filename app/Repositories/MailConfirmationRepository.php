<?php

namespace App\Repositories;

use App\Models\MailConfirmation;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Class MailConfirmationRepository
 *
 * @package App\Repositories
 */
class MailConfirmationRepository extends BaseRepository
{
    /**
     * MailConfirmationRepository constructor.
     *
     * @param MailConfirmation $model
     */
    public function __construct(MailConfirmation $model)
    {
        $this->model = $model;
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
     * @param  string $token
     * @param  string $email
     * @return mixed
     */
    public function updateOrCreateToken(string $token, string $email)
    {
        $mailConfirmation = $this->model::updateOrCreate(
            ['email' => $email],
            [
                'email' => $email,
                'token' => $token,
            ]
        );
        return $mailConfirmation;
    }

    /**
     * @param  string $token
     * @return mixed
     */
    public function getByToken(string $token)
    {
        $mailConfirmation = $this->model::where('token', $token)->first();
        return $mailConfirmation;
    }

    /**
     * @param  MailConfirmation $mailConfirmation
     * @return bool
     * @throws \Exception
     */
    public function deleteIfExpired(MailConfirmation $mailConfirmation)
    {
        if (Carbon::parse($mailConfirmation->updated_at)->addMinutes(720)->isPast()) {
            $mailConfirmation->delete();
            return true;
        }
        return false;
    }
}
