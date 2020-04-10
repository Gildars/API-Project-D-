<?php

namespace App\Repositories;

use App\Models\Friend;
use App\Models\User;
use Dingo\Api\Routing\Helpers;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;

/**
 * Class FriendRepository
 *
 * @package App\Repositories
 */
class StatRepository extends BaseRepository
{
    use Helpers;
    /**
     * @var Friend
     */
    protected $model;

    /**
     * @var User
     */
    protected $user;

    /**
     * FriendRepository constructor.
     *
     * @param Friend $friend
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->model = $user;
    }

    /**
     * @param int $id
     * @return \App\Models\Model
     */
    public function getStatsByUserId(int $idUser)
    {
        return $this->model
            ->where("id", "=", $idUser)
            ->get([
                'name',
                'lvl',
                'stat_points',
                'str',
                'dex',
                'sta',
                'int',
                'critical_chance',
                'block',
                'defense',
                'attack'
            ]);
    }

    /**
     * @param $stats
     * @return bool
     */
    public function increaseStats($stats)
    {
        if (!$totalStats = $this->validateTotalStats($stats)) {
            return false;
        }
        $user = $this->auth->user();
        $user->str += $stats['str'];
        $user->dex += $stats['dex'];
        $user->sta += $stats['sta'];
        $user->int += $stats['int'];
        $user->stat_points -= $totalStats;
        return $user->update();
    }

    /**
     * @param $stats
     * @return bool
     */
    private function validateTotalStats($stats)
    {
        $total = $stats['str'] + $stats['dex'] + $stats['sta'] + $stats['int'];
        return ($total <= $this->auth->user()->stat_points) ? $total : false;
    }
}
